CREATE TABLE IF NOT EXISTS `cash_register_sessions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `opening_time` DATETIME NOT NULL,
  `closing_time` DATETIME DEFAULT NULL,
  `opening_amount` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `closing_amount` DECIMAL(15,2) DEFAULT NULL,
  `discrepancy` DECIMAL(15,2) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'open',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_cash_sessions_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cash_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
