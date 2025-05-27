<?php
function asaas_CriarCobrancaCartao($customer_id, $dataForm, $config) {
	include('config.php');

    // Configura o fuso horário para São Paulo, Brasil
    date_default_timezone_set('America/Sao_Paulo');
    $date = date("Y-m-d"); // Obtém a data atual no formato "aaaa-mm-dd"

	$expiry = explode("/", $dataForm["card_expiry"]);

    $curl = curl_init();

    $fields = [
        "customer" => $customer_id,
        "billingType" => "CREDIT_CARD",
        "dueDate" => date("Y-m-d"),
        "value" => $dataForm["value"],
        "creditCard" => [
            "holderName" => $dataForm["card_name"],
            "number" => $dataForm["card_number"],
            "expiryMonth" => trim($expiry[0]),
            "expiryYear" => trim($expiry[1]),
            "ccv" => $dataForm["card_ccv"]
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
        return $retorno;
    } else {
        echo $response;
        exit();
    }
}