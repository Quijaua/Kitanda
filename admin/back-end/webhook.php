<?php
// Carrega as variáveis de ambiente do arquivo .env
require dirname(dirname(__DIR__)).'/vendor/autoload.php';
require_once dirname(dirname(__DIR__)).'/back-end/functions.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(__DIR__)));
$dotenv->load();
$client = new GuzzleHttp\Client();

// Acessa as variáveis de ambiente
$config['asaas_api_url'] = $_ENV['ASAAS_API_URL'];
$config['asaas_api_key'] = $_ENV['ASAAS_API_KEY'];

// Inclui o arquivo de configuração com as credenciais
include('../../back-end/config.php');
include('../../config.php');

session_start();
ob_start();

// Configura o fuso horário para São Paulo, Brasil
date_default_timezone_set('America/Sao_Paulo');

if (isset($_POST['btnAddWebhook'])) {
    // $url = INCLUDE_PATH . 'services/webhook/index.php';
    $url = INCLUDE_PATH.'services/webhook/';

    // Dados do webhook a ser criado
    $data = array(
        'url' => $url,
        'name' => $_POST['webhook_name'],
        'email' => $_POST['email'],
        'enabled' => ($_POST['enabled'] == 1) ? true : false,
        'interrupted' => ($_POST['interrupted'] == 1) ? true : false,
        'sendType' => $_POST['send_type'],
        'events' => array(
            'PAYMENT_CREATED',
            'PAYMENT_AWAITING_RISK_ANALYSIS',
            'PAYMENT_APPROVED_BY_RISK_ANALYSIS',
            'PAYMENT_REPROVED_BY_RISK_ANALYSIS',
            'PAYMENT_AUTHORIZED',
            'PAYMENT_UPDATED',
            'PAYMENT_CONFIRMED',
            'PAYMENT_RECEIVED',
            'PAYMENT_CREDIT_CARD_CAPTURE_REFUSED',
            'PAYMENT_ANTICIPATED',
            'PAYMENT_OVERDUE',
            'PAYMENT_DELETED',
            'PAYMENT_RESTORED',
            'PAYMENT_REFUNDED',
            'PAYMENT_PARTIALLY_REFUNDED',
            'PAYMENT_REFUND_IN_PROGRESS',
            'PAYMENT_REFUND_DENIED',
            'PAYMENT_RECEIVED_IN_CASH_UNDONE',
            'PAYMENT_CHARGEBACK_REQUESTED',
            'PAYMENT_CHARGEBACK_DISPUTE',
            'PAYMENT_AWAITING_CHARGEBACK_REVERSAL',
            'PAYMENT_DUNNING_RECEIVED',
            'PAYMENT_DUNNING_REQUESTED',
            'PAYMENT_BANK_SLIP_VIEWED',
            'PAYMENT_CHECKOUT_VIEWED'
        ),
    );

    // Inicializa a sessão curl
    $curl = curl_init();

    // Configuração das opções da requisição curl para criar o webhook
    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url'] . 'webhooks', // Endpoint para criação de webhooks
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data), // Converte os dados para JSON
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'access_token: ' . $config['asaas_api_key'], // Sua chave de acesso à API Asaas
            'User-Agent: ' . $application_name // Nome da sua aplicação
        ),
    ));

    // Executa a requisição curl para criar o webhook
    $response = curl_exec($curl);

    // Verifica se houve algum erro na requisição
    if (curl_errno($curl)) {
        echo 'Erro:' . curl_error($curl);
        exit(); // Encerra a execução em caso de erro
    }

    // Fecha a sessão curl
    curl_close($curl);

    // Decodifica a resposta JSON
    $retorno = json_decode($response, true);

    // Verificação se houve erro na resposta da API
    if (isset($retorno['errors'])) {
        // Exibe o primeiro erro retornado pela API
        echo '<strong>Erro ao criar webhook:</strong><br>';
        foreach ($retorno['errors'] as $error) {
            echo 'Código: ' . $error['code'] . '<br>';
            echo 'Descrição: ' . $error['description'] . '<br>';
        }
        exit();
    }

    // Verifica se o webhook foi criado com sucesso
    if (isset($retorno)) {
        $tabela = 'tb_webhook';

        $stmt = $conn->prepare("INSERT INTO $tabela (webhook_id, enabled, name, email, interrupted, send_type) VALUES (:webhook_id, :enabled, :name, :email, :interrupted, :send_type)");
        
        // Bind dos parâmetros
        $stmt->bindParam(':webhook_id', $retorno['id'], PDO::PARAM_STR);
        $stmt->bindParam(':enabled', $_POST['enabled'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $_POST['webhook_name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':interrupted', $_POST['interrupted'], PDO::PARAM_INT);
        $stmt->bindParam(':send_type', $_POST['send_type'], PDO::PARAM_STR);

        // Executando o update
        $stmt->execute();

        // Exibir a modal após salvar as informações
        $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
        $_SESSION['msg'] = 'Webhook criado com sucesso!';

        //Voltar para a pagina do formulario
        header('Location: ' . INCLUDE_PATH_ADMIN . 'webhook');
    } else {
        echo 'Erro ao criar webhook: ' . $response;
        header('Location: ' . INCLUDE_PATH_ADMIN . 'webhook');
        exit();
    }
} else if (isset($_POST['btnUpdWebhook'])) {
    // Dados do webhook a ser editado
    $data = array(
        'name' => $_POST['webhook_name'], // Nome do webhook
        'email' => $_POST['email'], // E-mail associado ao webhook
        'enabled' => ($_POST['enabled'] == 1) ? true : false, // Habilitado (true/false)
        'interrupted' => ($_POST['interrupted'] == 1) ? true : false, // Interrompido (true/false)
        'sendType' => $_POST['send_type'], // Tipo de envio
    );

    // Endpoint para editar um webhook específico
    $edit_url = $config['asaas_api_url'] . 'webhooks/' . $_POST['webhook_id'];

    // Inicializa a sessão curl
    $curl = curl_init();

    // Configuração das opções da requisição curl para editar o webhook
    curl_setopt_array($curl, array(
        CURLOPT_URL => $edit_url, // Endpoint para edição de webhooks
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => json_encode($data), // Converte os dados para JSON
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'access_token: ' . $config['asaas_api_key'], // Sua chave de acesso à API Asaas
            'User-Agent: ' . $application_name // Nome da sua aplicação
        ),
    ));

    // Executa a requisição curl para editar o webhook
    $response = curl_exec($curl);

    // Verifica se houve algum erro na requisição
    if (curl_errno($curl)) {
        echo 'Erro:' . curl_error($curl);
        exit(); // Encerra a execução em caso de erro
    }

    // Fecha a sessão curl
    curl_close($curl);

    // Decodifica a resposta JSON
    $retorno = json_decode($response, true);

    // Verificação se houve erro na resposta da API
    if (isset($retorno['errors'])) {
        // Exibe o primeiro erro retornado pela API
        echo '<strong>Erro ao criar webhook:</strong><br>';
        foreach ($retorno['errors'] as $error) {
            echo 'Código: ' . $error['code'] . '<br>';
            echo 'Descrição: ' . $error['description'] . '<br>';
        }
        exit();
    }

    // Verifica se o webhook foi editado com sucesso
    if (isset($retorno)) {
        // Atualiza informações do webhook no banco de dados
        $tabela = 'tb_webhook';

        $stmt = $conn->prepare("UPDATE $tabela SET enabled = :enabled, name = :name, email = :email, interrupted = :interrupted, send_type = :send_type WHERE webhook_id = :webhook_id");

        // Bind dos parâmetros
        $stmt->bindParam(':webhook_id', $_POST['webhook_id'], PDO::PARAM_STR);
        $stmt->bindParam(':enabled', $_POST['enabled'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $_POST['webhook_name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':interrupted', $_POST['interrupted'], PDO::PARAM_INT);
        $stmt->bindParam(':send_type', $_POST['send_type'], PDO::PARAM_STR);

        // Executando o update
        $stmt->execute();

        // Exibir a modal após salvar as informações
        $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
        $_SESSION['msg'] = 'Webhook editado com sucesso!';

        // Redireciona para a página de gerenciamento de webhooks
        header('Location: ' . INCLUDE_PATH_ADMIN . 'webhook');
    } else {
        echo 'Erro ao editar webhook: ' . $response;
        exit();
    }
} else if (isset($_POST['btnDltWebhook'])) {
    // Endpoint para deletar um webhook específico na API Asaas
    $delete_url = $config['asaas_api_url'] . 'webhooks/' . $_POST['webhook_id'];

    // Inicializa a sessão curl
    $curl = curl_init();

    // Configuração das opções da requisição curl para deletar o webhook na API Asaas
    curl_setopt_array($curl, array(
        CURLOPT_URL => $delete_url, // Endpoint para deleção de webhooks
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'access_token: ' . $config['asaas_api_key'], // Sua chave de acesso à API Asaas
            'User-Agent: ' . $application_name // Nome da sua aplicação
        ),
    ));

    // Executa a requisição curl para deletar o webhook na API Asaas
    $response = curl_exec($curl);

    // Verifica se houve algum erro na requisição
    if (curl_errno($curl)) {
        echo 'Erro:' . curl_error($curl);
        exit(); // Encerra a execução em caso de erro na API Asaas
    }

    // Fecha a sessão curl
    curl_close($curl);

    // Decodifica a resposta JSON
    $retorno = json_decode($response, true);

    // Verificação se houve erro na resposta da API
    if (isset($retorno['errors'])) {
        // Exibe o primeiro erro retornado pela API
        echo '<strong>Erro ao criar webhook:</strong><br>';
        foreach ($retorno['errors'] as $error) {
            echo 'Código: ' . $error['code'] . '<br>';
            echo 'Descrição: ' . $error['description'] . '<br>';
        }
        exit();
    }

    if ($retorno['deleted'] == true) {
        // Deletar do banco de dados
        $tabela = 'tb_webhook';
        $stmt = $conn->prepare("DELETE FROM $tabela WHERE webhook_id = :webhook_id");

        // Bind do parâmetro
        $stmt->bindParam(':webhook_id', $_POST['webhook_id'], PDO::PARAM_STR);

        // Executando o delete
        if ($stmt->execute()) {
            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'Webhook deletado com sucesso!';
    
            // Redireciona para a página de gerenciamento de webhooks
            header('Location: ' . INCLUDE_PATH_ADMIN . 'webhook');
        } else {
            echo 'Erro ao deletar webhook do banco de dados';
            exit();
        }
    } else {
        // Ocorreu algum erro ao deletar o webhook na API Asaas
        echo 'Erro ao deletar webhook na API Asaas: ' . $response;
        exit();
    }
}