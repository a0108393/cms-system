-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2014 at 10:53 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `marketing_advertising_links`
--

CREATE TABLE IF NOT EXISTS `marketing_advertising_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `marketing_channels_id` int(11) NOT NULL COMMENT 'References marketing_channels table',
  `destination_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'URL to redirect to',
  `generated_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Dynamic Link generated',
  `visits` int(11) NOT NULL DEFAULT '0' COMMENT 'No of times generated URL is visited',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

--
-- Dumping data for table `marketing_advertising_links`
--

INSERT INTO `marketing_advertising_links` (`id`, `description`, `marketing_channels_id`, `destination_url`, `generated_url`, `visits`) VALUES
(6, 'link3', 12, 'http://localhost/crm/marketing/AdvertisingLinks/add', 'aq09j', 3),
(7, 'link 1', 9, 'http://localhost/crm/marketing/AdvertisingLinks/add', 'a6s6a', 3),
(8, 'Link 2', 9, 'http://localhost/crm/marketing/advertising_links/index/', 'tdul3', 1),
(9, 'advertising link index', 9, 'http://localhost/crm/marketing/advertising_links/index/', 'u7rap', 0),
(10, 'Link 4', 9, 'http://localhost/crm/marketing/advertising_links/index/', '58oun', 1),
(11, 'advertising link index', 9, 'http://localhost/crm/marketing/advertising_links/index/', 'grfws', 0),
(12, 'advertising link index', 9, 'http://localhost/crm/marketing/advertising_links/index/', 'xlcpe', 0),
(13, 'advertising link index', 9, 'http://localhost/crm/marketing/AdvertisingLinks/add', '5194s', 0),
(14, 'advertising link index', 9, 'http://localhost/crm/marketing/advertising_links/index/', '7ri4j', 0),
(15, 'advertising link index', 9, 'http://localhost/crm/marketing/advertising_links/index/', 'gqcb2', 0),
(16, 'advertising link index', 9, 'http://localhost/crm/marketing/advertising_links/index/', 'jnopx', 0);

-- --------------------------------------------------------

--
-- Table structure for table `marketing_channels`
--

CREATE TABLE IF NOT EXISTS `marketing_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `marketing_channels`
--

INSERT INTO `marketing_channels` (`id`, `name`) VALUES
(7, 'Namecard'),
(8, 'Adwords'),
(9, 'Google Search '),
(10, 'Advertising'),
(11, 'Directory Listing'),
(12, 'Cobranding'),
(13, 'Social Media'),
(14, 'Affiliates / Referrals');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_events`
--

CREATE TABLE IF NOT EXISTS `marketing_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `marketing_events`
--

INSERT INTO `marketing_events` (`id`, `name`, `start_date`, `end_date`) VALUES
(1, 'asda', '2014-01-13', '2014-01-15'),
(2, 'asda', '2014-01-27', NULL),
(3, 'asd', '2014-01-14', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_link_visits`
--

CREATE TABLE IF NOT EXISTS `marketing_link_visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marketing_advertising_links_id` int(11) NOT NULL,
  `time_click` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `client_ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `marketing_link_visits`
--

INSERT INTO `marketing_link_visits` (`id`, `marketing_advertising_links_id`, `time_click`, `client_ip`) VALUES
(4, 7, '2014-01-13 19:20:16', '127.0.0.1'),
(5, 7, '2014-01-01 17:00:00', '127.0.0.1'),
(6, 7, '2013-12-23 21:22:35', '127.0.0.1'),
(7, 8, '2014-01-14 21:26:46', '127.0.0.1'),
(8, 10, '2014-02-05 16:59:59', '127.0.0.1');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
