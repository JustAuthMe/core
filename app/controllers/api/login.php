<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 02/12/2018
 * Time: 12:04
 */

if (Request::get()->getArg(2) === '') {
    Controller::error400BadRequest();
    Controller::renderApiError('No token provided');
}

$token = Request::get()->getArg(2);

/**
 * @var \Entity\UserAuth $auth
 */

if (!Persist::exists('UserAuth', 'token', $token)) {
    Controller::error404NotFound();
    Controller::renderApiError('No such token');
}

$auth = Persist::readBy('UserAuth', 'token', $token);

if (!isset($_POST['data']) || !is_array($_POST['data'])) {
    Controller::error400BadRequest();
    Controller::renderApiError('No data given');
}

$data = json_decode($auth->getData());

foreach ($data as $d) {
    if (\Model\UserAuth::isDataRequired($d) && !isset($_POST['data'][\Model\UserAuth::getDataSlug($d)])) {
        Controller::error400BadRequest();
        Controller::renderApiError('Missing param ' . $d);
    }
}


