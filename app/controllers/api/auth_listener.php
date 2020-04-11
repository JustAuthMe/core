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
const form = document.createElement('form');
form.style.display = 'none';
form.method = 'post';
form.action = '<?= $user_auth->getCallbackUrl() ?>';
document.body.append(form);

const conn = new WebSocket('<?= WEBSOCKET_SOCKET_REMOTE ?>');
conn.onopen = function (e) {
    var msg = {
        'type': 'await',
        'auth_id': '<?= $user_auth->getToken() ?>'
    };
    conn.send(JSON.stringify(msg));
};

conn.onmessage = function (e) {
    var data = JSON.parse(e.data);
    if (data.type && data.type === 'data') {
        for (var i in data.data) {
            var input = document.createElement('input');
            input.type = 'text';
            input.name = i;
            input.value = data.data[i];
            form.appendChild(input);
        }

        form.submit();
        conn.close();
    }
};

conn.onerror = function (e) {
};
conn.onclose = function (e) {
};