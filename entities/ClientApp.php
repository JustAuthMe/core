<?php
/**
 * Created by PhpStorm
 * User: peter_000
 * Date: 18/11/2018
 * Time: 14:18
 */
namespace Entity;

class ClientApp implements \Resourceable, \JsonSerializable {
	private $id;
	private $domain;
	private $app_id;
	private $name;
	private $redirect_url;
	private $data;
	private $public_key;
	private $secret;

	public function __construct($id = 0, $domain = '', $app_id = '', $name = '', $redirect_url = '', $data = '', $public_key = '', $secret = '') {
		$this->id = $id;
		$this->domain = $domain;
		$this->app_id = $app_id;
		$this->name = $name;
		$this->redirect_url = $redirect_url;
		$this->data = $data;
		$this->public_key = $public_key;
		$this->secret = $secret;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id, $it->public_key, $it->secret);
        $it->setData(json_decode($it->getData()));
        return get_object_vars($it);
    }

    public static function getTableName(): string {
		return 'client_app';
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getDomain() {
		return $this->domain;
	}

	public function setDomain($domain) {
		$this->domain = $domain;
	}

	public function getAppId() {
		return $this->app_id;
	}

	public function setAppId($app_id) {
		$this->app_id = $app_id;
	}

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getRedirectUrl() {
        return $this->redirect_url;
    }

    public function setRedirectUrl($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getPublicKey(): string {
        return $this->public_key;
    }

    public function setPublicKey(string $public_key) {
        $this->public_key = $public_key;
    }

    public function getSecret(): string {
        return $this->secret;
    }

    public function setSecret(string $secret) {
        $this->secret = $secret;
    }
}
