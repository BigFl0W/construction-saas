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
    } elseif ($_POST['action'] === 'create') {
        try {
            $equipment->addMaintenanceLog($_POST);
            $functions->logActivity($currentUser['id'], 'add_maintenance', 'Added maintenance log for equipment ID: ' . $_POST['equipment_id']);
            $_SESSION['toast_success'] = 'Maintenance log created successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } elseif ($_POST['action'] === 'update') {
        try {
            $db->query("UPDATE maintenance_logs SET equipment_id = :equipment_id, maintenance_date = :maintenance_date, description = :description, cost = :cost, performed_by = :performed_by, next_maintenance_date = :next_maintenance_date, notes = :notes, updated_at = NOW() WHERE id = :id", [
                'id' => $_POST['id'], 'equipment_id' => $_POST['equipment_id'], 'maintenance_date' => $_POST['maintenance_date'],
                'description' => $_POST['description'], 'cost' => $_POST['cost'] ?? 0, 'performed_by' => $_POST['performed_by'] ?? null,
                'next_maintenance_date' => $_POST['next_maintenance_date'] ?? null, 'notes' => $_POST['notes'] ?? null
            ]);
            $functions->logActivity($currentUser['id'], 'update_maintenance', 'Updated maintenance log ID: ' . $_POST['id']);
            $_SESSION['toast_success'] = 'Maintenance log updated successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    }
    header('Location: maintenance.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if ($auth->verifyCsrfToken($_GET['csrf_token'])) {
        try {
            $db->query("DELETE FROM maintenance_logs WHERE id = :id", ['id' => $_GET['id']]);
            $functions->logActivity($currentUser['id'], 'delete_maintenance', 'Deleted maintenance log ID: ' . $_GET['id']);
            $_SESSION['toast_success'] = 'Maintenance log deleted successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } else { $_SESSION['toast_error'] = 'Invalid security token'; }
    header('Location: maintenance.php');
    exit;
}

$items = $equipment->getMaintenanceLogs();
$equipmentList = $equipment->getAll();
$editItem = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $stmt = $db->query("SELECT * FROM maintenance_logs WHERE id = :id", ['id' => (int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}
$filterEquipment = isset($_GET['equipment_id']) ? (int)$_GET['equipment_id'] : null;
if ($filterEquipment) {
    $items = $equipment->getMaintenanceLogs($filterEquipment);
}

$pageActive = 'maintenance';
$pageTitle = 'TPV Construction and Services LTD · Maintenance';
require 'inc/admin_header.php';
?>
                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Resources</a></li>
                                <li class="breadcrumb-item active">Maintenance Logs</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <div class="filter-section">
                        <form method="GET" class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Filter by Equipment</label>
                                <select name="equipment_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Equipment</option>
                                    <?php foreach ($equipmentList as $eq): ?>
                                    <option value="<?php echo $eq['id']; ?>" <?php echo $filterEquipment == $eq['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($eq['name']); ?> (<?php echo htmlspecialchars($eq['serial_number'] ?? ''); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a href="maintenance.php" class="btn btn-secondary btn-sm w-100">Clear Filter</a>
                            </div>
                        </form>
                    </div>

                    <div class="card no-border">
                        <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">Maintenance Logs</span>
                            <div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#maintenanceModal"><i class="fas fa-plus"></i> Add Log</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="scroll-table">
                                <table id="maintenanceTable" class="table table-hover table-condensed" data-table style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Equipment</th>
                                            <th>Maintenance Date</th>
                                            <th>Description</th>
                                            <th>Cost</th>
                                            <th>Performed By</th>
                                            <th>Next Maintenance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): 
                                        $isOverdue = $item['next_maintenance_date'] && strtotime($item['next_maintenance_date']) < time();
                                        ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($item['equipment_name'] ?? '-'); ?></strong></td>
                                            <td><?php echo date('M d, Y', strtotime($item['maintenance_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($functions->truncateText($item['description'] ?? '-', 50)); ?></td>
                                            <td><?php echo $item['cost'] ? '$' . number_format($item['cost'], 2) : '-'; ?></td>
                                            <td><?php echo htmlspecialchars($item['performed_by'] ?? '-'); ?></td>
                                            <td class="<?php echo $isOverdue ? 'overdue' : ''; ?>">
                                                <?php echo $item['next_maintenance_date'] ? date('M d, Y', strtotime($item['next_maintenance_date'])) : '-'; ?>
                                                <?php if ($isOverdue): ?><br><small>Overdue!</small><?php endif; ?>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="?edit=<?php echo $item['id']; ?>&equipment_id=<?php echo $filterEquipment; ?>" class="btn btn-xs btn-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px"></i></a>
                                                <a href="?action=delete&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $auth->generateCsrfToken(); ?>" class="btn btn-xs btn-danger" onclick="return confirmAction(this, 'Delete this maintenance log?')" title="Delete"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                </div>
            </div>
            
<!-- MAINTENANCE MODAL -->
<div class="modal fade" id="maintenanceModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo isset($editItem) ? 'Edit Maintenance Log' : 'Add Maintenance Log'; ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="<?php echo isset($editItem) ? 'update' : 'create'; ?>">
          <?php if(isset($editItem)): ?>
            <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Equipment</label>
            <select name="equipment_id" class="form-select" required>
              <option value="">Select Equipment</option>
              <?php foreach ($equipmentList as $eq): ?>
                <option value="<?php echo $eq['id']; ?>" <?php echo (isset($editItem) && $editItem['equipment_id'] == $eq['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($eq['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Maintenance Date</label>
            <input type="date" name="maintenance_date" class="form-control" value="<?php echo htmlspecialchars($editItem['maintenance_date'] ?? date('Y-m-d')); ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Cost (₦)</label>
            <input type="number" step="0.01" name="cost" class="form-control" value="<?php echo htmlspecialchars($editItem['cost'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Performed By</label>
            <input type="text" name="performed_by" class="form-control" value="<?php echo htmlspecialchars($editItem['performed_by'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Next Maintenance Date</label>
            <input type="date" name="next_maintenance_date" class="form-control" value="<?php echo htmlspecialchars($editItem['next_maintenance_date'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($editItem['notes'] ?? ''); ?></textarea>
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

<?php if (isset($_GET['edit']) && isset($editItem) && $editItem): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var myModal = new bootstrap.Modal(document.getElementById('maintenanceModal'));
  myModal.show();
});
</script>
<?php endif; ?>

<?php require 'inc/admin_footer.php'; ?>
