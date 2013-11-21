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
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `max_file_size` int(10) unsigned NOT NULL,
  `images_per_post` int(2) unsigned NOT NULL,
  `thumb_width` int(3) unsigned NOT NULL,
  `pages` int(2) unsigned NOT NULL,
  `threads_per_page` int(2) unsigned NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица trillium_development.posts
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread` int(10) unsigned NOT NULL,
  `board` varchar(10) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `sage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ip` int(10) NOT NULL,
  `user_agent` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица trillium_development.threads
CREATE TABLE IF NOT EXISTS `threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board` varchar(10) NOT NULL,
  `theme` varchar(200) NOT NULL,
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `bump` int(10) unsigned NOT NULL DEFAULT '0',
  `op` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица trillium_development.users
CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(32) NOT NULL,
  `roles` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
