<?php
    $update = verificaPermissao($_SESSION['user_id'], 'sobre', 'update', $conn);
    $editorDisabled = !$update ? 'true' : 'false';
    $disabledUpdate = !$update ? 'disabled' : '';
    $disabled = $update ? 'disabled' : '';
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                	Dashboard
                </h2>
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
        <div class="row row-deck row-cards">

            <?php if (!$update): ?>
            <div class="col-12">
                <div class="alert alert-info">Você não tem permissão para acessar esta página.</div>
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
                            <h4 class="alert-title">Atenção!</h4>
                            <div class="text-secondary">Sua Webhook está inativa. <a href="<?php echo INCLUDE_PATH_ADMIN; ?>webhook" class="alert-link">Clique aqui</a> para corrigir.</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-lg-12">
                <div class="card">
                        <div class="card-body">



          <div class="col-12">
                <div class="row row-cards">
                  <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <span class="bg-primary text-white avatar"
                              ><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-1"
                              >
                                <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
                                <path d="M12 3v3m0 12v3" /></svg
                            ></span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">132 vendas</div>
                            <div class="text-secondary">12 aguardando pagamento</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <span class="bg-green text-white avatar"
                              ><!-- Download SVG icon from http://tabler.io/icons/icon/shopping-cart -->
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-1"
                              >
                                <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 17h-11v-14h-2" />
                                <path d="M6 5l14 1l-1 7h-13" /></svg
                            ></span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">78 Pedidos</div>
                            <div class="text-secondary">32 Enviados</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


                </div>
            </div>

             <div class="col-12 mt-4">
                <div class="card">
                  <div class="card-body">
                    <div id="chart-completion-tasks"></div>
                  </div>
                </div>
              </div>


<script>
      document.addEventListener("DOMContentLoaded", function () {
        window.ApexCharts &&
          new ApexCharts(document.getElementById("chart-completion-tasks"), {
            chart: {
              type: "bar",
              fontFamily: "inherit",
              height: 240,
              parentHeightOffset: 0,
              toolbar: {
                show: false,
              },
              animations: {
                enabled: false,
              },
            },
            plotOptions: {
              bar: {
                columnWidth: "50%",
              },
            },
            dataLabels: {
              enabled: false,
            },
            series: [
              {
                name: "Vendas",
                data: [155, 65, 465, 265, 225, 325, 80],
              },
            ],
            tooltip: {
              theme: "dark",
            },
            grid: {
              padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4,
              },
              strokeDashArray: 4,
            },
            xaxis: {
              labels: {
                padding: 0,
              },
              tooltip: {
                enabled: false,
              },
              axisBorder: {
                show: false,
              },
              type: "datetime",
            },
            yaxis: {
              labels: {
                padding: 4,
              },
            },
            labels: ["2020-06-21", "2020-06-22", "2020-06-23", "2020-06-24", "2020-06-25", "2020-06-26", "2020-06-27"],
            //colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 100%)"],
            legend: {
              show: false,
            },
          }).render();
      });
    </script>

        </div>
    </div>
</div>

<?php if (!verificaPermissao($_SESSION['user_id'], 'sobre', 'update', $conn)): ?>
</fieldset>
<?php endif; ?>

