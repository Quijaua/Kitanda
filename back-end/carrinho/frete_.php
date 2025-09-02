<?php 
header('Content-Type: application/json; charset=utf-8');

include_once('../../config.php');

// Consulta para obter o cep de origem do projeto
$stmt = $conn->query("SELECT title, cep FROM tb_checkout LIMIT 1");
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

$application_name = $projeto['title'];
$config['cep_origem'] = $projeto['cep'];
$config['melhor_envio_url'] = $_ENV['MELHOR_ENVIO_URL'];
$config['melhor_envio_token'] = $_ENV['MELHOR_ENVIO_TOKEN'];

date_default_timezone_set('America/Sao_Paulo');

$cepDestinoRaw = $_POST['cep'] ?? '';
$cepDestino    = preg_replace('/\D/', '', $cepDestinoRaw);

if (!$cepDestino) {
    http_response_code(400);
    echo json_encode(['status' => 'erro','mensagem' => 'CEP de destino inválido.']);
    exit;
}

$cart = $_POST['cart'] ?? [];
if (!is_array($cart) || empty($cart)) {
    http_response_code(400);
    echo json_encode(['status' => 'erro','mensagem' => 'Carrinho vazio ou formato inválido.']);
    exit;
}

// Dimensão padrão (id = 0)
$defaultDimensao = [
    'id' => 0,
    'nome' => 'Padrão',
    'altura' => 4,
    'largura' => 12,
    'comprimento' => 17,
    'peso' => 0.5
];

// Busca todas as dimensões cadastradas
$stmtDim = $conn->prepare("SELECT * FROM tb_frete_dimensoes");
$stmtDim->execute();
$dimensoesDb = $stmtDim->fetchAll(PDO::FETCH_ASSOC);

// Indexa por ID
$dimensoes = [0 => $defaultDimensao];
foreach ($dimensoesDb as $dim) {
    $dimensoes[(int)$dim['id']] = [
        'id' => (int)$dim['id'],
        'nome' => $dim['nome'],
        'altura' => (float)$dim['altura'],
        'largura' => (float)$dim['largura'],
        'comprimento' => (float)$dim['comprimento'],
        'peso' => (float)$dim['peso']
    ];
}

$fixedTotal = 0.0;
$volumes = [];

foreach ($cart as $item) {
    $prodId   = intval($item['id'] ?? 0);
    $quantity = intval($item['quantity'] ?? 0);
    if ($prodId <= 0 || $quantity <= 0) {
        continue;
    }

    $stmtProd = $conn->prepare("SELECT freight_type, freight_value, freight_dimension_id FROM tb_produtos WHERE id = :id");
    $stmtProd->bindParam(':id', $prodId, PDO::PARAM_INT);
    $stmtProd->execute();
    $produto = $stmtProd->fetch(PDO::FETCH_ASSOC);

    if (!$produto) continue;

    $type = $produto['freight_type'] ?? 'default';
    $value = floatval($produto['freight_value'] ?? 0);
    $dimensaoId = intval($produto['freight_dimension_id'] ?? 0);
    $dimensao = $dimensoes[$dimensaoId] ?? $dimensoes[0];

    if ($type === 'fixed' && $value > 0) {
        $fixedTotal += $value * $quantity;
        continue;
    }

    for ($i = 0; $i < $quantity; $i++) {
        $volumes[] = [
            'height' => $dimensao['altura'],
            'width' => $dimensao['largura'],
            'length' => $dimensao['comprimento'],
            'weight' => $dimensao['peso']
        ];
    }
}

if (empty($volumes)) {
    echo json_encode([
        'status'  => 'sucesso',
        'options' => [[
            'id'             => 'fixed_total',
            'name'           => 'Frete Fixo (total)',
            'price'          => number_format($fixedTotal, 2, '.', ''),
            'delivery_range' => ['min' => null, 'max' => null],
            'company'        => ['id' => null, 'name' => 'Valor Fixo', 'picture' => null],
            'error'          => null
        ]]
    ]);
    exit;
}

$cepOrigem = preg_replace('/\D/', '', $config['cep_origem']);

$payload = [
    'service' => [1, 2],
    "from"    => [
        "name" => "João",
        "phone" => "11999999999",
        "email" => "joao@email.com",
        "document" => "12345678909",
        "address" => "Rua A",
        "number" => "10",
        "district" => "Centro",
        "city" => "São Paulo",
        "state_abbr" => "SP",
        "country_id" => "BR",
        "postal_code" => "01001-000"
    ],
    'to'      => [
        'name'        => 'Cliente Final',
        'phone'       => '11999999999',
        'email'       => 'cliente@email.com',
        'document'    => '98765432100',
        'address'     => 'Rua do Cliente',
        'number'      => '456',
        'district'    => 'Bairro Legal',
        'city'        => 'Rio de Janeiro',
        'state_abbr'  => 'RJ',
        'country_id'  => 'BR',
        'postal_code' => $cepDestino
    ],
    'volumes' => $volumes,
    'products' => [], // opcional para o ME, você pode inserir se quiser seguro baseado no valor
    'options' => [
        'insurance_value' => 0,
        'non_commercial' => true
    ]
];

$ch = curl_init($config['melhor_envio_url'] . 'me/cart');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Authorization: Bearer ' . $config['melhor_envio_token'],
        'Content-Type: application/json',
        'User-Agent: ' . $application_name
    ],
    CURLOPT_TIMEOUT        => 30,
]);

$response = curl_exec($ch);
$err      = curl_error($ch);
curl_close($ch);

if ($err) {
    http_response_code(500);
    echo json_encode(['status' => 'erro','mensagem' => 'Comunicação falhou: ' . $err]);
    exit;
}

$data = json_decode($response, true);
if (isset($data['message'])) {
    http_response_code(500);
    $msg = $data['message'] ?? 'Erro desconhecido na API';
    echo json_encode(['status' => 'erro', 'mensagem' => $msg]);
    exit;
}

$options = [];
foreach ($data as $svc) {
    $svcPrice = floatval($svc['price'] ?? 0);
    $totalPrice = $svcPrice + $fixedTotal;

    $options[] = [
        'id'             => $svc['id'] ?? 1,
        'name'           => $svc['name'] ?? '',
        'price'          => number_format($totalPrice, 2, '.', ''),
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

if (empty($options) && $fixedTotal > 0) {
    $options[] = [
        'id'             => 'fixed_total',
        'name'           => 'Frete Fixo (total)',
        'price'          => number_format($fixedTotal, 2, '.', ''),
        'delivery_range' => ['min' => null, 'max' => null],
        'company'        => ['id' => null, 'name' => 'Valor Fixo', 'picture' => null],
        'error'          => null
    ];
}

echo json_encode([
    'status'  => 'sucesso',
    'options' => $options
]);