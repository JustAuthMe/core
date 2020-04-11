<?php

use chillerlan\QRCode\QRCode;
use Model\UserAuth;

Controller::sendNoCacheHeaders();

if (!isset($_GET['secret'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('Secret required');
}

if (!Persist::exists('ClientApp', 'secret', $_GET['secret'])) {
    Controller::http401Unauthorized();
    Controller::renderApiError('Wrong secret');
}

/**
 * @var \Entity\ClientApp $clientApp
 */
$clientApp = Persist::readBy('ClientApp', 'secret', $_GET['secret']);
if (!in_array($clientApp->getAppId(), ['jam_admin', 'jam_console'])) {
    Controller::http403Forbidden();
    Controller::renderApiError('This feature is still in internal beta test');
}

$authToken = UserAuth::generateAuthToken();
$userAuth = new \Entity\UserAuth(
    0,
    $authToken,
    $clientApp->getId(),
    $clientApp->getRedirectUrl(),
    Utils::time(),
    $_SERVER['REMOTE_ADDR']
);
$auth_id = Persist::create($userAuth);
$userAuth->setId($auth_id);

$qrCode = new QRCode();
$imgUrl = $qrCode->render(UserAuth::URL_SCHEME . $authToken);

Data::get()->add('token', $authToken);
Data::get()->add('qr', $imgUrl);
Controller::renderApiSuccess();