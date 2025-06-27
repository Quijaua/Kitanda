<?php
	function asaas_CriarCliente($dataForm, $config) {

		include('config.php');

		unset($dataForm['value']);

		$tabela = "tb_clientes";

		$newsletter     = (isset($dataForm['newsletter']) && $dataForm['newsletter']=='1') ? 1 : 0;
		$foreignCustomer = (isset($dataForm['foreignCustomer']) && $dataForm['foreignCustomer']=='1') ? 1 : 0;

		// Verifica existência do cliente local
		$sql  = "SELECT id, email, asaas_id FROM $tabela WHERE email = :email";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':email', $dataForm['email'], PDO::PARAM_STR);
		$stmt->execute();
		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

		// Helper para criar cliente no Asaas e atualizar local
		$criarEAtualizar = function($dataForm, $localId = null) use ($conn, $tabela, $config, $newsletter, $application_name) {
			// POST /customers
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL            => $config['asaas_api_url'].'customers',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST  => 'POST',
				CURLOPT_POSTFIELDS     => json_encode($dataForm),
				CURLOPT_HTTPHEADER     => [
					'Content-Type: application/json',
					'access_token: '.$config['asaas_api_key'],
					'User-Agent: '.$application_name
				],
			]);
			$response = curl_exec($curl);
			curl_close($curl);
			$retorno  = json_decode($response, true);

			if (isset($retorno['object']) && $retorno['object'] === 'customer') {
				$asaasId = $retorno['id'];
				if ($localId) {
					// Atualiza registro existente
					$upd = $conn->prepare("
						UPDATE $tabela SET asaas_id = :asaas_id WHERE id = :id
					");
					$upd->bindParam(':asaas_id', $asaasId, PDO::PARAM_STR);
					$upd->bindParam(':id', $localId, PDO::PARAM_INT);
					$upd->execute();
				} else {
					// Insere novo registro
					$ins = $conn->prepare("
						INSERT INTO $tabela
							(roles,nome,email,phone,cpf,cep,endereco,numero,complemento,municipio,cidade,uf,pais,estrangeiro,asaas_id,newsletter)
						VALUES
							(:roles,:name,:email,:phone,:cpfCnpj,:postalCode,:address,:addressNumber,:complement,:province,:city,:state,:country,:foreign,:id,:newsletter)
					");
					$roles = 0;
					// ...binds iguais aos seus originais...
					$ins->bindParam(':roles', $roles, PDO::PARAM_INT);
					$ins->bindParam(':name', $retorno['name'], PDO::PARAM_STR);
					$ins->bindParam(':email', $retorno['email'], PDO::PARAM_STR);
					$ins->bindParam(':phone', $dataForm['phone'], PDO::PARAM_STR);
					$ins->bindParam(':cpfCnpj', $dataForm['cpfCnpj'], PDO::PARAM_STR);
					$ins->bindParam(':postalCode', $dataForm['postalCode'], PDO::PARAM_STR);
					$ins->bindParam(':address', $retorno['address'], PDO::PARAM_STR);
					$ins->bindParam(':addressNumber', $retorno['addressNumber'], PDO::PARAM_INT);
					$ins->bindParam(':complement', $retorno['complement'], PDO::PARAM_STR);
					$ins->bindParam(':province', $retorno['province'], PDO::PARAM_STR);
					$ins->bindParam(':city', $dataForm['city'], PDO::PARAM_STR);
					$ins->bindParam(':state', $retorno['state'], PDO::PARAM_STR);
					$ins->bindParam(':country', $country, PDO::PARAM_STR);
					$ins->bindParam(':foreign', $foreignCustomer, PDO::PARAM_INT);
					$ins->bindParam(':id', $asaasId, PDO::PARAM_STR);
					$ins->bindParam(':newsletter', $newsletter, PDO::PARAM_INT);
					$ins->execute();
					$localId = $conn->lastInsertId();
				}
				$_SESSION['user_id'] = $localId;
				$_SESSION['email']   = $retorno['email'];
				return $asaasId;
			} else {
				// Erro na API
				echo $response;
				exit;
			}
		};

		if ($resultado) {
			// Se já tiver asaas_id, valida no Asaas
			if (!empty($resultado['asaas_id'])) {
				$existingId = $resultado['asaas_id'];

				// GET /customers/{id} para verificar existência
				$curl = curl_init();
				curl_setopt_array($curl, [
					CURLOPT_URL            => $config['asaas_api_url']."customers/{$existingId}",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST  => 'GET',
					CURLOPT_HTTPHEADER     => [
						'access_token: '.$config['asaas_api_key'],
						'User-Agent: '.$application_name
					],
				]);
				$response = curl_exec($curl);
				$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);

				// Se 2xx => existe, retorno direto
				if ($httpCode >= 200 && $httpCode < 300) {
					$_SESSION['user_id'] = $resultado['id'];
					$_SESSION['email']   = $resultado['email'];
					return $existingId;
				}

				// Se não existir ou erro, recria e atualiza local
				return $criarEAtualizar($dataForm, $resultado['id']);
			}

			// Cliente local mas sem asaas_id: cria e atualiza
			return $criarEAtualizar($dataForm, $resultado['id']);
		}

		// Cliente não existe local: cria novo e salva
		return $criarEAtualizar($dataForm, null);
	}