-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 13, 2025 at 06:18 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u111016890_salary`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `email` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `documents_submitted` text DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `assets_given` text DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT 0.00,
  `phone` varchar(20) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `previous_employers` varchar(255) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `department`, `created_at`, `status`, `email`, `dob`, `company_email`, `date_of_joining`, `documents_submitted`, `manager`, `assets_given`, `basic_salary`, `phone`, `emergency_phone`, `father_name`, `address`, `city`, `state`, `qualification`, `previous_employers`, `bank_name`, `branch_name`, `account_number`, `ifsc_code`, `username`, `password_hash`) VALUES
(15, 'Bhavesh Tolani', 'Meta Ads', '2025-07-11 13:36:23', 'inactive', 'Test@gmail.com', '2003-06-07', 'test1@gmail.com', '2001-01-01', 'Ration Card', 'Teri MAA', 'Car Tiago EV', 99999999.99, '8829958105', '8877445512', 'Mahaveer Sankhla', 'Suthla', 'Jodhpur', 'rajasthan', '12th fail', 'sabhi digital', 'dubai', 'ghasmandi', '8898464656123', '459764318940', 'bhavesh', '$2y$10$9UJte7zrnl0Q70FfR77ese1esuDuMuJrxjujJv.bVifBxOugEDxRy'),
(16, 'Pranjal Mathur', 'Web', '2025-07-11 17:13:55', 'inactive', 'pjmathur157@gmail.com', '2000-02-11', 'pranjal@mediagarh.com', '2024-10-14', 'PAN', 'NA', 'LAPTOP', 20000.00, '08003141049', '', 'Sanjay Mathur', '17E/826, Chopasni Housing Board', 'Jodhpur', 'Rajasthan', 'MCA', 'Coozmoo', 'Kotak Mahindra Bank', 'Bombay Motor', '98273827382783728732', 'KKBK', 'pjmathur157', '$2y$10$OyWrhWQLYblFBh4jxNvbFuBuN/v.0.x4Wia/6JjJFrHopXjUVokkq'),
(17, 'Pranjal Mathur', 'Web', '2025-07-12 10:14:15', 'inactive', 'pjmathur157@gmail.com', '2000-02-11', 'pranjal@mediagarh.com', '2024-10-14', 'PAN', 'NA', 'LAPTOP', 20000.00, '08003141049', '', 'Sanjay Mathur', '17E/826, Chopasni Housing Board', 'Jodhpur', 'Rajasthan', 'MCA', 'Coozmoo', 'Kotak Mahindra Bank', 'Bombay Motor', '98273827382783728732', 'KKBK', NULL, NULL),
(18, 'Pranjal Mathur Test', 'Web', '2025-07-12 10:27:16', 'inactive', 'pjmathur157@gmail.com', '2000-02-11', 'pranjal@mediagarh.com', '2024-10-14', 'PAN', 'NA', 'LAPTOP', 20000.00, '08003141049', '', 'Sanjay Mathur', '17E/826, Chopasni Housing Board', 'Jodhpur', 'Rajasthan', 'MCA', 'Coozmoo', 'Kotak Mahindra Bank', 'Bombay Motor', '98273827382783728732', 'KKBK', NULL, NULL),
(19, 'Pranjal Mathur', 'Developer', '2025-07-13 09:24:50', 'active', 'pjmathur157@gmail.com', '2000-02-11', 'pjmathur157@gmail.com', '2024-10-14', 'PAN', 'NA', 'MaCBook', 40000.00, '08003141049', '08458151222', 'Sanjay Mathur', '17E/826, Chopasni Housing Board', 'Jodhpur', 'Rajasthan', 'MCA', 'Coozmoo', 'Kotak Mahindra Bank', 'Bombay Motor', '98273827382783728732', 'KKBK', NULL, NULL),
(20, 'Dhananjay Soni', 'Social Media', '2025-07-13 15:24:35', 'active', 'pjmathur15786@gmail.com', '2000-02-11', 'pjmathur157@gmail.com', '2024-10-14', 'PAN', 'NA', '0', 40000.00, '08003141049', '', 'Sanjay Mathur', '17E/826, Chopasni Housing Board', 'Jodhpur', 'Rajasthan', 'MCA', 'Coozmoo', 'Kotak Mahindra Bank', 'Bombay Motor', '98273827382783728732', 'KKBK', 'admin_dj', '$2y$10$ZJWFzxfnK/vRPc8u97GvQeGD1CLnGCaRTJH5njddxYZ1K4fUkG23K');

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('Paid','Unpaid') NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `late_join_time` time DEFAULT NULL,
  `half_day_option` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `employee_id`, `leave_type`, `from_date`, `to_date`, `reason`, `status`, `created_at`, `updated_at`, `late_join_time`, `half_day_option`) VALUES
(1, 16, 'Paid', '2025-07-14', '2025-07-18', 'Sick', 'rejected', '2025-07-13 13:01:40', '2025-07-13 13:07:47', NULL, NULL),
(2, 16, 'Unpaid', '2025-07-14', '2025-07-15', 'Going out of station', 'approved', '2025-07-13 13:28:43', '2025-07-13 13:28:57', NULL, NULL),
(3, 16, 'Paid', '2025-07-14', '2025-07-17', 'not well', 'approved', '2025-07-13 14:00:03', '2025-07-13 14:00:13', NULL, NULL),
(4, 16, 'Unpaid', '2025-07-14', '2025-07-16', 'Sick', 'rejected', '2025-07-13 14:07:27', '2025-07-13 14:11:13', NULL, NULL),
(5, 16, 'Paid', '2025-07-14', '2025-07-18', 'Sick', 'rejected', '2025-07-13 14:11:39', '2025-07-13 14:26:34', NULL, NULL),
(6, 16, 'Unpaid', '2025-07-14', '2025-07-18', 'Sick', 'approved', '2025-07-13 14:26:25', '2025-07-13 14:26:41', NULL, NULL),
(7, 20, 'Paid', '2025-07-14', '2025-07-15', 'adf', 'rejected', '2025-07-13 15:27:46', '2025-07-13 15:29:00', NULL, NULL),
(8, 20, 'Paid', '2025-07-14', '2025-07-18', 'sock', 'rejected', '2025-07-13 15:29:52', '2025-07-13 15:40:29', NULL, NULL),
(9, 20, 'Unpaid', '2025-07-15', '2025-07-24', 'yt', 'rejected', '2025-07-13 15:30:23', '2025-07-13 15:40:26', NULL, NULL),
(10, 20, 'Paid', '2025-07-16', '2025-07-31', 'Sick', 'rejected', '2025-07-13 15:32:36', '2025-07-13 15:40:24', NULL, NULL),
(11, 20, 'Unpaid', '2025-07-14', '2025-07-23', 'jhgf', 'rejected', '2025-07-13 15:43:02', '2025-07-13 17:34:34', NULL, NULL),
(12, 20, '', '2025-07-14', '2025-07-14', 'Sick', 'rejected', '2025-07-13 17:31:01', '2025-07-13 17:34:31', NULL, 'Joining in 2nd Half'),
(13, 20, '', '2025-07-14', '2025-07-14', 'Sick', 'rejected', '2025-07-13 17:34:42', '2025-07-13 17:42:38', NULL, 'Joining in 2nd Half'),
(14, 20, '', '2025-07-14', '2025-07-14', 'Sick', 'rejected', '2025-07-13 17:35:02', '2025-07-13 17:42:35', NULL, 'Available for 1st Half'),
(15, 20, '', '2025-07-15', '2025-07-15', 'Sick', 'rejected', '2025-07-13 17:42:59', '2025-07-13 17:47:08', NULL, 'Joining in 2nd Half'),
(16, 20, '', '2025-07-15', '2025-07-15', 'Sick', 'pending', '2025-07-13 17:47:43', '2025-07-13 17:47:43', NULL, 'Available for 1st Half'),
(17, 20, '', '2025-07-17', '2025-07-17', 'Sick', 'pending', '2025-07-13 17:54:36', '2025-07-13 17:54:36', NULL, 'Available for 1st Half'),
(18, 20, '', '2025-07-15', '2025-07-15', 'Sick', 'pending', '2025-07-13 18:01:01', '2025-07-13 18:01:01', NULL, 'Available for 1st Half'),
(19, 20, '', '2025-07-15', '2025-07-15', 'test', 'pending', '2025-07-13 18:06:42', '2025-07-13 18:06:42', '11:30:00', ''),
(20, 20, '', '2025-07-15', '2025-07-15', 'Sick', 'pending', '2025-07-13 18:14:49', '2025-07-13 18:14:49', '11:44:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salaries`
--

INSERT INTO `salaries` (`id`, `employee_name`, `basic_salary`, `allowances`, `deductions`, `net_salary`, `created_at`) VALUES
(1, 'John Doe', 50000.00, 5000.00, 2000.00, 53000.00, '2025-07-08 11:55:32'),
(2, 'wewe', 2323.00, 0.00, 2.00, 0.00, '2025-07-08 12:50:51'),
(3, 'Pranjal Mathur', 40000.00, 0.00, 2000.00, 0.00, '2025-07-08 12:51:53'),
(4, 'Pranjal Mathur', 40000.00, 0.00, 20.00, 39980.00, '2025-07-08 12:53:29');

-- --------------------------------------------------------

--
-- Table structure for table `salary_records`
--

CREATE TABLE `salary_records` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `leaves` int(11) DEFAULT 0,
  `half_days` int(11) DEFAULT 0,
  `early_leaves` int(11) DEFAULT 0,
  `net_salary` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salary_records`
--

INSERT INTO `salary_records` (`id`, `employee_id`, `month`, `year`, `basic_salary`, `allowances`, `deductions`, `leaves`, `half_days`, `early_leaves`, `net_salary`, `created_at`) VALUES
(14, 15, 9, 2025, 20000.00, 0.00, 0.00, 2, 1, 0, 19032.26, '2025-07-11 13:37:10'),
(15, 16, 10, 2025, 36000.00, 0.00, 0.00, 1, 1, 0, 35419.35, '2025-07-11 17:16:22'),
(16, 18, 9, 2024, 30000.00, 0.00, 0.00, 0, 0, 0, 30000.00, '2025-07-12 10:27:41'),
(17, 15, 11, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:11:12'),
(18, 15, 10, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:11:22'),
(19, 15, 12, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:11:29'),
(20, 15, 1, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:11:37'),
(21, 15, 2, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:11:44'),
(22, 15, 3, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:12:20'),
(23, 15, 4, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:12:29'),
(24, 15, 5, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:12:38'),
(25, 15, 5, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:12:45'),
(26, 15, 6, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:12:52'),
(27, 15, 7, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:12:59'),
(28, 15, 8, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-12 12:13:06'),
(29, 15, 5, 2024, 50000.00, 0.00, 0.00, 0, 0, 0, 50000.00, '2025-07-13 07:17:47'),
(30, 15, 1, 2025, 30000.00, 0.00, 0.00, 0, 0, 0, 30000.00, '2025-07-13 09:08:12'),
(31, 15, 12, 2025, 30000.00, 0.00, 0.00, 0, 0, 0, 30000.00, '2025-07-13 09:08:29'),
(32, 15, 7, 2025, 30000.00, 0.00, 0.00, 0, 0, 0, 30000.00, '2025-07-13 09:08:57'),
(33, 19, 1, 2025, 30000.00, 0.00, 0.00, 0, 0, 0, 30000.00, '2025-07-13 09:25:28'),
(34, 19, 7, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-13 13:58:11'),
(35, 19, 7, 2025, 20000.00, 0.00, 0.00, 0, 0, 0, 20000.00, '2025-07-13 14:00:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `status` (`status`),
  ADD KEY `from_date` (`from_date`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salary_records`
--
ALTER TABLE `salary_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `salary_records`
--
ALTER TABLE `salary_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
