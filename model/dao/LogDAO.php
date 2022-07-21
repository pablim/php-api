<?php
namespace model\dao;

use config\Conexao;
use model\domain\Log;
use \PDO;

class LogDAO extends DAO {

	function __construct() {
		parent::__construct(Log::class);
	}

}
