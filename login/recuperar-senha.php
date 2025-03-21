<?php
    // Caso prefira o .env apenas descomente o codigo e comente o "include('parameters.php');" acima
	// Carrega as variáveis de ambiente do arquivo .env

    // Caminho para o diretório pai
    $parentDir = dirname(__DIR__);

	require $parentDir . '/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable($parentDir);
	$dotenv->load();

    // Informacoes para PHPMailer
	$smtp_host = $_ENV['SMTP_HOST'];
	$smtp_from = $_ENV['SMTP_FROM'];
	$smtp_username = $_ENV['SMTP_USERNAME'];
	$smtp_password = $_ENV['SMTP_PASSWORD'];
	$smtp_secure = $_ENV['SMTP_SECURE'];
	$smtp_port = $_ENV['SMTP_PORT'];

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

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require './../lib/vendor/autoload.php';

    // Crie uma nova instância do PHPMailer
    $mail = new PHPMailer(true);

    // Tabela que sera feita a consulta
    $tabela = "tb_clientes";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["SendRecupPassword"])) {

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
                $_SESSION['msg'] = "Falha na validação do " . $captcha['name'] . ".";
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
                $_SESSION['msg'] = "Falha na validação do " . $captcha['name'] . ".";
            }

        }

        // Verifique a resposta
        if ($captcha['type'] == 'none' || (isset($response) && isset($response['success']) && $response['success'] === true)) {

            //var_dump($dados);
            // Informacoes da instituicao
            $query_instituicao = "SELECT nome, email 
                        FROM tb_checkout 
                        WHERE id =:id  
                        LIMIT 1";
            $result_instituicao = $conn->prepare($query_instituicao);
            $result_instituicao->bindValue(':id', 1, PDO::PARAM_INT);
            $result_instituicao->execute();
            
            $row_instituicao = $result_instituicao->fetch(PDO::FETCH_ASSOC);
                
            $query_usuario = "SELECT id, nome, email 
                        FROM $tabela 
                        WHERE email =:email  
                        LIMIT 1";
            $result_usuario = $conn->prepare($query_usuario);
            $result_usuario->bindParam(':email', $dados['email'], PDO::PARAM_STR);
            $result_usuario->execute();

            if (($result_usuario) and ($result_usuario->rowCount() != 0)) {
                $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);

                $token = password_hash($row_usuario['id'], PASSWORD_DEFAULT);
                //echo "Chave $token <br>";

                $query_up_usuario = "UPDATE $tabela 
                            SET recup_password =:recup_password 
                            WHERE id =:id 
                            LIMIT 1";
                $result_up_usuario = $conn->prepare($query_up_usuario);
                $result_up_usuario->bindParam(':recup_password', $token, PDO::PARAM_STR);
                $result_up_usuario->bindParam(':id', $row_usuario['id'], PDO::PARAM_INT);

                if ($result_up_usuario->execute()) {
                    $mail = new PHPMailer(true);
            
                    $link = INCLUDE_PATH . "login/atualizar-senha.php?token=$token";

                    try {
                        /*$mail->SMTPDebug = SMTP::DEBUG_SERVER;*/
                        $mail->CharSet = 'UTF-8';
                        $mail->isSMTP();
                        $mail->Host       = $smtp_host;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $smtp_username;
                        $mail->Password   = $smtp_password;
                        $mail->SMTPSecure = $smtp_secure;
                        $mail->Port       = $smtp_port;

                        $mail->setFrom($smtp_from, 'Atendimento - ' . $row_instituicao['nome']);
                        $mail->addReplyTo($row_instituicao['email'], 'Atendimento - ' . $row_instituicao['nome']);
                        $mail->addAddress($row_usuario['email'], $row_usuario['nome']);

                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = 'Recuperar senha';
                        $mail->Body    = 'Prezado(a) ' . $row_usuario['nome'] .".<br><br>Você solicitou alteração de senha.<br><br>Para continuar o processo de recuperação de sua senha, clique no link abaixo ou cole o endereço no seu navegador: <br><br><a href='" . $link . "'>Clique Aqui Para Recuperar Sua Senha</a><br><br>Ou<br><br>Cole esse link no seu navegador:<br><p>" . $link . "</p><br><br>Se você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.<br><br>";
                        $mail->AltBody = 'Prezado(a) ' . $row_usuario['nome'] ."\n\nVocê solicitou alteração de senha.\n\nPara continuar o processo de recuperação de sua senha, clique no link abaixo ou cole o endereço no seu navegador: \n\n" . $link . "\n\nOu\n\nCole esse link no seu navegador:\n" . $link . "\n\nSe você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.\n\n";

                        $mail->send();

                        $_SESSION['msgcad'] = "Enviado e-mail com instruções para recuperar a senha. Acesse a sua caixa de e-mail para recuperar a senha!";
                        header("Location: " . INCLUDE_PATH . "login/");
                    } catch (Exception $e) {
                        $_SESSION['msg'] = "Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    $_SESSION['msg'] = "Erro: Usuário não encontrado!";
                }
            }

        } else {
            $_SESSION['msg'] = "Por favor preencha o " . $captcha['name'] . " para continuar.";
        }
    }
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>Floema Doar - Recuperar senha</title>

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
			<h1>Kitanda</h1>
<!--                        <img src="<?= INCLUDE_PATH_ADMIN; ?>images/logo-inverse.png" alt="Logo <?php echo $project['name']; ?>" class="navbar-brand-image" style="width: 149px; height: 21px;"> -->
                    </a>
                </div>
                <form class="card card-md" action="<?php echo INCLUDE_PATH; ?>login/recuperar-senha.php" method="post">
                    <div class="card-body">

                        <p class="text-danger mb-3">
                            <?php
                                if(isset($_SESSION['msg'])){
                                    echo $_SESSION['msg'];
                                    unset($_SESSION['msg']);
                                    echo "<br>";
                                }
                            ?>
                        </p>

                        <h2 class="card-title text-center mb-4">Esqueceu a senha?</h2>
                        <p class="text-secondary mb-4">Digite seu endereço de e-mail e sua senha será redefinida e enviada para o seu e-mail.</p>
                        <div class="mb-4">
                            <label for="email" class="form-label">Endereço de e-mail</label>
                            <input name="email" id="email" type="email" class="form-control" placeholder="Digite o e-mail" required>
                        </div>
                        <?php if (isset($hcaptcha)): ?>
                            <div class="h-captcha" data-sitekey="<?php echo $hcaptcha['public_key']; ?>"></div>
                        <?php elseif (isset($turnstile)): ?>
                            <div class="cf-turnstile" data-sitekey="<?php echo $turnstile['public_key']; ?>"></div>
                        <?php endif; ?>
                        <div class="form-footer mt-4">
                            <button type="submit" class="btn btn-primary btn-4 w-100" name="SendRecupPassword">
                                <!-- Baixar ícone SVG de http://tabler.io/icons/icon/mail -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2"><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                                Enviar nova senha
                            </button>
                        </div>
                    </div>
                </form>
                <div class="text-center text-secondary mt-3">
                    Esqueceu? <a href="<?php echo INCLUDE_PATH; ?>login/">Voltar para a tela de login</a>.
                </div>
            </div>

        </div>

        <!-- Tabler Core -->
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/tabler.min.js?1738096682" defer></script>
        <script src="<?php echo INCLUDE_PATH; ?>dist/js/demo.min.js?1738096682" defer></script>

    </body>
</html>
