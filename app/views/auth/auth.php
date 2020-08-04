<?php

use Model\UserAuth;

?>
<div class="text-center">

    <h1 class="auth-header"><?= L::auth_header($_GET['app_id'] !== 'ad' ? $client->getDomain() : 'monsupersite.com') ?></h1>
    <p><?= L::auth_text ?></p>

    <div>
        <img class="qrcode" src="<?= $qr_code ?>" alt="QR Code login" /><br>
        <?php if ($is_mobile): ?>
            <a href="<?= $_GET['app_id'] !== 'ad' ? UserAuth::URL_SCHEME . $auth->getToken() : 'https://justauth.me' ?>" class="btn btn-primary btn-sm d-block mx-5"><?= L::auth_button ?></a>
        <?php endif ?>
    </div>


</div>
<?php if ($_GET['app_id'] !== 'ad'): ?>
<script type="text/javascript">
    const conn = new WebSocket('<?= WEBSOCKET_SOCKET_REMOTE ?>');
    conn.onopen = e => {
        const msg = {
            'type': 'await',
            'auth_id': '<?= $auth->getToken() ?>'
        };
        conn.send(JSON.stringify(msg));
    };

    conn.onmessage = e => {
        const data = JSON.parse(e.data);

        if (data.type && data.type === 'data') {
            document.location.href = '<?= $auth->getCallbackUrl() . (strpos($auth->getCallbackUrl(), '?') !== false ? '&' : '?') . 'access_token=' ?>' + data.data['access_token'];
            conn.close();
        }
    };

    conn.onerror = e => {};
    conn.onclose = e => {};
</script>
<?php endif ?>
