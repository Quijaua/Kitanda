<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require __DIR__ . '/../../vendor/autoload.php';

    function sendMail($name, $email, $project, $subject, $content) {
        // Informações para PHPMailer
        $smtp_host = $_ENV['SMTP_HOST'];
        $smtp_username = $_ENV['SMTP_USERNAME'];
        $smtp_password = $_ENV['SMTP_PASSWORD'];
        $smtp_secure = $_ENV['SMTP_SECURE'];
        $smtp_port = $_ENV['SMTP_PORT'];
        $smtp_email = $project['email'];
        $smtp_title = $project['name'];

        // Verificação de configuração SMTP
        if (
            empty($smtp_host) || empty($smtp_username) ||
            empty($smtp_password) || empty($smtp_secure) ||
            empty($smtp_port) || empty($smtp_email)
        ) {
            return "SMTP não configurado corretamente. E-mail não enviado para {$email}";
        }

        // Inicializar PHPMailer
        $mail = new PHPMailer(true);

        include('email-layouts/' . $content['layout'] . '.php');

        try {
            // Configurações do servidor SMTP
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = $smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp_username;
            $mail->Password   = $smtp_password;
            $mail->SMTPSecure = $smtp_secure;
            $mail->Port       = $smtp_port;

            // Configurações do email
            $mail->setFrom($smtp_email, $smtp_title);
            $mail->addAddress($email, $name);

            // Conteúdo do email
            $mail->isHTML(true); // Formato do email para HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;

            // Enviar o e-mail
            $mail->send();
        } catch (Exception $e) {
            die('Erro ao enviar o e-mail: ' . $mail->ErrorInfo);
        }
    }
?>