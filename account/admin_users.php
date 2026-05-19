<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireUserType(['admin', 'super_admin']);
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$allowedRoles = ['super_admin', 'admin', 'manager'];
$allowedStatuses = ['active', 'inactive', 'suspended'];
$isSuperAdmin = ($currentUser['user_type'] ?? '') === 'super_admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
        header('Location: admin_users.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_admin') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'admin');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($firstName === '' || $lastName === '' || $username === '' || $email === '' || $password === '') {
                throw new Exception('All admin creation fields are required.');
            }
            if (!$functions->validateEmail($email)) {
                throw new Exception('Please enter a valid email address.');
            }
            if (!in_array($role, $allowedRoles, true)) {
                throw new Exception('Invalid admin role selected.');
            }
            if (!$isSuperAdmin && $role === 'super_admin') {
                throw new Exception('Only a super admin can create another super admin.');
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
                throw new Exception('An admin with that username or email already exists.');
            }

            $existingUser = $db->query(
                "SELECT id FROM users WHERE (username = :username OR email = :email) AND deleted_at IS NULL LIMIT 1",
                ['username' => $username, 'email' => $email]
            )->fetch();
            if ($existingUser) {
                throw new Exception('That username or email is already used by another account.');
            }

            $createdBy = $auth->isAdminAuth() ? $currentUser['id'] : null;
            $db->query(
                "INSERT INTO admins (username, email, password, first_name, last_name, role, status, created_by)
                 VALUES (:username, :email, :password, :first_name, :last_name, :role, 'active', :created_by)",
                [
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'role' => $role,
                    'created_by' => $createdBy
                ]
            );

            $functions->logActivity($currentUser['id'], 'create_admin', 'Created admin account: ' . $username);
            $_SESSION['toast_success'] = 'Admin account created successfully.';
        } elseif ($action === 'update_status') {
            $adminId = (int) ($_POST['admin_id'] ?? 0);
            $status = trim($_POST['status'] ?? '');

            if ($adminId <= 0 || !in_array($status, $allowedStatuses, true)) {
                throw new Exception('Invalid admin status request.');
            }
            if ($auth->isAdminAuth() && $currentUser['id'] === $adminId && $status !== 'active') {
                throw new Exception('You cannot deactivate or suspend your own current admin session.');
            }

            $db->query(
                "UPDATE admins SET status = :status WHERE id = :id AND deleted_at IS NULL",
                ['status' => $status, 'id' => $adminId]
            );

            $functions->logActivity($currentUser['id'], 'update_admin_status', 'Updated admin ID ' . $adminId . ' to ' . $status);
            $_SESSION['toast_success'] = 'Admin status updated.';
        } elseif ($action === 'reset_password') {
            $adminId = (int) ($_POST['admin_id'] ?? 0);
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_new_password'] ?? '';

            if ($adminId <= 0) {
                throw new Exception('Invalid admin selected for password reset.');
            }
            if (strlen($newPassword) < 8) {
                throw new Exception('New password must be at least 8 characters long.');
            }
            if ($newPassword !== $confirmPassword) {
                throw new Exception('New passwords do not match.');
            }

            $db->query(
                "UPDATE admins SET password = :password, login_attempts = 0, locked_until = NULL WHERE id = :id AND deleted_at IS NULL",
                ['password' => password_hash($newPassword, PASSWORD_DEFAULT), 'id' => $adminId]
            );

            $functions->logActivity($currentUser['id'], 'reset_admin_password', 'Reset password for admin ID ' . $adminId);
            $_SESSION['toast_success'] = 'Admin password reset successfully.';
        }
    } catch (Exception $e) {
        $_SESSION['toast_error'] = $e->getMessage();
    }

    header('Location: admin_users.php');
    exit;
}

$admins = $db->query(
    "SELECT a.*, creator.username AS created_by_username
     FROM admins a
     LEFT JOIN admins creator ON a.created_by = creator.id
     WHERE a.deleted_at IS NULL
     ORDER BY a.created_at DESC"
)->fetchAll();

$pageActive = 'admin_users';
$pageTitle = 'TPV Construction and Services LTD · Admin Users';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item active">Admin Users</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <div class="row g-3">
                        <div class="col-xl-5">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0"><i class="fas fa-user-shield me-2"></i>Create Admin Account</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="create_admin">
                                        <?php echo $auth->csrfField(); ?>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First name</label>
                                                <input type="text" name="first_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last name</label>
                                                <input type="text" name="last_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Username</label>
                                                <input type="text" name="username" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Role</label>
                                                <select name="role" class="form-select" required>
                                                    <?php foreach ($allowedRoles as $role): ?>
                                                    <?php if (!$isSuperAdmin && $role === 'super_admin') continue; ?>
                                                    <option value="<?php echo htmlspecialchars($role); ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $role))); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Email address</label>
                                                <input type="email" name="email" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Password</label>
                                                <input type="password" name="password" class="form-control" minlength="8" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Confirm password</label>
                                                <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Create Admin</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-7">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title m-0"><i class="fas fa-users-cog me-2"></i>Existing Admins</h5>
                                    <span class="badge text-bg-light"><?php echo count($admins); ?> total</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Admin</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Last Login</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($admins as $admin): ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold"><?php echo htmlspecialchars(trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''))); ?></div>
                                                        <div class="text-muted small"><?php echo htmlspecialchars($admin['username']); ?> · <?php echo htmlspecialchars($admin['email']); ?></div>
                                                        <div class="text-muted small">Created by <?php echo htmlspecialchars($admin['created_by_username'] ?: 'System'); ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="badge text-bg-dark"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $admin['role']))); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = 'secondary';
                                                        if (($admin['status'] ?? '') === 'active') $statusClass = 'success';
                                                        elseif (($admin['status'] ?? '') === 'inactive') $statusClass = 'warning';
                                                        elseif (($admin['status'] ?? '') === 'suspended') $statusClass = 'danger';
                                                        ?>
                                                        <span class="badge text-bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars(ucfirst($admin['status'])); ?></span>
                                                    </td>
                                                    <td class="text-muted small">
                                                        <?php echo $admin['last_login'] ? htmlspecialchars($functions->timeAgo($admin['last_login'])) : 'Never'; ?>
                                                    </td>
                                                    <td>
                                                        <form method="POST" class="d-flex flex-column gap-2">
                                                            <input type="hidden" name="admin_id" value="<?php echo (int) $admin['id']; ?>">
                                                            <?php echo $auth->csrfField(); ?>
                                                            <input type="hidden" name="action" value="update_status">
                                                            <select name="status" class="form-select form-select-sm">
                                                                <?php foreach ($allowedStatuses as $status): ?>
                                                                <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($admin['status'] === $status) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars(ucfirst($status)); ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update Status</button>
                                                        </form>

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-secondary mt-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#passwordModal<?php echo (int) $admin['id']; ?>"
                                                        >
                                                            Reset Password
                                                        </button>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="passwordModal<?php echo (int) $admin['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reset Password for <?php echo htmlspecialchars($admin['username']); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="action" value="reset_password">
                                                                    <input type="hidden" name="admin_id" value="<?php echo (int) $admin['id']; ?>">
                                                                    <?php echo $auth->csrfField(); ?>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">New password</label>
                                                                        <input type="password" name="new_password" class="form-control" minlength="8" required>
                                                                    </div>
                                                                    <div class="mb-0">
                                                                        <label class="form-label">Confirm new password</label>
                                                                        <input type="password" name="confirm_new_password" class="form-control" minlength="8" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Save New Password</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                                <?php if (empty($admins)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">No admin accounts found yet.</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
