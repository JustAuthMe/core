<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 18/11/2018
 * Time: 14:51
 */

namespace Model;

class ClientApp {
    public static function authenticate($secret) {
        return \Persist::exists('ClientApp', 'secret', $secret);
    }

    public static function getClientDetails($secret) {
        return \Persist::readBy('ClientApp', 'secret', $secret);
    }
}