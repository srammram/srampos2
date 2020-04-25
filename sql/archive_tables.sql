-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2019 at 01:22 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srampos`
--

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_bbq`
--

CREATE TABLE `srampos_archive_bbq` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(255) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `warehouse_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `number_of_adult` bigint(20) NOT NULL,
  `number_of_child` bigint(20) NOT NULL,
  `number_of_kids` bigint(20) NOT NULL,
  `bbq_set_id` bigint(20) NOT NULL,
  `adult_price` varchar(255) NOT NULL,
  `child_price` varchar(255) NOT NULL,
  `kids_price` varchar(255) NOT NULL,
  `discount` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `table_id` bigint(20) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `cancel_status` tinyint(4) NOT NULL,
  `cancel_msg` varchar(255) NOT NULL,
  `cancel_by` bigint(20) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `created_on` date NOT NULL,
  `confirmed_by` int(11) NOT NULL,
  `order_request` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_bbq_bil_items`
--

CREATE TABLE `srampos_archive_bbq_bil_items` (
  `id` int(11) NOT NULL,
  `bil_id` bigint(20) NOT NULL,
  `type` varchar(255) NOT NULL,
  `cover` bigint(20) NOT NULL,
  `price` varchar(255) NOT NULL,
  `days` varchar(255) NOT NULL,
  `buyx` bigint(20) NOT NULL,
  `getx` bigint(20) NOT NULL,
  `discount_cover` bigint(20) NOT NULL,
  `discount` varchar(255) NOT NULL,
  `tax_id` bigint(20) NOT NULL,
  `tax_type` varchar(255) NOT NULL,
  `tax` varchar(255) NOT NULL,
  `subtotal` varchar(255) NOT NULL,
  `created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_bils`
--

CREATE TABLE `srampos_archive_bils` (
  `id` int(11) NOT NULL,
  `bill_number` varchar(255) NOT NULL,
  `bill_sequence_number` varchar(100) NOT NULL,
  `bilgenerator_type` tinyint(4) NOT NULL COMMENT '0-POS, 1-CUSTOMER',
  `consolidated` tinyint(4) NOT NULL,
  `sales_id` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `delivery_person_id` bigint(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(100) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(100) NOT NULL,
  `bill_type` int(11) NOT NULL,
  `order_type` tinyint(4) NOT NULL COMMENT '1->dine in,2->take away,3->door delivery,4->bbq',
  `note` varchar(100) NOT NULL,
  `staff_note` varchar(100) NOT NULL,
  `currencies_id` bigint(20) NOT NULL,
  `total` decimal(25,6) NOT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,6) DEFAULT '0.000000',
  `birthday_discount` decimal(25,2) NOT NULL,
  `order_discount` decimal(25,6) DEFAULT '0.000000',
  `manual_item_discount` decimal(25,2) NOT NULL,
  `manual_item_discount_val` varchar(10) NOT NULL,
  `customer_discount_id` tinyint(5) NOT NULL,
  `customer_discount_status` varchar(20) NOT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,6) DEFAULT '0.000000',
  `recipe_tax` decimal(25,6) NOT NULL,
  `total_tax` decimal(25,6) DEFAULT '0.000000',
  `tax_type` tinyint(1) NOT NULL,
  `shipping` decimal(25,6) DEFAULT '0.000000',
  `grand_total` decimal(25,6) NOT NULL,
  `round_total` decimal(25,6) NOT NULL,
  `total_pay` varchar(255) NOT NULL,
  `balance` varchar(255) NOT NULL,
  `bil_status` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_items` smallint(6) DEFAULT NULL,
  `paid_by` text NOT NULL,
  `pos` tinyint(1) NOT NULL DEFAULT '0',
  `paid` decimal(25,6) DEFAULT '0.000000',
  `return_id` int(11) DEFAULT NULL,
  `surcharge` decimal(25,6) NOT NULL DEFAULT '0.000000',
  `rounding` decimal(10,6) DEFAULT NULL,
  `api` tinyint(1) DEFAULT '0',
  `cgst` decimal(25,6) DEFAULT NULL,
  `sgst` decimal(25,6) DEFAULT NULL,
  `igst` decimal(25,6) DEFAULT NULL,
  `warehouse_id` tinyint(4) NOT NULL,
  `default_currency_code` varchar(255) NOT NULL,
  `default_currency_rate` varchar(255) NOT NULL,
  `table_whitelisted` tinyint(4) NOT NULL DEFAULT '0',
  `discount_type` varchar(100) NOT NULL,
  `discount_val` varchar(100) NOT NULL,
  `unique_discount` tinyint(4) NOT NULL,
  `bbq_cover_discount` decimal(25,4) NOT NULL,
  `order_status` varchar(50) NOT NULL,
  `action` tinyint(4) NOT NULL COMMENT '1-edited,2-deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_bil_items`
--

CREATE TABLE `srampos_archive_bil_items` (
  `id` int(11) NOT NULL,
  `bil_id` int(11) UNSIGNED NOT NULL,
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `recipe_code` varchar(55) NOT NULL,
  `recipe_name` varchar(255) NOT NULL,
  `recipe_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `addon_id` varchar(255) NOT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,6) DEFAULT NULL,
  `quantity` bigint(20) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,6) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `tax_type` tinyint(1) NOT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,6) DEFAULT NULL,
  `off_discount` decimal(25,6) NOT NULL,
  `input_discount` decimal(25,6) NOT NULL,
  `birthday_discount` decimal(25,2) NOT NULL,
  `manual_item_discount` decimal(25,2) NOT NULL,
  `manual_item_discount_val` varchar(10) NOT NULL,
  `manual_item_discount_per_val` decimal(25,2) NOT NULL,
  `subtotal` decimal(25,6) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,6) DEFAULT NULL,
  `sale_item_id` int(11) DEFAULT NULL,
  `recipe_unit_id` int(11) DEFAULT NULL,
  `recipe_unit_code` varchar(255) DEFAULT NULL,
  `unit_quantity` bigint(20) NOT NULL,
  `comment` text NOT NULL,
  `recipe_variant` varchar(200) NOT NULL,
  `recipe_variant_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_kitchen_orders`
--

CREATE TABLE `srampos_archive_kitchen_orders` (
  `id` int(11) NOT NULL,
  `sale_id` bigint(20) NOT NULL,
  `waiter_id` bigint(20) NOT NULL,
  `chef_id` bigint(20) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_orders`
--

CREATE TABLE `srampos_archive_orders` (
  `id` int(11) NOT NULL,
  `order_type` tinyint(4) NOT NULL,
  `customer_request` tinyint(4) NOT NULL,
  `table_id` bigint(20) NOT NULL,
  `seats_id` bigint(20) NOT NULL,
  `split_id` varchar(255) NOT NULL,
  `order_status` varchar(55) NOT NULL,
  `order_from` enum('web','app') NOT NULL,
  `date` datetime NOT NULL COMMENT 'transaction date',
  `created_on` datetime NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `staff_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,6) NOT NULL,
  `recipe_discount` decimal(25,6) DEFAULT '0.000000',
  `manual_item_discount` decimal(25,2) NOT NULL,
  `manual_item_discount_val` varchar(10) NOT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,6) DEFAULT '0.000000',
  `order_discount` decimal(25,6) DEFAULT '0.000000',
  `recipe_tax` decimal(25,6) DEFAULT '0.000000',
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,6) DEFAULT '0.000000',
  `total_tax` decimal(25,6) DEFAULT '0.000000',
  `shipping` decimal(25,6) DEFAULT '0.000000',
  `grand_total` decimal(25,6) NOT NULL,
  `sale_status` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `order_cancel_id` tinyint(4) NOT NULL COMMENT 'order cancelled update used id',
  `order_cancel_status` tinyint(1) NOT NULL COMMENT '1->cancelled,0->not cancelled',
  `order_cancel_note` varchar(250) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_items` smallint(6) DEFAULT NULL,
  `pos` tinyint(1) NOT NULL DEFAULT '0',
  `paid` decimal(25,6) DEFAULT '0.000000',
  `return_id` int(11) DEFAULT NULL,
  `surcharge` decimal(25,6) NOT NULL DEFAULT '0.000000',
  `attachment` varchar(55) DEFAULT NULL,
  `return_sale_ref` varchar(55) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `return_sale_total` decimal(25,6) NOT NULL DEFAULT '0.000000',
  `rounding` decimal(10,6) DEFAULT NULL,
  `suspend_note` varchar(255) DEFAULT NULL,
  `api` tinyint(1) DEFAULT '0',
  `shop` tinyint(1) DEFAULT '0',
  `address_id` int(11) DEFAULT NULL,
  `reserve_id` int(11) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `manual_payment` varchar(55) DEFAULT NULL,
  `cgst` decimal(25,6) DEFAULT NULL,
  `sgst` decimal(25,6) DEFAULT NULL,
  `igst` decimal(25,6) DEFAULT NULL,
  `table_whitelisted` tinyint(4) NOT NULL,
  `ordered_by` enum('steward','customer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_order_items`
--

CREATE TABLE `srampos_archive_order_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) UNSIGNED NOT NULL,
  `item_status` varchar(255) NOT NULL,
  `kitchen_id` bigint(20) NOT NULL,
  `kitchen_type_id` bigint(20) NOT NULL,
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `recipe_code` varchar(55) NOT NULL,
  `recipe_name` varchar(255) NOT NULL,
  `recipe_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `addon_id` varchar(255) NOT NULL,
  `buy_id` bigint(20) NOT NULL,
  `buy_quantity` bigint(20) NOT NULL,
  `get_item` bigint(20) NOT NULL,
  `get_quantity` bigint(20) NOT NULL,
  `total_get_quantity` bigint(20) NOT NULL,
  `net_unit_price` decimal(25,6) NOT NULL,
  `unit_price` decimal(25,6) DEFAULT NULL,
  `quantity` bigint(20) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `order_item_cancel_id` tinyint(4) NOT NULL COMMENT 'order cancelled update used id',
  `order_item_cancel_status` tinyint(1) NOT NULL COMMENT '1->cancelled,0->not cancelled',
  `order_item_cancel_note` varchar(250) NOT NULL,
  `item_tax` decimal(25,6) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,6) DEFAULT NULL,
  `manual_item_discount` decimal(25,2) NOT NULL,
  `manual_item_discount_val` varchar(10) NOT NULL,
  `subtotal` decimal(25,6) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,6) DEFAULT NULL,
  `sale_item_id` int(11) DEFAULT NULL,
  `recipe_unit_id` int(11) DEFAULT NULL,
  `recipe_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,6) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,6) DEFAULT NULL,
  `sgst` decimal(25,6) DEFAULT NULL,
  `igst` decimal(25,6) DEFAULT NULL,
  `time_started` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  `escalation_one_time` datetime NOT NULL,
  `escalation_two_time` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `variant` varchar(200) NOT NULL,
  `recipe_variant_id` tinyint(4) NOT NULL,
  `stock_out_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_payments`
--

CREATE TABLE `srampos_archive_payments` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `date` datetime DEFAULT NULL,
  `paid_on` datetime NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `bill_id` int(11) NOT NULL,
  `return_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_no` varchar(20) DEFAULT NULL,
  `cc_holder` varchar(25) DEFAULT NULL,
  `cc_month` varchar(2) DEFAULT NULL,
  `cc_year` varchar(4) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `amount` decimal(25,6) NOT NULL,
  `amount_exchange` decimal(25,6) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `note` text NOT NULL,
  `type` varchar(20) NOT NULL,
  `sale_note` varchar(1000) DEFAULT NULL,
  `staff_note` text NOT NULL,
  `payment_note` text NOT NULL,
  `pos_paid` decimal(25,6) DEFAULT '0.000000',
  `pos_balance` decimal(25,6) DEFAULT '0.000000',
  `approval_code` varchar(50) DEFAULT NULL,
  `customer_payment_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_restaurant_table_orders`
--

CREATE TABLE `srampos_archive_restaurant_table_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_restaurant_table_sessions`
--

CREATE TABLE `srampos_archive_restaurant_table_sessions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `session_started` datetime NOT NULL,
  `session_end` datetime NOT NULL,
  `customer_id` int(11) NOT NULL,
  `split_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_rough_tender_payments`
--

CREATE TABLE `srampos_archive_rough_tender_payments` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `date` datetime DEFAULT NULL,
  `paid_on` datetime NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `bill_id` int(11) NOT NULL,
  `return_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_no` varchar(20) DEFAULT NULL,
  `cc_holder` varchar(25) DEFAULT NULL,
  `cc_month` varchar(2) DEFAULT NULL,
  `cc_year` varchar(4) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `amount` decimal(25,6) NOT NULL,
  `amount_exchange` decimal(25,6) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `note` text NOT NULL,
  `type` varchar(20) NOT NULL,
  `sale_note` varchar(1000) DEFAULT NULL,
  `staff_note` text NOT NULL,
  `payment_note` text NOT NULL,
  `pos_paid` decimal(25,6) DEFAULT '0.000000',
  `pos_balance` decimal(25,6) DEFAULT '0.000000',
  `approval_code` varchar(50) DEFAULT NULL,
  `customer_payment_type` varchar(100) NOT NULL,
  `bill_settled` tinyint(4) NOT NULL COMMENT '0-No, 1-yes',
  `loyalty_points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_rough_tender_sale_currency`
--

CREATE TABLE `srampos_archive_rough_tender_sale_currency` (
  `id` int(11) NOT NULL,
  `sale_id` bigint(20) NOT NULL,
  `bil_id` bigint(20) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency_rate` varchar(255) NOT NULL,
  `paid_type` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_sales`
--

CREATE TABLE `srampos_archive_sales` (
  `id` int(11) NOT NULL,
  `bilgenerator_type` tinyint(4) NOT NULL COMMENT '0-POS, 1-CUSTOMER',
  `consolidated` tinyint(4) NOT NULL,
  `sales_type_id` tinyint(4) NOT NULL,
  `sales_split_id` varchar(255) NOT NULL,
  `sales_table_id` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `delivery_person_id` bigint(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `staff_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,6) NOT NULL,
  `recipe_discount` decimal(25,6) DEFAULT '0.000000',
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,6) DEFAULT '0.000000',
  `order_discount` decimal(25,6) DEFAULT '0.000000',
  `manual_item_discount` decimal(25,2) NOT NULL,
  `manual_item_discount_val` varchar(10) NOT NULL,
  `recipe_tax` decimal(25,6) DEFAULT '0.000000',
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,6) DEFAULT '0.000000',
  `total_tax` decimal(25,6) DEFAULT '0.000000',
  `shipping` decimal(25,6) DEFAULT '0.000000',
  `grand_total` decimal(25,6) NOT NULL,
  `sale_status` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_items` smallint(6) DEFAULT NULL,
  `pos` tinyint(1) NOT NULL DEFAULT '0',
  `paid` decimal(25,6) DEFAULT '0.000000',
  `return_id` int(11) DEFAULT NULL,
  `surcharge` decimal(25,6) NOT NULL DEFAULT '0.000000',
  `attachment` varchar(55) DEFAULT NULL,
  `return_sale_ref` varchar(55) DEFAULT NULL,
  `cancel_status` tinyint(4) DEFAULT '0' COMMENT '0->not cancel,1->cancel',
  `cancel_remarks` text NOT NULL,
  `canceled_user_id` tinyint(4) NOT NULL,
  `return_sale_total` decimal(25,6) NOT NULL DEFAULT '0.000000',
  `rounding` decimal(10,6) DEFAULT NULL,
  `suspend_note` varchar(255) DEFAULT NULL,
  `api` tinyint(1) DEFAULT '0',
  `shop` tinyint(1) DEFAULT '0',
  `address_id` int(11) DEFAULT NULL,
  `reserve_id` int(11) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `manual_payment` varchar(55) DEFAULT NULL,
  `cgst` decimal(25,6) DEFAULT NULL,
  `sgst` decimal(25,6) DEFAULT NULL,
  `igst` decimal(25,6) DEFAULT NULL,
  `default_currency_code` varchar(255) NOT NULL,
  `default_currency_rate` varchar(255) NOT NULL,
  `table_whitelisted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_sale_currency`
--

CREATE TABLE `srampos_archive_sale_currency` (
  `id` int(11) NOT NULL,
  `sale_id` bigint(20) NOT NULL,
  `bil_id` bigint(20) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency_rate` varchar(255) NOT NULL,
  `paid_type` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `srampos_archive_sale_items`
--

CREATE TABLE `srampos_archive_sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) UNSIGNED NOT NULL,
  `bil_type` tinyint(4) NOT NULL,
  `bil_id` bigint(20) NOT NULL,
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `recipe_code` varchar(55) NOT NULL,
  `recipe_name` varchar(255) NOT NULL,
  `recipe_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `addon_id` varchar(255) NOT NULL,
  `net_unit_price` decimal(25,6) NOT NULL,
  `unit_price` decimal(25,6) DEFAULT NULL,
  `quantity` bigint(20) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,6) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,6) DEFAULT NULL,
  `manual_item_discount` decimal(25,2) NOT NULL,
  `manual_item_discount_val` varchar(10) NOT NULL,
  `subtotal` decimal(25,6) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,6) DEFAULT NULL,
  `sale_item_id` int(11) DEFAULT NULL,
  `recipe_unit_id` int(11) DEFAULT NULL,
  `recipe_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,6) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,6) DEFAULT NULL,
  `sgst` decimal(25,6) DEFAULT NULL,
  `igst` decimal(25,6) DEFAULT NULL,
  `recipe_variant_id` tinyint(4) NOT NULL,
  `variant` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srampos_archive_bbq`
--
ALTER TABLE `srampos_archive_bbq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_bbq_bil_items`
--
ALTER TABLE `srampos_archive_bbq_bil_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_bils`
--
ALTER TABLE `srampos_archive_bils`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `paymentstatus` (`payment_status`) USING BTREE;

--
-- Indexes for table `srampos_archive_bil_items`
--
ALTER TABLE `srampos_archive_bil_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bil_id` (`bil_id`),
  ADD KEY `product_id` (`recipe_id`),
  ADD KEY `product_id_2` (`recipe_id`,`bil_id`),
  ADD KEY `bil_id_2` (`bil_id`,`recipe_id`);

--
-- Indexes for table `srampos_archive_kitchen_orders`
--
ALTER TABLE `srampos_archive_kitchen_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_orders`
--
ALTER TABLE `srampos_archive_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `constr_ID` (`table_id`,`split_id`,`customer_id`,`created_on`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `srampos_archive_order_items`
--
ALTER TABLE `srampos_archive_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`recipe_id`),
  ADD KEY `product_id_2` (`recipe_id`,`sale_id`),
  ADD KEY `sale_id_2` (`sale_id`,`recipe_id`);

--
-- Indexes for table `srampos_archive_payments`
--
ALTER TABLE `srampos_archive_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_restaurant_table_orders`
--
ALTER TABLE `srampos_archive_restaurant_table_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_restaurant_table_sessions`
--
ALTER TABLE `srampos_archive_restaurant_table_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_rough_tender_payments`
--
ALTER TABLE `srampos_archive_rough_tender_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_rough_tender_sale_currency`
--
ALTER TABLE `srampos_archive_rough_tender_sale_currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_sales`
--
ALTER TABLE `srampos_archive_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `srampos_archive_sale_currency`
--
ALTER TABLE `srampos_archive_sale_currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srampos_archive_sale_items`
--
ALTER TABLE `srampos_archive_sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`recipe_id`),
  ADD KEY `product_id_2` (`recipe_id`,`sale_id`),
  ADD KEY `sale_id_2` (`sale_id`,`recipe_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
