<?php
    include('config.php');

    $p = base64_decode($_POST['encodedCode']);
    
    // Consulta SQL
    $sql = "SELECT valor, pix_encodedImage, pix_payload, pix_expirationDate, forma_pagamento, boleto_barCode, boleto_identificationField, data_vencimento, data_criacao, link_boleto FROM tb_doacoes WHERE payment_id = :p";

    // Preparação da declaração PDO
    $stmt = $conn->prepare($sql);

    // Bind do valor do ID
    $stmt->bindParam(':p', $p);

    // Execução da consulta
    $stmt->execute();

    // Obtenção do resultado
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificação se a consulta retornou algum resultado
    if ($result) {
        echo json_encode([
            "valor" => $result['valor'],
            "pix_encodedImage" => $result['pix_encodedImage'],
            "pix_payload" => $result['pix_payload'],
            "pix_expirationDate" => $result['pix_expirationDate'] ? date("d/m/Y", strtotime($result['pix_expirationDate'])) : null,
            "forma_pagamento" => $result['forma_pagamento'],
            "boleto_barCode" => $result['boleto_barCode'],
            "boleto_identificationField" => $result['boleto_identificationField'],
            "data_vencimento" => $result['data_vencimento'] ? date("d/m/Y", strtotime($result['data_vencimento'])) : null,
            "data_criacao" => $result['data_criacao'] ? date("d/m/Y", strtotime($result['data_criacao'])) : null,
            "link_boleto" => $result['link_boleto']
        ]);
    } else {
        echo "Nenhum resultado encontrado.";
    }
