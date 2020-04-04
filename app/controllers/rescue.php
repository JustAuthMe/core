<?php

use Model\User;

switch (\Request::get()->getArg(1)) {
    case 'challenge':
        if (POST && isset($_POST['email'])) {
            if (isset($_POST['passcode'])) {
                $postdata = http_build_query([
                    'email' => $_POST['email'],
                    'passcode' => $_POST['passcode'],
                    'pubkey' => ''
                ]);
                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => $postdata,
                        'ignore_errors' => true
                    ]
                ]);
                $response = json_decode(file_get_contents(SELF_API_URL . 'applogin/challenge', false, $context));
                $status = (int) substr($http_response_header[0], 9, 3);

                switch ($status) {
                    case 200:
                        \Controller::renderView('rescue/success', 'rescue/rescueView.php');
                        die;

                    case 403:
                        Data::get()->add('error', 'Wrong passcode.');
                        break;

                    case 429:
                        Data::get()->add('error', 'You have tried too any times. Please wait a few minutes.');
                        break;

                    default:
                        Data::get()->add('error', 'Unknow error, please contact <a href="mailto:support@justauth.me">support@justauth.me</a>.');
                }

                Data::get()->add('email', htmlentities($_POST['email']));
                \Controller::renderView('rescue/passcode', 'rescue/rescueView.php');
                die;
            }

            $postdata = http_build_query([
                'email' => $_POST['email']
            ]);
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $postdata,
                    'ignore_errors' => true
                ]
            ]);
            $response = json_decode(file_get_contents(SELF_API_URL . 'applogin/request?lock', false, $context));
            $status = (int) substr($http_response_header[0], 9, 3);

            $error = 'unknow';
            switch ($status) {
                case 200:
                    Data::get()->add('email', htmlentities($_POST['email']));
                    \Controller::renderView('rescue/passcode', 'rescue/rescueView.php');
                    die;

                case 400:
                case 404:
                    $error = 'email';
                    break;

                case 410:
                    $error = 'updated';
                    $_SESSION['rescue_updated'] = $response->updated_at;
                    break;

                case 423:
                    $error = 'locked';
                    break;

                case 429:
                    $error = strpos($response->message, 'code') !== false ? 'sent' : 'spam';
                    break;
            }
        }

        if (isset($error)) {
            $_SESSION['rescue_error'] = $error;
        }
        header('location: ' . WEBROOT . 'rescue');
        die;

    default:
        if (isset($_SESSION['rescue_error'])) {
            switch ($_SESSION['rescue_error']) {
                case 'email':
                    Data::get()->add('error', 'Unknow E-Mail.');
                    break;

                case 'updated':
                    Data::get()->add('error', '
                        Your account has had it\'s E-Mail address updated' .
                        (isset($_SESSION['rescue_updated']) ? ' at <strong>' . date('Y/m/d H:i:s', $_SESSION['rescue_updated']) . '</strong>' : '') . '.
                        If this update doesn\'t come from you, contact <a href="mailto:support@justauth.me">rescue@justauth.me</a>
                        as soon as possible!
                    ');
                    break;

                case 'spam':
                    Data::get()->add('error', 'You have tried too any times. Please wait a few minutes.');
                    break;

                case 'sent':
                    Data::get()->add('error', 'Please wait at least 2 minutes before asking for another code.');
                    break;

                case 'locked':
                    Data::get()->add('error', 'This account is already locked.');
                    break;

                default:
                    Data::get()->add('error', 'Unknow error, please contact <a href="mailto:support@justauth.me">support@justauth.me</a>.');
            }

            unset($_SESSION['rescue_error']);
        }

        \Controller::renderView('rescue/email', 'rescue/rescueView.php');
}