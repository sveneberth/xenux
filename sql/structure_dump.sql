-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Mai 2015 um 19:18
-- Server Version: 5.6.21
-- PHP-Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `xenux`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%contactpersons`
--

DROP TABLE IF EXISTS `%PREFIX%contactpersons`;
CREATE TABLE IF NOT EXISTS `%PREFIX%contactpersons` (
`id` int(10) NOT NULL,
  `name` varchar(150) NOT NULL,
  `position` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%events`
--

DROP TABLE IF EXISTS `%PREFIX%events`;
CREATE TABLE IF NOT EXISTS `%PREFIX%events` (
`id` int(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  `public` tinyint(1) DEFAULT '0' COMMENT '1=public;0=private',
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%files`
--

DROP TABLE IF EXISTS `%PREFIX%files`;
CREATE TABLE IF NOT EXISTS `%PREFIX%files` (
`id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `mime_type` varchar(200) DEFAULT NULL,
  `data` longblob,
  `filename` varchar(200) DEFAULT NULL,
  `size` int(20) NOT NULL COMMENT 'size in byte',
  `lastModified` timestamp NULL DEFAULT NULL,
  `parent_folder_id` int(10) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%main`
--

DROP TABLE IF EXISTS `%PREFIX%main`;
CREATE TABLE IF NOT EXISTS `%PREFIX%main` (
`id` int(10) NOT NULL,
  `name` varchar(150) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(150) NOT NULL,
  `label` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%news`
--

DROP TABLE IF EXISTS `%PREFIX%news`;
CREATE TABLE IF NOT EXISTS `%PREFIX%news` (
`id` int(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `lastModified_date` timestamp NULL DEFAULT NULL,
  `public` tinyint(1) DEFAULT '0' COMMENT '1=public;0=private',
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%sites`
--

DROP TABLE IF EXISTS `%PREFIX%sites`;
CREATE TABLE IF NOT EXISTS `%PREFIX%sites` (
`id` int(10) NOT NULL,
  `title` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastModified_date` timestamp NULL DEFAULT NULL,
  `parent_id` int(10) NOT NULL,
  `sortindex` int(10) NOT NULL,
  `public` tinyint(1) DEFAULT '0' COMMENT '1=public;0=private',
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%site_contactperson`
--

DROP TABLE IF EXISTS `%PREFIX%site_contactperson`;
CREATE TABLE IF NOT EXISTS `%PREFIX%site_contactperson` (
`id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `contactperson_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=363 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%users`
--

DROP TABLE IF EXISTS `%PREFIX%users`;
CREATE TABLE IF NOT EXISTS `%PREFIX%users` (
`id` int(10) NOT NULL,
  `username` varchar(200) NOT NULL,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `homepage` text,
  `bio` text,
  `password` varchar(200) NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `role` int(2) NOT NULL DEFAULT '1',
  `lastlogin_date` timestamp NULL DEFAULT NULL,
  `lastlogin_ip` varchar(100) DEFAULT NULL,
  `session_fingerprint` varchar(100) DEFAULT NULL,
  `verifykey` varchar(200) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `%PREFIX%contactpersons`
--
ALTER TABLE `%PREFIX%contactpersons`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `%PREFIX%events`
--
ALTER TABLE `%PREFIX%events`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `%PREFIX%files`
--
ALTER TABLE `%PREFIX%files`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `%PREFIX%main`
--
ALTER TABLE `%PREFIX%main`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `%PREFIX%news`
--
ALTER TABLE `%PREFIX%news`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `%PREFIX%sites`
--
ALTER TABLE `%PREFIX%sites`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `%PREFIX%site_contactperson`
--
ALTER TABLE `%PREFIX%site_contactperson`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `%PREFIX%users`
--
ALTER TABLE `%PREFIX%users`
 ADD PRIMARY KEY (`id`);
s
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
