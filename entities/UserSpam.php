<?php
/**
 * Created by PhpStorm
 * User: peter_000
 * Date: 29/11/2018
 * Time: 20:24
 */
namespace Entity;

class UserSpam implements \Resourceable {
	private $id;
	private $user_id;
	private $timestamp;
	private $ip_address;

	public function __construct($id = '', $user_id = 0, $timestamp = null, $ip_address = '') {
		$this->id = $id;
		$this->user_id = $user_id;
		$this->timestamp = $timestamp;
		$this->ip_address = $ip_address;
	}

	public static function getTableName(): string {
		return 'user_spam';
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
