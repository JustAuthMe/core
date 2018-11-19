<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 21:19
 */

if (Request::get()->getArg(2) === '') {
    Controller::error400BadRequest();
    Controller::renderApiError('No token provided');
}

$token = Request::get()->getArg(2);

/**
 * @var \Entity\UserAuth $auth
 */
$auth = Persist::readBy('UserAuth', 'token', $token);

Data::get()->add('auth', $auth);
Controller::renderApiSuccess();
