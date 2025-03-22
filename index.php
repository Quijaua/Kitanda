<?php
	// //Parametros
	// //Atualmente esta chamando $config['asaas_api_url'] e $config['asaas_api_key'] pelo param.php
	// //Esta sendo feita uma consulta no banco de dados e puxando com pdo
	// include('./back-end/parameters.php');

	// Caso prefira o .env apenas descomente o codigo e comente o "include('parameters.php');" acima
	// Carrega as variáveis de ambiente do arquivo .env
	require __DIR__.'/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

    include('./config.php');

    $url = isset($_GET['url']) ? $_GET['url'] : 'produtos';
	$link = '';

	// Verifica se a URL começa com "p/"
	if (strpos($url, 'p/') === 0) {
		$link = substr($url, 2); // Remove "p/" e guarda o restante em $link
		$url = 'produto'; // Define a página como "produto"
	}

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

    session_start();
    ob_start();

	if (isset($_SESSION['user_id'])) {
		$stmt = $conn->prepare("SELECT id, roles FROM tb_clientes WHERE id = ?");
		$stmt->execute([$_SESSION['user_id']]);
		$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
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
<!DOCTYPE html><html lang="pt-BR">

<head>
	<meta charset="utf-8">
	<title><?php echo ($title !== '') ? $title : 'Colabore com o Projeto '.$nome; ?></title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!-- 
	<link href="<?php echo INCLUDE_PATH; ?>assets/google/fonts/open-sans" rel="stylesheet" type="text/css">
	<link href="<?php echo INCLUDE_PATH; ?>assets/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"
		  type="text/css">


	<link href="<?php echo INCLUDE_PATH; ?>assets/google/fonts/newsreader" rel="stylesheet">

<link rel="icon" href="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" sizes="32x32" />
<link rel="apple-touch-icon" href="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" />
<meta name="msapplication-TileImage" content="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" /> -->

	<!-- CSS files -->
	<link href="<?php echo INCLUDE_PATH; ?>dist/libs/melloware/coloris/dist/coloris.min.css?1738096684" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler.min.css?1738096684" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-flags.min.css?1738096685" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-socials.min.css?1738096685" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-payments.min.css?1738096685" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-vendors.min.css?1738096685" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-marketing.min.css?1738096685" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/css/demo.min.css?1738096685" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>dist/libs/dropzone/dist/dropzone.css?1738096684" rel="stylesheet"/>
	<link href="<?php echo INCLUDE_PATH; ?>assets/css/custom.css" rel="stylesheet">
	<style>
		@import url('https://rsms.me/inter/inter.css');
	</style>
	<script src="<?php echo INCLUDE_PATH; ?>assets/google/jquery/jquery.min.js"></script>

<?php if (isset($hcaptcha)): ?>
	<!-- hCaptcha -->
	<script src="https://hcaptcha.com/1/api.js" async defer></script>
<?php elseif (isset($turnstile)): ?>
	<!-- Turnstile -->
	<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>


<link rel="canonical" href="<?php echo INCLUDE_PATH; ?>" />
<meta property="og:locale" content="pt_BR" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $title; ?>"/>
<meta property="og:description" name="description" content="<?php echo mb_strimwidth($descricao, 3, 120, '...'); ?>" />
<meta property="og:url" value="<?php echo INCLUDE_PATH; ?>"/>
<meta property="og:site_name" content="<?php echo $nome; ?>" />
<meta property="article:modified_time" content="2022-12-01T18:38:06+00:00" />
<meta property="og:image" content="<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $logo; ?>"/>
<meta property="og:image" value="<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $logo; ?>"/>
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="@FloemaDoar" />
<meta name="twitter:title" value="<?php echo $title; ?>"/>
<meta name="twitter:url" value="<?php echo INCLUDE_PATH; ?>"/>
<meta name="twitter:image" value="<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $logo; ?>"/>
<meta name="twitter:image" content="<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $logo; ?>"/>
<meta name="twitter:description" value="<?php echo mb_strimwidth($descricao, 3, 120, '...'); ?>"/>

<script type="application/ld+json">{
	"@context": "https://schema.org",
	"@graph": [
		{
			"@type": "WebSite",
			"@id": "<?php echo INCLUDE_PATH; ?>",
			"url": "<?php echo INCLUDE_PATH; ?>",
			"name": "<?php echo $title; ?>",
			"isPartOf": {
				"@id": "<?php echo INCLUDE_PATH; ?>#website"
			},
			"datePublished": "2023-03-02T19:50:30+00:00",
			"dateModified": "2023-03-21T12:51:52+00:00",
			"description": "<?php echo mb_strimwidth($descricao, 3, 120, '...'); ?>",
			"inLanguage": "pt-BR",
			"interactAction": [
				{
					"@type": "SubscribeAction",
					"target": [
						"<?php echo INCLUDE_PATH; ?>"
					]
				}
			]
		},
		{
			"@type": "Organization",
			"@id": "<?php echo INCLUDE_PATH; ?>#organization",
			"name": "<?php echo $nome; ?>",
			"url": "<?php echo INCLUDE_PATH; ?>",
			"logo": {
				"@type": "ImageObject",
				"inLanguage": "pt-BR",
				"@id": "<?php echo INCLUDE_PATH; ?>#/schema/logo/image/",
				"url": "<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $logo; ?>",
				"contentUrl": "<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $logo; ?>",
				"width": 140,
				"height": 64,
				"caption": "<?php echo $nome; ?>"
			},
			"image": {
				"@id": "<?php echo INCLUDE_PATH; ?>#/schema/logo/image/"
			}
		}
	]
}</script>

	<?php echo $fb_pixel; ?>

	<?php echo $gtm; ?>
	
	<?php echo $g_analytics; ?>
</head>
	<body>
		<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/js/demo-theme.min.js?1738096685"></script>

		<div class="page">

			<!-- Navbar -->
			<header class="navbar navbar-expand-md d-print-none" style="background-color: <?php echo $nav_background; ?>; color: <?php echo $nav_color; ?>;">
				<div class="container-xl">
					<div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
						<a href=".">
							<img class="navbar-brand-image" src="assets/img/<?php echo $logo; ?>" alt="Logo da Instituição">
						</a>
					</div>
					<h1 class="text-dark mb-0"><?php echo ($title !== '') ? $title : 'Colabore com o Projeto '.$nome; ?></h1>
				</div>
			</header>


			<div class="page-wrapper">

				<!-- Conteúdo da página -->
				<?php
					//Url Amigavel
					if(file_exists('pages/'.$url.'.php')){
						include('pages/'.$url.'.php');
					}else{
						//a pagina nao existe
						header('Location: '.INCLUDE_PATH_ADMIN.'404');
					}
				?>

			</div>

			<footer>
				<div class="container mt-5">
					<div class="row">
						<div class="col-md-3">
							<span class="h5"><?php echo $nome; ?></span><br />
							<div class="font-weight-light" style="font-size:13px;margin-top:5px">
							<!--<?php echo $rua; ?><?php echo ($numero !== '') ? ', ' . $numero : ''; ?> - <?php echo $bairro; ?>-->
							<?php echo $rua . ', '; ?><?php echo $numero ? $numero :  'S/N'; ?> - <?php echo $bairro; ?>
							<?php echo $cidade; ?> - <?php echo $estado; ?> CEP: <?php echo $cep; ?><br />
							<?php if($telefone): ?> Telefone: <a href="callto:<?php echo $telefone; ?>"> <?php echo $telefone; ?></a><br /> <?php endif; ?>
							E-mail: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
							</div>
							<div class="social-net mt-2 mb-4">
								<a href="<?php echo ($facebook !== '') ? $facebook : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($facebook == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-facebook p-2"></i></a>
								<a href="<?php echo ($instagram !== '') ? $instagram : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($instagram == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-instagram p-2"></i></a>
								<a href="<?php echo ($linkedin !== '') ? $linkedin : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($linkedin == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-linkedin p-2"></i></a>
								<a href="<?php echo ($twitter !== '') ? $twitter : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($twitter == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-twitter p-2"></i></a>
								<a href="<?php echo ($youtube !== '') ? $youtube : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($youtube == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-youtube p-2"></i></a>
								<a href="<?php echo ($website !== '') ? $website : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($website == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-globe-americas p-2"></i></a>
								<a href="<?php echo ($tiktok !== '') ? $tiktok : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($tiktok == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-tiktok p-2"></i></a>
								<a href="<?php echo ($linktree !== '') ? $linktree : '#'; ?>" target="_blank" rel="noopener noreferer" <?php echo ($linktree == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-share p-2"></i></a>
							</div>

						</div>
						<div class="col-md-6 text-center">
							<div class="social-net mt-2 mb-4">
								<img src="<?php echo INCLUDE_PATH; ?>assets/img/security.webp" alt="ambiente seguro" />
								<a class="p-2" href="https://transparencyreport.google.com/safe-browsing/search?url=<?php echo INCLUDE_PATH; ?>" target="_blank" rel="noreferrer">
									<img src="<?php echo INCLUDE_PATH; ?>assets/img/selo-google.png" width="150" height="42" alt="Safe Browsisng">
								</a>
							</div>
							<p class="footer-link ps-1">
								<?php
									if($use_privacy) {
										echo "<a href='politica-de-privacidade' rel='noopener noreferrer' target='_blank'>";
									} else {
										echo "<a href=" . $privacidade . " rel='noopener noreferrer' target='_blank'>";
									}
								?>
									PRIVACIDADE
								</a> | 
								<a href="<?php echo INCLUDE_PATH; ?>login" rel="noopener noreferrer" target="_blank">
									ÁREA DE CLIENTE
								</a><br />
								<?php
									if($use_faq) {
										echo "<a href='$faq' rel='noopener noreferrer' target='_blank'>PERGUNTAS FREQUENTES</a>";
									}
								?>
							</p>
						</div>
						<div class="col-md-3">
						<p class="footer-linkd mt-5 footer-floema-doar font-weight-bold">
								<a href="https://github.com/Quijaua/Kitanda" rel="noopener noreferrer" target="_blank">
									Usamos Kitanda | Open source
								</a>
							</p>
						</div>
					</div>
				</div>
			</footer>

			<style>
				.social-net a {color:#000}
				.bi {font-size:32px}
			</style>









			<!-- Libs JS -->
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/apexcharts/dist/apexcharts.min.js?1738096685" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/jsvectormap/dist/jsvectormap.min.js?1738096685" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/jsvectormap/dist/maps/world.js?1738096685" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/jsvectormap/dist/maps/world-merc.js?1738096685" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/dropzone/dist/dropzone-min.js?1738096684" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/fslightbox/index.js?1738096684" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/tinymce/tinymce.min.js?1738096684" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/nouislider/dist/nouislider.min.js?1738096684" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/litepicker/dist/litepicker.js?1738096684" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/tom-select/dist/js/tom-select.base.min.js?1738096684" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/melloware/coloris/dist/umd/coloris.min.js?1738096684" defer></script>

			<!-- Tabler Core -->
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/js/tabler.min.js?1738096685" defer></script>
			<script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/js/demo.min.js?1738096685" defer></script>
		</div>

		<style>
			.social-net a {color:#000}
			.bi {font-size:32px}
		</style>

		<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/bootstrap/1.10.5/font/bootstrap-icons.css">



		<script src="<?php echo INCLUDE_PATH; ?>assets/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
		<script src="<?php echo INCLUDE_PATH; ?>assets/google/jquery/jquery.min.js"></script>
		<script src="<?php echo INCLUDE_PATH; ?>assets/google/jquery/jquery-ui.js"></script>
		<script src="<?php echo INCLUDE_PATH; ?>assets/ajax/1.14.16/jquery.mask.min.js"></script>
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
	</body>
</html>
