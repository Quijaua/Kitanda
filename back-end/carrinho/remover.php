<?php
session_start();
include('../../config.php');

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
} elseif (isset($_COOKIE['cart_id'])) {
    // Usuário não logado: remove somente se o item pertencer ao cookie
    $cookieId = $_COOKIE['cart_id'];
    $stmt = $conn->prepare("DELETE FROM tb_carrinho WHERE id = ? AND cookie_id = ?");
    $stmt->execute([$itemId, $cookieId]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário não identificado."]);
    exit;
}

echo json_encode(["status" => "sucesso", "mensagem" => "Item removido com sucesso!"]);