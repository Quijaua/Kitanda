<?php
    if (!empty($link)) {
        // Consulta para buscar o produto selecionado
        $stmt = $conn->prepare("
            SELECT p.*, pi.imagem 
            FROM tb_produtos p
            LEFT JOIN tb_produto_imagens pi ON p.id = pi.produto_id
            WHERE p.link = ? 
            LIMIT 1
        ");
        $stmt->execute([$link]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($produto)) {
            $_SESSION['error_msg'] = 'Produto não encontrado.';
            header('Location: ' . INCLUDE_PATH_ADMIN . 'produtos');
            exit;
        }

        $stmt = $conn->prepare("SELECT imagem FROM tb_produto_imagens WHERE produto_id = ?");
        $stmt->execute([$produto['id']]);
        $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $produto['imagem'] = !empty($produto['imagem'])
                             ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $produto['imagem'])
                             : INCLUDE_PATH . "assets/preview-image/product.jpg";

        $produto['preco'] = number_format($produto['preco'], 2, ',', '.');

        // URL do produto
        $produto['url'] = urlencode(INCLUDE_PATH . "p/{$produto['link']}");
    } else {
        $_SESSION['error_msg'] = 'Insira o link do produto.';
        header('Location: ' . INCLUDE_PATH);
        exit;
    }
?>

<!-- Modal Sucesso -->
<div class="modal modal-blur fade" id="modal-success" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
                <!-- Ícone de sucesso -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-green icon-lg"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                <h3>Salvo com sucesso!</h3>
                <div class="text-secondary">O produto foi adicionado ao seu carrinho.</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-3 w-100" data-bs-dismiss="modal"> Continuar comprando </a>
                        </div>
                        <div class="col">
                            <a href="<?= INCLUDE_PATH; ?>carrinho" class="btn btn-success btn-4 w-100" > Ir para o carrinho </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Produtos</a></li>
                    <li class="breadcrumb-item active"><?= $produto['nome']; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <?php if (isset($usuario) && is_array($usuario) && isset($usuario['roles']) && $usuario['roles'] == 1 && $produto['vitrine'] == 0): ?>

                <div class="col-12">
                    <div class="alert alert-info w-100" role="alert">
                        <div class="d-flex">
                            <div class="alert-icon">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Atenção!</h4>
                                <div class="text-secondary">Este produto não está sendo listado. Ative a vitrine do produto para que ele seja exibido na página inicial.</div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

            <div class="col-md-6">
                <a id="mainImageLink" data-fslightbox="gallery" href="<?= $produto['imagem']; ?>">
                    <!-- Imagem Principal -->
                    <div id="mainImage" class="img-responsive img-responsive-1x1 rounded-3 border" 
                        style="background-image: url(<?= $produto['imagem']; ?>); cursor: pointer;">
                    </div>
                </a>

                <!-- Miniaturas -->
                <div class="col-md-12 d-flex flex-wrap gap-2 mt-3">
                    <?php foreach ($imagens as $imagem) : ?>
                        <?php $imagemUrl = str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $imagem['imagem']); ?>
                        <div class="rounded-3 border" 
                            style="background-image: url(<?= $imagemUrl; ?>); width: 80px; height: 80px; background-size: contain; background-repeat: no-repeat; background-position: center; cursor: pointer;" 
                            onclick="updateMainImage('<?= $imagemUrl; ?>')">
                        </div>

                        <!-- Adicionando a imagem ao fslightbox (exceto a primeira) -->
                        <a class="fslightbox" data-fslightbox="gallery" href="<?= $imagemUrl; ?>"></a>
                    <?php endforeach; ?>
                </div>

                <!-- Script para trocar a imagem -->
                <script>
                    function updateMainImage(imageUrl) {
                        document.getElementById('mainImage').style.backgroundImage = `url(${imageUrl})`;
                        document.getElementById('mainImageLink').href = imageUrl;

                        // Atualiza o fslightbox para reconhecer as novas imagens
                        refreshFsLightbox();
                    }
                </script>
            </div>

            <div class="col-md-6">
                <!-- Informações do Produto -->
                <h3 style="font-size: 2.5rem; line-height: normal;"><?= $produto['nome']; ?></h3>
                <h3 class="h1 mb-6"><?= "R$ {$produto['preco']}"; ?></h3>

                <hr>

                <!-- Botão de Compra -->
                <div class="row row-cards mb-6">
                    <div class="col-sm-4 col-md-2">
                        <input type="number" class="form-control quantidade-produto" value="1" min="1">
                    </div>
                    <div class="col-sm-8 col-md-10">
                        <button type="button" class="btn btn-primary btn-pill w-100 add-to-cart" data-produto-id="<?= $produto['id']; ?>">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/shopping-bag -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>
                            Comprar
                        </button>
                    </div>
                </div>

                <!-- Descrição -->
                <p class="h3 mb-2">Descrição</p>
                <div class="fs-2 text-secondary mb-7"><?= $produto['descricao']; ?></div>

                <!-- Compartilhar -->
                <div class="d-flex align-items-center">
                    <p class="text-secondary mb-0 me-4">Compartilhar</p>
                    <div class="d-flex align-items-center">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $produto['url']; ?>" class="text-dark" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 me-3 icon-tabler-brand-facebook">
                                <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"></path>
                            </svg>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://api.whatsapp.com/send?text=<?= $produto['titulo']; ?>%20<?= $produto['url']; ?>" class="text-dark" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 me-3 icon-tabler-brand-whatsapp">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                            </svg>
                        </a>

                        <!-- Email -->
                        <a href="mailto:?subject=<?= $produto['titulo']; ?>&body=Confira este produto: <?= $produto['url']; ?>" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2 me-3 icon-tabler-mail">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                <path d="M3 7l9 6l9 -6" />
                            </svg>
                        </a>
                    </div>
                </div>
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
            WHERE p.id != ? AND p.vitrine = 1 
            LIMIT 4
        ");
        $stmt->execute([$produto['id']]);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <style>
        .related-products::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: -1;
        }
        .related-products .card {
            box-shadow: 0 0 4px rgba(24, 36, 51, .04);
        }

        .card-img-top {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            display: block;
        }
    </style>

    <?php if ($produtos): ?>

    <div class="my-10 position-relative">
        <div class="related-products py-8">
            <div class="container-xl">
                <h2 class="page-title mb-5">
                    Outros Produtos
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
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>

<script>
    $(document).ready(function() {
        $(".add-to-cart").on("click", function(e) {
            e.preventDefault();

            var produtoId = $(this).data("produto-id");
            var quantidadeInput = $(this).closest(".row-cards").find(".quantidade-produto");
            var quantidade = parseInt(quantidadeInput.val(), 10);

            // Verifica se a quantidade é inválida
            if (isNaN(quantidade) || quantidade <= 0) {
                alert("Por favor, insira uma quantidade válida!");
                quantidadeInput.val(1);
                return;
            }

            $.ajax({
                url: "<?= INCLUDE_PATH; ?>back-end/carrinho/adicionar.php",
                method: "POST",
                data: {
                    produto_id: produtoId,
                    quantidade: quantidade
                },
                success: function(response) {
                    // Exibe o modal de sucesso
                    var myModal = new bootstrap.Modal(document.getElementById('modal-success'));
                    myModal.show();
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição AJAX: " + error);
                }
            });
        });
    });
</script>