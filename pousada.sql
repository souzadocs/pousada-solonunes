-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/02/2026 às 23:01
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pousada`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `passeios`
--

CREATE TABLE `passeios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `capacidade` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `descricao` text NOT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `galeria` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `quartos`
--

CREATE TABLE `quartos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco_noite` decimal(10,2) NOT NULL,
  `capacidade` int(11) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `galeria` text DEFAULT NULL,
  `andar` varchar(50) DEFAULT 'Térreo',
  `tipo_cama` varchar(100) DEFAULT '1 Casal',
  `quantidade` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `quartos`
--

INSERT INTO `quartos` (`id`, `nome`, `descricao`, `preco_noite`, `capacidade`, `imagem`, `imagem_url`, `galeria`, `andar`, `tipo_cama`, `quantidade`) VALUES
(8, 'Quarto Casal', 'ar condicionado, tv, banheiro, frigobar, wifi', 20.00, 2, NULL, 'imagens/quartos/quartos_699e39a71d50b.png', '[\"imagens/quartos/quartos_699e39a71d50b.png\"]', 'Térreo', '1 Casal', 6),
(9, 'Quarto triplo', 'ar condicionado, tv, banheiro, frigobar, wifi', 20.00, 3, NULL, '', '[]', '1º Andar', '3', 1),
(10, 'Quarto Quadruplo', 'ar condicionado, tv, banheiro, frigobar, wifi', 50.00, 2, NULL, '', '[]', 'Térreo', '4 casal', 1),
(11, 'Quarto Familia', 'ar condicionado, tv, banheiro, frigobar, wifi', 40.00, 2, NULL, '', '[]', 'Térreo', '4 casal', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `quarto_id` int(11) DEFAULT NULL,
  `nome_cliente` varchar(100) NOT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pendente',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `passeios_inclusos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `quarto_id`, `nome_cliente`, `whatsapp`, `checkin`, `checkout`, `valor_total`, `status`, `data_criacao`, `passeios_inclusos`) VALUES
(42, 8, 'luan', '92981702748', '2026-02-27', '2026-02-28', 20.00, 'pendente', '2026-02-27 18:27:34', NULL),
(43, 8, 'luan', '92981702748', '2026-02-27', '2026-02-28', 20.00, 'pendente', '2026-02-27 20:07:34', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_passeios`
--

CREATE TABLE `reservas_passeios` (
  `id` int(11) NOT NULL,
  `passeio_id` int(11) NOT NULL,
  `nome_cliente` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `status` varchar(50) DEFAULT 'pendente',
  `valor_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_personalizadas`
--

CREATE TABLE `reservas_personalizadas` (
  `id` int(11) NOT NULL,
  `nome_cliente` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `nome_empresa` varchar(100) DEFAULT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `qnt_pessoas` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'Em Análise',
  `obs` text DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas_personalizadas`
--

INSERT INTO `reservas_personalizadas` (`id`, `nome_cliente`, `whatsapp`, `nome_empresa`, `checkin`, `checkout`, `qnt_pessoas`, `status`, `obs`, `data_pedido`) VALUES
(1, 'luan', '92981702748', 'blablacar', '2026-02-25', '2026-02-26', 15, 'Em Análise', 'aaaaa', '2026-02-25 00:04:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `recuperacao_token` varchar(100) DEFAULT NULL,
  `recuperacao_expira` datetime DEFAULT NULL,
  `nivel` enum('admin','funcionario') DEFAULT 'funcionario',
  `senha_master` varchar(255) DEFAULT 'nunes2026'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `senha`, `recuperacao_token`, `recuperacao_expira`, `nivel`, `senha_master`) VALUES
(4, 'admin', '$2y$10$zD.2ZeVWWacPH/134lFjSOiy//VyKymMHUvQ4oYRUhGvysZbU4Yny', NULL, NULL, 'admin', '$2y$10$myZmwsnzO/clL0tk6xEt9e0syPhmG.uK9.f3zd4J/3RLdF8XIcfZu'),
(5, 'recepção', '$2y$10$P3pBNvuOy/AEILkgCdhBHuGkTMnfDaBJDXnmYYlScQwtt4I8eqh/y', NULL, NULL, 'funcionario', '$2y$10$nZSUq0Sir1c72MQ0cbv.JuBkmWQ8YJPEU2EZfXjv0LhKzOxof6y2C');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `passeios`
--
ALTER TABLE `passeios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `quartos`
--
ALTER TABLE `quartos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quarto_id` (`quarto_id`);

--
-- Índices de tabela `reservas_passeios`
--
ALTER TABLE `reservas_passeios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passeio_id` (`passeio_id`);

--
-- Índices de tabela `reservas_personalizadas`
--
ALTER TABLE `reservas_personalizadas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `passeios`
--
ALTER TABLE `passeios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `quartos`
--
ALTER TABLE `quartos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `reservas_passeios`
--
ALTER TABLE `reservas_passeios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `reservas_personalizadas`
--
ALTER TABLE `reservas_personalizadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`quarto_id`) REFERENCES `quartos` (`id`);

--
-- Restrições para tabelas `reservas_passeios`
--
ALTER TABLE `reservas_passeios`
  ADD CONSTRAINT `reservas_passeios_ibfk_1` FOREIGN KEY (`passeio_id`) REFERENCES `passeios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
