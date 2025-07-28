<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    // var_dump($_FILES);
    // exit;

    function uploadImagem($pagina_id, $conn) {
        $tabela = 'tb_paginas_conteudo';
        $_UP['pasta'] = "../../files/paginas/$pagina_id/";
        $_UP['tamanho'] = 1024 * 1024 * 2; // 2MB
        $_UP['extensoes'] = array('png', 'jpg', 'jpeg', 'webp');
        $_UP['renomeia'] = true;
        $_UP['erros'] = [
            'Não houve erro',
            'O arquivo no upload é maior que o limite do PHP',
            'O arquivo ultrapassa o limite de tamanho especificado no HTML',
            'O upload do arquivo foi feito parcialmente',
            'Não foi feito o upload do arquivo'
        ];

        if (!file_exists($_UP['pasta'])) {
            mkdir($_UP['pasta'], 0777, true);
        }

        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($extensao), $_UP['extensoes'])) {
            echo json_encode(['status' => 'error', 'message' => 'A extensão da imagem é inválida.']);
            exit;
        }

        if ($_FILES['imagem']['size'] > $_UP['tamanho']) {
            echo json_encode(['status' => 'error', 'message' => 'Arquivo muito grande.']);
            exit;
        }

        //O arquivo passou em todas as verificações, hora de tentar move-lo para a pasta foto
        //Primeiro verifica se deve trocar o nome do arquivo
        if ($_UP['renomeia'] == true) {
            // Pega a extensão do arquivo original
            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);

            // Cria um nome baseado no UNIX TIMESTAMP atual, um identificador único, e a extensão original do arquivo
            $nome_final = date('YmdHis') . '_imagem.' . $extensao;
        } else {
            // Mantém o nome original do arquivo
            $nome_final = date('YmdHis') . "_" . $_FILES['imagem']['name'];
        }

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $_UP['pasta'] . $nome_final)) {
            $stmt = $conn->prepare("UPDATE $tabela SET imagem = :img WHERE id = :pagina_id");
            $stmt->bindParam(':img', $nome_final, PDO::PARAM_STR);
            $stmt->bindParam(':pagina_id', $pagina_id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao fazer upload da imagem.']);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'criar-pagina') {
        // Obtendo os dados do formulário
        $titulo = trim($_POST['titulo']);
        $slug = trim($_POST['slug']);
        $conteudo = trim($_POST['conteudo']);
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

            // Inserindo o pagina no banco de dados
            $stmt = $conn->prepare("INSERT INTO tb_paginas_conteudo (titulo, slug, conteudo, criado_por) 
                                    VALUES (:titulo, :slug, :conteudo, :criado_por)");
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->bindParam(':conteudo', $conteudo, PDO::PARAM_STR);
            $stmt->bindParam(':criado_por', $criado_por, PDO::PARAM_INT);
            $stmt->execute();
            $pagina_id = $conn->lastInsertId();

            if (!empty($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                // Salvar imagem
                uploadImagem($pagina_id, $conn);
            }

            // Commit na transação
            $conn->commit();

            // Retorna um status de sucesso
            echo json_encode(['status' => 'success', 'message' => 'Página cadastrada com sucesso.']);
            $_SESSION['msg'] = 'Página cadastrada com sucesso.';
            exit;

        } catch (PDOException $e) {
            // Rollback em caso de erro
            $conn->rollBack();

            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar a pagina.', 'error' => $e->getMessage()]);
            exit;
        }
    }