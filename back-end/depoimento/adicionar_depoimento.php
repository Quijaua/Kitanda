<?php

    session_start();
    ob_start();

    include_once('../config.php');

    $criado_por = $_POST['criado_por'];
    $produto_id = $_POST['produto_id'];
    $depoimento = $_POST['depoimento'];

    $stmt = $conn->prepare("INSERT INTO tb_depoimentos (criado_por, produto_id, depoimento) VALUES (?, ?, ?)");
    $stmt->execute([$criado_por, $produto_id, $depoimento]);

    echo json_encode(["status" => "sucesso", "mensagem" => "Depoimento adicionado com sucesso!"]);