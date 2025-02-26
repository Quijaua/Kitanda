<?php
    session_start();
    session_destroy();
    include('../../config.php');
    session_start();
    ob_start();
    $_SESSION['msgcad'] = "Deslogado com sucesso!";
    header("Location: " . INCLUDE_PATH . "login/");
    exit();
?>