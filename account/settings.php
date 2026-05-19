<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Settings.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$settings = new Settings();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        if ($_POST['action'] === 'update' && isset($_POST['settings']) && is_array($_POST['settings'])) {
            foreach ($_POST['settings'] as $key => $value) {
                $group = $_POST['groups'][$key] ?? 'general';
                $settings->set($key, $value, $group);
            }
            $_SESSION['toast_success'] = 'Settings updated successfully.';
        } elseif ($_POST['action'] === 'add') {
            $key = trim($_POST['setting_key']);
            $val = trim($_POST['setting_value']);
            $group = trim($_POST['setting_group']);
            if ($key && $val && $group) {
                $settings->set($key, $val, $group);
                $_SESSION['toast_success'] = 'Setting "' . htmlspecialchars($key) . '" added.';
            } else {
                $_SESSION['toast_error'] = 'All fields required.';
            }
        } elseif ($_POST['action'] === 'delete' && !empty($_POST['setting_key'])) {
            $settings->delete($_POST['setting_key']);
            $_SESSION['toast_success'] = 'Setting deleted.';
        }
    }
    header('Location: settings.php');
    exit;
}

$groups = $settings->getGroups();
if (empty($groups)) {
    $settings->set('site_name', 'TPV Construction and Services LTD', 'general');
    $settings->set('site_tagline', 'Building Excellence', 'general');
    $settings->set('contact_email', 'info@tpvconstruction.com', 'general');
    $settings->set('currency', 'USD', 'financial');
    $settings->set('tax_rate', '7.5', 'financial');
    $settings->set('date_format', 'Y-m-d', 'formatting');
    $settings->set('time_format', 'H:i', 'formatting');
    $settings->set('blog_comments_enabled', '1', 'blog');
    $settings->set('blog_auto_approve', '0', 'blog');
    $groups = ['general', 'financial', 'formatting', 'blog'];
}

$allSettings = $settings->getAll();
$grouped = [];
foreach ($allSettings as $s) {
    $g = $s['setting_group'];
    if (!isset($grouped[$g])) $grouped[$g] = [];
    $grouped[$g][] = $s;
}

$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'settings';
$pageTitle = 'TPV Construction and Services LTD · Settings';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item active">System Settings</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        <?php echo $auth->csrfField(); ?>
                        <?php foreach ($grouped as $groupName => $groupSettings): ?>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <?php
                                    $icon = 'settings';
                                    if ($groupName === 'general') $icon = 'globe';
                                    elseif ($groupName === 'financial') $icon = 'dollar-sign';
                                    elseif ($groupName === 'formatting') $icon = 'type';
                                    elseif ($groupName === 'blog') $icon = 'edit';
                                    ?>
                                    <i class="fas fa-cog me-2"></i>
                                    <?php echo htmlspecialchars(ucfirst($groupName)); ?> Settings
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover m-b-0">
                                    <thead>
                                        <tr>
                                            <th style="width:250px;">Key</th>
                                            <th>Value</th>
                                            <th style="width:100px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupSettings as $s): ?>
                                        <tr class="setting-row">
                                            <td><code><?php echo htmlspecialchars($s['setting_key']); ?></code></td>
                                            <td class="setting-value">
                                                <input type="hidden" name="groups[<?php echo htmlspecialchars($s['setting_key']); ?>]" value="<?php echo htmlspecialchars($groupName); ?>">
                                                <?php if (in_array($s['setting_key'], ['blog_comments_enabled', 'blog_auto_approve', 'maintenance_mode'])): ?>
                                                <select name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-select form-select-sm">
                                                    <option value="1" <?php echo $s['setting_value'] === '1' ? 'selected' : ''; ?>>Enabled</option>
                                                    <option value="0" <?php echo $s['setting_value'] === '0' ? 'selected' : ''; ?>>Disabled</option>
                                                </select>
                                                <?php elseif (in_array($s['setting_key'], ['site_description', 'address', 'email_signature', 'invoice_terms', 'invoice_footer'])): ?>
                                                <textarea name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($s['setting_value']); ?></textarea>
                                                <?php elseif (in_array($s['setting_key'], ['tax_rate', 'currency'])): ?>
                                                <input type="text" name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['setting_value']); ?>" style="max-width:150px;">
                                                <?php else: ?>
                                                <input type="text" name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['setting_value']); ?>">
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-client-icon" onclick="return confirmAction(this, 'Delete this setting?', function(){ document.getElementById('deleteKey').value='<?php echo htmlspecialchars($s['setting_key']); ?>'; document.getElementById('deleteForm').submit(); })" title="Delete">
                                                    <i class="fas fa-trash" style="width:14px;height:14px;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Save All Settings</button>
                        </div>
                    </form>

                    <!-- Separate delete form -->
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="setting_key" id="deleteKey">
                        <?php echo $auth->csrfField(); ?>
                    </form>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
