<?php
session_start();
ob_start();
include('../config.php');

// Consulta as configurações de captcha para a página "login"
$query = "SELECT captcha_type AS type FROM tb_page_captchas WHERE page_name = :page_name";
$stmt = $conn->prepare($query);
$stmt->bindValue(':page_name', 'login');
$stmt->execute();
$captcha = $stmt->fetch(PDO::FETCH_ASSOC);

if ($captcha['type'] == 'hcaptcha') {
    $hcaptcha = [
        'public_key' => $_ENV['HCAPTCHA_CHAVE_DE_SITE']
    ];
} elseif ($captcha['type'] == 'turnstile') {
    $turnstile = [
        'public_key' => $_ENV['TURNSTILE_CHAVE_DE_SITE']
    ];
}

if (isset($_GET["token"])) {
    $token = $_GET["token"];

    // Consulta a tabela tb_clientes usando a coluna magic_link
    $tabela = "tb_clientes";
    $query_usuario = "SELECT id, nome, email FROM $tabela WHERE magic_link = :magic_link LIMIT 1";
    $result_usuario = $conn->prepare($query_usuario);
    $result_usuario->bindParam(':magic_link', $token, PDO::PARAM_STR);
    $result_usuario->execute();

    if ($result_usuario && $result_usuario->rowCount() != 0) {
        $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Captcha: recupera as chaves secretas conforme o tipo de captcha
            $hcaptcha_secret = $_ENV['HCAPTCHA_CHAVE_SECRETA'];
            $turnstile_secret = $_ENV['TURNSTILE_CHAVE_SECRETA'];

            // Consulta a configuração do captcha para a página "login"
            $query = "
                SELECT 
                    captcha_type AS type, 
                    CASE 
                        WHEN captcha_type = 'hcaptcha' THEN 'hCaptcha'
                        WHEN captcha_type = 'turnstile' THEN 'Turnstile'
                        ELSE 'Nenhum' 
                    END AS name
                FROM tb_page_captchas 
                WHERE page_name = :page_name
            ";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':page_name', 'login');
            $stmt->execute();
            $captcha = $stmt->fetch(PDO::FETCH_ASSOC);

            $responseKey = $_POST['h-captcha-response'] ?? $_POST['cf-turnstile-response'] ?? '';

            if ($captcha['type'] == 'hcaptcha') {
                if (!empty($responseKey)) {
                    $url = 'https://hcaptcha.com/siteverify';
                    $data = [
                        'secret' => $hcaptcha_secret,
                        'response' => $responseKey
                    ];
                    $options = [
                        'http' => [
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($data)
                        ]
                    ];
                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $response = json_decode($result, true);
                } else {
                    $_SESSION['captcha_error'] = true;
                    $_SESSION['msgcad'] = "Falha na validação do " . $captcha['name'] . ".";
                }
            } elseif ($captcha['type'] == 'turnstile') {
                if (!empty($responseKey)) {
                    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
                    $data = [
                        'secret' => $turnstile_secret,
                        'response' => $responseKey
                    ];
                    $options = [
                        'http' => [
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($data)
                        ]
                    ];
                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $response = json_decode($result, true);
                } else {
                    $_SESSION['captcha_error'] = true;
                    $_SESSION['msgcad'] = "Falha na validação do " . $captcha['name'] . ".";
                }
            }

            // Se não há captcha ou a validação foi bem-sucedida
            if ($captcha['type'] == 'none' || (isset($response) && isset($response['success']) && $response['success'] === true)) {

                // Recupera os dados do formulário
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $nome = $_POST['nome'];
                $email = $_POST['email'];
                // Atualiza a senha, o nome, o e-mail e zera o magic_link para finalizar o cadastro
                $query_up_usuario = "UPDATE $tabela 
                                     SET password = :password,
                                         nome = :nome,
                                         email = :email,
                                         magic_link = NULL,
                                         status = 1
                                     WHERE id = :id 
                                     LIMIT 1";
                $result_up_usuario = $conn->prepare($query_up_usuario);
                $result_up_usuario->bindParam(':password', $password, PDO::PARAM_STR);
                $result_up_usuario->bindParam(':nome', $nome, PDO::PARAM_STR);
                $result_up_usuario->bindParam(':email', $email, PDO::PARAM_STR);
                $result_up_usuario->bindParam(':id', $row_usuario['id'], PDO::PARAM_INT);

                if ($result_up_usuario->execute()) {
                    session_destroy();
                    $_SESSION['user_id'] = $row_usuario['id'];

                    $_SESSION['msg'] = "Cadastro finalizado com sucesso!";
                    header("Location: " . INCLUDE_PATH_ADMIN);
                    exit();
                } else {
                    $_SESSION['msgcad'] = "Erro: Tente novamente!";
                }
            } else {
                $_SESSION['captcha_error'] = true;
                $_SESSION['msgcad'] = "Por favor, preencha o " . $captcha['name'] . " para continuar.";
            }
        }
    } else {
        $_SESSION['msg'] = "Erro: Link inválido, solicite novo link para finalizar o cadastro!";
        header("Location: " . INCLUDE_PATH . "login/recuperar-senha.php");
        exit();
    }
} else {
    $_SESSION['msg'] = "Token não fornecido.";
    header("Location: " . INCLUDE_PATH . "login/recuperar-senha.php");
    exit();
}
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
    <link href="<?php echo INCLUDE_PATH; ?>dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="<?php echo INCLUDE_PATH; ?>dist/css/kitanda.min.css" rel="stylesheet"/>
    <link href="<?php echo INCLUDE_PATH; ?>dist/libs/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet"/>
    <?php if (isset($hcaptcha)): ?>
        <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <?php elseif (isset($turnstile)): ?>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php endif; ?>
</head>
<body>
    <div class="page">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="<?php echo INCLUDE_PATH_ADMIN; ?>" class="navbar-brand navbar-brand-autodark">
			<h1>Kitanda</h1>
<!--                    <img src="<?= INCLUDE_PATH_ADMIN; ?>images/logo-inverse.png" alt="<?php echo $project['name']; ?>" class="navbar-brand-image" style="width: 149px; height: 21px;"> -->
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Finalizar Cadastro</h2>

                    <?php if (isset($_SESSION['msgcad'])): ?>
                        <div id="password-error" tabindex="-1" class="alert alert-danger mb-3" role="alert" aria-live="assertive">
                            <?= $_SESSION['msgcad']; ?>
                        </div>
                    <?php endif; ?>

                    <form action="?token=<?php echo $token; ?>" method="post" onsubmit="return validatePasswordOnSubmit()">
                        <div id="form-error" class="alert alert-danger" role="alert" aria-live="assertive"></div>

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <!-- Campo editável para o nome -->
                            <input name="nome" id="nome" type="text" value="<?php echo htmlspecialchars($row_usuario['nome']); ?>" 
                                class="form-control <?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'is-invalid' : ''; ?>"
                                aria-invalid="<?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'true' : 'false'; ?>"
                                <?php if (isset($_SESSION['msgcad']) && !$_SESSION['captcha_error']): ?>aria-describedby="form-error"<?php endif; ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <!-- Campo editável para o e-mail -->
                            <input name="email" id="email" type="email" value="<?php echo htmlspecialchars($row_usuario['email']); ?>" 
                                class="form-control <?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'is-invalid' : ''; ?>"
                                aria-invalid="<?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'true' : 'false'; ?>"
                                <?php if (isset($_SESSION['msgcad']) && !$_SESSION['captcha_error']): ?>aria-describedby="form-error"<?php endif; ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <div class="input-group input-group-flat">
                                <input name="password" id="password" type="password" placeholder="Crie sua senha" oninput="validatePassword()" 
                                    class="form-control <?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'is-invalid' : ''; ?>"
                                    aria-invalid="<?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'true' : 'false'; ?>"
                                    <?php if (isset($_SESSION['msgcad']) && !$_SESSION['captcha_error']): ?>aria-describedby="form-error"<?php endif; ?> required>
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
                                <input name="confirmPassword" id="confirmPassword" type="password" placeholder="Confirme sua senha" oninput="validatePassword()" 
                                    class="form-control <?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'is-invalid' : ''; ?>"
                                    aria-invalid="<?php echo isset($_SESSION['msgcad']) && !$_SESSION['captcha_error'] ? 'true' : 'false'; ?>"
                                    <?php if (isset($_SESSION['msgcad']) && !$_SESSION['captcha_error']): ?>aria-describedby="form-error"<?php endif; ?> required>
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
                            <button type="submit" class="btn btn-primary w-100" id="SendNewPassword" name="SendNewPassword" disabled>Finalizar Cadastro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabler Core -->
    <script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler.min.js" defer></script>
    <script src="<?php echo INCLUDE_PATH; ?>dist/js/kitanda.min.js" defer></script>

    <!-- Exibir/Ocultar Senha -->
    <script>
        function validatePasswordOnSubmit() {
            const feedback = document.getElementById("form-error");

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
            const feedback = document.getElementById("form-error");
            const button = document.getElementById("SendNewPassword");

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

    <?php if (isset($_SESSION['msgcad'])): ?>
        <?php if (!isset($_SESSION['msg'])): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                document.getElementById('password-error').focus();
            });
        </script>
        <?php endif; ?>
    <?php unset($_SESSION['msgcad'], $_SESSION['captcha_error']); ?>
    <?php endif; ?>

</body>
</html>
