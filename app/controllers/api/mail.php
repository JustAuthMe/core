<?php

use Entity\UniqidUpdate;
use Model\UniqidUpdate as UniqidUpdateModel;
use Model\User;

switch (Request::get()->getArg(2)) {
    case 'check':
        // USELESS ENDPOINT BUT STILL HERE IF NEEDED...
        if (!Utils::isJamConsole()) {
            Controller::http401Unauthorized();
            Controller::renderApiError('You are not allowed to access this endpoint');
        }

        if (!POST) {
            Controller::http405MethodNotAllowed();
            Controller::renderApiError('Only POST requests are allowed');
        }

        if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            Controller::http400BadRequest();
            Controller::renderApiError('Invalid E-Mail address');
        }

        $redis = new \PHPeter\Redis();
        $cache_key = User::EMAIL_CHECK_CACHE_PREFIX . Utils::slugifyIp(
                Utils::truncateIPV6(
                    $_SERVER['REMOTE_ADDR'],
                    4
                )
            );
        $attempts = (int) $redis->get($cache_key);
        $new_ttl = $attempts > 0 ? $redis->ttl($cache_key) : User::EMAIL_CHECK_COOLDOWN;

        $attempts++;

        if ($attempts > 5) {
            Controller::http429TooManyRequests();
            Controller::renderApiError('You have tried to many times. Please wait a few minutes.');
        }

        $is_available = !Persist::exists('User', 'uniqid', User::hashInfo($_POST['email']));
        if (!$is_available) {
            $redis->set($cache_key, $attempts, $new_ttl);
        }

        Data::get()->add('available', $is_available);
        Controller::renderApiSuccess();
        break;

    case 'confirm':
        if (!POST) {
            Controller::http405MethodNotAllowed();
            Controller::renderApiError('Only POST requests are allowed');
        }

        if (!isset($_POST['email'])) {
            Controller::http400BadRequest();
            Controller::renderApiError('E-Mail is required');
        }

        $hashed_email = User::hashInfo($_POST['email']);

        if (!Persist::exists('User', 'uniqid', $hashed_email)) {
            if (!UniqidUpdateModel::isThereAnActiveUpdateByNewUniqid($hashed_email)) {
                Controller::http404NotFound();
                Controller::renderApiError('User not found');
            }

            /** @var UniqidUpdate $uniqid_update */
            $uniqid_update = UniqidUpdateModel::getActiveUpdateByNewUniqid($hashed_email);
            $user_id = $uniqid_update->getUserId();
        } else {
            /** @var \Entity\User $user */
            $user = Persist::readBy('User', 'uniqid', $hashed_email);
            if ($user->isActive()) {
                /*
                 * False reason for not to give hackers a chance to know which email is registered or not
                 * However, they could call this endpoint with any email, asking for a confirmation email,
                 * waiting for the API to respond "E-Mail already confirmed"
                 */
                Controller::http404NotFound();
                Controller::renderApiError('User not found');
            }

            $user_id = $user->getId();
        }

        $redis = new \PHPeter\Redis();
        $cache_key = User::EMAIL_CONFIRM_CACHE_PREFIX . $hashed_email;
        $cached = $redis->get($cache_key);

        if ($cached !== false) {
            Controller::http429TooManyRequests();
            Controller::renderApiError('Please wait at least 10 minutes before asking for a new confirmation E-Mail.');
        }

        User::sendConfirmMail($user_id, $_POST['email']);
        $redis->set($cache_key, 1, User::EMAIL_CONFIRM_COOLDOWN);

        Controller::renderApiSuccess();
        break;

    case 'update':
        if (!POST) {
            Controller::http405MethodNotAllowed();
            Controller::renderApiError('Only POST requests are allowed');
        }

        if (!isset($_POST['data'], $_POST['data']['email'], $_POST['sign']) || !User::authenticateRequest($_POST['data'], $_POST['sign'], false)) {
            Controller::http401Unauthorized();
            Controller::renderApiError('You\'re not allowed to access this resource');
        }

        $data = is_string($_POST['data']) && json_decode($_POST['data']) !== null ?
            json_decode($_POST['data'], true) :
            $_POST['data'];

        /** @var \Entity\User $user */
        $user = Persist::readBy('User', 'username', $data['jam_id']);
        if ($user->getUniqid() === User::hashInfo($data['email'])) {
            Controller::http400BadRequest();
            Controller::renderApiError('You already have registered this E-Mail');
        }

        if (Persist::exists('User', 'uniqid', User::hashInfo($data['email'])) || UniqidUpdateModel::isThisEmailAlmostTaken($data['email'])) {
            Controller::http409Conflict();
            Controller::renderApiError('This E-Mail is already registered');
        }

        if (UniqidUpdateModel::isThereAnActiveUpdate($user->getId())) {
            UniqidUpdateModel::removeActiveUpdates($user->getId());
        }

        $uniqid_update = new UniqidUpdate(
            0,
            $user->getId(),
            $user->getUniqid(),
            User::hashInfo($data['email']),
            $_SERVER['REMOTE_ADDR'],
            Utils::time(),
            1
        );
        Persist::create($uniqid_update);

        $user->setActive(0);
        Persist::update($user);
        User::sendConfirmMail($user->getId(), $data['email'], true);

        Controller::renderApiSuccess();
        break;

    default:
        Controller::http404NotFound();
        Controller::renderApiError('Resource not found');
}
