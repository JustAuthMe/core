<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 18/11/2018
 * Time: 16:34
 */

namespace Model;

class UserAuth {
    const EXPIRATION_TIME = 30; // 2 minutes
    const OAUTH_TOKEN_CACHE_PREFIX = 'token_';
    const URL_SCHEME = 'jam://';

    public static function generateAuthToken($length = 64) {
        if ($length % 4 !== 0) {
            throw new \Exception('$length must be a factor of 4');
        }

        $bytes_number = 0.75 * $length;
        return str_replace('+', '', str_replace('/', '', base64_encode(openssl_random_pseudo_bytes($bytes_number))));
    }

    public static function generateOAuthToken($length = 32) {
        return self::generateAuthToken($length);
    }

    public static function flushOutdatedAuths() {
        \DB::get()->query("DELETE FROM user_auth WHERE timestamp + " . self::EXPIRATION_TIME . " < " . \Utils::time());
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

    public static function generateUserAppPairHash($jam_id, $app_id) {
        return hash('sha512', $jam_id . $app_id);
    }

    public static function generateLoginSalt($length = 8) {
        return self::generateAuthToken($length);
    }
}