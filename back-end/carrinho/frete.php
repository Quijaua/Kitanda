<?php 
header('Content-Type: application/json; charset=utf-8');

include_once('../../config.php');

// Consulta para obter o cep de origem do projeto
$stmt = $conn->query("SELECT nome, cep FROM tb_checkout LIMIT 1");
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

$application_name = $projeto['nome'];
$config['cep_origem'] = $projeto['cep'];

$config['melhor_envio_url'] = $_ENV['MELHOR_ENVIO_URL'];
$config['melhor_envio_token'] = $_ENV['MELHOR_ENVIO_TOKEN'];

// Fuso horário de São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Recebe e valida dados do POST
$cepDestinoRaw = $_POST['cep'] ?? '';
$cepDestino    = preg_replace('/\D/', '', $cepDestinoRaw);

if (!$cepDestino) {
    http_response_code(400);
    echo json_encode([
        'status'   => 'erro',
        'mensagem' => 'CEP de destino inválido.'
    ]);
    exit;
}

// Recebe array de produtos do carrinho: cart[0][id], cart[0][quantity], etc.
$cart = $_POST['cart'] ?? [];
if (!is_array($cart) || empty($cart)) {
    http_response_code(400);
    echo json_encode([
        'status'   => 'erro',
        'mensagem' => 'Carrinho vazio ou formato inválido.'
    ]);
    exit;
}

// Pesos e dimensões padrão (já que não há dados específicos de cada produto)
$defaultHeight = 4;   // em cm
$defaultWidth  = 12;  // em cm
$defaultLength = 17;  // em cm
$defaultWeight = 0.5; // em kg (por unidade, por exemplo)

// Variáveis para acumular frete fixo e peso total para Melhor Envio
$fixedTotal     = 0.0;
$meTotalWeight  = 0.0;

foreach ($cart as $item) {
    $prodId   = intval($item['id'] ?? 0);
    $quantity = intval($item['quantity'] ?? 0);
    if ($prodId <= 0 || $quantity <= 0) {
        continue;
    }

    // Busca o produto no banco para verificar se tem frete fixo ou padrão
    $stmtProd = $conn->prepare("SELECT freight_type, freight_value FROM tb_produtos WHERE id = :id");
    $stmtProd->bindParam(':id', $prodId, PDO::PARAM_INT);
    $stmtProd->execute();
    $produto = $stmtProd->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        // Se não encontrar o produto, considera como padrão ME
        $meTotalWeight += $defaultWeight * $quantity;
        continue;
    }

    $type  = $produto['freight_type'] ?? 'default';
    $value = floatval($produto['freight_value'] ?? 0);

    if ($type === 'fixed' && $value > 0) {
        // Se for frete fixo, acumula o valor fixo * quantidade
        $fixedTotal += $value * $quantity;
    } else {
        // Caso contrário, considera peso padrão para Melhor Envio
        $meTotalWeight += $defaultWeight * $quantity;
    }
}

// Se não houver itens para Melhor Envio, retornamos apenas frete fixo como única "opção"
if ($meTotalWeight <= 0) {
    echo json_encode([
        'status'  => 'sucesso',
        'options' => [
            [
                'id'             => 'fixed_total',
                'name'           => 'Frete Fixo (total)',
                'price'          => number_format($fixedTotal, 2, '.', ''),
                'delivery_range' => ['min' => null, 'max' => null],
                'company'        => ['id' => null, 'name' => 'Valor Fixo', 'picture' => null],
                'error'          => null
            ]
        ]
    ]);
    exit;
}

// Monta o payload para chamar a API do Melhor Envio apenas com o peso total calculado
$cepOrigem = preg_replace('/\D/', '', $config['cep_origem']);

$payload = [
    'from'    => ['postal_code' => $cepOrigem],
    'to'      => ['postal_code' => $cepDestino],
    'package' => [
        'height' => $defaultHeight,
        'width'  => $defaultWidth,
        'length' => $defaultLength,
        'weight' => $meTotalWeight
    ],
    'options' => [
        'insurance_value' => 0,
        'receipt'         => false,
        'own_hand'        => false
    ]
];

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
if (isset($data['message'])) {
    http_response_code(500);
    $msg = $data['message'] ?? 'Erro desconhecido na API';
    echo json_encode(['status' => 'erro', 'mensagem' => $msg]);
    exit;
}

// Monta as opções adicionando o valor fixo ao preço retornado pelo Melhor Envio
$options = [];
foreach ($data as $svc) {
    $svcPrice = floatval($svc['price'] ?? 0);
    $totalPrice = $svcPrice + $fixedTotal;

    $options[] = [
        'id'             => $svc['id'],
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

// Se existirem itens de frete fixo e nenhum serviço ME válido, adicionamos uma opção só de frete fixo
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