<?php
    $read = verificaPermissao($_SESSION['user_id'], 'politica-de-privacidade', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'politica-de-privacidade', 'update', $conn);
    $editorDisabled = !$update ? 'true' : 'false';
    $disabledUpdate = !$update ? 'disabled' : '';
?>

<style>
.form-color {
    outline: none;
    background: none;
    width: 38px;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    margin-right: 10px;
}
  #colorPickerRGB {
    outline: none;
    background: none;
    width: 38px;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    margin-right: 10px;
  }

  #rgbInputs {
    display: flex;
    align-items: center;
  }

  .rgbInput {
    width: 70px;
    margin-right: 10px;
    text-align: center;
  }

  #colorPreview {
    width: 50px;
    height: 50px;
    margin-top: 10px;
    border: 1px solid #ccc;
  }

  textarea {
      height: 200px !important;
  }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Política de Privacidade
                </h2>
                <div class="text-secondary mt-1">Área para personalizar a política de privacidade/termos do sistema.</div>
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

                    <form id="messages_form" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="privacy_policy" class="form-label">Texto que vai ser exibido na página de privacidade</label>
                                <textarea id="privacy_policy" name="privacy_policy" placeholder="Insira a mensagem de boas vindas que será enviada no email aqui" style="min-height: 300px"><?php echo $privacy_policy; ?></textarea>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                    let options = {
                                        selector: '#privacy_policy',
                                        height: 300,
                                        menubar: false,
                                        disabled: <?php echo $editorDisabled; ?>,
                                        statusbar: false,
                                            license_key: 'gpl',
                                        plugins: [
                                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
                                            'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                                        ],
                                        toolbar: 'undo redo | formatselect | ' +
                                            'bold italic backcolor | alignleft aligncenter ' +
                                            'alignright alignjustify | bullist numlist outdent indent | ' +
                                            'removeformat',
                                        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }'
                                    }
                                    if (localStorage.getItem("tablerTheme") === 'dark') {
                                        options.skin = 'oxide-dark';
                                        options.content_css = 'dark';
                                    }
                                    tinyMCE.init(options);
                                })
                                </script>
                            </div>

                            <label class="form-check form-switch form-switch-2 mb-0">
                                <input name="use_privacy" id="use_privacy" type="checkbox" class="form-check-input" value="1" data-input-id="website" <?php if ($use_privacy) { echo 'checked'; } ?>>
                                <span class="form-check-label">Usar a Política de Privacidade padrão do sistema</span>
                            </label>
                        </div>

                        <div class="card-footer d-flex justify-content-end gap-2">
                            <a href="/politica-de-privacidade/" type="button" class="btn btn-secondary" id="btnPreview" target="_blank">Visualizar</a>
                            <button type="submit" name="btnPrivacy" class="btn btn-primary" form="messages_form">Salvar</button>
                        </div>

                        <!-- <div class="card-footer text-end">
                            <button type="submit" name="btnPrivacy" class="btn btn-primary" form="messages_form">Salvar</button>
                        </div> -->
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>