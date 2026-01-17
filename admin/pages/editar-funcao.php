<?php
    $read = verificaPermissao($_SESSION['user_id'], 'funcoes', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $only_own = verificaPermissao($_SESSION['user_id'], 'funcoes', 'only_own', $conn);
    $disabledOnlyOwn = !$only_own ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'funcoes', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';
?>

<?php
    // Valida o ID recebido por GET
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['error_msg'] = "ID da função inválido.";
        header('Location: ' . INCLUDE_PATH_ADMIN . 'funcoes');
    }
    $funcao_id = intval($_GET['id']);

    // Recupera os dados da função a ser editada
    $stmt = $conn->prepare("SELECT * FROM tb_funcoes WHERE id = :id");
    $stmt->bindParam(':id', $funcao_id, PDO::PARAM_INT);
    $stmt->execute();
    $funcao = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$funcao) {
        die("Função não encontrada.");
    }

    // Recupera as permissões atuais da função
    $stmt = $conn->prepare("SELECT * FROM tb_permissao_funcao WHERE funcao_id = :id");
    $stmt->bindParam(':id', $funcao_id, PDO::PARAM_INT);
    $stmt->execute();
    $currentPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organiza as permissões por página (ex: [pagina_id] => [acao_id, ...])
    $permissionsByPage = [];
    foreach ($currentPermissions as $perm) {
        $pagina_id = $perm['pagina_id'];
        $acao_id = $perm['acao_id'];
        $permissionsByPage[$pagina_id][] = $acao_id;
    }

    // Recupera as páginas ativas
    $stmt = $conn->query("SELECT * FROM tb_paginas WHERE status = 1");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    #editTable.card-table tbody tr td, #editTable.card-table thead tr th {
        border: 1px solid rgba(4, 32, 69, 0.1) !important;
        vertical-align: top !important;
    }
    #editFunction div > .form-check:last-child {
        margin-bottom: 0 !important;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Editar Função</h2>
                <div class="text-secondary mt-1">Altere as informações da função e suas permissões.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <nav aria-label="Caminho de navegação">
                        <ol class="breadcrumb breadcrumb-muted">
                            <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>funcoes">Funções</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar Função</li>
                        </ol>
                    </nav>
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
        <form id="editFunction" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update-function.php?id=<?php echo $funcao_id; ?>" method="post">
            <div class="row justify-content-between">

                <?php if (!getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                </div>
                <?php exit; endif; ?>

                <?php if ($only_own && $funcao['criado_por'] !== $_SESSION['user_id']): ?>
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
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
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

                <div class="col-lg-8 row row-deck row-cards mt-0">
                    <div class="col-lg-12 mt-0">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informações principais</h4>
                            </div>
                            <div class="card-body">
                                <!-- Campo para o nome da função -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label required">Nome da Função</label>
                                    <input id="nome" name="nome" type="text" class="form-control" required value="<?php echo htmlspecialchars($funcao['nome']); ?>">
                                </div>
                                <!-- Tabela com as páginas e permissões -->
                                <div class="table-responsive">
                                    <table id="editTable" class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Página</th>
                                                <th>Permissões</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pages as $page): 
                                                // Busca as ações disponíveis para cada página
                                                $stmt = $conn->prepare("SELECT acao_id FROM tb_pagina_acoes WHERE pagina_id = ?");
                                                $stmt->execute([$page['id']]);
                                                $actionsPage = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                $actionIds = array_column($actionsPage, 'acao_id');

                                                $only_own = in_array(1, $actionIds);
                                                $read     = in_array(2, $actionIds);
                                                $create   = in_array(3, $actionIds);
                                                $update   = in_array(4, $actionIds);
                                                $delete   = in_array(5, $actionIds);
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($page['nome']); ?></td>
                                                    <td class="text-secondary">
                                                        <div>
                                                            <?php if ($only_own): ?>
                                                            <!-- Visualizar (criados somente por ele) -->
                                                            <label class="form-check">
                                                                <input class="form-check-input only-own" type="checkbox" 
                                                                    name="permissions[<?php echo $page['id']; ?>][only_own]" value="1"
                                                                    data-page-id="<?= $page['id']; ?>"
                                                                    <?php if(isset($permissionsByPage[$page['id']]) && in_array(1, $permissionsByPage[$page['id']])) echo 'checked'; ?>>
                                                                <span class="form-check-label">Visualizar (criados somente por ele)</span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($read): ?>
                                                            <!-- Visualizar (criados por todos) -->
                                                            <label class="form-check">
                                                                <input class="form-check-input read-all" type="checkbox" 
                                                                    name="permissions[<?php echo $page['id']; ?>][read]" value="1"
                                                                    data-page-id="<?= $page['id']; ?>"
                                                                    <?php if(isset($permissionsByPage[$page['id']]) && in_array(2, $permissionsByPage[$page['id']])) echo 'checked'; ?>>
                                                                <span class="form-check-label">Visualizar<?= ($only_own) ? " (criados por todos)" : ""; ?></span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($create): ?>
                                                            <!-- Criar -->
                                                            <label class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                    name="permissions[<?php echo $page['id']; ?>][create]" value="1"
                                                                    <?php if(isset($permissionsByPage[$page['id']]) && in_array(3, $permissionsByPage[$page['id']])) echo 'checked'; ?>>
                                                                <span class="form-check-label">Criar</span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($update): ?>
                                                            <!-- Editar -->
                                                            <label class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                    name="permissions[<?php echo $page['id']; ?>][update]" value="1"
                                                                    <?php if(isset($permissionsByPage[$page['id']]) && in_array(4, $permissionsByPage[$page['id']])) echo 'checked'; ?>>
                                                                <span class="form-check-label">Editar</span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($delete): ?>
                                                            <!-- Deletar -->
                                                            <label class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                    name="permissions[<?php echo $page['id']; ?>][delete]" value="1"
                                                                    <?php if(isset($permissionsByPage[$page['id']]) && in_array(5, $permissionsByPage[$page['id']])) echo 'checked'; ?>>
                                                                <span class="form-check-label">Deletar</span>
                                                            </label>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                    <button type="submit" name="btnEditFunction" class="btn btn-primary ms-auto">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card com os usuários que utilizam a função -->
                <div class="col-lg-4">
                    <div class="card">
                        <?php
                            // Recupera os usuários que estão usando esta função
                            $stmtUsuarios = $conn->prepare("
                                SELECT c.id, c.nome 
                                FROM tb_clientes c
                                INNER JOIN tb_permissao_usuario pu ON c.id = pu.usuario_id
                                WHERE pu.permissao_id = :funcao_id
                                ORDER BY c.nome ASC
                            ");
                            $stmtUsuarios->bindParam(':funcao_id', $funcao_id, PDO::PARAM_INT);
                            $stmtUsuarios->execute();
                            $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <div class="card-header">
                            <h4 class="card-title">Usuários que estão usando esta função</h4>
                        </div>

                        <div class="card-table table-responsive">
                            <table id="usuarios" class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th>Nome do Usuário</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($usuarios): ?>
                                        <?php foreach($usuarios as $usuario): ?>
                                            <tr>
                                                <td data-label="Nome">
                                                    <a href="<?php echo INCLUDE_PATH_ADMIN . "editar-usuario?id={$usuario['id']}"; ?>" class="text-reset" target="_blank">
                                                        <?php echo htmlspecialchars($usuario['nome']); ?>
                                                    </a>
                                                </td>
                                                <td data-label="Ações" class="text-end">
                                                    <a href="<?php echo INCLUDE_PATH_ADMIN . "editar-usuario?id={$usuario['id']}"; ?>" class="btn btn-6 btn-external-link btn-icon" target="_blank">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td>
                                                Nenhum usuário usa esta função
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
    // Função para alternar o estado dos checkboxes com base no status atual
    function toggleCheckboxes(pageId) {
        var onlyOwn = $('input.only-own[data-page-id="'+pageId+'"]');
        var readAll = $('input.read-all[data-page-id="'+pageId+'"]');
        if (onlyOwn.is(':checked')) {
            readAll.prop('disabled', true);
        } else if (readAll.is(':checked')) {
            onlyOwn.prop('disabled', true);
        } else {
            onlyOwn.prop('disabled', false);
            readAll.prop('disabled', false);
        }
    }

    // Verifica o estado de cada grupo ao carregar a página
    $('input.only-own, input.read-all').each(function(){
        var pageId = $(this).data('page-id');
        toggleCheckboxes(pageId);
    });

    // Atualiza o estado quando algum checkbox é alterado
    $('input.only-own, input.read-all').on('change', function(){
        var pageId = $(this).data('page-id');
        toggleCheckboxes(pageId);
    });
});
</script>