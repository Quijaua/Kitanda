<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Cabeçalho
                </h2>
                <div class="text-secondary mt-1">Altere configurações da parte superior da página.</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-lg-12">
                <div class="card">

                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                        <div class="card-header">
                            <h4 class="card-title">Título da Página</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label for="title" class="col-3 col-form-label required">Texto do Título da Página</label>
                                <div class="col">
                                    <input name="title" id="title"
                                        type="text" class="form-control" value="<?php echo $title; ?>">
                                    <small class="form-hint">Será mostrado na aba do seu navegador e na página do Google.</small>
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

                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post" enctype="multipart/form-data">
                        <div class="card-header">
                            <h4 class="card-title">Logo</h4>
                        </div>
                        <div class="card-body">
                            <div class="row my-3">
                                <label for="pix_qr_code" class="col-3 col-form-label pt-0">QR Code</label>
                                <div class="col">
                                    <input type="file" name="logo" id="logo" 
                                        accept=".jpg, .jpeg, .png" value="<?php echo $logo; ?>" style="display: none;">
                                    <div class="row align-items-center mt-0">
                                        <div class="col-3 row g-2 g-md-3 mt-0">
                                            <div class="col-12 mt-0">
                                                <a data-fslightbox="gallery" href="<?php echo INCLUDE_PATH . 'assets/img/' . $logo; ?>" id="preview-link">
                                                    <!-- Photo -->
                                                    <div id="preview-container" class="img-responsive img-responsive-1x1 rounded-3 border" 
                                                        style="background-image: url(<?php echo INCLUDE_PATH . 'assets/img/' . $logo; ?>); background-size: cover; background-position: center;">
                                                    </div>
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

            <div class="col-lg-12">
                <div class="card">

                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post" enctype="multipart/form-data">
                        <div class="card-header">
                            <h4 class="card-title">Cores</h4>
                        </div>
                        <div class="card-body">

                            <div class="mb-3 row">
                                <label for="nav_color" class="col-3 col-form-label required">Cor dos textos</label>
                                <div class="col-2">
                                    <div>
                                        <input name="nav_color" id="nav_color"
                                            type="text" class="form-control d-block" value="<?php echo $nav_color; ?>">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                window.Coloris && (Coloris({
                                                    el: "#nav_color",
                                                    selectInput: false,
                                                    alpha: false,
                                                    format: "hex",
                                                    swatches: [
                                                        "#066fd1",
                                                        "#45aaf2",
                                                        "#6574cd",
                                                        "#a55eea",
                                                        "#f66d9b",
                                                        "#fa4654",
                                                        "#fd9644",
                                                        "#f1c40f",
                                                        "#7bd235",
                                                        "#5eba00",
                                                        "#2bcbba",
                                                        "#17a2b8",
                                                    ],
                                                }))
                                            })
                                        </script>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="nav_background" class="col-3 col-form-label required">Cor de fundo</label>
                                <div class="col-2">
                                    <div>
                                        <input name="nav_background" id="nav_background"
                                            type="text" class="form-control d-block" value="<?php echo $nav_background; ?>">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                window.Coloris && (Coloris({
                                                    el: "#nav_background",
                                                    selectInput: false,
                                                    alpha: false,
                                                    format: "hex",
                                                    swatches: [
                                                        "#066fd1",
                                                        "#45aaf2",
                                                        "#6574cd",
                                                        "#a55eea",
                                                        "#f66d9b",
                                                        "#fa4654",
                                                        "#fd9644",
                                                        "#f1c40f",
                                                        "#7bd235",
                                                        "#5eba00",
                                                        "#2bcbba",
                                                        "#17a2b8",
                                                    ],
                                                }))
                                            })
                                        </script>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" name="btnUpdNavColor" class="btn btn-primary ms-auto">Salvar</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>