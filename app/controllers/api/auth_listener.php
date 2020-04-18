<?php
if (!isset($_GET['token'])) {
    Controller::http400BadRequest();
    Controller::renderApiError('Token required');
}

if (!Persist::exists('UserAuth', 'token', $_GET['token'])) {
    Controller::http404NotFound();
    Controller::renderApiError('No such token');
}

/** @var \Entity\UserAuth $user_auth */
$user_auth = Persist::readBy('UserAuth', 'token', $_GET['token']);
if (!in_array($user_auth->client_app->getAppId(), ['jam_admin', 'jam_console'])) {
    Controller::http403Forbidden();
    Controller::renderApiError('This feature is still in internal beta test');
}

header('Content-Type: text/javascript;charset=utf-8');
?>
const conn = new WebSocket('<?= WEBSOCKET_SOCKET_REMOTE ?>');
conn.onopen = e => {
    const msg = {
        'type': 'await',
        'auth_id': '<?= $user_auth->getToken() ?>'
    };
    conn.send(JSON.stringify(msg));
};

conn.onmessage = e => {
    const data = JSON.parse(e.data);

    if (data.type && data.type === 'data') {
        document.location.href = '<?= $user_auth->getCallbackUrl() . (strpos($user_auth->getCallbackUrl(), '?') !== false ? '&' : '?') . 'access_token=' ?>' + data.data['access_token'];
        conn.close();
    }
};

conn.onerror = e => {};
conn.onclose = e => {};