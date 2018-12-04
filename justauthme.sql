-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 04, 2018 at 10:40 AM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.2.12-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `justauthme`
--
CREATE DATABASE IF NOT EXISTS `justauthme` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `justauthme`;

-- --------------------------------------------------------

--
-- Table structure for table `client_app`
--

DROP TABLE IF EXISTS `client_app`;
CREATE TABLE IF NOT EXISTS `client_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pubkey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_url` varchar(1023) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `client_app`
--

INSERT INTO `client_app` (`id`, `domain`, `pubkey`, `redirect_url`) VALUES
(1, 'phpeter.fr', 'PHPeter', 'https://phpeter.fr/callback_url_jam');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `timestamp`, `ip_address`) VALUES
(1, 'f7cc1a9841de219e4e2c3284', '2018-11-29 21:28:48', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
CREATE TABLE IF NOT EXISTS `user_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `client_app_id` int(11) DEFAULT NULL,
  `callback_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_app_id` (`client_app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_auth`
--

INSERT INTO `user_auth` (`id`, `token`, `client_app_id`, `callback_url`, `data`, `timestamp`, `ip_address`) VALUES
(7, 'XJIVMdhe_8dShRXvsGjKy7ymufvIoGDaN6yFCpFC1zbGTOJtnk5CVsiCOxk8dpZ+', 1, 'https://phpeter.fr/callback_url_jam', '[\"username\",\"email\"]', '2018-12-02 10:32:55', '192.168.1.254');

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

DROP TABLE IF EXISTS `user_login`;
CREATE TABLE IF NOT EXISTS `user_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id` int(11) NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `auth_id` (`auth_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_spam`
--

DROP TABLE IF EXISTS `user_spam`;
CREATE TABLE IF NOT EXISTS `user_spam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_spam`
--

INSERT INTO `user_spam` (`id`, `user_id`, `timestamp`, `ip_address`) VALUES
(15, 1, '2018-11-29 21:28:48', '127.0.0.1');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD CONSTRAINT `user_auth_ibfk_1` FOREIGN KEY (`client_app_id`) REFERENCES `client_app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_login`
--
ALTER TABLE `user_login`
  ADD CONSTRAINT `user_login_ibfk_1` FOREIGN KEY (`auth_id`) REFERENCES `user_auth` (`id`);

--
-- Constraints for table `user_spam`
--
ALTER TABLE `user_spam`
  ADD CONSTRAINT `user_spam_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
