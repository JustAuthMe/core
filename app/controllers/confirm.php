<?php

use Model\UniqidUpdate;
use Model\User;

$redis = new \PHPeter\Redis();
$cache_key = User::EMAIL_CONFIRM_CACHE_PREFIX . Request::get()->getArg(1);
$cached = $redis->get($cache_key);

if ($cached === false || !Persist::exists('User', 'id', $cached)) {
    Data::get()->add('error', true);
    Data::get()->add('TITLE', 'Invalid link');
    Controller::renderView('confirm/confirm');
    die;
}

/** @var \Entity\User $user */
$user = Persist::read('User', $cached);
if ($user->getPublicKey() === '') {
    $redis->del($cache_key);
    Data::get()->add('error', true);
    Data::get()->add('TITLE', 'Invalid link');
    Controller::renderView('confirm/confirm');
    die;
}

if (UniqidUpdate::isThereAnActiveUpdate($user->getId())) {
    $uniqid_update = UniqidUpdate::getActiveUpdate($user->getId());
    $user->setUniqid($uniqid_update->getNewUniqid());
    $uniqid_update->setActive(0);
    Persist::update($uniqid_update);
}

$user->setActive(1);
Persist::update($user);
$redis->del($cache_key);

Data::get()->add('TITLE', 'E-Mail address validated');
Controller::renderView('confirm/confirm');