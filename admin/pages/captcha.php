<?php
    $read = verificaPermissao($_SESSION['user_id'], 'captcha', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $create = verificaPermissao($_SESSION['user_id'], 'captcha', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'captcha', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';

    $delete = verificaPermissao($_SESSION['user_id'], 'captcha', 'delete', $conn);
    $disabledDelete = !$delete ? 'disabled' : '';
?>

<style>
.form-color {
    outline: none;
    background: none;
    width: 38px;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    margin-right: 10px;
}
  #colorPickerRGB {
    outline: none;
    background: none;
    width: 38px;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    margin-right: 10px;
  }

  #rgbInputs {
    display: flex;
    align-items: center;
  }

  .rgbInput {
    width: 70px;
    margin-right: 10px;
    text-align: center;
  }

  #colorPreview {
    width: 50px;
    height: 50px;
    margin-top: 10px;
    border: 1px solid #ccc;
  }

  textarea {
      height: 200px !important;
  }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Configuração de CAPTCHA
                </h2>
                <div class="text-secondary mt-1">Configure os CAPTCHA's que você quer usar em sua aplicação.</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <?php include_once('./template-parts/general-sidebar.php'); ?>

            <div class="col">
                <div class="row row-cards">

                    <?php if (!getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
                    <div class="col-lg-12">
                        <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                    </div>
                    <?php exit; endif; ?>

                    <?php if (!$read): ?>
                    <div class="col-12">
                        <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                    </div>
                    <?php exit; endif; ?>

                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">Configurar Captcha</h4>
                            </div>

                            <div class="card-body border-bottom py-3">
                                <!-- hCaptcha Section -->
                                <div class="mb-4 row g-5 align-items-center">
                                    <div class="col-md-10">
                                        <h3 class="card-title">hCaptcha</h3>
                                        <p class="card-subtitle">É uma plataforma de CAPTCHA que fornece uma camada de segurança adicional, protegendo sites contra bots e ataques automatizados, garantindo que apenas usuários humanos possam interagir com seu conteúdo. Ao implementar o hCaptcha, você pode melhorar a segurança do seu site de forma simples e eficaz.</p>
                                    </div>
                                    <div class="col-md-2 ms-auto d-print-none">
                                        <button class="mr-2 btn <?php echo ($hcaptcha_public) ? "btn-secondary" : "btn-primary"; ?> btn-3 w-100 <?php echo ($hcaptcha_public) ? $disabledUpdate : $disabledCreate; ?>" data-bs-toggle="modal" data-bs-target="#modalHcaptcha" <?php echo ($hcaptcha_public) ? $disabledUpdate : $disabledCreate; ?>><?php echo ($hcaptcha_public) ? "Editar Chaves" : "Configurar"; ?></button>
                                        <a href="https://www.hcaptcha.com" target="_blank" class="mr-2 btn btn-link btn-3 w-100">Saiba Mais</a>
                                    </div>
                                </div>

                                <!-- Cloudflare Turnstile Section -->
                                <div class="mb-0 row g-5 align-items-center">
                                    <div class="col-md-10">
                                        <h3 class="card-title">Cloudflare Turnstile</h3>
                                        <p class="card-subtitle">É uma alternativa inovadora e sem CAPTCHA oferecida pela Cloudflare. Em vez de apresentar desafios tradicionais, Turnstile permite que você proteja seu site contra bots e fraudes de forma mais eficiente, sem a necessidade de interação do usuário. Ele é ideal para uma experiência de navegação mais fluida e menos intrusiva.</p>
                                    </div>
                                    <div class="col-md-2 ms-auto d-print-none">
                                        <button class="mr-2 btn <?php echo ($turnstile_public) ? "btn-secondary" : "btn-primary"; ?> btn-3 w-100 <?php echo ($turnstile_public) ? $disabledUpdate : $disabledCreate; ?>" data-bs-toggle="modal" data-bs-target="#modalTurnstile" <?php echo ($turnstile_public) ? $disabledUpdate : $disabledCreate; ?>><?php echo ($turnstile_public) ? "Editar Chaves" : "Configurar"; ?></button>
                                        <a href="https://www.cloudflare.com/pt-br/application-services/products/turnstile/" target="_blank" class="mr-2 btn btn-link btn-3 w-100">Saiba Mais</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php
    // Função para buscar páginas associadas a um tipo de CAPTCHA
    function getCaptchaPages($conn, $captcha_type) {
        $query = "SELECT page_name FROM tb_page_captchas WHERE captcha_type = :captcha_type";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':captcha_type', $captcha_type);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Inicializar variáveis como arrays
    $hcaptcha = [
        'pages' => [],
        'config' => '',
        'button' => (!empty($hcaptcha_public)) ? true : false
    ];
    $turnstile = [
        'pages' => [],
        'config' => '',
        'button' => (!empty($turnstile_public)) ? true : false
    ];

    // Consultar páginas associadas a cada tipo de CAPTCHA
    $hcaptcha['pages'] = getCaptchaPages($conn, 'hcaptcha');
    $turnstile['pages'] = getCaptchaPages($conn, 'turnstile');

    // Todas as páginas disponíveis para CAPTCHA
    $all_pages = ['doacao', 'login', 'enviar_email', 'resetar_senha'];

    // Determinar configuração do hCaptcha
    if (empty($hcaptcha['pages'])) {
        $hcaptcha['config'] = 'none';
    } elseif (count($hcaptcha['pages']) == count($all_pages)) {
        $hcaptcha['config'] = 'all';
    } else {
        $hcaptcha['config'] = 'select';
    }

    // Determinar configuração do Turnstile
    if (empty($turnstile['pages'])) {
        $turnstile['config'] = 'none';
    } elseif (count($turnstile['pages']) == count($all_pages)) {
        $turnstile['config'] = 'all';
    } else {
        $turnstile['config'] = 'select';
    }
?>

<!-- Modal para Configuração do hCaptcha -->
<div class="modal modal-blur fade" id="modalHcaptcha" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php INCLUDE_PATH_ADMIN; ?>back-end/captcha.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Configurar hCaptcha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <?php if (!$update && $hcaptcha['button']): ?>
                <fieldset disabled>
                <?php endif; ?>

                <div class="modal-body">
                    <?php if (!$create && !$hcaptcha['button']): ?>
                    <div class="alert alert-danger">Você não tem permissão para configurar um CAPTCHA.</div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="hcaptcha_site" class="form-label">Chave de Site</label>
                        <input id="hcaptcha_site" name="hcaptcha_site" type="text" class="form-control" required value="<?php echo $hcaptcha_public; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="hcaptcha_secret" class="form-label">Chave Secreta</label>
                        <div class="input-group input-group-flat">
                            <input id="hcaptcha_secret" name="hcaptcha_secret" type="password" class="form-control" required autocomplete="off" value="<?php echo $hcaptcha_secret; ?>">
                            <span class="input-group-text">
                                <a href="#" id="toggleSecretHcaptcha" class="input-group-link">Mostrar</a>
                            </span>
                        </div>
                    </div>

                    <!-- Opções de Configuração -->
                    <div class="mt-3">
                        <label for="hcaptcha_config" class="form-label">Configuração do CAPTCHA</label>
                        <select class="form-select" id="hcaptcha_config" name="hcaptcha_config" required onchange="togglePageSelection('hcaptcha')">
                            <option value="none" <?= (!isset($hcaptcha['config']) || (isset($hcaptcha['config']) && $hcaptcha['config'] == 'none')) ? 'selected' : ''; ?>>Nenhuma página, apenas configurar hCaptcha</option>
                            <option value="all" <?= (isset($hcaptcha['config']) && $hcaptcha['config'] == 'all') ? 'selected' : ''; ?>>Para todas as páginas</option>
                            <option value="select" <?= (isset($hcaptcha['config']) && $hcaptcha['config'] == 'select') ? 'selected' : ''; ?>>Para uma página específica</option>
                        </select>
                    </div>

                    <!-- Opções de Páginas Específicas -->
                    <div id="specificPagesHcaptcha" class="options mt-3"<?= (!isset($hcaptcha['config']) || $hcaptcha['config'] !== 'select') ? ' style="display: none;"' : ''; ?>>
                        <div class="form-label">Selecione as páginas</div>
                        <label class="form-check">
                            <input id="doacaoHcaptcha" name="hcaptcha_pages[]" class="form-check-input" type="checkbox" value="doacao" <?= (isset($hcaptcha['pages']) && in_array('doacao', $hcaptcha['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página de Doação</span>
                        </label>
                        <label class="form-check">
                            <input id="loginHcaptcha" name="hcaptcha_pages[]" class="form-check-input" type="checkbox" value="login" <?= (isset($hcaptcha['pages']) && in_array('doacao', $hcaptcha['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página de Login</span>
                        </label>
                        <label class="form-check">
                            <input id="enviar_emailHcaptcha" name="hcaptcha_pages[]" class="form-check-input" type="checkbox" value="enviar_email" <?= (isset($hcaptcha['pages']) && in_array('enviar_email', $hcaptcha['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página Enviar Email Resetar Senha</span>
                        </label>
                        <label class="form-check mb-0">
                            <input id="resetar_senhaHcaptcha" name="hcaptcha_pages[]" class="form-check-input" type="checkbox" value="recuperar_senha" <?= (isset($hcaptcha['pages']) && in_array('recuperar_senha', $hcaptcha['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página Resetar Senha</span>
                        </label>
                    </div>
                </div>

                <?php if (!$update && $hcaptcha['button']): ?>
                </fieldset>
                <?php endif; ?>

                <div class="modal-footer">
                    <?php if ($hcaptcha['button']): ?>
                        <button type="button" class="btn me-auto btn-ghost-danger" id="removehCaptchaBtn" <?= $disabledDelete; ?>>Remover hCaptcha</button>
                    <?php else: ?>
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Fechar</button>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary" name="saveHcaptcha" <?= ($hcaptcha['button']) ? $disabledUpdate : $disabledCreate; ?>>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Configuração do Turnstile -->
<div class="modal fade" id="modalTurnstile" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php INCLUDE_PATH_ADMIN; ?>back-end/captcha.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Configurar Turnstile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <?php if (!$update && $turnstile['button']): ?>
                <fieldset disabled>
                <?php endif; ?>

                <div class="modal-body">
                    <?php if (!$create && !$turnstile['button']): ?>
                    <div class="alert alert-danger">Você não tem permissão para configurar um CAPTCHA.</div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="turnstile_site" class="form-label">Chave de Site</label>
                        <input id="turnstile_site" name="turnstile_site" type="text" class="form-control" required value="<?php echo $turnstile_public; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="turnstile_secret" class="form-label">Chave Secreta</label>
                        <div class="input-group input-group-flat">
                            <input id="turnstile_secret" name="turnstile_secret" type="password" class="form-control" required autocomplete="off" value="<?php echo $turnstile_secret; ?>">
                            <span class="input-group-text">
                                <a href="#" id="toggleSecretTurnstile" class="input-group-link">Mostrar</a>
                            </span>
                        </div>
                    </div>

                    <!-- Opções de Configuração -->
                    <div class="mb-3">
                        <label for="turnstile_config" class="form-label">Configuração do Turnstile</label>
                        <select class="form-select" id="turnstile_config" name="turnstile_config" required onchange="togglePageSelection('turnstile')">
                            <option value="none" <?= (!isset($turnstile['config']) || (isset($turnstile['config']) && $turnstile['config'] == 'none')) ? 'selected' : ''; ?>>Nenhuma página, apenas configurar Turnstile</option>
                            <option value="all" <?= (isset($turnstile['config']) && $turnstile['config'] == 'all') ? 'selected' : ''; ?>>Para todas as páginas</option>
                            <option value="select" <?= (isset($turnstile['config']) && $turnstile['config'] == 'select') ? 'selected' : ''; ?>>Para uma página específica</option>
                        </select>
                    </div>

                    <!-- Opções de Páginas Específicas -->
                    <div id="specificPagesTurnstile" class="options mt-3"<?= (!isset($turnstile['config']) || $turnstile['config'] !== 'select') ? ' style="display: none;"' : ''; ?>>
                        <div class="form-label">Selecione as páginas</div>
                        <label class="form-check">
                            <input id="doacaoTurnstile" name="turnstile_pages[]" class="form-check-input" type="checkbox" value="doacao" <?= (isset($turnstile['pages']) && in_array('doacao', $turnstile['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página de Doação</span>
                        </label>
                        <label class="form-check">
                            <input id="loginTurnstile" name="turnstile_pages[]" class="form-check-input" type="checkbox" value="login" <?= (isset($turnstile['pages']) && in_array('doacao', $turnstile['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página de Login</span>
                        </label>
                        <label class="form-check">
                            <input id="enviar_emailTurnstile" name="turnstile_pages[]" class="form-check-input" type="checkbox" value="enviar_email" <?= (isset($turnstile['pages']) && in_array('enviar_email', $turnstile['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página Enviar Email Resetar Senha</span>
                        </label>
                        <label class="form-check mb-0">
                            <input id="resetar_senhaTurnstile" name="turnstile_pages[]" class="form-check-input" type="checkbox" value="recuperar_senha" <?= (isset($turnstile['pages']) && in_array('recuperar_senha', $turnstile['pages'])) ? 'checked' : ''; ?>>
                            <span class="form-check-label">Página Resetar Senha</span>
                        </label>
                    </div>
                </div>

                <?php if (!$update && $turnstile['button']): ?>
                </fieldset>
                <?php endif; ?>

                <div class="modal-footer">
                    <?php if ($turnstile['button']): ?>
                        <button type="button" class="btn me-auto btn-ghost-danger" id="removeTurnstileBtn" <?= $disabledDelete; ?>>Remover Turnstile</button>
                    <?php else: ?>
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Fechar</button>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary" name="saveTurnstile" <?= ($turnstile['button']) ? $disabledUpdate : $disabledCreate; ?>>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#removeHcaptchaBtn').click(function(e) {
            e.preventDefault();

            if (confirm('Você tem certeza que deseja remover o hCaptcha e suas configurações associadas?')) {
                window.location.href = "<?php INCLUDE_PATH_ADMIN; ?>back-end/captcha.php?captcha=hcaptcha";
            }
        });

        $('#removeTurnstileBtn').click(function(e) {
            e.preventDefault();

            if (confirm('Você tem certeza que deseja remover o Turnstile e suas configurações associadas?')) {
                window.location.href = "<?php INCLUDE_PATH_ADMIN; ?>back-end/captcha.php?captcha=turnstile";
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        let pagesSelected = {
            hcaptcha: <?= json_encode($hcaptcha['pages']) ?>,
            turnstile: <?= json_encode($turnstile['pages']) ?>
        };

        function checkConflicts(currentCaptcha, selectedOption) {
            let otherCaptcha = currentCaptcha === 'hcaptcha' ? 'turnstile' : 'hcaptcha';

            if (selectedOption === 'all' && pagesSelected[otherCaptcha].length > 0) {
                if (!confirm("Existem páginas selecionadas para outro CAPTCHA. Se você continuar, todas as páginas serão atribuídas ao CAPTCHA atual. Deseja continuar?")) {
                    return false;
                }
            }
            return true;
        }

        function handleCheckboxSelection(currentCaptcha, page) {
            let otherCaptcha = currentCaptcha === 'hcaptcha' ? 'turnstile' : 'hcaptcha';

            if (pagesSelected[otherCaptcha].includes(page)) {
                if (!confirm("Essa página já está associada ao outro CAPTCHA. Se continuar, ela será atribuída ao CAPTCHA atual. Deseja continuar?")) {
                    return false;
                }
                pagesSelected[otherCaptcha] = pagesSelected[otherCaptcha].filter(p => p !== page);
            }

            if (!$(`#${page}${capitalizeFirstLetter(currentCaptcha)}`).is(':checked')) {
                pagesSelected[currentCaptcha] = pagesSelected[currentCaptcha].filter(p => p !== page);
            } else {
                pagesSelected[currentCaptcha].push(page);
            }
            return true;
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        $('#hcaptcha_config, #turnstile_config').change(function () {
            let currentCaptcha = $(this).attr('id').includes('hcaptcha') ? 'hcaptcha' : 'turnstile';
            let selectedOption = $(this).val();

            if (!checkConflicts(currentCaptcha, selectedOption)) {
                $(this).val(pagesSelected[currentCaptcha].length === 0 ? 'none' : 'select');
            }
        });

        $('input[type="checkbox"][name="hcaptcha_pages[]"], input[type="checkbox"][name="turnstile_pages[]"]').change(function () {
            let currentCaptcha = $(this).attr('name').includes('hcaptcha') ? 'hcaptcha' : 'turnstile';
            let page = $(this).val();

            if (!handleCheckboxSelection(currentCaptcha, page)) {
                $(this).prop('checked', false);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Função para mostrar/ocultar as opções de páginas específicas para o hCaptcha
        $('#hcaptcha_config').change(function() {
            const configValue = $(this).val();
            if (configValue === 'select') {
                $('#specificPagesHcaptcha').show();
                $('#specificPagesHcaptcha.options :checkbox[required]').attr('required', 'required');

                var requiredCheckboxesHcaptcha = $('#specificPagesHcaptcha.options :checkbox[required]');
                requiredCheckboxesHcaptcha.change(function(){
                    if(requiredCheckboxesHcaptcha.is(':checked')) {
                        requiredCheckboxesHcaptcha.removeAttr('required');
                    } else {
                        requiredCheckboxesHcaptcha.attr('required', 'required');
                    }
                });
            } else {
                $('#specificPagesHcaptcha').hide();
            }
        });

        // Função para mostrar/ocultar as opções de páginas específicas para o Turnstile
        $('#turnstile_config').change(function() {
            const configValue = $(this).val();
            if (configValue === 'select') {
                $('#specificPagesTurnstile').show();
                $('#specificPagesTurnstile.options :checkbox[required]').attr('required', 'required');

                var requiredCheckboxesTurnstile = $('#specificPagesTurnstile.options :checkbox[required]');
                requiredCheckboxesTurnstile.change(function(){
                    if(requiredCheckboxesTurnstile.is(':checked')) {
                        requiredCheckboxesTurnstile.removeAttr('required');
                    } else {
                        requiredCheckboxesTurnstile.attr('required', 'required');
                    }
                });
            } else {
                $('#specificPagesTurnstile').hide();
            }
        });
    });

    $(document).ready(function() {
        $("#toggleSecretHcaptcha").click(function() {
            // Seleciona o campo de senha
            var passwordField = $("#hcaptcha_secret");
            
            // Verifica o tipo atual do campo e alterna entre "password" e "text"
            var type = passwordField.attr("type") === "password" ? "text" : "password";
            passwordField.attr("type", type);

            // Alterar o texto do botão
            var buttonText = type === "password" ? "Mostrar" : "Ocultar";
            $("#toggleSecretHcaptcha").text(buttonText);
        });
    });

    $(document).ready(function() {
        $("#toggleSecretTurnstile").click(function() {
            // Seleciona o campo de senha
            var secretTurnstileField = $("#turnstile_secret");
            
            // Verifica o tipo atual do campo e alterna entre "password" e "text"
            var type = secretTurnstileField.attr("type") === "password" ? "text" : "password";
            secretTurnstileField.attr("type", type);

            // Alterar o texto do botão
            var buttonText = type === "password" ? "Mostrar" : "Ocultar";
            $("#toggleSecretTurnstile").text(buttonText);
        });
    });
</script>