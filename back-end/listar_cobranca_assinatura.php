<?php
function asaas_ObterIdPagamento($subscription_id, $config) {
	include('config.php');

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url']."subscriptions/$subscription_id/payments",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'access_token: '.$config["asaas_api_key"],
            'User-Agent: '.$application_name
        )
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $retorno = json_decode($response, true);

	if($retorno['object'] == 'list') {

        // Obtendo o ID dentro dos dados
        $payment_id = $retorno['data'][0]['id'];

		return $payment_id;

    } else {
        echo $response;
        exit();
    }
}