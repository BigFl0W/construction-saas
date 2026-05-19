<?php
/**
 * Database Connection Class
 * Handles PDO connections for both local and remote environments
 * Uses environment variables for configuration
 */

class Database {
    private static $instance = null;
    private $connection;
    private $environment;
    
    // Database configurations for different environments
    private $dbConfig = [
        'local' => [
            'host' => 'localhost',
            'name' => 'construction_db',
            'user' => 'root',
            'pass' => '',
            'charset' => 'utf8mb4',
            'port' => '3306'
        ],
        'production' => [
            'host' => 'localhost', // Set your production host
            'name' => 'tpvcons1_database', // Set your production database name
            'user' => 'tpvcons1_database', // Set your production username
            'pass' => '!@#admin!@#', // Set your production password
            'charset' => 'utf8mb4',
            'port' => '3306'
        ]
    ];
    
    /**
     * Private constructor to prevent direct creation of object
     */
    private function __construct() {
        // Auto-detect environment (you can also set a constant)
        $this->environment = $this->detectEnvironment();
        $this->connect();
    }
    
    /**
     * Detect if we're in local or production environment
     */
    private function detectEnvironment() {
        // Check if we're on localhost
        $whitelist = ['127.0.0.1', '::1', 'localhost'];
        
        if (in_array($_SERVER['SERVER_NAME'] ?? '', $whitelist) || 
            (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $whitelist))) {
            return 'local';
        }
        
        // You can also check for a constant or environment variable
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
            return 'production';
        }
        
        // Default to production for safety
        return 'production';
    }
    
    /**
     * Establish PDO connection
     */
    private function connect() {
    $config = $this->dbConfig[$this->environment];
    
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=%s;port=%s",
        $config['host'],
        $config['name'],
        $config['charset'],
        $config['port']
    );
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    try {
        $this->connection = new PDO($dsn, $config['user'], $config['pass'], $options);
        error_log("Database connected successfully to " . $config['name'] . " on " . $config['host']);
    } catch (PDOException $e) {
        error_log("DATABASE CONNECTION FATAL ERROR: " . $e->getMessage());
        error_log("DSN: " . $dsn);
        error_log("User: " . $config['user']);
        error_log("Environment: " . $this->environment);
        die("Database connection failed. Please check error logs.");
    }
}
    /**
     * Test database connection and return detailed status
     * This method can be used for debugging or connection testing
     */
    public function testConnection() {
        $config = $this->dbConfig[$this->environment];
        
        try {
            // Test basic connection
            $this->connection->query("SELECT 1")->fetch();
            
            // Get database info
            $stmt = $this->connection->query("SELECT DATABASE() as db_name");
            $dbName = $stmt->fetch()['db_name'];
            
            // Get MySQL version
            $stmt = $this->connection->query("SELECT VERSION() as version");
            $version = $stmt->fetch()['version'];
            
            // Get table count
            $stmt = $this->connection->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()");
            $tableCount = $stmt->fetch()['count'];
            
            return [
                'success' => true,
                'message' => '✅ Database connection successful!',
                'environment' => $this->environment,
                'database' => $dbName,
                'host' => $config['host'],
                'port' => $config['port'],
                'mysql_version' => $version,
                'table_count' => $tableCount,
                'charset' => $config['charset']
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => '❌ Database connection failed!',
                'error' => $e->getMessage(),
                'environment' => $this->environment,
                'host' => $config['host'],
                'database' => $config['name']
            ];
        }
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
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Get current environment
     */
    public function getEnvironment() {
        return $this->environment;
    }
    
    /**
     * Execute a query with parameters
     */
    /**
 * Execute a query with parameters
 */
public function query($sql, $params = []) {
    try {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        // Log the actual SQL error
        error_log("Query Error: " . $e->getMessage());
        error_log("Failed SQL: " . $sql);
        error_log("Parameters: " . print_r($params, true));
        
        // For debugging - remove in production
        if ($this->environment === 'local') {
            throw new Exception("Database query failed: " . $e->getMessage());
        } else {
            throw new Exception("Database query failed");
        }
    }
}
    
    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
}