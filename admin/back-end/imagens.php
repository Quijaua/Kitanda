<?php
    session_start();
    ob_start();

    include_once('../../config.php');

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    
    // //Criar Card
    // print_r($_FILES['file']);
    // exit;
    if (isset($_FILES['file'])) {
        // Nome da tabela para a busca
        $tabela = 'tb_imagens';
        
        $arquivo = $_FILES['file']['name'];
        
        //Pasta onde o arquivo vai ser salvo
        $_UP['pasta'] = '../../assets/img/';
        
        //Tamanho máximo do arquivo em Bytes
        $_UP['tamanho'] = 1024*1024*2; //2mb
        
        //Array com a extensões permitidas
        $_UP['extensoes'] = array('png', 'jpg', 'jpeg');
        
        //Renomeiar
        $_UP['renomeia'] = true;
        
        //Array com os tipos de erros de upload do PHP
        $_UP['erros'][0] = 'Não houve erro';
        $_UP['erros'][1] = 'O arquivo no upload é maior que o limite do PHP';
        $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especificado no HTML';
        $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
        $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
        
        //O arquivo passou em todas as verificações, hora de tentar move-lo para a pasta foto
        //Primeiro verifica se deve trocar o nome do arquivo
        if ($_UP['renomeia'] == true) {
            // Pega a extensão do arquivo original
            $extensao = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            
            // Cria um nome baseado no UNIX TIMESTAMP atual, um identificador único, e a extensão original do arquivo
            $nome_final = time() . '_' . uniqid() . '.' . $extensao;
        } else {
            // Mantém o nome original do arquivo
            $nome_final = $_FILES['file']['name'];
        }

        //Faz a verificação da extensao do arquivo
        $tmp = explode('.', $_FILES['file']['name']);
        $extensao = strtolower(end($tmp));

        if(array_search($extensao, $_UP['extensoes']) === false){
            // Mensagem de falha
            $_SESSION['msgaddcad'] = 'A extensão da imagem é inválida';
        }
        
        //Faz a verificação do tamanho do arquivo
        else if ($_UP['tamanho'] < $_FILES['file']['size']){
            // Mensagem de falha
            $_SESSION['msgaddcad'] = 'Arquivo muito grande.';
        }

        //Verificar se é possivel mover o arquivo para a pasta escolhida
        if(move_uploaded_file($_FILES['file']['tmp_name'], $_UP['pasta'] . $nome_final)){
            // Consulta SQL para inserir uma nova linha
            $stmt = $conn->prepare("INSERT INTO $tabela (imagem) 
            VALUES (:imagem)");

            $stmt->bindParam(':imagem', $nome_final, PDO::PARAM_STR);

            // Executando o update
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'O card foi salvo com sucesso com sucesso!';
            exit;
        }
        else
        {
            // Mensagem de falha
            $_SESSION['msgaddcad'] = 'Erro ao criar card.';
            exit;
        }
    }

    if (isset($_FILES['imagem'])) {
        // Nome da tabela para a busca
        $tabela = 'tb_imagens';

        // Dados para o update
        $ids = $_POST['ids']; // ID do registro a ser atualizado

        // Atualizar os registros no banco de dados
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];

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

            $updImage = true;
        
            $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            
            $arquivo = $_FILES['imagem']['name'];
            
            //Pasta onde o arquivo vai ser salvo
            $_UP['pasta'] = '../../assets/img/';
            
            //Tamanho máximo do arquivo em Bytes
            $_UP['tamanho'] = 1024*1024*2; //2mb
            
            //Array com a extensões permitidas
            $_UP['extensoes'] = array('png', 'jpg', 'jpeg');
            
            //Renomeiar
            $_UP['renomeia'] = true;
            
            //Array com os tipos de erros de upload do PHP
            $_UP['erros'][0] = 'Não houve erro';
            $_UP['erros'][1] = 'O arquivo no upload é maior que o limite do PHP';
            $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especificado no HTML';
            $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
            $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
            
            //O arquivo passou em todas as verificações, hora de tentar move-lo para a pasta foto
            //Primeiro verifica se deve trocar o nome do arquivo
            if ($_UP['renomeia'] == true) {
                // Pega a extensão do arquivo original
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                
                // Cria um nome baseado no UNIX TIMESTAMP atual, um identificador único, e a extensão original do arquivo
                $nome_final = time() . '_' . uniqid() . '.' . $extensao;
            } else {
                //mantem o nome original do arquivo
                $nome_final = $_FILES['imagem']['name'];
            }
            if ($_FILES['imagem']['name'] == '')
            {
                $updImage = false;
            }

            //Faz a verificação da extensao do arquivo
            $tmp = explode('.', $_FILES['imagem']['name']);
            $extensao = strtolower(end($tmp));

            if(array_search($extensao, $_UP['extensoes']) === false){
                // Mensagem de falha
                $_SESSION['msgupdcad'] = 'A extensão da imagem é inválida.';
                header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
            }
            
            //Faz a verificação do tamanho do arquivo
            else if ($_UP['tamanho'] < $_FILES['imagem']['size']){
                // Mensagem de falha
                $_SESSION['msgupdcad'] = 'Arquivo muito grande.';
                header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
            }

            else if ($updImage == true){
                //Verificar se é possivel mover o arquivo para a pasta escolhida
                if(unlink("../../assets/img/" . $imagem)){
                    if(move_uploaded_file($_FILES['imagem']['tmp_name'], $_UP['pasta']. $nome_final)){
                        // Preparando a consulta SQL
                        $stmt = $conn->prepare("UPDATE $tabela SET 
                            imagem=:imagem
                        WHERE id=:id");
        
                        // Substituindo os parâmetros na consulta
                        $stmt->bindParam(':imagem', $nome_final);
                        $stmt->bindParam(':id', $id);
        
                        // Executando o update
                        $stmt->execute();

                        // Exibir a modal após salvar as informações
                        $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                        $_SESSION['msg'] = 'A imagem foi atualizada com sucesso!';
                        header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
                    }else{
                        //Upload não efetuado com sucesso, exibe a mensagem
                        $_SESSION['msgupdcad'] = 'Erro ao atualizar.';
                        header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
                    }
                }
            }
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgupdcad'] = 'Erro ao editar a imagem.';
        header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
    }
