<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 18/11/2018
 * Time: 13:55
 */

if (Request::get()->getArg(2) === '') {
    Controller::error400BadRequest();
    Controller::renderApiError('No secret provided');
}

$secret = Request::get()->getArg(2);

if (!\Model\ClientApp::authenticate($secret)) {
    Controller::error403Forbbiden();
    Controller::renderApiError('Authentication failed');
}

if (!isset($_POST['callback_url']) || !isset($_POST['data']) || !is_array($_POST['data'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('Missing params');
}

/**
 * @var \Entity\ClientApp $clientApp
 */
$clientApp = \Model\ClientApp::getClientDetails($secret);
$authToken = \Model\UserAuth::generateAuthToken();

$userAuth = new \Entity\UserAuth(
    0,
    $authToken,
    $clientApp->getId(),
    $_POST['callback_url'],
    json_encode($_POST['data'])
);
Persist::create($userAuth);

$qrCode = new \chillerlan\QRCode\QRCode();
$imgUrl = $qrCode->render($authToken);

Data::get()->add('status', 'success');
Data::get()->add('qr_code', $imgUrl);
