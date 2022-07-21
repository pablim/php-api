<?php
namespace controller;

use model\dao\GrpDAO;
use model\domain\Grp;
// imports

/**
*
*/
class GrpController {

	function __construct() {

	}

	function save($grp) {

		$grpDao = new GrpDAO();
		$grpDao->save($grp);

		header("Location: /grp/show");
	}

	// acoes
}
