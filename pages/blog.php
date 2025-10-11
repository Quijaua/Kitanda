<?php
// pages/blog.php

// 1) Busca todas as categorias (agrupadas por id, em ordem decrescente)
$stmt = $conn->prepare("
    SELECT *
    FROM tb_blog_categorias
    GROUP BY id
    ORDER BY id DESC
");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Busca os primeiros 4 posts
$stmt = $conn->prepare("
    SELECT *
    FROM tb_blog_posts
    ORDER BY data_publicacao DESC
    LIMIT 4
");
$stmt->execute();
$postsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$qtdPostsIniciais = count($postsRaw);

// 3) Para cada post, busca suas categorias e formata URL de imagem
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

    // Monta o array de categorias apenas com id e nome
    $catsList = [];
    foreach ($cats as $c) {
        $catsList[] = [
            'id'   => $c['id'],
            'nome' => $c['nome'],
        ];
    }

    $posts[] = [
        'id'               => $post['id'],
        'titulo'           => $post['titulo'],
        'imagem'           => $imagemUrl,
        'data_publicacao'  => $post['data_publicacao'],
        'categorias'       => $catsList,
    ];
}

// 4) Retorna tudo para o index.php
return [
    'title'              => 'Blog',
    'categorias'         => $categorias,
    'posts'              => $posts,
    'initial_count'      => $qtdPostsIniciais,
];