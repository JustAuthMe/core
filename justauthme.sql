-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : mamp_mysql:3306
-- Généré le :  jeu. 19 déc. 2019 à 16:22
-- Version du serveur :  8.0.18
-- Version de PHP :  7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `justauthme`
--
CREATE DATABASE IF NOT EXISTS `justauthme` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `justauthme`;

-- --------------------------------------------------------

--
-- Structure de la table `banned_ip`
--

DROP TABLE IF EXISTS `banned_ip`;
CREATE TABLE IF NOT EXISTS `banned_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `client_app`
--

DROP TABLE IF EXISTS `client_app`;
CREATE TABLE IF NOT EXISTS `client_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `app_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(1023) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `redirect_url` varchar(1023) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `public_key` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `hash_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `client_app`
--

INSERT INTO `client_app` (`id`, `domain`, `app_id`, `name`, `logo`, `redirect_url`, `data`, `public_key`, `secret`, `hash_key`) VALUES
(1, 'localhost', 'demo', 'Démo', '', 'http://localhost/JustAuth.Me/demo/login', '[\"email\"]', '', 'iknowyoumasturbateinfrontofyourcamera', 'ArQ8pPOt3YtPKDCMvncvsSl8TAUt5qXyGyAvXf4qMIp863mLOU2sdas/v2YyNTAv');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uniqid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `public_key` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqid` (`uniqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
CREATE TABLE IF NOT EXISTS `user_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `client_app_id` int(11) DEFAULT NULL,
  `callback_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_app_id` (`client_app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `user_auth`
--

INSERT INTO `user_auth` (`id`, `token`, `client_app_id`, `callback_url`, `data`, `timestamp`, `ip_address`) VALUES
(10, 'mZGDEHCGecWhaMK4K92Tsry0wrF3_7ft3Bbq_L1gg765OKBh1CsyY3EoVRT2CQgp', 1, 'http://localhost/JustAuth.Me/demo/login', '[\"email!\"]', 1576695452, '172.30.0.1');

-- --------------------------------------------------------

--
-- Structure de la table `user_spam`
--

DROP TABLE IF EXISTS `user_spam`;
CREATE TABLE IF NOT EXISTS `user_spam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user_auth`
--
ALTER TABLE `user_auth`
  ADD CONSTRAINT `user_auth_ibfk_1` FOREIGN KEY (`client_app_id`) REFERENCES `client_app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_spam`
--
ALTER TABLE `user_spam`
  ADD CONSTRAINT `user_spam_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
