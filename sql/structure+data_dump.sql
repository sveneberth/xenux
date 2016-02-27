-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 2015-12-19 14:41
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
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(150) NOT NULL,
  `position` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `%PREFIX%contactpersons`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%events`
--

DROP TABLE IF EXISTS `%PREFIX%events`;
CREATE TABLE IF NOT EXISTS `%PREFIX%events` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `public` tinyint(1) DEFAULT '0' COMMENT '1=public;0=private',
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `%PREFIX%events`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%files`
--

DROP TABLE IF EXISTS `%PREFIX%files`;
CREATE TABLE IF NOT EXISTS `%PREFIX%files` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(50) NOT NULL,
  `mime_type` varchar(200) DEFAULT NULL,
  `data` longblob,
  `filename` varchar(200) DEFAULT NULL,
  `size` int(20) NOT NULL COMMENT 'size in byte',
  `lastModified` timestamp NULL DEFAULT NULL,
  `parent_folder_id` int(10) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `%PREFIX%files`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%main`
--

DROP TABLE IF EXISTS `%PREFIX%main`;
CREATE TABLE IF NOT EXISTS `%PREFIX%main` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(150) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `%PREFIX%main`
--

INSERT INTO `%PREFIX%main` (`name`, `value`) VALUES
('hp_name', 'Homepage'),
('default_language', 'de'),
('template', 'default'),
('meta_author', 'Xenux'),
('meta_desc', 'Hier die Beschreibung der Homepage, die in den Meta-Tags angezeigt wird'),
('meta_keys', 'Schlüsselwörter der Homepage, die in den Meta-Tags angezeigt werden'),
('admin_email', 'mail@xenux'),
('users_can_register', 'true'),
('homepage_offline', 'false'),
('HomePage_ID', ''),
('ImprintPage_ID', ''),
('ContactPage_ID', ''),
('sites_show_meta_info', 'true'),
('installed_modules', '[]'),
('installed_templates', '["default","bootstrap"]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%news`
--

DROP TABLE IF EXISTS `%PREFIX%news`;
CREATE TABLE IF NOT EXISTS `%PREFIX%news` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `lastModified_date` timestamp NULL DEFAULT NULL,
  `public` tinyint(4) NOT NULL DEFAULT '0',
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `%PREFIX%news`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%sites`
--

DROP TABLE IF EXISTS `%PREFIX%sites`;
CREATE TABLE IF NOT EXISTS `%PREFIX%sites` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastModified_date` timestamp NULL DEFAULT NULL,
  `parent_id` int(10) NOT NULL,
  `sortindex` int(10) NOT NULL,
  `public` tinyint(1) DEFAULT '1' COMMENT '1=public;0=private',
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `%PREFIX%sites`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%site_contactperson`
--

DROP TABLE IF EXISTS `%PREFIX%site_contactperson`;
CREATE TABLE IF NOT EXISTS `%PREFIX%site_contactperson` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `site_id` int(11) NOT NULL,
  `contactperson_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `%PREFIX%site_contactperson`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%users`
--

DROP TABLE IF EXISTS `%PREFIX%users`;
CREATE TABLE IF NOT EXISTS `%PREFIX%users` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(200) NOT NULL,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `realname_show_profile` tinyint(1) NOT NULL,
  `email` varchar(200) NOT NULL,
  `email_show_profile` tinyint(1) NOT NULL,
  `homepage` text,
  `social_media` text NOT NULL,
  `bio` text,
  `password` varchar(200) NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `role` int(2) NOT NULL DEFAULT '1',
  `lastlogin_date` timestamp NULL DEFAULT NULL,
  `lastlogin_ip` varchar(100) DEFAULT NULL,
  `session_fingerprint` varchar(100) DEFAULT NULL,
  `verifykey` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
