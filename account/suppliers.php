<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Supplier.php';
require_once '../classes/Material.php';
require_once '../classes/Mailer.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$supplier = new Supplier();
$material = new Material();
$mailer = new Mailer();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token';
    } elseif ($_POST['action'] === 'create') {
        try {
            $id = $supplier->create($_POST);
            $functions->logActivity($currentUser['id'], 'create_supplier', 'Created supplier: ' . $_POST['name']);
            $_SESSION['toast_success'] = 'Supplier created successfully';
            header('Location: suppliers.php');
            exit;
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } elseif ($_POST['action'] === 'update') {
        try {
            $supplier->update($_POST['id'], $_POST);
            $functions->logActivity($currentUser['id'], 'update_supplier', 'Updated supplier: ' . $_POST['name']);
            $_SESSION['toast_success'] = 'Supplier updated successfully';
            header('Location: suppliers.php');
            exit;
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } elseif ($_POST['action'] === 'send_email') {
        $toEmail = $_POST['email_to'] ?? '';
        $toName = $_POST['name_to'] ?? '';
        $subject = $_POST['email_subject'] ?? '';
        $message = $_POST['email_message'] ?? '';
        if ($toEmail && $subject && $message) {
            if ($mailer->sendToSupplier($toEmail, $toName, $subject, $message)) {
                $_SESSION['toast_success'] = 'Email sent to ' . htmlspecialchars($toName);
            } else {
                $_SESSION['toast_error'] = 'Failed to send email';
            }
        } else {
            $_SESSION['toast_error'] = 'All email fields are required';
        }
        header('Location: suppliers.php');
        exit;
    }
    if (!empty($_SESSION['toast_error'])) {
        header('Location: suppliers.php');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if ($auth->verifyCsrfToken($_GET['csrf_token'])) {
        try {
            $supplier->delete($_GET['id']);
            $functions->logActivity($currentUser['id'], 'delete_supplier', 'Deleted supplier ID: ' . $_GET['id']);
            $_SESSION['toast_success'] = 'Supplier deleted successfully';
        } catch (Exception $e) { $_SESSION['toast_error'] = $e->getMessage(); }
    } else { $_SESSION['toast_error'] = 'Invalid security token'; }
    header('Location: suppliers.php');
    exit;
}

$items = $supplier->getAll();
$editItem = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $editItem = $supplier->getById((int)$_GET['edit']);
}
$pageActive = 'suppliers';
$pageTitle = 'TPV Construction and Services LTD · Suppliers';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Resources</a></li>
                                <li class="breadcrumb-item active">Suppliers</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">

                    <div class="card no-border">
                        <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">Suppliers</span>
                            <div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fas fa-plus"></i> Add Supplier</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="scroll-table">
                                <table id="supplierTable" class="table table-hover table-condensed" data-table style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Contact Person</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Payment Terms</th>
                                            <th>Materials</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($item['contact_person'] ?? '-'); ?></td>
                                            <td><a href="mailto:<?php echo htmlspecialchars($item['email'] ?? ''); ?>"><?php echo htmlspecialchars($item['email'] ?? '-'); ?></a></td>
                                            <td><?php echo htmlspecialchars($item['phone'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($item['payment_terms'] ?? '-'); ?></td>
                                            <td><span class="badge bg-info"><?php echo (int)($item['material_count'] ?? 0); ?></span></td>
                                            <td><span class="status-badge status-<?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span></td>
                                            <td class="text-nowrap">
                                                <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-xs btn-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px"></i></a>
                                                <button type="button" class="btn btn-xs btn-info" title="Send Email" data-bs-toggle="modal" data-bs-target="#emailModal" data-email="<?php echo htmlspecialchars($item['email'] ?? ''); ?>" data-name="<?php echo htmlspecialchars($item['name']); ?>"><i class="fas fa-envelope" style="width:14px;height:14px"></i></button>
                                                <a href="?action=delete&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $auth->generateCsrfToken(); ?>" class="btn btn-xs btn-danger" onclick="return confirmAction(this, 'Delete this supplier?')" title="Delete"><i class="fas fa-trash" style="width:14px;height:14px"></i></a>
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
<div class="modal fade" id="supplierModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken() ?? ''; ?>">
          <input type="hidden" name="action" value="<?php echo isset($editItem) ? 'update' : 'create'; ?>">
          <?php if(isset($editItem) && isset($editItem['id'])): ?>
            <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
          <?php else: ?>
            <input type="hidden" name="id" id="supplierModal_id" value="">
          <?php endif; ?>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Contact Name</label>
              <input type="text" name="contact_name" class="form-control" value="<?php echo htmlspecialchars($editItem['contact_name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($editItem['email'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($editItem['phone'] ?? ''); ?>" required>
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
document.getElementById('emailModal').addEventListener('show.bs.modal', function(e) {
  var btn = e.relatedTarget;
  document.getElementById('emailTo').value = btn.getAttribute('data-email');
  document.getElementById('nameTo').value = btn.getAttribute('data-name');
});
</script>

<?php if (isset($_GET['edit']) && isset($editItem) && $editItem): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var myModal = new bootstrap.Modal(document.getElementById('supplierModal'));
  myModal.show();
});
</script>
<?php endif; ?>
<?php require 'inc/admin_footer.php'; ?>
