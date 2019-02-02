<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:23
 */

namespace Model;


class User {
    public static function generateUsername() {
        do {
            $username = bin2hex(openssl_random_pseudo_bytes(32));
        } while (\Persist::exists('User', 'username', $username));

        return $username;
    }

    public static function generateHashKey($length = 64) {
        if ($length % 4 !== 0) {
            throw new \Exception('$length must be a factor of 4');
        }

        $bytes_number = 0.75 * $length;
        return base64_encode(openssl_random_pseudo_bytes($bytes_number));
    }
}