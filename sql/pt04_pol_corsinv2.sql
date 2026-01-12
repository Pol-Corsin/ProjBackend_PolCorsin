CREATE DATABASE IF NOT EXISTS pt04_pol_corsinv2;
USE pt04_pol_corsinv2;

CREATE TABLE
    `users` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM ('user', 'admin') NOT NULL DEFAULT 'user',
        `creation_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE
    `articles` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(200) NOT NULL,
        `content` TEXT NOT NULL,
        `user_id` INT NULL,
        `creation_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
    );

CREATE TABLE 
    `tokens_recuperacio` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `token` VARCHAR(255) NOT NULL UNIQUE,
        `expiration_date` TIMESTAMP NOT NULL,
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    );

CREATE TABLE 
    `remember_me_tokens` (
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