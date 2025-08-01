<?php
    session_start();
    ob_start();
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

    if (isset($_GET["token"])) {
        $token = $_GET["token"];

        // Tabela que sera feita a consulta
        $tabela = "tb_clientes";
        
        $query_usuario = "SELECT id 
                            FROM $tabela 
                            WHERE recup_password = :recup_password  
                            LIMIT 1";
        $result_usuario = $conn->prepare($query_usuario);
        $result_usuario->bindParam(':recup_password', $token, PDO::PARAM_STR);
        $result_usuario->execute();

        if (($result_usuario) and ($result_usuario->rowCount() != 0)) {

            $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                // Acessa as variáveis de ambiente
                $hcaptcha_secret = $_ENV['HCAPTCHA_CHAVE_SECRETA'];
                $turnstile_secret = $_ENV['TURNSTILE_CHAVE_SECRETA'];

                // Consulta à tabela tb_page_captchas para verificar qual captcha usar
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

                $responseKey = $_POST['h-captcha-response'] ?? $_POST['cf-turnstile-response'] ?? ''; // Chaves de resposta

                if ($captcha['type'] == 'hcaptcha') {

                    // Verifique se a chave de resposta está presente
                    if (isset($responseKey) && !empty($responseKey)) {

                        // Faça uma solicitação para validar a resposta do hCaptcha
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
                        $_SESSION['msgcad'] = "Falha na validação do " . $captcha['name'] . ".";
                    }

                } elseif ($captcha['type'] == 'turnstile') {
                    
                    // Verifique se a chave de resposta está presente
                    if (isset($responseKey) && !empty($responseKey)) {

                        // Verificação do Turnstile
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
                        $_SESSION['msgcad'] = "Falha na validação do " . $captcha['name'] . ".";
                    }

                }

                // Verifique a resposta
                if ($captcha['type'] == 'none' || (isset($response) && isset($response['success']) && $response['success'] === true)) {

                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $recup_password = null;

                    $query_up_usuario = "UPDATE $tabela 
                            SET password = :password,
                            recup_password = :recup_password
                            WHERE id = :id 
                            LIMIT 1";
                    $result_up_usuario = $conn->prepare($query_up_usuario);
                    $result_up_usuario->bindParam(':password', $password, PDO::PARAM_STR);
                    $result_up_usuario->bindParam(':recup_password', $recup_password, PDO::PARAM_NULL);
                    $result_up_usuario->bindParam(':id', $row_usuario['id'], PDO::PARAM_INT);

                    if ($result_up_usuario->execute()) {
                        session_destroy();
                        $_SESSION['msg'] = "Senha atualizada com sucesso!";
                        header("Location: " . INCLUDE_PATH_ADMIN);
                    } else {
                        $_SESSION['msgcad'] = "Erro: Tente novamente!";
                    }

                } else {
                    $_SESSION['msgcad'] = "Por favor preencha o " . $captcha['name'] . " para continuar.";
                }
            }
        } else {
            $_SESSION['msg'] = "Erro: Link inválido, solicite novo link para atualizar a senha!";
            header("Location: " . INCLUDE_PATH . "login/recuperar-senha.php");
        }
    }

    $usuario = "";
    if (isset($_POST['password'])) {
        $usuario = $_POST['password'];
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
<!--                        <img src="<?= INCLUDE_PATH_ADMIN; ?>images/logo-inverse.png" alt="Logo <?php echo $project['name']; ?>" class="navbar-brand-image" style="width: 149px; height: 21px;"> -->
                    </a>
                </div>
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">Atualizar Senha</h2>
                        <p class="text-danger mb-3">
                            <?php
                                if(isset($_SESSION['msgcad'])){
                                    echo $_SESSION['msgcad'];
                                    unset($_SESSION['msgcad']);
                                    echo "<br>";
                                }
                            ?>
                        </p>
                        <form action="<?php echo INCLUDE_PATH; ?>login/atualizar-senha.php?token=<?php echo $token; ?>" method="post">
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
                                <button type="submit" class="btn btn-primary w-100" id="SendNewPassword" name="SendNewPassword" disabled>Atualizar Senha</button>
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
