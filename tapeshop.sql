-- phpMyAdmin SQL Dump
-- version 4.0.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 12. Jun 2013 um 22:29
-- Server Version: 5.6.10
-- PHP-Version: 5.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `tapeshop`
--
CREATE DATABASE IF NOT EXISTS `tapeshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tapeshop`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `price`, `image`) VALUES
(1, 'Ticket', 'The Ticket for the amazing event.', 2317, NULL),
(2, 'T-Shirt', 'A T-Shirt with our amazing logo on it.', 1500, NULL),
(3, 'Sweater', 'The amazing sweater, because you are so cool!', 2500, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orderitems`
--

CREATE TABLE IF NOT EXISTS `orderitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `size` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item` (`item_id`),
  KEY `order` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Daten für Tabelle `orderitems`
--

INSERT INTO `orderitems` (`id`, `item_id`, `order_id`, `amount`, `size`) VALUES
(1, 1, 22, 1, NULL),
(2, 1, 23, 1, NULL),
(3, 1, 29, 2, NULL),
(4, 2, 29, 1, 'M'),
(5, 3, 29, 1, 'L'),
(6, 1, 37, 1, NULL),
(7, 1, 41, 2, NULL),
(8, 2, 41, 1, 'S'),
(9, 3, 41, 1, 'L'),
(10, 2, 42, 1, 'L'),
(11, 1, 43, 1, NULL),
(12, 2, 43, 1, 'S'),
(13, 3, 43, 1, 'L');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `number` varchar(128) NOT NULL,
  `bill` varchar(128) NOT NULL,
  `ordertime` date DEFAULT NULL,
  `paymenttime` date DEFAULT NULL,
  `shippingtime` date DEFAULT NULL,
  `status` varchar(64) NOT NULL DEFAULT 'new',
  `hashlink` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  KEY `hashlink` (`hashlink`),
  KEY `hashlink_2` (`hashlink`),
  KEY `hashlink_3` (`hashlink`),
  KEY `hashlink_4` (`hashlink`),
  KEY `hashlink_5` (`hashlink`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `number`, `bill`, `ordertime`, `paymenttime`, `shippingtime`, `status`, `hashlink`) VALUES
(22, 1, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', 'f18be1fb-1f87-4b3a-8c88-cd70dec68e79'),
(23, 1, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '054b6094-104f-4161-ac9b-eb522d6ed861'),
(29, 1, 'asdf', 'asdf', '2013-06-09', '2013-06-10', NULL, 'payed', '63c45ca9-0eb0-4bbc-8015-b810e8a2bad8'),
(37, 1, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', 'af789838-ac49-4c30-898f-acfa2cfcae36'),
(41, 1, 'asdf', 'asdf', '2013-06-09', '2013-06-10', '2013-06-10', 'shipped', '0ff65ac9-d6c2-46b7-b649-726342c86a0e'),
(42, 24, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '497e0752-bcb9-46dd-9c3d-b2d0346b8468'),
(43, 27, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '64b66406-4aa0-4267-a654-6d2d97afbbd9');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sizes`
--

CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `size` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `sizes`
--

INSERT INTO `sizes` (`id`, `item_id`, `size`) VALUES
(1, 2, 'S'),
(2, 2, 'M'),
(3, 2, 'L'),
(4, 3, 'L'),
(5, 3, 'XL');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `name` varchar(512) NOT NULL,
  `lastname` varchar(512) NOT NULL,
  `street` varchar(512) NOT NULL,
  `street_number` int(5) NOT NULL,
  `plz` int(5) NOT NULL,
  `city` varchar(512) NOT NULL,
  `country` varchar(256) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `name`, `lastname`, `street`, `street_number`, `plz`, `city`, `country`, `admin`) VALUES
(1, 'test1', '098f6bcd4621d373cade4e832627b4f6', 'test1@example.com', 'Hans ', 'Wurst', 'teststr.', 5, 56142, 'Dortmund', 'Deutschland', 0),
(6, 'test6', 'pass6', 'test6@example.com', 'Klaus', 'Müller', 'Ingostr.', 5, 14414, 'Darmstadt', 'Deutschland', 0),
(18, 'test18', 'pass18', 'test18@example.com', 'Inga', 'Schmidt', 'Taunusstr.', 54, 51515, 'Köln', 'Deutschland', 0),
(19, 'test19', 'pass19', 'test19@example.com', 'Horst', 'Meyer', 'Pommernstr.', 12, 51515, 'Haintchen', 'Deutschland', 0),
(20, 'test20', 'pass20', 'test20@example.com', 'Klaus', 'Willert', 'Hauptstr.', 18, 51515, 'Frankfurt am Main', 'Deutschland', 0),
(21, 'test21', 'pass21', 'test21@example.com', 'Monika', 'Meyer', 'Arkaden', 12, 51515, 'Berlin', 'Deutschland', 0),
(22, NULL, '805748af1f06e479075be15c56bc7f73', 'rokr42@gmail.com', 'Martin', 'Bürgermeister', 'Duisbergstraße', 4, 60320, 'Frankfurt am Main', 'Deutschland', 0),
(23, NULL, '805748af1f06e479075be15c56bc7f73', 'rokr42@gmail.com', 'Robert', 'Kriese', 'Schwalbacher Straße', 12, 65185, 'Wiesbaden', 'Deutschland', 0),
(24, NULL, '805748af1f06e479075be15c56bc7f73', 'rokr42@gmail.com', 'Benny', 'Bilderberg', 'Büchnerweg', 12, 51515, 'Hannover', 'Deutschland', 0),
(25, NULL, 'a780be915203eb4cf326bece37f9b37f', 'rokr@example.com', 'Richard', 'Wagner', 'Beethovenallee', 12, 51533, 'Bremen', 'Deutschland', 0),
(26, NULL, 'bff149a0b87f5b0e00d9dd364e9ddaa0', 'asd', 'Klaus', 'Kinski', 'Draculaweg', 18, 51593, 'München', 'Deutschland', 0),
(27, NULL, 'fc640f7fdfac30d9f91a5b29463ac35d', 'fhjslkd', 'Hans', 'Urmel', 'Inselweg', 524, 98484, 'Trier', 'Deutschland', 0),
(28, 'tape', '098f6bcd4621d373cade4e832627b4f6', '', '', '', '', 0, 0, '', '', 1);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `sizes`
--
ALTER TABLE `sizes`
  ADD CONSTRAINT `sizes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
