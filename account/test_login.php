<?php
require_once '../config/config.php';
require_once '../classes/Database.php';

$dbStatus = 'Unknown';
$dbMessage = '';
$userCount = 0;
$users = [];
$errorOutput = '';

try {
    $db = Database::getInstance();
    $dbStatus = 'Connected';
    $dbMessage = 'Database connection successful';

    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $userCount = $stmt->fetch()['total'];

    $stmt = $db->query("SELECT id, username, email, first_name, last_name, user_type, status, created_at FROM users ORDER BY id LIMIT 20");
    $users = $stmt->fetchAll();

} catch (Exception $e) {
    $dbStatus = 'Error';
    $dbMessage = $e->getMessage();
    $errorOutput = $e->__toString();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPV Construction and Services LTD · System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 2rem; }
        .card { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 1.5rem; }
        .card-header { background: white; border-bottom: 1px solid #eaeef2; font-weight: 600; }
        .status-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .status-ok { background: #10b981; }
        .status-err { background: #ef4444; }
        .status-warn { background: #f59e0b; }
        .table th { font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.4px; color: #4a5568; }
        .table td { vertical-align: middle; }
        pre { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 8px; overflow-x: auto; }
        .badge { font-size: 0.7rem; padding: 0.25rem 0.75rem; border-radius: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fw-bold m-0"><span class="text-primary">TPV Construction and Services LTD</span> · System Diagnostics</h2>
            <a href="login.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Go to Login</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <span class="status-dot <?php echo $dbStatus === 'Connected' ? 'status-ok' : 'status-err'; ?>"></span>
                        <div>
                            <div class="fw-bold">Database</div>
                            <small class="text-muted"><?php echo htmlspecialchars($dbStatus); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <span class="status-dot <?php echo $userCount > 0 ? 'status-ok' : 'status-warn'; ?>"></span>
                        <div>
                            <div class="fw-bold">Users</div>
                            <small class="text-muted"><?php echo $userCount; ?> registered</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <span class="status-dot status-ok"></span>
                        <div>
                            <div class="fw-bold">PHP Version</div>
                            <small class="text-muted"><?php echo phpversion(); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($errorOutput): ?>
        <div class="card">
            <div class="card-header text-danger"><strong>Error Details</strong></div>
            <div class="card-body">
                <pre><?php echo htmlspecialchars($errorOutput); ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Users Table</span>
                <span class="badge bg-light text-dark"><?php echo $userCount; ?> total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover m-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
                            <?php else: ?>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><code><?php echo htmlspecialchars($u['username']); ?></code></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['first_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($u['last_name'] ?? '-'); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($u['user_type'] ?? 'user'); ?></span></td>
                                <td><span class="badge bg-<?php echo $u['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($u['status']); ?></span></td>
                                <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Database Connection Info</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9"><span class="badge bg-<?php echo $dbStatus === 'Connected' ? 'success' : 'danger'; ?>"><?php echo $dbStatus; ?></span></dd>

                    <dt class="col-sm-3">Message</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($dbMessage); ?></dd>

                    <dt class="col-sm-3">PHP Version</dt>
                    <dd class="col-sm-9"><?php echo phpversion(); ?></dd>

                    <dt class="col-sm-3">Server Time</dt>
                    <dd class="col-sm-9"><?php echo date('Y-m-d H:i:s'); ?></dd>
                </dl>
            </div>
        </div>

        <div class="text-center text-muted small mt-4 mb-2">
            &copy;2025 TPV Construction and Services LTD &middot; System Test Page
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
