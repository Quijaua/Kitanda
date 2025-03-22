<?php
    $read = verificaPermissao($_SESSION['user_id'], 'doadores', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';
?>

<style>
    #clientes_filter, #clientes_length {
        display: none;
    }

    .card-table tr th:first-child {
        padding-left: 10px !important;
    }
    .more-info {
        position: relative !important;
        padding-left: 30px !important;
    }
    table.dataTable.dtr-inline.collapsed>tbody>tr>td.sorting_1:before {
        top: 50%;
        left: 5px;
        height: 1em;
        width: 1em;
        margin-top: -9px;
        display: block;
        position: absolute;
        color: white;
        border: .15em solid white;
        border-radius: 1em;
        box-shadow: 0 0 .2em #444;
        box-sizing: content-box;
        text-align: center;
        text-indent: 0 !important;
        font-family: "Courier New", Courier, monospace;
        line-height: 1em;
        content: "+";
        background-color: #0d6efd;
    }

    table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.sorting_1:before {
        content: "-";
        background-color: #d33333;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Doadores
                </h2>
                <div class="text-secondary mt-1">Aqui estão os relatórios de doadores do sistema.</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <?php if (!$read): ?>
            <div class="col-12">
                <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
            </div>
            <?php exit; endif; ?>

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">Doadores</h4>
                        <div class="ms-auto lh-1">
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Exportar
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><button id="exportCSV" class="dropdown-item">Exportar CSV</button></li>
                                    <li><button id="exportXLS" class="dropdown-item">Exportar XLS</button></li>
                                    <li><button id="exportPDF" class="dropdown-item">Exportar PDF</button></li>
                                    <li><button id="exportPrint" class="dropdown-item">Imprimir</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card-body border-bottom py-3">
                        <div class="d-flex">
                            <div class="text-secondary">
                                Exibir
                                <div class="mx-2 d-inline-block">
                                    <select class="form-control form-control-sm" id="entries-select" aria-label="Contagem de faturas" w>
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

                    <table id="clientes" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>CPF</th>
                                <th>Endereço</th>
                                <th>Newsletter</th>
                                <th>Doador Anônimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($clientes as $cliente) : ?>
                            <tr>
                                <td class="more-info"><?php echo $cliente["nome"] ?></td>
                                <td><?php echo $cliente["email"] ?></td>
                                <td><?php echo $cliente["phone"] ?></td>
                                <td><?php echo $cliente["cpf"] ?></td>
                                <td>
                                    <?php
                                        echo $cliente["endereco"] . ", " . $cliente["numero"] . " - " . $cliente["municipio"] . " - " .$cliente["cidade"] . " / " . $cliente["uf"] . " - CEP " . $cliente["cep"]
                                    ?>
                                </td>
                                <td><?php echo $cliente["newsletter"] ? "Sim" : "Não"; ?></td>
                                <td><?php echo $cliente["private"] ? "Sim" : "Não"; ?></td>
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

<script>
    $(document).ready(function() {
        var table = $('#clientes').DataTable({
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