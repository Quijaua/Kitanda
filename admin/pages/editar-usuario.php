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
    $permissao_usuario = $stmtPermissaoUsuario->fetch(PDO::FETCH_ASSOC);
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Editar Usuário
                </h2>
                <div class="text-secondary mt-1">Edite as informações do usuário e sua função.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <ol class="breadcrumb breadcrumb-muted" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>usuarios">Usuários</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar Usuário</li>
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
        <form id="editUser" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update-user.php?id=<?php echo $usuario['id']; ?>" method="post">
            <div class="row">

                <?php if ($only_own && $produto['criado_por'] !== $_SESSION['user_id']): ?>
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
                                <h4 class="card-title">Informações do Usuário</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Coluna Esquerda -->
                                    <div class="col-md-6">
                                        <!-- Nome -->
                                        <div class="mb-3">
                                            <label for="nome" class="form-label required">Nome</label>
                                            <input id="nome" name="nome" type="text" class="form-control" required value="<?= $usuario['nome']; ?>">
                                        </div>
                                        <!-- Telefone -->
                                        <div class="mb-3">
                                            <label for="telefone" class="form-label">Telefone</label>
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <!-- Ícone de Telefone -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-phone">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                                    </svg>
                                                </span>
                                                <input id="telefone" name="telefone" type="text" class="form-control" value="<?= $usuario['phone']; ?>">
                                            </div>
                                        </div>
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
                                                <?php if ($usuario['status'] != 1): ?>
                                                <a class="btn" href="<?= INCLUDE_PATH_ADMIN; ?>back-end/resend-code.php?id=<?= $usuario['id']; ?>" id="btnResendCode" title="Reenviar código para <?= htmlspecialchars($usuario['email']); ?>?" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <div id="resendCodeLoader" class="spinner-border spinner-border-sm text-secondary me-2 d-none" role="status"></div>
                                                    <span id="resendCodeText">Reenviar Código</span>
                                                </a>
                                                <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if ($usuario['status'] == 1 && empty($usuario['magic_link'])): ?>
                                            <small class="form-hint">O cadastro foi concluído. Por motivos de segurança, o e-mail não poderá ser alterado.</small>
                                            <?php else: ?>
                                            <small class="form-hint">Uma mensagem será enviada para este e-mail para concluir o registro do usuário.</small>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Função -->
                                        <div class="mb-3">
                                            <label for="funcao" class="form-label required">Função</label>
                                            <select id="funcao" name="funcao_id" class="form-select" required>
                                                <option value="">Selecione a função</option>
                                                <?php foreach($funcoes as $funcao): ?>
                                                <option value="<?php echo $funcao['id']; ?>" <?php echo ($funcao['id'] == $permissao_usuario['permissao_id']) ? 'selected' : ''; ?>>
                                                    <?= $funcao['nome']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Coluna Direita -->
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!-- Instagram -->
                                                <div class="mb-3">
                                                    <label for="instagram" class="form-label">Instagram</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Ícone do Instagram -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-instagram">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" />
                                                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M16.5 7.5v.01" />
                                                            </svg>
                                                        </span>
                                                        <input id="instagram" name="instagram" type="text" class="form-control" value="<?= $usuario['instagram']; ?>">
                                                    </div>
                                                </div>
                                                <!-- Site -->
                                                <div class="mb-3">
                                                    <label for="site" class="form-label">Site</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Ícone do Site -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-world">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                                <path d="M3.6 9h16.8" />
                                                                <path d="M3.6 15h16.8" />
                                                                <path d="M11.5 3a17 17 0 0 0 0 18" />
                                                                <path d="M12.5 3a17 17 0 0 1 0 18" />
                                                            </svg>
                                                        </span>
                                                        <input id="site" name="site" type="text" class="form-control" value="<?= $usuario['site']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Facebook -->
                                                <div class="mb-3">
                                                    <label for="facebook" class="form-label">Facebook</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Ícone do Facebook -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-facebook">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                                                            </svg>
                                                        </span>
                                                        <input id="facebook" name="facebook" type="text" class="form-control" value="<?= $usuario['facebook']; ?>">
                                                    </div>
                                                </div>
                                                <!-- Tiktok -->
                                                <div class="mb-3">
                                                    <label for="tiktok" class="form-label">Tiktok</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Ícone do Tiktok -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-tiktok">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M21 7.917v4.034a9.948 9.948 0 0 1 -5 -1.951v4.5a6.5 6.5 0 1 1 -8 -6.326v4.326a2.5 2.5 0 1 0 4 2v-11.5h4.083a6.005 6.005 0 0 0 4.917 4.917z" />
                                                            </svg>
                                                        </span>
                                                        <input id="tiktok" name="tiktok" type="text" class="form-control" value="<?= $usuario['tiktok']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Descrição -->
                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição</label>
                                            <textarea id="descricao" name="descricao" class="form-control" rows="3"><?= $usuario['descricao']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                    <button type="submit" name="btnEditUser" class="btn btn-primary ms-auto">Salvar</button>
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

<script>
    $(document).ready(function(){
        $("#btnResendCode").on("click", function(e) {
            $("#resendCodeLoader").removeClass("d-none");
            $("#resendCodeText").text("Reenviando...");
            $(this).addClass("disabled");
        });
    });
</script>