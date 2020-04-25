-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 30, 2019 at 11:43 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sramposv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `srampos_shifts_settlement`
--

CREATE TABLE `srampos_shifts_settlement` (
  `id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `till_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `no_of_bills` int(11) NOT NULL,
  `no_of_items` int(11) NOT NULL,
  `bill_total` int(11) NOT NULL,
  `default_currency` int(11) NOT NULL,
  `cash_actual` decimal(10,2) NOT NULL,
  `cash_received` decimal(10,2) NOT NULL,
  `cash_difference` decimal(10,2) NOT NULL,
  `card_actual` decimal(10,2) NOT NULL,
  `card_received` decimal(10,2) NOT NULL,
  `card_difference` decimal(10,2) NOT NULL,
  `USD_1` int(11) NOT NULL,
  `USD_2` int(11) NOT NULL,
  `USD_5` int(11) NOT NULL,
  `USD_10` int(11) NOT NULL,
  `USD_20` int(11) NOT NULL,
  `USD_50` int(11) NOT NULL,
  `USD_100` int(11) NOT NULL,
  `USD_200` int(11) NOT NULL,
  `USD_500` int(11) NOT NULL,
  `KHR_100` int(11) NOT NULL,
  `KHR_1000` int(11) NOT NULL,
  `KHR_2000` int(11) NOT NULL,
  `KHR_5000` int(11) NOT NULL,
  `KHR_10000` int(11) NOT NULL,
  `KHR_50000` int(11) NOT NULL,
  `cash_USD_actual` decimal(10,2) NOT NULL,
  `cash_KHR_actual` decimal(10,2) NOT NULL,
  `cash_USD_received` decimal(10,2) NOT NULL,
  `cash_KHR_received` decimal(10,2) NOT NULL,
  `cash_USD_difference` decimal(10,2) NOT NULL,
  `cash_KHR_difference` decimal(10,2) NOT NULL,
  `opening_cash_USD` decimal(10,2) NOT NULL,
  `opening_cash_KHR` decimal(10,2) NOT NULL,
  `reprint` int(11) NOT NULL,
  `return_sales` decimal(10,2) NOT NULL,
  `credit_sales` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_shifts_settlement`
--
ALTER TABLE `srampos_shifts_settlement`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_shifts_settlement`
--
ALTER TABLE `srampos_shifts_settlement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
