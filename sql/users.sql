-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 16, 2013 at 04:43 PM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `auto_subdomain`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subdomain` varchar(64) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  `db_name` varchar(65) NOT NULL,
  `db_user` varchar(20) NOT NULL,
  `entrydate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `subdomain`, `username`, `password`, `email`, `db_name`, `db_user`, `entrydate`) VALUES
(30, 'blog', 'monmon', 'e10adc3949ba59abbe56e057f20f883e', 'mon@gmail.com', 'sgdeal_google1', 'sgdeal_246ea1', '2012-08-23 16:23:14');