<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:22
 */

Controller::sendNoCacheHeaders();

\Model\UserSpam::flushOutdatedBans();
if (\Model\UserSpam::isIpBanned($_SERVER['REMOTE_ADDR'])) {
    Controller::error429TooManyRequests();
    Controller::renderApiError('You cannot register twice in a row.');
}

if (!isset($_POST['pubkey'], $_POST['uniqid'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('Public key and unique ID needed.');
}

if (Persist::exists('User', 'uniqid', $_POST['uniqid'])) {
    Controller::error409Conflict();
    Controller::renderApiError('You already have a JAM account. Please log in.');
}

$username = \Model\User::generateUsername();
$user = new \Entity\User(
    0,
    $username,
    $_POST['uniqid'],
    null,
    $_SERVER['REMOTE_ADDR'],
    $_POST['pubkey'],
    0
);

$user_id = Persist::create($user);
$user->setId($user_id);

\Model\UserSpam::banIp($_SERVER['REMOTE_ADDR'], $user->getId());
Data::get()->add('user', $user);
Controller::renderApiSuccess();
