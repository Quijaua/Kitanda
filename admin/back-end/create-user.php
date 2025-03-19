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
    function sendConfirmationEmail($nome, $email, $token, $conn) {
        // Caminho para o diretório pai
        $parentDir = dirname(dirname(__DIR__));

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

        



        // Constroi o link de confirmação. Suponha que a página para finalizar o cadastro seja "finalize-registration.php"
        $confirmLink = INCLUDE_PATH . "login/finalize-registration.php?token=" . $token;

        $subject = "Finalize seu cadastro";
        $message = "Olá $nome,<br><br>Para concluir o seu cadastro, clique no link abaixo:<br><br><a href='$confirmLink'>$confirmLink</a>";
        $altMessage = "Olá $nome,\n\nPara concluir o seu cadastro, clique no link abaixo:\n\n<a href='$confirmLink'>$confirmLink</a>";






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

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = $altMessage;

            $mail->send();

            return "Enviado e-mail com instruções para recuperar a senha. Acesse a sua caixa de e-mail para recuperar a senha!";
        } catch (Exception $e) {
            return "Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Verifica se o formulário foi enviado e se o botão correto foi acionado
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAddUser'])) {
        // Captura e sanitiza os dados enviados
        $nome       = trim($_POST['nome']);
        $telefone   = trim($_POST['telefone']);
        $email      = trim($_POST['email']);
        $funcao_id  = intval($_POST['funcao_id']);
        $instagram  = trim($_POST['instagram']);
        $site       = trim($_POST['site']);
        $facebook   = trim($_POST['facebook']);
        $tiktok     = trim($_POST['tiktok']);
        $descricao  = trim($_POST['descricao']);

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
            // Supondo que a tabela possua as colunas: nome, phone, email, instagram, site, facebook, tiktok, descricao, magic_link e status (0 = inativo, aguardando confirmação)
            $stmtInsert = $conn->prepare("
                INSERT INTO tb_clientes 
                (nome, phone, email, instagram, site, facebook, tiktok, descricao, magic_link, status) 
                VALUES 
                (:nome, :phone, :email, :instagram, :site, :facebook, :tiktok, :descricao, :magic_link, 0)
            ");
            $stmtInsert->bindParam(':nome', $nome);
            $stmtInsert->bindParam(':phone', $telefone);
            $stmtInsert->bindParam(':email', $email);
            $stmtInsert->bindParam(':instagram', $instagram);
            $stmtInsert->bindParam(':site', $site);
            $stmtInsert->bindParam(':facebook', $facebook);
            $stmtInsert->bindParam(':tiktok', $tiktok);
            $stmtInsert->bindParam(':descricao', $descricao);
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

            // Envia o e-mail de confirmação para finalizar o cadastro
            sendConfirmationEmail($nome, $email, $token, $conn);

            $_SESSION['msg'] = "Usuário cadastrado com sucesso! Um e-mail foi enviado para que o usuário finalize o cadastro.";
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