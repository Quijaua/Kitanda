<?php
header('Content-Type: application/json; charset=utf-8');

include_once('../../config.php');

// Consulta para obter o cep
$stmt = $conn->query("SELECT nome, cep FROM tb_checkout LIMIT 1");
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

$application_name = $projeto['nome'];
$config['cep_origem'] = $projeto['cep'];

$config['melhor_envio_url'] = $_ENV['MELHOR_ENVIO_URL'];
$config['melhor_envio_token'] = $_ENV['MELHOR_ENVIO_TOKEN'];

// Fuso horário de São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Recebe e valida dados do POST
$cepOrigem   = preg_replace('/\D/', '', $config['cep_origem']);
$cepDestino  = preg_replace('/\D/', '', $_POST['cep'] ?? '');
$peso        = floatval(str_replace(',', '.', $_POST['weight'] ?? 2));
$height      = floatval($_POST['height'] ?? 4);
$width       = floatval($_POST['width'] ?? 12);
$length      = floatval($_POST['length'] ?? 17);
$insurance   = floatval(str_replace(',', '.', $_POST['insurance_value'] ?? 0));
$receipt     = filter_var($_POST['receipt'] ?? false, FILTER_VALIDATE_BOOLEAN);
$ownHand     = filter_var($_POST['own_hand'] ?? false, FILTER_VALIDATE_BOOLEAN);
// Serviços separados por vírgula, ex: "1,2,3,4"
$services    = $_POST['services'] ?? '1,2,3,4';

if (!$cepDestino || $peso <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'erro',
        'mensagem'=> 'CEP de destino ou peso inválido.'
    ]);
    exit;
}

// Monta o payload conforme exemplo
$payload = [
    'from'    => ['postal_code' => $cepOrigem],
    'to'      => ['postal_code' => $cepDestino],
    'package' => [
        'height' => $height,
        'width'  => $width,
        'length' => $length,
        'weight' => $peso
    ],
    'options' => [
        'insurance_value' => $insurance,
        'receipt'         => $receipt,
        'own_hand'        => $ownHand
    ]
];

// Inicializa o cURL
$ch = curl_init($config['melhor_envio_url'].'me/shipment/calculate');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Authorization: Bearer ' . $config['melhor_envio_token'],
        'Content-Type: application/json',
        'User-Agent: '.$application_name
    ],
    CURLOPT_TIMEOUT        => 30,
]);

$response = curl_exec($ch);
$err      = curl_error($ch);
curl_close($ch);

if ($err) {
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Comunicação falhou: '.$err]);
    exit;
}

$data = json_decode($response, true);
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// exit;
if (isset($data['message'])) {
    http_response_code(500);
    $msg = $data['message'] ?? 'Erro desconhecido na API';
    echo json_encode(['status' => 'erro', 'mensagem' => $msg]);
    exit;
}

// Adapta cada serviço ao front
$options = [];
foreach ($data as $svc) {
    $options[] = [
        'id'             => $svc['id'],
        'name'           => $svc['name'] ?? '',
        'price'          => $svc['price'] ?? '',
        'delivery_range' => [
            'min' => $svc['delivery_range']['min'] ?? null,
            'max' => $svc['delivery_range']['max'] ?? null
        ],
        'company' => [
            'id'      => $svc['company']['id'] ?? null,
            'name'    => $svc['company']['name'] ?? '',
            'picture' => $svc['company']['picture'] ?? ''
        ],
        'error'   => $svc['error'] ?? null
    ];
}

echo json_encode([
    'status'  => 'sucesso',
    'options' => $options
]);