<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    // var_dump($_FILES);
    // exit;

    function uploadImagem($post_id, $conn) {
        $tabela = 'tb_blog_posts';
        $_UP['pasta'] = "../../files/blog/$post_id/";
        $_UP['tamanho'] = 1024 * 1024 * 2; // 2MB
        $_UP['extensoes'] = array('png', 'jpg', 'jpeg');
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
            $stmt = $conn->prepare("UPDATE $tabela SET imagem = :img WHERE id = :post_id");
            $stmt->bindParam(':img', $nome_final, PDO::PARAM_STR);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao fazer upload da imagem.']);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'criar-post') {
        // Obtendo os dados do formulário
        $titulo = trim($_POST['titulo']);
        $tags = isset($_POST['tags']) ? trim($_POST['tags']) : null;
        $data_publicacao = trim($_POST['data_publicacao']);
        $categorias = (!empty($_POST['categorias']) && is_array($_POST['categorias'])) ? $_POST['categorias'] : null;
        $resumo = trim($_POST['resumo']);
        $seo_nome = trim($_POST['seo_nome']);
        $seo_descricao = trim($_POST['seo_descricao']);
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

            // Inserindo o post no banco de dados
            $stmt = $conn->prepare("INSERT INTO tb_blog_posts (titulo, tags, data_publicacao, resumo, seo_nome, seo_descricao, criado_por) 
                                    VALUES (:titulo, :tags, :data_publicacao, :resumo, :seo_nome, :seo_descricao, :criado_por)");
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':tags', $tags, PDO::PARAM_STR);
            $stmt->bindParam(':data_publicacao', $data_publicacao, PDO::PARAM_STR);
            $stmt->bindParam(':resumo', $resumo, PDO::PARAM_STR);
            $stmt->bindParam(':seo_nome', $seo_nome, PDO::PARAM_STR);
            $stmt->bindParam(':seo_descricao', $seo_descricao, PDO::PARAM_STR);
            $stmt->bindParam(':criado_por', $criado_por, PDO::PARAM_INT);
            $stmt->execute();
            $post_id = $conn->lastInsertId();

            if ($categorias) {
                foreach ($categorias as $key => $categoria_id) {
                    // Inserindo o post no banco de dados
                    $stmt = $conn->prepare("INSERT INTO tb_blog_categoria_posts (categoria_id, post_id) VALUES (:categoria_id, :post_id)");
                    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_STR);
                    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }

            if (!empty($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                // Salvar imagem
                uploadImagem($post_id, $conn);
            }

            // Commit na transação
            $conn->commit();

            // Retorna um status de sucesso
            echo json_encode(['status' => 'success', 'message' => 'Post cadastrado com sucesso.']);
            $_SESSION['msg'] = 'Post cadastrado com sucesso.';
            exit;

        } catch (PDOException $e) {
            // Rollback em caso de erro
            $conn->rollBack();

            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar post.', 'error' => $e->getMessage()]);
            exit;
        }
    }