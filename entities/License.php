<?php


namespace Entity;


class License implements \Resourceable, \JsonSerializable {
    private $id;
    private $owner;
    private $details;
    private $license_key;
    private $created_at;
    private $used_at;

    public function __construct($id = 0, $owner = '', $details = '', $key = '', $created_at = null, $used_at = null) {
        $this->id = $id;
        $this->owner = $owner;
        $this->details = $details;
        $this->license_key = $key;
        $this->created_at = $created_at;
        $this->used_at = $used_at;
    }

    public static function getTableName(): string {
        return 'license';
    }

    public function jsonSerialize() {
        if (!\Utils::isJamInternal()) {
            return null;
        }

        $it = clone $this;
        unset($it->id, $it->used_at);

        return get_object_vars($it);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
    }

    public function getDetails() {
        return $this->details;
    }

    public function setDetails($details) {
        $this->details = $details;
    }
    public function getLicenseKey() {
        return $this->license_key;
    }

    public function setLicenseKey($license_key) {
        $this->license_key = $license_key;
    }
    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function getUsedAt() {
        return $this->used_at;
    }

    public function isUsed(): bool {
        return !is_null($this->getUsedAt());
    }

    public function setUsedAt($used_at) {
        $this->used_at = $used_at;
    }
}