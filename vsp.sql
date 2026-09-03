-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 03, 2026 at 06:01 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vsp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `login` tinyint NOT NULL DEFAULT '1' COMMENT '1: Allowed, 0: Blocked',
  `is_super_admin` tinyint NOT NULL DEFAULT '0',
  `email_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `admins`
--

TRUNCATE TABLE `admins`;
--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `mobile_number`, `avatar`, `status`, `login`, `is_super_admin`, `email_notifications`, `remember_token`, `last_login_at`, `last_login_ip`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super', 'Admin', 'superadmin', 'admin@vsp.local', '$2y$12$OavsHbe3g43LWN3NadceO.CEmHXtkVspNfjTpOe99n3eS5ZLCx5Ty', NULL, NULL, 1, 1, 1, 1, NULL, '2026-09-02 23:19:55', '127.0.0.1', '2026-08-27 01:42:08', '2026-09-02 23:19:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  `admin_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_admin_id_foreign` (`admin_id`),
  KEY `audit_subject_index` (`subject_type`,`subject_id`),
  KEY `audit_module_action_index` (`module`,`action`),
  KEY `audit_created_at_index` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `audit_logs`
--

TRUNCATE TABLE `audit_logs`;
--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `admin_id`, `admin_name`, `action`, `module`, `subject_type`, `subject_id`, `description`, `old_values`, `new_values`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-08-27 01:42:18', '2026-08-27 01:42:18'),
(2, 1, 'Super Admin', 'logout', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged out', NULL, NULL, '127.0.0.1', '2026-08-27 01:42:37', '2026-08-27 01:42:37'),
(3, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-09-01 02:53:12', '2026-09-01 02:53:12'),
(4, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-09-01 03:27:02', '2026-09-01 03:27:02'),
(5, 1, 'Super Admin', 'import', 'Spare Parts', 'App\\Models\\ImportLog', 1, 'Imported \"SA-01074--SALE--PAVANAUTOPARTS.pdf\": 39 spare part(s) created, 0 restocked, 0 skipped', NULL, NULL, '127.0.0.1', '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(6, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-09-01 05:59:09', '2026-09-01 05:59:09'),
(7, 1, 'Super Admin', 'logout', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged out', NULL, NULL, '127.0.0.1', '2026-09-01 07:36:44', '2026-09-01 07:36:44'),
(8, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-09-01 22:45:39', '2026-09-01 22:45:39'),
(9, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-09-01 22:51:34', '2026-09-01 22:51:34'),
(10, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-09-02 22:37:57', '2026-09-02 22:37:57'),
(11, 1, 'Super Admin', 'import', 'Spare Parts', 'App\\Models\\ImportLog', 2, 'Imported \"SA-01074--SALE--PAVANAUTOPARTS.pdf\": 33 spare part(s) created, 6 restocked, 0 skipped', NULL, NULL, '127.0.0.1', '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(12, 1, 'Super Admin', 'delete', 'Spare Parts', 'App\\Models\\SparePart', 72, 'Deleted spare part \"FLOOR-ACTIVA 6G (N/M) (W.RED) ACTIVA 6G AP GO\" (APFL-89-7)', NULL, NULL, '127.0.0.1', '2026-09-02 22:52:07', '2026-09-02 22:52:07'),
(13, 1, 'Super Admin', 'delete', 'Spare Parts', 'App\\Models\\SparePart', 71, 'Deleted spare part \"FRONT MUJGARD — ACTIVA 5G (BROWN) ACTIVA 5G AP GO\" (APFM-89-27)', NULL, NULL, '127.0.0.1', '2026-09-02 22:52:35', '2026-09-02 22:52:35'),
(14, 1, 'Super Admin', 'login', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged in', NULL, NULL, '127.0.0.1', '2026-09-02 23:19:55', '2026-09-02 23:19:55'),
(15, 1, 'Super Admin', 'create', 'Menus', 'App\\Models\\Menu', 44, 'Created menu \"System\"', NULL, NULL, '127.0.0.1', '2026-09-02 23:34:38', '2026-09-02 23:34:38'),
(16, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 43, 'Updated menu \"Settings\"', '{\"name\": \"Settings\", \"status\": true, \"parent_id\": null, \"sort_order\": 430}', '{\"name\": \"Settings\", \"status\": true, \"parent_id\": \"3\", \"sort_order\": 430}', '127.0.0.1', '2026-09-02 23:35:18', '2026-09-02 23:35:18'),
(17, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 43, 'Updated menu \"Settings\"', '{\"name\": \"Settings\", \"status\": true, \"parent_id\": 3, \"sort_order\": 430}', '{\"name\": \"Settings\", \"status\": true, \"parent_id\": null, \"sort_order\": 430}', '127.0.0.1', '2026-09-02 23:35:36', '2026-09-02 23:35:36'),
(18, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 19, 'Updated menu \"Suppliers\"', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": null, \"sort_order\": 190}', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": \"22\", \"sort_order\": 190}', '127.0.0.1', '2026-09-02 23:45:05', '2026-09-02 23:45:05'),
(19, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 19, 'Updated menu \"Suppliers\"', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": 22, \"sort_order\": 190}', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": null, \"sort_order\": 190}', '127.0.0.1', '2026-09-02 23:46:05', '2026-09-02 23:46:05'),
(20, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 43, 'Updated menu \"Settings\"', '{\"name\": \"Settings\", \"status\": true, \"parent_id\": null, \"sort_order\": 430}', '{\"name\": \"Settings\", \"status\": true, \"parent_id\": \"38\", \"sort_order\": 430}', '127.0.0.1', '2026-09-03 00:06:55', '2026-09-03 00:06:55'),
(21, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 39, 'Updated menu \"Users & Permissions\"', '{\"name\": \"Users & Permissions\", \"status\": true, \"parent_id\": null, \"sort_order\": 390}', '{\"name\": \"Users & Permissions\", \"status\": true, \"parent_id\": \"38\", \"sort_order\": 390}', '127.0.0.1', '2026-09-03 00:12:37', '2026-09-03 00:12:37'),
(22, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 40, 'Updated menu \"Menu Management\"', '{\"name\": \"Menu Management\", \"status\": true, \"parent_id\": null, \"sort_order\": 400}', '{\"name\": \"Menu Management\", \"status\": true, \"parent_id\": \"38\", \"sort_order\": 400}', '127.0.0.1', '2026-09-03 00:12:50', '2026-09-03 00:12:50'),
(23, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 41, 'Updated menu \"Notifications\"', '{\"name\": \"Notifications\", \"status\": true, \"parent_id\": null, \"sort_order\": 410}', '{\"name\": \"Notifications\", \"status\": true, \"parent_id\": \"38\", \"sort_order\": 410}', '127.0.0.1', '2026-09-03 00:13:00', '2026-09-03 00:13:00'),
(24, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 42, 'Updated menu \"Audit Logs\"', '{\"name\": \"Audit Logs\", \"status\": true, \"parent_id\": null, \"sort_order\": 420}', '{\"name\": \"Audit Logs\", \"status\": true, \"parent_id\": \"38\", \"sort_order\": 420}', '127.0.0.1', '2026-09-03 00:13:10', '2026-09-03 00:13:10'),
(25, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 31, 'Updated menu \"Stock Valuation\"', '{\"name\": \"Stock Valuation\", \"status\": true, \"parent_id\": null, \"sort_order\": 310}', '{\"name\": \"Stock Valuation\", \"status\": true, \"parent_id\": \"30\", \"sort_order\": 310}', '127.0.0.1', '2026-09-03 00:13:32', '2026-09-03 00:13:32'),
(26, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 32, 'Updated menu \"Purchase Reports\"', '{\"name\": \"Purchase Reports\", \"status\": true, \"parent_id\": null, \"sort_order\": 320}', '{\"name\": \"Purchase Reports\", \"status\": true, \"parent_id\": \"30\", \"sort_order\": 320}', '127.0.0.1', '2026-09-03 00:13:44', '2026-09-03 00:13:44'),
(27, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 33, 'Updated menu \"Sales Reports\"', '{\"name\": \"Sales Reports\", \"status\": true, \"parent_id\": null, \"sort_order\": 330}', '{\"name\": \"Sales Reports\", \"status\": true, \"parent_id\": \"30\", \"sort_order\": 330}', '127.0.0.1', '2026-09-03 00:13:59', '2026-09-03 00:13:59'),
(28, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 34, 'Updated menu \"Profit Reports\"', '{\"name\": \"Profit Reports\", \"status\": true, \"parent_id\": null, \"sort_order\": 340}', '{\"name\": \"Profit Reports\", \"status\": true, \"parent_id\": \"30\", \"sort_order\": 340}', '127.0.0.1', '2026-09-03 00:14:15', '2026-09-03 00:14:15'),
(29, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 35, 'Updated menu \"Supplier Outstanding\"', '{\"name\": \"Supplier Outstanding\", \"status\": true, \"parent_id\": null, \"sort_order\": 350}', '{\"name\": \"Supplier Outstanding\", \"status\": true, \"parent_id\": \"30\", \"sort_order\": 350}', '127.0.0.1', '2026-09-03 00:14:27', '2026-09-03 00:14:27'),
(30, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 37, 'Updated menu \"Expenses\"', '{\"name\": \"Expenses\", \"status\": true, \"parent_id\": null, \"sort_order\": 370}', '{\"name\": \"Expenses\", \"status\": true, \"parent_id\": \"30\", \"sort_order\": 370}', '127.0.0.1', '2026-09-03 00:14:39', '2026-09-03 00:14:39'),
(31, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 36, 'Updated menu \"Customer Outstanding\"', '{\"name\": \"Customer Outstanding\", \"status\": true, \"parent_id\": null, \"sort_order\": 360}', '{\"name\": \"Customer Outstanding\", \"status\": true, \"parent_id\": \"30\", \"sort_order\": 360}', '127.0.0.1', '2026-09-03 00:14:51', '2026-09-03 00:14:51'),
(32, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 25, 'Updated menu \"Customers\"', '{\"name\": \"Customers\", \"status\": true, \"parent_id\": null, \"sort_order\": 250}', '{\"name\": \"Customers\", \"status\": true, \"parent_id\": \"24\", \"sort_order\": 250}', '127.0.0.1', '2026-09-03 00:16:06', '2026-09-03 00:16:06'),
(33, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 26, 'Updated menu \"Sales\"', '{\"name\": \"Sales\", \"status\": true, \"parent_id\": null, \"sort_order\": 260}', '{\"name\": \"Sales\", \"status\": true, \"parent_id\": \"24\", \"sort_order\": 260}', '127.0.0.1', '2026-09-03 00:16:29', '2026-09-03 00:16:29'),
(34, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 27, 'Updated menu \"Sales Returns\"', '{\"name\": \"Sales Returns\", \"status\": true, \"parent_id\": null, \"sort_order\": 270}', '{\"name\": \"Sales Returns\", \"status\": true, \"parent_id\": \"24\", \"sort_order\": 270}', '127.0.0.1', '2026-09-03 00:16:40', '2026-09-03 00:16:40'),
(35, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 19, 'Updated menu \"Suppliers\"', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": null, \"sort_order\": 190}', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": \"22\", \"sort_order\": 190}', '127.0.0.1', '2026-09-03 00:16:56', '2026-09-03 00:16:56'),
(36, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 19, 'Updated menu \"Suppliers\"', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": 22, \"sort_order\": 190}', '{\"name\": \"Suppliers\", \"status\": true, \"parent_id\": \"18\", \"sort_order\": 190}', '127.0.0.1', '2026-09-03 00:17:16', '2026-09-03 00:17:16'),
(37, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 20, 'Updated menu \"Purchase Orders\"', '{\"name\": \"Purchase Orders\", \"status\": true, \"parent_id\": null, \"sort_order\": 200}', '{\"name\": \"Purchase Orders\", \"status\": true, \"parent_id\": \"18\", \"sort_order\": 200}', '127.0.0.1', '2026-09-03 00:17:32', '2026-09-03 00:17:32'),
(38, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 21, 'Updated menu \"Goods Receipt\"', '{\"name\": \"Goods Receipt\", \"status\": true, \"parent_id\": null, \"sort_order\": 210}', '{\"name\": \"Goods Receipt\", \"status\": true, \"parent_id\": \"18\", \"sort_order\": 210}', '127.0.0.1', '2026-09-03 00:17:44', '2026-09-03 00:17:44'),
(39, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 22, 'Updated menu \"Purchases\"', '{\"name\": \"Purchases\", \"status\": true, \"parent_id\": null, \"sort_order\": 220}', '{\"name\": \"Purchases\", \"status\": true, \"parent_id\": \"18\", \"sort_order\": 220}', '127.0.0.1', '2026-09-03 00:17:59', '2026-09-03 00:17:59'),
(40, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 23, 'Updated menu \"Purchase Returns\"', '{\"name\": \"Purchase Returns\", \"status\": true, \"parent_id\": null, \"sort_order\": 230}', '{\"name\": \"Purchase Returns\", \"status\": true, \"parent_id\": \"18\", \"sort_order\": 230}', '127.0.0.1', '2026-09-03 00:18:14', '2026-09-03 00:18:14'),
(41, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 10, 'Updated menu \"Current Stock\"', '{\"name\": \"Current Stock\", \"status\": true, \"parent_id\": null, \"sort_order\": 100}', '{\"name\": \"Current Stock\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 100}', '127.0.0.1', '2026-09-03 00:18:33', '2026-09-03 00:18:33'),
(42, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 11, 'Updated menu \"Stock Movement\"', '{\"name\": \"Stock Movement\", \"status\": true, \"parent_id\": null, \"sort_order\": 110}', '{\"name\": \"Stock Movement\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 110}', '127.0.0.1', '2026-09-03 00:18:42', '2026-09-03 00:18:42'),
(43, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 12, 'Updated menu \"Stock Adjustment\"', '{\"name\": \"Stock Adjustment\", \"status\": true, \"parent_id\": null, \"sort_order\": 120}', '{\"name\": \"Stock Adjustment\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 120}', '127.0.0.1', '2026-09-03 00:18:59', '2026-09-03 00:18:59'),
(44, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 13, 'Updated menu \"Stock Transfer\"', '{\"name\": \"Stock Transfer\", \"status\": true, \"parent_id\": null, \"sort_order\": 130}', '{\"name\": \"Stock Transfer\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 130}', '127.0.0.1', '2026-09-03 00:19:09', '2026-09-03 00:19:09'),
(45, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 14, 'Updated menu \"Stock Take\"', '{\"name\": \"Stock Take\", \"status\": true, \"parent_id\": null, \"sort_order\": 140}', '{\"name\": \"Stock Take\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 140}', '127.0.0.1', '2026-09-03 00:19:18', '2026-09-03 00:19:18'),
(46, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 15, 'Updated menu \"Low Stock\"', '{\"name\": \"Low Stock\", \"status\": true, \"parent_id\": null, \"sort_order\": 150}', '{\"name\": \"Low Stock\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 150}', '127.0.0.1', '2026-09-03 00:19:27', '2026-09-03 00:19:27'),
(47, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 16, 'Updated menu \"Reorder Suggestions\"', '{\"name\": \"Reorder Suggestions\", \"status\": true, \"parent_id\": null, \"sort_order\": 160}', '{\"name\": \"Reorder Suggestions\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 160}', '127.0.0.1', '2026-09-03 00:19:35', '2026-09-03 00:19:35'),
(48, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 17, 'Updated menu \"Damaged Stock\"', '{\"name\": \"Damaged Stock\", \"status\": true, \"parent_id\": null, \"sort_order\": 170}', '{\"name\": \"Damaged Stock\", \"status\": true, \"parent_id\": \"9\", \"sort_order\": 170}', '127.0.0.1', '2026-09-03 00:19:44', '2026-09-03 00:19:44'),
(49, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 3, 'Updated menu \"Spare Parts\"', '{\"name\": \"Spare Parts\", \"status\": true, \"parent_id\": null, \"sort_order\": 30}', '{\"name\": \"Spare Parts\", \"status\": true, \"parent_id\": \"2\", \"sort_order\": 30}', '127.0.0.1', '2026-09-03 00:19:59', '2026-09-03 00:19:59'),
(50, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 4, 'Updated menu \"Categories\"', '{\"name\": \"Categories\", \"status\": true, \"parent_id\": null, \"sort_order\": 40}', '{\"name\": \"Categories\", \"status\": true, \"parent_id\": \"2\", \"sort_order\": 40}', '127.0.0.1', '2026-09-03 00:20:06', '2026-09-03 00:20:06'),
(51, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 5, 'Updated menu \"Brands\"', '{\"name\": \"Brands\", \"status\": true, \"parent_id\": null, \"sort_order\": 50}', '{\"name\": \"Brands\", \"status\": true, \"parent_id\": \"2\", \"sort_order\": 50}', '127.0.0.1', '2026-09-03 00:20:14', '2026-09-03 00:20:14'),
(52, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 6, 'Updated menu \"Manufacturers\"', '{\"name\": \"Manufacturers\", \"status\": true, \"parent_id\": null, \"sort_order\": 60}', '{\"name\": \"Manufacturers\", \"status\": true, \"parent_id\": \"2\", \"sort_order\": 60}', '127.0.0.1', '2026-09-03 00:20:25', '2026-09-03 00:20:25'),
(53, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 7, 'Updated menu \"Units\"', '{\"name\": \"Units\", \"status\": true, \"parent_id\": null, \"sort_order\": 70}', '{\"name\": \"Units\", \"status\": true, \"parent_id\": \"2\", \"sort_order\": 70}', '127.0.0.1', '2026-09-03 00:20:33', '2026-09-03 00:20:33'),
(54, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 8, 'Updated menu \"Vehicle Management\"', '{\"name\": \"Vehicle Management\", \"status\": true, \"parent_id\": null, \"sort_order\": 80}', '{\"name\": \"Vehicle Management\", \"status\": true, \"parent_id\": \"2\", \"sort_order\": 80}', '127.0.0.1', '2026-09-03 00:20:41', '2026-09-03 00:20:41'),
(55, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 28, 'Updated menu \"Warehouses Management\"', '{\"name\": \"Warehouses\", \"status\": false, \"parent_id\": null, \"sort_order\": 280}', '{\"name\": \"Warehouses Management\", \"status\": true, \"parent_id\": null, \"sort_order\": 280}', '127.0.0.1', '2026-09-03 00:21:20', '2026-09-03 00:21:20'),
(56, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 29, 'Updated menu \"Warehouses\"', '{\"name\": \"Warehouses\", \"status\": true, \"parent_id\": null, \"sort_order\": 290}', '{\"name\": \"Warehouses\", \"status\": true, \"parent_id\": \"28\", \"sort_order\": 290}', '127.0.0.1', '2026-09-03 00:21:36', '2026-09-03 00:21:36'),
(57, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 24, 'Updated menu \"Sales Management\"', '{\"name\": \"Sales\", \"status\": true, \"parent_id\": null, \"sort_order\": 240}', '{\"name\": \"Sales Management\", \"status\": true, \"parent_id\": null, \"sort_order\": 240}', '127.0.0.1', '2026-09-03 00:21:54', '2026-09-03 00:21:54'),
(58, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 18, 'Updated menu \"Purchases Management\"', '{\"name\": \"Purchases\", \"status\": true, \"parent_id\": null, \"sort_order\": 180}', '{\"name\": \"Purchases Management\", \"status\": true, \"parent_id\": null, \"sort_order\": 180}', '127.0.0.1', '2026-09-03 00:22:09', '2026-09-03 00:22:09'),
(59, 1, 'Super Admin', 'update', 'Menus', 'App\\Models\\Menu', 9, 'Updated menu \"Inventory Management\"', '{\"name\": \"Inventory\", \"status\": true, \"parent_id\": null, \"sort_order\": 90}', '{\"name\": \"Inventory Management\", \"status\": true, \"parent_id\": null, \"sort_order\": 90}', '127.0.0.1', '2026-09-03 00:22:20', '2026-09-03 00:22:20'),
(60, 1, 'Super Admin', 'logout', 'Auth', 'App\\Models\\Admin', 1, 'superadmin logged out', NULL, NULL, '127.0.0.1', '2026-09-03 00:29:03', '2026-09-03 00:29:03');

-- --------------------------------------------------------

--
-- Table structure for table `bins`
--

DROP TABLE IF EXISTS `bins`;
CREATE TABLE IF NOT EXISTS `bins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `shelf_id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bins_shelf_id_foreign` (`shelf_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `bins`
--

TRUNCATE TABLE `bins`;
-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_name_unique` (`name`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `brands`
--

TRUNCATE TABLE `brands`;
-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `cache`
--

TRUNCATE TABLE `cache`;
--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:69:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:10:\"admin.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:12:\"admin.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:10:\"admin.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"admin.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:9:\"role.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:11:\"role.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:9:\"role.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:11:\"role.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:13:\"settings.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:13:\"settings.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"audit-log.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"spare-part.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"spare-part.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:15:\"spare-part.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:17:\"spare-part.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:17:\"spare-part.import\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:17:\"spare-part.export\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:12:\"vehicle.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:14:\"vehicle.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:12:\"vehicle.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:14:\"vehicle.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:13:\"supplier.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:6;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:15:\"supplier.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:6;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:13:\"supplier.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:15:\"supplier.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:13:\"customer.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:15:\"customer.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:13:\"customer.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:15:\"customer.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:19:\"purchase-order.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:21:\"purchase-order.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:19:\"purchase-order.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:21:\"purchase-order.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:22:\"purchase-order.approve\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:13:\"purchase.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:6;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:15:\"purchase.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:6;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:13:\"purchase.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:15:\"purchase.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:20:\"purchase-return.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:22:\"purchase-return.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:20:\"purchase-return.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:22:\"purchase-return.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:23:\"purchase-return.approve\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:9:\"sale.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:11:\"sale.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:9:\"sale.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:11:\"sale.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:16:\"sale-return.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:18:\"sale-return.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:16:\"sale-return.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:18:\"sale-return.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:19:\"sale-return.approve\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:10:\"stock.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:21:\"stock-adjustment.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:23:\"stock-adjustment.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:24:\"stock-adjustment.approve\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:19:\"stock-transfer.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:21:\"stock-transfer.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:22:\"stock-transfer.approve\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:14:\"warehouse.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:16:\"warehouse.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:14:\"warehouse.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:16:\"warehouse.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:11:\"report.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:12:\"expense.view\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:14:\"expense.create\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:12:\"expense.edit\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:14:\"expense.delete\";s:1:\"c\";s:5:\"admin\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:6:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:5:\"admin\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:5:\"admin\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:17:\"Inventory Manager\";s:1:\"c\";s:5:\"admin\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:16:\"Purchase Manager\";s:1:\"c\";s:5:\"admin\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:13:\"Sales Manager\";s:1:\"c\";s:5:\"admin\";}i:5;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:19:\"Data Entry Operator\";s:1:\"c\";s:5:\"admin\";}}}', 1788498775),
('laravel-cache-settings.inventory', 'a:0:{}', 1788415976),
('laravel-cache-settings.company', 'a:0:{}', 1788418715),
('laravel-cache-settings.invoice', 'a:0:{}', 1788418715),
('laravel-cache-settings.general', 'a:0:{}', 1788418715);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `cache_locks`
--

TRUNCATE TABLE `cache_locks`;
-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `categories`
--

TRUNCATE TABLE `categories`;
--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `icon`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Engine Parts', 'engine-parts-EPj7', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(2, NULL, 'Brake Parts', 'brake-parts-Y7Ka', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(3, NULL, 'Suspension Parts', 'suspension-parts-Wq8s', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(4, NULL, 'Electrical Parts', 'electrical-parts-A1A2', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(5, NULL, 'Body Parts', 'body-parts-G0Cc', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(6, NULL, 'Steering Parts', 'steering-parts-KmmT', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(7, NULL, 'Transmission Parts', 'transmission-parts-poi8', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(8, NULL, 'Cooling System', 'cooling-system-1BUj', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(9, NULL, 'AC Parts', 'ac-parts-ID3K', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(10, NULL, 'Filters', 'filters-TUX3', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(11, NULL, 'Accessories', 'accessories-xSR4', NULL, NULL, 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit_limit` decimal(12,2) DEFAULT NULL,
  `payment_terms` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_customer_code_unique` (`customer_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `customers`
--

TRUNCATE TABLE `customers`;
-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

DROP TABLE IF EXISTS `customer_payments`;
CREATE TABLE IF NOT EXISTS `customer_payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','card','cheque','online','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_payments_payment_number_unique` (`payment_number`),
  KEY `customer_payments_sale_id_foreign` (`sale_id`),
  KEY `customer_payments_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `customer_payments`
--

TRUNCATE TABLE `customer_payments`;
-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('Transport','Delivery','Warehouse','Salary','Electricity','Packaging','Maintenance','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','card','cheque','online','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `description` text COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  KEY `expenses_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `expenses`
--

TRUNCATE TABLE `expenses`;
-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `failed_jobs`
--

TRUNCATE TABLE `failed_jobs`;
-- --------------------------------------------------------

--
-- Table structure for table `goods_receipts`
--

DROP TABLE IF EXISTS `goods_receipts`;
CREATE TABLE IF NOT EXISTS `goods_receipts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `receiving_date` date NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `received_by` bigint UNSIGNED DEFAULT NULL,
  `supplier_invoice_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','confirmed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_receipts_grn_number_unique` (`grn_number`),
  KEY `goods_receipts_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `goods_receipts_supplier_id_foreign` (`supplier_id`),
  KEY `goods_receipts_warehouse_id_foreign` (`warehouse_id`),
  KEY `goods_receipts_received_by_foreign` (`received_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `goods_receipts`
--

TRUNCATE TABLE `goods_receipts`;
-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_items`
--

DROP TABLE IF EXISTS `goods_receipt_items`;
CREATE TABLE IF NOT EXISTS `goods_receipt_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_receipt_id` bigint UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint UNSIGNED DEFAULT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `quantity_ordered` int NOT NULL DEFAULT '0',
  `quantity_received` int NOT NULL,
  `quantity_damaged` int NOT NULL DEFAULT '0',
  `quantity_short` int NOT NULL DEFAULT '0',
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_receipt_items_goods_receipt_id_foreign` (`goods_receipt_id`),
  KEY `goods_receipt_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  KEY `goods_receipt_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `goods_receipt_items`
--

TRUNCATE TABLE `goods_receipt_items`;
-- --------------------------------------------------------

--
-- Table structure for table `import_logs`
--

DROP TABLE IF EXISTS `import_logs`;
CREATE TABLE IF NOT EXISTS `import_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'spare_parts',
  `source_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sheet',
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_rows` int UNSIGNED NOT NULL DEFAULT '0',
  `imported_count` int UNSIGNED NOT NULL DEFAULT '0',
  `restocked_count` int UNSIGNED NOT NULL DEFAULT '0',
  `skipped_count` int UNSIGNED NOT NULL DEFAULT '0',
  `errors` json DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_logs_created_by_foreign` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `import_logs`
--

TRUNCATE TABLE `import_logs`;
--
-- Dumping data for table `import_logs`
--

INSERT INTO `import_logs` (`id`, `type`, `source_type`, `original_filename`, `total_rows`, `imported_count`, `restocked_count`, `skipped_count`, `errors`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'spare_parts', 'pdf', 'SA-01074--SALE--PAVANAUTOPARTS.pdf', 39, 39, 0, 0, '[]', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(2, 'spare_parts', 'pdf', 'SA-01074--SALE--PAVANAUTOPARTS.pdf', 39, 33, 6, 0, '[]', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_imports`
--

DROP TABLE IF EXISTS `inventory_imports`;
CREATE TABLE IF NOT EXISTS `inventory_imports` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_type` enum('pdf','excel','csv') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf',
  `document_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `total_items` int UNSIGNED NOT NULL DEFAULT '0',
  `total_quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `calculated_total` decimal(15,2) DEFAULT NULL,
  `total_mismatch_amount` decimal(15,2) DEFAULT NULL,
  `extraction_method` enum('pdf_text','ocr','excel','csv') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extraction_status` enum('uploaded','processing','extracted','needs_review','ready','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'uploaded',
  `matching_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `import_status` enum('uploaded','processing','ready','posted','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'uploaded',
  `confidence_score` tinyint UNSIGNED DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_imports_uuid_unique` (`uuid`),
  UNIQUE KEY `inventory_imports_logical_unique` (`supplier_id`,`document_number`,`document_date`),
  KEY `inventory_imports_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_imports_uploaded_by_foreign` (`uploaded_by`),
  KEY `inventory_imports_approved_by_foreign` (`approved_by`),
  KEY `inventory_imports_document_number_index` (`document_number`),
  KEY `inventory_imports_document_date_index` (`document_date`),
  KEY `inventory_imports_extraction_status_index` (`extraction_status`),
  KEY `inventory_imports_approval_status_index` (`approval_status`),
  KEY `inventory_imports_import_status_index` (`import_status`),
  KEY `inventory_imports_file_hash_index` (`file_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `inventory_imports`
--

TRUNCATE TABLE `inventory_imports`;
-- --------------------------------------------------------

--
-- Table structure for table `inventory_import_items`
--

DROP TABLE IF EXISTS `inventory_import_items`;
CREATE TABLE IF NOT EXISTS `inventory_import_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `inventory_import_id` bigint UNSIGNED NOT NULL,
  `row_number` int UNSIGNED NOT NULL,
  `raw_part_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `normalized_part_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `normalized_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_vehicle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `normalized_vehicle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_color` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `normalized_color` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comp_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `rate` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_rate` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expected_amount` decimal(15,2) DEFAULT NULL,
  `amount_mismatch` tinyint(1) NOT NULL DEFAULT '0',
  `matched_product_id` bigint UNSIGNED DEFAULT NULL,
  `matched_variant_id` bigint UNSIGNED DEFAULT NULL,
  `match_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_confidence` tinyint UNSIGNED DEFAULT NULL,
  `validation_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `validation_message` text COLLATE utf8mb4_unicode_ci,
  `user_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `ignored` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_import_items_matched_product_id_foreign` (`matched_product_id`),
  KEY `inventory_import_items_matched_variant_id_foreign` (`matched_variant_id`),
  KEY `inventory_import_items_import_validation_index` (`inventory_import_id`,`validation_status`),
  KEY `inventory_import_items_normalized_part_no_index` (`normalized_part_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `inventory_import_items`
--

TRUNCATE TABLE `inventory_import_items`;
-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `jobs`
--

TRUNCATE TABLE `jobs`;
-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `job_batches`
--

TRUNCATE TABLE `job_batches`;
-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
CREATE TABLE IF NOT EXISTS `manufacturers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manufacturers_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `manufacturers`
--

TRUNCATE TABLE `manufacturers`;
-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('heading','link') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'link',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_pattern` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menus_parent_id_sort_order_index` (`parent_id`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `menus`
--

TRUNCATE TABLE `menus`;
--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `parent_id`, `type`, `name`, `icon`, `url`, `route_name`, `active_pattern`, `permission`, `sort_order`, `target`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'link', 'Dashboard', 'bi-speedometer2', NULL, 'admin.dashboard', NULL, 'dashboard.view', 10, '_self', 1, '2026-09-01 23:22:14', '2026-09-01 23:22:14'),
(2, NULL, 'heading', 'Master Data', NULL, NULL, NULL, NULL, NULL, 20, '_self', 1, '2026-09-01 23:22:14', '2026-09-01 23:22:14'),
(3, 2, 'link', 'Spare Parts', 'bi-gear-wide-connected', NULL, 'admin.spare-parts.index', 'admin.spare-parts.*', 'spare-part.view', 30, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:19:59'),
(4, 2, 'link', 'Categories', 'bi-tags', NULL, 'admin.categories.index', 'admin.categories.*', 'spare-part.view', 40, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:20:06'),
(5, 2, 'link', 'Brands', 'bi-award', NULL, 'admin.brands.index', 'admin.brands.*', 'spare-part.view', 50, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:20:14'),
(6, 2, 'link', 'Manufacturers', 'bi-building', NULL, 'admin.manufacturers.index', 'admin.manufacturers.*', 'spare-part.view', 60, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:20:25'),
(7, 2, 'link', 'Units', 'bi-rulers', NULL, 'admin.units.index', 'admin.units.*', 'spare-part.view', 70, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:20:33'),
(8, 2, 'link', 'Vehicle Management', 'bi-car-front', NULL, 'admin.vehicles.index', 'admin.vehicles.*', 'vehicle.view', 80, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:20:41'),
(9, NULL, 'heading', 'Inventory Management', NULL, NULL, NULL, NULL, NULL, 90, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:22:20'),
(10, 9, 'link', 'Current Stock', 'bi-box-seam', NULL, 'admin.stock.index', 'admin.stock.*', 'stock.view', 100, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:18:33'),
(11, 9, 'link', 'Stock Movement', 'bi-arrow-left-right', NULL, 'admin.stock.movements', 'admin.stock.*', 'stock.view', 110, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:18:42'),
(12, 9, 'link', 'Stock Adjustment', 'bi-sliders', NULL, 'admin.stock-adjustments.index', 'admin.stock-adjustments.*', 'stock-adjustment.view', 120, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:18:59'),
(13, 9, 'link', 'Stock Transfer', 'bi-truck', NULL, 'admin.stock-transfers.index', 'admin.stock-transfers.*', 'stock-transfer.view', 130, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:19:09'),
(14, 9, 'link', 'Stock Take', 'bi-clipboard-check', NULL, 'admin.stock-takes.index', 'admin.stock-takes.*', 'stock.view', 140, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:19:18'),
(15, 9, 'link', 'Low Stock', 'bi-exclamation-triangle', NULL, 'admin.stock.low', 'admin.stock.*', 'stock.view', 150, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:19:27'),
(16, 9, 'link', 'Reorder Suggestions', 'bi-arrow-repeat', NULL, 'admin.stock.reorder-suggestions', 'admin.stock.*', 'stock.view', 160, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:19:35'),
(17, 9, 'link', 'Damaged Stock', 'bi-x-octagon', NULL, 'admin.stock.damaged', 'admin.stock.*', 'stock.view', 170, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:19:44'),
(18, NULL, 'heading', 'Purchases Management', NULL, NULL, NULL, NULL, NULL, 180, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:22:09'),
(19, 18, 'link', 'Suppliers', 'bi-people', NULL, 'admin.suppliers.index', 'admin.suppliers.*', 'supplier.view', 190, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:17:16'),
(20, 18, 'link', 'Purchase Orders', 'bi-file-earmark-text', NULL, 'admin.purchase-orders.index', 'admin.purchase-orders.*', 'purchase-order.view', 200, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:17:32'),
(21, 18, 'link', 'Goods Receipt', 'bi-box-arrow-in-down', NULL, 'admin.goods-receipts.index', 'admin.goods-receipts.*', 'purchase.view', 210, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:17:44'),
(22, 18, 'link', 'Purchases', 'bi-cart-plus', NULL, 'admin.purchases.index', 'admin.purchases.*', 'purchase.view', 220, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:17:59'),
(23, 18, 'link', 'Purchase Returns', 'bi-arrow-counterclockwise', NULL, 'admin.purchase-returns.index', 'admin.purchase-returns.*', 'purchase-return.view', 230, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:18:14'),
(24, NULL, 'heading', 'Sales Management', NULL, NULL, NULL, NULL, NULL, 240, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:21:54'),
(25, 24, 'link', 'Customers', 'bi-person-badge', NULL, 'admin.customers.index', 'admin.customers.*', 'customer.view', 250, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:16:06'),
(26, 24, 'link', 'Sales', 'bi-cart-check', NULL, 'admin.sales.index', 'admin.sales.*', 'sale.view', 260, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:16:29'),
(27, 24, 'link', 'Sales Returns', 'bi-arrow-return-left', NULL, 'admin.sales-returns.index', 'admin.sales-returns.*', 'sale-return.view', 270, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:16:40'),
(28, NULL, 'heading', 'Warehouses Management', NULL, NULL, NULL, NULL, NULL, 280, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:21:20'),
(29, 28, 'link', 'Warehouses', 'bi-building-gear', NULL, 'admin.warehouses.index', 'admin.warehouses.*', 'warehouse.view', 290, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:21:36'),
(30, NULL, 'heading', 'Reports', NULL, NULL, NULL, NULL, NULL, 300, '_self', 1, '2026-09-01 23:22:14', '2026-09-01 23:22:14'),
(31, 30, 'link', 'Stock Valuation', 'bi-graph-up', NULL, 'admin.reports.stock-valuation', 'admin.reports.*', 'report.view', 310, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:13:32'),
(32, 30, 'link', 'Purchase Reports', 'bi-bar-chart', NULL, 'admin.reports.purchases', 'admin.reports.*', 'report.view', 320, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:13:44'),
(33, 30, 'link', 'Sales Reports', 'bi-bar-chart-line', NULL, 'admin.reports.sales', 'admin.reports.*', 'report.view', 330, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:13:59'),
(34, 30, 'link', 'Profit Reports', 'bi-piggy-bank', NULL, 'admin.reports.profit', 'admin.reports.*', 'report.view', 340, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:14:15'),
(35, 30, 'link', 'Supplier Outstanding', 'bi-file-bar-graph', NULL, 'admin.reports.outstanding-suppliers', 'admin.reports.*', 'report.view', 350, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:14:27'),
(36, 30, 'link', 'Customer Outstanding', 'bi-file-bar-graph', NULL, 'admin.reports.outstanding-customers', 'admin.reports.*', 'report.view', 360, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:14:51'),
(37, 30, 'link', 'Expenses', 'bi-receipt', NULL, 'admin.expenses.index', 'admin.expenses.*', 'expense.view', 370, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:14:39'),
(38, NULL, 'heading', 'Administration', NULL, NULL, NULL, NULL, NULL, 380, '_self', 1, '2026-09-01 23:22:14', '2026-09-01 23:22:14'),
(39, 38, 'link', 'Users & Permissions', 'bi-person-gear', NULL, 'admin.admin.index', 'admin.admin.*', 'admin.view', 390, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:12:37'),
(40, 38, 'link', 'Menu Management', 'bi-list-nested', NULL, 'admin.menus.index', 'admin.menus.*', NULL, 400, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:12:50'),
(41, 38, 'link', 'Notifications', 'bi-bell', NULL, 'admin.notifications.index', 'admin.notifications.*', NULL, 410, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:13:00'),
(42, 38, 'link', 'Audit Logs', 'bi-journal-text', NULL, 'admin.audit-logs.index', 'admin.audit-logs.*', 'audit-log.view', 420, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:13:10'),
(43, 38, 'link', 'Settings', 'bi-gear', NULL, 'admin.settings.index', 'admin.settings.*', 'settings.view', 430, '_self', 1, '2026-09-01 23:22:14', '2026-09-03 00:06:55'),
(44, NULL, 'heading', 'System', 'bi bi-gear-fill', NULL, NULL, NULL, NULL, 10, '_self', 0, '2026-09-02 23:34:38', '2026-09-02 23:34:38');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `migrations`
--

TRUNCATE TABLE `migrations`;
--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_admins_table', 1),
(5, '2026_01_01_000002_create_permission_tables', 1),
(6, '2026_01_01_000003_create_settings_table', 1),
(7, '2026_01_01_000004_create_audit_logs_table', 1),
(8, '2026_01_02_000001_create_units_table', 1),
(9, '2026_01_02_000002_create_categories_table', 1),
(10, '2026_01_02_000003_create_brands_table', 1),
(11, '2026_01_02_000004_create_manufacturers_table', 1),
(12, '2026_01_02_000005_create_vehicle_types_table', 1),
(13, '2026_01_02_000006_create_vehicle_makes_table', 1),
(14, '2026_01_02_000007_create_vehicle_models_table', 1),
(15, '2026_01_02_000008_create_vehicle_variants_table', 1),
(16, '2026_01_02_000009_create_spare_parts_table', 1),
(17, '2026_01_02_000010_create_spare_part_images_table', 1),
(18, '2026_01_02_000011_create_spare_part_vehicle_table', 1),
(19, '2026_01_03_000001_create_warehouses_table', 1),
(20, '2026_01_03_000002_create_warehouse_hierarchy_tables', 1),
(21, '2026_01_03_000003_add_location_fks_to_spare_parts_table', 1),
(22, '2026_01_03_000004_create_stock_table', 1),
(23, '2026_01_03_000005_create_stock_movements_table', 1),
(24, '2026_01_03_000006_create_stock_adjustments_tables', 1),
(25, '2026_01_03_000007_create_stock_transfers_tables', 1),
(26, '2026_01_03_000008_create_stock_takes_tables', 1),
(27, '2026_01_04_000001_create_suppliers_table', 1),
(28, '2026_01_04_000002_create_purchase_orders_tables', 1),
(29, '2026_01_04_000003_create_goods_receipts_tables', 1),
(30, '2026_01_04_000004_create_purchases_tables', 1),
(31, '2026_01_04_000005_create_purchase_returns_tables', 1),
(32, '2026_01_05_000001_create_customers_table', 1),
(33, '2026_01_05_000002_create_sales_tables', 1),
(34, '2026_01_05_000003_create_sales_returns_tables', 1),
(35, '2026_01_06_000001_create_import_logs_table', 1),
(36, '2026_08_27_000003_create_products_table', 1),
(37, '2026_08_27_000004_create_product_variants_table', 1),
(38, '2026_08_27_000005_create_inventory_imports_table', 1),
(39, '2026_08_27_000006_create_inventory_import_items_table', 1),
(40, '2026_01_07_000001_create_expenses_table', 2),
(41, '2026_01_08_000001_add_is_published_to_spare_parts_table', 2),
(42, '2026_01_09_000001_add_email_notifications_to_admins_table', 2),
(43, '2026_01_10_000001_add_restock_columns_to_import_logs_table', 2),
(44, '2025_01_01_000001_create_menus_table', 3),
(45, '2026_01_11_000001_create_menus_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `model_has_permissions`
--

TRUNCATE TABLE `model_has_permissions`;
-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `model_has_roles`
--

TRUNCATE TABLE `model_has_roles`;
--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\Admin', 1);

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

--
-- Truncate table before insert `password_reset_tokens`
--

TRUNCATE TABLE `password_reset_tokens`;
-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `permissions`
--

TRUNCATE TABLE `permissions`;
--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard.view', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(2, 'admin.view', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(3, 'admin.create', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(4, 'admin.edit', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(5, 'admin.delete', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(6, 'role.view', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(7, 'role.create', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(8, 'role.edit', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(9, 'role.delete', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(10, 'settings.view', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(11, 'settings.edit', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(12, 'audit-log.view', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(13, 'spare-part.view', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(14, 'spare-part.create', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(15, 'spare-part.edit', 'admin', '2026-08-27 01:42:05', '2026-08-27 01:42:05'),
(16, 'spare-part.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(17, 'spare-part.import', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(18, 'spare-part.export', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(19, 'vehicle.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(20, 'vehicle.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(21, 'vehicle.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(22, 'vehicle.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(23, 'supplier.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(24, 'supplier.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(25, 'supplier.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(26, 'supplier.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(27, 'customer.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(28, 'customer.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(29, 'customer.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(30, 'customer.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(31, 'purchase-order.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(32, 'purchase-order.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(33, 'purchase-order.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(34, 'purchase-order.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(35, 'purchase-order.approve', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(36, 'purchase.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(37, 'purchase.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(38, 'purchase.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(39, 'purchase.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(40, 'purchase-return.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(41, 'purchase-return.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(42, 'purchase-return.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(43, 'purchase-return.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(44, 'purchase-return.approve', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(45, 'sale.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(46, 'sale.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(47, 'sale.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(48, 'sale.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(49, 'sale-return.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(50, 'sale-return.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(51, 'sale-return.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(52, 'sale-return.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(53, 'sale-return.approve', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(54, 'stock.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(55, 'stock-adjustment.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(56, 'stock-adjustment.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(57, 'stock-adjustment.approve', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(58, 'stock-transfer.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(59, 'stock-transfer.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(60, 'stock-transfer.approve', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(61, 'warehouse.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(62, 'warehouse.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(63, 'warehouse.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(64, 'warehouse.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(65, 'report.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(66, 'expense.view', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(67, 'expense.create', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(68, 'expense.edit', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(69, 'expense.delete', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `part_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `normalized_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_make` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_variant` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_year_from` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_year_to` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Piece',
  `purchase_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `mrp` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `current_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reserved_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `minimum_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `maximum_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  UNIQUE KEY `products_qr_code_unique` (`qr_code`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  KEY `products_warehouse_id_foreign` (`warehouse_id`),
  KEY `products_location_id_foreign` (`location_id`),
  KEY `products_created_by_foreign` (`created_by`),
  KEY `products_updated_by_foreign` (`updated_by`),
  KEY `products_part_no_vehicle_model_index` (`part_no`,`vehicle_model`),
  KEY `products_part_no_index` (`part_no`),
  KEY `products_normalized_description_index` (`normalized_description`),
  KEY `products_is_active_index` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `products`
--

TRUNCATE TABLE `products`;
-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `part_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_variant` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `mrp` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  UNIQUE KEY `product_variants_barcode_unique` (`barcode`),
  UNIQUE KEY `product_variants_qr_code_unique` (`qr_code`),
  KEY `product_variants_part_no_index` (`part_no`),
  KEY `product_variants_vehicle_model_index` (`vehicle_model`),
  KEY `product_variants_product_id_index` (`product_id`),
  KEY `product_variants_is_active_index` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `product_variants`
--

TRUNCATE TABLE `product_variants`;
-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `purchase_order_id` bigint UNSIGNED DEFAULT NULL,
  `goods_receipt_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('unpaid','partially_paid','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `stock_received_directly` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_invoice_number_unique` (`invoice_number`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchases_goods_receipt_id_foreign` (`goods_receipt_id`),
  KEY `purchases_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchases_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `purchases`
--

TRUNCATE TABLE `purchases`;
-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE IF NOT EXISTS `purchase_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `purchase_price` decimal(12,2) NOT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `purchase_items`
--

TRUNCATE TABLE `purchase_items`;
-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `po_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `po_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `payment_terms` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','pending','approved','partially_received','received','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_orders_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `purchase_orders`
--

TRUNCATE TABLE `purchase_orders`;
-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `quantity_received` int NOT NULL DEFAULT '0',
  `purchase_price` decimal(12,2) NOT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `purchase_order_items`
--

TRUNCATE TABLE `purchase_order_items`;
-- --------------------------------------------------------

--
-- Table structure for table `purchase_payments`
--

DROP TABLE IF EXISTS `purchase_payments`;
CREATE TABLE IF NOT EXISTS `purchase_payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','card','cheque','online','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_payments_payment_number_unique` (`payment_number`),
  KEY `purchase_payments_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_payments_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `purchase_payments`
--

TRUNCATE TABLE `purchase_payments`;
-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

DROP TABLE IF EXISTS `purchase_returns`;
CREATE TABLE IF NOT EXISTS `purchase_returns` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_returns_return_number_unique` (`return_number`),
  KEY `purchase_returns_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_returns_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_returns_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_returns_created_by_foreign` (`created_by`),
  KEY `purchase_returns_approved_by_foreign` (`approved_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `purchase_returns`
--

TRUNCATE TABLE `purchase_returns`;
-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

DROP TABLE IF EXISTS `purchase_return_items`;
CREATE TABLE IF NOT EXISTS `purchase_return_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  KEY `purchase_return_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `purchase_return_items`
--

TRUNCATE TABLE `purchase_return_items`;
-- --------------------------------------------------------

--
-- Table structure for table `racks`
--

DROP TABLE IF EXISTS `racks`;
CREATE TABLE IF NOT EXISTS `racks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `warehouse_zone_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `racks_warehouse_id_foreign` (`warehouse_id`),
  KEY `racks_warehouse_zone_id_foreign` (`warehouse_zone_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `racks`
--

TRUNCATE TABLE `racks`;
-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `roles`
--

TRUNCATE TABLE `roles`;
--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(2, 'Admin', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(3, 'Inventory Manager', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(4, 'Purchase Manager', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(5, 'Sales Manager', 'admin', '2026-08-27 01:42:06', '2026-08-27 01:42:06'),
(6, 'Data Entry Operator', 'admin', '2026-08-27 01:42:07', '2026-08-27 01:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `role_has_permissions`
--

TRUNCATE TABLE `role_has_permissions`;
--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(13, 3),
(13, 6),
(14, 1),
(14, 2),
(14, 3),
(14, 6),
(15, 1),
(15, 2),
(15, 3),
(15, 6),
(16, 1),
(16, 2),
(16, 3),
(17, 1),
(17, 2),
(17, 3),
(18, 1),
(18, 2),
(18, 3),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(23, 4),
(23, 6),
(24, 1),
(24, 2),
(24, 4),
(24, 6),
(25, 1),
(25, 2),
(25, 4),
(26, 1),
(26, 2),
(26, 4),
(27, 1),
(27, 2),
(27, 5),
(27, 6),
(28, 1),
(28, 2),
(28, 5),
(28, 6),
(29, 1),
(29, 2),
(29, 5),
(30, 1),
(30, 2),
(30, 5),
(31, 1),
(31, 2),
(31, 4),
(32, 1),
(32, 2),
(32, 4),
(33, 1),
(33, 2),
(33, 4),
(34, 1),
(34, 2),
(34, 4),
(35, 1),
(35, 2),
(35, 4),
(36, 1),
(36, 2),
(36, 4),
(36, 6),
(37, 1),
(37, 2),
(37, 4),
(37, 6),
(38, 1),
(38, 2),
(38, 4),
(39, 1),
(39, 2),
(39, 4),
(40, 1),
(40, 2),
(40, 4),
(41, 1),
(41, 2),
(41, 4),
(42, 1),
(42, 2),
(42, 4),
(43, 1),
(43, 2),
(43, 4),
(44, 1),
(44, 2),
(44, 4),
(45, 1),
(45, 2),
(45, 5),
(45, 6),
(46, 1),
(46, 2),
(46, 5),
(46, 6),
(47, 1),
(47, 2),
(47, 5),
(48, 1),
(48, 2),
(48, 5),
(49, 1),
(49, 2),
(49, 5),
(50, 1),
(50, 2),
(50, 5),
(51, 1),
(51, 2),
(51, 5),
(52, 1),
(52, 2),
(52, 5),
(53, 1),
(53, 2),
(53, 5),
(54, 1),
(54, 2),
(54, 3),
(55, 1),
(55, 2),
(55, 3),
(56, 1),
(56, 2),
(56, 3),
(57, 1),
(57, 2),
(57, 3),
(58, 1),
(58, 2),
(58, 3),
(59, 1),
(59, 2),
(59, 3),
(60, 1),
(60, 2),
(60, 3),
(61, 1),
(61, 2),
(61, 3),
(62, 1),
(62, 2),
(62, 3),
(63, 1),
(63, 2),
(63, 3),
(64, 1),
(64, 2),
(64, 3),
(65, 1),
(65, 2),
(65, 3),
(65, 4),
(65, 5),
(66, 1),
(66, 2),
(67, 1),
(67, 2),
(68, 1),
(68, 2),
(69, 1),
(69, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `invoice_date` date NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `salesperson_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cost_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('unpaid','partially_paid','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoice_number_unique` (`invoice_number`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_salesperson_id_foreign` (`salesperson_id`),
  KEY `sales_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `sales`
--

TRUNCATE TABLE `sales`;
-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
--

DROP TABLE IF EXISTS `sales_returns`;
CREATE TABLE IF NOT EXISTS `sales_returns` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_returns_return_number_unique` (`return_number`),
  KEY `sales_returns_customer_id_foreign` (`customer_id`),
  KEY `sales_returns_sale_id_foreign` (`sale_id`),
  KEY `sales_returns_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_returns_created_by_foreign` (`created_by`),
  KEY `sales_returns_approved_by_foreign` (`approved_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `sales_returns`
--

TRUNCATE TABLE `sales_returns`;
-- --------------------------------------------------------

--
-- Table structure for table `sales_return_items`
--

DROP TABLE IF EXISTS `sales_return_items`;
CREATE TABLE IF NOT EXISTS `sales_return_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sales_return_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `return_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition` enum('resalable','damaged','defective') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'resalable',
  `refund_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_return_items_sales_return_id_foreign` (`sales_return_id`),
  KEY `sales_return_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `sales_return_items`
--

TRUNCATE TABLE `sales_return_items`;
-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `selling_price` decimal(12,2) NOT NULL,
  `cost_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `sale_items`
--

TRUNCATE TABLE `sale_items`;
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
-- Truncate table before insert `sessions`
--

TRUNCATE TABLE `sessions`;
--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('eBXOdeVVzYgTFil7dFJISgI7TjmEzb6TWqGdFCM7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJGd2cxRmtTNXFUTFg1OWZJYmY0bVk4UmcydURhUjdTZ3Z1YjFta0xYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMVwvbGFyYXZlbFwvdnNwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1788408437),
('8TTmP3leQAQiKxn2WpUYdQnaUk14NWzMphJcrEas', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5YjlMeHZ3Z0ZCZVJTWnoxUWFiTHlzS3hmRWdjNU1WNW13U2JxdEJ0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMVwvbGFyYXZlbFwvdnNwXC9hZG1pblwvbWVudXMiLCJyb3V0ZSI6ImFkbWluLm1lbnVzLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fYWRtaW5fNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiaW1wb3J0IjpbXX0=', 1788410538),
('Gbo3y74KZjKO7rEoNxWloQucMpp9WhHDb1iuitVE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJVZk9OUkpWQUlUelpHbk15Y3ZVdUVnNWRkeHZzSXVibWVXb0Y2Z09vIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMVwvbGFyYXZlbFwvdnNwXC9hZG1pblwvbG9naW4iLCJyb3V0ZSI6ImFkbWluLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1788410922),
('dQjIyTjIVOyc76hBXHy3VquNgw3qpWQEjreL2SHE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJWOGZRYWRsMnBpYlVnSmhBNDNYVWVjZWNlVXkwOEJBcVI2S0xMenJSIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9sYXJhdmVsXC92c3BcL2FkbWluXC9sb2dpbiIsInJvdXRlIjoiYWRtaW4ubG9naW4ifX0=', 1788415143);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_group_key_unique` (`group`,`key`),
  KEY `settings_group_index` (`group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `settings`
--

TRUNCATE TABLE `settings`;
-- --------------------------------------------------------

--
-- Table structure for table `shelves`
--

DROP TABLE IF EXISTS `shelves`;
CREATE TABLE IF NOT EXISTS `shelves` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rack_id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shelves_rack_id_foreign` (`rack_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `shelves`
--

TRUNCATE TABLE `shelves`;
-- --------------------------------------------------------

--
-- Table structure for table `spare_parts`
--

DROP TABLE IF EXISTS `spare_parts`;
CREATE TABLE IF NOT EXISTS `spare_parts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `part_number` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oem_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternate_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `sub_category_id` bigint UNSIGNED DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `manufacturer_id` bigint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `part_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `wholesale_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `retail_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `min_selling_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `max_selling_price` decimal(12,2) DEFAULT NULL,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `opening_stock` int NOT NULL DEFAULT '0',
  `current_stock` int NOT NULL DEFAULT '0',
  `minimum_stock` int NOT NULL DEFAULT '0',
  `maximum_stock` int DEFAULT NULL,
  `reorder_level` int NOT NULL DEFAULT '0',
  `reserved_stock` int NOT NULL DEFAULT '0',
  `damaged_stock` int NOT NULL DEFAULT '0',
  `warehouse` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rack` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shelf` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `rack_id` bigint UNSIGNED DEFAULT NULL,
  `shelf_id` bigint UNSIGNED DEFAULT NULL,
  `bin_id` bigint UNSIGNED DEFAULT NULL,
  `main_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','discontinued') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spare_parts_part_number_unique` (`part_number`),
  UNIQUE KEY `spare_parts_sku_unique` (`sku`),
  UNIQUE KEY `spare_parts_barcode_unique` (`barcode`),
  UNIQUE KEY `spare_parts_slug_unique` (`slug`),
  KEY `spare_parts_category_id_foreign` (`category_id`),
  KEY `spare_parts_sub_category_id_foreign` (`sub_category_id`),
  KEY `spare_parts_brand_id_foreign` (`brand_id`),
  KEY `spare_parts_manufacturer_id_foreign` (`manufacturer_id`),
  KEY `spare_parts_unit_id_foreign` (`unit_id`),
  KEY `spare_parts_created_by_foreign` (`created_by`),
  KEY `spare_parts_oem_number_index` (`oem_number`),
  KEY `spare_parts_name_index` (`name`),
  KEY `spare_parts_warehouse_id_foreign` (`warehouse_id`),
  KEY `spare_parts_rack_id_foreign` (`rack_id`),
  KEY `spare_parts_shelf_id_foreign` (`shelf_id`),
  KEY `spare_parts_bin_id_foreign` (`bin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `spare_parts`
--

TRUNCATE TABLE `spare_parts`;
--
-- Dumping data for table `spare_parts`
--

INSERT INTO `spare_parts` (`id`, `part_number`, `sku`, `barcode`, `oem_number`, `alternate_number`, `name`, `short_description`, `description`, `category_id`, `sub_category_id`, `brand_id`, `manufacturer_id`, `unit_id`, `part_type`, `purchase_price`, `wholesale_price`, `retail_price`, `min_selling_price`, `max_selling_price`, `tax_percentage`, `discount_percentage`, `opening_stock`, `current_stock`, `minimum_stock`, `maximum_stock`, `reorder_level`, `reserved_stock`, `damaged_stock`, `warehouse`, `rack`, `shelf`, `bin`, `warehouse_id`, `rack_id`, `shelf_id`, `bin_id`, `main_image`, `slug`, `seo_title`, `seo_description`, `keywords`, `status`, `is_published`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'APFL-28', 'IMP-00001', NULL, NULL, NULL, 'FLOR — ACTIVA — WHITE ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 4, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'flor-activa-white-activa-n-ap-go-EYTeJ', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-02 22:50:52', NULL),
(2, 'APFL-28-2', 'IMP-00002', NULL, NULL, NULL, 'FLOR ASSY-ACTIVA (N/M) BK ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'flor-assy-activa-nm-bk-activa-n-ap-go-4SwKY', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47', NULL),
(3, 'APFL-28-3', 'IMP-00003', NULL, NULL, NULL, 'FLOR ASSY-ACTIVA (N/M) GRAY ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'flor-assy-activa-nm-gray-activa-n-ap-go-ITo63', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47', NULL),
(4, 'APFL-28-4', 'IMP-00004', NULL, NULL, NULL, 'FLOOR-ACTIVA (N/M) RED ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-nm-red-activa-n-ap-go-EI4Jr', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47', NULL),
(5, 'APFL-28-5', 'IMP-00005', NULL, NULL, NULL, 'FLOOR-ACTIVA (N/M) (M.SILVER) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-nm-msilver-activa-n-ap-go-ASP9B', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47', NULL),
(6, 'APFL-28-6', 'IMP-00006', NULL, NULL, NULL, 'FLOOR-ACTIVA (N/M) (M.GRAY) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-nm-mgray-activa-n-ap-go-ldpfI', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47', NULL),
(7, 'APFM-28', 'IMP-00007', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA (N/M) (M.GRAY) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 4, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-nm-mgray-activa-n-ap-go-ZNSPI', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-02 22:50:52', NULL),
(8, 'APFM-28-2', 'IMP-00008', NULL, NULL, NULL, 'FRONT MUDGARD N/M WHITE ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-nm-white-activa-n-ap-go-sa7ij', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47', NULL),
(9, 'APFM-89', 'IMP-00009', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 3G ( W.RED ) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-3g-wred-activa-3g-ap-go-C7pAW', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:47', '2026-09-02 22:50:52', NULL),
(10, 'APFM-89-2', 'IMP-00010', NULL, NULL, NULL, 'FRONT MUDGARD — ACTIVA 3G BLUE ACTIVA 5G AP', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-activa-3g-blue-activa-5g-ap-GvNYu', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(11, 'APFM-89-3', 'IMP-00011', NULL, NULL, NULL, 'FRONT MUJGARD-ACTIVA 4G (M.SILVER) ACTIVA 4G AP', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-4g-msilver-activa-4g-ap-wFZbl', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(12, 'APFM-89-4', 'IMP-00012', NULL, NULL, NULL, 'FRONT MUJGARD-ACTIVA 4G (MATT GREY) ACTIVA 4G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-4g-matt-grey-activa-4g-ap-go-FoYIT', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(13, 'APFM-89-5', 'IMP-00013', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA (4G) (I.RED) ACTIVA 4G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-4g-ired-activa-4g-ap-go-HU21g', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(14, 'APFM-28-3', 'IMP-00014', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA (N/M) BLACK ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-nm-black-activa-n-ap-go-aNDYz', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(15, 'APFM-89-6', 'IMP-00015', NULL, NULL, NULL, 'FRONT MUDGARD — ACTIVA 5G BK ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-activa-5g-bk-activa-5g-ap-go-DwQV8', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(16, 'APFM-89-7', 'IMP-00016', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 5G (M.GREY) ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-5g-mgrey-activa-5g-ap-go-KbjU5', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(17, 'APFM-89-8', 'IMP-00017', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 5G — WHITE ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-5g-white-activa-5g-ap-go-oMNUs', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(18, 'APFM-89-9', 'IMP-00018', NULL, NULL, NULL, 'FRPNT MUDGAURD-ACTIVA 5G(M.SILVER) ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'frpnt-mudgaurd-activa-5gmsilver-activa-5g-ap-go-cMy0H', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(19, 'APFM-89-10', 'IMP-00019', NULL, NULL, NULL, 'FRPNT MUDGAURD-ACTIVA 6G (M.GRAY) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'frpnt-mudgaurd-activa-6g-mgray-activa-6g-ap-go-rP8ki', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(20, 'APFM-89-11', 'IMP-00020', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 6G (D.BLUE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-6g-dblue-activa-6g-ap-go-xqXhH', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(21, 'APFM-89-12', 'IMP-00021', NULL, NULL, NULL, 'FRONT MUJGARD-ACTIVA 6G (WHITE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-6g-white-activa-6g-ap-go-31qgf', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(22, 'APFM-89-13', 'IMP-00022', NULL, NULL, NULL, 'FRONT MUDGARD — ACTIVA 6G ( BS-6 ) ( B ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-activa-6g-bs-6-b-activa-6g-ap-go-oNAOn', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(23, 'APSW-89', 'IMP-00023', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (BROWN) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-brown-activa-3g-ap-go-dzRxb', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-02 22:50:52', NULL),
(24, 'APSW-28', 'IMP-00024', NULL, NULL, NULL, 'SIDE WING-ACTIVA (N/M) (WHITE) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-nm-white-activa-n-ap-go-yg68k', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-02 22:50:52', NULL),
(25, 'APSW-28-2', 'IMP-00025', NULL, NULL, NULL, 'SIDE WING-ACTIVA (N/M) (GRAY) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-nm-gray-activa-n-ap-go-Mao5o', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(26, 'APSW-28-3', 'IMP-00026', NULL, NULL, NULL, 'SIDE WING-ACTIVA (N/M) (BLACK) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-nm-black-activa-n-ap-go-b0nE1', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(27, 'APSW-89-2', 'IMP-00027', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (M.GRAY) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-mgray-activa-3g-ap-go-EPSbl', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(28, 'APSW-89-3', 'IMP-00028', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (BLUE) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-blue-activa-3g-ap-go-seJmE', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(29, 'APSW-89-4', 'IMP-00029', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (M.SILVER) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-msilver-activa-3g-ap-go-9UJ1s', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(30, 'APSW-89-5', 'IMP-00030', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (BLACK) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-black-activa-3g-ap-go-gKuIa', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(31, 'APSW-89-6', 'IMP-00031', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (WHITE) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-white-activa-3g-ap-go-AF4kW', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(32, 'APSW-89-7', 'IMP-00032', NULL, NULL, NULL, 'SIDE WING-ACTIVA 6G (M.GRAY) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1600.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-6g-mgray-activa-6g-ap-go-JzHuX', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(33, 'APSW-89-8', 'IMP-00033', NULL, NULL, NULL, 'SIDE WING-ACTIVA (6G) (WHITE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1600.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-6g-white-activa-6g-ap-go-fYCQh', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(34, 'APSW-89-9', 'IMP-00034', NULL, NULL, NULL, 'SIDE WING-ACTIVA (6G) (BLACK) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1600.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-6g-black-activa-6g-ap-go-fVa9I', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(35, 'APFL-89', 'IMP-00035', NULL, NULL, NULL, 'FLOOR-ACTIVA 6G (O/M) (WHITE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 850.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-6g-om-white-activa-6g-ap-go-TSgVI', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-02 22:50:52', NULL),
(36, 'APFL-89-2', 'IMP-00036', NULL, NULL, NULL, 'FLOOR-ACTIVA 6G (O/M) (W.RED) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 850.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-6g-om-wred-activa-6g-ap-go-rO5UD', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(37, 'APFL-89-3', 'IMP-00037', NULL, NULL, NULL, 'FLOOR-ACTIVA 6G (O/M) (BLUE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 850.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-6g-om-blue-activa-6g-ap-go-TUw57', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(38, 'APFM-89-14', 'IMP-00038', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 5G (BROWN) ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-5g-brown-activa-5g-ap-go-VDLNy', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(39, 'APFL-89-4', 'IMP-00039', NULL, NULL, NULL, 'FLOOR-ACTIVA 6G (N/M) (W.RED) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 850.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-6g-nm-wred-activa-6g-ap-go-Dj2YL', NULL, NULL, NULL, 'active', 0, 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48', NULL),
(40, 'APFL-28-7', 'IMP-00040', NULL, NULL, NULL, 'FLOR ASSY-ACTIVA (N/M) BK ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'flor-assy-activa-nm-bk-activa-n-ap-go-L3eLE', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(41, 'APFL-28-8', 'IMP-00041', NULL, NULL, NULL, 'FLOR ASSY-ACTIVA (N/M) GRAY ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'flor-assy-activa-nm-gray-activa-n-ap-go-EKyEY', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(42, 'APFL-28-9', 'IMP-00042', NULL, NULL, NULL, 'FLOOR-ACTIVA (N/M) RED ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-nm-red-activa-n-ap-go-rAr49', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(43, 'APFL-28-10', 'IMP-00043', NULL, NULL, NULL, 'FLOOR-ACTIVA (N/M) (M.SILVER) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-nm-msilver-activa-n-ap-go-0c5vH', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(44, 'APFL-28-11', 'IMP-00044', NULL, NULL, NULL, 'FLOOR-ACTIVA (N/M) (M.GRAY) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 750.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-nm-mgray-activa-n-ap-go-mpFDP', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(45, 'APFM-28-4', 'IMP-00045', NULL, NULL, NULL, 'FRONT MUDGARD N/M WHITE ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-nm-white-activa-n-ap-go-JYk07', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(46, 'APFM-89-15', 'IMP-00046', NULL, NULL, NULL, 'FRONT MUDGARD — ACTIVA 3G BLUE ACTIVA 5G AP', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-activa-3g-blue-activa-5g-ap-2Nqe6', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(47, 'APFM-89-16', 'IMP-00047', NULL, NULL, NULL, 'FRONT MUJGARD-ACTIVA 4G (M.SILVER) ACTIVA 4G AP', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-4g-msilver-activa-4g-ap-pl66n', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(48, 'APFM-89-17', 'IMP-00048', NULL, NULL, NULL, 'FRONT MUJGARD-ACTIVA 4G (MATT GREY) ACTIVA 4G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-4g-matt-grey-activa-4g-ap-go-pqc2t', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(49, 'APFM-89-18', 'IMP-00049', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA (4G) (I.RED) ACTIVA 4G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-4g-ired-activa-4g-ap-go-6ApsZ', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(50, 'APFM-28-5', 'IMP-00050', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA (N/M) BLACK ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-nm-black-activa-n-ap-go-u1RYd', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(51, 'APFM-89-19', 'IMP-00051', NULL, NULL, NULL, 'FRONT MUDGARD — ACTIVA 5G BK ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-activa-5g-bk-activa-5g-ap-go-tBJl5', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(52, 'APFM-89-20', 'IMP-00052', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 5G (M.GREY) ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-5g-mgrey-activa-5g-ap-go-jA5GF', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(53, 'APFM-89-21', 'IMP-00053', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 5G — WHITE ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 2, 2, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-5g-white-activa-5g-ap-go-Ydgm8', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(54, 'APFM-89-22', 'IMP-00054', NULL, NULL, NULL, 'FRPNT MUDGAURD-ACTIVA 5G(M.SILVER) ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'frpnt-mudgaurd-activa-5gmsilver-activa-5g-ap-go-f286f', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(55, 'APFM-89-23', 'IMP-00055', NULL, NULL, NULL, 'FRPNT MUDGAURD-ACTIVA 6G (M.GRAY) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'frpnt-mudgaurd-activa-6g-mgray-activa-6g-ap-go-T7xB7', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(56, 'APFM-89-24', 'IMP-00056', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 6G (D.BLUE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-6g-dblue-activa-6g-ap-go-25Xtg', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(57, 'APFM-89-25', 'IMP-00057', NULL, NULL, NULL, 'FRONT MUJGARD-ACTIVA 6G (WHITE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-6g-white-activa-6g-ap-go-agdKc', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(58, 'APFM-89-26', 'IMP-00058', NULL, NULL, NULL, 'FRONT MUDGARD — ACTIVA 6G ( BS-6 ) ( B ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mudgard-activa-6g-bs-6-b-activa-6g-ap-go-0eFIN', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(59, 'APSW-28-4', 'IMP-00059', NULL, NULL, NULL, 'SIDE WING-ACTIVA (N/M) (GRAY) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-nm-gray-activa-n-ap-go-2WMsV', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(60, 'APSW-28-5', 'IMP-00060', NULL, NULL, NULL, 'SIDE WING-ACTIVA (N/M) (BLACK) ACTIVA N/ AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-nm-black-activa-n-ap-go-NNRmm', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(61, 'APSW-89-10', 'IMP-00061', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (M.GRAY) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-mgray-activa-3g-ap-go-knH64', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(62, 'APSW-89-11', 'IMP-00062', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (BLUE) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-blue-activa-3g-ap-go-xnRos', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(63, 'APSW-89-12', 'IMP-00063', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (M.SILVER) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-msilver-activa-3g-ap-go-gynv0', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(64, 'APSW-89-13', 'IMP-00064', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (BLACK) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-black-activa-3g-ap-go-3dX1E', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(65, 'APSW-89-14', 'IMP-00065', NULL, NULL, NULL, 'SIDE WING-ACTIVA (3G) (WHITE) ACTIVA 3G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-3g-white-activa-3g-ap-go-NyXET', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(66, 'APSW-89-15', 'IMP-00066', NULL, NULL, NULL, 'SIDE WING-ACTIVA 6G (M.GRAY) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1600.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-6g-mgray-activa-6g-ap-go-z97eU', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(67, 'APSW-89-16', 'IMP-00067', NULL, NULL, NULL, 'SIDE WING-ACTIVA (6G) (WHITE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1600.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-6g-white-activa-6g-ap-go-PPOIT', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(68, 'APSW-89-17', 'IMP-00068', NULL, NULL, NULL, 'SIDE WING-ACTIVA (6G) (BLACK) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1600.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'side-wing-activa-6g-black-activa-6g-ap-go-uiPmf', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(69, 'APFL-89-5', 'IMP-00069', NULL, NULL, NULL, 'FLOOR-ACTIVA 6G (O/M) (W.RED) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 850.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-6g-om-wred-activa-6g-ap-go-JOaxS', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(70, 'APFL-89-6', 'IMP-00070', NULL, NULL, NULL, 'FLOOR-ACTIVA 6G (O/M) (BLUE) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 850.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-6g-om-blue-activa-6g-ap-go-zWbKU', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52', NULL),
(71, 'APFM-89-27', 'IMP-00071', NULL, NULL, NULL, 'FRONT MUJGARD — ACTIVA 5G (BROWN) ACTIVA 5G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 580.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'front-mujgard-activa-5g-brown-activa-5g-ap-go-mMP6n', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:52:35', '2026-09-02 22:52:35'),
(72, 'APFL-89-7', 'IMP-00072', NULL, NULL, NULL, 'FLOOR-ACTIVA 6G (N/M) (W.RED) ACTIVA 6G AP GO', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 850.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 1, 1, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'floor-activa-6g-nm-wred-activa-6g-ap-go-MHL7I', NULL, NULL, NULL, 'active', 0, 1, '2026-09-02 22:50:52', '2026-09-02 22:52:07', '2026-09-02 22:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `spare_part_images`
--

DROP TABLE IF EXISTS `spare_part_images`;
CREATE TABLE IF NOT EXISTS `spare_part_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spare_part_images_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `spare_part_images`
--

TRUNCATE TABLE `spare_part_images`;
-- --------------------------------------------------------

--
-- Table structure for table `spare_part_vehicle`
--

DROP TABLE IF EXISTS `spare_part_vehicle`;
CREATE TABLE IF NOT EXISTS `spare_part_vehicle` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `vehicle_variant_id` bigint UNSIGNED NOT NULL,
  `oem_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` enum('Front','Rear','Left','Right','Front Left','Front Right','Rear Left','Rear Right','Universal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Universal',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spare_part_vehicle_unique` (`spare_part_id`,`vehicle_variant_id`,`position`),
  KEY `spare_part_vehicle_vehicle_variant_id_foreign` (`vehicle_variant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `spare_part_vehicle`
--

TRUNCATE TABLE `spare_part_vehicle`;
-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `current_stock` int NOT NULL DEFAULT '0',
  `reserved_stock` int NOT NULL DEFAULT '0',
  `damaged_stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_spare_part_id_warehouse_id_unique` (`spare_part_id`,`warehouse_id`),
  KEY `stock_warehouse_id_foreign` (`warehouse_id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock`
--

TRUNCATE TABLE `stock`;
--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `spare_part_id`, `warehouse_id`, `current_stock`, `reserved_stock`, `damaged_stock`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, 0, 0, '2026-09-01 03:31:47', '2026-09-02 22:50:52'),
(2, 2, 1, 2, 0, 0, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(3, 3, 1, 1, 0, 0, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(4, 4, 1, 1, 0, 0, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(5, 5, 1, 1, 0, 0, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(6, 6, 1, 1, 0, 0, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(7, 7, 1, 4, 0, 0, '2026-09-01 03:31:47', '2026-09-02 22:50:52'),
(8, 8, 1, 2, 0, 0, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(9, 9, 1, 2, 0, 0, '2026-09-01 03:31:47', '2026-09-02 22:50:52'),
(10, 10, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(11, 11, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(12, 12, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(13, 13, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(14, 14, 1, 2, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(15, 15, 1, 2, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(16, 16, 1, 2, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(17, 17, 1, 2, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(18, 18, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(19, 19, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(20, 20, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(21, 21, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(22, 22, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(23, 23, 1, 2, 0, 0, '2026-09-01 03:31:48', '2026-09-02 22:50:52'),
(24, 24, 1, 2, 0, 0, '2026-09-01 03:31:48', '2026-09-02 22:50:52'),
(25, 25, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(26, 26, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(27, 27, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(28, 28, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(29, 29, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(30, 30, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(31, 31, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(32, 32, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(33, 33, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(34, 34, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(35, 35, 1, 2, 0, 0, '2026-09-01 03:31:48', '2026-09-02 22:50:52'),
(36, 36, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(37, 37, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(38, 38, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(39, 39, 1, 1, 0, 0, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(40, 40, 1, 2, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(41, 41, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(42, 42, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(43, 43, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(44, 44, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(45, 45, 1, 2, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(46, 46, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(47, 47, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(48, 48, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(49, 49, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(50, 50, 1, 2, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(51, 51, 1, 2, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(52, 52, 1, 2, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(53, 53, 1, 2, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(54, 54, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(55, 55, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(56, 56, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(57, 57, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(58, 58, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(59, 59, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(60, 60, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(61, 61, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(62, 62, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(63, 63, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(64, 64, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(65, 65, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(66, 66, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(67, 67, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(68, 68, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(69, 69, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(70, 70, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(71, 71, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(72, 72, 1, 1, 0, 0, '2026-09-02 22:50:52', '2026-09-02 22:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

DROP TABLE IF EXISTS `stock_adjustments`;
CREATE TABLE IF NOT EXISTS `stock_adjustments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `adjustment_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `reason` enum('Physical stock difference','Damaged product','Lost product','Found stock','Data correction','Opening stock correction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_adjustments_adjustment_number_unique` (`adjustment_number`),
  KEY `stock_adjustments_warehouse_id_foreign` (`warehouse_id`),
  KEY `stock_adjustments_created_by_foreign` (`created_by`),
  KEY `stock_adjustments_approved_by_foreign` (`approved_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock_adjustments`
--

TRUNCATE TABLE `stock_adjustments`;
-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustment_items`
--

DROP TABLE IF EXISTS `stock_adjustment_items`;
CREATE TABLE IF NOT EXISTS `stock_adjustment_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_adjustment_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `current_quantity` int NOT NULL,
  `adjustment_type` enum('increase','decrease') COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjustment_quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_adjustment_items_stock_adjustment_id_foreign` (`stock_adjustment_id`),
  KEY `stock_adjustment_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock_adjustment_items`
--

TRUNCATE TABLE `stock_adjustment_items`;
-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `type` enum('OPENING_STOCK','PURCHASE','SALE','PURCHASE_RETURN','SALES_RETURN','TRANSFER_IN','TRANSFER_OUT','ADJUSTMENT_IN','ADJUSTMENT_OUT','DAMAGE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `stock_before` int NOT NULL,
  `stock_after` int NOT NULL,
  `reference_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_created_by_foreign` (`created_by`),
  KEY `stock_movements_part_warehouse_index` (`spare_part_id`,`warehouse_id`),
  KEY `stock_movements_reference_index` (`reference_type`,`reference_id`),
  KEY `stock_movements_warehouse_created_index` (`warehouse_id`,`created_at`),
  KEY `stock_movements_part_created_index` (`spare_part_id`,`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock_movements`
--

TRUNCATE TABLE `stock_movements`;
--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `spare_part_id`, `warehouse_id`, `type`, `quantity`, `stock_before`, `stock_after`, `reference_type`, `reference_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(2, 2, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(3, 3, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(4, 4, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(5, 5, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(6, 6, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(7, 7, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(8, 8, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(9, 9, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:47', '2026-09-01 03:31:47'),
(10, 10, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(11, 11, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(12, 12, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(13, 13, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(14, 14, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(15, 15, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(16, 16, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(17, 17, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(18, 18, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(19, 19, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(20, 20, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(21, 21, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(22, 22, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(23, 23, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(24, 24, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(25, 25, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(26, 26, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(27, 27, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(28, 28, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(29, 29, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(30, 30, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(31, 31, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(32, 32, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(33, 33, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(34, 34, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(35, 35, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(36, 36, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(37, 37, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(38, 38, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(39, 39, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-01 03:31:48', '2026-09-01 03:31:48'),
(40, 1, 1, 'PURCHASE', 2, 2, 4, NULL, NULL, 'Restocked via import: SA-01074--SALE--PAVANAUTOPARTS.pdf', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(41, 40, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(42, 41, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(43, 42, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(44, 43, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(45, 44, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(46, 7, 1, 'PURCHASE', 2, 2, 4, NULL, NULL, 'Restocked via import: SA-01074--SALE--PAVANAUTOPARTS.pdf', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(47, 45, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(48, 9, 1, 'PURCHASE', 1, 1, 2, NULL, NULL, 'Restocked via import: SA-01074--SALE--PAVANAUTOPARTS.pdf', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(49, 46, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(50, 47, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(51, 48, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(52, 49, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(53, 50, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(54, 51, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(55, 52, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(56, 53, 1, 'OPENING_STOCK', 2, 0, 2, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(57, 54, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(58, 55, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(59, 56, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(60, 57, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(61, 58, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(62, 23, 1, 'PURCHASE', 1, 1, 2, NULL, NULL, 'Restocked via import: SA-01074--SALE--PAVANAUTOPARTS.pdf', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(63, 24, 1, 'PURCHASE', 1, 1, 2, NULL, NULL, 'Restocked via import: SA-01074--SALE--PAVANAUTOPARTS.pdf', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(64, 59, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(65, 60, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(66, 61, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(67, 62, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(68, 63, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(69, 64, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(70, 65, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(71, 66, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(72, 67, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(73, 68, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(74, 35, 1, 'PURCHASE', 1, 1, 2, NULL, NULL, 'Restocked via import: SA-01074--SALE--PAVANAUTOPARTS.pdf', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(75, 69, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(76, 70, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(77, 71, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52'),
(78, 72, 1, 'OPENING_STOCK', 1, 0, 1, NULL, NULL, 'Opening stock from bulk import', 1, '2026-09-02 22:50:52', '2026-09-02 22:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `stock_takes`
--

DROP TABLE IF EXISTS `stock_takes`;
CREATE TABLE IF NOT EXISTS `stock_takes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_take_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `status` enum('draft','counting','pending_approval','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_takes_stock_take_number_unique` (`stock_take_number`),
  KEY `stock_takes_warehouse_id_foreign` (`warehouse_id`),
  KEY `stock_takes_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock_takes`
--

TRUNCATE TABLE `stock_takes`;
-- --------------------------------------------------------

--
-- Table structure for table `stock_take_items`
--

DROP TABLE IF EXISTS `stock_take_items`;
CREATE TABLE IF NOT EXISTS `stock_take_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_take_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `system_quantity` int NOT NULL,
  `counted_quantity` int DEFAULT NULL,
  `difference` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_take_items_stock_take_id_foreign` (`stock_take_id`),
  KEY `stock_take_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock_take_items`
--

TRUNCATE TABLE `stock_take_items`;
-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE IF NOT EXISTS `stock_transfers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_warehouse_id` bigint UNSIGNED NOT NULL,
  `to_warehouse_id` bigint UNSIGNED NOT NULL,
  `transfer_date` date NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','received','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `received_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_transfer_number_unique` (`transfer_number`),
  KEY `stock_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `stock_transfers_to_warehouse_id_foreign` (`to_warehouse_id`),
  KEY `stock_transfers_requested_by_foreign` (`requested_by`),
  KEY `stock_transfers_approved_by_foreign` (`approved_by`),
  KEY `stock_transfers_received_by_foreign` (`received_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock_transfers`
--

TRUNCATE TABLE `stock_transfers`;
-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer_items`
--

DROP TABLE IF EXISTS `stock_transfer_items`;
CREATE TABLE IF NOT EXISTS `stock_transfer_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` bigint UNSIGNED NOT NULL,
  `spare_part_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfer_items_stock_transfer_id_foreign` (`stock_transfer_id`),
  KEY `stock_transfer_items_spare_part_id_foreign` (`spare_part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `stock_transfer_items`
--

TRUNCATE TABLE `stock_transfer_items`;
-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit_limit` decimal(12,2) DEFAULT NULL,
  `payment_terms` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_details` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_supplier_code_unique` (`supplier_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `suppliers`
--

TRUNCATE TABLE `suppliers`;
-- --------------------------------------------------------

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `units`
--

TRUNCATE TABLE `units`;
--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `short_code`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Piece', 'pcs', 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(2, 'Set', 'set', 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(3, 'Pair', 'pr', 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(4, 'Box', 'box', 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(5, 'Kit', 'kit', 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(6, 'Liter', 'ltr', 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10'),
(7, 'Meter', 'm', 1, '2026-08-27 01:42:10', '2026-08-27 01:42:10');

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
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
-- --------------------------------------------------------

--
-- Table structure for table `vehicle_makes`
--

DROP TABLE IF EXISTS `vehicle_makes`;
CREATE TABLE IF NOT EXISTS `vehicle_makes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_makes_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `vehicle_makes`
--

TRUNCATE TABLE `vehicle_makes`;
-- --------------------------------------------------------

--
-- Table structure for table `vehicle_models`
--

DROP TABLE IF EXISTS `vehicle_models`;
CREATE TABLE IF NOT EXISTS `vehicle_models` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_make_id` bigint UNSIGNED NOT NULL,
  `vehicle_type_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_models_vehicle_make_id_name_unique` (`vehicle_make_id`,`name`),
  KEY `vehicle_models_vehicle_type_id_foreign` (`vehicle_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `vehicle_models`
--

TRUNCATE TABLE `vehicle_models`;
-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

DROP TABLE IF EXISTS `vehicle_types`;
CREATE TABLE IF NOT EXISTS `vehicle_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_types_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `vehicle_types`
--

TRUNCATE TABLE `vehicle_types`;
-- --------------------------------------------------------

--
-- Table structure for table `vehicle_variants`
--

DROP TABLE IF EXISTS `vehicle_variants`;
CREATE TABLE IF NOT EXISTS `vehicle_variants` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint UNSIGNED NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `generation` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_capacity` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuel_type` enum('Petrol','Diesel','Hybrid','Electric','CNG','LPG') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transmission` enum('Manual','Automatic','CVT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drive_type` enum('FWD','RWD','AWD','4WD') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_year` smallint UNSIGNED DEFAULT NULL,
  `end_year` smallint UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_variants_vehicle_model_id_foreign` (`vehicle_model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `vehicle_variants`
--

TRUNCATE TABLE `vehicle_variants`;
-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouses_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `warehouses`
--

TRUNCATE TABLE `warehouses`;
--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `code`, `manager`, `contact_number`, `address`, `city`, `country`, `is_default`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Main Warehouse', 'WH-MAIN', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-08-27 01:42:09', '2026-08-27 01:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_zones`
--

DROP TABLE IF EXISTS `warehouse_zones`;
CREATE TABLE IF NOT EXISTS `warehouse_zones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_zones_warehouse_id_foreign` (`warehouse_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `warehouse_zones`
--

TRUNCATE TABLE `warehouse_zones`;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
