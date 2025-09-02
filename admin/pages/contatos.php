<?php
    $read = verificaPermissao($_SESSION['user_id'], 'rodape', 'read', $conn);
    $disabledRead = !$read ? 'disabled' : '';

    $update = verificaPermissao($_SESSION['user_id'], 'rodape', 'update', $conn);
    $disabledUpdate = !$update ? 'disabled' : '';
?>

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Contatos
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
        <div class="row row-cards">

            <?php if (!getNomePermissao($_SESSION['user_id'], $conn) === 'Administrador'): ?>
            <div class="col-lg-12">
                <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
            </div>
            <?php exit; endif; ?>

            <?php if (!$read): ?>
            <div class="col-12">
                <div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>
            </div>
            <?php exit; endif; ?>

            <?php if (!$update): ?>
            <div class="col-12">
                <div class="alert alert-info">Você pode visualizar os detalhes desta página, mas não pode editá-los.</div>
            </div>
            <?php endif; ?>

            <div class="col-lg-12">
                <div class="card">

                    <form action="<?php echo INCLUDE_PATH_ADMIN; ?>back-end/update.php" method="post">
                        <div class="card-body">
                            <h3 class="card-title">Dúvidas e Privacidade</h3>
                            <div class="mb-3 row">
                                <label for="privacidade" class="col-3 col-form-label">Política de privacidade</label>
                                <div class="col">
                                    <input name="privacidade" id="privacidade"
                                        type="text" class="form-control" value="<?php echo $privacidade; ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="faq" class="col-3 col-form-label">Perguntas Frequentes</label>
                                <div class="col">
                                    <input name="faq" id="faq"
                                        type="text" class="form-control" value="<?php echo $faq; ?>">
                                    <label class="form-check mt-2">
                                        <input name="use_faq" id="use_faq" type="checkbox" class="form-check-input" data-input-id="use_faq" <?php if ($use_faq) { echo 'checked'; } ?>>
                                        <span class="form-check-label">Usar FAQ padrão do sistema</span>
                                    </label>
                                </div>
                            </div>

                            <h3 class="card-title">Links</h3>
                            <div class="mb-3 row">
                                <label for="facebook" class="col-3 col-form-label">Facebook</label>
                                <div class="col">
                                    <input name="facebook" id="facebook"
                                        type="text" class="form-control" value="<?php echo $facebook; ?>" <?php echo ($facebook == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dFacebook" id="dFacebook" type="checkbox" class="form-check-input" data-input-id="facebook" <?php echo ($facebook == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar Facebook</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="instagram" class="col-3 col-form-label">Instagram</label>
                                <div class="col">
                                    <input name="instagram" id="instagram"
                                        type="text" class="form-control" value="<?php echo $instagram; ?>" <?php echo ($instagram == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dInstagram" id="dInstagram" type="checkbox" class="form-check-input" data-input-id="instagram" <?php echo ($instagram == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar Instagram</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="whatsapp" class="col-3 col-form-label">WhatsApp</label>
                                <div class="col">
                                    <input name="whatsapp" id="whatsapp"
                                        type="text" class="form-control" value="<?php echo $whatsapp; ?>" <?php echo ($whatsapp == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dWhatsapp" id="dWhatsapp" type="checkbox" class="form-check-input" data-input-id="whatsapp" <?php echo ($whatsapp == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar WhatsApp</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="linkedin" class="col-3 col-form-label">LinkedIn</label>
                                <div class="col">
                                    <input name="linkedin" id="linkedin"
                                        type="text" class="form-control" value="<?php echo $linkedin; ?>" <?php echo ($linkedin == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dLinkedin" id="dLinkedin" type="checkbox" class="form-check-input" data-input-id="linkedin" <?php echo ($linkedin == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar LinkedIn</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="twitter" class="col-3 col-form-label">Twitter</label>
                                <div class="col">
                                    <input name="twitter" id="twitter"
                                        type="text" class="form-control" value="<?php echo $twitter; ?>" <?php echo ($twitter == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dTwitter" id="dTwitter" type="checkbox" class="form-check-input" data-input-id="twitter" <?php echo ($twitter == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar Twitter</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="youtube" class="col-3 col-form-label">YouTube</label>
                                <div class="col">
                                    <input name="youtube" id="youtube"
                                        type="text" class="form-control" value="<?php echo $youtube; ?>" <?php echo ($youtube == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dYoutube" id="dYoutube" type="checkbox" class="form-check-input" data-input-id="youtube" <?php echo ($youtube == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar YouTube</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="website" class="col-3 col-form-label">Website</label>
                                <div class="col">
                                    <input name="website" id="website"
                                        type="text" class="form-control" value="<?php echo $website; ?>" <?php echo ($website == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dWebsite" id="dWebsite" type="checkbox" class="form-check-input" data-input-id="website" <?php echo ($website == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar Website</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="tiktok" class="col-3 col-form-label">TikTok</label>
                                <div class="col">
                                    <input name="tiktok" id="tiktok"
                                        type="text" class="form-control" value="<?php echo $tiktok; ?>" <?php echo ($tiktok == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dTiktok" id="dTiktok" type="checkbox" class="form-check-input" data-input-id="tiktok" <?php echo ($tiktok == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar TikTok</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="linktree" class="col-3 col-form-label">Linktr.ee</label>
                                <div class="col">
                                    <input name="linktree" id="linktree"
                                        type="text" class="form-control" value="<?php echo $linktree; ?>" <?php echo ($linktree == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dLinktree" id="dLinktree" type="checkbox" class="form-check-input" data-input-id="linktree" <?php echo ($linktree == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar Linktr.ee</span>
                                    </label>
                                </div>
                            </div>

                            <h3 class="card-title">Contato</h3>
                            <div class="mb-3 row">
                                <label for="telefone" class="col-3 col-form-label">Telefone</label>
                                <div class="col">
                                    <input name="telefone" id="telefone"
                                        type="text" class="form-control" value="<?php echo $telefone; ?>" <?php echo ($telefone == '') ? 'disabled' : '';?>>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="email" class="col-3 col-form-label">E-mail</label>
                                <div class="col">
                                    <input name="email" id="email"
                                        type="text" class="form-control" value="<?php echo $email; ?>" <?php echo ($email == '') ? 'disabled' : '';?>>
                                </div>
                            </div>

                            <h3 class="card-title">Endereço</h3>
                            <div class="mb-3 row">
                                <label for="cep" class="col-3 col-form-label">CEP</label>
                                <div class="col">
                                    <input name="cep" id="cep" onblur="getCepData()"
                                        type="text" class="form-control" value="<?php echo $cep; ?>" <?php echo ($cep == '') ? 'disabled' : '';?>>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="rua" class="col-3 col-form-label">Rua</label>
                                <div class="col">
                                    <input name="rua" id="rua"
                                        type="text" class="form-control" value="<?php echo $rua; ?>" <?php echo ($rua == '') ? 'disabled' : '';?>>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="numero" class="col-3 col-form-label">Número</label>
                                <div class="col">
                                    <input name="numero" id="numero"
                                        type="text" class="form-control" value="<?php echo $numero; ?>" <?php echo ($numero == '') ? 'disabled' : '';?>>
                                    <label class="form-check mt-2">
                                        <input name="dNumero" id="dNumero" type="checkbox" class="form-check-input" data-input-id="numero" <?php echo ($numero == '') ? 'checked' : '';?>>
                                        <span class="form-check-label">Desabilitar Número</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="bairro" class="col-3 col-form-label">Bairro</label>
                                <div class="col">
                                    <input name="bairro" id="bairro"
                                        type="text" class="form-control" value="<?php echo $bairro; ?>" <?php echo ($bairro == '') ? 'disabled' : '';?>>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="cidade" class="col-3 col-form-label">Cidade</label>
                                <div class="col">
                                    <input name="cidade" id="cidade"
                                        type="text" class="form-control" value="<?php echo $cidade; ?>" <?php echo ($cidade == '') ? 'disabled' : '';?>>
                                </div>
                            </div>
                            <div class="row">
                                <label for="estado" class="col-3 col-form-label">Estado</label>
                                <div class="col">
                                    <input name="estado" id="estado"
                                        type="text" class="form-control" value="<?php echo $estado; ?>" <?php echo ($estado == '') ? 'disabled' : '';?>>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-1" onclick="location.reload();">Cancelar</button>
                                <button type="submit" name="btnUpdFooter" class="btn btn-primary ms-auto">Salvar</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$update): ?>
</fieldset>
<?php endif; ?>

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

    colorPicker.addEventListener('input', updateColorPreview);
    colorCodeInput.addEventListener('input', updateColorFromCode);

    function updateColorPreview(event) {
        const selectedColor = event.target.value;
        colorCodeInput.value = selectedColor;
    }

    function updateColorFromCode() {
        const colorCode = colorCodeInput.value;
        if (isValidHexColorCode(colorCode)) {
            colorPicker.value = colorCode;
        }
    }

    function isValidHexColorCode(code) {
        return /^#([0-9A-F]{3}){1,2}$/i.test(code);
    }
</script>
<script>
    const hoverPicker = document.getElementById('hoverPicker');
    const hoverCodeInput = document.getElementById('hoverCode');

    hoverPicker.addEventListener('input', updateHoverPreview);
    hoverCodeInput.addEventListener('input', updateHoverFromCode);

    function updateHoverPreview(event) {
        const selectedHover = event.target.value;
        hoverCodeInput.value = selectedHover;
    }

    function updateHoverFromCode() {
        const hoverCode = hoverCodeInput.value;
        if (isValidHexHoverCode(hoverCode)) {
            hoverPicker.value = hoverCode;
        }
    }

    function isValidHexHoverCode(code) {
        return /^#([0-9A-F]{3}){1,2}$/i.test(code);
    }
</script>
<script>
    const colorPickerRGB = document.getElementById('colorPickerRGB');
    const redInput = document.getElementById('red');
    const greenInput = document.getElementById('green');
    const blueInput = document.getElementById('blue');

    colorPickerRGB.addEventListener('input', updateColorFromPicker);
    redInput.addEventListener('input', updateColorFromRGBInputs);
    greenInput.addEventListener('input', updateColorFromRGBInputs);
    blueInput.addEventListener('input', updateColorFromRGBInputs);

    function updateColorFromPicker(event) {
      const selectedColor = event.target.value;
      const rgbValues = hexToRGB(selectedColor);
      redInput.value = rgbValues.r;
      greenInput.value = rgbValues.g;
      blueInput.value = rgbValues.b;
      updateColorPreview();
    }

    function updateColorFromRGBInputs() {
      const redValue = parseInt(redInput.value);
      const greenValue = parseInt(greenInput.value);
      const blueValue = parseInt(blueInput.value);
      const hexColor = RGBToHex(redValue, greenValue, blueValue);
      colorPickerRGB.value = hexColor;
      updateColorPreview();
    }

    function updateColorPreview() {
      const hexColor = RGBToHex(parseInt(redInput.value), parseInt(greenInput.value), parseInt(blueInput.value));
    }

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
