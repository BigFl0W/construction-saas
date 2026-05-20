<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class QuoteRequest {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
        $this->ensureTables();
    }

    public function ensureTables() {
        $this->db->query("CREATE TABLE IF NOT EXISTS quote_requests (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS quote_request_replies (
            id INT PRIMARY KEY AUTO_INCREMENT,
            request_id INT NOT NULL,
            admin_user_id INT NULL,
            reply_message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES quote_requests(id) ON DELETE CASCADE,
            INDEX idx_quote_replies_request (request_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function create(array $data) {
        $this->db->query(
            "INSERT INTO quote_requests (
                uuid, first_name, last_name, email, phone, company, client_type, services,
                project_type, project_size, project_location, start_date, budget, timeline,
                description, referral_source, attachments, status, ip_address, user_agent,
                created_at, updated_at
            ) VALUES (
                :uuid, :first_name, :last_name, :email, :phone, :company, :client_type, :services,
                :project_type, :project_size, :project_location, :start_date, :budget, :timeline,
                :description, :referral_source, :attachments, 'unread', :ip_address, :user_agent,
                NOW(), NOW()
            )",
            [
                'uuid' => $this->functions->generateUUID(),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company' => $data['company'] ?: null,
                'client_type' => $data['client_type'],
                'services' => json_encode($data['services'] ?? []),
                'project_type' => $data['project_type'],
                'project_size' => $data['project_size'] ?: null,
                'project_location' => $data['project_location'],
                'start_date' => $data['start_date'] ?: null,
                'budget' => (float) ($data['budget'] ?? 0),
                'timeline' => $data['timeline'] ?: null,
                'description' => $data['description'],
                'referral_source' => $data['referral_source'] ?: null,
                'attachments' => json_encode($data['attachments'] ?? []),
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function getUnreadCount() {
        return (int) $this->db->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'unread'")->fetchColumn();
    }

    public function getRecentUnread($limit = 5) {
        $limit = max(1, (int) $limit);
        return $this->db->query(
            "SELECT id, first_name, last_name, email, project_type, project_location, created_at, status
             FROM quote_requests
             WHERE status = 'unread'
             ORDER BY created_at DESC
             LIMIT {$limit}"
        )->fetchAll();
    }
}
