<?php

use Entity\EmailQueue;
use Model\User;

if (isset($_GET['render_key']) && $_GET['render_key'] === EMAIL_RENDERING_KEY) {
    /** @var EmailQueue $email */
    $email = Persist::read('EmailQueue', Request::get()->getArg(2));

    if ($email === false) {
        Controller::http404NotFound();
        Controller::renderApiError('E-mail not found');
    }

    Data::get()->setData(json_decode($email->getParams(), true));
    Data::get()->add('is_automated', $email->getSender() === '' || $email->getSender() === Mailer::SEND_AS_DEFAULT);
    Data::get()->add('unsubscribe_email', $email->getRecipient());
    Data::get()->add('unsubscribe_key', User::hashInfo(strtolower($email->getRecipient()) . UNSUBSCRIBE_SALT));
    Controller::renderView($email->getTemplate(), null);
    die;
}

if (!Utils::isJamInternal()) {
    Controller::http401Unauthorized();
    Controller::renderApiError('Authentication failed');
}

if (!POST) {
    Controller::http405MethodNotAllowed();
    Controller::renderApiError('Only POST requests are allowed');
}

if (!isset($_POST['to'], $_POST['subject'], $_POST['body']) || $_POST['to'] === '' || $_POST['subject'] === '' || $_POST['body'] === '') {
    Controller::http400BadRequest();
    Controller::renderApiError('To, Subject or Body are missing');
}

if (!filter_var($_POST['to'], FILTER_VALIDATE_EMAIL)) {
    Controller::http400BadRequest();
    Controller::renderApiError('The destination e-mail address must be valid');
}

$bcc = [];
if (isset($_POST['bcc']) && is_array($_POST['bcc'])) {
    $bcc = $_POST['bcc'];
}

$from = Mailer::SEND_AS_DEFAULT;
if (isset($_POST['from']) && $_POST['from'] !== '') {
    $from = $_POST['from'];
}

$params = [
    'subject' => Utils::secure($_POST['subject']),
    'body' => $_POST['body']
];

switch (Request::get()->getArg(2)) {
    case 'default':
    case 'default_fr':
        if (isset($_POST['call_to_action'])) {
            if (
                !isset($_POST['call_to_action']['title'], $_POST['call_to_action']['link']) ||
                $_POST['call_to_action']['title'] === '' || $_POST['call_to_action']['link'] === ''
            ) {
                Controller::http400BadRequest();
                Controller::renderApiError('Bad Call-to-action format');
            }

            $params['call_to_action'] = Utils::secure($_POST['call_to_action']);
        }

        $template = Request::get()->getArg(2) === 'default_fr' ? 'mail/fr/default' : 'mail/default';
        break;

    default:
        Controller::http404NotFound();
        Controller::renderApiError('This template does not exists');
}

$mailer = new Mailer();
$mailer->queueMail($_POST['to'], $_POST['subject'], $template, $params, $bcc, $from);

Controller::renderApiSuccess();
