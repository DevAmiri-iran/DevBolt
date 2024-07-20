<?php
namespace App\Encryption;

use Exception;

class Crypt
{
    private static string $tag = 'VA';
    private static string $cipher_algo = 'chacha20';
    private static function Key(): false|string
    {
        $config = config('app');
        self::$cipher_algo = $config['cipher'];
        return env('APP_KEY');
    }
    public static function encrypt($data): string
    {
        if (self::Key())
        {
            $ivSize = openssl_cipher_iv_length(self::$cipher_algo);
            $iv = openssl_random_pseudo_bytes($ivSize);
            $encodedData = openssl_encrypt($data, self::$cipher_algo, self::Key(), 0, $iv);
            return self::$tag . rtrim(str_replace(['+', '/'], ['.', '_'], base64_encode($iv .$encodedData)), '=');

        }
        return 'Invalid APP_KEY format';
    }
    public static function decrypt($data): ?string
    {
        if (self::Key()) {
            $data = str_replace(' ', '', $data);
            $tag = substr($data, 0, 2);
            $data = substr($data, 2);
            if ($tag == self::$tag) {
                try {
                    $data = str_replace(['.', '_'], ['+', '/'], $data);
                    $data = base64_decode($data);
                    $ivSize = openssl_cipher_iv_length(self::$cipher_algo);
                    $iv = substr($data, 0, $ivSize);
                    $encryptedData = substr($data, $ivSize);
                    $decryptedData = openssl_decrypt($encryptedData, self::$cipher_algo, self::Key(), 0, $iv);
                    if ($decryptedData)
                        return $decryptedData;
                    else
                        return NULL;
                } catch (Exception $e) {
                    return null;
                }
            }
            else
                return NULL;
        }
        return null;
    }
}
