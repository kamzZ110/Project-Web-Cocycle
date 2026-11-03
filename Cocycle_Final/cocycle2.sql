-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2025 at 08:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cocycle2`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `communities`
--

CREATE TABLE `communities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `focus_area` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('pending','paid','picked_up','received','processed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_details`
--

CREATE TABLE `donation_details` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `food_item_id` int(11) NOT NULL,
  `qty` decimal(8,2) NOT NULL,
  `subtotal_fee` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `quantity_available` decimal(8,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `handling_fee` decimal(10,2) DEFAULT 0.00,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` enum('available','reserved','donated') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `impact_trackings`
--

CREATE TABLE `impact_trackings` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `product_produced` varchar(100) NOT NULL,
  `product_qty_result` varchar(50) NOT NULL,
  `co2_saved_kg` decimal(8,2) DEFAULT NULL,
  `waste_saved_kg` decimal(8,2) NOT NULL,
  `community_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `status` enum('Preparing','Ready','Completed','Cancelled') DEFAULT 'Preparing',
  `notes` text DEFAULT NULL COMMENT 'Catatan dari customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu order selesai',
  `cancelled_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu order dibatalkan',
  `cancellation_reason` text DEFAULT NULL COMMENT 'Alasan pembatalan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `store_id`, `user_id`, `invoice_number`, `total_price`, `status`, `notes`, `created_at`, `completed_at`, `cancelled_at`, `cancellation_reason`) VALUES
(1, 1, 6, 'INV-20251220-5558', 36000.00, 'Preparing', NULL, '2025-12-20 07:35:29', NULL, NULL, NULL),
(2, 1, 6, 'INV-20251220-2666', 17000.00, 'Preparing', NULL, '2025-12-20 07:36:23', NULL, NULL, NULL),
(3, 1, 6, 'INV-20251220-1083', 17000.00, 'Preparing', NULL, '2025-12-20 07:40:10', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_per_item`) VALUES
(1, 1, 7, 1, 20000.00),
(2, 1, 3, 1, 16000.00),
(3, 2, 1, 1, 7000.00),
(4, 2, 2, 1, 10000.00),
(5, 3, 1, 1, 7000.00),
(6, 3, 2, 1, 10000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `store_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0 COMMENT 'Jumlah stok tersedia',
  `status` enum('active','inactive','out_of_stock') DEFAULT 'active' COMMENT 'Status produk',
  `views_count` int(11) DEFAULT 0 COMMENT 'Jumlah views untuk analytics',
  `sold_count` int(11) DEFAULT 0 COMMENT 'Jumlah terjual untuk analytics',
  `rating` decimal(3,1) DEFAULT 5.0,
  `distance` varchar(50) DEFAULT '1 km',
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Waktu produk ditambahkan',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Waktu terakhir diupdate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `store_id`, `name`, `category`, `price`, `stock`, `status`, `views_count`, `sold_count`, `rating`, `distance`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Red Sweet Pepper', 'Vegetables & Fruits', 7000.00, 100, 'active', 853, 169, 4.9, '1.2 km', 'Paprika merah segar organik.', '../assets/peppers.jpg', '2025-12-20 02:53:01', '2025-12-20 07:15:21'),
(2, 1, 'Lemon Cake', 'Bakery & Pastry', 10000.00, 100, 'active', 135, 329, 4.8, '1.5 km', 'Kue lemon lembut dengan icing gula.', '../assets/lemon cake.jpg', '2025-12-20 02:53:01', '2025-12-20 07:15:21'),
(3, 1, 'Chocolate Matcha Cake', 'Bakery & Pastry', 16000.00, 100, 'active', 888, 233, 5.0, '1.2 km', 'Perpaduan coklat dan matcha jepang.', '../assets/matcha.jpg', '2025-12-20 02:53:01', '2025-12-20 07:15:21'),
(4, 1, 'Strawberry Smoothie', 'Drinks', 14000.00, 100, 'active', 671, 478, 4.7, '500 m', 'Smoothie strawberry segar tanpa gula.', '../assets/smoothie.jpg', '2025-12-20 02:53:01', '2025-12-20 07:15:21'),
(5, 1, 'Granola Bar', 'Snacks', 24000.00, 100, 'active', 766, 481, 4.9, '2 km', 'Camilan sehat oat dan madu.', '../assets/granola.jpg', '2025-12-20 02:53:01', '2025-12-20 07:15:21'),
(6, 1, 'Frozen Raspberry', 'Meats, Frozen & Seafood', 19000.00, 100, 'active', 515, 345, 4.6, '800 m', 'Raspberry beku kualitas premium.', '../assets/raspberry.jpg\r\n', '2025-12-20 02:53:01', '2025-12-20 07:15:21'),
(7, 1, 'lemon_cake_yummy', 'Bakery & Pastry', 20000.00, 5, 'active', 0, 0, 5.0, '1 km', 'yummy bgt pokoknya', '../assets/products/product_69464efdcdd33.jpg', '2025-12-20 07:23:41', '2025-12-20 07:23:41');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `store_name` varchar(100) DEFAULT NULL,
  `operational_hours` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT 'Nomor telepon toko',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email toko',
  `logo_url` varchar(255) DEFAULT NULL COMMENT 'Logo toko',
  `status` enum('active','inactive','suspended') DEFAULT 'active' COMMENT 'Status toko',
  `description` text DEFAULT NULL COMMENT 'Deskripsi toko',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `user_id`, `store_name`, `operational_hours`, `address`, `phone`, `email`, `logo_url`, `status`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Toko Saya', NULL, NULL, NULL, NULL, NULL, 'active', 'Toko keren untuk demo campus', '2025-12-20 07:06:48', '2025-12-20 07:06:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('customer','partner') DEFAULT 'customer',
  `kode_otp` varchar(10) DEFAULT NULL,
  `batas_waktu` datetime DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `nama_toko` varchar(255) DEFAULT NULL,
  `balance` decimal(12,2) DEFAULT 0.00 COMMENT 'Saldo partner'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `profile_pic`, `created_at`, `updated_at`, `role`, `kode_otp`, `batas_waktu`, `address`, `fullname`, `nama_toko`, `balance`) VALUES
(1, 'firkam', 'kamz@gmail.com', '0819219829', '$2y$12$O7UHX9bjHfMElu7vSYi8ZONumc2UEbSqyh/bTlJIrA3p4eVCluqeS', NULL, '2025-12-04 19:12:39', '2025-12-04 19:12:39', 'customer', NULL, NULL, NULL, NULL, NULL, 0.00),
(2, 'ferduy', 'fer@gmail.com', '08123981923', '$2y$12$GwVreiTZz1ApePMhbYefmuFBwMfj6Mw41dJKMNkGNhG74VXfEbLH.', NULL, '2025-12-05 05:02:30', '2025-12-05 05:02:30', 'customer', NULL, NULL, NULL, NULL, NULL, 0.00),
(3, 'malvin', 'choi@gmail.com', '123', '$2y$12$3jh8EzqpArw1Mk5QZGg5cu8PHc3gqbZ2AJhpRHMk4dmDwq4SoiWvW', 'assets/profile_pics/profile_6937e2d62a58c9.94307947.jpg', '2025-12-09 08:50:16', '2025-12-09 08:54:44', 'customer', NULL, NULL, 'test', 'test', NULL, 0.00),
(4, 'tokoroti', 'roti@gmail.com', '081271931', '$2y$12$pvjTk9DCX.PUpWsg5SGze.YPkCTy9c03FgYpIfs1ndVe8/9soKzW.', 'assets/default_profile.png', '2025-12-20 01:49:34', '2025-12-20 01:49:34', 'customer', NULL, NULL, NULL, NULL, NULL, 0.00),
(5, 'tokodonut', 'don@gmail.com', '0812937192', '$2y$12$WfP286ZEoQr6slmwzX/XFOmuy1EaTEniiP6GjDMxEJxkodJc49eze', 'assets/default_profile.png', '2025-12-20 01:52:36', '2025-12-20 01:52:36', 'partner', NULL, NULL, NULL, NULL, 'dunutlejut', 0.00),
(6, 'kamzzz', 'kam@gmail.com', '08129', '$2y$12$XzzEjW/gPa7VyOPrjO.lUOR6a2jn33O/Ee.h/KlA1UWv0ueXXabQa', 'assets/default_profile.png', '2025-12-20 02:06:57', '2025-12-20 02:06:57', 'customer', NULL, NULL, NULL, NULL, NULL, 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_users` (`user_id`),
  ADD KEY `fk_cart_products` (`product_id`);

--
-- Indexes for table `communities`
--
ALTER TABLE `communities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_donations_users` (`user_id`),
  ADD KEY `fk_donations_communities` (`community_id`);

--
-- Indexes for table `donation_details`
--
ALTER TABLE `donation_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_details_donations` (`donation_id`),
  ADD KEY `fk_details_food_items` (`food_item_id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `impact_trackings`
--
ALTER TABLE `impact_trackings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_impact_donations` (`donation_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_users_new` (`user_id`),
  ADD KEY `idx_store_status` (`store_id`,`status`),
  ADD KEY `idx_status_created` (`status`,`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `communities`
--
ALTER TABLE `communities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_details`
--
ALTER TABLE `donation_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `impact_trackings`
--
ALTER TABLE `impact_trackings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `fk_donations_communities` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_donations_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donation_details`
--
ALTER TABLE `donation_details`
  ADD CONSTRAINT `fk_details_donations` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_details_food_items` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `impact_trackings`
--
ALTER TABLE `impact_trackings`
  ADD CONSTRAINT `fk_impact_donations` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users_new` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
