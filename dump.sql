DROP DATABASE IF EXISTS `trillium_production`;
DROP DATABASE IF EXISTS `trillium_development`;

CREATE DATABASE `trillium_production`;
USE `trillium_production`;
CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  `roles` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE DATABASE `trillium_development`;
USE `trillium_development`;
CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  `roles` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
