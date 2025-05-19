<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $category_id = intval($_GET['id']);

        // Remover a categoria do banco de dados
        $stmt = $conn->prepare("DELETE FROM tb_blog_category_posts WHERE category_id = ?");
        $stmt->execute([$category_id]);

        // Remover a categoria do banco de dados
        $stmt = $conn->prepare("DELETE FROM tb_blog_category WHERE id = ?");
        $stmt->execute([$category_id]);

        echo json_encode(["status" => "success", "message" => "Categoria excluída com sucesso"]);
        $_SESSION['msg'] = 'Categoria excluída com sucesso.';
        exit;
    }