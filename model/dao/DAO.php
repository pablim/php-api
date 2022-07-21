<?php
namespace model\dao;

use utils\Util;
use config\Conexao;
use \PDO;

class DAO {

  public $connection;
  public $className;
  public $tableName;
  public $properties;

  public $bindTypes = [
    "integer"=>PDO::PARAM_INT,
    "string"=>PDO::PARAM_STR,
    "float"=>PDO::PARAM_STR,
    "boolean"=>PDO::PARAM_BOOL,
    "date"=>PDO::PARAM_STR,
    "datetime"=>PDO::PARAM_STR,
    "null"=>PDO::PARAM_NULL
  ];

  function __construct($class) {
    $conn = new Conexao();
		$this->connection = $conn->getConexao();
    //$this->className = str_replace("/", "\\", "model/domain/" . Util::classCase($this->tableName));
    $this->className = str_replace("/", "\\", $class);
    
    $classParts = explode("\\", $class);
    $this->tableName = Util::convertCase($classParts[count($classParts)-1], 
      "pascal", "snake");

    $properties = $this->className::properties();
  }

  public function find($key) {
    try {
      $keyProperty = $this->className::getKey();

      // $sql = "SELECT * FROM " . $this->tableName . " "
      //     . " WHERE " . Util::primarys($this->tableName) ."= :key";

      $sql = "SELECT * FROM " . $this->tableName . " "
          . " WHERE " . $keyProperty ."= :key";

      $stmt = $this->connection->prepare($sql);
      $stmt->bindParam('key', $key, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetch();

      $object = new $this->className($result);
      return $object;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function conditions($conditions) {
    $properties = $this->className::properties();
    $sql = "";
    foreach ($conditions as $condition) {
      $literalCondition = " AND ";
      
      if (isset($condition[3])) {
        if ($condition[3]=="||") {
          $literalCondition = " OR ";
        } else if ($condition[3]!="&&" && $condition[3]!="||") {
          // Return error condition 
        }
      }

      if (is_array($condition[0])) {

        $sql = "(" . $this->conditions($condition) . ")" . $literalCondition;
      } else {
        $property = $condition[0];
        $operator = $condition[1];
        $camelProperty = Util::convertCase($property, "camel", "snake");
        $sql .= $camelProperty . $operator . " :" . $property 
            . $literalCondition;
      }
    }

    $sql = substr($sql, 0, strlen($sql) - strlen($literalCondition));

    return $sql;
  }

  function bindParameters($conditions, $stmt) {
    $properties = $this->className::properties();

    // & no $value para passar como referencia
    foreach ($conditions as $condition) {
      if (is_array($condition[0])) {
        $this->bindParameters($condition, $stmt);
      } else {

        $property = $condition[0];
        $value = &$condition[2];
  
        //$camelProperty = Util::convertCase($property, "camel", "snake");
        $bindType = $this->bindTypes[$properties[$property]["type"]];
        $stmt->bindParam($property, $value, $bindType);
      }

    }

    return $stmt;
  }

  function findBy($conditions, $operador="and") {
    
    try {
      $sql = "SELECT * FROM " . $this->tableName . " WHERE ";

      foreach ($conditions as $key => $value) {
        $sql .= $key . " = :" . $key . " " . $operador . " ";
      }

      $sql = substr($sql, 0, strlen($sql) - strlen($operador . " "));
      $stmt = $this->connection->prepare($sql);

      // & no $value para passar como referencia
      foreach ($conditions as $key => &$value) {
        $stmt->bindParam($key, $value, PDO::PARAM_STR);
      }

      $stmt->execute();
      $result = $stmt->fetchAll();

      $objects = [];
      foreach ($result as $objectArray) {
        $object = new $this->className($objectArray);
        array_push($objects, $object);
      }

      return $objects;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function findAll() {
    try {
      $sql = "SELECT * FROM " . $this->tableName . " ";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll();

      $objects = [];
      foreach ($result as $objectArray) {
        $object = new $this->className($objectArray);
        array_push($objects, $object);
      }

      return $objects;

    } catch (PDOException $e) {
        throw $e;
    }
  }

  function last() {
    try {
      $sql = "SELECT MAX(" . Util::primarys($this->tableName) . ") FROM " . $this->tableName . " ";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch();
      if ($result == "") {
        $result = 0;
      }

      return $result;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function lastBy($columnName) {
    try {
      $sql = "SELECT MAX(" . $columnName . ") AS last FROM " . $this->tableName . " ";
      // Util::debug("sql: " . $sql);

      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch();

      if ($result["last"] == "") {
        $result["last"] = 0;
      }

      return $result["last"];
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function findAllLimited($qtde, $page) {
    try {
      $qtde = intval($qtde);
      $inicio = ($qtde * $page) - $qtde;

      $sql = "SELECT * FROM " . $this->tableName . " "
          . "LIMIT :inicio, :qtde";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindParam('inicio', $inicio, PDO::PARAM_INT);
      $stmt->bindParam('qtde', $qtde, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll();

      $objects = [];
      foreach ($result as $objectArray) {
        $object = new $this->className($objectArray);
        array_push($objects, $object);
      }

      return $objects;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function save($object) {
    $columnsName = "";
    $columnsBinds = "";
    $binds = [];

    try {
      $properties = $this->className::properties();
      $keyProperty = $this->className::getKey();
      foreach ($object as $property => $value) {
        $camelProperty = $property;
        if (!Util::verify("key", $properties[$property], false) &&
            Util::verify("column", $properties[$property], true)) {
          // var_dump($value);
          //$property = Util::snakeCase($property);
          $property = Util::convertCase($property, "camel", "snake");
          $columnsName .= $property . ", ";
          $columnsBinds .= ":" . $property . ", ";
          array_push($binds, array($property, $value, $this->bindTypes[
            $properties[$camelProperty]["type"]
          ]));
        }
      }

      $columnsName = substr($columnsName, 0, strlen($columnsName)-2);
      $columnsBinds = substr($columnsBinds, 0, strlen($columnsBinds)-2);

      $sql = "INSERT INTO " . $this->tableName . "(" . $columnsName . ") "
          . " VALUES (" . $columnsBinds . ")";

      $this->connection->beginTransaction();
      $stmt = $this->connection->prepare($sql);

      foreach ($binds as &$bind) {
        //if ($bind[0] != Util::primarys($this->tableName)) {
        if ($bind[0] != $keyProperty) {
          $stmt->bindParam($bind[0], $bind[1], $bind[2]);
          // var_dump($bind[0]);
        }
      }

      $result = $stmt->execute();
      $result = $this->connection->lastInsertId();
			$this->connection->commit();
      return $result;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function bindProperties($object, $keyProperty) {
    $columnsBinds = "";
    $columnsBindId = "";
    $binds = [];

    $properties = $object::properties();
    
    foreach ($object as $property => $value) {
      $camelProperty = $property;
      $property = Util::convertCase($property, "camel", "snake");
      
      if ($property != $keyProperty) {
        $columnsBinds .= $property . " = :" . $property . ", ";
      } else {
        $columnsBindId = $property . " = :" . $property;
      }
      
      array_push($binds, array($property, $value, $this->bindTypes[
        $properties[$camelProperty]["type"]
      ]));
    }

    $columnsBinds = substr($columnsBinds, 0, strlen($columnsBinds)-2);

    return [$columnsBinds, $columnsBindId, $binds];
  }

  function update($object) {
    $columnsBinds = "";
    $columnsBindId = "";
    $binds = [];
    // var_dump($object);

    try {
      $properties = $object::properties();
      $keyProperty = $this->className::getKey();
      foreach ($object as $property => $value) {
        $camelProperty = $property;
        $property = Util::convertCase($property, "camel", "snake");
        if ($property != $keyProperty) {
          $columnsBinds .= $property . " = :" . $property . ", ";
        } else {
          $columnsBindId = $property . " = :" . $property;
        }
        //array_push($binds, array($property, $value, PDO::PARAM_STR));
        array_push($binds, array($property, $value, $this->bindTypes[
          $properties[$camelProperty]["type"]
        ]));
      }

      $columnsBinds = substr($columnsBinds, 0, strlen($columnsBinds)-2);

      $sql = "UPDATE " . $this->tableName . " SET " . $columnsBinds . " "
        ."WHERE " . $columnsBindId;

      $stmt = $this->connection->prepare($sql);

      foreach ($binds as &$bind) {
        // var_dump($bind[0], $bind[1], $bind[2]);
        if ($bind[0] != $keyProperty) {
          $stmt->bindParam($bind[0], $bind[1], $bind[2]);
        } else {
          $stmt->bindParam($bind[0], $bind[1], $bind[2]);
        }
      }

      $result = $stmt->execute();
      return $result;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function delete($codigo) {
    try {
      
      $keyProperty = $this->className::getKey();
      $sql = "DELETE FROM " . $this->tableName . " "
          . " WHERE ".$keyProperty." = :codigo";

      $stmt = $this->connection->prepare($sql);
      $stmt->bindParam('codigo', $codigo, PDO::PARAM_INT);
      // $result = $stmt->fetch();
      $result = $stmt->execute();
      $e = $stmt->errorInfo();
      
      if ($stmt->errorInfo()[0] != 0) {
        throw new Exception();
      }

      return $result;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  // [ ['despesa','>','50'],['and','parcela','=', '70'] ]
  function deleteBy($conditions, $operador="and") {
    try {

      $sql = "DELETE FROM " . $this->tableName . " WHERE ";

      foreach ($conditions as $key => $value) {
        $sql .= $key . " = :" . $key . " " . $operador . " ";
      }

      $sql = substr($sql, 0, strlen($sql) - strlen($operador . " "));
      $stmt = $this->connection->prepare($sql);

      // & no $value para passar como referencia
      foreach ($conditions as $key => &$value) {
        $stmt->bindParam($key, $value, PDO::PARAM_STR);
      }

      $result = $stmt->execute();
      $e = $stmt->errorInfo();
      
      if ($stmt->errorInfo()[0] != 0) {
        throw new Exception();
      }

      return $result;
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function count() {
    try {
      $keyProperty = $this->className::getKey();

      $sql = "SELECT COUNT(" . $keyProperty . ") "
            . "FROM " . $this->tableName . " ";

      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch();

      if ($result == null)
        return 0;
      return $result[0];
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function countBy($conditions) {
    try {
      $sqlConditions = $this->conditions($conditions);

      $keyProperty = $this->className::getKey();

      $sql = "SELECT COUNT(". $keyProperty .") count "
          . "FROM " . $this->tableName . " WHERE $sqlConditions";

      $stmt = $this->connection->prepare($sql);
      $stmt = $this->bindParameters($conditions, $stmt);
      $stmt->execute();
      $result = $stmt->fetch();
      return $result["count"];
    } catch (PDOException $e) {
        throw $e;
    }
  }

  function sumBy($fields, $conditions, $fetchType=PDO::FETCH_ASSOC) {
    try {

      $sqlSum = "";
      foreach ($fields as $field) {
        $camelField = Util::convertCase($field, "camel", "snake");
        $sqlSum .= "SUM(" . $camelField . "), ";
      }

      $sqlSum = substr($sqlSum, 0, strlen($sqlSum)-2);
      
      $sqlConditions = $this->conditions($conditions);

      $sql = "SELECT " . $sqlSum . " FROM " . $this->tableName . " "
          . "WHERE $sqlConditions";
          
      $stmt = $this->connection->prepare($sql);
      $stmt = $this->bindParameters($conditions, $stmt);
      $stmt->execute();
      $result = $stmt->fetch($fetchType);
      return $result;
    } catch (PDOException $e) {
        throw $e;
    }
  }

}
