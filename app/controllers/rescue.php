<?php

switch (\Request::get()->getArg(1)) {
    case 'check':
        if (POST) {
            \Controller::renderView('rescue/success', 'rescue/rescueView.php');
            die;
        }

        \Controller::renderView('rescue/check', 'rescue/rescueView.php');
        break;

    default:
        if (POST) {
            header('location: ' . WEBROOT . 'rescue/check');
            die;
        }

        \Controller::renderView('rescue/email', 'rescue/rescueView.php');
}