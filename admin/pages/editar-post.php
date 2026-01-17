<?php
    $read = verificaPermissao($_SESSION['user_id'], 'posts', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $only_own = verificaPermissao($_SESSION['user_id'], 'posts', 'only_own', $conn);
    $disabledOnlyOwn = !$only_own ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'posts', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';
?>

<?php
    if (isset($_GET['id'])) {
        // ID do post
        $post_id = $_GET['id'];

        // Consulta para buscar o post selecionado
        $stmt = $conn->prepare("
            SELECT * 
            FROM tb_blog_posts
            WHERE id = ? 
            LIMIT 1
        ");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($post)) {
            $_SESSION['error_msg'] = 'Post não encontrada.';
            header('Location: ' . INCLUDE_PATH_ADMIN . 'posts');
            exit;
        }

        // Consulta para buscar as categorias cadastradas
        $stmt = $conn->prepare("SELECT * FROM tb_blog_categorias");
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para buscar o post selecionado
        $stmt = $conn->prepare("
            SELECT categoria_id  
            FROM tb_blog_categoria_posts
            WHERE post_id = ?
        ");
        $stmt->execute([$post['id']]);
        $post['categorias'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $_SESSION['error_msg'] = 'Insira o ID da post.';
        header('Location: ' . INCLUDE_PATH_ADMIN . 'posts');
        exit;
    }
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Editar Post
                </h1>
                <div class="text-secondary mt-1">Aqui você pode editar uma post.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <nav aria-label="Caminho de navegação">
                        <ol class="breadcrumb breadcrumb-muted">
                            <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>posts">Posts</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar Post</li>
                        </ol>
                    </nav>
                </div>
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
        <form id="updatePost" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update-post.php" method="post" enctype="multipart/form-data">
            <div class="row">

                <?php if ($only_own && $post['criado_por'] !== $_SESSION['user_id']): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                </div>
                <?php exit; endif; ?>

                <?php if (!$only_own && !$read): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                </div>
                <?php exit; endif; ?>

                <?php if (!$update): ?>
                <div class="col-lg-12">
                    <div class="alert alert-info">Você pode visualizar os detalhes da post, mas não pode editá-la.</div>
                </div>
                <?php endif; ?>

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
                                <h4 class="alert-title">Erro!</h2>
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
                                <h2 class="card-title">Informações principais</h2>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <label for="titulo" class="col-3 col-form-label required">Título do Post</label>
                                    <div class="col">
                                        <input name="titulo" id="titulo"
                                            type="text" class="form-control" value="<?= $post['titulo']; ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="tags" class="col-3 col-form-label">Tags</label>
                                    <div class="col">
                                        <input name="tags" id="tags"
                                            class="form-control" placeholder="Digite e pressione Enter..." value="<?= htmlspecialchars($post['tags'], ENT_QUOTES, "UTF-8") ?>">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="data_publicacao" class="col-3 col-form-label">Data da Publicação</label>
                                    <div class="col row">
                                        <div class="col-lg-4 col-sm-6">
                                            <input name="data_publicacao" id="data_publicacao"
                                                type="date" class="form-control" value="<?= $post['data_publicacao']; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="categorias" class="col-3 col-form-label">Categorias</label>
                                    <div class="col">
                                        <select name="categorias[]" id="categorias" type="text" class="form-select" placeholder="Selecione uma ou mais categoria" multiple>
                                            <?php if ($categorias): ?>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?= $categoria['id']; ?>" <?= in_array($categoria['id'], $post['categorias']) ? 'selected' : ''; ?>><?= $categoria['nome']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                var el;
                                                window.TomSelect && (new TomSelect(el = document.getElementById('categorias'), {
                                                    copyClassesToDropdown: false,
                                                    dropdownParent: 'body',
                                                    controlInput: '<input>',
                                                    render:{
                                                        item: function(data,escape) {
                                                            if( data.customProperties ){
                                                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                                                            }
                                                            return '<div>' + escape(data.text) + '</div>';
                                                        },
                                                        option: function(data,escape){
                                                            if( data.customProperties ){
                                                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                                                            }
                                                            return '<div>' + escape(data.text) + '</div>';
                                                        },
                                                    },
                                                }));
                                            });
                                        </script>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Imagem</h2>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="imagem" class="dropzone w-100" id="dropzone-custom">
                                            <div class="fallback">
                                                <input name="imagem" id="imagem" type="file" class="d-none" accept="image/png,image/jpeg,image/webp" />
                                            </div>
                                            <div class="dz-message" <?= isset($post['imagem']) && !empty($post['imagem']) ? "style='display: none;'" : ""; ?>>
                                                <h3 class="dropzone-msg-title">
                                                    <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-library-photo"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 3m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 7.26a2.005 2.005 0 0 0 -1.012 1.737v10c0 1.1 .9 2 2 2h10c.75 0 1.158 -.385 1.5 -1" /><path d="M17 7h.01" /><path d="M7 13l3.644 -3.644a1.21 1.21 0 0 1 1.712 0l3.644 3.644" /><path d="M15 12l1.644 -1.644a1.21 1.21 0 0 1 1.712 0l2.644 2.644" /></svg>
                                                    Arraste e solte a imagem aqui
                                                </h3>
                                                <span class="dropzone-msg-desc">Essa imagem será mostrada na página do post.</span>
                                            </div>
                                            <?php if (isset($post['imagem']) && !empty($post['imagem'])): ?>
                                            <img class="previsualizacao" src="<?= INCLUDE_PATH . "files/blog/{$post['id']}/{$post['imagem']}" ?>" alt="" aria-hidden="true" style="display: block; max-width: 100%; height: auto; margin: 0px auto;">
                                            <?php endif; ?>
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
                                <h2 class="card-title">Conteúdo</h2>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <div class="col">
                                        <textarea name="resumo" id="resumo"><?= $post['resumo']; ?></textarea>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Google / SEO</h2>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <label for="seo_nome" class="col-3 col-form-label">Nome do post</label>
                                    <div class="col">
                                        <input name="seo_nome" id="seo_nome"
                                            type="text" class="form-control" value="<?= $post['seo_nome']; ?>">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="seo_descricao" class="col-3 col-form-label">Descrição do post</label>
                                    <div class="col">
                                        <textarea name="seo_descricao" id="seo_descricao" class="form-control" rows="3"><?= $post['seo_descricao']; ?></textarea>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="d-flex">
                        <!-- Botão Visualizar -->
                        <a href="<?php echo INCLUDE_PATH . "post?id={$post['id']}"; ?>" target="_blank" class="btn btn-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-external-link">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                <path d="M11 13l9 -9" />
                                <path d="M15 4h5v5" />
                            </svg>
                            Visualizar
                        </a>
                        <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary ms-auto">Salvar</button>
                    </div>

                </div>

            </div>
        </form>
    </div>
</div>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>

<!-- Tags -->
<script src="<?php echo INCLUDE_PATH; ?>dist/libs/tagify/dist/tagify.js"></script>
<script>
    var input = document.querySelector('#tags');
    new Tagify(input);
</script>

<!-- jQuery Validation, Input Mask, and Validation Script -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script>
    $(document).ready(function () {
        $("#updatePost").validate({
            rules: {
                titulo: {
                    required: true,
                    minlength: 3
                },
                seo_descricao: {
                    minlength: 10
                }
            },
            messages: {
                titulo: {
                    required: "Por favor, insira o título do post.",
                    minlength: "O título deve ter pelo menos 3 caracteres."
                },
                seo_descricao: {
                    minlength: "A descrição deve ter pelo menos 10 caracteres."
                }
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
                formData.append("post_id", <?= $post['id']; ?>);
                formData.append("action", "update-post");

                // Realiza o AJAX para enviar os dados
                $.ajax({
                    url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/update-post.php', // Substitua pelo URL do seu endpoint
                    type: 'POST',
                    data: formData,
                    processData: false, // Impede que o jQuery processe os dados
                    contentType: false, // Impede que o jQuery defina o Content-Type
                    success: function (response) {
                        if (response.status == "success") {
                            // Sucesso na resposta do servidor
                            window.location.href = "<?= INCLUDE_PATH_ADMIN; ?>posts";
                        } else {
                            // console.error("Erro no AJAX:", status, error);

                            // Caso contrário, exibe a mensagem de erro
                            $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                            $("#updatePost").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');
                        }
                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro no AJAX:", status, error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#updatePost").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');

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

<script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/hugerte.min.js"></script>
<script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/langs/pt_BR.js"></script>
<script>
    hugerte.init({
        selector: '#resumo',
        language: 'pt_BR',
        plugins: 'accordion advlist anchor autolink autosave charmap code codesample directionality emoticons fullscreen help image insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table template visualblocks visualchars wordcount',
    });
</script>
