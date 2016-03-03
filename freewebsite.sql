-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2016 at 03:23 PM
-- Server version: 5.0.95
-- PHP Version: 5.5.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `freewebsite`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbCareer`
--

CREATE TABLE IF NOT EXISTS `tbCareer` (
  `seq` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `content` text,
  `startDate` datetime default NULL,
  `endDate` datetime default NULL,
  `viewCount` int(11) NOT NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `title` (`title`,`delYn`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbCatalog`
--

CREATE TABLE IF NOT EXISTS `tbCatalog` (
  `seq` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `url` varchar(2000) default NULL,
  `viewCount` int(11) NOT NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `name` (`name`,`delYn`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbContact`
--

CREATE TABLE IF NOT EXISTS `tbContact` (
  `seq` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `content` text,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `name` (`name`,`delYn`),
  KEY `title` (`title`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbEvent`
--

CREATE TABLE IF NOT EXISTS `tbEvent` (
  `seq` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `content` text,
  `startDate` datetime default NULL,
  `endDate` datetime default NULL,
  `viewCount` int(11) NOT NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `title` (`title`,`delYn`),
  KEY `startDate` (`startDate`,`endDate`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbFile`
--

CREATE TABLE IF NOT EXISTS `tbFile` (
  `seq` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `channel` varchar(255) default NULL,
  `category` varchar(255) default NULL,
  `ownerSeq` int(11) default NULL,
  `originalName` varchar(255) default NULL,
  `size` int(11) default NULL,
  `path` varchar(255) default NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `channel` (`channel`),
  KEY `category` (`category`),
  KEY `ownerSeq` (`ownerSeq`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbNews`
--

CREATE TABLE IF NOT EXISTS `tbNews` (
  `seq` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `content` text,
  `viewCount` int(11) NOT NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `title` (`title`,`delYn`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbProduct`
--

CREATE TABLE IF NOT EXISTS `tbProduct` (
  `seq` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `extra1` text,
  `extra2` text,
  `extra3` text,
  `extra4` text,
  `extra5` text,
  `extra6` text,
  `extra7` text,
  `extra8` text,
  `extra9` text,
  `extra10` text,
  `viewCount` int(11) NOT NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `name` (`name`,`delYn`),
  KEY `delYn` (`delYn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbProject`
--

CREATE TABLE IF NOT EXISTS `tbProject` (
  `seq` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `country` varchar(255) default NULL,
  `location` varchar(255) default NULL,
  `startDate` datetime default NULL,
  `endDate` datetime default NULL,
  `viewCount` int(11) NOT NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `name` (`name`,`delYn`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbService`
--

CREATE TABLE IF NOT EXISTS `tbService` (
  `seq` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `extra1` text,
  `extra2` text,
  `extra3` text,
  `extra4` text,
  `extra5` text,
  `extra6` text,
  `extra7` text,
  `extra8` text,
  `extra9` text,
  `extra10` text,
  `viewCount` int(11) NOT NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `name` (`name`,`delYn`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbTerm`
--

CREATE TABLE IF NOT EXISTS `tbTerm` (
  `seq` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `taxonomy` varchar(3) default NULL,
  `lineage` varchar(255) default NULL,
  `parent` int(11) default NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `delYn` (`delYn`),
  KEY `taxonomy` (`taxonomy`),
  KEY `lineage` (`lineage`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbTermRelationship`
--

CREATE TABLE IF NOT EXISTS `tbTermRelationship` (
  `seq` int(11) NOT NULL auto_increment,
  `channel` varchar(255) default NULL,
  `ownerSeq` int(11) default NULL,
  `termSeq` int(11) default NULL,
  `taxonomy` varchar(3) default NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  KEY `channel` (`channel`,`ownerSeq`,`termSeq`),
  KEY `delYn` (`delYn`),
  KEY `taxonomy` (`taxonomy`),
  KEY `ownerSeq` (`ownerSeq`),
  KEY `termSeq` (`termSeq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=278 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbUser`
--

CREATE TABLE IF NOT EXISTS `tbUser` (
  `seq` int(11) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(50) default NULL,
  `lastName` varchar(50) default NULL,
  `email` varchar(255) default NULL,
  `regIp` varchar(20) default NULL,
  `regId` varchar(50) default NULL,
  `regDate` datetime default NULL,
  `modIp` varchar(20) default NULL,
  `modId` varchar(50) default NULL,
  `modDate` datetime default NULL,
  `delYn` varchar(1) default NULL,
  PRIMARY KEY  (`seq`),
  UNIQUE KEY `username` (`username`),
  KEY `delYn` (`delYn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
