<?php

use Entity\EmailQueue;

if (!isset($argc)) {
    Controller::http403Forbidden();
}

if ($argc !== 2) {
    echo 'Usage: php index.php cron' . "\n";
    die;
}

$start_time = time();
$end_time = $start_time + 60;

$redis = new \PHPeter\Redis();
$mailer = new Mailer();

while ($end_time > time()) {
    DB::get()->beginTransaction();
    $req = DB::get()->query("SELECT * FROM email_queue WHERE sent_at IS NULL ORDER BY id LIMIT 1 FOR UPDATE");
    $email = $req->fetch();
    if ($email !== false) {
        $mailer->sendMail($email);
        echo 'Mail #' . $email['id'] . ' sent!' . "\n";
    }
    DB::get()->commit();
}

echo 'Done in ' . (time() - $start_time) . ' seconds.' . "\n";
