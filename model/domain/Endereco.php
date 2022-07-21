<?php
namespace model\domain;

use model\dao\PessoaDAO;

	// imports

class Endereco extends Domain {

	public $codigo;
	public $logradouro;
	public $numero;
	public $bairro;
	public $pessoa;
	// properties

	function __toString() {
		return $this->codigo;
	}

	static function properties() {
		return [
			"bairro" => [
				"acess"=>"public","null"=>"no","type"=>"string",
				"name"=>"bairro","size"=>"50"],
			"codigo" => [
				"name"=>"codigo","increment"=>"yes","visible"=>"no",
				"key"=>"yes","null"=>"no","type"=>"integer"],
			"logradouro" => [
				"acess"=>"public","null"=>"no","type"=>"string",
				"name"=>"logradouro","size"=>"60"],
			"numero" => [
				"acess"=>"public","null"=>"yes","type"=>"integer",
				"name"=>"numero"],
			"pessoa" => [
				"acess"=>"public","referenceId"=>"codigo","name"=>"pessoa",
				"references"=>"Pessoa","null"=>"no","type"=>"integer"],
		];
	}
	
	function getPessoa() {
		$pessoaDAO = new pessoaDAO();
		$pessoa = $pessoaDAO->find($this->pessoa);
		return $pessoa;
	}

	function setPessoa($pessoa) {
		$this->pessoa = $pessoa;
	}

	// getters e setters
}
