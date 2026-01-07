<?php
    // Consulta os pedidos do usuário
    $stmt = $conn->prepare("SELECT * FROM tb_pedidos WHERE usuario_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    #pedidos_filter, #pedidos_length {
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

<!-- Modal de Rastreamento do Pedido -->
<div class="modal fade" id="modal-track" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <!-- Título que será atualizado com o ID da compra -->
        <h5 class="modal-title" id="modal-track-title">Rastrear Compra #</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar modal"></button>
      </div>
      <div class="modal-body">
        <!-- A timeline será preenchida dinamicamente -->
        <ul class="timeline mb-0"></ul>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Compras
                </h2>
                <div class="text-secondary mt-1">Aqui estão suas compras no sistema.</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">Compras</h4>
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

                    <table id="pedidos$pedidos" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>ID do Pedido</th>
                                <th>Data de Criação</th>
                                <th>Status</th>
                                <th>Forma de Pagamento</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pedidos as $pedido) : ?>
                                <?php
                                    $pedido['status'] = $pedido['status'] == 'CONFIRMED' ? "Confirmado" : ($pedido['status'] == 'PENDING' ? "Pendente" : ($pedido['status'] == 'OVERDUE' ? "Vencido" : ($pedido['status'] == 'CANCELED' ? "Cancelado" : "Indefinido")));
                                    $pedido['forma_pagamento'] = $pedido['forma_pagamento'] == 'PIX' ? "Pix" : ($pedido['status'] == 'CREDIT_CARD' ? "Cartão de crédito" : ($pedido['status'] == 'BOLETO' ? "Boleto Bancário" : "Indefinido"));
                                    $pedido['total'] = "R$ " . number_format($pedido['total'], 2, ',', '.');
                                ?>
                            <tr>
                                <td class="more-info"><?php echo $pedido["pedido_id"] ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($pedido["data_criacao"])); ?></td>
                                <td><?php echo $pedido["status"] ?></td>
                                <td><?php echo $pedido["forma_pagamento"] ?></td>
                                <td><?php echo $pedido["total"] ?></td>
                                <td class="text-end">
                                    <span class="dropdown">
                                        <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Ações</button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="<?php echo INCLUDE_PATH_USER . "compra?pedido={$pedido['pedido_id']}"; ?>" target="_blank">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/external-link -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                                Detalhes da Compra
                                            </a>
                                            <button type="button" class="dropdown-item btn-track" data-id="<?php echo $pedido['pedido_id']; ?>">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/truck -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler-truck"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                                                Acompanhar Pedido
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item text-danger btn-delete" data-id="<?php echo $pedido['pedido_id']; ?>">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/shopping-bag-x -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 text-danger icon-tabler-shopping-bag-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 21h-4.426a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304h11.339a2 2 0 0 1 1.977 2.304l-.506 3.287" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /><path d="M22 22l-5 -5" /><path d="M17 22l5 -5" /></svg>
                                                Cancelar Pedido
                                            </button>
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
    $('.btn-track').on('click', function() {
        var pedidoId = $(this).data('id'); // Obtém o ID do pedido a partir do atributo data-id
        
        // Atualiza o título do modal com o ID do pedido
        $('#modal-track-title').text('Rastrear Compra #' + pedidoId);
        
        // Realiza a requisição AJAX para buscar os dados do pedido
        $.ajax({
            url: '<?= INCLUDE_PATH_USER; ?>back-end/rastrear-pedido.php',  // Endpoint que retorna os dados do pedido em JSON
            type: 'GET',
            data: { pedido: pedidoId },
            dataType: 'json',
            success: function(response) {
                // Exemplo: 'response' contém data_criacao, data_pagamento, status, forma_pagamento etc.
                var timelineHtml = '';
                // Linha da criação do pedido
                timelineHtml += '<li class="timeline-item">' +
                                  '<div class="timeline-item-marker bg-primary"></div>' +
                                  '<div class="timeline-item-content">' +
                                  '<p class="text-muted">Pedido criado em ' + response.data_criacao + '</p>' +
                                  '</div>' +
                                  '</li>';
                // Linha para pagamento
                if(response.status.toLowerCase() !== 'pending') {
                    timelineHtml += '<li class="timeline-item">' +
                                      '<div class="timeline-item-marker bg-success"></div>' +
                                      '<div class="timeline-item-content">' +
                                      '<p class="text-muted">Pagamento confirmado em ' + (response.data_pagamento ? response.data_pagamento : "N/D") + '</p>' +
                                      '</div>' +
                                      '</li>';
                } else {
                    timelineHtml += '<li class="timeline-item">' +
                                      '<div class="timeline-item-marker bg-warning"></div>' +
                                      '<div class="timeline-item-content">' +
                                      '<p class="text-muted">Pagamento pendente</p>' +
                                      '</div>' +
                                      '</li>';
                }
                // Linha para rastreamento (ainda não disponível)
                timelineHtml += '<li class="timeline-item">' +
                                  '<div class="timeline-item-marker bg-light"></div>' +
                                  '<div class="timeline-item-content">' +
                                  '<p class="text-muted mb-0">Rastreamento não disponível</p>' +
                                  '</div>' +
                                  '</li>';
                
                // Atualiza a timeline no modal
                $('#modal-track .timeline').html(timelineHtml);
            },
            error: function(xhr, status, error) {
                console.error("Erro ao buscar os dados do pedido: " + error);
            }
        });
        
        // Exibe o modal utilizando Bootstrap 5
        var modalTrack = new bootstrap.Modal(document.getElementById('modal-track'));
        modalTrack.show();
    });
});
</script>

<script>
    $(document).ready(function() {
        var table = $('#pedidos$pedidos').DataTable({
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