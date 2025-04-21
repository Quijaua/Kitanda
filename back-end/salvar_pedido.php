<?php
function salvarPedido($customer_id, $dados_pagamento = null, $payment, $shipment, $dataForm, $compra, $produtos, $config) {
	include('config.php');

    // echo "Data Form";
    // echo "<pre>";
    // print_r($dataForm);
    // echo "</pre><br><br>";
    // echo "Paymewnt";
    // echo "<pre>";
    // print_r($payment);
    // echo "</pre><br><br>";
    // echo "Dados pagamento";
    // echo "<pre>";
    // print_r($dados_pagamento);
    // echo "</pre>";
    // exit;

    function gerarPedidoIdSequencial($conn) {
        // Seleciona o maior pedido_id já cadastrado, convertendo-o para inteiro
        $stmt = $conn->query("SELECT MAX(CAST(pedido_id AS UNSIGNED)) AS max_id FROM tb_pedidos");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $max = isset($row['max_id']) ? (int)$row['max_id'] : 0;
        
        // Incrementa o valor
        $novo = $max + 1;
        
        // Formata o número para 6 dígitos, com zeros à esquerda
        return str_pad($novo, 6, '0', STR_PAD_LEFT);
    }
    
    // Exemplo de uso:
    $pedido_id = gerarPedidoIdSequencial($conn);

    // O payment_id pode ser usado como transacao_id
    $transacao_id = $payment['id'];

    // Usuadio
    $usuario_id = $_SESSION['user_id'];
    $asaas_usuario_id    = $customer_id;

    // Extraia os dados da compra do array $compra
    $subtotal = isset($compra['subtotal']) ? $compra['subtotal'] : 0;
    $frete    = isset($compra['frete']) ? $compra['frete'] : 0;
    $total    = isset($compra['total']) ? $compra['total'] : 0;
    $desconto = isset($compra['desconto']) ? $compra['desconto'] : 0;

    // Geral
    $status                         = isset($payment['status']) ? $payment['status'] : null;
    $link_pagamento                 = isset($payment['invoiceUrl']) ? $payment['invoiceUrl'] : null;
    $forma_pagamento                = isset($payment['billingType']) ? $payment['billingType'] : null;
    $data_criacao                   = isset($payment['dateCreated']) ? $payment['dateCreated'] : null;
    $data_pagamento                 = isset($payment['paymentDate']) ? $payment['paymentDate'] : null;

    // Cartao de credito
    $cartao_numero                  = isset($payment['creditCard']['creditCardNumber']) ? $payment['creditCard']['creditCardNumber'] : null;
    $cartao_bandeira                = isset($payment['creditCard']['creditCardBrand']) ? $payment['creditCard']['creditCardBrand'] : null;

    // Boleto
    $link_boleto                    = isset($payment['bankSlipUrl']) ? $payment['bankSlipUrl'] : null;
    $boleto_barCode                 = isset($dados_pagamento['barCode']) ? $dados_pagamento['barCode'] : null;
    $boleto_nossoNumero             = isset($dados_pagamento['nossoNumero']) ? $dados_pagamento['nossoNumero'] : null;
    $boleto_identificationField     = isset($dados_pagamento['identificationField']) ? $dados_pagamento['identificationField'] : null;

    // Pix
    $pix_encodedImage               = isset($dados_pagamento['encodedImage']) ? $dados_pagamento['encodedImage'] : null;
    $pix_payload                    = isset($dados_pagamento['payload']) ? $dados_pagamento['payload'] : null;
    $pix_expirationDate             = isset($dados_pagamento['expirationDate']) ? $dados_pagamento['expirationDate'] : null;

    // Pix/Boleto
    $data_vencimento                = isset($payment['dueDate']) ? $payment['dueDate'] : null;

    // Rastreamento
    $tracking                       = isset($shipment['tracking']) ? $shipment['tracking'] : null;
    $initial_state                  = isset($shipment['initial_state']) ? $shipment['initial_state'] : null;





    
    // Prepare a inserção na tabela tb_pedidos
    $stmt = $conn->prepare("INSERT INTO tb_pedidos (
        pedido_id,
        transacao_id,
        usuario_id,
        asaas_usuario_id,
        desconto,
        frete,
        subtotal,
        total,
        forma_pagamento,
        link_pagamento,
        link_boleto,
        status,
        data_vencimento,
        data_criacao,
        data_pagamento,
        pix_encodedImage,
        pix_payload,
        pix_expirationDate,
        boleto_barCode,
        boleto_nossoNumero,
        boleto_identificationField,
        cartao_numero,
        cartao_bandeira,
        codigo_rastreamento,
        rastreamento_status,
        created_at,
        updated_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    )");

    $stmt->execute([
        $pedido_id,
        $transacao_id,
        $usuario_id,
        $asaas_usuario_id,
        $desconto,
        $frete,
        $subtotal,
        $total,
        $forma_pagamento,
        $link_pagamento,
        $link_boleto,
        $status,
        $data_vencimento,
        $data_criacao,
        $data_pagamento,
        $pix_encodedImage,
        $pix_payload,
        $pix_expirationDate,
        $boleto_barCode,
        $boleto_nossoNumero,
        $boleto_identificationField,
        $cartao_numero,
        $cartao_bandeira,
        $tracking,
        $initial_state
    ]);
    $pedidoId = $conn->lastInsertId();

    // Insere cada item da compra na tabela tb_pedido_itens
    $stmtItem = $conn->prepare("INSERT INTO tb_pedido_itens (pedido_id, produto_id, nome, preco, quantidade, preco_total) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($produtos as $produto) {
        $stmtItem->execute([
            $pedidoId,
            $produto['id'],
            $produto['nome'],
            $produto['preco'],
            $produto['quantidade'],
            $produto['preco_total']
        ]);
    }

    // ---------------------------
    // Remover os itens comprados do carrinho
    // Crie um array com os IDs dos produtos comprados
    $purchasedIds = array_map(function($produto) {
        return $produto['id'];
    }, $produtos);
    
    if (count($purchasedIds) > 0) {
        // Cria placeholders para a cláusula IN
        $placeholders = implode(',', array_fill(0, count($purchasedIds), '?'));
        
        // Se o usuário estiver logado (usando sessão), remove os itens correspondentes
        if (isset($_SESSION['user_id'])) {
            $stmtDel = $conn->prepare("DELETE FROM tb_carrinho WHERE usuario_id = ? AND produto_id IN ($placeholders)");
            $params = array_merge([$_SESSION['user_id']], $purchasedIds);
            $stmtDel->execute($params);
        }
        // Se estiver usando cookie, remove os itens com o cookie
        if (isset($_COOKIE['cart_id'])) {
            $cookieId = $_COOKIE['cart_id'];
            $stmtDel = $conn->prepare("DELETE FROM tb_carrinho WHERE cookie_id = ? AND produto_id IN ($placeholders)");
            $params = array_merge([$cookieId], $purchasedIds);
            $stmtDel->execute($params);
        }
    }
    
    // Se o usuário estava utilizando o cookie, atualize os itens restantes para associá-los à sessão
    if (isset($_COOKIE['cart_id'])) {
        $cookieId = $_COOKIE['cart_id'];
        $stmtUpd = $conn->prepare("UPDATE tb_carrinho SET usuario_id = ?, cookie_id = NULL WHERE cookie_id = ?");
        $stmtUpd->execute([$_SESSION['user_id'], $cookieId]);
        // Remove o cookie
        setcookie('cart_id', '', time() - 3600, '/');
    }

    return $pedido_id;

}