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
        'message' => 'Requisição processada com sucesso.'
    );

    echo json_encode($response);
    exit; // Encerra o script aqui para evitar processamento adicional
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
        $dataForm['email'] = $dataForm['eee'];

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
            $stmt = $conn->prepare("SELECT id, nome, preco FROM tb_produtos WHERE id = ?");
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Se o produto for encontrado, monta os dados do item
            if ($produto) {
                // Adiciona a quantidade e calcula o subtotal para esse item
                $produto['quantidade'] = $quantity;
                $produto['preco_total'] = $produto['preco'] * $quantity;
                $subtotal += $produto['preco_total'];
                
                // Adiciona o produto ao array de produtos
                $produtos[] = $produto;
            }
        }

        // Define um valor fixo para o frete (por exemplo, 10 reais)
        $frete = 10.00;

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













        $dataForm['value'] = $compra['total'];



        include('config.php');
        include_once('criar_cliente.php');
        include_once('cobranca_cartao.php');
        include_once('cobranca_pix.php');
        include_once('cobranca_boleto.php');
        include_once('listar_cobranca_assinatura.php');
        include_once('qr_code.php');
        include_once('linha_digitavel.php');
        include_once('salvar_pedido.php');
    
        switch($_POST["method"]) {
            case '100':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment = asaas_CriarCobrancaCartao($customer_id, $dataForm, $config);
                $pedido_id = salvarPedido($customer_id, null, $payment, $dataForm, $compra, $produtos, $config);
                echo json_encode(["status"=>200, "code"=>$payment['id'], "id"=>$customer_id, "order" => $pedido_id]);
                break;
            case '101':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment = asaas_CriarCobrancaBoleto($customer_id, $dataForm, $config);
                $boleto = asaas_ObterLinhaDigitavelBoleto($subscription_id, $payment['id'], $config);
                $pedido_id = salvarPedido($customer_id, $boleto, $payment, $dataForm, $compra, $produtos, $config);
                echo json_encode(["status"=>200, "code"=>$payment['id'], "id"=>$customer_id, "order" => $pedido_id]);
                break;
            case '102':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment = asaas_CriarCobrancaPix($customer_id, $dataForm, $config);
                $pix = asaas_ObterQRCodePix($subscription_id, $payment['id'], $config);
                $pedido_id = salvarPedido($customer_id, $pix, $payment, $dataForm, $compra, $produtos, $config);
                echo json_encode(["status"=>200, "code"=>$payment['id'], "id"=>$customer_id, "order" => $pedido_id]);
                break;
            default:
                echo json_encode(['status' => 404, 'message' => 'Método de pagamento inválido!']);
                break;
        }
    
    }
}