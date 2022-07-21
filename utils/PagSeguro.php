<?php
namespace utils;

class PagSeguro {

	const EMAIL = "vazsk8@gmail.com";
	const TOKEN = "9BDE4A7BB4A1484A8A95CCD288F4A116";

	const TEST_EMAIL_PAG="c79573477594212764152@sandbox.pagseguro.com.br";
	// const TEST_EMAIL_PAG="teste@sandbox.pagseguro.com.br";

	const NOTIFICATION_URL = "https://eevent.com.br/retorno.php";
	const TEST_NOTIFICATION_URL = "localhost/eevent.com/retorno.php";

	const TEST_CARD_NUMBER = "4111111111111111";
	const TEST_CARD_BRAND = "visa";
	const TEST_CARD_EXP_DATE = "12/2030";
	const TEST_CARD_CVV = "123";

	const URL = "https://ws.pagseguro.uol.com.br/v2/transactions";
	const URL_SESSION = "https://ws.pagseguro.uol.com.br/v2/sessions";
	const URL_NOTIFICATION = "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/";
	const URL_DIRECT_PAG = "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";

	const TEST_URL = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions";
	const TEST_URL_SESSION = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";
	const TEST_URL_NOTIFICATION = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/";
	const TEST_URL_DIRECT_PAG = "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";

	static function idSessaoPagamento() {

		$context = stream_context_create(array(
				'http' => array(
						'method' => 'POST',
						'header' => 'Content-Type: application/xml'
				)
		));

		//$url = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions?email=vazsk8@gmail.com&token=9BDE4A7BB4A1484A8A95CCD288F4A116";
		$url = self::TEST_URL_SESSION . "?email=" . self::EMAIL . "&token=" . self::TOKEN;

		$resultXML = file_get_contents($url, false, $context);
		$result = simplexml_load_string($resultXML);

		return $result->id;
	}

	static function checkout($dadosPagamento) {
		$xml = "";
		$resposta = "";

		// Util::debug($itens);
		if ($dadosPagamento["tokenCartao"] != "" &&
				$dadosPagamento["identificacaoComprador"] != "") {

			// $endereco = $pessoa->getEndereco();
			// $enderecoCobranca = $dadosPagamento["enderecoCobranca"];

			//Dados a serem enviados via requisição POST
			$data = [];

			$data["email"]=self::EMAIL; //"vazsk8@gmail.com";
			$data["token"]=self::TOKEN; //"9BDE4A7BB4A1484A8A95CCD288F4A116";

			$data["paymentMode"]="default"; //required
			$data["paymentMethod"]="creditCard"; //required
			$data["currency"]="BRL"; //required
			$data["receiverEmail"]=self::EMAIL; //"vazsk8@gmail.com";
			$data["extraAmount"]="0.00";

			// A soma dos itens deve estar de acordo com o valor total (installmentValue)
			// $numeroItem = 1;
			// foreach ($itens as $codigoLote => $item) {
				// Util::debug($item);
				$data["itemId1"]="0001"; //required
				$data["itemDescription1"]="Produto de teste"; //required - Livre, com limite de 100 caracteres.
				$data["itemAmount1"]="50.00"; //$dadosPagamento['valor']; //required - Decimal, com duas casas decimais separadas por ponto (p.e., 1234.56), maior que 0.00 e menor ou igual a 9999999.00.
				$data["itemQuantity1"]="1";//required - Um número inteiro maior ou igual a 1 e menor ou igual a 999.
				// $data["itemId" . $numeroItem]=$codigoLote; // $data["itemId1"]="0001";
				// $data["itemDescription" . $numeroItem]=$item["descricao"]; //$data["itemDescription1"]="Ingressos";
				// $data["itemAmount" . $numeroItem]=$item["valor"] . ".00"; //$data["itemAmount1"]= $valor . ".00";
				// $data["itemQuantity" . $numeroItem]=$item["quantidade"]; //$data["itemQuantity1"]="1";

				// $numeroItem++;
			// }

			// $data["itemId1"]="0001";
			// $data["itemDescription1"]="Ingressos";
			// $data["itemAmount1"]= $valor . ".00";
			// $data["itemQuantity1"]="1";

			$data["notificationURL"]=self::TEST_NOTIFICATION_URL; //"https://sualoja.com.br/notifica.html";
			$data["reference"]="REF1234";
			//$data["reference"]=$codigoPedido; //"REF1234";

			$data["senderName"]="Jose Comprador"; //required - No mínimo duas sequências de caracteres, com o limite total de 50 caracteres
			$data["senderCPFType"]="CPF"; //required
			$data["senderCPF"]="22111944785"; //required - Um número de 11 dígitos para CPF ou 14 dígitos para CNPJ.
			$data["senderAreaCode"]="37"; //required - Um número de 2 dígitos correspondente a um DDD válido.
			$data["senderPhone"]="56273440"; //required - Um número de 7 a 9 dígitos.
			$data["senderEmail"]=self::TEST_EMAIL_PAG; //required - Um e-mail válido (p.e., usuario@site.com.br), com no máximo 60 caracteres.
			$data["senderHash"]=$dadosPagamento['identificacaoComprador']; //required
			// $data["senderName"]=$pessoa->getNome(); //"Jose Comprador";
			// $data["senderCPF"]=$pessoa->getCpf(); //"22111944785";
			// $data["senderAreaCode"]="37";
			// $data["senderPhone"]=$pessoa->getTelefone();//"56273440"; //$pessoa->getTelefone();
			// $data["senderEmail"]=self::TEST_EMAIL_PAG; //$pessoa->getEmail();//"c79573477594212764152@sandbox.pagseguro.com.br";
			// $data["senderHash"]=$dadosPagamento['identificacaoComprador'];

			$data["shippingAddressRequired"]="False";
			// $data["shippingAddressStreet"]="Av. Brig. Faria Lima";
			// $data["shippingAddressNumber"]="1384";
			// $data["shippingAddressComplement"]="5o andar";
			// $data["shippingAddressDistrict"]="Jardim Paulistano";
			// $data["shippingAddressPostalCode"]="01452002";
			// $data["shippingAddressCity"]="Sao Paulo";
			// $data["shippingAddressState"]="SP";
					// $data["shippingAddressStreet"]=$endereco->getLogradouro(); //"Av. Brig. Faria Lima";
					// $data["shippingAddressNumber"]=$endereco->getNumero(); //"1384";
					// $data["shippingAddressComplement"]=$endereco->getComplemento(); //"5o andar";
					// $data["shippingAddressDistrict"]=$endereco->getBairro(); //"Jardim Paulistano";
					// $data["shippingAddressPostalCode"]=$endereco->getCep(); //"01452002";
					// $data["shippingAddressCity"]=$endereco->getCidade()->getNome(); //"Sao Paulo";
					// $data["shippingAddressState"]=$endereco->getEstado()->getSigla(); //"SP";

			// $data["shippingAddressCountry"]="BRA";
			// $data["shippingType"]="1";
			// $data["shippingCost"]="0.00";
			//
			$data["creditCardToken"]=$dadosPagamento['tokenCartao']; //required
			$data["installmentQuantity"]=$dadosPagamento['parcelas']; //"1";required - Um inteiro entre 1 e 18
			$data["installmentValue"]="50.00";//$dadosPagamento['valor']; // required - Numérico com 2 casas decimais e separado por ponto.
			$data["noInterestInstallmentQuantity"]=$dadosPagamento['parcSemJuros']; //"6"; // Parcelas sem juros // required

			$data["creditCardHolderName"]="Jose Comprador"; //$dadosPagamento["nome"]; // required - min = 1, max = 50 caracteres.
			$data["creditCardHolderCPFType"]="CPF"; //"22111944785"; // required - Um número de 11 dígitos para CPF ou 14 dígitos para CNPJ.
			$data["creditCardHolderCPF"]=$dadosPagamento["cpf"]; //"22111944785"; // required - Um número de 11 dígitos para CPF ou 14 dígitos para CNPJ.
			$data["creditCardHolderBirthDate"]="27/10/1987"; // required - dd/MM/yyyy
			$data["creditCardHolderAreaCode"]="11"; // required -  Um número de 2 dígitos correspondente a um DDD válido.
			$data["creditCardHolderPhone"]="56273440"; // required - Um número de 7 a 9 dígitos.

			$data["billingAddressStreet"]="Av. Brig. Faria Lima"; // required - Livre, com limite de 80 caracteres.
			$data["billingAddressNumber"]="1384"; // required - Livre, com limite de 20 caracteres
			$data["billingAddressComplement"]="5o andar"; // required - Livre, com limite de 40 caracteres.
			$data["billingAddressDistrict"]="Jardim Paulistano"; // required - Livre, com limite de 60 caracteres.
			$data["billingAddressPostalCode"]="01452002";// required - Um número de 8 dígitos
			$data["billingAddressCity"]="Sao Paulo"; // required - Livre. Deve ser um nome válido de cidade do Brasil, com no mínimo 2 e no máximo 60 caracteres
			$data["billingAddressState"]="SP"; // required - Duas letras, representando a sigla do estado brasileiro
			$data["billingAddressCountry"]="BRA"; // required

					// $data["billingAddressStreet"]=$enderecoCobranca->getLogradouro(); //"Av. Brig. Faria Lima";
					// $data["billingAddressNumber"]=$enderecoCobranca->getNumero(); //"1384";
					// $data["billingAddressComplement"]=$enderecoCobranca->getComplemento(); //"5o andar";
					// $data["billingAddressDistrict"]=$enderecoCobranca->getBairro(); //"Jardim Paulistano";
					// $data["billingAddressPostalCode"]=$enderecoCobranca->getCep(); //"01452002";
					// $data["billingAddressCity"]=$enderecoCobranca->getCidade()->getNome(); //"Sao Paulo";
					// $data["billingAddressState"]=$enderecoCobranca->getEstado()->getSigla(); //"SP";

			var_dump($data);

			$params = array('http'=>array('method'=>'post',
					'header'=>"Content-Type: application/x-www-form-urlencoded",
					'content'=>http_build_query($data))); Util::debug($params);

			$context = stream_context_create($params); Util::debug($context);

			var_dump($params);

			$stream = fopen(self::TEST_URL, 'rb', false, $context);


			if ($stream != false) {
				while (!feof($stream)) {
					$xml = fgets($stream);
				}
				fclose($stream);
			}

			var_dump($xml);
	 		$resposta = simplexml_load_string($xml);
			var_dump($resposta);
	 		return $resposta;
		} else {
			return false;
		}
	}

	static function consultaNotificacao($notificationCode) {
		$data = [];
		$xml = "";
		$resposta = "";
		$url = self::TEST_URL_NOTIFICATION . $notificationCode . "?email=" . self::EMAIL . "&token=" . self::TOKEN;

		var_dump(http_build_query($data));

		$params = array('http'=>array('method'=>'GET','content'=>http_build_query($data)));
		$context= stream_context_create($params);
		$stream= fopen($url, 'r', false, $context);

		if ($stream != false) {
			while (!feof($stream)) {
				$xml = fgets($stream);
			}
			fclose($stream);
		}

		$resposta = simplexml_load_string($xml);
		return $resposta;
	}

}
