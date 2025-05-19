<?php if (isset($_GET['id'])): ?>

<!-- CSS do Glider.js -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css"
/>

<?php
    $stmt = $conn->prepare("
        SELECT *
        FROM tb_blog_categorias
        WHERE id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($categoria)) {
        header('Location: ' . INCLUDE_PATH . 'blog');
        exit;
    }

?>


<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>blog">Blog</a></li>
                    <li class="breadcrumb-item active"><?= $categoria['nome']; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php
$stmt = $conn->prepare("
    SELECT p.* 
    FROM tb_blog_categoria_posts cp
    JOIN tb_blog_posts p ON cp.post_id = p.id
    WHERE cp.categoria_id = ?
    LIMIT 4
");
$stmt->execute([$categoria['id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$qtdPostsIniciais = count($posts);
?>

<style>
    .card-img-top {
        width: 100%;
        aspect-ratio: 2/1;
        object-fit: cover;
        display: block;
    }
</style>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">

            <?php if ($posts): ?>

                <div class="col-md-12">
                    <div class="row row-cards">
                        <?php foreach ($posts as $post) : ?>

                            <?php
                                $stmtCategoria = $conn->prepare("
                                    SELECT c.* 
                                    FROM tb_blog_categoria_posts cp
                                    JOIN tb_blog_categorias c ON cp.categoria_id = c.id
                                    WHERE cp.post_id = ?
                                ");
                                $stmtCategoria->execute([$post['id']]);
                                $categorias = $stmtCategoria->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php
                                $post['imagem'] = !empty($post['imagem'])
                                                ? str_replace(' ', '%20', INCLUDE_PATH . "files/blog/" . $post['id'] . "/" . $post['imagem'])
                                                : INCLUDE_PATH . "assets/preview-image/product.jpg";
                            ?>
                            <div class="col-sm-6 col-lg-6 d-grid">
                                <div class="card card-sm">
                                    <a href="<?= INCLUDE_PATH . "post?id={$post['id']}"; ?>" class="d-block">
                                        <img src="<?= $post['imagem']; ?>" class="card-img-top" id="card-img-preview">
                                    </a>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <?php if ($categorias): ?>
                                                    <div class="mb-2">
                                                    <?php foreach ($categorias as $categoria): ?>
                                                        <span class="badge badge-outline text-dark bg-light badge-lg"><?= $categoria['nome']; ?></span>
                                                    <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <small><?php echo date("d/m/Y", strtotime($post["data_publicacao"])); ?></small>
                                                <h3 id="title-preview"><?= $post['titulo']; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="col-12" id="load-more-wrapper">
                            <div class="d-flex justify-content-center">
                                <button id="btn-load-more" class="btn btn-6 btn-dark btn-pill">Ver mais</button>
                            </div>
                        </div>

                    </div>
                </div>

            <?php else: ?>

                <div class="col-12">
                    <div class="alert alert-info w-100" role="alert">
                        <div class="d-flex">
                            <div class="alert-icon">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Alerta do Sistema</h4>
                                <div class="text-secondary">Não encontramos nenhum artigo cadastrado na plataforma.</div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>


<script>
  $(function() {
    // Passa id da categoria
    var categoria_id = <?= $categoria['id']; ?>;
    // O offset inicial corresponde à quantidade de posts já exibidos (4)
    var offset = <?= $qtdPostsIniciais; ?>;
    // Como no SELECT usamos LIMIT 4, repetimos esse valor aqui:
    var limit  = 4;

    $('#btn-load-more').on('click', function() {
      $.ajax({
        url: '<?= INCLUDE_PATH; ?>back-end/posts/list-categoria.php',
        method: 'POST',
        data: { categoria_id: categoria_id, offset: offset, limit: limit },
        dataType: 'json',
        success: function(res) {
          // Se o servidor retornar status "sucesso" e existir array de posts
          if (res.status === 'sucesso' && res.data.length) {
            res.data.forEach(function(p) {
                // Monta as badges de categoria (se houver)
                var badgesHTML = '';
                if (p.categorias && p.categorias.length) {
                  badgesHTML = '<div class="mb-2">';
                  p.categorias.forEach(function(cat) {
                    badgesHTML += '<span class="badge badge-outline text-dark bg-light badge-lg">' 
                                   + cat.nome + 
                                  '</span> ';
                  });
                  badgesHTML += '</div>';
                }

                // Formata a data no padrão DD/MM/AAAA
                var dt = new Date(p.data_publicacao);
                var dia = String(dt.getDate()).padStart(2, '0');
                var mes = String(dt.getMonth() + 1).padStart(2, '0');
                var ano = dt.getFullYear();
                var dataFormatada = dia + '/' + mes + '/' + ano;

                // Monta o card do post
                var card = '\
                  <div class="col-sm-6 col-lg-6 d-grid">\
                    <div class="card card-sm">\
                      <a href="<?= INCLUDE_PATH; ?>post?id=' + p.id + '" class="d-block">\
                        <img src="' + p.imagem + '" class="card-img-top" id="card-img-preview">\
                      </a>\
                      <div class="card-body">\
                        <div class="d-flex align-items-center">\
                          <div>' +
                            badgesHTML + '\
                            <small>' + dataFormatada + '</small>\
                            <h3 id="title-preview">' + p.titulo + '</h3>\
                          </div>\
                        </div>\
                      </div>\
                    </div>\
                  </div>';

                // Insere o novo card **antes** do wrapper do botão “Ver mais”
                $('#load-more-wrapper').before(card);
            });

            // Atualiza o offset (somamos a quantidade de posts recebidos agora)
            offset += res.data.length;

            // Se vier menos que o limite, não há mais posts: desabilita o botão
            if (res.data.length < limit) {
              $('#btn-load-more').text('Não há mais');
              $('#btn-load-more').prop('disabled', true);
            }
          } else {
            // Se não retornar dados, também desabilita
            $('#btn-load-more').text('Não há mais');
            $('#btn-load-more').prop('disabled', true);
          }
        },
        error: function() {
          alert('Erro ao carregar mais posts.');
        }
      });
    });
  });
</script>

<?php else: ?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb breadcrumb-muted">
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH; ?>blog">Blog</a></li>
                    <li class="breadcrumb-item active">Categoria não encontrada</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-12">
                <div class="alert alert-info w-100" role="alert">
                    <div class="d-flex">
                        <div class="alert-icon">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
                        </div>
                        <div>
                            <h4 class="alert-title">Alerta do Sistema</h4>
                            <div class="text-secondary">Não encontramos esta categoria.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>