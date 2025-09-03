<?php
    session_start();
    include('../../config.php');

    header('Content-Type: application/json');

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    // var_dump($_FILES);
    // exit;

    function uploadImagem($user_id, $conn) {
        $tabela = 'tb_arquivos';
        $_UP['pasta'] = "../../files/arquivos/$user_id/";
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

        for( $count = 0; $count < count($_FILES['imagens']['name']); $count++ ) {

            $extensao = pathinfo($_FILES['imagens']['name'][$count], PATHINFO_EXTENSION);
            if (!in_array(strtolower($extensao), $_UP['extensoes'])) {
                echo json_encode(['status' => 'error', 'message' => 'A extensão da imagem é inválida.']);
                exit;
            }

            if (count($_FILES['imagens']['size']) > $_UP['tamanho']) {
                echo json_encode(['status' => 'error', 'message' => 'Arquivo muito grande.']);
                exit;
            }

            //O arquivo passou em todas as verificações, hora de tentar move-lo para a pasta foto
            //Primeiro verifica se deve trocar o nome do arquivo
            if ($_UP['renomeia'] == true) {
                // Pega a extensão do arquivo original
                $extensao = pathinfo($_FILES['imagens']['name'][$count], PATHINFO_EXTENSION);

                // Cria um nome baseado no UNIX TIMESTAMP atual, um identificador único, e a extensão original do arquivo
                $nome_final = date('YmdHis') . $count . '_imagem.' . $extensao;
            } else {
                // Mantém o nome original do arquivo
                $nome_final = $_FILES['imagens']['name'][$count];
            }

            //Depois verifica se pode mover o arquivo para a pasta escolhida
            if (move_uploaded_file($_FILES['imagens']['tmp_name'][$count], $_UP['pasta'] . $nome_final)) {
                $stmt = $conn->prepare("INSERT INTO $tabela (nome, criado_por) VALUES (:img, :user_id)");
                $stmt->bindParam(':img', $nome_final);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar o arquivo, tente novamente.']);
                exit;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload-arquivos') {

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
            $user_id = $_POST['criado_por'];

            if (!empty($_FILES['imagens'])) {
                // Salvar imagem
                uploadImagem($user_id, $conn);
            }

            // Commit na transação
            $conn->commit();

            // Retorna um status de sucesso
            echo json_encode(['status' => 'success', 'message' => 'Arquivos enviados com sucesso.']);
            $_SESSION['msg'] = 'Arquivos enviados com sucesso.';
            exit;

        } catch (PDOException $e) {
            // Rollback em caso de erro
            $conn->rollBack();

            echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar arquivos.', 'error' => $e->getMessage()]);
            exit;
        }
    }