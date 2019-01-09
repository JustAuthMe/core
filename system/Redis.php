<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 09/01/2019
 * Time: 20:54
 */

namespace PHPeter;

class Redis extends \Redis {
    public function __construct() {
        parent::__construct();
        $this->connect(REDIS_HOST, REDIS_PORT);
        $this->auth(REDIS_PASS);
    }
}