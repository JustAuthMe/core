<?php
/**
 * Created by PhpStorm
 * User: peter_000
 * Date: 18/11/2018
 * Time: 17:16
 */
namespace Entity;

class UserAuth implements \Resourceable {
	private $id;
	private $token;
	private $client_app_id;
	private $callback_url;
	private $data;

	public function __construct($id = 0, $token = '', $client_app_id = 0, $callback_url = '', $data = []) {
		$this->id = $id;
		$this->token = $token;
		$this->client_app_id = $client_app_id;
		$this->callback_url = $callback_url;
		$this->data = $data;
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
	}

	public function getCallbackUrl() {
		return $this->callback_url;
	}

	public function setCallbackUrl($callback_url) {
		$this->callback_url = $callback_url;
	}

	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
	}
}
