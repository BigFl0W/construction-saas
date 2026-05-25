<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();
$accountTable = $auth->isAdminAuth() ? 'admins' : 'users';
$accountLabel = $auth->isAdminAuth() ? 'Administrator' : 'User';

function profileAvatarInitials(array $user): string {
    $first = trim((string) ($user['first_name'] ?? ''));
    $last = trim((string) ($user['last_name'] ?? ''));
    $username = trim((string) ($user['username'] ?? 'U'));
    $initials = '';

    if ($first !== '') {
        $initials .= mb_substr($first, 0, 1);
    }
    if ($last !== '') {
        $initials .= mb_substr($last, 0, 1);
    }
    if ($initials === '') {
        $initials = mb_substr($username, 0, 2);
    }

    return strtoupper($initials);
}

function deleteProfileAvatarFile(?string $relativePath): void {
    $relativePath = trim((string) $relativePath);
    if ($relativePath === '' || strpos(str_replace('\\', '/', $relativePath), 'uploads/profiles/') !== 0) {
        return;
    }

    $absolutePath = dirname(__DIR__) . '/' . $relativePath;
    if (is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

function handleProfileAvatarUpload(array $user, bool $isAdminAuth): string {
    $file = $_FILES['profile_image'] ?? null;
    if (!is_array($file)) {
        throw new RuntimeException('No avatar upload data was received.');
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Please choose an image to upload.');
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Failed to upload avatar image.');
    }
    if (($file['size'] ?? 0) > MAX_FILE_SIZE) {
        throw new RuntimeException('Avatar image exceeds the 5MB size limit.');
    }

    $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Avatar must be a JPG, PNG, WEBP, or GIF image.');
    }

    $tmpPath = (string) ($file['tmp_name'] ?? '');
    if (@getimagesize($tmpPath) === false) {
        throw new RuntimeException('Uploaded avatar file is not a valid image.');
    }

    $subDirectory = 'profiles/' . ($isAdminAuth ? 'admins' : 'users');
    $targetDirectory = rtrim(UPLOAD_PATH, '/\\') . '/' . $subDirectory . '/';
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
    }

    $targetBaseName = ($isAdminAuth ? 'admin' : 'user') . '-' . (int) ($user['id'] ?? 0) . '-avatar';
    foreach (glob($targetDirectory . $targetBaseName . '.*') ?: [] as $existingFile) {
        if (is_file($existingFile)) {
            @unlink($existingFile);
        }
    }

    $relativePath = 'uploads/' . $subDirectory . '/' . $targetBaseName . '.' . $extension;
    $absolutePath = dirname(__DIR__) . '/' . $relativePath;

    if (!move_uploaded_file($tmpPath, $absolutePath)) {
        throw new RuntimeException('Unable to save avatar image.');
    }

    return $relativePath;
}

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
            $removeProfileImage = isset($_POST['remove_profile_image']) && $_POST['remove_profile_image'] === '1';

            if ($firstName && $lastName && $email && $username) {
                $profileImagePath = $currentUser['profile_image'] ?? null;

                if ($removeProfileImage) {
                    deleteProfileAvatarFile($profileImagePath);
                    $profileImagePath = null;
                }

                if (isset($_FILES['profile_image']) && is_array($_FILES['profile_image']) && ($_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $profileImagePath = handleProfileAvatarUpload($currentUser, $auth->isAdminAuth());
                }

                $db->query(
                    "UPDATE {$accountTable}
                     SET first_name = :fn,
                         last_name = :ln,
                         email = :email,
                         username = :username,
                         profile_image = :profile_image
                     WHERE id = :id",
                    [
                        'fn' => $firstName,
                        'ln' => $lastName,
                        'email' => $email,
                        'username' => $username,
                        'profile_image' => $profileImagePath,
                        'id' => $currentUser['id']
                    ]
                );
                $currentUser['first_name'] = $firstName;
                $currentUser['last_name'] = $lastName;
                $currentUser['email'] = $email;
                $currentUser['username'] = $username;
                $currentUser['profile_image'] = $profileImagePath;
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

            $stmt = $db->query("SELECT password FROM {$accountTable} WHERE id = :id", ['id' => $currentUser['id']]);
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
                $db->query("UPDATE {$accountTable} SET password = :pwd WHERE id = :id", ['pwd' => $hash, 'id' => $currentUser['id']]);
                $message = 'Password changed successfully.';
                $messageType = 'success';
            }
        }
    }
}

$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$profileAvatarUrl = !empty($currentUser['profile_image']) ? tpv_asset_url($currentUser['profile_image']) : '';
$profileAvatarInitials = profileAvatarInitials($currentUser);
$pageActive = 'profile';
$pageTitle = 'TPV Construction and Services LTD · Profile';
require 'inc/admin_header.php';
?>
                <style>
                    .profile-avatar-wrap {
                        width: 104px;
                        height: 104px;
                        border-radius: 24px;
                        overflow: hidden;
                        flex-shrink: 0;
                        border: 1px solid rgba(255,255,255,0.18);
                        background: rgba(255,255,255,0.08);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .profile-avatar {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        display: block;
                    }
                    .profile-avatar-fallback {
                        width: 100%;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 2rem;
                        font-weight: 700;
                        letter-spacing: 0.04em;
                        background: linear-gradient(135deg, #d4a13e 0%, #ef3d43 100%);
                    }
                    .profile-avatar-upload {
                        position: relative;
                        display: inline-flex;
                        cursor: pointer;
                    }
                    .profile-avatar-upload input[type="file"] {
                        position: absolute;
                        inset: 0;
                        opacity: 0;
                        cursor: pointer;
                    }
                    .profile-avatar-upload-badge {
                        position: absolute;
                        right: 8px;
                        bottom: 8px;
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        background: #fff;
                        color: #1a2332;
                        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.18);
                        border: 1px solid rgba(228, 233, 240, 0.95);
                    }
                    .profile-header-copy h3 {
                        color: #1a2332;
                    }
                    .profile-header-copy p {
                        color: #4f5d73;
                    }
                    .profile-header-copy small {
                        color: #7b889c;
                    }
                    .profile-avatar-note {
                        font-size: 0.78rem;
                        color: #6b7a8f;
                    }
                </style>

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
                        <label class="profile-avatar-upload" for="profileImageInput">
                            <div class="profile-avatar-wrap">
                                <?php if ($profileAvatarUrl !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($profileAvatarUrl); ?>" class="profile-avatar" alt="Avatar">
                                <?php else: ?>
                                    <div class="profile-avatar-fallback"><?php echo htmlspecialchars($profileAvatarInitials); ?></div>
                                <?php endif; ?>
                            </div>
                            <span class="profile-avatar-upload-badge"><i class="fas fa-camera"></i></span>
                        </label>
                        <div class="profile-header-copy">
                            <h3 class="mb-1"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h3>
                            <p class="mb-0"><?php echo htmlspecialchars($currentUser['user_type'] ?? 'User'); ?> &middot; <?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></p>
                            <small>Member since <?php echo htmlspecialchars($functions->formatDate($currentUser['created_at'] ?? '', 'M j, Y')); ?></small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0"><i class="fas fa-user me-2"></i> Profile Information</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="update_profile">
                                        <?php echo $auth->csrfField(); ?>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Avatar</label>
                                                <input type="file" id="profileImageInput" name="profile_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif">
                                                <div class="profile-avatar-note mt-2">Optional. Leave empty to keep the current avatar, upload a new one to replace it, or remove it to use your initials instead.</div>
                                                <?php if ($profileAvatarUrl !== ''): ?>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox" name="remove_profile_image" value="1" id="removeProfileImage">
                                                        <label class="form-check-label" for="removeProfileImage">Remove current avatar</label>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
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
                                    <div class="mb-2"><strong>Account type:</strong> <?php echo htmlspecialchars($accountLabel); ?></div>
                                    <div class="mb-2"><strong>Role:</strong> <?php echo htmlspecialchars($currentUser['user_type'] ?? 'N/A'); ?></div>
                                    <div class="mb-2"><strong>Last login:</strong> <?php echo htmlspecialchars($currentUser['last_login'] ? $functions->timeAgo($currentUser['last_login']) : 'Never'); ?></div>
                                    <div class="mb-0"><strong>Status:</strong> <?php echo htmlspecialchars($currentUser['status'] ?? 'Active'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
