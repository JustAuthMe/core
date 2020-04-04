<?php

namespace Entity;

class UserLogin implements \Resourceable {
    private $id;
    private $hash;
    private $salt;
    private $active;

    public function __construct($id = 0, $hash = '', $salt = '', $active = 0) {
        $this->id = $id;
        $this->hash = $hash;
        $this->salt = $salt;
        $this->active = $active;
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

    public function getActive() {
        return $this->active;
    }

    public function isActive(): bool {
        return !!$this->getActive();
    }

    public function setActive($active) {
        $this->active = $active;
    }
}