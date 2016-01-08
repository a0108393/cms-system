-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2014 at 02:55 AM
-- Server version: 5.5.34
-- PHP Version: 5.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `per`
--

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `foreign_key` int(10) unsigned DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `foreign_key` int(10) unsigned DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) unsigned NOT NULL,
  `aco_id` int(10) unsigned NOT NULL,
  `_create` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `_read` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `_update` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `_delete` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `created`, `modified`) VALUES
(1, 'admin', '2014-01-06 00:00:00', '2014-01-06 00:00:00'),
(2, 'mod', '2014-01-06 00:00:00', '2014-01-06 00:00:00'),
(3, 'user', '2014-01-06 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default_owner_bit` int(3) unsigned zerofill NOT NULL DEFAULT '000',
  `default_group_bit` int(3) unsigned zerofill NOT NULL DEFAULT '000',
  `default_public_bit` int(3) unsigned zerofill NOT NULL DEFAULT '000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `default_owner_bit`, `default_group_bit`, `default_public_bit`) VALUES
(1, 'Client', 448, 048, 004),
(2, 'Contact', 448, 048, 004);

-- --------------------------------------------------------

--
-- Table structure for table `module_permissions`
--

CREATE TABLE IF NOT EXISTS `module_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `_read` tinyint(4) NOT NULL,
  `_updae` tinyint(4) NOT NULL,
  `_delete` tinyint(4) NOT NULL,
  `_create` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_id` (`module_id`,`group_id`),
  UNIQUE KEY `module_id_2` (`module_id`,`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `module_permissions`
--

INSERT INTO `module_permissions` (`id`, `module_id`, `group_id`, `_read`, `_updae`, `_delete`, `_create`) VALUES
(1, 1, 1, 2, 2, 2, 2),
(2, 1, 2, 2, 1, 0, 2),
(3, 1, 3, 1, 0, 0, 0),
(4, 2, 1, 2, 2, 2, 2),
(5, 2, 2, 1, 1, 0, 2),
(6, 2, 3, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `body`, `created`, `modified`) VALUES
(1, 1, '1 admin', '1 admin', '2014-01-08 07:20:37', '2014-01-08 07:20:37'),
(2, 1, '2 admin', '2 admin', '2014-01-08 07:20:48', '2014-01-08 07:20:48'),
(4, 2, '4 mod', '4 mod', '2014-01-08 07:22:16', '2014-01-08 07:22:16'),
(5, 2, '5 mod', '5 mod', '2014-01-08 10:38:27', '2014-01-08 10:38:27');

-- --------------------------------------------------------

--
-- Table structure for table `row_permssions`
--

CREATE TABLE IF NOT EXISTS `row_permssions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `foreign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `perms` int(3) unsigned zerofill NOT NULL DEFAULT '000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `row_permssions`
--

INSERT INTO `row_permssions` (`id`, `module_id`, `foreign_id`, `user_id`, `group_id`, `perms`) VALUES
(1, 1, 1, 1, 1, 496),
(2, 1, 2, 1, 1, 500),
(3, 1, 3, 1, 1, 500),
(4, 1, 4, 2, 2, 500),
(5, 1, 5, 2, 2, 500);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `group_id`, `created`, `modified`) VALUES
(1, 'admin', '11024102c1e6227bb0000edbaac2be41550bd42a', 1, '2014-01-06 11:35:57', '2014-01-06 11:35:57'),
(2, 'phamtruong', '11024102c1e6227bb0000edbaac2be41550bd42a', 2, '2014-01-08 04:22:40', '2014-01-08 04:22:40'),
(3, 'tamao', '11024102c1e6227bb0000edbaac2be41550bd42a', 3, '2014-01-08 04:23:02', '2014-01-08 04:23:02');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
