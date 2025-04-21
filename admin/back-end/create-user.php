<?php
    session_start();
    include('../../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require './../../lib/vendor/autoload.php';

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit;

    /**
     * Função para enviar o e-mail de confirmação do cadastro.
     * Ao clicar no link do e-mail, o usuário poderá concluir o cadastro do perfil.
     */
    function sendEmail($nome, $email, $subject, $message, $conn) {
        // Caminho para o diretório pai
        $parentDir = dirname(dirname(__DIR__));

        require $parentDir . '/vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable($parentDir);
        $dotenv->load();

        // Dados de configuração do e-mail
        $smtp_host     = $_ENV['SMTP_HOST']     ?? null;
        $smtp_from     = $_ENV['SMTP_FROM']     ?? null;
        $smtp_username = $_ENV['SMTP_USERNAME'] ?? null;
        $smtp_password = $_ENV['SMTP_PASSWORD'] ?? null;
        $smtp_secure   = $_ENV['SMTP_SECURE']   ?? null;
        $smtp_port     = $_ENV['SMTP_PORT']     ?? null;
    
        // Verifica se os dados do e-mail estão configurados
        if (!$smtp_host || !$smtp_from || !$smtp_username || !$smtp_password || !$smtp_port) {
            return array("status" => "error", "message" => "Os dados de configuração do e-mail não estão completos.");
        }

        // Crie uma nova instância do PHPMailer
        $mail = new PHPMailer(true);

        // Informacoes da instituicao
        $query_instituicao = "SELECT nome, email 
                    FROM tb_checkout 
                    WHERE id =:id  
                    LIMIT 1";
        $result_instituicao = $conn->prepare($query_instituicao);
        $result_instituicao->bindValue(':id', 1, PDO::PARAM_INT);
        $result_instituicao->execute();
        
        $row_instituicao = $result_instituicao->fetch(PDO::FETCH_ASSOC);

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
            $mail->addAddress($email, $nome);

            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message['message'];
            $mail->AltBody = $message['alt'];

            $mail->send();

            return array("status" => "success", "message" => "Um e-mail foi enviado para o novo usuário");
        } catch (Exception $e) {
            return array("status" => "error", "message" => "Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    // Verifica se o formulário foi enviado e se o botão correto foi acionado
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAddUser'])) {
        // Captura e sanitiza os dados enviados
        $nome       = trim($_POST['nome']);
        $funcao_id  = intval($_POST['funcao_id']);
        $email      = trim($_POST['email']);
        $senha      = ($_POST['passwordMethod'] == 'set') ? trim($_POST['senha']) : null;

        // Gerar o hash da senha utilizando o algoritmo padrão (geralmente bcrypt)
        $senhaToHash = $senha;
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Verifica se o e-mail já está em uso
        $stmt = $conn->prepare("SELECT id FROM tb_clientes WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $_SESSION['error_msg'] = "O e-mail informado já está sendo utilizado.";
            header('Location: ' . INCLUDE_PATH_ADMIN . 'criar-usuario');
            exit;
        }

        // Gera um token para confirmação do cadastro
        $token = bin2hex(random_bytes(16));

        try {
            // Inicia a transação
            $conn->beginTransaction();

            // Insere o novo usuário na tabela tb_clientes
            // Supondo que a tabela possua as colunas: nome, email, senha, magic_link e status (0 = inativo, aguardando confirmação)
            $stmtInsert = $conn->prepare("
                INSERT INTO tb_clientes 
                (roles, nome, email, password, magic_link, status) 
                VALUES 
                (2, :nome, :email, :senha, :magic_link, 0)
            ");
            $stmtInsert->bindParam(':nome', $nome);
            $stmtInsert->bindParam(':email', $email);
            $stmtInsert->bindParam(':senha', $senhaHash);
            $stmtInsert->bindParam(':magic_link', $token);
            $stmtInsert->execute();

            // Recupera o ID gerado para o usuário
            $user_id = $conn->lastInsertId();

            // Insere o novo usuário na tabela tb_permissao_usuario
            $stmtInsert = $conn->prepare("
                INSERT INTO tb_permissao_usuario 
                (usuario_id, permissao_id) 
                VALUES 
                (:usuario_id, :permissao_id)
            ");
            $stmtInsert->bindParam(':usuario_id', $user_id);
            $stmtInsert->bindParam(':permissao_id', $funcao_id);
            $stmtInsert->execute();

            // Finaliza a transação
            $conn->commit();

            if (isset($_POST['email_boas_vindas']) || isset($_POST['email_senha'])) {
                $loginLink = INCLUDE_PATH . "login";

                $subject = "Seja bem-vindo ao {$project['name']}";
                $message['message'] = "Olá {$nome},<br><br>Você foi adicionado como membro do {$project['name']}.<br><br>";
                $message['alt'] = "Olá {$nome},\n\nVocê foi adicionado como membro do {$project['name']}.\n\n";

                if ($_POST['passwordMethod'] == 'set' && isset($_POST['email_senha'])) {
                    $message['message'] .= "Utilize as seguintes credenciais para acessar sua conta:<br><b>E-mail:</b> {$email}<br><b>Senha:</b> {$senha}<br><br>";
                    $message['alt'] .= "Utilize as seguintes credenciais para acessar sua conta:\n\nE-mail: {$email}\nSenha: {$senha}\n\n";
                }

                $message['message'] .= "Clique <a href='{$loginLink}'>aqui</a> para acessar o painel ou copie e cole o link abaixo no seu navegador:<br>{$loginLink}";
                $message['alt'] .= "Acesse o painel através do seguinte link: {$loginLink}";

                // Envia o e-mail de boas-vindas
                $returnWelcomeEmail = sendEmail($nome, $email, $subject, $message, $conn);
            }

            if ($_POST['passwordMethod'] == 'email') {
                // Constrói o link de confirmação para que o usuário finalize o cadastro definindo sua senha
                $confirmLink = INCLUDE_PATH . "login/finalize-registration.php?token=" . $token;

                $subject = "Finalize seu cadastro no {$project['name']}";
                $message['message'] = "Olá {$nome},<br><br>Para concluir seu cadastro, clique no link abaixo:<br><br>"
                         . "<a href='{$confirmLink}'>{$confirmLink}</a><br><br>"
                         . "Caso não consiga clicar, copie e cole o link no seu navegador.";
                $message['alt'] = "Olá {$nome},\n\nPara concluir seu cadastro, acesse o seguinte link:\n\n{$confirmLink}\n\n";

                // Envia o e-mail de confirmação para finalizar o cadastro
                $returnPasswordEmail = sendEmail($nome, $email, $subject, $message, $conn);
            }

            $emailMessage = '';

            if (isset($returnWelcomeEmail) && isset($returnPasswordEmail) && $returnWelcomeEmail == $returnPasswordEmail) {
                $emailMessage = "<br><small class='text-reset'>Obs.: {$returnWelcomeEmail['message']}</small>";
            }if (isset($returnWelcomeEmail) && isset($returnPasswordEmail)) {
                $emailMessage = "<br><br><small class='text-reset'>Obs.: <br>{$returnWelcomeEmail['message']}; <br>{$returnPasswordEmail['message']}</small>";
            } else if (isset($returnWelcomeEmail)) {
                $emailMessage = "<br><small class='text-reset'>Obs.: {$returnWelcomeEmail['message']}</small>";
            } else if (isset($returnPasswordEmail)) {
                $emailMessage = "<br><small class='text-reset'>Obs.: {$returnPasswordEmail['message']}</small>";
            }

            $_SESSION['msg'] = "Usuário cadastrado com sucesso! $emailMessage";

            header('Location: ' . INCLUDE_PATH_ADMIN . 'usuarios');
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['error_msg'] = "Erro ao cadastrar o usuário: " . $e->getMessage();
            header('Location: ' . INCLUDE_PATH_ADMIN . 'cadastrar-usuario');
            exit;
        }
    } else {
        $_SESSION['error_msg'] = "Método inválido.";
        header('Location: ' . INCLUDE_PATH_ADMIN . 'usuarios');
        exit;
    }