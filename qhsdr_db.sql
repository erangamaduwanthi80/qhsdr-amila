-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 30, 2026 at 03:39 PM
-- Server version: 8.0.45-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qhsdr_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int UNSIGNED DEFAULT NULL,
  `snapshot` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `username`, `action`, `entity`, `entity_id`, `snapshot`, `ip_address`, `created_at`) VALUES
(1, 1, 'admin', 'login', 'users', 1, NULL, '::1', '2026-04-30 14:31:48'),
(2, 1, 'admin', 'logout', 'users', 1, NULL, '::1', '2026-04-30 14:32:39'),
(3, NULL, NULL, 'login_failed', 'users', NULL, '{\"username\":\"test1@example.com\"}', '::1', '2026-04-30 15:27:50'),
(4, 1, 'admin', 'login', 'users', 1, NULL, '::1', '2026-04-30 15:27:57'),
(5, 1, 'admin', 'create', 'locations', 3, '{\"code\":\"J1\",\"name\":\"7C\",\"status\":\"active\",\"remark\":\"\"}', '::1', '2026-04-30 15:28:45'),
(6, 1, 'admin', 'create', 'locations', 4, '{\"code\":\"J2\",\"name\":\"Pannala\",\"status\":\"active\",\"remark\":\"\"}', '::1', '2026-04-30 15:29:07'),
(7, 1, 'admin', 'create', 'locations', 5, '{\"code\":\"J3\",\"name\":\"Kobeigane\",\"status\":\"active\",\"remark\":\"\"}', '::1', '2026-04-30 15:29:29'),
(8, 1, 'admin', 'create', 'locations', 6, '{\"code\":\"J4\",\"name\":\"JECOE\",\"status\":\"active\",\"remark\":\"\"}', '::1', '2026-04-30 15:29:40'),
(9, 1, 'admin', 'update', 'machines', 6, '{\"machine_type\":\"Plug Hole Machine\",\"machine_no\":\"PH-001\",\"machine_name\":\"Plug Alpha\",\"location_id\":4}', '::1', '2026-04-30 15:30:02'),
(10, 1, 'admin', 'update', 'machines', 7, '{\"machine_type\":\"Plug Hole Machine\",\"machine_no\":\"PH-002\",\"machine_name\":\"Plug Beta\",\"location_id\":4}', '::1', '2026-04-30 15:30:21'),
(11, 1, 'admin', 'update', 'machines', 1, '{\"machine_type\":\"Pressing Machine\",\"machine_no\":\"PM-001\",\"machine_name\":\"Press Alpha\",\"location_id\":4}', '::1', '2026-04-30 15:30:32'),
(12, 1, 'admin', 'update', 'machines', 2, '{\"machine_type\":\"Pressing Machine\",\"machine_no\":\"PM-002\",\"machine_name\":\"Press Beta\",\"location_id\":4}', '::1', '2026-04-30 15:30:38'),
(13, 1, 'admin', 'update', 'machines', 3, '{\"machine_type\":\"Pressing Machine\",\"machine_no\":\"PM-003\",\"machine_name\":\"Press Gamma\",\"location_id\":4}', '::1', '2026-04-30 15:30:44'),
(14, 1, 'admin', 'update', 'machines', 4, '{\"machine_type\":\"Wrapping Machine\",\"machine_no\":\"WM-001\",\"machine_name\":\"Wrap Alpha\",\"location_id\":4}', '::1', '2026-04-30 15:30:49'),
(15, 1, 'admin', 'update', 'machines', 5, '{\"machine_type\":\"Wrapping Machine\",\"machine_no\":\"WM-002\",\"machine_name\":\"Wrap Beta\",\"location_id\":4}', '::1', '2026-04-30 15:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `data_feed`
--

CREATE TABLE `data_feed` (
  `id` int NOT NULL,
  `feed_date` date NOT NULL,
  `shift_id` int NOT NULL,
  `hour_no` tinyint NOT NULL,
  `location_id` int UNSIGNED DEFAULT NULL,
  `machine_id` int NOT NULL,
  `defect_id` int NOT NULL,
  `defect_qty` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_feed`
--

INSERT INTO `data_feed` (`id`, `feed_date`, `shift_id`, `hour_no`, `location_id`, `machine_id`, `defect_id`, `defect_qty`, `created_at`) VALUES
(1, '2026-04-01', 1, 6, NULL, 1, 1, 5, '2026-04-23 12:05:22'),
(2, '2026-04-01', 1, 7, NULL, 2, 2, 3, '2026-04-23 12:05:22'),
(3, '2026-04-01', 2, 8, NULL, 4, 5, 7, '2026-04-23 12:05:22'),
(4, '2026-04-02', 1, 6, NULL, 3, 3, 2, '2026-04-23 12:05:22'),
(5, '2026-04-02', 2, 7, NULL, 5, 6, 4, '2026-04-23 12:05:22'),
(6, '2026-04-03', 1, 8, NULL, 6, 8, 6, '2026-04-23 12:05:22'),
(7, '2026-04-03', 1, 6, NULL, 7, 9, 3, '2026-04-23 12:05:22'),
(8, '2026-04-04', 2, 7, NULL, 1, 4, 8, '2026-04-23 12:05:22'),
(9, '2026-04-04', 1, 9, NULL, 4, 7, 2, '2026-04-23 12:05:22'),
(10, '2026-04-05', 2, 8, NULL, 2, 1, 5, '2026-04-23 12:05:22'),
(11, '2026-04-05', 1, 6, NULL, 6, 10, 4, '2026-04-23 12:05:22'),
(12, '2026-04-06', 2, 7, NULL, 3, 2, 3, '2026-04-23 12:05:22'),
(13, '2026-04-07', 1, 8, NULL, 5, 5, 6, '2026-04-23 12:05:22'),
(14, '2026-04-07', 2, 9, NULL, 7, 8, 2, '2026-04-23 12:05:22'),
(15, '2026-04-08', 1, 6, NULL, 1, 3, 4, '2026-04-23 12:05:22'),
(16, '2026-04-09', 2, 7, NULL, 2, 6, 5, '2026-04-23 12:05:22'),
(17, '2026-04-10', 1, 8, NULL, 4, 9, 3, '2026-04-23 12:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `defects`
--

CREATE TABLE `defects` (
  `id` int NOT NULL,
  `machine_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `defect_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `defect_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `defects`
--

INSERT INTO `defects` (`id`, `machine_type`, `defect_code`, `defect_name`, `created_at`) VALUES
(1, 'Pressing Machine', 'PM-D01', 'Surface Crack', '2026-04-23 12:05:22'),
(2, 'Pressing Machine', 'PM-D02', 'Dimension Error', '2026-04-23 12:05:22'),
(3, 'Pressing Machine', 'PM-D03', 'Edge Burr', '2026-04-23 12:05:22'),
(4, 'Pressing Machine', 'PM-D04', 'Material Deformation', '2026-04-23 12:05:22'),
(5, 'Wrapping Machine', 'WM-D01', 'Loose Wrap', '2026-04-23 12:05:22'),
(6, 'Wrapping Machine', 'WM-D02', 'Wrinkle', '2026-04-23 12:05:22'),
(7, 'Wrapping Machine', 'WM-D03', 'Seal Break', '2026-04-23 12:05:22'),
(8, 'Plug Hole Machine', 'PH-D01', 'Hole Misalignment', '2026-04-23 12:05:22'),
(9, 'Plug Hole Machine', 'PH-D02', 'Incomplete Hole', '2026-04-23 12:05:22'),
(10, 'Plug Hole Machine', 'PH-D03', 'Burr at Hole', '2026-04-23 12:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remark` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `code`, `name`, `status`, `remark`, `created_at`) VALUES
(3, 'J1', '7C', 'active', '', '2026-04-30 15:28:45'),
(4, 'J2', 'Pannala', 'active', '', '2026-04-30 15:29:07'),
(5, 'J3', 'Kobeigane', 'active', '', '2026-04-30 15:29:29'),
(6, 'J4', 'JECOE', 'active', '', '2026-04-30 15:29:40');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip_address`, `success`, `attempted_at`) VALUES
(1, 'admin', '::1', 1, '2026-04-30 14:31:48'),
(2, 'test1@example.com', '::1', 0, '2026-04-30 15:27:50'),
(3, 'admin', '::1', 1, '2026-04-30 15:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE `machines` (
  `id` int NOT NULL,
  `machine_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int UNSIGNED DEFAULT NULL,
  `machine_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `machine_type`, `location_id`, `machine_no`, `machine_name`, `machine_photo`, `remark`, `created_at`) VALUES
(1, 'Pressing Machine', 4, 'PM-001', 'Press Alpha', NULL, 'Line A', '2026-04-23 12:05:22'),
(2, 'Pressing Machine', 4, 'PM-002', 'Press Beta', NULL, 'Line B', '2026-04-23 12:05:22'),
(3, 'Pressing Machine', 4, 'PM-003', 'Press Gamma', NULL, 'Line C', '2026-04-23 12:05:22'),
(4, 'Wrapping Machine', 4, 'WM-001', 'Wrap Alpha', NULL, 'Line A', '2026-04-23 12:05:22'),
(5, 'Wrapping Machine', 4, 'WM-002', 'Wrap Beta', NULL, 'Line B', '2026-04-23 12:05:22'),
(6, 'Plug Hole Machine', 4, 'PH-001', 'Plug Alpha', NULL, 'Line A', '2026-04-23 12:05:22'),
(7, 'Plug Hole Machine', 4, 'PH-002', 'Plug Beta', NULL, 'Line B', '2026-04-23 12:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `role_id` int NOT NULL,
  `module` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_add` tinyint(1) DEFAULT '0',
  `can_view` tinyint(1) DEFAULT '0',
  `can_edit` tinyint(1) DEFAULT '0',
  `can_delete` tinyint(1) DEFAULT '0',
  `can_approve` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `role_id`, `module`, `can_add`, `can_view`, `can_edit`, `can_delete`, `can_approve`) VALUES
(1, 1, 'machines', 1, 1, 1, 1, 1),
(2, 1, 'defects', 1, 1, 1, 1, 1),
(3, 1, 'datafeed', 1, 1, 1, 1, 1),
(4, 1, 'dashboard', 1, 1, 1, 1, 1),
(5, 1, 'users', 1, 1, 1, 1, 1),
(6, 1, 'roles', 1, 1, 1, 1, 1),
(7, 1, 'locations', 1, 1, 1, 1, 1),
(8, 1, 'shifts', 1, 1, 1, 1, 1),
(10, 1, 'audit', 0, 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`) VALUES
(1, 'QHS Executive', '2026-04-07 06:54:36'),
(2, 'QA Operator', '2026-04-07 06:54:36'),
(3, 'QHS Manager', '2026-04-07 06:54:36'),
(4, 'Supervisor', '2026-04-07 06:54:36'),
(5, 'Production Executive', '2026-04-07 06:54:36'),
(6, 'Production Supervisor', '2026-04-07 06:54:36');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int NOT NULL,
  `shift_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hours` tinyint NOT NULL DEFAULT '8',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_name`, `hours`, `created_at`) VALUES
(1, 'Day', 8, '2026-04-07 06:54:36'),
(2, 'Night', 8, '2026-04-07 06:54:36');

-- --------------------------------------------------------

--
-- Table structure for table `shift_hours`
--

CREATE TABLE `shift_hours` (
  `id` int UNSIGNED NOT NULL,
  `shift_id` int NOT NULL,
  `hour_no` tinyint NOT NULL,
  `start_time` varchar(8) NOT NULL,
  `end_time` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shift_hours`
--

INSERT INTO `shift_hours` (`id`, `shift_id`, `hour_no`, `start_time`, `end_time`) VALUES
(1, 1, 1, '06:00', '06:59'),
(2, 1, 2, '07:00', '07:59'),
(3, 1, 3, '08:00', '08:59'),
(4, 1, 4, '09:00', '09:59'),
(5, 1, 5, '10:00', '10:59'),
(6, 1, 6, '11:00', '11:59'),
(7, 1, 7, '12:00', '12:59'),
(8, 1, 8, '13:00', '13:59'),
(9, 2, 1, '14:00', '14:59'),
(10, 2, 2, '15:00', '15:59'),
(11, 2, 3, '16:00', '16:59'),
(12, 2, 4, '17:00', '17:59'),
(13, 2, 5, '18:00', '18:59'),
(14, 2, 6, '19:00', '19:59'),
(15, 2, 7, '20:00', '20:59'),
(16, 2, 8, '21:00', '21:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'QHS Executive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$r4Z42TxNRouuWmF2M8d7b.RLsfhAGec3FsMRuEEQHgPuhhJHSJUFq', 'Administrator', 'QHS Executive', '2026-04-07 06:54:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_entity` (`entity`,`entity_id`),
  ADD KEY `idx_audit_created` (`created_at`),
  ADD KEY `idx_audit_action` (`action`);

--
-- Indexes for table `data_feed`
--
ALTER TABLE `data_feed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `machine_id` (`machine_id`),
  ADD KEY `defect_id` (`defect_id`),
  ADD KEY `idx_datafeed_location` (`location_id`);

--
-- Indexes for table `defects`
--
ALTER TABLE `defects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_location_code` (`code`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_username_time` (`username`,`attempted_at`),
  ADD KEY `idx_login_ip_time` (`ip_address`,`attempted_at`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_machines_location` (`location_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_module` (`role_id`,`module`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_hours`
--
ALTER TABLE `shift_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_shift_hour` (`shift_id`,`hour_no`);

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
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `data_feed`
--
ALTER TABLE `data_feed`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `defects`
--
ALTER TABLE `defects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shift_hours`
--
ALTER TABLE `shift_hours`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_feed`
--
ALTER TABLE `data_feed`
  ADD CONSTRAINT `data_feed_ibfk_1` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`),
  ADD CONSTRAINT `data_feed_ibfk_2` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`),
  ADD CONSTRAINT `data_feed_ibfk_3` FOREIGN KEY (`defect_id`) REFERENCES `defects` (`id`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_hours`
--
ALTER TABLE `shift_hours`
  ADD CONSTRAINT `shift_hours_ibfk_1` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
