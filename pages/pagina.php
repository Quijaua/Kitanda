<?php
// pages/pagina.php

if (empty($_GET['link']) && empty($link)) {
    // Retornamos sinal de “não encontrado”
    return [
        'not_found' => true
    ];
}

if (!empty($_GET['link'])) {
    $link = $_GET['link'];
}

// 2) Busca o pagina pelo ID
$stmt = $conn->prepare("
    SELECT *
    FROM tb_paginas_conteudo
    WHERE slug = ?
    LIMIT 1
");
$stmt->execute([$link]);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($pagina)) {
    // Se não existe, sinaliza “not_found”
    return [
        'not_found' => true
    ];
}

// 3) Formata a URL da imagem (ou null se não houver)
$pagina['imagem'] = !empty($pagina['imagem'])
    ? str_replace(
        ' ',
        '%20',
        INCLUDE_PATH . "files/paginas/{$pagina['id']}/{$pagina['imagem']}"
      )
    : null;

// 6) Formata a data de publicação para “DD/MM/AAAA”
$dataFormatada = date("d/m/Y", strtotime($pagina["criado_em"]));

// 11) Retorna o contexto para o Twig
return [
    'page_title'          => 'Home',
    'not_found'      => false,
    'pagina'           => $pagina,
    'data_publicacao'=> $dataFormatada,
];