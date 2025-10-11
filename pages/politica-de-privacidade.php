<?php
// pages/politica-de-privacidade.php

// ID que você deseja pesquisar
$id = 1;

// 2) Busca o post pelo ID
$stmt = $conn->prepare("
    SELECT privacy_policy
    FROM tb_mensagens
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$privacy_policy = $stmt->fetch(PDO::FETCH_ASSOC)['privacy_policy'];

if (empty($privacy_policy)) {
    // Se não existe, sinaliza “not_found”
    return [
        'not_found' => true
    ];
}

// 11) Retorna o contexto para o Twig
return [
    'title'          => 'Politica de privacidade',
    'not_found'      => false,
    'privacy_policy' => $privacy_policy,
];