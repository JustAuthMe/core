<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 18/11/2018
 * Time: 16:34
 */

namespace Model;

class UserAuth {
    public static function generateAuthToken($length = 64) {
        if ($length % 4 !== 0) {
            throw new \Exception('$length must be a factor of 4');
        }

        $bytes_number = 0.75 * $length;
        return base64_encode(openssl_random_pseudo_bytes($bytes_number));
    }
}