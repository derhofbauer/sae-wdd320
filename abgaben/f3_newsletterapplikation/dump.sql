-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 03, 2014 at 06:49 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `sae_newsletter`
--
CREATE DATABASE IF NOT EXISTS `sae_newsletter` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sae_newsletter`;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_categories`
--

CREATE TABLE IF NOT EXISTS `newsletter_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `newsletter_categories`
--

INSERT INTO `newsletter_categories` (`id`, `title`, `description`) VALUES
(1, 'Web', 'Mit Hilfe der dynamischen WWW-Seiten kann das WWW als Oberfläche für verteilte Programme dienen: Ein Programm wird nicht mehr konventionell lokal auf dem Rechner gestartet, sondern ist eine Menge von dynamischen WWW-Seiten, die durch einen Webbrowser betrachtet und bedient werden können.'),
(2, 'Print', 'Als Druckerzeugnisse (Druckmedien bzw. Printmedien) werden klassische gedruckte Informationsquellen wie Zeitschriften, Zeitungen, Bücher, Kataloge, geografische Karten und Pläne, aber auch Postkarten, Kalender, Poster, Flugblätter, Flugschriften, Plakate usw. bezeichnet.'),
(3, 'Social-Media', 'Social Media (auch soziale Medien) bezeichnen digitale Medien und Technologien (vgl. Social Software), die es Nutzern ermöglichen, sich untereinander auszutauschen und mediale Inhalte einzeln oder in Gemeinschaft zu erstellen.');

-- --------------------------------------------------------

--
-- Table structure for table `recipients`
--

CREATE TABLE IF NOT EXISTS `recipients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `newsletter_category_id` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `recipients`
--

INSERT INTO `recipients` (`id`, `email`, `fullname`, `newsletter_category_id`, `created_at`) VALUES
(1, 'michi@someotheremail.net', 'Michi', 2, 1409756883),
(5, 'klaus@someotheremail.net', 'Klaus E.', 1, 1409762724);
