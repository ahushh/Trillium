CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  `roles` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `users` VALUES ('root','zKgdNE7BHguhCKv+42U0WnRCbF8DgMJRQCi2aqzk3vMGfP0ZNIIes6SK+aE6cZtlVm4rEKfY4earvqcNGIMuSA==',0,'[\"ROLE_ROOT\"]','');
CREATE TABLE `boards` (
  `name` varchar(10) NOT NULL,
  `summary` varchar(100) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board` varchar(10) NOT NULL,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`board`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread` int(10) unsigned NOT NULL,
  `board` varchar(10) NOT NULL,
  `message` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`thread`),
  KEY (`board`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;