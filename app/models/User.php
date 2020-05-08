<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:23
 */

namespace Model;


class User {
    const REGISTER_CACHE_PREFIX = 'register_';
    const REGISTER_EXPIRATION_TIME = 30;

    const EMAIL_CONFIRM_CACHE_PREFIX = 'confirm_';
    const EMAIL_CONFIRM_EXPIRATION_TIME = 86400; // 24 hours
    const EMAIL_CONFIRM_COOLDOWN = 600; // 10 minutes

    const EMAIL_CHECK_CACHE_PREFIX = 'checkmail_';
    const EMAIL_CHECK_COOLDOWN = 300; // 2 minutes

    const APPLOGIN_CACHE_PREFIX = 'applogin_';
    const APPLOGIN_EXPIRATION_TIME = 600; // 10 minutes
    const APPLOGIN_EMAIL_COOLDOWN = 120; // 2 minutes
    const APPLOGIN_IP_COOLDOWN = 300; // 5 minutes
    const APPLOGIN_ATTEMPTS_COOLDOWN = 300; // 5 minutes

    public static function generateUsername() {
        do {
            $username = bin2hex(openssl_random_pseudo_bytes(32));
        } while (\Persist::exists('User', 'username', $username));

        return $username;
    }

    public static function hashInfo($info) {
        return hash('sha512', $info);
    }

    public static function hashEmail($email) {
        return self::hashInfo(strtolower($email));
    }

    public static function generateEmailConfirmToken() {
        return UserAuth::generateAuthToken();
    }

    public static function generatePasscode() {
        $doNotPick = null;
        $lastPicked = null;
        $passcode = '';
        for ($i = 0; $i < 6; $i++) {
            do {
                $pick = rand(1, 9);
            } while ($pick === $doNotPick);

            $doNotPick = $pick === $lastPicked ? $pick : null;
            $lastPicked = $pick;
            $passcode .= $pick;
        }

        return $passcode;
    }

    public static function sendConfirmMail($user_id, $email, $updating = false) {
        $confirm_token = self::generateEmailConfirmToken();
        $cache_key = self::EMAIL_CONFIRM_CACHE_PREFIX . $confirm_token;
        $redis = new \PHPeter\Redis();
        $redis->set($cache_key, $user_id, self::EMAIL_CONFIRM_EXPIRATION_TIME);
        $confirm_link = CLI_BASE_URL . 'confirm/' . $confirm_token;

        $mailer = new \Mailer();
        $mailer->queueMail(
            $email,
            'Confirmation de votre adresse E-Mail',
            'mail/' . ($updating ? 'new' : 'e') . 'mail_confirm',
            ['confirm_link' => $confirm_link]
        );
    }

    public static function authenticateRequest(?array $data = null, ?string $sign = null, bool $mustHaveValidAccount = true): bool {
        if (is_null($data) || is_null($sign) || !is_array($data) || !isset($data['jam_id'], $data['timestamp'])) {
            return false;
        }

        if ($data['timestamp'] + 30 < time()) {
            return false;
        }

        $stringified_data = urlencode(
            is_string($data) && json_decode($data) !== null ?
                $data :
                json_encode($data, JSON_UNESCAPED_UNICODE)
        );

        /**
         * @var \Entity\User $user
         */
        $user = \Persist::readBy('User', 'username', $data['jam_id']);
        if ($user->getPublicKey() === '') {
            \Controller::http423Locked();
            \Controller::renderApiError('This account is locked');
        }

        if ($mustHaveValidAccount && !$user->isActive()) {
            return false;
        }

        /*
         * Verigying data signature
         */
        $verify = \Crypt::verify($stringified_data, base64_decode($sign), $user->getPublicKey());

        return $verify === 1;
    }

}