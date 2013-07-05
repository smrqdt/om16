-- phpMyAdmin SQL Dump
-- version 4.0.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 05. Jul 2013 um 16:12
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
-- Tabellenstruktur für Tabelle `addresses`
--

CREATE TABLE IF NOT EXISTS `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(512) NOT NULL,
  `lastname` varchar(512) NOT NULL,
  `street` varchar(512) NOT NULL,
  `building_number` varchar(64) NOT NULL,
  `postcode` varchar(64) NOT NULL,
  `city` varchar(512) NOT NULL,
  `country` varchar(256) NOT NULL,
  `current` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Daten für Tabelle `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `name`, `lastname`, `street`, `building_number`, `postcode`, `city`, `country`, `current`) VALUES
(1, 1, 'Hans ', 'Wurst', 'teststr.', '6', '56142', 'Dortmund', 'Deutschland', 0),
(2, 6, 'Klaus', 'Müller', 'Ingostr.', '5', '14414', 'Darmstadt', 'Deutschland', 1),
(3, 18, 'Inga', 'Schmidt', 'Taunusstr.', '54', '51515', 'Köln', 'Deutschland', 1),
(4, 19, 'Horst', 'Meyer', 'Pommernstr.', '12', '51515', 'Haintchen', 'Deutschland', 1),
(5, 20, 'Klaus', 'Willert', 'Hauptstr.', '18', '51515', 'Frankfurt am Main', 'Deutschland', 1),
(6, 21, 'Monika', 'Meyer', 'Arkaden', '12', '51515', 'Berlin', 'Deutschland', 1),
(7, 22, 'Martin', 'Bürgermeister', 'Duisbergstraße', '4', '60320', 'Frankfurt am Main', 'Deutschland', 1),
(8, 23, 'Robert', 'Kriese', 'Schwalbacher Straße', '12', '65185', 'Wiesbaden', 'Deutschland', 1),
(9, 24, 'Benny', 'Bilderberg', 'Büchnerweg', '12', '51515', 'Hannover', 'Deutschland', 1),
(10, 25, 'Richard', 'Wagner', 'Beethovenallee', '12', '51533', 'Bremen', 'Deutschland', 1),
(11, 27, 'Hans', 'Urmel', 'Inselweg', '524', '98484', 'Trier', 'Deutschland', 1),
(12, 28, 'Tape', 'Shop', '', '0', '0', '', '', 1),
(19, 1, 'Hans ', 'Wurst', 'teststr.', '7', '56142', 'Dortmund', 'Deutschland', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `itemnumbers`
--

CREATE TABLE IF NOT EXISTS `itemnumbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `orderitem_id` int(11) DEFAULT NULL,
  `number` int(11) NOT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '1',
  `free` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`orderitem_id`),
  KEY `orderitem_id` (`orderitem_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `itemnumbers`
--

INSERT INTO `itemnumbers` (`id`, `item_id`, `orderitem_id`, `number`, `valid`, `free`) VALUES
(1, 1, NULL, 1, 0, 0),
(2, 1, NULL, 2, 0, 1),
(5, 1, NULL, 5, 1, 0),
(6, 1, NULL, 6, 1, 0),
(7, 1, NULL, 7, 1, 0),
(8, 1, 29, 8, 1, 1),
(9, 1, NULL, 9, 1, 1),
(10, 1, NULL, 10, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `image` varchar(256) DEFAULT NULL,
  `numbered` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `price`, `image`, `numbered`) VALUES
(1, 'Ticket', 'The Ticket for the amazing event.', 2317, '', 1),
(2, 'T-Shirt', 'A T-Shirt with our amazing logo on it.', 1600, NULL, 0),
(3, 'Sweater', 'The amazing sweater, because you are so cool!', 2500, NULL, 0);

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
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item` (`item_id`),
  KEY `order` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Daten für Tabelle `orderitems`
--

INSERT INTO `orderitems` (`id`, `item_id`, `order_id`, `amount`, `size`, `price`) VALUES
(1, 1, 22, 1, NULL, 2317),
(2, 1, 23, 1, NULL, 2317),
(3, 1, 29, 2, NULL, 2317),
(4, 2, 29, 1, 'M', 1600),
(5, 3, 29, 1, 'L', 2500),
(6, 1, 37, 1, NULL, 2317),
(7, 1, 41, 2, NULL, 2317),
(8, 2, 41, 1, 'S', 1600),
(9, 3, 41, 1, 'L', 2500),
(10, 2, 42, 1, 'L', 1600),
(29, 1, 77, 1, NULL, 2317),
(30, 1, 78, 1, NULL, 2317),
(31, 2, 78, 1, 'M', 1600),
(32, 2, 79, 1, 'S', 1600),
(33, 3, 80, 3, 'L', 2500),
(34, 2, 80, 1, 'S', 1600),
(35, 1, 81, 2, NULL, 2317),
(36, 2, 81, 1, 'S', 1600),
(37, 3, 81, 1, 'L', 2500);

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
  `address_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  KEY `hashlink` (`hashlink`),
  KEY `hashlink_2` (`hashlink`),
  KEY `hashlink_3` (`hashlink`),
  KEY `hashlink_4` (`hashlink`),
  KEY `hashlink_5` (`hashlink`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `number`, `bill`, `ordertime`, `paymenttime`, `shippingtime`, `status`, `hashlink`, `address_id`) VALUES
(22, 1, 'asdf', 'asdf', '2013-06-09', '2013-07-04', '2013-07-04', 'shipped', 'f18be1fb-1f87-4b3a-8c88-cd70dec68e79', NULL),
(23, 1, 'asdf', 'asdf', '2013-06-09', NULL, '2013-07-05', 'shipped', '054b6094-104f-4161-ac9b-eb522d6ed861', 1),
(29, 1, 'asdf', 'asdf', '2013-06-09', '2013-06-10', NULL, 'payed', '63c45ca9-0eb0-4bbc-8015-b810e8a2bad8', NULL),
(37, 1, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', 'af789838-ac49-4c30-898f-acfa2cfcae36', NULL),
(41, 1, 'asdf', 'asdf', '2013-06-09', '2013-06-10', '2013-06-10', 'shipped', '0ff65ac9-d6c2-46b7-b649-726342c86a0e', NULL),
(42, 24, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '497e0752-bcb9-46dd-9c3d-b2d0346b8468', NULL),
(77, 28, 'asdf', 'asdf', '2013-06-09', '2013-07-04', NULL, 'new', '15b89af4-d6bf-4985-a404-9cef2f9aed33', NULL),
(78, 28, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '3d63752b-6793-4706-9df2-a00c6e62ea63', NULL),
(79, 28, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '9e45490d-cc47-4701-86fc-2c37cce81f63', NULL),
(80, 28, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '70c1004b-033c-425f-93b3-785c0f1dbac0', NULL),
(81, 28, 'asdf', 'asdf', '2013-06-09', NULL, NULL, 'new', '1a908d16-2c73-45a9-9ee9-43a96d6218ad', NULL);

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
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `admin`) VALUES
(1, 'test1', '098f6bcd4621d373cade4e832627b4f6', 'test1@example.com', 0),
(6, 'test6', 'pass6', 'test6@example.com', 0),
(18, 'test18', 'pass18', 'test18@example.com', 0),
(19, 'test19', 'pass19', 'test19@example.com', 0),
(20, 'test20', 'pass20', 'test20@example.com', 0),
(21, 'test21', 'pass21', 'test21@example.com', 0),
(22, NULL, '805748af1f06e479075be15c56bc7f73', 'rokr42@gmail.com', 0),
(23, NULL, '805748af1f06e479075be15c56bc7f73', 'rokr42@gmail.com', 0),
(24, NULL, '805748af1f06e479075be15c56bc7f73', 'rokr42@gmail.com', 0),
(25, NULL, 'a780be915203eb4cf326bece37f9b37f', 'rokr@example.com', 0),
(27, NULL, 'fc640f7fdfac30d9f91a5b29463ac35d', 'fhjslkd', 0),
(28, 'tape', '$2a$12$rJkRgQhBHo6YTn4f7t8rG.T7NldB7clruytsYjt8nG1ByWbZeO0T6', '', 1);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `itemnumbers`
--
ALTER TABLE `itemnumbers`
  ADD CONSTRAINT `itemnumbers_ibfk_1` FOREIGN KEY (`orderitem_id`) REFERENCES `orderitems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `itemnumbers_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `sizes`
--
ALTER TABLE `sizes`
  ADD CONSTRAINT `sizes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
