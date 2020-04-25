-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2019 at 12:58 PM
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
-- Table structure for table `srampos_tills`
--

DROP TABLE IF EXISTS `srampos_tills`;
CREATE TABLE `srampos_tills` (
  `id` int(11) NOT NULL,
  `system_name` varchar(255) NOT NULL,
  `system_ip` varchar(255) NOT NULL,
  `till_name` varchar(255) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `action` tinyint(4) NOT NULL,
  `line_display_port` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `srampos_tills`
--

INSERT INTO `srampos_tills` (`id`, `system_name`, `system_ip`, `till_name`, `warehouse_id`, `status`, `action`, `line_display_port`) VALUES
(1, 'SRAMPC05', '192.168.0.153', 'POS 001', 1, 1, 1, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_tills`
--
ALTER TABLE `srampos_tills`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_tills`
--
ALTER TABLE `srampos_tills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
