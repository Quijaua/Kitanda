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

                        <?php if (isset($_SESSION['msg'])): ?>
                            <div id="password-error" tabindex="-1" class="alert alert-danger mb-3" role="alert" aria-live="assertive">
                                <?= $_SESSION['msg']; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['msgcad'])): ?>
                            <div id="password-success" tabindex="-1" class="alert alert-success mb-3" role="status" aria-live="polite">
                                <?= $_SESSION['msgcad']; ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/salvar-senha.php" method="post" onsubmit="return validatePasswordOnSubmit()">

                            <p id="password-feedback" class="text-danger" role="alert" aria-live="assertive"></p>

                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group input-group-flat">
                                    <input name="password" id="password" type="password" class="form-control" placeholder="Sua nova senha" aria-describedby="password-feedback" aria-invalid="false" oninput="validatePassword()" required>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" title="Mostrar senha" aria-label="Mostrar senha" aria-pressed="false" data-bs-toggle="tooltip" onclick="togglePassword('password', this); return false;">
                                            <i class="ti ti-eye icon icon-1"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="confirmPassword" class="form-label">Confirmar Senha</label>
                                <div class="input-group input-group-flat">
                                    <input name="confirmPassword" id="confirmPassword" type="password" class="form-control" placeholder="Confirme sua senha" aria-describedby="password-feedback" aria-invalid="false" oninput="validatePassword()" required>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" title="Mostrar senha" aria-label="Mostrar senha" aria-pressed="false" data-bs-toggle="tooltip" onclick="togglePassword('confirmPassword', this); return false;">
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
            function validatePasswordOnSubmit() {
                const feedback = document.getElementById("password-feedback");

                if (feedback.textContent !== "") {
                    feedback.focus();
                    return false;
                }

                return true;
            }

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

        <!-- Validar Senha -->
        <script>
            function validatePassword() {
                const password = document.getElementById("password");
                const confirmPassword = document.getElementById("confirmPassword");
                const feedback = document.getElementById("password-feedback");
                const button = document.getElementById("btnAddPassword");

                password.setAttribute("aria-invalid", "false");
                confirmPassword.setAttribute("aria-invalid", "false");

                if (password.value.length < 8) {
                    feedback.textContent = "A senha deve ter no mínimo 8 caracteres.";
                    password.setAttribute("aria-invalid", "true");
                    button.disabled = true;
                    return;
                }

                if (password.value !== confirmPassword.value) {
                    feedback.textContent = "As senhas não coincidem.";
                    password.setAttribute("aria-invalid", "true");
                    confirmPassword.setAttribute("aria-invalid", "true");
                    button.disabled = true;
                    return;
                }

                feedback.textContent = "";
                button.disabled = false;
            }
        </script>

        <?php if (isset($_SESSION['msg'])): ?>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('password-error').focus();
                });
            </script>
        <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['msgcad'])): ?>
            <?php if (!isset($_SESSION['msg'])): ?>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('password-success').focus();
                });
            </script>
            <?php endif; ?>
        <?php unset($_SESSION['msgcad']); ?>
        <?php endif; ?>

    </body>
</html>