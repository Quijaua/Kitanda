<?php
session_start();
ob_start();

// Verifique se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Inclui o arquivo 'config.php'
    include('../../config.php');
    require_once('services/BrevoService.php');

    //Informacoes coletadas pelo metodo POST
    if ( isset($_POST['bulk_email_body']) && $_POST['bulk_email_body'] != '' )
        {
            $tabela = 'tb_clientes';
            $sql = "SELECT nome, email FROM $tabela WHERE newsletter = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $title = $_POST['bulk_email_title'];
            $body = $_POST['bulk_email_body'];

            $data = [
                'title' => $title,
                'body' => $body,
                'recipients' => $resultados
            ];

            // Instantiate BrevoService class
            $service = new BrevoService();

            // Send emails and insert data into database
            if ( $service->sendEmail($data) ) {
                $tabela_emails = 'tb_bulk_emails';
                $sql_emails = "INSERT INTO $tabela_emails (title, body) VALUES (:title, :body)";
                $stmt_emails = $conn->prepare($sql_emails);
                $stmt_emails->bindParam(':title', $title);
                $stmt_emails->bindParam(':body', $body);
                $stmt_emails->execute();
            }

            // Exibir a modal
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'Os emails em massa estão sendo enviados!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'novidades');
        }
    else
        {
            // Exibir erro na modal
            $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
            $_SESSION['msg'] = 'O corpo do email deve ser preenchido!';

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'novidades');
        };
}