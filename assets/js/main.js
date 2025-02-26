let hasCPF = true;
let donationPeriodicity = "monthly";
let donationAmount = 50;
let config = null;
let selectedPayment = "credit_card";
let minOnceDonationCreditCard = 10;
let minOnceDonationBankSlip = 10;
let minOnceDonationPix = 10;
let lastDonationButtonClicked = null;

function setValuesFromQueryString() {
    let params = (new URL(document.location)).searchParams;
    let amount = params.get('amount');
    let periodicity = params.get('recurring_period');
    let paymentMethod = params.get('payment_method');
    let source = params.get('source');
    let source_url = params.get('referrer_url');
    let source_id = params.get('referrer_post_id');

    let fieldSetId = 'donation-monthly-group';
    let fieldOtherId = 'field-other-monthly';
    let fieldSourceId = 'field-source';
    let fieldSourceUrlId = 'field-source-url';
    let fieldSourceIdId = 'field-source-id';

    if (source) {
        let sourceEl = document.getElementById(fieldSourceId)
        sourceEl.value = source;
    }

    if (source_url) {
        let sourceUrlEl = document.getElementById(fieldSourceUrlId)
        sourceUrlEl.value = source_url;
    }

    if (source_id) {
        let sourceIdEl = document.getElementById(fieldSourceIdId)
        sourceIdEl.value = source_id;
    }

    if (periodicity) {
        let periodicityEl;
        periodicity = periodicity.toLowerCase();

        switch (periodicity) {
            case 'monthly':
            case 'months':
            case 'mensal':
                periodicityEl = document.getElementById('inlineRadio1');
                break;

            case 'yearly':
            case 'years':
            case 'anual':
                fieldSetId = 'donation-yearly-group';
                fieldOtherId = 'field-other-yearly';
                periodicityEl = document.getElementById('inlineRadio2');
                break;

            case 'única':
            case 'unica':
            case 'once':
            case 'one-time':
                fieldSetId = 'donation-once-group';
                fieldOtherId = 'field-other-once';
                periodicityEl = document.getElementById('inlineRadio3');
                break;

            default:
                break;
        }

        if (periodicityEl) {
            periodicityEl.click();
        }
    }

    if (paymentMethod) {
        let methodEl;
        paymentMethod = paymentMethod.toLowerCase();

        switch (paymentMethod) {
            case 'credit_card':
            case 'cartao':
            case 'cartão':
            case 'cartãodecrédito':
            case 'cartaodecredito':
            case 'cartão_de_crédito':
            case 'cartao_de_credito':
            case 'crédito':
            case 'credito':
                methodEl = document.getElementById('payment-credit-card');
                break;

            case 'bank_slip':
            case 'boleto':
                methodEl = document.getElementById('payment-bank-slip');
                break;

            case 'pix':
                methodEl = document.getElementById('payment-pix');
                break;

            default:
                break;
        }

        if (methodEl) {
            methodEl.click();
        }
    }

    if (amount) {
        let amountElementId = amount;
        let amountEl = document.querySelector('#' + fieldSetId + ' [data-amount-for-selection="' + amount + '"]');

        if (amountEl) {
            amountEl.click();
        } else {
            let otherElem = document.getElementById(fieldOtherId);

            if (otherElem) {
                otherElem.focus();
                otherElem.value = amount;
            }
        }
    }
}

function setPeriodOption(periodicity) {
    $('#field-other-monthly').val("");
    $('#field-other-yearly').val("");
    $('#field-other-once').val("");

    $('#donation-monthly-group').addClass('d-none').removeClass('d-block');
    $('#donation-yearly-group').addClass('d-none').removeClass('d-block');
    $('#donation-once-group').addClass('d-none').removeClass('d-block');
    $('#donation-' + periodicity + '-group').addClass('d-block').removeClass('d-none');

    $('.option-default-' + periodicity).trigger('click');
    $('#payment-credit-card').trigger('click');

    switch (periodicity) {
        case "monthly":
            $("#payment-pix").prop('disabled', true);
            $("#payment-bank-slip").prop('disabled', false);
            $("#payment-pix").prop('disabled', false);
            break;
        case "yearly":
            $("#payment-pix").prop('disabled', true);
            $("#payment-bank-slip").prop('disabled', false);
            $("#payment-pix").prop('disabled', false);
            break;
        case "once":
            $("#payment-pix").prop('disabled', false);
            $("#payment-bank-slip").prop('disabled', false);
            $("#payment-pix").next().html("PIX");
            $("#payment-bank-slip").next().html("Boleto");
            break;
    }

    if (hasCPF == false) {
        $("#payment-pix").prop('disabled', true);
        $("#payment-bank-slip").prop('disabled', true);
    }
}

function fetchFeeAmount(dadosDoacao) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            url: "./back-end/taxa_pagamento.php",
            data: dadosDoacao,
            dataType: 'JSON',
            success: function (response) {
                if (response.status == 200) {
                    resolve(response.feeAmount);
                } else {
                    reject('Erro na resposta do servidor');
                }
            },
            error: function (xhr, status, error) {
                reject(error);
            }
        });
    });
}

async function donationOption(el, type, amount, showAddOnFee) {
    lastDonationButtonClicked = el;
    $('#donation-' + type + '-group button').addClass('btn-outline-dark').removeClass('active');
    $(el).addClass('active').removeClass('btn-outline-dark');
    $("#field-other-" + type).css('background-color', '#fff').val("");

    if (config && showAddOnFee) {
        $("#div-add-on-fee").slideDown();

        var dadosDoacao = {
            method: selectedPayment,
            value: amount
        };

        try {
            var feeAmount = await fetchFeeAmount(dadosDoacao);

            $("#div-add-on-fee label").html("Adicione + <strong>R$ " + feeAmount.toFixed(2) + "</strong> para cobrir as tarifas bancárias");

            if ($('#field-add-on-fee').is(':checked')) {
                amount += feeAmount;
            }

        } catch (error) {
            console.error(error);
        }
    } else {
        $("#div-add-on-fee").slideUp();
    }

    switch (type) {
        case "monthly":
            $(".button-confirm-payment").text("Contribuir com R$ " + amount + " por mês");
            break;
        case "yearly":
            $(".button-confirm-payment").text("Contribuir com R$ " + amount + " por ano");
            break;
        case "once":
            $(".button-confirm-payment").text("Contribuir com R$ " + amount);
            break;
    }

    donationPeriodicity = type;
    donationAmount = amount;
}
async function donationOtherOption(el, type, showAddOnFee) {
    lastDonationButtonClicked = el;
    amount = parseFloat($(el).cleanVal());

    if (amount != "" && amount > 0) {
        switch (selectedPayment) {
            case "credit_card":
                if (amount < minOnceDonationCreditCard) {
                    $("#div-errors-price").html("Valor mínimo para contribuição em cartão de crédito é de R$ " + minOnceDonationCreditCard).slideDown('fast').effect("shake");
                    $('.option-default-' + type).trigger('click');
                    $(el).focus();
                    return;
                }
                break;
            case "bank_slip":
                if (amount < minOnceDonationBankSlip) {
                    $("#div-errors-price").html("Valor mínimo para contribuição em boleto bancário é de R$ " + minOnceDonationBankSlip).slideDown('fast').effect("shake");
                    $('.option-default-' + type).trigger('click');
                    $(el).focus();
                    return;
                }
                break;
            case "Pix":
                if (amount < minOnceDonationPix) {
                    $("#div-errors-price").html("Valor mínimo para contribuição em PIX é de R$ " + minOnceDonationPix).slideDown('fast').effect("shake");
                    $('.option-default-' + type).trigger('click');
                    $(el).focus();
                    return;
                }
                break;
        }
        $("#div-errors-price").slideUp('fast');

        $('#donation-' + type + '-group button').addClass('btn-outline-dark').removeClass('active');
        $(el).css('background-color', '#FFC107');

        if (showAddOnFee) {
            $("#div-add-on-fee").slideDown();

            var feeAmount = 0;

            var dadosDoacao = {
                method: selectedPayment,
                value: amount
            };

            try {
                feeAmount = await fetchFeeAmount(dadosDoacao);

                $("#div-add-on-fee label").html("Adicione + <strong>R$ " + feeAmount.toFixed(2) + "</strong> para cobrir as tarifas bancárias");

                // Check if the add-on fee is checked
                var addOnFeeChecked = $('#field-add-on-fee').is(':checked');
                var feeAdded = $('#field-add-on-fee').data('fee-added');

                if (addOnFeeChecked && !feeAdded) {
                    amount += feeAmount;
                    $('#field-add-on-fee').data('fee-added', true);
                } else if (!addOnFeeChecked && feeAdded) {
                    $('#field-add-on-fee').data('fee-added', false);
                }

            } catch (error) {
                console.error(error);
                return;
            }

        } else {
            $("#div-add-on-fee").slideUp();
            $('#field-add-on-fee').data('fee-added', false); // Reset the fee-added flag
        }

        switch (type) {
            case "monthly":
                $(".button-confirm-payment").text("Contribuir com R$ " + amount.toFixed(2) + " por mês");
                break;
            case "yearly":
                $(".button-confirm-payment").text("Contribuir com R$ " + amount.toFixed(2) + " por ano");
                break;
            case "once":
                $(".button-confirm-payment").text("Contribuir com R$ " + amount.toFixed(2));
                break;
        }
        donationPeriodicity = type;
        donationAmount = amount;
    } else {
        $(el).next().html("OUTRO<br/>VALOR");
        $('.option-default-' + type).trigger('click');
    }
}

function setPaymentMethod(paymentMethod) {
    switch (paymentMethod) {
        case "credit_card":
            $("#credit-card-fields").slideDown();
            break;
        case "bank_slip":
        case "Pix":
            $("#credit-card-fields").slideUp();
            break;
    }
    if (lastDonationButtonClicked != null) $(lastDonationButtonClicked).trigger('click').trigger('blur');

    selectedPayment = paymentMethod;
}

function getCepData() {
    let cep = $('#field-zipcode').val();
    cep = cep.replace(/\D/g, "");
    if (cep.length < 8) {
        $("#div-errors-price").html("CEP deve conter no mínimo 8 dígitos").slideDown('fast').effect("shake");
        $("#field-zipcode").addClass('is-invalid').focus();
        return;
    }
    $("#field-zipcode").removeClass('is-invalid');
    $("#div-errors-price").slideUp('fast');

    if (cep != "") {
        $("#field-street").val("Carregando...");
        $("#field-district").val("Carregando...");
        $("#field-city").val("Carregando...");
        $("#field-state").val("...");
        $.getJSON("https://viacep.com.br/ws/" + cep + "/json/", function (data) {
            $("#field-street").val(data.logradouro);
            $("#field-district").val(data.bairro);
            $("#field-city").val(data.localidade);
            $("#field-state").val(data.uf);
            $("#field-street-number").focus();

        }).fail(function () {
            $("#field-street").val("");
            $("#field-district").val("");
            $("#field-city").val("");
            $("#field-state").val(" ");
        });
    }
}

function flowProces(token) {
    if (window.inPaymentFlow === true) {
        return;
    }

    window.inPaymentFlow = true;

    if (selectedPayment === "credit_card" && token.length > 0) {
        getCreditCardToken(token);
    }
    else {
        subscription("", token);
    }

    window.inPaymentFlow = false;
}

function validateFields() {
    if ($("#field-email").val() == "") {
        $("#div-errors-price").html("Informe o E-mail").slideDown('fast').effect("shake");
        $("#field-email").addClass('is-invalid').focus();
        return false;
    }
    if (isValidEmail($("#field-email").val()) == false) {
        $("#div-errors-price").html("E-mail inválido").slideDown('fast').effect("shake");
        $("#field-email").addClass('is-invalid').focus();
        return false;
    }
    $("#field-email").removeClass('is-invalid');
    if ($("#field-name").val() == "") {
        $("#div-errors-price").html("Informe o nome").slideDown('fast').effect("shake");
        $("#field-name").addClass('is-invalid').focus();
        return false;
    }
    $("#field-name").removeClass('is-invalid');
    if ($("#field-surname").val() == "") {
        $("#div-errors-price").html("Informe o sobrenome").slideDown('fast').effect("shake");
        $("#field-surname").addClass('is-invalid').focus();
        return false;
    }
    $("#field-surname").removeClass('is-invalid');
    if ($('#is_foreigner').is(':checked') === false) // Sou estrangeiro
    {
        if ($("#field-cpf").val() == "") {
            $("#div-errors-price").html("Informe o CPF").slideDown('fast').effect("shake");
            $("#field-cpf").addClass('is-invalid').focus();
            return false;
        }

        if (isValidCPF($("#field-cpf").val()) == false) {
            $("#div-errors-price").html("CPF inválido").slideDown('fast').effect("shake");
            $("#field-cpf").addClass('is-invalid').focus();
            return false;
        }
    }
    $("#field-cpf").removeClass('is-invalid');

    if (selectedPayment === 'credit_card') {
        if ($("#field-card-number").val() == "") {
            $("#div-errors-price").html("Informe o número do cartão").slideDown('fast').effect("shake");
            $("#field-card-number").addClass('is-invalid').focus();
            return false;
        }
        $("#field-card-number").removeClass('is-invalid');
        if ($("#field-card-expiration").val() == "") {
            $("#div-errors-price").html("Informe a data de expiração").slideDown('fast').effect("shake");
            $("#field-card-expiration").addClass('is-invalid').focus();
            return false;
        }
        $("#field-card-expiration").removeClass('is-invalid');
        if ($("#field-card-cvc").val() == "") {
            $("#div-errors-price").html("Informe o código CVC").slideDown('fast').effect("shake");
            $("#field-card-cvc").addClass('is-invalid').focus();
            return false;
        }
        $("#field-card-cvc").removeClass('is-invalid');
    }
    else {
        //Endereço é obrigatório
        if ($("#field-country").val() == "") {
            $("#div-errors-price").html("Selecione o país").slideDown('fast').effect("shake");
            $("#field-country").addClass('is-invalid').focus();
            return false;
        }
        $("#field-country").removeClass('is-invalid');
        if ($("#field-zipcode").val() == "") {
            $("#div-errors-price").html("Informe o CEP").slideDown('fast').effect("shake");
            $("#field-zipcode").addClass('is-invalid').focus();
            return false;
        }
        cep = $("#field-zipcode").val().replace(/\D/g, "");
        if (cep.length < 8) {
            $("#div-errors-price").html("CEP deve conter no mínimo 8 dígitos").slideDown('fast').effect("shake");
            $("#field-zipcode").addClass('is-invalid').focus();
            return false;
        }
        $("#field-zipcode").removeClass('is-invalid');

        if ($("#field-street").val() == "") {
            $("#div-errors-price").html("Informe a rua").slideDown('fast').effect("shake");
            $("#field-street").addClass('is-invalid').focus();
            return false;
        }
        $("#field-street").removeClass('is-invalid');
        if ($("#field-street-number").val() == "") {
            $("#div-errors-price").html("Informe o número da casa").slideDown('fast').effect("shake");
            $("#field-street-number").addClass('is-invalid').focus();
            return false;
        }
        $("#field-street-number").removeClass('is-invalid');
        if ($("#field-district").val() == "") {
            $("#div-errors-price").html("Informe o bairro").slideDown('fast').effect("shake");
            $("#field-district").addClass('is-invalid').focus();
            return false;
        }
        $("#field-district").removeClass('is-invalid');
        if ($("#field-city").val() == "") {
            $("#div-errors-price").html("Informe a cidade").slideDown('fast').effect("shake");
            $("#field-city").addClass('is-invalid').focus();
            return false;
        }
        $("#field-city").removeClass('is-invalid');
        if ($("#field-state").val() == "") {
            $("#div-errors-price").html("Selecione o estado").slideDown('fast').effect("shake");
            $("#field-state").addClass('is-invalid').focus();
            return false;
        }
        $("#field-state").removeClass('is-invalid');
    }

    $("#div-errors-price").slideUp('fast');
    return true;
}

function printPaymentData(data) {
    $("#highlight").html("Muito obrigado!");

    let html = "<p class='text-block2'>Sua doação estimula a continuar nosso trabalho pela construção de um mundo melhor</p>";

    switch (data.forma_pagamento) {
        case "BOLETO":
            html += "<strong>Vencimento:</strong> " + data.data_vencimento + "<br/>";
            html += "<div class='row'><div class='col-md-12 p-3 mb-3 border text-center fw-bold text-muted' id='bank-slip-bars'><img alt='Barcode Generator TEC-IT' src='https://barcode.tec-it.com/barcode.ashx?data=" + data.boleto_barCode + "&code=Code128&translate-esc=on'/></div>";
            html += "<strong>Linha digitável do boleto</strong><br/>";
            html += "<div class='row'><div class='col-md-12 p-3 mb-3 border text-center fw-bold text-muted' id='bank-slip-code'>" + mascaraCodigoBoleto(data.boleto_identificationField) + "</div></div>";
            html += "<div class='row'>";
            html += "    <div class='col-md-6 text-center'><a href='" + data.link_boleto + "' target='_blank' class='btn btn-lg btn-light w-100'>Download PDF</a></div>";
            html += "    <div class='col-md-6 text-center'><a href=\"javascript:copyBoletoToClipboard('#bank-slip-code')\" id='boleto-copy-btn' class='btn btn-lg active  w-100'>Copiar código</a></div>";
            html += "</div>";
            break;
        case "CREDIT_CARD":
            html += "<div class='row'><div class='col-md-12 p-3 mb-3 border text-center fw-bold text-muted'>A contribuição está sendo processada, logo você receberá um e-mail com a confirmação.</div></div>";
            break;
        case "PIX":
            html += "<strong>Expiração:</strong> " + data.pix_expirationDate + "<br/>";
            html += "<div class='row'><div class='col-md-12 p-3 mb-3 border text-center fw-bold text-muted d-flex justify-content-center' id='pix-qrcode'><img src='data:image/png;base64," + data.pix_encodedImage + "' class='img-fluid' /></div></div>";
            html += "<strong>Código Pix</strong><br/>";
            html += "<div class='row'><div class='col-md-12 p-3 mb-3 border text-center fw-bold text-muted' id='pix-qrcode-code' style='white-space: pre-wrap;word-break: break-word;'>" + data.pix_payload + "</div></div>";
            html += "<div class='row'>";
            html += "    <div class='col-md-6 text-center'><a href='data:image/png;base64," + data.pix_encodedImage + "' target='_blank' class='btn btn-lg btn-light w-100'>Abrir QRCode</a></div>";
            html += "    <div class='col-md-6 text-center'><a href=\"javascript:copyPixToClipboard('#pix-qrcode-code')\" id='pix-copy-btn' class='btn btn-lg active  w-100'>Copiar código Pix</a></div>";
            html += "</div>";
            break;
    }

    $("#div-container-form").html(html);
}

// Função para copiar o código do Boleto para a área de transferência
function copyBoletoToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();

    // Alterar texto do botão para "Copiado!" e depois voltar para o texto original
    var originalText = $('#boleto-copy-btn').text();
    $('#boleto-copy-btn').text('Copiado!');

    setTimeout(function() {
        $('#boleto-copy-btn').text(originalText);
    }, 2000);  // O texto volta ao normal após 2 segundos
}

// Função para copiar o código Pix para a área de transferência
function copyPixToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();

    // Alterar texto do botão para "Copiado!" e depois voltar para o texto original
    var originalText = $('#pix-copy-btn').text();
    $('#pix-copy-btn').text('Copiado!');

    setTimeout(function() {
        $('#pix-copy-btn').text(originalText);
    }, 2000);  // O texto volta ao normal após 2 segundos
}

function isValidCPF(strCPF) {
    strCPF = strCPF.replace(/\D/g, "");
    let sumMod;
    let modRest;
    sumMod = 0;
    if (
        strCPF == "00000000000"
        || strCPF == "11111111111"
        || strCPF == "22222222222"
        || strCPF == "33333333333"
        || strCPF == "44444444444"
        || strCPF == "55555555555"
        || strCPF == "66666666666"
        || strCPF == "77777777777"
        || strCPF == "88888888888"
        || strCPF == "99999999999"
    ) return false;

    for (i = 1; i <= 9; i++) sumMod = sumMod + parseInt(strCPF.substring(i - 1, i)) * (11 - i);
    modRest = (sumMod * 10) % 11;

    if ((modRest == 10) || (modRest == 11)) modRest = 0;
    if (modRest != parseInt(strCPF.substring(9, 10))) return false;

    sumMod = 0;
    for (i = 1; i <= 10; i++) sumMod = sumMod + parseInt(strCPF.substring(i - 1, i)) * (12 - i);
    modRest = (sumMod * 10) % 11;

    if ((modRest == 10) || (modRest == 11)) modRest = 0;
    if (modRest != parseInt(strCPF.substring(10, 11))) return false;
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = document.getElementById('field-phone');

    phoneInput.addEventListener('input', function () {
        var phoneNumber = this.value.replace(/\D/g, ""); // Remove caracteres não numéricos

        var formattedPhoneNumber = "";

        if (phoneNumber.length > 0) {
            // Formato: (XX) XXXX-XXXX
            formattedPhoneNumber += "(" + phoneNumber.substring(0, 2) + ")";
        }
        if (phoneNumber.length > 2) {
            formattedPhoneNumber += " " + phoneNumber.substring(2, 7);
        }
        if (phoneNumber.length > 7) {
            formattedPhoneNumber += "-" + phoneNumber.substring(7, 11);
        }

        this.value = formattedPhoneNumber;
    });
});

function isValidEmail(input) {
    return String(input).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) != null;
}

function mascaraCodigoBoleto(codigo) {
    // Remover todos os caracteres não numéricos
    codigo = codigo.replace(/\D/g, '');

    // Aplicar a máscara
    codigo = codigo.replace(/(\d{5})(\d{5})(\d{5})(\d{6})(\d{5})(\d{14})/, '$1.$2 $3.$4 $5.$6');

    return codigo;
}
