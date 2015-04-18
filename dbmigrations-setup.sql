CREATE TABLE IF NOT EXISTS `dbmigrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(512) NOT NULL,
  `md5hash` varchar(80) NOT NULL,
  `time_applied` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

