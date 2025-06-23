<?php
// =============================================================================
// 1) BOOTSTRAP: CARREGA COMPOSER E VARIÁVEIS DE AMBIENTE
// -----------------------------------------------------------------------------

require __DIR__ . '/vendor/autoload.php';

// Carrega .env (dotenv)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Carrega config.php (onde vamos incluir a chave 'theme' em pouco)
include __DIR__ . '/config.php';


// =============================================================================
// 2) INICIALIZAÇÃO DO TEMPLATING (Twig)
// -----------------------------------------------------------------------------

// Descobre qual tema está ativo (Ankara ou TerraDourada). Em config.php já
// deve haver algo como 'theme' => 'Ankara' (ou 'TerraDourada').
$temaAtivo = ACTIVE_THEME;

// Define os diretórios de templates—primeiro o tema ativo, depois o fallback:
$templateDirs = [
    __DIR__ . "/templates/{$temaAtivo}",
    __DIR__ . "/templates/_fallback",
];

// Cria o loader do Twig
$loader = new \Twig\Loader\FilesystemLoader($templateDirs);

// Instancia o ambiente do Twig
$twig = new \Twig\Environment($loader, [
    'cache'       => __DIR__ . '/cache/twig',  // opcional, pode comentar se não quiser cache
    'auto_reload' => true,                    // recompila se mudar o template
]);

$twig->addGlobal('INCLUDE_PATH', INCLUDE_PATH);
$twig->addGlobal('INCLUDE_PATH_ADMIN', INCLUDE_PATH_ADMIN);

// Injeta a variável global 'app.theme' para usar nos templates
$twig->addGlobal('app', [
    'theme' => $temaAtivo
]);


// =============================================================================
// 3) LÓGICA DE ROTEAMENTO / PARÂMETROS GERAIS
// -----------------------------------------------------------------------------

// 3.1) Pega a “URL amigável” ou fallback para 'produtos'
$url = isset($_GET['url']) ? $_GET['url'] : 'produtos';
$link = '';

// Se for URL começando com "p/", converte para rota 'produto'
if (strpos($url, 'p/') === 0) {
    $link = substr($url, 2);
    $url  = 'produto';
}

// 3.2) Busca o tipo de captcha via BD (você já tinha isso)
$query = "SELECT captcha_type AS type FROM tb_page_captchas WHERE page_name = :page_name";
$stmt  = $conn->prepare($query);
$stmt->bindValue(':page_name', 'doacao');
$stmt->execute();
$captcha = $stmt->fetch(PDO::FETCH_ASSOC);

if ($captcha['type'] === 'hcaptcha') {
    $hcaptcha = [ 'public_key' => $_ENV['HCAPTCHA_CHAVE_DE_SITE'] ];
} elseif ($captcha['type'] === 'turnstile') {
    $turnstile = [ 'public_key' => $_ENV['TURNSTILE_CHAVE_DE_SITE'] ];
}

// 3.3) Inicia sessão e calcula $usuario se estiver logado
session_start();
ob_start();

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, roles FROM tb_clientes WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 3.4) Consulta para pegar configurações gerais (checkout, integrações, mensagens)
$tabela   = "tb_checkout";
$tabela2  = "tb_integracoes";
$tabela3  = "tb_mensagens";
$id       = 1;

$sql    = "SELECT *          FROM $tabela   WHERE id = :id";
$sql2   = "SELECT *          FROM $tabela2  WHERE id = :id";
$sql3   = "SELECT use_privacy FROM $tabela3 WHERE id = :id";

$stmt   = $conn->prepare($sql);
$stmt2  = $conn->prepare($sql2);
$stmt3  = $conn->prepare($sql3);

$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt2->bindParam(':id', $id, PDO::PARAM_INT);
$stmt3->bindParam(':id', $id, PDO::PARAM_INT);

$stmt->execute();
$stmt2->execute();
$stmt3->execute();

$resultado   = $stmt->fetch(PDO::FETCH_ASSOC);
$resultado2  = $stmt2->fetch(PDO::FETCH_ASSOC);
$resultado3  = $stmt3->fetch(PDO::FETCH_ASSOC);

if (!$resultado || !$resultado2 || !$resultado3) {
    // Caso não encontre algo, é melhor redirecionar ou exibir erro genérico
    die("Erro ao carregar configurações do sistema.");
}

// Mapeia as variáveis vindas do $resultado (checkout)
$nome                          = $resultado['nome'];
$logo                          = $resultado['logo'];
$title                         = $resultado['title'];
$descricao                     = $resultado['descricao'];
$vitrine_limite                = $resultado['vitrine_limite'];
$privacidade                   = $resultado['privacidade'];
$faq                           = $resultado['faq'];
$use_faq                       = $resultado['use_faq'];
$facebook                      = $resultado['facebook'];
$instagram                     = $resultado['instagram'];
$linkedin                      = $resultado['linkedin'];
$twitter                       = $resultado['twitter'];
$youtube                       = $resultado['youtube'];
$website                       = $resultado['website'];
$tiktok                        = $resultado['tiktok'];
$linktree                      = $resultado['linktree'];
$cep                           = $resultado['cep'];
$rua                           = $resultado['rua'];
$numero                        = $resultado['numero'];
$bairro                        = $resultado['bairro'];
$cidade                        = $resultado['cidade'];
$estado                        = $resultado['estado'];
$telefone                      = $resultado['telefone'];
$email                         = $resultado['email'];
$nav_color                     = $resultado['nav_color'];
$nav_background                = $resultado['nav_background'];
$background                    = $resultado['background'];
$text_color                    = $resultado['text_color'];
$color                         = $resultado['color'];
$hover                         = $resultado['hover'];
$progress                      = $resultado['progress'];

// Mapeia as variáveis vindas de $resultado2 (integrações)
$fb_pixel       = $resultado2['fb_pixel'];
$gtm            = $resultado2['gtm'];
$g_analytics    = $resultado2['g_analytics'];

// Mapeia variáveis vindas de $resultado3 (política de privacidade)
$use_privacy    = $resultado3['use_privacy'];


// 3.5) Calcula $cartCount (o número de itens no carrinho)
if (isset($_SESSION['user_id'])) {
    $field = 'usuario_id';
    $value = $_SESSION['user_id'];
} elseif (isset($_COOKIE['cart_id'])) {
    $field = 'cookie_id';
    $value = $_COOKIE['cart_id'];
} else {
    $cartCount = 0;
}

if (isset($field) && isset($value)) {
    $sqlCart = "SELECT COALESCE(SUM(quantidade),0) AS total_items
                FROM tb_carrinho
                WHERE {$field} = ?";
    $stmtCart = $conn->prepare($sqlCart);
    $stmtCart->execute([$value]);
    $cartCount = (int) $stmtCart->fetchColumn();
}


// =============================================================================
// 4) PREPARA O CONTEXTO PARA O TWIG
// -----------------------------------------------------------------------------

// Todas as variáveis que hoje eram ecoadas diretamente no HTML, agora ficam
// dentro de um array $context que será passado ao Twig. Se você precisar usar
// outras variáveis em alguma view específica, basta adicionar neste array.

$context = [
    // Informações básicas
    'logo'            => $logo,
    'nome'            => $nome,
    'title'           => $title,
    'descricao'       => $descricao,
    'vitrine_limite'  => $vitrine_limite,
    'nav_color'       => $nav_color,
    'nav_background'  => $nav_background,
    'background'      => $background,
    'text_color'      => $text_color,
    'color'           => $color,
    'hover'           => $hover,
    'progress'        => $progress,

    // Redes sociais e links
    'facebook'        => $facebook,
    'instagram'       => $instagram,
    'linkedin'        => $linkedin,
    'twitter'         => $twitter,
    'youtube'         => $youtube,
    'website'         => $website,
    'tiktok'          => $tiktok,
    'linktree'        => $linktree,

    // Endereço
    'cep'             => $cep,
    'rua'             => $rua,
    'numero'          => $numero,
    'bairro'          => $bairro,
    'cidade'          => $cidade,
    'estado'          => $estado,

    // Contato
    'telefone'        => $telefone,
    'email'           => $email,

    // Integrações (pixel, gtm, analytics)
    'fb_pixel'        => $fb_pixel,
    'gtm'             => $gtm,
    'g_analytics'     => $g_analytics,

    // Política de privacidade
    'use_privacy'     => $use_privacy,
    'privacidade'     => $privacidade,

    // FAQ
    'use_faq'         => $use_faq,
    'faq'             => $faq,

    // Carrinho
    'cartCount'       => $cartCount,

    // Captcha (se houver)
    'hcaptcha'        => $hcaptcha ?? null,
    'turnstile'       => $turnstile ?? null,

    // Caso queira passar $usuario autenticado:
    'usuario'         => $usuario ?? null,
];

switch ($url) {
    case 'produto':
        // Recebe o array de contexto montado dentro de pages/produto.php:
        $context_produto = include __DIR__ . '/pages/produto.php';

        $context = array_merge($context, $context_produto);
        break;

    case 'produtos':
        // Recebe o array de contexto montado dentro de pages/produtos.php:
        $context_produtos = include __DIR__ . '/pages/produtos.php';

        $context = array_merge($context, $context_produtos);
        break;

    case 'empreendedoras':
        // Recebe o array de contexto montado dentro de pages/empreendedoras.php:
        $context_empreendedoras = include __DIR__ . '/pages/empreendedoras.php';

        $context = array_merge($context, $context_empreendedoras);
        break;

    case 'empreendedora':
        // Recebe o array de contexto montado dentro de pages/empreendedora.php:
        $context_empreendedora = include __DIR__ . '/pages/empreendedora.php';

        $context = array_merge($context, $context_empreendedora);
        break;

    case 'blog':
        // Recebe o array de contexto montado dentro de pages/blog.php:
        $context_blog = include __DIR__ . '/pages/blog.php';

        $context = array_merge($context, $context_blog);
        break;

    case 'post':
        // Recebe o array de contexto montado dentro de pages/post.php:
        $context_post = include __DIR__ . '/pages/post.php';

        $context = array_merge($context, $context_post);
        break;

    case 'categoria':
        // Recebe o array de contexto montado dentro de pages/categoria.php:
        $context_categoria = include __DIR__ . '/pages/categoria.php';

        $context = array_merge($context, $context_categoria);
        break;

    case 'carrinho':
        // Recebe o array de contexto montado dentro de pages/carrinho.php:
        $context_carrinho = include __DIR__ . '/pages/carrinho.php';

        $context = array_merge($context, $context_carrinho);
        break;

    case 'checkout':
        // Recebe o array de contexto montado dentro de pages/checkout.php:
        $context_checkout = include __DIR__ . '/pages/checkout.php';

        $context = array_merge($context, $context_checkout);
        break;

    case 'contato':
        include __DIR__ . '/pages/contato.php';
        break;
}

// =============================================================================
// 5) RENDERIZAÇÃO FINAL COM TWIG
// -----------------------------------------------------------------------------

// Em vez de incluir diretamente 'pages/'.$url.'.php', vamos chamar um template:
//  - Primeiro ele tenta em templates/{TemaAtivo}/pages/{url}.html.twig
//  - Se não encontrar, busca em templates/_fallback/pages/{url}.html.twig
//
// Em cada template de página, você vai herdar do 'layouts/base.html.twig'
// (que também fica dentro de templates/{TemaAtivo}/layouts ou _fallback/layouts).

try {
    // Exemplo: se $url = 'produtos', vai tentar 'pages/produtos.html.twig'
    $conteudoDaPagina = $twig->render("pages/{$url}.html.twig", $context);

    // Depois injetamos esse conteúdo dentro do layout principal:
    echo $twig->render('layouts/base.html.twig', array_merge($context, [
        // 'page_content' será o bloco de conteúdo específico de cada rota
        'page_content' => $conteudoDaPagina
    ]));

} catch (\Twig\Error\LoaderError   $e) {
    // Se o template não existir em nenhum dos diretórios, cai aqui.
    // Podemos redirecionar para uma página 404 no Twig, por exemplo:
    echo $twig->render('pages/404.html.twig', $context);

} catch (\Twig\Error\RuntimeError  $e) {
    echo "Erro de execução no template: " . $e->getMessage();
} catch (\Twig\Error\SyntaxError   $e) {
    echo "Erro de sintaxe no template: " . $e->getMessage();
}