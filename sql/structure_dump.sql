-- phpMyAdmin SQL Dump
-- Last Modified: 2017-09-01 23:07

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
-- Tabellenstruktur für Tabelle `%PREFIX%events`
--

DROP TABLE IF EXISTS `%PREFIX%events`;
CREATE TABLE IF NOT EXISTS `%PREFIX%events` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` text NOT NULL,
  `text` text NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%files`
--

DROP TABLE IF EXISTS `%PREFIX%files`;
CREATE TABLE IF NOT EXISTS `%PREFIX%files` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(50) NOT NULL,
  `mime_type` varchar(200) DEFAULT NULL,
  `file_extension` varchar(300) DEFAULT NULL,
  `data` longblob,
  `filename` varchar(200) DEFAULT NULL,
  `size` bigint(20) NOT NULL COMMENT 'size in byte',
  `lastModified` timestamp NULL DEFAULT NULL,
  `parent_folder_id` int(10) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%posts`
--

DROP TABLE IF EXISTS `%PREFIX%posts`;
CREATE TABLE IF NOT EXISTS `%PREFIX%posts` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` text NOT NULL,
  `text` text NOT NULL,
  `thumbnail_id` int(11) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `lastModified_date` timestamp NULL DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `%PREFIX%sites`
--

DROP TABLE IF EXISTS `%PREFIX%sites`;
CREATE TABLE IF NOT EXISTS `%PREFIX%sites` (
`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` text NOT NULL,
  `text` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastModified_date` timestamp NULL DEFAULT NULL,
  `parent_id` int(10) NOT NULL,
  `sortindex` int(10) NOT NULL,
  `public` tinyint(1) DEFAULT '0' COMMENT '1=public;0=private',
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
