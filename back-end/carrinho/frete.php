<?php
header('Content-Type: application/json; charset=utf-8');

include_once('../../config.php');

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
    echo json_encode(['status' => 'erro', 'mensagem' => 'CEP de destino inválido.']);
    exit;
}

$cart = $_POST['cart'] ?? [];
if (!is_array($cart) || empty($cart)) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Carrinho vazio ou formato inválido.']);
    exit;
}

// Dimensões padrão (quando freight_dimension_id = 0)
$defaultDimensao = [
    'altura' => 4,
    'largura' => 12,
    'comprimento' => 17,
    'peso' => 0.5
];

// Inicializa variáveis para acumular volume total e peso total
$totalVolumeCm3 = 0;  // volume em cm³
$totalPesoKg = 0;
$fixedTotal = 0;

foreach ($cart as $item) {
    $prodId   = intval($item['id'] ?? 0);
    $quantity = intval($item['quantity'] ?? 0);
    if ($prodId <= 0 || $quantity <= 0) {
        continue;
    }

    // Buscar dimensões e peso do produto
    $stmtProd = $conn->prepare("
        SELECT 
            freight_type, freight_value, freight_dimension_id 
        FROM tb_produtos 
        WHERE id = :id
    ");
    $stmtProd->bindParam(':id', $prodId, PDO::PARAM_INT);
    $stmtProd->execute();
    $produto = $stmtProd->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        // Produto não encontrado: usa padrão
        $altura = $defaultDimensao['altura'];
        $largura = $defaultDimensao['largura'];
        $comprimento = $defaultDimensao['comprimento'];
        $peso = $defaultDimensao['peso'];
    } else {
        $type  = $produto['freight_type'] ?? 'default';
        $value = floatval($produto['freight_value'] ?? 0);
        $freight_dimension_id = intval($produto['freight_dimension_id'] ?? 0);

        if ($type === 'fixed' && $value > 0) {
            // Acumula frete fixo * quantidade
            $fixedTotal += $value * $quantity;
        }

        if ($freight_dimension_id === 0) {
            // Usa padrão
            $altura = $defaultDimensao['altura'];
            $largura = $defaultDimensao['largura'];
            $comprimento = $defaultDimensao['comprimento'];
            $peso = $defaultDimensao['peso'];
        } else {
            // Busca dimensões da dimensão personalizada
            $stmtDim = $conn->prepare("SELECT altura, largura, comprimento, peso FROM tb_frete_dimensoes WHERE id = :id");
            $stmtDim->bindParam(':id', $freight_dimension_id, PDO::PARAM_INT);
            $stmtDim->execute();
            $dim = $stmtDim->fetch(PDO::FETCH_ASSOC);

            if ($dim) {
                $altura = floatval($dim['altura']);
                $largura = floatval($dim['largura']);
                $comprimento = floatval($dim['comprimento']);
                $peso = floatval($dim['peso']);
            } else {
                // Caso não encontre, usa padrão
                $altura = $defaultDimensao['altura'];
                $largura = $defaultDimensao['largura'];
                $comprimento = $defaultDimensao['comprimento'];
                $peso = $defaultDimensao['peso'];
            }
        }
    }

    // Acumula volume e peso para Melhor Envio
    $volumeProduto = $altura * $largura * $comprimento; // cm³
    $totalVolumeCm3 += $volumeProduto * $quantity;
    $totalPesoKg += $peso * $quantity;
}

// Para o Melhor Envio precisamos enviar dimensões únicas do pacote.
// Estimamos o lado de um cubo que tenha volume total acumulado:
$ladoCm = pow($totalVolumeCm3, 1/3);

// Se o volume for 0 (ex: carrinho vazio), usar dimensões padrão:
if ($totalVolumeCm3 <= 0) {
    $ladoCm = $defaultDimensao['altura']; // ou 4
    $totalPesoKg = $defaultDimensao['peso'];
}

// Agora monta o payload com as dimensões do pacote calculadas e peso total
$cepOrigem = preg_replace('/\D/', '', $config['cep_origem']);

$payload = [
    'from'    => ['postal_code' => $cepOrigem],
    'to'      => ['postal_code' => $cepDestino],
    'package' => [
        'height' => ceil($ladoCm),
        'width'  => ceil($ladoCm),
        'length' => ceil($ladoCm),
        'weight' => max(0.1, round($totalPesoKg, 2)) // peso mínimo 100g para Melhor Envio
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