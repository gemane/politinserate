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
-- Tabellenstruktur für Tabelle `acd_inserate_config`
--

DROP TABLE IF EXISTS `acd_inserate_config`;
CREATE TABLE `acd_inserate_config` (
  `id_config` int(11) NOT NULL auto_increment,
  `party` varchar(255) NOT NULL default '',
  `region` varchar(255) NOT NULL default '',
  `region_abb` varchar(255) NOT NULL default '',
  `government` varchar(255) NOT NULL default '',
  `source` varchar(255) NOT NULL default '',
  `color_party` varchar(6) NOT NULL default '',
  `color_region` varchar(6) NOT NULL default '',
  `size_image` varchar(255) NOT NULL default '',
  `height_image` varchar(255) NOT NULL default '',
  `height_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_config`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Daten für Tabelle `acd_inserate_config`
--

INSERT INTO `acd_inserate_config` (`id_config`, `party`, `region`, `region_abb`, `government`, `source`, `color_party`, `color_region`, `size_image`, `height_image`, `height_name`) VALUES
(1, 'SPÖ', 'Österreich', 'aut', 'Republik Österreich', 'Homepage', 'e3001b', 'ec0000', '1x1_01-Seite', '1x1_01-Seite', 'Ganze Seite'),
(2, 'ÖVP', 'Wien', 'w', 'Gemeinde Wien', 'Twitter', '000000', 'ec8e00', '1xJuniorpage', '1x1_02-Seite', 'Halbe Seite'),
(3, 'FPÖ', 'Niederösterreich', 'noe', 'Land Niederösterreich', 'Facebook', '0c7fc2', 'bdec00', '1x1_02-Seite', '1x1_03-Seite', 'Drittel Seite'),
(4, 'Die Grünen', 'Oberösterreich', 'ooe', 'Land Oberösterreich', 'Android', '4da112', '2fec00', '1x1_04-Seite', '1x1_04-Seite', 'Viertel Seite'),
(5, 'BZÖ', 'Salzburg', 'sbg', 'Land Salzburg', 'iPhone', 'f29400', '00ec5f', '1x1_06-Seite', '1x1_06-Seite', 'Sechstel Seite'),
(6, 'FPK', 'Steiermark', 'stm', 'Land Steiermark', 'Nokia', '0076bd', '00ecec', '1x1_08-Seite', '1x1_08-Seite', 'Achtel Seite'),
(7, '', 'Burgenland', 'bgl', 'Land Burgenland', '', '', '005fec', '1x1_16-Seite', 'Kopfzeile', 'Kopfzeile'),
(8, '', 'Kärnten', 'ktn', 'Land Kärnten', '', '', '2f00ec', '2x1_1-Seite', 'Fusszeile', 'Fusszeile'),
(9, '', 'Tirol', 'tir', 'Land Tirol', '', '', 'bd00ec', '2xJuniorpage', 'Juniorpage', 'Juniorpage'),
(10, '', 'Vorarlberg', 'vbg', 'Land Vorarlberg', '', '', 'ec008e', '2x1_2-Seite', 'Sonderformat', 'Sonderformat'),
(11, '', '', '', '', '', '', '', 'Sonderformat', '1x2_03-Seite', 'Zweidrittel Seite'),
(12, '', '', '', '', '', '', '', '1x1_03-Seite', '', ''),
(13, '', '', '', '', '', '', '', '1x1_02-Seite_h', '', ''),
(14, '', '', '', '', '', '', '', '1x1_04-Seite_q', '', ''),
(15, '', '', '', '', '', '', '', '1x1_04-Seite_h', '', ''),
(16, '', '', '', '', '', '', '', 'Fusszeile_1_05', '', ''),
(17, '', '', '', '', '', '', '', 'Fusszeile_2_05', '', ''),
(18, '', '', '', '', '', '', '', 'Fusszeile_4_05', '', ''),
(19, '', '', '', '', '', '', '', 'Fusszeile_3_05', '', ''),
(20, '', '', '', '', '', '', '', 'Fusszeile_5_05', '', '');
