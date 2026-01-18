<?php
    $read = verificaPermissao($_SESSION['user_id'], 'usuarios', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $only_own = verificaPermissao($_SESSION['user_id'], 'usuarios', 'only_own', $conn);
    $disabledOnlyOwn = !$only_own ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'usuarios', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';
?>

<?php
    // Validação do ID do usuário recebido via GET
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['error_msg'] = "ID do usuário inválido.";
        header('Location: ' . INCLUDE_PATH_ADMIN . 'usuarios');
        exit;
    }
    $user_id = intval($_GET['id']);

    // Busca os dados do usuário
    $stmtUser = $conn->prepare("SELECT * FROM tb_clientes WHERE id = :id");
    $stmtUser->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmtUser->execute();
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['error_msg'] = "Usuário não encontrado.";
        header('Location: ' . INCLUDE_PATH_ADMIN . 'usuarios');
        exit;
    }

    // Busca as funções disponíveis
    $stmtFuncoes = $conn->query("SELECT id, nome FROM tb_funcoes");
    $funcoes = $stmtFuncoes->fetchAll(PDO::FETCH_ASSOC);

    // Busca as funções disponíveis
    $stmtPermissaoUsuario = $conn->prepare("SELECT permissao_id FROM tb_permissao_usuario WHERE usuario_id = ?");
    $stmtPermissaoUsuario->execute([$user_id]);
    $permissao_usuario = $stmtPermissaoUsuario->fetch(PDO::FETCH_ASSOC) ?: [];
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Visualizar Usuário
                </h1>
                <div class="text-secondary mt-1">Visualize as informações do usuário e sua função.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <nav aria-label="Caminho de navegação">
                        <ol class="breadcrumb breadcrumb-muted">
                            <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>usuarios">Usuários</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Visualizar Usuário</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <form id="editUser" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update-user.php?id=<?php echo $usuario['id']; ?>" method="post">
            <div class="row">

                <?php if ($only_own && $usuario['criado_por'] !== $_SESSION['user_id']): ?>
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
                    <div class="alert alert-info">Você pode visualizar os detalhes desta página, mas não pode editá-los.</div>
                </div>
                <?php endif; ?>

                <!-- Exibe mensagem de erro, se houver -->
                <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger w-100" role="alert">
                        <div class="d-flex">
                            <div>
                                <!-- Ícone de alerta -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0-18 0"></path>
                                    <path d="M12 8v4"></path>
                                    <path d="M12 16h.01"></path>
                                </svg>
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
                                <h2 class="card-title">Informações do Usuário</h2>
                            </div>
                            <div class="card-body">
                                <fieldset disabled>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Nome -->
                                            <div class="mb-3">
                                                <label for="nome" class="form-label required">Nome</label>
                                                <input id="nome" name="nome" type="text" class="form-control" required value="<?= $usuario['nome']; ?>">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <!-- E-mail -->
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label required">E-mail</label>
                                                        <div class="input-icon">
                                                            <span class="input-icon-addon">
                                                                <!-- Ícone de E-mail -->
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-mail">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                                                    <path d="M3 7l9 6l9 -6" />
                                                                </svg>
                                                            </span>
                                                            <div class="input-group" id="resendCodeContent">
                                                            <?php 
                                                            // Se o usuário estiver confirmado (status == 1) e o magic_link estiver preenchido, não permite alteração do e-mail
                                                            if ($usuario['status'] == 1 && empty($usuario['magic_link'])) {
                                                                echo '<input id="email" name="email_disabled" type="email" class="form-control" value="' . htmlspecialchars($usuario['email']) . '" disabled readonly>';
                                                                // Input oculto para enviar o e-mail atual
                                                                echo '<input type="hidden" name="email" value="' . htmlspecialchars($usuario['email']) . '">';
                                                            } else {
                                                                echo '<input id="email" name="email" type="email" class="form-control" required value="' . htmlspecialchars($usuario['email']) . '">';
                                                            }
                                                            ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- Função -->
                                                    <div class="mb-3">
                                                        <label for="funcao" class="form-label required">Função</label>
                                                        <select id="funcao" name="funcao_id" class="form-select" required>
                                                            <option value="">Selecione a função</option>
                                                            <?php foreach($funcoes as $funcao): ?>
                                                            <option value="<?php echo $funcao['id']; ?>" <?= isset($permissao_usuario['permissao_id']) && $funcao['id'] == $permissao_usuario['permissao_id'] ? 'selected' : ''; ?>>
                                                                <?= $funcao['nome']; ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <?php //if (isset($permissao_usuario['permissao_id']) && $permissao_usuario['permissao_id'] == 2): // Se for uma vendedora ?>
                                                <div class="col-md-4">
                                                    <!-- Função -->
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label required">Status</label>
                                                        <select id="status" name="status" class="form-select" required>
                                                            <option value="" disabled <?= $usuario['status'] === null ? 'selected' : '' ?>>Selecione o status</option>
                                                            <option value="1" <?= $usuario['status'] == 1 ? 'selected' : '' ?>>Ativo</option>
                                                            <option value="0" <?= $usuario['status'] == 0 ? 'selected' : '' ?>>Inativo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <?php //endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <?php if ($update): ?>
                            <div class="card-footer text-end">
                                <div class="d-flex justify-content-end">
                                    <!-- Botão Editar -->
                                    <a href="<?= INCLUDE_PATH_ADMIN . "editar-usuario?id={$usuario['id']}"; ?>" class="btn btn-6 btn-outline-primary d-flex align-items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-edit">
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                            <path d="M16 5l3 3"></path>
                                        </svg>
                                        Editar
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Botao para copiar codigo -->
<script>
    $(document).ready(function () {
        // Inicializa o tooltip para os elementos que possuem data-bs-toggle="tooltip"
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        $('.copy-btn').on('click', function () {
            var btn = $(this);
            var link = btn.data('link');

            // Copia o link para o clipboard
            navigator.clipboard.writeText(link).then(function () {
            // Altera o estilo do botão para exibir borda verde
            btn.css('border-color', '#2fb344');
            btn.css('box-shadow', '0 1px 1px rgba(24, 36, 51, .06);, 0 0 0 .25rem rgba(47, 179, 68, .25)');

            // Atualiza o tooltip para "Copiado!"
            btn.attr('title', 'Copiado!');
            btn.attr('aria-label', 'Copiado!');
            btn.attr('data-bs-original-title', 'Copiado!');
            var tooltipInstance = bootstrap.Tooltip.getInstance(btn[0]);
            if (tooltipInstance) {
                tooltipInstance.hide();
                // Recria o tooltip para refletir o novo title
                tooltipInstance.dispose();
                tooltipInstance = new bootstrap.Tooltip(btn[0]);
            }

            // Altera o ícone para um "check" (exemplo simples)
            btn.find('svg').replaceWith(
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" ' +
                'stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">' +
                '<polyline points="20 6 9 17 4 12"></polyline>' +
                '</svg>'
            );

            // Reverte as alterações após 3 segundos
            setTimeout(function () {
                btn.css('border', '');
                btn.attr('title', 'Copiar código para finalizar registro?');
                btn.attr('aria-label', 'Copiar código para finalizar registro?');
                btn.attr('data-bs-original-title', 'Copiar código para finalizar registro?');
                if (tooltipInstance) {
                    tooltipInstance.hide();
                    tooltipInstance.dispose();
                    tooltipInstance = new bootstrap.Tooltip(btn[0]);
                }
                // Restaura o ícone original
                btn.find('svg').replaceWith(
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" ' +
                    'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">' +
                        '<path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667-2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1-2.667 2.667h-8.666a2.667 2.667 0 0 1-2.667-2.667z"></path>' +
                        '<path d="M4.012 16.737a2.005 2.005 0 0 1-1.012-1.737v-10c0-1.1.9-2 2-2h10c.75 0 1.158.385 1.5 1"></path>' +
                    '</svg>'
                );
            }, 3000);
            }, function () {
                // Caso ocorra algum erro na cópia
                alert('Erro ao copiar o link! Tente copiar manualmente:\n<?= INCLUDE_PATH . "login/finalize-registration.php?token={$usuario['magic_link']}"; ?>');
            });
        });
    });
</script>

<!-- Botao para reenviar codigo -->
<script>
    $(document).ready(function(){
        $("#btnResendCode").on("click", function(e) {
            $("#resendCodeLoader").removeClass("d-none");
            $("#resendCodeText").text("Reenviando...");
            $(this).addClass("disabled");
        });
    });
</script>