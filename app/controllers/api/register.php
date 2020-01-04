<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:22
 */

Controller::sendNoCacheHeaders();

\Model\UserSpam::flushOutdatedBans();
if (\Model\UserSpam::isIpBanned($_SERVER['REMOTE_ADDR'])) {
    Controller::error429TooManyRequests();
    Controller::renderApiError('You cannot register twice in a row.');
}

if (!isset($_POST['pubkey'], $_POST['email'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('Public key and email needed.');
}

$uniqid = \Model\User::hashEmail($_POST['email']);
if (Persist::exists('User', 'uniqid', $uniqid)) {
    Controller::error409Conflict();
    Controller::renderApiError('You already have a JAM account. Please log in.');
}

$username = \Model\User::generateUsername();
$user = new \Entity\User(
    0,
    $username,
    $uniqid,
    null,
    $_SERVER['REMOTE_ADDR'],
    $_POST['pubkey'],
    0
);

$user_id = Persist::create($user);
$user->setId($user_id);

$confirm_token = \Model\UserAuth::generateAuthToken();
$cache_key = \Model\UserAuth::EMAIL_CONFIRM_CACHE_PREFIX . $confirm_token;
$redis = new \PHPeter\Redis();
$redis->set($cache_key, $user->getId(), \Model\UserAuth::EMAIL_CONFIRM_EXPIRATION_TIME);
$confirm_link = BASE_URL . 'confirm/' . $confirm_token;

$mailer = new Mailer();
$mailer->queueMail(
    $_POST['email'],
    'E-Mail address confirmation',
    'mail/default',
    [
        'subject' => 'E-Mail address confirmation',
        'body' => 'Bonjour,<br />' .
        'Vous avez récemment créé un compte JustAuth.Me sur votre appareil mobile. ' .
        'Afin de compléter le processus d\'inscription, nous vous invitons à valider ' .
        'votre adresse E-Mail en cliquant sur le bouton ci-dessous. ' .
        'Attention, ce lien n\'est valide que 24h ! ' .
        'Si le bouton ne s\'affiche pas ou si vous rencontrez des difficultés pour l\'utiliser, copiez ' .
        'simplement ce lien dans votre navigateur web :<br />' .
        '<span style="color:blue">' . $confirm_link . '</span>',
        'call_to_action' => [
            'title' => 'Confirm my E-Mail address',
            'link' => $confirm_link
        ]
    ]
);

\Model\UserSpam::banIp($_SERVER['REMOTE_ADDR'], $user->getId());
Data::get()->add('user', $user);
Controller::renderApiSuccess();
