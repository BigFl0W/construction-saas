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

// Session configuration
if (!headers_sent()) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
}

// Constants
define('SITE_NAME', 'TPV Construction and Services LTD');
$documentRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ($_SERVER['DOCUMENT_ROOT'] ?? ''));
$projectRoot = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__));
$basePath = '';
if ($documentRoot && strpos($projectRoot, $documentRoot) === 0) {
    $basePath = substr($projectRoot, strlen($documentRoot));
}
$basePath = '/' . trim($basePath, '/');
if ($basePath === '/') {
    $basePath = '';
}
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

// Timezone
$resolvedTimezone = 'Africa/Lagos';
try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'timezone' LIMIT 1");
    $stmt->execute();
    $timezoneValue = $stmt->fetchColumn();
    if (is_string($timezoneValue) && $timezoneValue !== '' && in_array($timezoneValue, timezone_identifiers_list(), true)) {
        $resolvedTimezone = $timezoneValue;
    }
} catch (Throwable $e) {
    // Fall back to the project default timezone when settings are unavailable.
}
date_default_timezone_set($resolvedTimezone);

if (!function_exists('tpv_asset_url')) {
    function tpv_asset_url($path) {
        if (!$path) {
            return '';
        }

        $path = str_replace('\\', '/', trim((string) $path));
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return SITE_URL . ltrim($path, '/');
    }
}

if (!function_exists('tpv_setting_asset_url')) {
    function tpv_setting_asset_url($key, $defaultRelativePath) {
        static $settingsInstance = null;

        if ($settingsInstance === null) {
            $settingsInstance = class_exists('Settings') ? new Settings() : null;
        }

        $value = $settingsInstance ? $settingsInstance->get($key, $defaultRelativePath) : $defaultRelativePath;
        if (!$value) {
            $value = $defaultRelativePath;
        }

        return tpv_asset_url($value);
    }
}
