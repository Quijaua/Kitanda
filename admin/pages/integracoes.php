<?php
    $read = verificaPermissao($_SESSION['user_id'], 'integracoes', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'integracoes', 'update', $conn);
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
                    Integrações
                </h2>
                <div class="text-secondary mt-1">Área para inserir os códigos de restreamento/analytics das rede sociais.</div>
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
            <div class="col-lg-12">
                <div class="alert alert-info">Você pode visualizar os detalhes desta página, mas não pode editá-los.</div>
            </div>
            <?php endif; ?>

            <div class="col-12">
                <div class="card">

                    <form id="integration_form" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="fb_pixel" class="form-label">Facebook Pixel</label>
                                <textarea class="form-control" placeholder="Insira o código completo do Facebook Pixel aqui" id="fb_pixel" name="fb_pixel"><?php echo $fb_pixel ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="gtm" class="form-label">Google Tag Manager</label>
                                <textarea class="form-control" placeholder="Insira o código completo do Google Tag Manager aqui" id="gtm" name="gtm"><?php echo $gtm; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="g_analytics" class="form-label">Google Analytics</label>
                                <textarea class="form-control" placeholder="Insira o código completo do Google Analytics aqui" id="g_analytics" name="g_analytics"><?php echo $g_analytics; ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" name="btnIntegration" class="btn btn-primary" form="integration_form">Salvar</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>