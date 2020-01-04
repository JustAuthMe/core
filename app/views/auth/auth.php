<div class="header">
    <h1><?= $_GET['app_id'] !== 'ad' ? 'You\'re about to log into ' . $client->getDomain() : 'Vous êtes sur le point de vous connecter à monsupersite.com' ?></h1>
    <p><?= $_GET['app_id'] !== 'ad' ? 'Just scan the following QR-Code with your JustAuthMe mobile App' : 'Scannez simplement le QR-Code ci-dessous avec votre application JustAuthMe' ?></p>
</div>
<div class="centered">
    <img class="qrcode" src="<?= $qr_code ?>" alt="login qr code" />
    <form style="display:none" id="submit_form"></form>
    <a href="<?= \Model\UserAuth::URL_SCHEME . $auth->getToken() ?>" style="display: block;margin-top: 30px;color: white; font-size: 14px">You are using your mobile device ? Click here to login directly via our app</a>
</div>
<script type="text/javascript">
    <?php if ($_GET['app_id'] !== 'ad'): ?>
    var form = document.getElementById('submit_form');
    form.method = 'post';
    form.action = '<?= $auth->getCallbackUrl() ?>';
    var conn = new WebSocket('<?= WEBSOCKET_SOCKET_REMOTE ?>');
    conn.onopen = function(e) {
        console.log("Connection established!");
        var msg = {
            'type': 'await',
            'auth_id': '<?= $auth->getToken() ?>'
        };
        console.log('Message sent', msg);
        conn.send(JSON.stringify(msg));
    };

    conn.onmessage = function(e) {
        var data = JSON.parse(e.data);
        console.log('Message received', data);
        if (data.type && data.type === 'data') {
            for (var i in data.data) {
                var input = document.createElement('input');
                input.type = 'text';
                input.name = i;
                input.value = data.data[i];
                form.appendChild(input);
            }

            console.log(form);
            form.submit();
            conn.close();
        }
    };

    conn.onerror = function(e) {
        console.log(e);
    };

    conn.onclose = function(e) {
        console.log('Connection closed');
    };
    <?php endif ?>
</script>