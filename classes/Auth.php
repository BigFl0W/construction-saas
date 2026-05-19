<?php
/**
 * Authentication Class
 * Handles user login, session management, and security
 */

require_once dirname(__DIR__) . '/config/Database.php';

class Auth {
    private $db;
    private $userId;
    private $userData;
    private $sessionName = 'construction_auth';
    private $cookieName = 'construction_remember';
    private $encryptionKey;
    private $debug = true; // Set to true for debugging
    
    /**
     * Constructor
     */
    public function __construct() {
        try {
            $this->db = Database::getInstance();
            $this->logDebug("Database connection successful");
        } catch (Exception $e) {
            error_log("CRITICAL: Database connection failed in Auth: " . $e->getMessage());
            die("Database connection failed. Check error log.");
        }
        
        $this->encryptionKey = $this->getEncryptionKey();
        
        // Start session securely
        $this->startSecureSession();
        
        // Check for existing session
        if ($this->isLoggedIn()) {
            $this->userId = $_SESSION[$this->sessionName]['user_id'];
            $this->loadUserData();
        }
    }
    
    /**
     * Start session with secure settings
     */
    private function startSecureSession() {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID periodically to prevent fixation
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
    
    /**
     * Get encryption key from environment or create one
     */
    private function getEncryptionKey() {
        // In production, set this in environment variables
        $key = getenv('ENCRYPTION_KEY') ?: 'your-secret-key-here-change-in-production';
        
        // Ensure key is proper length for AES-256
        return hash('sha256', $key, true);
    }
    
    /**
     * Log debug messages if debug mode is enabled
     */
    private function logDebug($message) {
        if ($this->debug) {
            error_log("[Auth Debug] " . $message);
        }
    }
    
    /**
     * Attempt to login user
     */
    public function login($username, $password, $remember = false) {
        // Validate input
        $username = trim($username);
        if (empty($username) || empty($password)) {
            $this->logDebug("Login failed: Empty username or password");
            return ['success' => false, 'message' => 'Username and password are required'];
        }
        
        // FIXED: Use different parameter names to avoid binding issues
        $sql = "SELECT id, username, email, password, first_name, last_name, 
                       user_type, status, last_login, login_attempts, locked_until 
                FROM users 
                WHERE (username = :username1 OR email = :username2) 
                AND deleted_at IS NULL 
                LIMIT 1";
        
        try {
            $this->logDebug("Attempting login for username: $username");
            $this->logDebug("SQL: " . $sql);
            
            // FIXED: Pass parameters with different names
            $params = [
                'username1' => $username,
                'username2' => $username
            ];
            
            $stmt = $this->db->query($sql, $params);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->logDebug("User not found: $username");
                $this->logFailedAttempt($username);
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            $this->logDebug("User found: " . $user['username'] . " (ID: " . $user['id'] . ")");
            $this->logDebug("User type: " . $user['user_type']);
            
            // Check if account is locked
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                $lockTime = new DateTime($user['locked_until']);
                $now = new DateTime();
                $remaining = $lockTime->diff($now);
                $minutes = $remaining->i;
                $this->logDebug("Account locked for user ID: " . $user['id']);
                return ['success' => false, 'message' => "Account locked. Try again in {$minutes} minutes"];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                $this->logDebug("Password verification failed for user ID: " . $user['id']);
                $this->handleFailedLogin($user['id']);
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            $this->logDebug("Password verified successfully for user ID: " . $user['id']);
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                $this->logDebug("Account not active for user ID: " . $user['id'] . " - Status: " . $user['status']);
                return ['success' => false, 'message' => 'Account is not active'];
            }
            
            // Login successful
            $this->loginSuccess($user, $remember);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $this->getPublicUserData($user)
            ];
            
        } catch (Exception $e) {
            $this->logDebug("Exception in login: " . $e->getMessage());
            $this->logDebug("Exception trace: " . $e->getTraceAsString());
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Handle successful login
     */
    private function loginSuccess($user, $remember) {
        // Reset failed attempts
        $this->resetFailedAttempts($user['id']);
        
        // Update last login
        $this->updateLastLogin($user['id']);
        
        // Set session - using user_type instead of role
        $_SESSION[$this->sessionName] = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'user_type' => $user['user_type'],
            'ip' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'logged_in_at' => time()
        ];
        
        $this->userId = $user['id'];
        $this->userData = $user;
        
        // Set remember me cookie if requested
        if ($remember) {
            $this->setRememberMeCookie($user['id']);
        }
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        $this->logDebug("Login successful for user ID: " . $user['id']);
    }
    
    /**
     * Handle failed login attempt
     */
    private function handleFailedLogin($userId) {
        $sql = "UPDATE users SET 
                login_attempts = login_attempts + 1,
                locked_until = CASE 
                    WHEN login_attempts >= 4 THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                    ELSE locked_until
                END
                WHERE id = :id";
        
        $this->db->query($sql, ['id' => $userId]);
        $this->logDebug("Failed login recorded for user ID: $userId");
    }
    
    /**
     * Reset failed attempts
     */
    private function resetFailedAttempts($userId) {
        $sql = "UPDATE users SET login_attempts = 0, locked_until = NULL WHERE id = :id";
        $this->db->query($sql, ['id' => $userId]);
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $this->db->query($sql, ['id' => $userId]);
    }
    
    /**
     * Log failed attempt (for non-existent users)
     */
    private function logFailedAttempt($username) {
        // Log to database or file for monitoring
        $ip = $this->getClientIp();
        error_log("Failed login attempt for username: {$username} from IP: {$ip}");
    }
    
    /**
     * Set remember me cookie
     */
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + 30 * 24 * 3600; // 30 days
        
        // Store token in database
        $sql = "INSERT INTO user_tokens (user_id, token, expires_at, created_at) 
                VALUES (:user_id, :token, FROM_UNIXTIME(:expires), NOW())";
        
        $this->db->query($sql, [
            'user_id' => $userId,
            'token' => hash('sha256', $token),
            'expires' => $expires
        ]);
        
        // Set cookie
        setcookie(
            $this->cookieName,
            $userId . ':' . $token,
            $expires,
            '/',
            '',
            isset($_SERVER['HTTPS']),
            true
        );
        
        $this->logDebug("Remember me cookie set for user ID: $userId");
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        // Check session
        if (isset($_SESSION[$this->sessionName]) && !empty($_SESSION[$this->sessionName]['user_id'])) {
            return $this->validateSession();
        }
        
        // Check remember me cookie
        if (isset($_COOKIE[$this->cookieName])) {
            return $this->validateRememberCookie();
        }
        
        return false;
    }
    
    /**
     * Validate current session
     */
    private function validateSession() {
        $session = $_SESSION[$this->sessionName];
        
        // Check IP consistency (optional)
        if ($session['ip'] !== $this->getClientIp()) {
            $this->logDebug("Session validation failed: IP mismatch");
            $this->logout();
            return false;
        }
        
        // Check user agent consistency
        if ($session['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $this->logDebug("Session validation failed: User agent mismatch");
            $this->logout();
            return false;
        }
        
        // Check session timeout (8 hours)
        if (time() - $session['logged_in_at'] > 28800) {
            $this->logDebug("Session validation failed: Timeout");
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate remember me cookie
     */
    private function validateRememberCookie() {
        if (!isset($_COOKIE[$this->cookieName])) {
            return false;
        }
        
        $cookie = $_COOKIE[$this->cookieName];
        $parts = explode(':', $cookie);
        
        if (count($parts) !== 2) {
            $this->logDebug("Remember cookie validation failed: Invalid format");
            return false;
        }
        
        list($userId, $token) = $parts;
        
        // Verify token in database
        $sql = "SELECT user_id FROM user_tokens 
                WHERE user_id = :user_id 
                AND token = :token 
                AND expires_at > NOW() 
                AND used = 0 
                LIMIT 1";
        
        try {
            $stmt = $this->db->query($sql, [
                'user_id' => $userId,
                'token' => hash('sha256', $token)
            ]);
            
            if ($stmt->rowCount() > 0) {
                // Mark token as used (one-time use)
                $this->db->query("UPDATE user_tokens SET used = 1 WHERE user_id = :user_id AND token = :token", [
                    'user_id' => $userId,
                    'token' => hash('sha256', $token)
                ]);
                
                // Log the user in
                $this->loginById($userId);
                
                // Generate new remember me token
                $this->setRememberMeCookie($userId);
                
                $this->logDebug("Remember cookie validated for user ID: $userId");
                return true;
            } else {
                $this->logDebug("Remember cookie validation failed: Token not found or expired");
            }
        } catch (Exception $e) {
            $this->logDebug("Remember cookie validation error: " . $e->getMessage());
            error_log("Remember cookie validation error: " . $e->getMessage());
        }
        
        // Clear invalid cookie
        setcookie($this->cookieName, '', time() - 3600, '/');
        return false;
    }
    
    /**
     * Login user by ID (used for remember me)
     */
    private function loginById($userId) {
        $sql = "SELECT id, username, email, first_name, last_name, user_type, status 
                FROM users WHERE id = :id AND status = 'active' AND deleted_at IS NULL";
        
        try {
            $stmt = $this->db->query($sql, ['id' => $userId]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION[$this->sessionName] = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'user_type' => $user['user_type'],
                    'ip' => $this->getClientIp(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'logged_in_at' => time()
                ];
                
                $this->userId = $user['id'];
                $this->userData = $user;
                $this->updateLastLogin($user['id']);
                session_regenerate_id(true);
                
                $this->logDebug("Login by ID successful for user ID: $userId");
            }
        } catch (Exception $e) {
            $this->logDebug("Login by ID error: " . $e->getMessage());
            error_log("Login by ID error: " . $e->getMessage());
        }
    }
    
    /**
     * Load user data into memory
     */
    private function loadUserData() {
        if (!$this->userId) {
            return;
        }
        
        $sql = "SELECT id, username, email, first_name, last_name, user_type, status, 
                       created_at, last_login, profile_image 
                FROM users 
                WHERE id = :id AND deleted_at IS NULL";
        
        try {
            $stmt = $this->db->query($sql, ['id' => $this->userId]);
            $this->userData = $stmt->fetch();
            $this->logDebug("User data loaded for ID: " . $this->userId);
        } catch (Exception $e) {
            $this->logDebug("Load user data error: " . $e->getMessage());
            error_log("Load user data error: " . $e->getMessage());
        }
    }
    
    /**
     * Get current user ID
     */
    public function getUserId() {
        return $this->userId;
    }
    
    /**
     * Get current user data
     */
    public function getUserData() {
        return $this->userData;
    }
    
    /**
     * Get public user data (safe to return)
     */
    private function getPublicUserData($user) {
        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_type' => $user['user_type'],
            'last_login' => $user['last_login']
        ];
    }
    
    /**
     * Check if user has specific user type
     */
    public function hasUserType($type) {
        if (!$this->isLoggedIn() || !$this->userData) {
            return false;
        }
        
        if (is_array($type)) {
            return in_array($this->userData['user_type'], $type);
        }
        
        return $this->userData['user_type'] === $type;
    }
    
    /**
     * Alias for backward compatibility
     */
    public function hasRole($role) {
        return $this->hasUserType($role);
    }
    
    /**
     * Require authentication
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Require specific user type
     */
    public function requireUserType($type) {
        $this->requireAuth();
        
        if (!$this->hasUserType($type)) {
            header('HTTP/1.0 403 Forbidden');
            die('Access denied');
        }
    }
    
    /**
     * Alias for backward compatibility
     */
    public function requireRole($role) {
        $this->requireUserType($role);
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Clear remember me tokens
        if ($this->userId) {
            $this->db->query("DELETE FROM user_tokens WHERE user_id = :user_id", ['user_id' => $this->userId]);
            $this->logDebug("User logged out: ID " . $this->userId);
        }
        
        // Clear session
        $_SESSION[$this->sessionName] = [];
        unset($_SESSION[$this->sessionName]);
        
        // Clear cookie
        setcookie($this->cookieName, '', time() - 3600, '/');
        
        // Destroy session
        session_destroy();
        
        $this->userId = null;
        $this->userData = null;
    }
    
    /**
     * Get client IP address
     */
    private function getClientIp() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public function verifyCsrfToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            $this->logDebug("CSRF verification failed: Empty token");
            return false;
        }
        $result = hash_equals($_SESSION['csrf_token'], $token);
        $this->logDebug("CSRF verification: " . ($result ? 'success' : 'failed'));
        return $result;
    }
    
    /**
     * CSRF token field for forms
     */
    public function csrfField() {
        $token = $this->generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Enable debug mode
     */
    public function enableDebug() {
        $this->debug = true;
    }

    /**
 * Verify CSRF token from form submission
 */
public function verifyCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

public function generateCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
    
    /**
     * Disable debug mode
     */
    public function disableDebug() {
        $this->debug = false;
    }

    public function csrfFieldValue() {
    return $_SESSION['csrf_token'] ?? '';
}
}