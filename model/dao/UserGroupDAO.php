<?php
namespace model\dao;

use config\Conexao;
use model\domain\UserGroup;
use \PDO;

class UserGroupDAO extends DAO {

	function __construct() {
		parent::__construct(UserGroup::class);
	}

}
