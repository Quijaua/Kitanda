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

    $loja['imagem_path'] = !empty($loja['imagem'])
                           ? str_replace(' ', '%20', INCLUDE_PATH . "files/lojas/{$loja['id']}/perfil/{$loja['imagem']}")
                           : INCLUDE_PATH . "assets/preview-image/profile.jpg";
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
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>minha-loja" class="list-group-item list-group-item-action d-flex align-items-center active">Minha loja</a>
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>meu-endereco" class="list-group-item list-group-item-action d-flex align-items-center">Meu endereço</a>
                            <a href="<?= INCLUDE_PATH_ADMIN; ?>configurar-asaas" class="list-group-item list-group-item-action d-flex align-items-center">Asaas</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                    <form id="updateStore" action="<?= INCLUDE_PATH_ADMIN; ?>back-end/update-store.php" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <h2 class="mb-4">Minha Loja</h2>
                            <h3 class="card-title">Personalização da Loja</h3>

                            <div id="fields-container" class="row align-items-center mb-4">
                                <div class="col-auto">
                                    <span id="store-avatar" class="avatar avatar-xl"
                                        style="background-image: url('<?= $loja['imagem_path']; ?>')">
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <label for="avatar-input" class="btn btn-1" id="change-avatar-btn">Alterar foto</label>
                                    <input type="file" name="imagem" id="avatar-input" accept="image/*" style="display:none">
                                </div>
                                <?php if (!empty($loja['imagem'])): ?>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-ghost-danger" id="delete-avatar-btn">Excluir foto</button>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label required">Nome da Loja</label>
                                    <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($loja['nome'] ?? '') ?>" required>
                                </div>

                                <div class="col-md-12 row g-3 mt-0">
                                    <!-- Instagram -->
                                    <div class="col-md-4">
                                        <label for="instagram" class="form-label">Instagram</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/brand-instagram -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-instagram"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M16.5 7.5v.01" /></svg>
                                            </span>
                                            <input id="instagram" name="instagram" type="text" class="form-control" value="<?= htmlspecialchars($loja['instagram'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <!-- Facebook -->
                                    <div class="col-md-4">
                                        <label for="facebook" class="form-label">Facebook</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/brand-facebook -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-facebook"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" /></svg>
                                            </span>
                                            <input id="facebook" name="facebook" type="text" class="form-control" value="<?= htmlspecialchars($loja['facebook'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <!-- Tiktok -->
                                    <div class="col-md-4">
                                        <label for="tiktok" class="form-label">Tiktok</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/brand-tiktok -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-tiktok"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 7.917v4.034a9.948 9.948 0 0 1 -5 -1.951v4.5a6.5 6.5 0 1 1 -8 -6.326v4.326a2.5 2.5 0 1 0 4 2v-11.5h4.083a6.005 6.005 0 0 0 4.917 4.917z" /></svg>
                                            </span>
                                            <input id="tiktok" name="tiktok" type="text" class="form-control" value="<?= htmlspecialchars($loja['tiktok'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <!-- Telefone -->
                                    <div class="col-md-6">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/phone -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-phone"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                                            </span>
                                            <input id="telefone" name="telefone" type="text" class="form-control" value="<?= htmlspecialchars($loja['telefone'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <!-- Site -->
                                    <div class="col-md-6">
                                        <label for="site" class="form-label">Site</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/world -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-world"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M3.6 9h16.8" /><path d="M3.6 15h16.8" /><path d="M11.5 3a17 17 0 0 0 0 18" /><path d="M12.5 3a17 17 0 0 1 0 18" /></svg>
                                            </span>
                                            <input id="site" name="site" type="text" class="form-control" value="<?= htmlspecialchars($loja['site'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label required">Mini Bio <small>(máx. 300 caracteres)</small></label>
                                    <textarea class="form-control" name="mini_bio" rows="3" maxlength="300" required><?= htmlspecialchars($loja['mini_bio'] ?? '') ?></textarea>
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
        $('#telefone').mask('(00) 00000-0000');

        // Preview da imagem da loja ao selecionar novo arquivo
        $("#avatar-input").on("change", function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $("#store-avatar").css("background-image", `url('${e.target.result}')`);
                };
                reader.readAsDataURL(file);
            }
        });

        $("#updateStore").validate({
            rules: {
                nome: {
                    required: true,
                    minlength: 3
                },
                mini_bio: {
                    required: true,
                    minlength: 3,
                    maxlength: 300
                }
            },
            messages: {
                nome: {
                    required: "Por favor, insira o nome do produto.",
                    minlength: "O nome deve ter pelo menos 3 caracteres."
                },
                mini_bio: {
                    required: "Por favor, insira a minibio da loja.",
                    minlength: "A minibio deve ter pelo menos 3 caracteres.",
                    maxlength: "A minibio deve ter no máx. 300 caracteres."
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
                <?php if (isset($loja['id'])): ?>
                formData.append("loja_id", <?= $loja['id']; ?>);
                <?php endif; ?>
                formData.append("action", "atualizar-loja");

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
                            $("#updateStore #fields-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        }
                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro no AJAX:", status, error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#updateStore #fields-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    }
                });
            }
        });

        // marca exclusão de foto
        $('#delete-avatar-btn').on('click', function() {
            // Confirmação
            if (!confirm('Deseja realmente excluir a foto de perfil?')) {
                return;
            }
            <?php if (isset($loja['id'])): ?>
            var lojaId = <?= json_encode($loja['id']); ?>;
            <?php endif; ?>
            $.ajax({
                url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/update-store.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'remover-imagem',
                    loja_id: lojaId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Remove preview
                        $('#store-avatar').css('background-image', "url('<?= INCLUDE_PATH; ?>assets/preview-image/product.jpg')");
                        // Remove botão de exclusão
                        $('#delete-avatar-btn').closest('.col-auto').remove();

                        // Caso contrário, exibe a mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#updateStore #fields-container").before('<div class="alert alert-success alert-dismissible fade show w-100" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    } else {
                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#updateStore #fields-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro ao remover a imagem, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                },
                error: function() {
                    // Caso haja erro na requisição, exibe uma mensagem de erro
                    $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                    $("#updateStore #fields-container").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            });
        });
    });
</script>