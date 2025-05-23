<?php
require '../../vendor/autoload.php';

use PixPhp\StaticPix;
use chillerlan\QRCode\QROptions;

session_start();
ob_start();

if (isset($_POST['btnUpdAbout'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        //Informacoes coletadas pelo metodo POST
        if(isset($_POST['title'])) {
            $title = $_POST['title'];
        }

        if(isset($_POST['nome']) && isset($_POST['descricao'])) {
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            $doacoes = isset($_POST['doacoes']) ? 1 : 0;
        }

        // Atualize o item no banco de dados
        if(isset($title)) {
            $sql = "UPDATE $tabela SET title = :title WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':id', $id);

            try {
                $stmt->execute();
    
                // Exibir a modal após salvar as informações
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'As informações sobre sua instituição foram atualizadas com sucesso!';
    
                //Voltar para a pagina do formulario
                header('Location: ' . INCLUDE_PATH_ADMIN . 'cabecalho');
            } catch (PDOException $e) {
                echo "Erro na atualização: " . $e->getMessage();
            }
        }

        if(isset($nome) && isset($descricao) && isset($doacoes)) {
            $sql = "UPDATE $tabela SET nome = :nome, descricao = :descricao, doacoes = :doacoes WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':doacoes', $doacoes);
            $stmt->bindParam(':id', $id);

            try {
                $stmt->execute();
    
                // Exibir a modal após salvar as informações
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'As informações sobre sua instituição foram atualizadas com sucesso!';
    
                //Voltar para a pagina do formulario
                header('Location: ' . INCLUDE_PATH_ADMIN . 'sobre');
            } catch (PDOException $e) {
                echo "Erro na atualização: " . $e->getMessage();
            }
        }
    }
}

if (isset($_POST['btnUpdLogo'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se um arquivo foi enviado
    if (isset($_FILES['logo'])) {
        $imagemNome = $_FILES['logo']['name'];
        $imagemTemp = $_FILES['logo']['tmp_name'];

        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        // Preparando a consulta SQL
        $sqlSelect = $conn->prepare("SELECT (logo) FROM $tabela WHERE id=:id");
            
        // Substituindo os parâmetros na consulta
        $sqlSelect->bindParam(':id', $id);

        // Executando a consulta
        $sqlSelect->execute();
        
        // Obtendo os resultados da busca
        $logo = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
        
        // Iterando sobre os resultados
        foreach ($logo as $data) {
            // Acessando os valores dos campos do resultado
            $logo = $data['logo'];
        }

        // Salve o nome da imagem no banco de dados
        $sql = "UPDATE $tabela SET logo = :logo WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':logo', $imagemNome);
        $stmt->bindParam(':id', $id);
        
        try {
            $stmt->execute();
        
            // Mova o arquivo para o servidor
            $caminhoDestino = "../../assets/img/" . $imagemNome;


            if(unlink("../../assets/img/" . $logo)){
                if (move_uploaded_file($imagemTemp, $caminhoDestino)) {
                    // Exibir a modal após salvar as informações
                    $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                    $_SESSION['msg'] = 'A logo foi salva com sucesso com sucesso!';

                    //Voltar para a pagina do formulario
                    header('Location: ' . INCLUDE_PATH_ADMIN . 'cabecalho');
                } else {
                    echo "Erro ao enviar o arquivo para o servidor.";
                }
            }

        } catch (PDOException $e) {
            echo "Erro ao salvar o nome da logo no banco de dados: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnUpdPix'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    function formatarChavePix($chave, $tipo) {
        switch ($tipo) {
            case "telefone":
                $chave = str_replace(["(", ")"], "", $chave); // Remove apenas os parênteses
                return "+55 " . $chave;
            case "cpf":
            case "cnpj":
            case "aleatoria":
                return $chave;
            default:
                return $chave;
        }
    }

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        //Informacoes coletadas pelo metodo POST
        if(isset($_POST['pix_chave'])) {
            $pix_chave_formatada = $_POST['pix_chave'];
        } else {
            echo "O campo Chave PIX é obrigatório.";
        }

        $pix_tipo = $_POST['pix_tipo'];

        $pix_chave = formatarChavePix($pix_chave_formatada, $pix_tipo);

        if(isset($_POST['pix_identificador_transacao'])) {
            $pix_identificador_transacao = $_POST['pix_identificador_transacao'];
        } else {
            echo "O campo Identificador da Transação é obrigatório.";
        }

        if(isset($_POST['pix_valor'])) {
            $pix_valor = $_POST['pix_valor'];
        } else {
            echo "O campo Valor é obrigatório.";
        }

        $pix_exibir = isset($_POST['pix_exibir']) ? true : false;

        // Generate a PIX code
        $pixCode = StaticPix::generatePix("$pix_chave", "$pix_identificador_transacao", $pix_valor);

        // Atualize o item no banco de dados
        if(isset($pixCode)) {
            // Instanciar a classe para enviar os parâmetros para o QRCode
            $options= new QROptions([
                // Número da versão do código QRCode
                'version'     => 7,
                // Alterar para base64
                'imageBase64' => true,
                // escala da imagem
                'scale'       => 3
            ]);

            // Gerar QRCode: instanciar a classe QRCode e enviar os dados para o render gerar o QRCode
            $qrcode = (new \chillerlan\QRCode\QRCode($options))->render($pixCode);

            $sql = "UPDATE $tabela SET pix_tipo = :pix_tipo, pix_chave = :pix_chave, pix_valor = :pix_valor, pix_codigo = :pix_codigo, pix_imagem_base64 = :pix_imagem_base64, pix_identificador_transacao = :pix_identificador_transacao, pix_exibir = :pix_exibir WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':pix_tipo', $pix_tipo);
            $stmt->bindParam(':pix_chave', $pix_chave_formatada);
            $stmt->bindParam(':pix_valor', $pix_valor);
            $stmt->bindParam(':pix_codigo', $pixCode);
            $stmt->bindParam(':pix_imagem_base64', $qrcode);
            $stmt->bindParam(':pix_identificador_transacao', $pix_identificador_transacao);
            $stmt->bindParam(':pix_exibir', $pix_exibir);
            $stmt->bindParam(':id', $id);

            try {
                $stmt->execute();
    
                // Exibir a modal após salvar as informações
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'As informações sobre sua instituição foram atualizadas com sucesso!';
    
                //Voltar para a pagina do formulario
                header('Location: ' . INCLUDE_PATH_ADMIN . 'sobre');
            } catch (PDOException $e) {
                echo "Erro na atualização: " . $e->getMessage();
            }
        }
    }
}

if (isset($_POST['btnUpdColor'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        //Informacoes coletadas pelo metodo POST
        $background = $_POST['background'];
        $text_color = $_POST['text_color'];
        $color = $_POST['color'];
        $hover = $_POST['hover'];
        $load_btn = $_POST['load_btn'];

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET background = :background, text_color = :text_color, color = :color, hover = :hover, load_btn = :load_btn WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':background', $background);
        $stmt->bindParam(':text_color', $text_color);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':hover', $hover);
        $stmt->bindParam(':load_btn', $load_btn);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações sobre sua instituição foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'aparencia');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnUpdNavColor'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        //Informacoes coletadas pelo metodo POST
        $nav_color = $_POST['nav_color'];
        $nav_background = $_POST['nav_background'];

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET nav_color = :nav_color, nav_background = :nav_background WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nav_color', $nav_color);
        $stmt->bindParam(':nav_background', $nav_background);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações sobre sua instituição foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'cabecalho');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnUpdFooter'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        //Informacoes coletadas pelo metodo POST
        $privacidade = $_POST['privacidade'];
        $faq = $_POST['faq'];
        $use_faq = $_POST['use_faq'];

        if (!isset($_POST["dFacebook"])) {
            $facebook = $_POST['facebook'];
        }
        if (!isset($_POST["dInstagram"])) {
            $instagram = $_POST['instagram'];
        }
        if (!isset($_POST["dLinkedin"])) {
            $linkedin = $_POST['linkedin'];
        }
        if (!isset($_POST["dTwitter"])) {
            $twitter = $_POST['twitter'];
        }
        if (!isset($_POST["dYoutube"])) {
            $youtube = $_POST['youtube'];
        }
        if (!isset($_POST["dWebsite"])) {
            $website = $_POST['website'];
        }
        if (!isset($_POST["dtiktok"])) {
            $tiktok = $_POST['tiktok'];
        }
        if (!isset($_POST["dlinktree"])) {
            $linktree = $_POST['linktree'];
        }

        $cep = $_POST['cep'];
        $rua = $_POST['rua'];
        if (!isset($_POST["dNumero"])) {
            $numero = $_POST['numero'];
        }
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        $telefone = $_POST['telefone'];
        $email = $_POST['email'];

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET privacidade = :privacidade, faq = :faq, use_faq = :use_faq, facebook = :facebook, instagram = :instagram, linkedin = :linkedin, twitter = :twitter, youtube = :youtube, website = :website, tiktok = :tiktok, linktree = :linktree, cep = :cep, rua = :rua, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, telefone = :telefone, email = :email WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':privacidade', $privacidade);
        $stmt->bindParam(':faq', $faq);
        $stmt->bindParam(':use_faq', $use_faq);
        $stmt->bindParam(':facebook', $facebook);
        $stmt->bindParam(':instagram', $instagram);
        $stmt->bindParam(':linkedin', $linkedin);
        $stmt->bindParam(':twitter', $twitter);
        $stmt->bindParam(':youtube', $youtube);
        $stmt->bindParam(':website', $website);
        $stmt->bindParam(':tiktok', $tiktok);
        $stmt->bindParam(':linktree', $linktree);
        $stmt->bindParam(':cep', $cep);
        $stmt->bindParam(':rua', $rua);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':bairro', $bairro);
        $stmt->bindParam(':cidade', $cidade);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações do rodapé foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'rodape');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnUpdDonations'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        // Id da tabela
        $id = '1';

        // Informacoes coletadas pelo metodo POST
        $monthly_1 = $_POST['monthly_1'];
        $monthly_2 = $_POST['monthly_2'];
        $monthly_3 = $_POST['monthly_3'];
        $monthly_4 = $_POST['monthly_4'];
        $monthly_5 = $_POST['monthly_5'];
        $yearly_1 = $_POST['yearly_1'];
        $yearly_2 = $_POST['yearly_2'];
        $yearly_3 = $_POST['yearly_3'];
        $yearly_4 = $_POST['yearly_4'];
        $yearly_5 = $_POST['yearly_5'];
        $once_1 = $_POST['once_1'];
        $once_2 = $_POST['once_2'];
        $once_3 = $_POST['once_3'];
        $once_4 = $_POST['once_4'];
        $once_5 = $_POST['once_5'];

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET monthly_1 = :monthly_1, monthly_2 = :monthly_2, monthly_3 = :monthly_3, monthly_4 = :monthly_4, monthly_5 = :monthly_5, yearly_1 = :yearly_1, yearly_2 = :yearly_2, yearly_3 = :yearly_3, yearly_4 = :yearly_4, yearly_5 = :yearly_5, once_1 = :once_1, once_2 = :once_2, once_3 = :once_3, once_4 = :once_4, once_5 = :once_5 WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':monthly_1', $monthly_1);
        $stmt->bindParam(':monthly_2', $monthly_2);
        $stmt->bindParam(':monthly_3', $monthly_3);
        $stmt->bindParam(':monthly_4', $monthly_4);
        $stmt->bindParam(':monthly_5', $monthly_5);
        $stmt->bindParam(':yearly_1', $yearly_1);
        $stmt->bindParam(':yearly_2', $yearly_2);
        $stmt->bindParam(':yearly_3', $yearly_3);
        $stmt->bindParam(':yearly_4', $yearly_4);
        $stmt->bindParam(':yearly_5', $yearly_5);
        $stmt->bindParam(':once_1', $once_1);
        $stmt->bindParam(':once_2', $once_2);
        $stmt->bindParam(':once_3', $once_3);
        $stmt->bindParam(':once_4', $once_4);
        $stmt->bindParam(':once_5', $once_5);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações dos valores foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'sobre');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }   
    }
}

if (isset($_POST['btnIntegration'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tabela onde sera feita a alteracao
        $tabela = 'tb_integracoes';

        // Id da tabela
        $id = '1';

        // Informacoes coletadas pelo metodo POST
        $fb_pixel = $_POST['fb_pixel'];
        $gtm = $_POST['gtm'];
        $g_analytics = $_POST['g_analytics'];

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET fb_pixel = :fb_pixel, gtm = :gtm, g_analytics = :g_analytics WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fb_pixel', $fb_pixel);
        $stmt->bindParam(':gtm', $gtm);
        $stmt->bindParam(':g_analytics', $g_analytics);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações de integração foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'integracoes');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }   
    }
}

if (isset($_POST['btnMessages'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tabela onde sera feita a alteracao
        $tabela = 'tb_mensagens';

        // Id da tabela
        $id = '1';

        // Informacoes coletadas pelo metodo POST
        $welcome_email = $_POST['welcome_email'];

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET welcome_email = :welcome_email WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':welcome_email', $welcome_email);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações de mensagens foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'mensagens');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnUnregister'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tabela onde sera feita a alteracao
        $tabela = 'tb_mensagens';

        // Id da tabela
        $id = '1';

        // Informacoes coletadas pelo metodo POST
        $unregister_message = isset($_POST['unregister_message']) && !empty($_POST['unregister_message']) ? $_POST['unregister_message'] : NULL;

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET unregister_message = :unregister_message WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':unregister_message', $unregister_message);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações de mensagens foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'mensagens');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnPrivacy'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tabela onde sera feita a alteracao
        $tabela = 'tb_mensagens';

        // Id da tabela
        $id = '1';

        // Informacoes coletadas pelo metodo POST
        $privacy_policy = $_POST['privacy_policy'];
        $privacy = $_POST['use_privacy'];

        if($privacy) {
            $use_privacy = 1;
        } else {
            $use_privacy = 0;
        }

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET privacy_policy = :privacy_policy, use_privacy = :use_privacy WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':privacy_policy', $privacy_policy);
        $stmt->bindParam(':use_privacy', $use_privacy);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As informações de privacidade foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'politica-de-privacidade');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}
