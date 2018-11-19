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

	public function __construct($id = 0, $username = '') {
		$this->id = $id;
		$this->username = $username;
	}

    function jsonSerialize() {
        $it = clone $this;
        unset($it->id);
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
}
