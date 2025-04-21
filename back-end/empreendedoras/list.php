<?php
header('Content-Type: application/json; charset=utf-8');

include_once('../../config.php');

$offset = intval($_POST['offset']  ?? 0);
$limit  = intval($_POST['limit']   ?? 6);

$stmt = $conn->prepare("SELECT *
                        FROM tb_lojas
                        ORDER BY nome
                        LIMIT :offset, :limit");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data['imagem'] = !empty($data['imagem'])
                  ? str_replace(' ', '%20', INCLUDE_PATH . "files/lojas/{$data['id']}/perfil/{$data['imagem']}")
                  : INCLUDE_PATH . "assets/preview-image/profile.jpg";

echo json_encode(['status' => 'sucesso', 'data' => $data]);