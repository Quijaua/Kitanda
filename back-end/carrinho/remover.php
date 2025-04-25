<?php
session_start();
include('../../config.php');

// Helper para pegar total de itens para o filtro atual
function pegaTotalItens($conn, $field, $value) {
    $sql = "SELECT COALESCE(SUM(quantidade),0) AS total FROM tb_carrinho WHERE {$field} = ?";
    $s = $conn->prepare($sql);
    $s->execute([$value]);
    return (int) $s->fetchColumn();
}

$itemId = $_POST['item_id'] ?? null;

if (!$itemId) {
    echo json_encode(["status" => "erro", "mensagem" => "ID do item inválido."]);
    exit;
}

// Verifica se o usuário está logado ou se existe um cookie de carrinho
if (isset($_SESSION['user_id'])) {
    // Usuário logado: remove somente se o item pertencer ao usuário
    $stmt = $conn->prepare("DELETE FROM tb_carrinho WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$itemId, $_SESSION['user_id']]);
    $total = pegaTotalItens($conn, 'usuario_id', $_SESSION['user_id']);
} elseif (isset($_COOKIE['cart_id'])) {
    // Usuário não logado: remove somente se o item pertencer ao cookie
    $cookieId = $_COOKIE['cart_id'];
    $stmt = $conn->prepare("DELETE FROM tb_carrinho WHERE id = ? AND cookie_id = ?");
    $stmt->execute([$itemId, $cookieId]);
    $total = pegaTotalItens($conn, 'cookie_id', $cookieId);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário não identificado."]);
    exit;
}

echo json_encode(["status" => "sucesso", "mensagem" => "Item removido com sucesso!", "numero_itens" => $total]);