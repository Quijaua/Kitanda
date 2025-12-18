<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['arquivo_id']) && isset($_GET['folder_id'])) {
        $arquivo_id = intval($_GET['arquivo_id']);
        $folder_id = intval($_GET['folder_id']);
        $uploadDir = __DIR__ . "/../../files/arquivos/{$folder_id}/";

        // $file = $conn->query("SELECT nome FROM tb_arquivos WHERE id = {$arquivo_id}")->fetchColumn();
        $stmt = $conn->query("SELECT nome FROM tb_arquivos WHERE id = {$arquivo_id}");
        $file = $stmt->fetchColumn();

        // Remover o produto do banco de dados
        $stmt = $conn->prepare("DELETE FROM tb_arquivos WHERE id = ?");
        $stmt->execute([$arquivo_id]);

        // Deletar pasta e imagens do servidor
        function deleteFolder($folder, $file) {
            if (!is_dir($folder)) return;

            $filePath = "$folder/$file";
            is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
        }

        if (is_dir($uploadDir)) {
            deleteFolder($uploadDir, $file);
        }

        echo json_encode(["status" => "success", "message" => "Arquivo excluído com sucesso"]);
        $_SESSION['msg'] = 'Arquivo excluído com sucesso.';
        exit;
    }