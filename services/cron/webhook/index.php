<?php
require '../../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable('../../../');
$dotenv->load();

// Acessa as variáveis de ambiente
$config = [
    'asaas_api_url' => $_ENV['ASAAS_API_URL'],
    'asaas_api_key' => $_ENV['ASAAS_API_KEY']
];

include('../../../config.php');

// Função para verificar o status do webhook
function verificarWebhook($config, $conn) {
    // Busca o ID do webhook na tabela tb_webhook
    $stmt = $conn->query("SELECT webhook_id FROM tb_webhook LIMIT 1");
    $webhook = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$webhook) {
        echo "Nenhum webhook encontrado.\n";
        return;
    }

    // URL para verificar o status do webhook
    $url = $config['asaas_api_url'] . "webhooks/" . $webhook['webhook_id'];

    // Configuração do cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'access_token: ' . $config['asaas_api_key']
        ],
    ]);

    // Executa a requisição cURL
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Erro: ' . curl_error($curl);
        curl_close($curl);
        return;
    }

    curl_close($curl);

    // Decodifica a resposta JSON
    $data = json_decode($response, true);

    // Verifica se há erro na resposta
    if (isset($data['errors'])) {
        foreach ($data['errors'] as $error) {
            echo "Erro: {$error['code']} - {$error['description']}\n";
        }
        return;
    }

    // Processa os dados da resposta
    if (!empty($data) && isset($data['id']) && $data['id'] === $webhook['webhook_id']) {
        $enabled = $data['enabled'];
        $interrupted = $data['interrupted'];

        // Atualiza o status do webhook na tabela
        $update_stmt = $conn->prepare("UPDATE tb_webhook SET enabled = :enabled, interrupted = :interrupted WHERE webhook_id = :webhook_id");
        $update_stmt->execute([
            ':enabled' => $enabled,
            ':interrupted' => $interrupted,
            ':webhook_id' => $webhook['webhook_id']
        ]);

        $status = $enabled ? 'Ativo' : 'Desativada';
        echo "O status do webhook foi atualizado para: $status.\n";
    } else {
        echo "Formato inesperado da resposta.\n";
        var_dump($data); // Para análise de problemas na resposta
    }
}

// Chama a função para verificar e atualizar o status do webhook
verificarWebhook($config, $conn);
?>