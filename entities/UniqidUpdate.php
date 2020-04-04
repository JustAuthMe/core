<?php


namespace Entity;


class UniqidUpdate implements \Resourceable, \JsonSerializable {
    private $id;
    private $user_id;
    private $old_uniqid;
    private $new_uniqid;
    private $ip;
    private $timestamp;
    private $active;

    public function __construct($id = 0, $user_id = 0, $old_uniqid = '', $new_uniqid = '', $ip = '', $timestamp = 0, $active = 0) {
        $this->id = $id;
        $this->setUserId($user_id);
        $this->old_uniqid = $old_uniqid;
        $this->new_uniqid = $new_uniqid;
        $this->ip = $ip;
        $this->timestamp = $timestamp;
        $this->active = $active;
    }


    public static function getTableName(): string {
        return 'uniqid_update';
    }

    public function jsonSerialize() {
        $it = clone $this;
        unset($it->user_id);

        return get_object_vars($it);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;

        if (\Persist::exists('User', 'id', $user_id)) {
            $this->user = \Persist::read('User', $user_id);
        }
    }

    public function getOldUniqid() {
        return $this->old_uniqid;
    }

    public function setOldUniqid($old_uniqid): void {
        $this->old_uniqid = $old_uniqid;
    }

    public function getNewUniqid() {
        return $this->new_uniqid;
    }

    public function setNewUniqid($new_uniqid) {
        $this->new_uniqid = $new_uniqid;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
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