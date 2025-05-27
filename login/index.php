<?php
    // Carrega as variáveis de ambiente do arquivo .env
    require dirname(__DIR__).'/vendor/autoload.php';
    require_once dirname(__DIR__).'/back-end/functions.php';

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
	$dotenv->load();

    include('../config.php');

    $query = "SELECT captcha_type AS type FROM tb_page_captchas WHERE page_name = :page_name";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':page_name', 'login');
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
        <title>Kitanda - Login</title>

        <!-- CSS files -->
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-flags.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-socials.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-payments.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-vendors.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-marketing.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/demo.min.css?1738096682" rel="stylesheet"/>
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
<!--                        <img src="<?= INCLUDE_PATH_ADMIN; ?>images/logo-inverse.png" alt="Logo <?php echo $project['name']; ?>" class="navbar-brand-image" style="width: 149px; height: 21px;"> -->
                    </a>
                </div>
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">Entrar na sua conta</h2>
                        <p class="text-danger mb-3">
                            <?php
                                if(isset($_SESSION['msg'])){
                                    echo $_SESSION['msg'];
                                    unset($_SESSION['msg']);
                                    echo "<br>";
                                }
                            ?>
                        </p>
                        <p class="text-success mb-3">
                            <?php
                                if(isset($_SESSION['msgcad'])){
                                    echo $_SESSION['msgcad'];
                                    unset($_SESSION['msgcad']);
                                    echo "<br>";
                                }
                            ?>
                        </p>
                        <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/login.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Endereço de e-mail</label>
                                <input name="email" id="email" type="email" class="form-control" placeholder="seu@email.com" required>
                            </div>
                            <div class="mb-2">
                                <label for="password" class="form-label">
                                    Senha
                                    <span class="form-label-description">
                                        <a href="<?php echo INCLUDE_PATH; ?>login/recuperar-senha.php">Esqueci a senha</a>
                                    </span>
                                </label>
                                <div class="input-group input-group-flat">
                                    <input name="password" id="password" type="password" class="form-control" placeholder="Sua senha" required>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" title="Mostrar senha" data-bs-toggle="tooltip" onclick="togglePassword('password', this); return false;">
                                            <i class="ti ti-eye icon icon-1"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input"/>
                                    <span class="form-check-label">Lembrar-me neste dispositivo</span>
                                </label>
                            </div>
                            <?php if (isset($hcaptcha)): ?>
                                <div class="h-captcha" data-sitekey="<?php echo $hcaptcha['public_key']; ?>"></div>
                            <?php elseif (isset($turnstile)): ?>
                                <div class="cf-turnstile" data-sitekey="<?php echo $turnstile['public_key']; ?>"></div>
                            <?php endif; ?>
                            <div class="form-footer mt-4">
                                <button type="submit" class="btn btn-primary w-100">Entrar</button>
                                <a href="<?php echo INCLUDE_PATH; ?>" class="btn btn-link w-100">← Ir para loja <?php echo $_SESSION['project_name']; ?></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabler Core -->
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler.min.js?1738096682" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/demo.min.js?1738096682" defer></script>

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

    </body>
</html>
