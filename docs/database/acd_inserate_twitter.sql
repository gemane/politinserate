-- phpMyAdmin SQL Dump
-- version 2.11.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.2
-- Erstellungszeit: 17. Februar 2011 um 17:13
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
-- Tabellenstruktur für Tabelle `acd_inserate_twitter`
--

DROP TABLE IF EXISTS `acd_inserate_twitter`;
CREATE TABLE `acd_inserate_twitter` (
  `id_twitter` bigint(20) NOT NULL default '0',
  `text` varchar(255) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `username` varchar(255) NOT NULL default '',
  `id_inserat` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `acd_inserate_twitter`
--

INSERT INTO `acd_inserate_twitter` (`id_twitter`, `text`, `user_id`, `name`, `username`, `id_inserat`) VALUES
(19985555100, '@xanzeigenbot_at Anzeige der Österreichischen Regierung am 30.7.10 auf Seite 5 in der Heute in Wien. http://twitpic.com/2abij8', 172735561, 'Gerold Neuwirt-Test', 'test_gemane', 16),
(19910430399, '@xanzeigenbot_at Anzeige der SPÖ Wien in der Krone #werzahltinserate http://twitpic.com/2a0jw1', 172735561, 'Gerold Neuwirt-Test', 'test_gemane', 17),
(21435051504, '@xanzeigenbot_at anzeige als ganze seite in der zeitung österreich in wien für die spö auf seite 19. bezahlt von d http://twitpic.com/2fp8uy', 172735561, 'Gerold Neuwirt-Test', 'test_gemane', 18);
