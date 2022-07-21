<?php
namespace controller;

use model\dao\EnderecoDAO;
use model\domain\Endereco;
// imports

/**
* 
*/
class EnderecoController {
	
	function __construct() {
		
	}

	function save($endereco) {

		$enderecoDao = new EnderecoDAO();
		$enderecoDao->save($endereco);

		header("Location: /endereco/show");
	}

	// acoes
}