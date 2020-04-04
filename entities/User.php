<?php
/**
 * Created by PhpStorm
 * User: peter_000
 * Date: 18/11/2018
 * Time: 14:19
 */
namespace Entity;

class User implements \Resourceable, \JsonSerializable {
	private $id;
	private $username;
	private $uniqid;
	private $timestamp;
	private $ip_address;
	private $public_key;
	private $active;

	public function __construct($id = 0, $username = '', $uniqid = '', $timestamp = null, $ip_address = '', $public_key = '', $active = 0) {
		$this->id = $id;
		$this->username = $username;
		$this->uniqid = $uniqid;
		$this->timestamp = $timestamp;
		$this->ip_address = $ip_address;
		$this->public_key = $public_key;
		$this->active = $active;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id, $it->timestamp, $it->ip_address, $it->hash_key);
        return get_object_vars($it);
    }

    public static function getTableName(): string {
		return 'user';
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

    public function getUniqid() {
        return $this->uniqid;
    }

    public function setUniqid($uniqid) {
        $this->uniqid = $uniqid;
    }

	public function getTimestamp() {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    public function getIpAddress() {
        return $this->ip_address;
    }

    public function setIpAddress($ip_address) {
        $this->ip_address = $ip_address;
    }

    public function getPublicKey() {
        return $this->public_key;
    }

    public function setPublicKey($public_key) {
        $this->public_key = $public_key;
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
