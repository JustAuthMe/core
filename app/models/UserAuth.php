<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 18/11/2018
 * Time: 16:34
 */

namespace Model;

class UserAuth {
    const EXPIRATION_TIME = 600; // 10 minutes

    public static function generateAuthToken($length = 64) {
        if ($length % 4 !== 0) {
            throw new \Exception('$length must be a factor of 4');
        }

        $bytes_number = 0.75 * $length;
        return str_replace('+', '_', str_replace('/', '_', base64_encode(openssl_random_pseudo_bytes($bytes_number))));
    }

    public static function flushOutdatedAuths() {
        \DB::get()->query("DELETE FROM user_auth WHERE timestamp + " . self::EXPIRATION_TIME . " < CURRENT_TIMESTAMP()");
    }

    public static function isDataRequired($data) {
        return strpos($data, '!') === mb_strlen($data) - 1;
    }

    public static function getDataSlug($data) {
        return self::isDataRequired($data) ? substr($data, 0, -1) : $data;
    }

    public static function signData($data) {
        return hash_hmac('sha512', json_encode($data), DATA_TRANSFERT_KEY);
    }

    public static function checkDataSign($data, $sign) {
        return self::signData($data) === $sign;
    }
}