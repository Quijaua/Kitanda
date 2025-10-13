<?php
// pages/empreendedoras.php

// Defina o limite inicial de empreendedoras a serem exibidas
$limit = 6;

// 1) Busca as primeiras $limit empreendedoras
$stmt = $conn->prepare("
    SELECT *
    FROM tb_lojas
    WHERE nome != ''
    ORDER BY nome
    LIMIT :limit
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$empreendedoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Formata cada empreendedora (URL da imagem e endereço)
foreach ($empreendedoras as &$e) {
    // Imagem de perfil
    $e['imagem'] = !empty($e['imagem'])
        ? str_replace(
            ' ',
            '%20',
            INCLUDE_PATH . "files/lojas/{$e['id']}/perfil/{$e['imagem']}"
          )
        : INCLUDE_PATH . "assets/preview-image/profile.jpg";

    // Monta o campo "address"
    $address = 'Não informado';
    if (!empty($e['cidade']) && !empty($e['estado'])) {
        $address = htmlspecialchars($e['cidade']) . '/' . htmlspecialchars($e['estado']);
    } elseif (!empty($e['cidade'])) {
        $address = htmlspecialchars($e['cidade']);
    } elseif (!empty($e['estado'])) {
        $address = htmlspecialchars($e['estado']);
    }
    $e['address'] = $address;
}
unset($e);

// 3) Retorna para o index.php (via include) as variáveis que o Twig vai precisar
return [
    'empreendedoras' => $empreendedoras,
    'limit'          => $limit,
    'initial_count'  => count($empreendedoras),
];