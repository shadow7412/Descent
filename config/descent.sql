-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 29, 2012 at 12:04 AM
-- Server version: 5.5.20
-- PHP Version: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `descent`
--
CREATE DATABASE `descent` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `descent`;

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
CREATE TABLE IF NOT EXISTS `campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `overlord` varchar(30) NOT NULL COMMENT 'Name of overlord',
  `created` timestamp NULL DEFAULT NULL COMMENT 'Date campaign was created',
  `played` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date campaign was last played (updated)',
  `state` varchar(9999) NOT NULL COMMENT 'JSON string showing current state of campaign',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Boolean value saying whether game should appear on initial selection screen',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID for manipulating entries',
  `campaign` int(11) NOT NULL COMMENT 'Campaign ID - see campaign table',
  `actionid` int(11) NOT NULL COMMENT 'Action ID - for tracking how many actions have happened in this campaign - and sorting them in order',
  `week` int(11) NOT NULL COMMENT 'What week did this event occur in?',
  `summary` varchar(35) NOT NULL COMMENT 'A textual summary of what happened.',
  `player` int(11) NOT NULL COMMENT 'Gained conquest by players',
  `gold` int(11) NOT NULL COMMENT 'Gained Gold by players',
  `overlord` int(11) NOT NULL COMMENT 'Gained conquest by overlord',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
