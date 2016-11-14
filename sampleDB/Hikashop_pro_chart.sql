-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 14, 2016 at 10:28 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `minuni`
--

-- --------------------------------------------------------

--
-- Table structure for table `c6ane_trangel_hikashop_product`
--

CREATE TABLE `c6ane_trangel_hikashop_product` (
  `id` int(10) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(15,7) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `c6ane_trangel_hikashop_variant`
--

CREATE TABLE `c6ane_trangel_hikashop_variant` (
  `id` int(11) NOT NULL,
  `parent_id` int(10) NOT NULL,
  `variant_id` int(10) NOT NULL,
  `variant_name` varchar(255) NOT NULL,
  `variant_price` decimal(15,7) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `c6ane_trangel_hikashop_product`
--
ALTER TABLE `c6ane_trangel_hikashop_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `c6ane_trangel_hikashop_variant`
--
ALTER TABLE `c6ane_trangel_hikashop_variant`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `c6ane_trangel_hikashop_product`
--
ALTER TABLE `c6ane_trangel_hikashop_product`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `c6ane_trangel_hikashop_variant`
--
ALTER TABLE `c6ane_trangel_hikashop_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
