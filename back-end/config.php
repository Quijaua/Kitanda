<?php
    // Caso prefira o .env apenas descomente o codigo e comente o "include('parameters.php');" acima
	// Carrega as variáveis de ambiente do arquivo .env

    // Caminho para o diretório pai
    $parentDir = dirname(__DIR__);

	require $parentDir . '/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable($parentDir);
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

        // Consulta para obter o nome da aplicação
        $stmt = $conn->query("SELECT nome FROM tb_checkout LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $application_name = $result['nome'];
        } else {
            throw new Exception('Nome da aplicação não encontrado na tabela tb_checkout');
        }

        //Conexão sem a porta
        //$conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
        //echo "Conexão com banco de dados realizado com sucesso!";
    } catch (PDOException $e) {
        // Tratamento de erros
        //echo 'Erro de conexão com o banco de dados: ' . $e->getMessage();
    }

    // Consulta para obter o nome da aplicação
    $stmt = $conn->query("SELECT title, email, logo FROM tb_checkout LIMIT 1");
    $projeto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Projeto
	$project = [
        'name' =>  $projeto['title'] ?? $_ENV['PROJECT_NAME'] ?? "Kitanda",
        'email' =>  $projeto['email'],
        'logo' => !empty($projeto['logo']) ? $_ENV['URL'] . "assets/img/{$projeto['logo']}" : "",
        'version' => $_ENV['PROJECT_VERSION'],
    ];
?>
