<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Novo email em massa
                </h2>
                <div class="text-secondary mt-1">Área para disparar emails em massa.</div>
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

                    <form id="bulk_email_form" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/bulk_email_send.php" method="post">
                        <div class="card-header">
                            <h4 class="card-title">Disparar email em massa</h4>
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
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        let options = {
                                            selector: '#bulk_email_body',
                                            height: 300,
                                            menubar: false,
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