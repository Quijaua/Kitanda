<?php
	function asaas_CriarCliente($dataForm, $config) {

		include('config.php');

		//removendo campos que não serão usados para incluir o cliente
		unset($dataForm['value']);

		// Tabela que sera feita a consulta
		$tabela = "tb_clientes";

		// Armazena a opção newsletter
        if (isset($dataForm['newsletter']) && $dataForm['newsletter'] == '1') {
            $newsletter = $dataForm['newsletter'];
        } else {
            $newsletter = 0;
        }

		// Armazena a opção foreignCustomer
        if (isset($dataForm['foreignCustomer']) && $dataForm['foreignCustomer'] == '1') {
            $foreignCustomer = $dataForm['foreignCustomer'];
        } else {
            $foreignCustomer = 0;
        }

		// Consulta SQL
		$sql = "SELECT id, asaas_id FROM $tabela WHERE email = :email";

		// Preparar a consulta
		$stmt = $conn->prepare($sql);

		// Vincular o valor do parâmetro
		$stmt->bindParam(':email', $dataForm["email"], PDO::PARAM_STR);

		// Executar a consulta
		$stmt->execute();

		// Obter o resultado como um array associativo
		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

		// Verifica se a consulta retornou algum resultado
		if ($stmt->rowCount() > 0 && $resultado) {
			// Cliente encontrado
			if (!empty($resultado['asaas_id'])) {
				// Já tem asaas_id, retorna direto
				return $resultado['asaas_id'];
			} else {
				// Cliente existe mas não tem asaas_id, criar no Asaas e atualizar
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $config['asaas_api_url'].'customers',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => json_encode($dataForm),
					CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json',
						'access_token: '.$config['asaas_api_key'],
						'User-Agent: '.$application_name
					)
				));
				$response = curl_exec($curl);
				curl_close($curl);

				$retorno = json_decode($response, true);

				if ($retorno['object'] == 'customer') {
					// Atualizar cliente existente com o novo asaas_id
					$update = $conn->prepare("
						UPDATE $tabela SET 
						asaas_id = :asaas_id
						WHERE id = :id
					");

					$update->bindParam(':asaas_id', $retorno['id'], PDO::PARAM_STR);
					$update->bindParam(':id', $resultado['id'], PDO::PARAM_INT);
					$update->execute();

					return $retorno['id'];
				} else {
					echo $response;
					exit();
				}
			}
		} else {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $config['asaas_api_url'].'customers',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode($dataForm),
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'access_token: '.$config['asaas_api_key'],
					'User-Agent: '.$application_name
				)
			));
			$response = curl_exec($curl);
			curl_close($curl);

			$retorno = json_decode($response, true);

			if($retorno['object'] == 'customer') {

				$tabela = 'tb_clientes';

				$stmt = $conn->prepare("INSERT INTO $tabela (roles, nome, email, phone, cpf, cep, endereco, numero, complemento, municipio, cidade, uf, pais, estrangeiro, asaas_id, newsletter) VALUES (
					:roles, :name, :email, :phone, :cpfCnpj, :postalCode, :address, :addressNumber, :complement, :province, :city, :state, :country, :foreign, :id, :newsletter)");

				$roles = 0;

				// Bind dos parâmetros
				$stmt->bindParam(':roles', $roles, PDO::PARAM_INT);
				$stmt->bindParam(':name', $retorno['name'], PDO::PARAM_STR);
				$stmt->bindParam(':email', $retorno['email'], PDO::PARAM_STR);
				$stmt->bindParam(':phone', $dataForm['phone'], PDO::PARAM_STR);
				$stmt->bindParam(':cpfCnpj', $dataForm['cpfCnpj'], PDO::PARAM_STR);
				$stmt->bindParam(':postalCode', $dataForm['postalCode'], PDO::PARAM_STR);
				$stmt->bindParam(':address', $retorno['address'], PDO::PARAM_STR);
				$stmt->bindParam(':addressNumber', $retorno['addressNumber'], PDO::PARAM_INT);
				$stmt->bindParam(':complement', $retorno['complement'], PDO::PARAM_STR);
				$stmt->bindParam(':province', $retorno['province'], PDO::PARAM_STR);
				$stmt->bindParam(':city', $dataForm['city'], PDO::PARAM_STR);
				$stmt->bindParam(':state', $retorno['state'], PDO::PARAM_STR);
				$stmt->bindParam(':country', $country, PDO::PARAM_STR);
				$stmt->bindParam(':foreign', $foreignCustomer, PDO::PARAM_INT);
				$stmt->bindParam(':id', $retorno['id'], PDO::PARAM_STR);
				$stmt->bindParam(':newsletter', $newsletter, PDO::PARAM_INT);

				// Executando o update
				$stmt->execute();
				$usuario_id = $conn->lastInsertId();

				// Atribuir o valor da coluna à variável, ex.: "nome" = $nome
				$_SESSION['user_id'] = $usuario_id;

				return $retorno['id'];
			} else {
				echo $response;
				exit();
			}
		}
	}