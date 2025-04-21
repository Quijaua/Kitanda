<?php
function melhorEnvioGetTracking($shipping, $config) {
include('config.php');

// Consulta para obter o cep
$stmt = $conn->query("SELECT cep FROM tb_checkout LIMIT 1");
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);
$config['cep_origem'] = $projeto['cep'];

// Dados básicos
$cepOrigem   = preg_replace('/\D/', '', $config['cep_origem']);
$cepDestino  = preg_replace('/\D/', '', $shipping['cep']);
$serviceId   = intval($shipping['shipping_method']);  // selecionado no front
$peso        = floatval(str_replace(',', '.', $shipping['weight'] ?? 2));
// dimensões fixas (ou receba via POST conforme seu produto)
$height = 4; $width = 12; $length = 17;

// 1) Inserir no carrinho
$cartPayload = [
  'from'    => ['postal_code'=>$cepOrigem],
  'to'      => ['postal_code'=>$cepDestino],
  'package' => ['height'=>$height,'width'=>$width,'length'=>$length,'weight'=>$peso],
  'options' => ['insurance_value'=>0,'receipt'=>false,'own_hand'=>false],
  'services'=> (string)$serviceId
];

$ch = curl_init($config['melhor_envio_url'].'me/cart');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_POST         =>true,
  CURLOPT_POSTFIELDS   =>json_encode($cartPayload),
  CURLOPT_HTTPHEADER   =>[
    'Accept: application/json',
    'Authorization: Bearer ' . $config['melhor_envio_token'],
    'Content-Type: application/json',
    'User-Agent: '.$application_name
  ],
  CURLOPT_TIMEOUT      =>30,
]);
$cartRes = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($cartRes['id'])) {
  return;
  exit(json_encode(['status'=>'erro','mensagem'=>'Falha ao inserir no carrinho.']));
}
$cartItemId = $cartRes['id'];

// 2) Comprar o frete (checkout)
$checkoutPayload = ['shipments'=>[$cartItemId]];
$ch = curl_init($config['melhor_envio_url'].'me/shipment/checkout');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_POST         =>true,
  CURLOPT_POSTFIELDS   =>json_encode($checkoutPayload),
  CURLOPT_HTTPHEADER   =>[
    'Accept: application/json',
    'Authorization: Bearer ' . $config['melhor_envio_token'],
    'Content-Type: application/json',
    'User-Agent: '.$application_name
  ],
  CURLOPT_TIMEOUT      =>30,
]);
$checkoutRes = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($checkoutRes['shipments'][0]['id'])) {
  return;
  exit(json_encode(['status'=>'erro','mensagem'=>'Falha no checkout de frete.']));
}
$purchasedId = $checkoutRes['shipments'][0]['id'];

// 3) Gerar a etiqueta
$genPayload = ['id'=>$purchasedId];
$ch = curl_init($config['melhor_envio_url'].'me/shipment/generate');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_POST         =>true,
  CURLOPT_POSTFIELDS   =>json_encode($genPayload),
  CURLOPT_HTTPHEADER   =>[
    'Accept: application/json',
    'Authorization: Bearer ' . $config['melhor_envio_token'],
    'Content-Type: application/json',
    'User-Agent: '.$application_name
  ],
  CURLOPT_TIMEOUT      =>30,
]);
$genRes = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($genRes['tracking'])) {
  return;
  exit(json_encode(['status'=>'erro','mensagem'=>'Falha ao gerar etiqueta.']));
}
$trackingCode = $genRes['tracking'];

// 4) (Opcional) Validar o rastreio imediatamente
$trackPayload = ['id'=>$purchasedId];
$ch = curl_init($config['melhor_envio_url'].'me/shipment/tracking');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_POST         =>true,
  CURLOPT_POSTFIELDS   =>json_encode($trackPayload),
  CURLOPT_HTTPHEADER   =>[
    'Accept: application/json',
    'Authorization: Bearer ' . $config['melhor_envio_token'],
    'Content-Type: application/json',
    'User-Agent: '.$application_name
  ],
  CURLOPT_TIMEOUT      =>30,
]);
$trackRes = json_decode(curl_exec($ch), true);
curl_close($ch);

// Retorna o código de rastreio e, se quiser, status inicial
$data = [
  'status'       => 'sucesso',
  'tracking'     => $trackingCode,
  'initial_state'=> $trackRes['status'] ?? null
];
return $data;
}