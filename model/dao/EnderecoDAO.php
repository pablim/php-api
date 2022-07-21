<?php
namespace model\dao;

use config\Conexao;
use model\domain\Endereco;
use \PDO;

class EnderecoDAO extends DAO {

	function __construct() {
		parent::__construct(Endereco::class);
	}

}
