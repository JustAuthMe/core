<?php

use Entity\ClientApp;
use Model\ClientApp as ClientAppModel;

if (!Utils::isJamInternal()) {
    Controller::http401Unauthorized();
    Controller::renderApiError('Authentication failed');
}

/** @var ClientApp $client_app */

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (!isset($_POST['url'], $_POST['name'], $_POST['logo'], $_POST['redirect_url'], $_POST['data'])) {
            Controller::http400BadRequest();
            Controller::renderApiError('You must provide url, name, logo, redirect_url and data set');
        }

        if ($_POST['url'] === '' || $_POST['name'] === '' || $_POST['redirect_url'] === '' || $_POST['data'] === '') {
            Controller::http400BadRequest();
            Controller::renderApiError('url, name, redirect_url and data set have to be filled');
        }

        if (!filter_var($_POST['url'], FILTER_VALIDATE_URL) || strpos($_POST['url'], 'https://') !== 0) {
            Controller::http400BadRequest();
            Controller::renderApiError('URL is invalid or does not begin with https://');
        }

        $url = trim($_POST['url'], '/');
        if (Persist::exists('ClientApp', 'url', $url)) {
            Controller::http409Conflict();
            Controller::renderApiError('An app with this url already exists');
        }

        if (!filter_var($_POST['redirect_url'], FILTER_VALIDATE_URL)) {
            Controller::http400BadRequest();
            Controller::renderApiError('Redirection URL is invalid');
        }

        if (!preg_match("#^" . addslashes($url) . "(\/.*)?$#", $_POST['redirect_url'])) {
            Controller::http400BadRequest();
            Controller::renderApiError('The redirection URL must be https and must be under the same base url');
        }

        if (json_decode($_POST['data']) === NULL) {
            Controller::http400BadRequest();
            Controller::renderApiError('Data set is not a valid JSON');
        }

        $app_id = ClientAppModel::generateAppId($_POST['url']);
        $logo = $_POST['logo'] === 'undefined' ? '' : $_POST['logo'];
        $secret = ClientAppModel::generateSecret();
        $hash_key = ClientAppModel::generateHashKey();

        $client_app = new ClientApp(
            0,
            $url,
            parse_url($url, PHP_URL_HOST),
            $app_id,
            $_POST['name'],
            $logo,
            $_POST['redirect_url'],
            $_POST['data'],
            isset($_POST['dev']) && $_POST['dev'] == 1 ? 1 : 0,
            '',
            $secret,
            $hash_key
        );

        $ca_id = Persist::create($client_app);
        $client_app->setId($ca_id);

        Data::get()->add('client_app', $client_app);
        Controller::renderApiSuccess();
        break;

    case 'GET':
        if (!Persist::exists('ClientApp', 'id', Request::get()->getArg(2))) {
            if (isset($_GET['list'])) {
                if (preg_match("#^[0-9]+(\,[0-9]+)+$#", $_GET['list'])) {
                    $ids = explode(',', $_GET['list']);
                    Data::get()->add('client_apps', Persist::fetchAll('ClientApp', "WHERE id IN (" . $_GET['list'] . ")"));
                    Controller::renderApiSuccess();
                }

                Controller::http400BadRequest();
                Controller::renderApiError('Wrong list format');
            } elseif (isset($_GET['url'])) {
                $url = trim($_GET['url'], '/');
                if (Persist::exists('ClientApp', 'url', $url)) {
                    $client_app = Persist::readBy('ClientApp', 'url', $url);
                    Data::get()->add('client_app', $client_app);
                    Controller::renderApiSuccess();
                }
            }

            Controller::http404NotFound();
            Controller::renderApiError('Client App not found');
        }

        $client_app = Persist::read('ClientApp', Request::get()->getArg(2));
        Data::get()->add('client_app', $client_app);
        Controller::renderApiSuccess();
        break;

    case 'PUT';
        if (!Persist::exists('ClientApp', 'id', Request::get()->getArg(2))) {
            Controller::http404NotFound();
            Controller::renderApiError('Client App not found');
        }

        $client_app = Persist::read('ClientApp', Request::get()->getArg(2));
        if (Request::get()->getArg(3) === 'revoke_secret') {
            $new_secret = ClientAppModel::generateSecret();
            $client_app->setSecret($new_secret);
            Persist::update($client_app);

            Data::get()->add('client_app', $client_app);
            Controller::renderApiSuccess();
        }

        if (isset($_POST['name']) && $_POST['name'] !== '') {
            $client_app->setName($_POST['name']);
        }

        if (isset($_POST['logo']) && $_POST['logo'] !== '') {
            $client_app->setLogo($_POST['logo']);
        }

        if (isset($_POST['redirect_url']) && $_POST['redirect_url'] !== '') {
            if (!filter_var($_POST['redirect_url'], FILTER_VALIDATE_URL)) {
                Controller::http400BadRequest();
                Controller::renderApiError('Redirection URL is invalid');
            }

            if (!preg_match("#^" . addslashes($client_app->getUrl()) . "(\/.*)?$#", $_POST['redirect_url'])) {
                Controller::http400BadRequest();
                Controller::renderApiError('The redirection URL must be https and must be under the same base url');
            }

            $client_app->setRedirectUrl($_POST['redirect_url']);
        }

        if (isset($_POST['data']) && $_POST['data'] !== '') {
            if (json_decode($_POST['data']) === NULL) {
                Controller::http400BadRequest();
                Controller::renderApiError('Data set is not a valid JSON');
            }

            $client_app->setData($_POST['data']);
        }

        if (isset($_POST['dev'])) {
            $client_app->setDev($_POST['dev'] == 1 ? 1 : 0);
        }

        Persist::update($client_app);
        Data::get()->add('client_app', $client_app);
        Controller::renderApiSuccess();
        break;

    case 'DELETE':
        if (!Persist::exists('ClientApp', 'id', Request::get()->getArg(2))) {
            Controller::http404NotFound();
            Controller::renderApiError('Client App not found');
        }

        $client_app = Persist::read('ClientApp', Request::get()->getArg(2));
        Persist::delete($client_app);
        Controller::renderApiSuccess();
        break;

    case 'OPTIONS':
        Controller::renderApiSuccess();
        break;

    default:
        Controller::http405MethodNotAllowed();
        Controller::renderApiError('Only POST, GET, PUT, DELETE and OPTIONS methods are allowed');
        break;
}
