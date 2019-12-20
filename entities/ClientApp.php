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
	private $logo;
	private $redirect_url;
	private $data;
	private $public_key;
	private $secret;
	private $hash_key;

	public function __construct($id = 0, $domain = '', $app_id = '', $name = '', $logo = '', $redirect_url = '', $data = '', $public_key = '', $secret = '', $hash_key = '') {
		$this->id = $id;
		$this->domain = $domain;
		$this->app_id = $app_id;
		$this->name = $name;
		$this->logo = $logo;
		$this->redirect_url = $redirect_url;
		$this->data = $data;
		$this->public_key = $public_key;
		$this->secret = $secret;
		$this->hash_key = $hash_key;
	}

    function jsonSerialize() {
        $it = clone $this;

        if (!\Model\ClientApp::isJamConsole()) {
            unset($it->id, $it->public_key, $it->secret);
        }
        unset($it->hash_key);

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

    public function getLogo() {
        return $this->logo;
    }

    public function setLogo($logo) {
        $this->logo = $logo;
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

    public function getPublicKey() {
        return $this->public_key;
    }

    public function setPublicKey($public_key) {
        $this->public_key = $public_key;
    }

    public function getSecret() {
        return $this->secret;
    }

    public function setSecret($secret) {
        $this->secret = $secret;
    }

    public function getHashKey() {
        return $this->hash_key;
    }

    public function setHashKey($hash_key) {
        $this->hash_key = $hash_key;
    }
}
