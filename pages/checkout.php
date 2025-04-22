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

$pedido_id = $_GET['pedido'] ?? null;

// Busca o pedido pelo ID de transação (pedido_id)
$stmt = $conn->prepare("SELECT * FROM tb_pedidos WHERE pedido_id = ?");
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);


if ($pedido) {

    // Supondo que a data de vencimento esteja armazenada como 'data_vencimento' no formato 'YYYY-MM-DD'
    $dueDate = strtotime($pedido['data_vencimento']);
    $currentDate = strtotime(date('Y-m-d'));

    if ($currentDate > $dueDate) {
        $_SESSION['error_msg'] = "Seu pedido expirou.";
        header('Location: ' . INCLUDE_PATH . 'carrinho');
        exit;
    }

    // Busca o cliente
    $stmt = $conn->prepare("SELECT * FROM tb_clientes WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Opção 1: Salvar em sessão
    $_SESSION['checkout_data'] = [
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
        'country'      => $usuario['pais'],
        'newsletter'   => $usuario['newsletter'],
        'terms'        => 1
    ];

    // Verifica o status do pedido (supondo que "pending" esteja em minúsculas ou padronizado)
    if (strtolower($pedido['status']) !== 'pending') {
        $_SESSION['error_msg'] = "O pedido já foi pago.";
        header('Location: ' . INCLUDE_PATH . 'carrinho');
        exit;
    }

    $stmt = $conn->prepare("SELECT pi.*, p.id AS product_id, p.nome AS produto_nome, p.preco AS produto_preco 
                            FROM tb_pedido_itens pi 
                            JOIN tb_produtos p ON pi.produto_id = p.id 
                            WHERE pi.pedido_id = ?");
    $stmt->execute([$pedido['id']]);

    $checkoutItems = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    // Calcula o subtotal dos itens
    $subtotal = 0;
    foreach ($checkoutItems as $item) {
        $subtotal += $item['produto_preco'] * $item['quantidade'];
    }

    // Valores do frete e desconto (podem ser dinâmicos)
    $frete = $pedido['frete'];
    $desconto = $pedido['desconto'];
    $total = $subtotal + $frete - $desconto;

} else {

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

    // Se não houver itens no carrinho, seta a mensagem de erro e redireciona
    if (empty($checkoutItems)) {
        header('Location: ' . INCLUDE_PATH . 'carrinho');
        exit;
    }

    // Calcula o subtotal dos itens
    $subtotal = 0;
    foreach ($checkoutItems as $item) {
        $subtotal += $item['produto_preco'] * $item['quantidade'];
    }

    // Valores do frete e desconto (podem ser dinâmicos)
    $frete = 0.00;
    $desconto = 0.00;
    $total = $subtotal + $frete - $desconto;

}
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

<!-- Modal Validar -->
<div class="modal modal-blur fade" id="modal-validar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
                <!-- Ícone de sucesso -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-green icon-lg"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                <h3 id="msg-modal-validar-titulo"></h3>
                <div id="msg-modal-validar" class="text-secondary"></div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-3 btn-success w-100" data-bs-dismiss="modal"> Ok </a>
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
                <ul class="steps steps-counter my-4">
                    <li class="step-item <?= (!$pedido) ? "active" : ""; ?>" id="step-item-1"> Informações Gerais </li>
                    <li class="step-item" id="step-item-2"> Pagamento </li>
                    <li class="step-item <?= ($pedido) ? "active" : ""; ?>" id="step-item-3"> Confirmação </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <div id="alert-container"></div>

            <!-- CHECKOUT ETAPA 1 - Informações Gerais -->

            <div class="col-md-8 <?= $pedido ? "d-none" : ""; ?>" id="step-1">
                <div class="card bg-dark-lt">
                    <div class="card-body row">
                        <div class="col-md-6">
                            <h3 class="card-title">Informações pessoais</h3>

							<div class="row">

								<!--div class="col-md-12 mb-3 d-none">
									<div class="form-floating">
										<input type="email" class="form-control" name="email" id="field-email" placeholder="nome@exemplo.com">
										<label for="field-email">Seu e-mail</label>
									</div>
								</div-->

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="email" class="form-control" name="email" id="field-email" placeholder="nome@exemplo.com" value="<?= @$_SESSION['checkout_data']['email'] ?? null; ?>" required>
										<label for="field-email">Seu e-mail</label>
									</div>
                                    <div id="input-email-error" class="text-danger d-none"><small>O campo email é obrigatório</small></div>
                                    <div id="input-email-format-error" class="text-danger d-none"><small>Email inválido</small></div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="text" class="form-control" name="name" id="field-name" placeholder="Primeiro nome" value="<?= @$_SESSION['checkout_data']['name'] ?? null; ?>" required>
										<label for="field-name">Nome completo</label>
									</div>
                                    <div id="input-name-error" class="text-danger d-none"><small>O campo nome é obrigatório</small></div>
								</div>

								<div class="col-md-12 mb-3" id="div-cpf-field">
									<div class="form-floating">
										<input type="text" class="form-control" name="cpfCnpj" id="field-cpf" placeholder="CPF" value="<?= @$_SESSION['checkout_data']['cpf'] ?? null; ?>" required>
										<label for="field-cpf">CPF/CNPJ</label>
									</div>
                                    <div id="input-cpf-error" class="text-danger d-none"><small>O campo CPF é obrigatório</small></div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="date" class="form-control" name="birth-date" id="field-birth-date" placeholder="(99) 99999-9999" maxlength="15" value="<?= @$_SESSION['checkout_data']['birthDate'] ?? null; ?>" required>
										<label for="birth-date">Data de Nascimento</label>
									</div>
                                    <div id="input-birth-date-error" class="text-danger d-none"><small>O campo data de nascimento é obrigatório</small></div>
								</div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" name="phone" id="field-phone" placeholder="(99) 99999-9999" maxlength="15" value="<?= @$_SESSION['checkout_data']['phone'] ?? null; ?>" required>
                                        <label for="phone">Telefone</label>
                                    </div>
                                    <div id="input-phone-error" class="text-danger d-none"><small>O campo telefone é obrigatório</small></div>
                                </div>

                            </div>

                        </div>
                        <div class="col-md-6">
                            <h3 class="card-title">Informações de cobrança</h3>

							<div class="row">

                                <div class="col-md-12 mb-3" id="div-cep-field">
                                    <div class="form-floating">
                                        <input onblur="getCepData()" type="text" class="form-control" name="postalCode" id="field-zipcode" placeholder="CEP endereço" value="<?= @$_SESSION['checkout_data']['zipcode'] ?? null; ?>" required>
                                        <label for="field-zipcode">CEP</label>
                                    </div>
                                    <div id="input-zipcode-error" class="text-danger d-none"><small>O campo CEP é obrigatório</small></div>
                                </div>

                                <div class="col-md-12 mb-3 country-brasil">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="address" id="field-street" placeholder="Endereço" value="<?= @$_SESSION['checkout_data']['street'] ?? null; ?>" required>
                                        <label for="field-street">Endereço</label>
                                    </div>
                                    <div id="input-street-error" class="text-danger d-none"><small>O campo endereço é obrigatório</small></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-4 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-center" name="addressNumber" id="field-street-number" placeholder="Número endereço" value="<?= @$_SESSION['checkout_data']['streetNumber'] ?? null; ?>" required>
                                                <label for="field-street-number">Número</label>
                                            </div>
                                            <div id="input-street-number-error" class="text-danger d-none"><small>O campo número é obrigatório</small></div>
                                        </div>

                                        <div class="col-md-8 mb-3 country-brasil">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="complement" id="field-complement" placeholder="Complemento endereço" value="<?= @$_SESSION['checkout_data']['complement'] ?? null; ?>">
                                                <label for="field-complement">Complemento</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-12 mb-3 country-brasil" id="div-district-field">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="province" id="field-district" placeholder="Bairro endereço" value="<?= @$_SESSION['checkout_data']['district'] ?? null; ?>" required>
                                                <label for="field-district">Bairro</label>
                                            </div>
                                            <div id="input-district-error" class="text-danger d-none"><small>O campo bairro é obrigatório</small></div>
                                        </div>

                                        <div class="col-md-8 mb-3 country-brasil" id="div-city-field">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="city" id="field-city" placeholder="Cidade endereço" value="<?= @$_SESSION['checkout_data']['city'] ?? null; ?>" required>
                                                <label for="field-city">Cidade</label>
                                            </div>
                                            <div id="input-city-error" class="text-danger d-none"><small>O campo cidade é obrigatório</small></div>
                                        </div>

                                        <div class="col-md-4 mb-3 country-brasil" id="div-state-field">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="state" id="field-state" placeholder="UF" value="<?= @$_SESSION['checkout_data']['state'] ?? null; ?>" required>
                                                <label for="field-state">UF</label>
                                            </div>
                                            <div id="input-state-error" class="text-danger d-none"><small>O campo estado é obrigatório</small></div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="newsletter" name="newsletter" <?= (isset($_SESSION['checkout_data']['newsletter']) && $_SESSION['checkout_data']['newsletter'] == 1) ? 'checked' : null; ?>>
                                    <span class="form-check-label">Quero receber as novidades das Mulheres Empreendedoras da Amazônia.</span>
                                </label>
                                <hr class="my-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" <?= (isset($_SESSION['checkout_data']['terms']) && $_SESSION['checkout_data']['terms'] == 1) ? 'checked' : null; ?> required>
                                    <span class="form-check-label">Declaro que li e aceito, as <a href="#">condições de compra</a>, <a href="#">políticas de cancelamento</a> e os <a href="#">Termos de Uso</a> da plataforma, estando de acordo com todos os termos das tarifas e serviços oferecidos pelas Mulheres Empreendedoras da Amazônia.</span>
                                    <div id="input-terms-error" class="text-danger d-none"><small>Por favor, aceite os termos de uso para prosseguir</small></div>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- CHECKOUT ETAPA 2 - Pagamento -->

            <div class="col-md-8 <?= !$pedido ? "d-none" : ""; ?>" id="step-2">
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
                                        <input type="disabled-email" class="form-control" name="disabled-email" id="saved-email" value="-" disabled>
                                        <label for="disabled-email">Seu e-mail</label>
                                    </div>
                                </div>
        
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label mb-0">CPF/CNPJ</label>
                                            <div class="form-control-plaintext" id="saved-cpf">-</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label mb-0">Nascimento</label>
                                            <div class="form-control-plaintext" id="saved-birth-date">-</div>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="col-md-12 mb-3">
                                    <label class="form-label mb-0">Telefone</label>
                                    <div class="form-control-plaintext" id="saved-phone">-</div>
                                </div>
        
                                <div class="col-md-12 mb-3">
                                    <label class="form-label mb-0">Endereço</label>
                                    <div class="form-control-plaintext" id="saved-street">-</div>
                                </div>
        
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label mb-0">CEP</label>
                                            <div class="form-control-plaintext" id="saved-zipcode">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-0">Cidade</label>
                                            <div class="form-control-plaintext" id="saved-city">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-0">UF</label>
                                            <div class="form-control-plaintext" id="saved-state">-</div>
                                        </div>
                                    </div>
                                </div>
        
                                <?php if (!$pedido): ?>
                                <div class="d-flex align-items-center justify-content-between mt-3" id="usuario-info">
                                    <button type="button" id="btn-edit-info" class="btn btn-dark btn-pill">Alterar informações</button>
                                    <a href="#" id="link-not-me" class="text-muted">Não sou eu</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-6 <?= $pedido ? "d-none" : ""; ?>" id="payment-section">
                        <div class="row g-4">
                            <div class="col-md-12 d-none">
                                <div class="card bg-dark-lt">
                                    <div class="card-body">
                                        <h3 class="card-title">Cupom</h3>
        
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input type="coupon" class="form-control" name="coupon" id="field-coupon" placeholder="Código do Cupom">
                                                <label for="coupon">Código do Cupom</label>
                                            </div>
                                        </div>
        
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" id="shipping-step">
                                <div class="card bg-dark-lt">
                                    <div class="card-body">
                                        <h3 class="card-title">Método de Envio</h3>
                                        <!-- container onde os radios serão inseridos -->
                                        <div id="shipping-options" class="d-flex flex-column gap-2">
                                            <!-- radios serão gerados aqui -->
                                            <div class="text-muted">Carregando opções...</div>
                                        </div>
                                        <!-- Botão para avançar para a etapa de pagamento -->
                                        <button type="button" id="btn-step-shipping-continue" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout mt-3 d-none">Selecionar</button>
                                        <div id="change-shipping-button" class="d-none text-center">
                                            <a href="#" id="change-shipping-option" class="text-muted">Alterar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-md-12 d-none" id="payment-section-form">
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
                                            <span>Avançar</span>
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
                                                        <label for="card-name">Nome impresso no cartão</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="number" id="card-number" placeholder="Número do Cartão">
                                                        <label for="card-number">Número do cartão</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control" name="expiry" id="card-expiry" placeholder="MM/AA">
                                                                <label for="card-expiry">Validade</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control" name="cvc" id="card-cvc" placeholder="000">
                                                                <label for="card-cvc">CVV</label>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="installments" id="card-installments" aria-label="Floating label select">
                                                            <option selected="">Selecione uma parcela</option>
                                                            <option value="1">A vista 1x - R$ <?= number_format($total, 2, ',', '.'); ?></option>
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

                    <div class="col-md-6 <?= !$pedido ? "d-none" : ""; ?>" id="payment-successful">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="card bg-dark-lt">
                                    <div class="card-body">

                                        <div class="d-flex">
                                            <!-- Download SVG icon from http://tabler.io/icons/icon/circle-check -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-3 icon-tabler icons-tabler-outline icon-tabler-circle-check" style="width: 4rem; height: 3rem;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>

                                            <h3 class="fs-1 ms-3">Pedido realizado com sucesso!</h3>
                                        </div>

                                        <h3 class="card-title">Número do pedido: <span class="badge badge-outline badge-pill badge-lg text-green ms-2" id="pedido-id"><?= $pedido ? "#{$pedido['pedido_id']}" : "#00001" ?></span></h3>
        
                                        <p>Acesse seu e-mail para ver os detalhes do pedido</p>

                                        <h3 class="card-title mb-0">Forma de pagamento: <span id="paymentMethodText" class="fw-normal"><?= $pedido && $pedido['forma_pagamento'] == 'PIX' ? "Pix" : ($pedido && $pedido['forma_pagamento'] == 'BOLETO' ? "Boleto Bancário" : "Cartão de crédito"); ?></span></h3>

                                        <div id="boleto-section" class="text-center row <?= (isset($pedido) && is_array($pedido) && $pedido['forma_pagamento'] == 'BOLETO') ? "" : "d-none"; ?>">
                                            <div class="col-md-12">
                                                <hr class="my-4">

                                                <p>Agora é só pagar o boleto para finalizar sua compra.</p>

                                                <h3 class="card-title">Clique para copiar o código de barras:</h3>

                                                <textarea class="form-control text-center mb-3" id="boleto-text" rows="2" style="resize: none; background: transparent; border: 1px solid;" readonly>48190.00003 00005.150412 84675.680148 5 99960000381866</textarea>

                                                <a href="<?= $pedido && $pedido['forma_pagamento'] == 'BOLETO' ? $pedido['link_boleto'] : "#"; ?>" target="_blank" class="btn btn-6 btn-dark btn-pill w-100" id="boleto-link">Visualizar boleto</a>
                                            </div>
                                        </div>

                                        <div id="pix-section" class="text-center row <?= (isset($pedido) && is_array($pedido) && $pedido['forma_pagamento'] == 'PIX') ? "" : "d-none"; ?>">
                                            <div class="col-md-12">
                                                <hr class="my-4">

                                                <p class="mb-0">Agora é só pagar com o Pix para finalizar sua compra.</p>

                                                <svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" id="svg859" version="1.1" viewBox="0 0 237.76514 84.263428" height="13.263428mm" class="my-4"><defs id="defs853"><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1420"><path d="M 0,1080 H 1920 V 0 H 0 Z" id="path1418"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1428"><path d="M 2.31224,1078.65 H 1921.71 V 0.354599 H 2.31224 Z" id="path1426"/></clipPath><radialGradient fx="0" fy="0" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(2279.4458,0,0,1280.5419,962.0127,539.50366)" spreadMethod="pad" id="radialGradient1438"><stop style="stop-opacity:1;stop-color:#ffffff" offset="0" id="stop1434"/><stop style="stop-opacity:1;stop-color:#000000" offset="1" id="stop1436"/></radialGradient><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1460"><path d="M 1.52588e-5,1083.19 H 1921.71 V -3.05673 H 1.52588e-5 Z" id="path1458"/></clipPath><radialGradient fx="0" fy="0" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(2282.1917,0,0,1289.9785,960.85657,540.06549)" spreadMethod="pad" id="radialGradient1470"><stop style="stop-opacity:1;stop-color:#ffffff" offset="0" id="stop1466"/><stop style="stop-opacity:1;stop-color:#000000" offset="1" id="stop1468"/></radialGradient><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1492"><path d="M 11.7831,1078.21 H 1910.67 V 42.0222 H 11.7831 Z" id="path1490"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1496"><path d="M 11.7831,1017.59 H 1879.91 V 42.0222 H 11.7831 Z" id="path1494"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1500"><path d="M 121.596,1078.21 H 1910.67 V 117.8 H 121.596 Z" id="path1498"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2268"><path d="M 0,1080 H 1920 V 0 H 0 Z" id="path2266"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2280"><path d="m 1609.83,82.2588 h 26.48 V 63.5831 h -26.48 z" id="path2278"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2296"><path d="m 1600.18,103.19 h 22.86 V 76.7676 h -22.86 z" id="path2294"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2312"><path d="m 1618.19,89.9323 h 22.12 V 63.6456 h -22.12 z" id="path2310"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2328"><path d="m 1604.2,103.117 h 26.46 V 84.441 h -26.46 z" id="path2326"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2474"><path d="M 0,1080 H 1920 V 0 H 0 Z" id="path2472"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2482"><path d="M 1258.575,-1094.026 926.826,-9.01 H 0 v -1085.016 z" id="path2480"/></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath2490"><path d="M -307.943,-1106.382 H 1369.67 V 12.027 H -307.943 Z" id="path2488"/></clipPath></defs><metadata id="metadata856"><rdf:RDF><cc:Work rdf:about=""><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/><dc:title/></cc:Work></rdf:RDF></metadata><g transform="translate(-535.59399,-20.808825)" id="layer1"><path d="m 633.42119,99.489186 v -48.3242 c 0,-8.89177 7.20795,-16.09972 16.09936,-16.09972 l 14.2681,0.0215 c 8.86566,0.0176 16.04363,7.20972 16.04363,16.07573 v 10.28594 c 0,8.89176 -7.20831,16.09972 -16.09972,16.09972 h -20.1616" style="fill:none;stroke:#939598;stroke-width:2.97638607;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" id="path2356"/><path d="m 683.81948,35.058846 h 6.18913 c 3.64913,0 6.60682,2.95804 6.60682,6.60717 v 36.09834" style="fill:none;stroke:#939598;stroke-width:2.97638607;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" id="path2360"/><path d="m 695.28853,29.466256 -2.8067,-2.807053 c -0.69674,-0.696383 -0.69674,-1.825625 0,-2.522008 l 2.80494,-2.805289 c 0.69779,-0.697441 1.82844,-0.697441 2.52553,0 l 2.80494,2.805289 c 0.69673,0.696383 0.69673,1.825625 0,2.522008 l -2.8067,2.807053 c -0.69638,0.69638 -1.82527,0.69638 -2.52201,0" style="fill:#32bcad;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2364"/><path d="m 708.48944,35.026636 h 6.13798 c 3.15771,0 6.18596,1.25448 8.41834,3.48686 l 14.35664,14.35664 c 1.85949,1.85984 4.87468,1.85984 6.73453,0 l 14.30408,-14.30408 c 2.23273,-2.23238 5.26062,-3.48686 8.41833,-3.48686 h 4.9904" style="fill:none;stroke:#939598;stroke-width:2.97638607;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" id="path2368"/><path d="m 708.48944,77.448336 h 6.13798 c 3.15771,0 6.18596,-1.25448 8.41834,-3.48686 l 14.35664,-14.35664 c 1.85949,-1.85984 4.87468,-1.85984 6.73453,0 l 14.30408,14.30408 c 2.23273,2.23238 5.26062,3.48686 8.41833,3.48686 h 4.9904" style="fill:none;stroke:#939598;stroke-width:2.97638607;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" id="path2372"/><path d="m 596.82737,86.620206 c -3.08045,0 -5.97782,-1.19944 -8.15622,-3.37679 l -11.77678,-11.77713 c -0.82691,-0.82903 -2.26801,-0.82656 -3.09456,0 l -11.81982,11.82017 c -2.17841,2.17734 -5.07577,3.37679 -8.15623,3.37679 h -2.32092 l 14.9158,14.915444 c 4.65807,4.65808 12.21069,4.65808 16.86912,0 l 14.95813,-14.958484 z" style="fill:#32bcad;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2376"/><path d="m 553.82362,44.963326 c 3.08046,0 5.97782,1.19944 8.15622,3.37679 l 11.81982,11.82193 c 0.85125,0.85161 2.2412,0.85479 3.09457,-10e-4 l 11.77678,-11.77784 c 2.1784,-2.17735 5.07576,-3.37679 8.15622,-3.37679 h 1.41852 l -14.95778,-14.95813 c -4.65878,-4.658432 -12.2114,-4.658432 -16.86948,0 l -14.91509,14.91509 z" style="fill:#32bcad;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2380"/><path d="m 610.61844,57.378776 -9.03922,-9.03922 c -0.19897,0.0797 -0.41452,0.12946 -0.64206,0.12946 h -4.10986 c -2.12478,0 -4.20476,0.86184 -5.70618,2.36432 l -11.77643,11.77678 c -1.10207,1.10208 -2.55022,1.65347 -3.99697,1.65347 -1.44815,0 -2.89524,-0.55139 -3.99697,-1.65241 l -11.82088,-11.82088 c -1.50142,-1.50283 -3.5814,-2.36431 -5.70618,-2.36431 h -5.05354 c -0.21555,0 -0.41698,-0.0508 -0.60713,-0.12242 l -9.07521,9.07521 c -4.65843,4.65843 -4.65843,12.2107 0,16.86913 l 9.07486,9.07485 c 0.1905,-0.0716 0.39193,-0.12241 0.60748,-0.12241 h 5.05354 c 2.12478,0 4.20476,-0.86148 5.70618,-2.36396 l 11.81982,-11.81982 c 2.13643,-2.13466 5.8607,-2.13537 7.995,0.001 l 11.77643,11.77573 c 1.50142,1.50248 3.5814,2.36431 5.70618,2.36431 h 4.10986 c 0.22754,0 0.44309,0.0497 0.64206,0.12947 l 9.03922,-9.03922 c 4.65808,-4.65843 4.65808,-12.2107 0,-16.86913" style="fill:#32bcad;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2384"/><path d="m 645.6909,95.381446 c -0.6671,0 -1.44356,0.16051 -2.21156,0.33761 v 2.94463 c 0.53199,0.19438 1.13947,0.28787 1.72191,0.28787 1.47673,0 2.17699,-0.49812 2.17699,-1.79881 0,-1.22273 -0.57362,-1.7713 -1.68734,-1.7713 m -2.70968,5.468764 v -5.823654 h 0.40534 l 0.0423,0.25364 c 0.68333,-0.16051 1.62842,-0.37147 2.30364,-0.37147 0.54927,0 1.07209,0.0836 1.51059,0.4385 0.50694,0.41416 0.66711,1.08021 0.66711,1.80552 0,0.76094 -0.25365,1.47778 -0.94545,1.87395 -0.48084,0.27023 -1.13065,0.37994 -1.71309,0.37994 -0.59937,0 -1.17298,-0.0931 -1.77235,-0.26987 v 1.713444 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2388"/><path d="m 651.61782,95.363876 c -1.47708,0 -2.13537,0.46461 -2.13537,1.76424 0,1.2573 0.64982,1.82316 2.13537,1.82316 1.46826,0 2.12654,-0.45614 2.12654,-1.75578 0,-1.2573 -0.64946,-1.83162 -2.12654,-1.83162 m 1.89865,3.5874 c -0.48966,0.35383 -1.14759,0.45543 -1.89865,0.45543 -0.768,0 -1.42664,-0.10971 -1.90747,-0.45543 -0.5401,-0.37959 -0.75989,-1.00471 -0.75989,-1.78894 0,-0.77717 0.21979,-1.40935 0.75989,-1.79846 0.48083,-0.34537 1.13947,-0.45544 1.90747,-0.45544 0.75918,0 1.40899,0.11007 1.89865,0.45544 0.54892,0.38911 0.75953,1.02129 0.75953,1.78894 0,0.78563 -0.21943,1.41887 -0.75953,1.79846" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2392"/><path d="m 660.50757,99.288706 -1.64571,-3.53554 h -0.0342 l -1.61995,3.53554 h -0.44732 l -1.75543,-4.26226 h 0.54857 l 1.46015,3.57822 h 0.0339 l 1.58609,-3.57822 h 0.45579 l 1.62912,3.57822 h 0.0339 l 1.42628,-3.57822 h 0.53129 l -1.75507,4.26226 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2396"/><path d="m 665.8936,95.355586 c -1.36701,0 -1.83126,0.60748 -1.91593,1.4859 h 3.83187 c -0.042,-0.97049 -0.54045,-1.4859 -1.91594,-1.4859 m -0.0166,4.05095 c -0.81915,0 -1.35043,-0.11783 -1.77235,-0.47273 -0.49812,-0.43038 -0.6671,-1.05445 -0.6671,-1.77165 0,-0.68368 0.22824,-1.40934 0.79375,-1.82315 0.47237,-0.32879 1.0548,-0.43039 1.66229,-0.43039 0.54892,0 1.1818,0.0589 1.70462,0.41381 0.6163,0.4131 0.73483,1.13947 0.73483,1.96603 h -4.37197 c 0.0166,0.87736 0.30374,1.65453 1.95756,1.65453 0.78529,0 1.51942,-0.127 2.2031,-0.24518 v 0.44697 c -0.70908,0.12735 -1.49401,0.26176 -2.24473,0.26176" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2400"/><path d="m 669.76178,99.288706 v -4.26226 h 0.40499 l 0.0427,0.25365 c 0.90276,-0.22755 1.32468,-0.37148 2.11808,-0.37148 h 0.0593 v 0.47272 h -0.11854 c -0.66639,0 -1.07138,0.0924 -2.00801,0.33761 v 3.56976 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2404"/><path d="m 675.27876,95.355586 c -1.36701,0 -1.83127,0.60748 -1.91593,1.4859 h 3.83187 c -0.042,-0.97049 -0.54046,-1.4859 -1.91594,-1.4859 m -0.0166,4.05095 c -0.81915,0 -1.35043,-0.11783 -1.77235,-0.47273 -0.49848,-0.43038 -0.66711,-1.05445 -0.66711,-1.77165 0,-0.68368 0.22825,-1.40934 0.79375,-1.82315 0.47237,-0.32879 1.05481,-0.43039 1.66229,-0.43039 0.54892,0 1.18181,0.0589 1.70462,0.41381 0.61631,0.4131 0.73484,1.13947 0.73484,1.96603 h -4.37197 c 0.0166,0.87736 0.30374,1.65453 1.95756,1.65453 0.78493,0 1.51906,-0.127 2.2031,-0.24518 v 0.44697 c -0.70909,0.12735 -1.49402,0.26176 -2.24473,0.26176" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2408"/><path d="m 683.17284,95.651526 c -0.53164,-0.19438 -1.13912,-0.28751 -1.72156,-0.28751 -1.47673,0 -2.1777,0.49882 -2.1777,1.7981 0,1.23155 0.57397,1.77165 1.68769,1.77165 0.6671,0 1.44357,-0.16051 2.21157,-0.32914 z m 0.0931,3.63714 -0.0423,-0.25365 c -0.68369,0.16052 -1.62913,0.37183 -2.30435,0.37183 -0.54786,0 -1.07174,-0.0759 -1.51059,-0.43886 -0.50624,-0.4138 -0.66675,-1.08055 -0.66675,-1.80587 0,-0.75953 0.25329,-1.47743 0.94509,-1.86548 0.48119,-0.27835 1.131,-0.38806 1.72191,-0.38806 0.5909,0 1.16487,0.1016 1.76389,0.27023 v -1.94945 h 0.49812 v 6.05931 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2412"/><path d="m 690.97215,95.381446 c -0.6671,0 -1.44356,0.16051 -2.21156,0.33761 v 2.93652 c 0.54046,0.20249 1.13947,0.29598 1.72191,0.29598 1.47673,0 2.17699,-0.49812 2.17699,-1.79881 0,-1.22273 -0.57362,-1.7713 -1.68734,-1.7713 m 1.27424,3.64525 c -0.48119,0.27023 -1.13101,0.37994 -1.71344,0.37994 -0.63289,0 -1.26577,-0.10971 -1.90712,-0.32067 l -0.0254,0.20285 h -0.33796 v -6.05967 h 0.49812 v 2.03341 c 0.68368,-0.15098 1.60337,-0.35383 2.25319,-0.35383 0.54928,0 1.07209,0.0836 1.5106,0.4385 0.50694,0.41416 0.6671,1.08021 0.6671,1.80552 0,0.76094 -0.25365,1.47778 -0.94509,1.87395" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2416"/><path d="m 693.85227,100.92563 v -0.46355 c 0.24447,0.0247 0.47307,0.0423 0.63323,0.0423 0.61631,0 0.98707,-0.1778 1.33315,-0.878414 l 0.16051,-0.33726 -2.22779,-4.26226 h 0.57397 l 1.90747,3.67947 h 0.0335 l 1.81434,-3.67947 h 0.5655 l -2.39677,4.78578 c -0.43886,0.869254 -0.91158,1.155704 -1.78082,1.155704 -0.19439,0 -0.40499,-0.0166 -0.61631,-0.0423" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2420"/><path d="m 705.5091,96.857996 h -1.65382 v 1.49437 h 1.66194 c 1.13947,0 1.57021,-0.12736 1.57021,-0.75142 0,-0.66746 -0.59055,-0.74295 -1.57833,-0.74295 m -0.30339,-2.42217 h -1.35043 v 1.51871 h 1.35855 c 1.12254,0 1.56951,-0.13441 1.56951,-0.76765 0,-0.67451 -0.56515,-0.75106 -1.57763,-0.75106 m 2.5654,4.44817 c -0.60819,0.38806 -1.34232,0.40464 -2.68393,0.40464 h -2.52342 v -5.78097 h 2.46451 c 1.15605,0 1.86478,0.0166 2.45568,0.37147 0.42228,0.2533 0.59055,0.64135 0.59055,1.14759 0,0.60713 -0.25259,1.01283 -0.91158,1.28305 v 0.0332 c 0.74331,0.16969 1.22414,0.54928 1.22414,1.36772 0,0.55669 -0.20249,0.9197 -0.61595,1.17334" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2424"/><path d="m 713.43591,97.499666 c -0.49847,-0.0427 -1.00436,-0.0674 -1.53599,-0.0674 -0.86925,0 -1.17369,0.17709 -1.17369,0.57326 0,0.37148 0.25364,0.57433 0.92004,0.57433 0.55704,0 1.22379,-0.1263 1.78964,-0.25365 z m 0.25294,1.78894 -0.0339,-0.2533 c -0.72601,0.1778 -1.5695,0.37148 -2.31245,0.37148 -0.45615,0 -0.9451,-0.0593 -1.29152,-0.31256 -0.31997,-0.22755 -0.47237,-0.59902 -0.47237,-1.02941 0,-0.48154 0.21131,-0.92851 0.71719,-1.15605 0.44733,-0.21096 1.0467,-0.22754 1.59562,-0.22754 0.44697,0 1.04598,0.0247 1.54446,0.0589 v -0.0765 c 0,-0.6664 -0.43921,-0.88583 -1.63759,-0.88583 -0.46426,0 -1.02976,0.0247 -1.56987,0.0755 v -0.86082 c 0.59902,-0.0497 1.27459,-0.084 1.83163,-0.084 0.74224,0 1.51094,0.0593 1.98331,0.39652 0.48895,0.34643 0.58244,0.82762 0.58244,1.45979 v 2.52378 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2428"/><path d="m 720.19002,99.288706 v -2.35514 c 0,-0.77576 -0.39617,-1.05446 -1.10561,-1.05446 -0.52281,0 -1.1811,0.13476 -1.73848,0.27023 v 3.13937 h -1.18992 v -4.26226 h 0.97049 l 0.0423,0.27023 c 0.75071,-0.19368 1.58679,-0.38806 2.27894,-0.38806 0.52282,0 1.05481,0.0755 1.4598,0.43886 0.33725,0.30409 0.46425,0.72531 0.46425,1.3335 v 2.60773 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2432"/><path d="m 724.73376,99.406676 c -0.54857,0 -1.14829,-0.0755 -1.58679,-0.44697 -0.52317,-0.42227 -0.67522,-1.08867 -0.67522,-1.80693 0,-0.67451 0.21943,-1.40899 0.86924,-1.82209 0.53199,-0.34643 1.18992,-0.42193 1.87361,-0.42193 0.48965,0 0.97084,0.0339 1.50213,0.0836 v 0.91158 c -0.43075,-0.0413 -0.94545,-0.0755 -1.35855,-0.0755 -1.13136,0 -1.66264,0.35489 -1.66264,1.33385 0,0.92004 0.39652,1.31621 1.32468,1.31621 0.5401,0 1.17369,-0.10125 1.78964,-0.21943 v 0.87736 c -0.6671,0.13582 -1.39277,0.27023 -2.0761,0.27023" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2436"/><path d="m 730.3248,95.802586 c -1.13101,0 -1.62913,0.35489 -1.62913,1.32539 0,0.97084 0.48965,1.38465 1.62913,1.38465 1.12218,0 1.61148,-0.34678 1.61148,-1.31727 0,-0.9705 -0.48048,-1.39277 -1.61148,-1.39277 m 2.04223,3.15701 c -0.52317,0.35383 -1.20686,0.44697 -2.04223,0.44697 -0.85267,0 -1.536,-0.10125 -2.0507,-0.44697 -0.5909,-0.38806 -0.80222,-1.02941 -0.80222,-1.7974 0,-0.76871 0.21132,-1.41852 0.80222,-1.80658 0.5147,-0.34572 1.19803,-0.44697 2.0507,-0.44697 0.84419,0 1.51906,0.10125 2.04223,0.44697 0.5909,0.38806 0.79339,1.03787 0.79339,1.7974 0,0.76871 -0.21096,1.41852 -0.79339,1.80658" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2440"/><path d="m 740.03066,99.406676 c -0.71684,0 -1.4933,-0.11783 -2.07609,-0.59902 -0.6918,-0.57432 -0.90276,-1.46014 -0.90276,-2.41441 0,-0.8516 0.26987,-1.86443 1.17299,-2.45498 0.70026,-0.45543 1.5695,-0.54857 2.44721,-0.54857 0.64206,0 1.29999,0.0423 2.01754,0.10125 v 1.03787 c -0.6163,-0.0508 -1.37548,-0.0931 -1.96638,-0.0931 -1.64606,0 -2.34633,0.62512 -2.34633,1.95756 0,1.35996 0.64947,1.96744 1.86514,1.96744 0.79304,0 1.67922,-0.16051 2.57386,-0.34678 v 1.02941 c -0.89464,0.17815 -1.83162,0.36336 -2.78518,0.36336" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2444"/><path d="m 746.31279,95.668076 c -0.98778,0 -1.36772,0.35489 -1.44357,1.00471 h 2.86985 c -0.0342,-0.69215 -0.43921,-1.00471 -1.42628,-1.00471 m -0.1778,3.73874 c -0.70026,0 -1.33315,-0.084 -1.80587,-0.47308 -0.50624,-0.42121 -0.68368,-1.05445 -0.68368,-1.78082 0,-0.64982 0.21131,-1.37513 0.80221,-1.7974 0.52282,-0.37112 1.18992,-0.44697 1.86514,-0.44697 0.60748,0 1.32503,0.0674 1.84785,0.43039 0.68404,0.48119 0.74295,1.22414 0.75106,2.1015 h -4.05059 c 0.025,0.65016 0.37112,1.07209 1.56951,1.07209 0.7426,0 1.56951,-0.10972 2.27048,-0.21943 v 0.83538 c -0.8188,0.13546 -1.71345,0.27834 -2.56611,0.27834" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2448"/><path d="m 754.20619,99.288706 v -2.35514 c 0,-0.77576 -0.39617,-1.05446 -1.1056,-1.05446 -0.52317,0 -1.1811,0.13476 -1.73849,0.27023 v 3.13937 h -1.18992 v -4.26226 h 0.97049 l 0.0423,0.27023 c 0.75071,-0.19368 1.5868,-0.38806 2.27895,-0.38806 0.52281,0 1.0548,0.0755 1.45979,0.43886 0.33726,0.30409 0.46426,0.72531 0.46426,1.3335 v 2.60773 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2452"/><path d="m 758.77509,99.406676 c -0.57362,0 -1.09714,-0.16051 -1.38395,-0.60748 -0.21096,-0.3041 -0.31256,-0.71685 -0.31256,-1.29117 v -1.59561 h -0.86078 v -0.88583 h 0.86078 l 0.127,-1.29152 h 1.05481 v 1.29152 h 1.67922 v 0.88583 h -1.67922 v 1.36772 c 0,0.32914 0.025,0.60748 0.11782,0.81033 0.12665,0.2868 0.40499,0.39617 0.77647,0.39617 0.27834,0 0.6163,-0.0423 0.85231,-0.0836 v 0.8516 c -0.38806,0.0766 -0.83573,0.15205 -1.2319,0.15205" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2456"/><path d="m 761.10053,99.288706 v -4.26226 h 0.97084 l 0.0423,0.27023 c 0.78493,-0.21943 1.36702,-0.38806 2.10997,-0.38806 0.0335,0 0.0843,0 0.15134,0.008 v 1.01317 c -0.13512,-0.008 -0.29528,-0.008 -0.41346,-0.008 -0.58243,0 -1.02094,0.067 -1.67111,0.21943 v 3.14748 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2460"/><path d="m 768.70144,97.499666 c -0.49812,-0.0427 -1.00435,-0.0674 -1.53599,-0.0674 -0.86925,0 -1.17369,0.17709 -1.17369,0.57326 0,0.37148 0.25364,0.57433 0.92004,0.57433 0.55739,0 1.22379,-0.1263 1.78964,-0.25365 z m 0.25295,1.78894 -0.0335,-0.2533 c -0.72602,0.1778 -1.56987,0.37148 -2.31282,0.37148 -0.45578,0 -0.94509,-0.0593 -1.29152,-0.31256 -0.31996,-0.22755 -0.47236,-0.59902 -0.47236,-1.02941 0,-0.48154 0.21131,-0.92851 0.71755,-1.15605 0.44732,-0.21096 1.04633,-0.22754 1.59526,-0.22754 0.44732,0 1.04634,0.0247 1.54446,0.0589 v -0.0765 c 0,-0.6664 -0.43921,-0.88583 -1.6376,-0.88583 -0.4639,0 -1.02976,0.0247 -1.56986,0.0755 v -0.86082 c 0.59902,-0.0497 1.27459,-0.084 1.83198,-0.084 0.74224,0 1.51059,0.0593 1.98296,0.39652 0.4893,0.34643 0.58244,0.82762 0.58244,1.45979 v 2.52378 z" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" id="path2464"/><path id="path2466" style="fill:#939598;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.35277778" d="m 771.42178,93.229356 h 1.18992 v 6.05931 h -1.18992 z"/></g></svg>

                                                <h3 class="card-title">Como pagar com Pix</h3>

                                                <style>
                                                    #pix-steps .step-item:after {
                                                        content: none !important;
                                                    }
                                                    #pix-steps .step-item:after, #pix-steps .step-item:before {
                                                        color: #182433 !important;
                                                        background: transparent !important;
                                                        border: 1px solid #182433 !important;
                                                        font-weight: 700;
                                                    }
                                                </style>

                                                <ul class="steps steps-counter steps-vertical" id="pix-steps">
                                                    <li class="step-item">Acesse o app ou site do seu banco</li>
                                                    <li class="step-item">Busque a opção de pagar com Pix</li>
                                                    <li class="step-item">Leia o QR code ou código Pix</li>
                                                    <li class="step-item">Pronto! Você verá a confirmação do pagamento.</li>
                                                </ul>

                                                <img class="w-100 p-5" src="<?= $pedido && $pedido['forma_pagamento'] == 'PIX' ? "data:image/png;base64,{$pedido['pix_encodedImage']}" : "#"; ?>" alt="QR Code" id="pix-qrcode">

                                                <p>Código válido por 30 minutos. Se preferir, você pode copiar o código abaixo:</p>

                                                <textarea class="form-control text-center mb-3" id="pix-text" rows="5" style="resize: none; background: transparent; border: 1px solid;" readonly><?= $pedido && $pedido['forma_pagamento'] == 'PIX' ? $pedido['pix_payload'] : ""; ?></textarea>

                                                <button type="button" id="copy-btn" class="btn btn-6 btn-dark btn-pill w-100 mb-0">
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 icon-tabler icon-tabler-check" style="display: none;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    <span>Copiar código</span>
                                                </button>
                                            </div>
                                        </div>
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
                                    <?php if ($pedido): ?>
                                    <th class="w-1 text-center"></th>
                                    <?php endif; ?>
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
                                            <?php if (!$pedido): ?>
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
                                            <?php endif; ?>
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

                        <?php if (!$pedido): ?>
                        <!-- <button type="button" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout"> Confirmar </button> -->
                        <!-- Botão para avançar para a etapa 2 -->
                        <button type="button" id="btn-step1-continue" class="btn btn-6 btn-dark btn-pill w-100 confirm-checkout mt-3">Continuar</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo INCLUDE_PATH; ?>assets/js/main.js" defer></script>
<!-- Inclusão do Card.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.5.0/card.min.js"></script>

<script>
function loadShippingOptions(cep, pesoEmGramas) {
  $.ajax({
    url: '<?= INCLUDE_PATH; ?>back-end/carrinho/frete.php',
    method: 'POST',
    data: { cep: cep, weight: pesoEmGramas },
    success: function(response) {
      var res = (typeof response === 'string') ? JSON.parse(response) : response;
      var $opts = $('#shipping-options').empty();

      if (res.status === 'sucesso') {
        // filtra apenas sem erro
        var valid = res.options.filter(function(opt) { return !opt.error; });

        if (valid.length === 0) {
          $opts.append('<div class="text-danger">Nenhuma opção de frete disponível.</div>');
          return;
        }

        valid.forEach(function(opt, i) {
          // formata preço em R$
          var priceText = Number(opt.price)
            .toLocaleString('pt-BR',{ style:'currency', currency:'BRL' });
          // formato do prazo (ex: de 1 a 6 dias úteis)
          var deadlineText = 'de ' + opt.delivery_range.min + ' a ' + opt.delivery_range.max + ' dias úteis';

          // cria radio + label
          var radioId = 'shipping-' + opt.id;
          var $radio = $(
            '<div class="form-check">'+
              '<input class="form-check-input" '+
                     'type="radio" '+
                     'name="shipping_method" '+
                     'id="'+ radioId +'" '+
                     'value="'+ opt.id +'" '+
                     'data-price="'+ opt.price +'">'+
              '<label class="form-check-label" for="'+ radioId +'">'+
                priceText + ' - ' + opt.company.name + ' - ' + deadlineText +
              '</label>'+
            '</div>'
          );
          $opts.append($radio);
        });

        // ao mudar, atualiza o TD do frete
        $opts.find('input[type=radio]').on('change', function() {
          var price = parseFloat($(this).data('price'));
          var priceText = price.toLocaleString('pt-BR',{ style:'currency', currency:'BRL' });

          // atualiza o texto e o atributo data-value
          $('#frete_valor')
            .text(priceText)
            .attr('data-value', price);

          // Quando qualquer input radio do grupo "shipping_method" for selecionado,
          // remove a classe "d-none" do botão de continuar
          $('#btn-step-shipping-continue').removeClass('d-none');
        });

      } else {
        $opts.append('<div class="text-danger">'+
                     (res.mensagem||'Não foi possível calcular o frete.')+
                     '</div>');
      }
    },
    error: function(xhr, status, error) {
      console.error('Erro AJAX:', xhr.responseJSON.mensagem);
      $('#shipping-options').empty()
        .append('<div class="text-danger">Erro ao carregar frete.</div>');
    }
  });
}

// dispara após usuário clicar em “continuar” da etapa 1
$('#btn-step1-continue').on('click', function() {
  var cep  = $('#field-zipcode').val().replace(/\D/g, '');
  var peso = 3; // substitua pelo peso real
  loadShippingOptions(cep, peso);
});

$('#btn-step-shipping-continue').on('click', function() {
    // Oculta o botão "Continuar"
    $(this).addClass("d-none");
    $('#change-shipping-button').removeClass("d-none");
    $('#payment-section-form').removeClass("d-none");

    // Obtém o input radio selecionado
    var selected = $('input[name="shipping_method"]:checked');

    if (selected.length === 0) {
        $(".alert").remove();
        $("#shipping-options").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Por favor, selecione uma opção de frete.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        return;
    }

    // Oculta todas as opções, exceto a selecionada
    $('input[name="shipping_method"]').not(':checked').closest('.form-check').hide();
});

$('#change-shipping-option').on('click', function() {
    // Oculta o botão "Continuar"
    $('#change-shipping-button').addClass("d-none");
    $('#payment-section-form').addClass("d-none");
    $('#btn-step-shipping-continue').removeClass("d-none");

    // Exibe todas as opções
    $('input[name="shipping_method"]').closest('.form-check').show();
});

</script>

<script>
    $(document).ready(function(){
        $("#boleto-text").on("click", function(){
            // Seleciona todo o conteúdo do textarea
            $(this).select();
            // Tenta copiar para a área de transferência
            try {
                var successful = document.execCommand('copy');
                if(successful){
                } else {

                    // Exibe o modal de validação
                    var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                    $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                    $('#modal-validar .modal-body #msg-modal-validar').text('Não foi possível copiar o código.');
                    myModal.show();
                }
            } catch (err) {

                // Exibe o modal de validação
                var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                $('#modal-validar .modal-body #msg-modal-validar').text('Ao copiar: ' + err);
                myModal.show();
            }
        });

        $("#pix-text").on("click", function(){
            // Seleciona todo o conteúdo do textarea
            $(this).select();
            // Tenta copiar para a área de transferência
            try {
                var successful = document.execCommand('copy');
                if(successful){
                } else {

                    // Exibe o modal de validação
                    var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                    $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                    $('#modal-validar .modal-body #msg-modal-validar').text('Não foi possível copiar o código.');
                    myModal.show();
                }
            } catch (err) {

                // Exibe o modal de validação
                var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                $('#modal-validar .modal-body #msg-modal-validar').text('Ao copiar: ' + err);
                myModal.show();
            }
        });

        $("#copy-btn").on("click", function(){
            // Seleciona todo o conteúdo do textarea
            $("#pix-text").select();
            try {
                var successful = document.execCommand('copy');
                if(successful){
                    var $btn = $(this);
                    // Altera a aparência do botão com animação simples
                    $btn.removeClass("btn-dark").addClass("btn-teal");
                    // Exibe o ícone de check com fadeIn
                    $btn.find("svg").fadeIn(200);
                    // Altera o texto do span para "Copiado"
                    $btn.find("span").text("Copiado");
                    
                    // Após 3 segundos, reverte para o estado original
                    setTimeout(function(){
                        $btn.removeClass("btn-teal").addClass("btn-dark");
                        $btn.find("span").text("Copiar código");
                        $btn.find("svg").fadeOut(200);
                    }, 3000);
                } else {

                    // Exibe o modal de validação
                    var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                    $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                    $('#modal-validar .modal-body #msg-modal-validar').text('Não foi possível copiar o código.');
                    myModal.show();
                }
            } catch(e) {

                    // Exibe o modal de validação
                    var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                    $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                    $('#modal-validar .modal-body #msg-modal-validar').text('Ao copiar: ' + e);
                    myModal.show();
            }
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#card-expiry').mask('00 / 00');

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

        if (subtotal === 0) {
            $('#frete_valor').data('value', 0);
            $('#frete_valor').text('R$ 0,00');
        }

        // Pega frete e desconto dos atributos data
        var frete = parseFloat($('#frete_valor').data('value')) || 0;
        var desconto = parseFloat($('#desconto_valor').data('value')) || 0;
        var total = subtotal + frete - desconto;
        // Atualiza o TOTAL na tabela
        $('#total_valor').text('R$ ' + total.toFixed(2).replace('.', ','));

        // Após recalcular o total, atualiza as opções de parcelamento
        atualizarParcelas(total);
    }

    // Função para gerar as opções de parcelamento com base no total
    function gerarParcelas(total) {
        var minParcela = 5.00; // Valor mínimo por parcela
        var maxParcelas = 6;  // Máximo de parcelas
        var parcelas = [];
        for (var i = 1; i <= maxParcelas; i++) {
            var valorParcela = total / i;
            if (valorParcela >= minParcela) {
                parcelas.push({ parcela: i, valor: valorParcela });
            } else {
                break; // Interrompe se o valor da parcela for menor que o mínimo
            }
        }
        return parcelas;
    }

    // Função para atualizar o select de parcelamento com as opções geradas
    function atualizarParcelas(total) {
        var parcelas = gerarParcelas(total);
        var $select = $('#card-installments');
        $select.empty();
        $.each(parcelas, function(index, item) {
            var optionText = "";
            if (item.parcela == 1) {
                optionText = "À vista 1x - R$ " + item.valor.toFixed(2).replace('.', ',');
            } else {
                optionText = item.parcela + "x de R$ " + item.valor.toFixed(2).replace('.', ',') + " sem juros";
            }
            $select.append($('<option>', { value: item.parcela, text: optionText }));
        });
        window.numParcelasDisponiveis = parcelas.length;
    }

    // Exemplo: Remover item do checkout via AJAX (já existente)
    $('.remove-checkout-item').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var itemId = btn.data('item-id');

        // Exibe o modal de confirmação
        var myModal = new bootstrap.Modal(document.getElementById('modal-delete'));
        myModal.show();

        $('#remove-item').on('click', function() {
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
                            // Atualiza ou remove o contador do carrinho
                            if (res.numero_itens > 0) {
                                $("#cart-count").text(res.numero_itens).show();
                            } else {
                                $("#cart-count").fadeOut();
                            }
                        } else {
                            // Exibe o modal de validação
                            var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                            $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                            $('#modal-validar .modal-body #msg-modal-validar').text(res.mensagem);
                            myModal.show();
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

        /*$.ajax({
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
        });*/
    });

    // Se necessário, inicialize as opções de parcelamento ao carregar a página
    // Exemplo: Se já existir um total definido
    var totalText = $('#total_valor').text().trim(); // Ex: "R$ 123,45"
    if(totalText) {
        var total = parseFloat(totalText.replace("R$", "").replace(/\./g, "").replace(",", "."));
        if(!isNaN(total)) {
            atualizarParcelas(total);
        }
    }
});
</script>

<script>
var pedidoExiste = <?= $pedido ? 'true' : 'false'; ?>;

$(document).ready(function() {
	$('#field-zipcode').mask('00000-000');

    function removeDeleteItemColumn() {
        $("#usuario-info").remove();
        $("#btn-step-shipping-continue, #change-shipping-button").remove();

        // Remove a coluna de remoção do cabeçalho da tabela
        $("thead tr th:last-child").remove();

        // Remove a coluna de remoção de cada linha no corpo da tabela
        $("#checkout-items tr").each(function() {
            $(this).find("td:last-child").remove();
        });

        console.log('Coluna de remoção de item do carrinho deletada.');
    }

    function mascaraCodigoBoleto(codigo) {
        // Remover todos os caracteres não numéricos
        codigo = codigo.replace(/\D/g, '');

        // Aplicar a máscara
        codigo = codigo.replace(/(\d{5})(\d{5})(\d{5})(\d{6})(\d{5})(\d{14})/, '$1.$2 $3.$4 $5.$6');

        return codigo;
    }

    // Exibe a etapa 1 e oculta as demais ao carregar
    if (!pedidoExiste) {
        $('#step-1').removeClass('d-none');
        $('#step-2').addClass('d-none');
    }

    // // Ao clicar no botão "Continuar" da Etapa 1
    // $('#btn-step1-continue').on('click', function() {
    //     // Coleta os dados do formulário da Etapa 1
    //     var dados = {
    //         email: $('#field-eee').val(),
    //         name: $('#field-name').val(),
    //         cpf: $("#foreign").is(":checked") ? "" : $('#field-cpf').val(),
    //         birthDate: $('#field-birth-date').val(),
    //         phone: $('#field-phone').val(),
    //         zipcode: $("#foreign").is(":checked") ? "" : $('#field-zipcode').val(),
    //         street: $('#field-street').val(),
    //         streetNumber: $('#field-street-number').val(),
    //         complement: $('#field-complement').val(),
    //         district: $('#field-district').val(),
    //         city: $('#field-city').val(),
    //         state: $('#field-state').val(),
    //         country: $("#foreign").is(":checked") ? $('#field-country').val() : "Brasil",

    //         foreign: $("#foreign").is(":checked") ? 1 : '',
    //         newsletter: $("#newsletter").is(":checked") ? 1 : '',
    //         terms: $("#terms").is(":checked") ? 1 : '',
    //     };

    //     // Validação simples: verifica se os campos obrigatórios estão preenchidos
    //     if (!dados.email || !dados.name) {
    //         alert('Por favor, preencha os campos obrigatórios.');
    //         return;
    //     }

    //     // Envia os dados via AJAX para salvar (pode ser em cookie ou em banco de dados)
    //     $.ajax({
    //         url: '<?= INCLUDE_PATH; ?>back-end/carrinho/salvar_dados.php',
    //         type: 'POST',
    //         data: dados,
    //         success: function(response) {
    //             try {
    //                 var res = JSON.parse(response);

    //                 if (res.status === 'sucesso') {
    //                     var address = `${dados.street}, ${dados.streetNumber} - ${dados.district}`;

    //                     var birthDateRaw = dados.birthDate;
    //                     var birthDateFormatted = birthDateRaw.split('-').reverse().join('/'); // Converte "aaaa-mm-dd" para "dd/mm/aaaa"

    //                     // Preenche os campos da Etapa 2 com os dados salvos
    //                     $('#saved-email').val(dados.email);
    //                     $('#saved-name').text(dados.name);
    //                     if($("#foreign").is(":checked")){
    //                         $('#saved-cpf').text("-");
    //                     }else{
    //                         $('#saved-cpf').text(dados.cpf);
    //                     }
    //                     $('#saved-birth-date').text(birthDateFormatted);
    //                     $('#saved-phone').text(dados.phone);
    //                     $('#saved-street').text(address);
    //                     if($("#foreign").is(":checked")){
    //                         $('#saved-zipcode').text("-");
    //                     }else{
    //                         $('#saved-zipcode').text(dados.zipcode);
    //                     }
    //                     $('#saved-city').text(dados.city);
    //                     $('#saved-state').text(dados.state);

    //                     // Oculta a Etapa 1 e exibe a Etapa 2
    //                     $('#step-1').addClass('d-none');
    //                     $('#step-2').removeClass('d-none');

    //                     $('#step-item-1').removeClass('active');
    //                     $('#step-item-2').addClass('active');

    //                     $('#btn-step1-continue').addClass('d-none');
    //                 } else {
    //                     alert("Erro ao salvar dados: " + res.mensagem);
    //                 }
    //             } catch(e) {
    //                 console.log("Resposta inválida: " + response);
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.log("Erro AJAX: " + error);
    //         }
    //     });
    // });

    // Função que envia os dados do formulário via AJAX
    function enviarDadosEtapa1() {
        var dados = {
            email: $('#field-email').val(),
            name: $('#field-name').val(),
            cpf: $('#field-cpf').val(),
            birthDate: $('#field-birth-date').val(),
            phone: $('#field-phone').val(),
            zipcode: $('#field-zipcode').val(),
            street: $('#field-street').val(),
            streetNumber: $('#field-street-number').val(),
            complement: $('#field-complement').val(),
            district: $('#field-district').val(),
            city: $('#field-city').val(),
            state: $('#field-state').val(),
            newsletter: $("#newsletter").is(":checked") ? 1 : '',
            terms: $("#terms").is(":checked") ? 1 : '',
        };

        function validate(campos) {

            let validated = false;

            Object.entries(campos).forEach(([key, value]) => {

                key = key.replace(/([a-zA-Z])(?=[A-Z])/g,'$1-').toLowerCase()

                if (!value && ($('#field-'+key).prop('required') || $('#'+key).prop('required'))) {

                    $('#field-'+key).addClass('is-invalid');
                    $('#input-'+key+'-error').removeClass('d-none');
                    $('#field-'+key).on('blur', function() {

                        if (key === 'email' && $('#field-'+key).val() !== '') {

                            const emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

                            if (!emailReg.test($('#field-'+key).val())) {
                                $('#input-email-format-error').removeClass('d-none');
                            } else {
                                $('#input-email-format-error').addClass('d-none');
                            }
                        }

                        if (key == 'terms') {
                            $('#input-terms-error').removeClass('d-none');
                        }

                        if ($('#field-'+key).val() != '') {
                            $('#field-'+key).removeClass('is-invalid');
                            $('#input-'+key+'-error').addClass('d-none');
                            $('input-email-format-error').addClass('d-none');
                        }
                    });

                    validated = false;
                } else if (value && $('#field-'+key).prop('required')) {

                    $('#field-'+key).removeClass('is-invalid');
                    $('#input-'+key+'-error').addClass('d-none');

                    validated = true;
                }
            })

            return validated;
        }

        // Validação de campos obrigatórios
        if (!validate(dados)) {
            return;
        };

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

                        if (pedidoExiste) {
                            $('#step-item-1').removeClass('active');
                            $('#step-item-3').addClass('active');
                        } else {
                            $('#step-item-1').removeClass('active');
                            $('#step-item-2').addClass('active');
                        }

                        $('#btn-step1-continue').addClass('d-none');
                    } else {

                        // Exibe o modal de validação
                        var myModal = new bootstrap.Modal(document.getElementById('modal-validar'));
                        $('#modal-validar .modal-body #msg-modal-validar-titulo').text('Erro...');
                        $('#modal-validar .modal-body #msg-modal-validar').text(res.mensagem);
                        myModal.show();
                    }
                } catch(e) {
                    console.log("Resposta inválida: " + response);
                }
            },
            error: function(xhr, status, error) {
                console.log("Erro AJAX: " + error);
            }
        });
    }

    // Se o pedido já existe, dispara a consulta AJAX imediatamente
    if (pedidoExiste) {
        enviarDadosEtapa1();
    }

    // Caso contrário, aguarda o clique do botão
    $('#btn-step1-continue').on('click', function() {
        enviarDadosEtapa1();
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
        $('#field-phone').val('');
        $('#field-zipcode').val('');
        $('#field-street').val('');
        $('#field-street-number').val('');
        $('#field-complement').val('');
        $('#field-district').val('');
        $('#field-city').val('');
        $('#field-state').val('');
        $('#newsletter').prop('checked', false);
        $('#terms').prop('checked', false);

        // Oculta a Etapa 2 e exibe a Etapa 1
        $('#step-2').addClass('d-none');
        $('#step-1').removeClass('d-none');

        // Voltar campos padrao para brasileiro
        $("#div-cpf-field").slideDown();
        $("#div-cep-field").slideDown();
        $("#div-country-field").addClass("d-none")

        // Restaurar classes de colunas para Brasil
        $("#div-district-field").removeClass("col-md-6").addClass("col-md-12");
        $("#div-city-field").removeClass("col-md-6").addClass("col-md-8");

        $('#btn-step1-continue').removeClass('d-none');
    });

    // Quando qualquer input radio do grupo "payment" for selecionado,
    // remove a classe "d-none" do botão de continuar
    $(document).ready(function(){
        $('input[name="payment"]').on('change', function() {
            var method = $(this).val(); // valor do método de pagamento
            $('#btn-step2-continue').removeClass('d-none');

            if(method === "102" || method.toUpperCase() === "PIX"){
                $('#btn-step2-continue span').html('Pagar via PIX');
            } else {
                $('#btn-step2-continue span').html('Avançar');
            }
        });
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

            // Salva os dados do frete
            var shipping = {
                valor: $('#frete_valor').data('value'),
                cep: $('#field-zipcode').val(),
                shipping_method: $('input[name="shipping_method"]:checked').val()
            }

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
                phone: $('#field-phone').val(),
                postalCode: $('#field-zipcode').val(),
                address: $('#field-street').val(),
                addressNumber: $('#field-street-number').val(),
                complement: $('#field-complement').val(),
                province: $('#field-district').val(),
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
                shipping: shipping,
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
                    removeDeleteItemColumn();

                    $(".alert").remove();

                    $('#step-item-2').removeClass('active');
                    $('#step-item-3').addClass('active');

                    $("#payment-section").addClass('d-none');
                    $("#payment-successful").removeClass('d-none');

                    $('#pedido-id').text(response.order);

                    // Atualiza a URL sem recarregar a página
                    let paymentUrl = window.location.pathname + "?pedido=" + response.order;
                    window.history.pushState({ path: paymentUrl }, "", paymentUrl);

                    var encodedCode = btoa(response.code);
                    var customerId = btoa(response.id);

                    $.ajax({
                        url: '<?php echo INCLUDE_PATH; ?>back-end/sql.php',
                        method: 'POST',
                        data: {encodedCode: encodedCode},
                        dataType: 'JSON'
                    })
                    .done(function(data) {
                        // printPaymentData(data);
                        console.log(data);
                        console.log("Metodo de pagamento: " + method);

                        if (method === "101") {
                            $('#paymentMethodText').text('Boleto Bancário');
                            $("#boleto-section").removeClass('d-none');

                            if (data.link_boleto) {
                                $("#boleto-link").attr("href", data.link_boleto);
                            }
                            if (data.boleto_identificationField) {
                                $("#boleto-text").val(mascaraCodigoBoleto(data.boleto_identificationField));
                            }
                        } else if (method === "102") {
                            $('#paymentMethodText').text('Pix');
                            $("#pix-section").removeClass('d-none');

                            // Verifica se há um código de imagem PIX Base64
                            if (data.pix_encodedImage) {
                                $("img#pix-qrcode").attr("src", "data:image/png;base64," + data.pix_encodedImage);
                            }
                            if (data.pix_payload) {
                                $("#pix-text").val(data.pix_payload);
                            }
                        }

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

                    // Remover botão carregando e exibir os demais controles (ajuste conforme sua lógica)
                    $(".progress-subscription").addClass('d-none').removeClass('d-flex');
                    $("#btn-step2-continue").addClass('d-block').removeClass('d-none');
                } else if (response.status == 400) {
                    $("#div-errors-price").html(response.message).slideDown('fast').effect("shake");
                    $('html, body').animate({scrollTop : 0});
                    $(".progress-subscription").addClass('d-none').removeClass('d-flex');
                    $("#btn-step2-continue").addClass('d-block').removeClass('d-none');

                    $(".alert").remove();
                    $("#alert-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Erro no AJAX:", status, error);

                $(".alert").remove();
                $("#alert-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                //Remove botão carregando
                $(".progress-subscription").addClass('d-none').removeClass('d-flex');
                $("#btn-step2-continue").addClass('d-block').removeClass('d-none');
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
        $("#confirm-payment .progress-subscription").addClass('d-flex').removeClass('d-none');
        $(this).addClass('d-none').removeClass('d-block');

        var typePayment = $('input[name="payment"]:checked').val();
        localStorage.setItem("method", typePayment);
        method = localStorage.getItem("method");

        // Salva os dados do frete
        var shipping = {
            valor: $('#frete_valor').data('value'),
            cep: $('#field-zipcode').val(),
            shipping_method: $('input[name="shipping_method"]:checked').val()
        }

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
            cpfCnpj: $('#field-cpf').val().match(/\d/g).join(""),
            name: $('#field-name').val(),
            birth_date: $('#field-birth-date').val(),
            phone: $('#field-phone').val().match(/\d/g).join(""),
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
            shipping: shipping,
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
                removeDeleteItemColumn();

                $(".alert").remove();

                $('#step-item-2').removeClass('active');
                $('#step-item-3').addClass('active');

                $("#payment-section").addClass('d-none');
                $("#payment-successful").removeClass('d-none');

                $('#pedido-id').text(response.order);

                // Atualiza a URL sem recarregar a página
                let paymentUrl = window.location.pathname + "?pedido=" + response.order;
                window.history.pushState({ path: paymentUrl }, "", paymentUrl);

                $('#paymentMethodText').text('Cartão de crédito');









                //Remove botão carregando
                $("#confirm-payment .progress-subscription").addClass('d-none').removeClass('d-flex');
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
            } else if (response.status == 400 || response.errors) {

                if (response.message) {
                    $("#div-errors-price").html(response.message).slideDown('fast').effect("shake");
                }

                if (response.errors) {
                    response.errors.forEach((error) => {
                        $("#div-errors-price").html(error.description).slideDown('fast').effect("shake");
                    })
                }

                $('html, body').animate({scrollTop : 0});

                //Remove botão carregando
                $("#confirm-payment .progress-subscription").addClass('d-none').removeClass('d-flex');
                $(this).addClass('d-block').removeClass('d-none');

                $(".alert").remove();
                if (response.error) {
                    $("#alert-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.error + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }

                if (response.errors) {
                    response.errors.forEach((error) => {
                        $("#alert-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + error.description + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    })
                }

            }
        })
        .fail(function(xhr, status, error) {
            console.error("Erro no AJAX:", status, error);

            $(".alert").remove();
            $("#alert-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

            //Remove botão carregando
            $("#confirm-payment .progress-subscription").addClass('d-none').removeClass('d-flex');
            $(this).addClass('d-block').removeClass('d-none');
        });
    });
});
</script>