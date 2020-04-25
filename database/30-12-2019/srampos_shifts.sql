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
-- Table structure for table `srampos_shifts`
--

CREATE TABLE `srampos_shifts` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `till_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_cash` decimal(10,2) NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `shift_from_time` time NOT NULL,
  `shift_to_time` time NOT NULL,
  `shift_start_time` datetime NOT NULL,
  `shift_end_time` datetime NOT NULL,
  `continued_shift` tinyint(4) NOT NULL,
  `continued_shift_approved_by` int(11) NOT NULL,
  `shiftmaster_id` int(11) NOT NULL,
  `settled` tinyint(4) NOT NULL,
  `CUR_USD` varchar(255) NOT NULL,
  `CUR_KHR` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_shifts`
--
ALTER TABLE `srampos_shifts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_shifts`
--
ALTER TABLE `srampos_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
