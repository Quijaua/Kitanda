<?php
require '../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable('../../');
$dotenv->load();

// Acessa as variáveis de ambiente
$config['asaas_api_url'] = $_ENV['ASAAS_API_URL'];
$config['asaas_api_key'] = $_ENV['ASAAS_API_KEY'];
$config['groupname'] = $_ENV['GROUPNAME'];

session_start();
ob_start();
include('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capture the incoming webhook data
    $data = json_decode(file_get_contents("php://input"), true);

    // Perform actions based on the data
    if ($data) {
        $customer_id = $data['payment']['customer'];

        // Se o groupName estiver definido, faz a requisição para a API do Asaas
        if ($config['groupname']) {
            $customerData = getCustomerDataFromAsaas($customer_id, $config);

            if ($customerData) {
                $customerGroups = $customerData['groups'] ?? [];

                foreach ($customerGroups as $group) {
                    if ($group['name'] == $config['groupname']) {
                        processPaymentData($data, $conn);
                        break;
                    }
                }
            }
        } else {
            // Se não houver groupName, processa todos os resultados do webhook
            processPaymentData($data, $conn);
        }
    } else {
        // Handle invalid data or errors
        http_response_code(400); // Bad Request
        return false;
    }
} else {
    echo "Method not allowed.";
    return false;
}

/**
 * Função para obter dados do cliente da API do Asaas
 */
function getCustomerDataFromAsaas($customer_id, $config) {
    $url = $config['asaas_api_url']."customers/$customer_id";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "access_token: ".$config['asaas_api_key']
    ));
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Função para processar os dados do pagamento e inseri-los no banco de dados
 */
function processPaymentData($data, $conn) {
    include('../../back-end/config.php');

    $event = $data["event"] ?? NULL;
    $payment_id = $data["payment"]["id"] ?? NULL;

    if ($event == "PAYMENT_RECEIVED") {
        $stmt = $conn->prepare("SELECT * FROM tb_pedidos WHERE transacao_id = ?");
        $stmt->execute([$payment_id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        $pedido['data_pagamento'] = $data["payment"]["paymentDate"];

        $stmt = $conn->prepare("SELECT * FROM tb_clientes WHERE id = ?");
        $stmt->execute([$pedido['usuario_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $usuario['endereco'] = $usuario['endereco'] . ', ' . $usuario['numero'];
        if (!empty($usuario['complemento'])) {
            $usuario['endereco'] .= ' - ' . $usuario['complemento'];
        }
        $usuario['endereco'] .= ', ' . $usuario['municipio'] . ' - ' . $usuario['cidade'] . '/' . $usuario['uf'] . ' - ' . $usuario['cep'];

        $stmt = $conn->prepare("SELECT p.*, pi.imagem AS produto_imagem FROM tb_pedido_itens p LEFT JOIN tb_produto_imagens pi ON p.produto_id = pi.produto_id WHERE p.pedido_id = ?");
        $stmt->execute([$pedido['id']]);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($produtos as &$produto) {
            $produto['imagem'] = !empty($produto['produto_imagem'])
                                 ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['produto_id'] . "/" . $produto['produto_imagem'])
                                 : INCLUDE_PATH . "assets/preview-image/product.jpg";
        }
        unset($produto);

        // Link do pedido
        $pedido_link = INCLUDE_PATH . "user/compra?pedido=" . $pedido['pedido_id'];

        // Enviar e-mail para finalizar o pagamento
        $subject = "Recebemos o pagamento do seu pedido #{$pedido['pedido_id']}";
        $content = array("layout" => "pagamento-recebido", "content" => array("name" => $usuario['nome'], "usuario" => $usuario, "pedido" => $pedido, "produtos" => $produtos, "link" => $pedido_link));
        sendMail($usuario['nome'], $usuario['email'], $project, $subject, $content);

        // $pedido['codigo_rastreio'] = 'RD123456789PT';
        // $pedido['transportadora'] = 'Correios';
        // $pedido['data_envio'] = $content['content']['pedido']['data_pagamento'];
        // $pedido['prazo_entrega'] = date('d/m/Y', strtotime($content['content']['pedido']['data_pagamento'] . ' +8 days'));

        // // Enviar e-mail para finalizar o pagamento
        // $subject = "Seu pedido #{$pedido['pedido_id']} está a caminho";
        // $content = array("layout" => "produto-enviado", "content" => array("name" => $usuario['nome'], "usuario" => $usuario, "pedido" => $pedido, "produtos" => $produtos, "link" => $pedido_link));
        // sendMail($usuario['nome'], $usuario['email'], $project, $subject, $content);

        // // Enviar e-mail para finalizar o pagamento
        // $subject = "Seu pedido #{$pedido['pedido_id']} foi entregue";
        // $content = array("layout" => "pedido-entregue", "content" => array("name" => $usuario['nome'], "usuario" => $usuario, "pedido" => $pedido, "produtos" => $produtos, "link" => $pedido_link));
        // sendMail($usuario['nome'], $usuario['email'], $project, $subject, $content);
    }

    $payment_date_created = $data["payment"]["dateCreated"] ?? NULL;
    $customer_id = $data["payment"]["customer"] ?? NULL;
    $subscription_id = $data["payment"]["subscription"] ?? NULL;
    $value = $data["payment"]["value"] ?? NULL;
    $net_value = $data["payment"]["netValue"] ?? NULL;
    $description = $data["payment"]["description"] ?? NULL;
    $billing_type = $data["payment"]["billingType"] ?? NULL;
    $confirmed_date = $data["payment"]["confirmedDate"] ?? NULL;
    $credit_card_number = $data["payment"]["creditCard"]["creditCardNumber"] ?? NULL;
    $credit_card_brand = $data["payment"]["creditCard"]["creditCardBrand"] ?? NULL;
    $credit_card_token = $data["payment"]["creditCard"]["creditCardToken"] ?? NULL;
    $status = $data["payment"]["status"] ?? NULL;
    $credit_date = $data["payment"]["creditDate"] ?? NULL;
    $estimated_credit_date = $data["payment"]["estimatedCreditDate"] ?? NULL;
    $webhook_date_created = $data["dateCreated"] ?? NULL;
    $data_pagamento = $data["payment"]["paymentDate"] ?? NULL;

    // Atualize o item no banco de dados
    $sql = "UPDATE tb_pedidos SET status = :status, data_pagamento = :data_pagamento WHERE transacao_id = :transacao_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':data_pagamento', $data_pagamento);
    $stmt->bindParam(':transacao_id', $payment_id);

    $stmt->execute();

    // TENTA IDENTIFICAR A TRANSAÇÃO PELO ID, SE ENCONTRAR EXECUTA O UPDATE, SENÃO EXECUTA O CREATE
    $tabela = "tb_transacoes";
    $sql = "SELECT event, payment_date_created, value, net_value, confirmed_date, credit_card_number, credit_card_brand, credit_card_token, status, credit_date, estimated_credit_date, webhook_date_created FROM $tabela WHERE payment_id = :payment_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':payment_id', $payment_id);
    $stmt->execute();
    $transacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transacao) {
        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET event = :event, payment_date_created = :payment_date_created, value = :value, net_value = :net_value, confirmed_date = :confirmed_date, credit_card_number = :credit_card_number, credit_card_brand = :credit_card_brand, credit_card_token = :credit_card_token, status = :status, credit_date = :credit_date, estimated_credit_date = :estimated_credit_date, webhook_date_created = :webhook_date_created WHERE payment_id = :payment_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event', $event);
        $stmt->bindParam(':payment_date_created', $payment_date_created);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':net_value', $net_value);
        $stmt->bindParam(':confirmed_date', $confirmed_date);
        $stmt->bindParam(':credit_card_number', $credit_card_number);
        $stmt->bindParam(':credit_card_brand', $credit_card_brand);
        $stmt->bindParam(':credit_card_token', $credit_card_token);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':credit_date', $credit_date);
        $stmt->bindParam(':estimated_credit_date', $estimated_credit_date);
        $stmt->bindParam(':webhook_date_created', $webhook_date_created);
        $stmt->bindParam(':payment_id', $payment_id);

        $stmt->execute();
    } else {
        // CREATE
        $sql = "INSERT INTO $tabela (event, payment_id, payment_date_created, customer_id, subscription_id, value, net_value, description, billing_type, confirmed_date, credit_card_number, credit_card_brand, credit_card_token, status, credit_date, estimated_credit_date, webhook_date_created) VALUES (:event, :payment_id, :payment_date_created, :customer_id, :subscription_id, :value, :net_value, :description, :billing_type, :confirmed_date, :credit_card_number, :credit_card_brand, :credit_card_token, :status, :credit_date, :estimated_credit_date, :webhook_date_created)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event', $event, PDO::PARAM_STR);
        $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_STR);
        $stmt->bindParam(':payment_date_created', $payment_date_created, PDO::PARAM_STR);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindParam(':subscription_id', $subscription_id, PDO::PARAM_STR);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $stmt->bindParam(':net_value', $net_value, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':billing_type', $billing_type, PDO::PARAM_STR);
        $stmt->bindParam(':confirmed_date', $confirmed_date, PDO::PARAM_STR);
        $stmt->bindParam(':credit_card_number', $credit_card_number, PDO::PARAM_STR);
        $stmt->bindParam(':credit_card_brand', $credit_card_brand, PDO::PARAM_STR);
        $stmt->bindParam(':credit_card_token', $credit_card_token, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':credit_date', $credit_date, PDO::PARAM_STR);
        $stmt->bindParam(':estimated_credit_date', $estimated_credit_date, PDO::PARAM_STR);
        $stmt->bindParam(':webhook_date_created', $webhook_date_created, PDO::PARAM_STR);

        $stmt->execute();
    }
}
?>