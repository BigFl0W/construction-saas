CREATE TABLE IF NOT EXISTS quote_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    first_name VARCHAR(120) NOT NULL,
    last_name VARCHAR(120) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(80) NOT NULL,
    company VARCHAR(255) NULL,
    client_type VARCHAR(120) NOT NULL,
    services TEXT NULL,
    project_type VARCHAR(150) NOT NULL,
    project_size VARCHAR(120) NULL,
    project_location VARCHAR(255) NOT NULL,
    start_date VARCHAR(120) NULL,
    budget DECIMAL(15,2) NULL DEFAULT 0,
    timeline VARCHAR(150) NULL,
    description TEXT NOT NULL,
    referral_source VARCHAR(255) NULL,
    attachments TEXT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    ip_address VARCHAR(100) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_quote_status (status),
    INDEX idx_quote_email (email),
    INDEX idx_quote_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quote_request_replies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    admin_user_id INT NULL,
    reply_message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES quote_requests(id) ON DELETE CASCADE,
    INDEX idx_quote_replies_request (request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
