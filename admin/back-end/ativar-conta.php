<?php
    session_start();
    ob_start();
    include('../../config.php');

    //Apagar Card
    $token = filter_input(INPUT_GET, 'token');

    if(!empty($token)){
        // Nome da tabela para a busca
        $tabela = 'tb_clientes';

        // Consulta SQL para selecionar todas as colunas com base no ID
        $sql = "SELECT * FROM $tabela WHERE magic_link = :token";

        // Preparar e executar a consulta
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        // Recuperar os resultados
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($resultados)) {
            $primeiroResultado = $resultados[0]; // Acessar o primeiro resultado do array
        
            $nome = $primeiroResultado['nome'];
            $password = $primeiroResultado['password'];
            $asaas_id = $primeiroResultado['asaas_id'];

            if ($password == '')
            {
                $_SESSION['asaas_id'] = $asaas_id;
                header("Location: " . INCLUDE_PATH . "login/ativar-conta.php");
            } else {
                $_SESSION['msg'] = "Por favor faça login em sua conta!";
                header("Location: " . INCLUDE_PATH_USER);
            }
        } else {
            $_SESSION['msg'] = "Link inválido!";
            header("Location: " . INCLUDE_PATH_USER);
        }
    } else {
        $_SESSION['msg'] = "É necessario inserir um link.";
        header("Location: " . INCLUDE_PATH . "login/");
    }