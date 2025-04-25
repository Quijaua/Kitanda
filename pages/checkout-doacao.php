<?php
    $query = "SELECT captcha_type AS type FROM tb_page_captchas WHERE page_name = :page_name";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':page_name', 'doacao');
    $stmt->execute();
    $captcha = $stmt->fetch(PDO::FETCH_ASSOC);

	// Acessa as variáveis de ambiente
    if ($captcha['type'] == 'hcaptcha') {
        $hcaptcha = [
            'public_key' => $_ENV['HCAPTCHA_CHAVE_DE_SITE']
        ];
    } elseif ($captcha['type'] == 'turnstile') {
        $turnstile = [
            'public_key' => $_ENV['TURNSTILE_CHAVE_DE_SITE']
        ];
    }

    // Tabela que sera feita a consulta
    $tabela = "tb_checkout";
	$tabela_2 = "tb_integracoes";
	$tabela_3 = "tb_mensagens";

    // ID que você deseja pesquisar
    $id = 1;

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";
	$sql_2 = "SELECT * FROM $tabela_2 WHERE id = :id";
	$sql_3 = "SELECT use_privacy FROM $tabela_3 WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);
	$stmt_2 = $conn->prepare($sql_2);
	$stmt_3 = $conn->prepare($sql_3);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt_2->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt_3->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();
	$stmt_2->execute();
	$stmt_3->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
	$resultado_2 = $stmt_2->fetch(PDO::FETCH_ASSOC);
	$resultado_3 = $stmt_3->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna à variável, ex.: "nome" = $nome
        $nome = $resultado['nome'];
        $logo = $resultado['logo'];
        $title = $resultado['title'];
        $descricao = $resultado['descricao'];
        $doacoes = $resultado['doacoes'];
        $pix_chave = $resultado['pix_chave'];
        $pix_valor = $resultado['pix_valor'];
        $pix_codigo = $resultado['pix_codigo'];
        $pix_imagem_base64 = $resultado['pix_imagem_base64'];
        $pix_identificador_transacao = $resultado['pix_identificador_transacao'];
        $pix_exibir = $resultado['pix_exibir'];
        $privacidade = $resultado['privacidade'];
        $faq = $resultado['faq'];
		$use_faq = $resultado['use_faq'];
        $facebook = $resultado['facebook'];
        $instagram = $resultado['instagram'];
        $linkedin = $resultado['linkedin'];
        $twitter = $resultado['twitter'];
        $youtube = $resultado['youtube'];
        $website = $resultado['website'];
		$tiktok = $resultado['tiktok'];
		$linktree = $resultado['linktree'];
        $cep = $resultado['cep'];
        $rua = $resultado['rua'];
        $numero = $resultado['numero'];
        $bairro = $resultado['bairro'];
        $cidade = $resultado['cidade'];
        $estado = $resultado['estado'];
        $telefone = $resultado['telefone'];
        $email = $resultado['email'];
        $nav_color = $resultado['nav_color'];
        $nav_background = $resultado['nav_background'];
        $background = $resultado['background'];
        $text_color = $resultado['text_color'];
        $color = $resultado['color'];
        $hover = $resultado['hover'];
        $progress = $resultado['progress'];
		$monthly_1 = $resultado['monthly_1'];
        $monthly_2 = $resultado['monthly_2'];
        $monthly_3 = $resultado['monthly_3'];
        $monthly_4 = $resultado['monthly_4'];
        $monthly_5 = $resultado['monthly_5'];
        $yearly_1 = $resultado['yearly_1'];
        $yearly_2 = $resultado['yearly_2'];
        $yearly_3 = $resultado['yearly_3'];
        $yearly_4 = $resultado['yearly_4'];
        $yearly_5 = $resultado['yearly_5'];
        $once_1 = $resultado['once_1'];
        $once_2 = $resultado['once_2'];
        $once_3 = $resultado['once_3'];
        $once_4 = $resultado['once_4'];
        $once_5 = $resultado['once_5'];
    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }

	// Verificar se o resultado_2 foi encontrado
	if ($resultado_2) {
		// Atribuir o valor da coluna à variável, ex.: "nome" = $nome
		$fb_pixel = $resultado_2['fb_pixel'];
		$gtm = $resultado_2['gtm'];
		$g_analytics = $resultado_2['g_analytics'];
	} else {
		// ID não encontrado ou não existente
		echo "ID não encontrado.";
	}

	// Verificar se o resultado_3 foi encontrado
	if ($resultado_3) {
		// Atribuir o valor da coluna à variável, ex.: "nome" = $nome
		$use_privacy = $resultado_3['use_privacy'];
	} else {
		// ID não encontrado ou não existente
		echo "ID não encontrado.";
	}
?>
<?php
	$donationButtons = array(
		"donationMonthlyButton1" => array("amount" => $monthly_1, "display" => "R$ $monthly_1", "showAddOnFee" => true),
		"donationMonthlyButton2" => array("amount" => $monthly_2, "display" => "R$ $monthly_2", "showAddOnFee" => true),
		"donationMonthlyButton3" => array("amount" => $monthly_3, "display" => "R$ $monthly_3", "showAddOnFee" => true),
		"donationMonthlyButton4" => array("amount" => $monthly_4, "display" => "R$ $monthly_4", "showAddOnFee" => true),
		"donationMonthlyButton5" => array("amount" => $monthly_5, "display" => "R$ $monthly_5", "showAddOnFee" => true),
	
		"donationYearlyButton1" => array("amount" => $yearly_1, "display" => "R$ $yearly_1", "showAddOnFee" => true),
		"donationYearlyButton2" => array("amount" => $yearly_2, "display" => "R$ $yearly_2", "showAddOnFee" => true),
		"donationYearlyButton3" => array("amount" => $yearly_3, "display" => "R$ $yearly_3", "showAddOnFee" => true),
		"donationYearlyButton4" => array("amount" => $yearly_4, "display" => "R$ $yearly_4", "showAddOnFee" => true),
		"donationYearlyButton5" => array("amount" => $yearly_5, "display" => "R$ $yearly_5", "showAddOnFee" => true),
	
		"donationOnceButton1" => array("amount" => $once_1, "display" => "R$ $once_1", "showAddOnFee" => true),
		"donationOnceButton2" => array("amount" => $once_2, "display" => "R$ $once_2", "showAddOnFee" => true),
		"donationOnceButton3" => array("amount" => $once_3, "display" => "R$ $once_3", "showAddOnFee" => true),
		"donationOnceButton4" => array("amount" => $once_4, "display" => "R$ $once_4", "showAddOnFee" => true),
		"donationOnceButton5" => array("amount" => $once_5, "display" => "R$ $once_5", "showAddOnFee" => true),
	);
	
	$addOnFeeValues = array(
		"creditCard" => array("fix" => 0, "percent" => 5),
		"bankSlip" => array("fix" => 3, "percent" => 5),
		"pix" => array("fix" => 0, "percent" => 5),
	);
	
	$minOnceDonation = array(
		"creditCard" => 10,
		"bankSlip" => 10,
		"pix" => 10,
	);
	
	$jsonData = array(
		"donationButtons" => $donationButtons,
		"addOnFeeValues" => $addOnFeeValues,
		"minOnceDonation" => $minOnceDonation,
	);
?>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">
			<div class="col-md-5">

				<form class="w-100" id="form-checkout" action="submit">

					<div class="divide-y">

						<div>

							<div class="mb-3">
								<div class="btn-group w-100" role="group">
									<input type="radio" class="btn-check" name="inlineRadioOptions" id="inlineRadio1" onclick="setPeriodOption('monthly')" value="MONTHLY" checked>
									<label for="inlineRadio1" type="button" class="btn">Mensal</label>

									<input type="radio" class="btn-check" name="inlineRadioOptions" id="inlineRadio2" onclick="setPeriodOption('yearly')" value="YEARLY">
									<label for="inlineRadio2" type="button" class="btn">Anual</label>

									<input type="radio" class="btn-check" name="inlineRadioOptions" id="inlineRadio3" onclick="setPeriodOption('once')" value="ONLY">
									<label for="inlineRadio3" type="button" class="btn">Única</label>
								</div>
							</div>

							<div id="donation-monthly-group" class="mb-2">
								<div class="form-selectgroup row g-2 w-100">
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly1" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'monthly',<?php echo $monthly_1; ?>,true)" data-amount-for-selection="<?php echo $monthly_1; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $monthly_1; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly2" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'monthly',<?php echo $monthly_2; ?>,true)" data-amount-for-selection="<?php echo $monthly_2; ?>" checked>
										<span class="form-selectgroup-label">R$ <?php echo $monthly_2; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly3" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'monthly',<?php echo $monthly_3; ?>,true)" data-amount-for-selection="<?php echo $monthly_3; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $monthly_3; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly4" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'monthly',<?php echo $monthly_4; ?>,true)" data-amount-for-selection="<?php echo $monthly_4; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $monthly_4; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly5" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'monthly',<?php echo $monthly_5; ?>,true)" data-amount-for-selection="<?php echo $monthly_5; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $monthly_5; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="text" class="form-control text-center" id="field-other-monthly"
											onfocus="changeLabelOtherOption(this)"
											onblur="donationOtherOption(this,'monthly',true)"
											style="border-radius: 3px;"
											placeholder="Outro Valor">
									</label>
								</div>
							</div>

							<div id="donation-yearly-group" class="d-none mb-2">
								<div class="form-selectgroup row g-2 w-100">
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly1" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'yearly',<?php echo $yearly_1; ?>,true)" data-amount-for-selection="<?php echo $yearly_1; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $yearly_1; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly2" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'yearly',<?php echo $yearly_2; ?>,true)" data-amount-for-selection="<?php echo $yearly_2; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $yearly_2; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly3" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'yearly',<?php echo $yearly_3; ?>,true)" data-amount-for-selection="<?php echo $yearly_3; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $yearly_3; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly4" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'yearly',<?php echo $yearly_4; ?>,true)" data-amount-for-selection="<?php echo $yearly_4; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $yearly_4; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_monthly" id="button-monthly5" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'yearly',<?php echo $yearly_5; ?>,true)" data-amount-for-selection="<?php echo $yearly_5; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $yearly_5; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="text" class="form-control text-center" id="field-other-yearly"
											onfocus="changeLabelOtherOption(this)"
											onblur="donationOtherOption(this,'yearly',true)"
											style="border-radius: 3px;"
											placeholder="Outro Valor">
									</label>
								</div>
							</div>

							<div id="donation-once-group" class="d-none mb-2">
								<div class="form-selectgroup row g-2 w-100">
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_once" id="button-once1" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'once',<?php echo $once_1; ?>,true)" data-amount-for-selection="<?php echo $once_1; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $once_1; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_once" id="button-once2" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'once',<?php echo $once_2; ?>,true)" data-amount-for-selection="<?php echo $once_2; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $once_2; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_once" id="button-once3" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'once',<?php echo $once_3; ?>,true)" data-amount-for-selection="<?php echo $once_3; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $once_3; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_once" id="button-once4" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'once',<?php echo $once_4; ?>,true)" data-amount-for-selection="<?php echo $once_4; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $once_4; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="radio" name="button_options_once" id="button-once5" class="form-selectgroup-input button-options"
											onclick="donationOption(this,'once',<?php echo $once_5; ?>,true)" data-amount-for-selection="<?php echo $once_5; ?>">
										<span class="form-selectgroup-label">R$ <?php echo $once_5; ?></span>
									</label>
									<label class="form-selectgroup-item col-4 me-0">
										<input type="text" class="form-control text-center" id="field-other-once"
											onfocus="changeLabelOtherOption(this)"
											onblur="donationOtherOption(this,'once',true)"
											style="border-radius: 3px;"
											placeholder="Outro Valor">
									</label>
								</div>
							</div>

							<script>
								function changeLabelOtherOption( ele ) {
									$( ele ).next().html( "OUTRO VALOR" );
								}
							</script>

							<div id="div-errors-price" style="display: none"></div>

						</div>

						<div>

							<div class="mb-3">
								<div class="form-label">Forma de pagamento</div>
								<div>
									<label class="form-check" for="payment-credit-card">
										<input onclick="setPaymentMethod('credit_card')" class="form-check-input" name="payment" id="payment-credit-card" type="radio" value="100" checked>
										<span class="form-check-label">Cartão de Crédito</span>
									</label>
									<label class="form-check" for="payment-bank-slip">
										<input onclick="setPaymentMethod('bank_slip')" class="form-check-input" name="payment" id="payment-bank-slip" type="radio" value="101" checked>
										<span class="form-check-label">Boleto</span>
									</label>
									<label class="form-check" for="payment-pix">
										<input class="form-check-input" type="radio">
										<input onclick="setPaymentMethod('Pix')" class="form-check-input" name="payment" id="payment-pix" type="radio" value="102" checked>
										<span class="form-check-label">PIX</span>
									</label>
								</div>
							</div>

						</div>

						<div>

							<div class="row">

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="email" class="form-control" name="email" id="field-email" placeholder="nome@exemplo.com">
										<label for="email">Endereço de e-mail</label>
									</div>
								</div>

								<div class="col-md-12 mb-3">
									<div class="form-floating">
										<input type="tel" class="form-control" name="phone" id="field-phone" placeholder="(99) 99999-9999" maxlength="15">
										<label for="phone">Telefone</label>
									</div>
								</div>

								<div class="col-md-6 mb-3">
									<div class="form-floating">
										<input type="text" class="form-control" name="name" id="field-name" placeholder="Primeiro nome">
										<label for="field-name">Primeiro nome</label>
									</div>
								</div>

								<div class="col-md-6 mb-3">
									<div class="form-floating">
										<input type="text" class="form-control" name="surname" id="field-surname" placeholder="Sobrenome">
										<label for="field-surname">Sobrenome</label>
									</div>
								</div>

								<div class="col-md-12 mb-3" id="div-cpf-field">
									<div class="form-floating">
										<input type="text" class="form-control" name="cpfCnpj" id="field-cpf" placeholder="CPF">
										<label for="field-cpf">CPF</label>
									</div>
								</div>

								<div id="bank-slip-fields">

									<div class="row">

										<div class="col-md-12 mb-3">
											<div class="form-floating">
												<input onblur="getCepData()" type="text" class="form-control" name="postalCode" id="field-zipcode" placeholder="CEP endereço">
												<label for="field-zipcode">CEP</label>
											</div>
										</div>

										<div class="col-md-8 mb-3 country-brasil">
											<div class="form-floating">
												<input type="text" class="form-control" name="address" id="field-street" placeholder="Logradouro endereço">
												<label for="field-street">Logradouro</label>
											</div>
										</div>

										<div class="col-md-4 mb-3 country-brasil">
											<div class="form-floating">
												<input type="text" class="form-control text-center" name="addressNumber" id="field-street-number" placeholder="Número endereço">
												<label for="field-street-number">Número</label>
											</div>
										</div>

										<div class="col-md-6 mb-2 country-brasil">
											<div class="form-floating">
												<input type="text" class="form-control" name="province" id="field-district" placeholder="Bairro endereço">
												<label for="field-district">Bairro</label>
											</div>
										</div>

										<div class="col-md-6 mb-2 country-brasil">
											<div class="form-floating">
												<input type="text" class="form-control" name="complement" id="field-complement" placeholder="Complemento endereço">
												<label for="field-complement">Complemento</label>
											</div>
										</div>

										<div class="col-md-8 mb-2 country-brasil">
											<div class="form-floating">
												<input type="text" class="form-control" name="city" id="field-city" placeholder="Cidade endereço">
												<label for="field-city">Cidade</label>
											</div>
										</div>

										<div class="col-md-4 mb-2 country-brasil">
											<div class="form-floating">
												<input type="text" class="form-control" name="state" id="field-state" placeholder="UF">
												<label for="field-state">UF</label>
											</div>
										</div>

									</div>

									<div id="credit-card-fields">

										<div class="row">

											<div class="col-md-12 mb-2">
												<div class="form-floating">
													<input type="text" class="form-control" name="card-number" id="field-card-number" placeholder="XXXX XXXX XXXX XXXX">
													<label for="field-card-number">Número do cartão</label>
												</div>
											</div>

											<div class="col-md-12 mb-2">
												<div class="form-floating">
													<input type="text" class="form-control" name="card-name" id="field-card-name" placeholder="Marcelo h Almeida">
													<label for="field-card-name">Titular do cartão</label>
												</div>
											</div>

											<div class="col-md-8 mb-2">
												<div class="form-floating">
													<input type="text" class="form-control" name="card-expiry" id="field-card-expiration" placeholder="MM/AA">
													<label for="field-card-expiration">Validade</label>
												</div>
											</div>

											<div class="col-md-4 mb-2">
												<div class="form-floating">
													<input type="text" class="form-control" name="card-ccv" id="field-card-cvc" placeholder="CVC" autocomplete="off">
													<label for="field-card-ccv">CVC</label>
												</div>
											</div>

										</div>

									</div>

								</div>

								<div class="row">
									<div class="col-12">
										<label class="form-check">
											<input class="form-check-input" type="checkbox" value="1" id="private" name="private">
											<span class="form-check-label">Fazer doação anonimamente</span>
										</label>
										<label class="form-check">
											<input class="form-check-input" type="checkbox" value="1" id="newsletter" name="newsletter">
											<span class="form-check-label">Quero receber divulgações e comunicações por e-mail</span>
										</label>
									</div>
									<div class="col-12" id="div-add-on-fee" style="display: none">
										<label class="form-check">
											<input class="form-check-input" type="checkbox" value="1" id="field-add-on-fee"
												onclick="if(lastDonationButtonClicked != null){$(lastDonationButtonClicked).trigger('click').trigger('blur');}">
											<span class="form-check-label">Adicione + R$ XX para cobrir as tarifas bancárias</span>
										</label>
									</div>
								</div>

								<?php if (isset($hcaptcha)): ?>
									<div class="h-captcha" data-sitekey="<?php echo $hcaptcha['public_key']; ?>"></div>
								<?php elseif (isset($turnstile)): ?>
									<div class="cf-turnstile" data-sitekey="<?php echo $turnstile['public_key']; ?>"></div>
								<?php endif; ?>

								<input type="hidden" name="value" id="value">

								<div class="col-md-12">
									<div class="d-grid gap-2">
										<button type="submit" class="btn btn-dark button-confirm-payment">
											Pagar
										</button>
										<div class="progress progress-subscription d-none" role="progressbar" style="height: 50px"
											aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
											<div class="progress-bar progress-bar-striped progress-bar-animated progress-active text-dark fw-bold" style="width: 100%">
												Processando requisição...
											</div>
										</div>
									</div>
								</div>

							</div>

						</div>

					</div>

				</form>

			</div>

			<div class="col-md-6 offset-md-1 info-site">
				<?php if ($doacoes): ?>
				<?php
					// Nome da tabela para a busca
					$tabela = 'tb_transacoes';
					// Preparando as consultas SQL
					$stmt_geral = $conn->prepare("SELECT COUNT(*) AS doadores_geral, SUM(value) AS valor_geral FROM $tabela WHERE status = 'CONFIRMED' OR status = 'RECEIVED'");
					$stmt_recorrencia = $conn->prepare("SELECT COUNT(*) AS doadores_recorrencia, SUM(value) AS valor_recorrencia FROM $tabela WHERE status = 'CONFIRMED' OR status = 'RECEIVED' AND description = 'Plano de assinatura' AND subscription_id LIKE 'sub_%'");
					// Executando as consultas SQL
					$stmt_geral->execute();
					$stmt_recorrencia->execute();
					// Obtendo os resultados das consultas
					$geral = $stmt_geral->fetch();
					$recorrencia = $stmt_recorrencia->fetch();

					$doadores_geral = $geral['doadores_geral'];
					$valor_geral = $geral['valor_geral'];
					$doadores_recorrencia = $recorrencia['doadores_recorrencia'];
					$valor_recorrencia = $recorrencia['valor_recorrencia'];
				?>
				<div class="row mb-5">
					<div class="col">
						<div class="card">
							<div class="card-body">
								<div class="card-text mb-2"><span class="h3">R$ <?php echo number_format((int)$valor_geral, 2, ',', '.'); ?></span> <span class="text-muted">é o que arrecadamos até agora</span></div>
								<!--<div class="progress mb-2" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
									<div class="progress-bar" style="width: 15%"></div>
								</div>-->
								<!--<div class="card-text mb-2 text-muted">Meta: R$ 30.000,00 por mês (71.2% alcançada)</div>-->
								<div class="card-text mb-2"><span class="h3">R$ <?php echo number_format((int)$valor_recorrencia, 2, ',', '.'); ?></span> <span class="text-muted">em doações recorrentes</span></div>
								<!--<div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
									<div class="progress-bar" style="width: 15%"></div>
								</div>-->
								<div class="card-text mb-2"><i class="bi bi-people"></i> <span class="h3"><?php echo $doadores_geral; ?></span> <span class="text-muted">pessoas apoiando</span></div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php
					// Nome da tabela para a busca
					$tabela = 'tb_imagens';
					
					// Preparando a consulta SQL
					$stmt = $conn->prepare("SELECT * FROM $tabela ORDER BY id DESC");
					
					// Executando a consulta
					$stmt->execute();
					
					// Obtendo os resultados da busca
					$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

					// Consulta SQL para recuperar informações das tabelas
					$sql = "SELECT COUNT(id) FROM $tabela";
					$stmt = $conn->query($sql);
					
					// Obter o número de linhas
					$numLinhas = $stmt->fetchColumn();
					
					// Consulta SQL para selecionar todas as colunas
					$sql = "SELECT * FROM $tabela";
					
					// Preparar e executar a consulta
					$stmt = $conn->prepare($sql);
					$stmt->execute();
					
					// Recuperar os resultados
					$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					// Loop através dos resultados e exibir todas as colunas
					foreach ($resultados as $usuario) {
						echo '
							<div class="row mb-3">
								<div class="col-md-12 text-center mt-3">
									<img src="'. INCLUDE_PATH .'assets/img/' . $usuario['imagem'] . '" alt="Card ' . $usuario['id'] . '" style="width: 500px" />
								</div>
							</div>
						';
					}
				?>
				<p class="col-md-12 text-block" id="text-block-content">
					<?php echo $descricao; ?>
					
					<?php if ($pix_exibir): ?>
						<div>
							<h3 class="text-center">Envie uma Doação diretamente para o pix do <?php echo $nome; ?></h3>
							<img src="<?php echo $pix_imagem_base64; ?>" alt="QR Code do Pix" style="width: 100%; padding: 1rem 5rem;">
							<div class="input-group px-3">
								<input type="text" class="form-control"
									id="codigo-pix" value="<?php echo $pix_codigo; ?>" readonly>
								<div class="input-group-append">
									<a href="javascript:copyPixCodeToClipboard('#codigo-pix')" id="pix-copy-codigo-btn" class="btn btn-primary" style="width: 90.05px; border-top-left-radius: 0; border-bottom-left-radius: 0;">
										Copiar
									</a>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</p>
			</div>
		</div>
	</div>
</div>

<style>
	.social-net a {color:#000}
	.bi {font-size:32px}
</style>

<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/bootstrap/1.10.5/font/bootstrap-icons.css">



<script src="<?php echo INCLUDE_PATH; ?>assets/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo INCLUDE_PATH; ?>assets/js/main.js" defer></script>

<script>
$(document).ready(function () {
	//$('.option-default-monthly').trigger('click');
	$('#field-zipcode').mask('00000-000');
	$('#field-cpf').mask('000.000.000-00');
	$('#field-card-number').mask('0000 0000 0000 0000');
	$('#field-card-expiration').mask('00/00');
	$('#field-card-cvc').mask('0000');

	$('#field-other-monthly').mask("R$ 0#");
	$('#field-other-yearly').mask("R$ 0#");
	$('#field-other-once').mask("R$ 0#");

	config = <?php echo json_encode($jsonData, JSON_PRETTY_PRINT); ?>;

	minOnceDonationCreditCard = config.minOnceDonation.creditCard;
	minOnceDonationBankSlip = config.minOnceDonation.bankSlip;
	minOnceDonationPix = config.minOnceDonation.pix;

	$("#text-block1-title").html(config.textBlock1.title);
	$("#text-block1-content").html(config.textBlock1.content);
	$("#text-block2-title").html(config.textBlock2.title);
	$("#text-block2-content").html(config.textBlock2.content);

	let htmlFooter = "";
	for (let i = 0; i < config.footerLinks.length; i++) {
		htmlFooter += "<a href='" + config.footerLinks[i].link + "' target='" + config.footerLinks[i].target + "' rel='noopener noreferrer'>" + config.footerLinks[i].name + "</a>" + (i + 1 < config.footerLinks.length ? " | " : "");
	}
	$("#footer-links").html(htmlFooter);


	$("#button-monthly1")
		.attr("onclick", "donationOption(this,'monthly'," + config.donationMonthlyButton1.amount + "," + config.donationMonthlyButton1.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationMonthlyButton1.amount)
		.text(config.donationMonthlyButton1.display);
	$("#button-monthly2")
		.attr("onclick", "donationOption(this,'monthly'," + config.donationMonthlyButton2.amount + "," + config.donationMonthlyButton2.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationMonthlyButton2.amount)
		.text(config.donationMonthlyButton2.display);
	$("#button-monthly3")
		.attr("onclick", "donationOption(this,'monthly'," + config.donationMonthlyButton3.amount + "," + config.donationMonthlyButton3.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationMonthlyButton3.amount)
		.text(config.donationMonthlyButton3.display);
	$("#button-monthly4")
		.attr("onclick", "donationOption(this,'monthly'," + config.donationMonthlyButton4.amount + "," + config.donationMonthlyButton4.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationMonthlyButton4.amount)
		.text(config.donationMonthlyButton4.display);
	$("#button-monthly5")
		.attr("onclick", "donationOption(this,'monthly'," + config.donationMonthlyButton5.amount + "," + config.donationMonthlyButton5.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationMonthlyButton5.amount)
		.text(config.donationMonthlyButton5.display);

	$("#button-yearly1")
		.attr("onclick", "donationOption(this,'yearly'," + config.donationYearlyButton1.amount + "," + config.donationYearlyButton1.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationYearlyButton1.amount)
		.text(config.donationYearlyButton1.display);
	$("#button-yearly2")
		.attr("onclick", "donationOption(this,'yearly'," + config.donationYearlyButton2.amount + "," + config.donationYearlyButton2.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationYearlyButton2.amount)
		.text(config.donationYearlyButton2.display);
	$("#button-yearly3")
		.attr("onclick", "donationOption(this,'yearly'," + config.donationYearlyButton3.amount + "," + config.donationYearlyButton3.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationYearlyButton3.amount)
		.text(config.donationYearlyButton3.display);
	$("#button-yearly4")
		.attr("onclick", "donationOption(this,'yearly'," + config.donationYearlyButton4.amount + "," + config.donationYearlyButton4.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationYearlyButton4.amount)
		.text(config.donationYearlyButton4.display);
	$("#button-yearly5")
		.attr("onclick", "donationOption(this,'yearly'," + config.donationYearlyButton5.amount + "," + config.donationYearlyButton5.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationYearlyButton5.amount)
		.text(config.donationYearlyButton5.display);

	$("#button-once1")
		.attr("onclick", "donationOption(this,'once'," + config.donationOnceButton1.amount + "," + config.donationOnceButton1.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationOnceButton1.amount)
		.text(config.donationOnceButton1.display);
	$("#button-once2")
		.attr("onclick", "donationOption(this,'once'," + config.donationOnceButton2.amount + "," + config.donationOnceButton2.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationOnceButton2.amount)
		.text(config.donationOnceButton2.display);
	$("#button-once3")
		.attr("onclick", "donationOption(this,'once'," + config.donationOnceButton3.amount + "," + config.donationOnceButton3.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationOnceButton3.amount)
		.text(config.donationOnceButton3.display);
	$("#button-once4")
		.attr("onclick", "donationOption(this,'once'," + config.donationOnceButton4.amount + "," + config.donationOnceButton4.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationOnceButton4.amount)
		.text(config.donationOnceButton4.display);
	$("#button-once5")
		.attr("onclick", "donationOption(this,'once'," + config.donationOnceButton5.amount + "," + config.donationOnceButton5.showAddOnFee + ")")
		.attr("data-amount-for-selection", config.donationOnceButton5.amount)
		.text(config.donationOnceButton5.display);

	$('.option-default-monthly').trigger('click');
});
</script>

<script>
	// Aguarde o carregamento do documento e, em seguida, chame a função
	$(document).ready(function () {
		donationOption('#button-monthly2', 'monthly', <?php echo $monthly_2; ?>, true);
	});
</script>

<script>
	// Função para copiar o código do Boleto para a área de transferência
	function copyPixCodeToClipboard(element) {
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val($(element).text()).select();
		document.execCommand("copy");
		$temp.remove();

		// Alterar texto do botão para "Copiado!" e depois voltar para o texto original
		var originalText = $('#pix-copy-codigo-btn').text();
		$('#pix-copy-codigo-btn').text('Copiado!');

		setTimeout(function() {
			$('#pix-copy-codigo-btn').text(originalText);
		}, 2000);  // O texto volta ao normal após 2 segundos
	}
</script>

<script>

	// Captura do evento de submit do formulário
	$('#form-checkout').submit(function(event) {
		event.preventDefault();
		
		//Botão carregando
		$(".progress-subscription").addClass('d-flex').removeClass('d-none');
		$(".button-confirm-payment").addClass('d-none').removeClass('d-block');

		// // Bloquear o submit do formulário
		// $(this).find('button[type="submit"]').prop('disabled', true);

		// if(!validateFields()) {
		//     // Desbloquear o submit do formulário se a validação falhar
		//     $(this).find('button[type="submit"]').prop('disabled', false);
		//     return;
		// }

		var dataForm = this;

		// Chama a função processForm sem passar o token do reCAPTCHA
		processForm(dataForm);
	});

	function processForm(dataForm) {
		var typePayment = $('input[name="payment"]:checked').val();
		localStorage.setItem("method", typePayment);
		method = localStorage.getItem("method");

		// Adicionar valor ao input valor
		document.getElementById('value').value = donationAmount;

		// Criação do objeto de dados para a requisição AJAX
		var ajaxData = {
			method: method,
			params: btoa($(dataForm).serialize())
		};

		// Requisição AJAX para o arquivo de criação do cliente
		$.ajax({
			url: '<?php echo INCLUDE_PATH; ?>back-end/subscription.php',
			method: 'POST',
			data: ajaxData,
			dataType: 'JSON',
			success: function(response) {
				window.respostaGlobal = response.id; // Atribui a resposta à propriedade global do objeto window
				// Outras ações que você queira fazer com a resposta
			}
		})
		.done(function(response) {
			if (response.status == 200) {
				//Remove botão carregando
				$(".progress-subscription").addClass('d-none').removeClass('d-flex');
				$(".button-confirm-payment").addClass('d-block').removeClass('d-none');

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
				$(".button-confirm-payment").addClass('d-block').removeClass('d-none');
			}
		})
	}
</script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Seleciona o elemento <html> (ou qualquer outro elemento de nível superior)
		const root = document.documentElement;
		const background = "<?php echo $background; ?>";
		const textColor = "<?php echo $text_color; ?>";
		const color = "<?php echo $color; ?>";
		const hover = "<?php echo $hover; ?>";
		const progress = "<?php echo $progress; ?>";

		// Altera o valor da variável --background-color
		root.style.setProperty('--background', background);
		root.style.setProperty('--text-color', textColor);

		root.style.setProperty('--primary-color', color);
		root.style.setProperty('--hover-color', hover);
		root.style.setProperty('--progress-color', progress);
	});
</script>
<script>
	$(document).ready(function(){
		const header = $("nav")
		const footer = $("footer")

		if ( self !== top ) {
			header.hide()
			footer.hide()
		}
	})
</script>