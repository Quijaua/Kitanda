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
</style>


<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Bem-vindo <?php echo $nome; ?>
                </h2>
                <div class="text-secondary mt-1">Dados pessoais.</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">

            <div class="col-lg-12">
                <div class="card">
                    <form class="">
                        <div class="card-header">
                            <h4 class="card-title">Conta</h4>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Informações pessoais</h3>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label required">Nome</label>
                                    <input name="nome" id="nome" type="text" class="form-control" value="<?php echo $nome; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">E-mail</label>
                                    <input name="email" id="email" type="email" class="form-control" value="<?php echo $email; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label required">Telefone</label>
                                    <input name="phone" id="phone" type="tel" class="form-control" value="<?php echo $phone; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cpf" class="form-label required">CPF</label>
                                    <input name="cpf" id="cpf" type="text" class="form-control" value="<?php echo $cpf; ?>" required>
                                </div>
                            </div>

                            <h3 class="card-title">Endereço</h3>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="cep" class="form-label required">CEP</label>
                                    <input name="cep" id="cep" type="text" class="form-control" value="<?php echo $cep; ?>" onblur="getCepData()" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="endereco" class="form-label required">Endereço</label>
                                    <input name="endereco" id="endereco" type="text" class="form-control" value="<?php echo $endereco; ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="numero" class="form-label required">Número</label>
                                    <input name="numero" id="numero" type="text" class="form-control" value="<?php echo $numero; ?>" required>
                                    <label class="form-check mt-2">
                                        <input name="dNumero" id="dNumero" type="checkbox" class="form-check-input" data-input-id="numero" <?php echo ($numero == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Sem número</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cidade" class="form-label required">Cidade</label>
                                    <input name="cidade" id="cidade" type="text" class="form-control" value="<?php echo $cidade; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="uf" class="form-label required">Estado</label>
                                    <input name="uf" id="uf" type="text" class="form-control" value="<?php echo $uf; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" class="btn btn-primary ms-auto">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cobranças</h4>
                    </div>
                    <div class="card-body">
                        <?php
                            // Nome da tabela para a busca
                            $tabela = 'tb_doacoes';

                            // Consulta SQL para selecionar todas as colunas com base no ID
                            $sql = "SELECT * FROM $tabela WHERE customer_id = :asaas_id ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':asaas_id', $asaas_id);
                            $stmt->execute();

                            // Recuperar os resultados
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Else para o foreach
                            $encontrouPagamento = false; // Variável de controle

                            // Loop através dos resultados e exibir todas as colunas
                            foreach ($resultados as $usuario):
                                if ($usuario['cycle'] == ''):
                                    $null = ($usuario['forma_pagamento'] === '') ? 'selected' : '';
                                    $boleto = ($usuario['forma_pagamento'] === 'BOLETO') ? 'selected' : '';
                                    $pix = ($usuario['forma_pagamento'] === 'PIX') ? 'selected' : '';

                                    $status = ($usuario['status'] === 'PENDING') ? 'Pendente' : '';

                                    $link_pagamento = ($usuario['forma_pagamento'] === 'PIX') ? $usuario['link_pagamento'] : $usuario['link_boleto'];

                                    $data_criacao = date("d/m/Y", strtotime($usuario['data_criacao']));

                                    $vencimento_pix = (!empty($usuario['pix_expirationDate'])) ? date("d/m/Y", strtotime($usuario['pix_expirationDate'])) : "";
                                    $vencimento_boleto = date("d/m/Y", strtotime($usuario['data_vencimento']));

                                    $data_vencimento = ($usuario['forma_pagamento'] === 'PIX') ? $vencimento_pix : $vencimento_boleto;
                        ?>
                        <fieldset class="form-fieldset gap-3">
                            <form action="<?= INCLUDE_PATH; ?>back-end/editar-cobranca.php" method="post">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Status</div>
                                                <div class="datagrid-content"><?= $status; ?></div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Criado em</div>
                                                <div class="datagrid-content"><?= $data_criacao; ?></div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Vencimento</div>
                                                <div class="datagrid-content"><?= $data_vencimento; ?></div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Pagar</div>
                                                <a href="<?= $link_pagamento; ?>" target="_blank">Link pagamento</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="valor" class="form-label required">Valor</label>
                                            <input name="valor" id="valor" type="text" class="form-control" value="<?php echo $usuario['valor']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="data_vencimento" class="form-label required">Vencimento</label>
                                            <input name="data_vencimento" id="data_vencimento" type="date" class="form-control" value="<?php echo $usuario['data_vencimento']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="forma_pagamento" class="form-label required">Tipo de Chave PIX</label>
                                            <select name="forma_pagamento" id="forma_pagamento" class="form-control" required>
                                                <option value="" disabled <?= $null; ?>>-- Selecione uma forma de pagamento --</option>
                                                <option value="BOLETO" <?= $boleto; ?>>Boleto</option>
                                                <option value="PIX" <?= $pix; ?>>Pix</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="payment_ids[]" value="<?= $usuario['payment_id']; ?>">
                                <div class="d-flex mt-3">
                                    <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                    <a href="<?= INCLUDE_PATH; ?>back-end/cancelar_pagamento.php?payment_id=<?= $usuario['payment_id']; ?>" class="btn btn-danger ms-auto">Cancelar Pagamento</a>
                                </div>
                            </form>
                        </fieldset>
                        <?php
                            $encontrouPagamento = true;
                            endif;
                        ?>
                    <?php endforeach; ?>

                    <?php if (!$encontrouPagamento): ?>
                        <h3 class="card-title">Você não possui nenhuma cobrança ativa.</h3>
                    <?php endif; ?>

                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Assinaturas</h4>
                    </div>
                    <div class="card-body">
                        <?php
                            // Nome da tabela para a busca
                            $tabela = 'tb_doacoes';

                            // Consulta SQL para selecionar todas as colunas com base no ID
                            $sql = "SELECT * FROM $tabela WHERE customer_id = :asaas_id ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':asaas_id', $asaas_id);
                            $stmt->execute();

                            // Recuperar os resultados
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            // Else para o foreach
                            $encontrouAssinatura = false; // Variável de controle
                            
                            // Loop através dos resultados e exibir todas as colunas
                            foreach ($resultados as $usuario):
                                if ($usuario['cycle'] == 'MONTHLY' || $usuario['cycle'] == 'YEARLY'):
                                    $status = ($usuario['status'] === 'ACTIVE') ? 'Ativo' : '';

                                    $monthly = ($usuario['cycle'] === 'MONTHLY') ? 'selected' : '';
                                    $yearly = ($usuario['cycle'] === 'YEARLY') ? 'selected' : '';

                                    $data_criacao = date("d/m/Y", strtotime($usuario['data_criacao']));
                        ?>
                        <fieldset class="form-fieldset gap-3">
                            <form action="<?= INCLUDE_PATH; ?>back-end/editar-cobranca.php" method="post">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Status</div>
                                                <div class="datagrid-content"><?= $status; ?></div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Criado em</div>
                                                <div class="datagrid-content"><?= $data_criacao; ?></div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Bandeira do cartão</div>
                                                <div class="datagrid-content"><?= $usuario['cartao_bandeira']; ?></div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Final</div>
                                                <div class="datagrid-content"><?= $usuario['cartao_numero']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="valor" class="form-label required">Valor</label>
                                            <input name="valor" id="valor" type="text" class="form-control" value="<?php echo $usuario['valor']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cycle" class="form-label required">Ciclo</label>
                                            <select name="cycle" id="cycle" class="form-control" required>
                                                <option value="" disabled <?= $null; ?>>-- Selecione o ciclo do pagamento --</option>
                                                <option value="MONTHLY" <?= $monthly; ?>>Mensal</option>
                                                <option value="YEARLY" <?= $yearly; ?>>Anual</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="forma_pagamento" class="form-label required">Forma de pagamento</label>
                                            <select name="forma_pagamento" id="forma_pagamento" class="form-control" required>
                                                <option value="" disabled>-- Selecione uma forma de pagamento --</option>
                                                <option value="CREDIT_CARD" selected>Cartão de Crédito</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="payment_ids[]" value="<?= $usuario['payment_id']; ?>">
                                <div class="d-flex mt-3">
                                    <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                    <a href="<?= INCLUDE_PATH; ?>back-end/cancelar_assinatura.php?payment_id=<?= $usuario['payment_id']; ?>" class="btn btn-danger ms-auto">Cancelar Assinatura</a>
                                </div>
                            </form>
                        </fieldset>
                        <?php
                            $encontrouAssinatura = true;
                            endif;
                        ?>
                    <?php endforeach; ?>

                    <?php if (!$encontrouAssinatura): ?>
                        <h3 class="card-title">Você não possui nenhuma assinatura ativa.</h3>
                    <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

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
        $("#endereco").val("Carregando...");
        $("#municipio").val("Carregando...");
        $("#cidade").val("Carregando...");
        $("#uf").val("...");
        $.getJSON( "https://viacep.com.br/ws/"+cep+"/json/", function( data )
        {
            $("#endereco").val(data.logradouro);
            $("#municipio").val(data.bairro);
            $("#cidade").val(data.localidade);
            $("#uf").val(data.uf);
            $("#numero").focus();
        }).fail(function()
        {
            $("#endereco").val("");
            $("#municipio").val("");
            $("#cidade").val("");
            $("#uf").val(" ");
        });
    }
}
</script>
<!-- Mascara de Inputs -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#phone').on('input', function() {
            let inputValue = $(this).val().replace(/\D/g, ''); // Remove todos os não dígitos
            if (inputValue.length > 0) {
                inputValue = '(' + inputValue;
                if (inputValue.length > 3) {
                    inputValue = [inputValue.slice(0, 3), ') ', inputValue.slice(3)].join('');
                }
                if (inputValue.length > 10) {
                    inputValue = [inputValue.slice(0, 10), '-', inputValue.slice(10)].join('');
                }
                if (inputValue.length > 15) {
                    inputValue = inputValue.substr(0, 15);
                }
            }
            $(this).val(inputValue);
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#cpf').on('input', function() {
            let inputValue = $(this).val().replace(/\D/g, ''); // Remove todos os não dígitos
            if (inputValue.length > 0) {
                inputValue = [inputValue.slice(0, 3), '.', inputValue.slice(3)].join('');
                if (inputValue.length > 7) {
                    inputValue = [inputValue.slice(0, 7), '.', inputValue.slice(7)].join('');
                }
                if (inputValue.length > 11) {
                    inputValue = [inputValue.slice(0, 11), '-', inputValue.slice(11)].join('');
                }
                if (inputValue.length > 14) {
                    inputValue = inputValue.substr(0, 14);
                }
            }
            $(this).val(inputValue);
        });
    });
</script>
