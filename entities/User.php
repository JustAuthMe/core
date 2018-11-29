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
	private $timestamp;
	private $ip_address;

	public function __construct($id = 0, $username = '', $timestamp = null, $ip_address = '') {
		$this->id = $id;
		$this->username = $username;
		$this->timestamp = $timestamp;
		$this->ip_address = $ip_address;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id, $it->timestamp, $it->ip_address);
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
}
