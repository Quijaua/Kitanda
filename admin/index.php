<?php
    session_start();
    ob_start();
    include('../config.php');

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['msg'] = "Por favor faça login para acessar essa página!";
        header("Location: " . INCLUDE_PATH . "login/");
        exit();
    }

    $url = isset($_GET['url']) ? $_GET['url'] : 'editar-perfil';

    // Tabela que sera feita a consulta
// Tabela que sera feita a consulta
$tabela = "tb_checkout";
$tabela_2 = "tb_integracoes";
$tabela_3 = "tb_mensagens";
$tabela_5 = "tb_clientes";
$tabela_6 = "tb_transacoes";
$tabela_7 = "tb_webhook";
$tabela_8 = "tb_pedidos";

// ID que você deseja pesquisar
$id = 1;
$user_id = $_SESSION['user_id'];

// Consulta SQL
$sql = "SELECT tk.*, tc.nome as nome_logado FROM $tabela AS tk JOIN tb_clientes as tc ON tc.id = $user_id WHERE tk.id = :id";
$sql_2 = "SELECT fb_pixel, gtm, g_analytics FROM $tabela_2 WHERE id = :id";
$sql_3 = "SELECT privacy_policy, use_privacy FROM $tabela_3 WHERE id = :id";
$sql_5 = "SELECT * FROM $tabela_5 WHERE roles != 1 AND id = :id";
date_default_timezone_set('America/Sao_Paulo');
$now = date("Y-m-d");
$start_date = new DateTime($now);
$st_date_str = date_format($start_date, "Y-m-d");
$end_date = date_sub($start_date, date_interval_create_from_date_string("90 days"));
$ed_date_str = date_format($end_date, "Y-m-d");
$sql_6 = "SELECT * FROM $tabela_6 as t6 JOIN $tabela_5 as t5 ON t6.customer_id = t5.asaas_id WHERE payment_date_created > $ed_date_str AND roles != 1";
$sql_7 = "SELECT * FROM $tabela_7 LIMIT 1";
//$sql_8 = "SELECT * FROM $tabela_8 ORDER BY id DESC";
//$sql_8 = "SELECT p.*, u.nome as nome_usuario FROM $tabela_8 p LEFT JOIN tb_clientes u ON p.user_id = u.id ORDER BY p.data_pedido DESC";
$sql_8 = "SELECT p.*, c.nome as nome_cliente, c.email as email_cliente
          FROM $tabela_8 p
          LEFT JOIN tb_clientes c ON p.usuario_id = c.id 
          ORDER BY p.id DESC";

// Preparar a consulta
$stmt = $conn->prepare($sql);
$stmt_2 = $conn->prepare($sql_2);
$stmt_3 = $conn->prepare($sql_3);
$stmt_5 = $conn->prepare($sql_5);
$stmt_6 = $conn->prepare($sql_6);
$stmt_7 = $conn->prepare($sql_7);
$stmt_8 = $conn->prepare($sql_8);

// Vincular o valor do parâmetro
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt_2->bindParam(':id', $id, PDO::PARAM_INT);
$stmt_3->bindParam(':id', $id, PDO::PARAM_INT);
$stmt_5->bindParam(':id', $user_id, PDO::PARAM_INT);

// Executar a consulta
$stmt->execute();
$stmt_2->execute();
$stmt_3->execute();
$stmt_5->execute();
$stmt_6->execute();
$stmt_7->execute();
$stmt_8->execute();

// Obter o resultado como um array associativo
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$resultado_2 = $stmt_2->fetch(PDO::FETCH_ASSOC);
$resultado_3 = $stmt_3->fetch(PDO::FETCH_ASSOC);
$resultado_5 = $stmt_5->fetchAll(PDO::FETCH_ASSOC);
$resultado_6 = $stmt_6->fetchAll(PDO::FETCH_ASSOC);
$resultado_7 = $stmt_7->fetch(PDO::FETCH_ASSOC);
$resultado_8 = $stmt_8->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna à variável, ex.: "nome" = $nome
        $nome = $resultado['nome'];
        $nome_logado = $resultado['nome_logado'];
        $permissao = getNomePermissao($_SESSION['user_id'], $conn);
        $logo = $resultado['logo'];
        $title = $resultado['title'];
        $descricao = $resultado['descricao'];
        $vitrine_limite = $resultado['vitrine_limite'];
        $privacidade = $resultado['privacidade'];
        $faq = $resultado['faq'];
        $use_faq = $resultado['use_faq'];
        $facebook = $resultado['facebook'];
        $instagram = $resultado['instagram'];
        $whatsapp = $resultado['whatsapp'];
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
        $cpfCnpj = $resultado['cpfCnpj'];
        $nav_color = $resultado['nav_color'];
        $nav_background = $resultado['nav_background'];
        $background = $resultado['background'];
        $color = $resultado['color'];
        $hover = $resultado['hover'];
        $text_color = $resultado['text_color'];
        $load_btn = $resultado['load_btn'];
        $current_theme = $resultado['theme'];
        $ankara_hero = $resultado['ankara_hero'];
        $ankara_colorful = $resultado['ankara_colorful'];
        $ankara_yellow = $resultado['ankara_yellow'];
        $ankara_footer_top = $resultado['ankara_footer_top'];
        $ankara_footer_blog = $resultado['ankara_footer_blog'];
        $td_hero = $resultado['td_hero'];
        $td_entrepreneurs = $resultado['td_entrepreneurs'];
        $td_news = $resultado['td_news'];
        $td_footer_info = $resultado['td_footer_info'];
        $td_footer_socials = $resultado['td_footer_socials'];
    } else {
        // ID não encontrado ou não existente
        $_SESSION['msg'] = "ID não encontrado.";
        header("Location: " . INCLUDE_PATH . "login/");
        exit;
    }

    // Verificar se o resultado_2 foi encontrado
    if ($resultado_2) {
        // Atribuir o valor da coluna à variável, ex.: "nome" = $nome
        $fb_pixel = $resultado_2['fb_pixel'];
        $gtm = $resultado_2['gtm'];
        $g_analytics = $resultado_2['g_analytics'];
    } else {
        // ID não encontrado ou não existente
        $_SESSION['msg'] = "ID não encontrado.";
        header("Location: " . INCLUDE_PATH . "login/");
        exit;
    }

    // Verificar se o resultado_3 foi encontrado
    if ($resultado_3) {
        // Atribuir o valor da coluna à variável, ex.: "nome" = $nome
        $privacy_policy = $resultado_3['privacy_policy'];
        $use_privacy = $resultado_3['use_privacy'];
    } else {
        // ID não encontrado ou não existente
        $_SESSION['msg'] = "ID não encontrado.";
        header("Location: " . INCLUDE_PATH . "login/");
        exit;
    }

    // Consulta para buscar o produto selecionado
    $stmt = $conn->prepare("
        SELECT * 
        FROM tb_lojas
        WHERE vendedora_id = ? 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $usuario = [
            'imagem' => null
        ];
    }

    $usuario['imagem'] = !empty($usuario['imagem'])
                         ? str_replace(' ', '%20', INCLUDE_PATH . "files/lojas/{$usuario['id']}/perfil/{$usuario['imagem']}")
                         : INCLUDE_PATH . "assets/preview-image/profile.jpg";

    $clientes = $resultado_5;
    $transacoes = $resultado_6;
    $webhook = $resultado_7;
    $pedidos = $resultado_8;

    $hcaptcha_public = $_ENV['HCAPTCHA_CHAVE_DE_SITE'];
    $hcaptcha_secret = $_ENV['HCAPTCHA_CHAVE_SECRETA'];
    $turnstile_public = $_ENV['TURNSTILE_CHAVE_DE_SITE'];
    $turnstile_secret = $_ENV['TURNSTILE_CHAVE_SECRETA'];
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Language" content="pt-BR">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?= $project['title'] ?: $project['name']; ?></title>
	<meta name=viewport content="width=device-width, initial-scale=1">

        <!-- Descrição -->
        <meta name="description" content="<?= htmlspecialchars(mb_substr($project['descricao'], 0, 160)); ?>">
        <meta property="og:description" content="<?= htmlspecialchars($project['descricao']); ?>" />

        <link rel="icon" href="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" sizes="32x32" />
        <link rel="apple-touch-icon" href="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" />
        <meta name="msapplication-TileImage" content="<?php echo INCLUDE_PATH; ?>assets/img/favicon.png" />
	    <meta name='robots' content='noindex, nofollow' />

        <!-- Disable tap highlight on IE -->
        <meta name="msapplication-tap-highlight" content="no">

        <!-- CSS files -->
        <link href="<?php echo INCLUDE_PATH; ?>dist/libs/melloware/coloris/dist/coloris.min.css?1738096684" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler.min.css?1738096684" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-flags.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-socials.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-payments.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-vendors.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-marketing.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/kitanda.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/libs/dropzone/dist/dropzone.css?1738096684" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/libs/tagify/dist/tagify.css" rel="stylesheet" />
        <link href="<?php echo INCLUDE_PATH; ?>assets/css/custom.css" rel="stylesheet">
        <script src="<?php echo INCLUDE_PATH; ?>assets/google/jquery/jquery.min.js"></script>
    </head>
    <body>
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/kitanda-theme.min.js?1738096685"></script>

        <?php if ($url == '404'): ?>

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

        <?php else: ?>

        <div class="page">

            <!-- Modal Sucesso -->
            <div class="modal modal-blur fade" id="modal-success" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar modal"></button>
                        <div class="modal-status bg-success"></div>
                        <div class="modal-body text-center py-4">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/circle-check -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-green icon-lg"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                            <h3>Salvo com sucesso!</h3>
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

            <div class="modal modal-blur fade" id="modal-error" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar modal"></button>
                        <div class="modal-status bg-danger"></div>
                        <div class="modal-body text-center py-4">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/alert-triangle -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg"><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                            <h3>Erro</h3>
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

            <?php include_once('./template-parts/sidebar.php'); ?>

            <?php include_once('./template-parts/header.php'); ?>

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

                <?php include_once('./template-parts/footer.php'); ?>

            </div>
        </div>

        <?php endif; ?>

        <!-- Libs JS -->
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/apexcharts/dist/apexcharts.min.js?1738096685" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/jsvectormap/dist/jsvectormap.min.js?1738096685" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/jsvectormap/dist/maps/world.js?1738096685" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/jsvectormap/dist/maps/world-merc.js?1738096685" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/dropzone/dist/dropzone-min.js?1738096684" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/fslightbox/index.js?1738096684" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/nouislider/dist/nouislider.min.js?1738096684" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/tom-select/dist/js/tom-select.base.min.js?1738096684" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/libs/melloware/coloris/dist/umd/coloris.min.js?1738096684" defer></script>

        <!-- Tabler Core -->
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler.min.js?1738096685" defer></script>
         <script src="<?php echo INCLUDE_PATH; ?>dist/js/kitanda.min.js?1738096685" defer></script>

		<link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-a11y.min.css" rel="stylesheet"/>
		<script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler-a11y.min.js" defer></script>
		<script>
		window.addEventListener('DOMContentLoaded', () => {
			new TablerA11y({
				position: 'bottom-right' // Opções: bottom-right, bottom-left, top-right, top-left
			});
		});
		</script>

        <?php if (isset($_SESSION['msg'])): ?>
        <script>
            // Espera o carregamento da página
            document.addEventListener("DOMContentLoaded", function () {
                var successModal = new bootstrap.Modal(document.getElementById('modal-success'));
                successModal.show(); // Abre o modal automaticamente
            });
        </script>
        <?php endif; unset($_SESSION['msg']); ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
        <script>
            // Espera o carregamento da página
            document.addEventListener("DOMContentLoaded", function () {
                var errorModal = new bootstrap.Modal(document.getElementById('modal-error'));
                errorModal.show(); // Abre o modal automaticamente
            });
        </script>
        <?php endif; unset($_SESSION['error_msg']); ?>
    </body>
</html>
