<?php
const PIXEL_SIGNING_KEY = 'aMJn6AbtUc855uL7BMdPwuMhRBU4WjuqNTtrHo58lGA2jz3jazn1Z/Y5Y+Pt';

if (isset($_GET['from'], $_GET['to'], $_GET['key'])) {
    $calculated_signature = hash_hmac('sha256', $_GET['from'] . '>' . $_GET['to'], PIXEL_SIGNING_KEY);
    if ($calculated_signature === $_GET['key'] && preg_match("#\@justauth\.me$#", $_GET['from'])) {
        $mailer = new Mailer();
        $mailer->queueMail(
            $_GET['from'],
            $_GET['to'] . ' opened an e-mail ' . date('Y-m-d \a\t H:i:s'),
            'mail/blank'
        );
    }
}

header('Content-Type: image/png');
echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
die;