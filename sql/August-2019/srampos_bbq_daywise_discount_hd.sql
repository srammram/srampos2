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
-- Table structure for table `srampos_bbq_daywise_discount_hd`
--

CREATE TABLE `srampos_bbq_daywise_discount_hd` (
  `id` int(11) NOT NULL,
  `bbq_menu_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_on` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `srampos_bbq_daywise_discount_hd`
--

INSERT INTO `srampos_bbq_daywise_discount_hd` (`id`, `bbq_menu_id`, `from_date`, `to_date`, `created_on`, `created_by`, `updated_on`, `updated_by`, `status`) VALUES
(3, 1, '2019-08-30', '2019-09-30', '2019-08-30 18:15:28', 1, '0000-00-00 00:00:00', 0, 1),
(4, 2, '2019-08-30', '2019-09-30', '2019-08-30 18:15:41', 1, '0000-00-00 00:00:00', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_bbq_daywise_discount_hd`
--
ALTER TABLE `srampos_bbq_daywise_discount_hd`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_bbq_daywise_discount_hd`
--
ALTER TABLE `srampos_bbq_daywise_discount_hd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
