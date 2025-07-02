<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config.php');

// Recebe offset e limit via POST (no JS está enviando limit = 4)
$categoria_id = $_POST['categoria_id'];
$offset = intval($_POST['offset'] ?? 0);
$limit  = intval($_POST['limit']  ?? 4);

if (!isset($categoria_id) || empty($categoria_id)) {
    echo json_encode(['status' => 'error']);
    exit;
}

// 1) Busca os posts com LIMIT e OFFSET
$stmt = $conn->prepare("
    SELECT p.*
    FROM tb_blog_categoria_posts cp
    JOIN tb_blog_posts p ON cp.post_id = p.id
    WHERE cp.categoria_id = :categoria_id
    ORDER BY data_publicacao DESC
    LIMIT :offset, :limit
");
$stmt->bindValue(':categoria_id', $categoria_id, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Para cada post, ajusta o caminho da imagem e busca categorias
foreach ($posts as &$post) {
    // Ajusta o campo “imagem”
    $post['imagem'] = !empty($post['imagem'])
        ? str_replace(
            ' ',
            '%20',
            INCLUDE_PATH . "files/blog/{$post['id']}/{$post['imagem']}"
        )
        : INCLUDE_PATH . "assets/preview-image/product.jpg";

    // Busca categorias relacionadas a este post
    $stmtCat = $conn->prepare("
        SELECT c.*
        FROM tb_blog_categoria_posts cp
        JOIN tb_blog_categorias c ON cp.categoria_id = c.id
        WHERE cp.post_id = ?
    ");
    $stmtCat->execute([$post['id']]);
    $post['categorias'] = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
}
unset($post);

// 3) Retorna JSON com status e array de posts (cada post inclui .imagem e .categorias)
echo json_encode([
    'status' => 'sucesso',
    'data'   => $posts
]);