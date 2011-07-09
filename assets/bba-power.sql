-- phpMyAdmin SQL Dump
-- version 3.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 03, 2011 at 12:50 AM
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
-- Creation: Jul 02, 2011 at 11:23 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `client`:
--   `clientCreateBy`
--       `users` -> `usersId`
--   `clientModBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_address`
--
-- Creation: Jul 02, 2011 at 11:25 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `client_address`:
--   `clientAddressModBy`
--       `users` -> `usersId`
--   `clientId`
--       `client` -> `clientId`
--   `clientAddressCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_contact`
--
-- Creation: Jul 02, 2011 at 11:26 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `client_contact`:
--   `clientContactModBy`
--       `users` -> `usersId`
--   `clientId`
--       `client` -> `clientId`
--   `clientAddressId`
--       `client_address` -> `clientAddressId`
--   `clientContactCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--
-- Creation: Jul 02, 2011 at 11:41 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `contract`:
--   `contractModBy`
--       `users` -> `usersId`
--   `tenderId`
--       `tender` -> `tenderId`
--   `supplierContactId`
--       `supplier_contact` -> `supplierId`
--   `contractCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `contract_site`
--
-- Creation: Jul 02, 2011 at 11:42 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `meter`
--
-- Creation: Jul 02, 2011 at 11:42 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `meter`:
--   `meterModBy`
--       `users` -> `usersId`
--   `meterCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `meter_contract`
--
-- Creation: Jul 02, 2011 at 11:43 PM
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
--   `meterContractModBy`
--       `users` -> `usersId`
--   `contractId`
--       `contract` -> `contractId`
--   `meterContractCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `reading`
--
-- Creation: Jul 02, 2011 at 11:44 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `reading`:
--   `readingModBy`
--       `users` -> `usersId`
--   `meterId`
--       `meter` -> `meterId`
--   `readingCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `site`
--
-- Creation: Jul 02, 2011 at 11:45 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `site`:
--   `siteModBy`
--       `users` -> `usersId`
--   `clientId`
--       `client` -> `clientId`
--   `clientAddressId`
--       `client_address` -> `clientAddressId`
--   `clientContactId`
--       `client_contact` -> `clientId`
--   `siteCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--
-- Creation: Jul 02, 2011 at 11:46 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `supplier`:
--   `supplierModBy`
--       `users` -> `usersId`
--   `supplierCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `supplier_contact`
--
-- Creation: Jul 02, 2011 at 11:46 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_estonian_ci AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `supplier_contact`:
--   `supplierContactModBy`
--       `users` -> `usersId`
--   `supplierId`
--       `supplier` -> `supplierId`
--   `supplierContactCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--
-- Creation: Jul 02, 2011 at 11:11 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tender`
--
-- Creation: Jul 02, 2011 at 11:48 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `tender`:
--   `tenderModBy`
--       `users` -> `usersId`
--   `contractId`
--       `contract` -> `contractId`
--   `supplierId`
--       `supplier` -> `supplierId`
--   `supplierContactId`
--       `supplier_contact` -> `supplierContactId`
--   `tenderCreateBy`
--       `users` -> `usersId`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Jul 02, 2011 at 11:11 PM
--

CREATE TABLE IF NOT EXISTS `users` (
  `usersId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usersUsername` varchar(50) NOT NULL,
  `usersPassword` varchar(32) NOT NULL,
  `usersRealName` varchar(150) NOT NULL,
  `usersRole` varchar(16) NOT NULL DEFAULT 'agent',
  `usersClientName` varchar(64) NOT NULL,
  PRIMARY KEY (`usersId`),
  UNIQUE KEY `usersUsername` (`usersUsername`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`clientCreateBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `client_ibfk_2` FOREIGN KEY (`clientModBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `client_address`
--
ALTER TABLE `client_address`
  ADD CONSTRAINT `client_address_ibfk_3` FOREIGN KEY (`clientAddressModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `client_address_ibfk_1` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`),
  ADD CONSTRAINT `client_address_ibfk_2` FOREIGN KEY (`clientAddressCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `client_contact`
--
ALTER TABLE `client_contact`
  ADD CONSTRAINT `client_contact_ibfk_4` FOREIGN KEY (`clientContactModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `client_contact_ibfk_1` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`),
  ADD CONSTRAINT `client_contact_ibfk_2` FOREIGN KEY (`clientAddressId`) REFERENCES `client_address` (`clientAddressId`),
  ADD CONSTRAINT `client_contact_ibfk_3` FOREIGN KEY (`clientContactCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_13` FOREIGN KEY (`contractModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `contract_ibfk_10` FOREIGN KEY (`tenderId`) REFERENCES `tender` (`tenderId`),
  ADD CONSTRAINT `contract_ibfk_11` FOREIGN KEY (`supplierContactId`) REFERENCES `supplier_contact` (`supplierId`),
  ADD CONSTRAINT `contract_ibfk_12` FOREIGN KEY (`contractCreateBy`) REFERENCES `users` (`usersId`);

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
  ADD CONSTRAINT `meter_ibfk_2` FOREIGN KEY (`meterModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `meter_ibfk_1` FOREIGN KEY (`meterCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `meter_contract`
--
ALTER TABLE `meter_contract`
  ADD CONSTRAINT `meter_contract_ibfk_3` FOREIGN KEY (`meterContractModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `meter_contract_ibfk_1` FOREIGN KEY (`contractId`) REFERENCES `contract` (`contractId`),
  ADD CONSTRAINT `meter_contract_ibfk_2` FOREIGN KEY (`meterContractCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `reading`
--
ALTER TABLE `reading`
  ADD CONSTRAINT `reading_ibfk_3` FOREIGN KEY (`readingModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `reading_ibfk_1` FOREIGN KEY (`meterId`) REFERENCES `meter` (`meterId`),
  ADD CONSTRAINT `reading_ibfk_2` FOREIGN KEY (`readingCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `site`
--
ALTER TABLE `site`
  ADD CONSTRAINT `site_ibfk_5` FOREIGN KEY (`siteModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `site_ibfk_1` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`),
  ADD CONSTRAINT `site_ibfk_2` FOREIGN KEY (`clientAddressId`) REFERENCES `client_address` (`clientAddressId`),
  ADD CONSTRAINT `site_ibfk_3` FOREIGN KEY (`clientContactId`) REFERENCES `client_contact` (`clientId`),
  ADD CONSTRAINT `site_ibfk_4` FOREIGN KEY (`siteCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_2` FOREIGN KEY (`supplierModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`supplierCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `supplier_contact`
--
ALTER TABLE `supplier_contact`
  ADD CONSTRAINT `supplier_contact_ibfk_3` FOREIGN KEY (`supplierContactModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `supplier_contact_ibfk_1` FOREIGN KEY (`supplierId`) REFERENCES `supplier` (`supplierId`),
  ADD CONSTRAINT `supplier_contact_ibfk_2` FOREIGN KEY (`supplierContactCreateBy`) REFERENCES `users` (`usersId`);

--
-- Constraints for table `tender`
--
ALTER TABLE `tender`
  ADD CONSTRAINT `tender_ibfk_5` FOREIGN KEY (`tenderModBy`) REFERENCES `users` (`usersId`),
  ADD CONSTRAINT `tender_ibfk_1` FOREIGN KEY (`contractId`) REFERENCES `contract` (`contractId`),
  ADD CONSTRAINT `tender_ibfk_2` FOREIGN KEY (`supplierId`) REFERENCES `supplier` (`supplierId`),
  ADD CONSTRAINT `tender_ibfk_3` FOREIGN KEY (`supplierContactId`) REFERENCES `supplier_contact` (`supplierContactId`),
  ADD CONSTRAINT `tender_ibfk_4` FOREIGN KEY (`tenderCreateBy`) REFERENCES `users` (`usersId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
