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
                    Sobre a sua Instituição
                </h2>
                <div class="text-secondary mt-1">Altere as informações da sua instituição aqui!</div>
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
                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                        <div class="card-header">
                            <h4 class="card-title">Sobre a Instituição</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label for="nome" class="col-3 col-form-label required">Nome da sua Instituição</label>
                                <div class="col">
                                    <input name="nome" id="nome"
                                        type="text" class="form-control" value="<?php echo $nome; ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="descricao" class="col-3 col-form-label required">Descrição da Instituição</label>
                                <div class="col">
                                    <textarea name="descricao" id="descricao"><?php echo $descricao; ?></textarea>
                                    <small class="form-hint">Preencha o campo com uma breve descrição sobre sua instituição. Esta informação ficará disponível no canto inferior direito do checkout.</small>
                                </div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        let options = {
                                            selector: '#descricao',
                                            height: 300,
                                            menubar: false,
                                            disabled: <?php echo $editorDisabled; ?>,
                                            statusbar: false,
                                                license_key: 'gpl',
                                            plugins: [
                                                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
                                                'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                                            ],
                                            toolbar: 'undo redo | formatselect | ' +
                                                'bold italic backcolor | alignleft aligncenter ' +
                                                'alignright alignjustify | bullist numlist outdent indent | ' +
                                                'removeformat',
                                            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }'
                                        }
                                        if (localStorage.getItem("tablerTheme") === 'dark') {
                                            options.skin = 'oxide-dark';
                                            options.content_css = 'dark';
                                        }
                                        tinyMCE.init(options);
                                    })
                                </script>
                            </div>
                            <div class="row">
                                <label for="doacoes" class="col-3 col-form-label pt-0">Exibir valores arrecadados?</label>
                                <span class="col">
                                    <label for="doacoes" class="form-check form-switch form-switch-3">
                                        <input name="doacoes" id="doacoes" type="checkbox" class="form-check-input" <?= ($card_doacoes == 1) ? "checked" : "" ?>>
                                        <span class="form-check-label form-check-label-on">Sim</span>
                                        <span class="form-check-label form-check-label-off">Não</span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" name="btnUpdAbout" class="btn btn-primary ms-auto">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

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
            </style>

            <div class="col-lg-12">
                <div class="card">
                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/imagens.php" method="post" enctype="multipart/form-data" autocomplete="off" novalidate>
                        <div class="card-header">
                            <h4 class="card-title">Adicionar Imagem</h4>
                        </div>
                        <div class="card-body">
                            <?php 
                                if(isset($_SESSION['msgaddcad'])){
                                    echo $_SESSION['msgaddcad'];
                                    unset($_SESSION['msgaddcad']);
                                }
                            ?>
                            <?php
                                // Nome da tabela para a busca
                                $tabela = 'tb_imagens';
                                
                                // Preparando a consulta SQL
                                $stmt = $conn->prepare("SELECT * FROM $tabela ORDER BY id DESC");
                                
                                // Executando a consulta
                                $stmt->execute();
                                
                                // Obtendo os resultados da busca
                                $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Consulta SQL para recuperar informações das tabelas
                                $sql = "SELECT COUNT(id) FROM $tabela";
                                $stmt = $conn->query($sql);
                                
                                // Obter o número de linhas
                                $numLinhas = $stmt->fetchColumn();
                                $novaLinha = $numLinhas + 1;
                                
                                // Loop através dos resultados e exibir todas as colunas
                                if ($numLinhas < 4):
                            ?>
                                <div class="mb-3 row">
                                    <label for="image" class="col-3 col-form-label required">Imagem</label>
                                    <div class="col">
                                        <div class="dropzone" id="dropzone-custom">
                                            <div class="fallback">
                                                <input name="file" type="file" />
                                            </div>
                                            <div class="dz-message">
                                                <h3 class="dropzone-msg-title">Arraste e solte as imagens aqui</h3>
                                                <span class="dropzone-msg-desc">Essa imagem será mostrada no checkout.</span>
                                            </div>
                                        </div>
                                        <small class="form-hint text-end">Imagem em <b>.png, .jpg, .jpeg</b> até <b>2MB</b> cada com <b>500px X 160px</b>.</small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info" role="alert">
                                    <div class="d-flex">
                                        <div>
                                            <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
                                        </div>
                                        <div>
                                            Só é possível adicionar até 4 imagens.
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($numLinhas < 4): ?>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="<?php echo ($numLinhas < 4) ? 'submit' : 'button'; ?>" name="btnAddCard" class="btn <?php echo ($numLinhas < 4) ? 'btn-primary' : 'btn-secondary'; ?> ms-auto">Adicionar</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <?php if ($numLinhas < 4): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    let uploadedFiles = 0; 
                    let maxFiles = 4 - <?= $numLinhas; ?>; 

                    // Configura o Dropzone, mas sem envio automático
                    let myDropzone = new Dropzone("#dropzone-custom", {
                        autoProcessQueue: false,
                        url: "<?php echo INCLUDE_PATH_ADMIN; ?>back-end/imagens.php", // A URL para o envio
                        maxFilesize: 2, 
                        acceptedFiles: "image/jpeg, image/png",
                        maxFiles: maxFiles,
                        addRemoveLinks: false, 
                        dictDefaultMessage: "Arraste e solte as imagens aqui ou clique para selecionar",
                        dictInvalidFileType: "Somente arquivos .jpg e .png são permitidos.",
                        dictFileTooBig: "A imagem não pode ultrapassar 2MB.",
                        dictMaxFilesExceeded: "Você só pode enviar até " + maxFiles + " imagens.",
                        parallelUploads: 1000, // Define o número máximo de arquivos enviados ao mesmo tempo (ou seja, todos os arquivos)

                        init: function() {
                            this.on("addedfile", function(file) {
                                if (uploadedFiles >= maxFiles) {
                                    this.removeFile(file);
                                    alert("Você já atingiu o limite de " + maxFiles + " imagens disponíveis.");
                                    return;
                                }

                                uploadedFiles++;

                                let removeButton = document.createElement("button");
                                removeButton.innerHTML = "X";
                                removeButton.classList.add("dz-remove-custom");

                                removeButton.addEventListener("click", function(event) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    myDropzone.removeFile(file);
                                });

                                file.previewElement.appendChild(removeButton);
                            });

                            this.on("removedfile", function() {
                                uploadedFiles--;
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

                    // Ação do botão "Adicionar"
                    document.querySelector('button[name="btnAddCard"]').addEventListener("click", function(event) {
                        event.preventDefault(); // Impede o comportamento padrão de envio do formulário
                        if (myDropzone.files.length > 0) {
                            myDropzone.options.url = "<?php echo INCLUDE_PATH_ADMIN; ?>back-end/imagens.php"; // Define a URL para o envio

                            // Envia os arquivos manualmente quando o botão for clicado
                            myDropzone.processQueue(); // Envia todos os arquivos na fila
                        } else {
                            alert("Adicione pelo menos uma imagem antes de enviar.");
                        }
                    });
                });
            </script>
            <?php endif; ?>

            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_imagens';
                
                // Consulta SQL para recuperar informações das tabelas
                $sql = "SELECT COUNT(id) FROM $tabela";
                $stmt = $conn->query($sql);
                
                // Obter o número de linhas
                $numLinhas = $stmt->fetchColumn();
                
                // Consulta SQL para selecionar todas as colunas
                $sql = "SELECT * FROM $tabela ORDER BY id DESC";
                
                // Preparar e executar a consulta
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                
                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($resultados):
            ?>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Imagens existentes</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                            if(isset($_SESSION['msgupdcad'])){
                                echo $_SESSION['msgupdcad'];
                                unset($_SESSION['msgupdcad']);
                            }
                        ?>
                        <?php foreach ($resultados as $imagem): ?>
                        <fieldset class="form-fieldset gap-3">
                            <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/imagens.php" method="post" enctype="multipart/form-data">
                                <input type="file" name="imagem" id="input<?php echo $imagem['id']; ?>" accept=".jpg, .jpeg, .png" value="<?php echo $imagem['imagem']; ?>" style="display: none;">
                                <div class="row align-items-center mt-0">
                                    <div class="col-1 row g-2 g-md-3 mt-0">
                                        <div class="col-12 mt-0">
                                            <a data-fslightbox="gallery" href="<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $imagem['imagem']; ?>">
                                                <!-- Photo -->
                                                <div class="img-responsive img-responsive-1x1 rounded-3 border" style="background-image: url(<?php echo INCLUDE_PATH; ?>assets/img/<?php echo $imagem['imagem']; ?>)"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-auto row align-items-center">
                                        <div class="col-auto">
                                            <label for="input<?php echo $imagem['id']; ?>" class="btn btn-1">Alterar Imagem</label>
                                        </div>
                                        <div class="col-auto">
                                            <a href="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/apagar-imagem.php?id=<?php echo $imagem['id']; ?>" class="btn btn-ghost-danger btn-3">Deletar</a>
                                        </div>
                                        <small class="form-hint mt-2">Imagem em <b>.png, .jpg, .jpeg</b> até <b>2MB</b> cada com <b>500px X 160px</b>.</small>
                                    </div>
                                </div>

                                <input type="hidden" name="ids[]" value="<?php echo $imagem['id']; ?>">
                            </form>
                        </fieldset>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    // Seleciona todos os inputs do tipo file
                    document.querySelectorAll('input[type="file"]').forEach(input => {
                        input.addEventListener("change", function () {
                            if (this.files.length > 0) {
                                this.closest("form").submit(); // Envia o formulário automaticamente
                            }
                        });
                    });
                });
            </script>

            <div class="col-lg-12">
                <div class="card">
                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                        <div class="card-header">
                            <h4 class="card-title">PIX</h4>
                        </div>
                        <div class="card-body">
                            <?php 
                                if(isset($_SESSION['msgupdcad'])){
                                    echo $_SESSION['msgupdcad'];
                                    unset($_SESSION['msgupdcad']);
                                }
                            ?>
                            <div class="mb-3 row">
                                <label for="pix_tipo" class="col-3 col-form-label required">Tipo de Chave PIX</label>
                                <div class="col">
                                    <select name="pix_tipo" id="pix_tipo" class="form-control">
                                        <option value="" <?php echo !isset($pix_tipo) ? 'selected' : ''; ?> disabled>Selecione uma Opção</option>
                                        <option value="cpf" <?php echo isset($pix_tipo) && $pix_tipo == 'cpf' ? 'selected' : ''; ?>>CPF</option>
                                        <option value="cnpj" <?php echo isset($pix_tipo) && $pix_tipo == 'cnpj' ? 'selected' : ''; ?>>CNPJ</option>
                                        <option value="email" <?php echo isset($pix_tipo) && $pix_tipo == 'email' ? 'selected' : ''; ?>>E-mail</option>
                                        <option value="telefone" <?php echo isset($pix_tipo) && $pix_tipo == 'telefone' ? 'selected' : ''; ?>>Telefone</option>
                                        <option value="aleatoria" <?php echo isset($pix_tipo) && $pix_tipo == 'aleatoria' ? 'selected' : ''; ?>>Chave Aleatória</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="pix_chave" class="col-3 col-form-label required">Chave PIX</label>
                                <div class="col">
                                    <input name="pix_chave" id="pix_chave"
                                        type="text" class="form-control" value="<?php echo $pix_chave; ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="pix_identificador_transacao" class="col-3 col-form-label required">Identificador da Transação</label>
                                <div class="col">
                                    <input name="pix_identificador_transacao" id="pix_identificador_transacao"
                                        type="text" class="form-control" value="<?php echo $pix_identificador_transacao; ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="pix_valor" class="col-3 col-form-label required">Valor</label>
                                <div class="col">
                                    <input name="pix_valor" id="pix_valor"
                                        type="number" class="form-control" value="<?php echo $pix_valor; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="pix_exibir" class="col-3 col-form-label pt-0">Exibir na página do checkout?</label>
                                <span class="col">
                                    <label for="pix_exibir" class="form-check form-switch form-switch-3">
                                        <input name="pix_exibir" id="pix_exibir" type="checkbox" class="form-check-input" <?= ($pix_exibir == 1) ? "checked" : "" ?>>
                                        <span class="form-check-label form-check-label-on">Sim</span>
                                        <span class="form-check-label form-check-label-off">Não</span>
                                    </label>
                                </span>
                            </div>
                            <?php if ($pix_exibir): ?>
                            <div class="row my-3">
                                <label for="pix_qr_code" class="col-3 col-form-label pt-0">QR Code</label>
                                <div class="col">
                                    <div class="col-3 row mt-0">
                                        <div class="col-12 mt-0">
                                            <a data-fslightbox="gallery" href="<?php echo $pix_imagem_base64; ?>">
                                                <!-- Photo -->
                                                <div class="img-responsive img-responsive-1x1 rounded-3 border" style="background-image: url(<?php echo $pix_imagem_base64; ?>)"></div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" name="btnUpdPix" class="btn btn-primary ms-auto">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <form id="donations_form" action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                        <div class="card-body">
                            <h3 class="card-title">Valores das doações</h3>
                            <p class="card-subtitle">Insira 5 opçoes de valores para cada modalidade, uma em cada box.</p>
                            
                            <div class="mb-3 row">
                                <label class="col-3 col-form-label required">Valores mensais</label>
                                <div class="col row">
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="monthly_1" id="monthly_1" type="number" class="form-control" value="<?php echo $monthly_1; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="monthly_2" id="monthly_2" type="number" class="form-control" value="<?php echo $monthly_2; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="monthly_3" id="monthly_3" type="number" class="form-control" value="<?php echo $monthly_3; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="monthly_4" id="monthly_4" type="number" class="form-control" value="<?php echo $monthly_4; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="monthly_5" id="monthly_5" type="number" class="form-control" value="<?php echo $monthly_5; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-3 col-form-label required">Valores anuais</label>
                                <div class="col row">
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="yearly_1" id="yearly_1" type="number" class="form-control" value="<?php echo $yearly_1; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="yearly_2" id="yearly_2" type="number" class="form-control" value="<?php echo $yearly_2; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="yearly_3" id="yearly_3" type="number" class="form-control" value="<?php echo $yearly_3; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="yearly_4" id="yearly_4" type="number" class="form-control" value="<?php echo $yearly_4; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="yearly_5" id="yearly_5" type="number" class="form-control" value="<?php echo $yearly_5; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-3 col-form-label required">Valores únicos</label>
                                <div class="col row">
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="once_1" id="once_1" type="number" class="form-control" value="<?php echo $once_1; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="once_2" id="once_2" type="number" class="form-control" value="<?php echo $once_2; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="once_3" id="once_3" type="number" class="form-control" value="<?php echo $once_3; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="once_4" id="once_4" type="number" class="form-control" value="<?php echo $once_4; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                    <div class="col-lg-2 col-sm-6 mb-2">
                                        <input name="once_5" id="once_5" type="number" class="form-control" value="<?php echo $once_5; ?>" onkeydown="if(event.key==='.' || event.key===','){event.preventDefault();}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" name="btnUpdDonations" class="btn btn-primary ms-auto" form="donations_form">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h3 class="card-title">Incorporar em um site</h3>
                        <p class="card-text">
                        <div class="alert alert-dark" role="alert">
                            <textarea id="embed_wrapper" class="m-0 p-0" disabled style="border: none; overflow: hidden; resize: none; width: 100%; background: transparent; text-align: left">
                                <iframe id="embed" src="<?php echo INCLUDE_PATH; ?>" frameborder="0" width="100%" height="1400"></iframe>
                            </textarea>
                        </div>
                        </p>
                        <button id="btnIframe" class="btn btn-primary">Copiar código</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php if (!verificaPermissao($_SESSION['user_id'], 'sobre', 'update', $conn)): ?>
</fieldset>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        function aplicarMascara() {
            var tipo = $('#pix_tipo').val();
            $('#pix_chave').unmask();
            
            if (tipo === 'cpf') {
                $('#pix_chave').mask('000.000.000-00');
                $('#pix_chave').attr('type', 'text');
            } else if (tipo === 'cnpj') {
                $('#pix_chave').mask('00.000.000/0000-00');
                $('#pix_chave').attr('type', 'text');
            } else if (tipo === 'telefone') {
                $('#pix_chave').mask('(00) 00000-0000');
                $('#pix_chave').attr('type', 'text');
            } else if (tipo === 'email') {
                $('#pix_chave').attr('type', 'email');
            } else if (tipo === 'aleatoria') {
                $('#pix_chave').attr('type', 'text');
            }
        }
        
        $('#pix_tipo').change(aplicarMascara);
        aplicarMascara();
    });
</script>

<script>
    const checkboxes = document.querySelectorAll('[name^="d"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const inputId = this.getAttribute('data-input-id');
            const input = document.getElementById(inputId);
            
            if (this.checked) {
                input.disabled = true;
            } else {
                input.disabled = false;
            }
        });
    });
</script>
<script>
    
function getCepData()
{
    let cep = $('#cep').val();
    cep = cep.replace(/\D/g, "");
    if(cep.length<8)
    {
        $("#div-errors-price").html("CEP deve conter no mínimo 8 dígitos").slideDown('fast').effect( "shake" );
        $("#cep").addClass('is-invalid').focus();
        return;
    }
    $("#cep").removeClass('is-invalid');
    $("#div-errors-price").slideUp('fast');


    if(cep != "")
    {
        $("#rua").val("Carregando...");
        $("#bairro").val("Carregando...");
        $("#cidade").val("Carregando...");
        $("#estado").val("...");
        $.getJSON( "https://viacep.com.br/ws/"+cep+"/json/", function( data )
        {
            $("#rua").val(data.logradouro);
            $("#bairro").val(data.bairro);
            $("#cidade").val(data.localidade);
            $("#estado").val(data.uf);
            $("#numero").focus();
        }).fail(function()
        {
            $("#rua").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#estado").val(" ");
        });
    }
}
</script>
<script>
    const colorPicker = document.getElementById('colorPicker');
    const colorCodeInput = document.getElementById('colorCode');

    //colorPicker.addEventListener('input', updateColorPreview);
    //colorCodeInput.addEventListener('input', updateColorFromCode);

    /*function updateColorPreview(event) {
        const selectedColor = event.target.value;
        colorCodeInput.value = selectedColor;
    }*/

    /*function updateColorFromCode() {
        const colorCode = colorCodeInput.value;
        if (isValidHexColorCode(colorCode)) {
            colorPicker.value = colorCode;
        }
    }*/

    function isValidHexColorCode(code) {
        return /^#([0-9A-F]{3}){1,2}$/i.test(code);
    }
</script>
<script>
    const hoverPicker = document.getElementById('hoverPicker');
    const hoverCodeInput = document.getElementById('hoverCode');

    //hoverPicker.addEventListener('input', updateHoverPreview);
    //hoverCodeInput.addEventListener('input', updateHoverFromCode);

    /*function updateHoverPreview(event) {
        const selectedHover = event.target.value;
        hoverCodeInput.value = selectedHover;
    }*/

    /*function updateHoverFromCode() {
        const hoverCode = hoverCodeInput.value;
        if (isValidHexHoverCode(hoverCode)) {
            hoverPicker.value = hoverCode;
        }
    }*/

    function isValidHexHoverCode(code) {
        return /^#([0-9A-F]{3}){1,2}$/i.test(code);
    }
</script>
<script>
    const loadBtnPicker = document.getElementById('loadBtnPicker');
    const loadBtnCodeInput = document.getElementById('loadBtnCode');

    //loadBtnPicker.addEventListener('input', updateLoadBtnPreview);
    //loadbtnCodeInput.addEventListener('input', updateLoadBtnFromCode);

    /*function updateLoadBtnPreview(event) {
        const selectedLoadBtn = event.target.value;
        loadBtnCodeInput.value = selectedLoadBtn;
    }*/

    /*function updateLoadBtnFromCode() {
        const loadBtnCode = loadBtnCodeInput.value;
        if (isValidHexHoverCode(loadBtnCode)) {
            loadBtnPicker.value = loadBtnCode;
        }
    }*/

    function isValidHexHoverCode(code) {
        return /^#([0-9A-F]{3}){1,2}$/i.test(code);
    }
</script>
<script>
    const colorPickerRGB = document.getElementById('colorPickerRGB');
    const redInput = document.getElementById('red');
    const greenInput = document.getElementById('green');
    const blueInput = document.getElementById('blue');

    //colorPickerRGB.addEventListener('input', updateColorFromPicker);
    /*redInput.addEventListener('input', updateColorFromRGBInputs);
    greenInput.addEventListener('input', updateColorFromRGBInputs);
    blueInput.addEventListener('input', updateColorFromRGBInputs);*/

    /*function updateColorFromPicker(event) {
      const selectedColor = event.target.value;
      const rgbValues = hexToRGB(selectedColor);
      redInput.value = rgbValues.r;
      greenInput.value = rgbValues.g;
      blueInput.value = rgbValues.b;
      updateColorPreview();
    }*/

    /*function updateColorFromRGBInputs() {
      const redValue = parseInt(redInput.value);
      const greenValue = parseInt(greenInput.value);
      const blueValue = parseInt(blueInput.value);
      const hexColor = RGBToHex(redValue, greenValue, blueValue);
      colorPickerRGB.value = hexColor;
      updateColorPreview();
    }*/

    /*function updateColorPreview() {
      const hexColor = RGBToHex(parseInt(redInput.value), parseInt(greenInput.value), parseInt(blueInput.value));
    }*/

    function hexToRGB(hex) {
      const bigint = parseInt(hex.slice(1), 16);
      const r = (bigint >> 16) & 255;
      const g = (bigint >> 8) & 255;
      const b = bigint & 255;
      return { r, g, b };
    }

    function RGBToHex(r, g, b) {
      return `#${(1 << 24 | r << 16 | g << 8 | b).toString(16).slice(1)}`;
    }
  </script>
  <script>
    $(document).ready(function() {
        $('#btnIframe').on('click', function() {
            let iframe = $('textarea#embed_wrapper').val()
            iframe = $.trim(iframe)

            // Verifica se o navegador tem suporte à API Clipboard
            if (typeof navigator.clipboard !== 'undefined') {
                navigator.clipboard.writeText(iframe)
                $('#btnIframe').removeClass('btn-primary').addClass('btn-success').prop('disabled', true).html('Copiado!')
            } else if (typeof navigator.clipboard === 'undefined') {
                let iframeText = $("#embed_wrapper")
                iframeText.focus()
                iframeText.select()
                document.execCommand('copy')
                $('#btnIframe').removeClass('btn-primary').addClass('btn-success').prop('disabled', true).html('Copiado!')
            } else {
                $('#btnIframe').removeClass('btn-primary').addClass('btn-danger').prop('disabled', true).html('Não foi possível copiar o código!')
            }

            // Após 3 segundos, retorne o botão ao texto original
            setTimeout(function() {
                $('#btnIframe').removeClass('btn-success btn-danger').addClass('btn-primary').prop('disabled', false).html('Copiar código');
            }, 3000); // 3000 milissegundos = 3 segundos
        });
    });
  </script>
