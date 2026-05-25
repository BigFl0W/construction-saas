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

$redirectUrl = 'project_stages.php';
if (!empty($_GET['project_id'])) {
    $redirectUrl .= '?project_id=' . urlencode($_GET['project_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                $project->deleteStage($_POST['id']);
                $_SESSION['toast_success'] = 'Stage deleted successfully.';
            } elseif (isset($_POST['id']) && !empty($_POST['id'])) {
                $project->updateStage($_POST['id'], $_POST);
                $_SESSION['toast_success'] = 'Stage updated successfully.';
            } else {
                $_POST['sort_order'] = $_POST['sort_order'] ?? 0;
                $project->addStage($_POST);
                $_SESSION['toast_success'] = 'Stage created successfully.';
            }
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: ' . $redirectUrl);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!$auth->verifyCSRF($_GET['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            $project->deleteStage($_GET['id']);
            $_SESSION['toast_success'] = 'Stage deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: ' . $redirectUrl);
    exit;
}

$filterProject = $_GET['project_id'] ?? null;
$stages = $db->query(
    "SELECT ps.*, p.name as project_name, p.project_number 
     FROM project_stages ps 
     JOIN projects p ON ps.project_id = p.id 
     WHERE p.deleted_at IS NULL" . 
    ($filterProject ? " AND ps.project_id = :project_id" : "") . 
    " ORDER BY p.name, ps.sort_order, ps.planned_start",
    $filterProject ? ['project_id' => $filterProject] : []
)->fetchAll();

$projects = $db->query("SELECT id, name, project_number FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$editStage = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editStage = $db->query("SELECT * FROM project_stages WHERE id = :id", ['id' => (int)$_GET['edit']])->fetch();
}

$totalStages = count($stages);
$inProgress = count(array_filter($stages, fn($s) => $s['status'] === 'in_progress'));
$completed = count(array_filter($stages, fn($s) => $s['status'] === 'completed'));
$delayed = count(array_filter($stages, fn($s) => $s['status'] === 'delayed'));
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'project_stages';
$pageTitle = 'TPV Construction and Services LTD · Project Stages';
require 'inc/admin_header.php';
?>
<style>
.project-stages-page .filter-chip {
    background: #fff;
    color: #526277;
    border-color: #d8e0eb !important;
    padding: 0.42rem 0.9rem;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.03);
}
.project-stages-page .filter-chip.active,
.project-stages-page .filter-chip:hover {
    background: #14213d;
    color: #fff;
    border-color: #14213d !important;
}
.project-stages-page .metric-tile {
    background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
    border: 1px solid #e6ecf3;
    border-radius: 22px;
    padding: 1.15rem 1.15rem 1rem;
    min-height: 116px;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.05);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.project-stages-page .metric-tile .value {
    font-size: 1.75rem;
    line-height: 1;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
}
.project-stages-page .metric-tile .label {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
    color: #6b7a90;
}
.project-stages-page .stages-card {
    border: 1px solid #e6ecf3;
    border-radius: 26px;
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.05);
}
.project-stages-page .stages-card .card-header {
    background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
    border-bottom: 1px solid #edf2f7;
}
.project-stages-page .table-toolbar {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 1rem;
    align-items: center;
}
.project-stages-page .table-search {
    max-width: 440px;
}
.project-stages-page .table-responsive-wrapper {
    border: 1px solid #edf2f7;
    border-radius: 22px;
    overflow: hidden;
}
.project-stages-page table.table {
    margin-bottom: 0;
}
.project-stages-page table.table thead th {
    background: #f8fafc;
    border-bottom: 1px solid #e8eef5;
    color: #64748b;
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 1rem 0.9rem;
}
.project-stages-page table.table tbody td {
    padding: 1rem 0.9rem;
    vertical-align: middle;
    border-color: #edf2f7;
    color: #213046;
}
.project-stages-page table.table tbody tr:hover {
    background: #fbfdff;
}
.project-stages-page table.table tbody tr {
    background: #ffffff;
}
.project-stages-page .stage-project-cell {
    display: flex;
    flex-direction: column;
    gap: 0.18rem;
    min-width: 0;
}
.project-stages-page .stage-project-name {
    color: #0f172a;
    font-size: 0.95rem;
    line-height: 1.38;
    font-weight: 700;
    letter-spacing: -0.01em;
}
.project-stages-page .stage-project-meta {
    color: #7b8aa0;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.project-stages-page .status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.16rem 0.48rem;
    border-radius: 12px;
    font-size: 0.56rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    border: 1px solid transparent;
    text-transform: none;
    min-width: 0;
    white-space: nowrap;
    line-height: 1.2;
}
.project-stages-page .status-pending { background: #fff8e1; border-color: #f7df9a; color: #9a6700; }
.project-stages-page .status-in_progress { background: #edf4ff; border-color: #cad9ff; color: #1d4ed8; }
.project-stages-page .status-completed { background: #ebf8ef; border-color: #cbe8d2; color: #15803d; }
.project-stages-page .status-delayed { background: #fef0f0; border-color: #f6cccc; color: #b91c1c; }
.project-stages-page .progress.progress-thin {
    height: 6px;
    border-radius: 999px;
    background: #eaf0f6;
}
.project-stages-page .progress.progress-thin .progress-bar {
    border-radius: 999px;
}
@media (max-width: 991.98px) {
    .project-stages-page .table-toolbar {
        grid-template-columns: 1fr;
    }
    .project-stages-page .table-search {
        max-width: none;
    }
}
@media (max-width: 767.98px) {
    .project-stages-page .metric-tile {
        min-height: 102px;
        border-radius: 18px;
        padding: 1rem;
    }
    .project-stages-page .metric-tile .value {
        font-size: 1.45rem;
    }
    .project-stages-page .stages-card {
        border-radius: 20px;
    }
    .project-stages-page .filter-chip {
        width: calc(50% - 0.3rem);
        justify-content: center;
    }
}
</style>
<div class="project-stages-page">

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="projects.php">Projects</a></li>
                                <li class="breadcrumb-item active">Project Stages</li>
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
                        <div class="col-md-5">
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="btn btn-sm rounded-pill border filter-chip active" data-filter="all">All stages</span>
                                <span class="btn btn-sm rounded-pill border filter-chip" data-filter="in_progress">In progress</span>
                                <span class="btn btn-sm rounded-pill border filter-chip" data-filter="pending">Pending</span>
                                <span class="btn btn-sm rounded-pill border filter-chip" data-filter="completed">Completed</span>
                                <span class="btn btn-sm rounded-pill border filter-chip" data-filter="delayed">Delayed</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#stageModal">
                                <i class="fas fa-plus me-1" style="width:14px;height:14px"></i> New stage
                            </button>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <div class="metric-tile"><div class="value"><?php echo $totalStages; ?></div><div class="label">Total stages</div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="metric-tile"><div class="value text-success"><?php echo $inProgress; ?></div><div class="label">In progress</div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="metric-tile"><div class="value text-primary"><?php echo $completed; ?></div><div class="label">Completed</div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="metric-tile"><div class="value text-warning"><?php echo $delayed; ?></div><div class="label">Delayed</div></div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                    <div class="card stages-card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center py-3 px-4">
                            <div class="card-title fw-bold fs-5 mb-0">
                                <i class="fas fa-layer-group me-2" style="width:20px;height:20px"></i> Project stages
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-toolbar mb-3">
                                <div class="table-search">
                                    <input type="text" class="form-control rounded-pill" id="searchStages" placeholder="Search stage name, project...">
                                </div>
                            </div>
                            <div class="table-responsive-wrapper">
                                <table class="table table-hover" data-table id="stagesTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Project</th>
                                            <th>Stage name</th>
                                            <th>Planned start</th>
                                            <th>Planned end</th>
                                            <th>Actual start</th>
                                            <th>Actual end</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th data-orderable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stages as $s): ?>
                                        <?php
                                        $statusClass = 'status-' . str_replace([' ', '-'], '_', $s['status']);
                                        $barColor = $s['progress_percent'] > 90 ? 'bg-danger' : ($s['progress_percent'] > 70 ? 'bg-warning' : 'bg-success');
                                        ?>
                                        <tr data-stage-id="<?php echo $s['id']; ?>" data-project="<?php echo $s['project_id']; ?>" data-status="<?php echo $s['status']; ?>">
                                            <td><?php echo $s['id']; ?></td>
                                            <td>
                                                <div class="stage-project-cell">
                                                    <span class="stage-project-name"><?php echo htmlspecialchars($s['project_name']); ?></span>
                                                    <?php if (!empty($s['project_number'])): ?>
                                                        <span class="stage-project-meta"><?php echo htmlspecialchars($s['project_number']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                                            <td><?php echo $s['planned_start'] ?: '—'; ?></td>
                                            <td><?php echo $s['planned_end'] ?: '—'; ?></td>
                                            <td><?php echo $s['actual_start'] ?: '—'; ?></td>
                                            <td><?php echo $s['actual_end'] ?: '—'; ?></td>
                                            <td style="min-width:120px">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2 small"><?php echo $s['progress_percent']; ?>%</span>
                                                    <div class="progress progress-thin w-100">
                                                        <div class="progress-bar <?php echo $barColor; ?>" style="width:<?php echo $s['progress_percent']; ?>%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst(str_replace('_', ' ', $s['status'])); ?></span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="?edit=<?php echo $s['id']; ?><?php echo $filterProject ? '&project_id=' . $filterProject : ''; ?>" class="btn btn-sm btn-light border edit-stage" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>
                                                    <a href="?action=delete&id=<?php echo $s['id']; ?>&csrf_token=<?php echo $auth->generateCSRF(); ?><?php echo $filterProject ? '&project_id=' . $filterProject : ''; ?>" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirmAction(this, 'Delete this stage?')"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
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
            </div>
<!-- Stage Modal -->
<div class="modal fade" id="stageModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $editStage ? 'Edit Stage' : 'New Stage'; ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
          <?php if ($editStage): ?><input type="hidden" name="id" value="<?php echo $editStage['id']; ?>"><?php endif; ?>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Project</label>
              <select name="project_id" class="form-select" required>
                <option value="">Select project</option>
                <?php foreach ($projects as $pr): ?>
                <option value="<?php echo $pr['id']; ?>" <?php echo (($editStage['project_id'] ?? $filterProject) == $pr['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($pr['name'] . ' (' . $pr['project_number'] . ')'); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Stage Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($editStage['name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Planned Start</label>
              <input type="date" name="planned_start" class="form-control" value="<?php echo htmlspecialchars($editStage['planned_start'] ?? ''); ?>" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Planned End</label>
              <input type="date" name="planned_end" class="form-control" value="<?php echo htmlspecialchars($editStage['planned_end'] ?? ''); ?>" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Actual Start</label>
              <input type="date" name="actual_start" class="form-control" value="<?php echo htmlspecialchars($editStage['actual_start'] ?? ''); ?>">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Actual End</label>
              <input type="date" name="actual_end" class="form-control" value="<?php echo htmlspecialchars($editStage['actual_end'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <?php foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'delayed' => 'Delayed'] as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php echo ($editStage['status'] ?? 'pending') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Progress %</label>
              <input type="number" min="0" max="100" name="progress_percent" class="form-control" value="<?php echo htmlspecialchars($editStage['progress_percent'] ?? 0); ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Sort Order</label>
              <input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($editStage['sort_order'] ?? 0); ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editStage['description'] ?? ''); ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($editStage['notes'] ?? ''); ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a href="project_stages.php<?php echo $filterProject ? '?project_id=' . urlencode($filterProject) : ''; ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Stage</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if ($editStage): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new bootstrap.Modal(document.getElementById('stageModal')).show();
});
</script>
<?php endif; ?>
</div>
<?php require 'inc/admin_footer.php'; ?>
