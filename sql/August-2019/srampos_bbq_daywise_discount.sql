-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2019 at 04:00 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 5.6.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `07082019`
--

-- --------------------------------------------------------

--
-- Table structure for table `srampos_bbq_daywise_discount`
--

CREATE TABLE `srampos_bbq_daywise_discount` (
  `id` int(11) NOT NULL,
  `bbq_daywise_discount_hd_id` int(11) NOT NULL,
  `days` varchar(255) NOT NULL,
  `discount_type` enum('percentage','amount') NOT NULL,
  `adult_discount_val` decimal(16,2) NOT NULL,
  `child_discount_val` decimal(16,2) NOT NULL,
  `kids_discount_val` decimal(16,2) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `srampos_bbq_daywise_discount`
--

INSERT INTO `srampos_bbq_daywise_discount` (`id`, `bbq_daywise_discount_hd_id`, `days`, `discount_type`, `adult_discount_val`, `child_discount_val`, `kids_discount_val`, `created_by`, `created_at`, `status`) VALUES
(1, 3, 'Friday', 'percentage', '1.00', '2.00', '3.00', 1, '2019-08-30 18:15:28', 1),
(2, 4, 'Friday', 'percentage', '1.00', '2.00', '3.00', 1, '2019-08-30 18:15:41', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_bbq_daywise_discount`
--
ALTER TABLE `srampos_bbq_daywise_discount`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_bbq_daywise_discount`
--
ALTER TABLE `srampos_bbq_daywise_discount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
