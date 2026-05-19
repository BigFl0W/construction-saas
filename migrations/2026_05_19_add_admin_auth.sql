CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    profile_image VARCHAR(500),
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_admins_created_by FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_admins_username (username),
    INDEX idx_admins_email (email),
    INDEX idx_admins_status (status),
    INDEX idx_admins_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_admin_tokens_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_admin_tokens_token (token),
    INDEX idx_admin_tokens_admin (admin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @has_admin_id := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activity_logs'
      AND COLUMN_NAME = 'admin_id'
);
SET @add_admin_id_sql := IF(
    @has_admin_id = 0,
    'ALTER TABLE activity_logs ADD COLUMN admin_id INT NULL AFTER user_id',
    'SELECT 1'
);
PREPARE stmt FROM @add_admin_id_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_idx_activity_admin := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activity_logs'
      AND INDEX_NAME = 'idx_activity_admin'
);
SET @add_idx_activity_admin_sql := IF(
    @has_idx_activity_admin = 0,
    'ALTER TABLE activity_logs ADD INDEX idx_activity_admin (admin_id)',
    'SELECT 1'
);
PREPARE stmt FROM @add_idx_activity_admin_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_fk_activity_admin := (
    SELECT COUNT(*)
    FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND CONSTRAINT_NAME = 'fk_activity_logs_admin'
      AND TABLE_NAME = 'activity_logs'
);
SET @add_fk_activity_admin_sql := IF(
    @has_fk_activity_admin = 0,
    'ALTER TABLE activity_logs ADD CONSTRAINT fk_activity_logs_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt FROM @add_fk_activity_admin_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO admins (username, email, password, first_name, last_name, role, status)
SELECT 'admin', 'admin@tpvconstruction.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'super_admin', 'active'
WHERE NOT EXISTS (SELECT 1 FROM admins WHERE username = 'admin' OR email = 'admin@tpvconstruction.com');

INSERT INTO admins (username, email, password, first_name, last_name, role, status)
SELECT 'manager', 'manager@tpvconstruction.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Project', 'Manager', 'manager', 'active'
WHERE NOT EXISTS (SELECT 1 FROM admins WHERE username = 'manager' OR email = 'manager@tpvconstruction.com');
