<?php
Controller::sendNoCacheHeaders();

/*
 * Common error cases
 */

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

$authToken = \Model\UserAuth::generateAuthToken();
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

$qrCode = new \chillerlan\QRCode\QRCode();
$imgUrl = $qrCode->render(\Model\UserAuth::URL_SCHEME . $authToken);

Data::get()->add('qr', $imgUrl);
Controller::renderApiSuccess();