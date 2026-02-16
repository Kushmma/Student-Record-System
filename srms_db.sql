-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2026 at 12:06 PM
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
-- Database: `srms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `finances`
--

CREATE TABLE `finances` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `status` enum('Paid','Pending','Overdue') DEFAULT 'Pending',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finances`
--

INSERT INTO `finances` (`id`, `student_id`, `amount_due`, `amount_paid`, `payment_date`, `status`, `remarks`, `created_at`) VALUES
(1, 2, 1000.00, 100.00, '2025-09-30', 'Pending', '..', '2025-10-22 09:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(100) NOT NULL,
  `marks` decimal(5,2) NOT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `subject`, `marks`, `grade`, `created_at`) VALUES
(1, 2, 'Math', 3.00, 'F', '2025-10-22 07:34:58'),
(3, 5, 'sci', 2.00, 'a', '2025-11-04 07:54:00');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `semester` varchar(50) DEFAULT '',
  `class` varchar(50) DEFAULT '',
  `roll_no` varchar(20) DEFAULT '',
  `status` varchar(20) DEFAULT 'Active',
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `parent_name` varchar(200) DEFAULT NULL,
  `parent_phone` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `phone`, `gender`, `is_admin`, `created_at`, `updated_at`, `semester`, `class`, `roll_no`, `status`, `date_of_birth`, `address`, `city`, `parent_name`, `parent_phone`, `emergency_contact`, `admission_date`, `course`, `department`) VALUES
(1, 'Admin', 'User', 'admin', 'admin@example.com', '$2y$10$2PvyCimFGf.zLtllwh1Ov.wHSnugh6oj4EdU53BAbY/ZHloaLSi3C', NULL, NULL, 1, '2025-10-20 02:24:44', '2025-10-22 00:09:34', '', '', '', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Apple', 'Play', 'Apple', 'apple@gmail.com', '$2y$10$iHguFDNlkYmpJUpd0CczTueEmB1bzuvBIHBkoyT7iG0Ky0UZQJyfe', '123456', 'Male', 0, '2025-10-20 09:49:12', '2025-10-22 06:28:44', '', '', '', 'Active', '2021-02-16', 'Sample Address 2', 'Sample City', 'Parent of Apple Play', '9876500002', '9876500102', '2025-10-10', 'Bachelor of Computer Applications', 'Computer Science'),
(3, 'Apples', 'Play', 'banana', 'banana@gmail.com', '$2y$10$EELnN8MPguzH2dD06jlEAebvKEvZboR8I3ck8M5JpMuniweggcwXK', '123456', 'Male', 0, '2025-10-20 10:55:08', '2025-10-22 06:28:44', '2', '4', '12', 'Active', '2018-03-01', 'Sample Address 3', 'Sample City', 'Parent of Apples Play', '9876500003', '9876500103', '2025-02-01', 'Bachelor of Computer Applications', 'Computer Science'),
(4, 'alu', 'gupta', 'alu', 'alu@gmail.com', '$2y$10$rJeg4MoRtlTM7KjRxvFFTuZshqiQNS28HiKBzR7dwVbRYdLl08XSG', '123456', 'Male', 0, '2025-10-26 11:22:44', '2025-10-26 11:22:44', '', '', '', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'rakesh', 'bakesh', 'rakeshbakesh', 'rakesh123@gmail.com', '$2y$10$BUjvJ4ymncFmVjhQ5rV2EeJdxlzGhf3lPHGjxt12ssXYZipRcXF6q', '123', 'Other', 0, '2025-11-04 07:49:42', '2025-11-04 07:49:42', '', '', '', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'SANDIP', 'OLI', 'sandip123', 'sandip@gmail.com', '$2y$10$2lYh01PtSERkdI0SbF8YRuosm5DhQMo82Tl67DK70O9ohCDQBJ73i', '123', 'Other', 0, '2025-11-04 08:02:07', '2025-11-04 08:02:07', '', '', '', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `finances`
--
ALTER TABLE `finances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_roll_no` (`roll_no`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_class` (`class`),
  ADD KEY `idx_semester` (`semester`),
  ADD KEY `idx_course` (`course`),
  ADD KEY `idx_department` (`department`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `finances`
--
ALTER TABLE `finances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `finances`
--
ALTER TABLE `finances`
  ADD CONSTRAINT `finances_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
