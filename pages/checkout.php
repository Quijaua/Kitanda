<?php
// pages/checkout.php

// 1) Pega o parâmetro “pedido” (se existir)
$pedido_id = $_GET['pedido'] ?? null;

// Variáveis que serão retornadas no contexto Twig
$userData       = [];
$checkoutItems  = [];
$pedido         = [];
$subtotal       = 0.00;
$frete          = 0.00;
$desconto       = 0.00;
$total          = 0.00;
$isPedido       = false;

// 2) Se veio um pedido_id, tenta carregar o pedido existente
if (!empty($pedido_id)) {
    $stmt = $conn->prepare("
        SELECT *
        FROM tb_pedidos
        WHERE pedido_id = ?
        LIMIT 1
    ");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pedido) {
        // 2.1) Verifica data de vencimento
        $dueDate     = strtotime($pedido['data_vencimento']);
        $currentDate = strtotime(date('Y-m-d'));
        if ($currentDate > $dueDate) {
            $_SESSION['error_msg'] = "Seu pedido expirou.";
            header('Location: ' . INCLUDE_PATH . 'carrinho');
            exit;
        }

        // 2.2) Carrega dados do usuário (já logado)
        if (isset($_SESSION['user_id'])) {
            $stmtUser = $conn->prepare("SELECT * FROM tb_clientes WHERE id = ? LIMIT 1");
            $stmtUser->execute([$_SESSION['user_id']]);
            $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $userData = [
                    'email'        => $usuario['email'],
                    'name'         => $usuario['nome'],
                    'cpf'          => $usuario['cpf'],
                    'birthDate'    => $usuario['data_nascimento'],
                    'phone'        => $usuario['phone'],
                    'zipcode'      => $usuario['cep'],
                    'street'       => $usuario['endereco'],
                    'streetNumber' => $usuario['numero'],
                    'complement'   => $usuario['complemento'],
                    'district'     => $usuario['municipio'],
                    'city'         => $usuario['cidade'],
                    'state'        => $usuario['uf'],
                    'newsletter'   => (bool) $usuario['newsletter'],
                    'terms'        => 1
                ];
            }
        }

        // 2.3) Verifica status do pedido
        if (strtolower($pedido['status']) !== 'pending') {
            $_SESSION['error_msg'] = "O pedido já foi pago.";
            header('Location: ' . INCLUDE_PATH . 'carrinho');
            exit;
        }

        // 2.4) Busca itens do próprio pedido (tb_pedido_itens)
        $stmtItems = $conn->prepare("
            SELECT 
                pi.id               AS item_id,
                pi.quantidade       AS quantidade,
                p.id                AS produto_id,
                p.nome              AS produto_nome,
                p.preco             AS produto_preco,
                pi.preco_total      AS preco_unitario
            FROM tb_pedido_itens pi
            JOIN tb_produtos p ON pi.produto_id = p.id
            WHERE pi.pedido_id = ?
        ");
        $stmtItems->execute([$pedido['id']]);
        $rows = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $item) {
            // Formata URL da imagem se houver
            $stmtImg = $conn->prepare("
                SELECT imagem 
                FROM tb_produto_imagens 
                WHERE produto_id = ? 
                ORDER BY imagem ASC 
                LIMIT 1
            ");
            $stmtImg->execute([$item['produto_id']]);
            $imgRow = $stmtImg->fetch(PDO::FETCH_ASSOC);

            $imagemUrl = !empty($imgRow['imagem'])
                ? str_replace(
                    ' ',
                    '%20',
                    INCLUDE_PATH . "files/produtos/{$item['produto_id']}/{$imgRow['imagem']}"
                  )
                : INCLUDE_PATH . "assets/preview-image/product.jpg";

            $checkoutItems[] = [
                'item_id'        => (int) $item['item_id'],
                'produto_id'     => (int) $item['produto_id'],
                'produto_nome'   => $item['produto_nome'],
                'produto_preco'  => (float) $item['produto_preco'],
                'preco_unitario' => (float) $item['preco_unitario'],
                'quantidade'     => (int) $item['quantidade'],
                'imagem'         => $imagemUrl
            ];

            // Soma ao subtotal: usamos produto_preco * quantidade
            $subtotal += $item['produto_preco'] * $item['quantidade'];
        }

        // 2.5) Busca frete e desconto diretamente do pedido
        $frete    = (float) $pedido['frete'];
        $desconto = (float) $pedido['desconto'];
        $total    = $subtotal + $frete - $desconto;

        $isPedido = true;
    }
}

// 3) Se não carregou nenhum pedido válido, usamos itens do carrinho
if (!$isPedido) {
    // 3.1) Carrega itens do carrinho (igual à página de carrinho)
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare("
            SELECT 
                c.id        AS carrinho_id,
                c.produto_id,
                c.quantidade,
                p.nome      AS produto_nome,
                p.preco     AS produto_preco
            FROM tb_carrinho c
            JOIN tb_produtos p ON c.produto_id = p.id
            WHERE c.usuario_id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$userId]);

    } elseif (isset($_COOKIE['cart_id'])) {
        $cookieId = $_COOKIE['cart_id'];
        $stmt = $conn->prepare("
            SELECT 
                c.id        AS carrinho_id,
                c.produto_id,
                c.quantidade,
                p.nome      AS produto_nome,
                p.preco     AS produto_preco
            FROM tb_carrinho c
            JOIN tb_produtos p ON c.produto_id = p.id
            WHERE c.cookie_id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$cookieId]);

    } else {
        $stmt = false;
    }

    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    if (empty($rows)) {
        // Se não houver itens no carrinho, redireciona de volta
        header('Location: ' . INCLUDE_PATH . 'carrinho');
        exit;
    }

    foreach ($rows as $item) {
        // Busca imagem do produto (primeira disponível)
        $stmtImg = $conn->prepare("
            SELECT imagem 
            FROM tb_produto_imagens 
            WHERE produto_id = ?
            ORDER BY imagem ASC
            LIMIT 1
        ");
        $stmtImg->execute([$item['produto_id']]);
        $imgRow = $stmtImg->fetch(PDO::FETCH_ASSOC);

        $imagemUrl = !empty($imgRow['imagem'])
            ? str_replace(
                ' ',
                '%20',
                INCLUDE_PATH . "files/produtos/{$item['produto_id']}/{$imgRow['imagem']}"
              )
            : INCLUDE_PATH . "assets/preview-image/product.jpg";

        $checkoutItems[] = [
            'item_id'        => (int) $item['carrinho_id'],
            'produto_id'     => (int) $item['produto_id'],
            'produto_nome'   => $item['produto_nome'],
            'produto_preco'  => (float) $item['produto_preco'],
            'preco_unitario' => (float) $item['produto_preco'],
            'quantidade'     => (int) $item['quantidade'],
            'imagem'         => $imagemUrl
        ];

        // Somatório de subtotal
        $subtotal += $item['produto_preco'] * $item['quantidade'];
    }

    // 3.2) Define frete e desconto como zero (ou valores padrão)
    $frete    = 0.00;
    $desconto = 0.00;
    $total    = $subtotal + $frete - $desconto;

    // 3.3) Se o usuário estiver logado, pré‐preenche dados
    if (isset($_SESSION['user_id'])) {
        $stmtUser = $conn->prepare("SELECT * FROM tb_clientes WHERE id = ? LIMIT 1");
        $stmtUser->execute([$_SESSION['user_id']]);
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $userData = [
                'email'        => $usuario['email'],
                'name'         => $usuario['nome'],
                'cpf'          => $usuario['cpf'],
                'birthDate'    => $usuario['data_nascimento'],
                'phone'        => $usuario['phone'],
                'zipcode'      => $usuario['cep'],
                'street'       => $usuario['endereco'],
                'streetNumber' => $usuario['numero'],
                'complement'   => $usuario['complemento'],
                'district'     => $usuario['municipio'],
                'city'         => $usuario['cidade'],
                'state'        => $usuario['uf'],
                'newsletter'   => (bool) $usuario['newsletter'],
                'terms'        => 1
            ];
        }
    }
}

$checkout_data = (isset($_SESSION['checkout_data'])) ? $_SESSION['checkout_data'] : $userData;
$userDataSession = [
    'email'        => isset($checkout_data['email']) && !empty($checkout_data['email']) ? $checkout_data['email'] : null,
    'name'         => isset($checkout_data['name']) && !empty($checkout_data['name']) ? $checkout_data['name'] : null,
    'cpf'          => isset($checkout_data['cpf']) && !empty($checkout_data['cpf']) ? $checkout_data['cpf'] : null,
    'birthDate'    => isset($checkout_data['birthDate']) && !empty($checkout_data['birthDate']) ? $checkout_data['birthDate'] : null,
    'phone'        => isset($checkout_data['phone']) && !empty($checkout_data['phone']) ? $checkout_data['phone'] : null,
    'zipcode'      => isset($checkout_data['zipcode']) && !empty($checkout_data['zipcode']) ? $checkout_data['zipcode'] : null,
    'street'       => isset($checkout_data['street']) && !empty($checkout_data['street']) ? $checkout_data['street'] : null,
    'streetNumber' => isset($checkout_data['streetNumber']) && !empty($checkout_data['streetNumber']) ? $checkout_data['streetNumber'] : null,
    'complement'   => isset($checkout_data['complement']) && !empty($checkout_data['complement']) ? $checkout_data['complement'] : null,
    'district'     => isset($checkout_data['district']) && !empty($checkout_data['district']) ? $checkout_data['district'] : null,
    'city'         => isset($checkout_data['city']) && !empty($checkout_data['city']) ? $checkout_data['city'] : null,
    'state'        => isset($checkout_data['state']) && !empty($checkout_data['state']) ? $checkout_data['state'] : null,
    'newsletter'   => isset($checkout_data['newsletter']) 
                        ? (bool) $checkout_data['newsletter'] 
                        : null,
    'terms'        => isset($checkout_data['terms']) 
                        ? (bool) $checkout_data['terms'] 
                        : null,
];

// 4) Retorna todo o contexto para o Twig
return [
    'isPedido'      => $isPedido,
    'pedido'        => $pedido,
    'user'          => $userData,
    'sessionUser'   => $userDataSession,
    'checkoutItems' => $checkoutItems,
    'subtotal'      => $subtotal,
    'frete'         => $frete,
    'desconto'      => $desconto,
    'total'         => $total,
];