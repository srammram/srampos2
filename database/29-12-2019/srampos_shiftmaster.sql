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
-- Table structure for table `srampos_shiftmaster`
--

DROP TABLE IF EXISTS `srampos_shiftmaster`;
CREATE TABLE `srampos_shiftmaster` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `srampos_shiftmaster`
--

INSERT INTO `srampos_shiftmaster` (`id`, `code`, `name`, `from_time`, `to_time`, `status`, `created_on`) VALUES
(4, '31742965', 'Shift1', '08:00:00', '13:59:00', 1, '2019-12-29 13:21:38'),
(5, '43694469', 'Shift 2', '14:00:00', '20:59:00', 1, '2019-12-29 13:22:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_shiftmaster`
--
ALTER TABLE `srampos_shiftmaster`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_shiftmaster`
--
ALTER TABLE `srampos_shiftmaster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
