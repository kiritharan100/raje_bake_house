-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 05:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raje_bake_house`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_cheque_payment`
--

CREATE TABLE `bank_cheque_payment` (
  `chq_id` int(11) NOT NULL,
  `cheque_no` varchar(30) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `cheque_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_cheque_payment`
--

INSERT INTO `bank_cheque_payment` (`chq_id`, `cheque_no`, `contact_id`, `issue_date`, `cheque_date`, `amount`, `status`) VALUES
(1, '5025', 1, '2025-12-15', '2025-12-15', 5000.00, 1),
(2, '2', 1, '2025-12-15', '2025-12-15', 12.00, 1),
(3, '5', 1, '2025-12-15', '2026-01-02', 5000005.00, 0),
(4, '15', 1, '2025-12-12', '2025-12-12', 5000.00, 0),
(5, 'sar', 1, '2025-12-16', '2025-12-16', 133.00, 1),
(6, '1212', 1, '2025-12-17', '2025-12-17', 250.00, 1),
(7, '5000', 2, '2025-12-15', '2026-12-14', 5000000.00, 0),
(8, '1212', 1, '2025-12-15', '2025-12-31', 58000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bank_contact`
--

CREATE TABLE `bank_contact` (
  `contact_id` int(11) NOT NULL,
  `contact_name` varchar(50) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_contact`
--

INSERT INTO `bank_contact` (`contact_id`, `contact_name`, `contact_number`, `status`) VALUES
(1, 'Astra', 0, 1),
(2, 'Me test payee', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bill_detail`
--

CREATE TABLE `bill_detail` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_detail`
--

INSERT INTO `bill_detail` (`id`, `bill_id`, `p_id`, `quantity`, `price`, `value`, `status`) VALUES
(0, 0, 2, 12.00, 130.00, 1560.00, 1),
(0, 0, 5, 50.00, 60.00, 3000.00, 1),
(0, 3, 3, 12.00, 70.00, 840.00, 1),
(0, 4, 3, 13.00, 70.00, 910.00, 1),
(0, 4, 5, 2.00, 60.00, 120.00, 1),
(0, 5, 3, 25.00, 70.00, 1750.00, 1),
(0, 5, 2, 30.00, 130.00, 3900.00, 1),
(0, 2, 1, 5.00, 200.00, 1000.00, 1),
(0, 6, 2, 12.00, 500.00, 6000.00, 1),
(0, 6, 3, 12.00, 70.00, 840.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

CREATE TABLE `bill_items` (
  `p_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `product_category` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `order_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_items`
--

INSERT INTO `bill_items` (`p_id`, `product_name`, `current_price`, `product_category`, `status`, `order_no`) VALUES
(1, 'Slice Bread', 200.00, 'Bakery Items', 1, 1),
(2, 'L Bread', 130.00, 'Bakery Items', 1, 2),
(3, 'S Bread', 70.00, 'Bakery Items', 1, 3),
(4, 'Rost Bread', 60.00, 'Bakery Items', 1, 4),
(5, 'Bun', 60.00, 'Bakery Items', 1, 5),
(6, 'Curry Bun', 80.00, 'Bakery Items', 1, 6),
(7, 'Creem Bun', 80.00, 'Bakery Items', 1, 7),
(8, 'Jam Bun', 80.00, 'Bakery Items', 1, 8),
(9, 'S S Bun', 80.00, 'Bakery Items', 1, 9),
(10, 'Saman Bun', 80.00, 'Bakery Items', 1, 10),
(11, 'Egg Bun', 100.00, 'Bakery Items', 1, 11),
(12, 'Keels Bun', 80.00, 'Bakery Items', 1, 12),
(13, 'Roll Bun', 60.00, 'Bakery Items', 1, 13),
(14, 'Donad', 130.00, 'Bakery Items', 1, 14),
(15, 'T Vadai', 60.00, 'Bakery Items', 1, 15),
(16, 'Browni', 200.00, 'Bakery Items', 1, 16),
(17, 'Butter Cake', 1100.00, 'Bakery Items', 1, 17),
(18, 'Dates Cake', 1100.00, 'Bakery Items', 1, 18),
(19, 'Layer Cake', 1200.00, 'Bakery Items', 1, 19),
(20, 'Sponch', 60.00, 'Bakery Items', 1, 20),
(21, 'Rusk', 1000.00, 'Bakery Items', 1, 21),
(22, 'Baby Rusk', 1000.00, 'Bakery Items', 1, 22),
(23, 'Butter Rusk', 1000.00, 'Bakery Items', 1, 23),
(24, 'Ganakkatha L', 40.00, 'Bakery Items', 1, 24),
(25, 'Ganakkatha S', 20.00, 'Bakery Items', 1, 25),
(26, 'Patties Veg', 40.00, 'Short dish', 1, 26),
(27, 'Patties Saman', 60.00, 'Short dish', 1, 27),
(28, 'Patties Egg', 60.00, 'Short dish', 1, 28),
(29, 'Rolls Veg', 60.00, 'Short dish', 1, 29),
(30, 'Rolls Saman', 80.00, 'Short dish', 1, 30),
(31, 'Rolls Egg', 80.00, 'Short dish', 1, 31),
(32, 'Rolls Chicken', 100.00, 'Short dish', 1, 32),
(33, 'Samosa Veg', 60.00, 'Short dish', 1, 33),
(34, 'Samosa Saman', 80.00, 'Short dish', 1, 34),
(35, 'Samosa Egg', 80.00, 'Short dish', 1, 35),
(36, 'Samosa Chicken', 100.00, 'Short dish', 1, 36),
(37, 'Rotti', 400.00, 'Short dish', 1, 37);

-- --------------------------------------------------------

--
-- Table structure for table `bill_payment`
--

CREATE TABLE `bill_payment` (
  `pay_id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `payment_mode` varchar(25) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_payment`
--

INSERT INTO `bill_payment` (`pay_id`, `bill_id`, `payment_mode`, `amount`, `payment_date`, `status`) VALUES
(1, 5, 'Cash', 750.00, '2025-12-15', 0),
(2, 5, 'Cash', 1000.00, '2025-12-15', 0),
(3, 5, 'Cash', 5650.00, '2025-12-15', 1),
(4, 3, 'Cash', 40.00, '2025-12-15', 1),
(5, 3, 'Cash', 800.00, '2025-12-15', 1),
(6, 6, 'Bank Deposit', 6840.00, '2025-12-15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `bill_summary`
--

CREATE TABLE `bill_summary` (
  `bill_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `bill_no` varchar(12) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_summary`
--

INSERT INTO `bill_summary` (`bill_id`, `customer_id`, `date`, `bill_no`, `amount`, `status`) VALUES
(1, 1, '2025-12-15', '', 1560.00, 1),
(2, 1, '2024-12-15', '125', 1000.00, 1),
(3, 2, '2025-12-15', '', 840.00, 1),
(4, 1, '2025-12-10', '12', 1030.00, 0),
(5, 2, '2025-12-15', '12', 5650.00, 1),
(6, 5, '2025-12-15', '123', 6840.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `client_registration`
--

CREATE TABLE `client_registration` (
  `c_id` int(10) NOT NULL,
  `client_name` varchar(300) NOT NULL,
  `coordinates` varchar(5000) NOT NULL,
  `md5_client` varchar(250) NOT NULL,
  `user_license` int(10) NOT NULL,
  `client_id` varchar(100) NOT NULL,
  `client_type` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `supply_by` varchar(50) NOT NULL,
  `client_email` varchar(100) NOT NULL,
  `client_phone` varchar(30) NOT NULL,
  `contact_name` varchar(80) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `regNumber` varchar(80) NOT NULL,
  `region` varchar(50) NOT NULL,
  `bank_and_branch` varchar(150) NOT NULL,
  `account_number` varchar(150) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `payment_sms` int(11) NOT NULL DEFAULT 0,
  `remindes_sms` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `client_registration`
--

INSERT INTO `client_registration` (`c_id`, `client_name`, `coordinates`, `md5_client`, `user_license`, `client_id`, `client_type`, `district`, `supply_by`, `client_email`, `client_phone`, `contact_name`, `contact_email`, `contact_phone`, `status`, `regNumber`, `region`, `bank_and_branch`, `account_number`, `account_name`, `payment_sms`, `remindes_sms`) VALUES
(2, 'Raja Bake House', '', 'c81e728d9d4c2f636f067f89cc14862c', 1, '3', 'DS', 'Batticaloa', '', '', '', '', '', '', 1, 'Batticaloa', 'Batticaloa', '', '', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `leases`
--

CREATE TABLE `leases` (
  `lease_id` int(11) NOT NULL,
  `land_id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `lease_number` varchar(100) DEFAULT NULL,
  `file_number` varchar(100) NOT NULL,
  `valuation_amount` decimal(15,2) NOT NULL,
  `premium` decimal(10,2) NOT NULL,
  `annual_rent_percentage` decimal(5,2) DEFAULT 4.00,
  `revision_period` int(11) DEFAULT 5,
  `revision_percentage` decimal(5,2) DEFAULT 20.00,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  `created_by` int(11) NOT NULL,
  `created_on` datetime DEFAULT current_timestamp(),
  `approved_date` date DEFAULT NULL,
  `valuation_date` date NOT NULL DEFAULT current_timestamp(),
  `value_date` date DEFAULT NULL,
  `duration_years` decimal(10,2) NOT NULL,
  `lease_type_id` int(11) NOT NULL,
  `type_of_project` varchar(100) NOT NULL,
  `name_of_the_project` varchar(100) NOT NULL,
  `updated_by` varchar(10) NOT NULL,
  `updated_on` date DEFAULT NULL,
  `lease_status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `leases`
--

INSERT INTO `leases` (`lease_id`, `land_id`, `beneficiary_id`, `location_id`, `lease_number`, `file_number`, `valuation_amount`, `premium`, `annual_rent_percentage`, `revision_period`, `revision_percentage`, `start_date`, `end_date`, `status`, `created_by`, `created_on`, `approved_date`, `valuation_date`, `value_date`, `duration_years`, `lease_type_id`, `type_of_project`, `name_of_the_project`, `updated_by`, `updated_on`, `lease_status`) VALUES
(1, 1, 27, 40, 'Pending', 'DS/TR/LS/01', 500000.00, 0.00, 2.00, 0, 0.00, '2020-01-02', '2050-01-02', 'active', 1, '2025-11-28 16:25:16', '0000-00-00', '2021-01-01', '0000-00-00', 30.00, 1, 'Commercial', '', '1', '2025-12-13', 1),
(2, 2, 28, 40, '12/asdsad', 'DS/TR/LS/45', 5000000.00, 300000.00, 2.00, 5, 20.00, '2018-11-01', '2048-11-01', 'active', 1, '2025-12-03 20:05:20', '0000-00-00', '2019-01-01', '0000-00-00', 30.00, 1, 'Commercial', 'Sop', '', NULL, 1),
(3, 3, 47, 40, '45645/154', 'DS/TR/LS/5/501', 6000000.00, 0.00, 4.00, 5, 20.00, '2022-01-01', '2052-01-01', 'active', 1, '2025-12-09 15:17:51', '0000-00-00', '2022-01-10', '0000-00-00', 30.00, 1, 'Commercial', 'sHOP', '1', '2025-12-09', 1),
(4, 4, 35, 40, 'test penalty', 'DS/TR/LS/', 800000.00, 48000.00, 2.00, 5, 20.00, '2007-09-19', '2037-09-19', 'active', 1, '2025-12-09 19:45:35', '0000-00-00', '2011-12-19', '0000-00-00', 30.00, 1, 'Commercial', '', '1', '2025-12-11', 1),
(5, 5, 40, 40, 'Pending', 'DS/TR/LS/', 500000.00, 60000.00, 4.00, 5, 20.00, '1987-01-01', '2017-01-01', 'active', 1, '2025-12-11 08:50:33', '0000-00-00', '0000-00-00', '0000-00-00', 30.00, 1, 'Commercial', '', '1', '2025-12-11', 1),
(6, 6, 29, 40, '45678/45', 'DS/TR/LS/1245/1234', 50000.00, 0.00, 5.00, 5, 20.00, '2021-01-01', '2051-01-01', 'active', 1, '2025-12-11 17:34:23', '0000-00-00', '0000-00-00', '0000-00-00', 30.00, 1, 'Commercial', '', '', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `letter_head`
--

CREATE TABLE `letter_head` (
  `id` int(11) NOT NULL,
  `entity` varchar(60) NOT NULL,
  `address` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telephone` varchar(100) NOT NULL,
  `vat_no` varchar(20) NOT NULL,
  `reg_no` varchar(100) NOT NULL,
  `invoice_prefix` varchar(10) NOT NULL,
  `admin_device_approval` int(11) NOT NULL DEFAULT 0,
  `company_name` varchar(100) NOT NULL,
  `VAT` varchar(50) NOT NULL,
  `gm_mobile` varchar(12) NOT NULL,
  `system_email` varchar(50) NOT NULL,
  `domain` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `letter_head`
--

INSERT INTO `letter_head` (`id`, `entity`, `address`, `email`, `telephone`, `vat_no`, `reg_no`, `invoice_prefix`, `admin_device_approval`, `company_name`, `VAT`, `gm_mobile`, `system_email`, `domain`) VALUES
(1, 'Raja Bake House', '', 'E-Mail: ', 'Phone ', '', '', '', 0, 'Raja Bake House', '', '0', '', ' ');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `ip_address` varchar(45) DEFAULT NULL,
  `attempt_time` datetime DEFAULT NULL,
  `try_for` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`ip_address`, `attempt_time`, `try_for`) VALUES
('::1', '2025-12-02 14:17:20', 'reset'),
('::1', '2025-12-02 14:19:03', 'reset'),
('::1', '2025-12-13 22:16:40', 'setup_password'),
('::1', '2025-12-13 22:16:57', 'setup_password_final');

-- --------------------------------------------------------

--
-- Table structure for table `manage_activities`
--

CREATE TABLE `manage_activities` (
  `act_id` int(11) NOT NULL,
  `activity` varchar(50) NOT NULL,
  `module` varchar(20) NOT NULL,
  `is_menu` int(11) NOT NULL DEFAULT 0,
  `activity_url` varchar(150) NOT NULL,
  `icon_script` varchar(60) NOT NULL,
  `order_no` int(11) NOT NULL DEFAULT 1000,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `manage_activities`
--

INSERT INTO `manage_activities` (`act_id`, `activity`, `module`, `is_menu`, `activity_url`, `icon_script`, `order_no`, `status`) VALUES
(1, 'Manage Users', 'Admin', 1, 'manage_user.php', '', 1, 1),
(2, 'Manage DS Division', 'Admin', 1, '', '', 1000, 1),
(3, 'Manage GN Division', 'Admin', 1, '', '', 1000, 1),
(4, 'Manage SMS Template', 'Admin', 0, '', '', 1000, 1),
(8, 'Long Term Lease > Write-Off Penalty & Premium', 'Land', 0, '', '', 1002, 1),
(9, 'Admin Module', 'Admin', 0, '', '', 1, 1),
(11, 'Divisional Secretariat Module', 'Land', 0, '', '', 1, 1),
(12, 'Long Term Lease > Register (Main Page)', 'Land', 0, '', '', 1, 1),
(13, 'Long Term Lease > Add, Edit Application', 'Land', 0, '', '', 2, 1),
(14, 'Long Term Lease Master (Setup Lease)', 'Admin', 0, 'lease_master.php', '', 2, 1),
(15, 'Admin Report', 'Admin', 0, '', '', 2000, 1),
(16, 'DS Report', 'Land', 0, '', '', 9999, 1),
(17, 'Short-Term Lease Management', 'Land', 0, '', '', 2000, 1),
(18, 'Long Term Lease > Payment Record', 'Land', 0, '', '', 900, 1),
(19, 'Long Term Lease > Payment Cancellation', 'Land', 0, '', '', 901, 1),
(20, 'Long Term Lease > Edit Lease Details ', 'Land', 0, '', '', 902, 1),
(21, 'Long Term Lease > Edit Land Information', 'Land', 0, '', '', 1000, 1),
(22, 'Long Term Lease > Delete Documents', 'Land', 0, '', '', 2000, 1),
(23, 'Long Term Lease > Add document', 'Land', 0, '', '', 1999, 1);

-- --------------------------------------------------------

--
-- Table structure for table `manage_customers`
--

CREATE TABLE `manage_customers` (
  `cus_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_number` varchar(25) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manage_customers`
--

INSERT INTO `manage_customers` (`cus_id`, `customer_name`, `contact_number`, `status`) VALUES
(1, 'Department of labour', '0770x501', 1),
(2, 'Departent of  building', '', 1),
(3, 'test', '12', 0),
(4, 'kiri', '12', 0),
(5, 'test23', '23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `manage_user_group`
--

CREATE TABLE `manage_user_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `manage_user_group`
--

INSERT INTO `manage_user_group` (`group_id`, `group_name`, `status`) VALUES
(1, 'System Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `production_daily_material_usage`
--

CREATE TABLE `production_daily_material_usage` (
  `id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `material_price` decimal(10,2) NOT NULL,
  `quantity_used` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_daily_material_usage`
--

INSERT INTO `production_daily_material_usage` (`id`, `material_id`, `date`, `material_price`, `quantity_used`) VALUES
(16, 3, '2025-12-14', 200.00, 17.00),
(17, 4, '2025-12-14', 0.23, 780.00),
(18, 5, '2025-12-14', 0.50, 325.00),
(19, 6, '2025-12-14', 1.10, 500.00),
(20, 7, '2025-12-14', 1.70, 160.00),
(21, 3, '2025-12-13', 200.00, 6.00);

-- --------------------------------------------------------

--
-- Table structure for table `production_daily_production`
--

CREATE TABLE `production_daily_production` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `product_id` int(11) NOT NULL,
  `sales_price` decimal(10,2) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `return_qty` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_daily_production`
--

INSERT INTO `production_daily_production` (`id`, `date`, `product_id`, `sales_price`, `quantity`, `return_qty`) VALUES
(5, '2025-12-14', 1, 200.00, 18.00, 1.00),
(6, '2025-12-14', 2, 130.00, 20.00, 0.00),
(9, '2025-12-13', 1, 200.00, 15.00, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `production_material`
--

CREATE TABLE `production_material` (
  `id` int(11) NOT NULL,
  `material_name` varchar(100) NOT NULL,
  `mesurement` varchar(10) NOT NULL,
  `current_price` decimal(10,3) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_material`
--

INSERT INTO `production_material` (`id`, `material_name`, `mesurement`, `current_price`, `status`) VALUES
(3, 'Flour', 'Kg', 200.000, 1),
(4, 'Suger', 'g', 0.230, 1),
(5, 'Solt', 'g', 0.500, 1),
(6, 'Margarin', 'g', 1.100, 1),
(7, 'Yest', 'g', 1.700, 1),
(8, 'Egg', 'g', 0.045, 1),
(9, 'Baking Power', 'g', 1.820, 1),
(10, 'Essance', 'Ml', 1.400, 1),
(11, 'Plams', 'g', 1.000, 1),
(12, 'Creem', 'Ml', 1.000, 1),
(13, 'Curry', 'g', 0.000, 1),
(14, 'Saman', 'g', 0.000, 1),
(15, 'Keels', 'g', 0.000, 1),
(16, 'Cup', 'g', 0.000, 1),
(17, 'Coco', 'g', 2.200, 1),
(18, 'Oil Paper', 'g', 0.020, 1),
(19, 'Milk', 'g', 2.500, 1);

-- --------------------------------------------------------

--
-- Table structure for table `production_material_allocation`
--

CREATE TABLE `production_material_allocation` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `unit` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_material_allocation`
--

INSERT INTO `production_material_allocation` (`id`, `product_id`, `material_id`, `unit`) VALUES
(1, 1, 3, 6.500),
(3, 1, 5, 130.000),
(4, 1, 6, 200.000),
(5, 1, 7, 60.000),
(6, 2, 3, 6.500),
(7, 2, 4, 300.000),
(8, 2, 5, 130.000),
(9, 2, 6, 200.000),
(10, 2, 7, 60.000),
(11, 3, 3, 5.500),
(12, 3, 4, 250.000),
(13, 3, 5, 110.000),
(14, 3, 6, 150.000),
(15, 3, 7, 75.000),
(16, 4, 3, 11.000),
(17, 4, 4, 300.000),
(18, 4, 5, 200.000),
(19, 4, 6, 220.000),
(20, 4, 7, 120.000),
(21, 5, 3, 10.000),
(22, 5, 4, 3500.000),
(23, 5, 5, 100.000),
(24, 5, 6, 1800.000),
(25, 5, 7, 150.000),
(26, 5, 11, 250.000),
(27, 6, 3, 10.000),
(28, 6, 4, 1200.000),
(29, 6, 5, 100.000),
(30, 6, 6, 1000.000),
(31, 6, 7, 120.000),
(32, 7, 3, 5.000),
(33, 7, 4, 1750.000),
(34, 7, 5, 50.000),
(35, 7, 6, 900.000),
(36, 7, 7, 75.000),
(37, 7, 12, 10.000),
(38, 8, 3, 5.000),
(39, 8, 4, 1850.000),
(40, 8, 5, 50.000),
(41, 8, 6, 900.000),
(42, 8, 7, 75.000),
(43, 8, 12, 10.000),
(44, 9, 3, 10.000),
(45, 9, 4, 1200.000),
(46, 9, 5, 100.000),
(47, 9, 6, 1000.000),
(48, 9, 7, 120.000),
(49, 10, 3, 10.000),
(50, 10, 4, 1200.000),
(51, 10, 5, 100.000),
(52, 10, 6, 1000.000),
(53, 10, 7, 120.000),
(54, 11, 3, 10.000),
(55, 11, 4, 1200.000),
(56, 11, 5, 100.000),
(57, 11, 6, 1000.000),
(58, 11, 7, 120.000),
(59, 12, 3, 10.000),
(60, 12, 4, 1200.000),
(61, 12, 5, 100.000),
(62, 12, 6, 1000.000),
(63, 12, 7, 120.000),
(64, 13, 3, 10.000),
(65, 13, 4, 1700.000),
(66, 13, 5, 100.000),
(67, 13, 6, 1000.000),
(68, 13, 7, 120.000),
(69, 14, 3, 2.750),
(70, 14, 4, 450.000),
(71, 14, 5, 30.000),
(72, 14, 6, 200.000),
(73, 14, 7, 40.000),
(74, 14, 8, 6.000),
(75, 14, 10, 15.000),
(76, 14, 12, 10.000),
(77, 15, 3, 2.750),
(78, 15, 4, 800.000),
(79, 15, 5, 30.000),
(80, 15, 6, 200.000),
(81, 15, 7, 40.000),
(82, 15, 8, 6.000),
(83, 15, 10, 15.000),
(88, 17, 3, 3.500),
(89, 17, 4, 3500.000),
(90, 17, 6, 3500.000),
(91, 17, 8, 60.000),
(92, 17, 10, 15.000),
(93, 18, 3, 3.000),
(94, 18, 4, 2500.000),
(95, 18, 6, 4000.000),
(96, 18, 10, 15.000),
(97, 19, 3, 1.500),
(98, 19, 4, 1500.000),
(99, 19, 6, 1500.000),
(100, 19, 8, 30.000),
(101, 19, 10, 15.000),
(102, 19, 12, 550.000),
(103, 20, 3, 1.000),
(104, 20, 4, 1000.000),
(105, 20, 6, 1000.000),
(106, 20, 8, 20.000),
(107, 20, 10, 10.000),
(108, 21, 3, 7.000),
(109, 21, 4, 250.000),
(110, 21, 5, 140.000),
(111, 21, 6, 100.000),
(112, 21, 7, 40.000),
(113, 22, 3, 5.000),
(114, 22, 4, 500.000),
(115, 22, 5, 50.000),
(116, 22, 6, 500.000),
(117, 22, 7, 50.000),
(118, 23, 3, 5.500),
(119, 23, 4, 250.000),
(120, 23, 5, 110.000),
(121, 23, 6, 150.000),
(122, 23, 7, 75.000),
(123, 24, 3, 2.000),
(124, 24, 4, 1500.000),
(125, 24, 6, 250.000),
(126, 24, 12, 10.000),
(127, 26, 3, 5.000),
(128, 26, 4, 100.000),
(129, 26, 5, 100.000),
(130, 26, 6, 250.000),
(131, 27, 3, 5.000),
(132, 27, 4, 100.000),
(133, 27, 5, 100.000),
(134, 27, 6, 250.000),
(135, 28, 3, 5.000),
(136, 28, 4, 100.000),
(137, 28, 5, 100.000),
(138, 28, 6, 250.000),
(139, 28, 8, 75.000),
(140, 29, 3, 10.000),
(141, 29, 4, 150.000),
(142, 29, 5, 200.000),
(143, 29, 6, 400.000),
(144, 30, 3, 10.000),
(145, 30, 4, 150.000),
(146, 30, 5, 200.000),
(147, 30, 6, 400.000),
(148, 31, 3, 10.000),
(149, 31, 4, 150.000),
(150, 31, 5, 200.000),
(151, 31, 6, 400.000),
(152, 31, 8, 135.000),
(153, 32, 3, 10.000),
(154, 32, 4, 150.000),
(155, 32, 5, 200.000),
(156, 32, 6, 400.000),
(157, 33, 3, 10.000),
(158, 33, 4, 150.000),
(159, 33, 5, 200.000),
(160, 33, 6, 400.000),
(161, 34, 3, 10.000),
(162, 34, 4, 150.000),
(163, 34, 5, 200.000),
(164, 34, 6, 400.000),
(165, 35, 3, 10.000),
(166, 35, 4, 150.000),
(167, 35, 5, 200.000),
(168, 35, 6, 400.000),
(169, 35, 8, 135.000),
(170, 36, 3, 10.000),
(171, 36, 4, 150.000),
(172, 36, 5, 200.000),
(173, 36, 6, 400.000),
(174, 37, 3, 30.000),
(175, 37, 4, 300.000),
(176, 37, 5, 300.000),
(177, 1, 4, 300.000),
(178, 16, 17, 550.000),
(179, 16, 12, 100.000),
(180, 16, 3, 0.180),
(181, 16, 6, 250.000),
(182, 16, 4, 500.000);

-- --------------------------------------------------------

--
-- Table structure for table `production_overhead`
--

CREATE TABLE `production_overhead` (
  `id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `over_head` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_overhead`
--

INSERT INTO `production_overhead` (`id`, `effective_from`, `over_head`, `status`) VALUES
(1, '2025-11-13', 16.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `production_product`
--

CREATE TABLE `production_product` (
  `p_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `product_category` varchar(50) NOT NULL,
  `batch_quantity` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `order_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_product`
--

INSERT INTO `production_product` (`p_id`, `product_name`, `current_price`, `product_category`, `batch_quantity`, `status`, `order_no`) VALUES
(1, 'Slice Bread', 200.00, 'Bakery Items', 18, 1, 1),
(2, 'L Bread', 130.00, 'Bakery Items', 20, 1, 2),
(3, 'S Bread', 70.00, 'Bakery Items', 30, 1, 3),
(4, 'Rost Bread', 60.00, 'Bakery Items', 120, 1, 4),
(5, 'Bun', 60.00, 'Bakery Items', 220, 1, 5),
(6, 'Curry Bun', 80.00, 'Bakery Items', 200, 1, 6),
(7, 'Creem Bun', 80.00, 'Bakery Items', 110, 1, 7),
(8, 'Jam Bun', 80.00, 'Bakery Items', 111, 1, 8),
(9, 'S S Bun', 80.00, 'Bakery Items', 200, 1, 9),
(10, 'Saman Bun', 80.00, 'Bakery Items', 200, 1, 10),
(11, 'Egg Bun', 100.00, 'Bakery Items', 200, 1, 11),
(12, 'Keels Bun', 80.00, 'Bakery Items', 200, 1, 12),
(13, 'Roll Bun', 60.00, 'Bakery Items', 200, 1, 13),
(14, 'Donad', 130.00, 'Bakery Items', 72, 1, 14),
(15, 'T Vadai', 60.00, 'Bakery Items', 72, 1, 15),
(16, 'Browni', 200.00, 'Bakery Items', 30, 1, 16),
(17, 'Butter Cake', 1100.00, 'Bakery Items', 12, 1, 17),
(18, 'Dates Cake', 1100.00, 'Bakery Items', 12, 1, 18),
(19, 'Layer Cake', 1200.00, 'Bakery Items', 6, 1, 19),
(20, 'Sponch', 60.00, 'Bakery Items', 72, 1, 20),
(21, 'Rusk', 1000.00, 'Bakery Items', 65, 1, 21),
(22, 'Baby Rusk', 1000.00, 'Bakery Items', 60, 1, 22),
(23, 'Butter Rusk', 1000.00, 'Bakery Items', 20, 1, 23),
(24, 'Ganakkatha L', 40.00, 'Bakery Items', 96, 1, 24),
(25, 'Ganakkatha S', 20.00, 'Bakery Items', 192, 1, 25),
(26, 'Patties Veg', 40.00, 'Short dish', 300, 1, 26),
(27, 'Patties Saman', 60.00, 'Short dish', 300, 1, 27),
(28, 'Patties Egg', 60.00, 'Short dish', 300, 1, 28),
(29, 'Rolls Veg', 60.00, 'Short dish', 400, 1, 29),
(30, 'Rolls Saman', 80.00, 'Short dish', 400, 1, 30),
(31, 'Rolls Egg', 80.00, 'Short dish', 400, 1, 31),
(32, 'Rolls Chicken', 100.00, 'Short dish', 400, 1, 32),
(33, 'Samosa Veg', 60.00, 'Short dish', 400, 1, 33),
(34, 'Samosa Saman', 80.00, 'Short dish', 400, 1, 34),
(35, 'Samosa Egg', 80.00, 'Short dish', 400, 1, 35),
(36, 'Samosa Chicken', 100.00, 'Short dish', 400, 1, 36),
(37, 'Rotti', 400.00, 'Short dish', 50, 1, 37);

-- --------------------------------------------------------

--
-- Table structure for table `user_device`
--

CREATE TABLE `user_device` (
  `d_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pf_no` varchar(100) NOT NULL,
  `token` varchar(200) NOT NULL,
  `v_from` datetime NOT NULL DEFAULT current_timestamp(),
  `IP` varchar(200) NOT NULL,
  `last_used` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `user_device`
--

INSERT INTO `user_device` (`d_id`, `user_id`, `pf_no`, `token`, `v_from`, `IP`, `last_used`) VALUES
(1, 0, '0740888501', '1f71e393b3809197ed66df836fe833e5', '2025-07-24 05:32:08', '', ''),
(2, 0, '0740888501', '4079016d940210b4ae9ae7d41c4a2065', '2025-07-25 09:20:25', '', ''),
(3, 0, '0753289502', '68148596109e38cf9367d27875e185be', '2025-07-25 09:27:08', '', ''),
(4, 0, '0740888501', 'a0872cc5b5ca4cc25076f3d868e1bdf8', '2025-07-25 13:10:57', '', ''),
(5, 0, '0740888501', 'd6723e7cd6735df68d1ce4c704c29a04', '2025-07-31 20:19:25', '', ''),
(6, 0, '0711986868', '55c567fd4395ecef6d936cf77b8d5b2b', '2025-08-01 10:36:04', '', ''),
(7, 0, '0740888501', 'f63f65b503e22cb970527f23c9ad7db1', '2025-08-03 20:40:02', '', ''),
(8, 0, '0740888501', 'dca5672ff3444c7e997aa9a2c4eb2094', '2025-08-06 15:55:03', '', ''),
(9, 0, '0740888501', '571e0f7e2d992e738adff8b1bd43a521', '2025-08-22 18:33:35', '', ''),
(10, 0, '0740888501', 'e3251075554389fe91d17a794861d47b', '2025-09-14 08:15:33', '', ''),
(11, 0, '0740888501', 'e8dfff4676a47048d6f0c4ef899593dd', '2025-10-11 13:07:01', '', ''),
(12, 0, '0740888501', '61b1fb3f59e28c67f3925f3c79be81a1', '2025-10-18 13:16:51', '', ''),
(13, 0, '0740888501', '7e9e346dc5fd268b49bf418523af8679', '2025-10-21 13:47:20', '', ''),
(14, 0, '0740888501', 'b710915795b9e9c02cf10d6d2bdb688c', '2025-10-21 14:32:19', '', ''),
(15, 0, '0740888501', '19de10adbaa1b2ee13f77f679fa1483a', '2025-10-25 17:50:58', '', ''),
(16, 0, '0740888501', '4da04049a062f5adfe81b67dd755cecc', '2025-10-29 08:55:43', '', ''),
(17, 0, '0740888501', '90599c8fdd2f6e7a03ad173e2f535751', '2025-11-02 17:45:33', '', ''),
(18, 0, '0740888501', 'f60bb6bb4c96d4df93c51bd69dcc15a0', '2025-11-02 19:01:53', '', ''),
(19, 0, '0740888501', 'f442d33fa06832082290ad8544a8da27', '2025-11-19 10:25:23', '', ''),
(20, 0, '0740888501', '15231a7ce4ba789d13b722cc5c955834', '2025-11-19 21:09:32', '', ''),
(21, 0, '0740888501', 'a223c6b3710f85df22e9377d6c4f7553', '2025-11-26 09:48:32', '', ''),
(22, 0, '0740888501', 'c45008212f7bdf6eab6050c2a564435a', '2025-11-29 07:41:26', '', ''),
(23, 0, '0740888501', '36a16a2505369e0c922b6ea7a23a56d2', '2025-12-02 09:03:21', '', ''),
(24, 0, '0740888501', '4921f95baf824205e1b13f22d60357a1', '2025-12-02 14:48:31', '', ''),
(25, 0, '0740888501', 'd8d31bd778da8bdd536187c36e48892b', '2025-12-03 09:04:59', '', ''),
(26, 0, '0740888501', '05311655a15b75fab86956663e1819cd', '2025-12-06 09:20:26', '', ''),
(27, 0, '0740888501', 'db1915052d15f7815c8b88e879465a1e', '2025-12-09 08:28:35', '', ''),
(28, 0, '0740888501', '97af4fb322bb5c8973ade16764156bed', '2025-12-09 15:04:10', '', ''),
(29, 0, '0740888501', 'e00406144c1e7e35240afed70f34166a', '2025-12-13 21:29:02', '', ''),
(30, 0, '0740888501', 'e53a0a2978c28872a4505bdb51db06dc', '2025-12-13 21:33:48', '', ''),
(31, 0, '0740888501', '883e881bb4d22a7add958f2d6b052c9f', '2025-12-13 21:37:25', '', ''),
(32, 0, '0740888501', '97af4fb322bb5c8973ade16764156bed', '2025-12-13 21:40:16', '', ''),
(33, 0, '0740888501', 'ae614c557843b1df326cb29c57225459', '2025-12-13 21:42:00', '', ''),
(34, 0, '0740888501', '90e1357833654983612fb05e3ec9148c', '2025-12-13 21:43:46', '', ''),
(35, 0, '0770888501', '15d185eaa7c954e77f5343d941e25fbd', '2025-12-13 22:17:31', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_license`
--

CREATE TABLE `user_license` (
  `usr_id` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `mobile_no` varchar(12) NOT NULL,
  `role_id` int(11) NOT NULL,
  `password` varchar(500) NOT NULL,
  `user_rights` varchar(100) NOT NULL,
  `account_status` int(5) NOT NULL DEFAULT 2,
  `i_name` varchar(50) NOT NULL,
  `nic` varchar(30) NOT NULL,
  `company` varchar(500) NOT NULL,
  `token` varchar(112) NOT NULL,
  `dr_token` varchar(112) NOT NULL,
  `last_log_in` varchar(50) NOT NULL,
  `last_token` varchar(180) NOT NULL,
  `material` int(11) NOT NULL,
  `accounts` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `admin` int(11) NOT NULL,
  `report` int(11) NOT NULL,
  `opd` int(11) NOT NULL,
  `ip` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `user_license`
--

INSERT INTO `user_license` (`usr_id`, `customer`, `username`, `mobile_no`, `role_id`, `password`, `user_rights`, `account_status`, `i_name`, `nic`, `company`, `token`, `dr_token`, `last_log_in`, `last_token`, `material`, `accounts`, `store`, `admin`, `report`, `opd`, `ip`) VALUES
(1, 0, '0740888501', '', 1, '$2y$10$nY48qthOlivF5rw0cWRZ2.PgsKMHmkFzKmaDjueMVuqcRBS8cu4X.', '', 1, 'Sys Admin', '', '', 'Expired', '3035', '2025-12-13 22:23:36', '255237bfc367574bf914f98b7066ac767c65bc461c3845639daba5d2d4b7b1fb', 0, 1, 1, 1, 0, 0, 0),
(109, 0, '0770888501', '0770888501', 1, '$2y$10$fZMpMcCgrFXiQHb16FDVZurTdN.PEKCiuOU80Nyx79XSzbpPLrFfy', '', 1, 'Kiritharan', '893121', '', 'Expired', '6987', '2025-12-15 10:08:19', 'f6210e457588df62f3ddab3958803c20bc822eb1bd33e77ef4257965752f0007', 0, 1, 1, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_location`
--

CREATE TABLE `user_location` (
  `id` int(11) NOT NULL,
  `usr_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `user_location`
--

INSERT INTO `user_location` (`id`, `usr_id`, `location_id`) VALUES
(36, 95, 4),
(37, 94, 4),
(38, 94, 3),
(39, 96, 3),
(40, 97, 3),
(41, 98, 3),
(85, 100, 35),
(179, 101, 38),
(180, 101, 42),
(181, 104, 38),
(182, 104, 39),
(183, 104, 41),
(184, 104, 40),
(186, 1, 2),
(188, 109, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

CREATE TABLE `user_log` (
  `id` int(11) NOT NULL,
  `ben_id` int(11) DEFAULT NULL,
  `usr_id` int(11) NOT NULL,
  `module` int(11) NOT NULL,
  `location` int(11) NOT NULL,
  `action` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `detail` varchar(2500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `log_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`id`, `ben_id`, `usr_id`, `module`, `location`, `action`, `detail`, `log_date`) VALUES
(1, NULL, 1, 1, 45, 'New User Created', 'User kiti created by admin', '2025-11-28 14:00:12'),
(2, NULL, 1, 1, 45, 'New User Created', 'User Kiritharan created by admin', '2025-11-28 15:51:34'),
(3, NULL, 1, 0, 45, 'Updated Profile', 'User Kiritharan updated by admin', '2025-11-28 16:00:48'),
(4, 27, 1, 2, 40, 'LTL Add Land', 'Land ID=1 | Ben ID=27  | Address=1212 | Extent= hectares', '2025-11-28 16:24:43'),
(5, 27, 1, 2, 40, 'LTL Edit Land', 'Land ID=1 | Extent:  > 2 | Extent ha:  > 2.000000', '2025-11-28 16:24:47'),
(6, 27, 1, 2, 40, 'LTL Create Lease', 'Created lease: Pending File No: DS/TR/LS/01', '2025-11-28 16:25:16'),
(7, 27, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=1 | Lease No=Pending | Changes: start_date: 2020-01-01 > 2020-01-02 | end_date: 2050-01-01 > 2050-01-02', '2025-11-28 16:25:38'),
(8, 27, 1, 2, 40, 'LTL New Payment', 'Lease ID=1 | Amount=5,000.00 | Rec No=50 | Method=cash | Date=2025-11-28 | Discount=NO', '2025-11-28 16:30:28'),
(9, 27, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: 50, Amount: 5000.00, Lease_file: DS/TR/LS/01', '2025-11-28 16:30:36'),
(10, 27, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=1 | Lease No=Pending | Changes: valuation_date: null > 2021-01-01', '2025-11-28 16:31:05'),
(11, 27, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=1, Schedule ID=92, Amount=1000.00, Old Penalty=1000.00, New Penalty=0.00', '2025-11-28 16:31:43'),
(12, 27, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=1, Lease ID=1, Schedule ID=92, Amount=1000.00', '2025-11-28 16:32:38'),
(13, NULL, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Name tamil:  > Karunakaran Sivalingam. | Address: 999 School road , Trincomalee ssadsadsad > 999 School road , Trincomalee ssadsadsad\\n', '2025-11-28 19:02:37'),
(14, NULL, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Name sinhala:  > Karunakaran Sivalingam. | Address: 999 School road , Trincomalee ssadsadsad > 999 School road , Trincomalee ssadsadsad\\n', '2025-11-28 19:02:45'),
(15, NULL, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Name: Karunakaran Sivalingam. > Karunakaran Sivalingam | Name tamil: Karunakaran Sivalingam. > ????????? ?????????? | Name sinhala: Karunakaran Sivalingam. > ????????? ????????? | Address: 999 School road , Trincomalee ssadsadsad > 999 School road , Trincomalee \\n', '2025-11-28 19:11:38'),
(16, NULL, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Address: 999 School road , Trincomalee > 999 School road , Trincomalee \\n', '2025-11-28 19:25:34'),
(17, NULL, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Address: 999 School road , Trincomalee > 999 School road , Trincomalee \\n | Address tamil:  > ????????? ?????????? | Address sinhala:  > ????????? ?????????', '2025-11-28 19:27:39'),
(18, 27, 1, 2, 40, 'LTL Document Uploaded', 'Ben ID=27 | File Type=1 | File URL=/files/40/1_24d7a0b831b6e6cac3efa75151e0d12b_20251128211629.pdf', '2025-11-28 21:16:29'),
(19, 27, 1, 2, 40, 'LTL Document Uploaded', 'Ben ID=27 | File Type=2 | File URL=/files/40/2_24d7a0b831b6e6cac3efa75151e0d12b_20251128211708.pdf', '2025-11-28 21:17:08'),
(20, NULL, 1, 2, 40, 'LTL Document Updated', 'Ben ID=27 | File Type=2 | File URL=/files/40/2_24d7a0b831b6e6cac3efa75151e0d12b_20251128211708.pdf', '2025-11-28 21:17:08'),
(21, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-11-29 06:47:51'),
(22, 27, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=1 | Lease No=Pending | Changes: valuation_date: null > 2021-01-01 | end_date: 2050-01-02 > 2080-01-02 | duration_years: 30.00 > 60.00', '2025-11-29 06:49:21'),
(23, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-11-29 07:32:44'),
(24, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-11-29 07:40:59'),
(25, NULL, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Address: 999 School road , Trincomalee > 999 School road , Trincomalee \\n | Address tamil: கருணாகரன் சிவலிங்கம் > கருணாகரன் சிவலிங்கம்.', '2025-11-29 07:44:43'),
(26, 27, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Address: 999 School road , Trincomalee > 999 School road , Trincomalee \\n | Address tamil: கருணாகரன் சிவலிங்கம். > கருணாகரன் சிவலிங்கம்', '2025-11-29 07:46:54'),
(27, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 09:00:18'),
(28, 27, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=27 | Address: 999 School road , Trincomalee > 999 School road , Trincomalee \\n | Address sinhala: කරුණාකරන් සිවලිංගම් > 898 ප්රධාන වීදිය, ත්රිකුණාමලය', '2025-12-02 09:41:22'),
(29, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:19:58'),
(30, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:23:17'),
(31, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:23:55'),
(32, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:26:25'),
(33, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:28:55'),
(34, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:29:33'),
(35, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:32:27'),
(36, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-02 14:48:09'),
(37, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-03 09:04:42'),
(38, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-03 10:02:42'),
(39, 27, 1, 2, 40, 'LTL New Payment', 'Lease ID=1 | Amount=46,000.00 | Rec No=sss | Method=cash | Date=2025-12-03 | Discount=NO', '2025-12-03 11:11:15'),
(40, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-03 17:57:31'),
(41, 28, 1, 2, 40, 'LTL Add Land', 'Land ID=2 | Ben ID=28  | Address=50 | Extent= hectares', '2025-12-03 20:03:05'),
(42, 28, 1, 2, 40, 'LTL Create Lease', 'Created lease: 12/asdsad File No: DS/TR/LS/45', '2025-12-03 20:05:20'),
(43, 28, 1, 2, 40, 'LTL New Payment', 'Lease ID=2 | Amount=1,372,000.00 | Rec No=s | Method=cash | Date=2025-12-03 | Discount=NO', '2025-12-03 20:05:34'),
(44, 28, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 1372000.00, Lease_file: DS/TR/LS/45', '2025-12-03 20:28:00'),
(45, 28, 1, 2, 40, 'LTL New Payment', 'Lease ID=2 | Amount=1,372,000.00 | Rec No=s | Method=cash | Date=2025-12-03 | Discount=NO', '2025-12-03 20:28:04'),
(46, 28, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 1372000.00, Lease_file: DS/TR/LS/45', '2025-12-03 20:28:13'),
(47, 28, 1, 2, 40, 'LTL New Payment', 'Lease ID=2 | Amount=1,372,000.00 | Rec No=sw | Method=cash | Date=2025-12-03 | Discount=NO', '2025-12-03 20:29:44'),
(48, 28, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: sw, Amount: 1372000.00, Lease_file: DS/TR/LS/45', '2025-12-03 20:30:01'),
(49, 28, 1, 2, 40, 'LTL New Payment', 'Lease ID=2 | Amount=1,372,000.00 | Rec No=s | Method=cash | Date=2025-12-03 | Discount=NO', '2025-12-03 20:32:28'),
(50, 28, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 1372000.00, Lease_file: DS/TR/LS/45', '2025-12-03 20:32:45'),
(51, 28, 1, 2, 40, 'LTL New Payment', 'Lease ID=2 | Amount=1,446,000.00 | Rec No=12 | Method=cash | Date=2025-12-03 | Discount=NO', '2025-12-03 20:46:15'),
(52, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-04 20:47:00'),
(53, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-05 08:33:42'),
(54, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-05 08:56:21'),
(55, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-06 09:20:01'),
(56, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-06 16:56:16'),
(57, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-09 08:28:07'),
(58, 27, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=1 | Lease No=Pending | Changes: valuation_date: null > 2021-01-01 | end_date: 2080-01-02 > 2050-01-02 | duration_years: 60.00 > 30.00', '2025-12-09 09:09:47'),
(59, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-09 15:03:24'),
(60, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-09 15:04:18'),
(61, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-09 15:07:20'),
(62, 47, 1, 2, 40, 'LTL Beneficiary Created', 'ID=47 Name=Krunakaran K', '2025-12-09 15:11:57'),
(63, 47, 1, 2, 40, 'LTL Add Land', 'Land ID=3 | Ben ID=47  | Address=454 AASDSADSAD | Extent=50 perch', '2025-12-09 15:15:12'),
(64, 47, 1, 2, 40, 'LTL Create Lease', 'Created lease: Pending File No: DS/TR/LS/5/501', '2025-12-09 15:17:52'),
(65, 47, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=3 | Lease No=Pending | Changes: valuation_date: null > 2022-01-10', '2025-12-09 15:19:03'),
(66, 47, 1, 2, 40, 'LTL New Payment', 'Lease ID=3 | Amount=100,000.00 | Rec No=AAS11111 | Method=cash | Date=2023-12-09 | Discount=NO', '2025-12-09 15:21:29'),
(67, 47, 1, 2, 40, 'LTL Add Field Visits', 'Added field visit: id=1  | date=3 | officers=2025-12-10 | status=asd', '2025-12-09 15:22:14'),
(68, 47, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=3, Schedule ID=273, Amount=48000.00, Old Penalty=48000.00, New Penalty=0.00', '2025-12-09 15:23:01'),
(69, 47, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=2, Lease ID=3, Schedule ID=273, Amount=48000.00', '2025-12-09 15:33:03'),
(70, 47, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=3 | Lease No=45645/154 | Changes: valuation_date: null > 2022-01-10 | lease_number: Pending > 45645/154', '2025-12-09 15:33:17'),
(71, 47, 1, 2, 40, 'LTL Beneficiary Edited', 'ID=47 | Name sinhala: கருணாகரன் கே auto_awesome Translate from: Hindi 12 / 5,000 කරුණාකරන් කේ > කරුණාකරන් කේ', '2025-12-09 15:38:21'),
(72, 47, 1, 2, 40, 'LTL Add Reminders', 'Added reminder: lease_id=3 | type=Annexure 09 | sent_date=2025-11-09 ', '2025-12-09 15:41:19'),
(73, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-09 19:40:58'),
(74, 35, 1, 2, 40, 'LTL Add Land', 'Land ID=4 | Ben ID=35  | Address=Wilgama | Extent=0.1012 hectares', '2025-12-09 19:43:29'),
(75, 35, 1, 2, 40, 'LTL Create Lease', 'Created lease: Pending File No: DS/TR/LS/', '2025-12-09 19:45:35'),
(76, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=64,000.00 | Rec No=test | Method=cash | Date=2025-11-16 | Discount=NO', '2025-12-09 20:46:04'),
(77, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: test, Amount: 64000.00, Lease_file: DS/TR/LS/', '2025-12-09 20:47:15'),
(78, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=64,000.00 | Rec No=12 | Method=cash | Date=2025-06-11 | Discount=NO', '2025-12-09 20:47:55'),
(79, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: 12, Amount: 64000.00, Lease_file: DS/TR/LS/', '2025-12-09 20:50:34'),
(80, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=104,000.00 | Rec No=sadsad | Method=cash | Date=2024-06-11 | Discount=NO', '2025-12-09 20:51:12'),
(81, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: sadsad, Amount: 104000.00, Lease_file: DS/TR/LS/', '2025-12-09 21:57:12'),
(82, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=64,000.00 | Rec No=dsf | Method=cash | Date=2025-06-11 | Discount=NO', '2025-12-09 21:57:41'),
(83, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: dsf, Amount: 64000.00, Lease_file: DS/TR/LS/', '2025-12-09 22:14:32'),
(84, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=64,000.00 | Rec No=sd | Method=cash | Date=2025-06-11 | Discount=NO', '2025-12-09 22:15:07'),
(85, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: sd, Amount: 64000.00, Lease_file: DS/TR/LS/', '2025-12-09 22:50:15'),
(86, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=64,000.00 | Rec No=sss | Method=cash | Date=2025-06-11 | Discount=NO', '2025-12-09 22:50:36'),
(87, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-10 07:06:18'),
(88, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: sss, Amount: 64000.00, Lease_file: DS/TR/LS/', '2025-12-10 08:03:18'),
(89, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=48,000.00 | Rec No=ss | Method=cash | Date=2025-09-08 | Discount=NO', '2025-12-10 08:04:11'),
(90, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=16,000.00 | Rec No=s | Method=cash | Date=2025-09-10 | Discount=NO', '2025-12-10 08:11:23'),
(91, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 16000.00, Lease_file: DS/TR/LS/', '2025-12-10 08:11:50'),
(92, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=16,000.00 | Rec No=dsd | Method=cash | Date=2025-10-09 | Discount=YES', '2025-12-10 08:12:20'),
(93, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: ss, Amount: 48000.00, Lease_file: DS/TR/LS/', '2025-12-10 08:15:03'),
(94, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: dsd, Amount: 16000.00, Lease_file: DS/TR/LS/', '2025-12-10 08:15:07'),
(95, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=32,000.00 | Rec No=s | Method=cash | Date=2023-09-21 | Discount=NO', '2025-12-10 08:16:06'),
(96, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=16,000.00 | Rec No=xs | Method=cash | Date=2024-09-21 | Discount=YES', '2025-12-10 08:17:39'),
(97, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: xs, Amount: 16000.00, Lease_file: DS/TR/LS/', '2025-12-10 10:15:36'),
(98, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 32000.00, Lease_file: DS/TR/LS/', '2025-12-10 10:15:39'),
(99, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=40,000.00 | Rec No=12 | Method=cash | Date=2024-08-19 | Discount=YES', '2025-12-10 10:21:00'),
(100, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: 12, Amount: 40000.00, Lease_file: DS/TR/LS/', '2025-12-10 10:32:31'),
(101, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=32,000.00 | Rec No=12 | Method=cash | Date=2023-09-24 | Discount=YES', '2025-12-10 10:33:41'),
(102, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=14,222.22 | Rec No=aaa | Method=cash | Date=2025-09-10 | Discount=NO', '2025-12-10 11:11:14'),
(103, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: aaa, Amount: 14222.22, Lease_file: DS/TR/LS/', '2025-12-10 12:08:20'),
(104, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=105,422.22 | Rec No=s | Method=cash | Date=2025-12-10 | Discount=YES', '2025-12-10 12:15:35'),
(105, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: 12, Amount: 32000.00, Lease_file: DS/TR/LS/', '2025-12-10 12:16:34'),
(106, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 105422.22, Lease_file: DS/TR/LS/', '2025-12-10 13:39:26'),
(107, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 105422.22, Lease_file: DS/TR/LS/', '2025-12-10 13:39:29'),
(108, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 105422.22, Lease_file: DS/TR/LS/', '2025-12-10 13:49:57'),
(109, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=Pending | Changes: valuation_date: null > 2023-12-19', '2025-12-10 13:50:30'),
(110, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=128,000.00 | Rec No=as | Method=cash | Date=2023-12-10 | Discount=NO', '2025-12-10 14:39:48'),
(111, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: as, Amount: 128000.00, Lease_file: DS/TR/LS/', '2025-12-10 14:41:10'),
(112, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=200,000.00 | Rec No=ss | Method=cash | Date=2020-12-07 | Discount=NO', '2025-12-10 14:41:34'),
(113, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: ss, Amount: 200000.00, Lease_file: DS/TR/LS/', '2025-12-10 14:44:13'),
(114, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=185,600.00 | Rec No=sas | Method=cash | Date=2018-09-08 | Discount=NO', '2025-12-10 14:45:08'),
(115, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=431,680.00 | Rec No=as | Method=cash | Date=2025-12-10 | Discount=YES', '2025-12-10 14:54:40'),
(116, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: as, Amount: 431680.00, Lease_file: DS/TR/LS/', '2025-12-10 14:58:03'),
(117, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: sas, Amount: 185600.00, Lease_file: DS/TR/LS/', '2025-12-10 14:59:49'),
(118, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=185,600.00 | Rec No=s | Method=cash | Date=2018-12-09 | Discount=NO', '2025-12-10 15:00:24'),
(119, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=Pending | Changes: valuation_date: null > 2014-12-19 | start_date: 2012-09-20 > 2009-09-20 | end_date: 2042-09-20 > 2039-09-20 | premium: 48000.00 > null', '2025-12-10 15:15:27'),
(120, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 185600.00, Lease_file: DS/TR/LS/', '2025-12-10 15:15:51'),
(121, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=Pending | Changes: valuation_date: null > 2011-12-19 | premium: 48000.00 > null', '2025-12-10 15:16:14'),
(122, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=310,400.00 | Rec No=h | Method=cash | Date=2018-12-08 | Discount=NO', '2025-12-10 15:17:25'),
(123, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: h, Amount: 310400.00, Lease_file: DS/TR/LS/', '2025-12-10 15:56:48'),
(124, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=224,800.00 | Rec No=a | Method=cash | Date=2016-08-10 | Discount=NO', '2025-12-10 15:57:25'),
(125, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: a, Amount: 224800.00, Lease_file: DS/TR/LS/', '2025-12-10 16:11:46'),
(126, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=224,800.00 | Rec No=s | Method=cash | Date=2016-09-18 | Discount=NO', '2025-12-10 17:02:35'),
(127, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 224800.00, Lease_file: DS/TR/LS/', '2025-12-10 17:18:23'),
(128, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=224,800.00 | Rec No=s | Method=cash | Date=2016-06-18 | Discount=YES', '2025-12-10 17:19:35'),
(129, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 224800.00, Lease_file: DS/TR/LS/', '2025-12-10 17:25:04'),
(130, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=636, Amount=12800.00, Old Penalty=12800.00, New Penalty=0.00', '2025-12-10 17:25:23'),
(131, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=637, Amount=15200.00, Old Penalty=15200.00, New Penalty=0.00', '2025-12-10 17:25:35'),
(132, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=4, Lease ID=4, Schedule ID=637, Amount=15200.00', '2025-12-10 17:25:54'),
(133, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=3, Lease ID=4, Schedule ID=636, Amount=12800.00', '2025-12-10 17:25:58'),
(134, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=64,000.00 | Rec No=ss | Method=cash | Date=2009-09-30 | Discount=YES', '2025-12-10 17:39:33'),
(135, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: ss, Amount: 64000.00, Lease_file: DS/TR/LS/', '2025-12-10 17:58:41'),
(136, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=762,000.00 | Rec No=s | Method=cash | Date=2025-12-10 | Discount=NO', '2025-12-10 18:05:47'),
(137, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 762000.00, Lease_file: DS/TR/LS/', '2025-12-10 18:16:29'),
(138, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=50,000.00 | Rec No=sad | Method=cash | Date=2025-12-10 | Discount=NO', '2025-12-10 18:46:20'),
(139, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: sad, Amount: 50000.00, Lease_file: DS/TR/LS/', '2025-12-10 19:24:03'),
(140, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=231,200.00 | Rec No=fdfg | Method=cash | Date=2017-12-08 | Discount=NO', '2025-12-10 19:25:43'),
(141, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=Pending | Changes: valuation_date: null > 2010-12-19 | premium: 48000.00 > null', '2025-12-10 19:35:16'),
(142, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: fdfg, Amount: 231200.00, Lease_file: DS/TR/LS/', '2025-12-10 19:36:24'),
(143, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=Pending | Changes: premium: 48000.00 > null', '2025-12-10 19:49:08'),
(144, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=5,000.00 | Rec No=s | Method=cash | Date=2025-12-10 | Discount=NO', '2025-12-10 20:14:08'),
(145, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 5000.00, Lease_file: DS/TR/LS/', '2025-12-10 20:15:21'),
(146, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 5000.00, Lease_file: DS/TR/LS/', '2025-12-10 20:15:28'),
(147, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 5000.00, Lease_file: DS/TR/LS/', '2025-12-10 20:15:40'),
(148, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=Pending | Changes: valuation_date: null > 2010-12-19 | premium: 48000.00 > null', '2025-12-10 20:24:04'),
(149, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=Pending | Changes: premium: 48000.00 > null', '2025-12-10 20:24:35'),
(150, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: premium: 48000.00 > null | lease_number: Pending > test penalty', '2025-12-10 20:25:27'),
(151, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2025-12-19 | premium: 48000.00 > null', '2025-12-10 20:25:41'),
(152, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2012-12-19 | premium: 48000.00 > null', '2025-12-10 20:53:30'),
(153, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=863,520.00 | Rec No=s | Method=cash | Date=2025-12-10 | Discount=NO', '2025-12-10 21:29:18'),
(154, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2012-12-19 | start_date: 2009-09-20 > 2009-09-19 | end_date: 2039-09-20 > 2039-09-19 | premium: 48000.00 > null', '2025-12-10 21:31:36'),
(155, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=96,000.00 | Rec No=dasd | Method=cash | Date=2012-08-10 | Discount=NO', '2025-12-10 21:32:58'),
(156, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2014-12-19 | premium: 48000.00 > null', '2025-12-10 22:23:43'),
(157, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2017-12-19 | premium: 48000.00 > null', '2025-12-10 22:26:03'),
(158, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2014-12-19 | premium: 48000.00 > null', '2025-12-10 22:32:46'),
(159, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2009-12-19 | premium: 48000.00 > null', '2025-12-10 22:33:09'),
(160, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: dasd, Amount: 96000.00, Lease_file: DS/TR/LS/', '2025-12-10 22:36:15'),
(161, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2023-12-19 | start_date: 2009-09-19 > 2021-09-19 | end_date: 2039-09-19 > 2051-09-19 | premium: 48000.00 > null', '2025-12-10 22:37:15'),
(162, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=32,000.00 | Rec No=discount  | Method=cash | Date=2022-09-20 | Discount=YES', '2025-12-10 22:39:50'),
(163, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2020-12-19 | start_date: 2021-09-19 > 2020-09-19 | end_date: 2051-09-19 > 2050-09-19', '2025-12-10 22:48:11'),
(164, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=82,080.00 | Rec No=s | Method=cash | Date=2025-12-10 | Discount=NO', '2025-12-10 22:49:55'),
(165, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 82080.00, Lease_file: DS/TR/LS/', '2025-12-10 22:50:13'),
(166, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=82,080.00 | Rec No=s | Method=cash | Date=2025-10-10 | Discount=YES', '2025-12-10 22:50:50'),
(167, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-11 05:23:09'),
(168, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 82080.00, Lease_file: DS/TR/LS/', '2025-12-11 05:43:20'),
(169, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: discount , Amount: 32000.00, Lease_file: DS/TR/LS/', '2025-12-11 05:43:23'),
(170, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=902, Amount=1600.00, Old Penalty=1600.00, New Penalty=0.00', '2025-12-11 05:43:29'),
(171, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=5, Lease ID=4, Schedule ID=902, Amount=1600.00', '2025-12-11 05:43:42'),
(172, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=902, Amount=1600.00, Old Penalty=1600.00, New Penalty=0.00', '2025-12-11 05:43:51'),
(173, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=123,200.00 | Rec No=s | Method=cash | Date=2025-12-11 | Discount=NO', '2025-12-11 06:17:45'),
(174, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=6, Lease ID=4, Schedule ID=902, Amount=1600.00', '2025-12-11 06:18:33'),
(175, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: s, Amount: 123200.00, Lease_file: DS/TR/LS/', '2025-12-11 06:19:36'),
(176, 35, 1, 2, 40, 'LTL New Payment', 'Lease ID=4 | Amount=123,200.00 | Rec No=asas | Method=cash | Date=2025-12-11 | Discount=NO', '2025-12-11 06:22:53'),
(177, 35, 1, 2, 40, 'LTL Cancel Payment', 'Cancelled payment: asas, Amount: 123200.00, Lease_file: DS/TR/LS/', '2025-12-11 06:23:05'),
(178, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=902, Amount=600.00, Old Penalty=1600.00, New Penalty=1000.00', '2025-12-11 06:23:18'),
(179, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=902, Amount=1000.00, Old Penalty=1000.00, New Penalty=0.00', '2025-12-11 06:23:27'),
(180, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=905, Amount=400.00, Old Penalty=6400.00, New Penalty=6000.00', '2025-12-11 06:37:48'),
(181, 35, 1, 2, 40, 'LTL Write Off Penalty', 'Written off penalty: Lease ID=4, Schedule ID=903, Amount=3200.00, Old Penalty=3200.00, New Penalty=0.00', '2025-12-11 06:40:54'),
(182, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=7, Lease ID=4, Schedule ID=902, Amount=600.00', '2025-12-11 06:42:51'),
(183, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=8, Lease ID=4, Schedule ID=902, Amount=1000.00', '2025-12-11 06:42:55'),
(184, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=9, Lease ID=4, Schedule ID=905, Amount=400.00', '2025-12-11 06:42:58'),
(185, 35, 1, 2, 40, 'LTL Cancel Write Off', 'Cancelled write-off: ID=10, Lease ID=4, Schedule ID=903, Amount=3200.00', '2025-12-11 06:43:01'),
(186, 35, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=4 | Lease No=test penalty | Changes: valuation_date: null > 2011-12-19 | start_date: 2020-09-19 > 2007-09-19 | end_date: 2050-09-19 > 2037-09-19', '2025-12-11 06:43:30'),
(187, 35, 1, 2, 40, 'LTL Edit Premium', 'Premium amount changed: Lease ID=4, Schedule ID=931, Old Amount=48000.00, New Amount=8000.00', '2025-12-11 06:43:40'),
(188, 35, 1, 2, 40, 'LTL Cancel Premium Change', 'Cancelled premium change: ID=1, Lease ID=4, Schedule ID=931, Old Amount=48000.00, New Amount=8000.00', '2025-12-11 06:44:44'),
(189, 35, 1, 2, 40, 'LTL Edit Premium', 'Premium amount changed: Lease ID=4, Schedule ID=931, Old Amount=48000.00, New Amount=8000.00', '2025-12-11 06:44:57'),
(190, 35, 1, 2, 40, 'LTL Edit Land', 'Land ID=4 | LandBoundary: [{\"lat\":\"\",\"lng\":\"\"},{\"lat\":\"\",\"lng\":\"\"},{\"lat\":\"\",\"lng\":\"\"},{\"lat\":\"\",\"lng\":\"\"}] > [{\"lat\":8.606742,\"lng\":81.206251},{\"lat\":8.606858,\"lng\":81.206412},{\"lat\":8.606742,\"lng\":81.20652},{\"lat\":8.606604,\"lng\":81.206412}]', '2025-12-11 08:14:20'),
(191, 35, 1, 2, 40, 'LTL Edit Premium', 'Premium amount changed: Lease ID=4, Schedule ID=931, Old Amount=8000.00, New Amount=0.00', '2025-12-11 08:14:58'),
(192, 40, 1, 2, 40, 'LTL Add Land', 'Land ID=5 | Ben ID=40  | Address=test | Extent=45 hectares', '2025-12-11 08:31:22'),
(193, 40, 1, 2, 40, 'LTL Create Lease', 'Created lease: Pending File No: DS/TR/LS/', '2025-12-11 08:50:33'),
(194, 40, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=5 | Lease No=Pending | Changes: annual_rent_percentage: 2.00 > 4.00 | premium: 30000.00 > null', '2025-12-11 12:06:54'),
(195, 40, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=5 | Lease No=Pending | Changes: annual_rent_percentage: 2.00 > 4.00 | premium: 30000.00 > null', '2025-12-11 14:53:25'),
(196, 29, 1, 2, 40, 'LTL Add Land', 'Land ID=6 | Ben ID=29  | Address=12 | Extent= hectares', '2025-12-11 17:33:29'),
(197, 29, 1, 2, 40, 'LTL Create Lease', 'Created lease: 45678/45 File No: DS/TR/LS/1234', '2025-12-11 17:34:23'),
(198, 30, 1, 2, 40, 'LTL Add Land', 'Land ID=7 | Ben ID=30  | Address=23 | Extent= hectares', '2025-12-11 17:44:10'),
(199, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-12 09:57:23'),
(200, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-12 17:39:21'),
(201, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 12:02:21'),
(202, 29, 1, 2, 40, 'LTL Add Reminders', 'Added reminder: lease_id=6 | type=Annexure 09 | sent_date=2025-12-13 ', '2025-12-13 13:31:56'),
(203, 27, 1, 2, 40, 'LTL Lease Updated', 'Lease ID=1 | Lease No=Pending | Changes: valuation_date: null > 2021-01-01 | revision_period: 5 > null | revision_percentage: 20.00 > null', '2025-12-13 18:45:19'),
(204, 27, 1, 2, 40, 'LTL Add Reminders', 'Added reminder: lease_id=1 | type=Annexure 09 | sent_date=2025-12-13 ', '2025-12-13 19:35:05'),
(205, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:24:35'),
(206, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:26:28'),
(207, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:27:19'),
(208, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:33:34'),
(209, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:37:12'),
(210, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:39:59'),
(211, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:40:25'),
(212, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:41:49'),
(213, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:43:27'),
(214, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:43:53'),
(215, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 21:45:19'),
(216, NULL, 1, 1, 2, 'New User Created', 'User Kiritharan created by admin', '2025-12-13 22:14:37'),
(217, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:17:10'),
(218, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:19:11'),
(219, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:19:20'),
(220, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:19:51'),
(221, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:20:04'),
(222, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:21:25'),
(223, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:21:30'),
(224, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:22:12'),
(225, NULL, 1, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:23:36'),
(226, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:23:59'),
(227, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:25:38'),
(228, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:25:42'),
(229, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:26:05'),
(230, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-13 22:26:52'),
(231, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-14 06:24:56'),
(232, NULL, 109, 0, 2, 'Created', 'Material ID: 0 - Suger', '2025-12-14 07:33:36'),
(233, NULL, 109, 0, 2, 'Created', 'Product ID: 1 - Bun', '2025-12-14 08:46:51'),
(234, NULL, 109, 0, 2, 'Updated', 'Product ID: 1 - Bun', '2025-12-14 08:46:58'),
(235, NULL, 109, 0, 2, 'Inactivated', 'Product ID: 1', '2025-12-14 08:47:06'),
(236, NULL, 109, 0, 2, 'Material Allocation', 'Product ID: 5, Allocated materials: 1', '2025-12-14 09:31:13'),
(237, NULL, 109, 0, 2, 'Material Allocation', 'Product ID: 16, Allocated materials: 5', '2025-12-14 10:18:39'),
(238, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-14 12:15:16'),
(239, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 12:54:18'),
(240, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 12:55:44'),
(241, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:18:40'),
(242, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:19:28'),
(243, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:19:39'),
(244, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:20:14'),
(245, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:28:57'),
(246, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:35:44'),
(247, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:38:25'),
(248, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:54:14'),
(249, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:59:18'),
(250, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 13:59:45'),
(251, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 14:00:10'),
(252, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 14:00:22'),
(253, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 14:02:12'),
(254, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 14:02:27'),
(255, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 14:13:18'),
(256, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 14:13:31'),
(257, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 14:20:42'),
(258, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-11', '2025-12-14 14:48:51'),
(259, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-10', '2025-12-14 14:57:48'),
(260, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-10', '2025-12-14 14:58:04'),
(261, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 15:05:00'),
(262, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-09', '2025-12-14 15:05:16'),
(263, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 15:06:01'),
(264, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 15:09:41'),
(265, NULL, 109, 0, 2, 'Created', 'Overhead ID: 0', '2025-12-14 15:17:04'),
(266, NULL, 109, 0, 2, 'Created', 'Overhead ID: 0', '2025-12-14 15:17:49'),
(267, NULL, 109, 0, 2, 'Created', 'Overhead ID: 0', '2025-12-14 15:18:18'),
(268, NULL, 109, 0, 2, 'Created', 'Overhead ID: 0', '2025-12-14 15:18:28'),
(269, NULL, 109, 0, 2, 'Created', 'Overhead ID: 0', '2025-12-14 15:18:46'),
(270, NULL, 109, 0, 2, 'Created', 'Overhead ID: 0', '2025-12-14 15:21:40'),
(271, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 3', '2025-12-14 15:27:08'),
(272, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 3', '2025-12-14 15:27:20'),
(273, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 1', '2025-12-14 15:27:30'),
(274, NULL, 109, 0, 2, 'Created', 'Overhead ID: 7', '2025-12-14 15:27:40'),
(275, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 7', '2025-12-14 15:27:51'),
(276, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 7', '2025-12-14 15:30:12'),
(277, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 7', '2025-12-14 15:30:26'),
(278, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 2', '2025-12-14 15:30:54'),
(279, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 6', '2025-12-14 15:33:33'),
(280, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-07', '2025-12-14 15:51:49'),
(281, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 15:54:06'),
(282, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 15:56:33'),
(283, NULL, 109, 0, 2, 'Updated', 'Overhead ID: 1', '2025-12-14 16:07:40'),
(284, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-06', '2025-12-14 16:23:06'),
(285, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-06', '2025-12-14 16:23:16'),
(286, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 16:44:06'),
(287, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 16:45:13'),
(288, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 17:25:04'),
(289, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-14', '2025-12-14 17:26:25'),
(290, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-13', '2025-12-14 19:12:09'),
(291, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-13', '2025-12-14 19:12:31'),
(292, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-14 19:23:15'),
(293, NULL, 109, 0, 2, 'Save', 'Daily production saved for 2025-12-13', '2025-12-14 19:35:06'),
(294, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-15 10:07:33'),
(295, NULL, 109, 0, 0, 'Login', 'Login from ::1', '2025-12-15 10:08:19'),
(296, NULL, 109, 0, 2, 'Created', 'Contact ID: 1 - Astra', '2025-12-15 11:34:45'),
(297, NULL, 109, 0, 2, 'Updated', 'Contact ID: 1 - Astra', '2025-12-15 11:44:28'),
(298, NULL, 109, 0, 2, 'Deactivated', 'Contact ID: 1', '2025-12-15 11:52:51'),
(299, NULL, 109, 0, 2, 'Activated', 'Contact ID: 1', '2025-12-15 11:53:01'),
(300, NULL, 109, 0, 2, 'Created', 'Cheque ID: 1 - 5025', '2025-12-15 12:53:53'),
(301, NULL, 109, 0, 2, 'Created', 'Cheque ID: 2 - 2', '2025-12-15 12:56:15'),
(302, NULL, 109, 0, 2, 'Created', 'Cheque ID: 3 - 5', '2025-12-15 12:59:30'),
(303, NULL, 109, 0, 2, 'Created', 'Cheque ID: 4 - 15', '2025-12-15 13:11:38'),
(304, NULL, 109, 0, 2, 'Created', 'Cheque ID: 5 - sar', '2025-12-15 13:11:58'),
(305, NULL, 109, 0, 2, 'Created', 'Cheque ID: 6 - 1212', '2025-12-15 13:12:13'),
(306, NULL, 109, 0, 2, 'Date Changed', 'Cheque ID: 3 to date: 2026-01-02', '2025-12-15 13:13:02'),
(307, NULL, 109, 0, 2, 'Created', 'Contact ID: 2 - Me test payee', '2025-12-15 14:26:06'),
(308, NULL, 109, 0, 2, 'Created', 'Cheque ID: 7 - 5000', '2025-12-15 14:30:47'),
(309, NULL, 109, 0, 2, 'Updated', 'Cheque ID: 7 - 5000', '2025-12-15 14:31:02'),
(310, NULL, 109, 0, 2, 'Date Changed', 'Cheque ID: 7 to date: 2025-12-14', '2025-12-15 14:47:48'),
(311, NULL, 109, 0, 2, 'Date Changed', 'Cheque ID: 7 to date: 2026-12-14', '2025-12-15 14:48:01'),
(312, NULL, 109, 0, 2, 'Updated', 'Cheque ID: 3 - 5', '2025-12-15 14:48:26'),
(313, NULL, 109, 0, 2, 'Created', 'Customer ID: 1 - Department of labour', '2025-12-15 15:42:59'),
(314, NULL, 109, 0, 2, 'Updated', 'Customer ID: 1 - Department of labour', '2025-12-15 15:43:40'),
(315, NULL, 109, 0, 2, 'Created', 'Item ID: 38 - test', '2025-12-15 15:54:12'),
(316, NULL, 109, 0, 2, 'Updated', 'Item ID: 38 - twet', '2025-12-15 15:56:52'),
(317, NULL, 109, 0, 2, 'Created', 'Item ID: 39 - wqrwqr', '2025-12-15 15:57:07'),
(318, NULL, 109, 0, 2, 'Created', 'Bill ID: 0', '2025-12-15 16:09:46'),
(319, NULL, 109, 0, 2, 'Created', 'Bill ID: 0', '2025-12-15 16:11:14'),
(320, NULL, 109, 0, 2, 'Created', 'Bill ID: 3', '2025-12-15 17:02:25'),
(321, NULL, 109, 0, 2, 'Created', 'Bill ID: 4', '2025-12-15 17:02:57'),
(322, NULL, 109, 0, 2, 'Updated', 'Bill ID: 4', '2025-12-15 17:05:35'),
(323, NULL, 109, 0, 2, 'Created', 'Customer ID: 2 - Departent of  building', '2025-12-15 17:14:04'),
(324, NULL, 109, 0, 2, 'Updated', 'Bill ID: 3', '2025-12-15 17:14:16'),
(325, NULL, 109, 0, 2, 'Updated', 'Bill ID: 4', '2025-12-15 17:14:38'),
(326, NULL, 109, 0, 2, 'Updated', 'Bill ID: 4', '2025-12-15 17:16:20'),
(327, NULL, 109, 0, 2, 'Deactivated', 'Bill ID: 4', '2025-12-15 17:53:36'),
(328, NULL, 109, 0, 2, 'Updated', 'Bill ID: 4', '2025-12-15 17:55:08'),
(329, NULL, 109, 0, 2, 'Created', 'Bill ID: 5', '2025-12-15 18:00:00'),
(330, NULL, 109, 0, 2, 'Deactivated', 'Bill ID: 4', '2025-12-15 18:02:55'),
(331, NULL, 109, 0, 2, 'Created', 'Customer ID: 3 - test', '2025-12-15 18:19:46'),
(332, NULL, 109, 0, 2, 'Created', 'Customer ID: 4 - kiri', '2025-12-15 18:22:47'),
(333, NULL, 109, 0, 2, 'Payment', 'Bill ID: 5, Amount: 750, Mode: Cash', '2025-12-15 18:37:11'),
(334, NULL, 109, 0, 2, 'Payment', 'Bill ID: 5, Amount: 1000, Mode: Cash', '2025-12-15 18:39:10'),
(335, NULL, 109, 0, 2, 'Deactivated', 'Bill ID: 5', '2025-12-15 18:39:17'),
(336, NULL, 109, 0, 2, 'Activated', 'Bill ID: 5', '2025-12-15 18:39:25'),
(337, NULL, 109, 0, 2, 'Updated', 'Bill ID: 5', '2025-12-15 18:43:19'),
(338, NULL, 109, 0, 2, 'Deactivated', 'Customer ID: 3', '2025-12-15 18:51:58'),
(339, NULL, 109, 0, 2, 'Deactivated', 'Customer ID: 4', '2025-12-15 18:52:02'),
(340, NULL, 109, 0, 2, 'Deactivated', 'Contact ID: 2', '2025-12-15 18:52:29'),
(341, NULL, 109, 0, 2, 'Payment Delete', 'Payment ID: 1', '2025-12-15 18:53:27'),
(342, NULL, 109, 0, 2, 'Payment Delete', 'Payment ID: 2', '2025-12-15 18:54:10'),
(343, NULL, 109, 0, 2, 'Payment', 'Bill ID: 5, Amount: 5650, Mode: Cash', '2025-12-15 19:28:14'),
(344, NULL, 109, 0, 2, 'Payment', 'Bill ID: 3, Amount: 40, Mode: Cash', '2025-12-15 19:28:23'),
(345, NULL, 109, 0, 2, 'Payment', 'Bill ID: 3, Amount: 800, Mode: Cash', '2025-12-15 19:28:34'),
(346, NULL, 109, 0, 2, 'Activated', 'Contact ID: 2', '2025-12-15 19:29:10'),
(347, NULL, 109, 0, 2, 'Updated', 'Cheque ID: 7 - 5000', '2025-12-15 19:29:16'),
(348, NULL, 109, 0, 2, 'Activated', 'Bill ID: 4', '2025-12-15 19:44:05'),
(349, NULL, 109, 0, 2, 'Deactivated', 'Bill ID: 4', '2025-12-15 19:44:30'),
(350, NULL, 109, 0, 2, 'Updated', 'Bill ID: 2', '2025-12-15 20:07:57'),
(351, NULL, 109, 0, 2, 'Created', 'Customer ID: 5 - test23', '2025-12-15 20:33:33'),
(352, NULL, 109, 0, 2, 'Created', 'Bill ID: 6', '2025-12-15 20:33:51'),
(353, NULL, 109, 0, 2, 'Updated', 'Bill ID: 6', '2025-12-15 20:34:24'),
(354, NULL, 109, 0, 2, 'Created', 'Cheque ID: 8 - 1212', '2025-12-15 20:35:02'),
(355, NULL, 109, 0, 2, 'Deactivated', 'Item ID: 5', '2025-12-15 20:53:14'),
(356, NULL, 109, 0, 2, 'Activated', 'Item ID: 5', '2025-12-15 21:00:43'),
(357, NULL, 109, 0, 2, 'Payment', 'Bill ID: 6, Amount: 6840, Mode: Bank Deposit', '2025-12-15 21:22:24'),
(358, NULL, 109, 0, 2, 'Deactivated', 'Cheque ID: 4', '2025-12-15 21:47:23'),
(359, NULL, 109, 0, 2, 'Deactivated', 'Cheque ID: 7', '2025-12-15 21:49:39'),
(360, NULL, 109, 0, 2, 'Deactivated', 'Cheque ID: 3', '2025-12-15 21:49:48'),
(361, NULL, 109, 0, 2, 'Deactivated', 'Cheque ID: 8', '2025-12-15 21:49:56'),
(362, NULL, 109, 0, 2, 'Payment Delete', 'Payment ID: 6', '2025-12-15 21:56:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_cheque_payment`
--
ALTER TABLE `bank_cheque_payment`
  ADD PRIMARY KEY (`chq_id`);

--
-- Indexes for table `bank_contact`
--
ALTER TABLE `bank_contact`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `bill_payment`
--
ALTER TABLE `bill_payment`
  ADD PRIMARY KEY (`pay_id`);

--
-- Indexes for table `bill_summary`
--
ALTER TABLE `bill_summary`
  ADD PRIMARY KEY (`bill_id`);

--
-- Indexes for table `client_registration`
--
ALTER TABLE `client_registration`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `leases`
--
ALTER TABLE `leases`
  ADD PRIMARY KEY (`lease_id`);

--
-- Indexes for table `letter_head`
--
ALTER TABLE `letter_head`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manage_activities`
--
ALTER TABLE `manage_activities`
  ADD PRIMARY KEY (`act_id`);

--
-- Indexes for table `manage_customers`
--
ALTER TABLE `manage_customers`
  ADD PRIMARY KEY (`cus_id`);

--
-- Indexes for table `manage_user_group`
--
ALTER TABLE `manage_user_group`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `production_daily_material_usage`
--
ALTER TABLE `production_daily_material_usage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_daily_production`
--
ALTER TABLE `production_daily_production`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_material`
--
ALTER TABLE `production_material`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_material_allocation`
--
ALTER TABLE `production_material_allocation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_overhead`
--
ALTER TABLE `production_overhead`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_product`
--
ALTER TABLE `production_product`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `user_device`
--
ALTER TABLE `user_device`
  ADD PRIMARY KEY (`d_id`);

--
-- Indexes for table `user_license`
--
ALTER TABLE `user_license`
  ADD PRIMARY KEY (`usr_id`);

--
-- Indexes for table `user_location`
--
ALTER TABLE `user_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_cheque_payment`
--
ALTER TABLE `bank_cheque_payment`
  MODIFY `chq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bank_contact`
--
ALTER TABLE `bank_contact`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `bill_payment`
--
ALTER TABLE `bill_payment`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bill_summary`
--
ALTER TABLE `bill_summary`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `client_registration`
--
ALTER TABLE `client_registration`
  MODIFY `c_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `leases`
--
ALTER TABLE `leases`
  MODIFY `lease_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `letter_head`
--
ALTER TABLE `letter_head`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `manage_activities`
--
ALTER TABLE `manage_activities`
  MODIFY `act_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `manage_customers`
--
ALTER TABLE `manage_customers`
  MODIFY `cus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `manage_user_group`
--
ALTER TABLE `manage_user_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `production_daily_material_usage`
--
ALTER TABLE `production_daily_material_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `production_daily_production`
--
ALTER TABLE `production_daily_production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `production_material`
--
ALTER TABLE `production_material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `production_material_allocation`
--
ALTER TABLE `production_material_allocation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `production_overhead`
--
ALTER TABLE `production_overhead`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `production_product`
--
ALTER TABLE `production_product`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `user_device`
--
ALTER TABLE `user_device`
  MODIFY `d_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `user_license`
--
ALTER TABLE `user_license`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `user_location`
--
ALTER TABLE `user_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=363;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
