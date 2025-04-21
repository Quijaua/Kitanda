<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config.php');

$offset = intval($_POST['offset']  ?? 0);
$limit  = intval($_POST['limit']   ?? 6);

$stmt = $conn->prepare("
    SELECT *
    FROM tb_lojas
    WHERE nome != ''
    ORDER BY nome
    LIMIT :offset, :limit
");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->execute();
$lojas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajusta o campo imagem *em cada loja*
foreach ($lojas as &$loja) {
    $loja['imagem'] = !empty($loja['imagem'])
        ? str_replace(
            ' ',
            '%20',
            INCLUDE_PATH . "files/lojas/{$loja['id']}/perfil/{$loja['imagem']}"
        )
        : INCLUDE_PATH . "assets/preview-image/profile.jpg";
}
unset($loja);

echo json_encode([
    'status' => 'sucesso',
    'data'   => $lojas
]);