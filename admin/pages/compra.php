<?php
    $order_id = $_GET['pedido'] ?? null;
    if (!$order_id) {
        $_SESSION['error_msg'] = "Pedido não identificado.";
        header("Location: " . INCLUDE_PATH . "carrinho");
        exit;
    }

    // Busca o pedido na tabela tb_pedidos usando o campo pedido_id
    $stmt = $conn->prepare("SELECT * FROM tb_pedidos WHERE pedido_id = ?");
    $stmt->execute([$order_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pedido) {
        $_SESSION['error_msg'] = "Pedido não encontrado.";
        header("Location: " . INCLUDE_PATH . "carrinho");
        exit;
    }

    // Busca os itens do pedido na tabela tb_pedido_itens
    $stmtItens = $conn->prepare("SELECT * FROM tb_pedido_itens WHERE pedido_id = ?");
    $stmtItens->execute([$pedido['id']]);
    $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

    $pedido['status_traduzido'] = $pedido['status'] == 'CONFIRMED' ? "Confirmado" : ($pedido['status'] == 'PENDING' ? "Pendente" : ($pedido['status'] == 'OVERDUE' ? "Vencido" : ($pedido['status'] == 'CANCELED' ? "Cancelado" : "Indefinido")));
    $pedido['forma_pagamento_traduzido'] = $pedido['forma_pagamento'] == 'PIX' ? "Pix" : ($pedido['status'] == 'CREDIT_CARD' ? "Cartão de crédito" : ($pedido['status'] == 'BOLETO' ? "Boleto Bancário" : "Indefinido"));
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Compra #<?= $pedido['pedido_id']; ?>
                </h2>
                <div class="text-secondary mt-1">Aqui estão os detalhes da sua compra.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <ol class="breadcrumb breadcrumb-muted" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_USER; ?>compras">Compras</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detalhes da Compra</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <!-- Detalhes Gerais do Pedido -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Detalhes do Pedido</h3>
                    </div>
                    <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">ID do Pedido:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($pedido['pedido_id']); ?></dd>
                            <dt class="col-sm-4">Data Criação:</dt>
                            <dd class="col-sm-8"><?= date("d/m/Y H:i", strtotime($pedido['data_criacao'])); ?></dd>
                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($pedido['status_traduzido']); ?></dd>
                        </dl>
                        </div>
                        <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Forma de Pagamento:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($pedido['forma_pagamento_traduzido']); ?></dd>
                            <dt class="col-sm-4">Total:</dt>
                            <dd class="col-sm-8">R$ <?= number_format($pedido['total'], 2, ',', '.'); ?></dd>
                        </dl>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                <!-- Linha do Tempo do Pedido -->
                <div class="<?= (strtolower($pedido['status']) == 'pending') ? "col-8" : "col-12" ?>">
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Linha do Tempo do Pedido</h3>
                    </div>
                    <div class="card-body">
                        <!-- Exemplo de timeline estática; você pode integrar com dados reais futuramente -->
                        <ul class="steps steps-vertical timeline">
                            <li class="step-item timeline-item">
                                <div class="h4 m-0">Pedido realizado</div>
                                <div class="text-secondary">
                                    Seu pedido foi registrado com sucesso em <?= date("d/m/Y H:i", strtotime($pedido['data_criacao'])); ?>. Estamos processando os detalhes e logo entraremos em contato com mais informações.
                                </div>
                            </li>

                            <?php if(strtolower($pedido['status']) != 'pending'): ?>
                                <li class="step-item timeline-item">
                                    <div class="h4 m-0">Pagamento confirmado</div>
                                    <div class="text-secondary">
                                        O pagamento foi recebido com sucesso em <?= !empty($pedido['data_pagamento']) ? date("d/m/Y H:i", strtotime($pedido['data_pagamento'])) : "N/D"; ?>. Agora estamos preparando seu pedido para envio.
                                    </div>
                                </li>
                            <?php else: ?>
                                <li class="step-item timeline-item active">
                                    <div class="h4 m-0">Aguardando pagamento</div>
                                    <div class="text-secondary">
                                        Seu pedido foi registrado, mas ainda não recebemos a confirmação do pagamento. Assim que o pagamento for confirmado, iniciaremos o processamento do seu pedido.
                                    </div>
                                </li>
                            <?php endif; ?>

                            <li class="step-item timeline-item">
                                <div class="h4 m-0">Pedido em separação</div>
                                <div class="text-secondary">
                                    Seu pedido está sendo preparado para envio. Em breve, você receberá o código de rastreamento para acompanhar a entrega.
                                </div>
                            </li>

                            <li class="step-item timeline-item">
                                <div class="h4 m-0">Enviado para transporte</div>
                                <div class="text-secondary">
                                    Seu pedido já está a caminho! Assim que houver uma atualização, você poderá acompanhar o status do envio pelo código de rastreamento.
                                </div>
                            </li>

                            <li class="step-item timeline-item">
                                <div class="h4 m-0">Produto entregue</div>
                                <div class="text-secondary">
                                    Seu pedido foi entregue com sucesso! Esperamos que você goste da sua compra. Caso precise de suporte, entre em contato conosco.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                </div>
                <!-- Dados de Pagamento (exibe apenas se o pedido estiver pendente) -->
                <?php if(strtolower($pedido['status']) == 'pending'): ?>
                <div class="col-4">
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Dados de Pagamento</h3>
                    </div>
                    <div class="card-body">
                    <?php if($pedido['forma_pagamento'] == 'PIX'): ?>
                        <div class="text-center">
                        <p>Pagamento via Pix pendente. Use o QR Code abaixo para pagar:</p>
                        <?php if(!empty($pedido['pix_encodedImage'])): ?>
                            <img src="data:image/png;base64,<?= $pedido['pix_encodedImage']; ?>" alt="QR Code Pix" style="max-width: 200px;">
                        <?php else: ?>
                            <p>QR Code não disponível.</p>
                        <?php endif; ?>
                        <?php if(!empty($pedido['pix_payload'])): ?>
                            <p><strong>Chave Pix:</strong> <?= htmlspecialchars($pedido['pix_payload']); ?></p>
                        <?php endif; ?>
                        </div>
                    <?php elseif($pedido['forma_pagamento'] == 'BOLETO'): ?>
                        <div class="text-center">
                        <p>Pagamento via Boleto pendente. Clique no botão abaixo para visualizar o boleto:</p>
                        <?php if(!empty($pedido['link_boleto'])): ?>
                            <a href="<?= $pedido['link_boleto']; ?>" target="_blank" class="btn btn-primary">Visualizar Boleto</a>
                        <?php else: ?>
                            <p>Boleto não disponível.</p>
                        <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Os dados de pagamento não estão disponíveis.</div>
                    <?php endif; ?>
                    </div>
                </div>
                </div>
                <?php endif; ?>
                <!-- Itens do Pedido -->
                <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Itens do Pedido</h3>
                    </div>
                    <div class="card-body">
                    <?php if(count($itens) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($itens as $item): ?>
                            <tr>
                            <td><?= htmlspecialchars($item['nome']); ?></td>
                            <td><?= $item['quantidade']; ?></td>
                            <td>R$ <?= number_format($item['preco'], 2, ',', '.'); ?></td>
                            <td>R$ <?= number_format($item['preco_total'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">Nenhum item encontrado para este pedido.</div>
                    <?php endif; ?>
                    </div>
                </div>
                </div>

        </div>
    </div>
</div>