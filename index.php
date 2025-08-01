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
} else if (strpos($url, 'pagina/') === 0) {
    $link = substr($url, 7);
    $url  = 'pagina';
} else if ($url === 'politica-de-privacidade/') {
    $url  = 'politica-de-privacidade';
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
$whatsapp                      = $resultado['whatsapp'];
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
$ankara_hero                   = (bool) $resultado['ankara_hero'];
$ankara_colorful               = (bool) $resultado['ankara_colorful'];
$ankara_yellow                 = (bool) $resultado['ankara_yellow'];
$ankara_footer_top             = (bool) $resultado['ankara_footer_top'];
$ankara_footer_blog            = (bool) $resultado['ankara_footer_blog'];
$td_hero                       = (bool) $resultado['td_hero'];
$td_entrepreneurs              = (bool) $resultado['td_entrepreneurs'];
$td_news                       = (bool) $resultado['td_news'];
$td_footer_info                = (bool) $resultado['td_footer_info'];
$td_footer_socials             = (bool) $resultado['td_footer_socials'];

// Mapeia as variáveis vindas de $resultado2 (integrações)
$fb_pixel       = $resultado2['fb_pixel'];
$gtm            = $resultado2['gtm'];
$g_analytics    = $resultado2['g_analytics'];

// Mapeia variáveis vindas de $resultado3 (política de privacidade)
$use_privacy    = $resultado3['use_privacy'];

// Busca os 2 últimos posts para o rodapé
$stmt = $conn->prepare("
    SELECT *
    FROM tb_blog_posts
    ORDER BY data_publicacao DESC
    LIMIT 2
");
$stmt->execute();
$postsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

$footerPosts = [];
foreach ($postsRaw as $post) {
    // URL da imagem de capa (com fallback)
    $imagemUrl = !empty($post['imagem'])
        ? str_replace(' ', '%20', INCLUDE_PATH . "files/blog/{$post['id']}/{$post['imagem']}")
        : INCLUDE_PATH . "assets/preview-image/product.jpg";

    // Busca categorias associadas ao post
    $stmtCat = $conn->prepare("
        SELECT c.id, c.nome
        FROM tb_blog_categoria_posts cp
        JOIN tb_blog_categorias c
          ON cp.categoria_id = c.id
        WHERE cp.post_id = ?
    ");
    $stmtCat->execute([$post['id']]);
    $cats = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    $footerPosts[] = [
        'id'              => $post['id'],
        'titulo'          => $post['titulo'],
        'imagem'          => $imagemUrl,
        'data_publicacao' => $post['data_publicacao'],
        'categorias'      => $cats,
    ];
}

// Busca todas as páginas cadastradas para exibir no rodapé
$stmt = $conn->prepare("SELECT titulo, slug FROM tb_paginas_conteudo ORDER BY id ASC");
$stmt->execute();
$paginasEstaticas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Carregar as primeiras 10 empreendedoras para exibir no carrossel
$limit = 10;
$stmt = $conn->prepare("SELECT * FROM tb_lojas WHERE nome != '' ORDER BY nome LIMIT :limit");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$empreendedoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatar imagens e endereços
foreach ($empreendedoras as &$e) {
    // Imagem de perfil
    $e['tem_imagem'] = !empty($e['imagem']);
    $e['imagem'] = !empty($e['imagem'])
        ? str_replace(
            ' ',
            '%20',
            INCLUDE_PATH . "files/lojas/{$e['id']}/perfil/{$e['imagem']}"
          )
        : INCLUDE_PATH . "assets/preview-image/profile.jpg";

    // Monta o campo "address"
    $address = 'Não informado';
    if (!empty($e['cidade']) && !empty($e['estado'])) {
        $address = htmlspecialchars($e['cidade']) . '/' . htmlspecialchars($e['estado']);
    } elseif (!empty($e['cidade'])) {
        $address = htmlspecialchars($e['cidade']);
    } elseif (!empty($e['estado'])) {
        $address = htmlspecialchars($e['estado']);
    }
    $e['address'] = $address;
}
unset($e);

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
    'is_home' => ($url === 'produtos'),

    // OG defaults
    'og_type'        => 'website',
    'og_title'       => $title,
    'og_description' => $descricao,
    'og_image'       => INCLUDE_PATH . 'assets/img/'.$temaAtivo.'.jpg',

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

    // Thema
    // Ankara
    'ankara_hero'        => $ankara_hero,
    'ankara_colorful'    => $ankara_colorful,
    'ankara_yellow'      => $ankara_yellow,
    'ankara_footer_top'  => $ankara_footer_top,
    'ankara_footer_blog' => $ankara_footer_blog,
    // Terra Dourada
    'td_hero'            => $td_hero,
    'td_entrepreneurs'   => $td_entrepreneurs,
    'td_news'            => $td_news,
    'td_footer_info'     => $td_footer_info,
    'td_footer_socials'  => $td_footer_socials,

    // Redes sociais e links
    'facebook'        => $facebook,
    'instagram'       => $instagram,
    'whatsapp'        => $whatsapp,
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

    // Empreendedoras
    'empreendedoras'  => $empreendedoras ?? [],

    // Posts do rodapé
    'footerPosts'     => $footerPosts ?? [],

    // Páginas do rodapé
    'paginas_estaticas' => $paginasEstaticas ?? [],
];

switch ($url) {
    case 'produto':
        // Recebe o array de contexto montado dentro de pages/produto.php:
        $context_produto = include __DIR__ . '/pages/produto.php';

        $context = array_merge($context, $context_produto);

        $context['og_type']  = 'product';

        $context['og_title']       = $context_produto['produto']['nome'] ?? $title;
        if (!empty($context_produto['produto']['descricao'])) {
            $context['og_description'] = strip_tags($context_produto['produto']['descricao']);
        }

        $firstImage = $context_produto['imagens'][0] ?? null;
        if (!empty($firstImage['imagem'])) {
            $imgPath = $firstImage['imagem'];
            $isAbsolute = str_starts_with($imgPath, 'http');
            $context['og_image'] = $isAbsolute 
                ? $imgPath 
                : str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $imgPath);
        } else {
            $context['og_image'] = INCLUDE_PATH . "assets/preview-image/product.jpg";
        }
        break;

    case 'produtos':
        // Recebe o array de contexto montado dentro de pages/produtos.php:
        $context_produtos = include __DIR__ . '/pages/produtos.php';

        $context = array_merge($context, $context_produtos);
        break;

    case 'categorias':
        // Recebe o array de contexto montado dentro de pages/categorias.php:
        $context_categorias = include __DIR__ . '/pages/categorias.php';

        $context = array_merge($context, $context_categorias);
        break;

    case 'categoria':
        // Recebe o array de contexto montado dentro de pages/categoria.php:
        $context_categoria = include __DIR__ . '/pages/categoria.php';

        $context = array_merge($context, $context_categoria);
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

        $context['og_type']  = 'article';

        $context['og_title']       = $context_post['post']['titulo'] ?? $title;
        if (!empty($context_post['post']['resumo'])) {
            $context['og_description'] = strip_tags($context_post['post']['resumo']);
        }

        if (!empty($context_post['post']['imagem'])) {
            $context['og_image'] = $context_post['post']['imagem'];
        }
        break;

    case 'blog-categoria':
        // Recebe o array de contexto montado dentro de pages/blog-categoria.php:
        $context_blog_categoria = include __DIR__ . '/pages/blog-categoria.php';

        $context = array_merge($context, $context_blog_categoria);
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

    case 'pagina':
        // Recebe o array de contexto montado dentro de pages/pagina.php:
        $context_pagina = include __DIR__ . '/pages/pagina.php';

        $context = array_merge($context, $context_pagina);
        break;

    case 'politica-de-privacidade':
        // Recebe o array de contexto montado dentro de pages/pagina.php:
        $context_politica_privacidade = include __DIR__ . '/pages/politica-de-privacidade.php';

        $context = array_merge($context, $context_politica_privacidade);
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