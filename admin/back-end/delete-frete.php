<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $frete_id = intval($_GET['id']);

        // Remover o frete do banco de dados
        $stmt = $conn->prepare("DELETE FROM tb_frete_dimensoes WHERE id = ?");
        $stmt->execute([$frete_id]);

        echo json_encode(["status" => "success", "message" => "Frete excluído com sucesso"]);
        $_SESSION['msg'] = 'Frete excluído com sucesso.';
        exit;
    }