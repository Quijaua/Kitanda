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

<?php
    $stmt = $conn->prepare("SELECT * FROM tb_clientes WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    Bem-vindo <?php echo $usuario['nome']; ?>
                </h1>
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
                            <h2 class="card-title">Conta</h2>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Informações pessoais</h3>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label disabled">Nome</label>
                                    <input name="nome" id="nome" type="text" class="form-control" value="<?php echo $usuario['nome']; ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label disabled">E-mail</label>
                                    <input name="email" id="email" type="email" class="form-control" value="<?php echo $usuario['email']; ?>" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label disabled">Telefone</label>
                                    <input name="phone" id="phone" type="tel" class="form-control" value="<?php echo $usuario['phone']; ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cpf" class="form-label disabled">CPF</label>
                                    <input name="cpf" id="cpf" type="text" class="form-control" value="<?php echo $usuario['cpf']; ?>" disabled>
                                </div>
                            </div>

                            <h3 class="card-title">Endereço</h3>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="cep" class="form-label disabled">CEP</label>
                                    <input name="cep" id="cep" type="text" class="form-control" value="<?php echo $usuario['cep']; ?>" onblur="getCepData()" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="endereco" class="form-label disabled">Endereço</label>
                                    <input name="endereco" id="endereco" type="text" class="form-control" value="<?php echo $usuario['endereco']; ?>" disabled>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="numero" class="form-label disabled">Número</label>
                                    <input name="numero" id="numero" type="text" class="form-control" value="<?php echo $usuario['numero']; ?>" disabled>
                                    <label class="form-check mt-2">
                                        <input name="dNumero" id="dNumero" type="checkbox" class="form-check-input" data-input-id="numero" <?php echo ($usuario['numero'] == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Sem número</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cidade" class="form-label disabled">Cidade</label>
                                    <input name="cidade" id="cidade" type="text" class="form-control" value="<?php echo $usuario['cidade']; ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="uf" class="form-label disabled">Estado</label>
                                    <input name="uf" id="uf" type="text" class="form-control" value="<?php echo $usuario['uf']; ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </form>
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

        let inputValue = $('#cpf').val().replace(/\D/g, ''); // Remove todos os não dígitos
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

        $('#cpf').val(inputValue);
    });
</script>
