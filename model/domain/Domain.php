<?php
namespace model\domain;

use utils\Util;

class Domain {

    function __construct($objectArray="") {	
		foreach ($this as $key => $value) {
			$snakeKey = Util::convertCase($key, "camel", "snake");
			$this->$key = isset($objectArray[$snakeKey]) ? $objectArray[$snakeKey] : "";
		}
    }
	
	function __toString() {
		$str = "";
		foreach ($this as $key => $value) {
			$str .= $value  . " - ";
		}

		return $str;
	}
	
    function toJSON() {
		return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    function toXML() {
		$classArray = explode("\\", __class__);
		$className = $classArray[count($classArray)-1];
		$xml="<".$className.">";
		foreach ($this as $key => $value) {
			$xml .= "<".$key.">".$value."<".$key.">";
		}
		$xml.="<".$className.">";

		return $xml;
    }
    
    static function props() {
		require $_SERVER["DOCUMENT_ROOT"] . "/properties/" . __class__ . ".php";
		return $params;
	}

	public function  __set ( $name , $value ) {
		$this->$name = $value;
	}

	public function  __get ( $name ) {
		return $this->$name;
	}
	
	static function getKey() {
		$properties = static::properties();
		foreach ($properties as $propertie => $definitions) {
			if (array_key_exists("key", $definitions)) {
				return $propertie;
			}
		}
	}

}