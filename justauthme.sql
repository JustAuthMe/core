-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 15, 2019 at 11:28 AM
-- Server version: 10.1.37-MariaDB-0+deb9u1
-- PHP Version: 7.3.3-1+0~20190307202245.32+stretch~1.gbp32ebb2

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

-- --------------------------------------------------------

--
-- Table structure for table `banned_ip`
--

CREATE TABLE `banned_ip` (
                             `id` int(11) NOT NULL,
                             `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                             `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_app`
--

CREATE TABLE `client_app` (
                              `id` int(11) NOT NULL,
                              `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                              `app_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                              `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                              `logo` varchar(1023) COLLATE utf8_unicode_ci NOT NULL,
                              `redirect_url` varchar(1023) COLLATE utf8_unicode_ci NOT NULL,
                              `data` text COLLATE utf8_unicode_ci NOT NULL,
                              `public_key` text COLLATE utf8_unicode_ci NOT NULL,
                              `secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
                        `id` int(11) NOT NULL,
                        `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                        `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                        `public_key` text COLLATE utf8_unicode_ci NOT NULL,
                        `hash_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

CREATE TABLE `user_auth` (
                             `id` int(11) NOT NULL,
                             `token` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
                             `client_app_id` int(11) DEFAULT NULL,
                             `callback_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                             `data` text COLLATE utf8_unicode_ci NOT NULL,
                             `timestamp` bigint(20) NOT NULL,
                             `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_spam`
--

CREATE TABLE `user_spam` (
                             `id` int(11) NOT NULL,
                             `user_id` int(11) DEFAULT NULL,
                             `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banned_ip`
--
ALTER TABLE `banned_ip`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- Indexes for table `client_app`
--
ALTER TABLE `client_app`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
    ADD PRIMARY KEY (`id`),
    ADD KEY `client_app_id` (`client_app_id`);

--
-- Indexes for table `user_spam`
--
ALTER TABLE `user_spam`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `ip_address` (`ip_address`),
    ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banned_ip`
--
ALTER TABLE `banned_ip`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_app`
--
ALTER TABLE `client_app`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_auth`
--
ALTER TABLE `user_auth`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_spam`
--
ALTER TABLE `user_spam`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_auth`
--
ALTER TABLE `user_auth`
    ADD CONSTRAINT `user_auth_ibfk_1` FOREIGN KEY (`client_app_id`) REFERENCES `client_app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_spam`
--
ALTER TABLE `user_spam`
    ADD CONSTRAINT `user_spam_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
