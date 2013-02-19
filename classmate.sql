-- phpMyAdmin SQL Dump
-- version 3.2.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 26, 2010 at 09:39 AM
-- Server version: 5.1.32
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `classmate`
--

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_evaluation`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_evaluation` (
  `evaluationId` int(11) NOT NULL AUTO_INCREMENT,
  `eventId` int(11) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`evaluationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_evaluation`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_evaluation_user`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_evaluation_user` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventId`,`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_evaluation_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event` (
  `eventId` int(11) NOT NULL AUTO_INCREMENT,
  `workshopId` int(11) NOT NULL DEFAULT '0',
  `locationId` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `startTime` time NOT NULL DEFAULT '00:00:00',
  `endTime` time NOT NULL DEFAULT '00:00:00',
  `maxSize` int(11) NOT NULL DEFAULT '0',
  `minSize` int(11) NOT NULL DEFAULT '0',
  `roleSize` int(10) unsigned NOT NULL DEFAULT '0',
  `waitlistSize` int(11) NOT NULL DEFAULT '0',
  `waitlistTotal` int(11) NOT NULL DEFAULT '0',
  `totalAttendance` int(11) NOT NULL DEFAULT '0',
  `status` enum('open','closed','canceled') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_event`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event_attendee`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event_attendee` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('attending','waitlist','canceled') NOT NULL DEFAULT 'attending',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `attended` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventId`,`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_event_attendee`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event_instructor`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event_instructor` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `accountId` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `eventId` (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_event_instructor`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event_restriction`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event_restriction` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `realm` varchar(64) NOT NULL DEFAULT '',
  `users` longtext NOT NULL,
  PRIMARY KEY (`eventId`,`realm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_event_restriction`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_location`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_location` (
  `locationId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `description` mediumtext NOT NULL,
  `address` varchar(255) NOT NULL DEFAULT '',
  `capacity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`locationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_location`
--

--
-- Table structure for table `oit_tbl_search_term`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_search_term` (
  `term` varchar(255) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `last` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_search_term`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_tag`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_tag` (
  `tagId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`tagId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_tag`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_tag_map`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_tag_map` (
  `attributeName` varchar(64) NOT NULL DEFAULT '',
  `attributeId` varchar(64) NOT NULL DEFAULT '',
  `tagId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`attributeName`,`attributeId`,`tagId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_tag_map`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop` (
  `workshopId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `prerequisites` mediumtext NOT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `featured` binary(1) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  PRIMARY KEY (`workshopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_workshop`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop_document`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop_document` (
  `workshopDocumentId` int(11) NOT NULL AUTO_INCREMENT,
  `workshopId` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `uploadDt` int(10) unsigned NOT NULL DEFAULT '0',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`workshopDocumentId`),
  KEY `workshopId` (`workshopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_workshop_document`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop_editor`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop_editor` (
  `workshopId` int(10) unsigned NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`workshopId`,`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_workshop_editor`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop_link`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop_link` (
  `workshopLinkId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `workshopId` int(10) unsigned NOT NULL DEFAULT '0',
  `url` mediumtext NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`workshopLinkId`),
  KEY `workshopId` (`workshopId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_workshop_link`
--

