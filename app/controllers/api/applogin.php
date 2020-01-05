<?php
if (!POST) {
    Controller::http405MethodNotAllowed();
    Controller::renderApiError('Only POST requests are accepted');
}

if (isset($_POST['email'])) {
    $hashed_email = \Model\User::hashInfo($_POST['email']);
    if (!Persist::exists('User', 'uniqid', $hashed_email)) {
        Controller::http404NotFound();
        Controller::renderApiError('Unknown account, please register');
    }

    // TODO User based cooldown (+ IP based if X connections from same IP in range of X seconds)
    /** @var \Entity\User $user */
    $user = Persist::readBy('User', 'uniqid', $hashed_email);
    $redis = new \PHPeter\Redis();
    $cooldown_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . str_replace('.', '_',
        str_replace(':', '_',
            $_SERVER['REMOTE_ADDR']
        )
    );
    $cooldown = $redis->get($cooldown_cache_key);

    if (!!$cooldown) {
        Controller::http429TooManyRequests();
        Controller::renderApiError('Please wait a few seconds before asking for another code');
    }

    $passcode = \Model\User::generatePasscode();

    $hashed_passcode = \Model\User::hashInfo($passcode);
    $user_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $user->getId();
    $passcode_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $hashed_passcode;
    $old_passcode_cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . $redis->get($user_cache_key);

    $redis->del($old_passcode_cache_key);
    $redis->set($user_cache_key, $hashed_passcode, \Model\User::APPLOGIN_EXPIRATION_TIME);
    $redis->set($passcode_cache_key, $user->getId(), \Model\User::APPLOGIN_EXPIRATION_TIME);
    $redis->set($cooldown_cache_key, 1, \Model\User::APPLOGIN_COOLDOWN);

    $mailer = new Mailer();
    $mailer->queueMail(
        $_POST['email'],
        'Your JustAuth.Me passcode',
        'mail/passcode',
        ['passcode' => $passcode]
    );
} elseif (isset($_POST['passcode'])) {
    $cache_key = \Model\User::APPLOGIN_CACHE_PREFIX . \Model\User::hashInfo($_POST['passcode']);
    $redis = new \PHPeter\Redis();
    $cached = $redis->get($cache_key);

    if ($cached === false) {
        Controller::http403Forbidden();
        Controller::renderApiError('Wrong passcode');
    }

    /** @var \Entity\User $user */
    $user = Persist::read('User', $cached);
    $redis->del($cache_key);
    Data::get()->add('jam_id', $user->getUsername());
    Controller::renderApiSuccess();
}
