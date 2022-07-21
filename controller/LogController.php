<?php
namespace controller;

use model\dao\LogDAO;
use model\domain\Log;
// imports

/**
*
*/
class LogController {

	function __construct() {

	}

	function save($log) {

		$logDao = new LogDAO();
		$logDao->save($log);

		header("Location: /log/show");
	}

	// acoes
}
