<?php
namespace model\domain;

use model\dao\UserDAO;

// imports

class Log extends Domain {

	public $codigo;
	public $controller;
	public $action;
	public $data;
	public $refId;
	public $user;
	// properties

	function __toString() {
		return $this->codigo;
	}

	static function properties() {
		return [
			"controller" => [
				"acess"=>"public","null"=>"no","type"=>"string",
				"name"=>"controller"],
			"user" => [
				"acess"=>"public","referenceId"=>"codigo","name"=>"user",
				"references"=>"User","null"=>"no","type"=>"integer"],
			"action" => [
				"acess"=>"public","null"=>"no","type"=>"integer",
				"name"=>"action"],
			"codigo" => [
				"name"=>"codigo","increment"=>"yes","visible"=>"no",
				"key"=>"yes","null"=>"no","type"=>"integer"],
			"data" => [
				"acess"=>"public","null"=>"no","type"=>"datetime",
				"name"=>"data"],
			"refId" => [
				"acess"=>"public","null"=>"no","type"=>"integer",
				"name"=>"ref_id"]
		];
	}

	function getUser() {
		$userDAO = new UserDAO();
		$user = $userDAO->find($this->user);
		return $user;
	}

	function setUser($user) {
		$this->user = $user;
	}

	// getters e setters

}
