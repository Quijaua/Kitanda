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
	// Determina o campo e valor para filtro (usuário ou cookie)
	if (isset($_SESSION['user_id'])) {
		$field = 'usuario_id';
		$value = $_SESSION['user_id'];
	} elseif (isset($_COOKIE['cart_id'])) {
		$field = 'cookie_id';
		$value = $_COOKIE['cart_id'];
	} else {
		// Sem usuário nem cookie => carrinho vazio
		$cartCount = 0;
	}

	if (isset($_SESSION['user_id']) || isset($_COOKIE['cart_id'])) {
		// Prepara e executa a query que soma todas as quantidades
		$sql = "SELECT COALESCE(SUM(quantidade),0) AS total_items 
				FROM tb_carrinho 
				WHERE {$field} = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$value]);
	
		// Pega o total (0 caso não existam registros)
		$cartCount = (int) $stmt->fetchColumn();
	}
?>
<!DOCTYPE html>
<html lang="pt-BR">
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
		<!--link href="<?php echo INCLUDE_PATH; ?>dist/libs/melloware/coloris/dist/coloris.min.css?1738096684" rel="stylesheet"/ -->
		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler.min.css?1738096684" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-flags.min.css?1738096685" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-socials.min.css?1738096685" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-payments.min.css?1738096685" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-vendors.min.css?1738096685" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-marketing.min.css?1738096685" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>dist/css/demo.min.css?1738096685" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>dist/libs/dropzone/dist/dropzone.css?1738096684" rel="stylesheet"/>
		<link href="<?php echo INCLUDE_PATH; ?>assets/css/custom.css" rel="stylesheet">
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
		<meta name="twitter:site" content="@Kitanda" />
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
		<script src="<?php echo INCLUDE_PATH; ?>dist/js/demo-theme.min.js?1738096685"></script>

		<div class="page">

			<!-- Modal Sucesso -->
			<div class="modal modal-blur fade" id="modal-status-success" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
					<div class="modal-content">
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						<div class="modal-status bg-success"></div>
						<div class="modal-body text-center py-4">
							<!-- Download SVG icon from http://tabler.io/icons/icon/circle-check -->
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-green icon-lg"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
							<h3>Sucesso!</h3>
							<div class="text-secondary">
								<?php
									if(isset($_SESSION['msg'])){
										echo $_SESSION['msg'];
									}
								?>
							</div>
						</div>
						<div class="modal-footer">
							<div class="w-100">
								<div class="row">
									<div class="col">
										<a href="#" class="btn btn-3 w-100" data-bs-dismiss="modal">Fechar</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal modal-blur fade" id="modal-status-error" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
					<div class="modal-content">
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						<div class="modal-status bg-danger"></div>
						<div class="modal-body text-center py-4">
							<!-- Download SVG icon from http://tabler.io/icons/icon/alert-triangle -->
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg"><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
							<h3>Erro!</h3>
							<div class="text-secondary">
								<?php
									if(isset($_SESSION['error_msg'])){
										echo $_SESSION['error_msg'];
									}
								?>
							</div>
						</div>
						<div class="modal-footer">
							<div class="w-100">
								<div class="row">
									<div class="col">
										<a href="#" class="btn btn-3 w-100" data-bs-dismiss="modal">Fechar</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Navbar -->
			<!-- <header class="navbar navbar-expand-md d-print-none" style="background-color: <?php echo $nav_background; ?>; color: <?php echo $nav_color; ?>;">
				<div class="container-xl">
					<div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
						<a href=".">
							<img class="navbar-brand-image" src="assets/img/<?php echo $logo; ?>" alt="Logo da Loja">
						</a>
					</div>
					<h1 class="text-dark mb-0"><?php echo ($title !== '') ? $title : 'Colabore com o Projeto '.$nome; ?></h1>
				</div>
			</header> -->

			<!-- BEGIN NAVBAR -->
			<header class="navbar navbar-expand-md d-print-none">
				<div class="container-xl py-3">
					<!-- BEGIN NAVBAR TOGGLER -->
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<!-- END NAVBAR TOGGLER -->
					<!-- BEGIN NAVBAR LOGO -->
					<div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
						<a href=".">
							<?php if (!empty($logo)): ?>
							<img class="navbar-brand-image" src="assets/img/<?php echo $logo; ?>" alt="Logo da Loja">
							<?php else: ?>
							<h1 class="mb-0">Kitanda</h1>
							<?php endif; ?>
						</a>
					</div>
					<!-- END NAVBAR LOGO -->
					<div class="navbar-nav flex-row order-md-last">
						<div class="nav-item dropdown">
							<a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
								<!-- <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)"></span> -->
								<!-- Download SVG icon from http://tabler.io/icons/icon/user-circle -->
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-user-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
								<div class="d-none d-xl-block ps-2">
									<div> Minha Conta </div>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
								<a href="#" class="dropdown-item">Status</a>
								<a href="./profile.html" class="dropdown-item">Profile</a>
								<a href="#" class="dropdown-item">Feedback</a>
								<div class="dropdown-divider"></div>
								<a href="./settings.html" class="dropdown-item">Settings</a>
								<a href="./sign-in.html" class="dropdown-item">Logout</a>
							</div>
						</div>
						<div class="d-none d-md-flex">
							<div class="nav-item dropdown d-none d-md-flex me-3">
								<a href="<?= INCLUDE_PATH; ?>carrinho" class="nav-link px-0 position-relative">
									<!-- ícone do carrinho -->
									<?php if ($cartCount > 0): ?>
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 me-2 icon-tabler icons-tabler-outline icon-tabler-shopping-cart-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M11.5 17h-5.5v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /><path d="M15 19l2 2l4 -4" /></svg>
									<?php else: ?>
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 me-2 icon-tabler icons-tabler-outline icon-tabler-shopping-cart"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
									<?php endif; ?>

									<span id="cart-count" class="badge bg-red badge-notification text-red-fg position-absolute"
											style="top: 0; right: 0; transform: translate(50%,-50%);<?= ($cartCount <= 0) ? "display: none;" : ""; ?>">
										<?= $cartCount > 9 ? '9+' : $cartCount; ?>
										<span class="visually-hidden">itens no carrinho</span>
									</span>

									Meu Carrinho
								</a>
							</div>
						</div>
					</div>
					<div class="collapse navbar-collapse" id="navbar-menu">
						<div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center justify-content-center">
							<!-- BEGIN NAVBAR MENU -->
							<ul class="navbar-nav">
								<li class="nav-item">
									<a class="nav-link" href="<?= INCLUDE_PATH; ?>">
										<span class="nav-link-title"> PRODUTOS </span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="<?= INCLUDE_PATH; ?>empreendedoras">
										<span class="nav-link-title"> EMPREENDEDORAS </span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="<?= INCLUDE_PATH; ?>">
										<span class="nav-link-title"> SOBRE NÓS </span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="<?= INCLUDE_PATH; ?>">
										<span class="nav-link-title"> BLOG </span>
									</a>
								</li>
							</ul>
							<!-- END NAVBAR MENU -->
						</div>
					</div>
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
						<p class="footer-linkd mt-5 footer-kitanda font-weight-bold">
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
		</div>

		<!-- jQuery -->
		<script src="<?php echo INCLUDE_PATH; ?>assets/google/jquery/jquery.min.js"></script>
		<script src="<?php echo INCLUDE_PATH; ?>assets/google/jquery/jquery-ui.js"></script>
		<script src="<?php echo INCLUDE_PATH; ?>assets/ajax/1.14.16/jquery.mask.min.js"></script>

		<!-- Libs JS -->
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/apexcharts/dist/apexcharts.min.js?1738096685" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/jsvectormap/dist/jsvectormap.min.js?1738096685" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/jsvectormap/dist/maps/world.js?1738096685" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/jsvectormap/dist/maps/world-merc.js?1738096685" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/dropzone/dist/dropzone-min.js?1738096684" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/fslightbox/index.js?1738096684" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/tinymce/tinymce.min.js?1738096684" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/nouislider/dist/nouislider.min.js?1738096684" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/litepicker/dist/litepicker.js?1738096684" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/libs/tom-select/dist/js/tom-select.base.min.js?1738096684" defer></script>
		<!--script src="<?php echo INCLUDE_PATH; ?>dist/libs/melloware/coloris/dist/umd/coloris.min.js?1738096684" defer></script -->

		<!-- Tabler Core -->
		<script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler.min.js?1738096685" defer></script>
		<script src="<?php echo INCLUDE_PATH; ?>dist/js/demo.min.js?1738096685" defer></script>

		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-a11y.min.css" rel="stylesheet"/>
		<script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler-a11y.min.js" defer></script>
		<script>
		window.addEventListener('DOMContentLoaded', () => {
			new TablerA11y({
				position: 'bottom-right' // Opções: bottom-right, bottom-left, top-right, top-left
			});
		});
		</script>

<!-- <style>
.btn.btn-floating.btn-icon.btn-primary.bottom-right {
	height: 32px;
}
</style> -->

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

        <?php if (isset($_SESSION['msg'])): ?>
        <script>
            // Espera o carregamento da página
            document.addEventListener("DOMContentLoaded", function () {
                var successModal = new bootstrap.Modal(document.getElementById('modal-status-success'));
                successModal.show(); // Abre o modal automaticamente
            });
        </script>
        <?php endif; unset($_SESSION['msg']); ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
        <script>
            // Espera o carregamento da página
            document.addEventListener("DOMContentLoaded", function () {
                var errorModal = new bootstrap.Modal(document.getElementById('modal-status-error'));
                errorModal.show(); // Abre o modal automaticamente
            });
        </script>
        <?php endif; unset($_SESSION['error_msg']); ?>
	</body>
</html>