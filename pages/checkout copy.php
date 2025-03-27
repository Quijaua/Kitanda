<?php
// Carrega os itens do carrinho para o checkout
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT c.*, p.nome AS produto_nome, p.preco AS produto_preco 
                            FROM tb_carrinho c 
                            JOIN tb_produtos p ON c.produto_id = p.id 
                            WHERE c.usuario_id = ?");
    $stmt->execute([$userId]);
} elseif (isset($_COOKIE['cart_id'])) {
    $cookieId = $_COOKIE['cart_id'];
    $stmt = $conn->prepare("SELECT c.*, p.nome AS produto_nome, p.preco AS produto_preco 
                            FROM tb_carrinho c 
                            JOIN tb_produtos p ON c.produto_id = p.id 
                            WHERE c.cookie_id = ?");
    $stmt->execute([$cookieId]);
} else {
    $stmt = false;
}

$checkoutItems = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Calcula o subtotal dos itens
$subtotal = 0;
foreach ($checkoutItems as $item) {
    $subtotal += $item['produto_preco'] * $item['quantidade'];
}

// Valores do frete e desconto (podem ser dinâmicos)
$frete = 10.00;
$desconto = 0.00;
$total = $subtotal + $frete - $desconto;
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ul class="steps steps-counter my-4">
                    <li class="step-item active"> Informações Gerais </li>
                    <li class="step-item"> Pagamento </li>
                    <li class="step-item"> Confirmação </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <div class="col-md-8">
                <div class="card bg-dark-lt">
                    <div class="card-body row">
                        <div class="col-md-6">
                            <h3 class="card-title">Informações pessoais</h3>

							<div class="row">

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="email" class="form-control" name="email" id="field-email" placeholder="nome@exemplo.com">
										<label for="email">Seu e-mail</label>
									</div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="text" class="form-control" name="name" id="field-name" placeholder="Primeiro nome">
										<label for="field-name">Nome completo</label>
									</div>
								</div>

								<div class="col-md-12 mb-3" id="div-cpf-field">
									<div class="form-floating">
										<input type="text" class="form-control" name="cpfCnpj" id="field-cpf" placeholder="CPF">
										<label for="field-cpf">CPF/CNPJ</label>
									</div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="date" class="form-control" name="birth-date" id="field-birth-date" placeholder="(99) 99999-9999" maxlength="15">
										<label for="birth-date">Data de Nascimento</label>
									</div>
								</div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-4 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="ddi" id="field-ddd" placeholder="Sobrenome">
                                                <label for="field-ddd">DDI</label>
                                            </div>
                                        </div>

                                        <div class="col-md-8 mb-3">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" name="phone" id="field-phone" placeholder="(99) 99999-9999" maxlength="15">
                                                <label for="phone">Telefone</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-md-6">
                            <h3 class="card-title">Informações de cobrança</h3>

							<div class="row">

                                <div class="col-md-12 mb-3">
                                    <div class="form-floating">
                                        <input onblur="getCepData()" type="text" class="form-control" name="postalCode" id="field-zipcode" placeholder="CEP endereço">
                                        <label for="field-zipcode">CEP</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 country-brasil">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="address" id="field-street" placeholder="Endereço">
                                        <label for="field-street">Endereço</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-4 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-center" name="addressNumber" id="field-street-number" placeholder="Número endereço">
                                                <label for="field-street-number">Número</label>
                                            </div>
                                        </div>

                                        <div class="col-md-8 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="complement" id="field-complement" placeholder="Complemento endereço">
                                                <label for="field-complement">Complemento</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 country-brasil">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="province" id="field-district" placeholder="Bairro endereço">
                                        <label for="field-district">Bairro</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-8 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="city" id="field-city" placeholder="Cidade endereço">
                                                <label for="field-city">Cidade</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="state" id="field-state" placeholder="UF">
                                                <label for="field-state">UF</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="private" name="private">
                                    <span class="form-check-label">Sou estrageiro(a) estou fora do Brasil</span>
                                </label>
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="newsletter" name="newsletter">
                                    <span class="form-check-label">Quero receber as novidades das Mulheres Empreendedoras da Amazônia.</span>
                                </label>
                                <hr class="my-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="newsletter" name="newsletter">
                                    <span class="form-check-label">Declaro que li e aceito, as <a href="#">condições de compra</a>, <a href="#">políticas de cancelamento</a> e os <a href="#">Termos de Uso</a> da plataforma, estando de acordo com todos os termos das tarifas e serviços oferecidos pelas Mulheres Empreendedoras da Amazônia.</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-dark-lt">
                    <div class="card-body">
                        <h3 class="card-title">Seu pedido</h3>

                        <!-- Tabela de itens com id para facilitar o jQuery -->
                        <table class="table table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="w-1 text-center">Quant.</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="w-1 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="checkout-items">
                                <?php if(count($checkoutItems) > 0): ?>
                                    <?php foreach($checkoutItems as $item): ?>
                                        <tr id="checkout-item-<?= $item['id']; ?>">
                                            <td><?= htmlspecialchars($item['produto_nome']); ?></td>
                                            <td class="w-1 text-center"><?= $item['quantidade']; ?></td>
                                            <td class="text-center">
                                                R$ <?= number_format($item['produto_preco'] * $item['quantidade'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="w-1 text-center">
                                                <a href="#" class="text-muted remove-checkout-item" data-item-id="<?= $item['id']; ?>">
                                                    <!-- Ícone de lixeira -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                                        class="icon icon-1 icon-tabler icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum item adicionado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <hr class="my-3">

                        <!-- Valores de frete e desconto com data-value para uso no JS -->
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Frete</td>
                                    <td id="frete_valor" class="w-10 fw-bold text-end" data-value="<?= $frete; ?>">
                                        R$ <?= number_format($frete, 2, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Desconto</td>
                                    <td id="desconto_valor" class="w-10 fw-bold text-end" data-value="<?= $desconto; ?>">
                                        R$ <?= number_format($desconto, 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="my-3">

                        <!-- Tabela de TOTAL com id para atualizar -->
                        <table class="table table-sm table-borderless mb-3">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">TOTAL</td>
                                    <td id="total_valor" class="w-10 fw-bold text-end">
                                        R$ <?= number_format($total, 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <a href="#" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout"> Confirmar </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function() {

    // Função para recalcular os totais do checkout
    function recalcularTotais() {
        var subtotal = 0;
        // Percorre cada linha de item dentro do tbody com id "checkout-items"
        $('#checkout-items tr').each(function(){
            // Pega o valor do subtotal a partir do texto da terceira coluna (índice 2)
            var cellText = $(this).find('td').eq(2).text().trim(); // Ex: "R$ 123,00"
            // Remove "R$", os pontos e troca a vírgula por ponto para converter para número
            var valorStr = cellText.replace("R$", "").replace(/\./g, "").replace(",", ".");
            var valor = parseFloat(valorStr);
            if(!isNaN(valor)) {
                subtotal += valor;
            }
        });
        // Pega frete e desconto dos atributos data
        var frete = parseFloat($('#frete_valor').data('value')) || 0;
        var desconto = parseFloat($('#desconto_valor').data('value')) || 0;
        var total = subtotal + frete - desconto;
        // Atualiza o TOTAL na tabela
        $('#total_valor').text('R$ ' + total.toFixed(2).replace('.', ','));
    }

    // Remover item do checkout via AJAX
    $('.remove-checkout-item').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var itemId = btn.data('item-id');
        if (!confirm("Tem certeza que deseja remover este item?")) {
            return;
        }
        $.ajax({
            url: '<?= INCLUDE_PATH; ?>back-end/carrinho/remover.php',
            method: 'POST',
            data: { item_id: itemId },
            success: function(response) {
                try {
                    var res = JSON.parse(response);
                    if (res.status === "sucesso") {
                        // Remove a linha do item e recalcule os totais
                        $("#checkout-item-" + itemId).fadeOut(300, function() {
                            $(this).remove();
                            recalcularTotais();
                        });
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

    // Ação do botão confirmar
    $('.confirm-checkout').on('click', function(e) {
        e.preventDefault();
        window.location.href = '<?= INCLUDE_PATH; ?>checkout/confirmacao.php';
    });
});
</script>