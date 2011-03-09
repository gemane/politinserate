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
-- Tabellenstruktur für Tabelle `acd_inserate_printmedium_type`
--

DROP TABLE IF EXISTS `acd_inserate_printmedium_type`;
CREATE TABLE `acd_inserate_printmedium_type` (
  `id_printmedium_type` int(11) NOT NULL auto_increment,
  `printmedium_type_position` int(11) NOT NULL default '0',
  `printmedium_type_name` varchar(255) NOT NULL default '',
  `printmedium_width` int(11) NOT NULL default '0',
  `printmedium_height` int(11) NOT NULL default '0',
  `printmedium_columns_width` int(11) NOT NULL default '0',
  `id_printmedium` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_printmedium_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `acd_inserate_printmedium_type`
--

INSERT INTO `acd_inserate_printmedium_type` (`id_printmedium_type`, `printmedium_type_position`, `printmedium_type_name`, `printmedium_width`, `printmedium_height`, `printmedium_columns_width`, `id_printmedium`) VALUES
(2, 0, 'Hauptblatt', 266, 421, 50, 5),
(3, 0, 'Tageszeitung', 266, 420, 50, 4),
(4, 0, 'Tageszeitung', 203, 272, 48, 3),
(5, 0, 'Tageszeitung', 216, 315, 40, 2),
(6, 0, 'Tageszeitung', 196, 265, 46, 1),
(11, 1, 'Farbmagazin', 227, 322, 40, 2),
(12, 1, 'Guide', 128, 185, 50, 5),
(15, 0, 'Broschüre A6', 115, 149, 115, 6),
(16, 0, 'Tageszeitung', 200, 279, 47, 7);
