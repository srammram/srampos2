-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2019 at 04:54 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srampos_single_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `srampos_pro_stock_ledger`
--

DROP TABLE IF EXISTS `srampos_pro_stock_ledger`;
CREATE TABLE `srampos_pro_stock_ledger` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `cm_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `batch` varchar(50) NOT NULL,
  `transaction_identify` varchar(50) NOT NULL COMMENT 'Invoice,Sales,Production,Adjustment',
  `transaction_type` enum('I','O') NOT NULL COMMENT 'I->Stock In,O->Stock Out',
  `transaction_qty` decimal(10,6) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_pro_stock_ledger`
--
ALTER TABLE `srampos_pro_stock_ledger`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_pro_stock_ledger`
--
ALTER TABLE `srampos_pro_stock_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
