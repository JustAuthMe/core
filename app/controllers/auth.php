<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 01/12/2018
 * Time: 14:50
 */

use chillerlan\QRCode\QRCode;
use Model\ClientApp;

if (!isset($_GET['app_id'])) {
    Controller::http400BadRequest();
}

$detect = new Mobile_Detect();
$is_mobile = $detect->isMobile() || $detect->isTablet();
Data::get()->add('is_mobile', $is_mobile);
Data::get()->add('TITLE', L::auth_title);

$appId = $_GET['app_id'];
if ($appId === 'ad') {
    $qrCode = new QRCode();
    $imgUrl = $qrCode->render('https://justauth.me/?pk_campaign=ad_qr');
    Data::get()->add('qr_code', $imgUrl);
    Controller::renderView('auth/auth');
    die;
}

/** OpenID handling */
$is_openid = false;
if (isset($_GET['openid_auth'], $_GET['openid_hash'])) {
    $calculated_openid_hash = hash_hmac('sha512', $_GET['openid_auth'], OPENID_SIGN_KEY);
    if ($calculated_openid_hash === $_GET['openid_hash']) {
        $is_openid = true;
        Data::get()->add('openid_auth', $_GET['openid_auth']);
    }
}
/** End */

if (!ClientApp::authenticate($appId)) {
    Controller::http403Forbbiden();

    if ($is_openid) {
        header('location: ' . OPENID_SERVER . 'authorization/callback?auth_id=' . $_GET['openid_auth'] . 'error=403');
        die;
    }
}

if (!isset($_GET['redirect_url'])) {
    Controller::http400BadRequest();
}

/**
 * @var \Entity\ClientApp $clientApp
 */
$clientApp = ClientApp::getClientDetails($appId);

if (
    $clientApp->getRedirectUrl() !== $_GET['redirect_url'] && (
        !$clientApp->isDev() ||
        !preg_match("#^https?\:\/\/(" . preg_quote($clientApp->getDomain(), '#') .
            "|localhost|127(\.[0-9]{1,3}){3}|192\.168(\.[0-9]{1,3}){2}|10(\.[0-9]{1,3}){3}|[a-z0-9-.]+\.local)\b(?!\.)#", $_GET['redirect_url'])
    )
) {
    Controller::http403Forbidden();

    if ($is_openid) {
        header('location: ' . OPENID_SERVER . 'authorization/callback?auth_id=' . $_GET['openid_auth'] . 'error=403');
        die;
    }
}

$authToken = \Model\UserAuth::generateAuthToken();
$userAuth = new \Entity\UserAuth(
    0,
    $authToken,
    $clientApp->getId(),
    $_GET['redirect_url'],
    Utils::time(),
    $_SERVER['REMOTE_ADDR']
);
$auth_id = Persist::create($userAuth);
$userAuth->setId($auth_id);

$qrCode = new QRCode();
$imgUrl = $qrCode->render(\Model\UserAuth::URL_SCHEME . $authToken);

Data::get()->add('client', $clientApp);
Data::get()->add('auth', $userAuth);
Data::get()->add('qr_code', $imgUrl);
Data::get()->add('is_openid', $is_openid);
Controller::renderView('auth/auth');
