-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 24, 2011 at 04:18 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bba-power`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `cl_id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `cl_desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `cl_doc_loa` int(11) NOT NULL DEFAULT '0',
  `cl_loa_expiryDate` date NOT NULL DEFAULT '2011-01-01',
  PRIMARY KEY (`cl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22 ;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`cl_id`, `cl_name`, `cl_desc`, `cl_doc_loa`, `cl_loa_expiryDate`) VALUES
(1, 'Alpha Holding Ltd.', 'Alpha Holding Group', 0, '2012-05-18'),
(2, 'Olivias Bakery', 'Olivias Bakery', 0, '2012-06-11'),
(7, 'ep1', 'EPdesc1wwww', 0, '2011-04-03'),
(18, 'qqq', 'werwer', 0, '2014-05-12'),
(14, 'newclient1', 'newclient1-desc', 0, '2013-12-12'),
(21, 'Keer PLC', 'Keer test client', 0, '2012-02-11');

-- --------------------------------------------------------

--
-- Table structure for table `client_address`
--

DROP TABLE IF EXISTS `client_address`;
CREATE TABLE IF NOT EXISTS `client_address` (
  `clad_id` int(11) NOT NULL AUTO_INCREMENT,
  `clad_cl_id` int(11) NOT NULL,
  `clad_address1` varchar(50) COLLATE utf8_bin NOT NULL,
  `clad_address2` varchar(50) COLLATE utf8_bin NOT NULL,
  `clad_address3` varchar(50) COLLATE utf8_bin NOT NULL,
  `clad_postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`clad_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `client_address`
--

INSERT INTO `client_address` (`clad_id`, `clad_cl_id`, `clad_address1`, `clad_address2`, `clad_address3`, `clad_postcode`) VALUES
(1, 1, 'client address 1 - 1', 'client address 1 - 2', 'client address 1 - 3', 'add1 - pc'),
(2, 2, 'client address 2 - 1', 'client address 2 - 2', 'client address 2 - 3', 'add2 - pc');

-- --------------------------------------------------------

--
-- Table structure for table `client_contact`
--

DROP TABLE IF EXISTS `client_contact`;
CREATE TABLE IF NOT EXISTS `client_contact` (
  `clco_id` int(11) NOT NULL AUTO_INCREMENT,
  `clco_cl_id` int(11) NOT NULL,
  `clco_type` varchar(8) COLLATE utf8_bin NOT NULL,
  `clco_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `clco_clad_id` int(11) NOT NULL,
  `clco_phone` varchar(50) COLLATE utf8_bin NOT NULL,
  `clco_email` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`clco_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `client_contact`
--

INSERT INTO `client_contact` (`clco_id`, `clco_cl_id`, `clco_type`, `clco_name`, `clco_clad_id`, `clco_phone`, `clco_email`) VALUES
(1, 1, 'boss1', 'eddie', 1, '123123', 'email1'),
(2, 2, 'boss2', 'name2', 2, '234234', 'email2');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

DROP TABLE IF EXISTS `contract`;
CREATE TABLE IF NOT EXISTS `contract` (
  `co_id` int(11) NOT NULL AUTO_INCREMENT,
  `co_type` varchar(6) COLLATE utf8_bin NOT NULL,
  `co_id_old` int(11) NOT NULL DEFAULT '0',
  `co_te_id_selected` int(11) NOT NULL DEFAULT '0',
  `co_suco_id_selected` int(11) NOT NULL DEFAULT '0',
  `co_dateStart` date NOT NULL,
  `co_dateEnd` date NOT NULL,
  `co_docTender` int(11) NOT NULL DEFAULT '0',
  `co_docAnalysis` int(11) NOT NULL DEFAULT '0',
  `co_periodBillCust` int(11) NOT NULL DEFAULT '0',
  `co_periodCommission` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`co_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `contract`
--

INSERT INTO `contract` (`co_id`, `co_type`, `co_id_old`, `co_te_id_selected`, `co_suco_id_selected`, `co_dateStart`, `co_dateEnd`, `co_docTender`, `co_docAnalysis`, `co_periodBillCust`, `co_periodCommission`) VALUES
(1, 'temp', 0, 0, 0, '2011-06-01', '2012-05-31', 0, 0, 0, 0),
(2, 'new', 1, 0, 0, '2011-06-01', '2012-05-31', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `contract_site`
--

DROP TABLE IF EXISTS `contract_site`;
CREATE TABLE IF NOT EXISTS `contract_site` (
  `cosi_id` int(11) NOT NULL,
  `cosi_co_id` int(11) NOT NULL,
  PRIMARY KEY (`cosi_id`,`cosi_co_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `contract_site`
--

INSERT INTO `contract_site` (`cosi_id`, `cosi_co_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `meter`
--

DROP TABLE IF EXISTS `meter`;
CREATE TABLE IF NOT EXISTS `meter` (
  `me_id` int(11) NOT NULL AUTO_INCREMENT,
  `me_si_id` int(11) NOT NULL,
  `me_no` varchar(25) COLLATE utf8_bin NOT NULL,
  `me_dateInstall` date NOT NULL,
  PRIMARY KEY (`me_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=14 ;

--
-- Dumping data for table `meter`
--

INSERT INTO `meter` (`me_id`, `me_si_id`, `me_no`, `me_dateInstall`) VALUES
(1, 1, 'meter no 1', '2012-06-14'),
(2, 2, 'meter no 2', '2012-05-31'),
(10, 1, 'wewe', '2019-05-23'),
(11, 1, 'ytuetyudaaaa', '2012-07-24'),
(13, 15, '03 456 456 673', '2000-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `meter_contract`
--

DROP TABLE IF EXISTS `meter_contract`;
CREATE TABLE IF NOT EXISTS `meter_contract` (
  `meco_me_id` int(11) NOT NULL,
  `meco_co_id` int(11) NOT NULL,
  PRIMARY KEY (`meco_me_id`,`meco_co_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `meter_contract`
--

INSERT INTO `meter_contract` (`meco_me_id`, `meco_co_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `reading`
--

DROP TABLE IF EXISTS `reading`;
CREATE TABLE IF NOT EXISTS `reading` (
  `re_id` int(11) NOT NULL AUTO_INCREMENT,
  `re_me_id` int(11) NOT NULL,
  `re_dateBill` date NOT NULL,
  `re_dateReading` date NOT NULL,
  `re_valueDay` int(11) NOT NULL,
  `re_valueNight` int(11) NOT NULL,
  `re_valueOther` int(11) NOT NULL,
  PRIMARY KEY (`re_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `reading`
--

INSERT INTO `reading` (`re_id`, `re_me_id`, `re_dateBill`, `re_dateReading`, `re_valueDay`, `re_valueNight`, `re_valueOther`) VALUES
(1, 1, '2012-05-30', '2012-05-31', 145, 146, 147),
(2, 1, '2012-06-30', '2012-05-23', 245, 246, 247),
(3, 2, '2011-05-30', '2011-05-31', 367, 368, 369),
(4, 2, '2011-04-30', '2011-03-23', 489, 488, 487),
(5, 1, '2012-07-14', '2012-07-15', 44, 55, 66),
(6, 1, '2011-04-30', '2011-04-12', 222, 333, 444),
(7, 13, '2011-06-01', '2011-05-26', 5678, 1234, 0),
(8, 13, '2011-07-01', '2011-06-26', 3656, 2345, 0);

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
CREATE TABLE IF NOT EXISTS `site` (
  `si_id` int(11) NOT NULL AUTO_INCREMENT,
  `si_cl_id` int(11) NOT NULL,
  `si_address1` varchar(50) COLLATE utf8_bin NOT NULL,
  `si_address2` varchar(50) COLLATE utf8_bin NOT NULL,
  `si_address3` varchar(50) COLLATE utf8_bin NOT NULL,
  `si_postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  `si_clad_id` int(11) NOT NULL,
  PRIMARY KEY (`si_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`si_id`, `si_cl_id`, `si_address1`, `si_address2`, `si_address3`, `si_postcode`, `si_clad_id`) VALUES
(1, 1, 'Alpha HQ', 'Alpha House', 'West Riding', 'HP7 2LR', 1),
(2, 2, 'site-address2 - 1', 'site-address2 - 2', 'site-address2 - 3', 'add2 - pc', 2),
(4, 1, 'Alpha House', '23-27 High Street', 'Tiverton', 'M23 5TR', 1),
(5, 7, 'aa7-1', 'aa7-2', 'aa7-3', 'aa7-pc', 2),
(6, 14, 'dsfgsfqqq', 'xcvxcvqqq', 'wertewrqqq', 'asaa', 0),
(14, 1, '12', '12', '12', '12', 1),
(15, 21, 'Tempsford Hut', 'North Road', 'SANDY', 'SG19 9PP', 1),
(16, 21, 'BigBuild Construction', 'Bishops Wharf', 'Sheffield', 'S12 3TR', 1);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
CREATE TABLE IF NOT EXISTS `supplier` (
  `su_id` int(11) NOT NULL AUTO_INCREMENT,
  `su_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_address1` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_address2` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_address3` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  `su_periodCommission` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`su_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`su_id`, `su_name`, `su_address1`, `su_address2`, `su_address3`, `su_postcode`, `su_periodCommission`) VALUES
(1, 'supplier 1', 's1 - add1', 's1 - add2', 's1 - add3', 's1-pc', 6),
(2, 'supplier 2', 's2 - add1', 's2 - add2', 's2 - add3', 's2 - pc', 3);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_contact`
--

DROP TABLE IF EXISTS `supplier_contact`;
CREATE TABLE IF NOT EXISTS `supplier_contact` (
  `suco_id` int(11) NOT NULL AUTO_INCREMENT,
  `suco_supplier-id` int(11) NOT NULL,
  `suco_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_phone` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_email` varchar(100) COLLATE utf8_bin NOT NULL,
  `suco_address1` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_address2` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_address3` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`suco_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `supplier_contact`
--

INSERT INTO `supplier_contact` (`suco_id`, `suco_supplier-id`, `suco_name`, `suco_phone`, `suco_email`, `suco_address1`, `suco_address2`, `suco_address3`, `suco_postcode`) VALUES
(1, 1, 'c1 - name', 'c1 - phone', 'c1 - email', 'c1 - add1', 'c1 - add2', 'c1 - add3', 'c1 - pc'),
(2, 2, 'c2 - namee', 'c2 - phone', 'c2 - email', 'c2 - add1', 'c2 - add2', 'c2 - add3', 'c2 - pc');

-- --------------------------------------------------------

--
-- Table structure for table `tender`
--

DROP TABLE IF EXISTS `tender`;
CREATE TABLE IF NOT EXISTS `tender` (
  `te_id` int(11) NOT NULL AUTO_INCREMENT,
  `te_co_id` int(11) NOT NULL,
  `te_su_id` int(11) NOT NULL,
  `te_suco_id` int(11) NOT NULL,
  `te_dateQuote` date NOT NULL,
  `te_docResponse` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`te_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tender`
--

INSERT INTO `tender` (`te_id`, `te_co_id`, `te_su_id`, `te_suco_id`, `te_dateQuote`, `te_docResponse`) VALUES
(1, 1, 1, 1, '2011-05-18', ''),
(2, 2, 2, 2, '2011-05-19', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `us_id` int(11) NOT NULL AUTO_INCREMENT,
  `us_username` varchar(50) COLLATE utf8_bin NOT NULL,
  `us_password` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `us_real_name` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`us_id`),
  UNIQUE KEY `us_username` (`us_username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`us_id`, `us_username`, `us_password`, `us_real_name`) VALUES
(1, 'epounce', '7997a2e1adfb9c86a00816cd40c2836e', 'Eddie'),
(2, 'fred', 'fred', 'Fred'),
(3, 'a', '8dcd7cdf35189a2a19000f7b96ffd7e1', 'A User'),
(4, 'paul', 'e8533e46435b7f77eb8fa7efa6c3eba2', 'Paul');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
