<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 21:19
 */

Controller::sendNoCacheHeaders();
\Model\UserAuth::flushOutdatedAuths();

if (Request::get()->getArg(2) === '') {
    Controller::http400BadRequest();
    Controller::renderApiError('No token provided');
}

$token = Request::get()->getArg(2);

/**
 * @var \Entity\UserAuth $auth
 */

if (!Persist::exists('UserAuth', 'token', $token)) {
    Controller::http404NotFound();
    Controller::renderApiError('No such token');
}

$auth = Persist::readBy('UserAuth', 'token', $token);

Data::get()->add('auth', $auth);
Controller::renderApiSuccess();
