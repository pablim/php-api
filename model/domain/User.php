<?php
namespace model\domain;

use model\dao\PessoaDAO;

// imports

class User extends Domain {

	public $codigo;
	public $login;
	public $senha;
	public $pessoa;
	// properties

	function __toString() {
		return $this->codigo;
	}

	static function properties() {
		return [
			"pessoa" => [
				"acess"=>"public","referenceId"=>"codigo","name"=>"pessoa",
				"references"=>"Pessoa","null"=>"no","type"=>"integer",
				"size"=>"3"],
			"login" => [
				"acess"=>"public","null"=>"no","type"=>"string",
				"name"=>"login","size"=>"30"],
			"codigo" => [
				"name"=>"codigo","increment"=>"yes","visible"=>"no",
				"key"=>"yes","null"=>"no","type"=>"integer"],
			"senha" => [
				"acess"=>"public","null"=>"yes","type"=>"password",
				"name"=>"senha","size"=>"60"]
		];
	}
		
	function getPessoa() {
		$pessoaDAO = new PessoaDAO();
		$pessoa = $pessoaDAO->find($this->pessoa);
		return $pessoa;
	}

	function getUserGroup() {
		$userGroupDAO = new UserGroupDAO();
		$userGroup = $userGroupDAO->find($this->userGroup);
		return $userGroup;
	}

	// getters e setters
}
