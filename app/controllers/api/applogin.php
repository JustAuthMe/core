<?php

use Model\User;

if (!POST) {
    Controller::http405MethodNotAllowed();
    Controller::renderApiError('Only POST requests are accepted');
}

$redis = new \PHPeter\Redis();
$ip_cooldown_cache_key = User::APPLOGIN_CACHE_PREFIX . Utils::slugifyIp(
    Utils::truncateIPV6(
        $_SERVER['REMOTE_ADDR'],
        4
    )
);
$ip_cooldown = (int) $redis->get($ip_cooldown_cache_key);
$new_ttl = $ip_cooldown > 0 ? $redis->ttl($ip_cooldown_cache_key) : User::APPLOGIN_IP_COOLDOWN;

$ip_cooldown++;
if ($ip_cooldown > 5) {
    Controller::http429TooManyRequests();
    Controller::renderApiError('You have tried to many times. Please wait a few minutes.');
}

switch (Request::get()->getArg(2)) {
    case 'request':
        if (ENABLE_APPLE_DEMO_ACCOUNT && $_POST['email'] === APPLE_DEMO_EMAIL) {
            Controller::renderApiSuccess();
        }

        if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http400BadRequest();
            Controller::renderApiError('Invalid e-mail address');
        }

        $hashed_email = User::hashEmail($_POST['email']);
        if (!Persist::exists('User', 'uniqid', $hashed_email)) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);

            $latest_update = \Model\UniqidUpdate::getLatestUpdate($hashed_email);
            if ($latest_update !== false) {
                /** @var \Entity\UniqidUpdate $latest_update */
                Data::get()->add('updated_at', (int) $latest_update->getTimestamp());
                Controller::http410Gone();
                Controller::renderApiError('This e-mail address was associated with a valid account, but has been updated');
            }

            Controller::http404NotFound();
            Controller::renderApiError('Unknown account, please register');
        }

        /** @var \Entity\User $user */
        $user = Persist::readBy('User', 'uniqid', $hashed_email);
        if (isset($_GET['lock']) && $user->getPublicKey() === '') {
            Controller::http423Locked();
            Controller::renderApiError('This account is already locked');
        }

        $email_cooldown_cache_key = User::APPLOGIN_CACHE_PREFIX . $hashed_email;
        $email_cooldown = $redis->get($email_cooldown_cache_key);
        if ($email_cooldown) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http429TooManyRequests();
            Controller::renderApiError('Please wait at least 2 minutes before asking for another code');
        }

        $passcode = User::generatePasscode();

        $hashed_passcode = User::hashInfo($passcode);
        $user_cache_key = User::APPLOGIN_CACHE_PREFIX . $user->getId();
        $passcode_cache_key = User::APPLOGIN_CACHE_PREFIX . $hashed_passcode;
        $old_passcode_cache_key = User::APPLOGIN_CACHE_PREFIX . $redis->get($user_cache_key);

        $redis->del($old_passcode_cache_key);
        $redis->set($user_cache_key, $hashed_passcode, User::APPLOGIN_EXPIRATION_TIME);
        $redis->set($passcode_cache_key, $user->getUniqid(), User::APPLOGIN_EXPIRATION_TIME);
        $redis->set($email_cooldown_cache_key, 1, User::APPLOGIN_EMAIL_COOLDOWN);

        $mailer = new Mailer();
        $mailer->queueMail(
            $_POST['email'],
            L::emails_passcode_subject,
            'mail/' . t()->getAppliedLang() . '/passcode' . (isset($_GET['lock']) ? '_lock' : ''),
            ['passcode' => $passcode]
        );

        Controller::renderApiSuccess();
        break;

    case 'challenge':
        if (!isset($_POST['email'], $_POST['passcode'], $_POST['pubkey'])) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http400BadRequest();
            Controller::renderApiError('E-mail and passcode are required');
        }

        $hashed_email = User::hashEmail($_POST['email']);
        if (ENABLE_APPLE_DEMO_ACCOUNT && $_POST['email'] === APPLE_DEMO_EMAIL) {
            /** @var \Entity\User $apple_user */
            $apple_user = Persist::readBy('User', 'uniqid', $hashed_email);
            $apple_user->setPublicKey($_POST['pubkey']);
            Persist::update($apple_user);
            Data::get()->add('jam_id', $apple_user->getUsername());
            Controller::renderApiSuccess();
        }

        $passcode_cache_key = User::APPLOGIN_CACHE_PREFIX . User::hashInfo($_POST['passcode']);
        $redis = new \PHPeter\Redis();
        $cached = $redis->get($passcode_cache_key);

        $attempts_cache_key = User::APPLOGIN_CACHE_PREFIX . 'attempts_' . $hashed_email;
        $attempts = (int) $redis->get($attempts_cache_key);
        if ($attempts >= 3) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            $redis->set($attempts_cache_key, $attempts, user::APPLOGIN_ATTEMPTS_COOLDOWN);
            Controller::http429TooManyRequests();
            Controller::renderApiError('You have tried to many times. Please wait a few minutes.');
        }

        if ($cached === false || $hashed_email !== $cached) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            $redis->set($attempts_cache_key, $attempts + 1);
            Controller::http403Forbidden();
            Controller::renderApiError('Wrong passcode');
        }

        /** @var \Entity\User $user */
        $user = Persist::readBy('User', 'uniqid', $cached);
        $user->setPublicKey($_POST['pubkey']);
        Persist::update($user);

        $user_cache_key = User::APPLOGIN_CACHE_PREFIX . $user->getId();
        $redis->del($passcode_cache_key);
        $redis->del($user_cache_key);
        $redis->del($attempts_cache_key);

        Data::get()->add('jam_id', $user->getUsername());
        Controller::renderApiSuccess();
        break;

    default:
        Controller::http404NotFound();
        Controller::renderApiError('Unknow endpoint');
}
