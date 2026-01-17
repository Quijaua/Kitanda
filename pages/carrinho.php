<?php
// pages/carrinho.php

// 1) Verifica se há usuário logado ou cookie de carrinho
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT 
            c.id AS carrinho_id,
            c.produto_id,
            c.quantidade,
            p.link AS produto_link,
            p.nome AS produto_nome,
            p.estoque as produto_estoque,
            p.preco AS produto_preco,
            p.codigo_produto AS produto_codigo,
            pi.imagem AS produto_imagem,
            u.nome AS empreendedora
        FROM tb_carrinho c
        JOIN tb_produtos p ON c.produto_id = p.id
        LEFT JOIN tb_produto_imagens pi ON p.id = pi.produto_id
        JOIN tb_clientes u ON p.criado_por = u.id
        WHERE c.usuario_id = ?
        GROUP BY c.id
    ");
    $stmt->execute([$userId]);

} elseif (isset($_COOKIE['cart_id'])) {
    $cookieId = $_COOKIE['cart_id'];
    $stmt = $conn->prepare("
        SELECT 
            c.id AS carrinho_id,
            c.produto_id,
            c.quantidade,
            p.link AS produto_link,
            p.nome AS produto_nome,
            p.estoque as produto_estoque,
            p.preco AS produto_preco,
            p.codigo_produto AS produto_codigo,
            pi.imagem AS produto_imagem,
            u.nome AS empreendedora
        FROM tb_carrinho c
        JOIN tb_produtos p ON c.produto_id = p.id
        LEFT JOIN tb_produto_imagens pi ON p.id = pi.produto_id
        JOIN tb_clientes u ON p.criado_por = u.id
        WHERE c.cookie_id = ?
        GROUP BY c.id
    ");
    $stmt->execute([$cookieId]);

} else {
    // Sem usuário nem cookie
    $stmt = false;
}

// 2) Monta o array de itens do carrinho
$cartItems = [];
if ($stmt) {
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $item) {
        // Formata URL da imagem ou image placeholder
        $imagemUrl = !empty($item['produto_imagem'])
            ? str_replace(
                ' ',
                '%20',
                INCLUDE_PATH . "files/produtos/{$item['produto_id']}/{$item['produto_imagem']}"
              )
            : INCLUDE_PATH . "assets/preview-image/product.jpg";

        $cartItems[] = [
            'carrinho_id'     => (int) $item['carrinho_id'],
            'produto_id'      => (int) $item['produto_id'],
            'produto_link'    => $item['produto_link'],
            'produto_nome'    => $item['produto_nome'],
            'produto_estoque' => (int) $item['produto_estoque'],
            'produto_preco'   => (float) $item['produto_preco'],
            'produto_imagem'  => $imagemUrl,
            'empreendedora'   => $item['empreendedora'],
            'quantidade'      => (int) $item['quantidade'],
            'codigo'          => $item['produto_codigo'],
        ];
    }
}

// 3) Retorna contexto para o Twig
return [
    'cartItems' => $cartItems,
    'has_items' => count($cartItems) > 0,
];