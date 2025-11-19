-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/11/2025 às 20:07
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
-- Banco de dados: `fabrica_picoles`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `aditivos_nutritivos`
--

CREATE TABLE `aditivos_nutritivos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `formula_quimica` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aditivos_nutritivos`
--

INSERT INTO `aditivos_nutritivos` (`id`, `nome`, `formula_quimica`) VALUES
(4, 'suplemento', 'sapato');

-- --------------------------------------------------------

--
-- Estrutura para tabela `aditivos_nutritivos_picole`
--

CREATE TABLE `aditivos_nutritivos_picole` (
  `id` int(11) NOT NULL,
  `id_aditivo_nutritivo` int(11) NOT NULL,
  `id_picole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aditivos_nutritivos_picole`
--

INSERT INTO `aditivos_nutritivos_picole` (`id`, `id_aditivo_nutritivo`, `id_picole`) VALUES
(6, 4, 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `conservantes`
--

CREATE TABLE `conservantes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `conservantes`
--

INSERT INTO `conservantes` (`id`, `nome`, `descricao`) VALUES
(4, 'quimico', 'banana'),
(5, 'alzheimer', 'bnao sei');

-- --------------------------------------------------------

--
-- Estrutura para tabela `conservantes_picole`
--

CREATE TABLE `conservantes_picole` (
  `id` int(11) NOT NULL,
  `id_conservante` int(11) NOT NULL,
  `id_picole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `conservantes_picole`
--

INSERT INTO `conservantes_picole` (`id`, `id_conservante`, `id_picole`) VALUES
(3, 4, 9),
(4, 5, 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ingredientes`
--

INSERT INTO `ingredientes` (`id`, `nome`) VALUES
(5, 'sorvete');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ingredientes_picole`
--

CREATE TABLE `ingredientes_picole` (
  `id` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `id_picole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ingredientes_picole`
--

INSERT INTO `ingredientes_picole` (`id`, `id_ingrediente`, `id_picole`) VALUES
(9, 5, 8),
(10, 5, 9),
(11, 5, 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `lotes`
--

CREATE TABLE `lotes` (
  `id` int(11) NOT NULL,
  `id_picole` int(11) NOT NULL,
  `id_tipo_picole` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lotes`
--

INSERT INTO `lotes` (`id`, `id_picole`, `id_tipo_picole`, `quantidade`) VALUES
(4, 0, 1, 10),
(5, 0, 2, 17),
(6, 0, 2, 20);

-- --------------------------------------------------------

--
-- Estrutura para tabela `lotes_notas_fiscal`
--

CREATE TABLE `lotes_notas_fiscal` (
  `id` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `id_nota_fiscal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lotes_notas_fiscal`
--

INSERT INTO `lotes_notas_fiscal` (`id`, `id_lote`, `id_nota_fiscal`) VALUES
(13, 4, 2),
(14, 5, 3),
(15, 6, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `notas_fiscal`
--

CREATE TABLE `notas_fiscal` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `numero_serie` varchar(50) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `id_revendedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notas_fiscal`
--

INSERT INTO `notas_fiscal` (`id`, `data`, `valor`, `numero_serie`, `descricao`, `id_revendedor`) VALUES
(2, '2025-11-18', 100.00, '125', '0', 3),
(3, '2025-11-18', 255.00, '1234', '0', 4),
(4, '2025-11-19', 200.00, '13123', '0', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `picoles`
--

CREATE TABLE `picoles` (
  `id` int(11) NOT NULL,
  `id_sabor` int(11) NOT NULL,
  `preco` decimal(6,2) NOT NULL,
  `id_tipo_embalagem` int(11) NOT NULL,
  `id_tipo_picole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `picoles`
--

INSERT INTO `picoles` (`id`, `id_sabor`, `preco`, `id_tipo_embalagem`, `id_tipo_picole`) VALUES
(8, 5, 10.00, 4, 1),
(9, 6, 15.00, 4, 2),
(10, 7, 10.00, 4, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `revendedores`
--

CREATE TABLE `revendedores` (
  `id` int(11) NOT NULL,
  `cnpj` varchar(18) NOT NULL,
  `razao_social` varchar(100) NOT NULL,
  `contato` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `revendedores`
--

INSERT INTO `revendedores` (`id`, `cnpj`, `razao_social`, `contato`) VALUES
(3, '231231424154341241', 'carlos', 'email@email.com'),
(4, '1234', 'joao', 'email@email.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sabores`
--

CREATE TABLE `sabores` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sabores`
--

INSERT INTO `sabores` (`id`, `nome`) VALUES
(5, 'chocolate'),
(6, 'morango'),
(7, 'cadeira');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_embalagem`
--

CREATE TABLE `tipos_embalagem` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_embalagem`
--

INSERT INTO `tipos_embalagem` (`id`, `nome`) VALUES
(4, 'plastico');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_picoles`
--

CREATE TABLE `tipos_picoles` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_picoles`
--

INSERT INTO `tipos_picoles` (`id`, `nome`) VALUES
(1, 'Normal'),
(2, 'Ao Leite');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','vendedor','user_fabrica') NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `criado_em`) VALUES
(7, 'Administrador', 'admin@fabrica.com', 'admin123', 'admin', '2025-11-14 17:02:48'),
(8, 'João Vendedor', 'vendedor@fabrica.com', 'vendedor123', 'vendedor', '2025-11-14 17:02:48'),
(9, 'Maria Fábrica', 'fabrica@fabrica.com', 'fabrica123', 'user_fabrica', '2025-11-14 17:02:48');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `aditivos_nutritivos`
--
ALTER TABLE `aditivos_nutritivos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `aditivos_nutritivos_picole`
--
ALTER TABLE `aditivos_nutritivos_picole`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aditivo_nutritivo` (`id_aditivo_nutritivo`),
  ADD KEY `id_picole` (`id_picole`);

--
-- Índices de tabela `conservantes`
--
ALTER TABLE `conservantes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `conservantes_picole`
--
ALTER TABLE `conservantes_picole`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_conservante` (`id_conservante`),
  ADD KEY `id_picole` (`id_picole`);

--
-- Índices de tabela `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ingredientes_picole`
--
ALTER TABLE `ingredientes_picole`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ingrediente` (`id_ingrediente`),
  ADD KEY `id_picole` (`id_picole`);

--
-- Índices de tabela `lotes`
--
ALTER TABLE `lotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo_picole` (`id_tipo_picole`);

--
-- Índices de tabela `lotes_notas_fiscal`
--
ALTER TABLE `lotes_notas_fiscal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_lote` (`id_lote`),
  ADD KEY `id_nota_fiscal` (`id_nota_fiscal`);

--
-- Índices de tabela `notas_fiscal`
--
ALTER TABLE `notas_fiscal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notas_fiscal_ibfk_1` (`id_revendedor`);

--
-- Índices de tabela `picoles`
--
ALTER TABLE `picoles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sabor` (`id_sabor`),
  ADD KEY `id_tipo_embalagem` (`id_tipo_embalagem`),
  ADD KEY `id_tipo_picole` (`id_tipo_picole`);

--
-- Índices de tabela `revendedores`
--
ALTER TABLE `revendedores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sabores`
--
ALTER TABLE `sabores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipos_embalagem`
--
ALTER TABLE `tipos_embalagem`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipos_picoles`
--
ALTER TABLE `tipos_picoles`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `aditivos_nutritivos`
--
ALTER TABLE `aditivos_nutritivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `aditivos_nutritivos_picole`
--
ALTER TABLE `aditivos_nutritivos_picole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `conservantes`
--
ALTER TABLE `conservantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `conservantes_picole`
--
ALTER TABLE `conservantes_picole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `ingredientes_picole`
--
ALTER TABLE `ingredientes_picole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `lotes`
--
ALTER TABLE `lotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `lotes_notas_fiscal`
--
ALTER TABLE `lotes_notas_fiscal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `notas_fiscal`
--
ALTER TABLE `notas_fiscal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `picoles`
--
ALTER TABLE `picoles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `revendedores`
--
ALTER TABLE `revendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `sabores`
--
ALTER TABLE `sabores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tipos_embalagem`
--
ALTER TABLE `tipos_embalagem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tipos_picoles`
--
ALTER TABLE `tipos_picoles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `aditivos_nutritivos_picole`
--
ALTER TABLE `aditivos_nutritivos_picole`
  ADD CONSTRAINT `aditivos_nutritivos_picole_ibfk_1` FOREIGN KEY (`id_aditivo_nutritivo`) REFERENCES `aditivos_nutritivos` (`id`),
  ADD CONSTRAINT `aditivos_nutritivos_picole_ibfk_2` FOREIGN KEY (`id_picole`) REFERENCES `picoles` (`id`);

--
-- Restrições para tabelas `conservantes_picole`
--
ALTER TABLE `conservantes_picole`
  ADD CONSTRAINT `conservantes_picole_ibfk_1` FOREIGN KEY (`id_conservante`) REFERENCES `conservantes` (`id`),
  ADD CONSTRAINT `conservantes_picole_ibfk_2` FOREIGN KEY (`id_picole`) REFERENCES `picoles` (`id`);

--
-- Restrições para tabelas `ingredientes_picole`
--
ALTER TABLE `ingredientes_picole`
  ADD CONSTRAINT `ingredientes_picole_ibfk_1` FOREIGN KEY (`id_ingrediente`) REFERENCES `ingredientes` (`id`),
  ADD CONSTRAINT `ingredientes_picole_ibfk_2` FOREIGN KEY (`id_picole`) REFERENCES `picoles` (`id`);

--
-- Restrições para tabelas `lotes`
--
ALTER TABLE `lotes`
  ADD CONSTRAINT `lotes_ibfk_1` FOREIGN KEY (`id_tipo_picole`) REFERENCES `tipos_picoles` (`id`);

--
-- Restrições para tabelas `lotes_notas_fiscal`
--
ALTER TABLE `lotes_notas_fiscal`
  ADD CONSTRAINT `lotes_notas_fiscal_ibfk_1` FOREIGN KEY (`id_lote`) REFERENCES `lotes` (`id`),
  ADD CONSTRAINT `lotes_notas_fiscal_ibfk_2` FOREIGN KEY (`id_nota_fiscal`) REFERENCES `notas_fiscal` (`id`);

--
-- Restrições para tabelas `notas_fiscal`
--
ALTER TABLE `notas_fiscal`
  ADD CONSTRAINT `notas_fiscal_ibfk_1` FOREIGN KEY (`id_revendedor`) REFERENCES `revendedores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `picoles`
--
ALTER TABLE `picoles`
  ADD CONSTRAINT `picoles_ibfk_1` FOREIGN KEY (`id_sabor`) REFERENCES `sabores` (`id`),
  ADD CONSTRAINT `picoles_ibfk_2` FOREIGN KEY (`id_tipo_embalagem`) REFERENCES `tipos_embalagem` (`id`),
  ADD CONSTRAINT `picoles_ibfk_3` FOREIGN KEY (`id_tipo_picole`) REFERENCES `tipos_picoles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
