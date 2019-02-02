<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:22
 */

\Model\UserSpam::flushOutdatedBans();
if (\Model\UserSpam::isIpBanned($_SERVER['REMOTE_ADDR'])) {
    Controller::error429TooManyRequests();
    Controller::renderApiError('You cannot register twice');
}

if (!isset($_POST['pubkey'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('Public key needed');
}

$username = \Model\User::generateUsername();
$hash_key = \Model\User::generateHashKey();
$user = new \Entity\User(
    0,
    $username,
    null,
    $_SERVER['REMOTE_ADDR'],
    $_POST['pubkey'],
    $hash_key
);

$user_id = Persist::create($user);
$user->setId($user_id);

\Model\UserSpam::banIp($_SERVER['REMOTE_ADDR'], $user->getId());
Data::get()->add('user', $user);
Controller::renderApiSuccess();
