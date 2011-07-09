-- phpMyAdmin SQL Dump
-- version 3.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 03, 2011 at 02:06 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET FOREIGN_KEY_CHECKS=0;
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

--
-- Dumping data for table `client_address`
--

INSERT INTO `client_address` (`clientAddressId`, `clientId`, `clientAddressAddress1`, `clientAddressAddress2`, `clientAddressAddress3`, `clientAddressPostcode`, `clientAddressCreateBy`, `clientAddressCreateDate`, `clientAddressModBy`, `clientAddressModDate`) VALUES
(1, 1, 'client address 1 - 1', 'client address 1 - 2', 'client address 1 - 3', 'add1 - pc', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'client address 2 - 1', 'client address 2 - 2', 'client address 2 - 3', 'add2 - pc', 1, '2011-07-01', 1, '2011-07-01');

--
-- Dumping data for table `client_contact`
--

INSERT INTO `client_contact` (`clientContactId`, `clientId`, `clientContactType`, `clientContactName`, `clientAddressId`, `clientContactPhone`, `clientContactEmail`, `clientContactCreateBy`, `clientContactCreateDate`, `clientContactModBy`, `clientContactModDate`) VALUES
(1, 1, 'boss1', 'eddie', 1, '123123', 'email1', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'boss2', 'name2', 2, '234234', 'email2', 1, '2011-07-01', 1, '2011-07-01');

--
-- Dumping data for table `contract`
--

INSERT INTO `contract` (`contractId`, `contractType`, `contractStatus`, `contractDesc`, `tenderId`, `supplierContactId`, `contractDateStart`, `contractDateEnd`, `contractTxtTender`, `contractDocAnalysis`, `contractDocTermination`, `contractPeriodBillCust`, `contractPeriodCommission`, `contractUserIdAgent`, `contractCreateBy`, `contractCreateDate`, `contractModBy`, `contractModDate`) VALUES
(1, 'temp', '', '', 0, 0, '2011-06-01', '2012-05-31', '0', '0', '', 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 'new', '', '', 0, 0, '2011-06-01', '2012-05-31', '0', '0', '', 0, 0, 0, 1, '2011-07-01', 1, '2011-07-01');

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

--
-- Dumping data for table `meter_contract`
--

INSERT INTO `meter_contract` (`meterId`, `contractId`, `useEstimate`, `meterContractCreateBy`, `meterContractCreateDate`, `meterContractModBy`, `meterContractModDate`) VALUES
(1, 1, 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 0, 1, '2011-07-01', 1, '2011-07-01');

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

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`id`, `modified`, `lifetime`, `data`) VALUES
('geu73lm09h60s71q7odbdm6cm1', 1309660777, 864000, 'Zend_Auth|a:1:{s:7:"storage";O:16:"Power_Model_User":8:{s:10:"\0*\0_userId";i:3;s:8:"\0*\0_role";s:4:"user";s:12:"\0*\0_realName";s:6:"A User";s:12:"\0*\0_username";s:1:"a";s:12:"\0*\0_password";s:32:"8dcd7cdf35189a2a19000f7b96ffd7e1";s:16:"\0*\0_classMethods";a:16:{i:0;s:9:"getUserId";i:1;s:9:"setUserId";i:2;s:7:"getRole";i:3;s:7:"setRole";i:4;s:11:"getUsername";i:5;s:11:"setUsername";i:6;s:11:"getPassword";i:7;s:11:"setPassword";i:8;s:11:"getRealName";i:9;s:11:"setRealName";i:10;s:11:"__construct";i:11;s:5:"__set";i:12;s:5:"__get";i:13;s:6:"__call";i:14;s:10:"setOptions";i:15;s:7:"toArray";}s:14:"\0*\0_dateFormat";N;s:8:"\0*\0_vars";a:0:{}}}');

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

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplierId`, `supplierName`, `supplierAddress1`, `supplierAddress2`, `supplierAddress3`, `supplierPostcode`, `supplierPeriodCommission`, `supplierCreateBy`, `supplierCreateDate`, `supplierModBy`, `supplierModDate`) VALUES
(1, 'supplier 1', 's1 - add1', 's1 - add2', 's1 - add3', 's1-pc', 6, 1, '2011-07-01', 1, '2011-07-01'),
(2, 'supplier 2', 's2 - add1', 's2 - add2', 's2 - add3', 's2 - pc', 3, 1, '2011-07-01', 1, '2011-07-01');

--
-- Dumping data for table `supplier_contact`
--

INSERT INTO `supplier_contact` (`supplierContactId`, `supplierId`, `supplierContactName`, `supplierContactPhone`, `supplierContactEmail`, `supplierContactAddress1`, `supplierContactAddress2`, `supplierContactAddress3`, `supplierContactPostcode`, `supplierContactCreateBy`, `supplierContactCreateDate`, `supplierContactModBy`, `supplierContactModDate`) VALUES
(1, 1, 'c1 - name', 'c1 - phone', 'c1 - email', 'c1 - add1', 'c1 - add2', 'c1 - add3', 'c1 - pc', 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 'c2 - namee', 'c2 - phone', 'c2 - email', 'c2 - add1', 'c2 - add2', 'c2 - add3', 'c2 - pc', 1, '2011-07-01', 1, '2011-07-01');

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

--
-- Dumping data for table `tender`
--

INSERT INTO `tender` (`tenderId`, `contractId`, `supplierId`, `supplierContactId`, `tenderDateQuoteExpires`, `tenderTxtResponse`, `tenderStdChargeDay`, `tenderStdChargeNight`, `tenderStdChargeOther`, `tenderUnitPriceDay`, `tenderUnitPriceNight`, `tenderUnitPriceOther`, `tenderPeriodContract`, `tenderCreateBy`, `tenderCreateDate`, `tenderModBy`, `tenderModDate`) VALUES
(1, 1, 1, 1, '2011-05-18', '', 0, 0, 0, '0.00', '0.00', '0.00', 0, 1, '2011-07-01', 1, '2011-07-01'),
(2, 2, 2, 2, '2011-05-19', '', 0, 0, 0, '0.00', '0.00', '0.00', 0, 1, '2011-07-01', 1, '2011-07-01');

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
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
