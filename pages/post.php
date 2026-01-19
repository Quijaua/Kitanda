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

// 7) Busca 3 produtos para exibir na sidebar
$stmtSidebar = $conn->prepare("
    SELECT p.id, p.titulo, p.descricao, p.link, pi.imagem, pi.alt
    FROM tb_produtos p
    LEFT JOIN (
        SELECT produto_id, MIN(imagem) AS imagem, MIN(alt) AS alt
        FROM tb_produto_imagens
        GROUP BY produto_id
    ) pi ON p.id = pi.produto_id
    WHERE p.vitrine = 1
    ORDER BY p.id DESC
    LIMIT 3
");
$stmtSidebar->execute();
$sidebar_produtos = $stmtSidebar->fetchAll(PDO::FETCH_ASSOC);

// 8) Trunca a descrição para até 150 caracteres
foreach ($sidebar_produtos as &$produto) {
    $texto = trim(strip_tags($produto['descricao']));
    if (mb_strlen($texto) > 150) {
        $texto = mb_substr($texto, 0, 150) . '...';
    }
    $produto['descricao_curta'] = $texto;
}
unset($produto);

// 9) Trunca a descrição para até 150 caracteres
foreach ($sidebar_produtos as &$produto) {
    $texto = trim(strip_tags($produto['descricao']));
    if (mb_strlen($texto) > 150) {
        $texto = mb_substr($texto, 0, 150) . '...';
    }
    $produto['descricao_curta'] = $texto;
}
unset($produto);

// 10) Busca o post anterior (id < atual) e o próximo (id > atual)
$stmtPrev = $conn->prepare("
    SELECT id, titulo
    FROM tb_blog_posts
    WHERE id < ?
    ORDER BY id DESC
    LIMIT 1
");
$stmtPrev->execute([$id]);
$prev_post = $stmtPrev->fetch(PDO::FETCH_ASSOC);

$stmtNext = $conn->prepare("
    SELECT id, titulo
    FROM tb_blog_posts
    WHERE id > ?
    ORDER BY id ASC
    LIMIT 1
");
$stmtNext->execute([$id]);
$next_post = $stmtNext->fetch(PDO::FETCH_ASSOC);

// 11) Retorna o contexto para o Twig
return [
    'page_title'          => $post['titulo'],
    'not_found'      => false,
    'post'           => $post,
    'categorias'     => $categorias,
    'tags_string'    => $tagsString,
    'data_publicacao'=> $dataFormatada,

    'prev_post'      => $prev_post,
    'next_post'      => $next_post,

    'sidebar_produtos'=> $sidebar_produtos,
];