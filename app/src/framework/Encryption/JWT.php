<?php

namespace App\Encryption;
use App\Encryption\Crypt;
use stdClass;

class JWT
{
    public function encode(array|stdClass $data): string
    {
        return Crypt::encrypt(json_encode($data, JSON_UNESCAPED_UNICODE));
    }


    public function decode(string $data): array|stdClass
    {
        return json_decode(Crypt::decrypt($data));
    }
}