<?php
function asaas_CriarCobrancaCartao($customer_id, $dataForm, $config) {
	include('config.php');

    // Configura o fuso horário para São Paulo, Brasil
    date_default_timezone_set('America/Sao_Paulo');
    $date = date("Y-m-d"); // Obtém a data atual no formato "aaaa-mm-dd"

	$expiry = explode("/", $dataForm["card-expiry"]);

    $curl = curl_init();

    $fields = [
        "customer" => $customer_id,
        "billingType" => "CREDIT_CARD",
        "dueDate" => date("Y-m-d"),
        "value" => $dataForm["value"],
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

        $stmt = $conn->prepare("INSERT INTO $tabela (customer_id, payment_id, valor, forma_pagamento, status, data_criacao, cartao_numero, cartao_bandeira) VALUES (
            :customer_id, :payment_id, :value, :forma_pagamento, :status, :data_criacao, :cartao_numero, :cartao_bandeira)");
        
        // Bind dos parâmetros
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindParam(':payment_id', $retorno['id'], PDO::PARAM_STR);
        $stmt->bindParam(':value', $retorno['value'], PDO::PARAM_STR);
        $stmt->bindParam(':forma_pagamento', $retorno['billingType'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $retorno['status'], PDO::PARAM_STR);
        $stmt->bindParam(':data_criacao', $date, PDO::PARAM_STR);
        $stmt->bindParam(':cartao_numero', $retorno['creditCard']['creditCardNumber'], PDO::PARAM_STR);
        $stmt->bindParam(':cartao_bandeira', $retorno['creditCard']['creditCardBrand'], PDO::PARAM_STR);
    
        // Executando o update
        $stmt->execute();

        return $retorno['id'];
    } else {
        echo $response;
        exit();
    }
}