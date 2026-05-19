<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Timesheet.php';
require_once '../classes/Employee.php';
require_once '../classes/Project.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$timesheet = new Timesheet();
$employeeObj = new Employee();
$project = new Project();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token';
        header('Location: timesheets.php');
        exit;
    } elseif ($_POST['action'] === 'create') {
        try {
            $id = $timesheet->create($_POST);
            $functions->logActivity($currentUser['id'], 'create_timesheet', 'Created timesheet for employee ID: ' . $_POST['employee_id']);
            $_SESSION['toast_success'] = 'Timesheet created successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
        header('Location: timesheets.php');
        exit;
    } elseif ($_POST['action'] === 'update') {
        try {
            $timesheet->update($_POST['id'], $_POST);
            $functions->logActivity($currentUser['id'], 'update_timesheet', 'Updated timesheet ID: ' . $_POST['id']);
            $_SESSION['toast_success'] = 'Timesheet updated successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
        header('Location: timesheets.php');
        exit;
    } elseif ($_POST['action'] === 'approve') {
        try {
            $timesheet->approve($_POST['id'], $currentUser['id']);
            $functions->logActivity($currentUser['id'], 'approve_timesheet', 'Approved timesheet ID: ' . $_POST['id']);
            $_SESSION['toast_success'] = 'Timesheet approved';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
        header('Location: timesheets.php');
        exit;
    } elseif ($_POST['action'] === 'reject') {
        try {
            $timesheet->reject($_POST['id'], $currentUser['id']);
            $functions->logActivity($currentUser['id'], 'reject_timesheet', 'Rejected timesheet ID: ' . $_POST['id']);
            $_SESSION['toast_success'] = 'Timesheet rejected';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
        header('Location: timesheets.php');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if ($auth->verifyCsrfToken($_GET['csrf_token'])) {
        try {
            $timesheet->delete($_GET['id']);
            $functions->logActivity($currentUser['id'], 'delete_timesheet', 'Deleted timesheet ID: ' . $_GET['id']);
            $_SESSION['toast_success'] = 'Timesheet deleted successfully';
            header('Location: timesheets.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast_error'] = $e->getMessage();
            header('Location: timesheets.php');
            exit;
        }
    } else {
        $_SESSION['toast_error'] = 'Invalid security token';
        header('Location: timesheets.php');
        exit;
    }
}

$employees = $employeeObj->getAll();
$projects = $project->getAll();

$filterEmployee = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : null;
$filterStatus = isset($_GET['status']) ? $_GET['status'] : null;
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;

$items = $timesheet->getAll($filterEmployee, null, $dateFrom, $dateTo, $filterStatus);
$editItem = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $editItem = $timesheet->getById((int)$_GET['edit']);
}
$statuses = ['draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected'];
$pageActive = 'timesheets';
$pageTitle = 'TPV Construction and Services LTD · Timesheets';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Workforce</a></li>
                                <li class="breadcrumb-item active">Timesheets</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">

                    <div class="filter-section">
                        <form method="GET" class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Employee</label>
                                <select name="employee_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Employees</option>
                                    <?php foreach ($employees as $e): ?>
                                    <option value="<?php echo $e['id']; ?>" <?php echo $filterEmployee == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <?php foreach ($statuses as $k => $v): ?>
                                    <option value="<?php echo $k; ?>" <?php echo $filterStatus === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo $dateFrom ?? date('Y-m-01'); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo $dateTo ?? date('Y-m-t'); ?>">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="timesheets.php" class="btn btn-secondary btn-sm w-100">Clear</a>
                            </div>
                        </form>
                    </div>

                    <div class="card no-border">
                        <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">Timesheets</span>
                            <div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#timesheetModal"><i class="fas fa-plus"></i> Add Timesheet</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="scroll-table">
                                <table id="timesheetTable" class="table table-hover table-condensed" data-table style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Project</th>
                                            <th>Work Date</th>
                                            <th>Hours</th>
                                            <th>Overtime</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($item['project_name'] ?? '-'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($item['work_date'])); ?></td>
                                            <td><?php echo number_format($item['hours_worked'], 1); ?></td>
                                            <td><?php echo $item['overtime_hours'] ? number_format($item['overtime_hours'], 1) : '-'; ?></td>
                                            <td><span class="status-badge status-<?php echo $item['status']; ?>"><?php echo htmlspecialchars($statuses[$item['status']] ?? $item['status']); ?></span></td>
                                            <td class="text-nowrap">
                                                <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-xs btn-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px"></i></a>
                                                <?php if ($item['status'] === 'submitted'): ?>
                                                <form method="POST" style="display:inline">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
                                                    <button type="submit" class="btn btn-xs btn-success" title="Approve"><i class="fas fa-check" style="width:14px;height:14px"></i></button>
                                                </form>
                                                <form method="POST" style="display:inline">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
                                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirmAction(this, 'Reject this timesheet?')" title="Reject"><i class="fas fa-times" style="width:14px;height:14px"></i></button>
                                                </form>
                                                <?php endif; ?>
                                                <a href="?action=delete&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $auth->generateCsrfToken(); ?>" class="btn btn-xs btn-danger" onclick="return confirmAction(this, 'Delete this timesheet?')" title="Delete"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
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
<!-- GENERATED MODAL -->
<div class="modal fade" id="timesheetModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Timesheet</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken() ?? ''; ?>">
          <input type="hidden" name="action" value="<?php echo isset($editItem) ? 'update' : 'create'; ?>">
          <?php if(isset($editItem) && isset($editItem['id'])): ?>
            <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
          <?php else: ?>
            <input type="hidden" name="id" id="timesheetModal_id" value="">
          <?php endif; ?>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Employee Id</label>
              <input type="text" name="employee_id" class="form-control" value="<?php echo htmlspecialchars($editItem['employee_id'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Project Id</label>
              <input type="text" name="project_id" class="form-control" value="<?php echo htmlspecialchars($editItem['project_id'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Work Date</label>
              <input type="date" name="work_date" class="form-control" value="<?php echo htmlspecialchars($editItem['work_date'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Hours Worked</label>
              <input type="text" name="hours_worked" class="form-control" value="<?php echo htmlspecialchars($editItem['hours_worked'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>


<?php if (isset($_GET['edit']) && isset($editItem) && $editItem): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var myModal = new bootstrap.Modal(document.getElementById('timesheetModal'));
  myModal.show();
});
</script>
<?php endif; ?>
<?php require 'inc/admin_footer.php'; ?>
