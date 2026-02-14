-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2026 at 08:18 PM
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
-- Database: `ventas_2026`
--
USE `ventas_2026`;
-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `document` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `customer_payments` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','check','card') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(500) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `inventory_adjustments` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `adjustment_type` enum('increase','decrease') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_adjustments`
--

INSERT INTO `inventory_adjustments` (`id`, `product_id`, `warehouse_id`, `user_id`, `adjustment_type`, `quantity`, `previous_stock`, `new_stock`, `reason`, `notes`, `created_at`) VALUES
(7, 7, 1, 5, 'decrease', 2, 10, 8, 'Otro', 'prueba', '2026-02-12 20:18:53'),
(8, 7, 1, 5, 'increase', 2, 8, 10, 'Otro', '', '2026-02-12 21:25:53'),
(9, 7, 2, 5, 'increase', 1, 0, 1, 'Otro', '', '2026-02-12 22:15:32'),
(10, 7, 1, 5, 'decrease', 1, 6, 5, 'Inventario físico', '', '2026-02-13 18:41:54'),
(11, 7, 2, 5, 'increase', 2, 1, 3, 'Producto vencido', '', '2026-02-13 21:09:08'),
(12, 7, 1, 6, 'decrease', 2, 7, 5, 'Corrección de error', '', '2026-02-13 21:31:23');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '20260118221900', 'App\\Database\\Migrations\\AddUpdatedAtToCategories', 'default', 'App', 1768774795, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL,
  `min_sale_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `code`, `name`, `description`, `cost_price`, `price`, `min_sale_price`, `stock`, `created_at`, `updated_at`) VALUES
(1, 1, 'PROD001', 'Mouse Inalámbrico', 'Mouse inalámbrico USB', 0.00, 15.99, 0.00, 55, '2025-12-28 20:21:42', '2026-02-12 22:23:59'),
(2, 1, 'PROD002', 'Teclado USB', 'Teclado estándar USB', 0.00, 25.50, 0.00, 32, '2025-12-28 20:21:42', '2026-02-12 22:23:59'),
(3, 2, 'PROD003', 'Arroz 1kg', 'Arroz blanco premium', 1.00, 50.00, 25.00, 100, '2025-12-28 20:21:42', '2025-12-29 03:54:37'),
(4, 3, 'PROD004', 'Coca Cola 2L', 'Bebida gaseosa', 0.00, 3.00, 0.00, 80, '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
(5, 4, 'PROD005', 'Detergente 500ml', 'Detergente líquido', 0.00, 4.50, 0.00, 60, '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
(7, 4, 'PROD006', 'Para pruebass', '', 5000.00, 10000.00, 7000.00, 15, '2026-02-12 18:31:42', '2026-02-13 21:37:03');

-- --------------------------------------------------------

--
-- Table structure for table `product_stock`
--

CREATE TABLE `product_stock` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_stock`
--

INSERT INTO `product_stock` (`id`, `product_id`, `warehouse_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 50, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(2, 2, 1, 30, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(3, 3, 1, 100, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(4, 4, 1, 80, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(5, 5, 1, 60, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(8, 7, 1, 13, '2026-02-12 18:31:42', '2026-02-13 21:37:03'),
(13, 7, 2, 2, '2026-02-12 21:22:41', '2026-02-13 21:10:59'),
(14, 1, 2, 5, '2026-02-12 22:23:59', '2026-02-12 22:23:59'),
(15, 2, 2, 2, '2026-02-12 22:23:59', '2026-02-12 22:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `user_id`, `warehouse_id`, `purchase_number`, `date`, `payment_type`, `subtotal`, `tax`, `total`, `status`, `created_at`) VALUES
(1, 1, 5, 2, 'C-000001', '2026-02-12', 'cash', 30000.00, 0.00, 30000.00, 'paid', '2026-02-12 21:22:41'),
(2, 1, 5, 1, 'C-000002', '2026-02-12', 'cash', 40000.00, 0.00, 40000.00, 'paid', '2026-02-12 21:27:08'),
(3, 2, 5, 2, 'C-000003', '2026-02-12', 'cash', 10000.00, 0.00, 10000.00, 'paid', '2026-02-12 21:27:18'),
(5, 1, 5, 2, 'C-000004', '2026-02-12', 'cash', 130.95, 0.00, 130.95, 'paid', '2026-02-12 22:23:59'),
(7, 2, 6, 1, 'C-000005', '2026-02-13', 'cash', 100000.00, 0.00, 100000.00, 'paid', '2026-02-13 21:37:03');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_details`
--

INSERT INTO `purchase_details` (`id`, `purchase_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 7, 3, 10000.00, 30000.00),
(2, 2, 7, 4, 10000.00, 40000.00),
(3, 3, 7, 1, 10000.00, 10000.00),
(5, 5, 1, 5, 15.99, 79.95),
(6, 5, 2, 2, 25.50, 51.00),
(8, 7, 7, 10, 10000.00, 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0 COMMENT 'Roles del sistema no se pueden eliminar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL COMMENT 'Nombre del módulo del sistema',
  `can_view` char(1) DEFAULT 'N' COMMENT 'Permiso para consultar (S/N)',
  `can_insert` char(1) DEFAULT 'N' COMMENT 'Permiso para insertar (S/N)',
  `can_update` char(1) DEFAULT 'N' COMMENT 'Permiso para modificar (S/N)',
  `can_delete` char(1) DEFAULT 'N' COMMENT 'Permiso para eliminar (S/N)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(8, 1, 6, 1, 'V-000008', '2026-02-13', 'cash', 20000.00, 0.00, 20000.00, 'paid', '2026-02-13 21:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 7, 1, 10000.00, 10000.00),
(2, 2, 7, 5, 10000.00, 50000.00),
(3, 3, 7, 2, 10000.00, 20000.00),
(4, 4, 7, 1, 10000.00, 10000.00),
(5, 5, 7, 1, 10000.00, 10000.00),
(7, 7, 7, 1, 10000.00, 10000.00),
(8, 8, 7, 2, 10000.00, 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `document` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `supplier_payments` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','check','card') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `created_at`, `updated_at`) VALUES
(5, 'admin', 'admin@nacho.com', '$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC', 1, '2025-12-28 21:08:31', '2026-02-13 15:27:42'),
(6, 'vendedor1', 'ventas@nacho.com', '$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC', 2, '2025-12-28 21:08:31', '2026-02-13 15:27:42');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `description`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Depósito Central', 'Depósito principal', 'Dirección Principal', 1, '2026-02-12 13:54:54', '2026-02-12 13:54:54'),
(2, 'Deposito amarillo', 'Deposito amarillo', 'Deposito amarillo', 1, '2026-02-12 15:25:37', '2026-02-12 15:25:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_document` (`document`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale` (`sale_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_date` (`payment_date`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_date` (`created_at`),
  ADD KEY `inventory_adjustments_warehouse_fk` (`warehouse_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `product_stock`
--
ALTER TABLE `product_stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stock` (`product_id`,`warehouse_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_warehouse` (`warehouse_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_number` (`purchase_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_purchase_number` (`purchase_number`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_supplier` (`supplier_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `purchases_warehouse_fk` (`warehouse_id`);

--
-- Indexes for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_purchase` (`purchase_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_module` (`role_id`,`module`),
  ADD KEY `idx_role` (`role_id`),
  ADD KEY `idx_module` (`module`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_number` (`sale_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_sale_number` (`sale_number`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `sales_warehouse_fk` (`warehouse_id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale` (`sale_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_document` (`document`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_purchase` (`purchase_id`),
  ADD KEY `idx_supplier` (`supplier_id`),
  ADD KEY `idx_date` (`payment_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_stock`
--
ALTER TABLE `product_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
