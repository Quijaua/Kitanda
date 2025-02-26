<?php

include('config.php');

$tabela = 'tb_parametros';

//Linha 1
$id = '1'; // Substitua pelo valor desejado

// Consulta no banco de dados
$query = "SELECT valor FROM $tabela WHERE id = :id";
$statement = $conn->prepare($query);
$statement->bindParam(':id', $id);
$statement->execute();

// Obter os resultados
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

// Iterar sobre os resultados
foreach ($results as $row) {
    $config['asaas_api_key'] = $row['valor'];
}

//Linha 2
$id = '2'; // Substitua pelo valor desejado

// Consulta no banco de dados
$query1 = "SELECT valor FROM $tabela WHERE id = :id";
$statement1 = $conn->prepare($query1);
$statement1->bindParam(':id', $id);
$statement1->execute();

// Obter os resultados
$results1 = $statement1->fetchAll(PDO::FETCH_ASSOC);

// Iterar sobre os resultados
foreach ($results1 as $row1) {
    $config['asaas_api_url'] = $row1['valor'];
}

//Linha 3
$id = '3'; // Substitua pelo valor desejado

// Consulta no banco de dados
$query2 = "SELECT valor FROM $tabela WHERE id = :id";
$statement2 = $conn->prepare($query2);
$statement2->bindParam(':id', $id);
$statement2->execute();

// Obter os resultados
$results2 = $statement2->fetchAll(PDO::FETCH_ASSOC);

// Iterar sobre os resultados
foreach ($results2 as $row2) {
    $config['recaptcha_token'] = $row2['valor'];
}

//Linha 4
$id = '4'; // Substitua pelo valor desejado

// Consulta no banco de dados
$query3 = "SELECT valor FROM $tabela WHERE id = :id";
$statement3 = $conn->prepare($query2);
$statement3->bindParam(':id', $id);
$statement3->execute();

// Obter os resultados
$results3 = $statement3->fetchAll(PDO::FETCH_ASSOC);

// Iterar sobre os resultados
foreach ($results3 as $row3) {
    $config['recaptcha_chave_de_site'] = $row3['valor'];
}