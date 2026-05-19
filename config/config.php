<?php
/**
 * Main Configuration File
 */

require_once __DIR__ . '/env.php';

$serverName = $_SERVER['SERVER_NAME'] ?? '';
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
$isLocalEnvironment = in_array($serverName, ['localhost', '127.0.0.1'], true)
    || in_array($remoteAddr, ['127.0.0.1', '::1'], true)
    || tpv_env('APP_ENV', '') === 'local';

$configuredEnvironment = tpv_env('APP_ENV', $isLocalEnvironment ? 'development' : 'production');
$debugEnabled = tpv_env_bool('APP_DEBUG', $configuredEnvironment !== 'production');

// Error reporting (turn off in production)
if ($debugEnabled) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Session configuration
if (!headers_sent()) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
}

// Constants
define('SITE_NAME', tpv_env('SITE_NAME', 'TPV Construction and Services LTD'));
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

$requestScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$detectedSiteUrl = $requestScheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $basePath . '/';
$configuredSiteUrl = tpv_env('APP_URL', '');
$siteUrl = $configuredSiteUrl !== '' ? rtrim($configuredSiteUrl, '/') . '/' : $detectedSiteUrl;

define('SITE_URL', $siteUrl);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ENVIRONMENT', $configuredEnvironment);

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
$resolvedTimezone = tpv_env('APP_TIMEZONE', 'Africa/Lagos');
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
