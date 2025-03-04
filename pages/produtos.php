<?php
// Definir o limite padrão de produtos por página
$limite = isset($_GET['limite']) && is_numeric($_GET['limite']) ? (int) $_GET['limite'] : 50;
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
    SELECT p.*, pi.imagem 
    FROM tb_produtos p
    LEFT JOIN (
        SELECT produto_id, MIN(imagem) AS imagem
        FROM tb_produto_imagens 
        GROUP BY produto_id
    ) pi ON p.id = pi.produto_id
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
?>

<style>
    .card-img-top {
        width: 100%;
        aspect-ratio: 1/1;
        object-fit: cover;
        display: block;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Produtos</h2>
                <div class="text-secondary mt-1">
                    <?= ($total_produtos > 0) ? ($offset + 1) . '-' . min($offset + $limite, $total_produtos) . ' de ' . $total_produtos . ' produtos' : 'Nenhum produto encontrado'; ?>
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <form method="GET" class="d-flex">
                    <div class="me-3">
                        <div class="input-icon">
                            <input type="text" name="busca" value="<?php echo htmlspecialchars($busca); ?>" class="form-control" placeholder="Pesquisar…">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                    <path d="M21 21l-6 -6"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-3">
                        Buscar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <?php if ($produtos): ?>

                <div class="col-md-12">
                    <div class="row row-cards">
                        <?php foreach ($produtos as $produto) : ?>
                            <?php
                                $produto['imagem'] = !empty($produto['imagem'])
                                                    ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $produto['imagem'])
                                                    : "https://placehold.co/1000";

                                $produto['preco'] = number_format($produto['preco'], 2, ',', '.');
                            ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card card-sm">
                                    <a href="<?= INCLUDE_PATH . "p/{$produto['link']}"; ?>" class="d-block">
                                        <img src="<?= $produto['imagem']; ?>" class="card-img-top" id="card-img-preview">
                                    </a>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h3 id="title-preview"><?= $produto['titulo']; ?></h3>
                                                <div id="price-preview" class="text-secondary"><?= "R$ {$produto['preco']}"; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Paginação -->
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between">
                        <form method="GET" class="d-flex">
                            <input type="number" name="pagina" class="form-control me-2" value="<?= $pagina_atual ?>" min="1" max="<?= $total_paginas ?>" placeholder="Página">
                            <select name="limite" class="form-select me-2" onchange="this.form.submit()">
                                <option value="10" <?= $limite == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= $limite == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= $limite == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $limite == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Ir</button>
                        </form>
                        <!-- Paginação no final -->
                        <?= criarPaginacao($pagina_atual, $total_paginas, $limite); ?>
                </div>

            <?php else: ?>

                <div class="col-12">
                    <div class="alert alert-info w-100" role="alert">
                        <div class="d-flex">
                            <div class="alert-icon">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Alerta do Sistema</h4>
                                <div class="text-secondary">Não encontramos nenhum produto cadastrado na plataforma.</div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>