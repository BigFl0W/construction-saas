<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid security token.';
        $messageType = 'danger';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_profile') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');

            if ($firstName && $lastName && $email && $username) {
                $db->query(
                    "UPDATE users SET first_name = :fn, last_name = :ln, email = :email, username = :username WHERE id = :id",
                    ['fn' => $firstName, 'ln' => $lastName, 'email' => $email, 'username' => $username, 'id' => $currentUser['id']]
                );
                $currentUser['first_name'] = $firstName;
                $currentUser['last_name'] = $lastName;
                $currentUser['email'] = $email;
                $currentUser['username'] = $username;
                $message = 'Profile updated successfully.';
                $messageType = 'success';
            } else {
                $message = 'All profile fields are required.';
                $messageType = 'danger';
            }
        } elseif ($action === 'change_password') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $stmt = $db->query("SELECT password FROM users WHERE id = :id", ['id' => $currentUser['id']]);
            $userRow = $stmt->fetch();

            if (!password_verify($currentPassword, $userRow['password'])) {
                $message = 'Current password is incorrect.';
                $messageType = 'danger';
            } elseif (strlen($newPassword) < 6) {
                $message = 'New password must be at least 6 characters.';
                $messageType = 'danger';
            } elseif ($newPassword !== $confirmPassword) {
                $message = 'New passwords do not match.';
                $messageType = 'danger';
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $db->query("UPDATE users SET password = :pwd WHERE id = :id", ['pwd' => $hash, 'id' => $currentUser['id']]);
                $message = 'Password changed successfully.';
                $messageType = 'success';
            }
        }
    }
}

$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'profile';
$pageTitle = 'TPV Construction and Services LTD · Profile';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item active">My Profile</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="profile-header d-flex align-items-center gap-3">
                        <img src="assets/img/profiles/avatar.jpg" class="profile-avatar" alt="Avatar">
                        <div>
                            <h3 class="text-white mb-1"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h3>
                            <p class="mb-0 opacity-75"><?php echo htmlspecialchars($currentUser['user_type'] ?? 'User'); ?> &middot; <?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></p>
                            <small class="opacity-50">Member since <?php echo htmlspecialchars($functions->formatDate($currentUser['created_at'] ?? '', 'M j, Y')); ?></small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0"><i class="fas fa-user me-2"></i> Profile Information</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_profile">
                                        <?php echo $auth->csrfField(); ?>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First Name</label>
                                                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($currentUser['first_name'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($currentUser['last_name'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Username</label>
                                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($currentUser['username'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0"><i class="fas fa-lock me-2"></i> Change Password</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="change_password">
                                        <?php echo $auth->csrfField(); ?>
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" name="current_password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="new_password" class="form-control" required minlength="6">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </form>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title m-0"><i class="fas fa-info-circle me-2"></i> Account Info</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2"><strong>User type:</strong> <?php echo htmlspecialchars($currentUser['user_type'] ?? 'N/A'); ?></div>
                                    <div class="mb-2"><strong>Last login:</strong> <?php echo htmlspecialchars($currentUser['last_login'] ? $functions->timeAgo($currentUser['last_login']) : 'Never'); ?></div>
                                    <div class="mb-0"><strong>Status:</strong> <?php echo htmlspecialchars($currentUser['status'] ?? 'Active'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
