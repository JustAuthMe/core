<?php
if (!isset($argc)) {
    Controller::http403Forbidden();
}

if ($argc !== 2) {
    echo 'Usage: php index.php cron' . "\n";
    die;
}

$redis = new \PHPeter\Redis();
$cached_keys = $redis->keys(Mailer::CACHE_PREFIX . '*');
$mailer = new Mailer();

foreach ($cached_keys as $cached_key) {
    $mailer->sendMail($cached_key);
    echo 'Mail ' . $cached_key . ' sent!' . "\n";
    $redis->del($cached_key);
}

echo 'Done.' . "\n";
