<?php
    // Caso prefira o .env apenas descomente o codigo e comente o "include('parameters.php');" acima
	// Carrega as variáveis de ambiente do arquivo .env
	require __DIR__.'/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	// Acessa as variáveis de ambiente
	$dbHost = $_ENV['DB_HOST'];
	$dbUsername = $_ENV['DB_USERNAME'];
	$dbPassword = $_ENV['DB_PASSWORD'];
	$dbName = $_ENV['DB_NAME'];
	$port = $_ENV['DB_PORT'];

    try{
        //Conexão com a porta
        $conn = new PDO("mysql:host=$dbHost;port=$port;dbname=" . $dbName, $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Conexão sem a porta
        //$conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
        //echo "Conexão com banco de dados realizado com sucesso!";
    } catch (PDOException $e) {
        // Tratamento de erros
        //echo 'Erro de conexão com o banco de dados: ' . $e->getMessage();
    }
    
    // Projeto
	$project = [
        'name' => $_ENV['PROJECT_NAME'],
        'version' => $_ENV['PROJECT_VERSION']
    ];
    
    define('INCLUDE_PATH', $_ENV['URL']);
    define('INCLUDE_PATH_ADMIN',INCLUDE_PATH.'admin/');
    define('INCLUDE_PATH_USER',INCLUDE_PATH.'user/');

    /**
     * Verifica se o usuário tem permissão para realizar uma ação em uma página.
     *
     * @param int    $usuarioId   ID do usuário logado.
     * @param string $link        Link da página (ex.: "produtos").
     * @param string $acaoTipo    Tipo da ação (ex.: 'read', 'create', 'update', 'delete').
     * @param PDO    $conn        Instância da conexão PDO.
     *
     * @return bool True se permitido, False caso contrário.
    **/
    function verificaPermissao(int $usuarioId, string $link, string $acaoTipo, PDO $conn): bool {
        // 0. Verifica se o usuário é administrador consultando a tabela tb_clientes.
        $stmt = $conn->prepare("SELECT roles FROM tb_clientes WHERE id = ?");
        $stmt->execute([$usuarioId]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cliente && $cliente['roles'] == 1) {
            // Se roles igual a 1, o usuário é administrador e tem acesso irrestrito.
            return true;
        }

        // 1. Obter a função do usuário (supondo que a tabela tb_permissao_usuario possua a coluna "permissao_id")
        $stmt = $conn->prepare("SELECT permissao_id AS funcao_id FROM tb_permissao_usuario WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            return false; // Usuário não encontrado ou sem permissão cadastrada
        }
        $funcaoId = $usuario['funcao_id'];

        // 2. Obter o ID da página com base no link
        $stmt = $conn->prepare("SELECT id FROM tb_paginas WHERE link = ?");
        $stmt->execute([$link]);
        $pagina = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pagina) {
            return false; // Página não encontrada
        }
        $paginaId = $pagina['id'];

        // 3. Obter o ID da ação com base no tipo da ação (ex.: 'read', 'create', etc.)
        $stmt = $conn->prepare("SELECT id FROM tb_acoes WHERE tipo = ?");
        $stmt->execute([$acaoTipo]);
        $acao = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$acao) {
            return false; // Ação não encontrada
        }
        $acaoId = $acao['id'];

        // 4. Verificar na tabela de permissões se há registro para a combinação: página, função e ação.
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_permissao_funcao WHERE pagina_id = ? AND funcao_id = ? AND acao_id = ?");
        $stmt->execute([$paginaId, $funcaoId, $acaoId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($resultado['total'] > 0);
    }
?>