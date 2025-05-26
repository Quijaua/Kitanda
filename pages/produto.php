<?php
// pages/produto.php

if (empty($link)) {
    $_SESSION['error_msg'] = 'Insira o link do produto.';
    header('Location: ' . INCLUDE_PATH);
    exit;
}

// 1) Busca o produto individual pelo 'link'
$stmt = $conn->prepare("
    SELECT p.*, pi.imagem
    FROM tb_produtos p
    LEFT JOIN tb_produto_imagens pi
      ON p.id = pi.produto_id
    WHERE p.link = ?
    LIMIT 1
");
$stmt->execute([$link]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($produto)) {
    $_SESSION['error_msg'] = 'Produto não encontrado.';
    header('Location: ' . INCLUDE_PATH . 'produtos');
    exit;
}

// 2) Busca todas as imagens (para galeria/miniaturas)
$stmt = $conn->prepare("
    SELECT imagem
    FROM tb_produto_imagens
    WHERE produto_id = ?
");
$stmt->execute([$produto['id']]);
$imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3) Ajusta a URL da imagem principal
$produto['imagem'] = !empty($produto['imagem'])
                     ? str_replace(
                         ' ',
                         '%20',
                         INCLUDE_PATH . "files/produtos/{$produto['id']}/{$produto['imagem']}"
                       )
                     : INCLUDE_PATH . "assets/preview-image/product.jpg";

// 4) Formata o preço
$produto['preco'] = number_format($produto['preco'], 2, ',', '.');

// 5) Monta a URL completa do produto para compartilhamento
$produto['url'] = urlencode(INCLUDE_PATH . "p/{$produto['link']}");

$stmt = $conn->prepare("SELECT * FROM tb_lojas WHERE vendedora_id = ?");
$stmt->execute([$produto['criado_por']]);
$e = $stmt->fetch(PDO::FETCH_ASSOC);

// 2) Formata a URL da imagem de perfil
$e['imagem'] = !empty($e['imagem'])
    ? str_replace(
        ' ',
        '%20',
        INCLUDE_PATH . "files/lojas/{$e['id']}/perfil/{$e['imagem']}"
      )
    : INCLUDE_PATH . "assets/preview-image/profile.jpg";

// 3) Monta o campo “address” (Cidade/Estado ou “Não informado”)
$address = 'Não informado';
if (!empty($e['cidade']) && !empty($e['estado'])) {
    $address = htmlspecialchars($e['cidade']) . '/' . htmlspecialchars($e['estado']);
} elseif (!empty($e['cidade'])) {
    $address = htmlspecialchars($e['cidade']);
} elseif (!empty($e['estado'])) {
    $address = htmlspecialchars($e['estado']);
}
$e['address'] = $address;

// 6) Busca “Outros Produtos” (limitado a 4)
$stmt = $conn->prepare("
    SELECT p.*, pi.imagem
    FROM tb_produtos p
    LEFT JOIN (
        SELECT produto_id, MIN(imagem) AS imagem
        FROM tb_produto_imagens
        GROUP BY produto_id
    ) pi ON p.id = pi.produto_id
    WHERE p.id != ? AND p.vitrine = 1
    LIMIT 4
");
$stmt->execute([$produto['id']]);
$outros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajusta URL e preço para cada “outro produto”
foreach ($outros as &$p) {
    $p['imagem'] = !empty($p['imagem'])
                   ? str_replace(
                       ' ',
                       '%20',
                       INCLUDE_PATH . "files/produtos/{$p['id']}/{$p['imagem']}"
                     )
                   : INCLUDE_PATH . "assets/preview-image/product.jpg";
    $p['preco'] = number_format($p['preco'], 2, ',', '.');
}
unset($p);

// 7) Retorna todas as variáveis que o Twig precisa:
return [
    'produto'        => $produto,
    'e'              => $e,
    'imagens'        => $imagens,
    'outros_produtos'=> $outros,
];
?>