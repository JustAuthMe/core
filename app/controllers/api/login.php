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
    Controller::error400BadRequest();
    Controller::renderApiError('No data given');
}

if (!isset($_POST['sign'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('Data signature is required');
}

$posted_data = json_decode($_POST['data'], true);
if (!is_array($posted_data)) {
    Controller::error400BadRequest();
    Controller::renderApiError('Wrong data format');
}

if (!isset($posted_data['token'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('No token provided');
}

if (!isset($posted_data['jam_id'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('JAM ID is required');
}

$token = $posted_data['token'];
if (!Persist::exists('UserAuth', 'token', $token)) {
    Controller::error404NotFound();
    Controller::renderApiError('No such token');
}

/*
 * Verigying data signature
 */

/**
 * @var \Entity\User $user
 */
$user = Persist::readBy('User', 'username', $posted_data['jam_id']);
$verify = Crypt::verify($_POST['data'], $_POST['sign'], $user->getPublicKey());

if ($verify === -1) {
    Controller::error500InternalServerError();
    Controller::renderApiError('Can\'t verify data signature');
} elseif ($verify === 0) {
    Controller::error400BadRequest();
    Controller::renderApiError('Wrong data signature');
}

/*
 * Hashing JAM ID
 */

$posted_data['jam_id'] = hash_hmac('sha512', $posted_data['jam_id'], $user->getHashKey());

/*
 * Verifying dataset
 */

/**
 * @var \Entity\UserAuth $auth
 */
$auth = Persist::readBy('UserAuth', 'token', $token);
$data = json_decode($auth->getData());

foreach ($data as $d) {
    if (\Model\UserAuth::isDataRequired($d) && !isset($posted_data[\Model\UserAuth::getDataSlug($d)])) {
        Controller::error400BadRequest();
        Controller::renderApiError('Missing param ' . $d);
    }
}

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
            Controller::error400BadRequest();
            Controller::renderApiError($obj->error);
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

    Controller::renderApiSuccess();
}, function (Exception $e) {
    error_log($e->getMessage());
    Controller::error500InternalServerError();
});
