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
}