<?php
    $read = verificaPermissao($_SESSION['user_id'], 'webhook', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'webhook', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';

    $delete = verificaPermissao($_SESSION['user_id'], 'webhook', 'delete', $conn);
    $disabledDelete = !$delete ? 'disabled' : '';
?>

<?php
    require '../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable('../');
    $dotenv->load();

    // Acessa as variáveis de ambiente
    $config['asaas_api_url'] = $_ENV['ASAAS_API_URL'];
    $config['asaas_api_key'] = $_ENV['ASAAS_API_KEY'];
    $config['groupname'] = $_ENV['GROUPNAME'];

    function getWebhookDataFromAsaas($webhook_id, $config) {
        $url = $config['asaas_api_url']."webhooks/$webhook_id";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: ".$config['asaas_api_key']
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // Tabela que sera feita a consulta
    $tabela = "tb_webhook";

    // Query SQL para selecionar todos os usuários
    $sql = "SELECT * FROM $tabela LIMIT 1";

    // Prepara a query
    $stmt = $conn->prepare($sql);

    // Executa a query
    $stmt->execute();

    // Obtém todos os resultados como um array associativo
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $addButton = true;
    $institution_email = $email;

    // Exibe os resultados
    if ($resultados) {
        foreach ($resultados as $webhook) {
            $webhook_id = $webhook['webhook_id'];
            $enabled = $webhook['enabled'];
            $webhook_name = $webhook['name'];
            $email = $webhook['email'];
            $interrupted = $webhook['interrupted'];
            $send_type = $webhook['send_type'];

            $addButton = false;
        }
    }

    // Verifica os dados do webhook na API da Asaas e atualiza o banco de dados se necessário
    if (isset($webhook_id)) {
        $asaasWebhookData = getWebhookDataFromAsaas($webhook_id, $config);

        if ($asaasWebhookData) {
            $differences = false;

            if ($asaasWebhookData['enabled'] != $webhook['enabled']) {
                $enabled = $asaasWebhookData['enabled'];
                $differences = true;
            }
            if ($asaasWebhookData['name'] != $webhook['name']) {
                $webhook_name = $asaasWebhookData['name'];
                $differences = true;
            }
            if ($asaasWebhookData['email'] != $webhook['email']) {
                $email = $asaasWebhookData['email'];
                $differences = true;
            }
            if ($asaasWebhookData['interrupted'] != $webhook['interrupted']) {
                $interrupted = $asaasWebhookData['interrupted'];
                $differences = true;
            }
            if ($asaasWebhookData['sendType'] != $webhook['send_type']) {
                $send_type = $asaasWebhookData['sendType'];
                $differences = true;
            }

            if ($differences) {
                $sql = "UPDATE $tabela SET enabled = :enabled, name = :name, email = :email, interrupted = :interrupted, send_type = :send_type WHERE webhook_id = :webhook_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':enabled', $enabled);
                $stmt->bindParam(':name', $webhook_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':interrupted', $interrupted);
                $stmt->bindParam(':send_type', $send_type);
                $stmt->bindParam(':webhook_id', $webhook['webhook_id']);
                $stmt->execute();
            }
        } else {
            // Deleta o webhook do banco de dados se não existir na API da Asaas
            $sql = "DELETE FROM $tabela WHERE webhook_id = :webhook_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':webhook_id', $webhook_id);
            $stmt->execute();

            // Esvazia as variáveis
            $webhook_id = null;
            $enabled = null;
            $webhook_name = null;
            $email = $institution_email;
            $interrupted = null;
            $send_type = null;

            $addButton = true;
        }
    }
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Webhook
                </h1>
                <div class="text-secondary mt-1">Aqui você pode criar um evento webhook para seu projeto.</div>
            </div>
            <?php if (isset($webhook_id) && $update) { ?>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-info btn-3" onclick="location.reload();">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-2 icons-tabler-outline icon-tabler-refresh"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                    Sincronizar Webhook
                </button>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php if (!$update): ?>
<fieldset disabled>
<?php endif; ?>

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

                    <?php if (!$update): ?>
                    <div class="col-12">
                        <div class="alert alert-info">Você pode visualizar os detalhes desta página, mas não pode editá-los.</div>
                    </div>
                    <?php endif; ?>

                    <!-- Aviso da webhook -->
                    <?php if ($webhook && (!$webhook['enabled'] || $webhook['interrupted'])): ?>
                    <div class="col-12">
                        <div class="alert alert-danger w-100" role="alert">
                            <div class="d-flex">
                                <div>
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                                </div>
                                <div>
                                    <h3 class="alert-title">Atenção!</h3>
                                    <div class="text-secondary">Sua Webhook está inativa. <a href="<?php echo INCLUDE_PATH_ADMIN; ?>webhook" class="alert-link">Clique aqui</a> para corrigir.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>    

                    <div class="col-lg-12">
                        <div class="card">

                            <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/webhook.php" method="post">
                                <div class="card-header">
                                    <h2 class="card-title">Dados do Webhook</h2>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 row">
                                        <label for="enabled" class="col-3 col-form-label required">Este Webhook ficará ativo?</label>
                                        <div class="col">
                                            <select name="enabled" id="enabled" class="form-control" required>
                                                <option value="1" <?= (isset($enabled) && $enabled == 1) ? "selected" : ""; ?>>Sim</option>
                                                <option value="0" <?= (isset($enabled) && $enabled == 0) ? "selected" : ""; ?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="webhookName" class="col-3 col-form-label required">Nome do Webhook</label>
                                        <div class="col">
                                            <input name="webhook_name" id="webhookName"
                                                type="text" class="form-control" value="<?php echo (isset($webhook_name)) ? $webhook_name : "Kitanda"; ?>" required>
                                            <small class="form-hint">No máximo 50 caracteres.</small>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="email" class="col-3 col-form-label required">E-mail</label>
                                        <div class="col">
                                            <input name="email" id="email"
                                                type="text" class="form-control" value="<?php echo $email; ?>" required>
                                            <small class="form-hint">Você será notificado neste e-mail em caso de falha na sincronia.</small>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="interrupted" class="col-3 col-form-label required">Fila de sincronização ativada?</label>
                                        <div class="col">
                                            <select name="interrupted" id="interrupted" class="form-control" required>
                                                <option value="0" <?= (isset($interrupted) && $interrupted == 0) ? "selected" : ""; ?>>Sim</option>
                                                <option value="1" <?= (isset($interrupted) && $interrupted == 1) ? "selected" : ""; ?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="sendType" class="col-3 col-form-label required">Fila de sincronização ativada?</label>
                                        <div class="col">
                                            <select name="send_type" id="sendType" class="form-control" required>
                                                <option value="SEQUENTIALLY" <?= (isset($send_type) && $send_type == "SEQUENTIALLY") ? "selected" : ""; ?>>Sequencial</option>
                                                <option value="NON_SEQUENTIALLY"  <?= (isset($send_type) && $send_type == "NON_SEQUENTIALLY") ? "selected" : ""; ?>>Não sequencial</option>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if (isset($webhook_id)) { ?>
                                        <input type="hidden" name="webhook_id" value="<?= $webhook_id; ?>">
                                    <?php } ?>

                                </div>
                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                        
                                        <?php if ($addButton == true) { ?>
                                            <button type="submit" name="btnAddWebhook" class="btn btn-primary ms-auto">Salvar</button>
                                        <?php } else { ?>
                                            <div class="ms-auto">
                                                <button type="submit" name="btnUpdWebhook" class="btn btn-primary me-2">Editar</button>
                                                <button type="submit" name="btnDltWebhook" class="btn btn-danger <?= $disabledDelete; ?>" <?= $disabledDelete; ?>>Deletar Webhook</button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>
