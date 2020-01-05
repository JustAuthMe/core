<?php
if (!isset($_POST['email']) || !Persist::exists('User', 'uniqid', \Model\User::hashInfo($_POST['email']))) {
    Controller::renderApiError('Unknow E-Mail address');
}

Controller::renderApiSuccess();