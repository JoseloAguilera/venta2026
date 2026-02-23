-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 23, 2026 at 06:26 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ventas_2026`
--
CREATE DATABASE IF NOT EXISTS `ventas_2026` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ventas_2026`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Electrónica', 'Productos electrónicos y tecnología en general', '2025-12-28 20:21:42', '2026-02-12 18:44:03'),
(2, 'Alimentos', 'Productos alimenticios', '2025-12-28 20:21:42', NULL),
(3, 'Bebidas', 'Bebidas en general', '2025-12-28 20:21:42', NULL),
(4, 'Limpieza', 'Productos de limpieza', '2025-12-28 20:21:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `document` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_document` (`document`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `document`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Juan Pérezz', '12345678', '555-0001', 'juan@email.com', 'Calle Principal 123', '2025-12-28 20:21:42', '2026-02-13 18:42:28'),
(2, 'María García', '87654321', '555-0002', 'maria@email.com', 'Avenida Central 456', '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
(3, 'Carlos López', '11223344', '555-0003', 'carlos@email.com', 'Plaza Mayor 789', '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
(4, 'Luz Escobar Miranda', '4664205', '0994967023', 'luz@apolo.com.pyy', 'km 6', '2026-02-12 21:49:23', '2026-02-13 21:31:48');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE IF NOT EXISTS `customer_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','check','card') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(500) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`),
  KEY `idx_category` (`category_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Servicios', 'Luz, agua, internet, teléfono', '2025-12-28 23:31:48'),
(2, 'Alquiler', 'Alquiler de local o depósito', '2025-12-28 23:31:48'),
(3, 'Sueldos', 'Pago de salarios y cargas sociales', '2025-12-28 23:31:48'),
(4, 'Impuestos', 'Impuestos y tasas municipales', '2025-12-28 23:31:48'),
(5, 'Mantenimiento', 'Reparaciones y mantenimiento', '2025-12-28 23:31:48'),
(6, 'Transporte', 'Combustible, fletes, envíos', '2025-12-28 23:31:48'),
(7, 'Publicidad', 'Marketing y publicidad', '2025-12-28 23:31:48'),
(8, 'Insumos', 'Materiales de oficina y limpieza', '2025-12-28 23:31:48'),
(9, 'Otros', 'Gastos varios', '2025-12-28 23:31:48');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_adjustments`
--

CREATE TABLE IF NOT EXISTS `inventory_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `adjustment_type` enum('increase','decrease') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_date` (`created_at`),
  KEY `inventory_adjustments_warehouse_fk` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_adjustments`
--

INSERT INTO `inventory_adjustments` (`id`, `product_id`, `warehouse_id`, `user_id`, `adjustment_type`, `quantity`, `previous_stock`, `new_stock`, `reason`, `notes`, `created_at`) VALUES
(7, 7, 1, 5, 'decrease', 2, 10, 8, 'Otro', 'prueba', '2026-02-12 20:18:53'),
(8, 7, 1, 5, 'increase', 2, 8, 10, 'Otro', '', '2026-02-12 21:25:53'),
(9, 7, 2, 5, 'increase', 1, 0, 1, 'Otro', '', '2026-02-12 22:15:32'),
(10, 7, 1, 5, 'decrease', 1, 6, 5, 'Inventario físico', '', '2026-02-13 18:41:54'),
(11, 7, 2, 5, 'increase', 2, 1, 3, 'Producto vencido', '', '2026-02-13 21:09:08'),
(12, 7, 1, 6, 'decrease', 2, 7, 5, 'Corrección de error', '', '2026-02-13 21:31:23'),
(13, 12, 1, 7, 'increase', 10, 0, 10, 'Inventario físico', '', '2026-02-18 14:51:40');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transfers`
--

CREATE TABLE IF NOT EXISTS `inventory_transfers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transfer_code` varchar(50) NOT NULL,
  `source_warehouse_id` int(11) NOT NULL,
  `destination_warehouse_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('completed','pending','cancelled') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transfer_code` (`transfer_code`),
  KEY `inventory_transfers_source_warehouse_id_foreign` (`source_warehouse_id`),
  KEY `inventory_transfers_destination_warehouse_id_foreign` (`destination_warehouse_id`),
  KEY `inventory_transfers_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_transfers`
--

INSERT INTO `inventory_transfers` (`id`, `transfer_code`, `source_warehouse_id`, `destination_warehouse_id`, `user_id`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'TR-2026-63236', 1, 2, 5, 'completed', '', '2026-02-16 04:13:26', '2026-02-16 04:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transfer_items`
--

CREATE TABLE IF NOT EXISTS `inventory_transfer_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_transfer_items_transfer_id_foreign` (`transfer_id`),
  KEY `inventory_transfer_items_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_transfer_items`
--

INSERT INTO `inventory_transfer_items` (`id`, `transfer_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 1, '2026-02-16 04:13:26', '2026-02-16 04:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '20260118221900', 'App\\Database\\Migrations\\AddUpdatedAtToCategories', 'default', 'App', 1768774795, 1),
(2, '2026-02-14-120837', 'App\\Database\\Migrations\\AddImeiToProducts', 'default', 'App', 1771215040, 2),
(3, '2026-02-16-120900', 'App\\Database\\Migrations\\CreateInventoryTransfersTables', 'default', 'App', 1771215130, 3),
(4, '2026-02-18-112000', 'App\\Database\\Migrations\\AddActiveToUsers', 'default', 'App', 1771424259, 4),
(5, '2026-02-18-120000', 'App\\Database\\Migrations\\AddDescriptionToSaleDetails', 'default', 'App', 1771425598, 5);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL,
  `min_sale_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `imei1` varchar(50) DEFAULT NULL,
  `imei2` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_name` (`name`),
  KEY `idx_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `code`, `name`, `description`, `cost_price`, `price`, `min_sale_price`, `stock`, `created_at`, `updated_at`, `deleted_at`, `imei1`, `imei2`) VALUES
(1, 1, 'PROD001_del_1771200320', 'Mouse Inalámbrico', 'Mouse inalámbrico USB', 0.00, 15.99, 0.00, 55, '2025-12-28 20:21:42', '2026-02-16 03:05:20', '2026-02-16 03:05:20', NULL, NULL),
(2, 1, 'PROD002', 'Teclado USB', 'Teclado estándar USB', 0.00, 25.50, 0.00, 31, '2025-12-28 20:21:42', '2026-02-16 11:28:25', NULL, NULL, NULL),
(3, 2, 'PROD003', 'Arroz 1kg', 'Arroz blanco premium', 1.00, 50.00, 25.00, 100, '2025-12-28 20:21:42', '2025-12-29 03:54:37', NULL, NULL, NULL),
(4, 3, 'PROD004', 'Coca Cola 2L', 'Bebida gaseosa', 0.00, 3.00, 0.00, 80, '2025-12-28 20:21:42', '2026-02-16 07:58:39', NULL, NULL, NULL),
(5, 4, 'PROD005', 'Detergente 500ml', 'Detergente líquido', 0.00, 4.50, 0.00, 60, '2025-12-28 20:21:42', '2026-02-16 07:13:26', NULL, NULL, NULL),
(7, 4, 'PROD006', 'Para pruebass', '', 5000.00, 10000.00, 7000.00, 15, '2026-02-12 18:31:42', '2026-02-13 21:37:03', NULL, NULL, NULL),
(9, 1, 'PROD007', 'iphone 11', 'asdasdd', 200.00, 400.00, 300.00, 11, '2026-02-16 06:54:23', '2026-02-18 14:59:08', NULL, '2342342343', '23423423432'),
(10, 1, '001', 'iphone 12 128 gb - blanco', '', 300.00, 350.00, 345.00, 10, '2026-02-18 14:50:06', '2026-02-18 14:50:06', NULL, NULL, NULL),
(11, 1, '002', 'iphone 13 128 gb - rosa', '', 300.00, 350.00, 345.00, 10, '2026-02-18 14:50:36', '2026-02-18 14:50:36', NULL, NULL, NULL),
(12, 1, '003', 'iphone 17 pro max - orange', '', 1250.00, 1300.00, 1280.00, 9, '2026-02-18 14:51:08', '2026-02-18 14:55:12', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_stock`
--

CREATE TABLE IF NOT EXISTS `product_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_stock` (`product_id`,`warehouse_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_warehouse` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_stock`
--

INSERT INTO `product_stock` (`id`, `product_id`, `warehouse_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 50, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(2, 2, 1, 29, '2026-02-12 13:54:54', '2026-02-16 11:28:25'),
(3, 3, 1, 100, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(4, 4, 1, 80, '2026-02-12 13:54:54', '2026-02-16 07:58:39'),
(5, 5, 1, 59, '2026-02-12 13:54:54', '2026-02-16 07:13:26'),
(8, 7, 1, 13, '2026-02-12 18:31:42', '2026-02-13 21:37:03'),
(13, 7, 2, 2, '2026-02-12 21:22:41', '2026-02-13 21:10:59'),
(14, 1, 2, 5, '2026-02-12 22:23:59', '2026-02-12 22:23:59'),
(15, 2, 2, 2, '2026-02-12 22:23:59', '2026-02-12 22:23:59'),
(17, 9, 1, 11, '2026-02-16 06:54:23', '2026-02-18 14:59:08'),
(18, 5, 2, 1, '2026-02-16 07:13:26', '2026-02-16 07:13:26'),
(19, 10, 1, 10, '2026-02-18 14:50:06', '2026-02-18 14:50:06'),
(20, 11, 1, 10, '2026-02-18 14:50:36', '2026-02-18 14:50:36'),
(21, 12, 1, 9, '2026-02-18 14:51:40', '2026-02-18 14:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `purchase_number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `payment_type` enum('cash','credit') NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','partial','paid','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_number` (`purchase_number`),
  KEY `user_id` (`user_id`),
  KEY `idx_purchase_number` (`purchase_number`),
  KEY `idx_date` (`date`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_status` (`status`),
  KEY `purchases_warehouse_fk` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `user_id`, `warehouse_id`, `purchase_number`, `date`, `payment_type`, `subtotal`, `tax`, `total`, `status`, `created_at`) VALUES
(1, 1, 5, 2, 'C-000001', '2026-02-12', 'cash', 30000.00, 0.00, 30000.00, 'paid', '2026-02-12 21:22:41'),
(2, 1, 5, 1, 'C-000002', '2026-02-12', 'cash', 40000.00, 0.00, 40000.00, 'paid', '2026-02-12 21:27:08'),
(3, 2, 5, 2, 'C-000003', '2026-02-12', 'cash', 10000.00, 0.00, 10000.00, 'paid', '2026-02-12 21:27:18'),
(5, 1, 5, 2, 'C-000004', '2026-02-12', 'cash', 130.95, 0.00, 130.95, 'paid', '2026-02-12 22:23:59'),
(7, 2, 6, 1, 'C-000005', '2026-02-13', 'cash', 100000.00, 0.00, 100000.00, 'paid', '2026-02-13 21:37:03'),
(8, 1, 5, 1, 'C-000006', '2026-02-16', 'cash', 200.00, 0.00, 200.00, 'paid', '2026-02-16 09:56:27'),
(9, 1, 7, 1, 'C-000007', '2026-02-18', 'cash', 3000.00, 0.00, 3000.00, 'paid', '2026-02-18 14:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE IF NOT EXISTS `purchase_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase` (`purchase_id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_details`
--

INSERT INTO `purchase_details` (`id`, `purchase_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 7, 3, 10000.00, 30000.00),
(2, 2, 7, 4, 10000.00, 40000.00),
(3, 3, 7, 1, 10000.00, 10000.00),
(5, 5, 1, 5, 15.99, 79.95),
(6, 5, 2, 2, 25.50, 51.00),
(8, 7, 7, 10, 10000.00, 100000.00),
(9, 8, 9, 1, 200.00, 200.00),
(10, 9, 9, 10, 300.00, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0 COMMENT 'Roles del sistema no se pueden eliminar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'Acceso total al sistema con todos los permisos', 1, '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(2, 'Ventas', 'Acceso a módulos de ventas y operaciones básicas', 0, '2026-02-13 15:27:42', '2026-02-13 21:47:11');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL COMMENT 'Nombre del módulo del sistema',
  `can_view` char(1) DEFAULT 'N' COMMENT 'Permiso para consultar (S/N)',
  `can_insert` char(1) DEFAULT 'N' COMMENT 'Permiso para insertar (S/N)',
  `can_update` char(1) DEFAULT 'N' COMMENT 'Permiso para modificar (S/N)',
  `can_delete` char(1) DEFAULT 'N' COMMENT 'Permiso para eliminar (S/N)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_module` (`role_id`,`module`),
  KEY `idx_role` (`role_id`),
  KEY `idx_module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `module`, `can_view`, `can_insert`, `can_update`, `can_delete`, `created_at`, `updated_at`) VALUES
(1, 1, 'dashboard', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(2, 1, 'categories', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(3, 1, 'products', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(4, 1, 'product_stock', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(5, 1, 'inventory_adjustments', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(6, 1, 'customers', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(7, 1, 'suppliers', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(8, 1, 'sales', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(9, 1, 'purchases', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(10, 1, 'collections', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(11, 1, 'payments', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(12, 1, 'expenses', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(13, 1, 'settings', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(14, 1, 'roles', 'S', 'S', 'S', 'S', '2026-02-13 15:27:42', '2026-02-13 15:27:42'),
(29, 2, 'dashboard', 'S', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(30, 2, 'categories', 'S', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(31, 2, 'products', 'S', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(32, 2, 'product_stock', 'S', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(33, 2, 'inventory_adjustments', 'S', 'S', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(34, 2, 'customers', 'S', 'S', 'S', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(35, 2, 'suppliers', 'S', 'S', 'S', 'S', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(36, 2, 'sales', 'S', 'S', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(37, 2, 'purchases', 'S', 'S', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(38, 2, 'collections', 'S', 'S', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(39, 2, 'payments', 'S', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(40, 2, 'expenses', 'N', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(41, 2, 'settings', 'N', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12'),
(42, 2, 'roles', 'N', 'N', 'N', 'N', '2026-02-13 21:47:12', '2026-02-13 21:47:12');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `sale_number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `payment_type` enum('cash','credit') NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','partial','paid','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sale_number` (`sale_number`),
  KEY `user_id` (`user_id`),
  KEY `idx_sale_number` (`sale_number`),
  KEY `idx_date` (`date`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_status` (`status`),
  KEY `sales_warehouse_fk` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `user_id`, `warehouse_id`, `sale_number`, `date`, `payment_type`, `subtotal`, `tax`, `total`, `status`, `created_at`) VALUES
(1, 1, 5, 2, 'V-000001', '2026-02-12', 'cash', 10000.00, 0.00, 10000.00, 'paid', '2026-02-12 21:23:36'),
(2, 1, 5, 1, 'V-000002', '2026-02-12', 'cash', 50000.00, 0.00, 50000.00, 'paid', '2026-02-12 21:26:22'),
(3, 3, 5, 2, 'V-000003', '2026-02-12', 'cash', 20000.00, 0.00, 20000.00, 'paid', '2026-02-12 21:26:45'),
(4, 1, 5, 1, 'V-000004', '2026-02-12', 'cash', 10000.00, 0.00, 10000.00, 'paid', '2026-02-12 22:07:00'),
(5, 1, 5, 2, 'V-000005', '2026-02-12', 'cash', 10000.00, 0.00, 10000.00, 'paid', '2026-02-12 22:07:29'),
(7, 4, 5, 2, 'V-000007', '2026-02-13', 'cash', 10000.00, 0.00, 10000.00, 'paid', '2026-02-13 21:09:51'),
(8, 1, 6, 1, 'V-000008', '2026-02-13', 'cash', 20000.00, 0.00, 20000.00, 'paid', '2026-02-13 21:35:31'),
(9, 1, 5, 1, 'V-000009', '2026-02-16', 'cash', 15.00, 0.00, 15.00, 'cancelled', '2026-02-16 07:58:10'),
(10, 1, 5, 1, 'V-000010', '2026-02-16', 'cash', 25.50, 0.00, 25.50, 'paid', '2026-02-16 11:28:25'),
(11, 3, 5, 1, 'V-000011', '2026-02-16', 'cash', 400.00, 0.00, 400.00, 'paid', '2026-02-16 09:50:00'),
(12, 4, 7, 1, 'V-000012', '2026-02-18', 'cash', 1300.00, 0.00, 1300.00, 'paid', '2026-02-18 14:55:12'),
(13, 1, 7, 1, 'V-000013', '2026-02-18', 'cash', 4000.00, 0.00, 4000.00, 'cancelled', '2026-02-18 14:58:19');

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE IF NOT EXISTS `sale_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `subtotal`, `description`) VALUES
(1, 1, 7, 1, 10000.00, 10000.00, NULL),
(2, 2, 7, 5, 10000.00, 50000.00, NULL),
(3, 3, 7, 2, 10000.00, 20000.00, NULL),
(4, 4, 7, 1, 10000.00, 10000.00, NULL),
(5, 5, 7, 1, 10000.00, 10000.00, NULL),
(7, 7, 7, 1, 10000.00, 10000.00, NULL),
(8, 8, 7, 2, 10000.00, 20000.00, NULL),
(9, 9, 4, 5, 3.00, 15.00, NULL),
(10, 10, 2, 1, 25.50, 25.50, NULL),
(11, 11, 9, 1, 400.00, 400.00, NULL),
(12, 12, 12, 1, 1300.00, 1300.00, 'IMEI 1:359802898510223\r\n\r\nIMEI 2:359802897316051\r\n\r\ncliente lleva sin caja'),
(13, 13, 9, 10, 400.00, 4000.00, 'IMEI: 359802898510223\r\nimei |: 359802897316051\r\n359802897316051\r\n359802897316051\r\n195950638004\r\n195950638004\r\n359802897316051\r\n359802897316051\r\n195950638004\r\n195950638004\r\n195950638004\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'Mi Empresa S.A.', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
(2, 'company_ruc', '12345678-9', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
(3, 'company_address', 'Av. Principal 123', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
(4, 'company_email', 'contacto@miempresa.com', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
(5, 'company_phone', '+595 981 123 456', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
(6, 'min_price_password', '0000', '2025-12-29 00:33:14', '2025-12-29 00:33:14');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `document` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_document` (`document`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `document`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Distribuidora Tech S.A.', '20123456789', '555-1001', 'ventas@tech.com', 'Zona Industrial 100', '2025-12-28 20:21:42', '2026-02-13 21:09:35'),
(2, 'Alimentos del Sur', '20987654321', '555-1002', 'info@alimentos.com', 'Mercado Central 200', '2025-12-28 20:21:42', '2025-12-28 20:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE IF NOT EXISTS `supplier_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','check','card') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_purchase` (`purchase_id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `fk_users_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `active`, `created_at`, `updated_at`) VALUES
(5, 'admin', 'admin@nacho.com', '$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC', 1, 1, '2025-12-28 21:08:31', '2026-02-13 15:27:42'),
(6, 'vendedor1', 'ventas@nacho.com', '$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC', 2, 1, '2025-12-28 21:08:31', '2026-02-13 15:27:42'),
(7, 'jose', 'joseaguilera1709@gmail.com', '$2y$10$GBzkEx1FQAmsFXszM6yiQ.j2WLQm7GwvtTbj6Yk8cynJbCOedgJ4q', 1, 1, '2026-02-18 14:31:32', '2026-02-18 14:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `description`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Depósito Central', 'Depósito principal', 'Dirección Principal', 1, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(2, 'Deposito amarillo', 'Deposito amarillo', 'Deposito amarillo', 1, '2026-02-12 15:25:37', '2026-02-12 15:25:37');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `customer_payments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  ADD CONSTRAINT `inventory_adjustments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_adjustments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `inventory_adjustments_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `inventory_transfers`
--
ALTER TABLE `inventory_transfers`
  ADD CONSTRAINT `inventory_transfers_destination_warehouse_id_foreign` FOREIGN KEY (`destination_warehouse_id`) REFERENCES `warehouses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transfers_source_warehouse_id_foreign` FOREIGN KEY (`source_warehouse_id`) REFERENCES `warehouses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transfers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventory_transfer_items`
--
ALTER TABLE `inventory_transfer_items`
  ADD CONSTRAINT `inventory_transfer_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transfer_items_transfer_id_foreign` FOREIGN KEY (`transfer_id`) REFERENCES `inventory_transfers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_stock`
--
ALTER TABLE `product_stock`
  ADD CONSTRAINT `product_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_stock_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `purchase_details_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sales_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  ADD CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
