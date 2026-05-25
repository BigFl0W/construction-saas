<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Project.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();
$project = new Project();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                $project->delete($_POST['id']);
                $_SESSION['toast_success'] = 'Project deleted successfully.';
            } elseif (isset($_POST['id']) && !empty($_POST['id'])) {
                $project->update($_POST['id'], $_POST);
                $_SESSION['toast_success'] = 'Project updated successfully.';
            } else {
                $_POST['project_number'] = $project->generateProjectNumber();
                $project->create($_POST);
                $_SESSION['toast_success'] = 'Project created successfully.';
            }
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: projects.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!$auth->verifyCSRF($_GET['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            $project->delete($_GET['id']);
            $_SESSION['toast_success'] = 'Project deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: projects.php');
    exit;
}

$projects = $project->getAll();
$clients = $functions->getOptions('clients', 'id', 'company_name', "deleted_at IS NULL");
$employees = $functions->getOptions('employees', 'id', "CONCAT(first_name, ' ', last_name)", "deleted_at IS NULL AND status = 'active'");
$editItem = null;
$readonlyProject = isset($_GET['readonly']);
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editItem = $project->getById((int)$_GET['edit']);
}

$totalProjects = count($projects);
$activeProjects = count(array_filter($projects, fn($p) => $p['status'] === 'active'));
$totalBudget = array_sum(array_column($projects, 'budget_total'));
$totalUsed = array_sum(array_column($projects, 'budget_used'));
$budgetPct = $totalBudget > 0 ? round(($totalUsed / $totalBudget) * 100) : 0;
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'projects';
$pageTitle = 'TPV Construction and Services LTD · Projects';
require 'inc/admin_header.php';
?>

<style>
.projects-page .projects-card {
    border: 1px solid #e4e9f0;
    border-radius: 24px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    overflow: hidden;
}
.projects-page .projects-card .card-header {
    background: #fff;
    border-bottom: 1px solid #edf2f7;
}
.projects-page .metric-tile {
    background: #fff;
    border: 1px solid #e4e9f0;
    border-radius: 18px;
    padding: 0.9rem 1rem;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
}
.projects-page .metric-tile .value {
    font-size: clamp(1.2rem, 0.9vw + 0.9rem, 1.45rem);
    font-weight: 700;
    line-height: 1;
    color: #0f172a;
    white-space: nowrap;
    letter-spacing: -0.03em;
}
.projects-page .metric-tile .label {
    margin-top: 0.25rem;
    font-size: 0.76rem;
    color: #6b7a8f;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.projects-page .metric-tile .text-end {
    min-width: 0;
}
.projects-page .budget-metric .value {
    font-size: clamp(0.95rem, 0.85vw + 0.7rem, 1.2rem);
}
.projects-page .budget-icon {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: rgba(212, 161, 62, 0.12);
    color: #d4a13e;
    font-size: 0.95rem;
    font-weight: 800;
}
.projects-page .projects-toolbar {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(220px, 0.5fr);
    gap: 0.75rem;
    align-items: center;
    margin-bottom: 1rem;
}
.projects-page .projects-toolbar .form-control,
.projects-page .projects-toolbar .form-select {
    height: 44px;
}
.projects-page .table-controls-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}
.projects-page .table-controls-row .dataTables_length,
.projects-page .table-controls-row .dataTables_filter {
    margin: 0;
}
.projects-page .table-controls-row .dataTables_length label,
.projects-page .table-controls-row .dataTables_filter label {
    margin: 0;
    color: #475569;
    font-weight: 500;
}
.projects-page .table-controls-row select,
.projects-page .table-controls-row input {
    border-radius: 12px !important;
    min-height: 40px;
    border-color: #d8e0ea;
    box-shadow: none !important;
    font-size: 0.8rem;
}
.projects-page .table-responsive-wrapper {
    overflow-x: auto;
}
.projects-page #projectsTable th,
.projects-page #projectsTable td {
    vertical-align: middle;
}
.projects-page #projectsTable th {
    white-space: nowrap;
}
@media (max-width: 991.98px) {
    .projects-page .projects-toolbar {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 767.98px) {
    .projects-page .metric-tile {
        padding: 0.85rem;
        border-radius: 16px;
    }
    .projects-page .metric-tile .value {
        font-size: 1.15rem;
    }
    .projects-page .projects-card {
        border-radius: 20px;
    }
    .projects-page .projects-card .card-header,
    .projects-page .projects-card .card-body {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    .projects-page .projects-toolbar .form-control,
    .projects-page .projects-toolbar .form-select {
        height: 42px;
    }
    .projects-page .table-controls-row {
        flex-direction: column;
        align-items: stretch;
    }
    .projects-page .table-controls-row .dataTables_filter,
    .projects-page .table-controls-row .dataTables_filter label,
    .projects-page .table-controls-row .dataTables_filter input,
    .projects-page .table-controls-row .dataTables_length,
    .projects-page .table-controls-row .dataTables_length label,
    .projects-page .table-controls-row .dataTables_length select {
        width: 100% !important;
    }
}
</style>

<div class="projects-page">

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item active">Projects</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-20">
                    <div class="row m-b-20">
                        <div class="col-md-3 col-sm-6">
                            <div class="metric-tile d-flex align-items-center justify-content-between">
                                <div><i class="fas fa-helmet-safety" style="width: 32px; height: 32px; stroke: #d4a13e"></i></div>
                                <div class="text-end">
                                    <div class="value"><?php echo $totalProjects; ?></div>
                                    <div class="label">Total projects</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="metric-tile d-flex align-items-center justify-content-between">
                                <div><i class="fas fa-activity" style="width: 32px; height: 32px; stroke: #d4a13e"></i></div>
                                <div class="text-end">
                                    <div class="value"><?php echo $activeProjects; ?></div>
                                    <div class="label">Active</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="metric-tile budget-metric d-flex align-items-center justify-content-between">
                                <div><span class="budget-icon">&#8358;</span></div>
                                <div class="text-end">
                                    <div class="value"><?php echo $functions->formatCurrency($totalBudget); ?></div>
                                    <div class="label">Total budget</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="metric-tile d-flex align-items-center justify-content-between">
                                <div><i class="fas fa-chart-line" style="width: 32px; height: 32px; stroke: #d4a13e"></i></div>
                                <div class="text-end">
                                    <div class="value"><?php echo $budgetPct; ?>%</div>
                                    <div class="label">Budget utilized</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                    <div class="card projects-card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center py-3 px-4">
                            <div class="card-title fw-bold fs-5 mb-0">
                                <i class="fas fa-helmet-safety me-2" style="width:20px;height:20px"></i> Construction projects
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#projectModal">
                                    <i class="fas fa-plus me-1" style="width:14px;height:14px"></i> New project
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="projects-toolbar">
                                <div>
                                    <input type="text" class="form-control rounded-pill" id="searchProjects" placeholder="Search project name, number, client...">
                                </div>
                                <div>
                                    <select class="form-select rounded-pill" id="statusFilter">
                                        <option value="">All statuses</option>
                                        <option value="planning">Planning</option>
                                        <option value="active">Active</option>
                                        <option value="on_hold">On hold</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-controls-row">
                                <div id="projectsLengthMount"></div>
                                <div id="projectsFilterMount"></div>
                            </div>
                            <div class="table-responsive-wrapper">
                                <table class="table table-hover" data-table id="projectsTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Project #</th>
                                            <th>Name</th>
                                            <th>Client</th>
                                            <th>Manager</th>
                                            <th>Start - End</th>
                                            <th>Budget</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th data-orderable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($projects as $p): ?>
                                        <?php
                                        $statusClass = 'status-' . str_replace([' ', '-'], '_', $p['status']);
                                        $budgetPct = $p['budget_total'] > 0 ? round(($p['budget_used'] / $p['budget_total']) * 100) : 0;
                                        $barColor = $budgetPct > 90 ? 'bg-danger' : ($budgetPct > 70 ? 'bg-warning' : 'bg-success');
                                        $priorityClass = 'priority-' . $p['priority'];
                                        ?>
                                        <tr>
                                            <td><?php echo $p['id']; ?></td>
                                            <td><code><?php echo htmlspecialchars($p['project_number']); ?></code></td>
                                            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($p['client_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo $p['manager_first'] ? htmlspecialchars($p['manager_first'] . ' ' . $p['manager_last']) : '<span class="text-muted">—</span>'; ?></td>
                                            <td><?php echo $p['start_date'] ? $functions->formatDate($p['start_date'], 'M d, Y') : '—'; ?><br><small class="text-muted">→ <?php echo $p['estimated_end_date'] ? $functions->formatDate($p['estimated_end_date'], 'M d, Y') : '—'; ?></small></td>
                                            <td><?php echo $functions->formatCurrency($p['budget_used']); ?> / <?php echo $functions->formatCurrency($p['budget_total']); ?></td>
                                            <td style="min-width:120px">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2 small"><?php echo $p['progress_percent']; ?>%</span>
                                                    <div class="progress progress-thin w-100">
                                                        <div class="progress-bar <?php echo $barColor; ?>" style="width:<?php echo $p['progress_percent']; ?>%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst(str_replace('_', ' ', $p['status'])); ?></span></td>
                                            <td><span class="<?php echo $priorityClass; ?>"><?php echo ucfirst($p['priority']); ?></span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="?edit=<?php echo $p['id']; ?>&readonly=1" class="btn btn-sm btn-light border view-project" title="View"><i class="fas fa-eye" style="width:14px;height:14px"></i></a>
                                                    <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-light border edit-project" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>
                                                    <a href="?action=delete&id=<?php echo $p['id']; ?>&csrf_token=<?php echo $auth->generateCSRF(); ?>" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirmAction(this, 'Delete this project?')"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                    <div class="card projects-card">
                        <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
                            <div class="card-title fw-bold fs-5 mb-0">
                                <i class="fas fa-layer-group me-2" style="width:20px;height:20px"></i> Project details
                            </div>
                        </div>
                        <div class="card-body p-4" id="projectDetailPanel">
                            <p class="text-muted text-center mb-0">Select a project to view details.</p>
                        </div>
                    </div>
                </div>
            </div>
<!-- GENERATED MODAL -->
<div class="modal fade" id="projectModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Project</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken() ?? ''; ?>">
          <input type="hidden" name="action" value="<?php echo $editItem ? 'update' : 'create'; ?>">
          <?php if(isset($editItem) && isset($editItem['id'])): ?>
            <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
            <input type="hidden" name="project_number" value="<?php echo htmlspecialchars($editItem['project_number']); ?>">
          <?php else: ?>
            <input type="hidden" name="id" id="projectModal_id" value="">
          <?php endif; ?>
          <div class="row">
            <?php if ($editItem): ?>
            <div class="col-md-6 mb-3">
              <label class="form-label">Project Number</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($editItem['project_number']); ?>" disabled>
            </div>
            <?php endif; ?>
            <div class="col-md-6 mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Client</label>
              <select name="client_id" class="form-select" required>
                <option value="">Select a Client</option>
                <?php
                foreach ($clients as $client) {
                    $clientValue = $client['value'] ?? '';
                    $clientText = $client['text'] ?? '';
                    $selected = (isset($editItem['client_id']) && $editItem['client_id'] == $clientValue) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars((string)$clientValue) . '" ' . $selected . '>' . htmlspecialchars((string)$clientText) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Project Manager</label>
              <select name="project_manager_id" class="form-select">
                <option value="">No manager</option>
                <?php foreach ($employees as $employee): ?>
                <?php
                $employeeValue = $employee['value'] ?? '';
                $employeeText = $employee['text'] ?? '';
                ?>
                <option value="<?php echo htmlspecialchars((string)$employeeValue); ?>" <?php echo (($editItem['project_manager_id'] ?? '') == $employeeValue) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars((string)$employeeText); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Start Date</label>
              <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($editItem['start_date'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Estimated End Date</label>
              <input type="date" name="estimated_end_date" class="form-control" value="<?php echo htmlspecialchars($editItem['estimated_end_date'] ?? ''); ?>">
            </div>
            <?php if ($editItem): ?>
            <div class="col-md-6 mb-3">
              <label class="form-label">Actual End Date</label>
              <input type="date" name="actual_end_date" class="form-control" value="<?php echo htmlspecialchars($editItem['actual_end_date'] ?? ''); ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-6 mb-3">
              <label class="form-label">Budget Total</label>
              <input type="number" step="0.01" name="budget_total" class="form-control" value="<?php echo htmlspecialchars($editItem['budget_total'] ?? '0'); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <?php foreach (['planning' => 'Planning', 'active' => 'Active', 'on_hold' => 'On hold', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php echo ($editItem['status'] ?? 'planning') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-select">
                <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php echo ($editItem['priority'] ?? 'medium') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Progress %</label>
              <input type="number" min="0" max="100" name="progress_percent" class="form-control" value="<?php echo htmlspecialchars($editItem['progress_percent'] ?? 0); ?>">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Location Address</label>
              <input type="text" name="location_address" class="form-control" value="<?php echo htmlspecialchars($editItem['location_address'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($editItem['city'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">State</label>
              <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($editItem['state'] ?? ''); ?>">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($editItem['notes'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="projects.php" class="btn btn-secondary"><?php echo $readonlyProject ? 'Close' : 'Cancel'; ?></a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>


<?php if (isset($_GET['edit']) && isset($editItem) && $editItem): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var myModal = new bootstrap.Modal(document.getElementById('projectModal'));
  myModal.show();
});
</script>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var projectsTable = document.getElementById('projectsTable');
  if (!projectsTable) return;

  var wrapper = projectsTable.closest('.dataTables_wrapper');
  if (!wrapper) return;

  var lengthWrapper = wrapper.querySelector('.dataTables_length');
  var filterWrapper = wrapper.querySelector('.dataTables_filter');
  var lengthMount = document.getElementById('projectsLengthMount');
  var filterMount = document.getElementById('projectsFilterMount');

  if (lengthWrapper && lengthMount) {
    lengthMount.appendChild(lengthWrapper);
  }
  if (filterWrapper && filterMount) {
    filterMount.appendChild(filterWrapper);
  }
});
</script>
<?php require 'inc/admin_footer.php'; ?>
</div>
