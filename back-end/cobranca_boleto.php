<?php

function asaas_CriarCobrancaBoleto($customer_id, $dataForm, $config) {

	include('config.php');

    // Configura o fuso horário para São Paulo, Brasil
    date_default_timezone_set('America/Sao_Paulo');

    $date = date("Y-m-d"); // Obtém a data atual no formato "aaaa-mm-dd"
    $vencimento = date("Y-m-d", strtotime($date . "+7 days")); // Adiciona 7 dias à data atual

	$fields = [
		"customer" => $customer_id,
		"billingType" => "BOLETO",
		"dueDate" => $vencimento,
		"value" => $dataForm["value"]
	];

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $config['asaas_api_url'].'payments',
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

	if($retorno['object'] == 'payment') {

		$tabela = 'tb_doacoes';

		$stmt = $conn->prepare("INSERT INTO $tabela (customer_id, payment_id, valor, forma_pagamento, link_pagamento, link_boleto, status, data_vencimento, data_criacao) VALUES (
			:customer_id, :payment_id, :value, :forma_pagamento, :link_pagamento, :link_boleto, :status, :data_vencimento, :data_criacao)");
		
		// Bind dos parâmetros
		$stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
		$stmt->bindParam(':payment_id', $retorno['id'], PDO::PARAM_STR);
		$stmt->bindParam(':value', $retorno['value'], PDO::PARAM_STR);
		$stmt->bindParam(':forma_pagamento', $retorno['billingType'], PDO::PARAM_STR);
		$stmt->bindParam(':link_pagamento', $retorno['invoiceUrl'], PDO::PARAM_STR);
		$stmt->bindParam(':link_boleto', $retorno['bankSlipUrl'], PDO::PARAM_STR);
		$stmt->bindParam(':status', $retorno['status'], PDO::PARAM_STR);
		$stmt->bindParam(':data_vencimento', $vencimento, PDO::PARAM_STR);
        $stmt->bindParam(':data_criacao', $date, PDO::PARAM_STR);

		// Executando o update
		$stmt->execute();

		return $retorno['id'];
	} else {
		echo $response;
		exit();
	}
}