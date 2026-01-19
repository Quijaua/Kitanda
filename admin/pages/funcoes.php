<?php
    $read = verificaPermissao($_SESSION['user_id'], 'funcoes', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $only_own = verificaPermissao($_SESSION['user_id'], 'funcoes', 'only_own', $conn);
    $disabledOnlyOwn = !$only_own ? 'disabled' : '';

    $create = verificaPermissao($_SESSION['user_id'], 'funcoes', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'funcoes', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';

    $delete = verificaPermissao($_SESSION['user_id'], 'funcoes', 'delete', $conn);
    $disabledDelete = !$delete ? 'disabled' : '';
?>

<?php
    // Se o usuário possui somente a permissão only_own, filtramos os produtos criados por ele.
    if ($read) {
        // Consulta para buscar as funções e contar o número de usuários vinculados a cada função
        $stmt = $conn->prepare("
            SELECT f.*, COUNT(pu.usuario_id) AS total_usuarios
            FROM tb_funcoes f
            LEFT JOIN tb_permissao_usuario pu ON f.id = pu.permissao_id
            GROUP BY f.id
            ORDER BY f.id DESC
        ");
        $stmt->execute();
    } else if ($only_own) {
        // Consulta para buscar as funções e contar o número de usuários vinculados a cada função
        $stmt = $conn->prepare("
            SELECT f.*, COUNT(pu.usuario_id) AS total_usuarios
            FROM tb_funcoes f
            LEFT JOIN tb_permissao_usuario pu ON f.id = pu.permissao_id
            WHERE f.criado_por = ?
            GROUP BY f.id
            ORDER BY f.id DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }
    $funcoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Modal de Confirmação -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar modal"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <!-- Download SVG icon from http://tabler.io/icons/icon/alert-triangle -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg"><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                <h3>Confirmar Exclusão</h3>
                <div class="text-secondary">Tem certeza de que deseja excluir o produto <b>"<span id="produtoNome"></span>"</b>?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-3 w-100" data-bs-dismiss="modal"> Cancel </button>
                        </div>
                        <div class="col">
                            <button type="button" id="confirmDelete" class="btn btn-danger btn-4 w-100"> Deletar </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #funcoes_filter, #funcoes_length {
        display: none;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Funções
                </h1>
                <div class="text-secondary mt-1">Aqui estão as funções do sistema.</div>
            </div>
            <?php if (getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= ($create) ? INCLUDE_PATH_ADMIN."criar-funcao" : "#"; ?>" class="btn btn-info btn-3 <?= $disabledCreate; ?>" <?= $disabledCreate; ?>>
                    <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2"><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    Nova Função
                </a>
            </div>
            <?php endif; ?>
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

                    <?php if (!$only_own && !$read): ?>
                    <div class="col-12">
                        <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
                    </div>
                    <?php exit; endif; ?>

                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                <h2 class="card-title">Funções</h2>
                            </div>

                            <div class="card-body border-bottom py-3">
                                <div class="d-flex">
                                    <div class="text-secondary">
                                        Exibir
                                        <div class="mx-2 d-inline-block">
                                            <select class="form-control form-control-sm" id="entries-select" aria-label="Contagem de faturas">
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                        entradas
                                    </div>
                                    <div class="ms-auto text-secondary">
                                        Buscar:
                                        <div class="ms-2 d-inline-block">
                                            <input type="text" class="form-control form-control-sm" id="search-input" aria-label="Buscar fatura">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table id="funcoes" class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-100">Nome do Função</th>
                                        <th>Total de Usuários</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($funcoes as $funcao) : ?>
                                    <tr>
                                        <td class="more-info" data-label="Name">
                                            <?php echo $funcao["nome"]; ?>
                                        </td>
                                        <td><?php echo $funcao["total_usuarios"] ?? 0; ?></td>
                                        <td class="text-end">
                                            <span class="dropdown">
                                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Ações</button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <?php if ($update): ?>
                                                    <a class="dropdown-item" href="<?php echo INCLUDE_PATH_ADMIN . "editar-funcao?id={$funcao['id']}"; ?>">
                                                        <!-- Download SVG icon from http://tabler.io/icons/icon/edit -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler-edit"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path><path d="M16 5l3 3"></path></svg>
                                                        Editar
                                                    </a>
                                                    <?php elseif ($only_own || $read): ?>
                                                    <a class="dropdown-item" href="<?= INCLUDE_PATH_ADMIN . "editar-funcao?id={$funcao['id']}"; ?>">
                                                        <!-- Download SVG icon from http://tabler.io/icons/icon/edit -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler-edit"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path><path d="M16 5l3 3"></path></svg>
                                                        Detalhes
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if ($delete): ?>
                                                    <div class="dropdown-divider"></div>
                                                    <button type="button" class="dropdown-item text-danger btn-delete" data-id="<?php echo $funcao['id']; ?>" data-name="<?php echo $funcao['nome']; ?>">
                                                        <!-- Download SVG icon from http://tabler.io/icons/icon/edit -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 text-danger icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                        Deletar
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="card-footer d-flex align-items-center">
                                <p class="m-0 text-secondary">Exibindo <span id="start-entry">0</span> até <span id="end-entry">0</span> de <span id="total-entry">0</span> entradas</p>

                                <ul class="pagination m-0 ms-auto" id="pagination-custom">
                                    <li class="page-item disabled" id="prev-page">
                                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                <path d="M15 6l-6 6l6 6"></path>
                                            </svg>
                                            anterior
                                        </a>
                                    </li>
                                    <!-- A paginação será gerada dinamicamente aqui -->
                                    <li class="page-item disabled" id="current-page">
                                        <a class="page-link" href="#">1</a>
                                    </li>
                                    <li class="page-item" id="next-page">
                                        <a class="page-link" href="#">
                                            próximo
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                <path d="M9 6l6 6l-6 6"></path>
                                            </svg>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/responsive/3.0.0/css/responsive.dataTables.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/responsive/3.0.0/css/responsive.bootstrap4.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/2.0.1/css/dataTables.bootstrap4.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/bootstrap-table/dist/bootstrap-table.min.js"></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/js/jquery.dataTables.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/responsive/2.0.0/js/dataTables.responsive.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/responsive/3.0.0/js/responsive.bootstrap4.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/1.13.6/js/dataTables.bootstrap4.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/buttons/2.4.1/js/buttons.html5.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/buttons/2.4.1/js/buttons.print.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/ajax/libs/jszip/3.10.1/jszip.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>dist/libs/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>

<!-- Deletar Produto -->
<script>
    $(document).ready(function () {
        let elementIdToDelete = null;

        // Quando clicar no botão de exclusão
        $(document).on('click', '.btn-delete', function () {
            elementIdToDelete = $(this).data('id'); // Obtém o ID do elemento a ser excluído
            const elementNameToDelete = $(this).data('name'); // Obtém o nome do elemento a ser excluído
            $('#produtoNome').text(elementNameToDelete); // Define o nome no modal
            $('#deleteModal').modal('show'); // Mostra o modal
        });

        // Quando confirmar a exclusão
        $('#confirmDelete').on('click', function () {
            if (elementIdToDelete) {
                $.ajax({
                    url: `<?= INCLUDE_PATH_ADMIN; ?>back-end/delete-product.php?id=${elementIdToDelete}`,
                    type: 'GET',
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.error('Erro:', error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#funcoes").before(`
                            <div class="alert alert-danger" role="alert">
                                Ocorreu um erro, tente novamente mais tarde.
                            </div>
                        `);
                    }
                });
            }
        });
    });
</script>

<!-- Alterar Vitrine -->
<script>
    $(document).ready(function() {
        $('input[name="vitrine"]').on('change', function() {
            var checkbox = $(this);
            var produtoId = checkbox.data('id');
            var produtoNome = checkbox.data('nome');
            var status = checkbox.prop('checked') ? 1 : 0;

            $.ajax({
                url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/update-vitrine.php',
                type: 'POST',
                data: { id: produtoId, vitrine: status },
                success: function(response) {
                    if (response.success) {
                        showToast('Vitrine do produto "' + produtoNome + '" atualizada com sucesso', "success", "green");
                    } else {
                        showToast('Erro ao atualizar a vitrine do produto "' + produtoNome + '"', "danger", "red");
                    }
                },
                error: function() {
                    showToast("Erro na requisição!", "danger");
                }
            });
        });
    });

    // Função para exibir o toast
    function showToast(message, type, color) {
        var toast = `<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
            <div class="toast show align-items-center bg-${color}-lt border-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar notificação"></button>
                </div>
            </div>
        </div>`;

        $('body').append(toast);

        // Remover o toast após 3 segundos
        setTimeout(function() {
            $(".toast").remove();
        }, 3000);
    }
</script>

<!-- Listar Produtos -->
<script>
    $(document).ready(function() {
        var table = $('#funcoes').DataTable({
            "paging": false, // Desativa a paginação do DataTable
            "info": false, // Desativa a informação sobre o número de registros
            "responsive": true,
            "pageLength": 10, // Define o número inicial de entradas por página
            "buttons": [
                "csv" ,"excel", "pdf", "print"
            ],
            "language": {
                "emptyTable": "Nenhum registro encontrado", // Mensagem quando a tabela estiver vazia
                "zeroRecords": "Nenhum registro corresponde à pesquisa", // Caso não tenha resultados na busca
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Nenhum registro disponível",
                "infoFiltered": "(filtrado de _MAX_ registros totais)",
                "search": "Pesquisar:",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                }
            }
        });

        $('#exportCSV').click(function () {
            table.button(0).trigger();  // 0 corresponde ao primeiro botão CSV
        });

        $('#exportXLS').click(function () {
            table.button(1).trigger();  // 1 corresponde ao botão Excel
        });

        $('#exportPDF').click(function () {
            table.button(2).trigger();  // 2 corresponde ao botão PDF
        });

        $('#exportPrint').click(function () {
            table.button(3).trigger();  // 3 corresponde ao botão Print
        });

        // Atualiza o texto de exibição de registros
        function updatePaginationInfo() {
            var pageInfo = table.page.info();

            if (pageInfo.recordsTotal === 0) {
                $('#start-entry').text(0);
                $('#end-entry').text(0);
                $('#total-entry').text(0);
            } else {
                $('#start-entry').text(pageInfo.start + 1);
                $('#end-entry').text(pageInfo.end);
                $('#total-entry').text(pageInfo.recordsTotal);
            }

            $('#prev-page').toggleClass('disabled', pageInfo.page === 0);
            $('#next-page').toggleClass('disabled', pageInfo.page === pageInfo.pages - 1);
            $('#current-page a').text(pageInfo.page + 1);
        }

        // Inicializa a página de navegação
        updatePaginationInfo();

        // Ação ao clicar no botão de página anterior
        $('#prev-page').click(function() {
            if ($(this).hasClass('disabled')) return;
            table.page('previous').draw('page');
            updatePaginationInfo();
        });

        // Ação ao clicar no botão de próxima página
        $('#next-page').click(function() {
            if ($(this).hasClass('disabled')) return;
            table.page('next').draw('page');
            updatePaginationInfo();
        });

        // Lidar com a mudança de página (se fosse necessário incluir múltiplas páginas com números)
        $('#pagination-custom').on('click', '.page-link', function(e) {
            var page = $(this).text() - 1;
            table.page(page).draw('page');
            updatePaginationInfo();
        });

        // Lida com a pesquisa
        $('#search-input').keyup(function() {
            table.search($(this).val()).draw();
        });

        // Lidar com a mudança de entradas por página (select)
        $('#entries-select').change(function() {
            var entries = $(this).val(); // Pega o valor selecionado
            table.page.len(entries).draw(); // Atualiza o número de entradas por página
            updatePaginationInfo(); // Atualiza a informação de navegação
        });
    });
</script>

<style>
div.dataTables_wrapper div.dataTables_paginate ul.pagination {
    justify-content: revert;
}
</style>