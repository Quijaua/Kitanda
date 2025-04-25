<?php
session_start();
include('../../config.php');

$produtoId = $_POST['produto_id'] ?? null;
$quantidade = $_POST['quantidade'] ?? 1;

if (!$produtoId) {
    die(json_encode(["status" => "erro", "mensagem" => "Produto inválido!"]));
}

// Função para responder com JSON
function responde($status, $mensagem, $numeroItens) {
    echo json_encode(["status" => $status, "mensagem" => $mensagem, "numero_itens" => $numeroItens]);
    exit;
}

// Helper para pegar total de itens para o filtro atual
function pegaTotalItens($conn, $field, $value) {
    $sql = "SELECT COALESCE(SUM(quantidade),0) AS total FROM tb_carrinho WHERE {$field} = ?";
    $s = $conn->prepare($sql);
    $s->execute([$value]);
    return (int) $s->fetchColumn();
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
            $total = pegaTotalItens($conn, 'usuario_id', $userId);
            responde("sucesso", "Produto já está no carrinho com a mesma quantidade.", $total);
        } else {
            // Atualiza a quantidade
            $stmt = $conn->prepare("UPDATE tb_carrinho SET quantidade = ? WHERE usuario_id = ? AND produto_id = ?");
            $stmt->execute([$quantidade, $userId, $produtoId]);

            $total = pegaTotalItens($conn, 'usuario_id', $userId);
            responde("sucesso", "Quantidade atualizada no carrinho do usuário!", $total);
        }
    } else {
        // Insere novo item
        $stmt = $conn->prepare("INSERT INTO tb_carrinho (usuario_id, produto_id, quantidade) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $produtoId, $quantidade]);

        $total = pegaTotalItens($conn, 'usuario_id', $userId);
        responde("sucesso", "Produto adicionado ao carrinho do usuário!", $total);
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
            $total = pegaTotalItens($conn, 'cookie_id', $cookieId);
            responde("sucesso", "Produto já está no carrinho temporário com a mesma quantidade.", $total);
        } else {
            // Atualiza a quantidade
            $stmt = $conn->prepare("UPDATE tb_carrinho SET quantidade = ? WHERE cookie_id = ? AND produto_id = ?");
            $stmt->execute([$quantidade, $cookieId, $produtoId]);

            $total = pegaTotalItens($conn, 'cookie_id', $cookieId);
            responde("sucesso", "Quantidade atualizada no carrinho temporário!", $total);
        }
    } else {
        // Insere novo item no carrinho temporário
        $stmt = $conn->prepare("INSERT INTO tb_carrinho (cookie_id, produto_id, quantidade) VALUES (?, ?, ?)");
        $stmt->execute([$cookieId, $produtoId, $quantidade]);

        $total = pegaTotalItens($conn, 'cookie_id', $cookieId);
        responde("sucesso", "Produto salvo no carrinho temporário!", $total);
    }
}