<?php
/**
 * Authentication Class
 * Handles admin and legacy user login, session management, and security.
 */

require_once dirname(__DIR__) . '/config/Database.php';

class Auth {
    private $db;
    private $userId;
    private $userData;
    private $actorType = 'user';
    private $sessionName = 'construction_auth';
    private $cookieName = 'construction_remember';
    private $encryptionKey;
    private $debug = true;

    public function __construct() {
        try {
            $this->db = Database::getInstance();
            $this->logDebug('Database connection successful');
        } catch (Exception $e) {
            error_log('CRITICAL: Database connection failed in Auth: ' . $e->getMessage());
            die('Database connection failed. Check error log.');
        }

        $this->encryptionKey = $this->getEncryptionKey();
        $this->startSecureSession();

        if ($this->isLoggedIn()) {
            $session = $_SESSION[$this->sessionName] ?? [];
            $this->userId = $session['user_id'] ?? null;
            $this->actorType = $session['actor_type'] ?? 'user';
            $this->loadUserData();
            if (empty($this->userData)) {
                $this->logout();
            }
        }
    }

    private function startSecureSession() {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }

    private function getEncryptionKey() {
        $key = getenv('ENCRYPTION_KEY') ?: 'your-secret-key-here-change-in-production';
        return hash('sha256', $key, true);
    }

    private function logDebug($message) {
        if ($this->debug) {
            error_log('[Auth Debug] ' . $message);
        }
    }

    private function getActorConfig($actorType) {
        if ($actorType === 'admin') {
            return [
                'table' => 'admins',
                'token_table' => 'admin_tokens',
                'role_column' => 'role',
                'id_label' => 'admin_id'
            ];
        }

        return [
            'table' => 'users',
            'token_table' => 'user_tokens',
            'role_column' => 'user_type',
            'id_label' => 'user_id'
        ];
    }

    private function fetchActorByIdentity($identity, $actorType) {
        $config = $this->getActorConfig($actorType);
        $roleColumn = $config['role_column'];

        $sql = "SELECT id, username, email, password, first_name, last_name,
                       {$roleColumn} AS user_type, status, last_login, login_attempts,
                       locked_until, created_at, profile_image
                FROM {$config['table']}
                WHERE (username = :username1 OR email = :username2)
                AND deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->query($sql, [
            'username1' => $identity,
            'username2' => $identity
        ]);

        $actor = $stmt->fetch();
        if ($actor) {
            $actor['auth_source'] = $actorType;
        }

        return $actor ?: null;
    }

    public function login($username, $password, $remember = false) {
        $username = trim($username);
        if ($username === '' || $password === '') {
            $this->logDebug('Login failed: Empty username or password');
            return ['success' => false, 'message' => 'Username and password are required'];
        }

        try {
            $this->logDebug("Attempting login for username: {$username}");

            $actor = $this->fetchActorByIdentity($username, 'admin');
            if (!$actor) {
                $actor = $this->fetchActorByIdentity($username, 'user');
            }

            if (!$actor) {
                $this->logDebug("Actor not found: {$username}");
                $this->logFailedAttempt($username);
                return ['success' => false, 'message' => 'Invalid username or password'];
            }

            $this->logDebug('Actor found: ' . $actor['username'] . ' (' . $actor['auth_source'] . ':' . $actor['id'] . ')');

            if ($actor['locked_until'] && strtotime($actor['locked_until']) > time()) {
                $lockTime = new DateTime($actor['locked_until']);
                $now = new DateTime();
                $remaining = $lockTime->diff($now);
                return ['success' => false, 'message' => 'Account locked. Try again in ' . $remaining->i . ' minutes'];
            }

            if (!password_verify($password, $actor['password'])) {
                $this->handleFailedLogin($actor['id'], $actor['auth_source']);
                return ['success' => false, 'message' => 'Invalid username or password'];
            }

            if (($actor['status'] ?? 'inactive') !== 'active') {
                return ['success' => false, 'message' => 'Account is not active'];
            }

            $this->loginSuccess($actor, $remember);

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $this->getPublicUserData($actor)
            ];
        } catch (Exception $e) {
            $this->logDebug('Exception in login: ' . $e->getMessage());
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }

    private function loginSuccess($actor, $remember) {
        $actorType = $actor['auth_source'] ?? 'user';

        $this->resetFailedAttempts($actor['id'], $actorType);
        $this->updateLastLogin($actor['id'], $actorType);

        $_SESSION[$this->sessionName] = [
            'user_id' => $actor['id'],
            'actor_type' => $actorType,
            'username' => $actor['username'],
            'user_type' => $actor['user_type'],
            'ip' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'logged_in_at' => time()
        ];

        $this->userId = $actor['id'];
        $this->actorType = $actorType;
        $this->userData = $actor;

        if ($remember) {
            $this->setRememberMeCookie($actor['id'], $actorType);
        }

        session_regenerate_id(true);
        $this->logDebug('Login successful for ' . $actorType . ' ID: ' . $actor['id']);
    }

    private function handleFailedLogin($actorId, $actorType = 'user') {
        $config = $this->getActorConfig($actorType);
        $sql = "UPDATE {$config['table']} SET
                login_attempts = login_attempts + 1,
                locked_until = CASE
                    WHEN login_attempts >= 4 THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                    ELSE locked_until
                END
                WHERE id = :id";

        $this->db->query($sql, ['id' => $actorId]);
        $this->logDebug('Failed login recorded for ' . $actorType . ' ID: ' . $actorId);
    }

    private function resetFailedAttempts($actorId, $actorType = 'user') {
        $config = $this->getActorConfig($actorType);
        $this->db->query(
            "UPDATE {$config['table']} SET login_attempts = 0, locked_until = NULL WHERE id = :id",
            ['id' => $actorId]
        );
    }

    private function updateLastLogin($actorId, $actorType = 'user') {
        $config = $this->getActorConfig($actorType);
        $this->db->query(
            "UPDATE {$config['table']} SET last_login = NOW() WHERE id = :id",
            ['id' => $actorId]
        );
    }

    private function logFailedAttempt($username) {
        $ip = $this->getClientIp();
        error_log("Failed login attempt for username: {$username} from IP: {$ip}");
    }

    private function setRememberMeCookie($actorId, $actorType = 'user') {
        $config = $this->getActorConfig($actorType);
        $token = bin2hex(random_bytes(32));
        $expires = time() + 30 * 24 * 3600;
        $idColumn = $config['id_label'];

        $sql = "INSERT INTO {$config['token_table']} ({$idColumn}, token, expires_at, created_at)
                VALUES (:actor_id, :token, FROM_UNIXTIME(:expires), NOW())";

        $this->db->query($sql, [
            'actor_id' => $actorId,
            'token' => hash('sha256', $token),
            'expires' => $expires
        ]);

        setcookie(
            $this->cookieName,
            $actorType . ':' . $actorId . ':' . $token,
            $expires,
            '/',
            '',
            isset($_SERVER['HTTPS']),
            true
        );

        $this->logDebug('Remember me cookie set for ' . $actorType . ' ID: ' . $actorId);
    }

    public function isLoggedIn() {
        if (isset($_SESSION[$this->sessionName]) && !empty($_SESSION[$this->sessionName]['user_id'])) {
            return $this->validateSession();
        }

        if (isset($_COOKIE[$this->cookieName])) {
            return $this->validateRememberCookie();
        }

        return false;
    }

    private function validateSession() {
        $session = $_SESSION[$this->sessionName];

        if (($session['ip'] ?? '') !== $this->getClientIp()) {
            $this->logDebug('Session validation failed: IP mismatch');
            $this->logout();
            return false;
        }

        if (($session['user_agent'] ?? '') !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $this->logDebug('Session validation failed: User agent mismatch');
            $this->logout();
            return false;
        }

        if (time() - ($session['logged_in_at'] ?? 0) > 28800) {
            $this->logDebug('Session validation failed: Timeout');
            $this->logout();
            return false;
        }

        return true;
    }

    private function validateRememberCookie() {
        if (!isset($_COOKIE[$this->cookieName])) {
            return false;
        }

        $parts = explode(':', $_COOKIE[$this->cookieName]);
        if (count($parts) === 3) {
            list($actorType, $actorId, $token) = $parts;
        } elseif (count($parts) === 2) {
            $actorType = 'user';
            list($actorId, $token) = $parts;
        } else {
            $this->logDebug('Remember cookie validation failed: Invalid format');
            return false;
        }

        if (!in_array($actorType, ['admin', 'user'], true)) {
            return false;
        }

        $config = $this->getActorConfig($actorType);
        $idColumn = $config['id_label'];

        $sql = "SELECT {$idColumn} FROM {$config['token_table']}
                WHERE {$idColumn} = :actor_id
                AND token = :token
                AND expires_at > NOW()
                AND used = 0
                LIMIT 1";

        try {
            $stmt = $this->db->query($sql, [
                'actor_id' => $actorId,
                'token' => hash('sha256', $token)
            ]);

            if ($stmt->rowCount() > 0) {
                $this->db->query(
                    "UPDATE {$config['token_table']} SET used = 1 WHERE {$idColumn} = :actor_id AND token = :token",
                    [
                        'actor_id' => $actorId,
                        'token' => hash('sha256', $token)
                    ]
                );

                $this->loginById($actorId, $actorType);
                $this->setRememberMeCookie($actorId, $actorType);
                $this->logDebug('Remember cookie validated for ' . $actorType . ' ID: ' . $actorId);
                return true;
            }
        } catch (Exception $e) {
            $this->logDebug('Remember cookie validation error: ' . $e->getMessage());
            error_log('Remember cookie validation error: ' . $e->getMessage());
        }

        setcookie($this->cookieName, '', time() - 3600, '/');
        return false;
    }

    private function loginById($actorId, $actorType = 'user') {
        $config = $this->getActorConfig($actorType);
        $roleColumn = $config['role_column'];

        $sql = "SELECT id, username, email, first_name, last_name,
                       {$roleColumn} AS user_type, status, created_at, last_login, profile_image
                FROM {$config['table']}
                WHERE id = :id AND status = 'active' AND deleted_at IS NULL";

        try {
            $stmt = $this->db->query($sql, ['id' => $actorId]);
            $actor = $stmt->fetch();

            if ($actor) {
                $actor['auth_source'] = $actorType;
                $_SESSION[$this->sessionName] = [
                    'user_id' => $actor['id'],
                    'actor_type' => $actorType,
                    'username' => $actor['username'],
                    'user_type' => $actor['user_type'],
                    'ip' => $this->getClientIp(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'logged_in_at' => time()
                ];

                $this->userId = $actor['id'];
                $this->actorType = $actorType;
                $this->userData = $actor;
                $this->updateLastLogin($actor['id'], $actorType);
                session_regenerate_id(true);
            }
        } catch (Exception $e) {
            $this->logDebug('Login by ID error: ' . $e->getMessage());
            error_log('Login by ID error: ' . $e->getMessage());
        }
    }

    private function loadUserData() {
        if (!$this->userId) {
            return;
        }

        $config = $this->getActorConfig($this->actorType);
        $roleColumn = $config['role_column'];

        $sql = "SELECT id, username, email, first_name, last_name,
                       {$roleColumn} AS user_type, status, created_at, last_login, profile_image
                FROM {$config['table']}
                WHERE id = :id AND deleted_at IS NULL";

        try {
            $stmt = $this->db->query($sql, ['id' => $this->userId]);
            $actor = $stmt->fetch();
            if ($actor) {
                $actor['auth_source'] = $this->actorType;
                $this->userData = $actor;
            }
        } catch (Exception $e) {
            $this->logDebug('Load user data error: ' . $e->getMessage());
            error_log('Load user data error: ' . $e->getMessage());
        }
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getActorId() {
        return $this->userId;
    }

    public function getAuthSource() {
        return $this->actorType;
    }

    public function isAdminAuth() {
        return $this->actorType === 'admin';
    }

    public function getUserData() {
        return $this->userData;
    }

    private function getPublicUserData($actor) {
        return [
            'id' => $actor['id'],
            'username' => $actor['username'],
            'email' => $actor['email'],
            'first_name' => $actor['first_name'],
            'last_name' => $actor['last_name'],
            'user_type' => $actor['user_type'],
            'last_login' => $actor['last_login'],
            'auth_source' => $actor['auth_source'] ?? 'user'
        ];
    }

    public function hasUserType($type) {
        if (!$this->isLoggedIn() || !$this->userData) {
            return false;
        }

        $userType = $this->userData['user_type'] ?? '';
        if (is_array($type)) {
            return in_array($userType, $type, true);
        }

        return $userType === $type;
    }

    public function hasRole($role) {
        return $this->hasUserType($role);
    }

    public function requireAuth() {
        if (!$this->isLoggedIn() || empty($this->userData)) {
            if (empty($this->userData)) {
                $this->logout();
            }
            header('Location: login.php');
            exit;
        }
    }

    public function requireUserType($type) {
        $this->requireAuth();

        if (!$this->hasUserType($type)) {
            header('HTTP/1.0 403 Forbidden');
            die('Access denied');
        }
    }

    public function requireRole($role) {
        $this->requireUserType($role);
    }

    public function logout() {
        if ($this->userId) {
            $config = $this->getActorConfig($this->actorType);
            $idColumn = $config['id_label'];
            $this->db->query(
                "DELETE FROM {$config['token_table']} WHERE {$idColumn} = :actor_id",
                ['actor_id' => $this->userId]
            );
        }

        $_SESSION[$this->sessionName] = [];
        unset($_SESSION[$this->sessionName]);
        setcookie($this->cookieName, '', time() - 3600, '/');
        session_destroy();

        $this->userId = null;
        $this->userData = null;
        $this->actorType = 'user';
    }

    private function getClientIp() {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (isset($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return 'UNKNOWN';
    }

    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function verifyCsrfToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            $this->logDebug('CSRF verification failed: Empty token');
            return false;
        }

        $result = hash_equals($_SESSION['csrf_token'], $token);
        $this->logDebug('CSRF verification: ' . ($result ? 'success' : 'failed'));
        return $result;
    }

    public function csrfField() {
        $token = $this->generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    public function enableDebug() {
        $this->debug = true;
    }

    public function verifyCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function generateCSRF() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function disableDebug() {
        $this->debug = false;
    }

    public function csrfFieldValue() {
        return $_SESSION['csrf_token'] ?? '';
    }
}
