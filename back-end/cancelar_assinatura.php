<?php
    session_start();
    ob_start();

    include_once('../config.php');

    $tabela_mensagem = "tb_mensagens";
    $id = '1';
    $stmt_mensagem = $conn->prepare("SELECT unregister_message FROM $tabela_mensagem WHERE id = :id");
    $stmt_mensagem->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_mensagem->execute();
    $result_mensagem = $stmt_mensagem->fetch(PDO::FETCH_ASSOC);

    //Apagar Card
    $payment_id = filter_input(INPUT_GET, 'payment_id');

    require dirname(__DIR__).'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();

    // Acessa as variáveis de ambiente
    $config['asaas_api_url'] = $_ENV['ASAAS_API_URL'];
    $config['asaas_api_key'] = $_ENV['ASAAS_API_KEY'];
    $config['recaptcha_token'] = $_ENV['RECAPTCHA_CHAVE_SECRETA'];

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url'] . 'subscriptions/' . $payment_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'access_token: ' . $config['asaas_api_key'],
            'User-Agent: '.$application_name
        )
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    $retorno = json_decode($response, true);
    
    if (isset($retorno['deleted']) && $retorno['deleted']) {
        if(!empty($payment_id)) {
            // Nome da tabela para a busca
            $tabela = 'tb_doacoes';

            // Consulta SQL para excluir a linha
            $stmt = $conn->prepare("DELETE FROM $tabela WHERE payment_id = :payment_id");
            
            // Bind dos parâmetros
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_STR);
            
            // Executar a consulta
            $stmt->execute();
            
            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = isset($result_mensagem['unregister_message']) ? $result_mensagem['unregister_message'] : 'A cobrança foi deletada com sucesso com sucesso!';

            header("Location: " . INCLUDE_PATH_USER);
        } else {
            // Mensagem de falha
            $_SESSION['msgcad'] = 'É necessário selecionar uma cobrança!';
            header("Location: " . INCLUDE_PATH_USER);
        }
    } else {
        $_SESSION['msgcad'] = 'Erro ao excluir a cobrança.';
        header("Location: " . INCLUDE_PATH_USER);
    }