CREATE TABLE IF NOT EXISTS `university_admission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dateBirth` date NOT NULL,
  `father` varchar(255) NOT NULL,
  `mother` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contactPhone` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `postalCode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `schoolName` varchar(255) NOT NULL,
  `schoolPassingYear` varchar(4) NOT NULL,
  `schoolGrade` varchar(255) NOT NULL,
  `schoolMarksheet` varchar(255) NOT NULL,
  `insituteName` varchar(255) DEFAULT NULL,
  `insitutePassingYear` varchar(4) DEFAULT NULL,
  `insituteGrade` varchar(255) DEFAULT NULL,
  `insituteMarksheet` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;