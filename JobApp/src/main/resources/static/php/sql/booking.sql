CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contactPhone` varchar(255) DEFAULT NULL,
  `adults` int(11) NOT NULL,
  `children` int(11) DEFAULT NULL,
  `checkInDate` date NOT NULL,
  `checkOutDate` date NOT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;