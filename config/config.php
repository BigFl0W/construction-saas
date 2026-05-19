<?php
/**
 * Main Configuration File
 */

// Error reporting (turn off in production)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Africa/Lagos'); // Set your timezone

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');

// Constants
define('SITE_NAME', 'TPV Construction and Services LTD');
$basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__DIR__));
$basePath = rtrim(str_replace('\\', '/', $basePath), '/');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . $basePath . '/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ENVIRONMENT', (in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']) ? 'development' : 'production'));

// Auto-loader (simple)
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../classes/',
        __DIR__ . '/../',
        __DIR__ . '/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});