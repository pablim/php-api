<?php
namespace model\dao;

use config\Conexao;
use model\domain\User;
use \PDO;

class UserDAO extends DAO {

	function __construct() {
		parent::__construct(User::class);
	}

}
