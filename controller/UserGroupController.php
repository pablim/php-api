<?php
namespace controller;

use model\dao\UserGroupDAO;
use model\domain\UserGroup;
// imports

/**
*
*/
class UserGroupController {

	function __construct() {

	}

	function save($userGroup) {

		$userGroupDao = new UserGroupDAO();
		$userGroupDao->save($userGroup);

		header("Location: /user_group/show");
	}

	// acoes
}
