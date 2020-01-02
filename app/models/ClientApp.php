<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 18/11/2018
 * Time: 14:51
 */

namespace Model;

class ClientApp {
    public static function authenticate($appId) {
        return \Persist::exists('ClientApp', 'app_id', $appId);
    }

    public static function getClientDetails($appId) {
        return \Persist::readBy('ClientApp', 'app_id', $appId);
    }

    public static function generateHashKey($length = 64) {
        if ($length % 4 !== 0) {
            throw new \Exception('$length must be a factor of 4');
        }

        $bytes_number = 0.75 * $length;
        return base64_encode(openssl_random_pseudo_bytes($bytes_number));
    }

    public static function generateAppId($domain) {
        return substr(sha1($domain . UserAuth::generateLoginSalt()), 0, 32);
    }

    public static function generateSecret($length = 32) {
        do {
            $secret = UserAuth::generateAuthToken($length);
        } while (\Persist::exists('ClientApp', 'secret', $secret));

        return $secret;
    }
}