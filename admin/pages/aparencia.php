<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Aparência
                </h2>
                <div class="text-secondary mt-1">Área para alterar cores na página principal.</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-12">
                <div class="card">

                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post" enctype="multipart/form-data">
                        <div class="card-header">
                            <h4 class="card-title">Cores</h4>
                        </div>
                        <div class="card-body">

                            <div class="mb-3 row">
                                <label for="background" class="col-3 col-form-label required">Cor de fundo</label>
                                <div class="col-2">
                                    <div>
                                        <input name="background" id="background"
                                            type="text" class="form-control d-block" value="<?php echo $background; ?>">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                window.Coloris && (Coloris({
                                                    el: "#background",
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
                                <label for="text_color" class="col-3 col-form-label required">Cor dos textos</label>
                                <div class="col-2">
                                    <div>
                                        <input name="text_color" id="text_color"
                                            type="text" class="form-control d-block" value="<?php echo $text_color; ?>">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                window.Coloris && (Coloris({
                                                    el: "#text_color",
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
                                <label for="color" class="col-3 col-form-label required">Cor dos Botões</label>
                                <div class="col-2">
                                    <div>
                                        <input name="color" id="color"
                                            type="text" class="form-control d-block" value="<?php echo $color; ?>">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                window.Coloris && (Coloris({
                                                    el: "#color",
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
                                <label for="hover" class="col-3 col-form-label required">Hover</label>
                                <div class="col-2">
                                    <div>
                                        <input name="hover" id="hover"
                                            type="text" class="form-control d-block" value="<?php echo $hover; ?>">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                window.Coloris && (Coloris({
                                                    el: "#hover",
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
                                <label for="load_btn" class="col-3 col-form-label required">Cor do botão carregar</label>
                                <div class="col-2">
                                    <div>
                                        <input name="load_btn" id="load_btn"
                                            type="text" class="form-control d-block" value="<?php echo $load_btn; ?>">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                window.Coloris && (Coloris({
                                                    el: "#load_btn",
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
                                <button type="submit" name="btnUpdColor" class="btn btn-primary ms-auto">Salvar</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>