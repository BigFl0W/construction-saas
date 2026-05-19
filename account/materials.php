<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Material.php';
require_once '../classes/Supplier.php';
require_once '../classes/Project.php';
require_once '../classes/Mailer.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$material = new Material();
$supplier = new Supplier();
$project = new Project();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token';
    } elseif ($_POST['action'] === 'create') {
        try {
            $id = $material->create($_POST);
            $functions->logActivity($currentUser['id'], 'create_material', 'Created material: ' . $_POST['name']);
            $_SESSION['toast_success'] = 'Material created successfully';
            header('Location: materials.php');
            exit;
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } elseif ($_POST['action'] === 'update') {
        try {
            $material->update($_POST['id'], $_POST);
            $functions->logActivity($currentUser['id'], 'update_material', 'Updated material: ' . $_POST['name']);
            $_SESSION['toast_success'] = 'Material updated successfully';
            header('Location: materials.php');
            exit;
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } elseif ($_POST['action'] === 'adjust_stock') {
        try {
            $material->adjustStock($_POST['material_id'], $_POST['quantity']);
            if (!empty($_POST['project_id']) && !empty($_POST['usage_date'])) {
                $material->logUsage([
                    'project_id' => $_POST['project_id'],
                    'material_id' => $_POST['material_id'],
                    'quantity_used' => abs($_POST['quantity']),
                    'usage_date' => $_POST['usage_date'],
                    'notes' => $_POST['notes'] ?? 'Stock adjustment',
                    'created_by' => $currentUser['id']
                ]);
            }
            $functions->logActivity($currentUser['id'], 'adjust_stock', 'Adjusted stock for material ID: ' . $_POST['material_id']);
            $_SESSION['toast_success'] = 'Stock adjusted successfully';
            header('Location: materials.php');
            exit;
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } elseif ($_POST['action'] === 'send_email') {
        $toEmail = $_POST['email_to'] ?? '';
        $toName = $_POST['name_to'] ?? '';
        $subject = $_POST['email_subject'] ?? '';
        $emailMessage = $_POST['email_message'] ?? '';
        if ($toEmail && $subject && $emailMessage) {
            $mailer = new Mailer();
            if ($mailer->sendToEmployee($toEmail, $toName, $subject, $emailMessage)) {
                $_SESSION['toast_success'] = 'Email sent to ' . htmlspecialchars($toName);
            } else {
                $_SESSION['toast_error'] = 'Failed to send email';
            }
        } else {
            $_SESSION['toast_error'] = 'All email fields are required';
        }
        header('Location: materials.php');
        exit;
    }
    if (!empty($_SESSION['toast_error'])) {
        header('Location: materials.php');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if ($auth->verifyCsrfToken($_GET['csrf_token'])) {
        try {
            $material->delete($_GET['id']);
            $functions->logActivity($currentUser['id'], 'delete_material', 'Deleted material ID: ' . $_GET['id']);
            $_SESSION['toast_success'] = 'Material deleted successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } else { $_SESSION['toast_error'] = 'Invalid security token'; }
    header('Location: materials.php');
    exit;
}


$items = $material->getAll();
$suppliers = $supplier->getAll();
$projects = $project->getAll();
$editItem = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $editItem = $material->getById((int)$_GET['edit']);
}
$viewItem = isset($_GET['view']) ? $material->getById((int)$_GET['view']) : null;
$usageLog = $viewItem ? $material->getUsage($viewItem['id']) : [];
$pageActive = 'materials';
$pageTitle = 'TPV Construction and Services LTD · Materials';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Resources</a></li>
                                <li class="breadcrumb-item active">Materials Inventory</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <div class="card no-border">
                        <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">Materials Inventory</span>
                            <div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#materialModal"><i class="fas fa-plus"></i> Add Material</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="scroll-table">
                                <table id="materialTable" class="table table-hover table-condensed" data-table style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Unit</th>
                                            <th>Current Stock</th>
                                            <th>Reorder Level</th>
                                            <th>Unit Cost</th>
                                            <th>Supplier</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item):
                                        $isLowStock = $item['current_stock'] <= $item['reorder_level'] && $item['status'] === 'active';
                                        ?>
                                        <tr class="<?php echo $isLowStock ? 'table-danger' : ''; ?>">
                                            <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($item['category'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($item['unit'] ?? '-'); ?></td>
                                            <td class="<?php echo $isLowStock ? 'low-stock' : ''; ?>"><?php echo number_format($item['current_stock']); ?></td>
                                            <td><?php echo number_format($item['reorder_level']); ?></td>
                                            <td><?php echo $item['unit_cost'] ? '$' . number_format($item['unit_cost'], 2) : '-'; ?></td>
                                            <td><?php echo htmlspecialchars($item['supplier_name'] ?? '-'); ?></td>
                                            <td><span class="status-badge status-<?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span></td>
                                            <td class="text-nowrap">
                                                <a href="?view=<?php echo $item['id']; ?>" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye" style="width:14px;height:14px"></i></a>
                                                <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-xs btn-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px"></i></a>
                                                <button class="btn btn-xs btn-warning" data-bs-toggle="modal" data-bs-target="#stockModal" data-id="<?php echo $item['id']; ?>" data-name="<?php echo htmlspecialchars($item['name']); ?>" title="Adjust Stock"><i class="fas fa-plus-circle" style="width:14px;height:14px"></i></button>
                                                <button type="button" class="btn btn-xs btn-info" title="Send Email" data-bs-toggle="modal" data-bs-target="#emailModal" data-email="<?php echo htmlspecialchars($item['supplier_email'] ?? ''); ?>" data-name="<?php echo htmlspecialchars($item['supplier_name'] ?? 'Supplier'); ?>"><i class="fas fa-envelope" style="width:14px;height:14px"></i></button>
                                                <a href="?action=delete&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $auth->generateCsrfToken(); ?>" class="btn btn-xs btn-danger" onclick="return confirmAction(this, 'Delete this material?')" title="Delete"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if ($viewItem): ?>
                    <div class="row m-t-20">
                        <div class="col-md-6">
                            <div class="card no-border detail-card">
                                <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                                    <span>Material Details: <?php echo htmlspecialchars($viewItem['name']); ?></span>
                                    <a href="materials.php" class="btn btn-sm btn-default"><i class="fas fa-times"></i></a>
                                </div>
                                <div class="card-body">
                                    <table class="table table-condensed table-borderless">
                                        <tr><td style="width:160px"><strong>Category</strong></td><td><?php echo htmlspecialchars($viewItem['category'] ?? '-'); ?></td></tr>
                                        <tr><td><strong>Unit</strong></td><td><?php echo htmlspecialchars($viewItem['unit'] ?? '-'); ?></td></tr>
                                        <tr><td><strong>Current Stock</strong></td><td class="<?php echo ($viewItem['current_stock'] <= $viewItem['reorder_level']) ? 'low-stock' : ''; ?>"><?php echo number_format($viewItem['current_stock']); ?> <?php echo htmlspecialchars($viewItem['unit'] ?? ''); ?></td></tr>
                                        <tr><td><strong>Reorder Level</strong></td><td><?php echo number_format($viewItem['reorder_level']); ?></td></tr>
                                        <tr><td><strong>Unit Cost</strong></td><td><?php echo $viewItem['unit_cost'] ? '$' . number_format($viewItem['unit_cost'], 2) : '-'; ?></td></tr>
                                        <tr><td><strong>Supplier</strong></td><td><?php echo htmlspecialchars($viewItem['supplier_name'] ?? '-'); ?></td></tr>
                                        <tr><td><strong>Location Stored</strong></td><td><?php echo htmlspecialchars($viewItem['location_stored'] ?? '-'); ?></td></tr>
                                        <tr><td><strong>Status</strong></td><td><span class="status-badge status-<?php echo $viewItem['status']; ?>"><?php echo ucfirst($viewItem['status']); ?></span></td></tr>
                                        <tr><td><strong>Description</strong></td><td><?php echo nl2br(htmlspecialchars($viewItem['description'] ?? '-')); ?></td></tr>
                                        <tr><td><strong>Notes</strong></td><td><?php echo nl2br(htmlspecialchars($viewItem['notes'] ?? '-')); ?></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card no-border">
                                <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                                    <span>Usage Log</span>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#stockModal" data-id="<?php echo $viewItem['id']; ?>" data-name="<?php echo htmlspecialchars($viewItem['name']); ?>"><i >plus-circle</i> Adjust</button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-condensed m-b-0">
                                        <thead><tr><th>Date</th><th>Project</th><th>Qty Used</th><th>Notes</th></tr></thead>
                                        <tbody>
                                            <?php if ($usageLog): foreach ($usageLog as $u): ?>
                                            <tr><td><?php echo date('M d, Y', strtotime($u['usage_date'])); ?></td><td><?php echo htmlspecialchars($u['project_name'] ?? '-'); ?></td><td><?php echo number_format($u['quantity_used']); ?></td><td><?php echo htmlspecialchars($u['notes'] ?? '-'); ?></td></tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="4" class="text-center text-muted">No usage recorded</td></tr>
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
<div class="modal fade" id="materialModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Material</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken() ?? ''; ?>">
          <input type="hidden" name="action" value="<?php echo isset($editItem) ? 'update' : 'create'; ?>">
          <?php if(isset($editItem) && isset($editItem['id'])): ?>
            <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
          <?php else: ?>
            <input type="hidden" name="id" id="materialModal_id" value="">
          <?php endif; ?>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Category</label>
              <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($editItem['category'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Unit</label>
              <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($editItem['unit'] ?? ''); ?>" required placeholder="e.g. kg, bags, tons">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Current Stock</label>
              <input type="number" name="current_stock" class="form-control" value="<?php echo htmlspecialchars($editItem['current_stock'] ?? 0); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Reorder Level</label>
              <input type="number" name="reorder_level" class="form-control" value="<?php echo htmlspecialchars($editItem['reorder_level'] ?? 0); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Unit Cost (₦)</label>
              <input type="number" step="0.01" name="unit_cost" class="form-control" value="<?php echo htmlspecialchars($editItem['unit_cost'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Supplier</label>
              <select name="supplier_id" class="form-select">
                <option value="">Select Supplier</option>
                <?php foreach ($suppliers as $sup): ?>
                <option value="<?php echo $sup['id']; ?>" <?php echo (isset($editItem['supplier_id']) && $editItem['supplier_id'] == $sup['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($sup['name']); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Location Stored</label>
              <input type="text" name="location_stored" class="form-control" value="<?php echo htmlspecialchars($editItem['location_stored'] ?? ''); ?>">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="active" <?php echo (isset($editItem['status']) && $editItem['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo (isset($editItem['status']) && $editItem['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
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
  var myModal = new bootstrap.Modal(document.getElementById('materialModal'));
  myModal.show();
});
</script>
<?php endif; ?>
<!-- STOCK MODAL -->
<div class="modal fade" id="stockModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-boxes-stacked me-2"></i>Adjust Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="adjust_stock">
          <input type="hidden" name="material_id" id="stockMaterialId">
          <div class="mb-3">
            <label class="form-label">Material</label>
            <input type="text" id="stockMaterialName" class="form-control" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Quantity Change</label>
            <input type="number" step="0.01" name="quantity" class="form-control" required placeholder="Use negative number for stock used">
          </div>
          <div class="mb-3">
            <label class="form-label">Project</label>
            <select name="project_id" class="form-select">
              <option value="">No project / inventory correction</option>
              <?php foreach ($projects as $pr): ?>
              <option value="<?php echo $pr['id']; ?>"><?php echo htmlspecialchars($pr['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Usage Date</label>
            <input type="date" name="usage_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Adjustment</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- EMAIL MODAL -->
<div class="modal fade" id="emailModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Send Email</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="send_email">
          <div class="mb-3">
            <label class="form-label">To</label>
            <input type="email" name="email_to" id="emailTo" class="form-control" readonly required>
            <input type="hidden" name="name_to" id="nameTo">
          </div>
          <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="email_subject" class="form-control" required placeholder="Enter subject">
          </div>
          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="email_message" class="form-control" rows="6" required placeholder="Type your message here..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Send</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
document.getElementById('stockModal').addEventListener('show.bs.modal', function(e) {
  var btn = e.relatedTarget;
  document.getElementById('stockMaterialId').value = btn.getAttribute('data-id');
  document.getElementById('stockMaterialName').value = btn.getAttribute('data-name');
});
document.getElementById('emailModal').addEventListener('show.bs.modal', function(e) {
  var btn = e.relatedTarget;
  document.getElementById('emailTo').value = btn.getAttribute('data-email');
  document.getElementById('nameTo').value = btn.getAttribute('data-name');
});
</script>
<?php require 'inc/admin_footer.php'; ?>
