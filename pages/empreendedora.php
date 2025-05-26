<?php
// pages/empreendedora.php

// Verifica se o parâmetro “id” foi enviado
if (empty($_GET['id'])) {
    $_SESSION['error_msg'] = 'Insira o link da empreendedora.';
    header('Location: ' . INCLUDE_PATH . 'empreendedoras');
    exit;
}

$id = (int) $_GET['id'];

// 1) Busca a empreendedora pelo ID
$stmt = $conn->prepare("SELECT * FROM tb_lojas WHERE id = ?");
$stmt->execute([$id]);
$e = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($e)) {
    $_SESSION['error_msg'] = 'Empreendedora não encontrada.';
    header('Location: ' . INCLUDE_PATH . 'empreendedoras');
    exit;
}

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

// 4) Busca os produtos “Destaques” dessa empreendedora (limit 6)
$stmt = $conn->prepare("
    SELECT p.*, pi.imagem
    FROM tb_produtos p
    LEFT JOIN (
        SELECT produto_id, MIN(imagem) AS imagem
        FROM tb_produto_imagens
        GROUP BY produto_id
    ) pi ON p.id = pi.produto_id
    WHERE p.criado_por = ?
      AND p.vitrine = 1
    ORDER BY p.vitrine DESC, p.nome ASC
    LIMIT 6
");
$stmt->execute([$e['vendedora_id']]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajusta URL e formata preço para cada produto
foreach ($produtos as &$produto) {
    $produto['imagem'] = !empty($produto['imagem'])
        ? str_replace(
            ' ',
            '%20',
            INCLUDE_PATH . "files/produtos/{$produto['id']}/{$produto['imagem']}"
          )
        : INCLUDE_PATH . "assets/preview-image/product.jpg";

    $produto['preco'] = number_format($produto['preco'], 2, ',', '.');
}
unset($produto);

// 5) Retorna o array de contexto para o Twig
return [
    'e'        => $e,
    'produtos' => $produtos,
];