<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar-categoria-post') {
        $categoria_id = $_POST['categoria_id'];
        $nome = trim($_POST['nome']);

        try {
            if (!$conn) {
                throw new Exception("Conexão inválida com o banco de dados.");
            }

            $conn->beginTransaction();

            $stmt = $conn->prepare("UPDATE tb_blog_categorias SET nome = :nome WHERE id = :id");
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':id', $categoria_id, PDO::PARAM_INT);
            $stmt->execute();

            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'Categoria atualizada com sucesso.']);
            $_SESSION['msg'] = 'Categoria atualizada com sucesso.';
            exit;

        } catch (PDOException $e) {
            $conn->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar a categoria.', 'error' => $e->getMessage()]);
            exit;
        }
    }