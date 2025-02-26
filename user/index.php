<?php
    session_start();
    ob_start();
    include('../config.php');

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['msg'] = "Por favor faça login para acessar essa página!";
        header("Location: " . INCLUDE_PATH . "login/");
        exit();
    }
    
    $url = isset($_GET['url']) ? $_GET['url'] : 'user';

    // Tabela que sera feita a consulta
    $tabela = "tb_clientes";

    // ID que você deseja pesquisar
    $id = $_SESSION['user_id'];

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna à variável, ex.: "nome" = $nome
        $nome = $resultado['nome'];
        $phone = $resultado['phone'];
        $email = $resultado['email'];
        $cpf = $resultado['cpf'];
        $cep = $resultado['cep'];
        $endereco = $resultado['endereco'];
        $numero = $resultado['numero'];
        $complemento = $resultado['complemento'];
        $municipio = $resultado['municipio'];
        $cidade = $resultado['cidade'];
        $uf = $resultado['uf'];
        $asaas_id = $resultado['asaas_id'];
    } else {
        // ID não encontrado ou não existente
        $_SESSION['msg'] = "ID não encontrado.";
        header("Location: " . INCLUDE_PATH . "login/");
        exit;
    }
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Language" content="pt-BR">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Floema Doar - Painel do usuário</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
        <meta name="description" content="Solução para recebimentos de doações">

        <!-- CSS files -->
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/melloware/coloris/dist/coloris.min.css?1738096684" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/css/tabler.min.css?1738096684" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/css/tabler-flags.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/css/tabler-socials.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/css/tabler-payments.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/css/tabler-vendors.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/css/tabler-marketing.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/css/demo.min.css?1738096685" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>dist/libs/dropzone/dist/dropzone.css?1738096684" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>styles/css/custom.css" rel="stylesheet">
        <style>
            @import url('https://rsms.me/inter/inter.css');
        </style>
        <script src="<?php echo INCLUDE_PATH; ?>assets/google/jquery/jquery.min.js"></script>
    </head>
    <body>
        <script src="<?php echo INCLUDE_PATH_ADMIN; ?>dist/js/demo-theme.min.js?1738096685"></script>

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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-danger"></div>
                        <div class="modal-body text-center py-4">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/alert-triangle -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg"><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                            <h3>Erro ao salvar</h3>
                            <div class="text-secondary">
                                <?php
                                    if(isset($_SESSION['msgupdcad'])){
                                        echo $_SESSION['msgupdcad'];
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" class="btn btn-3 w-100" data-bs-dismiss="modal">Cancel</a>
                                    </div>
                                    <div class="col">
                                        <a href="#" class="btn btn-danger btn-4 w-100" data-bs-dismiss="modal">Delete 84 items</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

        <?php if (isset($_SESSION['msg'])): ?>
        <script>
            // Espera o carregamento da página
            document.addEventListener("DOMContentLoaded", function () {
                var successModal = new bootstrap.Modal(document.getElementById('modal-success'));
                successModal.show(); // Abre o modal automaticamente
            });
        </script>
        <?php endif; unset($_SESSION['msg']); ?>

        <?php if (isset($_SESSION['msg'])): ?>
        <script>
            // Espera o carregamento da página
            document.addEventListener("DOMContentLoaded", function () {
                var errorModal = new bootstrap.Modal(document.getElementById('modal-error'));
                errorModal.show(); // Abre o modal automaticamente
            });
        </script>
        <?php endif; unset($_SESSION['msg']); ?>
    </body>
</html>