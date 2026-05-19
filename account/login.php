<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/classes/Auth.php';
require_once BASE_PATH . '/classes/Functions.php';

$auth = new Auth();
$functions = Functions::getInstance();

if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$showToast = false;
$toastType = '';
$toastMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh the page.';
        $showToast = true;
        $toastType = 'error';
        $toastMessage = $error;
        error_log("CSRF token verification failed from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) ? true : false;

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required';
            $showToast = true;
            $toastType = 'error';
            $toastMessage = $error;
        } else {
            $result = $auth->login($username, $password, $remember);

            if ($result['success']) {
                if (isset($result['user']['id'])) {
                    $functions->logActivity($result['user']['id'], 'login', 'User logged in successfully');
                }
                $_SESSION['login_success'] = 'Welcome back, ' . htmlspecialchars($result['user']['first_name'] ?? $username) . '.';
                header('Location: index.php');
                exit;
            } else {
                $error = $result['message'];
                $showToast = true;
                $toastType = 'error';
                $toastMessage = $error;
                error_log("Failed login attempt for username: $username from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            }
        }
    }
}

if (isset($_SESSION['login_success'])) {
    $success = $_SESSION['login_success'];
    $showToast = true;
    $toastType = 'success';
    $toastMessage = $success;
    unset($_SESSION['login_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPV Construction and Services LTD - Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-wrapper {
            display: flex;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 920px;
            width: 100%;
            min-height: 540px;
        }
        .login-brand {
            flex: 1;
            background: linear-gradient(135deg, #1a2332 0%, #232f41 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: #fff;
            position: relative;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="rgba(255,255,255,0.03)" d="M20 20L80 20L80 80L20 80Z"/><path fill="rgba(255,255,255,0.02)" d="M0 0L100 0L100 100L0 100Z"/></svg>') repeat;
            background-size: 40px;
        }
        .login-brand > * { position: relative; z-index: 1; }
        .login-brand img { max-height: 48px; margin-bottom: 20px; }
        .login-brand h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
        .login-brand p { font-size: 0.85rem; color: rgba(255,255,255,0.6); text-align: center; max-width: 280px; line-height: 1.5; }

        .login-form {
            flex: 1;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 460px;
        }
        .login-form h3 { font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; color: #1a2332; }
        .login-form .subtitle { font-size: 0.8rem; color: #6b7a8f; margin-bottom: 28px; }

        .form-floating { margin-bottom: 16px; }
        .form-floating .form-control {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding: 1rem 0.75rem 0.5rem;
            height: auto;
            font-size: 0.875rem;
        }
        .form-floating .form-control:focus {
            border-color: #d4a13e;
            box-shadow: 0 0 0 3px rgba(212,161,62,0.12);
        }
        .form-floating label { padding: 0.85rem 0.75rem; font-size: 0.825rem; color: #6b7a8f; }

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            background: #d4a13e;
            border: none;
            color: #1a2332;
            transition: all 0.2s;
        }
        .btn-login:hover { background: #c08e2e; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(212,161,62,0.3); }

        .form-check { margin-bottom: 20px; }
        .form-check-input:checked { background-color: #d4a13e; border-color: #d4a13e; }

        .login-footer { margin-top: auto; padding-top: 24px; }
        .login-footer p { font-size: 0.75rem; color: #6b7a8f; margin: 0; }

        /* Toast */
        .toast-custom {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            font-size: 0.825rem;
            line-height: 1.4;
            animation: toastSlideIn 0.3s ease-out;
            color: #fff;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 380px;
        }
        .toast-custom.success { background: #059669; }
        .toast-custom.error { background: #dc2626; }
        .toast-custom.warning { background: #d97706; }
        .toast-custom.info { background: #2563eb; }
        .toast-custom .toast-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
        .toast-custom .toast-body { flex: 1; padding: 0; }
        .toast-custom .toast-close {
            background: none; border: none; color: rgba(255,255,255,0.7);
            font-size: 1rem; cursor: pointer; padding: 0; line-height: 1; flex-shrink: 0;
        }
        .toast-custom .toast-close:hover { color: #fff; }

        @keyframes toastSlideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes toastSlideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; max-width: 420px; min-height: auto; }
            .login-brand { padding: 32px 24px; }
            .login-brand h2 { font-size: 1.25rem; }
            .login-form { padding: 32px 24px; max-width: 100%; }
            .toast-custom { max-width: calc(100% - 32px); right: 16px; top: 16px; }
        }
    </style>
</head>
<body>

<?php if ($showToast): ?>
<div class="toast-custom <?php echo $toastType; ?>" id="loginToast">
    <i class="fas <?php echo $toastType === 'success' ? 'fa-check-circle' : ($toastType === 'error' ? 'fa-exclamation-circle' : ($toastType === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle')); ?> toast-icon"></i>
    <div class="toast-body"><?php echo htmlspecialchars($toastMessage); ?></div>
    <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
</div>
<script>
setTimeout(function() {
    var t = document.getElementById('loginToast');
    if (t) { t.style.animation = 'toastSlideOut 0.3s ease forwards'; setTimeout(function(){t.remove()}, 300); }
}, 5000);
</script>
<?php endif; ?>

<div class="login-wrapper">
    <div class="login-brand">
        <img src="assets/img/logo-48x48_c.png" alt="TPV Construction and Services LTD" />
        <h2>TPV Construction and Services LTD</h2>
        <p>Manage projects, clients, workforce, and resources from one command center.</p>
    </div>
    <div class="login-form">
        <h3>Welcome back</h3>
        <p class="subtitle">Sign in to your account to continue.</p>
        <form method="POST" action="" id="loginForm">
            <?php echo $auth->csrfField(); ?>
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" placeholder="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required autofocus>
                <label for="username"><i class="far fa-user me-2"></i>Username or Email</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-login" id="loginBtn">
                <i class="fas fa-arrow-right me-2"></i>Sign In
            </button>
        </form>
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> TPV Construction and Services LTD. All rights reserved.</p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$('#loginForm').on('submit', function() {
    var btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing in...';
});
</script>
</body>
</html>
