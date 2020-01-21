<?php
if (!POST) {
    Controller::http405MethodNotAllowed();
    Controller::renderApiError('Only POST requests are allowed');
}

if (!isset($_POST['email'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('E-Mail is required');
}

$hashed_email = \Model\User::hashInfo($_POST['email']);

if (!Persist::exists('User', 'uniqid', $hashed_email)) {
    Controller::http404NotFound();
    Controller::renderApiError('User not found');
}

/** @var \Entity\User $user */
$user = Persist::readBy('User', 'uniqid', $hashed_email);
if ($user->isActive()) {
    Controller::http409Conflict();

    // False reason for not to give hackers a chance to know which email is registered or not
    Controller::renderApiError('User not found');
}

$redis = new \PHPeter\Redis();
$cache_key = \Model\User::EMAIL_CONFIRM_CACHE_PREFIX . $hashed_email;
$cached = $redis->get($cache_key);

if ($cached !== false) {
    Controller::http429TooManyRequests();
    Controller::renderApiError('Please wait at least 10 minutes before asking for a new confirmation E-Mail. Please check your junk mail.');
}

\Model\User::sendConfirmMail($user->getId(), $_POST['email']);

$redis->set($cache_key, 1, \Model\User::EMAIL_CONFIRM_COOLDOWN);

Controller::renderApiSuccess();
