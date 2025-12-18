<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_GET['pedido_id'])) {
    echo json_encode(['error' => 'Pedido ID não fornecido']);
    exit;
}

$pedido_id = $_GET['pedido_id'];

$stmt = $conn->prepare("SELECT status, forma_pagamento
                       FROM tb_pedidos 
                       WHERE pedido_id = ?");
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => $pedido['status'],
    'forma_pagamento' => $pedido['forma_pagamento'],
]);