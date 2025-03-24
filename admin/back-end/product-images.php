<?php
    include_once('../../config.php');

    if (isset($_GET['produto_id'])) {
        $produto_id = intval($_GET['produto_id']);

        $stmt = $conn->prepare("SELECT imagem FROM tb_produto_imagens WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($imagens);
    }