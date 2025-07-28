<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $pagina_id = intval($_GET['id']);
        $uploadDir = __DIR__ . "/../../files/paginas/{$pagina_id}/";

        // Remover o produto do banco de dados
        $stmt = $conn->prepare("DELETE FROM tb_paginas_conteudo WHERE id = ?");
        $stmt->execute([$pagina_id]);

        // Deletar pasta e imagens do servidor
        function deleteFolder($folder) {
            if (!is_dir($folder)) return;
            $files = array_diff(scandir($folder), array('.', '..'));
            foreach ($files as $file) {
                $filePath = "$folder/$file";
                is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
            }
            rmdir($folder);
        }

        if (is_dir($uploadDir)) {
            deleteFolder($uploadDir);
        }

        echo json_encode(["status" => "success", "message" => "Página excluída com sucesso"]);
        $_SESSION['msg'] = 'Página excluída com sucesso.';
        exit;
    }