<?php
$redis = new \PHPeter\Redis();
$cache_key = \Model\UserAuth::EMAIL_CONFIRM_CACHE_PREFIX . Request::get()->getArg(1);
$cached = $redis->get($cache_key);

if ($cached === false) {
    echo 'erreur'; // TODO
    die;
}

/** @var \Entity\User $user */
$user = Persist::read('User', $cached);
$user->setActive(1);
Persist::update($user);
$redis->del($cache_key);

echo 'Ok'; // TODO