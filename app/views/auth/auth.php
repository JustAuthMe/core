<h1>JustAuth.Me</h1>

<h3>You're about to log into <?= $client->getDomain() ?></h3>

<p>Just scan the following QR-Code with your JustAuth.Me mobile App</p>

<img src="<?= $qr_code ?>" alt="login qr code" />
<div style="display:none" id="submit_form"></div>
<script type="text/javascript">
    var form = document.getElementById('submit_form');
    form.method = 'post';
    form.action = '<?= $auth->getCallbackUrl() ?>';
    var conn = new WebSocket('ws://localhost:1337');
    conn.onopen = function(e) {
        console.log("Connection established!");
        conn.send(JSON.stringify({
            'type': 'await',
            'auth_id': '<?= $auth->getToken() ?>'
        }));
    };

    conn.onmessage = function(e) {
        var data = JSON.parse(e.data);
        if (data.type && data.type === 'data') {
            for (var i in data.data) {
                var input = document.createElement('input');
                input.type = 'text';
                input.name = i;
                input.value = data.data[i];
            }

            // form.submit();
            // conn.close();
        }
    };

    conn.onerror = function(e) {
        console.log(e);
    }
</script>