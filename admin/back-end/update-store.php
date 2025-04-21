<?php
session_start();
include('../../config.php');

header('Content-Type: application/json');

function uploadImagem($loja_id, $conn) {
    $tabela   = 'tb_lojas';
    $pasta    = __DIR__ . "/../../files/lojas/{$loja_id}/perfil/";
    $maxSize  = 2 * 1024 * 1024; // 2MB
    $exts     = ['png','jpg','jpeg'];
    $rename   = true;

    // Garante que a pasta existe
    if (!file_exists($pasta)) {
        mkdir($pasta, 0777, true);
    }

    // Remove imagem antiga (se existir)
    $stmtOld = $conn->prepare("SELECT imagem FROM {$tabela} WHERE id = :lid");
    $stmtOld->execute([':lid' => $loja_id]);
    $old = $stmtOld->fetch(PDO::FETCH_COLUMN);
    if ($old) {
        $path = $pasta . $old;
        if (file_exists($path)) {
            unlink($path);
        }
        // apaga registros antigos
        $conn->prepare("UPDATE {$tabela} SET imagem = NULL WHERE id = :lid")
             ->execute([':lid' => $loja_id]);
    }

    // Verifica se foi enviado um arquivo
    if (empty($_FILES['imagem']['name'])) {
        echo json_encode(['status'=>'error','message'=>'Nenhuma imagem enviada.']);
        exit;
    }

    $arquivo   = $_FILES['imagem']['name'];
    $tmpName   = $_FILES['imagem']['tmp_name'];
    $size      = $_FILES['imagem']['size'];
    $error     = $_FILES['imagem']['error'];
    $extensao  = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

    // Validações
    if ($error !== UPLOAD_ERR_OK) {
        echo json_encode(['status'=>'error','message'=>'Erro no upload da imagem.']);
        exit;
    }
    if (!in_array($extensao, $exts)) {
        echo json_encode(['status'=>'error','message'=>'Extensão inválida.']);
        exit;
    }
    if ($size > $maxSize) {
        echo json_encode(['status'=>'error','message'=>'Arquivo muito grande (máx 2MB).']);
        exit;
    }

    // Define nome final
    $nomeFinal = $rename
        ? date('YmdHis') . "_perf_{$loja_id}.{$extensao}"
        : $arquivo;

    // Move e insere
    if (move_uploaded_file($tmpName, $pasta . $nomeFinal)) {
        $stmt = $conn->prepare("UPDATE {$tabela} SET imagem = :img WHERE id = :lid");
        $stmt->execute([':lid'=>$loja_id, ':img'=>$nomeFinal]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Falha ao mover a imagem.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'atualizar-loja') {
    $loja_id = $_POST['loja_id'] ?? null;
    $vendedora_id = $_SESSION['user_id'];
    $nome = trim($_POST['nome']);
    $mini_bio = trim($_POST['mini_bio']);

    try {
        if (!$conn) {
            throw new Exception("Conexão inválida com o banco de dados.");
        }

        $conn->beginTransaction();

        if ($loja_id) {
            // Edita a loja ja existente
            $stmt = $conn->prepare("UPDATE tb_lojas SET nome = :nome, mini_bio = :mini_bio WHERE id = :loja_id AND vendedora_id = :vendedora_id");

            $stmt->bindParam(':loja_id', $loja_id, PDO::PARAM_INT);
        } else {
            // Cria uma loja para essa vendedora
            $stmt = $conn->prepare("INSERT INTO tb_lojas (vendedora_id, nome, mini_bio) VALUES (:vendedora_id, :nome, :mini_bio)");
        }
        $stmt->bindParam(':vendedora_id', $vendedora_id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':mini_bio', $mini_bio, PDO::PARAM_STR);
        $stmt->execute();
        if (!$loja_id) {
            // Caso a loja tenha sido criada agora salva o id
            $loja_id = $conn->lastInsertId();
        }

        // Processar novas imagens enviadas normalmente
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            uploadImagem($loja_id, $conn);
        }

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Loja atualizada com sucesso.']);
        $_SESSION['msg'] = 'Loja atualizada com sucesso.';
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar a loja.', 'error' => $e->getMessage()]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remover-imagem') {
    $loja_id = $_POST['loja_id'] ?? null;

    // Remove a imagem antiga
    $tabela = 'tb_lojas';
    $pasta  = __DIR__ . "/../../files/lojas/{$loja_id}/perfil/";

    // Busca nome do arquivo
    $stmt = $conn->prepare("SELECT imagem FROM {$tabela} WHERE id = ?");
    $stmt->execute([$loja_id]);
    $old = $stmt->fetchColumn();
    if ($old) {
        $file = $pasta . $old;
        if (is_file($file)) {
            unlink($file);
        }
        // Limpa o campo no banco
        $upd = $conn->prepare("UPDATE {$tabela} SET imagem = NULL WHERE id = ?");
        $upd->execute([$loja_id]);
    }
    // Retorna JSON de sucesso
    echo json_encode(['status'=>'success','message'=>'Imagem removida com sucesso.']);
    $_SESSION['msg'] = 'Imagem removida com sucesso.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'atualizar-endereco') {
    $loja_id = $_POST['loja_id'] ?? null;
    $vendedora_id = $_SESSION['user_id'];
    $cep = trim($_POST['cep']);
    $logradouro = trim($_POST['logradouro']);
    $numero = trim($_POST['numero']);
    $complemento = trim($_POST['complemento']);
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);

    try {
        if (!$conn) {
            throw new Exception("Conexão inválida com o banco de dados.");
        }

        $conn->beginTransaction();

        if ($loja_id) {
            // Edita a loja ja existente
            $stmt = $conn->prepare("UPDATE tb_lojas 
                                    SET 
                                        cep = :cep, 
                                        logradouro = :logradouro, 
                                        numero = :numero, 
                                        complemento = :complemento, 
                                        bairro = :bairro, 
                                        cidade = :cidade, 
                                        estado = :estado 
                                    WHERE id = :loja_id AND vendedora_id = :vendedora_id");

            $stmt->bindParam(':loja_id', $loja_id, PDO::PARAM_INT);
        } else {
            // Cria uma loja para essa vendedora
            $stmt = $conn->prepare("INSERT INTO tb_lojas (vendedora_id, cep, logradouro, numero, complemento, bairro, cidade, estado) 
                                    VALUES (:vendedora_id, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado)");
        }
        $stmt->bindParam(':vendedora_id', $vendedora_id, PDO::PARAM_INT);
        $stmt->bindParam(':cep', $cep, PDO::PARAM_STR);
        $stmt->bindParam(':logradouro', $logradouro, PDO::PARAM_STR);
        $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindParam(':complemento', $complemento, PDO::PARAM_STR);
        $stmt->bindParam(':bairro', $bairro, PDO::PARAM_STR);
        $stmt->bindParam(':cidade', $cidade, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Endereço atualizado com sucesso.']);
        $_SESSION['msg'] = 'Endereço atualizado com sucesso.';
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o endereço.', 'error' => $e->getMessage()]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'configurar-asaas') {
    $loja_id = $_POST['loja_id'] ?? null;
    $vendedora_id = $_SESSION['user_id'];
    $asaas_email = trim($_POST['email']);

    try {
        if (!$conn) {
            throw new Exception("Conexão inválida com o banco de dados.");
        }

        $conn->beginTransaction();

        if ($loja_id) {
            // Edita a loja ja existente
            $stmt = $conn->prepare("UPDATE tb_lojas 
                                    SET 
                                        asaas_email = :asaas_email 
                                    WHERE id = :loja_id AND vendedora_id = :vendedora_id");

            $stmt->bindParam(':loja_id', $loja_id, PDO::PARAM_INT);
        } else {
            // Cria uma loja para essa vendedora
            $stmt = $conn->prepare("INSERT INTO tb_lojas (vendedora_id, asaas_email) 
                                    VALUES (:vendedora_id, :asaas_email)");
        }
        $stmt->bindParam(':vendedora_id', $vendedora_id, PDO::PARAM_INT);
        $stmt->bindParam(':asaas_email', $asaas_email, PDO::PARAM_STR);
        $stmt->execute();

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Configurações do Asaas atualizadas com sucesso.']);
        $_SESSION['msg'] = 'Configurações do Asaas atualizadas com sucesso.';
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar as configurações do Asaas.', 'error' => $e->getMessage()]);
        exit;
    }
}