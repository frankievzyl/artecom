-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2018 at 11:37 AM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `artack_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attribute`
--

CREATE TABLE `attribute` (
  `AttributeID` tinyint(4) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Description` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `attributemeasurement`
--

CREATE TABLE `attributemeasurement` (
  `Amount` decimal(6,2) DEFAULT NULL,
  `BarCode` char(13) NOT NULL,
  `AttributeID` tinyint(4) NOT NULL,
  `UnitID` tinyint(4) DEFAULT NULL,
  `WAmountID` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `BrandID` tinyint(4) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Logo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `classification`
--

CREATE TABLE `classification` (
  `ClassID` tinyint(4) NOT NULL,
  `BarCode` char(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `colour`
--

CREATE TABLE `colour` (
  `ColourID` smallint(6) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `ColourCode` varchar(10) DEFAULT NULL,
  `ColourHex` char(6) NOT NULL DEFAULT 'FF0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CustomerID` mediumint(9) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `MobileNumber` varchar(15),
  `Address` varchar(50),
  `City` varchar(20) DEFAULT 'Cape Town',
  `PostCode` varchar(10),
  `Country` varchar(30) DEFAULT 'South Africa',
  `RegionState` varchar(40) DEFAULT 'Western Cape',
  `Password` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customerproduct`
--

CREATE TABLE `customerproduct` (
  `Quantity` tinyint(4) NOT NULL DEFAULT '1',
  `CustomerID` mediumint(9) NOT NULL,
  `BarCode` char(13) NOT NULL,
  `TransactionID` int(11) DEFAULT NULL,
  `ReviewID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `myclass`
--

CREATE TABLE `myclass` (
  `ClassID` tinyint(4) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `ClassLevel` tinyint(4) NOT NULL,
  `SuperClass` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `myrange`
--

CREATE TABLE `myrange` (
  `RangeID` tinyint(4) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `ColourChart` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mytransaction`
--

CREATE TABLE `mytransaction` (
  `TransactionID` int(11) NOT NULL,
  `TotalAmount` decimal(8,2) NOT NULL,
  `ShipAddress` varchar(150) NOT NULL,
  `Payment` enum('Credit Card','EFT') NOT NULL DEFAULT 'Credit Card',
  `Delivery` enum('Post Office CtC','Cape Town Courier','Courier DtD','Pick up') NOT NULL DEFAULT 'Courier DtD',
  `TrDateTime` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `BarCode` char(13) NOT NULL,
  `Price` decimal(8,2) NOT NULL,
  `StockLevel` tinyint(4) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Description` tinytext,
  `Image` varchar(100) DEFAULT NULL,
  `SpecialPrice` decimal(8,2),
  `SpecialEnd` datetime,
  `ColourID` smallint(6) DEFAULT NULL,
  `BrandID` tinyint(4) DEFAULT NULL,
  `RangeID` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `ReviewID` int(11) NOT NULL,
  `Text` tinytext,
  `Rating` tinyint(4) NOT NULL DEFAULT '0',
  `RvDateTime` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `UnitID` tinyint(4) NOT NULL,
  `Symbol` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `VoucherCode` varchar(10) NOT NULL,
  `Amount` decimal(6,2) NOT NULL,
  `Claimant` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wordamount`
--

CREATE TABLE `wordamount` (
  `WAmountID` tinyint(4) NOT NULL,
  `Name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attribute`
--
ALTER TABLE `attribute`
  ADD PRIMARY KEY (`AttributeID`);

--
-- Indexes for table `attributemeasurement`
--
ALTER TABLE `attributemeasurement`
  ADD KEY `BarCode` (`BarCode`),
  ADD KEY `AttributeID` (`AttributeID`),
  ADD KEY `UnitID` (`UnitID`),
  ADD KEY `WAmountID` (`WAmountID`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`BrandID`);

--
-- Indexes for table `classification`
--
ALTER TABLE `classification`
  ADD KEY `ClassID` (`ClassID`),
  ADD KEY `BarCode` (`BarCode`);

--
-- Indexes for table `colour`
--
ALTER TABLE `colour`
  ADD PRIMARY KEY (`ColourID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `customerproduct`
--
ALTER TABLE `customerproduct`
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `BarCode` (`BarCode`),
  ADD KEY `TransactionID` (`TransactionID`),
  ADD KEY `ReviewID` (`ReviewID`);

--
-- Indexes for table `myclass`
--
ALTER TABLE `myclass`
  ADD PRIMARY KEY (`ClassID`),
  ADD KEY `SuperClass` (`SuperClass`);

--
-- Indexes for table `myrange`
--
ALTER TABLE `myrange`
  ADD PRIMARY KEY (`RangeID`);

--
-- Indexes for table `mytransaction`
--
ALTER TABLE `mytransaction`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `TransactionDT` (`TrDateTime`) USING HASH;

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`BarCode`),
  ADD UNIQUE KEY `UPName` (`Name`),
  ADD KEY `ColourID` (`ColourID`),
  ADD KEY `BrandID` (`BrandID`),
  ADD KEY `RangeID` (`RangeID`),
  ADD KEY `ProductPrice` (`Price`) USING HASH,
  ADD KEY `ProductName` (`Name`) USING BTREE;

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`ReviewID`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`UnitID`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`VoucherCode`),
  ADD KEY `Claimant` (`Claimant`);

--
-- Indexes for table `wordamount`
--
ALTER TABLE `wordamount`
  ADD PRIMARY KEY (`WAmountID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attribute`
--
ALTER TABLE `attribute`
  MODIFY `AttributeID` tinyint(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `BrandID` tinyint(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `colour`
--
ALTER TABLE `colour`
  MODIFY `ColourID` smallint(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `CustomerID` mediumint(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `myclass`
--
ALTER TABLE `myclass`
  MODIFY `ClassID` tinyint(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `myrange`
--
ALTER TABLE `myrange`
  MODIFY `RangeID` tinyint(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mytransaction`
--
ALTER TABLE `mytransaction`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `UnitID` tinyint(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wordamount`
--
ALTER TABLE `wordamount`
  MODIFY `WAmountID` tinyint(4) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributemeasurement`
--
ALTER TABLE `attributemeasurement`
  ADD CONSTRAINT `attributemeasurement_ibfk_1` FOREIGN KEY (`BarCode`) REFERENCES `product` (`BarCode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attributemeasurement_ibfk_2` FOREIGN KEY (`AttributeID`) REFERENCES `attribute` (`AttributeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attributemeasurement_ibfk_3` FOREIGN KEY (`UnitID`) REFERENCES `unit` (`UnitID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `attributemeasurement_ibfk_4` FOREIGN KEY (`WAmountID`) REFERENCES `wordamount` (`WAmountID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `classification`
--
ALTER TABLE `classification`
  ADD CONSTRAINT `classification_ibfk_1` FOREIGN KEY (`ClassID`) REFERENCES `myclass` (`ClassID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `classification_ibfk_2` FOREIGN KEY (`BarCode`) REFERENCES `product` (`BarCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customerproduct`
--
ALTER TABLE `customerproduct`
  ADD CONSTRAINT `customerproduct_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customerproduct_ibfk_2` FOREIGN KEY (`BarCode`) REFERENCES `product` (`BarCode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customerproduct_ibfk_3` FOREIGN KEY (`TransactionID`) REFERENCES `mytransaction` (`TransactionID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `customerproduct_ibfk_5` FOREIGN KEY (`ReviewID`) REFERENCES `review` (`ReviewID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `myclass`
--
ALTER TABLE `myclass`
  ADD CONSTRAINT `myclass_ibfk_1` FOREIGN KEY (`SuperClass`) REFERENCES `myclass` (`ClassID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`ColourID`) REFERENCES `colour` (`ColourID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`BrandID`) REFERENCES `brand` (`BrandID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `product_ibfk_3` FOREIGN KEY (`RangeID`) REFERENCES `myrange` (`RangeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `voucher`
--
ALTER TABLE `voucher`
  ADD CONSTRAINT `voucher_ibfk_1` FOREIGN KEY (`Claimant`) REFERENCES `customer` (`CustomerID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
