<?php
session_start();
include('../../config.php');

$pedidoId = $_GET['pedido'] ?? null;
if (!$pedidoId) {
    http_response_code(400);
    echo json_encode(['error' => 'Pedido não informado']);
    exit;
}

// Busca o pedido na tabela tb_pedidos usando o pedido_id
$stmt = $conn->prepare("SELECT * FROM tb_pedidos WHERE pedido_id = ?");
$stmt->execute([$pedidoId]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    http_response_code(404);
    echo json_encode(['error' => 'Pedido não encontrado']);
    exit;
}

// Formata as datas, por exemplo, para o formato desejado "d/m/Y H:i"
$pedido['data_criacao'] = date("d/m/Y H:i", strtotime($pedido['data_criacao']));
$pedido['data_pagamento'] = !empty($pedido['data_pagamento']) ? date("d/m/Y H:i", strtotime($pedido['data_pagamento'])) : null;

// Retorna os dados do pedido em JSON
header('Content-Type: application/json');
echo json_encode($pedido);
?>