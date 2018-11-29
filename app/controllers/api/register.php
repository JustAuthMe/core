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

$username = \Model\User::generateUsername();
$user = new \Entity\User(
    0,
    $username,
    null,
    $_SERVER['REMOTE_ADDR']
);

$user_id = Persist::create($user);
$user->setId($user_id);

\Model\UserSpam::banIp($_SERVER['REMOTE_ADDR'], $user->getId());
Data::get()->add('user', $user);
Controller::renderApiSuccess();
