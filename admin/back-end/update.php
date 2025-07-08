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
        $title = $_POST['title'] ?? null;
        $descricao = $_POST['descricao'] ?? null;

        // Atualize o item no banco de dados
        $sql = "UPDATE $tabela SET title = :title, descricao = :descricao WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'As configurações gerais do site foram atualizadas com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'geral');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnUpdVitrine'])) {
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Id da tabela
        $id = '1';

        //Informacoes coletadas pelo metodo POST
        $vitrine_limite = (int) $_POST['vitrine_limite'];
    
        $sql = "UPDATE tb_checkout SET vitrine_limite = :vitrine_limite WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':vitrine_limite', $vitrine_limite);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'Limite da vitrine atualizado com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'geral');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
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
                    header('Location: ' . INCLUDE_PATH_ADMIN . 'geral');
                } else {
                    echo "Erro ao enviar o arquivo para o servidor.";
                }
            }

        } catch (PDOException $e) {
            echo "Erro ao salvar o nome da logo no banco de dados: " . $e->getMessage();
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

// if (isset($_POST['btnUpdTheme']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
//     //Inclui o arquivo 'config.php'
//     include('../../config.php');

//     // Por padrão, gravamos na linha de id = 1 (configurações gerais)
//     $tabela = 'tb_checkout';
//     $id     = 1;

//     // Coleta o tema selecionado
//     $theme = $_POST['theme'] ?? null;

//     // Atualiza a coluna theme (pode ser NULL para padrão)
//     $sql = "UPDATE {$tabela} 
//             SET theme = :theme 
//             WHERE id = :id";
//     $stmt = $conn->prepare($sql);
//     $stmt->bindParam(':theme', $theme);
//     $stmt->bindParam(':id', $id, PDO::PARAM_INT);

//     try {
//         $stmt->execute();

//         // Define variável para exibir modal ou mensagem de sucesso
//         $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
//         $_SESSION['msg'] = 'Tema atualizado com sucesso!';
//         header('Location: ' . INCLUDE_PATH_ADMIN . 'aparencia');
//         exit;
//     } catch (PDOException $e) {
//         echo "Erro ao atualizar tema: " . $e->getMessage();
//     }
// }

if (isset($_POST['btnUpdTheme']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    include('../../config.php');

    $tabela = 'tb_checkout';
    $id     = 1;

    // 1) coleta o tema
    $theme = $_POST['theme'] ?? null;

    // 2) coleta os novos flags (checkboxes vindo como 'on')
    $getBool = fn($key) => isset($_POST[$key]) && $_POST[$key] === 'on' ? 1 : 0;

    // Ankara
    $ankara_hero        = $getBool('ankara_hero');
    $ankara_colorful    = $getBool('ankara_colorful');
    $ankara_yellow      = $getBool('ankara_yellow');
    $ankara_footer_top  = $getBool('ankara_footer_top');
    $ankara_footer_blog = $getBool('ankara_footer_blog');

    // TerraDourada
    $td_hero            = $getBool('td_hero');
    $td_entrepreneurs   = $getBool('td_entrepreneurs');
    $td_news            = $getBool('td_news');
    $td_footer_info     = $getBool('td_footer_info');
    $td_footer_socials  = $getBool('td_footer_socials');

    // 3) monta o UPDATE incluindo theme e todos os flags
    $sql = "
      UPDATE {$tabela} SET
        theme                = :theme,
        ankara_hero          = :ankara_hero,
        ankara_colorful      = :ankara_colorful,
        ankara_yellow        = :ankara_yellow,
        ankara_footer_top    = :ankara_footer_top,
        ankara_footer_blog   = :ankara_footer_blog,
        td_hero              = :td_hero,
        td_entrepreneurs     = :td_entrepreneurs,
        td_news              = :td_news,
        td_footer_info       = :td_footer_info,
        td_footer_socials    = :td_footer_socials
      WHERE id = :id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':theme',               $theme);
    $stmt->bindParam(':ankara_hero',         $ankara_hero,        PDO::PARAM_BOOL);
    $stmt->bindParam(':ankara_colorful',     $ankara_colorful,    PDO::PARAM_BOOL);
    $stmt->bindParam(':ankara_yellow',       $ankara_yellow,      PDO::PARAM_BOOL);
    $stmt->bindParam(':ankara_footer_top',   $ankara_footer_top,  PDO::PARAM_BOOL);
    $stmt->bindParam(':ankara_footer_blog',  $ankara_footer_blog, PDO::PARAM_BOOL);
    $stmt->bindParam(':td_hero',             $td_hero,            PDO::PARAM_BOOL);
    $stmt->bindParam(':td_entrepreneurs',    $td_entrepreneurs,   PDO::PARAM_BOOL);
    $stmt->bindParam(':td_news',             $td_news,            PDO::PARAM_BOOL);
    $stmt->bindParam(':td_footer_info',      $td_footer_info,     PDO::PARAM_BOOL);
    $stmt->bindParam(':td_footer_socials',   $td_footer_socials,  PDO::PARAM_BOOL);
    $stmt->bindParam(':id',                  $id,                 PDO::PARAM_INT);

    try {
        $stmt->execute();

        $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
        $_SESSION['msg']        = 'Configurações de aparência e conteúdo da Home atualizadas com sucesso!';
        header('Location: ' . INCLUDE_PATH_ADMIN . 'aparencia');
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar configurações: " . $e->getMessage();
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
            header('Location: ' . INCLUDE_PATH_ADMIN . 'geral');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

if (isset($_POST['btnUpdFreight'])) {
    //Inclui o arquivo 'config.php'
    include('../../config.php');

    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tabela e ID do checkout que queremos atualizar
        $tabela = 'tb_checkout';
        $checkoutId = '1';

        // Dados do formulário
        $freightType = $_POST['freight_type'] ?? 'default';
        $rawValue = $_POST['freight_value'] ?? ''; // ex: "1.234,56" ou ""

        // Se for 'default', zera o valor; se for 'fixed', converte string para decimal
        if ($freightType === 'fixed') {
            // Remove pontos e troca vírgula por ponto:
            $num = str_replace(['.', ' '], ['', ''], $rawValue);
            $num = str_replace(',', '.', $num);
            $freightValue = floatval($num);
        } else {
            $freightValue = null;
        }

        // Prepara o UPDATE
        $stmt = $conn->prepare("UPDATE {$tabela} SET freight_type = :freight_type, freight_value = :freight_value WHERE id = :id");
        $stmt->bindParam(':freight_type', $freightType, PDO::PARAM_STR);

        if ($freightValue !== null) {
            $stmt->bindParam(':freight_value', $freightValue);
        } else {
            $stmt->bindValue(':freight_value', null, PDO::PARAM_NULL);
        }

        $stmt->bindParam(':id', $checkoutId, PDO::PARAM_INT);

        try {
            $stmt->execute();

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'Tipo de frete salvo com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'geral');
            exit;
        } catch (PDOException $e) {
            echo "Erro ao atualizar frete: " . $e->getMessage();
            exit;
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
        if (!isset($_POST["dWhatsapp"])) {
            $whatsapp = $_POST['whatsapp'];
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
        $sql = "UPDATE $tabela SET privacidade = :privacidade, faq = :faq, use_faq = :use_faq, facebook = :facebook, instagram = :instagram, whatsapp = :whatsapp, linkedin = :linkedin, twitter = :twitter, youtube = :youtube, website = :website, tiktok = :tiktok, linktree = :linktree, cep = :cep, rua = :rua, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, telefone = :telefone, email = :email WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':privacidade', $privacidade);
        $stmt->bindParam(':faq', $faq);
        $stmt->bindParam(':use_faq', $use_faq);
        $stmt->bindParam(':facebook', $facebook);
        $stmt->bindParam(':instagram', $instagram);
        $stmt->bindParam(':whatsapp', $whatsapp);
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
