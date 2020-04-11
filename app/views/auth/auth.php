<div class="header">
    <h1><?= $_GET['app_id'] !== 'ad' ? 'You\'re about to log into ' . $client->getDomain() : 'Vous êtes sur le point de vous connecter à monsupersite.com' ?></h1>
    <p><?= $_GET['app_id'] !== 'ad' ? 'Just scan the following QR-Code with your JustAuthMe mobile App' : 'Scannez simplement le QR-Code ci-dessous avec votre application JustAuthMe' ?></p>
</div>
<div class="centered">
    <img class="qrcode" src="<?= $qr_code ?>" alt="login qr code" />
    <form style="display:none" id="submit_form"></form>
    <?php
    $detect = new Mobile_Detect;
    if ($detect->isMobile() || $detect->isTablet()) {
    ?>
    <a href="<?= \Model\UserAuth::URL_SCHEME . $auth->getToken() ?>" class="btn-mobile">Open in JustAuthMe app</a>
    <?php } ?>
</div>
<script type="text/javascript">
    <?php if ($_GET['app_id'] !== 'ad'): ?>
    var form = document.getElementById('submit_form');
    form.method = 'post';
    form.action = '<?= $auth->getCallbackUrl() ?>';
    var conn = new WebSocket('<?= WEBSOCKET_SOCKET_REMOTE ?>');
    conn.onopen = function(e) {
        var msg = {
            'type': 'await',
            'auth_id': '<?= $auth->getToken() ?>'
        };
        conn.send(JSON.stringify(msg));
    };

    conn.onmessage = function(e) {
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

    conn.onerror = function(e) {};
    conn.onclose = function(e) {};
    <?php endif ?>
</script>