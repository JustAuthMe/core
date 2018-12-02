<h1>JustAuth.Me</h1>

<h3>You're about to log into <?= $client->getDomain() ?></h3>

<p>Just scan the following QR-Code with your JustAuth.Me mobile App</p>

<img src="<?= $qr_code ?>" alt="login qr code" />