<?php
    $create = verificaPermissao($_SESSION['user_id'], 'funcoes', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';
?>

<?php
    // Consulta as páginas ativas na tabela tb_paginas
    $stmt = $conn->query("SELECT * FROM tb_paginas WHERE status = 1");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Cadastrar Função
                </h1>
                <div class="text-secondary mt-1">Aqui você pode cadastrar novas funções e suas permissões.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <nav aria-label="Caminho de navegação">
                        <ol class="breadcrumb breadcrumb-muted">
                            <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>funcoes">Funções</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cadastrar Função</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-table tbody tr td, .card-table thead tr th {
        border: 1px solid rgba(4, 32, 69, 0.1) !important;
        vertical-align: top !important;
    }

    #registerFunction div > .form-check:last-child {
        margin-bottom: 0 !important;
    }
</style>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <form id="registerFunction" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/register-function.php" method="post">
            <div class="row">

                <?php if (!getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                </div>
                <?php exit; endif; ?>

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
                                <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
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
                                <h2 class="card-title">Informações principais</h2>
                            </div>
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="nome" class="form-label required">Nome da Função</label>
                                    <input id="nome" name="nome" type="text" class="form-control" required>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Página</th>
                                                <th>Permissões</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pages as $page): 
                                                // Buscar as ações associadas à página
                                                $stmt = $conn->prepare("SELECT acao_id FROM tb_pagina_acoes WHERE pagina_id = ?");
                                                $stmt->execute([$page['id']]);
                                                $actionsPage = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                // // Exibe para debug
                                                // echo "Página {$page['id']}";
                                                // echo "<pre>";
                                                // print_r($actionsPage);
                                                // echo "</pre>";

                                                // Cria um array com os IDs das ações
                                                $actionIds = array_column($actionsPage, 'acao_id');

                                                // Verifica se cada ação está presente
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
                                                            <!-- Visualizar (somente os criados pelo usuário) -->
                                                            <label class="form-check">
                                                                <input class="form-check-input only-own" type="checkbox"
                                                                    name="permissions[<?= $page['id']; ?>][only_own]" value="1"
                                                                    data-page-id="<?= $page['id']; ?>">
                                                                <span class="form-check-label">Visualizar (criados somente por ele)</span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($read): ?>
                                                            <!-- Visualizar (todos) -->
                                                            <label class="form-check">
                                                                <input class="form-check-input read-all" type="checkbox"
                                                                    name="permissions[<?= $page['id']; ?>][read]" value="1"
                                                                    data-page-id="<?= $page['id']; ?>">
                                                                <span class="form-check-label">Visualizar<?= ($only_own) ? " (criados por todos)" : ""; ?></span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($create): ?>
                                                            <!-- Criar -->
                                                            <label class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="permissions[<?= $page['id']; ?>][create]" value="1">
                                                                <span class="form-check-label">Criar</span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($update): ?>
                                                            <!-- Editar -->
                                                            <label class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="permissions[<?= $page['id']; ?>][update]" value="1">
                                                                <span class="form-check-label">Editar</span>
                                                            </label>
                                                            <?php endif; ?>
                                                            <?php if ($delete): ?>
                                                            <!-- Deletar -->
                                                            <label class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="permissions[<?= $page['id']; ?>][delete]" value="1">
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
                                    <button type="submit" name="btnAddFunction" class="btn btn-primary ms-auto">Salvar</button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Ao alterar um dos checkboxes "only-own" ou "read-all", desabilita o outro para o mesmo page-id
        $('.only-own, .read-all').on('change', function(){
            var pageId = $(this).data('page-id');
            if ($(this).is(':checked')) {
                if ($(this).hasClass('only-own')) {
                    $('input.read-all[data-page-id="'+pageId+'"]').prop('disabled', true);
                } else if ($(this).hasClass('read-all')) {
                    $('input.only-own[data-page-id="'+pageId+'"]').prop('disabled', true);
                }
            } else {
                // Se desmarcado, habilita ambos
                $('input.only-own[data-page-id="'+pageId+'"], input.read-all[data-page-id="'+pageId+'"]').prop('disabled', false);
            }
        });
    });
</script>