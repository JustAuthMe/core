<?php

use Entity\EmailQueue;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends PHPMailer {
    const SEND_AS_DEFAULT = 'JustAuthMe <hello@justauth.me>';
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

    public function sendMail(array $email) {
        $sender = $email['sender'] !== '' ? $email['sender'] : self::SEND_AS_DEFAULT;
        $bcc = json_decode($email['bcc']);

        $contact_from = self::getContactDetailsFromString($sender);
        $contact_to = self::getContactDetailsFromString($email['recipient']);
        array_walk($bcc, function(&$item, $key) {
            $item = self::getContactDetailsFromString($item);
        });

        try {
            $this->isHtml(true);
            $this->setFrom($contact_from['email'], $contact_from['name']);
            $this->addAddress($contact_to['email'], $contact_to['name']);
            foreach ($bcc as $contact) {
                $this->addBCC($contact['email'], $contact['name']);
            }

            $this->Subject = $email['subject'];
            $body = file_get_contents(CLI_BASE_URL . 'api/mailer/' . $email['id'] . '?render_key=' . EMAIL_RENDERING_KEY);
            $this->Body = $body;

            $text = html_entity_decode($body);
            $text = strip_tags($text);
            $text = trim($text);
            $text = preg_replace("#(\s*\\n){3,}#", "\n\n", $text);
            $text = preg_replace("#( ){2,}#", "", $text);
            $text = wordwrap($text);
            $this->AltBody = $text;

            $sent_at = null;
            $error = null;
            if ($this->send() === false) {
                $error = $this->ErrorInfo;
                Logger::logError('PHPMailer error: ' . $this->ErrorInfo);
            } else {
                $sent_at = date('Y-m-d H:i:s');
            }

            $req = DB::getMaster()->prepare("UPDATE email_queue SET sent_at = ?, error = ? WHERE id = ?");
            $req->execute([$sent_at, $error, $email['id']]);
        } catch (Exception $e) {
            Logger::logError('PHPMailer exception: ' . $e->getMessage());
        }
    }

    public function queueMail($to, $subject, $template = 'mail/default', $params = [], $bcc = [], $from = self::SEND_AS_DEFAULT) {
        $email_queue = new EmailQueue(
            0,
            $from,
            $to,
            $subject,
            $template,
            json_encode($params),
            json_encode($bcc)
        );
        Persist::create($email_queue);
    }
}