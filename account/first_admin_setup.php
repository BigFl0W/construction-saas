<?php
ob_start();
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/classes/Auth.php';
require_once BASE_PATH . '/classes/Functions.php';

$auth = new Auth();
$functions = Functions::getInstance();
$db = Database::getInstance();

$adminCount = (int) ($db->query("SELECT COUNT(*) AS total FROM admins WHERE deleted_at IS NULL")->fetch()['total'] ?? 0);
if ($adminCount > 0) {
    $_SESSION['toast_warning'] = 'Admin accounts already exist. Use the normal sign-in page.';
    header('Location: login.php');
    exit;
}

$error = '';
$showToast = false;
$toastType = '';
$toastMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh and try again.';
        $showToast = true;
        $toastType = 'error';
        $toastMessage = $error;
    } else {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        try {
            if ($firstName === '' || $lastName === '' || $username === '' || $email === '' || $password === '') {
                throw new Exception('All fields are required.');
            }
            if (!$functions->validateEmail($email)) {
                throw new Exception('Please enter a valid email address.');
            }
            if (strlen($password) < 8) {
                throw new Exception('Password must be at least 8 characters long.');
            }
            if ($password !== $confirmPassword) {
                throw new Exception('Passwords do not match.');
            }

            $existingAdmin = $db->query(
                "SELECT id FROM admins WHERE (username = :username OR email = :email) AND deleted_at IS NULL LIMIT 1",
                ['username' => $username, 'email' => $email]
            )->fetch();
            if ($existingAdmin) {
                throw new Exception('That username or email is already in use.');
            }

            $existingUser = $db->query(
                "SELECT id FROM users WHERE (username = :username OR email = :email) AND deleted_at IS NULL LIMIT 1",
                ['username' => $username, 'email' => $email]
            )->fetch();
            if ($existingUser) {
                throw new Exception('That username or email is already used by a legacy user account.');
            }

            $db->query(
                "INSERT INTO admins (username, email, password, first_name, last_name, role, status)
                 VALUES (:username, :email, :password, :first_name, :last_name, 'super_admin', 'active')",
                [
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'first_name' => $firstName,
                    'last_name' => $lastName
                ]
            );

            $_SESSION['login_success'] = 'Admin account created. Sign in to continue.';
            header('Location: login.php');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
            $showToast = true;
            $toastType = 'error';
            $toastMessage = $error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPV Construction and Services LTD - Create Admin</title>
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
            padding: 18px;
        }
        .login-wrapper {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 520px;
            width: 100%;
            min-height: 0;
        }
        .login-form {
            padding: 28px 28px 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(212,161,62,0.14);
            color: #b78010;
            margin-bottom: 14px;
            font-size: 1rem;
        }
        .login-form h3 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: #1a2332;
        }
        .login-form .subtitle {
            font-size: 0.8rem;
            color: #6b7a8f;
            margin-bottom: 18px;
        }

        .setup-note {
            border: none;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 14px;
            background: #fff7db;
            color: #7a5800;
        }
        .setup-note .title {
            font-weight: 700;
            margin-bottom: 4px;
            color: #5f4600;
        }
        .setup-note .copy {
            font-size: 0.8rem;
            line-height: 1.45;
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .form-grid .full {
            grid-column: 1 / -1;
        }

        .form-floating {
            margin-bottom: 0;
        }
        .form-floating .form-control {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 0.88rem 0.75rem 0.42rem;
            height: auto;
            font-size: 0.875rem;
            min-height: 50px;
        }
        .form-floating .form-control:focus {
            border-color: #d4a13e;
            box-shadow: 0 0 0 3px rgba(212,161,62,0.12);
        }
        .form-floating label {
            padding: 0.72rem 0.75rem;
            font-size: 0.79rem;
            color: #6b7a8f;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            background: #d4a13e;
            border: none;
            color: #1a2332;
            transition: all 0.2s;
            margin-top: 14px;
        }
        .btn-login:hover {
            background: #c08e2e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212,161,62,0.3);
        }

        .helper-link {
            margin-top: 14px;
            text-align: center;
        }
        .helper-link a {
            color: #6b7a8f;
            font-size: 0.82rem;
            text-decoration: none;
        }
        .helper-link a:hover {
            color: #1a2332;
        }

        .footer-copy {
            margin-top: 14px;
            font-size: 0.75rem;
            color: #6b7a8f;
            text-align: left;
        }

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
            .login-wrapper {
                max-width: 430px;
                min-height: auto;
            }
            .login-form {
                padding: 20px 20px 22px;
            }
            .login-form h3 {
                font-size: 1.18rem;
            }
            .login-form .subtitle {
                font-size: 0.76rem;
                margin-bottom: 12px;
            }
            .setup-note {
                padding: 12px 14px;
                margin-bottom: 14px;
                border-radius: 14px;
            }
            .setup-note .title {
                font-size: 0.95rem;
                margin-bottom: 2px;
            }
            .setup-note .copy {
                font-size: 0.76rem;
            }
            .form-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .form-grid .full {
                grid-column: auto;
            }
            .form-floating .form-control {
                min-height: 46px;
                font-size: 0.82rem;
                padding: 0.8rem 0.72rem 0.35rem;
            }
            .form-floating label {
                font-size: 0.75rem;
                padding: 0.68rem 0.72rem;
            }
            .btn-login {
                margin-top: 12px;
                padding: 11px;
                font-size: 0.86rem;
            }
            .helper-link {
                margin-top: 12px;
            }
            .footer-copy {
                display: none;
            }
            .toast-custom {
                max-width: calc(100% - 32px);
                right: 16px;
                top: 16px;
            }
        }

        @media (max-width: 420px) {
            body {
                padding: 10px;
            }
            .login-wrapper {
                border-radius: 14px;
            }
            .login-form {
                padding: 18px 16px 20px;
            }
        }
    </style>
</head>
<body>

<?php if ($showToast): ?>
<div class="toast-custom <?php echo $toastType; ?>" id="setupToast">
    <i class="fas <?php echo $toastType === 'success' ? 'fa-check-circle' : ($toastType === 'error' ? 'fa-exclamation-circle' : ($toastType === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle')); ?> toast-icon"></i>
    <div class="toast-body"><?php echo htmlspecialchars($toastMessage); ?></div>
    <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
</div>
<script>
setTimeout(function() {
    var t = document.getElementById('setupToast');
    if (t) { t.style.animation = 'toastSlideOut 0.3s ease forwards'; setTimeout(function(){t.remove()}, 300); }
}, 5000);
</script>
<?php endif; ?>

<div class="login-wrapper">
    <div class="login-form">
        <div class="form-badge">
            <i class="fas fa-user-shield"></i>
        </div>
        <h3>Create admin</h3>
        <p class="subtitle">Set the primary dashboard account.</p>

        <div class="setup-note">
            <div class="title">First account access</div>
            <p class="copy">This account becomes the first super admin for the dashboard.</p>
        </div>

        <form method="POST" action="" id="setupForm">
            <?php echo $auth->csrfField(); ?>

            <div class="form-grid">
                <div class="form-floating">
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                    <label for="first_name"><i class="far fa-user me-2"></i>First name</label>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                    <label for="last_name"><i class="far fa-user me-2"></i>Last name</label>
                </div>

                <div class="form-floating full">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                    <label for="username"><i class="far fa-id-badge me-2"></i>Username</label>
                </div>

                <div class="form-floating full">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    <label for="email"><i class="far fa-envelope me-2"></i>Email address</label>
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" minlength="8" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password" minlength="8" required>
                    <label for="confirm_password"><i class="fas fa-shield-alt me-2"></i>Confirm password</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login" id="createBtn">
                <i class="fas fa-arrow-right me-2"></i>Create Admin
            </button>
        </form>

        <div class="helper-link">
            <a href="login.php">Back to sign in</a>
        </div>

        <div class="footer-copy">
            &copy; <?php echo date('Y'); ?> TPV Construction and Services LTD. All rights reserved.
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$('#setupForm').on('submit', function() {
    var btn = document.getElementById('createBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
});
</script>
</body>
</html>
