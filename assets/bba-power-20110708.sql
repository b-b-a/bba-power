-- phpMyAdmin SQL Dump
-- version 3.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 08, 2011 at 06:37 AM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


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

CREATE TABLE IF NOT EXISTS `client` (
  `cl_id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `cl_desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `cl_doc_loa` int(11) NOT NULL DEFAULT '0',
  `cl_date_expiry_loa` date NOT NULL DEFAULT '2011-01-01',
  `cl_create_by` int(11) NOT NULL,
  `cl_create_date` date NOT NULL,
  `cl_mod_by` int(11) NOT NULL,
  `cl_mod_date` date NOT NULL,
  PRIMARY KEY (`cl_id`),
  KEY `cl_createBy` (`cl_create_by`,`cl_mod_by`),
  KEY `cl_modBy` (`cl_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22 ;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`cl_id`, `cl_name`, `cl_desc`, `cl_doc_loa`, `cl_date_expiry_loa`, `cl_create_by`, `cl_create_date`, `cl_mod_by`, `cl_mod_date`) VALUES
(1, 'Alpha Holding Ltd.', 'Alpha Holding Group', 0, '2012-05-18', 1, '2011-07-01', 1, '2011-07-01'),
(2, 'Olivias Bakery', 'Olivias Bakery', 0, '2012-06-11', 1, '2011-07-01', 1, '2011-07-01'),
(7, 'ep1', 'EPdesc1wwww', 0, '2011-04-03', 1, '2011-07-01', 1, '2011-07-01'),
(14, 'newclient1', 'newclient1-desc', 0, '2013-12-12', 1, '2011-07-01', 1, '2011-07-01'),
(18, 'qqq', 'werwer', 0, '2014-05-12', 1, '2011-07-01', 1, '2011-07-01'),
(21, 'Keer PLC', 'Keer test client', 0, '2012-02-11', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `client_address`
--

CREATE TABLE IF NOT EXISTS `client_address` (
  `clad_id` int(11) NOT NULL AUTO_INCREMENT,
  `clad_client_id` int(11) NOT NULL,
  `clad_address1` varchar(50) COLLATE utf8_bin NOT NULL,
  `clad_address2` varchar(50) COLLATE utf8_bin NOT NULL,
  `clad_address3` varchar(50) COLLATE utf8_bin NOT NULL,
  `clad_postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  `clad_create_by` int(11) NOT NULL,
  `clad_create_date` date NOT NULL,
  `clad_mod_by` int(11) NOT NULL,
  `clad_mod_date` date NOT NULL,
  PRIMARY KEY (`clad_id`),
  KEY `clad_client_id` (`clad_client_id`),
  KEY `clad_createBy` (`clad_create_by`,`clad_mod_by`),
  KEY `clad_modBy` (`clad_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `client_address`
--

INSERT INTO `client_address` (`clad_id`, `clad_client_id`, `clad_address1`, `clad_address2`, `clad_address3`, `clad_postcode`, `clad_create_by`, `clad_create_date`, `clad_mod_by`, `clad_mod_date`) VALUES
(1, 1, 'client address 1 - 1', 'client address 1 - 2', 'client address 1 - 3', 'add1 - pc', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'client address 2 - 1', 'client address 2 - 2', 'client address 2 - 3', 'add2 - pc', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `client_contact`
--

CREATE TABLE IF NOT EXISTS `client_contact` (
  `clco_id` int(11) NOT NULL AUTO_INCREMENT,
  `clco_client_id` int(11) NOT NULL,
  `clco_type` varchar(8) COLLATE utf8_bin NOT NULL,
  `clco_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `clco_client_address_id` int(11) NOT NULL,
  `clco_phone` varchar(50) COLLATE utf8_bin NOT NULL,
  `clco_email` varchar(100) COLLATE utf8_bin NOT NULL,
  `clco_create_by` int(11) NOT NULL,
  `clco_create_date` date NOT NULL,
  `clco_mod_by` int(11) NOT NULL,
  `clco_mod_date` date NOT NULL,
  PRIMARY KEY (`clco_id`),
  KEY `clco_client_id` (`clco_client_id`),
  KEY `clco_client_address_id` (`clco_client_address_id`),
  KEY `clco_createBy` (`clco_create_by`,`clco_mod_by`),
  KEY `clco_modBy` (`clco_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `client_contact`
--

INSERT INTO `client_contact` (`clco_id`, `clco_client_id`, `clco_type`, `clco_name`, `clco_client_address_id`, `clco_phone`, `clco_email`, `clco_create_by`, `clco_create_date`, `clco_mod_by`, `clco_mod_date`) VALUES
(1, 1, 'boss1', 'eddie', 1, '123123', 'email1', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'boss2', 'name2', 2, '234234', 'email2', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE IF NOT EXISTS `contract` (
  `co_id` int(11) NOT NULL AUTO_INCREMENT,
  `co_type` varchar(8) COLLATE utf8_bin NOT NULL,
  `co_status` varchar(16) COLLATE utf8_bin NOT NULL,
  `co_desc` varchar(256) COLLATE utf8_bin NOT NULL,
  `co_tender_id_selected` int(11) NOT NULL DEFAULT '0',
  `co_supplier_contact_id_selected` int(11) NOT NULL DEFAULT '0',
  `co_date_start` date NOT NULL,
  `co_date_end` date NOT NULL,
  `co_txt_tender` mediumtext COLLATE utf8_bin NOT NULL,
  `co_doc_analysis` varchar(64) COLLATE utf8_bin NOT NULL,
  `co_doc_termination` varchar(64) COLLATE utf8_bin NOT NULL,
  `co_period_bill_cust` int(11) NOT NULL DEFAULT '0',
  `co_period_commission` int(11) NOT NULL DEFAULT '0',
  `co_user_id_agent` int(11) NOT NULL,
  `co_create_by` int(11) NOT NULL,
  `co_create_date` date NOT NULL,
  `co_mod_by` int(11) NOT NULL,
  `co_mod_date` date NOT NULL,
  PRIMARY KEY (`co_id`),
  KEY `co_user_id_agent` (`co_user_id_agent`),
  KEY `co_tender_id_selected` (`co_tender_id_selected`),
  KEY `co_supplier_contact_id_selected` (`co_supplier_contact_id_selected`),
  KEY `co_createBy` (`co_create_by`,`co_mod_by`),
  KEY `co_modBy` (`co_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `contract`
--

INSERT INTO `contract` (`co_id`, `co_type`, `co_status`, `co_desc`, `co_tender_id_selected`, `co_supplier_contact_id_selected`, `co_date_start`, `co_date_end`, `co_txt_tender`, `co_doc_analysis`, `co_doc_termination`, `co_period_bill_cust`, `co_period_commission`, `co_user_id_agent`, `co_create_by`, `co_create_date`, `co_mod_by`, `co_mod_date`) VALUES
(1, 'temp', '', '', 0, 0, '2011-06-01', '2012-05-31', '0', '0', '', 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 'new', '', '', 0, 0, '2011-06-01', '2012-05-31', '0', '0', '', 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `contract_site`
--

CREATE TABLE IF NOT EXISTS `contract_site` (
  `cosi_contract_id` int(11) NOT NULL,
  `cosi_site_id` int(11) NOT NULL,
  PRIMARY KEY (`cosi_contract_id`,`cosi_site_id`),
  KEY `cosi_contract_id` (`cosi_contract_id`),
  KEY `cosi_site_id` (`cosi_site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `contract_site`
--

INSERT INTO `contract_site` (`cosi_contract_id`, `cosi_site_id`) VALUES
(1, 15),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `meter`
--

CREATE TABLE IF NOT EXISTS `meter` (
  `me_id` int(11) NOT NULL AUTO_INCREMENT,
  `me_site_id` int(11) NOT NULL,
  `me_type` varchar(8) COLLATE utf8_bin NOT NULL,
  `me_no` varchar(25) COLLATE utf8_bin NOT NULL,
  `me_date_install` date NOT NULL,
  `me_date_removed` date NOT NULL,
  `me_pipe_size` varchar(32) COLLATE utf8_bin NOT NULL,
  `me_create_by` int(11) NOT NULL,
  `me_create_date` date NOT NULL,
  `me_mod_by` int(11) NOT NULL,
  `me_mod_date` date NOT NULL,
  PRIMARY KEY (`me_id`),
  KEY `me_createBy` (`me_create_by`,`me_mod_by`),
  KEY `me_modBy` (`me_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=15 ;

--
-- Dumping data for table `meter`
--

INSERT INTO `meter` (`me_id`, `me_site_id`, `me_type`, `me_no`, `me_date_install`, `me_date_removed`, `me_pipe_size`, `me_create_by`, `me_create_date`, `me_mod_by`, `me_mod_date`) VALUES
(1, 1, '', 'meter no 1', '2012-06-14', '0000-00-00', '', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, '', 'meter no 2', '2012-05-31', '0000-00-00', '', 1, '2011-07-01', 1, '2011-07-01'),
(10, 1, '', 'a meter no', '2019-04-23', '0000-00-00', '', 1, '2011-07-01', 1, '2011-07-01'),
(11, 1, '', 'ytuetyudaaaa', '2012-07-24', '0000-00-00', '', 1, '2011-07-01', 1, '2011-07-01'),
(13, 15, '', '03 456 456 673', '2000-01-01', '0000-00-00', '', 1, '2011-07-01', 1, '2011-07-01'),
(14, 4, '', '1212', '0000-00-00', '0000-00-00', '', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `meter_contract`
--

CREATE TABLE IF NOT EXISTS `meter_contract` (
  `meco_meter_id` int(11) NOT NULL,
  `meco_contract_id` int(11) NOT NULL,
  `meco_use_estimate` int(11) NOT NULL,
  `meco_create_by` int(11) NOT NULL,
  `meco_create_date` date NOT NULL,
  `meco_mod_by` int(11) NOT NULL,
  `meco_mod_date` date NOT NULL,
  PRIMARY KEY (`meco_meter_id`,`meco_contract_id`),
  KEY `meco_meter_id` (`meco_meter_id`),
  KEY `meco_contract_id` (`meco_contract_id`),
  KEY `meco_createBy` (`meco_create_by`,`meco_mod_by`),
  KEY `meco_modBy` (`meco_mod_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `meter_contract`
--

INSERT INTO `meter_contract` (`meco_meter_id`, `meco_contract_id`, `meco_use_estimate`, `meco_create_by`, `meco_create_date`, `meco_mod_by`, `meco_mod_date`) VALUES
(1, 1, 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 0, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `reading`
--

CREATE TABLE IF NOT EXISTS `reading` (
  `re_id` int(11) NOT NULL AUTO_INCREMENT,
  `re_meter_id` int(11) NOT NULL,
  `re_date_bill` date NOT NULL,
  `re_date_reading` date NOT NULL,
  `re_value_day` int(11) NOT NULL,
  `re_value_night` int(11) NOT NULL,
  `re_value_other` int(11) NOT NULL,
  `re_type` varchar(8) COLLATE utf8_bin NOT NULL,
  `re_create_by` int(11) NOT NULL,
  `re_create_date` date NOT NULL,
  `re_mod_by` int(11) NOT NULL,
  `re_mod_date` date NOT NULL,
  PRIMARY KEY (`re_id`),
  KEY `re_meter_id` (`re_meter_id`),
  KEY `re_createBy` (`re_create_by`,`re_mod_by`),
  KEY `re_modBy` (`re_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Dumping data for table `reading`
--

INSERT INTO `reading` (`re_id`, `re_meter_id`, `re_date_bill`, `re_date_reading`, `re_value_day`, `re_value_night`, `re_value_other`, `re_type`, `re_create_by`, `re_create_date`, `re_mod_by`, `re_mod_date`) VALUES
(1, 1, '2012-05-30', '2012-05-31', 145, 146, 147, '', 1, '2011-07-01', 1, '2011-07-01'),
(2, 1, '2012-06-30', '2012-05-23', 245, 246, 247, '', 1, '2011-07-01', 1, '2011-07-01'),
(3, 2, '2011-05-30', '2011-05-31', 367, 368, 369, '', 1, '2011-07-01', 1, '2011-07-01'),
(4, 2, '2011-04-30', '2011-03-23', 489, 488, 487, '', 1, '2011-07-01', 1, '2011-07-01'),
(5, 1, '2012-07-14', '2012-07-15', 44, 55, 66, '', 1, '2011-07-01', 1, '2011-07-01'),
(6, 1, '2011-04-30', '2011-04-12', 222, 333, 444, '', 1, '2011-07-01', 1, '2011-07-01'),
(7, 13, '2011-06-01', '2011-05-26', 5678, 1234, 0, '', 1, '2011-07-01', 1, '2011-07-01'),
(8, 13, '2011-07-01', '2011-06-26', 3656, 2345, 0, '', 1, '2011-07-01', 1, '2011-07-01'),
(9, 1, '2012-07-14', '2012-07-15', 1212, 0, 0, '', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` char(32) NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`id`, `modified`, `lifetime`, `data`) VALUES
('2cpl7tdj0o65vhu9ip4j5vk5h3', 1310102109, 864000, '__ZF|a:1:{s:33:"Zend_Form_Element_Hash_unique_bba";a:2:{s:4:"ENNH";i:1;s:3:"ENT";i:1310102409;}}Zend_Form_Element_Hash_unique_bba|a:1:{s:4:"hash";s:32:"3c8ffcba7ea33db4457e668ae07f48fc";}');

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `si_id` int(11) NOT NULL AUTO_INCREMENT,
  `si_client_id` int(11) NOT NULL,
  `si_client_address_id` int(11) NOT NULL,
  `si_client_address_id_bill` int(11) NOT NULL,
  `si_client_contact_id` int(11) NOT NULL,
  `si_create_by` int(11) NOT NULL,
  `si_create_date` date NOT NULL,
  `si_mod_by` int(11) NOT NULL,
  `si_mod_date` date NOT NULL,
  PRIMARY KEY (`si_id`),
  KEY `si_client_id` (`si_client_id`),
  KEY `si_client_address_id` (`si_client_address_id`),
  KEY `si_client_address_id_bill` (`si_client_address_id_bill`),
  KEY `si_client_contact_id` (`si_client_contact_id`),
  KEY `si_createBy` (`si_create_by`,`si_mod_by`),
  KEY `si_modBy` (`si_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`si_id`, `si_client_id`, `si_client_address_id`, `si_client_address_id_bill`, `si_client_contact_id`, `si_create_by`, `si_create_date`, `si_mod_by`, `si_mod_date`) VALUES
(1, 1, 1, 1, 1, 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 2, 2, 2, 1, '2011-07-01', 1, '2011-07-01'),
(4, 1, 1, 2, 2, 1, '2011-07-01', 1, '2011-07-01'),
(5, 7, 2, 1, 1, 1, '2011-07-01', 1, '2011-07-01'),
(6, 14, 1, 1, 1, 1, '2011-07-01', 1, '2011-07-01'),
(14, 1, 1, 2, 2, 1, '2011-07-01', 1, '2011-07-01'),
(15, 21, 1, 1, 1, 1, '2011-07-01', 1, '2011-07-01'),
(16, 21, 1, 2, 2, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE IF NOT EXISTS `supplier` (
  `su_id` int(11) NOT NULL AUTO_INCREMENT,
  `su_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_address1` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_address2` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_address3` varchar(50) COLLATE utf8_bin NOT NULL,
  `su_postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  `su_period_commission` int(11) NOT NULL DEFAULT '0',
  `su_create_by` int(11) NOT NULL,
  `su_create_date` date NOT NULL,
  `su_mod_by` int(11) NOT NULL,
  `su_mod_date` date NOT NULL,
  PRIMARY KEY (`su_id`),
  KEY `su_createBy` (`su_create_by`,`su_mod_by`),
  KEY `su_modBy` (`su_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`su_id`, `su_name`, `su_address1`, `su_address2`, `su_address3`, `su_postcode`, `su_period_commission`, `su_create_by`, `su_create_date`, `su_mod_by`, `su_mod_date`) VALUES
(1, 'supplier 1', 's1 - add1', 's1 - add2', 's1 - add3', 's1-pc', 6, 1, '2011-07-01', 1, '2011-07-01'),
(2, 'supplier 2', 's2 - add1', 's2 - add2', 's2 - add3', 's2 - pc', 3, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_contact`
--

CREATE TABLE IF NOT EXISTS `supplier_contact` (
  `suco_id` int(11) NOT NULL AUTO_INCREMENT,
  `suco_supplier_id` int(11) NOT NULL,
  `suco_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_phone` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_email` varchar(100) COLLATE utf8_bin NOT NULL,
  `suco_address1` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_address2` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_address3` varchar(50) COLLATE utf8_bin NOT NULL,
  `suco_postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  `suco_create_by` int(11) NOT NULL,
  `suco_create_date` date NOT NULL,
  `suco_mod_by` int(11) NOT NULL,
  `suco_mod_date` date NOT NULL,
  PRIMARY KEY (`suco_id`),
  KEY `suco_supplier-id` (`suco_supplier_id`),
  KEY `suco_createBy` (`suco_create_by`,`suco_mod_by`),
  KEY `suco_modBy` (`suco_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `supplier_contact`
--

INSERT INTO `supplier_contact` (`suco_id`, `suco_supplier_id`, `suco_name`, `suco_phone`, `suco_email`, `suco_address1`, `suco_address2`, `suco_address3`, `suco_postcode`, `suco_create_by`, `suco_create_date`, `suco_mod_by`, `suco_mod_date`) VALUES
(1, 1, 'c1 - name', 'c1 - phone', 'c1 - email', 'c1 - add1', 'c1 - add2', 'c1 - add3', 'c1 - pc', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'c2 - namee', 'c2 - phone', 'c2 - email', 'c2 - add1', 'c2 - add2', 'c2 - add3', 'c2 - pc', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE IF NOT EXISTS `tables` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` char(32) COLLATE utf8_bin NOT NULL,
  `table_key` char(32) COLLATE utf8_bin NOT NULL,
  `table_value` char(32) COLLATE utf8_bin NOT NULL,
  `table_sort` int(11) NOT NULL,
  PRIMARY KEY (`table_id`),
  KEY `table_name` (`table_name`),
  KEY `table_key` (`table_key`),
  KEY `table_sort` (`table_sort`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=23 ;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`table_id`, `table_name`, `table_key`, `table_value`, `table_sort`) VALUES
(1, 'clientContactType', 'type1', 'Type 1', 30),
(2, 'clientContactType', 'type2', 'Type 2', 20),
(3, 'period', '0', 'Not Set', 0),
(8, 'period', '1', '1 Month', 1),
(9, 'period', '3', '3 Months', 3),
(10, 'period', '6', '6 Months', 6),
(11, 'period', '12', '12 Months', 12),
(12, 'period', '18', '18 Months', 18),
(13, 'period', '24', '2 Years', 24),
(14, 'period', '36', '3 Years', 36),
(15, 'period', '48', '4 Years', 48),
(16, 'period', '60', '5 Years', 60),
(17, 'contractStatus', 'notBBA', 'Not BBA', 100),
(18, 'contractStatus', 'new', 'New', 10),
(19, 'contractStatus', 'tender', 'Out To Tender', 20),
(20, 'contractStatus', 'choose', 'Choosing', 30),
(21, 'contractStatus', 'selected', 'Selected', 40),
(22, 'contractStatus', 'signed', 'Signed', 50);

-- --------------------------------------------------------

--
-- Table structure for table `tender`
--

CREATE TABLE IF NOT EXISTS `tender` (
  `te_id` int(11) NOT NULL AUTO_INCREMENT,
  `te_contract_id` int(11) NOT NULL,
  `te_supplier_id` int(11) NOT NULL,
  `te_supplier_contact_id` int(11) NOT NULL,
  `te_date_quote_expires` date NOT NULL,
  `te_txt_response` mediumtext COLLATE utf8_bin NOT NULL,
  `te_std_charge_day` int(11) NOT NULL,
  `te_std_charge_night` int(11) NOT NULL,
  `te_std_charge_other` int(11) NOT NULL,
  `te_unit_price_day` int(11) NOT NULL,
  `te_unit_price_night` int(11) NOT NULL,
  `te_unit_price_other` int(11) NOT NULL,
  `te_period_contract` int(11) NOT NULL,
  `te_create_by` int(11) NOT NULL,
  `te_create_date` date NOT NULL,
  `te_mod_by` int(11) NOT NULL,
  `te_mod_date` date NOT NULL,
  PRIMARY KEY (`te_id`),
  KEY `te_contract_id` (`te_contract_id`),
  KEY `te_supplier_id` (`te_supplier_id`),
  KEY `te_supplier_contact_id` (`te_supplier_contact_id`),
  KEY `te_createBy` (`te_create_by`,`te_mod_by`),
  KEY `te_modBy` (`te_mod_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tender`
--

INSERT INTO `tender` (`te_id`, `te_contract_id`, `te_supplier_id`, `te_supplier_contact_id`, `te_date_quote_expires`, `te_txt_response`, `te_std_charge_day`, `te_std_charge_night`, `te_std_charge_other`, `te_unit_price_day`, `te_unit_price_night`, `te_unit_price_other`, `te_period_contract`, `te_create_by`, `te_create_date`, `te_mod_by`, `te_mod_date`) VALUES
(1, 1, 1, 1, '2011-05-18', '', 0, 0, 0, 0, 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 2, 2, '2011-05-19', '', 0, 0, 0, 0, 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `us_id` int(11) NOT NULL AUTO_INCREMENT,
  `us_username` varchar(50) COLLATE utf8_bin NOT NULL,
  `us_password` varchar(32) COLLATE utf8_bin NOT NULL,
  `us_real_name` varchar(150) COLLATE utf8_bin NOT NULL,
  `us_role` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT 'agent',
  `us_client_name` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`us_id`),
  UNIQUE KEY `us_username` (`us_username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`us_id`, `us_username`, `us_password`, `us_real_name`, `us_role`, `us_client_name`) VALUES
(1, 'epounce', 'd9eff137f0b3a9de921d1a71d677cb54', 'Eddie', 'admin', ''),
(2, 'fred', '1a0444c50eaec3459b7d334c0540d3ff', 'Fred', 'agent', 'Kier'),
(3, 'a', '8dcd7cdf35189a2a19000f7b96ffd7e1', 'A User', 'user', ''),
(4, 'paul', 'e8533e46435b7f77eb8fa7efa6c3eba2', 'Paul', 'admin', ''),
(5, 'shaun', 'c929017c9d349fe0dd9fcf458ea6031d', 'Shaun', 'admin', ''),
(6, 'read', 'cc6d9e335510828bed902ddd09d73e0b', 'read', 'read', ''),
(7, 'meter', '5ac885cdd3c79c117c1f50f516293126', 'Meter Reading Loaded', 'meterReading', ''),
(8, 'user', '1d37302b83654d9aff99b58797a25580', 'User', 'user', ''),
(9, 'xyz', '', 'Not Valid', 'decline', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
