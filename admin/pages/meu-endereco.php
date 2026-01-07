<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Configurações da Loja
                </h2>
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
                        <h4 class="subheader">Configurações da loja</h4>
                        <div class="list-group list-group-transparent">
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>minha-loja" class="list-group-item list-group-item-action d-flex align-items-center">Minha loja</a>
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>meu-endereco" class="list-group-item list-group-item-action d-flex align-items-center active">Meu endereço</a>
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>configurar-asaas" class="list-group-item list-group-item-action d-flex align-items-center">Asaas</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                    <form id="updateAddress" action="<?= INCLUDE_PATH_ADMIN; ?>back-end/update-store.php" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <h2 class="mb-4">Meu Endereço</h2>
                            <h3 class="card-title mb-0">Endereço de Origem</h3>

                            <div id="fields-container" class="row align-items-center mb-4">
                                <div class="row g-3 mt-0">
                                    <div class="col-md-4">
                                        <label class="form-label">CEP</label>
                                        <input onblur="getCepData()" type="text" class="form-control" name="cep" id="field-zipcode" value="<?= htmlspecialchars($loja['cep'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Logradouro</label>
                                        <input type="text" class="form-control" name="logradouro" id="field-street" value="<?= htmlspecialchars($loja['logradouro'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Número</label>
                                        <input type="text" class="form-control" name="numero" id="numero" value="<?= htmlspecialchars($loja['numero'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Complemento</label>
                                        <input type="text" class="form-control" name="complemento" id="complemento" value="<?= htmlspecialchars($loja['complemento'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bairro</label>
                                        <input type="text" class="form-control" name="bairro" id="field-district" value="<?= htmlspecialchars($loja['bairro'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Cidade</label>
                                        <input type="text" class="form-control" name="cidade" id="field-city" value="<?= htmlspecialchars($loja['cidade'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" name="estado" id="field-state" value="<?= htmlspecialchars($loja['estado'] ?? '') ?>" required>
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
    function getCepData() {
        let cep = $('#field-zipcode').val();
        cep = cep.replace(/\D/g, "");
        if (cep.length < 8) {
            $(".alert").remove(); // Remove qualquer mensagem de erro anterior
            $("#updateAddress #fields-container").before('<div class="alert alert-danger alert-dismissible fade show w-100 mb-0 mt-3" role="alert">CEP deve conter no mínimo 8 dígitos<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar alerta"></button></div>');
            $("#field-zipcode").addClass('is-invalid').focus();
            return;
        }
        $("#field-zipcode").removeClass('is-invalid');
        $(".alert").remove(); // Remove qualquer mensagem

        if (cep != "") {
            $("#field-street").val("Carregando...");
            $("#field-district").val("Carregando...");
            $("#field-city").val("Carregando...");
            $("#field-state").val("...");
            $.getJSON("https://viacep.com.br/ws/" + cep + "/json/", function (data) {
                $("#field-street").val(data.logradouro);
                $("#field-district").val(data.bairro);
                $("#field-city").val(data.localidade);
                $("#field-state").val(data.uf);
                $("#field-street-number").focus();

            }).fail(function () {
                $("#field-street").val("");
                $("#field-district").val("");
                $("#field-city").val("");
                $("#field-state").val(" ");
            });
        }
    }

    $(document).ready(function () {
        $('#field-zipcode').mask('00000-000');

        $("#updateAddress").validate({
            rules: {
                cep: {
                    required: true,
                    minlength: 9,
                    maxlength: 9
                },
                logradouro: {
                    required: true,
                    minlength: 3
                },
                numero: {
                    required: true
                },
                bairro: {
                    required: true,
                    minlength: 2
                },
                cidade: {
                    required: true,
                    minlength: 2
                },
                estado: {
                    required: true,
                    minlength: 2
                }
            },
            messages: {
                cep: {
                    required: "Por favor, informe o CEP.",
                    minlength: "O CEP deve ter ao menos 9 dígitos.",
                    maxlength: "O CEP deve ter no máximo 9 caracteres."
                },
                logradouro: {
                    required: "Por favor, informe o logradouro.",
                    minlength: "Informe um logradouro válido."
                },
                numero: {
                    required: "Por favor, digite o número ou 0 se não houver."
                },
                bairro: {
                    required: "Por favor, informe o bairro.",
                    minlength: "Informe um bairro válido."
                },
                cidade: {
                    required: "Por favor, informe a cidade.",
                    minlength: "Informe uma cidade válida."
                },
                estado: {
                    required: "Por favor, informe o estado.",
                    minlength: "Informe um estado válido."
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
                <?php if ($loja): ?>
                formData.append("loja_id", <?= $loja['id']; ?>);
                <?php endif; ?>
                formData.append("action", "atualizar-endereco");

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