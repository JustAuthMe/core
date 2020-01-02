<?php
if (!Utils::isJamConsole()) {
    Controller::http401Unauthorized();
    Controller::renderApiError('Authentication failed');
}

/** @var \Entity\ClientApp $client_app */

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (!isset($_POST['domain'], $_POST['name'], $_POST['logo'], $_POST['redirect_url'], $_POST['data'])) {
            Controller::http400BadRequest();
            Controller::renderApiError('You must provide domain, name, logo, redirect_url and data set');
        }

        if ($_POST['domain'] === '' || $_POST['name'] === '' || $_POST['redirect_url'] === '' || $_POST['data'] === '') {
            Controller::http400BadRequest();
            Controller::renderApiError('domain, name, redirect_url and data set have to be filled');
        }

        if (!filter_var($_POST['domain'], FILTER_VALIDATE_DOMAIN)) {
            Controller::http400BadRequest();
            Controller::renderApiError('Domain name is invalid');
        }

        if (Persist::exists('ClientApp', 'domain', $_POST['domain'])) {
            Controller::http409Conflict();
            Controller::renderApiError('An app with this domain name already exists');
        }

        if (Persist::exists('ClientApp', 'name', $_POST['name'])) {
            Controller::http409Conflict();
            Controller::renderApiError('An app with this name already exists');
        }

        if (!filter_var($_POST['redirect_url'], FILTER_VALIDATE_URL)) {
            Controller::http400BadRequest();
            Controller::renderApiError('Redirection URL is invalid');
        }

        if (!preg_match("#^https:\/\/" . addslashes($_POST['domain']) . "(\/.*)?$#", $_POST['redirect_url'])) {
            Controller::http400BadRequest();
            Controller::renderApiError('The redirection URL must be https and must be under the same domain');
        }

        if (Persist::exists('ClientApp', 'redirect_url', $_POST['domain'])) {
            Controller::http409Conflict();
            Controller::renderApiError('An app with this redirection URL already exists');
        }

        if (json_decode($_POST['data']) === NULL) {
            Controller::http400BadRequest();
            Controller::renderApiError('Data set is not a valid JSON');
        }

        $app_id = \Model\ClientApp::generateAppId($_POST['domain']);
        $logo = $_POST['logo'] === 'undefined' ? '' : $_POST['logo'];
        $secret = \Model\ClientApp::generateSecret();
        $hash_key = \Model\ClientApp::generateHashKey();

        $client_app = new \Entity\ClientApp(
            0,
            $_POST['domain'],
            $app_id,
            $_POST['name'],
            $logo,
            $_POST['redirect_url'],
            $_POST['data'],
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
            $new_secret = \Model\ClientApp::generateSecret();
            $client_app->setSecret($new_secret);
            Persist::update($client_app);

            Data::get()->add('client_app', $client_app);
            Controller::renderApiSuccess();
        }

        if (isset($_POST['name']) && $_POST['name'] !== '') {
            if (Persist::exists('ClientApp', 'name', $_POST['name']) ) {
                Controller::http409Conflict();
                Controller::renderApiError('An app with this name already exists');
            }

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

            if (!preg_match("#^https:\/\/" . addslashes($client_app->getDomain()) . "(\/.*)?$#", $_POST['redirect_url'])) {
                Controller::http400BadRequest();
                Controller::renderApiError('The redirection URL must be https and must be under the same domain');
            }

            if (Persist::exists('ClientApp', 'redirect_url', $_POST['redirect_url'])) {
                Controller::http409Conflict();
                Controller::renderApiError('An app with this name already exists');
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