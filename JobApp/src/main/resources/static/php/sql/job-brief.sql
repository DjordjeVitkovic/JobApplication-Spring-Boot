CREATE TABLE IF NOT EXISTS `job_brief` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jobNumber` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isDraft` tinyint(1) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `userid` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jobNumber` (`jobNumber`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `job_brief_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jobID` varchar(255) NOT NULL,
  `fieldName` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `job_id` (`jobID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;