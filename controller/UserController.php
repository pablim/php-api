<?php
namespace controller;

use config\Session;

use model\dao\UserDAO;
use model\domain\User;

// imports

/**
*
*/
class UserController {

	function __construct() {

	}

	function save($user) {

		$userDao = new UserDAO();
		var_dump($user);
		
		// $userDao->save($user);

		// header("Location: /user/show");
	}

	function login() {

		try {

			$login = isset($_REQUEST["login"]) ? $_REQUEST["login"] : "";
			$senha = isset($_REQUEST["senha"]) ? $_REQUEST["senha"] : "";
			$senha = md5($senha);
	
			$userDAO = new UserDAO();
			$conditions = ["login" => $login, "senha" => $senha];
			$user = $userDAO->findBy($conditions)[0];
	
			if ($user != null) {
				Session::registerUser($user);
				header("Location: /home");
			} else {
				header("Location: /login");
			}
		} catch(\Exception $e) {
			$message["erro"][] = $e->getMessage();
		}

		return ["messages"=>$message];
	}

	function logout() {
		Session::end();
		header("Location: /index");
	}
	// acoes
}
