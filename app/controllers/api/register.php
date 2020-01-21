<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:22
 */

Controller::sendNoCacheHeaders();

$redis = new \PHPeter\Redis();
$cache_key = \Model\User::REGISTER_CACHE_PREFIX . Utils::slugifyIp(
    Utils::truncateIPV6(
        $_SERVER['REMOTE_ADDR'],
        4
    )
);
$cached = $redis->get($cache_key);

if ($cached !== false) {
    Controller::http429TooManyRequests();
    Controller::renderApiError('You cannot register twice in a row.');
}

if (!isset($_POST['pubkey'], $_POST['email'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('Public key and email needed.');
}

$uniqid = \Model\User::hashInfo($_POST['email']);
if (Persist::exists('User', 'uniqid', $uniqid)) {
    Controller::http409Conflict();
    Controller::renderApiError('You already have a JAM account. Please log in.');
}

$username = \Model\User::generateUsername();
$user = new \Entity\User(
    0,
    $username,
    $uniqid,
    null,
    $_SERVER['REMOTE_ADDR'],
    $_POST['pubkey'],
    0
);

$user_id = Persist::create($user);
$user->setId($user_id);

\Model\User::sendConfirmMail($user->getId(), $_POST['email']);
$redis->set($cache_key, 1, \Model\User::REGISTER_EXPIRATION_TIME);

Data::get()->add('user', $user);
Controller::renderApiSuccess();
