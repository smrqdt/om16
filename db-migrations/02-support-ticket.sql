ALTER TABLE  `items` ADD  `support_ticket` BOOLEAN NOT NULL DEFAULT FALSE ;

ALTER TABLE  `orderitems` ADD  `support_price` int(11) NOT NULL DEFAULT 0;
