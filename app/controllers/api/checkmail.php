<?php
if (!isset($_POST['email'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('E-Mail address is required');
}

$redis = new \PHPeter\Redis();
$cache_key = \Model\User::EMAIL_CHECK_CACHE_PREFIX . Utils::slugifyIp(
    Utils::truncateIPV6(
        $_SERVER['REMOTE_ADDR'],
        4
    )
);
$attempts = (int) $redis->get($cache_key);
$new_ttl = $attempts > 0 ? $redis->ttl($cache_key) : \Model\User::EMAIL_CHECK_COOLDOWN;

$attempts++;

if ($attempts > 5) {
    Controller::http429TooManyRequests();
    Controller::renderApiError('You have tried to many times. Please wait a few minutes.');
}

$is_available = !Persist::exists('User', 'uniqid', \Model\User::hashInfo($_POST['email']));
if (!$is_available) {
    $redis->set($cache_key, $attempts, $new_ttl);
}

Data::get()->add('available', $is_available);
Controller::renderApiSuccess();