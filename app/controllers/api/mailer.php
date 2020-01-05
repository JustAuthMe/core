<?php
if (isset($_GET['render_key']) && $_GET['render_key'] === EMAIL_RENDERING_KEY) {
    $cache_key = Request::get()->getArg(2);
    $redis = new \PHPeter\Redis();
    $cached = $redis->get($cache_key);

    if ($cached === false) {
        Controller::http404NotFound();
        Controller::renderApiError('E-Mail not found');
    }

    Data::get()->setData((array) $cached->params);
    Controller::renderView($cached->template, false);
    die;
}

if (!Utils::isJamConsole()) {
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
    Controller::renderApiError('The destination E-Mail address lust be valid');
}

$params = [
    'subject' => Utils::secure($_POST['subject']),
    'body' => $_POST['body']
];
switch (Request::get()->getArg(2)) {
    case 'default':
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

        $template = 'mail/default';
        break;

    default:
        Controller::http404NotFound();
        Controller::renderApiError('This template does not exists');
}

$mailer = new Mailer();
$queue_id = $mailer->queueMail($_POST['to'], $_POST['subject'], $template, $params);

Data::get()->add('queue_id', $queue_id);
Controller::renderApiSuccess();
