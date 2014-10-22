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
