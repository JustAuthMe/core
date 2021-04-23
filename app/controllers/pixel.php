<?php
const PIXEL_SIGNING_KEY = 'aMJn6AbtUc855uL7BMdPwuMhRBU4WjuqNTtrHo58lGA2jz3jazn1Z/Y5Y+Pt';
const PIXEL_CACHE_PREFIX = 'pixel_';
const PIXEL_CACHE_TTL = 86400; // 24 hours

$base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFBQIAX8jx0gAAAABJRU5ErkJggg==';
$not_allowed_ips = ['45.155.168.240', '185.216.24.254'];

if (!in_array($_SERVER['REMOTE_ADDR'], $not_allowed_ips) && isset($_GET['from'], $_GET['to'], $_GET['time'], $_GET['key'])) {
    $calculated_signature = hash_hmac('sha256', $_GET['from'] . '.' . $_GET['to'] . '.' . $_GET['time'], PIXEL_SIGNING_KEY);

    $redis = new \PHPeter\Redis();
    $cache_key = PIXEL_CACHE_PREFIX . $calculated_signature;
    $cached = $redis->get($cache_key);
    if ($cached === false && $calculated_signature === $_GET['key'] && preg_match("#\@justauth\.me$#", $_GET['from'])) {
        $message = $_GET['to'] . ' opened an e-mail ' . date('Y-m-d \a\t H:i:s', Utils::time());
        $mailer = new Mailer();
        $mailer->queueMail(
            $_GET['from'],
            $message,
            'mail/blank',
            ['body' => $message]
        );
        $redis->set($cache_key, 1, PIXEL_CACHE_TTL);
        $base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    }
}

header('Content-Type: image/png');
echo base64_decode($base64);
die;