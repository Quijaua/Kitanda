<?php

$logoHtml = '';

if (!empty($logo)){
    $logoHtml = '<img class="logo" src="' . htmlspecialchars($project['logo'], ENT_QUOTES, 'UTF-8') . '" width="114" height="32" alt="Logo da Loja">';
} else {
    $logoHtml = '<h1 class="mb-0">' . $project['name'] . '</h1>';
}

$message = '
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="telephone=no" name="format-detection" />

    <meta name="x-apple-disable-message-reformatting" />

    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet" type="text/css" />
    <meta name="color-scheme" content="light dark" />
    <meta name="supported-color-schemes" content="light dark only" />
    <style type="text/css">
        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }
    </style>
    <style data-premailer="ignore">
        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }

        @media screen and (max-width: 600px) {
            u+.body {
                width: 100vw !important;
            }
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
    </style>

    <link href="https://rsms.me/inter/inter.css" rel="stylesheet" type="text/css" data-premailer="ignore" />
    <style type="text/css" data-premailer="ignore">
        @import url(https://rsms.me/inter/inter.css);
    </style>

    <style>
        body{margin:0;padding:0;background-color:#f9fafb;font-size:15px;line-height:160%;mso-line-height-rule:exactly;color:#4b5563;width:100%;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;-webkit-font-feature-settings:"cv02","cv03","cv04","cv11";font-feature-settings:"cv02","cv03","cv04","cv11"}@media only screen and (max-width:560px){body{font-size:14px!important}}body,h1,h4,table,td{font-family:Inter,-apple-system,BlinkMacSystemFont,San Francisco,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif}table{border-collapse:collapse;width:100%}table:not(.main){-premailer-cellpadding:0;-premailer-cellspacing:0}.preheader{padding:0;font-size:0;display:none;max-height:0;mso-hide:all;line-height:0;color:transparent;height:0;max-width:0;opacity:0;overflow:hidden;visibility:hidden;width:0}.main{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}.wrap{width:100%;max-width:640px;text-align:left}.box{background:#fff;border-radius:8px;-webkit-box-shadow:0 1px 4px rgba(0,0,0,.05);box-shadow:0 1px 4px rgba(0,0,0,.05);border:1px solid #e8ebee}.box+.box{margin-top:24px}.box-table{background:#fff;border-radius:8px}.content{padding:48px 48px}@media only screen and (max-width:560px){.content{padding:24px!important}}.h1,.h4,h1,h4{font-weight:500;mso-line-height-rule:exactly;margin:0 0 .5em;color:#111827}.h1 a,.h4 a,h1 a,h4 a{color:inherit}.h1,h1{font-size:30px;line-height:126%;font-weight:700}@media only screen and (max-width:560px){.h1,h1{font-size:24px!important}}.h4,h4{font-size:16px}img{display:inline-block;border:0 none;line-height:100%;outline:0;text-decoration:none;vertical-align:bottom;font-size:0}a{color:#066fd1;text-decoration:none}a:hover{text-decoration:underline!important}a img{border:0 none}.row{table-layout:fixed}.row .row{height:100%}.col,.col-mobile,.col-spacer{vertical-align:top}.col-mobile-spacer,.col-spacer{width:24px}.col-spacer-sm{width:16px}.col-spacer-xs{width:8px}.col-hr{width:1px!important;border-left:16px solid #fff;border-right:16px solid #fff;background:#e8ebee}@media only screen and (max-width:560px){.col,.col-hr,.col-spacer,.col-spacer-sm,.col-spacer-xs,.row{display:table!important;width:100%!important}.col-hr{border:0!important;height:24px!important;width:auto!important;background:0 0!important}.col-spacer{width:100%!important;height:24px!important}.col-spacer-sm{height:16px!important}.col-spacer-xs{height:8px!important}}.table td{padding:4px 12px}.table td:first-child{padding-left:0}.table td:last-child{padding-right:0}.icon{padding:0;border-radius:50%;background:#edeef0;line-height:100%;font-weight:300;width:32px;height:32px;font-size:20px;border-collapse:separate;text-align:center}.icon img{display:block}.icon-lg{width:64px;height:64px;font-size:48px}.icon-lg img{width:32px;height:32px}.bg-light{background-color:#f6f6f6}.bg-body{background-color:#f9fafb}.text-muted{color:#667382}.text-muted-light{color:#8491a1}.bg-blue{background-color:#066fd1;color:#fff}a.bg-blue:hover{background-color:#0667c2!important}.text-right{text-align:right}.text-center{text-align:center}.va-middle{vertical-align:middle}.w-auto{width:auto}.font-strong{font-weight:600}.border{border:1px solid #e8ebee}.m-0{margin:0}.mt-md{margin-top:16px}.pt-0{padding-top:0}.pb-0{padding-bottom:0}.p-sm{padding:8px}.px-sm{padding-right:8px}.px-sm{padding-left:8px}.pt-md{padding-top:16px}.pb-md{padding-bottom:16px}.py-lg{padding-top:24px}.py-lg{padding-bottom:24px}.py-xl{padding-top:48px}.py-xl{padding-bottom:48px}.logo{filter: invert(1) brightness(10);}.img-dark{display:none}.theme-dark.bg-body{background:#212936}.theme-dark .text-muted{color:rgba(255,255,255,.4)!important}.theme-dark .bg-body{background:#212936!important}.theme-dark .box,.theme-dark .box-table{background:#2b3648!important;border-color:#2b3648!important;color:rgba(255,255,255,.7)!important}.theme-dark .h1,.theme-dark .h4,.theme-dark h1,.theme-dark h4{color:rgba(255,255,255,.9)!important}.theme-dark .col-hr{border-color:#2b3648!important;background-color:#212936!important}.theme-dark .border{border-color:#3e495b!important}.theme-dark .bg-light{background-color:#354258!important}.theme-dark .img-dark{display:inline-block!important}.theme-dark .img-light{display:none!important}@media (prefers-color-scheme:dark){.text-muted{color:rgba(255,255,255,.4)!important}.bg-body{background:#212936!important}.box,.box-table{background:#2b3648!important;border-color:#2b3648!important;color:rgba(255,255,255,.7)!important}.h1,.h4,h1,h4{color:rgba(255,255,255,.9)!important}.col-hr{border-color:#2b3648!important;background-color:#212936!important}.border{border-color:#3e495b!important}.bg-light{background-color:#354258!important}.img-dark{display:inline-block!important}.img-light{display:none!important}}
    </style>
</head>

<body class="bg-body">
    <center>
        <table class="main bg-body" width="100%" cellspacing="0" cellpadding="0" role="presentation">
            <tr>
                <td align="center" valign="top">
                    <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>

                    <table class="wrap" cellspacing="0" cellpadding="0" role="presentation">
                        <tr>
                            <td class="p-sm">

                                <!-- HEADER -->

                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="py-lg">
                                            <table cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <a href="' . INCLUDE_PATH . '">' . $logoHtml . '</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="' . $content['content']['link'] . '" class="text-muted-light">
                                                            Ver Pedido
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <!-- /HEADER -->

                                <div class="main-content">
                                    <div class="box">
                                        <table class="box-table" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td>
                                                    <table cellpadding="0" cellspacing="0">

                                                        <tr>
                                                            <td class="content pb-0" align="center">
                                                                <table class="icon icon-lg bg-blue " cellspacing="0" cellpadding="0" role="presentation">
                                                                    <tr>
                                                                        <td valign="middle" align="center">
<img src="data:image/png;base64,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
+QbmAHKFdgAAAABJRU5ErkJggg==" alt="Carrinho" />
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <h1 class="text-center m-0 font-strong mt-md">
                                                                    Pedido Recebido
                                                                </h1>
                                                                <h4>Pedido nº: #' . htmlspecialchars($content['content']['pedido']['compra']['id'], ENT_QUOTES, 'UTF-8') . '</h4>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="content">
                                                                <table class="row" cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td class="col">
                                                                            <h4>Resumo</h4>

                                                                            <table class="row">
                                                                                <tr>
                                                                                    <td class="col-mobile">Data do pedido</td>
                                                                                    <td class="col-mobile">' . htmlspecialchars($content['content']['pedido']['compra']['data'], ENT_QUOTES, 'UTF-8') . '</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="col-mobile">Pagamento</td>
                                                                                    <td class="col-mobile">' . htmlspecialchars($content['content']['pedido']['compra']['pagamento'], ENT_QUOTES, 'UTF-8') . '</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="col-mobile">Total do pedido</td>
                                                                                    <td class="col-mobile">R$ ' . number_format($content['content']['pedido']['compra']['total'], 2, ',', '.') . '</td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                        <td class="col-hr"></td>
                                                                        <td class="col">
                                                                            <h4>Endereço para envio</h4>

                                                                            ' . htmlspecialchars($content['content']['pedido']['compra']['endereco'], ENT_QUOTES, 'UTF-8') . '
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="content pt-0">
                                                                <table class="row">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 70%;">Itens do Pedido</th>
                                                                            <th class="text-center">Qtde.</th>
                                                                            <th class="text-right">Preço</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>';

                                                                    $produtos = $content['content']['pedido']['produtos'];

                                                                    foreach ($produtos as $p) {
                                                                        $message .= '<tr>
                                                                            <td><div style="display: flex; align-items: center;"><img src="' . $p['imagem'] . '" class="va-middle" style="padding: 4px; margin-right: 8px;" width="48" height="48" alt="layout-grid" />' . htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') . '</div></td>
                                                                            <td class="text-center">' . (int) $p['quantidade'] . '</td>
                                                                            <td class="text-right">
                                                                                R$ ' . number_format($p['preco'], 2, ',', '.') . '
                                                                            </td>
                                                                        </tr>';
                                                                    }

                                                            $message .= '
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td colspan="1" class="text-right">Subtotal:</td>
                                                                            <td colspan="2" class="text-right">R$ ' . number_format($content['content']['pedido']['compra']['subtotal'], 2, ',', '.') . '</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1" class="text-right">Frete:</td>
                                                                            <td colspan="2" class="text-right">R$ ' . number_format($content['content']['pedido']['compra']['frete'], 2, ',', '.') . '</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1" class="text-right">Total:</td>
                                                                            <td colspan="2" class="text-right">R$ ' . number_format($content['content']['pedido']['compra']['total'], 2, ',', '.') . '</td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <table cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="py-xl">
                                            <table class="text-center text-muted" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="pt-md">
                                                        Caso tenha alguma dúvida, fique à vontade para nos enviar uma mensagem para <a href="mailto:' . htmlspecialchars($project['email'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($project['email'], ENT_QUOTES, 'UTF-8') . '</a>.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="pt-md">
                                                        Copyright &copy; ' . date('Y') . ' ' . htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') . '. Todos os direitos reservados.
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- /FOOTER -->
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </center>
</body>

</html>
'; ?>
