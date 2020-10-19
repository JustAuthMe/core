<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 02/12/2018
 * Time: 12:04
 */

use Entity\UserLogin;
use Model\UserAuth;

Controller::sendNoCacheHeaders();

/*
 * Common error cases
 */

if (!isset($_POST['data'], $_POST['sign'])) {
    Controller::http401Unauthorized();
    Controller::renderApiError('You\'re not allowed to access this resource');
}

$posted_data = is_string($_POST['data']) && json_decode($_POST['data']) !== null ?
    json_decode($_POST['data'], true) :
    $_POST['data'];

$stringified_data = urlencode(
    is_string($_POST['data']) && json_decode($_POST['data']) !== null ?
        $_POST['data'] :
        json_encode($_POST['data'], JSON_UNESCAPED_UNICODE)
);

if (!is_array($posted_data) || !isset($posted_data['token'], $posted_data['jam_id'])) {
    Controller::http401Unauthorized();
    Controller::renderApiError('You\'re not allowed to access this resource');
}

$token = $posted_data['token'];
if (!Persist::exists('UserAuth', 'token', $token)) {
    Controller::http404NotFound();
    Controller::renderApiError('No such token');
}

/**
 * @var \Entity\User $user
 */
$user = Persist::readBy('User', 'username', $posted_data['jam_id']);

if (!$user->isActive()) {
    Controller::http403Forbidden();
    Controller::renderApiError('You haven\'t activated your e-mail address yet.');
}

if ($user->getPublicKey() === '') {
    Controller::http423Locked();
    Controller::renderApiError('This account is locked');
}

/*
 * Verigying data signature
 */

$verify = Crypt::verify($stringified_data, base64_decode($_POST['sign']), $user->getPublicKey());

            /*
             * (Logging)
             */

            /**
            // (Verifying manually)
            openssl_public_decrypt(base64_decode($_POST['sign']), $clair, $user->getPublicKey());
            $toSave = $_POST['plain'] . "\n\n" .
                $stringified_data . "\n\n" .
                hash('sha512', $stringified_data) . "\n\n" .
                substr(bin2hex($clair), -128) . "\n\n" .
                $_POST['sign'];
            Logger::logInfo($toSave);
            /**/

            /*
             * (/Logging)
             */

if ($verify < 1) {
    Controller::http401Unauthorized();
    Controller::renderApiError('You\'re not allowed to access this resource');
}

/*
 * Verifying dataset
 */

/**
 * @var \Entity\UserAuth $auth
 */
$auth = Persist::readBy('UserAuth', 'token', $token);

$login_hash = UserAuth::generateUserAppPairHash($posted_data['jam_id'], $auth->client_app->getAppId());
$isFirstTime = true;
$user_login = null;

if (Persist::exists('UserLogin', 'hash', $login_hash)) {
    /** @var UserLogin $user_login */
    $user_login = Persist::readBy('UserLogin', 'hash', $login_hash);
    $isFirstTime = !$user_login->isActive();
}

if ($isFirstTime) {
    $data = json_decode($auth->client_app->getData());

    foreach ($data as $d) {
        if (UserAuth::isDataRequired($d) && !isset($posted_data[UserAuth::getDataSlug($d)])) {
            Controller::http400BadRequest();
            Controller::renderApiError('Missing param ' . $d);
        }
    }

    if (is_null($user_login)) {
        $salt = UserAuth::generateLoginSalt();
        $user_login = new UserLogin(
            0,
            $login_hash,
            $salt,
            1
        );
        $ul_id = Persist::create($user_login);
        $user_login->setId($ul_id);
    } else {
        $user_login->setActive(1);
        Persist::update($user_login);
    }
}

/*
 * Hashing JAM ID
 */

$posted_data['jam_id'] = hash_hmac('sha512', $posted_data['jam_id'] . $user_login->getSalt(), $auth->client_app->getHashKey());

/*
 * Data storage
 */

unset($posted_data['token']);
$oauth_token = UserAuth::generateOAuthToken();
$cacheKey = UserAuth::ACCESS_TOKEN_CACHE_PREFIX . $auth->getClientAppId() . '_' . $oauth_token;
$redis = new \PHPeter\Redis();
$redis->set($cacheKey, $posted_data, UserAuth::EXPIRATION_TIME);

/*
 * Auth deletion
 */

Persist::delete($auth);

/*
 * Data transfert
 */

\Ratchet\Client\connect(WEBSOCKET_SOCKET_LOCAL)->then(function($conn) use ($token, $oauth_token) {
    /**
     * @var \Ratchet\Client\WebSocket $conn
     */
    $conn->on('message', function($msg) use ($conn) {
        $obj = json_decode($msg);
        if ($obj !== null && isset($obj->error)) {
            Controller::http400BadRequest();
            Controller::renderApiError($obj->error);
        }
    });

    $dataToSend = [
        'access_token' => $oauth_token
    ];
    $data = [
        'type' => 'data',
        'auth_id' => $token,
        'data' => $dataToSend,
        'sign' => UserAuth::signData($dataToSend)
    ];

    $conn->send(json_encode($data));
    $conn->close();
}, function (Exception $e) {
    error_log($e->getMessage());
    Controller::http500InternalServerError();
});

Controller::renderApiSuccess();
