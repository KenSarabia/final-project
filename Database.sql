-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2025 at 04:29 AM
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
-- Database: `barangay_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangay_clearances`
--

CREATE TABLE `barangay_clearances` (
  `clearance_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `issued_date` date NOT NULL,
  `status` enum('Pending','Approved','Released') DEFAULT 'Approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_clearances`
--

INSERT INTO `barangay_clearances` (`clearance_id`, `resident_id`, `issued_by`, `purpose`, `issued_date`, `status`) VALUES
(3, 4, 1, 'Going to New York', '2003-10-27', 'Released'),
(4, 6, 1, 'Employment Requirment', '2023-09-12', 'Released'),
(5, 5, 1, 'Employment Requirment', '2023-10-23', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `household_id` int(11) NOT NULL,
  `household_no` varchar(20) NOT NULL,
  `head_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `purok` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `officials`
--

CREATE TABLE `officials` (
  `official_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `position` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officials`
--

INSERT INTO `officials` (`official_id`, `first_name`, `last_name`, `position`, `contact_number`, `address`, `created_at`) VALUES
(2, 'Maria', 'Santos', 'Barangay Secretary', '09182345678', 'Purok 2, Nag-iba 1', '2025-10-26 08:14:07'),
(3, 'PaoPao', 'Luna', 'Barangay Tanggol', '09478378483', 'Nag Iba 1, Purok 3', '2025-10-26 10:54:26');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `resident_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`resident_id`, `first_name`, `last_name`, `birthdate`, `gender`, `purok`, `address`, `created_at`) VALUES
(4, 'Jetro', 'Plata', '2015-10-04', 'Male', 'Purok 2', '', '2025-10-26 10:52:07'),
(5, 'Kaye', 'Sarabia', '2010-10-29', 'Male', 'Purok 4', '', '2025-10-27 03:13:10'),
(6, 'Jon', 'Umali', '2006-12-23', 'Male', 'Purok 2', '', '2025-10-27 05:56:10'),
(7, 'Michell', 'Alvarez', '2003-11-25', 'Female', 'Purok 3', '', '2025-10-27 06:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `first_name`, `last_name`) VALUES
(1, 'admin', '$2y$10$Fh1GrVhoPLU9KIn7TKRg/eHwd.rBpD2a4lNRl.8AamFEzi7N4gKsa', 'admin', 'System', 'Administrator'),
(2, 'staff', '$2y$10$4faNgtRzhEjxbEV0GlWByOY7kDKsLQorATtcOJknjTbuPQKOnZxX6', 'staff', 'Barangay', 'Staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  ADD PRIMARY KEY (`clearance_id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `issued_by` (`issued_by`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`household_id`),
  ADD UNIQUE KEY `household_no` (`household_no`),
  ADD KEY `head_id` (`head_id`);

--
-- Indexes for table `officials`
--
ALTER TABLE `officials`
  ADD PRIMARY KEY (`official_id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`resident_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  MODIFY `clearance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `household_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `official_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `resident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  ADD CONSTRAINT `barangay_clearances_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`resident_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `barangay_clearances_ibfk_2` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `households`
--
ALTER TABLE `households`
  ADD CONSTRAINT `households_ibfk_1` FOREIGN KEY (`head_id`) REFERENCES `residents` (`resident_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
