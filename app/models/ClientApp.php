<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 18/11/2018
 * Time: 14:51
 */

namespace Model;

class ClientApp {
    public static function authenticate($pubkey) {
        return \Persist::exists('ClientApp', 'pubkey', $pubkey);
    }

    public static function getClientDetails($pubkey) {
        return \Persist::readBy('ClientApp', 'pubkey', $pubkey);
    }
}