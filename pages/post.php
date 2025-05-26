<?php
// pages/post.php

// 1) Verifica se o parâmetro “id” foi enviado
if (empty($_GET['id'])) {
    // Retornamos sinal de “não encontrado”
    return [
        'not_found' => true
    ];
}

$id = (int) $_GET['id'];

// 2) Busca o post pelo ID
$stmt = $conn->prepare("
    SELECT *
    FROM tb_blog_posts
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($post)) {
    // Se não existe, sinaliza “not_found”
    return [
        'not_found' => true
    ];
}

// 3) Formata a URL da imagem (ou null se não houver)
$post['imagem'] = !empty($post['imagem'])
    ? str_replace(
        ' ',
        '%20',
        INCLUDE_PATH . "files/blog/{$post['id']}/{$post['imagem']}"
      )
    : null;

// 4) Busca as categorias associadas a este post
$stmtCat = $conn->prepare("
    SELECT c.*
    FROM tb_blog_categoria_posts cp
    JOIN tb_blog_categorias c
      ON cp.categoria_id = c.id
    WHERE cp.post_id = ?
");
$stmtCat->execute([$post['id']]);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// 5) Formata as tags (JSON de objetos com campo “value”)
//    Observamos que $post['tags'] é um JSON como: [{"value":"tag1"},{"value":"tag2"}, …]
//    Queremos juntar em string, limitando a 5000 chars e separando por espaço.
function formatarTags(?string $jsonTags, int $limit = 5000, string $sep = ', '): string
{
    $dados = @json_decode($jsonTags ?? '[]', true);
    if (!is_array($dados)) {
        $dados = [];
    }
    $valores = array_column($dados, 'value');
    $todas   = implode($sep, $valores);

    return mb_strlen($todas) > $limit
        ? mb_substr($todas, 0, $limit) . '...'
        : $todas;
}

$tagsString = formatarTags($post['tags']);

// 6) Formata a data de publicação para “DD/MM/AAAA”
$dataFormatada = date("d/m/Y", strtotime($post["data_publicacao"]));

// 7) Retorna o contexto para o Twig
return [
    'not_found'      => false,
    'post'           => $post,
    'categorias'     => $categorias,
    'tags_string'    => $tagsString,
    'data_publicacao'=> $dataFormatada,
];