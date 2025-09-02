<?php
session_start();
include('../../config.php');

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['user_id']) || getNomePermissao($_SESSION['user_id'], $conn) !== 'Administrador') {
    echo 'Você não tem permissão para executar essa ação.';
    exit;
}

header('Content-Type: application/json');

// Apenas requisições GET serão aceitas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id'])) {
        echo 'ID do vendedora não informada.';
        exit;
    }

    $id = intval($_GET['id']);

    // Pega o status atual
    $stmt = $conn->prepare("SELECT status FROM tb_clientes WHERE id = ?");
    $stmt->execute([$id]);
    $vendedora = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vendedora) {
        echo 'Vendedora não encontrada.';
        exit;
    }

    // Inverte o status: se for 1 vira 0, se for 0 vira 1
    $novo_status = $vendedora['status'] == 1 ? 0 : 1;

    // Atualiza o status
    $stmt = $conn->prepare("UPDATE tb_clientes SET status = ? WHERE id = ?");
    $success = $stmt->execute([$novo_status, $id]);

    if ($success) {
        $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
        $_SESSION['msg'] = 'Status do professor atualizado com sucesso.';

        // Redireciona para a página de listagem
        header('Location: ' . INCLUDE_PATH_ADMIN . 'vendedoras');
    } else {
        echo 'Erro ao atualizar status do professor.';
        exit;
    }

}

echo 'Método não permitido.';
exit;