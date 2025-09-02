<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar-frete') {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
            exit;
        }

        $frete_id = $_POST['frete_id'];
        $nome = $_POST['nome'];
        $altura = $_POST['altura'];
        $largura = $_POST['largura'];
        $comprimento = $_POST['comprimento'];
        $peso = $_POST['peso'];

        try {
            // Verifica a conexão com o banco
            if (!$conn) {
                throw new Exception("Conexão inválida com o banco de dados.");
            }

            // Iniciar transação
            $conn->beginTransaction();

            // Inserindo a categoria no banco de dados
            $stmt = $conn->prepare("UPDATE tb_frete_dimensoes SET nome = :nome, altura = :altura, largura = :largura, comprimento = :comprimento, peso = :peso WHERE id = :id");
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':altura', $altura, PDO::PARAM_STR);
            $stmt->bindParam(':largura', $largura, PDO::PARAM_STR);
            $stmt->bindParam(':comprimento', $comprimento, PDO::PARAM_STR);
            $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
            $stmt->bindParam(':id', $frete_id, PDO::PARAM_INT);
            $stmt->execute();

            // Commit na transação
            $conn->commit();

            // Retorna um status de sucesso
            echo json_encode(['status' => 'success', 'message' => 'Medidas do frete atualizadas com sucesso.']);
            $_SESSION['msg'] = 'Medidas do frete atualizadas com sucesso.';
            exit;
        } catch (PDOException $e) {
            // Rollback em caso de erro
            $conn->rollBack();

            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar as medidas do frete.', 'error' => $e->getMessage()]);
            exit;
        }
    }