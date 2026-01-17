<?php
    $create = verificaPermissao($_SESSION['user_id'], 'usuarios', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';
?>

<?php
    // Busca as funções disponíveis
    $stmtFuncoes = $conn->query("SELECT id, nome FROM tb_funcoes");
    $funcoes = $stmtFuncoes->fetchAll(PDO::FETCH_ASSOC);
?>

<link href="<?php echo INCLUDE_PATH; ?>dist/libs/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet"/>

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
                    <nav aria-label="Caminho de navegação">
                        <ol class="breadcrumb breadcrumb-muted">
                            <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>usuarios">Usuários</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cadastrar Usuário</li>
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
                                    <div class="col-md-12">
                                        <!-- Nome -->
                                        <div class="mb-3">
                                            <label for="nome" class="form-label required">Nome</label>
                                            <input id="nome" name="nome" type="text" class="form-control" required>
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
                                            <div class="input-group input-group-flat">
                                                <input id="senha" name="senha" type="password" class="form-control" required>
                                                <span class="input-group-text">
                                                    <a href="#" class="link-secondary" title="Mostrar senha" data-bs-toggle="tooltip" onclick="togglePassword('senha', this); return false;">
                                                        <i class="ti ti-eye icon icon-1"></i>
                                                    </a>
                                                </span>
                                            </div>
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

<!-- Exibir/Ocultar Senha -->
<script>
    function togglePassword(inputId, toggleLink) {
        var input = document.getElementById(inputId);
        var icon = toggleLink.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            toggleLink.title = "Ocultar senha";
            toggleLink.setAttribute("aria-label", "Ocultar senha");
            toggleLink.setAttribute("data-bs-original-title", "Ocultar senha");
            // Altera o ícone para "eye-off"
            if (icon) {
                icon.classList.remove("ti-eye");
                icon.classList.add("ti-eye-off");
            }
        } else {
            input.type = "password";
            toggleLink.title = "Mostrar senha";
            toggleLink.setAttribute("aria-label", "Mostrar senha");
            toggleLink.setAttribute("data-bs-original-title", "Mostrar senha");
            // Altera o ícone para "eye"
            if (icon) {
                icon.classList.remove("ti-eye-off");
                icon.classList.add("ti-eye");
            }
        }
    }
</script>