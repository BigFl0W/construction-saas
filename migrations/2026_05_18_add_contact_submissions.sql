-- Adds public contact submissions and admin replies for older installs.

CREATE TABLE IF NOT EXISTS `contact_submissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('unread', 'read', 'replied', 'archived') NOT NULL DEFAULT 'unread',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contact_replies` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `submission_id` INT UNSIGNED NOT NULL,
    `admin_user_id` INT(11) DEFAULT NULL,
    `reply_message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`submission_id`) REFERENCES `contact_submissions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
