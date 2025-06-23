<?php
session_start();
include('../../config.php');

header('Content-Type: application/json');

function uploadImagens($produto_id, $conn) {
    $tabela = 'tb_produto_imagens';
    $_UP['pasta'] = "../../files/produtos/$produto_id/";
    $_UP['tamanho'] = 1024 * 1024 * 2; // 2MB
    $_UP['extensoes'] = array('png', 'jpg', 'jpeg');
    $_UP['renomeia'] = true;

    if (!file_exists($_UP['pasta'])) {
        mkdir($_UP['pasta'], 0777, true);
    }

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

        $nome_final = $_UP['renomeia'] ? date('YmdHis') . "_imagem_" . $key . "." . $extensao : $_FILES['imagens']['name'][$key];

        if (move_uploaded_file($_FILES['imagens']['tmp_name'][$key], $_UP['pasta'] . $nome_final)) {
            $stmt = $conn->prepare("INSERT INTO $tabela (produto_id, imagem) VALUES (:produto_id, :imagem)");
            $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
            $stmt->bindParam(':imagem', $nome_final, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao fazer upload da imagem.']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar-produto') {
    $produto_id = $_POST['produto_id'];
    $nome = trim($_POST['nome']);
    $titulo = trim($_POST['titulo']);
    $vitrine = isset($_POST['vitrine']) ? 1 : 0;
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(['.', ','], ['', '.'], trim($_POST['preco']));

    $freight_type = $_POST['freight_type'] ?? 'default';
    $rawValue = $_POST['freight_value'] ?? '';

    if ($freight_type === 'fixed') {
        $num = str_replace(['.', ' '], ['', ''], $rawValue);
        $num = str_replace(',', '.', $num);
        $freight_value = floatval($num);
    } else {
        $freight_value = null;
    }

    $seo_nome = trim($_POST['seo_nome']);
    $seo_descricao = trim($_POST['seo_descricao']);
    $link = trim($_POST['link']);
    $criado_por = trim($_POST['criado_por']);

    if (!filter_var(INCLUDE_PATH . $link, FILTER_VALIDATE_URL)) {
        echo json_encode(['status' => 'error', 'message' => 'O link informado não é válido.']);
        exit;
    }

    try {
        if (!$conn) {
            throw new Exception("Conexão inválida com o banco de dados.");
        }

        $conn->beginTransaction();

        $stmt = $conn->prepare("UPDATE tb_produtos SET nome = :nome, titulo = :titulo, descricao = :descricao, preco = :preco, vitrine = :vitrine, freight_type = :freight_type, freight_value = :freight_value, seo_nome = :seo_nome, seo_descricao = :seo_descricao, link = :link, criado_por = :criado_por WHERE id = :produto_id");
        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
        $stmt->bindParam(':vitrine', $vitrine, PDO::PARAM_INT);
        $stmt->bindParam(':freight_type', $freight_type, PDO::PARAM_STR);
        $stmt->bindParam(':freight_value', $freight_value, PDO::PARAM_STR);
        $stmt->bindParam(':seo_nome', $seo_nome, PDO::PARAM_STR);
        $stmt->bindParam(':seo_descricao', $seo_descricao, PDO::PARAM_STR);
        $stmt->bindParam(':link', $link, PDO::PARAM_STR);
        $stmt->bindParam(':criado_por', $criado_por, PDO::PARAM_STR);
        $stmt->execute();

        // Remover imagens se houver alguma na lista
        if (!empty($_POST['imagens_removidas'])) {
            $imagens_removidas = json_decode($_POST['imagens_removidas'], true);

            foreach ($imagens_removidas as $imagem) {
                // Caminho da imagem
                $caminhoImagem = "../../files/produtos/$produto_id/$imagem";

                // Remove do banco
                $stmt = $conn->prepare("DELETE FROM tb_produto_imagens WHERE produto_id = :produto_id AND imagem = :imagem");
                $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
                $stmt->bindParam(':imagem', $imagem, PDO::PARAM_STR);
                $stmt->execute();

                // Remove do servidor
                if (file_exists($caminhoImagem)) {
                    unlink($caminhoImagem);
                }
            }
        }

        // Processar novas imagens enviadas normalmente
        if (!empty($_FILES['imagens'])) {
            uploadImagens($produto_id, $conn);
        }

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Produto atualizado com sucesso.']);
        $_SESSION['msg'] = 'Produto atualizado com sucesso.';
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar produto.', 'error' => $e->getMessage()]);
        exit;
    }
}