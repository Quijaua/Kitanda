<?php
session_start();
include('../../config.php');

// Valida o ID recebido via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_msg'] = "ID da função inválido.";
    header('Location: ' . INCLUDE_PATH_ADMIN . 'funcoes');
    exit;
}
$funcao_id = intval($_GET['id']);

// Verifica se o formulário foi enviado via POST e se o botão correto foi acionado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnEditFunction'])) {
    // Captura e sanitiza o nome da função
    $nome = trim($_POST['nome']);

    try {
        // Inicia a transação para garantir a consistência dos dados
        $conn->beginTransaction();

        // Atualiza o nome da função na tabela tb_funcoes
        $stmt = $conn->prepare("UPDATE tb_funcoes SET nome = :nome WHERE id = :id");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':id', $funcao_id, PDO::PARAM_INT);
        $stmt->execute();

        // Remove as permissões atuais da função na tabela tb_permissao_funcao
        $stmt = $conn->prepare("DELETE FROM tb_permissao_funcao WHERE funcao_id = :id");
        $stmt->bindParam(':id', $funcao_id, PDO::PARAM_INT);
        $stmt->execute();

        // Mapeamento dos tipos de permissão para os IDs definidos na tabela tb_acoes
        $permissionsMapping = [
            'only_own' => 1,
            'read'     => 2,
            'create'   => 3,
            'update'   => 4,
            'delete'   => 5,
        ];

        // Verifica se foram enviadas novas permissões e insere cada uma
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            foreach ($_POST['permissions'] as $pagina_id => $perms) {
                foreach ($perms as $permissionKey => $value) {
                    // Se o checkbox estiver marcado e o tipo de permissão existir no mapeamento
                    if ($value == 1 && isset($permissionsMapping[$permissionKey])) {
                        $acao_id = $permissionsMapping[$permissionKey];

                        // Insere na tabela tb_permissao_funcao a nova relação entre página, função e ação
                        $stmt2 = $conn->prepare("
                            INSERT INTO tb_permissao_funcao (pagina_id, funcao_id, acao_id) 
                            VALUES (:pagina_id, :funcao_id, :acao_id)
                        ");
                        $stmt2->bindParam(':pagina_id', $pagina_id, PDO::PARAM_INT);
                        $stmt2->bindParam(':funcao_id', $funcao_id, PDO::PARAM_INT);
                        $stmt2->bindParam(':acao_id', $acao_id, PDO::PARAM_INT);
                        $stmt2->execute();
                    }
                }
            }
        }

        // Finaliza a transação se tudo ocorrer corretamente
        $conn->commit();

        $_SESSION['msg'] = 'Função e permissões atualizadas com sucesso!';
        header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-funcao?id=' . $funcao_id);
        exit;
    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação e retorna uma mensagem de erro
        $conn->rollBack();
        $_SESSION['error_msg'] = 'Erro ao atualizar a função: ' . $e->getMessage();
        header('Location: ' . INCLUDE_PATH_ADMIN . 'editar-funcao?id=' . $funcao_id);
        exit;
    }
} else {
    $_SESSION['error_msg'] = "Método inválido.";
    header('Location: ' . INCLUDE_PATH_ADMIN . 'funcoes');
    exit;
}