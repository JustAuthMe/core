<?php
if (!POST) {
    Controller::http405MethodNotAllowed();
    Controller::renderApiError('Only POST requests are allowed');
}

if (isset($_POST['email'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('E-Mail is required');
}

$hashed_email = \Model\User::hashInfo($_POST['email']);

if (!Persist::exists('User', 'uniqid', $hashed_email)) {
    Controller::http404NotFound();
    Controller::renderApiError('User not found');
}

$user = Persist::readBy('User', 'uniqid', $hashed_email);
\Model\User::sendConfirmMail($user->getId(), $_POST['email']);

// TODO user based cooldown

Controller::renderApiSuccess();
