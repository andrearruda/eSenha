-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 14-Mar-2016 às 18:41
-- Versão do servidor: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `essj_senha`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `box`
--

CREATE TABLE IF NOT EXISTS `box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Extraindo dados da tabela `box`
--

INSERT INTO `box` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Guichê 01', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL),
(2, 'Guichê 02', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL),
(3, 'Guichê 03', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL),
(4, 'Guichê 04', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL),
(5, 'Guichê 05', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL),
(6, 'Guichê 06', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `ticket`
--

CREATE TABLE IF NOT EXISTS `ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_type_id` int(11) NOT NULL,
  `box_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `displayed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ticket_ticket_type_idx` (`ticket_type_id`),
  KEY `fk_ticket_box1_idx` (`box_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ticket_type`
--

CREATE TABLE IF NOT EXISTS `ticket_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `ticket_type`
--

INSERT INTO `ticket_type` (`id`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ESSJ', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL),
(2, 'UNISAL', '2016-03-08 17:05:00', '2016-03-08 17:05:00', NULL);

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_box1` FOREIGN KEY (`box_id`) REFERENCES `box` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_ticket_type` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
