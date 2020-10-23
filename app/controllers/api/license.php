<?php

use Entity\License;

if (!Utils::isJamInternal()) {
    Controller::http401Unauthorized();
    Controller::renderApiError('Authentication failed');
}

/** @var License $license_key */

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (Request::get()->getArg(2) === '') {
            Controller::http400BadRequest();
            Controller::renderApiError('License key is required');
        }

        $key = Request::get()->getArg(2);
        if (!Persist::exists('License', 'license_key', $key)) {
            Controller::http404NotFound();
            Controller::renderApiError('License key not found');
        }

        $license_key = Persist::readBy('License', 'license_key', $key);
        if ($license_key->isUsed()) {
            Controller::http409Conflict();
            Controller::renderApiError('This license key as already been used');
        }

        Data::get()->add('license', $license_key);
        Controller::renderApiSuccess();
        break;

    case 'PUT':
        if (Request::get()->getArg(2) === '') {
            Controller::http400BadRequest();
            Controller::renderApiError('License key is required');
        }

        $key = Request::get()->getArg(2);
        if (!Persist::exists('License', 'license_key', $key)) {
            Controller::http404NotFound();
            Controller::renderApiError('License key not found');
        }

        $license_key = Persist::readBy('License', 'license_key', $key);
        $license_key->setUsedAt(date('Y-m-d H:i:s'));
        Persist::update($license_key);
        Controller::renderApiSuccess();
        break;

    default:
        Controller::http405MethodNotAllowed();
        Controller::renderApiError('Only GET and PUT methods are allowed');
}

