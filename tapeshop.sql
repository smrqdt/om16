SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `image` varchar(256) DEFAULT NULL,
  `numbered` tinyint(1) NOT NULL DEFAULT '0',
  `shipping` int(11) NOT NULL,
  `ticketscript` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70 ;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ordertime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `paymenttime` timestamp NULL DEFAULT NULL,
  `shippingtime` timestamp NULL DEFAULT NULL,
  `status` varchar(64) NOT NULL DEFAULT 'new',
  `hashlink` varchar(64) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `shipping` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  KEY `hashlink` (`hashlink`),
  KEY `hashlink_2` (`hashlink`),
  KEY `hashlink_3` (`hashlink`),
  KEY `hashlink_4` (`hashlink`),
  KEY `hashlink_5` (`hashlink`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `size` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;


ALTER TABLE `itemnumbers`
  ADD CONSTRAINT `itemnumbers_ibfk_1` FOREIGN KEY (`orderitem_id`) REFERENCES `orderitems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `itemnumbers_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE;

ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`);

ALTER TABLE `sizes`
  ADD CONSTRAINT `sizes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
