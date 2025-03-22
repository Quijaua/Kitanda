<?php
    // Caso prefira o .env apenas descomente o codigo e comente o "include('parameters.php');" acima
	// Carrega as variáveis de ambiente do arquivo .env

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
        
    include_once('../../config.php');

    // Tabela onde sera inserido o codigo
    $tabela = "tb_clientes";

    //Decodificando base64 e passando para $p
    $asaas_id = base64_decode($_POST['customerId']);

    // Gera um valor aleatório seguro em hexadecimal
    $random_value = bin2hex(random_bytes(16));
    $hash = hash('sha256', $asaas_id . $random_value); // Combinação do ID do usuário e valor aleatório
    $link_magico = INCLUDE_PATH_ADMIN . 'back-end/ativar-conta.php?token=' . $hash; // URL do link mágico

    $query = "UPDATE $tabela SET magic_link = :magic_link WHERE asaas_id = :asaas_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':magic_link', $hash, PDO::PARAM_STR);
    $stmt->bindParam(':asaas_id', $asaas_id, PDO::PARAM_STR);
    $stmt->execute();

    // Informacoes do usuario
    $sql = "SELECT nome, email FROM tb_checkout WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', 1, PDO::PARAM_INT);
    $stmt->execute();

    // Recuperar os resultados do usuario
    $instituicao = $stmt->fetch(PDO::FETCH_ASSOC);

    // Informacoes do usuario
    $sql = "SELECT * FROM $tabela WHERE asaas_id = :asaas_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':asaas_id', $asaas_id, PDO::PARAM_STR);
    $stmt->execute();

    // Recuperar os resultados do usuario
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if (!empty($resultados)) {
        $primeiroResultado = $resultados[0]; // Acessar o primeiro resultado do array

        $nome = $primeiroResultado['nome'];
        $email = $primeiroResultado['email'];
    }

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require './../../lib/vendor/autoload.php';

    // Crie uma nova instância do PHPMailer
    $mail = new PHPMailer(true);

    // Recupera a mensagem do DB
    $tabela_1 = "tb_mensagens";
    $sql_1 = "SELECT welcome_email FROM $tabela_1";
    $stmt_1 = $conn->prepare($sql_1);
    $stmt_1->execute();
    $resultado = $stmt_1->fetch(PDO::FETCH_ASSOC);
    $mensagem = $resultado['welcome_email'];

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

        // Define o remetente e destinatário
        $mail->setFrom($smtp_from, 'Atendimento - ' . $instituicao['nome']);
        $mail->addReplyTo($instituicao['email'], 'Atendimento - ' . $instituicao['nome']);
        $mail->addAddress($email, $nome);

        // Configurações do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Ativar sua conta';
        $mail->Body = 'Olá ' . $nome . ',<br>' . $mensagem . '<br><br>Clique no link a seguir para ativar sua conta: <a href="' . $link_magico . '">Ativar Conta</a><br><br>Ou<br><br>Cole esse link no seu navegador:<br>' . $link_magico . '';

        // Envia o e-mail
        $mail->send();
        echo json_encode(["msg"=>"E-mail enviado com sucesso!"]);
    } catch (Exception $e) {
        echo json_encode(["msg"=>"Erro ao enviar o e-mail: " . $mail->ErrorInfo]);
    }