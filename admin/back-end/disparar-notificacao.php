<?php

//Inclui o arquivo 'config.php'
include('../../config.php');

session_start();
ob_start();

if (isset($_POST['btnSendOrder']) || isset($_POST['btnOrderDelivered'])) {
    // Verifique se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Informacoes coletadas pelo metodo POST
        $id = $_POST['id'];

        if (isset($_POST['btnSendOrder'])) {
            $codigo_rastreamento = $_POST['codigo_rastreamento'];
            $url_rastreamento = $_POST['url_rastreamento'];
            $data_envio = date('Y-m-d');
        } else {
            $data_entrega = $_POST['data_entrega'];
        }
        $rastreamento_status = isset($_POST['btnSendOrder']) ? 'enviado' : 'entregue';

        if (isset($_POST['btnSendOrder'])) {
            // Atualize o item no banco de dados
            $sql = "UPDATE tb_pedidos SET codigo_rastreamento = :codigo_rastreamento, url_rastreamento = :url_rastreamento, rastreamento_status = :rastreamento_status, data_envio = :data_envio WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':codigo_rastreamento', $codigo_rastreamento);
            $stmt->bindParam(':url_rastreamento', $url_rastreamento);
            $stmt->bindParam(':rastreamento_status', $rastreamento_status);
            $stmt->bindParam(':data_envio', $data_envio);
            $stmt->bindParam(':id', $id);
        } else {
            // Atualize o item no banco de dados
            $sql = "UPDATE tb_pedidos SET rastreamento_status = :rastreamento_status, data_entrega = :data_entrega WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':rastreamento_status', $rastreamento_status);
            $stmt->bindParam(':data_entrega', $data_entrega);
            $stmt->bindParam(':id', $id);
        }

        try {
            $stmt->execute();

            $sql = "SELECT p.*, c.nome, c.email, c.endereco, c.numero, c.complemento, c.municipio, c.cidade, c.uf, c.cep FROM tb_pedidos p JOIN tb_clientes c ON c.id = p.usuario_id WHERE p.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            // Busca os dados reais do produto no banco
            $stmt = $conn->prepare("SELECT pi.*, pimage.imagem AS produto_imagem FROM tb_pedido_itens pi LEFT JOIN tb_produto_imagens pimage ON pi.produto_id = pimage.produto_id WHERE pi.pedido_id = ?");
            $stmt->execute([$pedido['id']]);
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($produtos) {
                foreach ($produtos as &$produto) {
                    $produto['imagem'] = !empty($produto['produto_imagem'])
                                    ? str_replace(' ', '%20', INCLUDE_PATH . "files/produtos/" . $produto['id'] . "/" . $produto['produto_imagem'])
                                    : INCLUDE_PATH . "assets/preview-image/product.jpg";
                }
            }

            $endereco = $pedido['endereco'] . ', ' . $pedido['numero'];
            if (!empty($pedido['complemento'])) {
                $endereco .= ' - ' . $pedido['complemento'];
            }
            $endereco .= ', ' . $pedido['municipio'] . ' - ' . $pedido['cidade'] . '/' . $pedido['uf'] . ' - ' . $pedido['cep'];

            if (isset($_POST['btnSendOrder'])) {
                // Enviar e-mail
                $pedido_link = INCLUDE_PATH . "user/compra?pedido=" . $pedido['pedido_id'];
                $subject = "Pedido #{$pedido['pedido_id']} gerado com sucesso em " . $project['name'];
                $content = array("layout" => "produto-enviado", "content" => array("name" => $pedido['nome'], "endereco" => $endereco, "pedido" => $pedido, "produtos" => $produtos, "link" => $pedido_link));
                $mail1 = sendMail($pedido['nome'], $pedido['email'], $project, $subject, $content);
            } else {
                // Enviar e-mail
                $pedido_link = INCLUDE_PATH . "user/compra?pedido=" . $pedido['pedido_id'];
                $subject = "Seu pedido #{$pedido['pedido_id']} foi entregue com sucesso - {$project['name']}";
                $content = array("layout" => "pedido-entregue", "content" => array("name" => $pedido['nome'], "endereco" => $endereco, "pedido" => $pedido, "produtos" => $produtos, "link" => $pedido_link));
                $mail1 = sendMail($pedido['nome'], $pedido['email'], $project, $subject, $content);
            }
    
            if (!empty($mail1)) {
                // Exibir a modal após salvar as informações
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'Erro ao atualizar enviar e-mail.';
            } else {
                // Exibir a modal após salvar as informações
                $_SESSION['show_modal'] = "<script>$('#staticBackdrop').modal('toggle');</script>";
                $_SESSION['msg'] = 'Pedido atualizado com sucesso!';
            }

            //Voltar para a pagina do formulario
            header('Location: ' . INCLUDE_PATH_ADMIN . 'compra?pedido=' . $pedido['pedido_id']);
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}