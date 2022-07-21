<?php
namespace controller;

use config\Session;

use model\dao\UserDAO;
use model\dao\PessoaDAO;
use model\domain\Pessoa;
use utils\Util;
use League\OAuth2\Client\Provider\Google;

// imports

define("GOOGLE", [
	'clientId'     => '449567276275-le90tkd8k4n2m325gq48cjkre516c15d.apps.googleusercontent.com',
    'clientSecret' => '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',
    'redirectUri'  => 'https://julius.com/auth/googleLogin',
]);
/**
*
*/
class AuthController {

	function __construct() {

	}

	function trocarSenha() {
		try {

			$login = isset($_REQUEST["login"]) ? $_REQUEST["login"] : "";
			$senha = isset($_REQUEST["senha"]) ? $_REQUEST["senha"] : "";
			$auth = isset($_REQUEST["auth"]) ? $_REQUEST["auth"] : "";
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

	}

	function googleLogin() {

		try {
			$google = new Google(GOOGLE);
			$code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
			$error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRING);

			if ($error) {
				header("Location: /login");
			}

			if (!$code) {
				// Fetch the authorization URL from the provider; this returns the
				// urlAuthorize option and generates and applies any necessary parameters
				// (e.g. state).
				$authURL = $google->getAuthorizationUrl();

				// Get the state generated for you and store it to the session.
				Session::registerGoogleState($google->getState());

				header("Location: " . $authURL);
				exit;

			} else {
				$token = $google->getAccessToken("authorization_code", 
					["code"=> $code]);

				$resourceOwner = $google->getResourceOwner($token);

				$pessoaDAO = new PessoaDAO();
				$pessoa = $pessoaDAO->findBy(["email" => $resourceOwner->getEmail()])[0];
				if (!$pessoa) {
					 $pessoa = new Pessoa([
					 	"nome" => $resourceOwner->getName(),
					 	"email" => $resourceOwner->getEmail(),
					 	"avatar" => $resourceOwner->getAvatar(),
						"data_nascimento" => "1920-01-01"
					]);
					$codigoPessoa = $pessoaDAO->save($pessoa);
				}

				// The provider provides a way to get an authenticated API request for
				// the service, using the access token; it returns an object conforming
				// to Psr\Http\Message\RequestInterface.
				// $request = $google->getAuthenticatedRequest(
				// 	'GET',
				// 	'https://service.example.com/resource',
				// 	$accessToken
				// );

				Session::registerGoogleUser($pessoa, $resourceOwner);
				header("Location: /home");
			}

			// $login = isset($_REQUEST["login"]) ? $_REQUEST["login"] : "";
			// $senha = isset($_REQUEST["senha"]) ? $_REQUEST["senha"] : "";
			// $senha = md5($senha);
	
			// $userDAO = new UserDAO();
			// $conditions = ["login" => $login, "senha" => $senha];
			// $user = $userDAO->findBy($conditions)[0];
	
			// if ($user != null) {
			// 	Session::registerUser($user);
			// 	header("Location: /home");
			// } else {
			// 	header("Location: /login");
			// }
		} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

			// Failed to get the access token or user details.
			$message["erro"][] = $e->getMessage();
		} catch(\Exception $e) {
			$message["erro"][] = $e->getMessage();
		}

		return ["messages"=>$message];
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

	function verificaEmail($params) {
		try {
			$email = $params["email"];

			$pessoaDAO = new PessoaDAO();
			$result = $pessoaDAO->findBy(["email"=>$email]);

			if (!$result) {
				Util::response("ok", 
					["Disponível"]
				);
			} else {
				Util::response("ok", 
					["Este e-mail já está cadastrado no nosso sistema"]
				);
			}
		
		} catch (\Exception $e) {
			$message = $e->getMessage();

			Util::response("error", 
				[$message]
			);
		}
	}


	// acoes
}
