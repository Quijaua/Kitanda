<?php
    session_start();
    ob_start();

    include_once('../config.php');

    if (isset($_POST['btnUpdCobranca'])) {
        // Nome da tabela para a busca
        $tabela = 'tb_doacoes';
        
        // Recupera os dados do formulário
        $valor = $_POST['valor'];
        $data_vencimento = $_POST['data_vencimento'];
        $forma_pagamento = $_POST['forma_pagamento'];
        $payment_ids = $_POST['payment_ids'];

        // Loop através dos IDs das cobranças
        foreach ($payment_ids as $payment_id) {
            // Atualize os registros no banco de dados com base em cada ID
            $sql = "UPDATE tb_doacoes SET valor = :valor, data_vencimento = :data_vencimento, forma_pagamento = :forma_pagamento WHERE payment_id = :payment_id";
    
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->bindParam(':data_vencimento', $data_vencimento, PDO::PARAM_STR);
            $stmt->bindParam(':forma_pagamento', $forma_pagamento, PDO::PARAM_STR);
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_STR);

            // Executando o update
            if ($stmt->execute()) {
                // Exibir a modal após salvar as informações
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'A cobrança foi atualizada com sucesso!';
                header("Location: " . INCLUDE_PATH_USER);
            } else {
                // Mensagem de falha
                $_SESSION['msgupdcad'] = 'Erro ao editar a imagem.';
                header("Location: " . INCLUDE_PATH_USER);
            }
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgupdcad'] = 'Erro ao editar a imagem.';
        header("Location: " . INCLUDE_PATH_USER);
    }