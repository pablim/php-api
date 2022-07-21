<?php

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');

// linguagem
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);

require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config/autoLoader.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/strings/caminhos.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config/routes.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config/params.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config/permissions.php";

// require_once $_SERVER["DOCUMENT_ROOT"] . "/strings/msgs.php";
// require_once $_SERVER["DOCUMENT_ROOT"] . "/strings/texts.php";
// require_once $_SERVER["DOCUMENT_ROOT"] . "/strings/labels.php";
// require_once $_SERVER["DOCUMENT_ROOT"] . "/utils/calendar_config.php";

use utils\Util;
use config\Session;

// var_dump($_REQUEST["url"]);

Session::check();

// Define o idioma padrão da data
setlocale(LC_TIME, 'pt_BR','pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

$url = isset($_REQUEST["url"]) ? $_REQUEST["url"] : "";
$urlArray = explode("/", $url); Util::debug($urlArray);
$f = scandir($_SERVER["DOCUMENT_ROOT"] . "/" . DRT_DOMAIN);

// Permissões
Util::debug($urlArray[0]);
$blocks = Util::verify($urlArray[0], $permissions["block"]["admin"]);
Util::debug($blocks);
if ($blocks != "" && in_array($urlArray[1], $blocks)) {
	header("Location: " . "/errors/block");
	exit();
}

$entityNameURL = explode("_", $urlArray[0]);

$className = Util::camelCase($entityNameURL);
$_REQUEST["class_name"] = $className;
$_REQUEST["snake_class_name"] = Util::snakeCase($className);

$controller = $className . "Controller";
$domain = $className;
$namespaceControllerClass = str_replace("/", "\\", DRT_CONTROLLER . $controller);
$namespaceDomainClass = str_replace("/", "\\", DRT_DOMAIN . $domain);

$metodos = null;
if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/controller/" . $controller .".php")) {

	$itemController = new $namespaceControllerClass;
	$metodos = get_class_methods(new $itemController);
}

// function foo() {
// 	foreach (headers_list() as $header) {
// 	  if (strpos($header, 'X-Powered-By:') !== false) {
// 		header_remove('X-Powered-By');
// 	  }
// 	  header_remove('X-Test');
// 	}
// }
// header_register_callback ( 'foo' );

$m = "";
if (key_exists(1, $urlArray))
	$m = Util::snakeToCamel($urlArray[1]);

if ($metodos && in_array($m, $metodos)) {

	if ($_SERVER["REQUEST_METHOD"]=="POST") {
		$args = $_POST;
	} else if ($_SERVER["REQUEST_METHOD"]=="GET") {
		$args = $_GET;
	} else {
		$args = array_slice($urlArray, 2);
	}

	call_user_func_array(array($itemController, $m), array($args));
} else {
	extract($_REQUEST);

	if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . "pages/" . $url . ".php")) {
		include_once $_SERVER["DOCUMENT_ROOT"] . "/" . "pages/" . $url . ".php";
	} else if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $url . ".php")) {
		include_once $_SERVER["DOCUMENT_ROOT"] . "/" . $url . ".php";
	} else {
		echo "inválido";
	}
}

//
// 	$posIni = strpos($pageString, '<!-- layout:');
// 	$posFim = strpos($pageString, '-->');
// 	var_dump($posFim);
// 	//$layout = substr($pageString, $pos, strlen('<!-- layout: layout.php -->'));
// 	$layout = substr($pageString, $posIni, $posFim+strlen("-->"));
// 	var_dump($layout);
// 	$pageString = str_replace('<!-- layout: layout.php -->', "", $pageString);
//
// 	$layout = str_replace("<!-- ", "", $layout);
// 	$layout = str_replace(" -->", "", $layout);
// 	$layout = explode(":", $layout)[1];
// 	$layout = trim($layout);
//
// 	ini_set('xdebug.var_display_max_depth', -1);
// 	ini_set('xdebug.var_display_max_children', -1);
// 	ini_set('xdebug.var_display_max_data', -1);
//
// 	$pageLayout = Util::open('../' . DRT_PAGES . $layout);
// 	$pageLayout = str_replace('<!-- include -->', $pageString, $pageLayout);
//
//  	//echo $pageLayout;
// 	//echo "hey hey";
// 	//$out = ob_get_clean();
// 	//echo $out;
//
//
// 	var_dump($pageLayout);
//
// 	//include_once '../' . DRT_PAGES . $entity . '.php';
// }
