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

    if (!empty($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];

        // Busca no banco o hash do token ainda válido
        $stmt = $conn->prepare("
            SELECT user_id, token_hash
            FROM tb_user_tokens
            WHERE expires_at >= NOW()
        ");
        $stmt->execute();
        $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tokens as $row) {
            if (password_verify($token, $row['token_hash'])) {
                $_SESSION['user_id'] = $row['user_id'];
                if (getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador') {
                    header('Location: ' . INCLUDE_PATH_ADMIN . 'painel');
                } else {
                    header("Location: " . INCLUDE_PATH_USER);
                }
                break;
            }
        }
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

        <style>
            .logo-login {
                width: 220px;
                filter: grayscale(100%) brightness(0%);
            }

            .alert.alert-danger:focus-visible {
                outline: 3px solid #d63939;
                outline-offset: 2px;
            }
            .alert.alert-success:focus-visible {
                outline: 3px solid #2fb344;
                outline-offset: 2px;
            }
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
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/kitanda-theme.min.js?1738096682"></script>

        <div class="page">

            <div class="container container-tight py-4">
                <div class="text-center mb-4">
                    <a href="<?php echo INCLUDE_PATH; ?>" class="navbar-brand navbar-brand-autodark">
			            <img src="<?php echo $project['logo']; ?>" alt="<?php echo $project['name']; ?>" class="logo-login">
                    </a>
                </div>
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">Entrar na sua conta</h2>

                        <?php if (isset($_SESSION['msg'])): ?>
                            <div id="login-error" tabindex="-1" class="alert alert-danger mb-3" role="alert" aria-live="assertive">
                                <?= $_SESSION['msg']; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['msgcad'])): ?>
                            <div id="login-success" tabindex="-1" class="alert alert-success mb-3" role="status" aria-live="polite">
                                <?= $_SESSION['msgcad']; ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/login.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Endereço de e-mail</label>
                                <input name="email" id="email" type="email" placeholder="seu@email.com" 
                                    class="form-control <?php echo isset($_SESSION['msg']) ? 'is-invalid' : ''; ?>"
                                    aria-invalid="<?php echo isset($_SESSION['msg']) ? 'true' : 'false'; ?>"
                                    <?php if (isset($_SESSION['msg'])): ?>aria-describedby="login-error"<?php endif; ?> required>
                            </div>
                            <div class="mb-2">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group input-group-flat">
                                    <input name="password" id="password" type="password" placeholder="Sua senha" 
                                        class="form-control <?php echo isset($_SESSION['msg']) ? 'is-invalid' : ''; ?>"
                                        aria-invalid="<?php echo isset($_SESSION['msg']) ? 'true' : 'false'; ?>"
                                        <?php if (isset($_SESSION['msg'])): ?>aria-describedby="login-error"<?php endif; ?> required>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" title="Mostrar senha" aria-label="Mostrar senha" aria-pressed="false" data-bs-toggle="tooltip" onclick="togglePassword('password', this); return false;">
                                            <i class="ti ti-eye icon icon-1"></i>
                                        </a>
                                    </span>
                                </div>
                                <div class="mt-1 mb-3">
                                    <a href="<?php echo INCLUDE_PATH; ?>login/recuperar-senha.php">Esqueci a senha</a>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-check">
                                    <input type="checkbox" name="remember" id="remember" class="form-check-input" />
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
                    toggleLink.setAttribute(
                        "aria-pressed",
                        input.type === "text" ? "true" : "false"
                    );
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
                    toggleLink.setAttribute(
                        "aria-pressed",
                        input.type === "text" ? "true" : "false"
                    );
                }
            }
        </script>

        <?php if (isset($_SESSION['msg'])): ?>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('login-error').focus();
                });
            </script>
        <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['msgcad'])): ?>
            <?php if (!isset($_SESSION['msg'])): ?>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('login-success').focus();
                });
            </script>
            <?php endif; ?>
        <?php unset($_SESSION['msgcad']); ?>
        <?php endif; ?>

    </body>
</html>
