-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2025 at 02:45 AM
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
-- Database: `cms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'News', 'News', NULL),
(2, 'Event', 'Event', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `contact_person`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'St. Mary\'s Angels College of Valenzuela', 'President, Atty. Ma. Sharon Peñaflorida', 'example@gmail.com', '09123456789', 'Sta Ana, Pampanga', '2025-04-02 03:03:40'),
(2, 'example_name', 'example_person', 'example@gmail.com', '09123456789', 'example_address', '2025-04-22 01:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `author_email` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','spam') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engineers`
--

CREATE TABLE `engineers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `engineers`
--

INSERT INTO `engineers` (`id`, `name`, `email`, `phone`) VALUES
(1, 'Eng. Manny', 'example@gmail.com', '09123456789'),
(2, 'example_ne', 'example@gmail.com', '09123456789');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `file_name`, `file_path`, `file_type`, `uploaded_by`, `uploaded_at`) VALUES
(2, '1_img.png', '67ede80667974.png', 'image/png', 1, '2025-04-03 01:44:38'),
(5, '1_img.png', '67ede80668615.png', 'image/png', 1, '2025-04-03 01:44:38'),
(6, '1-img.png', '67ede806687ae.png', 'image/png', 1, '2025-04-03 01:44:38'),
(7, '2_img.png', '67ede80668907.png', 'image/png', 1, '2025-04-03 01:44:38'),
(8, '1 - VPO.png', '67ede81a0e4ff.png', 'image/png', 1, '2025-04-03 01:44:58'),
(9, '2 - VPE.png', '67ede81a0ee9d.png', 'image/png', 1, '2025-04-03 01:44:58'),
(10, '4 - QS Manager.png', '67ede81a0f0eb.png', 'image/png', 1, '2025-04-03 01:44:58'),
(11, '1 - VPO.png', '67ede81a0f2c1.png', 'image/png', 1, '2025-04-03 01:44:58'),
(12, '2 - VPE.png', '67ede81a0f473.png', 'image/png', 1, '2025-04-03 01:44:58'),
(13, '4 - QS Manager.png', '67ede81a0f5dc.png', 'image/png', 1, '2025-04-03 01:44:58'),
(14, 'Screenshot 2025-03-04 144103.png', '67edfa7acf71c.png', 'image/png', 1, '2025-04-03 03:03:22'),
(15, 'raprap.PNG', '67edfa8d9a143.png', 'image/png', 1, '2025-04-03 03:03:41'),
(16, 'partner-2.png', '67edfa940c148.png', 'image/png', 1, '2025-04-03 03:03:48'),
(17, 'raprap.PNG', '67edfa940ce65.png', 'image/png', 1, '2025-04-03 03:03:48'),
(18, 'logo-6.png', '67edfa940d2b6.png', 'image/png', 1, '2025-04-03 03:03:48'),
(19, '5 - QS Supervisor.png', '67edfa9da6484.png', 'image/png', 1, '2025-04-03 03:03:57'),
(20, '4 - QS Manager.png', '67edfa9da67cd.png', 'image/png', 1, '2025-04-03 03:03:57'),
(21, '5 - QS Supervisor.png', '67edfa9da6a3a.png', 'image/png', 1, '2025-04-03 03:03:57'),
(22, '4 - QS Manager.png', '67edfa9da6bad.png', 'image/png', 1, '2025-04-03 03:03:57'),
(23, 'partner-3.png', '67ee00f164e76.png', 'image/png', 1, '2025-04-03 03:30:57'),
(24, 'partner-4.png', '67ee00f165591.png', 'image/png', 1, '2025-04-03 03:30:57'),
(25, 'partner-5.png', '67ee00f16582a.png', 'image/png', 1, '2025-04-03 03:30:57'),
(26, 'partner-6.png', '67ee00f1659c3.png', 'image/png', 1, '2025-04-03 03:30:57'),
(27, 'partner-3.png', '67ee00f165b13.png', 'image/png', 1, '2025-04-03 03:30:57'),
(28, 'partner-4.png', '67ee00f1662d5.png', 'image/png', 1, '2025-04-03 03:30:57'),
(29, 'partner-5.png', '67ee00f1664ef.png', 'image/png', 1, '2025-04-03 03:30:57'),
(30, 'partner-6.png', '67ee00f16678c.png', 'image/png', 1, '2025-04-03 03:30:57'),
(31, '4-img.png', '67ee00fd69b07.png', 'image/png', 1, '2025-04-03 03:31:09'),
(32, '4-img.png', '67ee00fd69f87.png', 'image/png', 1, '2025-04-03 03:31:09'),
(33, 'partner-2.png', '67ee0139f13bd.png', 'image/png', 1, '2025-04-03 03:32:09'),
(34, 'partner-3.png', '67ee0139f186e.png', 'image/png', 1, '2025-04-03 03:32:09'),
(35, 'partner.png', '67ee0139f1b8b.png', 'image/png', 1, '2025-04-03 03:32:09'),
(36, '1 - VPO.png', '67ee0148c3337.png', 'image/png', 1, '2025-04-03 03:32:24'),
(37, '1_img.png', '67ee0148c3859.png', 'image/png', 1, '2025-04-03 03:32:24'),
(38, '1-img.png', '67ee0148c3a8c.png', 'image/png', 1, '2025-04-03 03:32:24'),
(39, '1 - VPO.png', '67ee0148c3c3d.png', 'image/png', 1, '2025-04-03 03:32:24'),
(40, '1_img.png', '67ee0148c3dfe.png', 'image/png', 1, '2025-04-03 03:32:24'),
(41, '1-img.png', '67ee0148c45a3.png', 'image/png', 1, '2025-04-03 03:32:24'),
(42, 'partner-2.png', '67ee014f52340.png', 'image/png', 1, '2025-04-03 03:32:31'),
(43, 'partner-3.png', '67ee014f526fe.png', 'image/png', 1, '2025-04-03 03:32:31'),
(44, 'partner.png', '67ee014f5287b.png', 'image/png', 1, '2025-04-03 03:32:31'),
(45, '4-img.png', '67ee047f910e7.png', 'image/png', 1, '2025-04-03 03:46:07'),
(46, 'logo-4.png', '67ee0491991ae.png', 'image/png', 1, '2025-04-03 03:46:25'),
(48, 'logo-6.png', '67ee049199bda.png', 'image/png', 1, '2025-04-03 03:46:25');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `slug` varchar(255) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `status`, `user_id`, `created_at`, `updated_at`, `slug`, `excerpt`, `featured_image`) VALUES
(1, 'Rcrcc', 'Happy Birthday!', 'draft', 1, '2025-04-03 01:03:47', '2025-04-03 01:03:47', 'rcrcc', 'Happy Birthday!', NULL),
(2, 'asda', 'dasdasd', 'published', 1, '2025-04-03 06:03:18', '2025-04-03 06:03:18', 'asda', 'dasdasd', 34),
(3, 'asdas', 'dasdasdasd', 'archived', 1, '2025-04-03 06:33:32', '2025-04-03 06:33:32', 'asdas', 'dasdasdasd', 33);

-- --------------------------------------------------------

--
-- Table structure for table `post_categories`
--

CREATE TABLE `post_categories` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_categories`
--

INSERT INTO `post_categories` (`post_id`, `category_id`) VALUES
(3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `industry` varchar(255) NOT NULL,
  `structure` varchar(100) DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `contract_amount` decimal(12,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planning','in_progress','on_hold','completed','cancelled') DEFAULT 'planning',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `engineer_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `location`, `industry`, `structure`, `client_id`, `contract_amount`, `start_date`, `end_date`, `status`, `created_by`, `created_at`, `updated_at`, `engineer_id`, `latitude`, `longitude`) VALUES
(1, 'Rafael Lazatin Memorial & Medical Center', 'General Construction', 'P. Gomez St. Fortune Village 7, Brgy, Parada, Valenzuela', 'Construction', 'School', 2, 97386297.00, '1969-01-09', '1999-12-09', 'completed', 1, '2025-04-02 03:07:20', '2025-04-22 05:10:58', 2, 15.91554906, 120.58297416),
(26, 'Metro Pangasinan Hospital and Medical Center (MPHMC)', 'General Construction', 'Brgy. Bacag, McArthur Highway, Villasis, Pangasinan', 'Construction', 'Hospital', 2, 9123456789.00, '2020-04-01', '2025-04-22', 'in_progress', 1, '2025-04-22 02:32:44', '2025-04-22 05:11:32', 2, 15.91554906, 120.58297416),
(28, 'Taberna Group of Companies Inc. Building', 'General Construction', 'Lot 631-A-12 Dona Sotera Street, Pillarville Subdivision Tandang Sora, Quezon City', 'Commercial', 'Commercial Office', 2, 9123456789.00, '2020-04-01', '2025-04-22', 'in_progress', 1, '2025-04-22 02:44:18', '2025-04-22 02:44:18', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `is_thumbnail` tinyint(1) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`id`, `project_id`, `file_name`, `file_path`, `is_thumbnail`, `uploaded_at`) VALUES
(2, 1, '1_img.png', '67ecafd6b8edc.png', 0, '2025-04-02 03:32:38'),
(3, 1, '1-img.png', '67ecafd6b93eb.png', 0, '2025-04-02 03:32:38'),
(4, 1, '2-img.png', '67ecafd6b9969.png', 1, '2025-04-02 03:32:38'),
(5, 26, '1-img.png', '6807004e2e8e6.png', 1, '2025-04-22 02:34:54'),
(7, 28, '465971900_1118831143579145_496892044050818851_n.jpg', '680702a5b79e5.jpg', 1, '2025-04-22 02:44:53'),
(8, 28, '465733058_1118831173579142_4082315301342805219_n.jpg', '680713eb41f4c.jpg', 0, '2025-04-22 03:58:35'),
(9, 28, '465734291_1118831213579138_1815987749983061277_n.jpg', '680713eb424c4.jpg', 0, '2025-04-22 03:58:35'),
(10, 28, '466110844_1118831256912467_8804097836596828990_n.jpg', '680713eb428c1.jpg', 0, '2025-04-22 03:58:35'),
(11, 28, '465892156_1118831276912465_46007509759900958_n.jpg', '680713eb42fe6.jpg', 0, '2025-04-22 03:58:35'),
(12, 26, '472370423_1166154155513510_8507234834191557065_n.jpg', '68071a623d75c.jpg', 0, '2025-04-22 04:26:10'),
(13, 26, '472569800_1166154208846838_8847383412141457187_n.jpg', '68071a623dd24.jpg', 0, '2025-04-22 04:26:10'),
(14, 26, '472538076_1166154242180168_1242267278437370311_n.jpg', '68071a623e069.jpg', 0, '2025-04-22 04:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `project_updates`
--

CREATE TABLE `project_updates` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `update_text` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote_requests`
--

CREATE TABLE `quote_requests` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `project_type` varchar(100) NOT NULL,
  `budget` varchar(50) NOT NULL,
  `timeline` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','contacted','quoted','converted','rejected') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote_request_files`
--

CREATE TABLE `quote_request_files` (
  `id` int(11) NOT NULL,
  `quote_request_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` enum('admin','editor','viewer') NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `permissions`) VALUES
(1, 'admin', '{\n    \"manage_posts\": true,\n    \"manage_media\": true,\n    \"manage_users\": true,\n    \"manage_settings\": true,\n\n\"projects\": true\n}'),
(2, 'editor', '{\r\n    \"manage_posts\": true,\r\n    \"manage_media\": true,\r\n    \"manage_users\": false,\r\n    \"manage_settings\": false\r\n}'),
(3, 'viewer', '{\r\n    \"manage_posts\": false,\r\n    \"manage_media\": false,\r\n    \"manage_users\": false,\r\n    \"manage_settings\": false\r\n}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'ralfh peniafiel', 'ralfhpeniafiel@gmail.com', '$2y$10$AcJPfzez9cAPDee5RKf/W.StrutlCf74.4wDe8ghkZB.rlM7WJidi', 1, '2025-04-01 01:45:55', '2025-04-23 02:13:41', 1),
(2, 'Richmon Bulanadi', 'richmonbulanadi@gmail.com', '$2y$10$9VujWpYLmwVqcVuLSEA6M.R9laWzXPI0Hxrbg.CY2fMOlAH7UKJxS', 3, '2025-04-01 02:27:03', '2025-04-01 07:23:10', 1),
(3, 'raprap', 'raprap@gmail.com', '$2y$10$tnCuRFU/1bbz4Tg/CD/p4.Q2hYTpiwwvitbGhO90EYIajV3S7wWuW', 2, '2025-04-01 07:24:25', '2025-04-23 02:13:11', 1),
(4, 'sample name', 'sampleemail@gmail.com', '$2y$10$yjccNi14ZT3CZ08fnwMg.uv5Y9kAV21Oh7zMp6AdgEsiTHy9pz5SS', 3, '2025-04-23 02:20:16', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `engineers`
--
ALTER TABLE `engineers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `featured_image` (`featured_image`);

--
-- Indexes for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_engineer` (`engineer_id`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_updates`
--
ALTER TABLE `project_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `quote_requests`
--
ALTER TABLE `quote_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `quote_request_files`
--
ALTER TABLE `quote_request_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quote_request_id` (`quote_request_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `engineers`
--
ALTER TABLE `engineers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `project_updates`
--
ALTER TABLE `project_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quote_requests`
--
ALTER TABLE `quote_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quote_request_files`
--
ALTER TABLE `quote_request_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`featured_image`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD CONSTRAINT `post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_engineer` FOREIGN KEY (`engineer_id`) REFERENCES `engineers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `project_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_updates`
--
ALTER TABLE `project_updates`
  ADD CONSTRAINT `project_updates_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_updates_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `quote_requests`
--
ALTER TABLE `quote_requests`
  ADD CONSTRAINT `quote_requests_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quote_requests_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quote_request_files`
--
ALTER TABLE `quote_request_files`
  ADD CONSTRAINT `quote_request_files_ibfk_1` FOREIGN KEY (`quote_request_id`) REFERENCES `quote_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
