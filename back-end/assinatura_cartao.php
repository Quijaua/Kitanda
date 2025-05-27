<?php
function asaas_CriarAssinaturaCartao($customer_id, $dataForm, $config) {
	include('config.php');

    // Configura o fuso horário para São Paulo, Brasil
    date_default_timezone_set('America/Sao_Paulo');

    $date = date("Y-m-d"); // Obtém a data atual no formato "aaaa-mm-dd"
    $vencimento = date("Y-m-d", strtotime($date . "+7 days")); // Adiciona 7 dias à data atual

	$expiry = explode("/", $dataForm["card-expiry"]);
	
	$curl = curl_init();
	
	$fields = [
		"customer" => $customer_id,
		"billingType" => "CREDIT_CARD",
		"nextDueDate" => date('Y-m-d'),
		"value" => $dataForm["value"],
		"cycle" => $dataForm["inlineRadioOptions"],
		"description" => "Plano de assinatura",
		"creditCard" => [
			"holderName" => $dataForm["card-name"],
			"number" => $dataForm["card-number"],
			"expiryMonth" => trim($expiry[0]),
			"expiryYear" => trim($expiry[1]),
			"ccv" => $dataForm["card-ccv"]
		],
		"creditCardHolderInfo" => [
			"name" => $dataForm["name"],
			"email" => $dataForm["email"],
			"cpfCnpj" => $dataForm["cpfCnpj"],
			"postalCode" => $dataForm["postalCode"],
			"addressNumber" => $dataForm["addressNumber"],
			"phone" => $dataForm["phone"]
		]
	];
	
	curl_setopt_array($curl, array(
		CURLOPT_URL => $config['asaas_api_url'].'subscriptions',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => json_encode($fields),
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'access_token: '.$config['asaas_api_key'],
            'User-Agent: '.$application_name
		)
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	
	$retorno = json_decode($response, true);
	
	if($retorno['object'] == 'subscription') {
		return $retorno['id'];
	} else {
		echo $response;
		exit();
	}
}