<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 02/12/2018
 * Time: 12:04
 */

Controller::sendNoCacheHeaders();

/*
 * Common error cases
 */

if (!isset($_POST['data'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('No data given');
    Logger::logError('No data given');
}

if (!isset($_POST['sign'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('Data signature is required');
    Logger::logError('Data signature is required');
}

$posted_data = is_string($_POST['data']) && json_decode($_POST['data']) !== null ?
    json_decode($_POST['data'], true) :
    $_POST['data'];

$stringified_data = urlencode(
    is_string($_POST['data']) && json_decode($_POST['data']) !== null ?
        $_POST['data'] :
        json_encode($_POST['data'], JSON_UNESCAPED_UNICODE)
);

if (!is_array($posted_data)) {
    Controller::http400BadRequest();
    Controller::renderApiError('Wrong data format');
    Logger::logError('Wrong data format');
    Logger::logInfo(json_encode($posted_data));
}

if (!isset($posted_data['token'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('No token provided');
    Logger::logError('No token provided');
}

if (!isset($posted_data['jam_id'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('JAM ID is required');
    Logger::logError('JAM ID is required');
}

$token = $posted_data['token'];
if (!Persist::exists('UserAuth', 'token', $token)) {
    Controller::http404NotFound();
    Controller::renderApiError('No such token');
    Logger::logError('No such token: ' . $token);
}

/**
 * @var \Entity\User $user
 */
$user = Persist::readBy('User', 'username', $posted_data['jam_id']);

if (!$user->isActive()) {
    Controller::http403Forbidden();
    Controller::renderApiError('You haven\'t activated your E-Mail address yet.');
    Logger::logError('User #' . $user->getId() . ' have a unactive account');
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

if ($verify === -1) {
    Controller::http500InternalServerError();
    Controller::renderApiError('Can\'t verify data signature');
    Logger::logError('Can\'t verify data signature');
} elseif ($verify === 0) {
    Controller::http400BadRequest();
    Controller::renderApiError('Wrong data signature');
    Logger::logError('Wrong data signature');
}

/*
 * Verifying dataset
 */

/**
 * @var \Entity\UserAuth $auth
 */
$auth = Persist::readBy('UserAuth', 'token', $token);

$login_hash = \Model\UserAuth::generateUserAppPairHash($posted_data['jam_id'], $auth->client_app->getAppId());
if (!Persist::exists('UserLogin', 'hash', $login_hash)) {
    $data = json_decode($auth->getData());

    foreach ($data as $d) {
        if (\Model\UserAuth::isDataRequired($d) && !isset($posted_data[\Model\UserAuth::getDataSlug($d)])) {
            Controller::http400BadRequest();
            Controller::renderApiError('Missing param ' . $d);
            Logger::logError('Missing param ' . $d);
        }
    }

    $salt = \Model\UserAuth::generateLoginSalt();
    $user_login = new \Entity\UserLogin(
        0,
        $login_hash,
        $salt
    );
    $ul_id = Persist::create($user_login);
    $user_login->setId($ul_id);
} else {
    $user_login = Persist::readBy('UserLogin', 'hash', $login_hash);
}

/*
 * Hashing JAM ID
 */

$posted_data['jam_id'] = hash_hmac('sha512', $posted_data['jam_id'] . $user_login->getSalt(), $auth->client_app->getHashKey());

/*
 * Data storage
 */

$oauth_token = \Model\UserAuth::generateOAuthToken();
$cacheKey = \Model\UserAuth::OAUTH_TOKEN_CACHE_PREFIX . $auth->getClientAppId() . '_' . $oauth_token;
$redis = new \PHPeter\Redis();
$redis->set($cacheKey, $posted_data, \Model\UserAuth::EXPIRATION_TIME);

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
            Logger::logError($obj->error);
        }
    });

    $dataToSend = [
        'access_token' => $oauth_token,
        'token_type' => 'bearer'
    ];
    $data = [
        'type' => 'data',
        'auth_id' => $token,
        'data' => $dataToSend,
        'sign' => \Model\UserAuth::signData($dataToSend)
    ];

    $conn->send(json_encode($data));
    $conn->close();

}, function (Exception $e) {
    error_log($e->getMessage());
    Controller::http500InternalServerError();
    Logger::logError($e->getMessage());
});

Controller::renderApiSuccess();
