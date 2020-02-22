<?php

use Model\Alert;

if (POST) {
    if (!Utils::isJamConsole()) {
        Controller::http401Unauthorized();
        Controller::renderApiError('Authentication failed');
    }

    if (!isset($_POST['alert_type'], $_POST['alert_text'])) {
        Controller::http400BadRequest();
        Controller::renderApiError('alert_type and alert_text are required');
    }

    if (!in_array($_POST['alert_type'], Alert::ALERT_TYPES)) {
        Controller::http400BadRequest();
        Controller::renderApiError('alert_type must be "info" or "warning"');
    }

    if (isset($_POST['alert_ttl']) && !is_numeric($_POST['alert_ttl'])) {
        Controller::http400BadRequest();
        Controller::renderApiError('The TTL must be a number of seconds');
    }

    $to_cache = json_encode([
        'type' => $_POST['alert_type'],
        'text' => htmlentities($_POST['alert_text'])
    ]);
    $ttl = $_POST['alert_ttl'] ?? Alert::ALERT_MINIMUM_TTL;

    $redis = new \PHPeter\Redis();
    $redis->set(Alert::ALERT_CACHE_KEY, $to_cache, $ttl);

    Controller::renderApiSuccess();
    die;
}

$redis = new \PHPeter\Redis();
$cached = $redis->get(Alert::ALERT_CACHE_KEY);

if ($cached === false) {
    Controller::http404NotFound();
    Controller::renderApiError('There is currently no alert on JAM App');
}

Data::get()->add('alert', $cached);
Controller::renderApiSuccess();
