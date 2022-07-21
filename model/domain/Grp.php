<?php
namespace model\domain;
// imports

class Grp extends Domain {

	public $codigo;
	public $nome;
	// properties

	function __toString() {
		return $this->codigo;
	}

	static function properties() {
		return [
			"codigo" => [
				"name"=>"codigo","increment"=>"yes","visible"=>"no",
				"key"=>"yes","null"=>"no","type"=>"integer"],
			"nome" => [
				"null"=>"no","type"=>"string","name"=>"nome"]
		];
	}
	// getters e setters

}
