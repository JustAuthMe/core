<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 01/12/2018
 * Time: 14:50
 */

\Model\UserAuth::flushOutdatedAuths();

if (!isset($_GET['pubkey'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('No public key provided');
}

$pubkey = $_GET['pubkey'];

if (!\Model\ClientApp::authenticate($pubkey)) {
    Controller::error403Forbbiden();
    Controller::renderApiError('Authentication failed');
}

if (!isset($_GET['redirect_url']) || !isset($_GET['data'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('Missing params');
}

/**
 * TODO: Ajouter la vérification de la liste des datas et de leur format
 */

/**
 * @var \Entity\ClientApp $clientApp
 */
$clientApp = \Model\ClientApp::getClientDetails($pubkey);

if ($clientApp->getRedirectUrl() !== $_GET['redirect_url']) {
    Controller::error403Forbidden();
    Controller::renderApiError('Please don\'t use illegitimate public API keys');
}

$authToken = \Model\UserAuth::generateAuthToken();

$userAuth = new \Entity\UserAuth(
    0,
    $authToken,
    $clientApp->getId(),
    $_GET['redirect_url'],
    json_encode(explode(',', $_GET['data'])),
    null,
    $_SERVER['REMOTE_ADDR']
);
Persist::create($userAuth);

$qrCode = new \chillerlan\QRCode\QRCode();
$imgUrl = $qrCode->render($authToken);

Data::get()->add('client', $clientApp);
Data::get()->add('qr_code', $imgUrl);
Controller::renderView('auth/auth');
