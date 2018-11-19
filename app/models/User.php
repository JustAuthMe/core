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
            $username = bin2hex(openssl_random_pseudo_bytes(12));
        } while (\Persist::exists('User', 'username', $username));

        return $username;
    }
}