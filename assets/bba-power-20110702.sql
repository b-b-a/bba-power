-- phpMyAdmin SQL Dump
-- version 3.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 03, 2011 at 04:26 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
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
-- Creation: Jul 03, 2011 at 03:18 PM
--

CREATE TABLE IF NOT EXISTS `client` (
  `clientId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `clientName` varchar(50) NOT NULL,
  `clientDesc` varchar(255) NOT NULL,
  `clientDocLoa` int(11) unsigned NOT NULL DEFAULT '0',
  `clientDateExpiryLoa` date NOT NULL DEFAULT '2011-01-01',
  `clientCreateBy` int(11) unsigned NOT NULL,
  `clientCreateDate` date NOT NULL,
  `clientModBy` int(11) unsigned NOT NULL,
  `clientModDate` date NOT NULL,
  PRIMARY KEY (`clientId`),
  KEY `clientModBy` (`clientModBy`),
  KEY `clientCreateBy` (`clientCreateBy`,`clientModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- RELATIONS FOR TABLE `client`:
--   `clientCreateBy`
--       `users` -> `userId`
--   `clientModBy`
--       `users` -> `userId`
--

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`clientId`, `clientName`, `clientDesc`, `clientDocLoa`, `clientDateExpiryLoa`, `clientCreateBy`, `clientCreateDate`, `clientModBy`, `clientModDate`) VALUES
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
-- Creation: Jul 03, 2011 at 03:19 PM
--

CREATE TABLE IF NOT EXISTS `client_address` (
  `clientAddressId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `clientId` int(11) unsigned NOT NULL,
  `clientAddressAddress1` varchar(50) NOT NULL,
  `clientAddressAddress2` varchar(50) NOT NULL,
  `clientAddressAddress3` varchar(50) NOT NULL,
  `clientAddressPostcode` varchar(10) NOT NULL,
  `clientAddressCreateBy` int(11) unsigned NOT NULL,
  `clientAddressCreateDate` date NOT NULL,
  `clientAddressModBy` int(11) unsigned NOT NULL,
  `clientAddressModDate` date NOT NULL,
  PRIMARY KEY (`clientAddressId`),
  KEY `clientAddressClientId` (`clientId`),
  KEY `clientAddressCreateBy` (`clientAddressCreateBy`,`clientAddressModBy`),
  KEY `clientAddressModBy` (`clientAddressModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- RELATIONS FOR TABLE `client_address`:
--   `clientAddressCreateBy`
--       `users` -> `userId`
--   `clientAddressModBy`
--       `users` -> `userId`
--   `clientId`
--       `client` -> `clientId`
--

--
-- Dumping data for table `client_address`
--

INSERT INTO `client_address` (`clientAddressId`, `clientId`, `clientAddressAddress1`, `clientAddressAddress2`, `clientAddressAddress3`, `clientAddressPostcode`, `clientAddressCreateBy`, `clientAddressCreateDate`, `clientAddressModBy`, `clientAddressModDate`) VALUES
(1, 1, 'client address 1 - 1', 'client address 1 - 2', 'client address 1 - 3', 'add1 - pc', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'client address 2 - 1', 'client address 2 - 2', 'client address 2 - 3', 'add2 - pc', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `client_contact`
--
-- Creation: Jul 03, 2011 at 03:19 PM
--

CREATE TABLE IF NOT EXISTS `client_contact` (
  `clientContactId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `clientId` int(11) unsigned NOT NULL,
  `clientContactType` varchar(8) NOT NULL,
  `clientContactName` varchar(50) NOT NULL,
  `clientAddressId` int(11) unsigned NOT NULL,
  `clientContactPhone` varchar(50) NOT NULL,
  `clientContactEmail` varchar(100) NOT NULL,
  `clientContactCreateBy` int(11) unsigned NOT NULL,
  `clientContactCreateDate` date NOT NULL,
  `clientContactModBy` int(11) unsigned NOT NULL,
  `clientContactModDate` date NOT NULL,
  PRIMARY KEY (`clientContactId`),
  KEY `clientContactClientId` (`clientId`),
  KEY `clientContactClientAddressId` (`clientAddressId`),
  KEY `clientContactCreateBy` (`clientContactCreateBy`,`clientContactModBy`),
  KEY `clientContactModBy` (`clientContactModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- RELATIONS FOR TABLE `client_contact`:
--   `clientAddressId`
--       `client_address` -> `clientAddressId`
--   `clientContactCreateBy`
--       `users` -> `userId`
--   `clientContactModBy`
--       `users` -> `userId`
--   `clientId`
--       `client` -> `clientId`
--

--
-- Dumping data for table `client_contact`
--

INSERT INTO `client_contact` (`clientContactId`, `clientId`, `clientContactType`, `clientContactName`, `clientAddressId`, `clientContactPhone`, `clientContactEmail`, `clientContactCreateBy`, `clientContactCreateDate`, `clientContactModBy`, `clientContactModDate`) VALUES
(1, 1, 'boss1', 'eddie', 1, '123123', 'email1', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'boss2', 'name2', 2, '234234', 'email2', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--
-- Creation: Jul 03, 2011 at 03:20 PM
--

CREATE TABLE IF NOT EXISTS `contract` (
  `contractId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contractType` varchar(8) NOT NULL,
  `contractStatus` varchar(16) NOT NULL,
  `contractDesc` varchar(256) NOT NULL,
  `tenderId` int(11) unsigned NOT NULL DEFAULT '0',
  `supplierContactId` int(11) unsigned NOT NULL DEFAULT '0',
  `contractDateStart` date NOT NULL,
  `contractDateEnd` date NOT NULL,
  `contractTxtTender` mediumtext NOT NULL,
  `contractDocAnalysis` varchar(64) NOT NULL,
  `contractDocTermination` varchar(64) NOT NULL,
  `contractPeriodBillCust` int(11) unsigned NOT NULL DEFAULT '0',
  `contractPeriodCommission` int(11) unsigned NOT NULL DEFAULT '0',
  `contractUserIdAgent` int(11) unsigned NOT NULL,
  `contractCreateBy` int(11) unsigned NOT NULL,
  `contractCreateDate` date NOT NULL,
  `contractModBy` int(11) unsigned NOT NULL,
  `contractModDate` date NOT NULL,
  PRIMARY KEY (`contractId`),
  KEY `contractUserIdAgent` (`contractUserIdAgent`),
  KEY `contractCreateBy` (`contractCreateBy`,`contractModBy`),
  KEY `contractModBy` (`contractModBy`),
  KEY `tenderId` (`tenderId`),
  KEY `supplierContactId` (`supplierContactId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- RELATIONS FOR TABLE `contract`:
--   `contractCreateBy`
--       `users` -> `userId`
--   `contractModBy`
--       `users` -> `userId`
--   `supplierContactId`
--       `supplier_contact` -> `supplierContactId`
--   `tenderId`
--       `tender` -> `tenderId`
--

--
-- Dumping data for table `contract`
--

INSERT INTO `contract` (`contractId`, `contractType`, `contractStatus`, `contractDesc`, `tenderId`, `supplierContactId`, `contractDateStart`, `contractDateEnd`, `contractTxtTender`, `contractDocAnalysis`, `contractDocTermination`, `contractPeriodBillCust`, `contractPeriodCommission`, `contractUserIdAgent`, `contractCreateBy`, `contractCreateDate`, `contractModBy`, `contractModDate`) VALUES
(1, 'temp', '', '', 0, 0, '2011-06-01', '2012-05-31', '0', '0', '', 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 'new', '', '', 0, 0, '2011-06-01', '2012-05-31', '0', '0', '', 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `contract_site`
--
-- Creation: Jul 03, 2011 at 03:24 PM
--

CREATE TABLE IF NOT EXISTS `contract_site` (
  `contractId` int(11) unsigned NOT NULL,
  `siteId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`contractId`,`siteId`),
  KEY `contractSiteContractId` (`contractId`),
  KEY `contractSiteSiteId` (`siteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONS FOR TABLE `contract_site`:
--   `siteId`
--       `site` -> `siteId`
--   `contractId`
--       `contract` -> `contractId`
--

--
-- Dumping data for table `contract_site`
--

INSERT INTO `contract_site` (`contractId`, `siteId`) VALUES
(1, 15),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `meter`
--
-- Creation: Jul 03, 2011 at 03:20 PM
--

CREATE TABLE IF NOT EXISTS `meter` (
  `meterId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `siteId` int(11) unsigned NOT NULL,
  `meterType` varchar(8) NOT NULL,
  `meterNo` varchar(25) NOT NULL,
  `meterDateInstall` date NOT NULL,
  `meterDateRemoved` date NOT NULL,
  `meterPipeSize` varchar(32) NOT NULL,
  `meterCreateBy` int(11) unsigned NOT NULL,
  `meterCreateDate` date NOT NULL,
  `meterModBy` int(11) unsigned NOT NULL,
  `meterModDate` date NOT NULL,
  PRIMARY KEY (`meterId`),
  KEY `meterCreateBy` (`meterCreateBy`,`meterModBy`),
  KEY `meterModBy` (`meterModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- RELATIONS FOR TABLE `meter`:
--   `meterCreateBy`
--       `users` -> `userId`
--   `meterModBy`
--       `users` -> `userId`
--

--
-- Dumping data for table `meter`
--

INSERT INTO `meter` (`meterId`, `siteId`, `meterType`, `meterNo`, `meterDateInstall`, `meterDateRemoved`, `meterPipeSize`, `meterCreateBy`, `meterCreateDate`, `meterModBy`, `meterModDate`) VALUES
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
-- Creation: Jul 03, 2011 at 03:21 PM
--

CREATE TABLE IF NOT EXISTS `meter_contract` (
  `meterId` int(11) unsigned NOT NULL,
  `contractId` int(11) unsigned NOT NULL,
  `useEstimate` int(11) unsigned NOT NULL,
  `meterContractCreateBy` int(11) unsigned NOT NULL,
  `meterContractCreateDate` date NOT NULL,
  `meterContractModBy` int(11) unsigned NOT NULL,
  `meterContractModDate` date NOT NULL,
  PRIMARY KEY (`meterId`,`contractId`),
  KEY `meterContractMeterId` (`meterId`),
  KEY `meterContractContractId` (`contractId`),
  KEY `meterContractCreateBy` (`meterContractCreateBy`,`meterContractModBy`),
  KEY `meterContractModBy` (`meterContractModBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONS FOR TABLE `meter_contract`:
--   `contractId`
--       `contract` -> `contractId`
--   `meterContractCreateBy`
--       `users` -> `userId`
--   `meterContractModBy`
--       `users` -> `userId`
--

--
-- Dumping data for table `meter_contract`
--

INSERT INTO `meter_contract` (`meterId`, `contractId`, `useEstimate`, `meterContractCreateBy`, `meterContractCreateDate`, `meterContractModBy`, `meterContractModDate`) VALUES
(1, 1, 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 0, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `reading`
--
-- Creation: Jul 03, 2011 at 03:21 PM
--

CREATE TABLE IF NOT EXISTS `reading` (
  `readingId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `meterId` int(11) unsigned NOT NULL,
  `readingDateBill` date NOT NULL,
  `readingDateReading` date NOT NULL,
  `readingValueDay` int(11) unsigned NOT NULL,
  `readingValueNight` int(11) unsigned NOT NULL,
  `readingValueOther` int(11) unsigned NOT NULL,
  `readingType` varchar(8) NOT NULL,
  `readingCreateBy` int(11) unsigned NOT NULL,
  `readingCreateDate` date NOT NULL,
  `readingModBy` int(11) unsigned NOT NULL,
  `readingModDate` date NOT NULL,
  PRIMARY KEY (`readingId`),
  KEY `readingMeterId` (`meterId`),
  KEY `readingCreateBy` (`readingCreateBy`,`readingModBy`),
  KEY `readingModBy` (`readingModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- RELATIONS FOR TABLE `reading`:
--   `meterId`
--       `meter` -> `meterId`
--   `readingCreateBy`
--       `users` -> `userId`
--   `readingModBy`
--       `users` -> `userId`
--

--
-- Dumping data for table `reading`
--

INSERT INTO `reading` (`readingId`, `meterId`, `readingDateBill`, `readingDateReading`, `readingValueDay`, `readingValueNight`, `readingValueOther`, `readingType`, `readingCreateBy`, `readingCreateDate`, `readingModBy`, `readingModDate`) VALUES
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
-- Creation: Jul 03, 2011 at 01:30 AM
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` char(32) NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `site`
--
-- Creation: Jul 03, 2011 at 03:21 PM
--

CREATE TABLE IF NOT EXISTS `site` (
  `siteId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `clientId` int(11) unsigned NOT NULL,
  `clientAddressId` int(11) unsigned NOT NULL,
  `siteClientAddressIdBill` int(11) unsigned NOT NULL,
  `clientContactId` int(11) unsigned NOT NULL,
  `siteCreateBy` int(11) unsigned NOT NULL,
  `siteCreateDate` date NOT NULL,
  `siteModBy` int(11) unsigned NOT NULL,
  `siteModDate` date NOT NULL,
  PRIMARY KEY (`siteId`),
  KEY `siteClientId` (`clientId`),
  KEY `siteClientAddressId` (`clientAddressId`),
  KEY `siteClientAddressIdBill` (`siteClientAddressIdBill`),
  KEY `siteClientContactId` (`clientContactId`),
  KEY `siteCreateBy` (`siteCreateBy`,`siteModBy`),
  KEY `siteModBy` (`siteModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- RELATIONS FOR TABLE `site`:
--   `clientAddressId`
--       `client_address` -> `clientAddressId`
--   `clientContactId`
--       `client_contact` -> `clientContactId`
--   `clientId`
--       `client` -> `clientId`
--   `siteCreateBy`
--       `users` -> `userId`
--   `siteModBy`
--       `users` -> `userId`
--

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`siteId`, `clientId`, `clientAddressId`, `siteClientAddressIdBill`, `clientContactId`, `siteCreateBy`, `siteCreateDate`, `siteModBy`, `siteModDate`) VALUES
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
-- Creation: Jul 03, 2011 at 03:22 PM
--

CREATE TABLE IF NOT EXISTS `supplier` (
  `supplierId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplierName` varchar(50) NOT NULL,
  `supplierAddress1` varchar(50) NOT NULL,
  `supplierAddress2` varchar(50) NOT NULL,
  `supplierAddress3` varchar(50) NOT NULL,
  `supplierPostcode` varchar(10) NOT NULL,
  `supplierPeriodCommission` int(11) unsigned NOT NULL DEFAULT '0',
  `supplierCreateBy` int(11) unsigned NOT NULL,
  `supplierCreateDate` date NOT NULL,
  `supplierModBy` int(11) unsigned NOT NULL,
  `supplierModDate` date NOT NULL,
  PRIMARY KEY (`supplierId`),
  KEY `supplierCreateBy` (`supplierCreateBy`,`supplierModBy`),
  KEY `supplierModBy` (`supplierModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- RELATIONS FOR TABLE `supplier`:
--   `supplierCreateBy`
--       `users` -> `userId`
--   `supplierModBy`
--       `users` -> `userId`
--

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplierId`, `supplierName`, `supplierAddress1`, `supplierAddress2`, `supplierAddress3`, `supplierPostcode`, `supplierPeriodCommission`, `supplierCreateBy`, `supplierCreateDate`, `supplierModBy`, `supplierModDate`) VALUES
(1, 'supplier 1', 's1 - add1', 's1 - add2', 's1 - add3', 's1-pc', 6, 1, '2011-07-01', 1, '2011-07-01'),
(2, 'supplier 2', 's2 - add1', 's2 - add2', 's2 - add3', 's2 - pc', 3, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_contact`
--
-- Creation: Jul 03, 2011 at 03:22 PM
--

CREATE TABLE IF NOT EXISTS `supplier_contact` (
  `supplierContactId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplierId` int(11) unsigned NOT NULL,
  `supplierContactName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `supplierContactPhone` varchar(50) CHARACTER SET utf8 NOT NULL,
  `supplierContactEmail` varchar(100) CHARACTER SET utf8 NOT NULL,
  `supplierContactAddress1` varchar(50) CHARACTER SET utf8 NOT NULL,
  `supplierContactAddress2` varchar(50) CHARACTER SET utf8 NOT NULL,
  `supplierContactAddress3` varchar(50) CHARACTER SET utf8 NOT NULL,
  `supplierContactPostcode` varchar(10) CHARACTER SET utf8 NOT NULL,
  `supplierContactCreateBy` int(11) unsigned NOT NULL,
  `supplierContactCreateDate` date NOT NULL,
  `supplierContactModBy` int(11) unsigned NOT NULL,
  `supplierContactModDate` date NOT NULL,
  PRIMARY KEY (`supplierContactId`),
  KEY `supplierContactSupplierId` (`supplierId`),
  KEY `supplierContactCreateBy` (`supplierContactCreateBy`,`supplierContactModBy`),
  KEY `supplierContactModBy` (`supplierContactModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_estonian_ci AUTO_INCREMENT=3 ;

--
-- RELATIONS FOR TABLE `supplier_contact`:
--   `supplierContactCreateBy`
--       `users` -> `userId`
--   `supplierContactModBy`
--       `users` -> `userId`
--   `supplierId`
--       `supplier` -> `supplierId`
--

--
-- Dumping data for table `supplier_contact`
--

INSERT INTO `supplier_contact` (`supplierContactId`, `supplierId`, `supplierContactName`, `supplierContactPhone`, `supplierContactEmail`, `supplierContactAddress1`, `supplierContactAddress2`, `supplierContactAddress3`, `supplierContactPostcode`, `supplierContactCreateBy`, `supplierContactCreateDate`, `supplierContactModBy`, `supplierContactModDate`) VALUES
(1, 1, 'c1 - name', 'c1 - phone', 'c1 - email', 'c1 - add1', 'c1 - add2', 'c1 - add3', 'c1 - pc', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'c2 - namee', 'c2 - phone', 'c2 - email', 'c2 - add1', 'c2 - add2', 'c2 - add3', 'c2 - pc', 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--
-- Creation: Jul 03, 2011 at 01:26 AM
--

CREATE TABLE IF NOT EXISTS `tables` (
  `tableId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tableName` char(32) NOT NULL,
  `tableKey` char(32) NOT NULL,
  `tableValue` char(32) NOT NULL,
  `tableSort` int(11) unsigned NOT NULL,
  PRIMARY KEY (`tableId`),
  KEY `tableName` (`tableName`),
  KEY `tableKey` (`tableKey`),
  KEY `tableSort` (`tableSort`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`tableId`, `tableName`, `tableKey`, `tableValue`, `tableSort`) VALUES
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
-- Creation: Jul 03, 2011 at 03:23 PM
--

CREATE TABLE IF NOT EXISTS `tender` (
  `tenderId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contractId` int(11) unsigned NOT NULL,
  `supplierId` int(11) unsigned NOT NULL,
  `supplierContactId` int(11) unsigned NOT NULL,
  `tenderDateQuoteExpires` date NOT NULL,
  `tenderTxtResponse` mediumtext NOT NULL,
  `tenderStdChargeDay` int(11) unsigned NOT NULL,
  `tenderStdChargeNight` int(11) unsigned NOT NULL,
  `tenderStdChargeOther` int(11) unsigned NOT NULL,
  `tenderUnitPriceDay` decimal(5,2) NOT NULL,
  `tenderUnitPriceNight` decimal(5,2) NOT NULL,
  `tenderUnitPriceOther` decimal(5,2) NOT NULL,
  `tenderPeriodContract` int(11) unsigned NOT NULL,
  `tenderCreateBy` int(11) unsigned NOT NULL,
  `tenderCreateDate` date NOT NULL,
  `tenderModBy` int(11) unsigned NOT NULL,
  `tenderModDate` date NOT NULL,
  PRIMARY KEY (`tenderId`),
  KEY `tenderContractId` (`contractId`),
  KEY `tenderSupplierId` (`supplierId`),
  KEY `tenderSupplierContactId` (`supplierContactId`),
  KEY `tenderCreateBy` (`tenderCreateBy`,`tenderModBy`),
  KEY `tenderModBy` (`tenderModBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- RELATIONS FOR TABLE `tender`:
--   `contractId`
--       `contract` -> `contractId`
--   `supplierContactId`
--       `supplier_contact` -> `supplierContactId`
--   `supplierId`
--       `supplier` -> `supplierId`
--   `tenderCreateBy`
--       `users` -> `userId`
--   `tenderModBy`
--       `users` -> `userId`
--

--
-- Dumping data for table `tender`
--

INSERT INTO `tender` (`tenderId`, `contractId`, `supplierId`, `supplierContactId`, `tenderDateQuoteExpires`, `tenderTxtResponse`, `tenderStdChargeDay`, `tenderStdChargeNight`, `tenderStdChargeOther`, `tenderUnitPriceDay`, `tenderUnitPriceNight`, `tenderUnitPriceOther`, `tenderPeriodContract`, `tenderCreateBy`, `tenderCreateDate`, `tenderModBy`, `tenderModDate`) VALUES
(1, 1, 1, 1, '2011-05-18', '', 0, 0, 0, '0.00', '0.00', '0.00', 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 2, 2, '2011-05-19', '', 0, 0, 0, '0.00', '0.00', '0.00', 0, 1, '2011-07-01', 1, '2011-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Jul 03, 2011 at 01:28 AM
--

CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `realName` varchar(150) NOT NULL,
  `role` varchar(16) NOT NULL DEFAULT 'agent',
  `clientName` varchar(64) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `usersUsername` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `password`, `realName`, `role`, `clientName`) VALUES
(1, 'epounce', 'd9eff137f0b3a9de921d1a71d677cb54', 'Eddie', 'admin', ''),
(2, 'fred', '1a0444c50eaec3459b7d334c0540d3ff', 'Fred', 'agent', 'Kier'),
(3, 'a', '8dcd7cdf35189a2a19000f7b96ffd7e1', 'A User', 'user', ''),
(4, 'paul', 'e8533e46435b7f77eb8fa7efa6c3eba2', 'Paul', 'admin', ''),
(5, 'shaun', 'c929017c9d349fe0dd9fcf458ea6031d', 'Shaun', 'admin', ''),
(6, 'read', 'cc6d9e335510828bed902ddd09d73e0b', 'read', 'read', ''),
(7, 'meter', '5ac885cdd3c79c117c1f50f516293126', 'Meter Reading Loaded', 'meterReading', ''),
(8, 'user', '1d37302b83654d9aff99b58797a25580', 'User', 'user', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_2` FOREIGN KEY (`clientModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`clientCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `client_address`
--
ALTER TABLE `client_address`
  ADD CONSTRAINT `client_address_ibfk_3` FOREIGN KEY (`clientAddressModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `client_address_ibfk_1` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`),
  ADD CONSTRAINT `client_address_ibfk_2` FOREIGN KEY (`clientAddressCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `client_contact`
--
ALTER TABLE `client_contact`
  ADD CONSTRAINT `client_contact_ibfk_8` FOREIGN KEY (`clientContactModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `client_contact_ibfk_5` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`),
  ADD CONSTRAINT `client_contact_ibfk_6` FOREIGN KEY (`clientAddressId`) REFERENCES `client_address` (`clientAddressId`),
  ADD CONSTRAINT `client_contact_ibfk_7` FOREIGN KEY (`clientContactCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`contractModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`contractCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `contract_site`
--
ALTER TABLE `contract_site`
  ADD CONSTRAINT `contract_site_ibfk_2` FOREIGN KEY (`siteId`) REFERENCES `site` (`siteId`),
  ADD CONSTRAINT `contract_site_ibfk_1` FOREIGN KEY (`contractId`) REFERENCES `contract` (`contractId`);

--
-- Constraints for table `meter`
--
ALTER TABLE `meter`
  ADD CONSTRAINT `meter_ibfk_2` FOREIGN KEY (`meterModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `meter_ibfk_1` FOREIGN KEY (`meterCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `meter_contract`
--
ALTER TABLE `meter_contract`
  ADD CONSTRAINT `meter_contract_ibfk_3` FOREIGN KEY (`meterContractModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `meter_contract_ibfk_1` FOREIGN KEY (`contractId`) REFERENCES `contract` (`contractId`),
  ADD CONSTRAINT `meter_contract_ibfk_2` FOREIGN KEY (`meterContractCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `reading`
--
ALTER TABLE `reading`
  ADD CONSTRAINT `reading_ibfk_3` FOREIGN KEY (`readingModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `reading_ibfk_1` FOREIGN KEY (`meterId`) REFERENCES `meter` (`meterId`),
  ADD CONSTRAINT `reading_ibfk_2` FOREIGN KEY (`readingCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `site`
--
ALTER TABLE `site`
  ADD CONSTRAINT `site_ibfk_8` FOREIGN KEY (`siteModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `site_ibfk_4` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`),
  ADD CONSTRAINT `site_ibfk_5` FOREIGN KEY (`clientAddressId`) REFERENCES `client_address` (`clientAddressId`),
  ADD CONSTRAINT `site_ibfk_6` FOREIGN KEY (`clientContactId`) REFERENCES `client_contact` (`clientContactId`),
  ADD CONSTRAINT `site_ibfk_7` FOREIGN KEY (`siteCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_2` FOREIGN KEY (`supplierModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`supplierCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `supplier_contact`
--
ALTER TABLE `supplier_contact`
  ADD CONSTRAINT `supplier_contact_ibfk_3` FOREIGN KEY (`supplierContactModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `supplier_contact_ibfk_1` FOREIGN KEY (`supplierId`) REFERENCES `supplier` (`supplierId`),
  ADD CONSTRAINT `supplier_contact_ibfk_2` FOREIGN KEY (`supplierContactCreateBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `tender`
--
ALTER TABLE `tender`
  ADD CONSTRAINT `tender_ibfk_13` FOREIGN KEY (`tenderModBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `tender_ibfk_10` FOREIGN KEY (`supplierId`) REFERENCES `supplier` (`supplierId`),
  ADD CONSTRAINT `tender_ibfk_11` FOREIGN KEY (`supplierContactId`) REFERENCES `supplier_contact` (`supplierContactId`),
  ADD CONSTRAINT `tender_ibfk_12` FOREIGN KEY (`tenderCreateBy`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `tender_ibfk_9` FOREIGN KEY (`contractId`) REFERENCES `contract` (`contractId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
