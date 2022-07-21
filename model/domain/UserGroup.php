<?php
namespace model\domain;

use model\dao\UserDAO;
use model\dao\GrpDAO;

// imports

class UserGroup extends Domain {

	public $codigo;
	public $usuario;
	public $grupo;
	// properties

	function __toString() {
		return $this->codigo;
	}

	static function properties() {
		return [
			"grupo" => [
				"acess"=>"public","referenceId"=>"codigo","name"=>"grupo",
				"references"=>"Grp","null"=>"no","type"=>"integer"],
			"codigo" => [
				"name"=>"codigo","increment"=>"yes","visible"=>"no",
				"key"=>"yes","null"=>"no","type"=>"integer"],
			"usuario" => [
				"acess"=>"public","referenceId"=>"codigo","name"=>"usuario",
				"references"=>"User","null"=>"no","type"=>"integer"]
		];
	}
	
	function getUsuario() {
		$usuarioDAO = new UserDAO();
		$usuario = $usuarioDAO->find($this->usuario);
		return $usuario;
	}

	function getGrupo() {
		$grupoDAO = new GrpDAO();
		$grupo = $grupoDAO->find($this->grupo);
		return $grupo;
	}

	// getters e setters

}
