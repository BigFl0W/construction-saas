<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Communication.php';
require_once '../classes/Employee.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$comm = new Communication();
$employee = new Employee();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!$auth->verifyCsrfToken($_GET['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $comm->delete((int)$_GET['delete']);
        $_SESSION['toast_success'] = 'Communication deleted successfully.';
    }
    header('Location: communications.php');
    exit;
}

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['toast_error'] = 'Invalid security token.';
        } else {
            $comm->create([
                'project_id' => $_POST['project_id'] ?: null,
                'client_id' => $_POST['client_id'] ?: null,
                'employee_id' => $_POST['employee_id'] ?: null,
                'direction' => $_POST['direction'],
                'type' => $_POST['type'],
                'subject' => $_POST['subject'],
                'content' => $_POST['content'],
                'communication_date' => $_POST['communication_date'],
                'created_by' => $currentUser['id'] ?? null
            ]);
            $_SESSION['toast_success'] = 'Communication created successfully.';
        }
    } elseif ($_POST['action'] === 'update') {
        if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['toast_error'] = 'Invalid security token.';
        } else {
            $comm->update((int)$_POST['id'], [
                'project_id' => $_POST['project_id'] ?: null,
                'client_id' => $_POST['client_id'] ?: null,
                'employee_id' => $_POST['employee_id'] ?: null,
                'direction' => $_POST['direction'],
                'type' => $_POST['type'],
                'subject' => $_POST['subject'],
                'content' => $_POST['content'],
                'communication_date' => $_POST['communication_date']
            ]);
            $_SESSION['toast_success'] = 'Communication updated successfully.';
        }
    }
    header('Location: communications.php');
    exit;
}

// Filters
$filterProject = $_GET['project_id'] ?? null;
$filterClient = $_GET['client_id'] ?? null;
$filterType = $_GET['type'] ?? null;

$communications = $comm->getAll($filterProject, $filterClient, $filterType);

// Get projects, clients for dropdowns
$projects = $db->query("SELECT id, name FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$clients = $db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name")->fetchAll();
$employees = $employee->getAll();
$editCommunication = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editCommunication = $comm->getById((int)$_GET['edit']);
}
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'communications';
$pageTitle = 'TPV Construction and Services LTD · Communications';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="clients.php">Clients</a></li>
                                <li class="breadcrumb-item active">Communications</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <div class="card card-default">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title"><i class="fas fa-comments me-2"></i> Communications Log</div>
                            <div>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#commModal">
                                    <i class="fas fa-plus me-1"></i> Add
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="filter-row mb-3">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small">Project</label>
                                        <select name="project_id" class="form-select form-select-sm">
                                            <option value="">All projects</option>
                                            <?php foreach ($projects as $p): ?>
                                            <option value="<?php echo $p['id']; ?>" <?php echo $filterProject == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Client</label>
                                        <select name="client_id" class="form-select form-select-sm">
                                            <option value="">All clients</option>
                                            <?php foreach ($clients as $c): ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo $filterClient == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['company_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Type</label>
                                        <select name="type" class="form-select form-select-sm">
                                            <option value="">All types</option>
                                            <option value="email" <?php echo $filterType === 'email' ? 'selected' : ''; ?>>Email</option>
                                            <option value="phone" <?php echo $filterType === 'phone' ? 'selected' : ''; ?>>Phone</option>
                                            <option value="meeting" <?php echo $filterType === 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                                            <option value="note" <?php echo $filterType === 'note' ? 'selected' : ''; ?>>Note</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover" data-table id="communicationsTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Direction</th>
                                            <th>Subject</th>
                                            <th>Project</th>
                                            <th>Client</th>
                                            <th>Created By</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($communications)): ?>
                                        <tr><td colspan="8" class="text-center text-muted py-4">No communications found.</td></tr>
                                        <?php else: ?>
                                        <?php foreach ($communications as $c): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($functions->formatDate($c['communication_date'], 'M j, Y')); ?></td>
                                            <td><span class="type-badge"><?php echo htmlspecialchars(ucfirst($c['type'])); ?></span></td>
                                            <td>
                                                <span class="direction-badge direction-<?php echo $c['direction']; ?>">
                                                    <?php echo htmlspecialchars(ucfirst($c['direction'])); ?>
                                                </span>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($functions->truncateText($c['subject'] ?? '', 40)); ?></strong></td>
                                            <td><?php echo htmlspecialchars($c['project_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($c['client_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($c['created_by'] ?? 'System'); ?></td>
                                            <td class="text-end">
                                                <a href="?edit=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-secondary btn-client-icon" title="Edit">
                                                    <i class="fas fa-pen" style="width:14px;height:14px;"></i>
                                                </a>
                                                <a href="?delete=<?php echo $c['id']; ?>&csrf_token=<?php echo $auth->generateCsrfToken(); ?>" class="btn btn-sm btn-outline-danger btn-client-icon" onclick="return confirmAction(this, 'Delete this communication?')">
                                                    <i class="fas fa-trash" style="width:14px;height:14px;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!-- Communication Modal -->
<div class="modal fade" id="commModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $editCommunication ? 'Edit Communication' : 'Add Communication'; ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="<?php echo $editCommunication ? 'update' : 'create'; ?>">
          <?php if ($editCommunication): ?>
          <input type="hidden" name="id" value="<?php echo $editCommunication['id']; ?>">
          <?php endif; ?>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Project</label>
              <select name="project_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($projects as $p): ?>
                <option value="<?php echo $p['id']; ?>" <?php echo ($editCommunication['project_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Client</label>
              <select name="client_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($clients as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo ($editCommunication['client_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['company_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Employee</label>
              <select name="employee_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($employees as $emp): ?>
                <option value="<?php echo $emp['id']; ?>" <?php echo ($editCommunication['employee_id'] ?? '') == $emp['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Direction</label>
              <select name="direction" class="form-select" required>
                <option value="outbound" <?php echo ($editCommunication['direction'] ?? 'outbound') === 'outbound' ? 'selected' : ''; ?>>Outbound</option>
                <option value="inbound" <?php echo ($editCommunication['direction'] ?? '') === 'inbound' ? 'selected' : ''; ?>>Inbound</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Type</label>
              <select name="type" class="form-select" required>
                <?php foreach (['email' => 'Email', 'phone' => 'Phone', 'meeting' => 'Meeting', 'note' => 'Note'] as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php echo ($editCommunication['type'] ?? '') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Date</label>
              <input type="datetime-local" name="communication_date" class="form-control" value="<?php echo htmlspecialchars(isset($editCommunication['communication_date']) ? date('Y-m-d\TH:i', strtotime($editCommunication['communication_date'])) : date('Y-m-d\TH:i')); ?>" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($editCommunication['subject'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5"><?php echo htmlspecialchars($editCommunication['content'] ?? ''); ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a href="communications.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if ($editCommunication): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new bootstrap.Modal(document.getElementById('commModal')).show();
});
</script>
<?php endif; ?>
<?php require 'inc/admin_footer.php'; ?>
