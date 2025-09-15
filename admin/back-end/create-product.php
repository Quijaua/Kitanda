<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    // var_dump($_FILES);

    function uploadImagens($produto_id, $conn) {
        $tabela = 'tb_produto_imagens';
        $_UP['pasta'] = "../../files/produtos/$produto_id/";
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

        $id = 0;

        foreach ($_FILES['imagens']['name'] as $key => $arquivo) {
            $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
            if (!in_array(strtolower($extensao), $_UP['extensoes'])) {
                echo json_encode(['status' => 'error', 'message' => 'A extensão da imagem é inválida.']);
                continue;
            }

            if ($_FILES['imagens']['size'][$key] > $_UP['tamanho']) {
                echo json_encode(['status' => 'error', 'message' => 'Arquivo muito grande.']);
                continue;
            }

            //O arquivo passou em todas as verificações, hora de tentar move-lo para a pasta foto
            //Primeiro verifica se deve trocar o nome do arquivo
            if ($_UP['renomeia'] == true) {
                // Pega a extensão do arquivo original
                $extensao = pathinfo($_FILES['imagens']['name'][$key], PATHINFO_EXTENSION);

                // Cria um nome baseado no UNIX TIMESTAMP atual, um identificador único, e a extensão original do arquivo
                $nome_final = date('YmdHis') . '_imagem_' . $id . '.' . $extensao;
            } else {
                // Mantém o nome original do arquivo
                $nome_final = date('YmdHis') . "_" . $_FILES['imagens']['name'][$key];
            }

            if (move_uploaded_file($_FILES['imagens']['tmp_name'][$key], $_UP['pasta'] . $nome_final)) {
                $stmt = $conn->prepare("INSERT INTO $tabela (produto_id, imagem) VALUES (:produto_id, :imagem)");
                $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
                $stmt->bindParam(':imagem', $nome_final, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao fazer upload da imagem.']);
            }

            $id++;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar-produto') {

        // Obtendo os dados do formulário
        $nome = trim($_POST['nome']);
        $titulo = trim($_POST['titulo']);
        $estoque = trim($_POST['estoque']);
        $vitrine = isset($_POST['vitrine']) ? 1 : 0;
        $peso = floatval($_POST['peso']);
        $descricao = trim($_POST['descricao']);
        $preco = trim($_POST['preco']);
        $preco = str_replace('.', '', $preco);
        $preco = str_replace(',', '.', $preco);

        $freight_type = $_POST['freight_type'] ?? 'default';
        $rawValue = $_POST['freight_value'] ?? '';
        $freight_dimension_id = $_POST['freight_dimension_id'] ?? 0;

        if ($freight_type === 'fixed') {
            $num = str_replace(['.', ' '], ['', ''], $rawValue);
            $num = str_replace(',', '.', $num);
            $freight_value = floatval($num);
        } else {
            $freight_value = null;
        }

        $categorias = (!empty($_POST['categorias']) && is_array($_POST['categorias'])) ? $_POST['categorias'] : null;
        $seo_nome = trim($_POST['seo_nome']);
        $seo_descricao = trim($_POST['seo_descricao']);
        $link = trim($_POST['link']);
        $criado_por = (isset($_POST['criado_por']) && !empty($_POST['criado_por'])) ? $_POST['criado_por'] : $_SESSION['user_id'];

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
            exit;
        }

        // Validando o link (deve ser uma URL válida)
        if (!filter_var(INCLUDE_PATH . $link, FILTER_VALIDATE_URL)) {
            echo json_encode(['status' => 'error', 'message' => 'O link informado não é válido.']);
            exit;
        }

        try {
            // Verifica a conexão com o banco
            if (!$conn) {
                throw new Exception("Conexão inválida com o banco de dados.");
            }

            // Iniciar transação
            $conn->beginTransaction();

            // Verifica se o link já existe
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tb_produtos WHERE link = :link");
            $stmt->bindParam(':link', $link, PDO::PARAM_STR);
            $stmt->execute();
            $existe = $stmt->fetchColumn();

            if ($existe) {
                echo json_encode(['status' => 'error', 'message' => 'Já existe um produto cadastrado com esse link.']);
                exit;
            }

            // Inserindo o produto no banco de dados
            $stmt = $conn->prepare("INSERT INTO tb_produtos (nome, titulo, estoque, descricao, preco, vitrine, peso, freight_type, freight_value, freight_dimension_id, seo_nome, seo_descricao, link, criado_por) 
                                    VALUES (:nome, :titulo, :estoque, :descricao, :preco, :vitrine, :peso, :freight_type, :freight_value, :freight_dimension_id, :seo_nome, :seo_descricao, :link, :criado_por)");
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':estoque', $estoque, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
            $stmt->bindParam(':vitrine', $vitrine, PDO::PARAM_STR);
            $stmt->bindParam(':peso', $peso, PDO::PARAM_STR);
            $stmt->bindParam(':freight_type', $freight_type, PDO::PARAM_STR);
            $stmt->bindParam(':freight_value', $freight_value, PDO::PARAM_STR);
            $stmt->bindParam(':freight_dimension_id', $freight_dimension_id, PDO::PARAM_INT);
            $stmt->bindParam(':seo_nome', $seo_nome, PDO::PARAM_STR);
            $stmt->bindParam(':seo_descricao', $seo_descricao, PDO::PARAM_STR);
            $stmt->bindParam(':link', $link, PDO::PARAM_STR);
            $stmt->bindParam(':criado_por', $criado_por, PDO::PARAM_INT);
            $stmt->execute();
            $produto_id = $conn->lastInsertId();

            if ($categorias) {
                foreach ($categorias as $key => $categoria_id) {
                    // Inserindo o post no banco de dados
                    $stmt = $conn->prepare("INSERT INTO tb_categoria_produtos (categoria_id, produto_id) VALUES (:categoria_id, :produto_id)");
                    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_STR);
                    $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }

            if (!empty($_FILES['imagens'])) {
                // Salvar imagens
                uploadImagens($produto_id, $conn);
            }

            // Commit na transação
            $conn->commit();

            // Retorna um status de sucesso
            echo json_encode(['status' => 'success', 'message' => 'Produto cadastrado com sucesso.']);
            $_SESSION['msg'] = 'Produto cadastrado com sucesso.';
            exit;

        } catch (PDOException $e) {
            // Rollback em caso de erro
            $conn->rollBack();

            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar produto.', 'error' => $e->getMessage()]);
            exit;
        }
    }