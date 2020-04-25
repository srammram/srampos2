-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2019 at 11:17 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srampos_naturethaibistro`
--

-- --------------------------------------------------------

--
-- Table structure for table `srampos_recipe_addon`
--

CREATE TABLE `srampos_recipe_addon` (
  `id` int(11) NOT NULL,
  `recipe_id` bigint(20) NOT NULL,
  `variant_id` tinyint(4) NOT NULL,
  `create_on` datetime NOT NULL,
  `created_by` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_recipe_addon_details`
--

CREATE TABLE `srampos_recipe_addon_details` (
  `id` int(11) NOT NULL,
  `addon_head_id` tinyint(4) NOT NULL,
  `addon_item_id` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_recipe_addon`
--
ALTER TABLE `srampos_recipe_addon`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_recipe_addon_details`
--
ALTER TABLE `srampos_recipe_addon_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srampos_recipe_addon`
--
ALTER TABLE `srampos_recipe_addon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `srampos_recipe_addon_details`
--
ALTER TABLE `srampos_recipe_addon_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
