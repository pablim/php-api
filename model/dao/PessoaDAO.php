<?php
namespace model\dao;

use config\Conexao;
use model\domain\Pessoa;
use \PDO;

class PessoaDAO extends DAO {

	function __construct() {
		parent::__construct(Pessoa::class);
	}

}
