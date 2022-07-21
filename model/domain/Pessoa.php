<?php
namespace model\domain;


// imports

class Pessoa extends Domain {

	public $codigo;
	public $nome;
	public $email;
	public $cpf;
	public $dataNascimento;
	public $avatar;
	public $senha;
	// properties

	function __toString() {
		return $this->nome;
	}

	static function properties() {
		return [
			"codigo" => [
				"name"=>"codigo","increment"=>"yes","visible"=>"no",
				"key"=>"yes","null"=>"no","type"=>"integer"],
			"cpf" => [
				"acess"=>"public","name"=>"cpf","label"=>"CPF","null"=>"no",
				"type"=>"string","size"=>"15"],
			"nome" => [
				"acess"=>"public","name"=>"nome","toString"=>"true",
				"null"=>"no","type"=>"string","size"=>"30"],
			"email" => [
				"acess"=>"public","name"=>"email","toString"=>"true",
			 	"null"=>"no","type"=>"string","size"=>"100"],	 
			"senha" => [
				"acess"=>"public","column"=>"senha","toString"=>"no",
				"null"=>"no","type"=>"string","size"=>"100"],
			"dataNascimento" => [
				"acess"=>"public","column"=>"data_nascimento","toString"=>"no",
				"null"=>"no","type"=>"date","size"=>"100"],
			"avatar" => [
				"acess"=>"public","column"=>"avatar","toString"=>"true",
				"null"=>"no","type"=>"string","size"=>"1000"]
		];
	}

	// getters e setters

}
