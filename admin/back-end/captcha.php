<?php
//Inclui o arquivo 'config.php'
include('../../config.php');

session_start();
ob_start();

// Configura o fuso horário para São Paulo, Brasil
date_default_timezone_set('America/Sao_Paulo');

// print_r($_POST);
// exit;

if (isset($_POST['saveHcaptcha'])) {
    // Verifique se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        $captcha_type = 'hcaptcha';
        $site_key = $_POST['hcaptcha_site'];
        $secret_key = $_POST['hcaptcha_secret'];
        $hcaptcha_config = $_POST['hcaptcha_config'];
        $hcaptcha_pages = $_POST['hcaptcha_pages'] ?? null;

        // Atualizar as configurações no banco de dados
        $sql = "UPDATE $tabela SET hcaptcha = true WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Inserir ou atualizar as páginas dependendo da configuração
            if ($hcaptcha_config === "all") {
                // Adiciona todas as páginas à tabela
                $pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
                foreach ($pages as $page) {
                    // Verifica se já existe um registro para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se já existir, faz um UPDATE
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = :captcha_type WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->bindParam(':captcha_type', $captcha_type);
                        $stmtUpdate->execute();
                    } else {
                        // Se não existir, faz um INSERT
                        $sqlInsert = "INSERT INTO tb_page_captchas (page_name, captcha_type) VALUES (:page_name, :captcha_type)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->bindParam(':page_name', $page);
                        $stmtInsert->bindParam(':captcha_type', $captcha_type);
                        $stmtInsert->execute();
                    }
                }
            } elseif ($hcaptcha_config === "select" && isset($hcaptcha_pages)) {
                // Adiciona as páginas selecionadas pelo usuário
                $selected_pages = $hcaptcha_pages;
                foreach ($selected_pages as $page) {
                    // Verifica se já existe um registro para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se já existir, faz um UPDATE
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = :captcha_type WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->bindParam(':captcha_type', $captcha_type);
                        $stmtUpdate->execute();
                    } else {
                        // Se não existir, faz um INSERT
                        $sqlInsert = "INSERT INTO tb_page_captchas (page_name, captcha_type) VALUES (:page_name, :captcha_type)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->bindParam(':page_name', $page);
                        $stmtInsert->bindParam(':captcha_type', $captcha_type);
                        $stmtInsert->execute();
                    }
                }

                // Atualiza as páginas que não foram selecionadas para 'none'
                $all_pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
                $pages_to_update = array_diff($all_pages, $selected_pages);

                foreach ($pages_to_update as $page) {
                    // Verifica se o registro existe para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE captcha_type = :captcha_type AND page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':captcha_type', $captcha_type);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se existir, faz um UPDATE para mudar para 'none'
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->execute();
                    }
                }
            } elseif ($hcaptcha_config === "select") {
                // Atualiza as páginas que não foram selecionadas para 'none'
                $remove_all_pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];

                foreach ($remove_all_pages as $page) {
                    // Verifica se o registro existe para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE captcha_type = :captcha_type AND page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':captcha_type', $captcha_type);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se existir, faz um UPDATE para mudar para 'none'
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->execute();
                    }
                }
            } else if ($hcaptcha_config === "none") {
                // Remove todas as páginas da tabela
                $pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
                foreach ($pages as $page) {
                    // Verifica se o registro existe para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE captcha_type = :captcha_type AND page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':captcha_type', $captcha_type);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se existir, faz um UPDATE para mudar para 'none'
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->execute();
                    }
                }
            }

            // Atualizar o arquivo .env
            $envPath = dirname(__DIR__, 2) . '/.env';
            $envContent = file_get_contents($envPath);

            // Substituir as chaves existentes
            $envContent = preg_replace("/HCAPTCHA_CHAVE_DE_SITE=.*/", "HCAPTCHA_CHAVE_DE_SITE='$site_key'", $envContent);
            $envContent = preg_replace("/HCAPTCHA_CHAVE_SECRETA=.*/", "HCAPTCHA_CHAVE_SECRETA='$secret_key'", $envContent);

            // Salvar as alterações no .env
            file_put_contents($envPath, $envContent);

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'hCaptcha configurado com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'captcha');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    } else {
        echo "Método inválido.";
    }
}

if (isset($_POST['saveTurnstile'])) {
    // Verifique se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Tabela onde sera feita a alteracao
        $tabela = 'tb_checkout';

        //Id da tabela
        $id = '1';

        $captcha_type = 'turnstile';
        $site_key = $_POST['turnstile_site'];
        $secret_key = $_POST['turnstile_secret'];
        $turnstile_config = $_POST['turnstile_config'];
        $turnstile_pages = $_POST['turnstile_pages'] ?? null;

        // Atualizar as configurações no banco de dados
        $sql = "UPDATE $tabela SET turnstile = true WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();

            // Inserir ou atualizar as páginas dependendo da configuração
            if ($turnstile_config === "all") {
                // Adiciona todas as páginas à tabela
                $pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
                foreach ($pages as $page) {
                    // Verifica se já existe um registro para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se já existir, faz um UPDATE
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = :captcha_type WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->bindParam(':captcha_type', $captcha_type);
                        $stmtUpdate->execute();
                    } else {
                        // Se não existir, faz um INSERT
                        $sqlInsert = "INSERT INTO tb_page_captchas (page_name, captcha_type) VALUES (:page_name, :captcha_type)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->bindParam(':page_name', $page);
                        $stmtInsert->bindParam(':captcha_type', $captcha_type);
                        $stmtInsert->execute();
                    }
                }
            } elseif ($turnstile_config === "select" && isset($turnstile_pages)) {
                // Adiciona as páginas selecionadas pelo usuário
                $selected_pages = $turnstile_pages;
                foreach ($selected_pages as $page) {
                    // Verifica se já existe um registro para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se já existir, faz um UPDATE
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = :captcha_type WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->bindParam(':captcha_type', $captcha_type);
                        $stmtUpdate->execute();
                    } else {
                        // Se não existir, faz um INSERT
                        $sqlInsert = "INSERT INTO tb_page_captchas (page_name, captcha_type) VALUES (:page_name, :captcha_type)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->bindParam(':page_name', $page);
                        $stmtInsert->bindParam(':captcha_type', $captcha_type);
                        $stmtInsert->execute();
                    }
                }

                // Atualiza as páginas que não foram selecionadas para 'none'
                $all_pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
                $pages_to_update = array_diff($all_pages, $selected_pages);

                foreach ($pages_to_update as $page) {
                    // Verifica se o registro existe para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE captcha_type = :captcha_type AND page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':captcha_type', $captcha_type);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se existir, faz um UPDATE para mudar para 'none'
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->execute();
                    }
                }
            } elseif ($turnstile_config === "select") {
                // Atualiza as páginas que não foram selecionadas para 'none'
                $remove_all_pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];

                foreach ($remove_all_pages as $page) {
                    // Verifica se o registro existe para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE captcha_type = :captcha_type AND page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':captcha_type', $captcha_type);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se existir, faz um UPDATE para mudar para 'none'
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->execute();
                    }
                }
            } else if ($turnstile_config === "none") {
                // Remove todas as páginas da tabela
                $pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
                foreach ($pages as $page) {
                    // Verifica se o registro existe para a página
                    $sqlCheck = "SELECT COUNT(*) FROM tb_page_captchas WHERE captcha_type = :captcha_type AND page_name = :page_name";
                    $stmtCheck = $conn->prepare($sqlCheck);
                    $stmtCheck->bindParam(':captcha_type', $captcha_type);
                    $stmtCheck->bindParam(':page_name', $page);
                    $stmtCheck->execute();
                    $count = $stmtCheck->fetchColumn();

                    if ($count > 0) {
                        // Se existir, faz um UPDATE para mudar para 'none'
                        $sqlUpdate = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE page_name = :page_name";
                        $stmtUpdate = $conn->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':page_name', $page);
                        $stmtUpdate->execute();
                    }
                }
            }

            // Atualizar o arquivo .env
            $envPath = dirname(__DIR__, 2) . '/.env';
            $envContent = file_get_contents($envPath);

            // Substituir as chaves existentes
            $envContent = preg_replace("/TURNSTILE_CHAVE_DE_SITE=.*/", "TURNSTILE_CHAVE_DE_SITE='$site_key'", $envContent);
            $envContent = preg_replace("/TURNSTILE_CHAVE_SECRETA=.*/", "TURNSTILE_CHAVE_SECRETA='$secret_key'", $envContent);

            // Salvar as alterações no .env
            file_put_contents($envPath, $envContent);

            // Exibir a modal após salvar as informações
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'Turnstile configurado com sucesso!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'captcha');
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    } else {
        echo "Método inválido.";
    }
}

if (isset($_GET['captcha']) && $_GET['captcha'] == 'hcaptcha') {
    // Remover hCaptcha do banco de dados
    $sql = "UPDATE tb_checkout SET hcaptcha = 0 WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Remover as páginas associadas ao hCaptcha
    $pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
    foreach ($pages as $page) {
        $sqlDelete = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE captcha_type = :captcha_type AND page_name = :page_name";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bindParam(':captcha_type', 'hcaptcha');
        $stmtDelete->bindParam(':page_name', $page);
        $stmtDelete->execute();
    }

    // Atualizar o arquivo .env
    $envPath = dirname(__DIR__, 2) . '/.env';
    $envContent = file_get_contents($envPath);

    // Remover as entradas do hCaptcha
    $envContent = preg_replace("/HCAPTCHA_CHAVE_DE_SITE=.*/", "HCAPTCHA_CHAVE_DE_SITE=''", $envContent);
    $envContent = preg_replace("/HCAPTCHA_CHAVE_SECRETA=.*/", "HCAPTCHA_CHAVE_SECRETA=''", $envContent);

    // Salvar alterações no .env
    file_put_contents($envPath, $envContent);

    // Exibir a modal após salvar as informações
    $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
    $_SESSION['msg'] = 'hCaptcha removido com sucesso!';

    // Redireciona para a página de configuração
    header("Location: " . INCLUDE_PATH_ADMIN . "captcha");
    exit();
}

if (isset($_GET['captcha']) && $_GET['captcha'] == 'turnstile') {
    // Remover Turnstile do banco de dados
    $sql = "UPDATE tb_checkout SET turnstile = 0 WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Remover as páginas associadas ao Turnstile
    $pages = ['doacao', 'login', 'enviar_email', 'recuperar_senha'];
    foreach ($pages as $page) {
        $sqlDelete = "UPDATE tb_page_captchas SET captcha_type = 'none' WHERE captcha_type = :captcha_type AND page_name = :page_name";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bindValue(':captcha_type', 'turnstile');
        $stmtDelete->bindParam(':page_name', $page);
        $stmtDelete->execute();
    }

    // Atualizar o arquivo .env
    $envPath = dirname(__DIR__, 2) . '/.env';
    $envContent = file_get_contents($envPath);

    // Remover as entradas do Turnstile
    $envContent = preg_replace("/TURNSTILE_CHAVE_DE_SITE=.*/", "TURNSTILE_CHAVE_DE_SITE=''", $envContent);
    $envContent = preg_replace("/TURNSTILE_CHAVE_SECRETA=.*/", "TURNSTILE_CHAVE_SECRETA=''", $envContent);

    // Salvar alterações no .env
    file_put_contents($envPath, $envContent);

    // Exibir a modal após salvar as informações
    $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
    $_SESSION['msg'] = 'Turnstile removido com sucesso!';

    // Redireciona para a página de configuração
    header("Location: " . INCLUDE_PATH_ADMIN . "captcha");
    exit();
}

//Voltar para a pagina do formulario
header('Location: ' . INCLUDE_PATH_ADMIN . 'captcha');
?>