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

<!-- Modal -->
<div class="modal modal-blur fade" id="emailBodyModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-2 modal-lg modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h5 class="modal-title">Conteúdo do email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Título</div>
                        <div class="datagrid-content" id="emailTitle">–</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Data de Envio</div>
                        <div class="datagrid-content" id="emailDate">–</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Conteúdo</div>
                        <div class="datagrid-content" id="emailBody">–</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Fechar</button>
            </div>
		</div>
	</div>
</div>

<?php
    function tempoDecorrido($data) {
        $agora = new DateTime();
        $dataEnvio = new DateTime($data);
        $diferenca = $agora->diff($dataEnvio);

        if ($diferenca->y > 0) {
            return $diferenca->y . ' ano' . ($diferenca->y > 1 ? 's' : '');
        } elseif ($diferenca->m > 0) {
            return $diferenca->m . ' mês' . ($diferenca->m > 1 ? 'es' : '');
        } elseif ($diferenca->d >= 7) {
            return floor($diferenca->d / 7) . ' semana' . (floor($diferenca->d / 7) > 1 ? 's' : '');
        } elseif ($diferenca->d > 0) {
            return $diferenca->d . ' dia' . ($diferenca->d > 1 ? 's' : '');
        } elseif ($diferenca->h > 0) {
            return $diferenca->h . ' hora' . ($diferenca->h > 1 ? 's' : '');
        } elseif ($diferenca->i > 0) {
            return $diferenca->i . ' minuto' . ($diferenca->i > 1 ? 's' : '');
        } else {
            return $diferenca->s . ' segundo' . ($diferenca->s > 1 ? 's' : '');
        }
    }

    $tabela = 'tb_bulk_emails';
    $sql = "SELECT * FROM $tabela ORDER BY date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Novidades
                </h2>
                <div class="text-secondary mt-1">Listagem dos emails em massa enviados pelo sistema.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <a href="<?php echo INCLUDE_PATH_ADMIN; ?>email_em_massa" class="btn btn-info btn-3">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2"><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    Novo email em massa
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="divide-y">

                            <?php foreach( $resultados as $res ): ?>
                            <?php 
                                $date = date('d/m/Y H:i:s', strtotime($res['date']));
                                $time = tempoDecorrido($res['date']);
                            ?>
                            <div>
                                <a href="#" onclick='modalToggle(<?php echo json_encode($res["title"]); ?>, <?php echo json_encode($date); ?>, <?php echo json_encode($res["body"]); ?>)' style="text-decoration: none;">
                                    <div class="row">
                                        <div class="col">
                                            <h3 class="text-truncate text-dark mb-0">
                                                <?php echo $res['title']; ?>
                                            </h3>
                                            <div class="text-secondary">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $date; ?>">
                                                    <?php echo $time; ?> atrás
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-auto align-self-center">
                                            <button type="button" class="btn me-auto">Ver Conteúdo</button>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>

                            <?php if (!$encontrouPagamento): ?>
                                <h3 class="card-title">Você não possui nenhum envio de E-mail em massa registrado.</h3>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const modalToggle = (title, date, content) => {
        $('#emailTitle').html(title);
        $('#emailDate').html(date);
        $('#emailBody').html(content);
        $('#emailBodyModal').modal('toggle');
    }
</script>