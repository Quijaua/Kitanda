<?php
  $read = verificaPermissao($_SESSION['user_id'], 'usuarios', 'read', $conn);
  $disabledRead = !$read ? 'disabled' : '';

  $only_own = verificaPermissao($_SESSION['user_id'], 'usuarios', 'only_own', $conn);
  $disabledOnlyOwn = !$only_own ? 'disabled' : '';

  $create = verificaPermissao($_SESSION['user_id'], 'usuarios', 'create', $conn);
  $disabledCreate = !$create ? 'disabled' : '';

  $update = verificaPermissao($_SESSION['user_id'], 'usuarios', 'update', $conn);
  $disabledUpdate = !$update ? 'disabled' : '';

  $delete = verificaPermissao($_SESSION['user_id'], 'usuarios', 'delete', $conn);
  $disabledDelete = !$delete ? 'disabled' : '';
?>

<?php
// Se o usuário possui somente a permissão only_own, filtramos os produtos criados por ele.
if ($read) {
  // Consulta para buscar os usuários cadastrados, juntando com a função (caso exista)
  $stmt = $conn->prepare("
    SELECT c.*, f.nome AS funcao_nome
    FROM tb_clientes c
    LEFT JOIN tb_permissao_usuario pu ON c.id = pu.usuario_id
    LEFT JOIN tb_funcoes f ON pu.permissao_id = f.id
    WHERE c.id != 1 AND c.id != ? AND f.id = 2
    GROUP BY c.id
    ORDER BY c.id DESC
  ");
  $stmt->execute([$_SESSION['user_id']]);
} else if ($only_own) {
  // Consulta para buscar os usuários cadastrados, juntando com a função (caso exista)
  $stmt = $conn->prepare("
    SELECT c.*, f.nome AS funcao_nome
    FROM tb_clientes c
    LEFT JOIN tb_permissao_usuario pu ON c.id = pu.usuario_id
    LEFT JOIN tb_funcoes f ON pu.permissao_id = f.id
    WHERE c.id != 1 AND c.id != ? AND c.criado_por = ? AND f.id = 2
    GROUP BY c.id
    ORDER BY c.id DESC
  ");
  $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
}
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .status-btn {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.25rem 0.75rem;
        border-radius: 9rem;
        min-width: 100px;
        border: 2px solid;
        font-size: 0.95rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .25rem;
        border-radius: 999px;
        min-width: 100px;
        font-size: 0.95rem;
    }

    .status-ativo {
        background-color: #e6f4e6;
        color: #2ecc71;
        border-color: #2ecc71;
    }

    .status-inativo {
        background-color: #fde6e6;
        color: #e74c3c;
        border-color: #e74c3c;
    }

    .status-circle {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        margin-left: 8px;
    }

    .status-ativo .status-circle {
        background-color: #2ecc71;
    }

    .status-inativo .status-circle {
        background-color: #e74c3c;
        margin-left: 0;
        margin-right: 8px;
    }
</style>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="confirmModalLabel">Confirmação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="confirmModalMessage">
        <!-- Mensagem será injetada via JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn" id="confirmModalAction">Confirmar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <div class="modal-body text-center py-4">
        <!-- Ícone de alerta -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
             stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
          <path d="M12 9v4"></path>
          <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636-2.87l-8.106-13.536a1.914 1.914 0 0 0-3.274 0z"></path>
          <path d="M12 16h.01"></path>
        </svg>
        <h3>Confirmar Exclusão</h3>
        <div class="text-secondary">
          Tem certeza de que deseja excluir o usuário da vendedora <b>"<span id="usuarioNome"></span>"</b>?
        </div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <div class="row">
            <div class="col">
              <button type="button" class="btn btn-3 w-100" data-bs-dismiss="modal">Cancelar</button>
            </div>
            <div class="col">
              <button type="button" id="confirmDelete" class="btn btn-danger btn-4 w-100">Deletar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Esconde filtros padrão do DataTable -->
<style>
  #usuarios_filter, #usuarios_length {
      display: none;
  }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Vendedoras</h2>
        <div class="text-secondary mt-1">Aqui estão as vendedoras cadastradas no sistema.</div>
      </div>
      <?php if (getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
      <div class="col-auto ms-auto d-print-none">
        <a href="<?= ($create) ? INCLUDE_PATH_ADMIN."criar-usuario" : "#"; ?>" class="btn btn-info btn-3 <?= $disabledCreate; ?>" <?= $disabledCreate; ?>>
          <!-- Ícone de Adicionar -->
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
              stroke-linecap="round" stroke-linejoin="round" class="icon icon-2">
            <path d="M12 5v14"></path>
            <path d="M5 12h14"></path>
          </svg>
          Novo Usuário
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
            <h4 class="card-title">Vendedoras Cadastradas</h4>
          </div>

          <div class="card-body border-bottom py-3">
            <div class="d-flex">
              <div class="text-secondary">
                Exibir
                <div class="mx-2 d-inline-block">
                  <select class="form-control form-control-sm" id="entries-select">
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
                  <input type="text" class="form-control form-control-sm" id="search-input" placeholder="Buscar usuário">
                </div>
              </div>
            </div>
          </div>

          <table id="usuarios" class="table card-table table-vcenter text-nowrap datatable">
            <thead>
              <tr>
                <th>Nome da Vendedora</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Função</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($usuarios as $usuario): ?>
                <?php
                  if (!empty($usuario['funcao_nome'])) {
                    $usuario['funcao'] = $usuario['funcao_nome'];
                  } else if (!empty($usuario['roles'])) {
                    switch ($usuario['roles']) {
                      case 1:
                        $usuario['funcao'] = "Administrador";
                        break;
                      case 2:
                        $usuario['funcao'] = "Doador";
                        break;
                    }
                  } else {
                    $usuario['funcao'] = "N/A";
                  }
                ?>
                <tr>
                  <td data-label="Nome"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                  <td data-label="E-mail"><?php echo htmlspecialchars($usuario['email']); ?></td>
                  <td data-label="Telefone"><?php echo !empty($usuario['phone']) ? $usuario['phone'] : '--'; ?></td>
                  <td data-label="Função"><?php echo htmlspecialchars($usuario['funcao']); ?></td>
                  <!-- <td data-label="Status"><?php echo $usuario['status'] ? 'Ativo' : 'Inativo'; ?></td> -->

                  <td data-label="Status">
                    <?php if ($usuario['status'] === 1) : ?>
                        <span class="status-badge status-ativo">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-checkbox me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                            Ativo
                        </span>
                    <?php else: ?>
                        <span class="status-badge status-inativo">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-circle-off me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20.042 16.045a9 9 0 0 0 -12.087 -12.087m-2.318 1.677a9 9 0 1 0 12.725 12.73" /><path d="M3 3l18 18" /></svg>
                            Inativo
                        </span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <div class="d-flex align-items-center justify-content-end">
                        <?php if ($usuario['status'] === 1) : ?>
                            <a onclick="modalShow(
                                    'Inativar vendedora',
                                    'Tem certeza que deseja inativar essa vendedora?',
                                    'danger',
                                    () => window.location.href = 'back-end/toggle_status.php?id=<?= $usuario['id']; ?>'
                                )"
                            >
                                <span class="status-btn status-inativo ms-8">
                                    <span class="status-circle"></span>
                                    Inativar
                                </span>
                            </a>
                        <?php else: ?>
                            <a class="text-decoration-none" href="<?= INCLUDE_PATH_ADMIN; ?>back-end/toggle_status.php?id=<?= $usuario['id']; ?>">
                                <span class="status-btn status-ativo ms-8">
                                    Ativar
                                    <span class="status-circle"></span>
                                </span>
                            </a>
                        <?php endif; ?>

                        <span class="dropdown ms-2">
                        <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Ações</button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <?php if ($update): ?>
                            <a class="dropdown-item" href="<?php echo INCLUDE_PATH_ADMIN . "editar-usuario?id={$usuario['id']}"; ?>">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/edit -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler-edit"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path><path d="M16 5l3 3"></path></svg>
                                Editar
                            </a>
                            <?php elseif ($only_own || $read): ?>
                            <a class="dropdown-item" href="<?= INCLUDE_PATH_ADMIN . "editar-usuario?id={$usuario['id']}"; ?>">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/edit -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 icon-tabler-edit"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path><path d="M16 5l3 3"></path></svg>
                                Detalhes
                            </a>
                            <?php endif; ?>
                            <?php if ($delete): ?>
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item text-danger btn-delete" data-id="<?php echo $usuario['id']; ?>" data-name="<?php echo $usuario['nome']; ?>">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/edit -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon dropdown-item-icon icon-2 text-danger icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                Deletar
                            </button>
                            <?php endif; ?>
                        </div>
                        </span>
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
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                       viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                       stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                    <path d="M15 6l-6 6l6 6"></path>
                  </svg>
                  anterior
                </a>
              </li>
              <li class="page-item disabled" id="current-page">
                <a class="page-link" href="#">1</a>
              </li>
              <li class="page-item" id="next-page">
                <a class="page-link" href="#">
                  próximo
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                       viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                       stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
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

<!-- Inclusão de CSS para DataTables -->
<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/responsive/3.0.0/css/responsive.dataTables.css" type="text/css">
<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/responsive/3.0.0/css/responsive.bootstrap4.css" type="text/css">
<link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>dist/libs/datatables.net/2.0.1/css/dataTables.bootstrap4.css" type="text/css">

<!-- Inclusão de JS para DataTables e funcionalidades -->
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
  function modalShow(titulo, mensagem, tipo = 'primary', callbackConfirmar) {
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));

    document.getElementById('confirmModalLabel').innerText = titulo;
    document.getElementById('confirmModalMessage').innerText = mensagem;

    const btnConfirmar = document.getElementById('confirmModalAction');
    btnConfirmar.className = 'btn btn-' + tipo; // Ex: btn-danger
    btnConfirmar.onclick = function () {
      if (typeof callbackConfirmar === 'function') callbackConfirmar();
      modal.hide();
    };

    modal.show();
  }
</script>

<!-- Script para exclusão -->
<script>
$(document).ready(function () {
    let elementIdToDelete = null;

    // Ao clicar no botão de exclusão
    $(document).on('click', '.btn-delete', function () {
        elementIdToDelete = $(this).data('id');
        const elementNameToDelete = $(this).data('name');
        $('#usuarioNome').text(elementNameToDelete);
        $('#deleteModal').modal('show');
    });

    // Ao confirmar a exclusão
    $('#confirmDelete').on('click', function () {
        if (elementIdToDelete) {
            $.ajax({
                url: `<?= INCLUDE_PATH_ADMIN; ?>back-end/delete-user.php?id=${elementIdToDelete}`,
                type: 'GET',
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Erro:', error);
                    $(".alert").remove();
                    $("#usuarios").before(`
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

<!-- Script para DataTables com colunaDefs para ocultar certas colunas no layout principal -->
<script>
$(document).ready(function() {
    var table = $('#usuarios').DataTable({
        "paging": false,
        "info": false,
        "responsive": true,
        "pageLength": 10,
        "buttons": ["csv", "excel", "pdf", "print"],
        "columnDefs": [
            // Colunas com índices 5 a 8 (Tiktok, Facebook, Instagram e Site) serão ocultas no layout principal
            { "className": "none", "targets": [5, 6, 7, 8] }
        ],
        "language": {
            "emptyTable": "Nenhum registro encontrado",
            "zeroRecords": "Nenhum registro corresponde à pesquisa",
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

    $('#exportCSV').click(function () { table.button(0).trigger(); });
    $('#exportXLS').click(function () { table.button(1).trigger(); });
    $('#exportPDF').click(function () { table.button(2).trigger(); });
    $('#exportPrint').click(function () { table.button(3).trigger(); });

    function updatePaginationInfo() {
        var pageInfo = table.page.info();
        $('#start-entry').text(pageInfo.recordsTotal === 0 ? 0 : pageInfo.start + 1);
        $('#end-entry').text(pageInfo.recordsTotal === 0 ? 0 : pageInfo.end);
        $('#total-entry').text(pageInfo.recordsTotal);
        $('#prev-page').toggleClass('disabled', pageInfo.page === 0);
        $('#next-page').toggleClass('disabled', pageInfo.page === pageInfo.pages - 1);
        $('#current-page a').text(pageInfo.page + 1);
    }

    updatePaginationInfo();

    $('#prev-page').click(function() {
        if ($(this).hasClass('disabled')) return;
        table.page('previous').draw('page');
        updatePaginationInfo();
    });

    $('#next-page').click(function() {
        if ($(this).hasClass('disabled')) return;
        table.page('next').draw('page');
        updatePaginationInfo();
    });

    $('#pagination-custom').on('click', '.page-link', function(e) {
        var page = $(this).text() - 1;
        table.page(page).draw('page');
        updatePaginationInfo();
    });

    $('#search-input').keyup(function() { table.search($(this).val()).draw(); });
    $('#entries-select').change(function() {
        table.page.len($(this).val()).draw();
        updatePaginationInfo();
    });
});
</script>

<style>
  div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      justify-content: revert;
  }
</style>