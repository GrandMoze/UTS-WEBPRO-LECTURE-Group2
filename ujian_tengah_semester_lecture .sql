-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2024 at 12:36 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ujian tengah semester lecture`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  `date` date NOT NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `image` varchar(255) NOT NULL,
  `max_registrations` int NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `date`, `location`, `image`, `max_registrations`, `description`) VALUES
(16, 'mmmmmmggghtjsjs', '2024-10-25', 'Palembang', 'uploads/1729706398_08271e96-e71a-480f-a5ff-f82d454f5ee4_0.jpeg', 111, ''),
(23, 'ulang tahun', '2024-10-25', 'Gambir Expo, Jakarta', 'uploads/1729763969_08271e96-e71a-480f-a5ff-f82d454f5ee4_0.jpeg', 100, ''),
(28, 'hari raya', '2024-10-31', 'jakarta, GBK', 'uploads/1729767088_gambar3.png', 1000, ''),
(33, 'ulang tahun', '2024-10-30', 'jakarta, Kemayoran', 'uploads/1729772409_Untitled image.png', 100, ''),
(34, 'MotionIme Festival', '2024-12-07', 'Gambir Expo Kemayoran,Jakarta', 'uploads/1729777239_Untitled image.png', 100, ''),
(41, 'HAJI KIKIR', '2024-11-09', 'Indonesia Arena', 'uploads/1729820394_08271e96-e71a-480f-a5ff-f82d454f5ee4_0.jpeg', 10, 'ddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `event_id`) VALUES
(43, 1, 33),
(45, 1, 28);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `profile_picture` varchar(225) NOT NULL,
  `phone_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `profile_picture`, `phone_number`) VALUES
(1, 'ilman khoir keren', 'agenggamer4@gmail.com', '$2y$10$ZoIlVAOY0wcUkEGjUQUqY.mpHwHrJrImBVrFmeEtZzwLCYCeef0r.', 'user', 'inactive', '../admin/uploads/Untitled image.png', '085893930323'),
(3, 'ddddTeam', 'agenggamer5@gmail.com', '$2y$10$Ea5hBFbx/A2n3XqUC2GY..EZtzays6Q16QmzCVWxnO8K5Zbp.dUXq', 'user', 'active', '', ''),
(100, 'admin web', 'ilman@gmail.com', '$2y$10$oMhScmfvweieRIN.fFtFYuZO16UL8EU6hWVrHUMa9vgnQYl149W5q', 'admin', 'inactive', '', ''),
(103, 'fadhil', 'hahaha@gmail.com', '$2y$10$aM0X33xIYM4ez1IQ6YXYeOZnBZH6wHM99s5mKM5fCCSS9Fi.TuscO', 'user', 'inactive', '../admin/uploads/gambar1.png', '0987654321');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registrations_ibfk_2` (`event_id`),
  ADD KEY `registrations_ibfk_1` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
