<?php

namespace utils;

use DateTime;
use \ReflectionException;

class Util {

	// static function definePrimeiraPagina($page, $position, $exibir) {
	// 	$primeira = 1;
	// 	if ($position == $exibir) {
	// 		$primeira = $page - 1;
	// 	} else if ($page == $position && $position == 1) {
	// 		if ($page-$exibir > 0) {
	// 			$primeira = $page-$exibir;
	// 		}
	// 	} else if ($page != $position && $position == 1) {
	// 		$primeira = $page - $exibir + 2;
	// 	} else if ($page != $position && $position != $exibir) {
	// 		$primeira = $page - $position + 1;
	// 	}
	// 	if ($primeira <= 0)	$primeira = 1;
	//
	// 	return $primeira;
	// }

	static function response($status, $msgs=[], $results=[]) {
		$jsonText = json_encode([
			"status"=>$status,
			"msgs"=>$msgs,
			"result"=>$results
		]);

		echo $jsonText;
	}

	static function redirect($url, $status, $msgs, $results) {
		$status = htmlspecialchars($status);
		$status = urlencode($status);
		
		$msgsResp = json_encode($msgs, JSON_HEX_APOS);
		$msgsResp = htmlspecialchars($msgsResp);
		$msgsResp = urlencode($msgsResp);
		
		$resultsResp = json_encode($results, JSON_HEX_APOS);
		$resultsResp = htmlspecialchars($resultsResp);
		$resultsResp = urlencode($resultsResp);

		$data = "status=". $status 
			. "&msgs=" . $msgsResp
			. "&result=" . $resultsResp;

		header("Location: " . $url . "?" . $data);
		exit;
	}

	static function fragment($fragment, $style="default") {
		return $_SERVER["DOCUMENT_ROOT"] . "/fragments/". $style . "/" . $fragment . ".php";
	}

	static function component($component) {
		return $_SERVER["DOCUMENT_ROOT"] . "/components/". $component . ".js";
	}

	static function strings($strings) {
		return $_SERVER["DOCUMENT_ROOT"] . "/strings/". $strings . ".php";
	}

	static function css($resource) {
		return "/resources/css/" . $resource . ".css";
	}

	static function js($resource) {
		return "/resources/js/" . $resource . ".js";
	}

	static function icon($resource) {
		return "/resources/imagens/icones/" . $resource . ".png";
	}

	static function img($resource) {
		return "/resources/imagens/" . $resource . ".png";
	}

	static function iconSVG($resource) {
		return "/resources/imagens/icones/" . $resource . ".svg";
	}

	static function open($file) {
		return file_get_contents($_SERVER["DOCUMENT_ROOT"] . $file);
	}

	static function caminhosArray() {
		require_once $_SERVER["DOCUMENT_ROOT"] . "/strings/caminhos.php";
		return get_defined_constants(true);
	}

	static function labels($lang) {
		$path = $_SERVER["DOCUMENT_ROOT"] . "/strings/labels/".str_replace("-", "_", strtolower($lang)).".php";
		if (file_exists($path))
			return $path;
		else return $_SERVER["DOCUMENT_ROOT"] . "/strings/labels/pt_br.php";
	}
	static function texts($lang) {
		$path = $_SERVER["DOCUMENT_ROOT"] . "/strings/text/".str_replace("-", "_", strtolower($lang)).".php";
		if (file_exists($path))
			return $path;
		else return $_SERVER["DOCUMENT_ROOT"] . "/strings/labels/pt_br.php";
	}
	static function msgs($lang) {
		$path = $_SERVER["DOCUMENT_ROOT"] . "/strings/msgs/".str_replace("-", "_", strtolower($lang)).".php";
		if (file_exists($path))
			return $path;
		else return $_SERVER["DOCUMENT_ROOT"] . "/strings/labels/pt_br.php";
	}

	static function verify($string, $array, $isNotSetString="") {
		return isset($array[$string]) ? $array[$string] : $isNotSetString;
	}

	static function primarys($entityName) {
		require $_SERVER["DOCUMENT_ROOT"] . "/properties/primarys.php";
		return $primarys[$entityName];
	}

	static function properties($entityName) {
		require $_SERVER["DOCUMENT_ROOT"] . "/properties/".$entityName.".php";
		return $params;
	}

	static function debug($var) {
		require_once $_SERVER["DOCUMENT_ROOT"] . "/config/system_config.php";
		if (DEBUG == true) {
			var_dump($var);
		}
	}

	static function arrayToString($array, $separator=",") {
		return implode($separator,$array);
	}

	/*
		Percorre uma lista adicionando algum texto em cada item
	*/
	static function appendTextList($array, $textBefore="", $textAfter="") {
		foreach ($array as $key => $item) {
			$array[$key] = $textBefore . $item . $textAfter;
		}
		return $array;
	}

	// Formato de moeda
	static function currency($number) {
		return number_format(floatval($number), 2, ',', '');
	}

	static function currencyPoint($number) {
		$number = str_replace(",", ".", $number);
		return number_format(floatval($number), 2, '.', '');
	}

	// Formato cartao crédito
	static function cardNumber($number) {

		$formatingNumber = substr($number, 0, 4) . "&nbsp;&nbsp;";
		$formatingNumber .= substr($number, 4, 4) . "&nbsp;&nbsp;";
		$formatingNumber .= substr($number, 8, 4) . "&nbsp;&nbsp;";
		$formatingNumber .= substr($number, 12, 4);
		return $formatingNumber;
	}

	static function cardNumberGroups($number): array {
		$groups[0] = substr($number, 0, 4);
		$groups[1] = substr($number, 4, 4);
		$groups[2] = substr($number, 8, 4);
		$groups[3] = substr($number, 12, 4);
		return $groups;
	}

	static function secureCardNumber($number) {
		$formatingNumber = "xxxx xxxx xxxx " . substr($number, 12, 4);
		return $formatingNumber;
	}

	static function lastCardNumbers($number) {
		return substr($number, 12, 4);
	}

	static function request($varName) {
		Util::verify($varName, $_REQUEST, "");
	}

	static function formataData($data) {
		if ($data != null) {
			$date = new DateTime($data);
			return $date->format('d/m/Y');
		}
	}

	static function diaSemana($data) {
		if ($data == "") 
			return "";

		return strtoupper(Util::removeAcentos(strftime("%a", strtotime($data))));
	}

	static function dataExtenso($data) {
		if ($data == "")
			return "";
		$dataExtenso = Util::datePatternFormat($data, 'd');
		$dataExtenso .= " " . strtoupper(strftime("%h", strtotime($data)));
		$dataExtenso .= " " . Util::datePatternFormat($data, 'Y');
		return $dataExtenso;
	}

	// Recebe uma string. Por padrão o formato é brasileiro
	static function datePatternFormat($data, $format="d/m/Y") {
		if ($data != null) {
			$date = new DateTime($data);
			return $date->format($format);
		}
	}

	static function formataDataDiaMes($data) {
		if ($data != null) {
			$date = new DateTime($data);
			return $date->format('d/m');
		}
	}

	static function meses($dataInicio, $dataFim) {
		$date1 = new DateTime($dataInicio);
		$date2 = new DateTime($dataFim);

		//Repare que inverto a ordem, assim terei a subtração da ultima data pela primeira.
		//Calculando a diferença entre os meses
		$meses = ((int)$date2->format('m') - (int)$date1->format('m'))
		//    e somando com a diferença de anos multiplacado por 12
			+ (((int)$date2->format('y') - (int)$date1->format('y')) * 12);

		return $meses;//2
	}

	// Calcula a diferença em dias entre duas datas
	static function dias($dataInicio, $dataFim) {
		// Calcula a diferença em segundos entre as datas
		$diferenca = strtotime($dataFim) - strtotime($dataInicio);

		//Calcula a diferença em dias
		$dias = floor($diferenca / (60 * 60 * 24));

		return intval($dias);
	}

	static function isDate($date, $format = 'Y-m-d') {
		$d = DateTime::createFromFormat($format, $date);
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format($format) === $date;
	}

	static function removeAcentos($string){
			$chars = [
				"/(á|à|ã|â|ä)/",
				"/(Á|À|Ã|Â|Ä)/",
				"/(é|è|ê|ë)/",
				"/(É|È|Ê|Ë)/",
				"/(í|ì|î|ï)/",
				"/(Í|Ì|Î|Ï)/",
				"/(ó|ò|õ|ô|ö)/",
				"/(Ó|Ò|Õ|Ô|Ö)/",
				"/(ú|ù|û|ü)/",
				"/(Ú|Ù|Û|Ü)/",
				"/(ñ)/",
				"/(Ñ)/"
			];

	    return preg_replace(
				$chars,
				explode(" ","a A e E i I o O u U n N"),
				$string);
	}

	static function replace($replaces, $string) {

		$keys = [];
		$values = [];

		foreach ($replaces as $key => $value) {
			$keys[] = $key;
			$values[] = $value;
		}

		return str_replace($keys, $values, $string);
	}

	// A string deve entrar CamelCase, ou Kebab-case
	static function snakeCase($string) {

		$string = strtolower($string);

		// Remove espaços em branco
		$string = preg_replace("/ /", "_", $string);
		$string = preg_replace("/-/", "_", $string);

		// preg_match_all("/[A-Z]*|[-]/", $string, $matches);

		// $keys = [];
		// $values = [];
		//
		// foreach($matches[0] as $match) {
		// 	// var_dump($match);
		// 	$keys[] = $match;
		// 	// $values[] = "_" . strtolower($match);
		// 	$values[] = strtolower($match);
		// }
		//
		// $string = str_replace($keys, $values, $string);
		// if (substr($string, 0,1) == "_") {
		// 	$string = substr($string, 1);
		// }

		return $string;
	}


	/**
	 * Converte uma frase para um padrão camel, kebab, snake ou pascal case
	 * 
	 * @param case camel|kebab|snake|pascal
	 */
	static function convertPhraseToCase($str, $case) {

		$desc = strtolower($str);

		if ($case == "camel") {
			return preg_replace_callback('/\s./', function ($matches) {
				return strtoupper($matches[0][1]);
			}, $desc);
		}
		if ($case == "snake") {
			return preg_replace_callback('/\s./', function ($matches) {
				return "_".$matches[0][1];
			}, $desc);
		}
		if ($case == "kebab") {
			//
		}
		if ($case == "pascal") {
			//
		}

	}

	static function convertCase($str, $caseIn, $toCase) {
		$patterns = [
			"camel"=> ["pattern"=>"/[A-Z]/", "adjust"=>"", 
				"adjustFunction"=>"strtoupper"],
			"pascal"=> ["pattern"=>"/[A-Z]/", "adjust"=>""],
			"snake"=> ["pattern"=>"/_[\w]/", "adjust"=>"_", 
				"adjustFunction"=>"strtolower"],
			"kebab"=> ["pattern"=>"/-[\w]/", "adjust"=>""],
			"phrase"=> ["pattern"=>"/\s./", "adjust"=>""]
		];

		$patternIn = $patterns[$caseIn];
		$patternTo = $patterns[$toCase];

		if ($caseIn == "phrase") {
			$str = strtolower($str);
		}

		$result = preg_replace_callback(
			$patternIn["pattern"], 
			function ($matches) use ($patternTo) {
				if (isset($patternTo["adjustFunction"])) {
					$result = call_user_func($patternTo["adjustFunction"], 
						$matches[0]);
					return $patternTo["adjust"].$result;
				} else {
					return $patternTo["adjust"].$matches[0];
				}
			}, $str);

		$firstChar = substr($result, 0, 1);
		if ($firstChar == "_" || $firstChar == "-") {
			$result = substr($result, 1, strlen($result)-1);
		}

		return $result;
	}

	static function snakeToCamel($string) {
		$string_parts = explode("_", $string);
		$first_part = $string_parts[0];
		$string_parts = array_slice($string_parts, 1);
		$camelCase = "";
		foreach($string_parts as $part) {
			$camelCase .= ucfirst($part);
		}
		return $first_part . $camelCase;
	}

	// transforma em nome de classe
	static function camelCase($string) {
		$className = "";
 		foreach ($string as $part) {
 			$className .= ucfirst($part);
 		}
		return $className;
	}

	static function classCase($string) {
		// var_dump($string);
		$string_parts = explode("_", $string);
		$camelCase = "";
		foreach($string_parts as $part) {
			$camelCase .= ucfirst($part);
		}

		return $camelCase;
	}

	static function executeFunction($executeCommand, $params) {

		// var_dump($params);

		$entity = Util::classCase($executeCommand["entity"]);
		$function = $executeCommand["function"];
		extract($params,  EXTR_PREFIX_ALL, "execute");

		// var_dump(extract($params,  EXTR_PREFIX_INVALID, "pab"));


		// var_dump($entity.$posFix);
		$domain = "\\model\\dao\\".$entity;
		$instance = new $domain();
		// $function = $instance->$function;

		$result = call_user_func_array(
			array($instance,$function), $params);
		// $result = call_user_func_array("\\model\\dao\\".$entity."::".$function, $params);
		// $result = $instance->$function();
		// var_dump($instance);
		// var_dump($result);

		return $result;
	}



	static function sortDate($dateArray) {
		uksort($dateArray, function ($a, $b) {
				return strtotime($a) - strtotime($b);
			}
		);
		return $dateArray;
	}

	static function shortClassName($object) {
		var_dump($object);
		$reflect = new \ReflectionClass($object);
		return $reflect->getShortName();
	}


	static function stringListGraph($infoGraph, $propriedade) {
		$list = "";
		foreach ($infoGraph as $key => $value) {
			$list .= $value[$propriedade] . ",";
		}

		return $list;
	}

	static function dateStringsForGraph($array) {
		$list = "";
		foreach ($infoGraph as $key => $value) {
			// $list .= Util::formataData($value[$propriedade]) . ",";
			$list .= "'".$value[$propriedade] . "',";
		}

		return $list;
	}

	static function extractForGraph($array, $keyOrvalue="value") {
		$list = "";
		foreach ($array as $key => $value) {
			if ($keyOrvalue == "key")
				$list .= "'".$key . "',";
			else {
				// $list .= $value . ",";
				$list .= Util::currencyPoint($value) . ",";
			}
		}

		return $list;
	}

	static function decrescimoRenda($renda, $arrayDataDespesa) {
		$decrecimoRenda = [];
		foreach ($arrayDataDespesa as $key => $value) {
			$decrecimoRenda[$key] = $renda - $value;
			$decrecimoRenda[$key] = Util::currencyPoint($renda - $value);
			// var_dump(Util::currency($renda - $value));
			$renda = $renda - $value;

			// code...
		}
		return $decrecimoRenda;
	}

	static function testeDadosGrafico() {
		//$dadosGrafico[$formaPagamento["descricao"]] = ($total != null ? Util::currency($total) : 0);
		$chaves = explode(",", array_keys($dadosGrafico)); // "chave1, chave2, chave3"
		$chaves = explode(",", array_values($dadosGrafico)); // "valor1, valor2, valor33"
	}

	function validaCampos($objeto) {

		$nomeClasse = shortClassName($objeto);
		var_dump($nomeClasse);
		$properties = properties($nomeClasse);

		foreach ($objeto as $campo => $valor) {
			var_dump($campo);

		}

	}

	static function inicioFimMes($year, $month) {
		$dias_mes = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$inicio = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
		$fim = date('Y-m-d', mktime(0, 0, 0, $month, $dias_mes, $year));

		return ["inicio"=>$inicio, "fim"=>$fim];
	}

}
