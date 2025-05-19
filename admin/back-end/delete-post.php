<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $post_id = intval($_GET['id']);
        $uploadDir = __DIR__ . "/../../files/blog/{$post_id}/";

        // Remover o produto do banco de dados
        $stmt = $conn->prepare("DELETE FROM tb_blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);

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

        echo json_encode(["status" => "success", "message" => "Post excluído com sucesso"]);
        $_SESSION['msg'] = 'Post excluído com sucesso.';
        exit;
    }