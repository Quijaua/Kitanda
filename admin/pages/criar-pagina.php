<?php
    $create = verificaPermissao($_SESSION['user_id'], 'pagina', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';
?>
<style>
    .dz-preview {
        position: relative;
        display: inline-block;
    }
    .dz-remove-custom {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        color: #6c757d;
        padding: 4px 8px;
        font-size: 14px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease-in-out;
        cursor: pointer !important;
        z-index: 999999;
    }
    .dz-remove-custom:hover {
        background-color: #e9ecef;
    }
    .dz-progress {
        display: none;
    }

    .preview-product {
        position: absolute;
        left: 20px;
        top: 20px;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Criar Nova Página
                </h2>
                <div class="text-secondary mt-1">Aqui você pode criar novas páginas.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <ol class="breadcrumb breadcrumb-muted" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>paginas">Páginas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Criar Página</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <form id="createPagina" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/create-pagina.php" method="post" enctype="multipart/form-data">
            <div class="row">

                <?php if (!$create): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                </div>
                <?php exit; endif; ?>

                <!-- Mensagem de erro -->
                <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger w-100" role="alert">
                        <div class="d-flex">
                            <div>
                                <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Erro!</h4>
                                <div class="text-secondary"><?php echo $_SESSION['error_msg']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; unset($_SESSION['error_msg']); ?>

                <div class="col-lg-12 row row-deck row-cards mt-0">

                    <div class="col-lg-12 mt-0">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informações principais</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <label for="titulo" class="col-3 col-form-label required">Título da Página</label>
                                    <div class="col">
                                        <input name="titulo" id="titulo"
                                            type="text" class="form-control" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="slug" class="col-3 col-form-label required">SLUG (Link da página)</label>
                                    <div class="col">
                                        <div class="input-group input-group-flat">
                                            <span class="input-group-text"> <?= INCLUDE_PATH . 'pagina/'; ?> </span>
                                            <input name="slug" id="slug"
                                                type="text" class="form-control ps-0" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Imagem</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="imagem" class="dropzone w-100" id="dropzone-custom">
                                            <div class="fallback">
                                                <input name="imagem" id="imagem" type="file" class="d-none" accept="image/png,image/jpeg,image/webp" />
                                            </div>
                                            <div class="dz-message">
                                                <h3 class="dropzone-msg-title">
                                                    <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-library-photo"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 3m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 7.26a2.005 2.005 0 0 0 -1.012 1.737v10c0 1.1 .9 2 2 2h10c.75 0 1.158 -.385 1.5 -1" /><path d="M17 7h.01" /><path d="M7 13l3.644 -3.644a1.21 1.21 0 0 1 1.712 0l3.644 3.644" /><path d="M15 12l1.644 -1.644a1.21 1.21 0 0 1 1.712 0l2.644 2.644" /></svg>
                                                    Arraste e solte a imagem aqui
                                                </h3>
                                                <span class="dropzone-msg-desc">Essa imagem será mostrada no conteúdo da página.</span>
                                            </div>
                                        </label>
                                        <small class="form-hint text-end">Imagem em <b>.png, .jpg, .jpeg, .webp</b> até <b>2MB</b>. Sugerimos dimensões de <b>1200px X 630px</b>.</small>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Conteúdo</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <div class="col">
                                        <textarea name="conteudo" id="conteudo"></textarea>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="d-flex">
                        <button type="button" class="btn btn-1" onclick="location.reload();">Resetar</button>
                        <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary ms-auto">Salvar</button>
                    </div>

                </div>

            </div>
        </form>
    </div>
</div>

<!-- jQuery Validation, Input Mask, and Validation Script -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script>
    $(document).ready(function () {
        $("#createPagina").validate({
            rules: {
                titulo: {
                    required: true,
                    minlength: 3
                },
            },
            messages: {
                titulo: {
                    required: "Por favor, insira o título da página.",
                    minlength: "O título deve ter pelo menos 3 caracteres."
                },
            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.next("label"));
                } else if (element.prop("type") === "select-one") {
                    error.insertAfter(element.next("span.select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).addClass("is-valid").removeClass("is-invalid");
            },
            submitHandler: function(form) {
                // Impede o envio padrão do formulário
                event.preventDefault(); 

                // Define os botões como variáveis
                var btnSubmit = $("#btnSubmit");
                var btnLoader = $("#btnLoader");

                // Desabilitar botão submit e habilitar loader
                btnSubmit.prop("disabled", true).addClass("d-none");
                btnLoader.removeClass("d-none");

                // Cria um objeto FormData a partir do formulário
                var formData = new FormData(form);

                // Adiciona um novo campo
                formData.append("action", "criar-pagina");

                // Realiza o AJAX para enviar os dados
                $.ajax({
                    url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/create-pagina.php', // Substitua pelo URL do seu endpoint
                    type: 'POST',
                    data: formData,
                    processData: false, // Impede que o jQuery processe os dados
                    contentType: false, // Impede que o jQuery defina o Content-Type
                    success: function (response) {
                        if (response.status == "success") {
                            // Sucesso na resposta do servidor
                            window.location.href = "<?= INCLUDE_PATH_ADMIN; ?>paginas";
                        } else {
                            // console.error("Erro no AJAX:", status, error);

                            // Caso contrário, exibe a mensagem de erro
                            $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                            $("#createPagina").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');
                        }
                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro no AJAX:", status, error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#createPagina").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');

                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    }
                });
            }
        });
    });
</script>

<!-- Imagem Preview -->
<script>
    $(document).ready(function() {
        $('#imagem').on('change', function(e) {
            var fileInput = this;
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(evt) {
                    // 1) Esconde tudo dentro do label (#dropzone-custom)
                    var $label = $('#dropzone-custom');
                    $label.find('.dz-message, .fallback').hide();
                    // Remove previews anteriores (caso haja)
                    $label.find('img.previsualizacao').remove();

                    // 2) Cria a tag <img> com a imagem carregada
                    var $img = $('<img class="previsualizacao" alt="" aria-hidden="true" />')
                                .attr('src', evt.target.result)
                                .css({
                                    'display': 'block',
                                    'max-width': '100%',
                                    'height': 'auto',
                                    'margin': '0 auto'
                                });
                    // 3) Insere a <img> dentro do label
                    $label.append($img);
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        });
    });
</script>

<!-- Slug (Link) da Página -->
<script>
    $(document).ready(function () {
        function formatarLink(texto) {
            return texto.normalize('NFD') // Remove acentos
                        .replace(/[\u0300-\u036f]/g, '') // Remove diacríticos
                        .replace(/~/g, '') // Substitui "~"
                        .replace(/\s+/g, '-') // Substitui espaços por "-"
                        .toLowerCase(); // Converte para minúsculas
        }

        $('#titulo').on('input', function () {
            let linkFormatado = formatarLink($(this).val());
            $('#slug').val(linkFormatado); // Insere no campo #slug
        });
    });
</script>

<script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/hugerte.min.js"></script>
<script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/langs/pt_BR.js"></script>
<script>
    hugerte.init({
        selector: '#conteudo',
        language: 'pt_BR',
        plugins: 'accordion advlist anchor autolink autosave charmap code codesample directionality emoticons fullscreen help image insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table template visualblocks visualchars wordcount',
    });
</script>
