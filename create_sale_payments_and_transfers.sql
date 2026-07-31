CREATE TABLE IF NOT EXISTS `sale_payments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` INT(11) UNSIGNED NOT NULL,
  `account_id` INT(11) UNSIGNED NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `fk_sale_payments_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sale_payments_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
