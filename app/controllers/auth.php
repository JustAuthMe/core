<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 01/12/2018
 * Time: 14:50
 */

\Model\UserAuth::flushOutdatedAuths();

if (!isset($_GET['app_id'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('No App ID provided');
}

$appId = $_GET['app_id'];

if (!\Model\ClientApp::authenticate($appId)) {
    Controller::error403Forbbiden();
    Controller::renderApiError('Authentication failed');
}

if (!isset($_GET['redirect_url']) || !isset($_GET['data'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('Missing params');
}

/**
 * @var \Entity\ClientApp $clientApp
 */
$clientApp = \Model\ClientApp::getClientDetails($appId);

if ($clientApp->getRedirectUrl() !== $_GET['redirect_url']) {
    Controller::error403Forbidden();
    Controller::renderApiError('Wrong redirection URL');
}

/**
 * TODO: Ajouter la vérification de la liste des datas et de leur format
 */

$data = explode(',', $_GET['data']);
$allowed_data = json_decode($clientApp->getData());
foreach($data as $d) {
    if (!in_array(\Model\UserAuth::getDataSlug($d), $allowed_data)) {
        Controller::error403Forbidden();
        Controller::renderApiError('Unauthorized data type');
    }
}
$authToken = \Model\UserAuth::generateAuthToken();

$userAuth = new \Entity\UserAuth(
    0,
    $authToken,
    $clientApp->getId(),
    $_GET['redirect_url'],
    json_encode($data),
    Utils::time(),
    $_SERVER['REMOTE_ADDR']
);
$auth_id = Persist::create($userAuth);
$userAuth->setId($auth_id);

$qrCode = new \chillerlan\QRCode\QRCode();
$imgUrl = $qrCode->render(\Model\UserAuth::URL_SCHEME . $authToken);

Data::get()->add('client', $clientApp);
Data::get()->add('auth', $userAuth);
Data::get()->add('qr_code', $imgUrl);
Controller::renderView('auth/auth');
