<?php
/**
 * Functions Class
 * Common utility functions for the application
 */

require_once dirname(__DIR__) . '/config/Database.php';

class Functions {
    private $db;
    private static $instance = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Sanitize input data
     */
    public function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     */
    public function validatePhone($phone) {
        // Remove common separators
        $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);
        return preg_match('/^[0-9]{10,15}$/', $phone);
    }
    
    /**
     * Generate UUID v4
     */
    public function generateUUID() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    /**
     * Format date
     */
    public function formatDate($date, $format = 'Y-m-d') {
        if (empty($date)) return '';
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date($format, $timestamp);
    }
    
    /**
     * Format currency
     */
    public function formatCurrency($amount, $currency = 'NGN') {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'NGN' => '₦'
        ];
        
        $symbol = $symbols[$currency] ?? '₦';
        return $symbol . number_format((float)$amount, 2);
    }
    
    /**
     * Truncate text
     */
    public function truncateText($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        $truncated = substr($text, 0, $length);
        $lastSpace = strrpos($truncated, ' ');
        
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }
        
        return $truncated . $suffix;
    }
    
    /**
     * Generate slug from string
     */
    public function createSlug($string) {
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', '-', trim($string));
        return strtolower($string);
    }
    
    /**
     * Get pagination data
     */
    public function getPaginationData($currentPage, $totalRecords, $perPage = 20) {
        $totalPages = ceil($totalRecords / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        
        $start = ($currentPage - 1) * $perPage;
        
        return [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages,
            'start' => $start,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage - 1,
            'next_page' => $currentPage + 1
        ];
    }
    
    /**
     * Upload file with security checks
     */
    public function uploadFile($file, $targetDir, $allowedTypes = [], $maxSize = 5242880) {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds limit of ' . ($maxSize / 1048576) . 'MB';
        }
        
        // Check file type
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
            $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
        }
        
        // Create directory if not exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $file['name']);
        $filepath = $targetDir . '/' . $filename;
        
        // Move file
        if (empty($errors) && move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'mime_type' => $mimeType,
                'size' => $file['size']
            ];
        } else {
            $errors[] = 'Failed to move uploaded file';
            return ['success' => false, 'errors' => $errors];
        }
    }
    
    /**
     * Delete file
     */
    public function deleteFile($filepath) {
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    /**
     * Send email using PHP mail (or configure for SMTP)
     */
    public function sendEmail($to, $subject, $message, $from = 'noreply@tpvconstruction.com') {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $from . "\r\n";
        
        return mail($to, $subject, $message, $headers);
    }
    
    /**
     * Get settings from database
     */
    public function getSetting($key, $default = null) {
        $sql = "SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1";
        
        try {
            $stmt = $this->db->query($sql, ['key' => $key]);
            $result = $stmt->fetch();
            
            return $result ? $result['setting_value'] : $default;
        } catch (Exception $e) {
            error_log("Get setting error: " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Log activity
     */
    public function logActivity($userId, $action, $description, $ip = null, $actorType = null) {
        if (!$ip) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }

        if ($actorType === null) {
            $actorType = $_SESSION['construction_auth']['actor_type'] ?? 'user';
        }

        $userColumn = $actorType === 'admin' ? 'admin_id' : 'user_id';
        $otherColumn = $actorType === 'admin' ? 'user_id' : 'admin_id';

        $sql = "INSERT INTO activity_logs ({$userColumn}, {$otherColumn}, action, description, ip_address, created_at)
                VALUES (:actor_id, NULL, :action, :description, :ip, NOW())";
        
        try {
            $this->db->query($sql, [
                'actor_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip' => $ip
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Log activity error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get time ago string
     */
    public function timeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return $diff . ' seconds ago';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $time);
        }
    }
    
    /**
     * Generate random password
     */
    public function generateRandomPassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        $max = strlen($chars) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }
        
        return $password;
    }
    
    /**
     * Get dropdown options from table
     */
    public function getOptions($table, $valueField, $textField, $where = '1', $params = []) {
        $sql = "SELECT {$valueField} as value, {$textField} as text FROM {$table} WHERE {$where} ORDER BY {$textField}";
        
        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get options error: " . $e->getMessage());
            return [];
        }
    }
}
