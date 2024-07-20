<?php

namespace App\Support;

use App\Encryption\Crypt;

class CookieManager
{
    public bool $encode = true;

    public function set($name, $value, $expiry = 3600, $path = '/', $domain = null, $secure = false, $httponly = true): CookieManager
    {
        if ($this->encode)
            $value = Crypt::encrypt($value);

        $expiryTime = time() + $expiry;
        setcookie($name, $value, $expiryTime, $path, $domain, $secure, $httponly);
        return $this;
    }

    public function get($name)
    {
        $respose = null;
        if ($this->has($name))
        {
            if ($this->encode)
                $respose = Crypt::decrypt($_COOKIE[$name]);
            else
                $respose = $_COOKIE[$name];
        }
        return $respose;
    }

    public function delete($name, $path = '/', $domain = null, $secure = false, $httponly = true): CookieManager
    {
        setcookie($name, '', time() - 3600, $path, $domain, $secure, $httponly);
        unset($_COOKIE[$name]);
        return $this;
    }

    public function has($name): bool
    {
        return isset($_COOKIE[$name]);
    }
}
