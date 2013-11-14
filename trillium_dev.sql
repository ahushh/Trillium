-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.6.14-log - MySQL Community Server (GPL)
-- ОС Сервера:                   Win64
-- HeidiSQL Версия:              8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных trillium_development
CREATE DATABASE IF NOT EXISTS `trillium_development` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `trillium_development`;


-- Дамп структуры для таблица trillium_development.boards
CREATE TABLE IF NOT EXISTS `boards` (
  `name` varchar(10) NOT NULL,
  `summary` varchar(200) NOT NULL,
  `max_file_size` int(10) unsigned NOT NULL,
  `images_per_post` int(2) unsigned NOT NULL,
  `thumb_width` int(3) unsigned NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Дамп данных таблицы trillium_development.boards: 1 rows
/*!40000 ALTER TABLE `boards` DISABLE KEYS */;
INSERT INTO `boards` (`name`, `summary`, `max_file_size`, `images_per_post`, `thumb_width`) VALUES
	('b', 'Random', 10485760, 1, 320);
/*!40000 ALTER TABLE `boards` ENABLE KEYS */;


-- Дамп структуры для таблица trillium_development.images
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board` varchar(10) NOT NULL,
  `thread` int(10) unsigned NOT NULL,
  `post` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `ext` varchar(3) NOT NULL,
  `width` int(5) NOT NULL,
  `height` int(5) NOT NULL,
  `size` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы trillium_development.images: 1 rows
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` (`id`, `board`, `thread`, `post`, `name`, `ext`, `width`, `height`, `size`) VALUES
	(2, 'b', 6, 15, '46adfdbc900c3b62fe9ba3861e8adda4', 'jpg', 1312, 2000, 1220080),
	(3, 'b', 6, 16, 'a865e4e8ba604a4cc98effba9dd2ae21', 'jpg', 2560, 1600, 1168794),
	(4, 'b', 8, 21, '5febb56ec5ddaf64dd7eee29dd3ff250', 'jpg', 900, 600, 260757);
/*!40000 ALTER TABLE `images` ENABLE KEYS */;


-- Дамп структуры для таблица trillium_development.posts
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread` int(10) unsigned NOT NULL,
  `board` varchar(10) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `ip` int(10) NOT NULL,
  `user_agent` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы trillium_development.posts: 12 rows
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` (`id`, `thread`, `board`, `time`, `text`, `ip`, `user_agent`) VALUES
	(6, 6, 'b', 1384267233, 'sfdsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(5, 6, 'b', 1384258518, 'Message \r\n\r\nololo\r\nbr\r\nbr', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(4, 5, 'b', 1383922766, 'Message', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(7, 6, 'b', 1384267244, 'sfdsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(8, 6, 'b', 1384353242, 'sdfsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(9, 6, 'b', 1384353484, 'sdfsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(10, 6, 'b', 1384353506, 'sdfsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(11, 6, 'b', 1384353597, 'sdfsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(12, 6, 'b', 1384353622, 'sdfsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(13, 6, 'b', 1384353713, 'sdfsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(14, 6, 'b', 1384353772, 'sdfsdfsdf', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(15, 6, 'b', 1384357359, 'Ololo', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(16, 6, 'b', 1384438783, 'ololo', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(17, 6, 'b', 1384445182, 'ololo', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(18, 6, 'b', 1384445208, 'ololo', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(19, 7, 'b', 1384445283, 'new thread', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(20, 6, 'b', 1384445292, 'new message', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14'),
	(21, 8, 'b', 1384445417, 'Ololo', 2130706433, 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;


-- Дамп структуры для таблица trillium_development.threads
CREATE TABLE IF NOT EXISTS `threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board` varchar(10) NOT NULL,
  `theme` varchar(200) NOT NULL,
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `bump` int(10) unsigned NOT NULL DEFAULT '0',
  `op` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы trillium_development.threads: 2 rows
/*!40000 ALTER TABLE `threads` DISABLE KEYS */;
INSERT INTO `threads` (`id`, `board`, `theme`, `created`, `bump`, `op`) VALUES
	(5, 'b', 'Theme', 1383922766, 1383922766, 4),
	(6, 'b', 'Test thread', 1384258518, 1384445292, 18),
	(7, 'b', 'Theme of the new thread', 1384445283, 1384445283, 19),
	(8, 'b', 'Test create thread with picture', 1384445417, 1384445417, 21);
/*!40000 ALTER TABLE `threads` ENABLE KEYS */;


-- Дамп структуры для таблица trillium_development.users
CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(32) NOT NULL,
  `roles` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Дамп данных таблицы trillium_development.users: 2 rows
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`username`, `roles`, `password`) VALUES
	('admin', 'ROLE_ROOT', 'zKgdNE7BHguhCKv+42U0WnRCbF8DgMJRQCi2aqzk3vMGfP0ZNIIes6SK+aE6cZtlVm4rEKfY4earvqcNGIMuSA=='),
	('User', 'ROLE_USER', 'FxCHSNkTGKeznEVR5Vp0O7lxobtkZkMg3aw1wEeZHUcNYNLYRxAs2QPP3L4vxfXOxc7sssqL4asHwObeQM0cYA==');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
