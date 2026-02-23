-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: ventas_2026
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Electrónica','Productos electrónicos y tecnología en general','2025-12-28 20:21:42','2026-02-12 18:44:03'),(2,'Alimentos','Productos alimenticios','2025-12-28 20:21:42',NULL),(3,'Bebidas','Bebidas en general','2025-12-28 20:21:42',NULL),(4,'Limpieza','Productos de limpieza','2025-12-28 20:21:42',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_payments`
--

DROP TABLE IF EXISTS `customer_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_payments` (
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
  KEY `idx_date` (`payment_date`),
  CONSTRAINT `customer_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `customer_payments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_payments`
--

LOCK TABLES `customer_payments` WRITE;
/*!40000 ALTER TABLE `customer_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Juan Pérezz','12345678','555-0001','juan@email.com','Calle Principal 123','2025-12-28 20:21:42','2026-02-13 18:42:28'),(2,'María García','87654321','555-0002','maria@email.com','Avenida Central 456','2025-12-28 20:21:42','2025-12-28 20:21:42'),(3,'Carlos López','11223344','555-0003','carlos@email.com','Plaza Mayor 789','2025-12-28 20:21:42','2025-12-28 20:21:42'),(4,'Luz Escobar Miranda','4664205','0994967023','luz@apolo.com.pyy','km 6','2026-02-12 21:49:23','2026-02-13 21:31:48');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,'Servicios','Luz, agua, internet, teléfono','2025-12-28 23:31:48'),(2,'Alquiler','Alquiler de local o depósito','2025-12-28 23:31:48'),(3,'Sueldos','Pago de salarios y cargas sociales','2025-12-28 23:31:48'),(4,'Impuestos','Impuestos y tasas municipales','2025-12-28 23:31:48'),(5,'Mantenimiento','Reparaciones y mantenimiento','2025-12-28 23:31:48'),(6,'Transporte','Combustible, fletes, envíos','2025-12-28 23:31:48'),(7,'Publicidad','Marketing y publicidad','2025-12-28 23:31:48'),(8,'Insumos','Materiales de oficina y limpieza','2025-12-28 23:31:48'),(9,'Otros','Gastos varios','2025-12-28 23:31:48');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
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
  KEY `idx_user` (`user_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_adjustments`
--

DROP TABLE IF EXISTS `inventory_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_adjustments` (
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
  KEY `inventory_adjustments_warehouse_fk` (`warehouse_id`),
  CONSTRAINT `inventory_adjustments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `inventory_adjustments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `inventory_adjustments_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_adjustments`
--

LOCK TABLES `inventory_adjustments` WRITE;
/*!40000 ALTER TABLE `inventory_adjustments` DISABLE KEYS */;
INSERT INTO `inventory_adjustments` VALUES (7,7,1,5,'decrease',2,10,8,'Otro','prueba','2026-02-12 20:18:53'),(8,7,1,5,'increase',2,8,10,'Otro','','2026-02-12 21:25:53'),(9,7,2,5,'increase',1,0,1,'Otro','','2026-02-12 22:15:32'),(10,7,1,5,'decrease',1,6,5,'Inventario físico','','2026-02-13 18:41:54'),(11,7,2,5,'increase',2,1,3,'Producto vencido','','2026-02-13 21:09:08'),(12,7,1,6,'decrease',2,7,5,'Corrección de error','','2026-02-13 21:31:23');
/*!40000 ALTER TABLE `inventory_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_transfer_items`
--

DROP TABLE IF EXISTS `inventory_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_transfer_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) unsigned NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_transfer_items_transfer_id_foreign` (`transfer_id`),
  KEY `inventory_transfer_items_product_id_foreign` (`product_id`),
  CONSTRAINT `inventory_transfer_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_transfer_items_transfer_id_foreign` FOREIGN KEY (`transfer_id`) REFERENCES `inventory_transfers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_transfer_items`
--

LOCK TABLES `inventory_transfer_items` WRITE;
/*!40000 ALTER TABLE `inventory_transfer_items` DISABLE KEYS */;
INSERT INTO `inventory_transfer_items` VALUES (1,1,5,1,'2026-02-16 04:13:26','2026-02-16 04:13:26');
/*!40000 ALTER TABLE `inventory_transfer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_transfers`
--

DROP TABLE IF EXISTS `inventory_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_transfers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  KEY `inventory_transfers_user_id_foreign` (`user_id`),
  CONSTRAINT `inventory_transfers_destination_warehouse_id_foreign` FOREIGN KEY (`destination_warehouse_id`) REFERENCES `warehouses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_transfers_source_warehouse_id_foreign` FOREIGN KEY (`source_warehouse_id`) REFERENCES `warehouses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_transfers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_transfers`
--

LOCK TABLES `inventory_transfers` WRITE;
/*!40000 ALTER TABLE `inventory_transfers` DISABLE KEYS */;
INSERT INTO `inventory_transfers` VALUES (1,'TR-2026-63236',1,2,5,'completed','','2026-02-16 04:13:26','2026-02-16 04:13:26');
/*!40000 ALTER TABLE `inventory_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'20260118221900','App\\Database\\Migrations\\AddUpdatedAtToCategories','default','App',1768774795,1),(2,'2026-02-14-120837','App\\Database\\Migrations\\AddImeiToProducts','default','App',1771215040,2),(3,'2026-02-16-120900','App\\Database\\Migrations\\CreateInventoryTransfersTables','default','App',1771215130,3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_stock`
--

DROP TABLE IF EXISTS `product_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_stock` (`product_id`,`warehouse_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_warehouse` (`warehouse_id`),
  CONSTRAINT `product_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stock_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_stock`
--

LOCK TABLES `product_stock` WRITE;
/*!40000 ALTER TABLE `product_stock` DISABLE KEYS */;
INSERT INTO `product_stock` VALUES (1,1,1,50,'2026-02-12 13:54:54','2026-02-12 13:54:54'),(2,2,1,29,'2026-02-12 13:54:54','2026-02-16 11:28:25'),(3,3,1,100,'2026-02-12 13:54:54','2026-02-12 13:54:54'),(4,4,1,80,'2026-02-12 13:54:54','2026-02-16 07:58:39'),(5,5,1,59,'2026-02-12 13:54:54','2026-02-16 07:13:26'),(8,7,1,13,'2026-02-12 18:31:42','2026-02-13 21:37:03'),(13,7,2,2,'2026-02-12 21:22:41','2026-02-13 21:10:59'),(14,1,2,5,'2026-02-12 22:23:59','2026-02-12 22:23:59'),(15,2,2,2,'2026-02-12 22:23:59','2026-02-12 22:23:59'),(17,9,1,1,'2026-02-16 06:54:23','2026-02-16 09:56:27'),(18,5,2,1,'2026-02-16 07:13:26','2026-02-16 07:13:26');
/*!40000 ALTER TABLE `product_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
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
  KEY `idx_category` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'PROD001_del_1771200320','Mouse Inalámbrico','Mouse inalámbrico USB',0.00,15.99,0.00,55,'2025-12-28 20:21:42','2026-02-16 03:05:20','2026-02-16 03:05:20',NULL,NULL),(2,1,'PROD002','Teclado USB','Teclado estándar USB',0.00,25.50,0.00,31,'2025-12-28 20:21:42','2026-02-16 11:28:25',NULL,NULL,NULL),(3,2,'PROD003','Arroz 1kg','Arroz blanco premium',1.00,50.00,25.00,100,'2025-12-28 20:21:42','2025-12-29 03:54:37',NULL,NULL,NULL),(4,3,'PROD004','Coca Cola 2L','Bebida gaseosa',0.00,3.00,0.00,80,'2025-12-28 20:21:42','2026-02-16 07:58:39',NULL,NULL,NULL),(5,4,'PROD005','Detergente 500ml','Detergente líquido',0.00,4.50,0.00,60,'2025-12-28 20:21:42','2026-02-16 07:13:26',NULL,NULL,NULL),(7,4,'PROD006','Para pruebass','',5000.00,10000.00,7000.00,15,'2026-02-12 18:31:42','2026-02-13 21:37:03',NULL,NULL,NULL),(9,1,'PROD007','iphone 11','asdasdd',200.00,400.00,300.00,1,'2026-02-16 06:54:23','2026-02-16 09:56:27',NULL,'2342342343','23423423432');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_details`
--

DROP TABLE IF EXISTS `purchase_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase` (`purchase_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `purchase_details_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_details`
--

LOCK TABLES `purchase_details` WRITE;
/*!40000 ALTER TABLE `purchase_details` DISABLE KEYS */;
INSERT INTO `purchase_details` VALUES (1,1,7,3,10000.00,30000.00),(2,2,7,4,10000.00,40000.00),(3,3,7,1,10000.00,10000.00),(5,5,1,5,15.99,79.95),(6,5,2,2,25.50,51.00),(8,7,7,10,10000.00,100000.00),(9,8,9,1,200.00,200.00);
/*!40000 ALTER TABLE `purchase_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
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
  KEY `purchases_warehouse_fk` (`warehouse_id`),
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `purchases_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,1,5,2,'C-000001','2026-02-12','cash',30000.00,0.00,30000.00,'paid','2026-02-12 21:22:41'),(2,1,5,1,'C-000002','2026-02-12','cash',40000.00,0.00,40000.00,'paid','2026-02-12 21:27:08'),(3,2,5,2,'C-000003','2026-02-12','cash',10000.00,0.00,10000.00,'paid','2026-02-12 21:27:18'),(5,1,5,2,'C-000004','2026-02-12','cash',130.95,0.00,130.95,'paid','2026-02-12 22:23:59'),(7,2,6,1,'C-000005','2026-02-13','cash',100000.00,0.00,100000.00,'paid','2026-02-13 21:37:03'),(8,1,5,1,'C-000006','2026-02-16','cash',200.00,0.00,200.00,'paid','2026-02-16 09:56:27');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
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
  KEY `idx_module` (`module`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1,'dashboard','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(2,1,'categories','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(3,1,'products','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(4,1,'product_stock','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(5,1,'inventory_adjustments','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(6,1,'customers','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(7,1,'suppliers','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(8,1,'sales','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(9,1,'purchases','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(10,1,'collections','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(11,1,'payments','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(12,1,'expenses','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(13,1,'settings','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(14,1,'roles','S','S','S','S','2026-02-13 15:27:42','2026-02-13 15:27:42'),(29,2,'dashboard','S','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(30,2,'categories','S','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(31,2,'products','S','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(32,2,'product_stock','S','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(33,2,'inventory_adjustments','S','S','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(34,2,'customers','S','S','S','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(35,2,'suppliers','S','S','S','S','2026-02-13 21:47:12','2026-02-13 21:47:12'),(36,2,'sales','S','S','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(37,2,'purchases','S','S','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(38,2,'collections','S','S','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(39,2,'payments','S','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(40,2,'expenses','N','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(41,2,'settings','N','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12'),(42,2,'roles','N','N','N','N','2026-02-13 21:47:12','2026-02-13 21:47:12');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','Acceso total al sistema con todos los permisos',1,'2026-02-13 15:27:42','2026-02-13 15:27:42'),(2,'Ventas','Acceso a módulos de ventas y operaciones básicas',0,'2026-02-13 15:27:42','2026-02-13 21:47:11');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_details`
--

DROP TABLE IF EXISTS `sale_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_details`
--

LOCK TABLES `sale_details` WRITE;
/*!40000 ALTER TABLE `sale_details` DISABLE KEYS */;
INSERT INTO `sale_details` VALUES (1,1,7,1,10000.00,10000.00),(2,2,7,5,10000.00,50000.00),(3,3,7,2,10000.00,20000.00),(4,4,7,1,10000.00,10000.00),(5,5,7,1,10000.00,10000.00),(7,7,7,1,10000.00,10000.00),(8,8,7,2,10000.00,20000.00),(9,9,4,5,3.00,15.00),(10,10,2,1,25.50,25.50),(11,11,9,1,400.00,400.00);
/*!40000 ALTER TABLE `sale_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
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
  KEY `sales_warehouse_fk` (`warehouse_id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,1,5,2,'V-000001','2026-02-12','cash',10000.00,0.00,10000.00,'paid','2026-02-12 21:23:36'),(2,1,5,1,'V-000002','2026-02-12','cash',50000.00,0.00,50000.00,'paid','2026-02-12 21:26:22'),(3,3,5,2,'V-000003','2026-02-12','cash',20000.00,0.00,20000.00,'paid','2026-02-12 21:26:45'),(4,1,5,1,'V-000004','2026-02-12','cash',10000.00,0.00,10000.00,'paid','2026-02-12 22:07:00'),(5,1,5,2,'V-000005','2026-02-12','cash',10000.00,0.00,10000.00,'paid','2026-02-12 22:07:29'),(7,4,5,2,'V-000007','2026-02-13','cash',10000.00,0.00,10000.00,'paid','2026-02-13 21:09:51'),(8,1,6,1,'V-000008','2026-02-13','cash',20000.00,0.00,20000.00,'paid','2026-02-13 21:35:31'),(9,1,5,1,'V-000009','2026-02-16','cash',15.00,0.00,15.00,'cancelled','2026-02-16 07:58:10'),(10,1,5,1,'V-000010','2026-02-16','cash',25.50,0.00,25.50,'paid','2026-02-16 11:28:25'),(11,3,5,1,'V-000011','2026-02-16','cash',400.00,0.00,400.00,'paid','2026-02-16 09:50:00');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'company_name','Mi Empresa S.A.','2025-12-29 00:33:14','2025-12-29 00:33:14'),(2,'company_ruc','12345678-9','2025-12-29 00:33:14','2025-12-29 00:33:14'),(3,'company_address','Av. Principal 123','2025-12-29 00:33:14','2025-12-29 00:33:14'),(4,'company_email','contacto@miempresa.com','2025-12-29 00:33:14','2025-12-29 00:33:14'),(5,'company_phone','+595 981 123 456','2025-12-29 00:33:14','2025-12-29 00:33:14'),(6,'min_price_password','0000','2025-12-29 00:33:14','2025-12-29 00:33:14');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_payments`
--

DROP TABLE IF EXISTS `supplier_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_payments` (
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
  KEY `idx_date` (`payment_date`),
  CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_payments`
--

LOCK TABLES `supplier_payments` WRITE;
/*!40000 ALTER TABLE `supplier_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Distribuidora Tech S.A.','20123456789','555-1001','ventas@tech.com','Zona Industrial 100','2025-12-28 20:21:42','2026-02-13 21:09:35'),(2,'Alimentos del Sur','20987654321','555-1002','info@alimentos.com','Mercado Central 200','2025-12-28 20:21:42','2025-12-28 20:21:42');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `fk_users_role` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (5,'admin','admin@nacho.com','$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC',1,'2025-12-28 21:08:31','2026-02-13 15:27:42'),(6,'vendedor1','ventas@nacho.com','$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC',2,'2025-12-28 21:08:31','2026-02-13 15:27:42');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (1,'Depósito Central','Depósito principal','Dirección Principal',1,'2026-02-12 13:54:54','2026-02-12 13:54:54'),(2,'Deposito amarillo','Deposito amarillo','Deposito amarillo',1,'2026-02-12 15:25:37','2026-02-12 15:25:37');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-16  7:05:49
