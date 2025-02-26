<?php
    // Carrega as variáveis de ambiente do arquivo .env
    require dirname(__DIR__).'/vendor/autoload.php';
    require_once dirname(__DIR__).'/back-end/functions.php';

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    $client = new GuzzleHttp\Client();

    // Acessa as variáveis de ambiente
    $config['asaas_api_url'] = $_ENV['ASAAS_API_URL'];
    $config['asaas_api_key'] = $_ENV['ASAAS_API_KEY'];

    include('config.php');

    // Configura o fuso horário para São Paulo, Brasil
    date_default_timezone_set('America/Sao_Paulo');

    $method = $_POST['method']; // Obtém a data atual no formato "aaaa-mm-dd"
    $value = $_POST['value']; // Adiciona 7 dias à data atual

    $curl = curl_init();

    $fields = [
        "billingTypes" => [
            "BOLETO",
            "CREDIT_CARD",
            "PIX"
        ],
        "value" => $value
    ];

    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url'].'payments/simulate',
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

	if($retorno) {
        if ($method == "credit_card") {
            $netValue = $retorno['creditCard']['netValue'];
        } elseif ($method == "bank_slip") {
            $netValue = $retorno['bankSlip']['netValue'];
        } elseif ($method == "Pix") {
            $netValue = $retorno['pix']['netValue'];
        }

        $feeAmount = round($value - $netValue, 2);

        echo json_encode(["status"=>200, "feeAmount"=>$feeAmount]);
	} else {
		echo $response;
		exit();
	}