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
	private $pubkey;
	private $redirect_url;
	private $data;

	public function __construct($id = 0, $domain = '', $pubkey = '', $redirect_url = '', $data = '') {
		$this->id = $id;
		$this->domain = $domain;
		$this->pubkey = $pubkey;
		$this->redirect_url = $redirect_url;
		$this->data = $data;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id);
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

	public function getPubkey() {
		return $this->pubkey;
	}

	public function setPubkey($pubkey) {
		$this->pubkey = $pubkey;
	}

    public function getRedirectUrl() {
        return $this->redirect_url;
    }

    public function setRedirectUrl($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    public function getData(): string {
        return $this->data;
    }

    public function setData(string $data) {
        $this->data = $data;
    }
}
