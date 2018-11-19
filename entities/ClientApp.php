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
	private $secret;

	public function __construct($id = 0, $domain = '', $secret = '') {
		$this->id = $id;
		$this->domain = $domain;
		$this->secret = $secret;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id, $it->secret);
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

	public function getSecret() {
		return $this->secret;
	}

	public function setSecret($secret) {
		$this->secret = $secret;
	}
}
