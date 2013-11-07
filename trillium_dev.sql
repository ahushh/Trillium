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
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Дамп данных таблицы trillium_development.boards: 5 rows
/*!40000 ALTER TABLE `boards` DISABLE KEYS */;
INSERT INTO `boards` (`name`, `summary`) VALUES
	('b', 'Random'),
	('mu', 'Music'),
	('pr', 'Programming'),
	('t', 'Test'),
	('d', 'Discuss');
/*!40000 ALTER TABLE `boards` ENABLE KEYS */;


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
