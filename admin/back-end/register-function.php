<?php
    session_start();
    include('../../config.php');

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAddFunction'])) {
        // Captura e sanitiza o nome da função
        $nome = trim($_POST['nome']);

        try {
            // Inicia uma transação para garantir a consistência dos dados
            $conn->beginTransaction();

            // Insere o nome da função na tabela tb_funcoes
            $stmt = $conn->prepare("INSERT INTO tb_funcoes (nome) VALUES (:nome)");
            $stmt->bindParam(':nome', $nome);
            $stmt->execute();
            $funcao_id = $conn->lastInsertId();

            // Mapeamento dos tipos de permissão para os IDs definidos
            $permissionsMapping = [
                'only_own' => 1,
                'read'     => 2,
                'create'   => 3,
                'update'   => 4,
                'delete'   => 5,
            ];

            // Verifica se foram enviadas permissões e itera sobre elas
            if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
                foreach ($_POST['permissions'] as $pagina_id => $perms) {
                    foreach ($perms as $permissionKey => $value) {
                        // Se o checkbox estiver marcado e o tipo de permissão existir no mapeamento
                        if ($value == 1 && isset($permissionsMapping[$permissionKey])) {
                            $acao_id = $permissionsMapping[$permissionKey];

                            // Insere na tabela tb_permissao_funcao a relação entre página, função e ação
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

            $_SESSION['msg'] = 'Função e permissões cadastradas com sucesso!';
            header('Location: ' . INCLUDE_PATH_ADMIN . 'funcoes');
        } catch (Exception $e) {
            // Em caso de erro, desfaz as alterações
            $conn->rollBack();

            $_SESSION['error_msg'] = 'Erro ao cadastrar a função: ' . $e->getMessage();
            header('Location: ' . INCLUDE_PATH_ADMIN . 'criar-funcao');
        }
    } else {
        $_SESSION['error_msg'] = "Método inválido.";
        header('Location: ' . INCLUDE_PATH_ADMIN);
    }