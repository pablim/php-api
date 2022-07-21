<?php
namespace config;
use PDO;
class Conexao {
	function getConexao() {
		try {
			// Produção
			// $conn = new PDO('mysql:host=31.220.48.124;dbname=julius', 'julius', '123',
			// 		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

			// Localhost
			$conn = new PDO('mysql:host=localhost;dbname=julius', 'julius', '123',
					array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

			//
			// $conn = new PDO('mysql:host=sql208.epizy.com;dbname=epiz_22689941_julius',
			// 		'epiz_22689941', 'xKNh4jTH75E');
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		} catch(PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
		}
	}
}
