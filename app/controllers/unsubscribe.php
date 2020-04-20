<?php

use Model\User;

if (!isset($_GET['email'], $_GET['key'])) {
    Controller::http404NotFound();
}

if (User::hashInfo($_GET['email'] . UNSUBSCRIBE_SALT) !== $_GET['key']) {
    Controller::http403Forbidden();
}

if (Persist::exists('Customer', 'email', $_GET['email'])) {
    Persist::deleteBy('Customer', 'email', $_GET['email']);
}

Data::get()->add('email', $_GET['email']);
Controller::renderView('unsubscribe/unsubscribe');