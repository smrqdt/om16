ALTER TABLE `orders` ADD(
`payment_method_id` int(11) NOT NULL,
`payment_id` varchar(64) NULL DEFAULT NULL,
`payment_fee` int(11) NOT NULL DEFAULT 0,
`payment_status` varchar( 32 ) NULL DEFAULT NULL);

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `fix` int(5) NOT NULL,
  `fee` float(5,3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

INSERT INTO `payment_methods` (`id`, `name`, `fix`, `fee`) VALUES
(1, 'paypal', 35, 0.019),
(2, 'sofort', 25, 0.009),
(3, 'prepay', 0, 0.000);
