<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                $db->query("DELETE FROM project_budgets WHERE id = :id", ['id' => $_POST['id']]);
                $_SESSION['toast_success'] = 'Budget item deleted successfully.';
            } elseif (isset($_POST['id']) && !empty($_POST['id'])) {
                $db->query("UPDATE project_budgets SET project_id = :project_id, category = :category, allocated_amount = :allocated_amount, spent_amount = :spent_amount, notes = :notes, updated_at = NOW() WHERE id = :id", [
                    'id' => $_POST['id'], 'project_id' => $_POST['project_id'], 'category' => $_POST['category'],
                    'allocated_amount' => $_POST['allocated_amount'] ?? 0, 'spent_amount' => $_POST['spent_amount'] ?? 0,
                    'notes' => $_POST['notes'] ?? null
                ]);
                $_SESSION['toast_success'] = 'Budget item updated successfully.';
            } else {
                $db->query("INSERT INTO project_budgets (project_id, category, allocated_amount, spent_amount, notes, created_at, updated_at) VALUES (:project_id, :category, :allocated_amount, :spent_amount, :notes, NOW(), NOW())", [
                    'project_id' => $_POST['project_id'], 'category' => $_POST['category'],
                    'allocated_amount' => $_POST['allocated_amount'] ?? 0, 'spent_amount' => $_POST['spent_amount'] ?? 0,
                    'notes' => $_POST['notes'] ?? null
                ]);
                $_SESSION['toast_success'] = 'Budget item created successfully.';
            }
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: project_budget.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!$auth->verifyCSRF($_GET['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            $db->query("DELETE FROM project_budgets WHERE id = :id", ['id' => $_GET['id']]);
            $_SESSION['toast_success'] = 'Budget item deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: project_budget.php');
    exit;
}

$filterProject = $_GET['project_id'] ?? null;

$budgets = $db->query(
    "SELECT pb.*, p.name as project_name, p.project_number, 
     COALESCE((SELECT SUM(allocated_amount) FROM project_budgets WHERE project_id = p.id), p.budget_total) as project_budget_total, 
     COALESCE((SELECT SUM(spent_amount) FROM project_budgets WHERE project_id = p.id), 0) as project_budget_used
     FROM project_budgets pb
     JOIN projects p ON pb.project_id = p.id
     WHERE p.deleted_at IS NULL" .
    ($filterProject ? " AND pb.project_id = :project_id" : "") .
    " ORDER BY p.name, pb.category",
    $filterProject ? ['project_id' => $filterProject] : []
)->fetchAll();

$projects = $db->query("SELECT id, name, project_number FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$editBudget = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editBudget = $db->query("SELECT * FROM project_budgets WHERE id = :id", ['id' => (int)$_GET['edit']])->fetch();
}

// Compute per-project budget summaries
$projectSummaries = [];
foreach ($budgets as $b) {
    $pid = $b['project_id'];
    if (!isset($projectSummaries[$pid])) {
        $projectSummaries[$pid] = [
            'project_name' => $b['project_name'],
            'project_number' => $b['project_number'],
            'total_allocated' => 0,
            'total_spent' => 0,
            'project_budget_total' => $b['project_budget_total'],
            'project_budget_used' => $b['project_budget_used'],
            'categories' => []
        ];
    }
    $projectSummaries[$pid]['total_allocated'] += floatval($b['allocated_amount']);
    $projectSummaries[$pid]['total_spent'] += floatval($b['spent_amount']);
    $projectSummaries[$pid]['categories'][] = $b;
}

$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'project_budget';
$pageTitle = 'TPV Construction and Services LTD · Project Budget';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="projects.php">Projects</a></li>
                                <li class="breadcrumb-item active">Project Budget</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-20">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Filter by project</label>
                            <select class="form-select rounded-pill" id="projectFilter" onchange="window.location='?project_id=' + this.value">
                                <option value="">All projects</option>
                                <?php foreach ($projects as $pr): ?>
                                <option value="<?php echo $pr['id']; ?>" <?php echo $filterProject == $pr['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($pr['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-8 text-end">
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#budgetModal">
                                <i class="fas fa-plus me-1" style="width:14px;height:14px"></i> Add budget category
                            </button>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="metric-tile"><div class="value"><?php echo count($budgets); ?></div><div class="label">Budget items</div></div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-tile"><div class="value"><?php echo count($projectSummaries); ?></div><div class="label">Projects tracked</div></div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-tile"><div class="value text-success"><?php echo $functions->formatCurrency(array_sum(array_column($budgets, 'allocated_amount'))); ?></div><div class="label">Total allocated</div></div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-tile"><div class="value text-warning"><?php echo $functions->formatCurrency(array_sum(array_column($budgets, 'spent_amount'))); ?></div><div class="label">Total spent</div></div>
                        </div>
                    </div>
                </div>

                <?php foreach ($projectSummaries as $pid => $summary): ?>
                <?php
                $pct = $summary['total_allocated'] > 0 ? round(($summary['total_spent'] / $summary['total_allocated']) * 100) : 0;
                $barColor = $pct > 90 ? 'bg-danger' : ($pct > 70 ? 'bg-warning' : 'bg-success');
                $remaining = $summary['total_allocated'] - $summary['total_spent'];
                $remainingClass = $remaining < 0 ? 'budget-over' : ($remaining < $summary['total_allocated'] * 0.1 ? 'budget-warning' : 'budget-positive');
                $overallPct = $summary['project_budget_total'] > 0 ? round(($summary['project_budget_used'] / $summary['project_budget_total']) * 100) : 0;
                ?>
                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center py-3 px-4">
                            <div class="card-title fw-bold fs-5 mb-0">
                                <i class="fas fa-dollar-sign me-2" style="width:20px;height:20px"></i>
                                <?php echo htmlspecialchars($summary['project_name']); ?>
                                <code class="ms-2"><?php echo htmlspecialchars($summary['project_number']); ?></code>
                            </div>
                            <span class="badge bg-light text-dark p-2">
                                Budget: <?php echo $functions->formatCurrency($summary['project_budget_used']); ?> / <?php echo $functions->formatCurrency($summary['project_budget_total']); ?> (<?php echo $overallPct; ?>%)
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="fw-semibold me-2">Overall project budget</span>
                                        <span class="ms-auto fw-bold"><?php echo $functions->formatCurrency($summary['project_budget_used']); ?> / <?php echo $functions->formatCurrency($summary['project_budget_total']); ?></span>
                                    </div>
                                    <div class="progress progress-thin">
                                        <div class="progress-bar <?php echo $overallPct > 90 ? 'bg-danger' : ($overallPct > 70 ? 'bg-warning' : 'bg-success'); ?>" style="width:<?php echo $overallPct; ?>%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Category budget allocated</small>
                                    <strong><?php echo $functions->formatCurrency($summary['total_allocated']); ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Category budget spent</small>
                                    <strong><?php echo $functions->formatCurrency($summary['total_spent']); ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Remaining</small>
                                    <strong class="<?php echo $remainingClass; ?>"><?php echo $functions->formatCurrency($remaining); ?></strong>
                                </div>
                            </div>

                            <div class="progress progress-thin mb-4">
                                <div class="progress-bar <?php echo $barColor; ?>" style="width:<?php echo $pct; ?>%"></div>
                            </div>

                            <div class="table-responsive-wrapper">
                                <table class="table table-hover data-table" id="budgetTable">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">Category</th>
                                            <th data-priority="3">Allocated</th>
                                            <th data-priority="4">Spent</th>
                                            <th data-priority="5">Remaining</th>
                                            <th data-priority="6">Usage</th>
                                            <th data-priority="7">Notes</th>
                                            <th data-priority="1" data-orderable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($summary['categories'] as $b): ?>
                                        <?php
                                        $catPct = $b['allocated_amount'] > 0 ? round(($b['spent_amount'] / $b['allocated_amount']) * 100) : 0;
                                        $catBarColor = $catPct > 90 ? 'bg-danger' : ($catPct > 70 ? 'bg-warning' : 'bg-success');
                                        $catRemaining = $b['allocated_amount'] - $b['spent_amount'];
                                        $catRemClass = $catRemaining < 0 ? 'budget-over' : ($catRemaining < $b['allocated_amount'] * 0.1 ? 'budget-warning' : 'budget-positive');
                                        ?>
                                        <tr>
                                            <td><span class="category-badge"><?php echo htmlspecialchars($b['category']); ?></span></td>
                                            <td><?php echo $functions->formatCurrency($b['allocated_amount']); ?></td>
                                            <td><?php echo $functions->formatCurrency($b['spent_amount']); ?></td>
                                            <td class="<?php echo $catRemClass; ?> fw-semibold"><?php echo $functions->formatCurrency($catRemaining); ?></td>
                                            <td style="min-width:120px">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2 small"><?php echo $catPct; ?>%</span>
                                                    <div class="progress progress-thin w-100">
                                                        <div class="progress-bar <?php echo $catBarColor; ?>" style="width:<?php echo $catPct; ?>%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo htmlspecialchars($b['notes'] ?: '—'); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="?edit=<?php echo $b['id']; ?><?php echo $filterProject ? '&project_id=' . $filterProject : ''; ?>" class="btn btn-sm btn-light border edit-budget" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>
                                                    <a href="?action=delete&id=<?php echo $b['id']; ?>&csrf_token=<?php echo $auth->generateCSRF(); ?><?php echo $filterProject ? '&project_id=' . $filterProject : ''; ?>" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirmAction(this, 'Delete this budget category?')"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
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
                <?php endforeach; ?>

                <?php if (empty($projectSummaries)): ?>
                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                    <div class="card">
                        <div class="card-body p-5 text-center">
                            <i class="fas fa-dollar-sign" style="width:48px;height:48px;stroke:#d4a13e" class="mb-3"></i>
                            <h5>No budget data found</h5>
                            <p class="text-muted">Add budget categories to track project spending.</p>
                            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#budgetModal">
                                <i class="fas fa-plus me-1" style="width:14px;height:14px"></i> Add budget category
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
<!-- Budget Modal -->
<div class="modal fade" id="budgetModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $editBudget ? 'Edit Budget Category' : 'Add Budget Category'; ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
          <?php if ($editBudget): ?><input type="hidden" name="id" value="<?php echo $editBudget['id']; ?>"><?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Project</label>
            <select name="project_id" class="form-select" required>
              <option value="">Select project</option>
              <?php foreach ($projects as $pr): ?>
              <option value="<?php echo $pr['id']; ?>" <?php echo (($editBudget['project_id'] ?? $filterProject) == $pr['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($pr['name'] . ' (' . $pr['project_number'] . ')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($editBudget['category'] ?? ''); ?>" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Allocated Amount</label>
              <input type="number" step="0.01" name="allocated_amount" class="form-control" value="<?php echo htmlspecialchars($editBudget['allocated_amount'] ?? 0); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Spent Amount</label>
              <input type="number" step="0.01" name="spent_amount" class="form-control" value="<?php echo htmlspecialchars($editBudget['spent_amount'] ?? 0); ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($editBudget['notes'] ?? ''); ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a href="project_budget.php<?php echo $filterProject ? '?project_id=' . urlencode($filterProject) : ''; ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Budget</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if ($editBudget): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new bootstrap.Modal(document.getElementById('budgetModal')).show();
});
</script>
<?php endif; ?>
<?php require 'inc/admin_footer.php'; ?>
