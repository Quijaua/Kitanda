<?php
    session_start();
    include('../../config.php'); // Arquivo de conexão com o banco

    // Coleta os dados enviados via POST e faz uma validação simples
    $email        = trim($_POST['email'] ?? '');
    $name         = trim($_POST['name'] ?? '');
    $cpf          = trim($_POST['cpf'] ?? '');
    $birthDate    = trim($_POST['birthDate'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $zipcode      = trim($_POST['zipcode'] ?? '');
    $street       = trim($_POST['street'] ?? '');
    $streetNumber = trim($_POST['streetNumber'] ?? '');
    $complement   = trim($_POST['complement'] ?? '');
    $district     = trim($_POST['district'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $state        = trim($_POST['state'] ?? '');
    $country      = trim($_POST['country'] ?? '');
    $foreign      = isset($_POST['foreign']) && !empty($_POST['foreign']) ? 1 : 0;
    $newsletter   = isset($_POST['newsletter']) && !empty($_POST['newsletter']) ? 1 : 0;
    $terms        = isset($_POST['terms']) && !empty($_POST['terms']) ? 1 : 0;

    // Validação básica (exemplo: email e nome são obrigatórios)
    if (empty($email) || empty($name)) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Email e nome são obrigatórios.']);
        exit;
    }

    // Opção 1: Salvar em sessão
    $_SESSION['checkout_data'] = [
        'email'        => $email,
        'name'         => $name,
        'cpf'          => $cpf,
        'birthDate'    => $birthDate,
        'phone'        => $phone,
        'zipcode'      => $zipcode,
        'street'       => $street,
        'streetNumber' => $streetNumber,
        'complement'   => $complement,
        'district'     => $district,
        'city'         => $city,
        'state'        => $state,
        'country'      => $country,
        'foreign'      => $foreign,
        'newsletter'   => $newsletter,
        'terms'        => $terms
    ];

    // Opção 2: Salvar em um cookie (expira em 1 hora)
    $cookieData = json_encode($_SESSION['checkout_data']);
    setcookie('checkout_data', $cookieData, time() + 3600, "/");

    // // Opção 3: Salvar no banco de dados (certifique-se de ter a tabela tb_checkout)
    // // Exemplo: a tabela tb_checkout possui as colunas: email, name, cpf, birthDate, phone, zipcode, street, streetNumber, complement, district, city, state, private, newsletter, terms
    // try {
    //     $stmt = $conn->prepare("INSERT INTO tb_checkout 
    //         (email, name, cpf, birthDate, phone, zipcode, street, streetNumber, complement, district, city, state, private, newsletter, terms)
    //         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    //     $stmt->execute([
    //         $email,
    //         $name,
    //         $cpf,
    //         $birthDate,
    //         $phone,
    //         $zipcode,
    //         $street,
    //         $streetNumber,
    //         $complement,
    //         $district,
    //         $city,
    //         $state,
    //         $private,
    //         $newsletter,
    //         $terms
    //     ]);
    // } catch (PDOException $e) {
    //     echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao salvar no banco: ' . $e->getMessage()]);
    //     exit;
    // }

    // Retorna resposta JSON para o AJAX
    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Dados salvos com sucesso.']);