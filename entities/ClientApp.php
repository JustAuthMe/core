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

	public function __construct($id = 0, $domain = '', $pubkey = '') {
		$this->id = $id;
		$this->domain = $domain;
		$this->pubkey = $pubkey;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id);
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
}
