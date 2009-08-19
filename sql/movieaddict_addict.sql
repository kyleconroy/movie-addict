-- phpMyAdmin SQL Dump
-- version 2.11.9.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2009 at 11:54 PM
-- Server version: 5.0.81
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `brokenva_adddict`
--

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
  `movie_id` int(11) NOT NULL,
  `netflix_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `imdb_pos` int(11) default NULL,
  `afi_pos` int(11) default NULL,
  `instant` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`movie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) unsigned NOT NULL,
  `seen` int(11) NOT NULL default '0',
  `seen_imdb` int(11) NOT NULL default '0',
  `seen_afi` int(11) NOT NULL default '0',
  `percent` float NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_movies`
--

CREATE TABLE IF NOT EXISTS `users_movies` (
  `user_id` bigint(20) unsigned NOT NULL,
  `movie_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`movie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
