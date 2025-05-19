<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'criar-categoria') {

        // Obtendo os dados do formulário
        $nome = trim($_POST['nome']);
        $criado_por = $_SESSION['user_id'];

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
            exit;
        }

        try {
            // Verifica a conexão com o banco
            if (!$conn) {
                throw new Exception("Conexão inválida com o banco de dados.");
            }

            // Iniciar transação
            $conn->beginTransaction();

            // Inserindo a categoria no banco de dados
            $stmt = $conn->prepare("INSERT INTO tb_blog_categorias (nome, criado_por) 
                                    VALUES (:nome, :criado_por)");
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':criado_por', $criado_por, PDO::PARAM_INT);
            $stmt->execute();

            // Commit na transação
            $conn->commit();

            // Retorna um status de sucesso
            echo json_encode(['status' => 'success', 'message' => 'Categoria cadastrada com sucesso.']);
            $_SESSION['msg'] = 'Categoria cadastrada com sucesso.';
            exit;

        } catch (PDOException $e) {
            // Rollback em caso de erro
            $conn->rollBack();

            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar a categoria.', 'error' => $e->getMessage()]);
            exit;
        }
    }