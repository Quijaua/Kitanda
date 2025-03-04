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
        <title>Floema Doar - Login</title>

        <!-- CSS files -->
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-flags.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-socials.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-payments.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-vendors.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler-marketing.min.css?1738096682" rel="stylesheet"/>
        <link href="<?php echo INCLUDE_PATH; ?>dist/css/demo.min.css?1738096682" rel="stylesheet"/>
        <style>
            @import url('https://rsms.me/inter/inter.css');
        </style>

        <?php if (isset($hcaptcha)): ?>
            <!-- hCaptcha -->
            <script src="https://hcaptcha.com/1/api.js" async defer></script>
        <?php elseif (isset($turnstile)): ?>
            <!-- Turnstile -->
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <?php endif; ?>
    </head>
    <body class=" d-flex flex-column">
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/demo-theme.min.js?1738096682"></script>

        <div class="page">

            <div class="container container-tight py-4">
                <div class="text-center mb-4">
                    <a href="<?php echo INCLUDE_PATH_ADMIN; ?>" class="navbar-brand navbar-brand-autodark">
                        <img src="<?= INCLUDE_PATH_ADMIN; ?>images/logo-inverse.png" alt="Logo <?php echo $project['name']; ?>" class="navbar-brand-image" style="width: 149px; height: 21px;">
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
                                        <a href="#" class="link-secondary" title="Mostrar senha" data-bs-toggle="tooltip">
                                            <!-- Download do ícone SVG de http://tabler.io/icons/icon/eye -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
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
                                <a href="<?php echo INCLUDE_PATH; ?>" class="btn btn-link w-100"><?php echo $_SESSION['project_name']; ?></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabler Core -->
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler.min.js?1738096682" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/demo.min.js?1738096682" defer></script>

    </body>
</html>