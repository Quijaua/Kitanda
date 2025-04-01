<!-- Inclui o CSS do Card.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.5.0/card.min.css">
<style>
    .card-wrapper {
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
</style>

<?php
// Carrega os itens do carrinho para o checkout
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT c.*, p.id AS product_id, p.nome AS produto_nome, p.preco AS produto_preco 
                            FROM tb_carrinho c 
                            JOIN tb_produtos p ON c.produto_id = p.id 
                            WHERE c.usuario_id = ?");
    $stmt->execute([$userId]);
} elseif (isset($_COOKIE['cart_id'])) {
    $cookieId = $_COOKIE['cart_id'];
    $stmt = $conn->prepare("SELECT c.*, p.id AS product_id, p.nome AS produto_nome, p.preco AS produto_preco 
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
                    <li class="step-item active" id="step-item-1"> Informações Gerais </li>
                    <li class="step-item" id="step-item-2"> Pagamento </li>
                    <li class="step-item" id="step-item-3"> Confirmação </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <!-- CHECKOUT ETAPA 1 - Informações Gerais -->

            <div class="col-md-8" id="step-1">
                <div class="card bg-dark-lt">
                    <div class="card-body row">
                        <div class="col-md-6">
                            <h3 class="card-title">Informações pessoais</h3>

							<div class="row">

								<div class="col-md-12 mb-3 d-none">
									<div class="form-floating">
										<input type="email" class="form-control" name="email" id="field-email" placeholder="nome@exemplo.com">
										<label for="field-email">Seu e-mail</label>
									</div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="email" class="form-control" name="eee" id="field-eee" placeholder="nome@exemplo.com" value="<?= $_SESSION['checkout_data']['email'] ?? null; ?>">
										<label for="field-eee">Seu e-mail</label>
									</div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="text" class="form-control" name="name" id="field-name" placeholder="Primeiro nome" value="<?= $_SESSION['checkout_data']['name'] ?? null; ?>">
										<label for="field-name">Nome completo</label>
									</div>
								</div>

								<div class="col-md-12 mb-3" id="div-cpf-field">
									<div class="form-floating">
										<input type="text" class="form-control" name="cpfCnpj" id="field-cpf" placeholder="CPF" value="<?= $_SESSION['checkout_data']['cpf'] ?? null; ?>">
										<label for="field-cpf">CPF/CNPJ</label>
									</div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="date" class="form-control" name="birth-date" id="field-birth-date" placeholder="(99) 99999-9999" maxlength="15" value="<?= $_SESSION['checkout_data']['birthDate'] ?? null; ?>">
										<label for="birth-date">Data de Nascimento</label>
									</div>
								</div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-4 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="ddi" id="field-ddd" placeholder="Sobrenome" value="<?= $_SESSION['checkout_data']['ddd'] ?? null; ?>">
                                                <label for="field-ddd">DDI</label>
                                            </div>
                                        </div>

                                        <div class="col-md-8 mb-3">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" name="phone" id="field-phone" placeholder="(99) 99999-9999" maxlength="15" value="<?= $_SESSION['checkout_data']['phone'] ?? null; ?>">
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
                                        <input onblur="getCepData()" type="text" class="form-control" name="postalCode" id="field-zipcode" placeholder="CEP endereço" value="<?= $_SESSION['checkout_data']['zipcode'] ?? null; ?>">
                                        <label for="field-zipcode">CEP</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 country-brasil">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="address" id="field-street" placeholder="Endereço" value="<?= $_SESSION['checkout_data']['street'] ?? null; ?>">
                                        <label for="field-street">Endereço</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-4 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-center" name="addressNumber" id="field-street-number" placeholder="Número endereço" value="<?= $_SESSION['checkout_data']['streetNumber'] ?? null; ?>">
                                                <label for="field-street-number">Número</label>
                                            </div>
                                        </div>

                                        <div class="col-md-8 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="complement" id="field-complement" placeholder="Complemento endereço" value="<?= $_SESSION['checkout_data']['complement'] ?? null; ?>">
                                                <label for="field-complement">Complemento</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 country-brasil">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="province" id="field-district" placeholder="Bairro endereço" value="<?= $_SESSION['checkout_data']['district'] ?? null; ?>">
                                        <label for="field-district">Bairro</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-8 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="city" id="field-city" placeholder="Cidade endereço" value="<?= $_SESSION['checkout_data']['city'] ?? null; ?>">
                                                <label for="field-city">Cidade</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="state" id="field-state" placeholder="UF" value="<?= $_SESSION['checkout_data']['state'] ?? null; ?>">
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
                                    <input class="form-check-input" type="checkbox" value="1" id="private" name="private" <?= (isset($_SESSION['checkout_data']['private']) && $_SESSION['checkout_data']['private'] == 1) ? 'checked' : null; ?>>
                                    <span class="form-check-label">Sou estrageiro(a) estou fora do Brasil</span>
                                </label>
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="newsletter" name="newsletter" <?= (isset($_SESSION['checkout_data']['newsletter']) && $_SESSION['checkout_data']['newsletter'] == 1) ? 'checked' : null; ?>>
                                    <span class="form-check-label">Quero receber as novidades das Mulheres Empreendedoras da Amazônia.</span>
                                </label>
                                <hr class="my-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" <?= (isset($_SESSION['checkout_data']['terms']) && $_SESSION['checkout_data']['terms'] == 1) ? 'checked' : null; ?>>
                                    <span class="form-check-label">Declaro que li e aceito, as <a href="#">condições de compra</a>, <a href="#">políticas de cancelamento</a> e os <a href="#">Termos de Uso</a> da plataforma, estando de acordo com todos os termos das tarifas e serviços oferecidos pelas Mulheres Empreendedoras da Amazônia.</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- CHECKOUT ETAPA 2 - Pagamento -->

            <div class="col-md-8 d-none" id="step-2">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card bg-dark-lt">
                            <div class="card-body row">
                                <h3 class="card-title">Informações pessoais</h3>
        
                                <div class="col-md-12 mb-3">
                                    <div class="form-control-plaintext" id="saved-name">Nome do Usuário</div>
                                </div>
        
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating">
                                        <input type="disabled-email" class="form-control" name="disabled-email" id="saved-email" placeholder="nome@exemplo.com" value="nome@exemplo.com" disabled>
                                        <label for="disabled-email">Seu e-mail</label>
                                    </div>
                                </div>
        
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label mb-0">CPF/CNPJ</label>
                                            <div class="form-control-plaintext" id="saved-cpf">Input value</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label mb-0">Nascimento</label>
                                            <div class="form-control-plaintext" id="saved-birth-date">Input value</div>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="col-md-12 mb-3">
                                    <label class="form-label mb-0">Telefone</label>
                                    <div class="form-control-plaintext" id="saved-phone">Input value</div>
                                </div>
        
                                <div class="col-md-12 mb-3">
                                    <label class="form-label mb-0">Endereço</label>
                                    <div class="form-control-plaintext" id="saved-street">Input value</div>
                                </div>
        
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label mb-0">CEP</label>
                                            <div class="form-control-plaintext" id="saved-zipcode">Input value</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label mb-0">Cidade</label>
                                            <div class="form-control-plaintext" id="saved-city">Input value</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label mb-0">UF</label>
                                            <div class="form-control-plaintext" id="saved-state">Input value</div>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="d-flex align-items-center justify-content-between">
                                    <button type="button" id="btn-edit-info" class="btn btn-dark btn-pill">Alterar informações</button>
                                    <a href="#" id="link-not-me" class="text-muted">Não sou eu</a>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-6" id="payment-section">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="card bg-dark-lt">
                                    <div class="card-body">
                                        <h3 class="card-title">Cupom</h3>
        
                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="coupon" class="form-control" name="coupon" id="field-coupon" placeholder="Código do Cupom">
                                                <label for="coupon">Código do Cupom</label>
                                            </div>
                                        </div>
        
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-md-12">
                                <div class="card bg-dark-lt">
                                    <div class="card-body">
                                        <h3 class="card-title">Forma de pagamento</h3>

                                        <div>
                                            <label class="form-check">
                                                <input class="form-check-input" type="radio" name="payment" id="payment-credit-card" type="radio" value="100">
                                                <span class="form-check-label">Cartão de Crédito</span>
                                            </label>
                                            <label class="form-check">
                                                <input class="form-check-input" type="radio" name="payment" id="payment-bank-slip" type="radio" value="101">
                                                <span class="form-check-label">Boleto Bancário</span>
                                            </label>
                                            <label class="form-check">
                                                <input class="form-check-input" type="radio" name="payment" id="payment-pix" type="radio" value="102">
                                                <span class="form-check-label">PIX</span>
                                            </label>
                                        </div>

                                        <button type="button" id="btn-step2-continue" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout mt-3 d-none">
                                            Avançar
                                            <!-- Download SVG icon from http://tabler.io/icons/icon/arrow-narrow-right -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 icon-tabler icons-tabler-outline ms-1 icon-tabler-arrow-narrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M15 16l4 -4" /><path d="M15 8l4 4" /></svg>
                                        </button>
                                        <div class="progress progress-subscription mt-3 d-none" role="progressbar" style="height: 36px; border-radius: 18px;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated progress-active text-dark fs-4 fw-bold w-100">
                                                Processando requisição...
                                            </div>
                                        </div>

                                        <div id="card-form" class="d-none mt-3">
                                            <!-- Div onde o card de preview será renderizado -->
                                            <div class="card-wrapper"></div>

                                            <!-- Formulário de dados do cartão -->
                                            <form id="payment-form" class="row">

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="name" id="card-name" placeholder="Nome do Titular">
                                                        <label for="field-ddd">Nome impresso no cartão</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="number" id="card-number" placeholder="Número do Cartão">
                                                        <label for="field-ddd">Número do cartão</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control" name="expiry" id="card-expiry" placeholder="MM/AA">
                                                                <label for="field-ddd">Validade</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control" name="cvc" id="card-cvc" placeholder="000">
                                                                <label for="field-ddd">CVV</label>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="installments" id="card-installments" aria-label="Floating label select">
                                                            <option selected="">Selecione uma parcela</option>
                                                            <option value="1">A vista 1x - R$256,00</option>
                                                            <option value="2">2x - R$123,00</option>
                                                            <option value="3">3x - R$85,33</option>
                                                        </select>
                                                        <label for="card-installments">Número de parcelas</label>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>

                                        <div id="confirm-payment" class="d-none text-center">
                                            <button type="button" id="confirm-payment-checkout" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout mb-3">Confirmar Pagamento</button>
                                            <div class="progress progress-subscription mb-3 d-none" role="progressbar" style="height: 36px; border-radius: 18px;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated progress-active text-dark fs-4 fw-bold w-100">
                                                    Processando requisição...
                                                </div>
                                            </div>
                                            <a href="#" id="back" class="text-muted">Voltar</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 d-none" id="payment-successful">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="card bg-dark-lt">
                                    <div class="card-body">

                                        <div class="d-flex">
                                            <!-- Download SVG icon from http://tabler.io/icons/icon/circle-check -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-3 icon-tabler icons-tabler-outline icon-tabler-circle-check" style="width: 4rem; height: 3rem;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>

                                            <h3 class="fs-1 ms-3">Pedido realizado com sucesso!</h3>
                                        </div>

                                        <h3 class="card-title">Número do pedido: <span class="badge badge-outline badge-pill badge-lg text-green ms-2">#00001</span></h3>
        
                                        <p>Acesse seu e-mail para ver os detalhes do pedido</p>

                                        <h3 class="card-title">Forma de pagamento: <span class="fw-normal">Cartão de crédito</span></h3>
        
                                    </div>
                                </div>
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
                                        <tr data-product-id="<?= $item['product_id']; ?>" id="checkout-item-<?= $item['id']; ?>">
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
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fs-3 fw-bold">TOTAL</td>
                                    <td id="total_valor" class="fs-3 w-10 fw-bold text-end">
                                        R$ <?= number_format($total, 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- <button type="button" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout"> Confirmar </button> -->
                        <!-- Botão para avançar para a etapa 2 -->
                        <button type="button" id="btn-step1-continue" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout mt-3">Continuar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Inclusão do Card.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.5.0/card.min.js"></script>
<script>
    $(document).ready(function(){
        // Inicializa o Card.js
        var card = new Card({
            form: '#payment-form', // Seletor do formulário
            container: '.card-wrapper', // Onde o cartão será renderizado
            width: 350, // Largura do cartão
            formatting: true, // Formata automaticamente o número do cartão
            placeholders: {
                number: '•••• •••• •••• ••••',
                name: 'Seu Nome',
                expiry: '••/••',
                cvc: '•••'
            },
            messages: {
                validDate: 'válido até'
            },
            debug: false
        });
    });
</script>

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
});
</script>

<script>
$(document).ready(function() {

    // Exibe a etapa 1 e oculta as demais ao carregar
    $('#step-1').removeClass('d-none');
    $('#step-2').addClass('d-none');

    // Ao clicar no botão "Continuar" da Etapa 1
    $('#btn-step1-continue').on('click', function() {
        // Coleta os dados do formulário da Etapa 1
        var dados = {
            email: $('#field-eee').val(),
            name: $('#field-name').val(),
            cpf: $('#field-cpf').val(),
            birthDate: $('#field-birth-date').val(),
            ddd: $('#field-ddd').val(),
            phone: $('#field-phone').val(),
            zipcode: $('#field-zipcode').val(),
            street: $('#field-street').val(),
            streetNumber: $('#field-street-number').val(),
            complement: $('#field-complement').val(),
            district: $('#field-district').val(),
            city: $('#field-city').val(),
            state: $('#field-state').val(),
            private: $('#private').val(),
            newsletter: $('#newsletter').val(),
            terms: $('#terms').val(),
        };

        // Validação simples: verifica se os campos obrigatórios estão preenchidos
        if (!dados.email || !dados.name) {
            alert('Por favor, preencha os campos obrigatórios.');
            return;
        }

        // Envia os dados via AJAX para salvar (pode ser em cookie ou em banco de dados)
        $.ajax({
            url: '<?= INCLUDE_PATH; ?>back-end/carrinho/salvar_dados.php',
            type: 'POST',
            data: dados,
            success: function(response) {
                try {
                    var res = JSON.parse(response);

                    if (res.status === 'sucesso') {
                        var address = `${dados.street}, ${dados.streetNumber} - ${dados.district}`;

                        var birthDateRaw = dados.birthDate;
                        var birthDateFormatted = birthDateRaw.split('-').reverse().join('/'); // Converte "aaaa-mm-dd" para "dd/mm/aaaa"

                        // Preenche os campos da Etapa 2 com os dados salvos
                        $('#saved-email').val(dados.email);
                        $('#saved-name').text(dados.name);
                        $('#saved-cpf').text(dados.cpf);
                        $('#saved-birth-date').text(birthDateFormatted);
                        $('#saved-phone').text(dados.phone);
                        $('#saved-street').text(address);
                        $('#saved-zipcode').text(dados.zipcode);
                        $('#saved-city').text(dados.city);
                        $('#saved-state').text(dados.state);

                        // Oculta a Etapa 1 e exibe a Etapa 2
                        $('#step-1').addClass('d-none');
                        $('#step-2').removeClass('d-none');

                        $('#step-item-1').removeClass('active');
                        $('#step-item-2').addClass('active');

                        $('#btn-step1-continue').addClass('d-none');
                    } else {
                        alert("Erro ao salvar dados: " + res.mensagem);
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

    // Ao clicar em "Alterar informações" na Etapa 2, volta para a Etapa 1 com os dados preenchidos
    $('#btn-edit-info').on('click', function() {
        // Exibe a Etapa 1
        $('#step-1').removeClass('d-none');
        // Oculta a Etapa 2
        $('#step-2').addClass('d-none');

        $('#btn-step1-continue').removeClass('d-none');

        // // (Opcional) Preenche os campos da Etapa 1 com os dados salvos exibidos na Etapa 2
        // $('#field-email').val($('#saved-email').text());
        // $('#field-name').val($('#saved-name').text());

        // // Preenche os campos da Etapa 2 com os dados salvos
        // $('#field-email').val($('#saved-email').text());
        // $('#field-name').val($('#saved-name').text());
        // $('#field-cpf').val($('#saved-cpf').text());
        // $('#field-birth-date').val($('#saved-birth-date').text());
        // $('#field-ddd').val($('#saved-ddd').text());
        // $('#field-phone').val($('#saved-phone').text());
        // $('#field-zipcode').val($('#saved-zipcode').text());
        // $('#field-street').val($('#saved-street').text());
        // $('#field-district').val($('#saved-district').text());
        // $('#field-city').val($('#saved-city').text());
        // $('#field-state').val($('#saved-state').text());
    });

    // Ao clicar em "Não sou eu", limpa os campos e volta para a Etapa 1 vazia
    $('#link-not-me').on('click', function(e) {
        e.preventDefault();
        // Limpa os campos da Etapa 1
        $('#field-eee').val('');
        $('#field-name').val('');
        $('#field-cpf').val('');
        $('#field-birth-date').val('');
        $('#field-ddd').val('');
        $('#field-phone').val('');
        $('#field-zipcode').val('');
        $('#field-street').val('');
        $('#field-street-number').val('');
        $('#field-complement').val('');
        $('#field-district').val('');
        $('#field-city').val('');
        $('#field-state').val('');
        $('#private').prop('checked', false);
        $('#newsletter').prop('checked', false);
        $('#terms').prop('checked', false);

        // Oculta a Etapa 2 e exibe a Etapa 1
        $('#step-2').addClass('d-none');
        $('#step-1').removeClass('d-none');

        $('#btn-step1-continue').removeClass('d-none');
    });

    // Quando qualquer input radio do grupo "payment" for selecionado,
    // remove a classe "d-none" do botão de continuar
    $('input[name="payment"]').on('change', function() {
        $('#btn-step2-continue').removeClass('d-none');
        // Se o botão estivesse desabilitado via atributo, desabilite-o assim:
        // $('#btn-step2-continue').prop('disabled', false);
    });








    // Ao clicar no botão "Continuar" da Etapa 2
    $("#btn-step2-continue").on("click", function() {
        // Verifica se o input radio selecionado tem value "100" (Cartão de Crédito)
        if ($('input[name="payment"]:checked').val() == "100") {
            // Oculta o botão "Continuar"
            $(this).addClass("d-none");

            // Oculta os outros inputs radio (exceto o com value "100")
            $('input[name="payment"]').not('[value="100"]').closest('.form-check').hide();

            // Exibe o card-form e a área de confirmação
            $("#card-form").removeClass("d-none");
            $("#confirm-payment").removeClass("d-none");
        } else {
            //Botão carregando
            $(".progress-subscription").addClass('d-flex').removeClass('d-none');
            $("#btn-step2-continue").addClass('d-none').removeClass('d-block');

            // Para boleto ou PIX (por exemplo, "101" ou "102"), enviamos os dados diretamente

            // Exibe o botão de continuar (caso esteja oculto) e oculta o card-form se estiver visível
            $("#card-form").addClass("d-none");
            $("#confirm-payment").addClass("d-none");

            // Salva o método de pagamento (ex: "101" ou "102")
            var typePayment = $('input[name="payment"]:checked').val();
            localStorage.setItem("method", typePayment);
            var method = localStorage.getItem("method");

            // Cria o array com os itens do carrinho
            var cartItems = [];
            $('#checkout-items tr').each(function(){
                var productId = $(this).data('product-id'); // Certifique-se que cada TR tem o atributo data-product-id
                var quantity = $(this).find('td').eq(1).text().trim();
                if(productId && quantity) {
                    cartItems.push({
                        id: productId,
                        quantity: quantity
                    });
                }
            });

            console.log("Carrinho:", cartItems);

            // Cria o objeto com os dados do usuário.
            // Para boleto ou PIX, os dados do cartão ficam vazios.
            var paramsData = {
                email: $('#field-email').val(),
                eee: $('#field-eee').val(),
                cpfCnpj: $('#field-cpf').val(),
                name: $('#field-name').val(),
                birth_date: $('#field-birth-date').val(),
                ddd: $('#field-ddd').val(),
                phone: $('#field-phone').val(),
                postalCode: $('#field-zipcode').val(),
                street: $('#field-street').val(),
                addressNumber: $('#field-street-number').val(),
                complement: $('#field-complement').val(),
                district: $('#field-district').val(),
                city: $('#field-city').val(),
                state: $('#field-state').val(),

                card_name: "",         // Não se aplica
                card_number: "",       // Não se aplica
                card_expiry: "",       // Não se aplica
                card_ccv: "",          // Não se aplica
                card_installments: ""  // Não se aplica
            };

            // Cria o objeto de dados para a requisição AJAX
            var ajaxData = {
                method: method,
                params: btoa(JSON.stringify(paramsData)),
                cart: cartItems
            };

            console.log("Dados a enviar:", ajaxData);

            // Envia a requisição AJAX para criar a compra
            $.ajax({
                url: '<?php echo INCLUDE_PATH; ?>back-end/subscription.php',
                method: 'POST',
                data: ajaxData,
                dataType: 'JSON',
                success: function(response) {
                    window.respostaGlobal = response.id; // Guarda a resposta globalmente, se necessário
                },
                error: function(xhr, status, error) {
                    console.error("Erro no envio:", error);
                }
            })
            .done(function(response) {
                if (response.status == 200) {
                    $("#payment-section").addClass('d-none');
                    $("#payment-successful").removeClass('d-none');

                    // Remover botão carregando e exibir os demais controles (ajuste conforme sua lógica)
                    $(".progress-subscription").addClass('d-none').removeClass('d-flex');
                    // Aqui você pode executar outras chamadas AJAX ou redirecionar

                } else if (response.status == 400) {
                    $("#div-errors-price").html(response.message).slideDown('fast').effect("shake");
                    $('html, body').animate({scrollTop : 0});
                    $(".progress-subscription").addClass('d-none').removeClass('d-flex');
                }
            });
        }
    });

    // Ao clicar no botão "Voltar"
    $("#back").on("click", function(e) {
        e.preventDefault();

        // Oculta o card-form e a área de confirmação
        $("#card-form").addClass("d-none");
        $("#confirm-payment").addClass("d-none");

        // Remove a seleção do input radio com value "100"
        $('input[name="payment"][value="100"]').prop("checked", false);

        // Exibe novamente todos os inputs radio (todos os labels com a classe .form-check)
        $('input[name="payment"]').closest('.form-check').show();
    });






    // Ao clicar no botão "Confirmar Pagamento"
    $("#confirm-payment-checkout").on("click", function() {
        //Botão carregando
        $(".progress-subscription").addClass('d-flex').removeClass('d-none');
        $(this).addClass('d-none').removeClass('d-block');

        var typePayment = $('input[name="payment"]:checked').val();
        localStorage.setItem("method", typePayment);
        method = localStorage.getItem("method");

        // Exemplo de array com os itens do carrinho
        var cartItems = [];
        $('#checkout-items tr').each(function(){
            var productId = $(this).data('product-id'); // Supondo que cada linha tenha um atributo data-item-id
            var quantity = $(this).find('td').eq(1).text().trim();
            if(productId && quantity) {
                cartItems.push({
                    id: productId,
                    quantity: quantity
                });
            }
        });

        console.log("Carrinho:");
        console.log(cartItems);

        // Criando o objeto com os dados
        var paramsData = {
            email: $('#field-email').val(),
            eee: $('#field-eee').val(),
            cpfCnpj: $('#field-cpf').val(),
            name: $('#field-name').val(),
            birth_date: $('#field-birth-date').val(),
            ddd: $('#field-ddd').val(),
            phone: $('#field-phone').val(),
            postalCode: $('#field-zipcode').val(),
            street: $('#field-street').val(),
            addressNumber: $('#field-street-number').val(),
            complement: $('#field-complement').val(),
            district: $('#field-district').val(),
            city: $('#field-city').val(),
            state: $('#field-state').val(),

            card_name: $("#card-name").val(),
            card_number: $("#card-number").val(),
            card_expiry: $("#card-expiry").val(),
            card_ccv: $("#card-cvc").val(),
            card_installments: $("#card-installments").val(),

            private: $('#private').val(),
            newsletter: $('#newsletter').val(),
            terms: $('#terms').val(),
        };

        // Criação do objeto de dados para a requisição AJAX
        var ajaxData = {
            method: method,
            params: btoa(JSON.stringify(paramsData)),
            cart: cartItems
        };

        // Requisição AJAX para o arquivo de criação do cliente
        $.ajax({
            url: '<?php echo INCLUDE_PATH; ?>back-end/subscription.php',
            method: 'POST',
            data: ajaxData,
            dataType: 'JSON',
            success: function(response) {
                window.respostaGlobal = response.id; // Atribui a resposta à propriedade global do objeto window
            },
            error: function(xhr, status, error) {
                console.error("Erro no envio:", error);
            }
        })
        .done(function(response) {
            if (response.status == 200) {
                $("#payment-section").addClass('d-none');
                $("#payment-successful").removeClass('d-none');










                //Remove botão carregando
                $(".progress-subscription").addClass('d-none').removeClass('d-flex');
                $(this).addClass('d-block').removeClass('d-none');

                var encodedCode = btoa(response.code);
                var customerId = btoa(response.id);

                $.ajax({
                    url: '<?php echo INCLUDE_PATH; ?>back-end/sql.php',
                    method: 'POST',
                    data: {encodedCode: encodedCode},
                    dataType: 'JSON'
                })
                .done(function(data) {
                    printPaymentData(data);
                })

                $.ajax({
                    url: '<?php echo INCLUDE_PATH_ADMIN; ?>back-end/magic-link.php',
                    method: 'POST',
                    data: {customerId: customerId},
                    dataType: 'JSON'
                })
                .done(function(data) {
                    console.log(data.msg);
                })
            } else if (response.status == 400) {
                $("#div-errors-price").html(response.message).slideDown('fast').effect("shake");
                $('html, body').animate({scrollTop : 0});

                //Remove botão carregando
                $(".progress-subscription").addClass('d-none').removeClass('d-flex');
                $(this).addClass('d-block').removeClass('d-none');
            }
        })
    });
});
</script>