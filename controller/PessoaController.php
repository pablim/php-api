<?php
namespace controller;

use model\dao\PessoaDAO;
use model\dao\UserDAO;
use model\domain\User;
use utils\Util;
// imports

/**
*
*/
class PessoaController {

	function __construct() {

	}

	function save($pessoa) {


		$user = $_REQUEST["user"];
		$senhaMD5 = md5($user["senha"]);
		if ($senhaMD5 != md5($user["confirmacao_senha"])){
			header("Location: /novo_usuario?msg=senhas não são iguais");
			die();
		}

		$pessoaDao = new PessoaDAO();
		$userDao = new UserDAO();

		// var_dump($pessoa);
		if ($pessoa->getCpf() == "") {
			$pessoa->setCpf("null");
		}

		$user = new User($user);

		$codigo_pessoa = $pessoaDao->save($pessoa);
		$user->setPessoa($codigo_pessoa);
		$user->setLogin($pessoa->getEmail());
		$user->setSenha($senhaMD5);



		// var_dump($user);

		// Util::validaCampos($user);

		$userDao->save($user);

		header("Location: /index");
	}

	// acoes
}
