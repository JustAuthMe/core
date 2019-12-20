<?php

namespace Entity;

class UserLogin implements \Resourceable {
    private $id;
    private $hash;
    private $salt;

    public function __construct($id = 0, $hash = '', $salt = '') {
        $this->id = $id;
        $this->hash = $hash;
        $this->salt = $salt;
    }

    public static function getTableName(): string {
        return 'user_login';
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getHash() {
        return $this->hash;
    }

    public function setHash($hash) {
        $this->hash = $hash;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
    }
}