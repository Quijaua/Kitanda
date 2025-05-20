<?php if (isset($_GET['id'])): ?>

<?php
    $stmt = $conn->prepare("
        SELECT *
        FROM tb_blog_posts
        WHERE id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($post)) {
        header('Location: ' . INCLUDE_PATH . 'blog');
        exit;
    }

    $post['imagem'] = !empty($post['imagem'])
                    ? str_replace(' ', '%20', INCLUDE_PATH . "files/blog/" . $post['id'] . "/" . $post['imagem'])
                    : null;
?>

<?php
    function formatarTags(?string $jsonTags, int $limit = 5000, string $sep = ' '): string
    {
        // Garante que, se vier null ou JSON inválido, teremos um array vazio
        $dados   = json_decode($jsonTags ?? '[]', true);
        $valores = array_column(is_array($dados) ? $dados : [], 'value');
        $todas   = implode($sep, $valores);

        return mb_strlen($todas) > $limit
            ? mb_substr($todas, 0, $limit) . '...'
            : $todas;
    }
?>

<?php
    $stmtCategoria = $conn->prepare("
        SELECT c.* 
        FROM tb_blog_categoria_posts cp
        JOIN tb_blog_categorias c ON cp.categoria_id = c.id
        WHERE cp.post_id = ?
    ");
    $stmtCategoria->execute([$post['id']]);
    $categorias = $stmtCategoria->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>blog">Blog</a></li>
                    <li class="breadcrumb-item active"><?= $post['titulo']; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-12">
                <h3 style="font-size: 2.5rem; line-height: normal;"><?= $post['titulo']; ?></h3>
                <?php if ($categorias): ?>
                    <div class="mb-3">
                    <?php foreach ($categorias as $categoria): ?>
                        <a href="<?= INCLUDE_PATH . "categoria?id={$categoria['id']}"; ?>" class="btn btn-outline-dark btn-pill me-2"><?= $categoria['nome']; ?></a>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($post['imagem'])): ?>
                <img src="<?= $post['imagem']; ?>" class="w-100 mb-3">
                <?php endif; ?>

                <div class="mt-3 mb-4">
                    <?= $post['resumo']; ?>
                </div>

                <p class="fs-3"><b>Publicado em:</b> <?php echo date("d/m/Y", strtotime($post["data_publicacao"])); ?></p>

                <hr>

                <p class="fs-2"><b>Tags:</b> <?= formatarTags($post['tags']); ?></p>
            </div>
        </div>
    </div>
</div>




<?php else: ?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>blog">Blog</a></li>
                    <li class="breadcrumb-item active">Post não encontrado</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-12">
                <div class="alert alert-info w-100" role="alert">
                    <div class="d-flex">
                        <div class="alert-icon">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
                        </div>
                        <div>
                            <h4 class="alert-title">Alerta do Sistema</h4>
                            <div class="text-secondary">Não encontramos este post.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
