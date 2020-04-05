<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends PHPMailer {
    const SEND_AS = 'JustAuth.Me <hello@justauth.me>';
    const CACHE_PREFIX = 'email_';

    public function __construct() {
        parent::__construct(true);


        $this->IsSMTP();
        $this->CharSet = parent::CHARSET_UTF8;
        $this->SMTPDebug  = 0;
        $this->SMTPAuth   = true;
        $this->Host       = SMTP_HOST;
        $this->Port       = SMTP_PORT;
        $this->Username   = SMTP_USER;
        $this->Password   = SMTP_PASS;

    }

    private static function getContactDetailsFromString($contact) {
        if (filter_var(trim($contact), FILTER_VALIDATE_EMAIL)) {
            return [
                'email' => trim($contact),
                'name' => ''
            ];
        }

        $split = explode(' ', $contact);
        $contact_email = trim($split[count($split) - 1], '<>');
        unset($split[count($split) - 1]);
        $contact_name = implode(' ', $split);

        return [
            'email' => $contact_email,
            'name' => $contact_name
        ];
    }

    public function sendMail($cache_key) {
        $redis = new \PHPeter\Redis();
        $cached = $redis->get($cache_key);

        $contact_from = self::getContactDetailsFromString(self::SEND_AS);
        $contact_to = self::getContactDetailsFromString($cached->to);
        array_walk($cached->bcc, function(&$item, $key) {
            $item = self::getContactDetailsFromString($item);
        });

        try {
            $this->isHtml(true);
            $this->setFrom($contact_from['email'], $contact_from['name']);
            $this->addAddress($contact_to['email'], $contact_to['name']);
            foreach ($cached->bcc as $contact) {
                $this->addBCC($contact['email'], $contact['name']);
            }

            $this->Subject = $cached->subject;
            $this->Body = file_get_contents(CLI_BASE_URL . 'api/mailer/' . $cache_key . '?render_key=' . EMAIL_RENDERING_KEY);

            if ($this->send() === false) {
                Logger::logError('PHPMailer error: ' . $this->ErrorInfo);
            }
        } catch (Exception $e) {
            Logger::logError('PHPMailer exception: ' . $e->getMessage());
        }
    }

    public function queueMail($to, $subject, $template = 'mail/default', $params = [], $bcc = []) {
        $redis = new \PHPeter\Redis();
        $cache_key = self::CACHE_PREFIX . ((int) (microtime(true) * 1000));
        $redis->set($cache_key, json_encode([
            'to' => $to,
            'subject' => $subject,
            'template' => $template,
            'params' => $params,
            'bcc' => $bcc
        ]));

        return $cache_key;
    }
}