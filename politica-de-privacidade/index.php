<?php
	// //Parametros
	// //Atualmente esta chamando $config['asaas_api_url'] e $config['asaas_api_key'] pelo param.php
	// //Esta sendo feita uma consulta no banco de dados e puxando com pdo
	// include('./back-end/parameters.php');

	// // Acessa as variáveis de ambiente
	// $recaptcha_key = $config['recaptcha_chave_de_site'];

	// Caso prefira o .env apenas descomente o codigo e comente o "include('parameters.php');" acima
	// Carrega as variáveis de ambiente do arquivo .env

	require '../vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable('../');
	$dotenv->load();

	// Acessa as variáveis de ambiente
	$recaptcha_key = $_ENV['RECAPTCHA_CHAVE_DE_SITE'];

    session_start();
    ob_start();
    include('../config.php');

    // Tabela que sera feita a consulta
    $tabela = "tb_checkout";
	$tabela_2 = "tb_integracoes";
	$tabela_3 = "tb_mensagens";

    // ID que você deseja pesquisar
    $id = 1;

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";
	$sql_2 = "SELECT * FROM $tabela_2 WHERE id = :id";
	$sql_3 = "SELECT privacy_policy FROM $tabela_3 WHERE id = :id";

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
        $privacidade = $resultado['privacidade'];
        $faq = $resultado['faq'];
        $facebook = $resultado['facebook'];
        $instagram = $resultado['instagram'];
        $linkedin = $resultado['linkedin'];
        $twitter = $resultado['twitter'];
        $youtube = $resultado['youtube'];
        $website = $resultado['website'];
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
		$privacy_policy = $resultado_3['privacy_policy'];
	} else {
		// ID não encontrado ou não existente
		echo "ID não encontrado.";
	}

?>
<html lang="pt">
<head>
	<meta charset="utf-8">
	<title><?php echo ($title !== '') ? $title : 'Colabore com o Projeto '.$nome; ?></title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link href="<?php echo INCLUDE_PATH; ?>assets/google/fonts/open-sans" rel="stylesheet" type="text/css">
	<link href="<?php echo INCLUDE_PATH; ?>assets/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"
		  type="text/css">
	<link href="<?php echo INCLUDE_PATH; ?>assets/google/fonts/newsreader" rel="stylesheet">

<link rel="icon" href="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" sizes="32x32" />
<link rel="apple-touch-icon" href="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" />
<meta name="msapplication-TileImage" content="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" />



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

	<link rel="stylesheet" type="text/css" href="<?php echo INCLUDE_PATH; ?>assets/css/main.css"/>

	<?php echo $fb_pixel; ?>

	<?php echo $gtm; ?>
	
	<?php echo $g_analytics; ?>
</head>
<body>


<nav class="navbar navbar-expand-md navbar-dark" style="background-color: <?php echo $nav_background; ?>; color: <?php echo $nav_color; ?>;">
	<div class="container">
		<div class="row">
			<div class="col-md-4 p-1 text-center">
				<img src="../assets/img/<?php echo $logo; ?>">
			</div>
			<div class="col-md-8 mt-4 p-md-3">
				<h1 class="h2"><?php echo ($title !== '') ? $title : 'Colabore com o Projeto '.$nome; ?></h1>
			</div>
		</div>
	</div>
</nav>

<main class="container mt-5">
	<div class="row">
		<div class="col-md-5 mb-3">

			<h3 class="highlight mb-3" id="highlight">Política de Privacidade</h3>
			<div id="div-container-form">
				<?php echo $privacy_policy; ?>
			</div>
		</div>
		<div class="col-md-6 ms-auto">
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
							<div class="col-md-10 mt-3">
								<img src="'. INCLUDE_PATH .'assets/img/' . $usuario['imagem'] . '" alt="Card ' . $usuario['id'] . '" style="width: 500px; height: 159px" />
							</div>
						</div>
					';
				}
			?>
			<p class="col-md-10 text-block" id="text-block-content"><?php echo $descricao; ?></div>
		</div>
	</div>
</main>

<!--<footer>
 <div class="container mt-5">
	<div class="row">
		<div class="col-md-3">
			<span class="h5"><?php echo $nome; ?></span><br />
			<div class="font-weight-light" style="font-size:13px;margin-top:5px">
			<?php echo $rua; ?><?php echo ($numero !== '') ? ', ' . $numero : ''; ?> - <?php echo $bairro; ?>
			<?php echo $cidade; ?> - <?php echo $estado; ?>, <?php echo $cep; ?><br />
			Telefone: <a href="callto:<?php echo $telefone; ?>"><?php echo $telefone; ?></a> | E-mail: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><br />
			</div>
		</div>
		<div class="col-md-6">
			<div class="social-net mt-2 mb-4">
				<a href="<?php echo ($facebook !== '') ? $facebook : '#'; ?>" <?php echo ($facebook == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-facebook p-2"></i></a>
				<a href="<?php echo ($instagram !== '') ? $instagram : '#'; ?>" <?php echo ($instagram == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-instagram p-2"></i></a>
				<a href="<?php echo ($linkedin !== '') ? $linkedin : '#'; ?>" <?php echo ($linkedin == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-linkedin p-2"></i></a>
				<a href="<?php echo ($twitter !== '') ? $twitter : '#'; ?>" <?php echo ($twitter == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-twitter p-2"></i></a>
				<a href="<?php echo ($youtube !== '') ? $youtube : '#'; ?>" <?php echo ($youtube == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-youtube p-2"></i></a>
				<a href="<?php echo ($website !== '') ? $website : '#'; ?>" <?php echo ($website == '') ? 'class="d-none"' : ''; ?>><i class="bi bi-globe-americas p-2"></i></a>
			</div>
			<p class="footer-link ps-1">
				<?php
					if($use_privacy) {
						echo "<a href='/politica-de-privacidade' rel='noopener noreferrer' target='_blank'>";
					} else {
						echo "<a href=" . $privacidade . " rel='noopener noreferrer' target='_blank'>";
					}
				?>
					PRIVACIDADE DOS DOADORES
				</a>
				 | 
				<a href="<?php echo $faq; ?>" rel="noopener noreferrer" target="_blank">
					PERGUNTAS FREQUENTES
				</a>
				| 
				<a href="/login" rel="noopener noreferrer" target="_blank">
					LOGIN
				</a>
			</p>
		</div>
		<div class="col-md-3">
		<p class="footer-linkd mt-5 footer-floema-doar font-weight-bold">
				<a href="#" rel="noopener noreferrer" target="_blank">
					Usamos Floema Doar | Open source
				</a>
			</p>
		</div>
	</div>
 </div>
</footer>-->

</body>
</html>
