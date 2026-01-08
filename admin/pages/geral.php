<?php
    $read = verificaPermissao($_SESSION['user_id'], 'cabecalho', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'cabecalho', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Geral
                </h2>
                <div class="text-secondary mt-1">Altere configurações do site.</div>
            </div>
        </div>
    </div>
</div>

<?php if (!$update): ?>
<fieldset disabled>
<?php endif; ?>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <?php include_once('./template-parts/general-sidebar.php'); ?>

            <div class="col">
                <div class="row row-cards">

                    <?php if (!getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
                    <div class="col-lg-12">
                        <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                    </div>
                    <?php exit; endif; ?>

                    <?php if (!$read): ?>
                    <div class="col-12">
                        <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                    </div>
                    <?php exit; endif; ?>

                    <?php if (!$update): ?>
                    <div class="col-12">
                        <div class="alert alert-info">Você pode visualizar os detalhes desta página, mas não pode editá-los.</div>
                    </div>
                    <?php endif; ?>

                    <div class="col-lg-12">
                        <div class="card">

                            <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                                <div class="card-header">
                                    <h4 class="card-title">Configurações Gerais do Site</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 row">
                                        <label for="title" class="col-3 col-form-label required">Título da Página</label>
                                        <div class="col">
                                            <input name="title" id="title"
                                                type="text" class="form-control" value="<?php echo $title; ?>">
                                            <small class="form-hint">Será mostrado na aba do seu navegador e na página do Google.</small>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="descricao" class="col-3 col-form-label">Descrição do Site</label>
                                        <div class="col">
                                            <textarea name="descricao" id="descricao"
                                                class="form-control" rows="4"><?php echo $descricao; ?></textarea>
                                            <small class="form-hint">Descrição para SEO e exibição no rodapé.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                        <button type="submit" name="btnUpdAbout" class="btn btn-primary ms-auto">Salvar</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">

                            <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                                <div class="card-header">
                                    <h4 class="card-title">Vitrine do Site</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 row">
                                        <label for="vitrine_limite" class="col-3 col-form-label required">Limite de Itens</label>
                                        <div class="col row">
                                            <div class="col-lg-4 col-sm-6">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    name="vitrine_limite"
                                                    id="vitrine_limite"
                                                    class="form-control"
                                                    value="<?php echo $vitrine_limite; ?>"
                                                    required
                                                >
                                            </div>
                                            <small class="form-hint">Número máximo de produtos que serão exibidos na vitrine do site.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                        <button type="submit" name="btnUpdVitrine" class="btn btn-primary ms-auto">Salvar</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">

                            <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post" enctype="multipart/form-data">
                                <div class="card-header">
                                    <h4 class="card-title">Logo</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row my-3">
                                        <label for="pix_qr_code" class="col-3 col-form-label pt-0">Imagem</label>
                                        <div class="col">
                                            <input type="file" name="logo" id="logo" 
                                                accept=".jpg, .jpeg, .png" value="<?php echo $logo; ?>" style="display: none;">
                                            <div class="row align-items-center mt-0">
                                                <div class="col-3 row g-2 g-md-3 mt-0">
                                                    <div class="col-12 mt-0">
                                                        <a data-fslightbox="gallery" href="<?php echo INCLUDE_PATH . 'assets/img/' . $logo; ?>" id="preview-link" aria-label="Abrir imagem ampliada da logo">
                                                            <!-- Photo -->
                                                            <img
                                                                src="<?php echo INCLUDE_PATH . 'assets/img/' . $logo; ?>"
                                                                alt="Kitanda"
                                                                class="img-responsive img-responsive-1x1 rounded-3 border pt-0"
                                                            >
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-auto row align-items-center">
                                                    <div class="col-auto">
                                                        <label for="logo" class="btn btn-1">Alterar Imagem</label>
                                                    </div>
                                                    <small class="form-hint mt-2">Essa será a logo mostrada no cabeçalho do checkout. Tamanho ideal é <b>148 X 148px</b>.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                        <button type="submit" name="btnUpdLogo" class="btn btn-primary ms-auto">Salvar</button>
                                    </div>
                                </div>
                            </form>

                            <script>
                                document.getElementById("logo").addEventListener("change", function(event) {
                                    var input = event.target;
                                    if (input.files && input.files[0]) {
                                        var reader = new FileReader();

                                        reader.onload = function(e) {
                                            // Atualiza a prévia da imagem
                                            document.getElementById("preview-container").style.backgroundImage = "url(" + e.target.result + ")";
                                            // Atualiza o href do fslightbox
                                            document.getElementById("preview-link").href = e.target.result;

                                            // 🛠️ IMPORTANTE: Força a atualização do fslightbox
                                            setTimeout(() => {
                                                refreshFsLightbox();
                                            }, 100);
                                        };

                                        reader.readAsDataURL(input.files[0]); // Converte a imagem para URL
                                    }
                                });
                            </script>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>