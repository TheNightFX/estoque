-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/05/2026 às 22:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `estoque`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cautelas_materiais`
--

CREATE TABLE `cautelas_materiais` (
  `id` int(11) NOT NULL,
  `grupo_id` bigint(20) DEFAULT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade_cautelada` int(11) NOT NULL,
  `responsavel_nome` varchar(100) NOT NULL,
  `responsavel_secao` varchar(50) NOT NULL,
  `responsavel_telefone` varchar(30) DEFAULT NULL,
  `data_cautela` datetime NOT NULL DEFAULT current_timestamp(),
  `data_prevista_devolucao` date DEFAULT NULL,
  `data_devolucao` datetime DEFAULT NULL,
  `estoque_movimentado` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cautelas_materiais`
--

INSERT INTO `cautelas_materiais` (`id`, `grupo_id`, `produto_id`, `quantidade_cautelada`, `responsavel_nome`, `responsavel_secao`, `responsavel_telefone`, `data_cautela`, `data_prevista_devolucao`, `data_devolucao`, `estoque_movimentado`) VALUES
(1, NULL, 6, 50, 'Hudson', 'STI', NULL, '2026-05-18 19:28:00', NULL, '2026-05-18 13:38:07', 0),
(2, NULL, 4, 25, 'Hudson', 'STI', '67991555663', '2026-05-18 19:35:00', '2026-05-18', '2026-05-18 13:38:17', 0),
(3, NULL, 4, 25, 'Hudson', 'STI', '67991555663', '2026-05-18 19:39:00', '2026-05-20', '2026-05-18 13:40:09', 0),
(4, NULL, 3, 5, 'Hudson', 'STI', '67991555663', '2026-05-18 19:42:00', '2026-05-20', '2026-05-18 13:57:46', 0),
(5, NULL, 3, 40, 'Hudson', 'STI', '67991555663', '2026-05-18 19:55:00', '2026-05-20', '2026-05-18 16:16:15', 0),
(6, NULL, 3, 10, 'Sgt Hudson', 'STI', '67991555663', '2026-05-18 22:52:00', '2026-05-22', '2026-05-19 10:18:23', 0),
(7, NULL, 1, 2, 'Hudson', 'STI', 'asdasdasd', '2026-05-19 00:02:00', NULL, '2026-05-18 20:26:54', 0),
(8, NULL, 6, 5, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 02:27:00', '2026-05-19', '2026-05-18 20:29:50', 0),
(9, 26051903185939, 1, 4, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:18:00', '2026-05-22', '2026-05-18 21:21:43', 0),
(10, 26051903185939, 3, 1, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:18:00', '2026-05-22', '2026-05-18 21:21:43', 0),
(11, 26051903185939, 4, 1, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:18:00', '2026-05-22', '2026-05-18 21:21:43', 0),
(12, 26051903185939, 10, 15, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:18:00', '2026-05-22', '2026-05-18 21:21:43', 0),
(13, 26051903284322, 10, 16, 'Sgt Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:28:00', '2026-05-29', '2026-05-19 10:18:20', 1),
(14, 26051903284322, 1, 1, 'Sgt Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:28:00', '2026-05-29', '2026-05-19 10:18:20', 1),
(15, 26051903301572, 1, 4, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:29:00', '2026-05-30', '2026-05-19 10:18:17', 1),
(22, 26051903521152, 1, 1, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:52:00', '2026-05-26', '2026-05-19 10:19:14', 1),
(23, 26051903521152, 5, 80, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:52:00', '2026-05-26', '2026-05-19 10:19:14', 1),
(24, 26051903521152, 11, 1, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:52:00', '2026-05-26', '2026-05-19 10:19:14', 1),
(25, 26051903521152, 8, 16, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:52:00', '2026-05-26', '2026-05-19 10:19:14', 1),
(26, 26051903521152, 50, 1, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:52:00', '2026-05-26', '2026-05-19 10:19:14', 1),
(27, 26051903521152, 52, 1, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 03:52:00', '2026-05-26', '2026-05-19 10:19:14', 1),
(33, 26051915534256, 3, 5, 'Sgt Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 15:53:00', '2026-05-19', '2026-05-19 10:04:22', 1),
(34, 26051915534256, 5, 3, 'Sgt Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 15:53:00', '2026-05-19', '2026-05-19 10:04:22', 1),
(35, 26051915534256, 7, 3, 'Sgt Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 15:53:00', '2026-05-19', '2026-05-19 10:04:22', 1),
(36, 26051915534256, 9, 5, 'Sgt Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 15:53:00', '2026-05-19', '2026-05-19 10:04:22', 1),
(37, 26051915534256, 11, 1, 'Sgt Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 15:53:00', '2026-05-19', '2026-05-19 10:04:22', 1),
(38, 26051916185419, 1, 1, 'Hudson', 'STI', '(67) 9 9155-5663', '2026-05-19 10:18:00', '2026-05-30', '2026-05-19 10:19:17', 1),
(39, 26052214565634, 7, 1, 'Sd EP Vilalva', '9 BPE', '(67) 9 9862-3732', '2026-05-22 08:56:00', '2026-05-25', '2026-05-22 08:58:00', 1),
(40, 26052214565634, 3, 1, 'Sd EP Vilalva', '9 BPE', '(67) 9 9862-3732', '2026-05-22 08:56:00', '2026-05-25', '2026-05-22 08:58:00', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs_movimentacoes`
--

CREATE TABLE `logs_movimentacoes` (
  `id` int(11) NOT NULL,
  `secao` varchar(50) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nome` varchar(100) DEFAULT NULL,
  `acao` varchar(50) NOT NULL,
  `entidade` varchar(50) NOT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `data_log` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `logs_movimentacoes`
--

INSERT INTO `logs_movimentacoes` (`id`, `secao`, `usuario_id`, `usuario_nome`, `acao`, `entidade`, `entidade_id`, `detalhes`, `data_log`) VALUES
(1, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 5, 'Material: Caneta Preta | Descricao: Caneta esferográfica preta | Quantidade cautelada: 40 | Responsavel cautela: Hudson | Secao responsavel: STI | Telefone: 67991555663 | Data cautela: 2026-05-18 19:55:00 | Possivel devolucao: 2026-05-20 | Devolucao marcada em: 2026-05-18 22:16:15', '2026-05-18 16:16:15'),
(2, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 6, 'Material: Caneta Preta | Descricao: Caneta esferográfica preta | Secao material: STI | Quantidade cautelada: 10 | Responsavel: Sgt Hudson | Secao responsavel: STI | Telefone: 67991555663 | Data cautela: 2026-05-18T22:52 | Possivel devolucao: 2026-05-21', '2026-05-18 16:53:23'),
(3, 'STI', 3, 'Hudson', 'EDITOU_CAUTELA', 'cautela', 6, 'Material: Caneta Preta | Descricao: Caneta esferográfica preta | Secao material: STI | Quantidade cautelada: 10 | Responsavel: Sgt Hudson | Secao responsavel: STI | Telefone: 67991555663 | Data cautela: 2026-05-18T22:52 | Possivel devolucao: 2026-05-19', '2026-05-18 16:56:06'),
(4, 'STI', 3, 'Hudson', 'EDITOU_CAUTELA', 'cautela', 6, 'Material: Caneta Preta | Descricao: Caneta esferográfica preta | Secao material: STI | Quantidade cautelada: 10 | Responsavel: Sgt Hudson | Secao responsavel: STI | Telefone: 67991555663 | Data cautela: 2026-05-18T22:52 | Possivel devolucao: 2026-05-21', '2026-05-18 16:57:07'),
(5, 'STI', 3, 'Hudson', 'CADASTROU_MATERIAL', 'produto', 56, 'Material: Poltrona | Descricao: Poltrona reclinavel | Secao: STI | Quantidade: 1 | Data entrada: 2026-05-18 | Data saida: ', '2026-05-18 17:47:56'),
(6, 'STI', 3, 'Hudson', 'CADASTROU_MATERIAL', 'produto', 57, 'Material: Mesa | Descricao: Mesa em L | Secao: STI | Quantidade: 1 | Data entrada: 18-05-2026 23:59', '2026-05-18 17:59:58'),
(7, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 7, 'Material: Cadeira | Descricao: Cadeira azul com suporte para os braços | Secao material: STI | Quantidade cautelada: 2 | Responsavel: Hudson | Secao responsavel: STI | Telefone: asdasdasd | Data cautela: 19/05/2026 00:02 | Possivel devolucao: ', '2026-05-18 18:03:08'),
(8, 'STI', 3, 'Hudson', 'CADASTROU_MATERIAL', 'produto', 58, 'Material: Caneta | Descricao: Azul | Secao: STI | Quantidade: 5 | Data entrada: 19/05/2026 02:26', '2026-05-18 20:26:09'),
(9, 'STI', 3, 'Hudson', 'EDITOU_MATERIAL', 'produto', 58, 'Material: Caneta | Descricao: Azul | Secao: STI | Quantidade: 10 | Data entrada: 19/05/2026 02:26', '2026-05-18 20:26:20'),
(10, 'STI', 3, 'Hudson', 'EXCLUIU_MATERIAL', 'produto', 58, 'Material excluido: Caneta | Descricao: Azul | Quantidade: 10', '2026-05-18 20:26:27'),
(11, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 7, 'Material: Cadeira | Descricao: Cadeira azul com suporte para os braços | Quantidade cautelada: 2 | Responsavel cautela: Hudson | Secao responsavel: STI | Telefone: asdasdasd | Data cautela: 19/05/2026 00:02 | Possivel devolucao:  | Devolucao marcada em: 19/05/2026 02:26', '2026-05-18 20:26:54'),
(12, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 8, 'Material: Borracha Branca | Descricao: Borracha escolar branca | Secao material: STI | Quantidade cautelada: 5 | Responsavel: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 02:27 | Possivel devolucao: 19/05/2026', '2026-05-18 20:28:08'),
(13, 'STI', 3, 'Hudson', 'EDITOU_CAUTELA', 'cautela', 8, 'Material: Borracha Branca | Descricao: Borracha escolar branca | Secao material: STI | Quantidade cautelada: 5 | Responsavel: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 02:27 | Possivel devolucao: 19/05/2026', '2026-05-18 20:28:24'),
(14, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 8, 'Material: Borracha Branca | Descricao: Borracha escolar branca | Quantidade cautelada: 5 | Responsavel cautela: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 02:27 | Possivel devolucao: 19/05/2026 | Devolucao marcada em: 19/05/2026 02:29', '2026-05-18 20:29:50'),
(15, 'STI', 3, 'Hudson', 'EDITOU_CAUTELA', 'cautela', 6, 'Material: Caneta Preta | Descricao: Caneta esferográfica preta | Secao material: STI | Quantidade cautelada: 10 | Responsavel: Sgt Hudson | Secao responsavel: STI | Telefone: 67991555663 | Data cautela: 18/05/2026 22:52 | Possivel devolucao: 22/05/2026', '2026-05-18 21:06:04'),
(16, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 9, 'Materiais: Cadeira (4), Caneta Preta (1), Caneta Vermelha (1), Bloco de Notas (15) | Responsavel: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:18 | Possivel devolucao: 22/05/2026', '2026-05-18 21:18:59'),
(17, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 12, 'Material: Bloco de Notas | Descricao: Bloco adesivo amarelo | Quantidade cautelada: 15 | Responsavel cautela: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:18 | Possivel devolucao: 22/05/2026 | Devolucao marcada em: 19/05/2026 03:21', '2026-05-18 21:21:43'),
(18, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 13, 'Materiais: Bloco de Notas (16), Cadeira (1) | Responsavel: Sgt Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:28 | Possivel devolucao: 29/05/2026', '2026-05-18 21:28:43'),
(19, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 15, 'Materiais: Cadeira (4) | Responsavel: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:29 | Possivel devolucao: 30/05/2026', '2026-05-18 21:30:15'),
(20, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 16, 'Materiais: Cadeira (1), Lápis HB (1), Marcador Permanente (1), Caderno Pequeno (1), Pincel Quadro Branco (1), Caneta (1) | Responsavel: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:52 | Possivel devolucao: 26/05/2026', '2026-05-18 21:52:11'),
(21, 'STI', 3, 'Hudson', 'EDITOU_CAUTELA', 'cautela', 22, 'Materiais: Cadeira (1), Lápis HB (80), Marcador Permanente (1), Caderno Pequeno (16), Pincel Quadro Branco (1), Caneta (1) | Responsavel: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:52 | Possivel devolucao: 26/05/2026', '2026-05-18 21:57:20'),
(22, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 28, 'Materiais: Caneta Preta (5), Lápis HB (3), Apontador (3), Caderno Grande (5), Marcador Permanente (1) | Responsavel: Sgt Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 15:53 | Possivel devolucao: 22/05/2026', '2026-05-19 09:53:42'),
(23, 'STI', 3, 'Hudson', 'EDITOU_CAUTELA', 'cautela', 33, 'Materiais: Caneta Preta (5), Lápis HB (3), Apontador (3), Caderno Grande (5), Marcador Permanente (1) | Responsavel: Sgt Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 15:53 | Possivel devolucao: 19/05/2026', '2026-05-19 09:54:39'),
(24, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 33, 'Material: Caneta Preta | Descricao: Caneta esferográfica preta | Quantidade cautelada: 5 | Responsavel cautela: Sgt Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 15:53 | Possivel devolucao: 19/05/2026 | Devolucao marcada em: 19/05/2026 16:04', '2026-05-19 10:04:22'),
(25, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 15, 'Material: Cadeira | Descricao: Cadeira azul com suporte para os braços | Quantidade cautelada: 4 | Responsavel cautela: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:29 | Possivel devolucao: 30/05/2026 | Devolucao marcada em: 19/05/2026 16:18', '2026-05-19 10:18:17'),
(26, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 13, 'Material: Bloco de Notas | Descricao: Bloco adesivo amarelo | Quantidade cautelada: 16 | Responsavel cautela: Sgt Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:28 | Possivel devolucao: 29/05/2026 | Devolucao marcada em: 19/05/2026 16:18', '2026-05-19 10:18:20'),
(27, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 6, 'Material: Caneta Preta | Descricao: Caneta esferográfica preta | Quantidade cautelada: 10 | Responsavel cautela: Sgt Hudson | Secao responsavel: STI | Telefone: 67991555663 | Data cautela: 18/05/2026 22:52 | Possivel devolucao: 22/05/2026 | Devolucao marcada em: 19/05/2026 16:18', '2026-05-19 10:18:23'),
(28, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 38, 'Materiais: Cadeira (1) | Responsavel: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 10:18 | Possivel devolucao: 30/05/2026', '2026-05-19 10:18:54'),
(29, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 22, 'Material: Cadeira | Descricao: Cadeira azul com suporte para os braços | Quantidade cautelada: 1 | Responsavel cautela: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 03:52 | Possivel devolucao: 26/05/2026 | Devolucao marcada em: 19/05/2026 16:19', '2026-05-19 10:19:14'),
(30, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 38, 'Material: Cadeira | Descricao: Cadeira azul com suporte para os braços | Quantidade cautelada: 1 | Responsavel cautela: Hudson | Secao responsavel: STI | Telefone: (67) 9 9155-5663 | Data cautela: 19/05/2026 10:18 | Possivel devolucao: 30/05/2026 | Devolucao marcada em: 19/05/2026 16:19', '2026-05-19 10:19:17'),
(31, 'STI', 3, 'Hudson', 'CAUTELOU_MATERIAL', 'cautela', 39, 'Materiais: Apontador (1), Caneta Preta (1) | Responsavel: Sd EP Vilalva | Secao responsavel: 9 BPE | Telefone: (67) 9 9862-3732 | Data cautela: 22/05/2026 08:56 | Possivel devolucao: 25/05/2026', '2026-05-22 08:56:56'),
(32, 'STI', 3, 'Hudson', 'MARCOU_DEVOLUCAO', 'cautela', 39, 'Material: Apontador | Descricao: Apontador metálico pequeno | Quantidade cautelada: 1 | Responsavel cautela: Sd EP Vilalva | Secao responsavel: 9 BPE | Telefone: (67) 9 9862-3732 | Data cautela: 22/05/2026 08:56 | Possivel devolucao: 25/05/2026 | Devolucao marcada em: 22/05/2026 14:58', '2026-05-22 08:58:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `secao` varchar(20) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `data_entrada` varchar(25) DEFAULT NULL,
  `data_saida` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `secao`, `quantidade`, `data_entrada`, `data_saida`) VALUES
(1, 'Cadeira', 'Cadeira azul com suporte para os braços', 'STI', 6, '2026-05-18', ''),
(3, 'Caneta Preta', 'Caneta esferográfica preta', 'STI', 45, '2026-05-18', ''),
(4, 'Caneta Vermelha', 'Caneta esferográfica vermelha', 'STI', 30, '08/10/2025', NULL),
(5, 'Lápis HB', 'Lápis grafite HB', 'STI', 100, '08/10/2025', NULL),
(6, 'Borracha Branca', 'Borracha escolar branca', 'STI', 60, '08/10/2025', NULL),
(7, 'Apontador', 'Apontador metálico pequeno', 'STI', 35, '08/10/2025', NULL),
(8, 'Caderno Pequeno', 'Caderno capa dura 100 folhas', 'STI', 20, '08/10/2025', NULL),
(9, 'Caderno Grande', 'Caderno universitário 200 folhas', 'STI', 15, '08/10/2025', NULL),
(10, 'Bloco de Notas', 'Bloco adesivo amarelo', 'STI', 25, '08/10/2025', NULL),
(11, 'Marcador Permanente', 'Marcador permanente preto', 'STI', 18, '08/10/2025', NULL),
(12, 'Marca Texto Amarelo', 'Caneta marca texto amarela', 'STI', 22, '08/10/2025', NULL),
(13, 'Marca Texto Verde', 'Caneta marca texto verde', 'STI', 20, '08/10/2025', NULL),
(14, 'Régua 30cm', 'Régua transparente 30 centímetros', 'STI', 12, '08/10/2025', NULL),
(15, 'Tesoura', 'Tesoura média para escritório', 'STI', 10, '08/10/2025', NULL),
(16, 'Cola Bastão', 'Cola bastão 20g', 'STI', 14, '08/10/2025', NULL),
(17, 'Cola Líquida', 'Cola líquida branca 90g', 'STI', 16, '08/10/2025', NULL),
(18, 'Fita Adesiva', 'Fita adesiva transparente', 'STI', 28, '08/10/2025', NULL),
(19, 'Fita Crepe', 'Fita crepe branca', 'STI', 17, '08/10/2025', NULL),
(20, 'Grampeador', 'Grampeador metálico médio', 'STI', 8, '08/10/2025', NULL),
(21, 'Grampo 26/6', 'Caixa de grampos 26/6', 'STI', 50, '08/10/2025', NULL),
(22, 'Perfurador', 'Perfurador de papel dois furos', 'STI', 6, '08/10/2025', NULL),
(23, 'Clips Pequeno', 'Caixa de clips pequenos', 'STI', 45, '08/10/2025', NULL),
(24, 'Clips Grande', 'Caixa de clips grandes', 'STI', 30, '08/10/2025', NULL),
(25, 'Pasta AZ', 'Pasta AZ preta', 'STI', 18, '08/10/2025', NULL),
(26, 'Pasta Suspensa', 'Pasta suspensa kraft', 'STI', 35, '08/10/2025', NULL),
(27, 'Envelope A4', 'Envelope branco tamanho A4', 'STI', 100, '08/10/2025', NULL),
(28, 'Envelope Ofício', 'Envelope papel pardo ofício', 'STI', 70, '08/10/2025', NULL),
(29, 'Papel Sulfite A4', 'Resma de papel sulfite A4', 'STI', 25, '08/10/2025', NULL),
(30, 'Papel Fotográfico', 'Pacote de papel fotográfico', 'STI', 12, '08/10/2025', NULL),
(31, 'Calculadora', 'Calculadora eletrônica de mesa', 'STI', 7, '08/10/2025', NULL),
(32, 'Mouse USB', 'Mouse óptico USB', 'STI', 9, '08/10/2025', NULL),
(33, 'Teclado USB', 'Teclado padrão ABNT2', 'STI', 5, '08/10/2025', NULL),
(34, 'Monitor 19', 'Monitor LED 19 polegadas', 'STI', 4, '08/10/2025', NULL),
(35, 'Cadeira Escritório', 'Cadeira giratória preta', 'STI', 3, '08/10/2025', NULL),
(36, 'Mesa Escritório', 'Mesa branca para escritório', 'STI', 2, '08/10/2025', NULL),
(37, 'Suporte Notebook', 'Suporte ergonômico para notebook', 'STI', 11, '08/10/2025', NULL),
(38, 'Pendrive 32GB', 'Pendrive USB 32GB', 'STI', 14, '08/10/2025', NULL),
(39, 'HD Externo 1TB', 'HD externo portátil 1TB', 'STI', 3, '08/10/2025', NULL),
(40, 'Cabo HDMI', 'Cabo HDMI 2 metros', 'STI', 13, '08/10/2025', NULL),
(41, 'Filtro de Linha', 'Filtro de linha com 5 tomadas', 'STI', 10, '08/10/2025', NULL),
(42, 'Extensão Elétrica', 'Extensão elétrica 10 metros', 'STI', 6, '08/10/2025', NULL),
(43, 'Lixeira Escritório', 'Lixeira plástica pequena', 'STI', 8, '08/10/2025', NULL),
(44, 'Álcool em Gel', 'Frasco de álcool em gel 500ml', 'STI', 16, '2026-05-18', ''),
(45, 'Máscara Descartável', 'Caixa com máscaras descartáveis', 'STI', 15, '08/10/2025', NULL),
(46, 'Copo Descartável', 'Pacote com copos descartáveis', 'STI', 40, '08/10/2025', NULL),
(47, 'Garrafa Térmica', 'Garrafa térmica 1 litro', 'STI', 4, '08/10/2025', NULL),
(48, 'Quadro Branco', 'Quadro branco médio', 'STI', 2, '08/10/2025', NULL),
(49, 'Apagador Quadro', 'Apagador para quadro branco', 'STI', 5, '08/10/2025', NULL),
(50, 'Pincel Quadro Branco', 'Pincel azul para quadro branco', 'STI', 18, '08/10/2025', NULL),
(51, 'Organizador Mesa', 'Organizador acrílico para mesa', 'STI', 7, '08/10/2025', NULL),
(52, 'Caneta', 'Caneta Esferografica Vermelha', 'STI', 30, '2023-09-11', ''),
(53, 'Caneta', 'Azul', 'SGO', 14, '2025-05-15', ''),
(54, 'caneta', 'azul', 'sgo', 5, '2026-05-10', ''),
(55, 'aa', 'aa', 'sti', 5, '2025-10-08', '2026-05-23'),
(56, 'Poltrona', 'Poltrona reclinavel', 'STI', 1, '2026-05-18', ''),
(57, 'Mesa', 'Mesa em L', 'STI', 1, '2026-05-18 23:59:58', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `secoes`
--

CREATE TABLE `secoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `secoes`
--

INSERT INTO `secoes` (`id`, `nome`) VALUES
(37, 'COP'),
(2, 'SGO'),
(3, 'SSGIE'),
(1, 'STI');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `posto` varchar(20) NOT NULL,
  `secao` varchar(50) NOT NULL,
  `privilegio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `senha`, `posto`, `secao`, `privilegio_id`) VALUES
(1, 'teste', 'teste', 'sgt', 'sti', 1),
(2, 'asd', '123', 'asd', 'asd', 2),
(3, 'Hudson', '123', '3º Sgt', 'STI', 2),
(5, 'QWE', '123', 'Sgt', 'COP', 2),
(6, 'SGO', 'sgo', 'sgt', 'SGO', 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cautelas_materiais`
--
ALTER TABLE `cautelas_materiais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `logs_movimentacoes`
--
ALTER TABLE `logs_movimentacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `secoes`
--
ALTER TABLE `secoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cautelas_materiais`
--
ALTER TABLE `cautelas_materiais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de tabela `logs_movimentacoes`
--
ALTER TABLE `logs_movimentacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de tabela `secoes`
--
ALTER TABLE `secoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cautelas_materiais`
--
ALTER TABLE `cautelas_materiais`
  ADD CONSTRAINT `cautelas_materiais_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
