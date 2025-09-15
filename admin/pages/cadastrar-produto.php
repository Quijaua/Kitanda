<?php
    $create = verificaPermissao($_SESSION['user_id'], 'produtos', 'create', $conn);
    $disabledCreate = !$create ? 'disabled' : '';

    // Verifica permissão
    if (getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador') {
        // Dimensão padrão (simulada)
        $defaultDimensao = [
            'id' => 0,
            'nome' => 'Padrão',
            'altura' => 4,
            'largura' => 12,
            'comprimento' => 17,
            'peso' => 0.5
        ];

        // Buscar do banco
        $stmt = $conn->prepare("SELECT * FROM tb_frete_dimensoes");
        $stmt->execute();
        $dimensoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Junta e ordena
        $dimensoes[] = $defaultDimensao;
        usort($dimensoes, fn($a, $b) => strcmp($a['nome'], $b['nome']));

        // Define o ID da função “Vendedora”
        $funcaoVendedora = 2;

        // Busca todas as vendedoras e sua loja (se houver)
        $stmt = $conn->prepare("
            SELECT
                c.id,
                c.nome,
                l.id AS loja_id,
                l.imagem AS loja_imagem
            FROM tb_clientes c
            INNER JOIN tb_permissao_usuario pu ON c.id = pu.usuario_id
            LEFT JOIN tb_lojas l ON c.id = l.vendedora_id
            WHERE c.roles = :funcaoVendedora
            ORDER BY c.nome ASC
        ");
        $stmt->bindParam(':funcaoVendedora', $funcaoVendedora, PDO::PARAM_INT);
        $stmt->execute();

        $empreendedoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($empreendedoras as $key => $e) {
            if (!empty($e['loja_imagem']) && !empty($e['loja_id'])) {
                $empreendedoras[$key]['imagem'] = str_replace(
                    ' ',
                    '%20',
                    INCLUDE_PATH . "files/lojas/{$e['loja_id']}/perfil/{$e['loja_imagem']}"
                );
            } else {
                $empreendedoras[$key]['imagem'] = INCLUDE_PATH . "assets/preview-image/profile.jpg";
            }
        }
    }

    if (getNomePermissao($_SESSION['user_id'], $conn) === 'Vendedor') {
        // Dimensão padrão (simulada)
        $defaultDimensao = [
            'id' => 0,
            'nome' => 'Padrão',
            'altura' => 4,
            'largura' => 12,
            'comprimento' => 17,
            'peso' => 0.5
        ];

        // Buscar do banco
        $stmt = $conn->prepare("SELECT * FROM tb_frete_dimensoes");
        $stmt->execute();
        $dimensoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Junta e ordena
        $dimensoes[] = $defaultDimensao;
        usort($dimensoes, fn($a, $b) => strcmp($a['nome'], $b['nome']));
    }
?>

<?php
    // Consulta para buscar as categorias cadastradas
    $stmt = $conn->prepare("SELECT * FROM tb_categorias");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .dz-preview {
        position: relative;
        display: inline-block;
    }
    .dz-remove-custom {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        color: #6c757d;
        padding: 4px 8px;
        font-size: 14px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease-in-out;
        cursor: pointer !important;
        z-index: 999999;
    }
    .dz-remove-custom:hover {
        background-color: #e9ecef;
    }
    .dz-progress {
        display: none;
    }

    .preview-product {
        position: absolute;
        left: 20px;
        top: 20px;
    }
</style>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Cadastrar Produto
                </h2>
                <div class="text-secondary mt-1">Aqui você pode cadastrar novos produtos.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <ol class="breadcrumb breadcrumb-muted" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="<?= INCLUDE_PATH_ADMIN; ?>produtos">Produtos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cadastrar Produto</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <form id="createProduct" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/create-product.php" method="post" enctype="multipart/form-data">
            <div class="row">

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
                                <h4 class="alert-title">Erro!</h4>
                                <div class="text-secondary"><?php echo $_SESSION['error_msg']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; unset($_SESSION['error_msg']); ?>

                <div class="col-lg-8 row row-deck row-cards mt-0">

                    <div class="col-lg-12 mt-0">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informações principais</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <label for="nome" class="col-3 col-form-label required">Nome do produto</label>
                                    <div class="col">
                                        <input name="nome" id="nome"
                                            type="text" class="form-control" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="titulo" class="col-3 col-form-label required">Título</label>
                                    <div class="col">
                                        <input name="titulo" id="titulo"
                                            type="text" class="form-control" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="estoque" class="col-3 col-form-label required">Estoque</label>
                                    <div class="col-md-3">
                                        <input name="estoque" id="estoque"
                                            type="number" class="form-control" required>
                                    </div>

                                    <label for="vitrine" class="col-3 col-form-label pt-0">Vitrine</label>
                                    <span class="col">
                                        <label for="vitrine" class="form-check form-switch form-switch-3">
                                            <input name="vitrine" id="vitrine" type="checkbox" class="form-check-input" value="1">
                                            <span class="form-check-label form-check-label-on">Sim</span>
                                            <span class="form-check-label form-check-label-off">Não</span>
                                        </label>
                                    </span>
                                </div>

                                <div class="row">
                                    <label for="peso" class="col-3 col-form-label">Peso</label>
                                    <div class="col-md-3">
                                        <input name="peso" id="peso"
                                            type="number" class="form-control" >
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Descrição do produto</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <div class="col">
                                        <textarea name="descricao" id="descricao"></textarea>
                                        <small class="form-hint">Preencha o campo com uma breve descrição sobre seu produto.</small>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Imagens</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <div class="col">
                                        <div class="dropzone" id="dropzone-custom">
                                            <div class="fallback">
                                                <input name="imagens" type="file" accept="image/png,image/jpeg,image/webp" />
                                            </div>
                                            <div class="dz-message">
                                                <h3 class="dropzone-msg-title">
                                                    <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-library-photo"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 3m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 7.26a2.005 2.005 0 0 0 -1.012 1.737v10c0 1.1 .9 2 2 2h10c.75 0 1.158 -.385 1.5 -1" /><path d="M17 7h.01" /><path d="M7 13l3.644 -3.644a1.21 1.21 0 0 1 1.712 0l3.644 3.644" /><path d="M15 12l1.644 -1.644a1.21 1.21 0 0 1 1.712 0l2.644 2.644" /></svg>
                                                    Arraste e solte as imagens aqui
                                                </h3>
                                                <span class="dropzone-msg-desc">Essas imagens serão mostradas na página do produto.</span>
                                            </div>
                                        </div>
                                        <small class="form-hint text-end">Imagens em <b>.png, .jpg, .jpeg, .webp</b> até <b>2MB</b>. Sugerimos dimensões de <b>1000px X 1000px</b>.</small>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Preço</h4>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <label for="preco" class="col-3 col-form-label required">Preço do produto</label>
                                    <div class="col row">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-text"> R$ </span>
                                                <input name="preco" id="preco"
                                                    type="text" class="form-control" autocomplete="off" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">Frete</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-0 row">
                                    <label class="col-3 col-form-label required">Tipo de Frete</label>
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="freight_type" id="freight_default" value="default" checked />
                                            <label class="form-check-label" for="freight_default">Melhor Envio (padrão)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="freight_type" id="freight_fixed" value="fixed" />
                                            <label class="form-check-label" for="freight_fixed">Frete de valor fixo</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 mt-3 row" id="divFreightValue" style="display: none;">
                                    <label for="freight_value" class="col-3 col-form-label required">Valor do Frete (R$)</label>
                                    <div class="col row">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-text"> R$ </span>
                                                <input name="freight_value" id="freight_value" 
                                                    type="text" class="form-control mask-money" placeholder="0,00" autocomplete="off" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 mt-3 row" id="divFreightDimension">
                                    <label for="freight_dimension_id" class="col-3 col-form-label required">Medidas do Produto para Frete</label>
                                    <div class="col">
                                        <select name="freight_dimension_id" id="freight_dimension_id" class="form-select" placeholder="Selecione as medidas deste produto..." required>
                                            <option value="" disabled>Selecione as medidas deste produto</option>
                                            <?php foreach ($dimensoes as $dim): ?>
                                                <option value="<?= $dim['id'] ?>" <?= ($dim['id'] == 0) ? 'selected' : '' ?>>
                                                    <?= $dim['nome'] ?> (<?= $dim['altura'] ?>x<?= $dim['largura'] ?>x<?= $dim['comprimento'] ?> cm, <?= $dim['peso'] ?> kg)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Categorias do produto</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <label for="categorias" class="col-3 col-form-label">Categorias</label>
                                    <div class="col">
                                        <select name="categorias[]" id="categorias" type="text" class="form-select" placeholder="Selecione uma ou mais categoria" multiple>
                                            <?php if ($categorias): ?>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?= $categoria['id']; ?>"><?= $categoria['nome']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                var el;
                                                window.TomSelect && (new TomSelect(el = document.getElementById('categorias'), {
                                                    copyClassesToDropdown: false,
                                                    dropdownParent: 'body',
                                                    controlInput: '<input>',
                                                    render:{
                                                        item: function(data,escape) {
                                                            if( data.customProperties ){
                                                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                                                            }
                                                            return '<div>' + escape(data.text) + '</div>';
                                                        },
                                                        option: function(data,escape){
                                                            if( data.customProperties ){
                                                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                                                            }
                                                            return '<div>' + escape(data.text) + '</div>';
                                                        },
                                                    },
                                                }));
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Vendedora</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-0 row">
                                    <label class="col-3 col-form-label">Vendedora do Produto</label>
                                    <div class="col">
                                        <select id="select-people" name="criado_por" class="form-select" placeholder="Selecione a vendedora deste produto..." >
                                            <option value="">Selecione uma vendedora</option>
                                            <?php foreach ($empreendedoras as $e): ?>
                                            <option value="<?= $e['id']; ?>" data-custom-properties="<span class='avatar avatar-xs' style='background-image: url(<?= $e['imagem']; ?>)'></span>">
                                                <?= htmlspecialchars($e['nome']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        new TomSelect(document.getElementById('select-people'), {
                                            copyClassesToDropdown: false,
                                            dropdownParent: 'body',
                                            render: {
                                                item: function(data, escape) {
                                                    return data.customProperties 
                                                        ? '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>'
                                                        : '<div>' + escape(data.text) + '</div>';
                                                },
                                                option: function(data, escape) {
                                                    return data.customProperties 
                                                        ? '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>'
                                                        : '<div>' + escape(data.text) + '</div>';
                                                },
                                            },
                                        });
                                    });
                                </script>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Google / SEO</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <label for="seo_nome" class="col-3 col-form-label">Nome do produto</label>
                                    <div class="col">
                                        <input name="seo_nome" id="seo_nome"
                                            type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="link" class="col-3 col-form-label required">Link do produto</label>
                                    <div class="col">
                                        <div class="input-group input-group-flat">
                                            <span class="input-group-text"> <?= INCLUDE_PATH; ?> </span>
                                            <input name="link" id="link"
                                                type="text" class="form-control ps-0" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="seo_descricao" class="col-3 col-form-label">Descrição do produto</label>
                                    <div class="col">
                                        <textarea name="seo_descricao" id="seo_descricao" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="d-flex">
                        <button type="button" class="btn btn-1" onclick="location.reload();">Resetar</button>
                        <button type="submit" name="btnCreateProduct" class="btn btn-primary ms-auto">Salvar</button>
                    </div>

                </div>

                <div class="col-lg-4 ms-auto">

                    <div class="col-lg-12">
                        <div class="card card-sm">
                            <div class="d-block">
                                <span class="badge bg-light text-light-fg preview-product">Prévia do Produto</span>
                                <img src="<?= INCLUDE_PATH . "assets/preview-image/product.jpg"; ?>" class="card-img-top" id="card-img-preview">
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span class="badge bg-default text-default-fg" id="link-preview"> <?= INCLUDE_PATH; ?>pagina-do-produto </span>
                                        <h3 id="title-preview">Título do Produto</h3>
                                        <div id="price-preview" class="text-secondary">R$ 0,00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </form>
    </div>
</div>

<!-- jQuery Validation, Input Mask, and Validation Script -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script>
    $(document).ready(function () {
        // Dropzone
        let uploadedFiles = 0;
        let maxFiles = 5;
        let firstImageSet = false; // Para garantir que a prévia seja definida apenas uma vez

        let myDropzone = new Dropzone("#dropzone-custom", {
            autoProcessQueue: false,
            url: "<?php echo INCLUDE_PATH_ADMIN; ?>back-end/product-image.php", // Nenhum envio será feito
            maxFilesize: 2,
            acceptedFiles: "image/jpeg,image/png,image/webp",
            maxFiles: maxFiles,
            addRemoveLinks: false,
            dictDefaultMessage: "Arraste e solte as imagens aqui ou clique para selecionar",
            dictInvalidFileType: "Somente arquivos .jpg e .png são permitidos.",
            dictFileTooBig: "A imagem não pode ultrapassar 2MB.",
            dictMaxFilesExceeded: "Você só pode enviar até " + maxFiles + " imagens.",
            parallelUploads: 1000,

            init: function () {
                this.on("addedfile", function (file) {
                    if (uploadedFiles >= maxFiles) {
                        this.removeFile(file);
                        alert("Você já atingiu o limite de " + maxFiles + " imagens disponíveis.");
                        return;
                    }

                    uploadedFiles++;

                    // Garante que a primeira imagem da fila será usada como prévia
                    if (!firstImageSet && myDropzone.files.length > 0) {
                        setPreviewImage(myDropzone.files[0]); // Usa a primeira imagem da fila
                    }

                    let removeButton = document.createElement("button");
                    removeButton.innerHTML = "X";
                    removeButton.classList.add("dz-remove-custom");

                    removeButton.addEventListener("click", function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        myDropzone.removeFile(file);
                    });

                    file.previewElement.appendChild(removeButton);
                });

                this.on("removedfile", function () {
                    uploadedFiles--;

                    // Se a imagem removida era a prévia, definir outra
                    if (myDropzone.files.length > 0) {
                        setPreviewImage(myDropzone.files[0]); // Sempre usa a primeira imagem restante
                    } else {
                        resetPreviewImage(); // Reseta para a imagem padrão se não houver mais imagens
                    }
                });

                // Evento de sucesso ou finalização do envio
                this.on("complete", function(file) {
                    // Verifica se todos os arquivos foram enviados
                    if (myDropzone.getUploadingFiles().length === 0 && myDropzone.getQueuedFiles().length === 0) {
                        // Recarrega a página após o envio
                        location.reload();
                    }
                });
            }
        });

        function setPreviewImage(file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("card-img-preview").src = e.target.result;
                firstImageSet = true;
            };
            reader.readAsDataURL(file);
        }

        function resetPreviewImage() {
            document.getElementById("card-img-preview").src = "<?= INCLUDE_PATH . "assets/preview-image/product.jpg"; ?>"; // Substitua pelo caminho da imagem padrão
            firstImageSet = false;
        }

        // Adiciona um método personalizado ao jQuery Validator
        $.validator.addMethod("urlSuffix", function(value, element) {
            // Expressão regular para validar apenas o sufixo da URL (sem domínio)
            var urlRegex = /^[a-zA-Z0-9\-._~:/?#[\]@!$&'()*+,;=]+$/;
            return this.optional(element) || urlRegex.test(value);
        }, "Por favor, insira um sufixo de URL válido");

        jQuery.validator.addMethod("price", function(value, element) {
            var valor = value.replace(".",",").replace(",",".");
            return this.optional(element) || (parseFloat(valor) >= 0.01);
        }, "Valor tem que maior que 0,01");

        $("#createProduct").validate({
            rules: {
                nome: {
                    required: true,
                    minlength: 3
                },
                titulo: {
                    required: true,
                    minlength: 3
                },
                estoque: {
                    required: true
                },
                preco: {
                    required: true,
                    price: true
                },
                link: {
                    required: true,
                    urlSuffix: true
                },
                seo_descricao: {
                    minlength: 10
                }
            },
            messages: {
                nome: {
                    required: "Por favor, insira o nome do produto.",
                    minlength: "O nome deve ter pelo menos 3 caracteres."
                },
                titulo: {
                    required: "Por favor, insira o título do produto.",
                    minlength: "O título deve ter pelo menos 3 caracteres."
                },
                estoque: {
                    required: "Informe o estoque do produto."
                },
                preco: {
                    required: "Informe o preço do produto.",
                    price: "Valor tem que maior que 0,01"
                },
                link: {
                    required: "Informe o link do produto.",
                    urlSuffix: "Por favor, insira apenas o caminho do link sem o domínio"
                },
                seo_descricao: {
                    minlength: "A descrição deve ter pelo menos 10 caracteres."
                }
            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.next("label"));
                } else if (element.prop("type") === "select-one") {
                    error.insertAfter(element.next("span.select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).addClass("is-valid").removeClass("is-invalid");
            },
            submitHandler: function(form) {
                // Impede o envio padrão do formulário
                event.preventDefault(); 

                // Define os botões como variáveis
                var btnSubmit = $("#btnSubmit");
                var btnLoader = $("#btnLoader");

                // Desabilitar botão submit e habilitar loader
                btnSubmit.prop("disabled", true).addClass("d-none");
                btnLoader.removeClass("d-none");

                // Cria um objeto FormData a partir do formulário
                var formData = new FormData(form);

                // Adiciona um novo campo
                formData.append("action", "cadastrar-produto");

                // Adiciona imagens ao formulário
                myDropzone.files.forEach(file => {
                    formData.append('imagens[]', file); // Adiciona cada imagem ao FormData
                });

                // Realiza o AJAX para enviar os dados
                $.ajax({
                    url: '<?= INCLUDE_PATH_ADMIN; ?>back-end/create-product.php', // Substitua pelo URL do seu endpoint
                    type: 'POST',
                    data: formData,
                    processData: false, // Impede que o jQuery processe os dados
                    contentType: false, // Impede que o jQuery defina o Content-Type
                    success: function (response) {
                        if (response.status == "success") {
                            // Sucesso na resposta do servidor
                            window.location.href = "<?= INCLUDE_PATH_ADMIN; ?>produtos";
                        } else {
                            // console.error("Erro no AJAX:", status, error);

                            // Caso contrário, exibe a mensagem de erro
                            $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                            $("#createProduct").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        }
                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro no AJAX:", status, error);

                        // Caso haja erro na requisição, exibe uma mensagem de erro
                        $(".alert").remove(); // Remove qualquer mensagem de erro anterior
                        $("#createProduct").before('<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">Ocorreu um erro, tente novamente mais tarde.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                        btnSubmit.prop("disabled", false).removeClass("d-none");
                        btnLoader.addClass("d-none");
                    }
                });
            }
        });
    });
</script>

<!-- Máscara de Preço -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        $('#preco').mask("#.##0,00", {reverse: true});
        $('.mask-money').mask("#.##0,00", {reverse: true});
    });
</script>

<!-- Tipo de frete -->
<script>
    $(document).ready(function() {
        $('input[name="freight_type"]').on('change', function() {
            if ($(this).val() === 'fixed') {
                $('#divFreightValue').slideDown();
                $('#freight_value').prop('disabled', false);
                $('#divFreightDimension').slideUp();
                $('#freight_dimension_id').prop('disabled', true).val('');
            } else {
                $('#divFreightValue').slideUp();
                $('#freight_value').prop('disabled', true).val('');
                $('#divFreightDimension').slideDown();
                $('#freight_dimension_id').prop('disabled', false);
            }
        });
    });
</script>

<!-- Link do Produto -->
<script>
    $(document).ready(function () {
        function formatarLink(texto) {
            return texto.normalize('NFD') // Remove acentos
                        .replace(/[\u0300-\u036f]/g, '') // Remove diacríticos
                        .replace(/~/g, '') // Substitui "~"
                        .replace(/\s+/g, '-') // Substitui espaços por "-"
                        .toLowerCase(); // Converte para minúsculas
        }

        $('#titulo').on('input', function () {
            let linkFormatado = formatarLink($(this).val());
            $('#link').val(linkFormatado); // Insere no campo #link
            $('#link-preview').text(linkFormatado); // Insere na pré-visualização
        });

        $('#link').on('input', function () {
            $(this).val(formatarLink($(this).val())); // Garante a formatação no campo link
            $('#link-preview').text($(this).val()); // Atualiza a pré-visualização
        });
    });
</script>

<!-- Prévia do Produto -->
<script>
    $(document).ready(function() {
        function atualizarPrevia() {
            let urlProduto = "<?= INCLUDE_PATH; ?>";
            let titulo = $("#titulo").val() || "Título do Produto";
            let preco = $("#preco").val() 
                ? `R$ ${$("#preco").val()}` 
                : "R$ 0,00";
            let link = $("#link").val() || "pagina-do-produto";

            $(".preview-product").text("Prévia do Produto");
            $("#link-preview").text(urlProduto + link);
            $("#title-preview").text(titulo);
            $("#price-preview").text(preco);
        }

        $("#titulo, #preco, #link").on("input", atualizarPrevia);
    });
</script>

<script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/hugerte.min.js"></script>
<script src="<?php echo INCLUDE_PATH; ?>dist/libs/hugerte/langs/pt_BR.js"></script>
<script>
    hugerte.init({
        selector: '#descricao',
        language: 'pt_BR',
        plugins: 'accordion advlist anchor autolink autosave charmap code codesample directionality emoticons fullscreen help image insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table template visualblocks visualchars wordcount',
    });
</script>

<!-- Dropzone de Imagens -->
<script>
    // document.addEventListener("DOMContentLoaded", function () {
    //     let uploadedFiles = 0;
    //     let maxFiles = 5;
    //     let firstImageSet = false; // Para garantir que a prévia seja definida apenas uma vez

    //     let myDropzone = new Dropzone("#dropzone-custom", {
    //         autoProcessQueue: false,
    //         url: "<?php echo INCLUDE_PATH_ADMIN; ?>back-end/product-image.php", // Nenhum envio será feito
    //         maxFilesize: 2,
    //         acceptedFiles: "image/jpeg, image/png",
    //         maxFiles: maxFiles,
    //         addRemoveLinks: false,
    //         dictDefaultMessage: "Arraste e solte as imagens aqui ou clique para selecionar",
    //         dictInvalidFileType: "Somente arquivos .jpg e .png são permitidos.",
    //         dictFileTooBig: "A imagem não pode ultrapassar 2MB.",
    //         dictMaxFilesExceeded: "Você só pode enviar até " + maxFiles + " imagens.",
    //         parallelUploads: 1000,

    //         init: function () {
    //             this.on("addedfile", function (file) {
    //                 if (uploadedFiles >= maxFiles) {
    //                     this.removeFile(file);
    //                     alert("Você já atingiu o limite de " + maxFiles + " imagens disponíveis.");
    //                     return;
    //                 }

    //                 uploadedFiles++;

    //                 // Garante que a primeira imagem da fila será usada como prévia
    //                 if (!firstImageSet && myDropzone.files.length > 0) {
    //                     setPreviewImage(myDropzone.files[0]); // Usa a primeira imagem da fila
    //                 }

    //                 let removeButton = document.createElement("button");
    //                 removeButton.innerHTML = "X";
    //                 removeButton.classList.add("dz-remove-custom");

    //                 removeButton.addEventListener("click", function (event) {
    //                     event.preventDefault();
    //                     event.stopPropagation();
    //                     myDropzone.removeFile(file);
    //                 });

    //                 file.previewElement.appendChild(removeButton);
    //             });

    //             this.on("removedfile", function () {
    //                 uploadedFiles--;

    //                 // Se a imagem removida era a prévia, definir outra
    //                 if (myDropzone.files.length > 0) {
    //                     setPreviewImage(myDropzone.files[0]); // Sempre usa a primeira imagem restante
    //                 } else {
    //                     resetPreviewImage(); // Reseta para a imagem padrão se não houver mais imagens
    //                 }
    //             });

    //             // Evento de sucesso ou finalização do envio
    //             this.on("complete", function(file) {
    //                 // Verifica se todos os arquivos foram enviados
    //                 if (myDropzone.getUploadingFiles().length === 0 && myDropzone.getQueuedFiles().length === 0) {
    //                     // Recarrega a página após o envio
    //                     location.reload();
    //                 }
    //             });
    //         }
    //     });

    //     // Ação do botão "Adicionar"
    //     document.querySelector('button[name="btnCreateProduct"]').addEventListener("click", function(event) {
    //         event.preventDefault(); // Impede o comportamento padrão de envio do formulário
    //         if (myDropzone.files.length > 0) {
    //             myDropzone.options.url = "<?php echo INCLUDE_PATH_ADMIN; ?>back-end/product-image.php"; // Define a URL para o envio

    //             // Envia os arquivos manualmente quando o botão for clicado
    //             myDropzone.processQueue(); // Envia todos os arquivos na fila
    //         } else {
    //             alert("Adicione pelo menos uma imagem antes de enviar.");
    //         }
    //     });

    //     function setPreviewImage(file) {
    //         let reader = new FileReader();
    //         reader.onload = function (e) {
    //             document.getElementById("card-img-preview").src = e.target.result;
    //             firstImageSet = true;
    //         };
    //         reader.readAsDataURL(file);
    //     }

    //     function resetPreviewImage() {
    //         document.getElementById("card-img-preview").src = "<?= INCLUDE_PATH . "assets/preview-image/product.jpg"; ?>"; // Substitua pelo caminho da imagem padrão
    //         firstImageSet = false;
    //     }
    // });
</script>
