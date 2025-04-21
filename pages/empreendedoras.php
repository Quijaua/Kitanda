
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item active">Empreendedoras</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php
    // Consulta para buscar as lojas
    $limit = 6;
    $stmt = $conn->prepare("SELECT * FROM tb_lojas ORDER BY nome LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $empreendedoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <?php foreach ($empreendedoras as $e): ?>
            <?php
                $e['imagem'] = !empty($e['imagem'])
                               ? str_replace(' ', '%20', INCLUDE_PATH . "files/lojas/{$e['id']}/perfil/{$e['imagem']}")
                               : INCLUDE_PATH . "assets/preview-image/profile.jpg";
            ?>

            <div class="col-md-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <span class="avatar avatar-xxl rounded-circle" style="background-image: url(<?= $e['imagem']; ?>)"></span>
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <h3 class="card-title mb-0"><?= htmlspecialchars($e['nome']); ?></h3>
                                <p class="d-inline-flex align-items-center lh-1 mb-4">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/map-pin -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1 icon-tabler icons-tabler-outline icon-tabler-map-pin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                    <?= htmlspecialchars($e['cidade']) . '/' . htmlspecialchars($e['estado']); ?>
                                </p>
                                <div class="d-flex">
                                    <a href="<?= INCLUDE_PATH . 'empreendedora?id=' . $e['id']; ?>" class="btn btn-6 btn-outline-dark btn-pill"> Ver mais </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>

            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <button id="btn-load-more" class="btn btn-6 btn-dark btn-pill">Ver mais</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
  $(function() {
    var offset = <?= count($empreendedoras); ?>;
    var limit  = <?= $limit; ?>;

    $('#btn-load-more').on('click', function() {
      $.ajax({
        url: '<?= INCLUDE_PATH; ?>back-end/empreendedoras/list.php',
        method: 'POST',
        data: { offset: offset, limit: limit },
        dataType: 'json',
        success: function(res) {
          if (res.status === 'sucesso' && res.data.length) {
            res.data.forEach(function(e) {
              var card = '\
                <div class="col-md-6 col-xl-6">\
                    <div class="card">\
                        <div class="card-body">\
                            <div class="row">\
                                <div class="col-auto">\
                                    <span class="avatar avatar-xxl rounded-circle" style="background-image: url(' +
                                        e.imagem + ')\"></span>\
                                    </div>\
                                    <div class="col d-flex flex-column justify-content-center">\
                                    <h3 class="card-title mb-0">' + e.nome + '</h3>\
                                    <p class="d-inline-flex align-items-center lh-1 mb-4">\
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-map-pin">\
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>\
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/>\
                                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"/>\
                                        </svg>\
                                        ' + e.cidade + '/' + e.estado + '\
                                    </p>\
                                    <div class="d-flex">\
                                        <a href="<?= INCLUDE_PATH; ?>empreendedora.php?id=' + e.id + '" class="btn btn-6 btn-outline-dark btn-pill">Ver mais</a>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                </div>';
              $('#load-more-wrapper').before(card);
            });
            offset += res.data.length;
            if (res.data.length < limit) {
              $('#btn-load-more').text('Não há mais');
              $('#btn-load-more').prop('disabled', true);
            }
          } else {
            $('#btn-load-more').text('Não há mais');
            $('#btn-load-more').prop('disabled', true);
          }
        },
        error: function() {
          alert('Erro ao carregar mais empreendedoras.');
        }
      });
    });
  });
  </script>