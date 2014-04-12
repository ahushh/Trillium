CREATE TABLE `users` (
  `username`      VARCHAR(255)     NOT NULL,
  `password`      VARCHAR(255)     NOT NULL,
  `last_activity` INT(10) UNSIGNED NOT NULL,
  `roles`         VARCHAR(255)     NOT NULL,
  `salt`          VARCHAR(255)     NOT NULL,
  PRIMARY KEY (`username`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;
INSERT INTO `users` VALUES
  ('root', 'zKgdNE7BHguhCKv+42U0WnRCbF8DgMJRQCi2aqzk3vMGfP0ZNIIes6SK+aE6cZtlVm4rEKfY4earvqcNGIMuSA==', 0,
   '[\"ROLE_ROOT\"]', '');
CREATE TABLE `boards` (
  `name`    VARCHAR(10)  NOT NULL,
  `summary` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`name`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;
CREATE TABLE `threads` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `board` VARCHAR(10)      NOT NULL,
  `title` VARCHAR(30)      NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`board`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;
CREATE TABLE `posts` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `thread`  INT(10) UNSIGNED NOT NULL,
  `board`   VARCHAR(10)      NOT NULL,
  `message` TEXT             NOT NULL,
  `time`    INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`thread`),
  KEY (`board`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;