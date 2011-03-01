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
-- Tabellenstruktur für Tabelle `acd_inserate_size`
--

DROP TABLE IF EXISTS `acd_inserate_size`;
CREATE TABLE `acd_inserate_size` (
  `id_size` int(11) NOT NULL auto_increment,
  `size` varchar(255) NOT NULL default '',
  `cover` int(11) NOT NULL default '0',
  `id_printmedium_type` int(11) NOT NULL default '0',
  `id_size_image` int(11) NOT NULL default '0',
  `id_height_image` int(11) NOT NULL default '0',
  `size_height` int(11) NOT NULL default '0',
  `price` int(11) NOT NULL default '0',
  `id_datafile` int(11) NOT NULL default '0',
  `id_weekdays` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_size`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

--
-- Daten für Tabelle `acd_inserate_size`
--

INSERT INTO `acd_inserate_size` (`id_size`, `size`, `cover`, `id_printmedium_type`, `id_size_image`, `id_height_image`, `size_height`, `price`, `id_datafile`, `id_weekdays`, `id_user`, `timestamp`) VALUES
(1, 'Ganze Seite', 0, 6, 1, 1, 265, 20352, 36, 1, 0, '0000-00-00 00:00:00'),
(2, 'Halbe Seite', 0, 6, 3, 2, 135, 10368, 36, 2, 0, '0000-00-00 00:00:00'),
(12, 'Ganze Seite', 0, 6, 1, 1, 265, 21200, 36, 17, 0, '0000-00-00 00:00:00'),
(11, 'Ganze Seite', 0, 6, 1, 1, 265, 20776, 36, 16, 0, '0000-00-00 00:00:00'),
(16, 'Halbe Seite', 0, 6, 3, 2, 135, 10800, 36, 21, 0, '0000-00-00 00:00:00'),
(15, 'Halbe Seite', 0, 6, 3, 2, 135, 10584, 36, 20, 0, '0000-00-00 00:00:00'),
(22, 'Halbe Seite - Textteil', 0, 6, 3, 2, 135, 23868, 41, 27, 0, '0000-00-00 00:00:00'),
(23, 'Halbe Seite - Textteil', 0, 6, 3, 2, 135, 25434, 41, 28, 0, '0000-00-00 00:00:00'),
(24, 'Halbe Seite - Textteil', 0, 6, 3, 2, 135, 26406, 41, 29, 0, '0000-00-00 00:00:00'),
(26, '1/1 Seite', 0, 4, 1, 1, 272, 13600, 50, 31, 0, '0000-00-00 00:00:00'),
(94, 'Juniorpage', 0, 5, 0, 9, 240, 18925, 52, 108, 0, '0000-00-00 00:00:00'),
(31, '1/2 Seite', 0, 4, 3, 2, 136, 6800, 50, 36, 0, '0000-00-00 00:00:00'),
(32, '1/1 Seite', 0, 5, 1, 1, 315, 14600, 52, 37, 0, '0000-00-00 00:00:00'),
(33, '1/1 Seite', 0, 5, 1, 1, 315, 18370, 52, 38, 0, '0000-00-00 00:00:00'),
(34, '1/1 Seite', 0, 5, 1, 1, 315, 21900, 52, 39, 0, '0000-00-00 00:00:00'),
(35, '1/1 Seite', 0, 11, 1, 1, 322, 24900, 52, 40, 0, '0000-00-00 00:00:00'),
(37, '1/2 quer', 0, 5, 3, 2, 154, 3400, 53, 42, 0, '0000-00-00 00:00:00'),
(38, '1/2 quer', 0, 5, 3, 2, 154, 3600, 53, 43, 0, '0000-00-00 00:00:00'),
(39, '1/2 quer', 0, 5, 3, 2, 154, 4300, 53, 44, 0, '0000-00-00 00:00:00'),
(93, 'Juniorpage', 0, 5, 0, 9, 240, 11800, 52, 107, 0, '0000-00-00 00:00:00'),
(92, 'Juniorpage', 0, 4, 0, 9, 202, 14544, 56, 106, 0, '0000-00-00 00:00:00'),
(44, '1/1 Seite', 0, 4, 1, 1, 272, 3808, 51, 49, 0, '0000-00-00 00:00:00'),
(46, '1/2 Seite', 0, 4, 3, 2, 136, 1904, 51, 51, 0, '0000-00-00 00:00:00'),
(91, 'Juniorpage', 0, 4, 0, 9, 202, 10100, 50, 105, 0, '0000-00-00 00:00:00'),
(48, '1/2 quer', 0, 5, 3, 2, 154, 8300, 52, 53, 0, '0000-00-00 00:00:00'),
(49, '1/1 Seite', 0, 5, 1, 1, 315, 13100, 58, 54, 0, '0000-00-00 00:00:00'),
(50, '1/1 Seite', 0, 5, 1, 1, 315, 16720, 58, 55, 0, '0000-00-00 00:00:00'),
(51, '1/1 Seite', 0, 5, 1, 1, 315, 19900, 58, 56, 0, '0000-00-00 00:00:00'),
(52, '1/2 quer', 0, 5, 3, 2, 154, 7900, 58, 57, 0, '0000-00-00 00:00:00'),
(53, '1/2 quer', 0, 5, 3, 2, 154, 10010, 58, 58, 0, '0000-00-00 00:00:00'),
(54, '1/2 quer', 0, 5, 3, 2, 154, 11900, 58, 59, 0, '0000-00-00 00:00:00'),
(55, '1/2 quer', 0, 5, 3, 2, 154, 10340, 52, 60, 0, '0000-00-00 00:00:00'),
(56, '1/2 quer', 0, 5, 3, 2, 154, 12500, 52, 61, 0, '0000-00-00 00:00:00'),
(60, '1/3 quer', 0, 5, 12, 3, 101, 5600, 58, 65, 0, '0000-00-00 00:00:00'),
(61, '1/3 quer', 0, 5, 12, 3, 101, 7370, 58, 66, 0, '0000-00-00 00:00:00'),
(62, '1/3 quer', 0, 5, 12, 3, 101, 8400, 58, 67, 0, '0000-00-00 00:00:00'),
(90, '1/3 quer', 0, 5, 0, 3, 101, 9700, 52, 104, 0, '0000-00-00 00:00:00'),
(89, '1/3 quer', 0, 5, 0, 3, 101, 8140, 52, 103, 0, '0000-00-00 00:00:00'),
(66, '1/3 Seite', 0, 4, 12, 3, 91, 4550, 50, 71, 0, '0000-00-00 00:00:00'),
(67, '5spaltig', 1, 5, 20, 8, 45, 8870, 52, 72, 0, '0000-00-00 00:00:00'),
(68, '5spaltig', 1, 11, 20, 8, 45, 20700, 52, 73, 0, '0000-00-00 00:00:00'),
(69, '1/4 Seite', 0, 4, 14, 4, 68, 3400, 50, 74, 0, '0000-00-00 00:00:00'),
(71, '5spaltig', 1, 5, 20, 8, 45, 10950, 52, 76, 0, '0000-00-00 00:00:00'),
(72, '5spaltig', 1, 5, 20, 8, 45, 13120, 52, 77, 0, '0000-00-00 00:00:00'),
(88, '1/3 quer', 0, 5, 0, 3, 101, 6400, 52, 102, 0, '0000-00-00 00:00:00'),
(74, '1/1 Seite', 0, 4, 1, 1, 272, 19584, 56, 79, 0, '0000-00-00 00:00:00'),
(78, '1/2 Seite', 0, 4, 3, 2, 136, 9792, 56, 92, 0, '0000-00-00 00:00:00'),
(79, '1/3 Seite', 0, 4, 12, 3, 91, 6552, 56, 93, 0, '0000-00-00 00:00:00'),
(81, '9 Seiten in Beilage Presse-Scout Lehre', 0, 2, 11, 0, 0, 25000, 49, 95, 0, '0000-00-00 00:00:00'),
(82, '1/2 Seite', 0, 3, 0, 2, 208, 10990, 48, 96, 0, '0000-00-00 00:00:00'),
(83, '1/2 Seite', 0, 3, 0, 2, 208, 14790, 48, 97, 0, '0000-00-00 00:00:00'),
(84, '1/1-Seite Basis', 0, 2, 0, 1, 421, 17704, 49, 98, 0, '0000-00-00 00:00:00'),
(85, '1/1-Seite Basis', 0, 2, 0, 1, 421, 21534, 49, 99, 0, '0000-00-00 00:00:00'),
(86, 'Kopfzeile', 0, 4, 0, 7, 46, 2300, 50, 100, 0, '0000-00-00 00:00:00'),
(87, 'Fußzeile', 0, 4, 0, 8, 50, 2500, 50, 101, 0, '0000-00-00 00:00:00'),
(95, 'Juniorpage', 0, 5, 0, 9, 240, 22480, 52, 109, 0, '0000-00-00 00:00:00'),
(96, '1/4 Seite', 0, 4, 0, 4, 68, 4896, 56, 110, 0, '0000-00-00 00:00:00'),
(97, 'Fußzeile Titelseite', 1, 4, 0, 8, 48, 5800, 50, 111, 54, '2011-02-26 19:44:35'),
(98, 'Titelseite Fußzeile', 1, 4, 0, 8, 48, 8592, 56, 112, 54, '2011-02-26 19:46:09'),
(99, 'Kopfzeile', 0, 4, 0, 7, 46, 3312, 56, 113, 54, '2011-02-26 19:48:33'),
(100, 'Fußzeile', 0, 4, 0, 8, 50, 3600, 56, 114, 54, '2011-02-26 19:49:23');