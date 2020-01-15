<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:22
 */

Controller::sendNoCacheHeaders();

\Model\UserSpam::flushOutdatedBans();
// TODO Redis cooldown istead of MySQL
if (\Model\UserSpam::isIpBanned($_SERVER['REMOTE_ADDR'])) {
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

\Model\UserSpam::banIp($_SERVER['REMOTE_ADDR'], $user->getId());
Data::get()->add('user', $user);
Controller::renderApiSuccess();
