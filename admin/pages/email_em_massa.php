<?php
    $create = verificaPermissao($_SESSION['user_id'], 'novidades', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Novo email em massa
                </h1>
                <div class="text-secondary mt-1">Área para disparar emails em massa.</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <?php if (!$create): ?>
            <div class="col-12">
                <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
            </div>
            <?php exit; endif; ?>

            <div class="col-lg-12">
                <div class="card">

                    <form id="bulk_email_form" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/bulk_email_send.php" method="post">
                        <div class="card-header">
                            <h2 class="card-title">Disparar email em massa</h2>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label for="bulk_email_title" class="col-3 col-form-label required">Título do email</label>
                                <div class="col">
                                    <input name="bulk_email_title" id="bulk_email_title"
                                        type="text" class="form-control" placeholder="Insira o título do email aqui" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="bulk_email_body" class="col-3 col-form-label required">Corpo do email</label>
                                <div class="col">
                                    <textarea name="bulk_email_body" id="bulk_email_body" placeholder="Insira o corpo do email aqui"></textarea>
                                </div>
                                <script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/hugerte.min.js"></script>
                                <script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/langs/pt_BR.js"></script>
                                <script>
                                    hugerte.init({
                                        selector: '#bulk_email_body',
                                        language: 'pt_BR',
                                        plugins: 'accordion advlist anchor autolink autosave charmap code codesample directionality emoticons fullscreen help image insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table template visualblocks visualchars wordcount',
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <a href="<?php echo INCLUDE_PATH_ADMIN; ?>novidades" class="btn btn-1">Voltar</a>
                                <button type="submit" class="btn btn-primary ms-auto" form="bulk_email_form">Disparar</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>