-- Adds the project media library used by the project gallery manager.

CREATE TABLE IF NOT EXISTS project_media (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service VARCHAR(100) NOT NULL,
    title VARCHAR(255),
    description TEXT,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('image', 'video') NOT NULL,
    mime_type VARCHAR(100),
    file_size INT,
    thumbnail_path VARCHAR(500),
    featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_service (service),
    INDEX idx_featured (featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
