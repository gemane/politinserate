-- phpMyAdmin SQL Dump
-- version 3.3.7deb3build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 19. Februar 2011 um 21:40
-- Server Version: 5.1.49
-- PHP-Version: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `inserate`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `acd_inserate_ip`
--

DROP TABLE IF EXISTS `acd_inserate_ip`;
CREATE TABLE IF NOT EXISTS `acd_inserate_ip` (
  `id_ip` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_user` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  PRIMARY KEY (`id_ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Daten für Tabelle `acd_inserate_ip`
--

INSERT INTO `acd_inserate_ip` (`id_ip`, `ip`, `timestamp`, `id_user`, `task`) VALUES
(42, '127.0.0.1', '2011-02-19 15:09:35', 1, 'entry');
