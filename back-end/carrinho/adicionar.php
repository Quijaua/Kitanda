<?php
session_start();
include('../../config.php');

$produtoId = $_POST['produto_id'] ?? null;
$quantidade = $_POST['quantidade'] ?? 1;

if (!$produtoId) {
    die(json_encode(["status" => "erro", "mensagem" => "Produto inválido!"]));
}

// Função para responder com JSON
function responde($status, $mensagem) {
    echo json_encode(["status" => $status, "mensagem" => $mensagem]);
    exit;
}

if (isset($_SESSION['user_id'])) {
    // Usuário logado
    $userId = $_SESSION['user_id'];

    // Verifica se o produto já está cadastrado no carrinho do usuário
    $stmt = $conn->prepare("SELECT quantidade FROM tb_carrinho WHERE usuario_id = ? AND produto_id = ?");
    $stmt->execute([$userId, $produtoId]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        // Se a quantidade for igual, não faz nada
        if ($registro['quantidade'] == $quantidade) {
            responde("sucesso", "Produto já está no carrinho com a mesma quantidade.");
        } else {
            // Atualiza a quantidade
            $stmt = $conn->prepare("UPDATE tb_carrinho SET quantidade = ? WHERE usuario_id = ? AND produto_id = ?");
            $stmt->execute([$quantidade, $userId, $produtoId]);
            responde("sucesso", "Quantidade atualizada no carrinho do usuário!");
        }
    } else {
        // Insere novo item
        $stmt = $conn->prepare("INSERT INTO tb_carrinho (usuario_id, produto_id, quantidade) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $produtoId, $quantidade]);
        responde("sucesso", "Produto adicionado ao carrinho do usuário!");
    }
} else {
    // Usuário não logado: utiliza o cookie
    if (!isset($_COOKIE['cart_id'])) {
        $cookieId = bin2hex(random_bytes(16));
        setcookie('cart_id', $cookieId, time() + 3600 * 24 * 365, "/"); // Expira em 1 ano
    } else {
        $cookieId = $_COOKIE['cart_id'];
    }

    // Verifica se o produto já está cadastrado no carrinho temporário
    $stmt = $conn->prepare("SELECT quantidade FROM tb_carrinho WHERE cookie_id = ? AND produto_id = ?");
    $stmt->execute([$cookieId, $produtoId]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        if ($registro['quantidade'] == $quantidade) {
            responde("sucesso", "Produto já está no carrinho temporário com a mesma quantidade.");
        } else {
            // Atualiza a quantidade
            $stmt = $conn->prepare("UPDATE tb_carrinho SET quantidade = ? WHERE cookie_id = ? AND produto_id = ?");
            $stmt->execute([$quantidade, $cookieId, $produtoId]);
            responde("sucesso", "Quantidade atualizada no carrinho temporário!");
        }
    } else {
        // Insere novo item no carrinho temporário
        $stmt = $conn->prepare("INSERT INTO tb_carrinho (cookie_id, produto_id, quantidade) VALUES (?, ?, ?)");
        $stmt->execute([$cookieId, $produtoId, $quantidade]);
        responde("sucesso", "Produto salvo no carrinho temporário!");
    }
}