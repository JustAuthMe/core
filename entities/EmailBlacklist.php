<?php


namespace Entity;


class EmailBlacklist implements \Resourceable {
    private $id;
    private $email;
    private $created_at;

    public function __construct($id = 0, $email = '', $created_at = null) {
        $this->id = $id;
        $this->email = $email;
        $this->created_at = $created_at;
    }

    public static function getTableName(): string {
        return 'email_blacklist';
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
}