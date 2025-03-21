<?php
    $create = verificaPermissao($_SESSION['user_id'], 'usuarios', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';
?>

<?php
    // Busca as funções disponíveis
    $stmtFuncoes = $conn->query("SELECT id, nome FROM tb_funcoes");
    $funcoes = $stmtFuncoes->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Cadastrar Usuário
                </h2>
                <div class="text-secondary mt-1">Cadastre novos usuários e selecione a função desejada.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <ol class="breadcrumb breadcrumb-muted" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>usuarios">Usuários</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cadastrar Usuário</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <form id="createUser" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/create-user.php" method="post">
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
                                    <div class="col-md-6">
                                        <!-- Nome -->
                                        <div class="mb-3">
                                            <label for="nome" class="form-label required">Nome</label>
                                            <input id="nome" name="nome" type="text" class="form-control" required>
                                        </div>
                                        <!-- Telefone -->
                                        <div class="mb-3">
                                            <label for="telefone" class="form-label">Telefone</label>
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/phone -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-phone"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                                                </span>
                                                <input id="telefone" name="telefone" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <!-- Função -->
                                        <div class="mb-3">
                                            <label for="funcao" class="form-label required">Função</label>
                                            <select id="funcao" name="funcao_id" class="form-select" required>
                                                <option value="">Selecione a função</option>
                                                <?php foreach($funcoes as $funcao): ?>
                                                <option value="<?php echo $funcao['id']; ?>"><?php echo htmlspecialchars($funcao['nome']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <!-- E-mail -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label required">E-mail</label>
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/mail -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-mail"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                                                </span>
                                                <input id="email" name="email" type="email" class="form-control" required>
                                            </div>
                                            <small class="form-hint">Uma mensagem será enviada para este e-mail para concluir o registro do usuário.</small>
                                        </div>
                                        <!-- Opções para definição de senha -->
                                        <div class="mb-3">
                                            <label class="form-label">Forma de cadastro de senha</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="passwordMethod" id="setPassword" value="set" checked>
                                                <label class="form-check-label" for="setPassword">
                                                    Cadastrar senha agora
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="passwordMethod" id="emailPassword" value="email">
                                                <label class="form-check-label" for="emailPassword">
                                                    Enviar email para cadastro de senha
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Campo de senha (visível apenas se "Cadastrar senha agora" estiver selecionado) -->
                                        <div class="mb-3" id="passwordFields">
                                            <label for="senha" class="form-label required">Senha</label>
                                            <input id="senha" name="senha" type="password" class="form-control" required>
                                            <label class="form-check form-switch form-switch-2 mt-2">
                                                <input id="email_senha" name="email_senha" class="form-check-input" type="checkbox" value="1" checked>
                                                <span class="form-check-label">Enviar senha para o e-mail do novo usuário?</span>
                                            </label>
                                        </div>
                                        <!-- Notificação de Boas-Vindas por E-mail -->
                                        <div>
                                            <div class="form-label">Notificações por E-mail</div>
                                            <label class="form-check form-switch form-switch-2">
                                                <input id="email_boas_vindas" name="email_boas_vindas" class="form-check-input" type="checkbox" value="1" checked>
                                                <span class="form-check-label">Enviar mensagem de boas-vindas para o novo usuário?</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!-- Instagram -->
                                                <div class="mb-3">
                                                    <label for="instagram" class="form-label">Instagram</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Download SVG icon from http://tabler.io/icons/icon/brand-instagram -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-instagram"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M16.5 7.5v.01" /></svg>
                                                        </span>
                                                        <input id="instagram" name="instagram" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <!-- Site -->
                                                <div class="mb-3">
                                                    <label for="site" class="form-label">Site</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Download SVG icon from http://tabler.io/icons/icon/world -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-world"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M3.6 9h16.8" /><path d="M3.6 15h16.8" /><path d="M11.5 3a17 17 0 0 0 0 18" /><path d="M12.5 3a17 17 0 0 1 0 18" /></svg>
                                                        </span>
                                                        <input id="site" name="site" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Facebook -->
                                                <div class="mb-3">
                                                    <label for="facebook" class="form-label">Facebook</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Download SVG icon from http://tabler.io/icons/icon/brand-facebook -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-facebook"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" /></svg>
                                                        </span>
                                                        <input id="facebook" name="facebook" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <!-- Tiktok -->
                                                <div class="mb-3">
                                                    <label for="tiktok" class="form-label">Tiktok</label>
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <!-- Download SVG icon from http://tabler.io/icons/icon/brand-tiktok -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-brand-tiktok"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 7.917v4.034a9.948 9.948 0 0 1 -5 -1.951v4.5a6.5 6.5 0 1 1 -8 -6.326v4.326a2.5 2.5 0 1 0 4 2v-11.5h4.083a6.005 6.005 0 0 0 4.917 4.917z" /></svg>
                                                        </span>
                                                        <input id="tiktok" name="tiktok" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Descrição -->
                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição</label>
                                            <textarea id="descricao" name="descricao" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                    <button type="submit" name="btnAddUser" class="btn btn-primary ms-auto">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Selecionar metodo de criacao de senha -->
<script>
    $(document).ready(function(){
        // Verifica a seleção ao carregar a página
        togglePasswordFields();

        // Monitora mudanças nos radio buttons
        $('input[name="passwordMethod"]').on('change', function(){
            togglePasswordFields();
        });

        function togglePasswordFields() {
            if ($('#setPassword').is(':checked')) {
                // Se "Cadastrar senha agora" estiver selecionado, mostra o campo e marca como obrigatório
                $('#passwordFields').show();
                $('#senha').prop('required', true);
                $('#email_senha').prop('disabled', false);
            } else {
                // Se "Enviar email para cadastro de senha" estiver selecionado, oculta o campo e retira a obrigatoriedade
                $('#passwordFields').hide();
                $('#senha').prop('required', false);
                $('#email_senha').prop('disabled', true);
            }
        }
    });
</script>

<!-- Ativar e desabilitar checkbox enviar email boas-vindas -->
<script>
    $(document).ready(function(){
        function updateEmailBoasVindas() {
            // Se o método de senha selecionado for 'email', habilita o checkbox
            if ($('input[name="passwordMethod"]:checked').val() === 'email') {
                $('#email_boas_vindas').prop('disabled', false);
            }
            // Caso contrário, se o checkbox "email_senha" estiver ativo, força o checkbox de boas-vindas a ficar checado e desabilitado
            else if ($('#email_senha').is(':checked')) {
                $('#email_boas_vindas').prop('checked', true).prop('disabled', true);
            }
            // Em outras situações, permite alteração no checkbox
            else {
                $('#email_boas_vindas').prop('disabled', false);
            }
        }

        // Atualiza no carregamento da página
        updateEmailBoasVindas();

        // Atualiza sempre que o checkbox "email_senha" for alterado
        $('#email_senha').on('change', function(){
            updateEmailBoasVindas();
        });
        
        // Atualiza sempre que o radio button "passwordMethod" for alterado
        $('input[name="passwordMethod"]').on('change', function(){
            updateEmailBoasVindas();
        });
    });
</script>