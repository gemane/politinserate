-- phpMyAdmin SQL Dump
-- version 2.11.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.2
-- Erstellungszeit: 28. Februar 2011 um 02:01
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
-- Tabellenstruktur für Tabelle `acd_inserate_datafile`
--

DROP TABLE IF EXISTS `acd_inserate_datafile`;
CREATE TABLE `acd_inserate_datafile` (
  `id_datafile` int(11) NOT NULL auto_increment,
  `path` varchar(255) NOT NULL default '',
  `id_printmedium` int(11) NOT NULL default '0',
  `id_region_printmedium_bit` int(4) NOT NULL default '0',
  `year` year(4) NOT NULL default '0000',
  `date_from` date NOT NULL default '0000-00-00',
  `date_to` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_datafile`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Daten für Tabelle `acd_inserate_datafile`
--

INSERT INTO `acd_inserate_datafile` (`id_datafile`, `path`, `id_printmedium`, `id_region_printmedium_bit`, `year`, `date_from`, `date_to`) VALUES
(40, 'KroneTarife2010BGLD.pdf', 1, 128, 2010, '2010-01-01', '2010-12-31'),
(39, 'KroneTarife2010OOE.pdf', 1, 16, 2010, '2010-01-01', '2010-12-31'),
(38, 'KroneTarife2010NOE.pdf', 1, 8, 2010, '2010-01-01', '2010-12-31'),
(37, 'KroneTarife2010STMK.pdf', 1, 64, 2010, '2010-01-01', '2010-12-31'),
(36, 'KroneTarife2010WIEN.pdf', 1, 4, 2010, '2010-01-01', '2010-12-31'),
(41, 'krone_GesamtTarife2010.pdf', 1, 2046, 2010, '2010-01-01', '2010-12-31'),
(42, 'KroneTarife2010SBG.pdf', 1, 32, 2010, '2010-01-01', '2010-12-31'),
(43, 'KroneTarife2010KTN.pdf', 1, 256, 2010, '2010-01-01', '2010-12-31'),
(44, 'KroneTarife2010TIROL.pdf', 1, 512, 2010, '2010-01-01', '2010-12-31'),
(48, 'PL_TZ_2010_.pdf', 4, 2046, 2010, '2010-01-01', '2010-12-31'),
(49, 'Die_Presse_Anzeigentarif_2010.pdf', 5, 2046, 2010, '2010-01-01', '2010-12-31'),
(50, '471_HEUTE_TARIF2010_WIEN_lo.pdf', 3, 4, 2010, '2010-01-01', '2010-12-31'),
(51, 'HEUTE_TARIF2010_OOe_lo.pdf', 3, 16, 2010, '2010-01-01', '2010-12-31'),
(52, 'preise_4Seiter_2010_20100420.pdf', 2, 2046, 2010, '2010-01-01', '2010-12-31'),
(53, 'preise_OOe_2010.pdf', 2, 16, 2010, '2010-01-01', '2010-12-31'),
(55, '474_HEUTE_TARIF2010_WIEN_NOe_lo.pdf', 3, 12, 2010, '2010-01-01', '2010-12-31'),
(56, '475_HEUTE_TARIF2010_W_NOe_OOe_lo.pdf', 3, 2046, 2010, '2010-01-01', '2010-12-31'),
(57, '472_HEUTE_TARIF2010_NOe_lo.pdf', 3, 8, 2010, '2010-01-01', '2010-12-31'),
(58, 'preise_4Seiter_2010_20100420.pdf', 2, 12, 2010, '2010-01-01', '2010-12-31');
