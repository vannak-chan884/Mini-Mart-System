-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 04, 2026 at 12:13 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `minimart`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Water', '2026-03-03 23:14:02', '2026-03-03 23:14:02'),
(2, 'Drink', '2026-03-03 23:14:11', '2026-03-03 23:14:11');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_02_021759_add_role_to_users_table', 1),
(5, '2026_03_02_023535_create_categories_table', 1),
(6, '2026_03_02_024343_create_products_table', 1),
(7, '2026_03_02_073737_add_image_to_products_table', 1),
(8, '2026_03_02_074721_create_stock_histories_table', 1),
(9, '2026_03_03_031051_create_sale_items_table', 1),
(10, '2026_03_03_031055_create_sales_table', 1),
(11, '2026_03_03_075032_add_payment_fields_to_sales_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED NOT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `low_stock_alert` int NOT NULL DEFAULT '5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_category_id_foreign` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `barcode`, `name`, `image`, `cost_price`, `sell_price`, `stock`, `low_stock_alert`, `created_at`, `updated_at`) VALUES
(1, 1, '001', 'Hi-Tech drinking water 1.5L', 'products/Jr7kXtiWIOtA11oF1MrU9ZdfDDOWk6r0c9eHKBpT.png', 0.01, 0.02, 3, 5, '2026-03-03 23:14:50', '2026-03-04 01:47:18'),
(2, 1, '002', 'Vital Premium Water 1.5L', 'products/LnvMInTsnYr0Z1EVbTPuzIRzsqmSWSIYuQNSQ62K.jpg', 0.01, 0.02, 0, 5, '2026-03-03 23:15:44', '2026-03-04 03:58:18'),
(3, 2, '003', 'CAMBODIA Cola', 'products/qdeCWObYNRAUcqmUtddiMlL1w1WNf5yfpWa7HsHS.jpg', 0.01, 0.02, 4, 5, '2026-03-03 23:16:18', '2026-03-04 03:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `invoice_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `change_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `bakong_hash` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoice_no_unique` (`invoice_no`),
  KEY `sales_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `invoice_no`, `total_amount`, `paid_amount`, `change_amount`, `payment_method`, `bakong_hash`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-20260304-0001', 0.06, 1.00, 0.94, 'cash', NULL, '2026-03-03 23:59:20', '2026-03-03 23:59:20'),
(2, 1, 'INV-20260304-0002', 0.06, 0.06, 0.00, 'khqr_khr', '4e6b0f598da0d4655f4e2ec5947ac59aa709445cfcf40d8962d1d107f2b66b80', '2026-03-04 00:00:29', '2026-03-04 00:00:29'),
(3, 1, 'INV-20260304-0003', 0.04, 0.50, 0.46, 'cash', NULL, '2026-03-04 00:24:17', '2026-03-04 00:24:17'),
(4, 1, 'INV-20260304-0004', 0.06, 0.06, 0.00, 'khqr_usd', '3f61d9d456086dc922bcccf5d897e61b153fcdc80c9f9b000514cfe542eb8f68', '2026-03-04 00:38:12', '2026-03-04 00:38:12'),
(5, 1, 'INV-20260304-0005', 0.02, 0.20, 0.18, 'cash', NULL, '2026-03-04 01:38:06', '2026-03-04 01:38:06'),
(6, 1, 'INV-20260304-0006', 0.04, 0.10, 0.06, 'cash', NULL, '2026-03-04 01:47:18', '2026-03-04 01:47:18'),
(7, 1, 'INV-20260304-0007', 0.08, 0.10, 0.02, 'cash', NULL, '2026-03-04 01:48:02', '2026-03-04 01:48:02'),
(8, 1, 'INV-20260304-0008', 0.04, 0.04, 0.00, 'khqr_khr', 'a91ec0553c7b5290d1828a1116da96e38b9281d1a63eed2c5bd307595b05d07a', '2026-03-04 03:58:18', '2026-03-04 03:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, 0.02, 0.02, '2026-03-03 23:59:20', '2026-03-03 23:59:20'),
(2, 1, 2, 1, 0.02, 0.02, '2026-03-03 23:59:20', '2026-03-03 23:59:20'),
(3, 1, 1, 1, 0.02, 0.02, '2026-03-03 23:59:20', '2026-03-03 23:59:20'),
(4, 2, 3, 1, 0.02, 0.02, '2026-03-04 00:00:29', '2026-03-04 00:00:29'),
(5, 2, 2, 1, 0.02, 0.02, '2026-03-04 00:00:29', '2026-03-04 00:00:29'),
(6, 2, 1, 1, 0.02, 0.02, '2026-03-04 00:00:29', '2026-03-04 00:00:29'),
(7, 3, 3, 1, 0.02, 0.02, '2026-03-04 00:24:17', '2026-03-04 00:24:17'),
(8, 3, 2, 1, 0.02, 0.02, '2026-03-04 00:24:17', '2026-03-04 00:24:17'),
(9, 4, 1, 1, 0.02, 0.02, '2026-03-04 00:38:12', '2026-03-04 00:38:12'),
(10, 4, 2, 1, 0.02, 0.02, '2026-03-04 00:38:12', '2026-03-04 00:38:12'),
(11, 4, 3, 1, 0.02, 0.02, '2026-03-04 00:38:12', '2026-03-04 00:38:12'),
(12, 5, 1, 1, 0.02, 0.02, '2026-03-04 01:38:06', '2026-03-04 01:38:06'),
(13, 6, 1, 1, 0.02, 0.02, '2026-03-04 01:47:18', '2026-03-04 01:47:18'),
(14, 6, 2, 1, 0.02, 0.02, '2026-03-04 01:47:18', '2026-03-04 01:47:18'),
(15, 7, 3, 4, 0.02, 0.08, '2026-03-04 01:48:02', '2026-03-04 01:48:02'),
(16, 8, 3, 1, 0.02, 0.02, '2026-03-04 03:58:18', '2026-03-04 03:58:18'),
(17, 8, 2, 1, 0.02, 0.02, '2026-03-04 03:58:18', '2026-03-04 03:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('IRnM9QopdxPCKAY41takZHsLlhLesj9CPNRJkdtp', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYnc4YUlrVWR2RFJXeWEzek4zeXhBTHlLZVpXamozUEJYajM5RnlidyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1772625877),
('Ei4E8FF9UrSqClrvxfE3SjBcswMifO3OOVQMZCVa', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZXdVWDV2TXBOb3M0ZEhvMXFteTBaNUZEaXFqWkNZYm1veHBHQmgzUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1772625912);

-- --------------------------------------------------------

--
-- Table structure for table `stock_histories`
--

DROP TABLE IF EXISTS `stock_histories`;
CREATE TABLE IF NOT EXISTS `stock_histories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_histories_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_histories`
--

INSERT INTO `stock_histories` (`id`, `product_id`, `quantity`, `type`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 11, 'in', 'Initial stock', '2026-03-03 23:14:50', '2026-03-03 23:14:50'),
(2, 2, 13, 'in', 'Initial stock', '2026-03-03 23:15:44', '2026-03-03 23:15:44'),
(3, 3, 14, 'in', 'Initial stock', '2026-03-03 23:16:18', '2026-03-03 23:16:18'),
(4, 2, 1, 'out', 'Sold via POS (KHQR_USD)', '2026-03-03 23:36:44', '2026-03-03 23:36:44'),
(5, 3, 1, 'out', 'Sold via POS (Cash)', '2026-03-03 23:37:36', '2026-03-03 23:37:36'),
(6, 1, 1, 'out', 'Sold via POS (KHQR_KHR)', '2026-03-03 23:38:08', '2026-03-03 23:38:08'),
(7, 1, 1, 'out', 'Sold via POS (KHQR_USD)', '2026-03-03 23:40:49', '2026-03-03 23:40:49'),
(8, 2, 1, 'out', 'Sold via POS (KHQR_USD)', '2026-03-03 23:42:41', '2026-03-03 23:42:41'),
(9, 1, 1, 'out', 'Sold via POS (KHQR_USD)', '2026-03-03 23:42:41', '2026-03-03 23:42:41'),
(10, 2, 5, 'out', 'Sold via POS (KHQR_KHR)', '2026-03-03 23:46:30', '2026-03-03 23:46:30'),
(11, 3, 1, 'out', 'Sold via POS (Cash)', '2026-03-03 23:59:20', '2026-03-03 23:59:20'),
(12, 2, 1, 'out', 'Sold via POS (Cash)', '2026-03-03 23:59:20', '2026-03-03 23:59:20'),
(13, 1, 1, 'out', 'Sold via POS (Cash)', '2026-03-03 23:59:20', '2026-03-03 23:59:20'),
(14, 3, 1, 'out', 'Sold via POS (KHQR_KHR)', '2026-03-04 00:00:29', '2026-03-04 00:00:29'),
(15, 2, 1, 'out', 'Sold via POS (KHQR_KHR)', '2026-03-04 00:00:29', '2026-03-04 00:00:29'),
(16, 1, 1, 'out', 'Sold via POS (KHQR_KHR)', '2026-03-04 00:00:29', '2026-03-04 00:00:29'),
(17, 3, 1, 'out', 'Sold via POS (Cash)', '2026-03-04 00:24:17', '2026-03-04 00:24:17'),
(18, 2, 1, 'out', 'Sold via POS (Cash)', '2026-03-04 00:24:17', '2026-03-04 00:24:17'),
(19, 1, 1, 'out', 'Sold via POS (KHQR_USD)', '2026-03-04 00:38:12', '2026-03-04 00:38:12'),
(20, 2, 1, 'out', 'Sold via POS (KHQR_USD)', '2026-03-04 00:38:12', '2026-03-04 00:38:12'),
(21, 3, 1, 'out', 'Sold via POS (KHQR_USD)', '2026-03-04 00:38:12', '2026-03-04 00:38:12'),
(22, 1, 1, 'out', 'Sold via POS (Cash)', '2026-03-04 01:38:06', '2026-03-04 01:38:06'),
(23, 1, 1, 'out', 'Sold via POS (Cash)', '2026-03-04 01:47:18', '2026-03-04 01:47:18'),
(24, 2, 1, 'out', 'Sold via POS (Cash)', '2026-03-04 01:47:18', '2026-03-04 01:47:18'),
(25, 3, 4, 'out', 'Sold via POS (Cash)', '2026-03-04 01:48:02', '2026-03-04 01:48:02'),
(26, 3, 1, 'out', 'Sold via POS (KHQR_KHR)', '2026-03-04 03:58:18', '2026-03-04 03:58:18'),
(27, 2, 1, 'out', 'Sold via POS (KHQR_KHR)', '2026-03-04 03:58:18', '2026-03-04 03:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cashier',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Vannak', 'vannakchan884@gmail.com', NULL, '$2y$12$Bociw6XQdvcbTbRmSwaH..TBzBH9zpBV45jMGajYJpEGe.IBWFJ8u', 'N0CT5qQhc2kiyy6ZQvo2X4G6hwlsGHydGna2jwOiBRss1SL8q0Q5E17UaYKK', 'admin', '2026-03-03 23:13:39', '2026-03-03 23:13:50'),
(2, 'Khin Sreynoy', 'khinsreynoy547@gmail.com', NULL, '$2y$12$9FwbPJILWOAkVdrUrvOqF.DHlSqTq.EDptN4222UkajYgzsr5CSrO', 'DQoHxjdRZtnNTdaNXn9MrNOeo6lEtFsoE1Osp15thdKhv0GHUgWi0HxJnBij', 'cashier', '2026-03-04 04:20:30', '2026-03-04 05:02:31');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
