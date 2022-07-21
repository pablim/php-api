<?php
namespace model\dao;

use config\Conexao;
use model\domain\Grp;
use \PDO;

class GrpDAO extends DAO {

	function __construct() {
		parent::__construct(Grp::class);
	}

}
