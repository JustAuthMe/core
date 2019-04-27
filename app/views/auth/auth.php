
	<div class="container">
		<div class="login">
			<h1>You're about to log into alpha.justauth.me/demo</h1>
			<p>Just scan the following QR-Code with your JustAuth.Me mobile App</p>
			<img class="qrcode" src="<?= $qr_code ?>" alt="login qr code" />
			<form style="display:none" id="submit_form"></form>
		</div>
		
		<div class="footer">
			<img src="assets/img/logo.png" alt="JustAuthMe Logo" />
		</div>
	</div>
	<script type="text/javascript">
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
	</script>