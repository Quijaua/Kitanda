<?php
    $read = verificaPermissao($_SESSION['user_id'], 'paginas', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $only_own = verificaPermissao($_SESSION['user_id'], 'paginas', 'only_own', $conn);
    $disabledOnlyOwn = !$only_own ? 'disabled' : '';

    $create = verificaPermissao($_SESSION['user_id'], 'paginas', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'paginas', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';

    $delete = verificaPermissao($_SESSION['user_id'], 'paginas', 'delete', $conn);
    $disabledDelete = !$delete ? 'disabled' : '';
?>

<?php
    function formatarTags(?string $jsonTags, int $limit = 150, string $sep = ', '): string
    {
        // Garante que, se vier null ou JSON inválido, teremos um array vazio
        $dados   = json_decode($jsonTags ?? '[]', true);
        $valores = array_column(is_array($dados) ? $dados : [], 'value');
        $todas   = implode($sep, $valores);

        return mb_strlen($todas) > $limit
            ? mb_substr($todas, 0, $limit) . '...'
            : $todas;
    }
?>

<?php
    // Se o usuário possui somente a permissão only_own, filtramos as paginas criados por ele.
    if ($read) {
        // Caso contrário, exibe todos as paginas
        $stmt = $conn->prepare("
            SELECT *
            FROM tb_paginas_conteudo
            GROUP BY id
            ORDER BY id DESC
        ");
        $stmt->execute();
    } else if ($only_own) {
        $stmt = $conn->prepare("
            SELECT *
            FROM tb_paginas_conteudo
            WHERE criado_por = ?
            GROUP BY id
            ORDER BY id DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }

    $paginas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <div class="text-secondary">Tem certeza de que deseja excluir a página <b>"<span id="paginaNome"></span>"</b>?</div>
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
    #paginas_filter, #paginas_length {
        display: none;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        justify-content: revert;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Páginas
                </h1>
                <div class="text-secondary mt-1">Aqui estão as páginas do sistema.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= ($create) ? INCLUDE_PATH_ADMIN."criar-pagina" : "#"; ?>" class="btn btn-info btn-3 <?= $disabledCreate; ?>" <?= $disabledCreate; ?>>
                    <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2"><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    Criar Nova Página
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <?php if (!$only_own && !$read): ?>
            <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
            <?php exit; endif; ?>

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="card-title">Páginas</h2>
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

                    <table id="paginas" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Título da Página</th>
                                <th>Data de Criação</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($paginas as $pagina) : ?>
                            <tr>
                                <td class="more-info" data-label="Título"><?php echo $pagina["titulo"]; ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($pagina["criado_em"])); ?></td>
                                <td class="text-end">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <!-- Botão Visualizar -->
                                        <a href="<?php echo INCLUDE_PATH . "pagina/{$pagina['slug']}"; ?>" target="_blank" class="btn btn-6 btn-outline-primary d-flex align-items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-external-link">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                <path d="M11 13l9 -9" />
                                                <path d="M15 4h5v5" />
                                            </svg>
                                            Visualizar
                                        </a>

                                        <?php if ($update): ?>
                                        <!-- Botão Editar -->
                                        <a href="<?= INCLUDE_PATH_ADMIN . "editar-pagina?id={$pagina['id']}"; ?>" class="btn btn-6 btn-outline-secondary d-flex align-items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-edit">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                                <path d="M16 5l3 3"></path>
                                            </svg>
                                            Editar
                                        </a>
                                        <?php elseif ($only_own || $read): ?>
                                        <!-- Botão Detalhes -->
                                        <a href="<?= INCLUDE_PATH_ADMIN . "editar-pagina?id={$pagina['id']}"; ?>" class="btn btn-6 btn-outline-info d-flex align-items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-edit">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                                <path d="M16 5l3 3"></path>
                                            </svg>
                                            Detalhes
                                        </a>
                                        <?php endif; ?>

                                        <?php if ($delete): ?>
                                        <!-- Espaçamento antes do botão Deletar -->
                                        <div class="ms-auto"></div>
                                        
                                        <!-- Botão Deletar -->
                                        <a type="button" class="btn btn-6 btn-outline-danger d-flex align-items-center gap-1 btn-delete" data-id="<?php echo $pagina['id']; ?>" data-name="<?php echo $pagina['titulo']; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler-trash">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                            Apagar
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    
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
            $('#paginaNome').text(elementNameToDelete); // Define o nome no modal
            $('#deleteModal').modal('show'); // Mostra o modal
        });

        // Quando confirmar a exclusão
        $('#confirmDelete').on('click', function () {
            if (elementIdToDelete) {
                $.ajax({
                    url: `<?= INCLUDE_PATH_ADMIN; ?>back-end/delete-pagina.php?id=${elementIdToDelete}`,
                    type: 'GET',
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.error('Erro:', error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#paginas").before(`
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

<!-- Listar Páginas -->
<script>
    $(document).ready(function() {
        var table = $('#paginas').DataTable({
            "paging": false, // Desativa a paginação do DataTable
            "info": false, // Desativa a informação sobre o número de registros
            "responsive": true,
            "pageLength": 10, // Define o número inicial de entradas por página
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