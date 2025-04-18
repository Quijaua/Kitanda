<?php
// Verifica se o usuário está logado ou se há um cookie
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT c.*, p.link AS produto_link, p.nome AS produto_nome, pi.imagem AS produto_imagem, p.preco AS produto_preco, u.nome AS empreendedora
                            FROM tb_carrinho c
                            JOIN tb_produtos p ON c.produto_id = p.id
                            LEFT JOIN tb_produto_imagens pi ON p.id = pi.produto_id
                            JOIN tb_clientes u ON p.criado_por = u.id
                            WHERE c.usuario_id = ?");
    $stmt->execute([$userId]);
} elseif (isset($_COOKIE['cart_id'])) {
    $cookieId = $_COOKIE['cart_id'];
    $stmt = $conn->prepare("SELECT c.*, p.link AS produto_link, p.nome AS produto_nome, pi.imagem AS produto_imagem, p.preco AS produto_preco, u.nome AS empreendedora
                            FROM tb_carrinho c
                            JOIN tb_produtos p ON c.produto_id = p.id
                            LEFT JOIN tb_produto_imagens pi ON p.id = pi.produto_id
                            JOIN tb_clientes u ON p.criado_por = u.id
                            WHERE c.cookie_id = ?");
    $stmt->execute([$cookieId]);
} else {
    // Caso não exista usuário logado nem cookie, o carrinho estará vazio.
    $stmt = false;
}

$cartItems = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<!-- Modal Sucesso -->
<div class="modal modal-blur fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
                <!-- Ícone de sucesso -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-green icon-lg"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                <h3>Tem certeza?</h3>
                <div class="text-secondary">Deseja remover o produto do seu carrinho?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-3 btn-success w-100" data-bs-dismiss="modal"> Não, manter </a>
                        </div>
                        <div class="col">
                            <!--a href="<?= INCLUDE_PATH; ?>carrinho" class="btn btn-success btn-4 w-100" data-bs-dismiss="modal"> Ir para o carrinho </a-->
                            <button id="remove-item" type="button" class="btn btn-warning btn-4 w-100" data-bs-dismiss="modal" id="btn-modal-success"> Sim, remover </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item active">Meu Carrinho</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <div class="col-md-12">
                <h3 style="font-size: 2.5rem; line-height: normal; font-weight: 800;">Meu Carrinho</h3>
            </div>

            <div class="col-md-8">
                <?php if(count($cartItems) > 0): ?>
                    <?php foreach($cartItems as $item): ?>
                        <?php
                            $item['produto_imagem'] = !empty($item['produto_imagem'])
                                                    ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $item['produto_id'] . "/" . $item['produto_imagem'])
                                                    : INCLUDE_PATH . "assets/preview-image/product.jpg";
                        ?>
                        <div class="card bg-dark-lt p-5 mb-3" id="item-<?= $item['id']; ?>">
                            <div class="row align-items-center mt-0">
                                <div class="col-3 row g-2 g-md-3 mt-0">
                                    <div class="col-12 mt-0">
                                        <a data-fslightbox="gallery" href="<?= $item['produto_imagem']; ?>">
                                            <!-- Photo -->
                                            <div class="img-responsive img-responsive-1x1 rounded-3 border" style="background-image: url(<?= $item['produto_imagem']; ?>)"></div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-9 row align-items-center ms-auto">
                                    <div class="col-10">
                                        <div>
                                            <a href="<?= INCLUDE_PATH . "p/" . htmlspecialchars($item['produto_link']); ?>" class="text-body">
                                                <h3 class="h2 mb-0"><?= htmlspecialchars($item['produto_nome']); ?></h3>
                                            </a>
                                            <div class="text-secondary mb-3">
                                                Produzido por: 
                                                <a href="#" class="text-muted">
                                                    <?= htmlspecialchars($item['empreendedora']); ?>
                                                </a>
                                            </div>
                                            <h3 class="h2 mb-4">R$ <?= number_format($item['produto_preco'], 2, ',', '.'); ?></h3>
                                            <!-- Link de exclusão com classes e data attributes -->
                                            <a href="#" class="text-muted remove-item" data-item-id="<?= $item['id']; ?>" data-produto-id="<?= $item['produto_id']; ?>">Excluir</a>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <!-- Input de quantidade com data attributes -->
                                        <input type="number" class="form-control quantidade-produto" 
                                               value="<?= $item['quantidade']; ?>" min="1" 
                                               data-price="<?= $item['produto_preco']; ?>"
                                               data-produto-id="<?= $item['produto_id']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div id="cart-empty" <?php if(count($cartItems) > 0) echo 'style="display: none;"'; ?> >
                    <h2>Seu carrinho está vazio :(</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-dark-lt">
                    <div class="card-body">
                        <h3 class="card-title">Resumo do pedido</h3>

                        <?php 
                        // Calcula o total dos itens do carrinho
                        $total = 0;
                        foreach ($cartItems as $item) {
                            $total += $item['produto_preco'] * $item['quantidade'];
                        }
                        ?>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <td>Itens do carrinho</td>
                                    <!-- id "cart-total" para atualização dinâmica -->
                                    <td id="cart-total" class="w-10 fw-bold text-end">R$ <?= number_format($total, 2, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td>Desconto</td>
                                    <td class="w-10 fw-bold text-end">R$ 0,00</td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="my-2">

                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">TOTAL</td>
                                    <td id="cart-total-final" class="w-10 fw-bold text-end">R$ <?= number_format($total, 2, ',', '.'); ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-center">
                            <div id="bloco-checkout" <?php if(count($cartItems) == 0) echo('style="display: none;"'); ?> >
                                <a href="<?= INCLUDE_PATH; ?>checkout" class="btn btn-6 btn-dark btn-pill w-100 mb-3"> Finalizar compra </a>
                                <a href="<?= INCLUDE_PATH; ?>produtos" class="text-muted"> Continuar comprando </a>
                            </div>

                            <div id="bloco-empty" <?php if(count($cartItems) > 0) echo('style="display: none;"'); ?> >
                                <a href="<?= INCLUDE_PATH; ?>produtos" class="btn btn-6 btn-dark btn-pill w-100 mb-3"> VOLTAR AS COMPRAS </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Função para formatar o valor em reais
    function formatarReais(valor) {
        return 'R$ ' + Number(valor).toFixed(2).replace('.', ',');
    }
    
    // Função para recalcular o total do carrinho
    function atualizarTotalCarrinho() {
        var total = 0;
        $('.quantidade-produto').each(function() {
            var quantidade = parseInt($(this).val(), 10);
            var preco = parseFloat($(this).data('price'));
            total += quantidade * preco;
        });
        $('#cart-total').text(formatarReais(total));
        $('#cart-total-final').text(formatarReais(total));

        if (total === 0) {
            $('#bloco-checkout').hide();
            $('#bloco-empty').show();
            $('#cart-empty').show();
        }
    }
    
    // Atualiza o carrinho quando o input de quantidade é alterado
    $('.quantidade-produto').on('click', function() {
        var input = $(this);
        var quantidade = parseInt(input.val(), 10);
        if (isNaN(quantidade) || quantidade < 1) {
            input.val(1);
            quantidade = 1;
        }
        atualizarTotalCarrinho();
        var produtoId = input.data('produto-id');
        $.ajax({
            url: "<?= INCLUDE_PATH; ?>back-end/carrinho/adicionar.php",
            method: 'POST',
            data: {
                produto_id: produtoId,
                quantidade: quantidade
            },
            success: function(response) {
                try {
                    var res = JSON.parse(response);
                    if (res.status === 'sucesso') {
                        console.log("Atualização: " + res.mensagem);
                    } else {
                        alert("Erro: " + res.mensagem);
                    }
                } catch(e) {
                    console.log("Resposta inválida: " + response);
                }
            },
            error: function(xhr, status, error) {
                console.log("Erro AJAX: " + error);
            }
        });
    });
    
    // Ao clicar no botão de excluir
    $('.remove-item').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);

        // Exibe o modal de confirmação
        var myModal = new bootstrap.Modal(document.getElementById('modal-delete'));
        myModal.show();

        $('#remove-item').on('click', function() {
            // Obtém o id do item no carrinho (registro no banco)
            var itemId = btn.data('item-id');
            $.ajax({
                url: "<?= INCLUDE_PATH; ?>back-end/carrinho/remover.php",
                method: "POST",
                data: {
                    item_id: itemId
                },
                success: function(response) {
                    try {
                        var res = JSON.parse(response);
                        if (res.status === "sucesso") {
                            // Remove o card do item da tela
                            $("#item-" + itemId).fadeOut(300, function() {
                                $(this).remove();
                                atualizarTotalCarrinho();
                            });
                            console.log("Remoção: " + res.mensagem);
                        } else {
                            alert("Erro: " + res.mensagem);
                        }
                    } catch(e) {
                        console.log("Resposta inválida: " + response);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Erro AJAX: " + error);
                }
            });
        });

        // Obtém o id do item no carrinho (registro no banco)
        /*var itemId = btn.data('item-id');
        $.ajax({
            url: "<?= INCLUDE_PATH; ?>back-end/carrinho/remover.php",
            method: "POST",
            data: {
                item_id: itemId
            },
            success: function(response) {
                try {
                    var res = JSON.parse(response);
                    if (res.status === "sucesso") {
                        // Remove o card do item da tela
                        $("#item-" + itemId).fadeOut(300, function() {
                            $(this).remove();
                            atualizarTotalCarrinho();
                        });
                        console.log("Remoção: " + res.mensagem);
                    } else {
                        alert("Erro: " + res.mensagem);
                    }
                } catch(e) {
                    console.log("Resposta inválida: " + response);
                }
            },
            error: function(xhr, status, error) {
                console.log("Erro AJAX: " + error);
            }
        });*/
    });
});
</script>