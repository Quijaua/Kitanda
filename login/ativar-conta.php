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
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title><?= $project['title'] ?: $project['name']; ?></title>

        <!-- Descrição -->
        <meta name="description" content="<?= htmlspecialchars(mb_substr($project['descricao'], 0, 160)); ?>">
        <meta property="og:description" content="<?= htmlspecialchars($project['descricao']); ?>" />

        <!-- CSS files -->
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-flags.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-socials.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-payments.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-vendors.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-marketing.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/kitanda.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/libs/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet"/>

        <?php if (isset($hcaptcha)): ?>
            <!-- hCaptcha -->
            <script src="https://hcaptcha.com/1/api.js" async defer></script>
        <?php elseif (isset($turnstile)): ?>
            <!-- Turnstile -->
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <?php endif; ?>
    </head>
    <body class=" d-flex flex-column">
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/kitanda-theme.min.js?1738096682"></script>

        <div class="page">

            <div class="container container-tight py-4">
                <div class="text-center mb-4">
                    <a href="<?php echo INCLUDE_PATH_ADMIN; ?>" class="navbar-brand navbar-brand-autodark">
			<h1>Kitanda</h1>
<!--                        <img src="<?= INCLUDE_PATH_ADMIN; ?>images/logo-inverse.png" alt="<?php echo $project['name']; ?>" class="navbar-brand-image" style="width: 149px; height: 21px;"> -->
                    </a>
                </div>
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-0">Bem vindo,</h2>
                        <p class="text-center text-secondary mb-4">Por favor crie uma senha para sua conta.</p>
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
                        <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/salvar-senha.php" method="post">
                            <p id="message"></p>
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group input-group-flat">
                                    <input name="password" id="password" type="password" class="form-control" placeholder="Sua nova senha" onblur="validatePassword()" required>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" title="Mostrar senha" data-bs-toggle="tooltip" onclick="togglePassword('password', this); return false;">
                                            <i class="ti ti-eye icon icon-1"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="confirmPassword" class="form-label">Confirmar Senha</label>
                                <div class="input-group input-group-flat">
                                    <input name="confirmPassword" id="confirmPassword" type="password" class="form-control" placeholder="Confirme sua senha" onblur="validatePassword()" required>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" title="Mostrar senha" data-bs-toggle="tooltip" onclick="togglePassword('confirmPassword', this); return false;">
                                            <i class="ti ti-eye icon icon-1"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" name="token" value="<?php echo $token; ?>">

                            <?php if (isset($hcaptcha)): ?>
                                <div class="h-captcha" data-sitekey="<?php echo $hcaptcha['public_key']; ?>"></div>
                            <?php elseif (isset($turnstile)): ?>
                                <div class="cf-turnstile" data-sitekey="<?php echo $turnstile['public_key']; ?>"></div>
                            <?php endif; ?>

                            <div class="form-footer mt-4">
                                <button type="submit" class="btn btn-primary w-100" id="btnAddPassword" name="btnLogin" disabled>Salvar senha</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabler Core -->
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler.min.js?1738096682" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/kitanda.min.js?1738096682" defer></script>

        <!-- Exibir/Ocultar Senha -->
        <script>
            function togglePassword(inputId, toggleLink) {
                var input = document.getElementById(inputId);
                var icon = toggleLink.querySelector('i');
                if (input.type === "password") {
                    input.type = "text";
                    toggleLink.title = "Ocultar senha";
                    toggleLink.setAttribute("aria-label", "Ocultar senha");
                    toggleLink.setAttribute("data-bs-original-title", "Ocultar senha");
                    // Altera o ícone para "eye-off"
                    if (icon) {
                        icon.classList.remove("ti-eye");
                        icon.classList.add("ti-eye-off");
                    }
                } else {
                    input.type = "password";
                    toggleLink.title = "Mostrar senha";
                    toggleLink.setAttribute("aria-label", "Mostrar senha");
                    toggleLink.setAttribute("data-bs-original-title", "Mostrar senha");
                    // Altera o ícone para "eye"
                    if (icon) {
                        icon.classList.remove("ti-eye-off");
                        icon.classList.add("ti-eye");
                    }
                }
            }
        </script>

        <!-- Validar Senha -->
        <script>
            function validatePassword() {
                var password = document.getElementById("password").value;
                var confirmPassword = document.getElementById("confirmPassword").value;
                var SendNewPassword = document.getElementById("SendNewPassword");

                if (password.length < 7) {
                    document.getElementById("message").innerHTML = "A senha deve ter no mínimo 8 caracteres";
                    document.getElementById("message").style.color = "red";
                    SendNewPassword.disabled = true;
                } else {
                    if (password !== confirmPassword) {
                        document.getElementById("message").innerHTML = "As senhas não coincidem";
                        document.getElementById("message").style.color = "red";
                        SendNewPassword.disabled = true;
                    } else {
                        document.getElementById("message").innerHTML = "";
                        SendNewPassword.disabled = false;
                    }
                }
            }
        </script>

    </body>
</html>













    
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
