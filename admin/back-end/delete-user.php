<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $usuario_id = intval($_GET['id']);

        // Remover o usuário do banco de dados
        $stmt = $conn->prepare("DELETE FROM tb_clientes WHERE id = ?");
        $stmt->execute([$usuario_id]);

        echo json_encode(["status" => "success", "message" => "Usuário excluído com sucesso"]);
        $_SESSION['msg'] = 'Usuário excluído com sucesso.';
        exit;
    }