-- Crear tabla para Remember Me tokens
CREATE TABLE IF NOT EXISTS `remember_me_tokens` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `token` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    `user_agent` VARCHAR(500),
    `ip_address` VARCHAR(45),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX (`token`),
    INDEX (`expires_at`)
);
