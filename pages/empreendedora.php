<?php
    if (!empty($_GET['id'])) {
        // Consulta para buscar as lojas
        $stmt = $conn->prepare("SELECT * FROM tb_lojas WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $e = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($e)) {
            $_SESSION['error_msg'] = 'Empreendedora não encontrada.';
            header('Location: ' . INCLUDE_PATH . 'empreendedoras');
            exit;
        }   
        $e['imagem'] = !empty($e['imagem'])
                       ? str_replace(' ', '%20', INCLUDE_PATH . "files/lojas/{$e['id']}/perfil/{$e['imagem']}")
                       : INCLUDE_PATH . "assets/preview-image/profile.jpg";

        $e['address'] = 'Não informado';
        if ($e['cidade'] && $e['estado']) {
            $e['address'] = htmlspecialchars($e['cidade']) . '/' . htmlspecialchars($e['estado']);
        } else if ($e['cidade']) {
            $e['address'] = htmlspecialchars($e['cidade']);
        } else if ($e['estado']) {
            $e['address'] = htmlspecialchars($e['estado']);
        }
    } else {
        $_SESSION['error_msg'] = 'Insira o link da empreendedora.';
        header('Location: ' . INCLUDE_PATH . 'empreendedoras');
        exit;
    }
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>empreendedoras">Empreendedoras</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($e['nome']); ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <div class="col-md-4">
                <a id="mainImageLink" data-fslightbox="gallery" href="<?= $e['imagem']; ?>">
                    <!-- Imagem Principal -->
                    <div id="mainImage" class="img-responsive img-responsive-1x1 rounded-3 border" 
                        style="background-image: url(<?= $e['imagem']; ?>); cursor: pointer;">
                    </div>
                </a>
            </div>

            <div class="col-md-8">
                <!-- Informações da Empreendedora -->
                <h3 style="font-size: 2.5rem; line-height: normal;"><?= htmlspecialchars($e['nome']); ?></h3>

                <!-- Descrição -->
                <div class="markdown">
                    <p class="d-inline-flex align-items-center lh-1 mb-4">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/map-pin -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-map-pin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                        <?= $e['address']; ?>
                    </p>
                    <p><?= htmlspecialchars($e['mini_bio']); ?></p>
                </div>

                <div class="d-flex align-items-center mt-5">
                    <p class="text-secondary mb-0 me-4">Links</p>
                    <div class="d-flex align-items-center">
                        <?php if (!empty($e['facebook'])): ?>
                            <a href="<?= htmlspecialchars($e['facebook']); ?>" target="_blank" class="text-dark me-3">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/brand-facebook -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 icon-tabler-brand-facebook"><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"></path></svg>
                            </a>
                        <?php endif; ?>
    
                        <?php if (!empty($e['instagram'])): ?>
                            <a href="<?= htmlspecialchars($e['instagram']); ?>" target="_blank" class="text-dark me-3">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/brand-instagram -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 icon-tabler-brand-instagram"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M16.5 7.5v.01" /></svg>
                            </a>
                        <?php endif; ?>
    
                        <?php if (!empty($e['tiktok'])): ?>
                            <a href="<?= htmlspecialchars($e['tiktok']); ?>" target="_blank" class="text-dark me-3">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/brand-tiktok -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 icon-tabler-brand-tiktok"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 7.917v4.034a9.948 9.948 0 0 1 -5 -1.951v4.5a6.5 6.5 0 1 1 -8 -6.326v4.326a2.5 2.5 0 1 0 4 2v-11.5h4.083a6.005 6.005 0 0 0 4.917 4.917z" /></svg>
                            </a>
                        <?php endif; ?>
    
                        <?php if (!empty($e['site'])): ?>
                            <a href="<?= htmlspecialchars($e['site']); ?>" target="_blank" class="text-dark me-3">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/world -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 icon-tabler-world"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M3.6 9h16.8" /><path d="M3.6 15h16.8" /><path d="M11.5 3a17 17 0 0 0 0 18" /><path d="M12.5 3a17 17 0 0 1 0 18" /></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php
                $stmt = $conn->prepare("
                    SELECT p.*, pi.imagem 
                    FROM tb_produtos p
                    LEFT JOIN (
                        SELECT produto_id, MIN(imagem) AS imagem
                        FROM tb_produto_imagens 
                        GROUP BY produto_id
                    ) pi ON p.id = pi.produto_id
                    WHERE p.criado_por = ?
                    ORDER BY vitrine DESC, nome ASC
                    LIMIT 6
                ");
                $stmt->execute([$e['vendedora_id']]);
                $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if ($produtos): ?>

            <hr class="my-5">

            <h2 class="page-title mb-5">
                Destaques
            </h2>

            <div class="row">
                <div class="col-md-12">
                    <div class="row row-cards">
                        <?php foreach ($produtos as $produto) : ?>
                            <?php
                                $produto['imagem'] = !empty($produto['imagem'])
                                                    ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $produto['imagem'])
                                                    : INCLUDE_PATH . "assets/preview-image/product.jpg";

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
            </div>

            <?php endif; ?>

        </div>
    </div>
</div>