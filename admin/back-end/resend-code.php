<?php
    session_start();
    include_once('../../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require './../../lib/vendor/autoload.php';

    function resendFinalizationEmail($nome, $email, $id, $conn) {
        include_once('../../config.php');

        // Gera um novo token para o magic_link
        $token = bin2hex(random_bytes(16));
        
        // Atualiza o campo magic_link na tabela tb_clientes para o usuário
        $query = "UPDATE tb_clientes SET magic_link = :magic_link WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':magic_link', $token, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return "Erro ao atualizar o token de finalização.";
        }
        
        // Configurações SMTP (definidas no .env)
        $smtp_host     = $_ENV['SMTP_HOST'];
        $smtp_from     = $_ENV['SMTP_FROM'];
        $smtp_username = $_ENV['SMTP_USERNAME'];
        $smtp_password = $_ENV['SMTP_PASSWORD'];
        $smtp_secure   = $_ENV['SMTP_SECURE'];
        $smtp_port     = $_ENV['SMTP_PORT'];
        
        // Cria uma instância do PHPMailer
        $mail = new PHPMailer(true);
        
        // Informações da instituição (opcional, para personalizar o remetente)
        $query_instituicao = "SELECT nome, email FROM tb_checkout WHERE id = :id LIMIT 1";
        $stmt_inst = $conn->prepare($query_instituicao);
        $stmt_inst->bindValue(':id', 1, PDO::PARAM_INT);
        $stmt_inst->execute();
        $row_instituicao = $stmt_inst->fetch(PDO::FETCH_ASSOC);
        
        // Constrói o link de confirmação para finalizar o cadastro
        $confirmLink = INCLUDE_PATH . "login/finalize-registration.php?token=" . $token;
        
        $subject    = "Reenvio: Finalize seu cadastro";
        $message    = "Olá $nome,<br><br>Recebemos sua solicitação para reenvio do e-mail de finalização de cadastro. Para concluir seu registro, clique no link abaixo:<br><br><a href='$confirmLink'>$confirmLink</a>";
        $altMessage = "Olá $nome,\n\nRecebemos sua solicitação para reenvio do e-mail de finalização de cadastro. Para concluir seu registro, acesse o link:\n\n$confirmLink";
        
        try {
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
        
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = $altMessage;
        
            $mail->send();
            
            $response = array('status' => 'success', 'message' => "E-mail reenviado com sucesso para <b>$email</b>.");
            return $response;
        } catch (Exception $e) {
            $response = array('status' => 'error', 'message' => "Erro: E-mail não enviado. Mailer Error: {$mail->ErrorInfo}");
            return $response;
        }
    }

    // Exemplo: ao receber o ID do usuário via GET ou POST
    $id = $_GET['id'] ?? null; // ou $_POST['id']
    if ($id) {
        // Recupere os dados do usuário (nome e e-mail) conforme sua lógica
        $stmt = $conn->prepare("SELECT nome, email FROM tb_clientes WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            $resultMessage = resendFinalizationEmail($usuario['nome'], $usuario['email'], $id, $conn);
            if ($resultMessage['status'] == 'success') {
                $_SESSION['msg'] = $resultMessage['message'];
            } else {
                $_SESSION['error_msg'] = $resultMessage['message'];
            }
            header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-usuario?id=' . $id);
        } else {
            $_SESSION['error_msg'] = "Usuário não encontrado";
            header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-usuario?id=' . $id);
        }
    } else {
        $_SESSION['error_msg'] = "ID não informado";
        header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-usuario?id=' . $id);
    }