<?php
include('../../config.php');
session_start();

if (!empty($_COOKIE['remember_me']) && !empty($_SESSION['user_id'])) {
    $stmt = $conn->prepare("
        DELETE FROM tb_user_tokens
        WHERE user_id = :user_id AND token = :token
    ");
    $stmt->execute([
        'user_id'    => $_SESSION['user_id'],
        'token' => $_COOKIE['remember_me']
    ]);

    // Apaga o cookie
    setcookie('remember_me', '', time() - 3600, '/');
}

// Destroi a sessão
session_destroy();

// Inicia nova sessão só para a mensagem
session_start();
ob_start();
$_SESSION['msgcad'] = "Deslogado com sucesso!";
header("Location: " . INCLUDE_PATH . "login/");
exit();