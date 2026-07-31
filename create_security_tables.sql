USE ventas_2026;

-- Tabla de Registro de Auditoría (Audit Logs)
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `username` VARCHAR(100) NULL,
  `module` VARCHAR(50) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `old_values` TEXT NULL,
  `new_values` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_module` (`module`),
  INDEX `idx_action` (`action`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Añadir columna supervisor_pin a la tabla users si no existe
SET @dbname = DATABASE();
SET @tablename = "users";
SET @columnname = "supervisor_pin";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE users ADD COLUMN supervisor_pin VARCHAR(255) DEFAULT '1234' AFTER password;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Tabla para Control de Intentos Fallidos de Inicio de Sesión
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `attempts` INT DEFAULT 1,
  `last_attempt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_ip_user` (`ip_address`, `username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
