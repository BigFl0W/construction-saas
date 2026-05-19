<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Report.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();
$report = new Report();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
        header('Location: daily_reports.php');
        exit;
    }
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $report->deleteDailyReport($_POST['id']);
            $_SESSION['toast_success'] = 'Daily report deleted successfully.';
        } elseif (isset($_POST['id']) && !empty($_POST['id'])) {
            $report->updateDailyReport($_POST['id'], $_POST);
            $_SESSION['toast_success'] = 'Daily report updated successfully.';
        } else {
            $_POST['created_by'] = $currentUser['id'] ?? 1;
            $report->createDailyReport($_POST);
            $_SESSION['toast_success'] = 'Daily report created successfully.';
        }
        header('Location: daily_reports.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        header('Location: daily_reports.php');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!$auth->verifyCSRF($_GET['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            $report->deleteDailyReport($_GET['id']);
            $_SESSION['toast_success'] = 'Daily report deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: daily_reports.php');
    exit;
}

$filterProject = $_GET['project_id'] ?? null;
$filterDate = $_GET['date'] ?? null;
$reports = $report->getDailyReports($filterProject, $filterDate, $filterDate);

$projects = $db->query("SELECT id, name, project_number FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$employees = $db->query("SELECT id, first_name, last_name FROM employees WHERE deleted_at IS NULL AND status = 'active' ORDER BY first_name")->fetchAll();
$editReport = null;
$readonlyReport = isset($_GET['readonly']);
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editReport = $report->getDailyReportById((int)$_GET['edit']);
}

$totalReports = count($reports);
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'daily_reports';
$pageTitle = 'TPV Construction and Services LTD · Daily Reports';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="projects.php">Projects</a></li>
                                <li class="breadcrumb-item active">Daily Reports</li>
                            </ol>
                        </div>
                    </div>
                </div>



                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-20">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Filter by project</label>
                            <select class="form-select rounded-pill" id="projectFilter" onchange="filterReports()">
                                <option value="">All projects</option>
                                <?php foreach ($projects as $pr): ?>
                                <option value="<?php echo $pr['id']; ?>" <?php echo $filterProject == $pr['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($pr['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Filter by date</label>
                            <input type="date" class="form-control rounded-pill" id="dateFilter" value="<?php echo htmlspecialchars($filterDate ?? ''); ?>" onchange="filterReports()">
                        </div>
                        <div class="col-md-5 text-end">
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#reportModal">
                                <i class="fas fa-plus me-1" style="width:14px;height:14px"></i> New daily report
                            </button>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="metric-tile"><div class="value"><?php echo $totalReports; ?></div><div class="label">Total reports</div></div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-tile"><div class="value"><?php echo count(array_unique(array_column($reports, 'project_id'))); ?></div><div class="label">Projects covered</div></div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-tile"><div class="value"><?php echo $filterDate ?: 'All dates'; ?></div><div class="label">Current filter</div></div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center py-3 px-4">
                            <div class="card-title fw-bold fs-5 mb-0">
                                <i class="fas fa-clipboard me-2" style="width:20px;height:20px"></i> Daily reports
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <input type="text" class="form-control rounded-pill" id="searchReports" placeholder="Search reports...">
                                </div>
                            </div>
                            <div class="table-responsive-wrapper">
                                <table class="table table-hover" data-table id="reportsTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Project</th>
                                            <th>Date</th>
                                            <th>Weather</th>
                                            <th>Temperature</th>
                                            <th>Created by</th>
                                            <th>Work summary</th>
                                            <th data-orderable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reports as $r): ?>
                                        <tr data-report-id="<?php echo $r['id']; ?>">
                                            <td><?php echo $r['id']; ?></td>
                                            <td><span class="badge bg-light text-dark p-2"><?php echo htmlspecialchars($r['project_name']); ?></span></td>
                                            <td><strong><?php echo $functions->formatDate($r['report_date'], 'M d, Y'); ?></strong></td>
                                            <td><?php echo htmlspecialchars($r['weather'] ?: '—'); ?></td>
                                            <td><?php echo htmlspecialchars($r['temperature'] ?: '—'); ?></td>
                                            <td><?php echo htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')); ?></td>
                                            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo htmlspecialchars($functions->truncateText($r['work_description'] ?? '', 80)); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="?edit=<?php echo $r['id']; ?>&readonly=1" class="btn btn-sm btn-light border view-report" title="View"><i class="fas fa-eye" style="width:14px;height:14px"></i></a>
                                                    <a href="?edit=<?php echo $r['id']; ?>" class="btn btn-sm btn-light border edit-report" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>
                                                    <a href="?action=delete&id=<?php echo $r['id']; ?>&csrf_token=<?php echo $auth->generateCSRF(); ?>" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirmAction(this, 'Delete this report?')"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
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
<!-- Daily Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $readonlyReport ? 'View Daily Report' : ($editReport ? 'Edit Daily Report' : 'New Daily Report'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
          <?php if ($editReport && !$readonlyReport): ?><input type="hidden" name="id" value="<?php echo $editReport['id']; ?>"><?php endif; ?>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Project</label>
              <select name="project_id" class="form-select" <?php echo $readonlyReport ? 'disabled' : 'required'; ?>>
                <option value="">Select project</option>
                <?php foreach ($projects as $pr): ?>
                <option value="<?php echo $pr['id']; ?>" <?php echo (($editReport['project_id'] ?? $filterProject) == $pr['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($pr['name'] . ' (' . $pr['project_number'] . ')'); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Report Date</label>
              <input type="date" name="report_date" class="form-control" value="<?php echo htmlspecialchars($editReport['report_date'] ?? date('Y-m-d')); ?>" <?php echo $readonlyReport ? 'disabled' : 'required'; ?>>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Temperature</label>
              <input type="text" name="temperature" class="form-control" value="<?php echo htmlspecialchars($editReport['temperature'] ?? ''); ?>" <?php echo $readonlyReport ? 'disabled' : ''; ?>>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Weather</label>
            <input type="text" name="weather" class="form-control" value="<?php echo htmlspecialchars($editReport['weather'] ?? ''); ?>" <?php echo $readonlyReport ? 'disabled' : ''; ?>>
          </div>
          <div class="mb-3">
            <label class="form-label">Work Description</label>
            <textarea name="work_description" class="form-control" rows="4" <?php echo $readonlyReport ? 'disabled' : ''; ?>><?php echo htmlspecialchars($editReport['work_description'] ?? ''); ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Delays / Issues</label>
            <textarea name="delays_issues" class="form-control" rows="3" <?php echo $readonlyReport ? 'disabled' : ''; ?>><?php echo htmlspecialchars($editReport['delays_issues'] ?? ''); ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Safety Notes</label>
            <textarea name="safety_notes" class="form-control" rows="3" <?php echo $readonlyReport ? 'disabled' : ''; ?>><?php echo htmlspecialchars($editReport['safety_notes'] ?? ''); ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a href="daily_reports.php" class="btn btn-secondary"><?php echo $readonlyReport ? 'Close' : 'Cancel'; ?></a>
          <?php if (!$readonlyReport): ?>
          <button type="submit" class="btn btn-primary">Save Report</button>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if ($editReport): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new bootstrap.Modal(document.getElementById('reportModal')).show();
});
</script>
<?php endif; ?>
<?php require 'inc/admin_footer.php'; ?>
