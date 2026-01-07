<?php
    $read = verificaPermissao($_SESSION['user_id'], 'posts', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $only_own = verificaPermissao($_SESSION['user_id'], 'posts', 'only_own', $conn);
    $disabledOnlyOwn = !$only_own ? 'disabled' : '';

    $create = verificaPermissao($_SESSION['user_id'], 'posts', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'posts', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';

    $delete = verificaPermissao($_SESSION['user_id'], 'posts', 'delete', $conn);
    $disabledDelete = !$delete ? 'disabled' : '';
?>

<?php
    function formatarTags(?string $jsonTags, int $limit = 150, string $sep = ', '): string
    {
        // Garante que, se vier null ou JSON inválido, teremos um array vazio
        $dados   = json_decode($jsonTags ?? '[]', true);
        $valores = array_column(is_array($dados) ? $dados : [], 'value');
        $todas   = implode($sep, $valores);

        return mb_strlen($todas) > $limit
            ? mb_substr($todas, 0, $limit) . '...'
            : $todas;
    }
?>

<?php
    // Se o usuário possui somente a permissão only_own, filtramos os arquivos criados por ele.
    if ($read) {
        // Caso contrário, exibe todos os arquivos
        $stmt = $conn->prepare("
            SELECT *
            FROM tb_arquivos
            GROUP BY id
            ORDER BY id DESC
        ");
        $stmt->execute();
    } else if ($only_own) {
        $stmt = $conn->prepare("
            SELECT *
            FROM tb_arquivos
            WHERE criado_por = ?
            GROUP BY id
            ORDER BY id DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }

    $arquivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Modal de Upload -->
<div class="modal modal-blur fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar modal"></button> -->
            <div class="modal-status bg-info"></div>
            <div class="modal-body text-center py-4">
                <!-- Download SVG icon from https://tabler.io/icons/icon/cloud-up -->
                <!-- <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-up"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18.004h-5.343c-2.572 -.004 -4.657 -2.011 -4.657 -4.487c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.38 0 2.57 .811 3.128 1.986" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg> -->
                <div class="card">
                  <div class="card-body">
                    <form class="dropzone" id="uploadArquivo" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/upload-arquivos.php" method="post" enctype="multipart/form-data">
                      <div class="fallback">
                        <input name="imagem" id="imagem" type="file" accept="image/png,image/jpeg,image/webp" />
                      </div>
                      <div class="dz-message">
                        <h3 class="dropzone-msg-title">Enviar Arquivo</h3>
                        <span class="dropzone-msg-desc">Arraste ou clique para enviar arquivos</span>
                      </div>
                    </form>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-3 w-100" data-bs-dismiss="modal"> Cancelar </button>
                        </div>
                        <div class="col">
                            <button type="submit" form="uploadArquivo" class="btn btn-info btn-4 w-100"> Enviar </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar modal"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <!-- Download SVG icon from http://tabler.io/icons/icon/alert-triangle -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg"><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                <h3>Confirmar Exclusão</h3>
                <div class="text-secondary">Tem certeza de que deseja excluir o arquivo <b>"<span id="arquivoNome"></span>"</b>?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-3 w-100" data-bs-dismiss="modal"> Cancel </button>
                        </div>
                        <div class="col">
                            <button type="button" id="confirmDelete" class="btn btn-danger btn-4 w-100"> Deletar </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #posts_filter, #posts_length {
        display: none;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        justify-content: revert;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Arquivos
                </h2>
                <div class="text-secondary mt-1">Aqui estão os arquivos do sistema.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <a href="#" type="button" class="btn btn-6 btn-info d-flex align-items-center gap-1 btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2"><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    Enviar Arquivos
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <?php if (!$only_own && !$read): ?>
            <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
            <?php exit; endif; ?>

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">Arquivos</h4>
                    </div>

                    <div class="row row-cards">
                        <?php foreach($arquivos as $arquivo) : ?>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-sm">
                                <img src="<?php echo INCLUDE_PATH; ?>files/arquivos/<?php echo $arquivo['criado_por']; ?>/<?php echo $arquivo['nome']; ?>" class="card-img-top"/>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div><?php echo $arquivo['nome']; ?></div>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="<?php echo INCLUDE_PATH; ?>files/arquivos/<?php echo $arquivo['criado_por']; ?>/<?php echo $arquivo['nome']; ?>" class="text-secondary copy-link">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/eye -->
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                                Copiar
                                            </a>

                                            <span class="text-success" style="display: none">
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-file-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2l.117 .007a1 1 0 0 1 .876 .876l.007 .117v4l.005 .15a2 2 0 0 0 1.838 1.844l.157 .006h4l.117 .007a1 1 0 0 1 .876 .876l.007 .117v9a3 3 0 0 1 -2.824 2.995l-.176 .005h-10a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-14a3 3 0 0 1 2.824 -2.995l.176 -.005zm3.707 10.293a1 1 0 0 0 -1.414 0l-3.293 3.292l-1.293 -1.292a1 1 0 1 0 -1.414 1.414l2 2a1 1 0 0 0 1.414 0l4 -4a1 1 0 0 0 0 -1.414m-.707 -9.294l4 4.001h-4z" /></svg>
                                            </span>

                                            <a href="javascript:void(0)" id="deletaArquivo" class="text-danger btn-delete" data-id="<?php echo $arquivo['id']; ?>" data-folder="<?php echo $arquivo['criado_por']; ?>" data-name="<?php echo $arquivo['nome']; ?>">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/eye -->
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-file-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2l.117 .007a1 1 0 0 1 .876 .876l.007 .117v4l.005 .15a2 2 0 0 0 1.838 1.844l.157 .006h4l.117 .007a1 1 0 0 1 .876 .876l.007 .117v9a3 3 0 0 1 -2.824 2.995l-.176 .005h-10a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-14a3 3 0 0 1 2.824 -2.995l.176 -.005h5zm-1.489 9.14a1 1 0 0 0 -1.301 1.473l.083 .094l1.292 1.293l-1.292 1.293l-.083 .094a1 1 0 0 0 1.403 1.403l.094 -.083l1.293 -1.292l1.293 1.292l.094 .083a1 1 0 0 0 1.403 -1.403l-.083 -.094l-1.292 -1.293l1.292 -1.293l.083 -.094a1 1 0 0 0 -1.403 -1.403l-.094 .083l-1.293 1.292l-1.293 -1.292l-.094 -.083l-.102 -.07z" /><path d="M19 7h-4l-.001 -4.001z" /></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(count($arquivos) == 0) : ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="text-center">Nenhum Arquivo Encontrado</h3>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery Validation, Input Mask, and Validation Script -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>

<!-- Listar Arquivos -->
<script>
    $(document).ready(function() {

        let arquivosDropzone = new Dropzone("#uploadArquivo");

        $("#uploadArquivo").validate({
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
                formData.append("action", "upload-arquivos");
                formData.append("criado_por", "<?= $_SESSION['user_id']; ?>");

                // Adiciona imagens ao formulário
                arquivosDropzone.files.forEach(file => {
                    formData.append('imagens[]', file); // Adiciona cada imagem ao FormData
                });

                console.log('Form Data:', formData);

                // Realiza o AJAX para enviar os dados
                $.ajax({
                    url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/upload-arquivos.php', // Substitua pelo URL do seu endpoint
                    type: 'POST',
                    data: formData,
                    processData: false, // Impede que o jQuery processe os dados
                    contentType: false, // Impede que o jQuery defina o Content-Type
                    success: function (response) {
                        if (response.status == "success") {
                            // Sucesso na resposta do servidor
                            window.location.href = "<?= INCLUDE_PATH_ADMIN; ?>arquivos";
                        } else {
                            // console.error("Erro no AJAX:", status, error);

                            // Caso contrário, exibe a mensagem de erro
                            $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                            $("#createPost").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');
                        }
                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro no AJAX:", status, error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#createPost").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');

                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    }
                });
            }
        });

        let elementIdToDelete = null;

        // Quando clicar no botão de exclusão
        $(document).on('click', '.btn-delete', function () {
            elementIdToDelete = $(this).data('id'); // Obtém o ID do elemento a ser excluído
            elementFolderId = $(this).data('folder');
            const elementNameToDelete = $(this).data('name'); // Obtém o nome do elemento a ser excluído
            $('#arquivoNome').text(elementNameToDelete); // Define o nome no modal
            $('#deleteModal').modal('show'); // Mostra o modal
        });

        // Quando confirmar a exclusão
        $('#confirmDelete').on('click', function () {
            if (elementIdToDelete) {
                $.ajax({
                    url: `<?= INCLUDE_PATH_ADMIN; ?>back-end/delete-arquivos.php?arquivo_id=${elementIdToDelete}&folder_id=${elementFolderId}`,
                    type: 'GET',
                    success: function (response) {
                        window.location.href = "<?= INCLUDE_PATH_ADMIN; ?>arquivos";
                    },
                    error: function (xhr, status, error) {
                        console.error('Erro:', error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#posts").before(`
                            <div class="alert alert-danger" role="alert">
                                Ocorreu um erro, tente novamente mais tarde.
                            </div>
                        `);
                    }
                });
            }
        });

        $(".copy-link").click(function (e) {
            e.preventDefault();
            let temp = $("<input>");
            $("body").append(temp);
            temp.val($(this).attr("href")).select();
            document.execCommand("copy");
            temp.remove();
            $(this).hide();
            $(this).parent().children("span").fadeIn(400);
            setTimeout(() => {
                $(this).parent().children("span").fadeOut(100);
                $(this).fadeIn(400);
            }, 2000);
        })

    });
</script>