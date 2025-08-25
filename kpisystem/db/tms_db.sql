-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2025 at 10:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(60) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `firstname`, `lastname`, `photo`, `created_on`) VALUES
(1, 'serbermz', '$2y$10$1VmOehdw8EfSiTn.wRR2EOmRviX23G6G/8KrbTRkAatc4dRTBLB2q', 'Lyndon', 'Bermoy', 'profile_youtube.jpg', '2018-05-03');

-- --------------------------------------------------------

--
-- Table structure for table `billing_details`
--

CREATE TABLE `billing_details` (
  `id` int(5) NOT NULL,
  `bill_id` varchar(50) NOT NULL,
  `product_company` varchar(50) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_unit` varchar(20) NOT NULL,
  `packing_size` varchar(30) NOT NULL,
  `price` varchar(10) NOT NULL,
  `qty` varchar(10) NOT NULL,
  `total` int(30) NOT NULL,
  `inventory_selection` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_details`
--

INSERT INTO `billing_details` (`id`, `bill_id`, `product_company`, `product_name`, `product_unit`, `packing_size`, `price`, `qty`, `total`, `inventory_selection`) VALUES
(3, '5', 'sample2', 'same', 'kg', '', '12.51', '1', 13, ''),
(4, '5', 'sample1', 'wood', 'kg', '', '200', '2', 400, ''),
(5, '6', 'sample1', 'wood', 'kg', '', '200', '1', 200, ''),
(6, '6', 'sample2', 'same', 'kg', '', '12.51', '1', 13, ''),
(7, '7', 'sample1', 'wood', 'kg', '', '200', '3', 600, ''),
(8, '7', 'sample2', 'same', 'kg', '', '12.51', '1', 13, ''),
(9, '12', 'sample2', 'same', 'kg', '', '12.51', '1', 13, 'Steels'),
(11, '14', 'sample2', 'same', 'kg', '', '12.51', '1', 13, 'Steels'),
(12, '15', 'sample2', 'same', 'kg', '', '12.51', '1', 13, 'Steels'),
(13, '16', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '2', 500, 'Wood'),
(14, '16', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(15, '17', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '1', 250, 'Wood'),
(16, '17', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(17, '18', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '1', 250, 'Wood'),
(18, '18', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(19, '19', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '2', 500, 'Wood'),
(20, '19', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(21, '20', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '2', 500, 'Wood'),
(22, '20', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(23, '21', 'sample2', 'same', 'kg', '', '12.51', '2', 25, 'Steels'),
(24, '22', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '1', 250, 'Wood'),
(25, '23', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '2', 440, 'Wood'),
(26, '24', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '7', 1750, 'Wood'),
(27, '25', 'sample1', 'wood', 'kg', '', '200', '1', 200, 'Wood'),
(28, '25', 'sample2', 'steel', 'kg', '', '15', '3', 45, 'Steels'),
(29, '26', 'sample2', 'steel', 'kg', '', '15', '3', 45, 'Steels'),
(30, '26', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '3', 750, 'Wood'),
(31, '27', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '2', 500, 'Wood'),
(32, '28', 'sample2', 'steel', 'kg', '', '15', '2', 30, 'Steels'),
(33, '29', 'sample2', 'steel', 'kg', '', '15', '2', 30, 'Steels'),
(34, '30', 'sample1', 'wood', 'kg', '', '200', '5', 1000, 'Wood'),
(35, '31', 'sample2', 'steel', 'kg', '', '15', '2', 30, 'Steels'),
(36, '32', 'sample2', 'steel', 'kg', '', '15', '1', 15, 'Steels'),
(37, '33', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '3', 750, 'Wood'),
(38, '34', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '250', '2', 500, 'Wood'),
(39, '35', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(40, '36', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(41, '37', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(42, '38', 'sample1', 'wood', 'kg', '', '200', '1', 200, 'Wood'),
(43, '39', 'sample1', 'wood', 'kg', '', '200', '1', 200, 'Wood'),
(44, '40', 'sample1', 'wood', 'kg', '', '200', '1', 200, 'Wood'),
(45, '40', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(46, '41', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood'),
(47, '42', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '2', 440, 'Wood'),
(48, '43', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '3', 660, 'Wood'),
(49, '44', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '2', 440, 'Wood'),
(50, '45', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '2', 440, 'Wood'),
(51, '46', 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '300', '1', 300, 'Wood'),
(52, '46', 'Adhesive and Scews', 'Sealant', 'pc', '', '220', '1', 220, 'Wood');

-- --------------------------------------------------------

--
-- Table structure for table `billing_header`
--

CREATE TABLE `billing_header` (
  `id` int(5) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `bill_type` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `bill_no` varchar(10) NOT NULL,
  `username` text NOT NULL,
  `project_name` varchar(30) NOT NULL,
  `module_title` varchar(30) NOT NULL,
  `location` varchar(30) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `designer` varchar(30) NOT NULL,
  `dimension` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_header`
--

INSERT INTO `billing_header` (`id`, `full_name`, `bill_type`, `date`, `bill_no`, `username`, `project_name`, `module_title`, `location`, `remarks`, `designer`, `dimension`) VALUES
(5, 'Princess', 'Cash', '2025-04-17', '00001', '', '', '', '', '', '', ''),
(6, 'Gab', 'Cash', '2025-04-17', '00006', '', '', '', '', '', '', ''),
(7, 'Lors', 'Cash', '2025-04-17', '00007', '', '', '', '', '', '', ''),
(12, 'Princess', 'Cash', '2025-04-19', '00008', '', '', '', '', '', '', ''),
(14, 'Lors', 'Cash', '2025-04-19', '00013', '', '', '', '', '', '', ''),
(15, 'Lors', 'Cash', '2025-04-19', '00015', '', '', '', '', '', '', ''),
(16, 'Princess Ionie', 'Cash', '2025-05-16', '00016', '', '', '', '', '', '', ''),
(17, 'Princess Santillan', 'Cash', '2025-05-20', '00017', '', '', '', '', '', '', ''),
(18, 'Princess Santillan', 'Cash', '2025-05-20', '00017', '', '', '', '', '', '', ''),
(19, 'Princess Ionie', 'Cash', '2025-05-20', '00019', '', '', '', '', '', '', ''),
(20, 'Ionie', 'Cash', '2025-05-20', '00020', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(21, 'Ionie', 'Debit', '2025-05-20', '00021', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(22, 'Ionie', 'Cash', '2025-05-28', '00022', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(23, 'Gab', 'Cash', '2025-05-28', '00023', '', 'SM', 'SM DEPT', 'Parañaque', 'rough', 'Albert Antrada', '87 x 4 x 7'),
(24, 'Princess', 'Cash', '2025-07-15', '00024', '', '', '', '', '', '', ''),
(25, 'Ionie', 'Cash', '2025-07-15', '00025', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(26, 'Princess', 'Cash', '2025-07-17', '00026', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(27, 'Ionie', 'Cash', '2025-07-17', '00027', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(28, 'Ionie', 'Cash', '2025-07-17', '00028', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(29, 'Ionie', 'Cash', '2025-07-17', '00028', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(30, 'Vov', 'Cash', '2025-07-17', '00030', '', '', '', '', '', '', ''),
(31, 'Yanna', 'Cash', '2025-07-17', '00031', '', '', '', '', '', '', ''),
(32, 'Ionie', 'Cash', '2025-07-19', '00032', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(33, 'Ionie', 'Cash', '2025-07-19', '00033', '', '', '', '', '', '', ''),
(34, 'Ionie', 'Cash', '2025-07-19', '00034', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(35, 'Princess', 'Cash', '2025-07-19', '00035', '', 'SM', '', '', '', '', ''),
(36, 'VON', 'Cash', '2025-07-19', '00036', '', '', '', '', '', '', ''),
(37, 'VON', 'Cash', '2025-07-19', '00036', '', '', '', '', '', '', ''),
(38, 'Princess', 'Cash', '2025-07-19', '00038', '', '', '', '', '', '', ''),
(39, 'Princess', 'Cash', '2025-07-19', '00039', '', '', '', '', '', '', ''),
(40, 'Ionie', 'Cash', '2025-07-25', '00040', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(41, 'Ionie', 'Cash', '2025-07-25', '00040', '', 'SM', 'SM DEPT', 'Parañaque', 'smooth', 'Albert Antrada', '87 x 4 x 7'),
(42, 'assa', 'Cash', '2025-08-15', '00042', '', 'sample', 'Reception', 'LPC', 'aaa', 'aaa', 'aa'),
(43, 'assa', 'Cash', '2025-08-15', '00042', '', 'sample', 'Reception', 'LPC', 'aaa', 'aaa', 'aa'),
(44, 'assa', 'Cash', '2025-08-15', '00042', '', 'sample', 'Reception', 'LPC', 'aaa', 'aaa', 'aa'),
(45, 'assa', 'Cash', '2025-08-15', '00042', '', 'sample', 'Reception', 'LPC', 'aaa', 'aaa', 'aa'),
(46, 'assa', 'Cash', '2025-08-15', '00042', '', 'sample', 'Reception', 'LPC', 'aaa', 'aaa', 'aa');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `author` varchar(150) NOT NULL,
  `equip_qty` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `publisher` varchar(150) NOT NULL,
  `publish_date` date NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `category_id`, `title`, `author`, `equip_qty`, `publisher`, `publish_date`, `status`) VALUES
(20, '', 1, 'hammer', '', '2', '', '0000-00-00', 0),
(21, '', 2, 'drill', '', '16', '', '0000-00-00', 0),
(23, '', 2, 'MDS Ledge', '', '5', '', '0000-00-00', 0),
(24, '', 5, 'Cutting disk', '', '30', '', '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `borrow`
--

CREATE TABLE `borrow` (
  `id` int(11) NOT NULL,
  `firstname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lastname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_borrow` date NOT NULL,
  `status` int(1) NOT NULL,
  `project` varchar(255) DEFAULT NULL,
  `store` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `borrow`
--

INSERT INTO `borrow` (`id`, `firstname`, `lastname`, `user_id`, `book_id`, `quantity`, `date_borrow`, `status`, `project`, `store`) VALUES
(56, 'Claire', 'Blake', 3, 20, '1', '2025-01-12', 1, NULL, NULL),
(57, 'Claire', 'Blake', 3, 21, '1', '2025-01-12', 1, NULL, NULL),
(58, 'John', 'Smith', 2, 21, '2', '2025-01-15', 1, NULL, NULL),
(59, 'John', 'Smith', 2, 20, '1', '2025-01-15', 1, NULL, NULL),
(60, 'Claire', 'Blake', 3, 21, '5', '2025-01-15', 1, NULL, NULL),
(61, 'John', 'Smith', 2, 20, '1', '2025-01-15', 1, NULL, NULL),
(62, 'Mike', 'Williams', 5, 23, '1', '2025-02-04', 1, NULL, NULL),
(63, 'Mike', 'Williams', 5, 24, '1', '2025-03-20', 1, NULL, NULL),
(64, 'Mike', 'Williams', 5, 24, '2', '2025-08-15', 1, 'sample', NULL),
(65, 'George', 'Wilson', 4, 24, '2', '2025-08-15', 1, 'sample', ''),
(66, 'Mike', 'Williams', 5, 24, '13', '2025-08-15', 1, 'sample', '');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'BOM'),
(2, 'Stainless Steel'),
(5, 'Consumable');

-- --------------------------------------------------------

--
-- Table structure for table `company_name`
--

CREATE TABLE `company_name` (
  `id` int(5) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `inventory_selection` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_name`
--

INSERT INTO `company_name` (`id`, `company_name`, `inventory_selection`) VALUES
(1, 'sample1', 'Wood'),
(5, 'sample2', 'Steels'),
(6, 'Electrical', 'Wood'),
(7, 'Adhesive and Scews', 'Wood'),
(10, 'Paintings', 'Wood');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `code` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `title`, `code`) VALUES
(1, 'Bachelor of Science in Information Systems', 'BSIS'),
(2, 'Bachelor of Science in Computer Science', 'BSCS');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `material_type` enum('Wood','Steel') NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `team_member_name` varchar(255) NOT NULL,
  `task_description` text NOT NULL,
  `assigned_date` date NOT NULL,
  `due_date` date NOT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `on_time` enum('Yes','No') NOT NULL,
  `revisions_errors` text DEFAULT NULL,
  `error_category` varchar(100) DEFAULT NULL,
  `qc_passed` enum('Yes','No') NOT NULL,
  `material_used` enum('Yes','No') NOT NULL,
  `waste_quantity` varchar(100) DEFAULT NULL,
  `cost_per_unit` decimal(10,2) DEFAULT NULL,
  `reason_for_waste` text DEFAULT NULL,
  `client_feedback` text DEFAULT NULL,
  `note_issues` text DEFAULT NULL,
  `task_type` enum('Individual','Team') NOT NULL,
  `output_percentage` decimal(5,2) DEFAULT NULL,
  `timeliness_percentage` decimal(5,2) DEFAULT NULL,
  `accuracy_percentage` decimal(5,2) DEFAULT NULL,
  `teamwork_percentage` decimal(5,2) DEFAULT NULL,
  `material_efficiency_percentage` decimal(5,2) DEFAULT NULL,
  `overall_kpi_percentage` decimal(5,2) DEFAULT NULL,
  `color_code` enum('Green','Yellow','Orange','Red') NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `production_efficiency_percentage` decimal(5,2) DEFAULT NULL,
  `yield_percentage` decimal(5,2) DEFAULT NULL,
  `scrap_rate_percentage` decimal(5,2) DEFAULT NULL,
  `equipment_utilization_percentage` decimal(5,2) DEFAULT NULL,
  `energy_consumption` decimal(10,2) DEFAULT NULL,
  `safety_score_percentage` decimal(5,2) DEFAULT NULL,
  `inventory_turnover_rate` decimal(10,2) DEFAULT NULL,
  `client_satisfaction_score` decimal(5,2) DEFAULT NULL,
  `planned_material_quantity` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `material_type`, `project_name`, `client_name`, `role`, `team_member_name`, `task_description`, `assigned_date`, `due_date`, `actual_completion_date`, `on_time`, `revisions_errors`, `error_category`, `qc_passed`, `material_used`, `waste_quantity`, `cost_per_unit`, `reason_for_waste`, `client_feedback`, `note_issues`, `task_type`, `output_percentage`, `timeliness_percentage`, `accuracy_percentage`, `teamwork_percentage`, `material_efficiency_percentage`, `overall_kpi_percentage`, `color_code`, `created_by`, `date_created`, `production_efficiency_percentage`, `yield_percentage`, `scrap_rate_percentage`, `equipment_utilization_percentage`, `energy_consumption`, `safety_score_percentage`, `inventory_turnover_rate`, `client_satisfaction_score`, `planned_material_quantity`) VALUES
(23, 'Wood', 'sample', 'sample', 'Designer', 'Dimple', 'sample', '2025-08-13', '2025-08-13', '2025-08-13', 'Yes', NULL, 'None', 'Yes', 'Yes', NULL, NULL, NULL, '10', 'great', 'Individual', 100.00, 100.00, 90.00, 95.00, NULL, 95.75, 'Green', 1, '2025-08-13 23:57:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 90.00, 100.00),
(24, 'Wood', 'sample', 'sample', 'Project Manager', 'Jacky', 'wq', '2025-08-13', '2025-08-13', '2025-08-13', 'Yes', 'none', 'None', 'Yes', 'Yes', NULL, NULL, NULL, '9', 'none', 'Individual', 100.00, 95.00, 95.00, 95.00, NULL, 96.00, 'Green', 1, '2025-08-13 23:59:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 90.00, NULL),
(25, 'Wood', 'sample', 'sample', 'Designer', 'Dimple', 'q', '2025-08-13', '2025-08-13', '2025-08-13', 'Yes', NULL, 'None', 'Yes', 'Yes', NULL, NULL, NULL, NULL, NULL, 'Individual', 100.00, 85.00, 90.00, 95.00, NULL, 92.00, 'Green', 1, '2025-08-13 23:59:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'Wood', 'sample', 'sample', 'Project Manager', 'Jacky', 'dad', '2025-08-14', '2025-08-14', '2025-08-14', 'Yes', NULL, 'None', 'Yes', 'Yes', NULL, NULL, NULL, '9', NULL, 'Individual', 100.00, 100.00, 95.00, 90.00, NULL, 96.00, 'Green', 1, '2025-08-14 00:09:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 90.00, NULL),
(27, 'Steel', 'sample', 'sample', 'Production / Operations', 'sample', 'sample', '2025-08-15', '2025-08-15', '2025-08-15', 'Yes', NULL, 'None', 'Yes', 'Yes', NULL, NULL, 'none', '9', 'none', 'Team', 90.00, 90.00, 90.00, 90.00, 90.00, 90.00, 'Green', 1, '2025-08-15 08:34:24', 90.00, 90.00, NULL, 100.00, 9.00, 90.00, 90.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `color` varchar(7) DEFAULT NULL,
  `event_type` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `start_datetime`, `end_datetime`, `color`, `event_type`, `created_by`) VALUES
(2, 'gwilson\'s meeting', '2025-03-27 12:10:00', '2025-03-27 13:10:00', '#FF0000', 'urgent_meeting', 1),
(7, 'wilson', '2025-03-27 13:20:00', '2025-03-27 15:20:00', NULL, 'urgent_meeting', NULL),
(8, 'for mr wilson', '2025-03-26 13:29:00', '2025-03-26 16:29:00', '#FF0000', NULL, 1),
(15, 'HOLIDAYYYY FUN DAY', '2025-04-02 00:00:00', '2025-04-02 12:00:00', '#FF8C00', 'events_holidays', 1),
(16, 'hilday', '2025-03-25 17:15:00', '2025-03-25 18:15:00', '#FF8C00', 'events_holidays', 1),
(17, 'wiwo', '2025-03-17 18:10:00', '2025-03-17 20:10:00', '#FF0000', 'urgent_meeting', 1),
(18, 'let\'s go', '2025-03-28 18:11:00', '2025-03-28 20:11:00', NULL, 'urgent_meeting', 12),
(22, 'urgent', '2025-03-29 19:50:00', '2025-03-29 20:50:00', NULL, 'urgent_meeting', 12),
(23, 'meeting lors 2', '2025-03-08 19:56:00', '2025-03-08 20:57:00', NULL, 'urgent_meeting', 12),
(52, 'final', '2025-04-01 20:15:00', '2025-04-01 21:16:00', '#FF0000', 'urgent_meeting', 12),
(53, 'final', '2025-03-28 20:15:00', '2025-03-28 21:16:00', NULL, 'urgent_meeting', 12),
(54, 'sa', '2025-03-11 22:17:00', '2025-03-11 23:17:00', NULL, 'urgent_meeting', 12),
(63, 'sa', '2025-03-28 20:19:00', '2025-03-28 23:22:00', '#FF0000', 'urgent_meeting', 12),
(64, 'sakak', '2025-03-19 20:19:00', '2025-03-19 22:21:00', NULL, 'urgent_meeting', 12),
(89, 'everyone', '2025-03-12 21:10:00', '2025-03-12 22:10:00', NULL, 'urgent_meeting', 12),
(90, 'last', '2025-03-01 09:12:00', '2025-03-01 22:12:00', NULL, 'urgent_meeting', 12),
(91, 'new', '2025-03-26 21:32:00', '2025-03-26 22:32:00', '#FF0000', 'urgent_meeting', 12),
(92, 'lpc', '2025-03-27 13:16:00', '2025-03-27 19:59:00', '#FF0000', 'urgent_meeting', 1),
(96, 'lpc day2', '2025-03-27 10:40:00', '2025-03-27 11:59:00', '#FF8C00', 'events_holidays', 1),
(97, 'muslim', '2025-04-01 15:12:00', '2025-04-01 23:38:00', '#FF8C00', 'events_holidays', 1),
(98, 'sample', '2025-08-14 23:18:00', '0000-00-00 00:00:00', '#FF0000', 'urgent_meeting', 1),
(100, 's', '2025-08-14 18:18:00', '2025-08-14 20:18:00', '#FF0000', 'urgent_meeting', 14),
(101, 'sa', '2025-08-15 18:20:00', '2025-08-16 18:20:00', '#FF8C00', 'events_holidays', 14),
(102, 'sas', '2025-08-15 18:43:00', '2025-08-15 21:43:00', '#FF0000', 'urgent_meeting', 14),
(103, 'sa', '2025-08-14 20:15:00', '2025-08-14 21:15:00', '#FF0000', 'urgent_meeting', 19),
(104, 'dasasdada', '2025-08-14 20:15:00', '2025-08-14 22:15:00', '#FF0000', 'urgent_meeting', 14),
(105, 'sample', '2025-08-15 00:00:00', '2025-08-16 23:59:00', '#FF8C00', 'events_holidays', 14),
(106, 'sasasas', '2025-08-18 20:45:00', '2025-08-18 21:45:00', '#FF0000', 'urgent_meeting', 1),
(107, 'morning', '2025-08-15 08:05:00', '2025-08-15 09:05:00', '#FF0000', 'urgent_meeting', 14);

-- --------------------------------------------------------

--
-- Table structure for table `event_colors`
--

CREATE TABLE `event_colors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hex_code` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_colors`
--

INSERT INTO `event_colors` (`id`, `name`, `hex_code`) VALUES
(1, 'Dark blue', '#0071c5'),
(2, 'Turquoise', '#40E0D0'),
(3, 'Green', '#008000'),
(4, 'Yellow', '#FFD700'),
(5, 'Orange', '#FF8C00'),
(6, 'Red', '#FF0000'),
(7, 'Black', '#000');

-- --------------------------------------------------------

--
-- Table structure for table `event_visibility`
--

CREATE TABLE `event_visibility` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_visibility`
--

INSERT INTO `event_visibility` (`id`, `event_id`, `user_id`, `role_id`) VALUES
(88, 2, NULL, 3),
(116, 8, 4, NULL),
(117, 8, 12, NULL),
(118, 8, NULL, 1),
(324, 28, 1, NULL),
(325, 28, 11, NULL),
(326, 28, 12, NULL),
(327, 28, 5, NULL),
(328, 28, NULL, 1),
(334, 30, 1, NULL),
(335, 30, 11, NULL),
(336, 30, 12, NULL),
(337, 30, 5, NULL),
(338, 30, NULL, 1),
(344, 32, 1, NULL),
(345, 32, 11, NULL),
(346, 32, 12, NULL),
(347, 32, 5, NULL),
(348, 32, NULL, 1),
(349, 33, 1, NULL),
(350, 33, 11, NULL),
(351, 33, 12, NULL),
(352, 33, 5, NULL),
(353, 33, NULL, 1),
(354, 34, 1, NULL),
(355, 34, 11, NULL),
(356, 34, 12, NULL),
(357, 34, 5, NULL),
(358, 34, NULL, 1),
(359, 35, 1, NULL),
(360, 35, 11, NULL),
(361, 35, 12, NULL),
(362, 35, 5, NULL),
(363, 35, NULL, 1),
(364, 36, 1, NULL),
(365, 36, 11, NULL),
(366, 36, 12, NULL),
(367, 36, 5, NULL),
(368, 36, NULL, 1),
(369, 37, 1, NULL),
(370, 37, 11, NULL),
(371, 37, 12, NULL),
(372, 37, 5, NULL),
(373, 37, NULL, 1),
(374, 38, 1, NULL),
(375, 38, 11, NULL),
(376, 38, 12, NULL),
(377, 38, 5, NULL),
(378, 38, NULL, 1),
(379, 39, 1, NULL),
(380, 39, 11, NULL),
(381, 39, 12, NULL),
(382, 39, 5, NULL),
(383, 39, NULL, 1),
(384, 40, 1, NULL),
(385, 40, 11, NULL),
(386, 40, 12, NULL),
(387, 40, 5, NULL),
(388, 40, NULL, 1),
(394, 42, 1, NULL),
(395, 42, 11, NULL),
(396, 42, 12, NULL),
(397, 42, 5, NULL),
(398, 42, NULL, 1),
(404, 44, 1, NULL),
(405, 44, 11, NULL),
(406, 44, 12, NULL),
(407, 44, 5, NULL),
(408, 44, NULL, 1),
(409, 45, 1, NULL),
(410, 45, 11, NULL),
(411, 45, 12, NULL),
(412, 45, 5, NULL),
(413, 45, NULL, 1),
(419, 47, 1, NULL),
(420, 47, 11, NULL),
(421, 47, 12, NULL),
(422, 47, 5, NULL),
(423, 47, NULL, 1),
(424, 48, 1, NULL),
(425, 48, 11, NULL),
(426, 48, 12, NULL),
(427, 48, 5, NULL),
(428, 48, NULL, 1),
(429, 49, 1, NULL),
(430, 49, 11, NULL),
(431, 49, 12, NULL),
(432, 49, 5, NULL),
(433, 49, NULL, 1),
(449, 41, 1, NULL),
(450, 41, 11, NULL),
(451, 41, 12, NULL),
(452, 41, 5, NULL),
(453, 41, NULL, 1),
(467, 55, 1, NULL),
(468, 55, 11, NULL),
(469, 55, 12, NULL),
(470, 55, 5, NULL),
(471, 55, NULL, 1),
(472, 56, 1, NULL),
(473, 56, 11, NULL),
(474, 56, 12, NULL),
(475, 56, 5, NULL),
(476, 56, NULL, 1),
(477, 57, 1, NULL),
(478, 57, 11, NULL),
(479, 57, 12, NULL),
(480, 57, 5, NULL),
(481, 57, NULL, 1),
(482, 58, 1, NULL),
(483, 58, 11, NULL),
(484, 58, 12, NULL),
(485, 58, 5, NULL),
(486, 58, NULL, 1),
(487, 59, 1, NULL),
(488, 59, 11, NULL),
(489, 59, 12, NULL),
(490, 59, 5, NULL),
(491, 59, NULL, 1),
(492, 60, 1, NULL),
(493, 60, 11, NULL),
(494, 60, 12, NULL),
(495, 60, 5, NULL),
(496, 60, NULL, 1),
(497, 61, 3, NULL),
(498, 61, 2, NULL),
(499, 61, 11, NULL),
(500, 61, NULL, 4),
(501, 61, NULL, 1),
(502, 62, 3, NULL),
(503, 62, 2, NULL),
(504, 62, 11, NULL),
(505, 62, NULL, 4),
(506, 62, NULL, 1),
(516, 65, 1, NULL),
(517, 65, 4, NULL),
(518, 65, 12, NULL),
(519, 65, NULL, 1),
(528, 68, 1, NULL),
(529, 68, 4, NULL),
(530, 68, 12, NULL),
(531, 68, NULL, 1),
(792, 91, NULL, 1),
(793, 91, NULL, 2),
(794, 91, NULL, 4),
(795, 91, NULL, 5),
(796, 91, NULL, 6),
(797, 91, NULL, 6),
(798, 91, NULL, 5),
(799, 91, NULL, 4),
(800, 91, NULL, 3),
(801, 91, NULL, 2),
(802, 91, NULL, 1),
(819, 63, 1, NULL),
(820, 63, 4, NULL),
(821, 63, 3, NULL),
(822, 63, 2, NULL),
(823, 63, 11, NULL),
(824, 63, 12, NULL),
(825, 63, 5, NULL),
(826, 63, 6, NULL),
(827, 63, 9, NULL),
(828, 63, 10, NULL),
(829, 63, NULL, 6),
(830, 63, NULL, 5),
(831, 63, NULL, 4),
(832, 63, NULL, 3),
(833, 63, NULL, 2),
(834, 63, NULL, 1),
(851, 17, 1, NULL),
(852, 17, 4, NULL),
(853, 17, 3, NULL),
(854, 17, 2, NULL),
(855, 17, 11, NULL),
(856, 17, 12, NULL),
(857, 17, 5, NULL),
(858, 17, 6, NULL),
(859, 17, 9, NULL),
(860, 17, 10, NULL),
(861, 17, NULL, 6),
(862, 17, NULL, 5),
(863, 17, NULL, 4),
(864, 17, NULL, 3),
(865, 17, NULL, 2),
(866, 17, NULL, 1),
(876, 52, 1, NULL),
(877, 52, 3, NULL),
(878, 52, 11, NULL),
(879, 52, NULL, 6),
(880, 52, NULL, 5),
(881, 52, NULL, 4),
(882, 52, NULL, 3),
(883, 52, NULL, 2),
(884, 52, NULL, 1),
(885, 92, NULL, 1),
(886, 92, NULL, 2),
(887, 92, NULL, 4),
(888, 92, NULL, 5),
(889, 92, NULL, 6),
(890, 92, NULL, 6),
(891, 92, NULL, 5),
(892, 92, NULL, 4),
(893, 92, NULL, 2),
(895, 98, 19, NULL),
(896, 98, 6, NULL),
(897, 100, 19, NULL),
(898, 102, 19, NULL),
(899, 102, 1, NULL),
(900, 103, 19, NULL),
(901, 104, 19, NULL),
(902, 106, 14, NULL),
(903, 107, 19, NULL),
(904, 107, 18, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `files_upload`
--

CREATE TABLE `files_upload` (
  `upload_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `upload_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files_upload`
--

INSERT INTO `files_upload` (`upload_id`, `file_id`, `upload_date`) VALUES
(19, 129, '2025-03-31 20:00:50'),
(20, 130, '2025-03-31 20:14:06'),
(21, 131, '2025-03-31 20:15:12'),
(22, 132, '2025-03-31 20:15:56'),
(23, 133, '2025-03-31 20:17:16'),
(24, 134, '2025-03-31 20:17:24'),
(25, 135, '2025-03-31 20:17:25'),
(26, 136, '2025-03-31 20:17:25'),
(27, 137, '2025-03-31 20:17:25'),
(28, 138, '2025-03-31 20:17:25'),
(29, 139, '2025-03-31 20:17:25'),
(30, 140, '2025-03-31 20:17:25'),
(31, 141, '2025-03-31 20:17:25'),
(32, 142, '2025-03-31 20:17:25'),
(33, 143, '2025-03-31 20:17:26'),
(34, 144, '2025-03-31 20:18:25'),
(35, 145, '2025-03-31 20:44:25'),
(36, 146, '2025-03-31 23:57:17'),
(37, 147, '2025-04-01 10:31:34'),
(38, 148, '2025-04-01 10:35:20'),
(41, 151, '2025-04-11 13:42:26'),
(42, 152, '2025-04-11 13:51:01'),
(43, 153, '2025-04-11 13:51:26'),
(44, 154, '2025-04-11 14:11:25'),
(45, 155, '2025-04-11 14:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_list`
--

CREATE TABLE `inquiry_list` (
  `id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `business_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `inquiry_status` tinyint(2) NOT NULL,
  `quotation_status` tinyint(2) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiry_list`
--

INSERT INTO `inquiry_list` (`id`, `name`, `contact`, `business_name`, `description`, `inquiry_status`, `quotation_status`, `date_created`) VALUES
(5, 'Princess Santillan', '091568518668', 'Stop2CHa', '&lt;p&gt;saa&lt;/p&gt;', 2, 0, '2025-02-24 10:59:13'),
(9, 'dsada', '425453', 'dada', '&lt;p&gt;dada&lt;/p&gt;', 2, 0, '2025-04-16 13:16:11'),
(10, 'sample', '09999999999', 'sample', 'this is sample inquiry', 2, 0, '2025-08-12 12:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_selection`
--

CREATE TABLE `inventory_selection` (
  `id` int(10) NOT NULL,
  `inventory_selection` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_selection`
--

INSERT INTO `inventory_selection` (`id`, `inventory_selection`) VALUES
(3, 'Steels'),
(4, 'Wood');

-- --------------------------------------------------------

--
-- Table structure for table `out_of_stock`
--

CREATE TABLE `out_of_stock` (
  `id` int(5) NOT NULL,
  `product_selling_price` varchar(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_qty` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `party_info`
--

CREATE TABLE `party_info` (
  `id` int(5) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `businessname` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` varchar(500) NOT NULL,
  `city` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `party_info`
--

INSERT INTO `party_info` (`id`, `firstname`, `lastname`, `businessname`, `contact`, `address`, `city`) VALUES
(3, 'asdsad a', 'asdasda', 'adsasd a', '425453', 'dadasd asdasd', 'dasdas da');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(5) NOT NULL,
  `inventory_selection` varchar(255) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `packing_size` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `inventory_selection`, `company_name`, `product_name`, `unit`, `packing_size`) VALUES
(6, 'Wood', 'sample1', 'wood', 'kg', '23'),
(9, 'Steels', 'sample2', 'steel', 'kg', '23'),
(10, 'Wood', 'Adhesive and Scews', 'Sealant', 'pc', ''),
(14, 'Wood', 'Adhesive and Scews', 'Liquid Nail', 'bot', '');

-- --------------------------------------------------------

--
-- Table structure for table `progress_files`
--

CREATE TABLE `progress_files` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `project_id` int(11) DEFAULT 0,
  `task_id` int(11) NOT NULL,
  `upload_date` datetime NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `upload_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress_files`
--

INSERT INTO `progress_files` (`id`, `filename`, `original_name`, `file_type`, `file_path`, `url`, `uploaded_by`, `project_id`, `task_id`, `upload_date`, `is_deleted`, `upload_id`) VALUES
(225, '1744782689_1743388593_CalendarPrint.pdf', '1743388593_CalendarPrint.pdf', 'pdf', 'uploads/progress_documents/1744782689_1743388593_CalendarPrint.pdf', 'http://localhost:3000\\/uploads/progress_documents/1744782689_1743388593_CalendarPrint.pdf', 1, 103, 0, '2025-04-16 13:51:29', 0, 0),
(226, '1744782705_DoBronxEchaluceErrolGabrielL.jpg', 'Do Bronx(Echaluce, Errol Gabriel L).jpg', 'jpg', 'uploads/progress_images/1744782705_DoBronxEchaluceErrolGabrielL.jpg', 'http://localhost:3000\\/uploads/progress_images/1744782705_DoBronxEchaluceErrolGabrielL.jpg', 1, 103, 0, '2025-04-16 13:51:45', 0, 0),
(227, '1744782740_04PRACTICUMPROGRAMEVALUATION.docx', '04-PRACTICUM-PROGRAM-EVALUATION.docx', 'docx', 'uploads/progress_documents/1744782740_04PRACTICUMPROGRAMEVALUATION.docx', 'http://localhost:3000\\/uploads/progress_documents/1744782740_04PRACTICUMPROGRAMEVALUATION.docx', 1, 103, 0, '2025-04-16 13:52:20', 0, 0),
(228, '1744782859_imageremovebgpreview.png', 'image-removebg-preview.png', 'png', 'uploads/progress_images/1744782859_imageremovebgpreview.png', 'http://localhost:3000\\/uploads/progress_images/1744782859_imageremovebgpreview.png', 1, 89, 0, '2025-04-16 13:54:19', 0, 0),
(232, '1744783204_imageremovebgpreview.png', 'image-removebg-preview.png', 'png', 'uploads/progress_images/1744783204_imageremovebgpreview.png', 'http://localhost:3000\\/uploads/progress_images/1744783204_imageremovebgpreview.png', 1, 89, 0, '2025-04-16 14:00:04', 0, 0),
(233, '1744783652_imageremovebgpreview.png', 'image-removebg-preview.png', 'png', 'uploads/progress_images/1744783652_imageremovebgpreview.png', 'http://localhost:3000\\/uploads/progress_images/1744783652_imageremovebgpreview.png', 1, 89, 0, '2025-04-16 14:07:32', 0, 0),
(235, '1744784669_Screenshot20250415220024.png', 'Screenshot 2025-04-15 220024.png', 'png', 'uploads/progress_images/1744784669_Screenshot20250415220024.png', 'http://localhost:3000\\/uploads/progress_images/1744784669_Screenshot20250415220024.png', 1, 89, 0, '2025-04-16 14:24:29', 0, 0),
(238, '1744810455_CalendarPrint.pdf', 'Calendar Print.pdf', 'pdf', 'uploads/progress_documents/1744810455_CalendarPrint.pdf', 'http://localhost:3000\\/uploads/progress_documents/1744810455_CalendarPrint.pdf', 11, 103, 0, '2025-04-16 21:34:15', 0, 0),
(240, '1755157034_PagesOJTReport2025.docx', 'Pages-OJT-Report-2025.docx', 'docx', 'uploads/progress_documents/1755157034_PagesOJTReport2025.docx', 'http://localhost:3000/RGAKLMS/uploads/progress_documents/1755157034_PagesOJTReport2025.docx', 4, 106, 0, '2025-08-14 15:37:14', 0, 0),
(241, '1755157041_Picture1.jpg', 'Picture1.jpg', 'jpg', 'uploads/progress_images/1755157041_Picture1.jpg', 'http://localhost:3000/RGAKLMS/uploads/progress_images/1755157041_Picture1.jpg', 4, 106, 0, '2025-08-14 15:37:21', 0, 0),
(242, '1755157733_Picture1.jpg', 'Picture1.jpg', 'jpg', 'uploads/progress_images/1755157733_Picture1.jpg', 'http://localhost:3000/RGAKLMS/uploads/progress_images/1755157733_Picture1.jpg', 4, 106, 0, '2025-08-14 15:48:53', 0, 0),
(243, '1755157744_7.png', '7.png', 'png', 'uploads/progress_images/1755157744_7.png', 'http://localhost:3000/RGAKLMS/uploads/progress_images/1755157744_7.png', 4, 106, 0, '2025-08-14 15:49:04', 0, 0),
(244, '1755157777_fdffsff.docx', 'fdffsff.docx', 'docx', 'uploads/progress_documents/1755157777_fdffsff.docx', 'http://localhost:3000/RGAKLMS/uploads/progress_documents/1755157777_fdffsff.docx', 4, 106, 0, '2025-08-14 15:49:37', 0, 0),
(245, '1755159551_1.png', '1.png', 'png', 'uploads/progress_images/1755159551_1.png', 'http://localhost:3000/RGAKLMS/uploads/progress_images/1755159551_1.png', 4, 107, 0, '2025-08-14 16:19:11', 0, 0);

--
-- Triggers `progress_files`
--
DELIMITER $$
CREATE TRIGGER `sync_original_name_to_user_productivity` AFTER INSERT ON `progress_files` FOR EACH ROW BEGIN
    INSERT INTO user_productivity (
        project_id,
        original_name,
        upload,         -- Assuming this matches progress_files.file_path
        date_created    -- Use current timestamp
    )
    VALUES (
        NEW.project_id,
        NEW.original_name,
        NEW.file_path,
        NOW()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_list`
--

CREATE TABLE `project_list` (
  `id` int(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `files` text NOT NULL DEFAULT '\'no-image-available.png\'',
  `original_name` varchar(255) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `manager_id` text NOT NULL,
  `user_ids` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `notified` tinyint(1) DEFAULT 0,
  `notified_users` text DEFAULT NULL,
  `notified_manager` tinyint(1) DEFAULT 0,
  `reminder_sent_users` text DEFAULT NULL,
  `reminder_sent_manager` tinyint(1) DEFAULT 0,
  `estimator_ids` text DEFAULT NULL,
  `designer_ids` text DEFAULT NULL,
  `inventory_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_list`
--

INSERT INTO `project_list` (`id`, `user_id`, `name`, `description`, `files`, `original_name`, `status`, `start_date`, `end_date`, `manager_id`, `user_ids`, `date_created`, `notified`, `notified_users`, `notified_manager`, `reminder_sent_users`, `reminder_sent_manager`, `estimator_ids`, `designer_ids`, `inventory_ids`) VALUES
(5, 0, 'SM Taytay ', '																																																																																																																dismantling asasd sa																																										', '\'no-image-available.png\'', '', 5, '2025-01-16', '2025-01-20', '1,6', '4,5', '2025-01-15 16:00:34', 1, NULL, 0, NULL, 0, NULL, NULL, NULL),
(16, 0, 'medplants', '																																																								casad as ff														', '\'no-image-available.png\'', '', 5, '2025-01-30', '2025-02-09', '1,6,3,2', '4,5', '2025-01-30 20:01:28', 1, NULL, 0, NULL, 0, NULL, NULL, NULL),
(31, 0, 'Hand Tool', '																								&lt;p class=&quot;MsoNormal&quot;&gt;&lt;b&gt;ECHALUCE, ERROL GABRIEL L.&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp; Prof.\r\nLeopoldo Causon III&lt;o:p&gt;&lt;/o:p&gt;&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot;&gt;&lt;b&gt;FCL &ndash; 4208 (S 10:00 &ndash; 11:00)&lt;o:p&gt;&lt;/o:p&gt;&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot; align=&quot;center&quot; style=&quot;text-align:center&quot;&gt;&lt;b&gt;&amp;nbsp;&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot; align=&quot;center&quot; style=&quot;text-align:center&quot;&gt;&lt;b&gt;Reflection #1&lt;o:p&gt;&lt;/o:p&gt;&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot;&gt;&lt;b&gt;What can I do to help the elders and adopted children?&lt;o:p&gt;&lt;/o:p&gt;&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot; style=&quot;text-indent:36.0pt&quot;&gt;&lt;br&gt;\r\nElders who care for and adopt children require careful and intentional efforts,\r\nadapted to their unique needs, to build relationships and a sense of security.\r\nOlder adults require social and emotional support to avoid loneliness; the best\r\nway is by regularly visiting, calling, or volunteering in elderly centers to\r\nprovide companionship. Intergenerational activities involving older persons\r\nwith younger generations can also promote quality of life among both parties\r\nthrough opportunities to share stories, mentorship, and learn from each other.\r\nHelp with grocery shopping, transportation, or chores alleviates daily burdens,\r\nand guidance on using technology enables them to communicate with their\r\nfamilies and utilize telemedicine. Advocacy counts, too&mdash;working on a policy\r\nthat safeguards seniors against exploitation, guiding them through healthcare\r\nsystems, or advocating for organizations covering medical or housing bills can\r\ngo a long way toward improving their general well-being. In addition, financial\r\nand legal assistance, including help preparing wills or averting exploitation,\r\nalso maintains their dignity and independence.&lt;o:p&gt;&lt;/o:p&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot;&gt;&lt;o:p&gt;&amp;nbsp;&lt;/o:p&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot;&gt;For adopted children, emotional health relies on resolving\r\nissues of identity, belonging, and potential trauma. Access to\r\nadoption-specialist therapists to process their experience and maintain\r\ncultural or family connections alive through language classes, heritage\r\ncelebrations, or open adoptions (where feasible) provides a sense of grounding.\r\nTrauma-informed parenting classes and access to support groups where families\r\nexchange challenges and solutions assist adoptive families. In contrast,\r\neducation support, such as tutoring or scholarship assistance, ensures\r\nchildren&amp;#x2019;s academic success, and advocacy for inclusive school policies creates\r\nan environment where adopted children are heard. Financial obstacles to\r\nadoption may be overcome by contributing to grants or organizations that assist\r\nfamilies in paying adoption fees and placing more children in stable, loving\r\nhomes.&lt;o:p&gt;&lt;/o:p&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot;&gt;&lt;o:p&gt;&amp;nbsp;&lt;/o:p&gt;&lt;/p&gt;\r\n\r\n&lt;p class=&quot;MsoNormal&quot;&gt;Efforts that overlap across groups, such as fundraising\r\ncampaigns for basics such as clothes medical equipment, or volunteering with\r\nnonprofits serving both populations, maximize your impact. Campaigns for\r\nstructural change&mdash;such as paid family leave for adoptive parents or subsidies\r\nfor elder care&mdash;construct a safety net in society. By addressing practical\r\nneeds, emotional deficits, and systemic inequalities, you can help create\r\ncommunities where elders are respected and adopted children are raised with the\r\nsecurity and support they are entitled to. Each act of kindness, whether by\r\ndirect service, advocacy, or education, makes the world more compassionate and\r\ninclusive for these strong yet vulnerable populations.&lt;o:p&gt;&lt;/o:p&gt;&lt;/p&gt;																					', '\'no-image-available.png\'', '', 5, '2025-02-13', '2025-02-28', '1,6', '4,5', '2025-02-13 19:08:19', 1, NULL, 0, NULL, 0, NULL, NULL, NULL),
(37, 0, 'sample2', '								sample							', '\'no-image-available.png\'', '', 0, '2025-02-22', '2025-03-22', '1', '5,9', '2025-02-22 22:20:40', 1, NULL, 0, NULL, 0, NULL, NULL, NULL),
(38, 0, 'sampleqw', '																								&lt;p&gt;awq&lt;/p&gt;																					', '\'no-image-available.png\'', '', 2, '2025-02-24', '2025-02-28', '1', '5', '2025-02-24 10:08:41', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(39, 0, 'Consumable', '								&lt;p&gt;sadaa&lt;/p&gt;							', '\'no-image-available.png\'', '', 2, '2025-02-24', '2025-02-27', '6', '5', '2025-02-24 11:54:22', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(43, 0, 'SM', '&lt;p&gt;ASA&lt;/p&gt;', '\'no-image-available.png\'', '', 0, '2025-02-26', '2025-02-28', '6', '5', '2025-02-26 11:59:09', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(52, 0, 'hmm', '								&lt;p&gt;dsaddaa&lt;/p&gt;							', '\'no-image-available.png\'', '', 0, '2025-02-28', '2025-03-03', '6', '5', '2025-02-28 14:11:30', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(85, 0, 'sample212', '								&lt;p&gt;sada&lt;/p&gt;							', '\'no-image-available.png\'', '', 1, '2025-03-11', '2025-03-14', '6', '5,10', '2025-03-11 14:32:36', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(86, 0, 'PASASDA', '								&lt;p&gt;SADADA&lt;/p&gt;							', '\'no-image-available.png\'', '', 1, '2025-03-11', '2025-03-15', '6', '5,10', '2025-03-11 14:47:58', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(87, 0, 'sampelee', '&lt;p&gt;dsaa&lt;/p&gt;', '\'no-image-available.png\'', '', 1, '2025-03-24', '2025-03-27', '1,6,3,2', '4,5,9,10', '2025-03-24 11:23:22', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(89, 0, 'GRWM', '																																&lt;p&gt;								&lt;/p&gt;&lt;p&gt;																																																																&lt;/p&gt;&lt;p&gt;10 vmds&lt;br&gt;10 backwall&amp;nbsp;&lt;br&gt;&lt;br&gt;initial report: we are in talks with designers and are awaiting a layout.&lt;br&gt;&lt;br&gt;&lt;br&gt;+Tonight (March 23,2025)&lt;br&gt;- GRWM added new vmds&lt;/p&gt;																																																								&lt;p&gt;&lt;/p&gt;							&lt;p&gt;&lt;/p&gt;																												', '\'no-image-available.png\'', '', 0, '2025-03-29', '2025-04-18', '3', '4,10', '2025-03-28 15:33:51', 0, NULL, 0, NULL, 0, NULL, NULL, NULL),
(99, 0, 'dasdawa', '																																																																								&lt;p&gt;sadas&lt;/p&gt;																																																															', '\'no-image-available.png\'', '', 2, '2025-04-15', '2025-04-19', '10', '4,3', '2025-04-15 18:15:32', 0, NULL, 1, NULL, 1, '18', '19', '20'),
(103, 0, 'ygygyg', '																																																&lt;p&gt;																								sa&lt;br&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;&amp;nbsp;&lt;img src=&quot;http://localhost:3000\\/uploads/images/1744781645_rga.png?t=1744781645497&quot; style=&quot;width: 212px;&quot;&gt;&lt;/p&gt;																																										', '\'no-image-available.png\'', '', 2, '2025-04-15', '2025-04-18', '11', '4,5,9,3,2,10', '2025-04-15 21:44:23', 0, NULL, 1, NULL, 1, '18', '19', '20'),
(106, 0, 'sample', '								&lt;p&gt;sample&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;http://localhost:3000/RGAKLMS/uploads/images/1755156779_1.png?t=1755156779489&quot; style=&quot;width: 50%;&quot;&gt;&lt;/p&gt;							', '\'no-image-available.png\'', '', 2, '2025-08-14', '2025-08-21', '11', '4,10,18,19,20', '2025-08-14 15:33:30', 0, NULL, 1, NULL, 0, NULL, NULL, NULL),
(107, 0, 'sample1', '								&lt;p&gt;sample description here&lt;br&gt;&lt;img src=&quot;http://localhost:3000/RGAKLMS/uploads/images/1755159388_yubu.PNG?t=1755159388716&quot; style=&quot;width: 50%;&quot;&gt;&lt;/p&gt;							', '\'no-image-available.png\'', '', 2, '2025-08-14', '2025-08-21', '11', '4,10,18,19,20', '2025-08-14 16:16:59', 0, NULL, 1, NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_team`
--

CREATE TABLE `project_team` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `users_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '''member'''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_history`
--

CREATE TABLE `purchase_history` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `inventory_selection` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `party_name` varchar(255) DEFAULT NULL,
  `purchase_type` varchar(50) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `action_type` enum('create','update','delete') DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_history`
--

INSERT INTO `purchase_history` (`id`, `purchase_id`, `inventory_selection`, `company_name`, `product_name`, `unit`, `quantity`, `price`, `party_name`, `purchase_type`, `purchase_date`, `action_type`, `action_time`) VALUES
(1, 12, 'Wood', 'Adhesive and Scews', 'Liquid Nail', 'bot', 5.00, 250.00, 'adsasd a', 'Cash', '2025-08-12', 'update', '2025-08-12 15:04:54'),
(2, 12, 'Wood', 'Adhesive and Scews', 'Liquid Nail', 'bot', 5.00, 300.00, 'adsasd a', 'Cash', '2025-08-12', 'update', '2025-08-12 15:05:34'),
(3, 16, 'Steels', 'sample2', 'steel', 'kg', 5.00, 15.00, 'adsasd a', 'Cash', '2025-08-12', 'update', '2025-08-12 15:11:17'),
(4, 16, 'Steels', 'sample2', 'steel', 'kg', 5.00, 30.00, 'adsasd a', 'Debit', '2025-08-12', 'update', '2025-08-12 15:11:47'),
(5, 14, 'Wood', 'Adhesive and Scews', 'Sealant', 'pc', 5.00, 250.00, 'adsasd a', 'Cash', '2025-08-15', 'update', '2025-08-15 00:11:45'),
(6, 14, 'Wood', 'Adhesive and Scews', 'Sealant', 'pc', 5.00, 220.00, 'adsasd a', 'Cash', '2025-08-15', 'update', '2025-08-15 00:12:45');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_master`
--

CREATE TABLE `purchase_master` (
  `id` int(5) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `packing_size` varchar(20) NOT NULL,
  `quantity` varchar(10) NOT NULL,
  `price` varchar(100) NOT NULL,
  `party_name` varchar(100) NOT NULL,
  `purchase_type` varchar(100) NOT NULL,
  `expiry_date` date NOT NULL,
  `purchase_date` date NOT NULL,
  `username` varchar(50) NOT NULL,
  `inventory_selection` varchar(20) NOT NULL,
  `last_updated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_master`
--

INSERT INTO `purchase_master` (`id`, `company_name`, `product_name`, `unit`, `packing_size`, `quantity`, `price`, `party_name`, `purchase_type`, `expiry_date`, `purchase_date`, `username`, `inventory_selection`, `last_updated`) VALUES
(8, 'sample1', 'wood', 'kg', '23', '7', '200', 'adsasd a', 'Cash', '2025-03-28', '2025-04-18', '', 'Wood', '2025-07-15'),
(12, 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '9', '300', 'adsasd a', 'Cash', '2025-07-15', '2025-08-12', '', 'Wood', '2025-08-12'),
(14, 'Adhesive and Scews', 'Sealant', 'pc', '', '5', '220', 'adsasd a', 'Cash', '2025-08-15', '2025-08-15', '', 'Wood', '2025-08-15'),
(16, 'sample2', 'steel', 'kg', '', '10', '30', 'adsasd a', 'Cash', '0000-00-00', '2025-08-12', '', 'Steels', '2025-08-12');

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `firstname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lastname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_return` date NOT NULL,
  `status` int(1) NOT NULL,
  `project` varchar(255) DEFAULT NULL,
  `store` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`id`, `firstname`, `lastname`, `user_id`, `book_id`, `quantity`, `date_return`, `status`, `project`, `store`) VALUES
(36, 'Claire', 'Blake', 3, 20, '1', '2025-01-12', 0, NULL, NULL),
(37, 'Claire', 'Blake', 3, 21, '1', '2025-01-12', 0, NULL, NULL),
(38, 'John', 'Smith', 2, 21, '2', '2025-01-15', 0, NULL, NULL),
(39, 'John', 'Smith', 2, 20, '1', '2025-01-15', 0, NULL, NULL),
(40, 'Claire', 'Blake', 3, 20, '1', '2025-01-15', 0, NULL, NULL),
(41, 'Jian', 'Detablan', 3, 21, '1', '2025-01-15', 0, NULL, NULL),
(42, 'Jian', 'Detablan', 3, 21, '5', '2025-01-15', 0, NULL, NULL),
(43, 'Mike', 'Williams', 5, 23, '1', '2025-02-04', 0, NULL, NULL),
(44, 'Joshua', 'Fulgencio', 2, 20, '1', '2025-02-04', 0, NULL, NULL),
(45, 'Mike', 'Williams', 5, 24, '1', '2025-03-20', 0, NULL, NULL),
(46, 'Mike', 'Williams', 5, 24, '5', '2025-08-15', 0, 'sample', ''),
(47, 'Mike', 'Williams', 5, 24, '2', '2025-08-15', 0, 'sample', ''),
(48, 'Mike', 'Williams', 5, 24, '13', '2025-08-15', 0, 'sample', ''),
(49, 'Mike', 'Williams', 5, 24, '2', '2025-08-15', 0, 'sample', ''),
(50, 'George', 'Wilson', 4, 24, '2', '2025-08-15', 0, 'sample', '');

-- --------------------------------------------------------

--
-- Table structure for table `return_products`
--

CREATE TABLE `return_products` (
  `id` int(5) NOT NULL,
  `bill_no` varchar(10) NOT NULL,
  `return_date` varchar(15) NOT NULL,
  `product_company` varchar(50) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_unit` varchar(20) NOT NULL,
  `packing_size` varchar(20) NOT NULL,
  `product_price` varchar(10) NOT NULL,
  `product_qty` varchar(10) NOT NULL,
  `total` varchar(10) NOT NULL,
  `return_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_master`
--

CREATE TABLE `stock_master` (
  `id` int(5) NOT NULL,
  `product_company` varchar(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_unit` varchar(50) NOT NULL,
  `packing_size` varchar(20) NOT NULL,
  `product_qty` varchar(50) NOT NULL,
  `product_selling_price` varchar(100) NOT NULL,
  `inventory_selection` varchar(20) NOT NULL,
  `last_updated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_master`
--

INSERT INTO `stock_master` (`id`, `product_company`, `product_name`, `product_unit`, `packing_size`, `product_qty`, `product_selling_price`, `inventory_selection`, `last_updated`) VALUES
(2, 'sample1', 'wood', 'kg', '23', '7', '200', 'Wood', '2025-07-15'),
(9, 'Adhesive and Scews', 'Liquid Nail', 'bot', '', '9', '300', 'Wood', '2025-08-12'),
(11, 'sample2', 'steel', 'kg', '', '10', '30', 'Steels', '2025-08-12'),
(12, 'Adhesive and Scews', 'Sealant', 'pc', '', '5', '220', 'Wood', '2025-08-15');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(15) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `firstname`, `lastname`, `photo`, `course_id`, `created_on`) VALUES
(5, 'XSJ491538702', 'qwera', 'osas', '', 1, '2025-01-08');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `cover_img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `address`, `cover_img`) VALUES
(1, 'Task Management System', 'info@sample.comm', '+6948 8542 623', '2102  Caldwell Road, Rochester, New York, 14608', '');

-- --------------------------------------------------------

--
-- Table structure for table `task_list`
--

CREATE TABLE `task_list` (
  `id` int(30) NOT NULL,
  `project_id` int(30) NOT NULL,
  `task` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_list`
--

INSERT INTO `task_list` (`id`, `project_id`, `task`, `description`, `status`, `date_created`) VALUES
(1, 1, 'Sample Task 1', '								&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 14px; text-align: justify;&quot;&gt;Fusce ullamcorper mattis semper. Nunc vel risus ipsum. Sed maximus dapibus nisl non laoreet. Pellentesque quis mauris odio. Donec fermentum facilisis odio, sit amet aliquet purus scelerisque eget.&amp;nbsp;&lt;/span&gt;													', 3, '2020-12-03 11:08:58'),
(2, 1, 'Sample Task 2', '				Sample Task 2										', 3, '2020-12-03 13:50:15'),
(3, 2, 'Task Test', 'Sample', 1, '2020-12-03 13:52:25'),
(4, 2, 'test 23', 'Sample test 23', 1, '2020-12-03 13:52:40'),
(5, 6, 'Tasssk', 'installation', 2, '2025-01-15 16:49:33'),
(6, 5, 'Tasssk', '												dasdac as						', 3, '2025-01-30 18:23:58'),
(7, 5, 'sada', '				asdad			', 3, '2025-01-30 20:48:29'),
(8, 31, 'assembly', 'as', 1, '2025-02-13 19:12:30'),
(9, 37, 'assembly', 'saa', 1, '2025-02-25 10:07:57'),
(10, 89, 'Task 1 - Reiner', '				Reiner has to look into the different materials and check if all of it is quality			', 3, '2025-03-28 15:35:30'),
(11, 99, 'assembly', 'saasa', 1, '2025-04-15 19:16:34'),
(14, 103, 'sasa', '								sas						', 3, '2025-04-16 18:29:32'),
(15, 103, 'Tasssk', 'sas', 1, '2025-04-16 21:28:24'),
(16, 106, 'task 1', '								task description						', 3, '2025-08-14 15:36:35'),
(17, 107, 'task - sample', '								task						', 3, '2025-08-14 16:18:29');

-- --------------------------------------------------------

--
-- Table structure for table `track`
--

CREATE TABLE `track` (
  `id` int(5) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `destination` varchar(255) NOT NULL,
  `address` varchar(500) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `vehicle` varchar(255) NOT NULL,
  `driver` varchar(50) NOT NULL,
  `dept` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `track`
--

INSERT INTO `track` (`id`, `date`, `time`, `destination`, `address`, `purpose`, `vehicle`, `driver`, `dept`) VALUES
(1, '2025-03-24', '15:00:00', 'SFDM', 'Quezon City', 'Dismantling', 'L300', 'Rollie', 'Ian'),
(2, '2025-03-24', '14:54:00', 'Manila', 'San Juan', 'Assemble', 'Lite-Ace', 'Noel', 'Stiph'),
(6, '2025-08-14', '16:23:00', 'Laguna', 'Quezon City', 'sas', 'L300', 'Noel', 'Ian');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(5) NOT NULL,
  `unit` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit`) VALUES
(1, 'ml'),
(2, 'kg'),
(3, 'pc'),
(4, 'bot'),
(5, 'box'),
(6, 'gal');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_files`
--

CREATE TABLE `uploaded_files` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `project_id` int(11) DEFAULT 0,
  `upload_date` datetime NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `progress_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploaded_files`
--

INSERT INTO `uploaded_files` (`id`, `filename`, `original_name`, `file_type`, `file_path`, `url`, `uploaded_by`, `project_id`, `upload_date`, `is_deleted`, `progress_id`) VALUES
(133, '1744781645_rga.png', 'rga.png', 'png', 'uploads/images/1744781645_rga.png', 'http://localhost:3000\\/uploads/images/1744781645_rga.png', 1, 103, '2025-04-16 13:34:05', 0, NULL),
(140, '1755156779_1.png', '1.png', 'png', 'uploads/images/1755156779_1.png', 'http://localhost:3000/RGAKLMS/uploads/images/1755156779_1.png', 10, 106, '2025-08-14 15:32:59', 0, NULL),
(141, '1755156808_PagesOJTReport2025.docx', 'Pages-OJT-Report-2025.docx', 'docx', 'uploads/documents/1755156808_PagesOJTReport2025.docx', 'http://localhost:3000/RGAKLMS/uploads/documents/1755156808_PagesOJTReport2025.docx', 10, 106, '2025-08-14 15:33:28', 0, NULL),
(142, '1755159378_FINALRGAWOODKPI.xlsx', 'FINAL RGA WOOD KPI.xlsx', 'xlsx', 'uploads/documents/1755159378_FINALRGAWOODKPI.xlsx', 'http://localhost:3000/RGAKLMS/uploads/documents/1755159378_FINALRGAWOODKPI.xlsx', 10, 107, '2025-08-14 16:16:18', 0, NULL),
(143, '1755159388_yubu.PNG', 'yubu.PNG', 'png', 'uploads/images/1755159388_yubu.PNG', 'http://localhost:3000/RGAKLMS/uploads/images/1755159388_yubu.PNG', 10, 107, '2025-08-14 16:16:28', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1 = admin, 2 = staff',
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `type`, `avatar`, `date_created`) VALUES
(1, 'Executive', ',', 'executive', '$2y$10$87Af8tEExSX.eHxxikQ48.hGtUC4i6sl5M4bQAt4WgsV.bS3Tfzz.', 1, '1741748580_1605918720_avatar2.png', '2020-11-26 10:57:04'),
(2, 'Joshua', 'Fulgencio', 'joshua@gmail.com', 'ed39ee32edc6c43caef42543e346f3f6', 2, '1606978560_avatar.jpg', '2020-12-03 09:26:03'),
(3, 'Jian', 'Detablan', 'rgajiand@gmail.com', 'a7e37aa0f8951ab69828a4b4494cff22', 2, '1606958760_47446233-clean-noir-et-gradient-sombre-image-de-fond-abstrait-.jpg', '2020-12-03 09:26:42'),
(4, 'George', 'Wilson', 'gwilson@sample.com', '$2y$10$cKK0hZyXIpZUYqqVdVYzN.GpbQJL.4QPPQsUCi7L0lhooVSYamV0S', 3, '1606963560_avatar.jpg', '2020-12-03 10:46:41'),
(5, 'Mike', 'Williams', 'mwilliams@sample.com', '3cc93e9a6741d8b40460457139cf8ced', 3, '1606963620_47446233-clean-noir-et-gradient-sombre-image-de-fond-abstrait-.jpg', '2020-12-03 10:47:06'),
(6, 'orier', 'ere', 'ori', '$2y$10$vZQTJuegAnYwGONmkl9Teew/F7VMktYm0d/urqakqFQXv83Hx27vW', 1, '1739771040_Screenshot 2024-09-23 164904.png', '2025-01-09 18:43:01'),
(9, 'Princess Ionie', 'Santillan', 'princess.santillan26@gmail.com', '2e35676f70e917270981b0dc4e72ae53', 3, '1740143460_2x2.jpg', '2025-02-21 21:11:59'),
(10, 'project', 'coor', 'coor', '$2y$10$1uwZKvjd0qENNhwT117g0uA.6b6YmXMl9lS/YMzRpYZq2ywFf4aQu', 2, 'no-image-available.png', '2025-03-03 10:30:58'),
(11, 'project', 'manager', 'pm', '$2y$10$1x2jtPhLVsPbFvsFSpyoIuTMqWe0EciOBVZbYmJf1rrh1pNdGgvUi', 14, 'no-image-available.png', '2025-04-13 17:10:35'),
(14, 'hr', 'resource', 'hr', '$2y$10$AKVT6GHq6QnTCBaYllCGXuePdLULGyzivE9ViPu1WdRPx1axOhwNS', 13, 'no-image-available.png', '2025-04-13 18:03:43'),
(18, 'estim', 'ator', 'estimate', '$2y$10$paKCiQ3a2EJHv6Xmbjp6LuoMliWOXfYDnYlpQ2VfILfHENQHjXPR6', 6, 'no-image-available.png', '2025-04-15 17:26:29'),
(19, 'design', 'er', 'design', '$2y$10$9cUu1DLOSZjmYlVkSvOJRuCjnXB1qaERBb6ACZ/1GXK0devzCXL7C', 4, 'no-image-available.png', '2025-04-15 18:09:58'),
(20, 'invent', 'coor', 'invent', '$2y$10$hwPXp91Urln/B00NGdyLP.Mq1noGbRJ9aVkRZ8gVOmgYKylstvORy', 5, 'no-image-available.png', '2025-04-15 19:09:30'),
(21, 'sampe', 'same', 'sasa', '$2y$10$DMb1Hs5Ntos8HnZWMBUiceEnwYvvwEgIAjpdmHHSmV7N3OzAY6Mdy', 1, 'no-image-available.png', '2025-08-14 16:24:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_productivity`
--

CREATE TABLE `user_productivity` (
  `id` int(30) NOT NULL,
  `project_id` int(30) NOT NULL,
  `task_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `upload` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `user_id` int(30) NOT NULL,
  `time_rendered` float NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `upload_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_productivity`
--

INSERT INTO `user_productivity` (`id`, `project_id`, `task_id`, `comment`, `upload`, `original_name`, `file`, `subject`, `date`, `start_time`, `end_time`, `user_id`, `time_rendered`, `date_created`, `upload_id`) VALUES
(268, 89, 0, '', 'uploads/progress_documents/1744350146_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 13:42:26', 0),
(270, 89, 0, '', 'uploads/progress_documents/1744350661_EchaluceInitialReportforDailyAccomplishmentforMarch.docx', 'Echaluce - Initial Report for Daily Accomplishment for March.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 13:51:01', 0),
(271, 89, 0, '', 'uploads/progress_documents/1744350686_EchaluceInitialReportforDailyAccomplishment.docx', 'Echaluce - Initial Report for Daily Accomplishment.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 13:51:26', 0),
(272, 89, 0, '', 'uploads/progress_documents/1744351885_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:11:25', 0),
(273, 89, 0, '', 'uploads/progress_documents/1744351982_THESISwithtableofcontents.docx', 'THESIS with table of contents.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:13:02', 0),
(274, 89, 0, '', 'uploads/progress_documents/1744352168_EchaluceInitialReportforDailyAccomplishmentforFeb.docx', 'Echaluce - Initial Report for Daily Accomplishment for Feb.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:16:08', 0),
(276, 89, 0, '', 'uploads/progress_documents/1744352447_1743387952_plant.pdf', '1743387952_plant.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:20:47', 0),
(277, 89, 0, '                                                    ', '', '', '', 'Progress update on Apr 11, 2025', '2025-04-11', '08:20:00', '08:20:00', 1, 0, '2025-04-11 14:20:51', 0),
(278, 89, 0, '', 'uploads/progress_documents/1744352835_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:27:15', 0),
(279, 89, 0, '', 'uploads/progress_documents/1744353243_EchaluceInitialReportforDailyAccomplishment.docx', 'Echaluce - Initial Report for Daily Accomplishment.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:34:03', 0),
(280, 89, 0, '', 'uploads/progress_documents/1744353638_EchaluceInitialReportforDailyAccomplishment.docx', 'Echaluce - Initial Report for Daily Accomplishment.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:40:38', 0),
(281, 89, 0, '', 'uploads/progress_documents/1744353691_EchaluceInitialReportforDailyAccomplishmentforMarch.docx', 'Echaluce - Initial Report for Daily Accomplishment for March.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:41:31', 0),
(282, 89, 0, '', 'uploads/progress_documents/1744353817_EchaluceInitialReportforDailyAccomplishment.docx', 'Echaluce - Initial Report for Daily Accomplishment.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:43:37', 0),
(283, 89, 0, '', 'uploads/progress_documents/1744354240_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:50:40', 0),
(284, 89, 0, '', 'uploads/progress_documents/1744354675_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 14:57:55', 0),
(286, 89, 0, '', 'uploads/progress_documents/1744355085_fdffsff.docx', 'fdffsff.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 15:04:45', 0),
(288, 89, 0, '', 'uploads/progress_documents/1744356106_fdffsff.docx', 'fdffsff.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 15:21:46', 0),
(290, 89, 0, '', 'uploads/progress_documents/1744356998_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 15:36:38', 0),
(292, 89, 0, '', 'uploads/progress_documents/1744357018_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 15:36:58', 0),
(294, 89, 0, '', 'uploads/progress_documents/1744357120_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 15:38:40', 0),
(295, 89, 10, 'make the comment also deleted when the file is deleted', '', '', '', 'Progress update on Apr 11, 2025', '2025-04-11', '09:38:00', '09:38:00', 1, 0, '2025-04-11 15:39:10', 0),
(296, 89, 0, '', 'uploads/progress_documents/1744359194_ThesisDeepResidualNeuralNetworkapp.docx', 'Thesis - Deep Residual Neural Network app.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 16:13:14', 0),
(298, 89, 0, '', 'uploads/progress_documents/1744361310_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 16:48:30', 0),
(299, 89, 0, '', 'uploads/progress_documents/1744361318_ThesisDeepResidualNeuralNetworkapp1.docx', 'Thesis - Deep Residual Neural Network app (1).docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 16:48:38', 0),
(301, 89, 0, '', 'uploads/progress_documents/1744361426_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 16:50:26', 0),
(302, 89, 0, '                                                    ', '', '', '', 'Progress update on Apr 11, 2025', '2025-04-11', '10:50:00', '10:50:00', 1, 0, '2025-04-11 16:50:30', 0),
(303, 89, 0, '', 'uploads/progress_documents/1744362293_ThesisDeepResidualNeuralNetworkapp1.docx', 'Thesis - Deep Residual Neural Network app (1).docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-11 17:04:53', 0),
(305, 89, 0, '', 'uploads/progress_documents/1744530717_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 15:51:57', 0),
(307, 89, 0, '', 'uploads/progress_documents/1744530851_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 15:54:11', 0),
(309, 89, 0, '', 'uploads/progress_documents/1744530888_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 15:54:48', 0),
(310, 89, 10, '                                                    ', '', '', '', 'Progress update on Apr 13, 2025', '2025-04-13', '09:54:00', '09:54:00', 1, 0, '2025-04-13 15:55:01', 0),
(311, 89, 0, '', 'uploads/progress_documents/1744531538_04PRACTICUMPROGRAMEVALUATION.docx', '04-PRACTICUM-PROGRAM-EVALUATION.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:05:38', 0),
(312, 89, 10, '                                                    ', '', '', '', 'Progress update on Apr 13, 2025', '2025-04-13', '10:05:00', '10:05:00', 1, 0, '2025-04-13 16:05:40', 0),
(313, 89, 0, '', 'uploads/progress_documents/1744531553_ThesisCapstoneCoverandTOC.docx', 'Thesis-Capstone Cover and TOC.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:05:53', 0),
(315, 89, 0, '', 'uploads/progress_documents/1744532146_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:15:46', 0),
(317, 89, 0, '', 'uploads/progress_documents/1744532173_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:16:13', 0),
(319, 89, 0, '', 'uploads/progress_documents/1744532701_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:25:01', 0),
(321, 89, 0, '', 'uploads/progress_documents/1744532816_04PRACTICUMPROGRAMEVALUATION.docx', '04-PRACTICUM-PROGRAM-EVALUATION.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:26:56', 0),
(323, 89, 0, '', 'uploads/progress_documents/1744532921_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:28:41', 0),
(325, 89, 0, '', 'uploads/progress_documents/1744533078_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:31:18', 0),
(327, 89, 0, '', 'uploads/progress_documents/1744533144_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 16:32:24', 0),
(328, 89, 10, '                                                    ', '', '', '', 'Progress update on Apr 13, 2025', '2025-04-13', '10:32:00', '10:32:00', 1, 0, '2025-04-13 16:32:24', 0),
(329, 31, 0, '', 'uploads/progress_documents/1744541002_ECHALUCEFCLREFLECTION3.docx', 'ECHALUCE - FCL REFLECTION #3.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-13 18:43:22', 0),
(330, 31, 8, '                                                    ', '', '', '', 'Progress update on Apr 13, 2025', '2025-04-13', '12:43:00', '12:43:00', 5, 0, '2025-04-13 18:43:23', 0),
(331, 99, 0, '', 'uploads/progress_documents/1744715808_APPROVALSHEETTEMPLATE.docx', 'APPROVAL-SHEET-TEMPLATE.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 19:16:48', 0),
(332, 99, 0, '', 'uploads/progress_documents/1744715818_OJTTimeLogforFeb1.docx', 'OJT Time-Log for Feb (1).docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 19:16:58', 0),
(334, 99, 0, '', 'uploads/progress_images/1744716113_IMG_5255.JPG', 'IMG_5255.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 19:21:53', 0),
(335, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744716113_IMG_5255.JPG?t=1744716113561&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744716113_IMG_5255.JPG?t=1744716113561&quot; style=&quot;width: 637.75px;&quot;&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '13:21:00', '13:21:00', 11, 0, '2025-04-15 19:21:58', 0),
(336, 99, 0, '', 'uploads/progress_images/1744721390_IMG_5256.JPG', 'IMG_5256.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:49:50', 0),
(337, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744721390_IMG_5256.JPG?t=1744721390516&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '14:49:00', '14:49:00', 11, 0, '2025-04-15 20:49:57', 0),
(338, 99, 0, '', 'uploads/progress_documents/1744721671_APPROVALSHEETTEMPLATE.docx', 'APPROVAL-SHEET-TEMPLATE.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:54:31', 0),
(339, 99, 11, '                                                    ', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '14:54:00', '14:54:00', 11, 0, '2025-04-15 20:54:34', 0),
(340, 99, 0, '', 'uploads/progress_images/1744721682_IMG_5256.JPG', 'IMG_5256.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:54:42', 0),
(341, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744721682_IMG_5256.JPG?t=1744721682112&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '14:54:00', '14:54:00', 11, 0, '2025-04-15 20:54:49', 0),
(342, 99, 0, '', 'uploads/progress_documents/1744721752_WOODINVENTORYLIST1.xlsx', 'WOOD INVENTORY LIST  (1).xlsx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:55:52', 0),
(343, 99, 0, '', 'uploads/progress_documents/1744721757_THESISwithtableofcontents.docx', 'THESIS with table of contents.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:55:57', 0),
(344, 99, 0, '', 'uploads/progress_documents/1744721762_CalendarPrint.pdf', 'Calendar Print.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:56:02', 0),
(345, 99, 0, '', 'uploads/progress_documents/1744721777_IMG_8213.jpg.pdf', 'IMG_8213.jpg.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:56:17', 0),
(346, 99, 0, '', 'uploads/progress_documents/1744721787_RizalinFranceandGermany1.pptx', 'Rizal in France and Germany (1).pptx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 20:56:27', 0),
(347, 99, 0, '', 'uploads/progress_images/1744722068_IMG_5255.JPG', 'IMG_5255.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:01:08', 0),
(348, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744722068_IMG_5255.JPG?t=1744722068466&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '15:01:00', '15:01:00', 11, 0, '2025-04-15 21:01:13', 0),
(349, 99, 0, '', 'uploads/progress_images/1744723241_SANTILLAN_MIDTERMPERMIT.png', 'SANTILLAN_MIDTERM PERMIT.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:20:41', 0),
(350, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744723241_SANTILLAN_MIDTERMPERMIT.png?t=1744723241647&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '15:20:00', '15:20:00', 11, 0, '2025-04-15 21:20:45', 0),
(351, 99, 0, '', 'uploads/progress_images/1744723650_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:27:30', 0),
(352, 99, 0, '', 'uploads/progress_images/1744723662_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:27:42', 0),
(353, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744723662_IMG_1118.JPG?t=1744723662287&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px; width: 100%;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '15:27:00', '15:27:00', 11, 0, '2025-04-15 21:27:47', 0),
(354, 99, 0, '', 'uploads/progress_images/1744724543_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:42:23', 0),
(355, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744724543_IMG_1118.JPG?t=1744724543651&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '15:42:00', '15:42:00', 11, 0, '2025-04-15 21:42:27', 0),
(356, 103, 0, '', 'uploads/progress_images/1744724918_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:48:38', 0),
(357, 99, 0, '', 'uploads/progress_images/1744724932_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:48:52', 0),
(358, 99, 0, '', 'uploads/progress_images/1744724938_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:48:58', 0),
(359, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744724938_IMG_1118.JPG?t=1744724938383&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '15:48:00', '15:48:00', 11, 0, '2025-04-15 21:49:01', 0),
(360, 99, 0, '', 'uploads/progress_documents/1744724954_AcceptanceForm.docx', 'Acceptance-Form.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:49:14', 0),
(361, 99, 0, '', 'uploads/progress_documents/1744724959_Endorsement_Echalucecopy.docx', 'Endorsement_Echaluce copy.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:49:19', 0),
(362, 99, 11, '                                                    ', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '15:49:00', '15:49:00', 11, 0, '2025-04-15 21:49:20', 0),
(363, 99, 0, '', 'uploads/progress_images/1744724971_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:49:31', 0),
(364, 99, 0, '', 'uploads/progress_images/1744724977_IMG_0049.PNG', 'IMG_0049.PNG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 21:49:37', 0),
(365, 99, 11, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744724977_IMG_0049.PNG?t=1744724977698&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;img src=&quot;http://localhost/taskmanagementsystem/uploads/progress_images/1744724977_IMG_0049.PNG?t=1744724977698&quot; style=&quot;width: 637.75px;&quot;&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 15, 2025', '2025-04-15', '15:49:00', '15:49:00', 11, 0, '2025-04-15 21:49:38', 0),
(366, 99, 0, '', 'uploads/progress_images/1744725732_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 22:02:12', 0),
(368, 99, 0, '', 'uploads/progress_images/1744726433_IMG_1118.JPG', 'IMG_1118.JPG', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-15 22:13:53', 0),
(370, 103, 0, '', 'uploads/progress_images/1744779795_ADMINPANEL.png', 'ADMIN PANEL.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:03:15', 0),
(371, 103, 0, '', 'uploads/progress_images/1744779802_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:03:22', 0),
(372, 103, 0, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744779802_imageremovebgpreview.png?t=1744779802191&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744779802_imageremovebgpreview.png?t=1744779802191&quot; style=&quot;width: 334px;&quot;&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:03:00', '07:03:00', 1, 0, '2025-04-16 13:03:23', 0),
(373, 103, 0, '', 'uploads/progress_images/1744780049_DoBronxEchaluceErrolGabrielL.jpg', 'Do Bronx(Echaluce, Errol Gabriel L).jpg', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:07:29', 0),
(374, 103, 0, '&lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744780049_DoBronxEchaluceErrolGabrielL.jpg?t=1744780049225&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:07:00', '07:07:00', 1, 0, '2025-04-16 13:07:31', 0),
(375, 99, 0, '', 'uploads/progress_images/1744780487_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:14:47', 0),
(377, 99, 0, '', 'uploads/progress_images/1744780633_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:17:13', 0),
(379, 99, 0, '', 'uploads/progress_documents/1744780967_AcceptanceForm.docx', 'Acceptance-Form.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:22:47', 0),
(381, 99, 0, '', 'uploads/progress_images/1744781216_rga.png', 'rga.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:26:56', 0),
(383, 99, 0, '', 'uploads/progress_documents/1744781232_AcceptanceForm.docx', 'Acceptance-Form.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:27:12', 0),
(385, 103, 0, '', 'uploads/progress_images/1744781479_16Ptest.png', '16P test-.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:31:19', 0),
(386, 103, 0, '&lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744781479_16Ptest.png?t=1744781479005&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:31:00', '07:31:00', 1, 0, '2025-04-16 13:31:26', 0),
(387, 103, 0, '', 'uploads/progress_images/1744781504_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:31:44', 0),
(388, 103, 12, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744781504_imageremovebgpreview.png?t=1744781504372&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:31:00', '07:31:00', 1, 0, '2025-04-16 13:31:46', 0),
(389, 103, 0, '', 'uploads/progress_documents/1744782338_CV.pdf', 'CV.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:45:38', 0),
(390, 103, 12, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:45:00', '07:45:00', 1, 0, '2025-04-16 13:45:39', 0),
(391, 103, 0, '', 'uploads/progress_documents/1744782359_as.pdf', 'as.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:45:59', 0),
(392, 103, 12, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:45:00', '07:45:00', 1, 0, '2025-04-16 13:46:01', 0),
(393, 103, 0, '', 'uploads/progress_documents/1744782689_1743388593_CalendarPrint.pdf', '1743388593_CalendarPrint.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:51:29', 0),
(394, 103, 12, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:51:00', '07:51:00', 1, 0, '2025-04-16 13:51:30', 0),
(395, 103, 0, '', 'uploads/progress_images/1744782705_DoBronxEchaluceErrolGabrielL.jpg', 'Do Bronx(Echaluce, Errol Gabriel L).jpg', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:51:45', 0),
(396, 103, 12, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744782705_DoBronxEchaluceErrolGabrielL.jpg?t=1744782705409&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:51:00', '07:51:00', 1, 0, '2025-04-16 13:51:48', 0),
(397, 103, 0, '', 'uploads/progress_documents/1744782740_04PRACTICUMPROGRAMEVALUATION.docx', '04-PRACTICUM-PROGRAM-EVALUATION.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:52:20', 0),
(398, 103, 12, '&lt;br&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:52:00', '07:52:00', 1, 0, '2025-04-16 13:52:25', 0),
(399, 89, 0, '', 'uploads/progress_images/1744782859_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:54:19', 0),
(400, 89, 10, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744782859_imageremovebgpreview.png?t=1744782859127&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:54:00', '07:54:00', 1, 0, '2025-04-16 13:54:24', 0),
(401, 89, 0, '', 'uploads/progress_documents/1744782882_AcceptanceForm.docx', 'Acceptance-Form.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:54:42', 0),
(402, 89, 10, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:54:00', '07:54:00', 1, 0, '2025-04-16 13:54:43', 0),
(403, 89, 0, '', 'uploads/progress_documents/1744782984_AcceptanceForm.docx', 'Acceptance-Form.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:56:24', 0),
(404, 89, 10, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:56:00', '07:56:00', 1, 0, '2025-04-16 13:58:08', 0),
(405, 89, 0, '', 'uploads/progress_documents/1744783100_04PRACTICUMPROGRAMEVALUATION.docx', '04-PRACTICUM-PROGRAM-EVALUATION.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 13:58:20', 0),
(406, 89, 10, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:58:00', '07:58:00', 1, 0, '2025-04-16 13:58:20', 0),
(407, 89, 0, '', 'uploads/progress_images/1744783204_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 14:00:04', 0),
(408, 89, 10, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744783204_imageremovebgpreview.png?t=1744783204150&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '07:59:00', '07:59:00', 1, 0, '2025-04-16 14:00:06', 0),
(409, 89, 0, '', 'uploads/progress_images/1744783652_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 14:07:32', 0),
(410, 89, 10, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744783652_imageremovebgpreview.png?t=1744783652393&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '08:07:00', '08:07:00', 1, 0, '2025-04-16 14:07:34', 0),
(411, 89, 0, '', 'uploads/progress_documents/1744784613_cruzImageDocumentary.pdf', 'cruz-Image Documentary.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 14:23:33', 0),
(412, 89, 10, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '08:23:00', '08:23:00', 1, 0, '2025-04-16 14:23:34', 0),
(413, 89, 0, '', 'uploads/progress_images/1744784669_Screenshot20250415220024.png', 'Screenshot 2025-04-15 220024.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 14:24:29', 0),
(414, 89, 10, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744784669_Screenshot20250415220024.png?t=1744784669595&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '08:24:00', '08:24:00', 1, 0, '2025-04-16 14:24:32', 0),
(415, 103, 0, '', 'uploads/progress_images/1744799357_imageremovebgpreview.png', 'image-removebg-preview.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 18:29:17', 0),
(416, 103, 0, '                                                    &lt;div class=&quot;file-attachment&quot;&gt;\r\n                            &lt;img src=&quot;http://localhost:3000/uploads/progress_images/1744799357_imageremovebgpreview.png?t=1744799357477&quot; class=&quot;img-fluid&quot; style=&quot;max-width: 300px; max-height: 200px;&quot;&gt;\r\n                        &lt;br&gt;&lt;/div&gt;', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '12:29:00', '12:29:00', 11, 0, '2025-04-16 18:29:22', 0),
(417, 103, 0, '', 'uploads/progress_images/1744799382_rga.png', 'rga.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 18:29:42', 0),
(419, 103, 0, '', 'uploads/progress_documents/1744810455_CalendarPrint.pdf', 'Calendar Print.pdf', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-04-16 21:34:15', 0),
(420, 103, 14, '                                                    ', '', '', '', 'Progress update on Apr 16, 2025', '2025-04-16', '15:34:00', '15:34:00', 11, 0, '2025-04-16 21:34:16', 0),
(421, 103, 0, '', 'uploads/progress_images/1755100459_Screenshot1.png', 'Screenshot (1).png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-08-13 23:54:19', 0),
(423, 106, 0, '', 'uploads/progress_documents/1755157034_PagesOJTReport2025.docx', 'Pages-OJT-Report-2025.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-08-14 15:37:14', 0),
(424, 106, 0, '', 'uploads/progress_images/1755157041_Picture1.jpg', 'Picture1.jpg', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-08-14 15:37:21', 0),
(425, 106, 16, '&lt;div class=&quot;file-attachment&quot;&gt;\r\n                                &lt;p&gt;&lt;img src=&quot;http://localhost:3000/RGAKLMS/uploads/progress_images/1755157041_Picture1.jpg?t=1755157041945&quot; style=&quot;width: 50%;&quot;&gt;&lt;i class=&quot;fas fa-file-word text-primary&quot; style=&quot;font-size: 16px; margin-right: 5px;&quot;&gt;&lt;/i&gt; \r\n                                &lt;a href=&quot;http://localhost:3000/RGAKLMS/uploads/progress_documents/1755157034_PagesOJTReport2025.docx&quot; target=&quot;_blank&quot;&gt;Pages-OJT-Report-2025.docx&lt;/a&gt;&lt;/p&gt;\r\n                            &lt;/div&gt;here is the update', '', '', '', 'Progress update on Aug 14, 2025', '2025-08-14', '09:36:00', '09:36:00', 4, 0, '2025-08-14 15:37:33', 0),
(426, 106, 0, '', 'uploads/progress_images/1755157733_Picture1.jpg', 'Picture1.jpg', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-08-14 15:48:53', 0),
(427, 106, 0, '', 'uploads/progress_images/1755157744_7.png', '7.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-08-14 15:49:04', 0),
(428, 106, 0, '', 'uploads/progress_documents/1755157777_fdffsff.docx', 'fdffsff.docx', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-08-14 15:49:37', 0),
(429, 106, 16, '&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;div class=&quot;file-attachment&quot;&gt;\r\n                                &lt;p&gt;&lt;i class=&quot;fas fa-file-word text-primary&quot; style=&quot;font-size: 16px; margin-right: 5px;&quot;&gt;&lt;/i&gt; \r\n                                &lt;a href=&quot;http://localhost:3000/RGAKLMS/uploads/progress_documents/1755157777_fdffsff.docx&quot; target=&quot;_blank&quot;&gt;fdffsff.docx&lt;/a&gt;&lt;/p&gt;\r\n                            &lt;/div&gt;&lt;p&gt;&lt;img src=&quot;http://localhost:3000/RGAKLMS/uploads/progress_images/1755157744_7.png?t=1755157744716&quot; style=&quot;width: 25%;&quot;&gt;&lt;img src=&quot;http://localhost:3000/RGAKLMS/uploads/progress_images/1755157733_Picture1.jpg?t=1755157733099&quot; style=&quot;width: 25%;&quot;&gt;                                                    &lt;/p&gt;', '', '', '', 'Progress update on Aug 14, 2025', '2025-08-14', '09:48:00', '09:48:00', 4, 0, '2025-08-14 15:49:40', 0),
(430, 107, 0, '', 'uploads/progress_images/1755159551_1.png', '1.png', '', '', '0000-00-00', '00:00:00', '00:00:00', 0, 0, '2025-08-14 16:19:11', 0),
(431, 107, 17, '&lt;p&gt;&lt;img src=&quot;http://localhost:3000/RGAKLMS/uploads/progress_images/1755159551_1.png?t=1755159551743&quot; style=&quot;width: 50%;&quot;&gt;                                                    &lt;/p&gt;', '', '', '', 'Progress update on Aug 14, 2025', '2025-08-14', '10:19:00', '10:19:00', 4, 0, '2025-08-14 16:19:21', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billing_details`
--
ALTER TABLE `billing_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billing_header`
--
ALTER TABLE `billing_header`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_name`
--
ALTER TABLE `company_name`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_colors`
--
ALTER TABLE `event_colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_visibility`
--
ALTER TABLE `event_visibility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `files_upload`
--
ALTER TABLE `files_upload`
  ADD PRIMARY KEY (`upload_id`),
  ADD KEY `file_id` (`file_id`);

--
-- Indexes for table `inquiry_list`
--
ALTER TABLE `inquiry_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_selection`
--
ALTER TABLE `inventory_selection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `out_of_stock`
--
ALTER TABLE `out_of_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `party_info`
--
ALTER TABLE `party_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `progress_files`
--
ALTER TABLE `progress_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `project_list`
--
ALTER TABLE `project_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_team`
--
ALTER TABLE `project_team`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_history`
--
ALTER TABLE `purchase_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `purchase_master`
--
ALTER TABLE `purchase_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_products`
--
ALTER TABLE `return_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_master`
--
ALTER TABLE `stock_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_list`
--
ALTER TABLE `task_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `track`
--
ALTER TABLE `track`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `progress_id` (`progress_id`),
  ADD KEY `idx_progress_id` (`progress_id`),
  ADD KEY `idx_project_id` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_productivity`
--
ALTER TABLE `user_productivity`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `billing_details`
--
ALTER TABLE `billing_details`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `billing_header`
--
ALTER TABLE `billing_header`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `borrow`
--
ALTER TABLE `borrow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `company_name`
--
ALTER TABLE `company_name`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `event_colors`
--
ALTER TABLE `event_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `event_visibility`
--
ALTER TABLE `event_visibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=905;

--
-- AUTO_INCREMENT for table `files_upload`
--
ALTER TABLE `files_upload`
  MODIFY `upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `inquiry_list`
--
ALTER TABLE `inquiry_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventory_selection`
--
ALTER TABLE `inventory_selection`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `out_of_stock`
--
ALTER TABLE `out_of_stock`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `party_info`
--
ALTER TABLE `party_info`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `progress_files`
--
ALTER TABLE `progress_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `project_list`
--
ALTER TABLE `project_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `project_team`
--
ALTER TABLE `project_team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_history`
--
ALTER TABLE `purchase_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_master`
--
ALTER TABLE `purchase_master`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `return_products`
--
ALTER TABLE `return_products`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_master`
--
ALTER TABLE `stock_master`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task_list`
--
ALTER TABLE `task_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `track`
--
ALTER TABLE `track`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_productivity`
--
ALTER TABLE `user_productivity`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=432;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_history`
--
ALTER TABLE `purchase_history`
  ADD CONSTRAINT `purchase_history_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase_master` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
