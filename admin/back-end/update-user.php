<?php
session_start();
include('../../config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './../../lib/vendor/autoload.php';

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

    // Informações para PHPMailer
    $smtp_host     = $_ENV['SMTP_HOST'];
    $smtp_from     = $_ENV['SMTP_FROM'];
    $smtp_username = $_ENV['SMTP_USERNAME'];
    $smtp_password = $_ENV['SMTP_PASSWORD'];
    $smtp_secure   = $_ENV['SMTP_SECURE'];
    $smtp_port     = $_ENV['SMTP_PORT'];

    $mail = new PHPMailer(true);

    // Informações da instituição
    $query_instituicao = "SELECT nome, email FROM tb_checkout WHERE id = :id LIMIT 1";
    $stmtInstituicao = $conn->prepare($query_instituicao);
    $stmtInstituicao->bindValue(':id', 1, PDO::PARAM_INT);
    $stmtInstituicao->execute();
    $row_instituicao = $stmtInstituicao->fetch(PDO::FETCH_ASSOC);

    // Constroi o link de confirmação – supondo que a página para finalizar o cadastro seja "finalize-registration.php"
    $confirmLink = INCLUDE_PATH . "login/finalize-registration.php?token=" . $token;
    $subject = "Finalize seu cadastro";
    $message = "Olá $nome,<br><br>Para concluir o seu cadastro, clique no link abaixo:<br><br><a href='$confirmLink'>$confirmLink</a>";
    $altMessage = "Olá $nome,\n\nPara concluir o seu cadastro, clique no link abaixo:\n\n$confirmLink";

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
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEditUser'])) {
    // Valida o ID do usuário recebido via GET
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['error_msg'] = "ID do usuário inválido.";
        header('Location: ' . INCLUDE_PATH_ADMIN . 'usuarios');
        exit;
    }
    $user_id = intval($_GET['id']);

    // Captura e sanitiza os dados enviados
    $nome       = trim($_POST['nome']);
    $submitted_email = trim($_POST['email']);
    $funcao_id  = intval($_POST['funcao_id']);

    // Carrega os dados atuais do usuário
    $stmt = $conn->prepare("SELECT * FROM tb_clientes WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$currentUser) {
        $_SESSION['error_msg'] = "Usuário não encontrado.";
        header('Location: ' . INCLUDE_PATH_ADMIN . 'usuarios');
        exit;
    }

    // Verifica se o e-mail foi alterado
    $emailChanged = false;
    if ($submitted_email !== $currentUser['email']) {
        $emailChanged = true;
        // Verifica se o novo e-mail já está em uso por outro usuário
        $stmtCheck = $conn->prepare("SELECT id FROM tb_clientes WHERE email = :email AND id != :id");
        $stmtCheck->bindParam(':email', $submitted_email);
        $stmtCheck->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmtCheck->execute();
        if ($stmtCheck->rowCount() > 0) {
            $_SESSION['error_msg'] = "O e-mail informado já está sendo utilizado por outro usuário.";
            header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-usuario?id=' . $user_id);
            exit;
        }
    }

    // Se o e-mail foi alterado, gera novo token e define status para não confirmado (0)
    if ($emailChanged) {
        $token = bin2hex(random_bytes(16));
        $newStatus = 0;
    } else {
        // Caso contrário, mantém os valores atuais
        $token = $currentUser['magic_link'];
        $newStatus = $currentUser['status'];
    }

    try {
        // Inicia a transação
        $conn->beginTransaction();

        // Atualiza os dados do usuário na tabela tb_clientes
        $stmtUpdate = $conn->prepare("
            UPDATE tb_clientes SET 
                nome = :nome,
                email = :email,
                magic_link = :token,
                status = :status
            WHERE id = :id
        ");
        $stmtUpdate->bindParam(':nome', $nome);
        $stmtUpdate->bindParam(':email', $submitted_email);
        $stmtUpdate->bindParam(':token', $token);
        $stmtUpdate->bindParam(':status', $newStatus, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmtUpdate->execute();

        // Atualiza a função do usuário na tabela tb_permissao_usuario
        $stmtUpdatePerm = $conn->prepare("
            UPDATE tb_permissao_usuario SET permissao_id = :funcao_id
            WHERE usuario_id = :user_id
        ");
        $stmtUpdatePerm->bindParam(':funcao_id', $funcao_id, PDO::PARAM_INT);
        $stmtUpdatePerm->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtUpdatePerm->execute();

        $conn->commit();

        // Se o e-mail foi alterado, envia o e-mail de confirmação para o novo endereço
        if ($emailChanged) {
            $sent = sendConfirmationEmail($nome, $submitted_email, $token, $conn);
            if (!$sent) {
                $_SESSION['error_msg'] = "Usuário atualizado, mas não foi possível enviar o e-mail de confirmação.";
                header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-usuario?id=' . $user_id);
                exit;
            }
            $_SESSION['msg'] = "Usuário atualizado com sucesso! Um e-mail de confirmação foi enviado para o novo endereço.";
        } else {
            $_SESSION['msg'] = "Usuário atualizado com sucesso!";
        }

        header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-usuario?id=' . $user_id);
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_msg'] = "Erro ao atualizar o usuário: " . $e->getMessage();
        header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-usuario?id=' . $user_id);
        exit;
    }
} else {
    $_SESSION['error_msg'] = "Método inválido.";
    header('Location: ' . INCLUDE_PATH_ADMIN . 'usuarios');
    exit;
}