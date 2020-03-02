<?php

use Model\Alert;

if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE']) && !Utils::isJamConsole()) {
    Controller::http401Unauthorized();
    Controller::renderApiError('Authentication failed');
}

$redis = new \PHPeter\Redis();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
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
            'id' => time(),
            'type' => $_POST['alert_type'],
            'text' => htmlentities($_POST['alert_text'])
        ]);
        $ttl = $_POST['alert_ttl'] ?? Alert::ALERT_MINIMUM_TTL;

        $redis->set(Alert::ALERT_CACHE_KEY, $to_cache, $ttl);
        Controller::renderApiSuccess();
        die;

    case 'DELETE':
        $redis->del(Alert::ALERT_CACHE_KEY);
        Controller::renderApiSuccess();
}

$cached = $redis->get(Alert::ALERT_CACHE_KEY);

if ($cached === false) {
    Controller::http404NotFound();
    Controller::renderApiError('There is currently no alert on JAM App');
}

Data::get()->add('alert', $cached);
Controller::renderApiSuccess();
