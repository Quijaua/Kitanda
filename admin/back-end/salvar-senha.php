<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_clientes';

        // Recebe os dados do formulário
        $password = $_POST['password'];
        $asaas_id = $_POST['asaas_id'];

        $query = "UPDATE $tabela SET password = :password, magic_link = :magic_link WHERE asaas_id = :asaas_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindValue(':magic_link', null);
        $stmt->bindValue(':asaas_id', $asaas_id);

        if ($stmt->execute()) {
            // Consulta SQL para selecionar todas as colunas com base no ID
            $sql = "SELECT * FROM $tabela WHERE asaas_id = :asaas_id";

            // Preparar e executar a consulta
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':asaas_id', $asaas_id);
            $stmt->execute();

            // Recuperar os resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($resultados)) {
                $primeiroResultado = $resultados[0]; // Acessar o primeiro resultado do array
                
                $_SESSION['user_id'] = $primeiroResultado['id']; // Você pode definir informações do usuário aqui
                header("Location: " . INCLUDE_PATH_USER);
            } else {
                $_SESSION['msg'] = "Por favor faça login na sua conta";
                header("Location: " . INCLUDE_PATH_USER);
            }
        } else {
            $_SESSION['msg'] = "Não foi possível cadastrar a senha";
            header("Location: " . INCLUDE_PATH . "login/ativar-conta.php");
        }
    } else {
        $_SESSION['msg'] = "Erro ao cadastrar a senha";
        header("Location: " . INCLUDE_PATH . "login/ativar-conta.php");
    }