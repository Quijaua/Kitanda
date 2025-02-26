-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 03/07/2024 às 09:35
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Alteração na tabela `tb_checkout`
--

ALTER TABLE `tb_checkout`
ADD COLUMN `pix_chave` VARCHAR(255) AFTER `doacoes`,
ADD COLUMN `pix_valor` VARCHAR(255) AFTER `pix_chave`,
ADD COLUMN `pix_codigo` VARCHAR(255) AFTER `pix_valor`,
ADD COLUMN `pix_imagem_base64` TEXT AFTER `pix_codigo`,
ADD COLUMN `pix_identificador_transacao` VARCHAR(255) AFTER `pix_imagem_base64`,
ADD COLUMN `pix_exibir` BOOLEAN DEFAULT 0 AFTER `pix_identificador_transacao`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
