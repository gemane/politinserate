-- phpMyAdmin SQL Dump
-- version 2.11.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.2
-- Erstellungszeit: 30. August 2010 um 16:23
-- Server Version: 4.1.22
-- PHP-Version: 4.4.8

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
-- Tabellenstruktur für Tabelle `acd_inserate_session`
--

DROP TABLE IF EXISTS `acd_inserate_session`;
CREATE TABLE `acd_inserate_session` (
  `id_session` varchar(32) NOT NULL default '',
  `modified` int(11) NOT NULL default '0',
  `lifetime` int(11) NOT NULL default '0',
  `session_data` text NOT NULL,
  PRIMARY KEY  (`id_session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `acd_inserate_session`
--

