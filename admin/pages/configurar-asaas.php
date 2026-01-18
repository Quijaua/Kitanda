<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Configurações da Loja
                </h1>
                <div class="text-secondary mt-1">Altere as informações da sua loja aqui!</div>
            </div>
        </div>
    </div>
</div>

<?php
    // Consulta para buscar o produto selecionado
    $stmt = $conn->prepare("
        SELECT * 
        FROM tb_lojas
        WHERE vendedora_id = ? 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $loja = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="row g-0">
                <div class="col-12 col-md-3 border-end">
                    <div class="card-body">
                        <h2 class="subheader">Configurações da loja</h2>
                        <div class="list-group list-group-transparent">
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>minha-loja" class="list-group-item list-group-item-action d-flex align-items-center">Minha loja</a>
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>meu-endereco" class="list-group-item list-group-item-action d-flex align-items-center">Meu endereço</a>
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>configurar-asaas" class="list-group-item list-group-item-action d-flex align-items-center active">Asaas</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                    <form id="updateAddress" action="<?= INCLUDE_PATH_ADMIN; ?>back-end/update-store.php" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <h2 class="mb-4">Configurações Asaas</h1>
                            <h3 class="card-title mb-0">Conta</h3>

                            <div id="fields-container" class="row align-items-center mb-4">
                                <div class="row g-3 mt-0">
                                    <div class="col-md-5">
                                        <!-- E-mail Asaas -->
                                        <label for="email" class="form-label required">E-mail Asaas</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/mail -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-mail"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                                            </span>
                                            <input id="email" name="email" type="email" class="form-control" value="<?= htmlspecialchars($loja['asaas_email'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent mt-auto">
                            <div class="btn-list justify-content-end">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" class="btn btn-primary btn-2">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery Validation, Input Mask, and Validation Script -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="<?php echo INCLUDE_PATH; ?>assets/ajax/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function () {
        $("#updateAddress").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: "Por favor, informe o e-mail.",
                    email: "O e-mail inserido é inválido."
                }
            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.next("label"));
                } else if (element.prop("type") === "email") {
                    error.insertAfter(element.closest(".input-icon"));
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
                <?php if ($loja): ?>
                formData.append("loja_id", <?= $loja['id']; ?>);
                <?php endif; ?>
                formData.append("action", "configurar-asaas");

                // Realiza o AJAX para enviar os dados
                $.ajax({
                    url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/update-store.php', // Substitua pelo URL do seu endpoint
                    type: 'POST',
                    data: formData,
                    processData: false, // Impede que o jQuery processe os dados
                    contentType: false, // Impede que o jQuery defina o Content-Type
                    success: function (response) {
                        if (response.status == "success") {
                            // Sucesso na resposta do servidor
                            location.reload();
                        } else {
                            // console.error("Erro no AJAX:", status, error);

                            // Caso contrário, exibe a mensagem de erro
                            $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                            $("#updateAddress #fields-container").before('<div class="alert alert-danger alert-dismissible fade show w-100 mb-0 mt-3" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');
                        }
                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro no AJAX:", status, error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#updateAddress #fields-container").before('<div class="alert alert-danger alert-dismissible fade show w-100 mb-0 mt-3" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');

                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    }
                });
            }
        });
    });
</script>