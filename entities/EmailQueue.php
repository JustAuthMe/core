<?php


namespace Entity;


class EmailQueue implements \Resourceable {
    private $id;
    private $recipient;
    private $subject;
    private $template;
    private $params;
    private $bcc;
    private $created_at;
    private $sent_at;
    private $error;

    public function __construct($id = 0, $recipient = '', $subject = '', $template = 'mail/default', $params = '', $bcc = '', $created_at = null, $sent_at = null, $error = null) {
        $this->id = $id;
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

    public function setId($id): void {
        $this->id = $id;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function setRecipient($recipient): void {
        $this->recipient = $recipient;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function setSubject($subject): void {
        $this->subject = $subject;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function setTemplate($template): void {
        $this->template = $template;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($params): void {
        $this->params = $params;
    }

    public function getBcc() {
        return $this->bcc;
    }

    public function setBcc($bcc): void {
        $this->bcc = $bcc;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): void {
        $this->created_at = $created_at;
    }

    public function getSentAt() {
        return $this->sent_at;
    }

    public function isSent(): bool {
        return !is_null($this->getSentAt());
    }

    public function setSentAt($sent_at): void {
        $this->sent_at = $sent_at;
    }

    public function getError() {
        return $this->error;
    }

    public function isError() {
        return !is_null($this->getError());
    }

    public function setError($error): void {
        $this->error = $error;
    }
}