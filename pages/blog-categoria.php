<?php
// pages/categoria.php

// 1) Verifica se o parâmetro “id” foi enviado
if (empty($_GET['id'])) {
    return [
        'not_found' => true
    ];
}

$categoriaId = (int) $_GET['id'];

// 2) Busca a categoria pelo ID
$stmt = $conn->prepare("
    SELECT *
    FROM tb_blog_categorias
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$categoriaId]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($categoria)) {
    // Se não existe, sinaliza “not_found”
    return [
        'not_found' => true
    ];
}

// 3) Busca os primeiros 4 posts dessa categoria
$stmt = $conn->prepare("
    SELECT p.*
    FROM tb_blog_categoria_posts cp
    JOIN tb_blog_posts p ON cp.post_id = p.id
    WHERE cp.categoria_id = ?
    ORDER BY p.data_publicacao DESC
    LIMIT 4
");
$stmt->execute([$categoriaId]);
$postsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$qtdPostsIniciais = count($postsRaw);

// 4) Para cada post, busca categorias, formata imagem e data
$posts = [];
foreach ($postsRaw as $post) {
    // Imagem de capa do post
    $imagemUrl = !empty($post['imagem'])
        ? str_replace(
            ' ',
            '%20',
            INCLUDE_PATH . "files/blog/{$post['id']}/{$post['imagem']}"
          )
        : INCLUDE_PATH . "assets/preview-image/product.jpg";

    // Busca categorias desse post
    $stmtCat = $conn->prepare("
        SELECT c.*
        FROM tb_blog_categoria_posts cp
        JOIN tb_blog_categorias c
          ON cp.categoria_id = c.id
        WHERE cp.post_id = ?
    ");
    $stmtCat->execute([$post['id']]);
    $cats = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    // Monta lista básica de categorias apenas com id e nome
    $catsList = [];
    foreach ($cats as $c) {
        $catsList[] = [
            'id'   => $c['id'],
            'nome' => $c['nome'],
        ];
    }

    // Formata a data de publicação para “DD/MM/AAAA”
    $dataFormatada = date("d/m/Y", strtotime($post["data_publicacao"]));

    $posts[] = [
        'id'               => $post['id'],
        'titulo'           => $post['titulo'],
        'imagem'           => $imagemUrl,
        'data_publicacao'  => $dataFormatada,
        'categorias'       => $catsList,
    ];
}

// 5) Retorna o contexto para o Twig
return [
    'page_title'             => $categoria['nome'],
    'not_found'         => false,
    'categoria'         => $categoria,
    'posts'             => $posts,
    'initial_count'     => $qtdPostsIniciais,
];