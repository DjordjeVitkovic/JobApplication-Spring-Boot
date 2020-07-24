CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
  `department` varchar(255) DEFAULT NULL,
  `productQuality` int(11) NOT NULL,
  `serviceQuality` int(11) NOT NULL,
  `supportQuality` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;