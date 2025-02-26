-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28-Set-2023 às 20:05
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `doacoes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_checkout`
--

CREATE TABLE `tb_checkout` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `privacidade` varchar(255) NOT NULL,
  `faq` varchar(255) NOT NULL,
  `use_faq` tinyint(1) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `cep` varchar(255) NOT NULL,
  `rua` varchar(255) NOT NULL,
  `numero` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nav_background` varchar(255) NOT NULL,
  `nav_color` varchar(255) NOT NULL,
  `background` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `hover` varchar(255) NOT NULL,
  `text_color` varchar(255) NOT NULL,
  `load_btn` varchar(255) NOT NULL,
  `progress` varchar(255) NOT NULL,
  `monthly_1` varchar(255) NOT NULL,
  `monthly_2` varchar(255) NOT NULL,
  `monthly_3` varchar(255) NOT NULL,
  `monthly_4` varchar(255) NOT NULL,
  `monthly_5` varchar(255) NOT NULL,
  `yearly_1` varchar(255) NOT NULL,
  `yearly_2` varchar(255) NOT NULL,
  `yearly_3` varchar(255) NOT NULL,
  `yearly_4` varchar(255) NOT NULL,
  `yearly_5` varchar(255) NOT NULL,
  `once_1` varchar(255) NOT NULL,
  `once_2` varchar(255) NOT NULL,
  `once_3` varchar(255) NOT NULL,
  `once_4` varchar(255) NOT NULL,
  `once_5` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_checkout`
--

INSERT INTO `tb_checkout` (`id`, `nome`, `logo`, `title`, `descricao`, `privacidade`, `faq`, `use_faq`, `facebook`, `instagram`, `linkedin`, `twitter`, `youtube`, `website`, `cep`, `rua`, `numero`, `bairro`, `cidade`, `estado`, `telefone`, `email`, `nav_background`, `nav_color`, `background`, `color`, `hover`, `text_color`, `load_btn`, `progress`, `monthly_1`, `monthly_2`, `monthly_3`, `monthly_4`, `monthly_5`, `yearly_1`, `yearly_2`, `yearly_3`, `yearly_4`, `yearly_5`, `once_1`, `once_2`, `once_3`, `once_4`, `once_5`) VALUES (1, 'Floema', 'floema-logo.png', 'Faça uma doação', 'Doações para o projeto Floema', 'https://seusite.com.br/politica-de-privacidade', 'https://seusite.com.br/faq', 1, 'https://facebook.com/seufacebook', 'https://instagram.com/seuinstagram', NULL, NULL, NULL, NULL, '12345678', 'Rua', '123', 'Bairro', 'Cidade', 'São Paulo', '(11) 1111-1111', 'seuemail@seusite.com.br', '#ffc107', '#212529', '#f5f5f5', '#ffc107', '#212529', '#212529', '#ffc107', '#212529', '10', '15', '30', '40', '70', '120', '360', '480', '840', '960', '200', '300', '400', '500', '1000');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_clientes`
--

CREATE TABLE `tb_clientes` (
  `id` int(11) NOT NULL,
  `roles` tinyint(1) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `recup_password` varchar(255) DEFAULT NULL,
  `magic_link` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `cpf` varchar(25) DEFAULT NULL,
  `cep` char(10) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `municipio` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `uf` varchar(255) DEFAULT NULL,
  `asaas_id` varchar(255) DEFAULT NULL,
  `newsletter` tinyint(1) NOT NULL,
  `private` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tb_clientes` (`id`, `roles`, `nome`, `email`, `password`, `magic_link`, `phone`, `cpf`, `cep`, `endereco`, `numero`, `complemento`, `municipio`, `cidade`, `uf`, `asaas_id`, `newsletter`, `private`) VALUES (1, 1, 'Admin', 'admin@admin.com', '$2y$10$gphtP5ZDgkZNctcEhLKfs.MQ8qWc6Ebf8V6sqRf4q7QhClHSojT7.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_mensagens`
--

CREATE TABLE `tb_mensagens` (
  `id` int(11) NOT NULL,
  `welcome_email` longtext DEFAULT NULL,
  `privacy_policy` longtext DEFAULT NULL,
  `use_privacy` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tb_mensagens` (`id`, `welcome_email`, `privacy_policy`, `use_privacy`) VALUES (1, 'Muito obrigado por colaborar com nossa instituição.', '<h4>SEÇÃO 1 - O QUE FAREMOS COM ESTA INFORMAÇÃO?</h4><p>&nbsp;</p><p>Quando você realiza alguma transação em nosso site, como parte do processo de compra e venda, coletamos as informações pessoais que você nos dá tais como: nome, e-mail e endereço.</p><p>Quando você acessa nosso site, também recebemos automaticamente o protocolo de internet do seu computador, endereço de IP, a fim de obter informações que nos ajudam a aprender sobre seu navegador e sistema operacional.</p><p>Email Marketing será realizado apenas caso você permita. Nestes emails você poderá receber notícia sobre nossa loja, novos produtos e outras atualizações.</p><h4><br>SEÇÃO 2 - CONSENTIMENTO</h4><p>&nbsp;</p><p>Como vocês obtêm meu consentimento?</p><p>Quando você fornece informações pessoais como nome, telefone e endereço, para completar: uma transação, verificar seu cartão de crédito, fazer um pedido, providenciar uma entrega ou retornar uma compra. Após a realização de ações entendemos que você está de acordo com a coleta de dados para serem utilizados pela nossa empresa.</p><p>Se pedimos por suas informações pessoais por uma razão secundária, como marketing, vamos lhe pedir diretamente por seu consentimento, ou lhe fornecer a oportunidade de dizer não.</p><p>E caso você queira retirar seu consentimento, como proceder?</p><p>Se após você nos fornecer seus dados, você mudar de ideia, você pode retirar o seu consentimento para que possamos entrar em contato, para a coleção de dados contínua, uso ou divulgação de suas informações, a qualquer momento, entrando em contato conosco por telefone ou nos enviando uma correspondência em: NOME INSTITUIÇÃO - Rua NOME-DA-RUA, NUMERO, COMPLEMENTO, BAIRRO, CIDADE – UF – CEP 99999-999</p><p>&nbsp;</p><h4>SEÇÃO 3 - DIVULGAÇÃO</h4><p>&nbsp;</p><p>Podemos divulgar suas informações pessoais caso sejamos obrigados pela lei para fazê-lo ou se você violar nossos Termos de Serviço.</p><h4>&nbsp;</h4><h4>SEÇÃO 4 - SERVIÇOS DE TERCEIROS</h4><p>&nbsp;</p><p>No geral, os fornecedores terceirizados usados por nós irão apenas coletar, usar e divulgar suas informações na medida do necessário para permitir que eles realizem os serviços que eles nos fornecem.</p><p>Entretanto, certos fornecedores de serviços terceirizados, tais como gateways de pagamento e outros processadores de transação de pagamento, têm suas próprias políticas de privacidade com respeito à informação que somos obrigados a fornecer para eles de suas transações relacionadas com compras.</p><p>Para esses fornecedores, recomendamos que você leia suas políticas de privacidade para que você possa entender a maneira na qual suas informações pessoais serão usadas por esses fornecedores.</p><p>Em particular, lembre-se que certos fornecedores podem ser localizados em ou possuir instalações que são localizadas em jurisdições diferentes que você ou nós. Assim, se você quer continuar com uma transação que envolve os serviços de um fornecedor de serviço terceirizado, então suas informações podem tornar-se sujeitas às leis da(s) jurisdição(ões) nas quais o fornecedor de serviço ou suas instalações estão localizados.</p><p>Uma vez que você deixe o nosso site ou seja redirecionado para um aplicativo ou site de terceiros, você não será mais regido por essa Política de Privacidade ou pelos Termos de Serviço do nosso site.</p><p>Links</p><p>Quando você clica em links no nosso site, eles podem lhe direcionar links externos. Não somos responsáveis pelas práticas de privacidade de outros sites e lhe incentivamos a ler as declarações de privacidade deles.</p><p>&nbsp;</p><h4>SEÇÃO 5 - SEGURANÇA</h4><p>&nbsp;</p><p>Para proteger suas informações pessoais, tomamos precauções razoáveis e seguimos as melhores práticas da indústria para nos certificar que elas não serão perdidas inadequadamente, usurpadas, acessadas, divulgadas, alteradas ou destruídas.</p><p>Se você nos fornecer as suas informações de cartão de crédito, essa informação é criptografada usando tecnologia \"secure socket layer\" (SSL). Embora nenhum método de transmissão pela Internet ou armazenamento eletrônico é 100% seguro, nós seguimos todos os requisitos da PCI-DSS.</p><p>&nbsp;</p><h4>SEÇÃO 6 - ALTERAÇÕES PARA ESSA POLÍTICA DE PRIVACIDADE</h4><p>&nbsp;</p><p>Reservamos o direito de modificar essa política de privacidade a qualquer momento, então por favor, revise-a com frequência. Alterações e esclarecimentos vão surtir efeito imediatamente após sua publicação no site. Se fizermos alterações de materiais para essa política, iremos notificá-lo aqui que eles foram atualizados, para que você tenha ciência sobre quais informações coletamos, como as usamos, e sob que circunstâncias, se alguma, usamos e/ou divulgamos elas.</p><p>Se nosso site for adquirido ou fundido com outra empresa, suas informações podem ser transferidas para os novos proprietários para que possamos continuar a prestar serviços para você.</p>', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_doacoes`
--

CREATE TABLE `tb_doacoes` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `payment_id` varchar(100) NOT NULL,
  `cycle` varchar(20) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `forma_pagamento` varchar(50) NOT NULL,
  `link_pagamento` longtext DEFAULT NULL,
  `link_boleto` longtext DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `data_vencimento` date DEFAULT NULL,
  `data_criacao` date DEFAULT NULL,
  `data_pagamento` datetime DEFAULT NULL,
  `pix_encodedImage` longtext DEFAULT NULL,
  `pix_payload` longtext DEFAULT NULL,
  `pix_expirationDate` datetime DEFAULT NULL,
  `boleto_barCode` varchar(255) DEFAULT NULL,
  `boleto_nossoNumero` varchar(255) DEFAULT NULL,
  `boleto_identificationField` varchar(255) DEFAULT NULL,
  `cartao_numero` int(4) DEFAULT NULL,
  `cartao_bandeira` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tb_doacoes` WRITE;
/*!40000 ALTER TABLE `tb_doacoes` DISABLE KEYS */;
INSERT INTO `tb_doacoes` VALUES (1,'cus_000005450967','pay_6237274104232345',NULL,200.00,'BOLETO','https://sandbox.asaas.com/i/6237274104232345','https://sandbox.asaas.com/b/pdf/6237274104232345','PENDING','2023-10-04','2023-09-27',NULL,NULL,NULL,NULL,'23795949300000200002693090000121979000092560','1219790','23792693079000012197190000925603594930000020000',NULL,NULL),(2,'cus_000005450969','pay_7644352752939147',NULL,213.00,'BOLETO','https://sandbox.asaas.com/i/7644352752939147','https://sandbox.asaas.com/b/pdf/7644352752939147','PENDING','2023-10-04','2023-09-27',NULL,NULL,NULL,NULL,'23794949300000213002693090000121979100092560','1219791','23792693079000012197191000925601494930000021300',NULL,NULL);
/*!40000 ALTER TABLE `tb_doacoes` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_imagens`
--

CREATE TABLE `tb_imagens` (
  `id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_imagens`
--

INSERT INTO `tb_imagens` (`id`, `imagem`) VALUES
(11, '1691640386.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_integracoes`
--

CREATE TABLE `tb_integracoes` (
  `id` int(11) NOT NULL,
  `fb_pixel` longtext DEFAULT NULL,
  `gtm` longtext DEFAULT NULL,
  `g_analytics` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_integracoes`
--

INSERT INTO `tb_integracoes` (`id`, `fb_pixel`, `gtm`, `g_analytics`) VALUES
(1, '', '', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_integracoes`
--

CREATE TABLE `tb_transacoes` (
  `id` int(11) NOT NULL,
  `event` varchar(255) DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_date_created` varchar(255) DEFAULT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `subscription_id` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `net_value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `billing_type` varchar(255) DEFAULT NULL,
  `confirmed_date` varchar(255) DEFAULT NULL,
  `credit_card_number` varchar(255) DEFAULT NULL,
  `credit_card_brand` varchar(255) DEFAULT NULL,
  `credit_card_token` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `credit_date` varchar(255) DEFAULT NULL,
  `estimated_credit_date` varchar(255) DEFAULT NULL,
  `webhook_date_created` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_webhook`
--

CREATE TABLE `tb_webhook` (
  `id` int(11) NOT NULL,
  `webhook_id` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `interrupted` tinyint(1) NOT NULL,
  `send_type` varchar(100) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tb_checkout`
--
ALTER TABLE `tb_checkout`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_clientes`
--
ALTER TABLE `tb_clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_doacoes`
--
ALTER TABLE `tb_doacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_imagens`
--
ALTER TABLE `tb_imagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_integracoes`
--
ALTER TABLE `tb_integracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_mensagens`
--
ALTER TABLE `tb_mensagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_transacoes`
--
ALTER TABLE `tb_transacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_webhook`
--
ALTER TABLE `tb_webhook`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_checkout`
--
ALTER TABLE `tb_checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_doacoes`
--
ALTER TABLE `tb_doacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_imagens`
--
ALTER TABLE `tb_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

-- AUTO_INCREMENT de tabela `tb_integracoes`
--
ALTER TABLE `tb_integracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_mensagens`
--
ALTER TABLE `tb_mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_transacoes`
--
ALTER TABLE `tb_transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT para tabela `tb_webhook`
--
ALTER TABLE `tb_webhook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

CREATE TABLE tb_bulk_emails (
	title varchar(100) NULL,
	body varchar(100) NULL,
	`date` DATETIME DEFAULT now() NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

ALTER TABLE tb_mensagens ADD unregister_message LONGTEXT NULL;
ALTER TABLE tb_checkout
ADD COLUMN tiktok VARCHAR(255) DEFAULT NULL AFTER website,
ADD COLUMN linktree VARCHAR(255) DEFAULT NULL AFTER tiktok;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
