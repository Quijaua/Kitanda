<?php
    session_start();
    include('../config.php');

    $asaas_id = $_SESSION['asaas_id'];

    if (!isset($asaas_id)) {
        header("Location: " . INCLUDE_PATH . "login/");
        exit();
    }

    $tabela = 'tb_checkout';
    $sql = "SELECT nome FROM $tabela";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $project_name = $resultado['nome'];
    $_SESSION['project_name'] = $project_name;
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Floema Doar - Ativar conta</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
        <meta name="description" content="Solução para recebimentos de doações">
	<meta name='robots' content='noindex, nofollow' />
        <!-- Disable tap highlight on IE -->
        <meta name="msapplication-tap-highlight" content="no">
        <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>vendors/pixeden-stroke-7-icon-master/pe-icon-7-stroke/dist/pe-icon-7-stroke.css">
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>styles/css/base.css" rel="stylesheet">
        <link href="<?php echo INCLUDE_PATH_ADMIN; ?>styles/css/custom.css" rel="stylesheet">

    </head>
    <body>
        <div class="app-container app-theme-white body-tabs-shadow">
            <div class="app-container">
                <div class="h-100">
                    <div class="h-100 no-gutters row">
                        <div class="d-none d-lg-block col-lg-4">
                            <div class="slider-light">
                                <div class="slick-slider">
                                    <div>
                                    <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-plum-plate" tabindex="-1">
                                            <div class="slide-img-bg" style="background-image: url('<?php echo INCLUDE_PATH_ADMIN; ?>images/donate.jpg');"></div>
                                            <div class="slider-content">
                                                <h3>Doação é amor em dobro: preenche o coração de quem dá e de quem recebe.</h3>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-8">
                            <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                                <div class="app-logo"></div>
                                <h4 class="mb-0">
                                    <span class="d-block">Bem vindo,</span>
                                    <span>Por favor crie uma senha para sua conta.</span>
                                </h4>
                                <br>
                                <p class="text-danger">
                                    <?php
                                        if(isset($_SESSION['msg'])){
                                            echo $_SESSION['msg'];
                                            unset($_SESSION['msg']);
                                            echo "<br>";
                                        }
                                    ?>
                                </p>
                                <p class="text-success">
                                    <?php
                                        if(isset($_SESSION['msgcad'])){
                                            echo $_SESSION['msgcad'];
                                            unset($_SESSION['msgcad']);
                                            echo "<br>";
                                        }
                                    ?>
                                </p>
                                <div class="divider row"></div>
                                <div>
                                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/salvar-senha.php" method="post">
                                        <p id="message"></p>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <div class="position-relative form-group">
                                                    <label for="password" class="">Senha</label>
                                                    <input name="password" id="password"
                                                        placeholder="Sua senha..." type="password" class="form-control" onblur="validatePassword()" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="position-relative form-group">
                                                    <label for="confirmPassword" class="">Confirmar Senha</label>
                                                    <input name="confirmPassword" id="confirmPassword"
                                                        placeholder="Confirme sua senha..." type="password" class="form-control" onblur="validatePassword()" required>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="asaas_id" value="<?php echo $asaas_id; ?>">
                                        <div class="divider row"></div>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-auto">
                                                <a href="<?php echo INCLUDE_PATH; ?>" class="d-block"><?php echo $_SESSION['project_name']; ?></a>
                                            </div>
                                            <div class="ml-auto">
                                                <button class="btn btn-primary btn-lg" name="btnLogin" id="btnAddPassword" disabled>Salvar senha</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- plugin dependencies -->
        <script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>vendors/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>vendors/slick-carousel/slick/slick.min.js"></script>
        <!-- custome.js -->
        <script type="text/javascript" src="<?php echo INCLUDE_PATH_ADMIN; ?>js/carousel-slider.js"></script>

        <script>
            function validatePassword() {
                var password = document.getElementById("password").value;
                var confirmPassword = document.getElementById("confirmPassword").value;
                
                var btnAddPassword = document.getElementById("btnAddPassword");

                if (password.length  < 7) {
                    document.getElementById("message").innerHTML = "A senha deve ter no minimo 8 caracteres";
                    document.getElementById("message").style.color = "red";
                    btnAddPassword.disabled = true;
                } else {
                    if (password !== confirmPassword) {
                        document.getElementById("message").innerHTML = "As senhas não coincidem";
                        document.getElementById("message").style.color = "red";
                        btnAddPassword.disabled = true;
                    } else {
                        document.getElementById("message").innerHTML = "";
                        btnAddPassword.disabled = false;
                    }
                }
            }
        </script>
    </body>
</html>
