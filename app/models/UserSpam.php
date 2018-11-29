<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 29/11/2018
 * Time: 20:30
 */

namespace Model;


class UserSpam {
    const BAN_DURATION = 30; // In seconds

    public static function banIp(string $ip_address, int $user_id) {
        $user_spam = new \Entity\UserSpam(
            0,
            $user_id,
            null,
            $ip_address
        );
        \Persist::create($user_spam);
    }

    public static function isIpBanned(string $ip_address): bool {
        /**
         * @var \Entity\UserSpam $user_spam
         */

        if (\Persist::exists('UserSpam', 'ip_address', $ip_address)) {
            $user_spam = \Persist::readBy('UserSpam', 'ip_address', $ip_address);
            return strtotime($user_spam->getTimestamp()) + self::BAN_DURATION >= \Utils::time();
        }

        return false;
    }

    public static function flushOutdatedBans(): void {
        \DB::get()->query("DELETE FROM user_spam WHERE timestamp + " . self::BAN_DURATION . " < CURRENT_TIMESTAMP()");
    }
}