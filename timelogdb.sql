-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql101.infinityfree.com
-- Generation Time: Aug 03, 2025 at 05:14 PM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39624585_timelogdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `timelogs`
--

CREATE TABLE `timelogs` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `log_time` time NOT NULL,
  `type` enum('IN','OUT') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `timelogs`
--

INSERT INTO `timelogs` (`id`, `employee_id`, `log_date`, `log_time`, `type`) VALUES
(1, 2, '2025-08-03', '11:50:25', ''),
(2, 3, '2025-08-03', '11:50:58', ''),
(3, 4, '2025-08-03', '11:51:26', ''),
(4, 2, '2025-08-03', '11:59:56', 'IN'),
(5, 2, '2025-08-03', '11:59:59', 'OUT'),
(6, 2, '2025-08-03', '12:00:09', 'OUT'),
(7, 2, '2025-08-03', '12:04:16', 'IN'),
(8, 2, '2025-08-03', '12:04:18', 'OUT'),
(9, 2, '2025-08-03', '12:05:08', 'IN'),
(10, 2, '2025-08-03', '12:05:24', 'OUT'),
(11, 2, '2025-08-03', '12:11:23', 'IN'),
(12, 2, '2025-08-03', '12:16:39', 'IN'),
(13, 2, '2025-08-03', '12:16:46', 'OUT'),
(14, 2, '2025-08-03', '12:33:59', 'OUT'),
(15, 2, '2025-08-03', '12:34:00', 'IN'),
(16, 2, '2025-08-03', '12:34:05', 'OUT'),
(17, 3, '2025-08-03', '13:14:36', 'IN'),
(18, 4, '2025-08-03', '13:36:32', 'IN'),
(19, 2, '2025-08-03', '14:05:13', 'IN'),
(20, 2, '2025-08-03', '14:08:19', 'IN'),
(21, 4, '2025-08-03', '14:08:47', 'OUT'),
(22, 2, '2025-08-03', '14:09:42', 'IN'),
(23, 2, '2025-08-04', '02:13:40', 'IN'),
(24, 2, '2025-08-04', '02:13:46', 'OUT'),
(25, 2, '2025-08-04', '02:39:44', 'IN'),
(26, 2, '2025-08-04', '02:39:46', 'OUT'),
(27, 6, '2025-08-04', '04:06:45', 'IN'),
(28, 6, '2025-08-04', '04:06:48', 'OUT'),
(29, 5, '2025-08-04', '04:09:30', 'IN'),
(30, 5, '2025-08-04', '04:09:33', 'OUT');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `position`, `password`, `role`) VALUES
(1, 'admin', 'Sebastian', 'Pacaldo', 'IT Administrator', '$2y$10$McI5STiwYpDMQ2GU6yF2yebE4p7ViAQlVuOYJ7Ns8gnBCQfkroY62', 'admin'),
(2, 'employee1', 'Maria', 'Santos', 'Sales Associate', '$2y$10$FQOFWZomxf2CFYJz3IOu1On1GOWyKDaxGrFfScp6sihHK/JA17ADe', 'employee'),
(3, 'employee2', 'John', 'Reyes', 'IT Support', '$2y$10$g347zweCSytgrD6LTBTp7OdvRhIDNtxXLnMmDJAMwIcxYAcbAhSs6', 'employee'),
(4, 'employee3', 'Angela', 'Cruz', 'HR Specialist', '$2y$10$/Yp7nOq/ukJpJKh083jCDe9zfXQ2J4vinYCQj5Tt7APZe3pqqekTK', 'employee'),
(5, 'employee4', 'Gerald', 'Ginto', 'Sales Associate', '$2y$10$p/YDufKXJqpi6SrBDjjoJe41xRKW6e8cEZde4SgvtVonhqiVs/XDS', 'employee'),
(6, 'employee5', 'Hayb', 'Suarez', 'Sales Associate', '$2y$10$PO7fkurdrakOs6iSAh6jeO5mFP0GvbhpapuPSFoJRK2tJq3hsHMmi', 'employee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `timelogs`
--
ALTER TABLE `timelogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `timelogs`
--
ALTER TABLE `timelogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
