<?php
// Definir o limite padrão de produtos por página
$limite = isset($vitrine_limite) && is_numeric($vitrine_limite) && $vitrine_limite > 0
        ? (int) $vitrine_limite
        : (isset($_GET['limite']) && is_numeric($_GET['limite']) ? (int) $_GET['limite'] : 50);
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $limite;

// Captura o termo de busca, se existir
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Consulta para contar o total de produtos (com busca)
$sql_count = "SELECT COUNT(DISTINCT id) AS total FROM tb_produtos WHERE vitrine = 1";
if (!empty($busca)) {
    $sql_count .= " AND (nome LIKE :busca OR descricao LIKE :busca)";
}
$total_stmt = $conn->prepare($sql_count);
if (!empty($busca)) {
    $busca_param = "%{$busca}%";
    $total_stmt->bindParam(':busca', $busca_param, PDO::PARAM_STR);
}
$total_stmt->execute();
$total_produtos = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_produtos / $limite);

// Consulta para buscar os produtos paginados (com busca)
$sql = "
    SELECT p.*, pi.imagem, c.nome AS empreendedora 
    FROM tb_produtos p
    LEFT JOIN (
        SELECT produto_id, MIN(imagem) AS imagem
        FROM tb_produto_imagens 
        GROUP BY produto_id
    ) pi ON p.id = pi.produto_id
    LEFT JOIN tb_clientes c ON c.id = p.criado_por
    WHERE p.vitrine = 1";

if (!empty($busca)) {
    $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca)";
}

$sql .= " ORDER BY p.id DESC LIMIT :limite OFFSET :offset";

$stmt = $conn->prepare($sql);
if (!empty($busca)) {
    $stmt->bindParam(':busca', $busca_param, PDO::PARAM_STR);
}
$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para criar os links de paginação
function criarPaginacao($pagina_atual, $total_paginas, $limite) {
    if ($total_paginas <= 1) {
        return '<div class="d-flex">
                    <ul class="pagination ms-auto mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M15 6l-6 6l6 6"></path></svg>
                                Anterior
                            </a>
                        </li>
                        <div class="d-flex">
                            <ul class="pagination ms-auto mb-0">
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            </ul>
                        </div>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-disabled="true">
                                Próximo
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M9 6l6 6l-6 6"></path></svg>
                            </a>
                        </li>
                    </ul>
                </div>';
    }

    $html = '<div class="d-flex">
                <ul class="pagination ms-auto mb-0">';

    // Botão "Anterior"
    $prev_disabled = ($pagina_atual == 1) ? 'disabled' : '';
    $prev_link = $pagina_atual > 1 ? '?pagina=' . ($pagina_atual - 1) . '&limite=' . $limite : '#';
    $html .= '<li class="page-item ' . $prev_disabled . '">
                <a class="page-link" href="' . $prev_link . '" tabindex="-1" aria-disabled="' . ($pagina_atual == 1 ? 'true' : 'false') . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M15 6l-6 6l6 6"></path></svg>
                    Anterior
                </a>
              </li>';

    // Criar botões de página
    for ($i = 1; $i <= $total_paginas; $i++) {
        $active = $pagina_atual == $i ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '">
                    <a class="page-link" href="?pagina=' . $i . '&limite=' . $limite . '">' . $i . '</a>
                  </li>';
    }

    // Botão "Próximo"
    $next_disabled = ($pagina_atual == $total_paginas) ? 'disabled' : '';
    $next_link = $pagina_atual < $total_paginas ? '?pagina=' . ($pagina_atual + 1) . '&limite=' . $limite : '#';
    $html .= '<li class="page-item ' . $next_disabled . '">
                <a class="page-link" href="' . $next_link . '" aria-disabled="' . ($pagina_atual == $total_paginas ? 'true' : 'false') . '">
                    Próximo
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M9 6l6 6l-6 6"></path></svg>
                </a>
              </li>';

    $html .= '</ul></div>';

    return $html;
}

$paginationHtml = criarPaginacao($pagina_atual, $total_paginas, $limite);

// 6) Monta o array de contexto desta página
$context_produtos = [
    'produtos'        => $produtos,
    'limite'          => $limite,
    'pagina_atual'    => $pagina_atual,
    'total_paginas'   => $total_paginas,
    'busca'           => $busca,
    'total_produtos'  => $total_produtos,
    'pagination_html' => $paginationHtml,
];

// Retorna tudo para o index.php
return $context_produtos;
?>