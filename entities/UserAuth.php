<?php
/**
 * Created by PhpStorm
 * User: peter_000
 * Date: 18/11/2018
 * Time: 17:16
 */
namespace Entity;

class UserAuth implements \Resourceable, \JsonSerializable {
	private $id;
	private $token;
	private $client_app_id;
	private $callback_url;
	private $timestamp;
	private $ip_address;

	public function __construct($id = 0, $token = '', $client_app_id = 0, $callback_url = '', $timestamp = null, $ip_address = '') {
		$this->id = $id;
		$this->token = $token;
		$this->setClientAppId($client_app_id);
		$this->callback_url = $callback_url;
		$this->timestamp = $timestamp;
		$this->ip_address = $ip_address;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id, $it->client_app_id, $it->ip_address);
        $it->setData(json_decode($it->getData()));
        return get_object_vars($it);
    }

	public static function getTableName(): string {
		return 'user_auth';
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getToken() {
		return $this->token;
	}

	public function setToken($token) {
		$this->token = $token;
	}

	public function getClientAppId() {
		return $this->client_app_id;
	}

	public function setClientAppId($client_app_id) {
		$this->client_app_id = $client_app_id;
		if (\Persist::exists('ClientApp', 'id', $client_app_id)) {
            $this->client_app = \Persist::read('ClientApp', $client_app_id);
        }
	}

	public function getCallbackUrl() {
		return $this->callback_url;
	}

	public function setCallbackUrl($callback_url) {
		$this->callback_url = $callback_url;
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
