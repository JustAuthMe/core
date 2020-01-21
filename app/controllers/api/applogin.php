<?php
if (!POST) {
    Controller::http405MethodNotAllowed();
    Controller::renderApiError('Only POST requests are accepted');
}

$redis = new \PHPeter\Redis();
$ip_cooldown_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . Utils::slugifyIp(
    Utils::truncateIPV6(
        $_SERVER['REMOTE_ADDR'],
        4
    )
);
$ip_cooldown = (int) $redis->get($ip_cooldown_cache_key);
$new_ttl = $ip_cooldown > 0 ? $redis->ttl($ip_cooldown_cache_key) : \Model\User::APPLOGIN_IP_COOLDOWN;

$ip_cooldown++;
if ($ip_cooldown > 5) {
    Controller::http429TooManyRequests();
    Controller::renderApiError('You have tried to many times. Please wait a few minutes.');
}

switch (Request::get()->getArg(2)) {
    case 'request':
        if (isset($_POST['email'])) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http400BadRequest();
            Controller::renderApiError('E-Mail is required');
        }

        $hashed_email = \Model\User::hashInfo($_POST['email']);
        if (!Persist::exists('User', 'uniqid', $hashed_email)) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http404NotFound();
            Controller::renderApiError('Unknown account, please register');
        }

        /** @var \Entity\User $user */
        $user = Persist::readBy('User', 'uniqid', $hashed_email);

        $email_cooldown_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $hashed_email;
        $email_cooldown = $redis->get($email_cooldown_cache_key);
        if ($email_cooldown) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http429TooManyRequests();
            Controller::renderApiError('Please wait at least 2 minutes before asking for another code');
        }

        $passcode = \Model\User::generatePasscode();

        $hashed_passcode = \Model\User::hashInfo($passcode);
        $user_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $user->getId();
        $passcode_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $hashed_passcode;
        $old_passcode_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $redis->get($user_cache_key);

        $redis->del($old_passcode_cache_key);
        $redis->set($user_cache_key, $hashed_passcode, \Model\User::APPLOGIN_EXPIRATION_TIME);
        $redis->set($passcode_cache_key, $user->getUniqid(), \Model\User::APPLOGIN_EXPIRATION_TIME);
        $redis->set($email_cooldown_cache_key, 1, \Model\User::APPLOGIN_EMAIL_COOLDOWN);

        $mailer = new Mailer();
        $mailer->queueMail(
            $_POST['email'],
            'Your JustAuth.Me passcode',
            'mail/passcode',
            ['passcode' => $passcode]
        );

        Controller::renderApiSuccess();
        break;

    case 'challenge':
        if (!isset($_POST['email'], $_POST['passcode'])) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http400BadRequest();
            Controller::renderApiError('E-Mail and passcode are required');
        }

        $passcode_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . \Model\User::hashInfo($_POST['passcode']);
        $redis = new \PHPeter\Redis();
        $cached = $redis->get($passcode_cache_key);

        if ($cached === false) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http403Forbidden();
            Controller::renderApiError('Wrong passcode');
        }

        if ($hashed_email !== $cached) {
            $redis->set($ip_cooldown_cache_key, $ip_cooldown, $new_ttl);
            Controller::http403Forbidden();
            Controller::renderApiError('Wrong passcode');
        }

        /** @var \Entity\User $user */
        $user = Persist::readBy('User', 'uniqid', $cached);
        $user_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $user->getId();
        $redis->del($passcode_cache_key);
        $redis->del($user_cache_key);
        Data::get()->add('jam_id', $user->getUsername());
        Controller::renderApiSuccess();
        break;

    default:
        Controller::http404NotFound();
        Controller::renderApiError('Unknow endpoint');
}