-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2014 at 10:11 AM
-- Server version: 5.5.34
-- PHP Version: 5.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `crm_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `module_permissions`
--

CREATE TABLE IF NOT EXISTS `module_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `aro_id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'group',
  `_read` tinyint(4) DEFAULT NULL,
  `_update` tinyint(4) DEFAULT NULL,
  `_delete` tinyint(4) DEFAULT NULL,
  `_create` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_id` (`module_id`,`aro_id`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `module_permissions`
--

INSERT INTO `module_permissions` (`id`, `module_id`, `aro_id`, `type`, `_read`, `_update`, `_delete`, `_create`) VALUES
(1, 30, 38, 'user', 1, NULL, NULL, NULL),
(2, 30, 11, 'group', 1, 1, 0, 1),
(3, 30, 39, 'user', 0, 0, 0, 1),
(4, 31, 38, 'user', 1, 1, 1, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
