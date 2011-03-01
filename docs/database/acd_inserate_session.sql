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
-- Datenbank: `db213870`
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

INSERT INTO `acd_inserate_session` (`id_session`, `modified`, `lifetime`, `session_data`) VALUES
('0023ca45d4a09268c59ccfff2b9b316e', 1282261458, 2592000, 'Zend_Auth|a:1:{s:7:"storage";O:8:"stdClass":15:{s:7:"id_user";s:2:"54";s:8:"username";s:6:"gemane";s:8:"password";s:40:"5203357d3aa6ca5d88d1f52f77e6e8e4fb738287";s:13:"date_register";s:19:"2010-07-14 16:45:40";s:11:"last_access";s:19:"2010-08-13 15:00:19";s:10:"user_email";s:24:"gerold.neuwirt@gmail.com";s:14:"user_activated";s:1:"1";s:19:"user_activationcode";s:40:"871d97621fc8a44b68780d856c9acad5091bb0e9";s:13:"user_fullname";s:14:"Gerold Neuwirt";s:9:"user_show";s:1:"0";s:15:"prefered_region";s:1:"2";s:20:"prefered_printmedium";s:1:"0";s:11:"max_uploads";s:2:"10";s:2:"ip";s:14:"91.113.168.134";s:10:"user_agent";s:98:"Mozilla/5.0 (X11; U; Linux i686; de; rv:1.9.2.8) Gecko/20100723 Ubuntu/10.04 (lucid) Firefox/3.6.8";}}'),
('457f8cd4b94b1cd6f7545354635f26a1', 1282305924, 2592000, '__ZF|a:1:{s:33:"Zend_Form_Element_Hash_salt_token";a:2:{s:4:"ENNH";i:1;s:3:"ENT";i:1282306102;}}Zend_Form_Element_Hash_salt_token|a:1:{s:4:"hash";s:32:"4f43bdf7541b2a743333ea89dc11571a";}'),
('46161cf17cdbb715881aa2e8023f26f2', 1282418886, 2592000, 'Default|a:1:{s:18:"userJustRegistered";s:2:"gh";}Zend_Auth|a:1:{s:7:"storage";O:8:"stdClass":15:{s:7:"id_user";s:2:"57";s:8:"username";s:2:"gh";s:8:"password";s:40:"81644f0b8fa6c3b058b83b92e7580b08a8a53eb3";s:13:"date_register";s:19:"2010-08-21 21:26:14";s:11:"last_access";s:19:"2010-08-21 21:26:14";s:10:"user_email";s:10:"gh@live.at";s:14:"user_activated";s:1:"1";s:19:"user_activationcode";s:40:"5d79f439436fd7192e6645d375cd1687d16f9fa2";s:13:"user_fullname";s:0:"";s:9:"user_show";s:1:"0";s:15:"prefered_region";s:1:"0";s:20:"prefered_printmedium";s:1:"0";s:11:"max_uploads";s:2:"10";s:2:"ip";s:14:"195.16.252.135";s:10:"user_agent";s:117:"Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.127 Safari/533.4";}}__ZF|a:1:{s:33:"Zend_Form_Element_Hash_salt_token";a:2:{s:4:"ENNH";i:1;s:3:"ENT";i:1282419060;}}Zend_Form_Element_Hash_salt_token|a:1:{s:4:"hash";s:32:"1b2cae0475ee48ed8d738a5171296b74";}'),
('527d5859d34e020e08fb4a5834214557', 1283178079, 2592000, 'Zend_Auth|a:1:{s:7:"storage";O:8:"stdClass":15:{s:7:"id_user";s:2:"54";s:8:"username";s:6:"gemane";s:8:"password";s:40:"5203357d3aa6ca5d88d1f52f77e6e8e4fb738287";s:13:"date_register";s:19:"2010-07-14 16:45:40";s:11:"last_access";s:19:"2010-08-20 01:40:49";s:10:"user_email";s:24:"gerold.neuwirt@gmail.com";s:14:"user_activated";s:1:"1";s:19:"user_activationcode";s:40:"871d97621fc8a44b68780d856c9acad5091bb0e9";s:13:"user_fullname";s:14:"Gerold Neuwirt";s:9:"user_show";s:1:"0";s:15:"prefered_region";s:1:"2";s:20:"prefered_printmedium";s:1:"0";s:11:"max_uploads";s:2:"10";s:2:"ip";s:14:"91.113.168.134";s:10:"user_agent";s:107:"Mozilla/5.0 (X11; U; Linux i686; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.9 Safari/533.2";}}__ZF|a:1:{s:33:"Zend_Form_Element_Hash_salt_token";a:2:{s:4:"ENNH";i:1;s:3:"ENT";i:1283178256;}}Zend_Form_Element_Hash_salt_token|a:1:{s:4:"hash";s:32:"c362cbc31407ee3d7098125ef36ca24e";}'),
('6bbe3f6028d755f6224b414e31ef5321', 1282836432, 2592000, ''),
('8a2fcf05bed63fe0f22311cf9f6dcf95', 1282841585, 2592000, ''),
('b9b6691ce8f56fc42a24ffd46a8b182d', 1282575724, 2592000, ''),
('c10ae1f26f5db9b85215acc6b81e07ad', 1282308354, 2592000, ''),
('d3e0bdc7c2af6b3eb4b1e35a8207947e', 1282834056, 2592000, '__ZF|a:1:{s:33:"Zend_Form_Element_Hash_salt_token";a:2:{s:4:"ENNH";i:1;s:3:"ENT";i:1282834190;}}Zend_Form_Element_Hash_salt_token|a:1:{s:4:"hash";s:32:"e7e083c1aadc84353279bb89f719861c";}'),
('d76302012e1defa04fd19a4ff4b4bc5c', 1282182705, 2592000, '');
