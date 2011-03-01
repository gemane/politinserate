-- phpMyAdmin SQL Dump
-- version 2.11.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.2
-- Erstellungszeit: 28. Februar 2011 um 02:02
-- Server Version: 4.1.22
-- PHP-Version: 4.4.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `db213870`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `acd_inserate_size_weekdays`
--

DROP TABLE IF EXISTS `acd_inserate_size_weekdays`;
CREATE TABLE `acd_inserate_size_weekdays` (
  `id_weekdays` int(11) NOT NULL auto_increment,
  `Mon` tinyint(1) NOT NULL default '0',
  `Tue` tinyint(1) NOT NULL default '0',
  `Wed` tinyint(1) NOT NULL default '0',
  `Thu` tinyint(1) NOT NULL default '0',
  `Fri` tinyint(1) NOT NULL default '0',
  `Sat` tinyint(1) NOT NULL default '0',
  `Sun` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_weekdays`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115 ;

--
-- Daten für Tabelle `acd_inserate_size_weekdays`
--

INSERT INTO `acd_inserate_size_weekdays` (`id_weekdays`, `Mon`, `Tue`, `Wed`, `Thu`, `Fri`, `Sat`, `Sun`) VALUES
(1, 1, 1, 1, 0, 0, 0, 0),
(2, 1, 1, 1, 0, 0, 0, 0),
(20, 0, 0, 0, 1, 1, 1, 0),
(21, 0, 0, 0, 0, 0, 0, 1),
(16, 0, 0, 0, 1, 1, 1, 0),
(17, 0, 0, 0, 0, 0, 0, 1),
(27, 1, 1, 1, 0, 0, 0, 0),
(28, 0, 0, 0, 1, 1, 1, 0),
(29, 0, 0, 0, 0, 0, 0, 1),
(31, 1, 1, 1, 1, 1, 1, 1),
(36, 1, 1, 1, 1, 1, 1, 1),
(37, 1, 1, 1, 0, 0, 1, 0),
(38, 0, 0, 0, 1, 1, 0, 0),
(39, 0, 0, 0, 0, 0, 0, 1),
(40, 1, 1, 1, 1, 1, 1, 1),
(42, 1, 1, 1, 0, 0, 1, 0),
(43, 0, 0, 0, 1, 1, 0, 0),
(44, 0, 0, 0, 0, 0, 0, 1),
(49, 1, 1, 1, 1, 1, 1, 1),
(51, 1, 1, 1, 1, 1, 1, 1),
(53, 1, 1, 1, 0, 0, 1, 0),
(54, 1, 1, 1, 0, 0, 1, 0),
(55, 0, 0, 0, 1, 1, 0, 0),
(56, 0, 0, 0, 0, 0, 0, 1),
(57, 1, 1, 1, 0, 0, 1, 0),
(58, 0, 0, 0, 1, 1, 0, 0),
(59, 0, 0, 0, 0, 0, 0, 1),
(60, 0, 0, 0, 1, 1, 0, 0),
(61, 0, 0, 0, 0, 0, 0, 1),
(65, 1, 1, 1, 0, 0, 1, 0),
(66, 0, 0, 0, 1, 1, 0, 0),
(67, 0, 0, 0, 0, 0, 0, 1),
(71, 1, 1, 1, 1, 1, 1, 1),
(72, 1, 1, 1, 0, 0, 1, 0),
(73, 0, 0, 0, 0, 0, 0, 1),
(74, 1, 1, 1, 1, 1, 1, 1),
(76, 0, 0, 0, 1, 1, 0, 0),
(77, 0, 0, 0, 0, 0, 0, 1),
(79, 1, 1, 1, 1, 1, 1, 1),
(92, 1, 1, 1, 1, 1, 1, 1),
(93, 1, 1, 1, 1, 1, 1, 1),
(95, 1, 1, 1, 1, 1, 1, 1),
(96, 1, 1, 1, 1, 1, 0, 0),
(97, 0, 0, 0, 0, 0, 1, 1),
(98, 1, 1, 1, 1, 1, 0, 0),
(99, 0, 0, 0, 0, 0, 1, 1),
(100, 1, 1, 1, 1, 1, 1, 1),
(101, 1, 1, 1, 1, 1, 1, 1),
(102, 1, 1, 1, 0, 0, 1, 0),
(103, 0, 0, 0, 1, 1, 0, 0),
(104, 0, 0, 0, 0, 0, 0, 1),
(105, 1, 1, 1, 1, 1, 1, 1),
(106, 1, 1, 1, 1, 1, 1, 1),
(107, 1, 1, 1, 0, 0, 1, 0),
(108, 0, 0, 0, 1, 1, 0, 0),
(109, 0, 0, 0, 0, 0, 0, 1),
(110, 1, 1, 1, 1, 1, 1, 1),
(111, 1, 1, 1, 1, 1, 1, 1),
(112, 1, 1, 1, 1, 1, 1, 1),
(113, 1, 1, 1, 1, 1, 1, 1),
(114, 1, 1, 1, 1, 1, 1, 1);
