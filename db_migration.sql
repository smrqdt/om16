ALTER TABLE `sizes` ADD `stock` INT NOT NULL DEFAULT '0';
ALTER TABLE `items` ADD `stock` INT NOT NULL DEFAULT '0';

ALTER TABLE  `items` ADD  `manage_stock` BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE  `sizes` ADD  `size_id` INT NULL ;
ALTER TABLE  `orderitems` ADD  `size_id` INT NULL ;
ALTER TABLE  `orderitems` ADD INDEX (  `size_id` ) ;
ALTER TABLE  `orderitems` ADD FOREIGN KEY (  `size_id` ) REFERENCES  `tapeshop`.`sizes` (
  `id`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;

UPDATE orderitems t1 SET t1.size_id = (
  SELECT id
  FROM sizes t2
  WHERE t2.item_id = t1.item_id
        AND t2.size = t1.size
);

ALTER TABLE  `orderitems` DROP  `size` ;

ALTER TABLE  `sizes` ADD  `deleted` BOOLEAN NOT NULL DEFAULT FALSE ;

ALTER TABLE  `itemnumbers` DROP  `free` ;

ALTER TABLE  `items` ADD  `shownumbers` BOOLEAN NOT NULL DEFAULT TRUE ;

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `fix` int(5) NOT NULL,
  `fee` float(5,3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `fix`, `fee`) VALUES
  (1, 'paypal', 35, 0.019),
  (2, 'sofort', 25, 0.009),
  (3, 'prepay', 0, 0.000);

ALTER TABLE `orders` ADD `payment_method_id` INT NULL;

ALTER TABLE  `orders` ADD FOREIGN KEY (  `payment_method_id` ) REFERENCES  `tapeshop`.`payment_methods` (
  `id`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE `orders` ADD `payment_id` VARCHAR(64) NULL;
ALTER TABLE `orders` ADD `payment_fee` INT NOT NULL DEFAULT '0';
ALTER TABLE `orders` ADD `payment_status` VARCHAR(32) NULL;
