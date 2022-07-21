<?php
namespace config;

use League\OAuth2\Client\Provider\Google;

class Session {

	static function checkAutorization($url, $autorizedUrls) {

		$urlParts = parse_url($url);

		// $result = preg_match(
		// 	'/[\/a-zA-Z0-9\/]+\?/',
		// 	$url,
		// 	$matches
		// );

		if (in_array($urlParts["path"], $autorizedUrls)) {
			return true;
		} 

		return false;
	}

	static function start() {
		if (session_id() == "")
			session_start();
	}

	static function check() {
		Session::start();
		$uri = $_SERVER["REQUEST_URI"];
		
		// URLs autorizadas
		require_once $_SERVER["DOCUMENT_ROOT"] . "/config/acesso_autorizado.php";

		// Se o usuário já estiver autenticado e chamar a página de login
		if (isset($_SESSION["auth"]) && $uri == "/login") {
			header("Location: /home");
		}

		// Se não está autenticado e não está autorizado redireciona para o login
		if (!isset($_SESSION["auth"]) && (!Session::checkAutorization($uri, $autorizado))) {
			//$_REQUEST['imediate_redirect']
			header("Location: /login");
		}
	}

	static function registerUser($user){
		Session::start();
		$_SESSION["auth"] = true;
		$_SESSION["user"] = $user;
	}

	static function registerGoogleUser($user){
		Session::start();
		$_SESSION["auth"] = true;
		$_SESSION["googleAuth"] = true;
		$_SESSION["user"] = $user;
	}

	static function registerGoogleState($state){
		Session::start();
		$_SESSION["oauth2state"] = true;
	}

	static function set($key, $value) {
		Session::start();
		$_SESSION[$key] = $value;
	}

	static function get($key) {
		Session::start();
		return isset($_SESSION[$key]) ? $_SESSION[$key] : "";
	}

	static function user() {
		return isset($_SESSION["user"]) ? $_SESSION["user"] : "";
	}

	static function authenticated() {
		return isset($_SESSION["auth"]) ? $_SESSION["user"] : "";
	}

	static function end() {
		$_SESSION = array(); // Elimina os dados da sessão
		session_start();
		session_destroy();
	}
}
