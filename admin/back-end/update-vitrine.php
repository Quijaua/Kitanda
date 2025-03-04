<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id']);
        $vitrine = intval($_POST['vitrine']);

        $stmt = $conn->prepare("UPDATE tb_produtos SET vitrine = ? WHERE id = ?");
        $success = $stmt->execute([$vitrine, $id]);

        echo json_encode(["success" => $success]);
    }