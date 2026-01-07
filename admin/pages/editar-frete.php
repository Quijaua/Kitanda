<?php
    $read = verificaPermissao($_SESSION['user_id'], 'fretes', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $only_own = verificaPermissao($_SESSION['user_id'], 'fretes', 'only_own', $conn);
    $disabledOnlyOwn = !$only_own ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'fretes', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';

    $isAdmin = (getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador');
?>

<?php
    if (isset($_GET['id'])) {
        // ID da frete
        $frete_id = $_GET['id'];

        // Consulta para buscar a frete selecionado
        $stmt = $conn->prepare("
            SELECT * 
            FROM tb_frete_dimensoes
            WHERE id = ? 
            LIMIT 1
        ");
        $stmt->execute([$frete_id]);
        $frete = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($frete)) {
            $_SESSION['error_msg'] = 'Frete não encontrado.';
            header('Location: ' . INCLUDE_PATH_ADMIN . 'fretes');
            exit;
        }
    } else {
        $_SESSION['error_msg'] = 'Insira o ID do frete.';
        header('Location: ' . INCLUDE_PATH_ADMIN . 'fretes');
        exit;
    }
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Editar Medidas
                </h2>
                <div class="text-secondary mt-1">Aqui você pode editar um frete.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <ol class="breadcrumb breadcrumb-muted" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>frete">Fretes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar Medidas</li>
                    </ol>
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
        <form id="updateFreight" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update-freight.php" method="post" enctype="multipart/form-data">
            <div class="row">

                <?php if (!$isAdmin): ?>
                    <?php if ($only_own): ?>
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
                        <div class="alert alert-info">Você pode visualizar os detalhes do frete, mas não pode editá-la.</div>
                    </div>
                    <?php endif; ?>
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
                                <h4 class="card-title">Peso e Dimensões</h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <div class="row g-3">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label required">Nome</label>
                                                    <input name="nome" id="nome"
                                                        type="text" class="form-control" value="<?= $frete['nome']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label required">Altura (cm)</label>
                                                    <input name="altura" id="altura"
                                                        type="number" class="form-control" step="0.01" value="<?= $frete['altura']; ?>" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label required">Largura (cm)</label>
                                                    <input name="largura" id="largura"
                                                        type="number" class="form-control" step="0.01" value="<?= $frete['largura']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label required">Comprimento (cm)</label>
                                                    <input name="comprimento" id="comprimento"
                                                        type="number" class="form-control" step="0.01" value="<?= $frete['comprimento']; ?>" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label required">Peso (kg)</label>
                                                    <input name="peso" id="peso"
                                                        type="number" class="form-control" step="0.01" value="<?= $frete['peso']; ?>" required>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center justify-content-center">
                                        <img src="<?= INCLUDE_PATH; ?>assets/img/package-icon.png" alt="Ícone de pacote" style="height: 250px;">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                    <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary ms-auto">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>

<!-- jQuery Validation, Input Mask, and Validation Script -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script>
    $(document).ready(function () {
        $("#updateFreight").validate({
            rules: {
                nome: {
                    required: true,
                    minlength: 3
                },
                altura: {
                    required: true,
                },
                largura: {
                    required: true,
                },
                comprimento: {
                    required: true,
                },
                peso: {
                    required: true,
                },
            },
            messages: {
                nome: {
                    required: "Por favor, insira o nome do frete.",
                    minlength: "O nome deve ter pelo menos 3 caracteres."
                },
                altura: {
                    required: "Por favor, insira a altura do frete",
                },
                largura: {
                    required: "Por favor, insira a largura do frete",
                },
                comprimento: {
                    required: "Por favor, insira o comprimento do frete",
                },
                peso: {
                    required: "Por favor, insira o peso do frete",
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
                formData.append("frete_id", <?= $frete['id']; ?>);
                formData.append("action", "editar-frete");

                // Realiza o AJAX para enviar os dados
                $.ajax({
                    url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/update-freight.php', // Substitua pelo URL do seu endpoint
                    type: 'POST',
                    data: formData,
                    processData: false, // Impede que o jQuery processe os dados
                    contentType: false, // Impede que o jQuery defina o Content-Type
                    success: function (response) {
                        if (response.status == "success") {
                            // Sucesso na resposta do servidor
                            window.location.href = "<?= INCLUDE_PATH_ADMIN; ?>frete";
                        } else {
                            // console.error("Erro no AJAX:", status, error);

                            // Caso contrário, exibe a mensagem de erro
                            $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                            $("#updateFreight").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');
                        }
                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro no AJAX:", status, error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#updateFreight").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');

                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    }
                });
            }
        });
    });
</script>