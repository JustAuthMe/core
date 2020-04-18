<?php

use Model\UserAuth;

?>
<div class="header">
    <h1><?= $_GET['app_id'] !== 'ad' ? 'You\'re about to log into ' . $client->getDomain() : 'Vous êtes sur le point de vous connecter à monsupersite.com' ?></h1>
    <p><?= $_GET['app_id'] !== 'ad' ? 'Just scan the following QR-Code with your JustAuthMe mobile App' : 'Scannez simplement le QR-Code ci-dessous avec votre application JustAuthMe' ?></p>
</div>
<div class="centered">
    <img class="qrcode" src="<?= $qr_code ?>" alt="login qr code" />
    <?php
    $detect = new Mobile_Detect();
    if ($detect->isMobile() || $detect->isTablet()) {
    ?>
    <a href="<?= UserAuth::URL_SCHEME . $auth->getToken() ?>" class="btn-mobile">Open in JustAuthMe app</a>
    <?php } ?>
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
