<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 03/02/2019
 * Time: 14:32
 */

Controller::sendNoCacheHeaders();

/*
 * Common error cases
 */

if (!isset($_GET['token'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('Token required');
}

if (!isset($_GET['secret'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('Secret required');
}

if (!Persist::exists('ClientApp', 'secret', $_GET['secret'])) {
    Controller::http403Forbidden();
    Controller::renderApiError('Wrong secret');
}

/*
 * Get data from Redis
 */

/**
 * @var \Entity\ClientApp $clientApp
 */
$clientApp = Persist::readBy('ClientApp', 'secret', $_GET['secret']);

$redis = new \PHPeter\Redis();
$cacheKey = \Model\UserAuth::OAUTH_TOKEN_CACHE_PREFIX . $clientApp->getId() . '_' . $_GET['token'];
$cached = $redis->get($cacheKey, true);

/*
 * Redis error cases
 */

if ($cached === false) {
    Controller::http403Forbidden();
    Controller::renderApiError('No such token');
}

if (!is_array($cached)) {
    Controller::http500InternalServerError();
    Controller::renderApiError('Wrong data format');
}

/*
 * Data transfert
 */

$redis->del($cacheKey);

Data::get()->setData($cached);
Controller::renderApiSuccess();
