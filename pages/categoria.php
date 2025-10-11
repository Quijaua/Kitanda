<?php
// pages/categoria.php
// 1) Limite e página
$limite = isset($vitrine_limite) && is_numeric($vitrine_limite) && $vitrine_limite > 0
        ? (int) $vitrine_limite
        : (isset($_GET['limite']) && is_numeric($_GET['limite']) ? (int) $_GET['limite'] : 50);
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $limite;

// 2) Busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$busca_param = "%{$busca}%";

// 3) ID da categoria
if (empty($_GET['id'])) {
    return ['not_found' => true];
}
$categoriaId = (int) $_GET['id'];

// 4) Verifica categoria
$stmt = $conn->prepare("
    SELECT * FROM tb_categorias WHERE id = ? LIMIT 1
");
$stmt->execute([$categoriaId]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$categoria) {
    return ['not_found' => true];
}

// 5) Conta total de produtos na categoria
$sql_count = "
    SELECT COUNT(DISTINCT p.id) AS total
    FROM tb_categoria_produtos cp
    JOIN tb_produtos p ON cp.produto_id = p.id
    WHERE cp.categoria_id = :categoria_id
      AND p.vitrine = 1
";
if ($busca !== '') {
    $sql_count .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca)";
}
$total_stmt = $conn->prepare($sql_count);
$total_stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
if ($busca !== '') {
    $total_stmt->bindValue(':busca', $busca_param, PDO::PARAM_STR);
}
$total_stmt->execute();
$total_produtos = (int) $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ($total_produtos > 0) ? ceil($total_produtos / $limite) : 1;

// 6) Busca produtos paginados
$sql = "
    SELECT p.*, pi.imagem as imagem_produto, u.nome as empreendedora
    FROM tb_categoria_produtos cp
    JOIN tb_produtos p ON cp.produto_id = p.id
    LEFT JOIN tb_produto_imagens pi ON pi.produto_id = p.id
    LEFT JOIN tb_clientes u ON u.id = p.criado_por
    WHERE cp.categoria_id = :categoria_id
      AND p.vitrine = 1
";
if ($busca !== '') {
    $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca)";
}
$sql .= " ORDER BY p.id DESC LIMIT :limite OFFSET :offset";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
if ($busca !== '') {
    $stmt->bindValue(':busca', $busca_param, PDO::PARAM_STR);
}
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produtosRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7) Formata produtos (mesma lógica que você já tinha)
$produtos = [];
foreach ($produtosRaw as $produto) {
    // Imagem de capa do produto
    $imagemUrl = isset($produto['imagem_produto'])
        ? str_replace(
            ' ',
            '%20',
            INCLUDE_PATH . "files/produtos/{$produto['id']}/{$produto['imagem_produto']}"
          )
        : INCLUDE_PATH . "assets/preview-image/product.jpg";

    // Busca categorias desse produto
    $stmtCat = $conn->prepare("
        SELECT c.*
        FROM tb_categoria_produtos cp
        JOIN tb_categorias c
          ON cp.categoria_id = c.id
        WHERE cp.produto_id = ?
    ");
    $stmtCat->execute([$produto['id']]);
    $cats = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    // Monta lista básica de categorias apenas com id e nome
    $catsList = [];
    foreach ($cats as $c) {
        $catsList[] = [
            'id'   => $c['id'],
            'nome' => $c['nome'],
        ];
    }

    $produtos[] = [
        'id'               => $produto['id'],
        'titulo'           => $produto['titulo'],
        'codigo_produto'   => $produto['codigo_produto'],
        'preco'            => $produto['preco'],
        'link'             => $produto['link'],
        'empreendedora'    => $produto['empreendedora'],
        'imagem'           => $imagemUrl,
        'categorias'       => $catsList,
    ];
}

// 8) Função de paginação
function criarPaginacao($atual, $total, $limite, $categoriaId, $busca = '')
{
    if ($total <= 1) {
        return ''; // sem paginação
    }

    // base de query
    $base = "?id={$categoriaId}&limite={$limite}";
    if ($busca !== '') {
        $base .= "&busca=" . urlencode($busca);
    }

    $html = '<ul class="pagination">';

    // Anterior
    if ($atual > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $base . '&pagina=' . ($atual - 1) . '">« Anterior</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">« Anterior</span></li>';
    }

    // Páginas
    for ($i = 1; $i <= $total; $i++) {
        $active = $i === $atual ? ' active' : '';
        $html .= "<li class=\"page-item{$active}\">";
        $html .= '<a class="page-link" href="' . $base . '&pagina=' . $i . "\">{$i}</a>";
        $html .= '</li>';
    }

    // Próximo
    if ($atual < $total) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $base . '&pagina=' . ($atual + 1) . '">Próximo »</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Próximo »</span></li>';
    }

    $html .= '</ul>';
    return $html;
}

$paginationHtml = criarPaginacao(
    $pagina_atual,
    $total_paginas,
    $limite,
    $categoriaId,
    $busca
);

// 9) Retorna para o Twig
return [
    'title'           => $categoria['nome'],
    'not_found'       => false,
    'categoria'       => $categoria,
    'produtos'        => $produtos,
    'initial_count'   => count($produtosRaw),
    'pagination_html' => $paginationHtml,
    'total_produtos'  => $total_produtos,
    'pagina_atual'    => $pagina_atual,
    'busca'           => $busca,
];