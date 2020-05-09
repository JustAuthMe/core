<?php


namespace Entity;


class EmailQueue implements \Resourceable {
    private $id;
    private $sender;
    private $recipient;
    private $subject;
    private $template;
    private $params;
    private $bcc;
    private $created_at;
    private $sent_at;
    private $error;

    public function __construct($id = 0, $sender = \Mailer::SEND_AS_DEFAULT, $recipient = '', $subject = '', $template = 'mail/default', $params = '', $bcc = '', $created_at = null, $sent_at = null, $error = null) {
        $this->id = $id;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->template = $template;
        $this->params = $params;
        $this->bcc = $bcc;
        $this->created_at = $created_at;
        $this->sent_at = $sent_at;
        $this->error = $error;
    }

    public static function getTableName(): string {
        return 'email_queue';
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getSender() {
        return $this->sender;
    }

    public function setSender($sender) {
        $this->sender = $sender;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function setRecipient($recipient) {
        $this->recipient = $recipient;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function setTemplate($template) {
        $this->template = $template;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getBcc() {
        return $this->bcc;
    }

    public function setBcc($bcc) {
        $this->bcc = $bcc;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function getSentAt() {
        return $this->sent_at;
    }

    public function isSent(): bool {
        return !is_null($this->getSentAt());
    }

    public function setSentAt($sent_at) {
        $this->sent_at = $sent_at;
    }

    public function getError() {
        return $this->error;
    }

    public function isError() {
        return !is_null($this->getError());
    }

    public function setError($error) {
        $this->error = $error;
    }
}