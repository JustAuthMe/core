<?php

use Model\User;
use Model\UserAuth;

if (!isset($_POST['data'], $_POST['data']['app_id'], $_POST['sign']) || !User::authenticateRequest($_POST['data'], $_POST['sign'])) {
    Controller::http401Unauthorized();
    Controller::renderApiError('You\'re not allowed to access this resource');
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    Controller::http405MethodNotAllowed();
    Controller::renderApiError('Only DELETE requests are allowed');
}

$data = $_POST['data'];
$login_hash = UserAuth::generateUserAppPairHash($data['jam_id'], $data['app_id']);
if (!Persist::exists('UserLogin', 'hash', $login_hash)) {
    Controller::http404NotFound();
    Controller::renderApiError('Resource not found');
}

/** @var \Entity\UserLogin $user_login */
$user_login = Persist::readBy('UserLogin', 'hash', $login_hash);
$user_login->setActive(0);
Persist::update($user_login);
Controller::renderApiSuccess();
