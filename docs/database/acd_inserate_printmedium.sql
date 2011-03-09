-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.2
-- Erstellungszeit: 06. März 2011 um 22:34
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
-- Tabellenstruktur für Tabelle `acd_inserate_printmedium`
--

DROP TABLE IF EXISTS `acd_inserate_printmedium`;
CREATE TABLE `acd_inserate_printmedium` (
  `id_printmedium` int(11) NOT NULL auto_increment,
  `printmedium` varchar(255) NOT NULL default '',
  `color_printmedium` varchar(6) NOT NULL default '',
  `keywords_printmedium` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_printmedium`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `acd_inserate_printmedium`
--

INSERT INTO `acd_inserate_printmedium` (`id_printmedium`, `printmedium`, `color_printmedium`, `keywords_printmedium`) VALUES
(1, 'Neue Krone', 'e2001a', 'Krone'),
(2, 'Österreich', '3a6dab', ''),
(3, 'Heute', 'ee3048', ''),
(4, 'Der Standard', 'fdd9a7', 'Standard DerStandard'),
(5, 'Die Presse', '80a5b9', 'Presse'),
(6, 'Postwurf', '', ''),
(7, 'Kleine Zeitung Kärnten', '', '');
