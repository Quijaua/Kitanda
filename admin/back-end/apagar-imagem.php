<?php
    session_start();
    ob_start();

    include_once('../../config.php');

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Nome da tabela para a busca
        $tabela = 'tb_imagens';

        // Preparando a consulta SQL
        $sqlSelect = $conn->prepare("SELECT (imagem) FROM $tabela WHERE id=:id");
        
        // Substituindo os parâmetros na consulta
        $sqlSelect->bindParam(':id', $id);

        // Executando a consulta
        $sqlSelect->execute();
        
        // Obtendo os resultados da busca
        $imagem = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
        
        // Iterando sobre os resultados
        foreach ($imagem as $data) {
            // Acessando os valores dos campos do resultado
            $imagem = $data['imagem'];
        }

        // Caminho completo para a imagem que você deseja excluir
        $caminhoDaImagem = "../../assets/img/" . $imagem;

        // Verifique se o arquivo existe antes de tentar excluí-lo
        if (file_exists($caminhoDaImagem)) {
            // Tente excluir o arquivo
            if(unlink($caminhoDaImagem)){
                // Consulta SQL para excluir a linha
                $stmt = $conn->prepare("DELETE FROM $tabela WHERE id = :id");
                
                // Bind dos parâmetros
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                
                // Executar a consulta
                $stmt->execute();
                
                // Exibir a modal após salvar as informações
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'A imagem foi deletada com sucesso com sucesso!';

                header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
            } else {
                // Caso a imagem não exista, apenas limpa a coluna no banco
                $stmt = $conn->prepare("DELETE FROM $tabela WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
    
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'O arquivo da imagem não foi encontrado, mas o registro foi atualizado!';
                header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
            }
        }
    }else{
        // Mensagem de falha
        $_SESSION['msgupdcad'] = 'É necessário selecionar uma imagem!';
        header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
    }