<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Equipment.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$equipment = new Equipment();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token';
        header('Location: equipment.php'); exit;
    } elseif ($_POST['action'] === 'create') {
        try {
            $id = $equipment->create($_POST);
            $functions->logActivity($currentUser['id'], 'create_equipment', 'Created equipment: ' . $_POST['name']);
            $_SESSION['toast_success'] = 'Equipment created successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
        header('Location: equipment.php'); exit;
    } elseif ($_POST['action'] === 'update') {
        try {
            $equipment->update($_POST['id'], $_POST);
            $functions->logActivity($currentUser['id'], 'update_equipment', 'Updated equipment: ' . $_POST['name']);
            $_SESSION['toast_success'] = 'Equipment updated successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
        header('Location: equipment.php'); exit;
    } elseif ($_POST['action'] === 'add_maintenance') {
        try {
            $equipment->addMaintenanceLog($_POST);
            $functions->logActivity($currentUser['id'], 'add_maintenance', 'Added maintenance log for equipment ID: ' . $_POST['equipment_id']);
            $_SESSION['toast_success'] = 'Maintenance log added';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
        header('Location: equipment.php'); exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if ($auth->verifyCsrfToken($_GET['csrf_token'])) {
        try {
            $equipment->delete($_GET['id']);
            $functions->logActivity($currentUser['id'], 'delete_equipment', 'Deleted equipment ID: ' . $_GET['id']);
            $_SESSION['toast_success'] = 'Equipment deleted successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } else { $_SESSION['toast_error'] = 'Invalid security token'; }
    header('Location: equipment.php'); exit;
}

$items = $equipment->getAll();
$categories = $equipment->getCategories();
$editItem = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $editItem = $equipment->getById((int)$_GET['edit']);
}
$selectedEq = isset($_GET['view']) ? $equipment->getById((int)$_GET['view']) : null;
$maintenanceLogs = $selectedEq ? $equipment->getMaintenanceLogs($selectedEq['id']) : [];
$assignments = $selectedEq ? $equipment->getAssignments($selectedEq['id']) : [];
$statuses = ['available' => 'Available', 'in_use' => 'In Use', 'maintenance' => 'Maintenance', 'out_of_service' => 'Out of Service', 'retired' => 'Retired'];
$pageActive = 'equipment';
$pageTitle = 'TPV Construction and Services LTD · Equipment';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Resources</a></li>
                                <li class="breadcrumb-item active">Equipment Fleet</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <div class="card no-border">
                        <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">Equipment Fleet</span>
                            <div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#equipmentModal"><i class="fas fa-plus"></i> Add Equipment</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="scroll-table">
                                <table id="equipmentTable" class="table table-hover table-condensed" data-table style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Model</th>
                                            <th>Serial #</th>
                                            <th>Status</th>
                                            <th>Location</th>
                                            <th>Purchase Date</th>
                                            <th>Value</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($item['category_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($item['model'] ?? '-'); ?></td>
                                            <td><code><?php echo htmlspecialchars($item['serial_number'] ?? '-'); ?></code></td>
                                            <td><span class="status-badge status-<?php echo $item['status']; ?>"><?php echo htmlspecialchars($statuses[$item['status']] ?? $item['status']); ?></span></td>
                                            <td><?php echo htmlspecialchars($item['location'] ?? '-'); ?></td>
                                            <td><?php echo $item['purchase_date'] ? date('M d, Y', strtotime($item['purchase_date'])) : '-'; ?></td>
                                            <td><?php echo $item['current_value'] ? '$' . number_format($item['current_value'], 2) : '-'; ?></td>
                                            <td class="text-nowrap">
                                                <a href="?view=<?php echo $item['id']; ?>" class="btn btn-xs btn-info" title="View Details"><i class="fas fa-eye" style="width:14px;height:14px"></i></a>
                                                <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-xs btn-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px"></i></a>
                                                <a href="?action=delete&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $auth->generateCsrfToken(); ?>" class="btn btn-xs btn-danger" onclick="return confirmAction(this, 'Delete this equipment?')" title="Delete"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if ($selectedEq): ?>
                    <div class="row m-t-20">
                        <div class="col-md-6">
                            <div class="card no-border detail-card">
                                <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                                    <span>Equipment Details: <?php echo htmlspecialchars($selectedEq['name']); ?></span>
                                    <a href="equipment.php" class="btn btn-sm btn-default"><i class="fas fa-times"></i></a>
                                </div>
                                <div class="card-body">
                                    <table class="table table-condensed table-borderless">
                                        <tr><td style="width:160px"><strong>Category</strong></td><td><?php echo htmlspecialchars($selectedEq['category_name'] ?? '-'); ?></td></tr>
                                        <tr><td><strong>Model</strong></td><td><?php echo htmlspecialchars($selectedEq['model'] ?? '-'); ?></td></tr>
                                        <tr><td><strong>Serial Number</strong></td><td><code><?php echo htmlspecialchars($selectedEq['serial_number'] ?? '-'); ?></code></td></tr>
                                        <tr><td><strong>Status</strong></td><td><span class="status-badge status-<?php echo $selectedEq['status']; ?>"><?php echo htmlspecialchars($statuses[$selectedEq['status']] ?? $selectedEq['status']); ?></span></td></tr>
                                        <tr><td><strong>Location</strong></td><td><?php echo htmlspecialchars($selectedEq['location'] ?? '-'); ?></td></tr>
                                        <tr><td><strong>Purchase Date</strong></td><td><?php echo $selectedEq['purchase_date'] ? date('M d, Y', strtotime($selectedEq['purchase_date'])) : '-'; ?></td></tr>
                                        <tr><td><strong>Purchase Price</strong></td><td><?php echo $selectedEq['purchase_price'] ? '$' . number_format($selectedEq['purchase_price'], 2) : '-'; ?></td></tr>
                                        <tr><td><strong>Current Value</strong></td><td><?php echo $selectedEq['current_value'] ? '$' . number_format($selectedEq['current_value'], 2) : '-'; ?></td></tr>
                                        <tr><td><strong>Notes</strong></td><td><?php echo nl2br(htmlspecialchars($selectedEq['notes'] ?? '-')); ?></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card no-border m-b-10">
                                <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                                    <span>Assignments</span>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-condensed m-b-0">
                                        <thead><tr><th>Project</th><th>Assigned</th><th>Returned</th></tr></thead>
                                        <tbody>
                                            <?php if ($assignments): foreach ($assignments as $a): ?>
                                            <tr><td><?php echo htmlspecialchars($a['project_name'] ?? '-'); ?></td><td><?php echo $a['assigned_date'] ? date('M d, Y', strtotime($a['assigned_date'])) : '-'; ?></td><td><?php echo $a['returned_date'] ? date('M d, Y', strtotime($a['returned_date'])) : '-'; ?></td></tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="3" class="text-center text-muted">No assignments</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card no-border">
                                <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                                    <span>Maintenance Logs</span>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#maintenanceModal"><i class="fas fa-plus"></i> Log</button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-condensed m-b-0">
                                        <thead><tr><th>Date</th><th>Description</th><th>Cost</th><th>Performed By</th></tr></thead>
                                        <tbody>
                                            <?php if ($maintenanceLogs): foreach ($maintenanceLogs as $ml): ?>
                                            <tr><td><?php echo date('M d, Y', strtotime($ml['maintenance_date'])); ?></td><td><?php echo htmlspecialchars($ml['description'] ?? '-'); ?></td><td><?php echo $ml['cost'] ? '$' . number_format($ml['cost'], 2) : '-'; ?></td><td><?php echo htmlspecialchars($ml['performed_by'] ?? '-'); ?></td></tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="4" class="text-center text-muted">No maintenance logs</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
<!-- GENERATED MODAL -->
<div class="modal fade" id="equipmentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Equipment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken() ?? ''; ?>">
          <input type="hidden" name="action" value="<?php echo isset($editItem) ? 'update' : 'create'; ?>">
          <?php if(isset($editItem) && isset($editItem['id'])): ?>
            <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
          <?php else: ?>
            <input type="hidden" name="id" id="equipmentModal_id" value="">
          <?php endif; ?>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat['id']; ?>" <?php echo (isset($editItem['category_id']) && $editItem['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <?php foreach ($statuses as $val => $label): ?>
                  <option value="<?php echo $val; ?>" <?php echo (isset($editItem['status']) && $editItem['status'] === $val) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Model</label>
              <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($editItem['model'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial_number" class="form-control" value="<?php echo htmlspecialchars($editItem['serial_number'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Purchase Date</label>
              <input type="date" name="purchase_date" class="form-control" value="<?php echo htmlspecialchars($editItem['purchase_date'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Purchase Price (₦)</label>
              <input type="number" step="0.01" name="purchase_price" class="form-control" value="<?php echo htmlspecialchars($editItem['purchase_price'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Current Value (₦)</label>
              <input type="number" step="0.01" name="current_value" class="form-control" value="<?php echo htmlspecialchars($editItem['current_value'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Location</label>
              <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($editItem['location'] ?? ''); ?>">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($editItem['notes'] ?? ''); ?></textarea>
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


<?php if ($selectedEq): ?>
<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Log Maintenance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="add_maintenance">
          <input type="hidden" name="equipment_id" value="<?php echo $selectedEq['id']; ?>">
          <div class="mb-3">
            <label class="form-label">Equipment</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($selectedEq['name']); ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Maintenance Date</label>
            <input type="date" name="maintenance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Cost (₦)</label>
            <input type="number" step="0.01" name="cost" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Performed By</label>
            <input type="text" name="performed_by" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Next Maintenance Date</label>
            <input type="date" name="next_maintenance_date" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Log</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if (isset($_GET['edit']) && isset($editItem) && $editItem): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var myModal = new bootstrap.Modal(document.getElementById('equipmentModal'));
  myModal.show();
});
</script>
<?php endif; ?>
<?php require 'inc/admin_footer.php'; ?>
