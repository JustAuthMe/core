<?php
$redis = new \PHPeter\Redis();
$cache_key = \Model\User::EMAIL_CONFIRM_CACHE_PREFIX . Request::get()->getArg(1);
$cached = $redis->get($cache_key);

if ($cached === false) {
    Data::get()->add('error', true);
    Controller::renderView('confirm/confirm');
    die;
}

/** @var \Entity\User $user */
$user = Persist::read('User', $cached);
$user->setActive(1);
Persist::update($user);
$redis->del($cache_key);

Controller::renderView('confirm/confirm');