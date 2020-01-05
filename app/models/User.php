<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:23
 */

namespace Model;


class User {
    const EMAIL_CONFIRM_CACHE_PREFIX = 'confirm_';
    const EMAIL_CONFIRM_EXPIRATION_TIME = 86400; // 24 hours
    const EMAIL_CONFIRM_COOLDOWN = 600; // 10 minutes
    const APPLOGIN_CACHE_PREFIX = 'applogin_';
    const APPLOGIN_EXPIRATION_TIME = 600; // 10 minutes
    const APPLOGIN_COOLDOWN = 120; // 2 minutes

    public static function generateUsername() {
        do {
            $username = bin2hex(openssl_random_pseudo_bytes(32));
        } while (\Persist::exists('User', 'username', $username));

        return $username;
    }

    public static function hashInfo($info) {
        return hash('sha512', $info);
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

            $doNotPick = $lastPicked;
            $lastPicked = $pick;
            $passcode .= $pick;
        }

        return $passcode;
    }

    public static function sendConfirmMail($user_id, $email) {
        $confirm_token = self::generateEmailConfirmToken();
        $cache_key = self::EMAIL_CONFIRM_CACHE_PREFIX . $confirm_token;
        $redis = new \PHPeter\Redis();
        $redis->set($cache_key, $user_id, self::EMAIL_CONFIRM_EXPIRATION_TIME);
        $confirm_link = BASE_URL . 'confirm/' . $confirm_token;

        $mailer = new \Mailer();
        $mailer->queueMail(
            $email,
            'E-Mail address confirmation',
            'mail/email_confirm',
            ['confirm_link' => $confirm_link]
        );
    }
}