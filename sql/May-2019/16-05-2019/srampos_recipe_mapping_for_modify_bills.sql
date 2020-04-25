-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2019 at 08:28 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srampos1`
--

-- --------------------------------------------------------

--
-- Table structure for table `srampos_recipe_mapping_for_modify_bills`
--

CREATE TABLE IF NOT EXISTS `srampos_recipe_mapping_for_modify_bills` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `srampos_recipe_mapping_for_modify_bills`
--

INSERT INTO `srampos_recipe_mapping_for_modify_bills` (`id`, `recipe_id`) VALUES
(1, 444),
(2, 459),
(3, 491),
(4, 297),
(5, 296),
(6, 217),
(7, 299),
(8, 1),
(9, 2),
(10, 3),
(11, 298);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_recipe_mapping_for_modify_bills`
--
ALTER TABLE `srampos_recipe_mapping_for_modify_bills`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_recipe_mapping_for_modify_bills`
--
ALTER TABLE `srampos_recipe_mapping_for_modify_bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
