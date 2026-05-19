<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$filterUser = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$filterAction = isset($_GET['action']) ? $_GET['action'] : null;
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;

try {
    $sql = "SELECT al.*, u.first_name, u.last_name, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id WHERE 1=1";
    $params = [];
    if ($filterUser) { $sql .= " AND al.user_id = :user_id"; $params['user_id'] = $filterUser; }
    if ($filterAction) { $sql .= " AND al.action = :action"; $params['action'] = $filterAction; }
    if ($dateFrom) { $sql .= " AND DATE(al.created_at) >= :date_from"; $params['date_from'] = $dateFrom; }
    if ($dateTo) { $sql .= " AND DATE(al.created_at) <= :date_to"; $params['date_to'] = $dateTo; }
    $sql .= " ORDER BY al.created_at DESC LIMIT 1000";
    $items = $db->query($sql, $params)->fetchAll();

    $users = $db->query("SELECT id, first_name, last_name, username FROM users WHERE deleted_at IS NULL ORDER BY first_name")->fetchAll();
    $actions = $db->query("SELECT DISTINCT action FROM activity_logs ORDER BY action")->fetchAll();
} catch (Exception $e) {
    $_SESSION['toast_error'] = $e->getMessage();
    header('Location: activity.php');
    exit;
}
$pageActive = 'activity';
$pageTitle = 'TPV Construction and Services LTD · Activity Log';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item active">Activity Log</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <div class="filter-section">
                        <form method="GET" class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>" <?php echo $filterUser == $u['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name'] . ' (' . $u['username'] . ')'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Action</label>
                                <select name="action" class="form-control">
                                    <option value="">All Actions</option>
                                    <?php foreach ($actions as $a): ?>
                                    <option value="<?php echo htmlspecialchars($a['action']); ?>" <?php echo $filterAction === $a['action'] ? 'selected' : ''; ?>><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($a['action']))); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo $dateFrom ?? date('Y-m-d', strtotime('-30 days')); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo $dateTo ?? date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                            </div>
                            <div class="col-md-1">
                                <a href="activity.php" class="btn btn-secondary btn-sm w-100">Clear</a>
                            </div>
                        </form>
                    </div>

                    <div class="card no-border">
                        <div class="card-header card-header-custom">
                            <span class="card-title font-montserrat all-caps">Activity Log</span>
                            <small class="text-muted m-l-10">Last 1000 entries</small>
                        </div>
                        <div class="card-body">
                            <div class="scroll-table">
                                <table id="activityTable" class="table table-hover table-condensed" data-table style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr class="log-entry">
                                            <td class="text-nowrap"><?php echo date('M d, Y g:i A', strtotime($item['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? $item['username'] ?? 'System')); ?></td>
                                            <td><span class="activity-action"><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($item['action']))); ?></span></td>
                                            <td><?php echo htmlspecialchars($item['description'] ?? '-'); ?></td>
                                            <td><code><?php echo htmlspecialchars($item['ip_address'] ?? '-'); ?></code></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($items)): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No activity logs found</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
