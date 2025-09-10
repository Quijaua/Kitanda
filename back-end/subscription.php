<?php
// Carrega as variáveis de ambiente do arquivo .env
require dirname(__DIR__).'/vendor/autoload.php';
require_once dirname(__DIR__).'/back-end/functions.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$client = new GuzzleHttp\Client();

// Acessa as variáveis de ambiente
$config['asaas_api_url'] = $_ENV['ASAAS_API_URL'];
$config['asaas_api_key'] = $_ENV['ASAAS_API_KEY'];

$config['melhor_envio_url'] = $_ENV['MELHOR_ENVIO_URL'];
$config['melhor_envio_token'] = $_ENV['MELHOR_ENVIO_TOKEN'];

$hcaptcha_secret = $_ENV['HCAPTCHA_CHAVE_SECRETA'];
$turnstile_secret = $_ENV['TURNSTILE_CHAVE_SECRETA'];

//Decodificando base64 e passando para $dataForm
$dataFormBase64 = base64_decode($_POST['params']);

// Converte de ISO-8859-1 para UTF-8, ignorando caracteres inválidos
$dataFormBase64 = iconv("ISO-8859-1", "UTF-8//TRANSLIT//IGNORE", $dataFormBase64);

$dataForm = json_decode($dataFormBase64, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Error: " . json_last_error_msg());
}

// Verifica se o honeypot está vazio antes de processar a solicitação
if (!empty($dataForm['email'])) {
    // Honeypot preenchido, retorna status 200 sem fazer alterações
    $response = array(
        'status' => 200,
        'message' => 'Requisição processada com sucesso.',
        'data' => $dataForm
    );

    //echo json_encode($response);
    //exit; // Encerra o script aqui para evitar processamento adicional
}

// Consulta à tabela tb_page_captchas para verificar qual captcha usar
include_once('../config.php');
$query = "
    SELECT 
        captcha_type AS type, 
        CASE 
            WHEN captcha_type = 'hcaptcha' THEN 'hCaptcha'
            WHEN captcha_type = 'turnstile' THEN 'Turnstile'
            ELSE 'Nenhum' 
        END AS name
    FROM tb_page_captchas 
    WHERE page_name = :page_name
";
$stmt = $conn->prepare($query);
$stmt->bindValue(':page_name', 'doacao');
$stmt->execute();
$captcha = $stmt->fetch(PDO::FETCH_ASSOC);

$responseKey = $dataForm['h-captcha-response'] ?? $dataForm['cf-turnstile-response'] ?? ''; // Chaves de resposta


if ($captcha['type'] == 'hcaptcha') {

    // Verifique se a chave de resposta está presente
    if (isset($responseKey) && !empty($responseKey)) {

        // Faça uma solicitação para validar a resposta do hCaptcha
        $url = 'https://hcaptcha.com/siteverify';
        $data = [
            'secret' => $hcaptcha_secret,
            'response' => $responseKey
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

    } else {
        echo json_encode(["status"=>400, "message" => "Falha na validação do " . $captcha['name'] . "."]);
        exit;
    }

} elseif ($captcha['type'] == 'turnstile') {

    // Verifique se a chave de resposta está presente
    if (isset($responseKey) && !empty($responseKey)) {

        // Verificação do Turnstile
        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret' => $turnstile_secret,
            'response' => $responseKey
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

    } else {
        echo json_encode(["status"=>400, "message" => "Falha na validação do " . $captcha['name'] . "."]);
        exit;
    }

}

    // Verifique a resposta
    if ($captcha['type'] == 'none' || ($response && isset($response['success']) && $response['success'] === true)) {
        // Tudo certo cria a cobrança
        makeDonation($dataForm, $config);
    } else {
        echo json_encode(["status"=>400, "message" => "Por favor preencha o " . $captcha['name'] . " para continuar."]);
        exit;
    }

$response = array(
    'status' => 200,
    'message' => 'Requisição processada com sucesso.'
);

return json_encode($response);

function makeDonation($dataForm, $config){
    include('config.php');
    session_start();

    if(isset($_POST)) {
        // Passando valor do email
        //$dataForm['email'] = $dataForm['eee'];

        // Passa o group se ouver
        $dataForm['groupName'] = $_ENV['GROUPNAME'];

        // Iniciando variavel "$subscription_id"
        $subscription_id = null;
        
        // Recebe os dados do carrinho enviados pelo AJAX (por exemplo, via método POST)
        // Espera que os dados estejam no formato: cart[0][id], cart[0][quantity], etc.
        $cartItemsPost = $_POST['cart'] ?? [];

        // Array que armazenará os produtos com os dados do banco
        $produtos = [];
        $subtotal = 0;

        // Percorre os itens enviados
        foreach ($cartItemsPost as $item) {
            // Certifique-se de que os índices existem e que a quantidade é válida
            if (!isset($item['id']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                continue;
            }
            
            $id = $item['id'];
            $quantity = (int)$item['quantity'];

            // Busca os dados reais do produto no banco
            $stmt = $conn->prepare(
                "SELECT p.id, p.nome, p.preco, f.altura, f.largura, f.comprimento, f.peso, pi.imagem AS produto_imagem
                FROM tb_produtos p
                LEFT JOIN tb_produto_imagens pi ON p.id = pi.produto_id
                LEFT JOIN tb_frete_dimensoes f ON f.id = p.freight_dimension_id
                WHERE p.id = ?"
            );
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            // Busca os dados do remetente
            $stmt = $conn->prepare(
                "SELECT * FROM tb_checkout WHERE id = ?"
            );
            $stmt->execute([1]);
            $remetente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Se o produto for encontrado, monta os dados do item
            if ($produto) {
                // Adiciona a quantidade e calcula o subtotal para esse item
                $produto['imagem'] = !empty($produto['produto_imagem'])
                                   ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $produto['produto_imagem'])
                                   : INCLUDE_PATH . "assets/preview-image/product.jpg";
                $produto['quantidade'] = $quantity;
                $produto['preco_total'] = $produto['preco'] * $quantity;
                $produto['altura'] = $produto['altura'] ?? 4;
                $produto['largura'] = $produto['largura'] ?? 12;
                $produto['comprimento'] = $produto['comprimento'] ?? 17;
                $produto['peso'] = $produto['peso'] ?? 0.5;
                $subtotal += $produto['preco_total'];
                
                // Adiciona o produto ao array de produtos
                $produtos[] = $produto;
            }
        }

        // Primeiro agrupamos os produtos por vendedora
        $vendedoras = [];

        foreach ($cartItemsPost as $item) {
            if (!isset($item['id']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                continue;
            }
            
            $id = $item['id'];
            $quantity = (int)$item['quantity'];

            // Busca dados do produto incluindo a vendedora
            $stmt = $conn->prepare("SELECT 
                p.id, 
                p.nome, 
                p.preco, 
                c.nome AS vendedora_nome,
                c.email AS vendedora_email,
                pi.imagem AS produto_imagem 
                FROM tb_produtos p 
                LEFT JOIN tb_produto_imagens pi ON p.id = pi.produto_id 
                LEFT JOIN tb_clientes c ON p.criado_por = c.id 
                WHERE p.id = ?");
            
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto && !empty($produto['vendedora_email'])) {
                // Prepara os dados do item
                $produto['imagem'] = !empty($produto['produto_imagem'])
                    ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $produto['produto_imagem'])
                    : INCLUDE_PATH . "assets/preview-image/product.jpg";
                
                $produto['quantidade'] = $quantity;
                $produto['preco_total'] = $produto['preco'] * $quantity;

                // Agrupa por email da vendedora
                $nomeVendedora = $produto['vendedora_nome'];
                $emailVendedora = $produto['vendedora_email'];
                
                if (!isset($vendedoras[$emailVendedora])) {
                    $vendedoras[$emailVendedora] = [
                        'nome' => $nomeVendedora,
                        'email' => $emailVendedora,
                        'produtos' => [],
                        'total' => 0
                    ];
                }
                
                $vendedoras[$emailVendedora]['produtos'][] = $produto;
                $vendedoras[$emailVendedora]['total'] += $produto['preco_total'];
            }
        }

        // Agora envia um e-mail único para cada vendedora
        foreach ($vendedoras as $vendedora) {
            // Monta o corpo do e-mail
            $table = "
                <h2>Novos Pedidos dos Seus Produtos</h2>
                <p>Você tem novos pedidos em sua loja na {$project['name']}. Segue detalhes:</p>
                
                <table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($vendedora['produtos'] as $produto) {
                $table .= "
                        <tr>
                            <td>{$produto['nome']}</td>
                            <td>{$produto['quantidade']}</td>
                            <td>R$ " . number_format($produto['preco'], 2, ',', '.') . "</td>
                            <td>R$ " . number_format($produto['preco_total'], 2, ',', '.') . "</td>
                        </tr>";
            }

            $table .= "
                        <tr>
                            <td colspan='3' style='text-align: right;'><strong>Total Geral:</strong></td>
                            <td><strong>R$ " . number_format($vendedora['total'], 2, ',', '.') . "</strong></td>
                        </tr>
                    </tbody>
                </table>";

            // Configuração do e-mail
            $subject = "Novos Pedidos - " . date('d/m/Y') . " - " . $project['name'];
            $content = array("layout" => "produtos-vendidos", "content" => array("name" => $vendedora['nome'], "table" => $table));
            sendMail($vendedora['nome'], $vendedora['email'], $project, $subject, $content);
        }

        // Define um valor fixo para o frete (por exemplo, 10 reais)
        $frete = $_POST['shipping']['valor'];

        // Calcula o total da compra
        $total = $subtotal + $frete;

        // Monta o array da compra com os valores calculados
        $compra = [
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $total
        ];

        // Cria o array final que contém os produtos e os dados da compra
        $resultado = [
            'produtos' => $produtos,
            'compra' => $compra
        ];






        // echo "<pre>";
        // print_r($resultado);
        // echo "</pre>";
        // exit;






        $dataForm['value'] = $compra['total'];
        if (isset($dataForm['card_installments']) && !empty($dataForm['card_installments']) && $dataForm['card_installments'] !== 1) {
            $dataForm['installmentCount'] = $compra['total'];
        }



        include('config.php');
        include_once('criar_cliente.php');
        include_once('cobranca_cartao.php');
        include_once('cobranca_pix.php');
        include_once('cobranca_boleto.php');
        include_once('listar_cobranca_assinatura.php');
        include_once('qr_code.php');
        include_once('linha_digitavel.php');
        // include_once('rastreamento.php');
        include_once('salvar_pedido.php');

        // Define variavel como nula para evitar erros
        $shipment = null;
    
        switch($_POST["method"]) {
            case '100':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment = asaas_CriarCobrancaCartao($customer_id, $dataForm, $config);
                // $shipment = melhorEnvioGetTracking($_POST['shipping'], $config);
                $pedido_id = salvarPedido($customer_id, null, $payment, $shipment, $dataForm, $compra, $produtos, $config);

                $shipment_id = $_POST['shipping']['service_id'];
                $etiqueta = criarGerarEtiqueta($config, $dataForm, $produtos, $compra, $remetente);

                $resultado['compra']['data'] = date('d/m/Y');
                $resultado['compra']['id'] = $pedido_id;
                $resultado['compra']['pagamento'] = 'Crédito';

                $resultado['compra']['endereco'] = $dataForm['street'] . ', ' . $dataForm['addressNumber'];
                if (!empty($dataForm['complement'])) {
                    $resultado['compra']['endereco'] .= ' - ' . $dataForm['complement'];
                }
                $resultado['compra']['endereco'] .= ', ' . $dataForm['district'] . ' - ' . $dataForm['city'] . '/' . $dataForm['state'] . ' - ' . $dataForm['postalCode'];

                foreach ($vendedoras as $vendedora) {

                    $resultado['produtos'] = [];
                    $resultado['produtos'] = $vendedora['produtos'];

                    unset($vendedora['produtos']);
                    $resultado['vendedora'] = $vendedora;

                    $project['nome_vendedora'] = $vendedora['nome'];
                    $project['email_vendedora'] = $vendedora['email'];

                    // Enviar e-mail de verificação
                    $pedido_link = INCLUDE_PATH . "user/compra?pedido=" . $pedido_id;
                    $subject = "Pedido #$pedido_id gerado com sucesso em " . $project['name'];
                    $content = array("layout" => "pedido-recebido-vendedora", "content" => array("name" => $dataForm['name'], "pedido" => $resultado, "link" => $pedido_link));
                    $mail1 = sendMail($dataForm['name'], $dataForm['email'], $project, $subject, $content);

                    unset(
                        $project['nome_vendedora'],
                        $project['email_vendedora']
                    );

                }

                $response = [
                    "status" => 200,
                    "code" => $payment['id'],
                    "id" => $customer_id,
                    "order" => $pedido_id
                ];

                if (!empty($mail1)) {
                    $response["email_status"] = [
                        "pedido_recebido" => $mail1
                    ];
                }

                echo json_encode($response);

                break;
            case '101':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment = asaas_CriarCobrancaBoleto($customer_id, $dataForm, $config);
                $boleto = asaas_ObterLinhaDigitavelBoleto($subscription_id, $payment['id'], $config);
                // $shipment = melhorEnvioGetTracking($_POST['shipping'], $config);
                $pedido_id = salvarPedido($customer_id, $boleto, $payment, $shipment, $dataForm, $compra, $produtos, $config);

                $shipment_id = $_POST['shipping']['service_id'];
                $etiqueta = criarGerarEtiqueta($config, $dataForm, $produtos, $compra, $remetente);

                $resultado['compra']['data'] = date('d/m/Y');
                $resultado['compra']['id'] = $pedido_id;
                $resultado['compra']['pagamento'] = 'Boleto';

                $resultado['compra']['endereco'] = $dataForm['street'] . ', ' . $dataForm['addressNumber'];
                if (!empty($dataForm['complement'])) {
                    $resultado['compra']['endereco'] .= ' - ' . $dataForm['complement'];
                }
                $resultado['compra']['endereco'] .= ', ' . $dataForm['district'] . ' - ' . $dataForm['city'] . '/' . $dataForm['state'] . ' - ' . $dataForm['postalCode'];

                foreach ($vendedoras as $vendedora) {

                    $resultado['produtos'] = [];
                    $resultado['produtos'] = $vendedora['produtos'];

                    unset($vendedora['produtos']);
                    $resultado['vendedora'] = $vendedora;

                    $project['nome_vendedora'] = $vendedora['nome'];
                    $project['email_vendedora'] = $vendedora['email'];

                    // Enviar e-mail de verificação
                    $pedido_link = INCLUDE_PATH . "user/compra?pedido=" . $pedido_id;
                    $subject = "Pedido #$pedido_id gerado com sucesso em " . $project['name'];
                    $content = array("layout" => "pedido-recebido-vendedora", "content" => array("name" => $dataForm['name'], "pedido" => $resultado, "link" => $pedido_link));
                    $mail1 = sendMail($dataForm['name'], $dataForm['email'], $project, $subject, $content);

                    unset(
                        $project['nome_vendedora'],
                        $project['email_vendedora']
                    );

                }

                $response = [
                    "status" => 200,
                    "code" => $payment['id'],
                    "id" => $customer_id,
                    "order" => $pedido_id
                ];

                if (!empty($mail1)) {
                    $response["email_status"] = [
                        "pedido_recebido" => $mail1,
                    ];
                }

                echo json_encode($response);

                break;
            case '102':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment = asaas_CriarCobrancaPix($customer_id, $dataForm, $config);
                $pix = asaas_ObterQRCodePix($subscription_id, $payment['id'], $config);
                // $shipment = melhorEnvioGetTracking($_POST['shipping'], $config);
                $pedido_id = salvarPedido($customer_id, $pix, $payment, $shipment, $dataForm, $compra, $produtos, $config);

                $shipment_id = $_POST['shipping']['service_id'];
                $etiqueta = criarGerarEtiqueta($config, $dataForm, $produtos, $compra, $remetente);

                $resultado['compra']['data'] = date('d/m/Y');
                $resultado['compra']['id'] = $pedido_id;
                $resultado['compra']['pagamento'] = 'PIX';
        
                $resultado['compra']['endereco'] = $dataForm['street'] . ', ' . $dataForm['addressNumber'];
                if (!empty($dataForm['complement'])) {
                    $resultado['compra']['endereco'] .= ' - ' . $dataForm['complement'];
                }
                $resultado['compra']['endereco'] .= ', ' . $dataForm['district'] . ' - ' . $dataForm['city'] . '/' . $dataForm['state'] . ' - ' . $dataForm['postalCode'];

                foreach ($vendedoras as $vendedora) {

                    $resultado['produtos'] = [];
                    $resultado['produtos'] = $vendedora['produtos'];

                    unset($vendedora['produtos']);
                    $resultado['vendedora'] = $vendedora;

                    $project['nome_vendedora'] = $vendedora['nome'];
                    $project['email_vendedora'] = $vendedora['email'];

                    // Enviar e-mail de verificação
                    $pedido_link = INCLUDE_PATH . "user/compra?pedido=" . $pedido_id;
                    $subject = "Pedido #$pedido_id gerado com sucesso em " . $project['name'];
                    $content = array("layout" => "pedido-recebido-vendedora", "content" => array("name" => $dataForm['name'], "pedido" => $resultado, "pix" => $pix, "link" => $pedido_link));
                    $mail1 = sendMail($dataForm['name'], $dataForm['email'], $project, $subject, $content);

                    unset(
                        $project['nome_vendedora'],
                        $project['email_vendedora']
                    );

                }

                // Enviar e-mail para finalizar o pagamento
                $subject = "Seu código Pix está disponível para pagamento";
                $content = array("layout" => "finalizar-pagamento", "content" => array("name" => $dataForm['name'], "pedido" => $resultado, "pix" => $pix, "link" => $pedido_link));
                $mail2 = sendMail($dataForm['name'], $dataForm['email'], $project, $subject, $content);

                $response = [
                    "status" => 200,
                    "code" => $payment['id'],
                    "id" => $customer_id,
                    "order" => $pedido_id
                ];

                if (!empty($mail1) || !empty($mail2)) {
                    $response["email_status"] = [
                        "pedido_recebido" => $mail1,
                        "finalizar_pagamento" => $mail2
                    ];
                }

                echo json_encode($response);

                break;
            default:
                echo json_encode(['status' => 404, 'message' => 'Método de pagamento inválido!']);
                break;
        }
    }
}

#ETIQUETAS
function criarGerarEtiqueta($config, $dataForm, $produtos, $compra, $remetente) {
    $client = new \GuzzleHttp\Client();

    try {
        // 0. Criar etiqueta
        $payload = [
            "service" => intval($_POST['shipping']['service_id']), // precisa vir da cotação
            "agency"  => $_POST['shipping']['agency_id'] ?? null,
            "from" => [
                "name"        => $remetente['nome'],
                "phone"       => $remetente['telefone'],
                "email"       => $remetente['email'],
                "document"    => $remetente['cpfCnpj'],
                "address"     => $remetente['rua'],
                "complement"  => $remetente['complemento'] ?? "",
                "number"      => $remetente['numero'],
                "district"    => $remetente['bairro'],
                "city"        => $remetente['cidade'],
                "state_abbr"  => $remetente['estado'],
                "country_id"  => "BR",
                "postal_code" => $remetente['cep']
            ],
            "to" => [
                "name"        => $dataForm['name'],
                "phone"       => $dataForm['phone'],
                "email"       => $dataForm['email'],
                "document"    => preg_replace('/[^0-9]/', '', $dataForm['cpfCnpj']),
                "address"     => $dataForm['street'],
                "complement"  => $dataForm['complement'] ?? "",
                "number"      => $dataForm['addressNumber'],
                "district"    => $dataForm['district'],
                "city"        => $dataForm['city'],
                "state_abbr"  => $dataForm['state'],
                "country_id"  => "BR",
                "postal_code" => preg_replace('/[^0-9]/', '', $dataForm['postalCode'])
            ],
            "products" => array_map(function($produto) {
                return [
                    "name"   => $produto['nome'],
                    "quantity" => $produto['quantidade'],
                    "unitary_value" => $produto['preco'],
                    "weight" => floatval($produto['peso']),
                    "length" => intval($produto['altura']),
                    "height" => intval($produto['largura']),
                    "width"  => intval($produto['comprimento']),
                ];
            }, $produtos),
            "volumes" => array_map(function($produto) {
                return [
                    "weight" => floatval($produto['peso']),
                    "length" => intval($produto['altura']),
                    "height" => intval($produto['largura']),
                    "width"  => intval($produto['comprimento']),
                ];
            }, $produtos),
            "options" => [
                "insurance_value" => max(1, floatval($compra['total'])),
                "receipt" => false,
                "own_hand" => false,
                "reverse" => false,
                "non_commercial" => true
            ]
        ];

        $cartResponse = $client->post($config['melhor_envio_url'] . 'me/cart', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $config['melhor_envio_token']
            ],
            'json' => $payload
        ]);

        $cartData = json_decode($cartResponse->getBody()->getContents(), true);

        // Pega o ID da etiqueta criada
        $orderId = $cartData['id'] ?? null;
        if (!$orderId) {
            throw new Exception("Nenhuma etiqueta foi criada no carrinho");
        }

        // 1. Realizar o checkout
        $checkout = $client->request('POST', $config['melhor_envio_url'] . "me/shipment/checkout", [
            "form_params" => [
                'orders' => [$orderId]
            ],
            "headers" => [
                "Accept" => "application/json",
                "Content-Type" => "application/x-www-form-urlencoded",
                "Authorization" => "Bearer " . trim($config['melhor_envio_token'])
            ],
        ]);

        $checkoutData = json_decode($checkout->getBody()->getContents(), true);
        $purchaseId = $checkoutData['purchase']['id'];

        // 2. Gerar a etiqueta
        $generate = $client->request('POST', $config['melhor_envio_url'] . "me/shipment/generate", [
            "form_params" => [
                'orders' => [$orderId]
            ],
            "headers" => [
                "Accept" => "application/json",
                "Content-Type" => "application/x-www-form-urlencoded",
                "Authorization" => "Bearer " . trim($config['melhor_envio_token'])
            ],
        ]);

    } catch (Exception $e) {
        error_log("Erro gerar etiqueta: " . $e->getMessage());
        return [
            "status" => false,
            "message" => $e->getMessage()
        ];
    }
}



// === Função utilitária para gerar etiquetas ===
function gerarEtiquetaMelhorEnvio($dataForm, $config, $produtos, $compra) {
    $client = new GuzzleHttp\Client();

    $payload = [
        "service" => $_POST['shipping']['service_id'], // precisa vir da cotação
        "agency"  => $_POST['shipping']['agency_id'] ?? null,
        "from" => [
            "name"        => "Loja Exemplo", // <<< SUBSTITUA PELOS SEUS DADOS FIXOS
            "phone"       => "11999999999",
            "email"       => "contato@sualoja.com.br",
            "document"    => "00000000000", // CPF ou CNPJ da loja
            "address"     => "Rua de Origem",
            "complement"  => "Sala 1",
            "number"      => "123",
            "district"    => "Centro",
            "city"        => "São Paulo",
            "state_abbr"  => "SP",
            "country_id"  => "BR",
            "postal_code" => "01001000"
        ],
        "to" => [
            "name"        => $dataForm['name'],
            "phone"       => $dataForm['phone'] ?? "11999999999",
            "email"       => $dataForm['email'],
            "document"    => $dataForm['cpf'] ?? "00000000000",
            "address"     => $dataForm['street'],
            "complement"  => $dataForm['complement'] ?? "",
            "number"      => $dataForm['addressNumber'],
            "district"    => $dataForm['district'],
            "city"        => $dataForm['city'],
            "state_abbr"  => $dataForm['state'],
            "country_id"  => "BR",
            "postal_code" => preg_replace('/[^0-9]/', '', $dataForm['postalCode'])
        ],
        "products" => array_map(function($produto) {
            return [
                "name"   => $produto['nome'],
                "quantity" => $produto['quantidade'],
                "unitary_value" => $produto['preco'],
                "weight" => 0.3, // <<< AJUSTAR peso real
                "length" => 16,  // <<< AJUSTAR dimensões reais
                "height" => 2,
                "width"  => 11
            ];
        }, $produtos),
        "options" => [
            "insurance_value" => $compra['total'],
            "receipt" => false,
            "own_hand" => false,
            "reverse" => false,
            "non_commercial" => true
        ]
    ];

    try {
        $response = $client->post($config['melhor_envio_url'] . "shipments", [
            "headers" => [
                "Accept" => "application/json",
                "Authorization" => "Bearer " . $config['melhor_envio_token'],
                "Content-Type" => "application/json"
            ],
            "json" => $payload
        ]);

        return json_decode($response->getBody(), true);
    } catch (Exception $e) {
        error_log("Erro ao gerar etiqueta: " . $e->getMessage());
        return null;
    }
}

