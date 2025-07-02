<?php
    $read = verificaPermissao($_SESSION['user_id'], 'aparencia', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'aparencia', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';
?>

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

<?php if (!$update): ?>
<fieldset disabled>
<?php endif; ?>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

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

            <div class="col-lg-12">
                <div class="card">

                    <form action="<?= INCLUDE_PATH_ADMIN ?>back-end/update.php" method="post">
                        <div class="card-header">
                            <h4 class="card-title">Tema</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6 col-sm-4">
                                    <label class="form-imagecheck mb-2">
                                        <input 
                                            name="theme" 
                                            type="radio" 
                                            value="Ankara" 
                                            class="form-imagecheck-input"
                                            <?php if ($current_theme == 'Ankara') { echo "checked"; } ?>>
                                        <span class="form-imagecheck-figure">
                                            <img src="<?= INCLUDE_PATH ?>assets/Ankara/thumbs/ankara.webp"
                                                alt="Tema Ankara" 
                                                class="form-imagecheck-image">
                                        </span>
                                        <div class="fs-2 mt-2 text-center">Ankara</div>
                                    </label>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="form-imagecheck mb-2">
                                        <input 
                                            name="theme" 
                                            type="radio" 
                                            value="TerraDourada" 
                                            class="form-imagecheck-input"
                                            <?php if ($current_theme == 'TerraDourada') { echo "checked"; } ?>>
                                        <span class="form-imagecheck-figure">
                                            <img src="<?= INCLUDE_PATH ?>assets/TerraDourada/thumbs/terradourada.webp"
                                                alt="Tema TerraDourada" 
                                                class="form-imagecheck-image">
                                        </span>
                                        <div class="fs-2 mt-2 text-center">Terra Dourada</div>
                                    </label>
                                </div>

                                <div class="col-6 col-sm-4">
                                    <label class="form-imagecheck mb-2">
                                        <input 
                                            name="theme" 
                                            type="radio" 
                                            value="Oralituras" 
                                            class="form-imagecheck-input"
                                            <?php if ($current_theme == 'Oralituras') { echo "checked"; } ?>>
                                        <span class="form-imagecheck-figure">
                                            <img src="<?= INCLUDE_PATH ?>assets/Oralituras/thumbs/oralituras.webp" 
                                                alt="Tema TerraDourada" 
                                                class="form-imagecheck-image">
                                        </span>
                                        <div class="fs-2 mt-2 text-center">Oralituras</div>
                                    </label>
                                </div>

                                <!-- Configurações de conteúdo por tema -->
                                <div class="mt-4">   

                                    <!-- Ankara -->
                                    <div class="theme-options" data-theme="Ankara" style="display: <?= ($current_theme == 'Ankara') ? 'block' : 'none' ?>;">
                                        <h4 class="card-title">Configurações de Conteúdo da Home Ankara</h4>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-hero" name="ankara_hero" <?= ($ankara_hero) ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="toggle-hero">
                                                Exibir seção <strong>Hero</strong> (banner principal) somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-colorful" name="ankara_colorful" <?= ($ankara_colorful) ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="toggle-colorful">
                                                Exibir bloco <strong>“Colorful”</strong> somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-yellow" name="ankara_yellow" <?= ($ankara_yellow) ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="toggle-yellow">
                                                Exibir seção <strong>Amarela</strong> somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-footer-top" name="ankara_footer_top" <?= ($ankara_footer_top) ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="toggle-footer-top">
                                                Exibir faixa superior do footer somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="toggle-footer-blog" name="ankara_footer_blog" <?= ($ankara_footer_blog) ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="toggle-footer-blog">
                                                Exibir seção de posts do blog no footer somente na Home
                                            </label>
                                        </div>
                                    </div>


                                    <!-- Terra Dourada -->
                                    <div class="theme-options" data-theme="TerraDourada" style="display: <?= ($current_theme == 'TerraDourada') ? 'block' : 'none' ?>;">
                                        <h4 class="card-title">Configurações de Conteúdo da Home Terra Dourada</h4>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-hero-td" name="td_hero" <?= $td_hero ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="toggle-hero-td">
                                                Mostrar <strong>Hero</strong> (banner principal) somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-entrepreneurs" name="td_entrepreneurs" <?= $td_entrepreneurs ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="toggle-entrepreneurs">
                                                Mostrar seção <strong>Empreendedoras</strong> somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-news" name="td_news" <?= $td_news ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="toggle-news">
                                                Mostrar bloco de <strong>Últimas notícias</strong> somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-footer-info" name="td_footer_info" <?= $td_footer_info ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="toggle-footer-info">
                                                Mostrar informações de contato no footer somente na Home
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="toggle-footer-socials" name="td_footer_socials" <?= $td_footer_socials ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="toggle-footer-socials">
                                                Mostrar ícones de redes sociais no footer somente na Home
                                            </label>
                                        </div>
                                    </div>

                                </div>






                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" name="btnUpdTheme" class="btn btn-primary ms-auto">Salvar</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="theme"]');
    const optionGroups = document.querySelectorAll('.theme-options');

    function updateVisibility() {
      const selected = document.querySelector('input[name="theme"]:checked');
      const theme = selected ? selected.value : null;
      optionGroups.forEach(group => {
        group.style.display = (group.dataset.theme === theme)
          ? 'block'
          : 'none';
      });
    }

    // Inicializa
    updateVisibility();

    // Reage a mudanças
    radios.forEach(radio => {
      radio.addEventListener('change', updateVisibility);
    });
  });
</script>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>
