<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();
require_once '../classes/Invoice.php';

$invoice = new Invoice();
// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Security validation failed. Please try again.';
    } else {
        try {
            if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
                $data = [
                    'invoice_number' => $_POST['invoice_number'],
                    'project_id' => $_POST['project_id'] ?: null,
                    'client_id' => $_POST['client_id'] ?: null,
                    'invoice_date' => $_POST['invoice_date'],
                    'due_date' => $_POST['due_date'],
                    'subtotal' => floatval($_POST['subtotal'] ?? 0),
                    'tax' => floatval($_POST['tax'] ?? 0),
                    'total' => floatval($_POST['total'] ?? 0),
                    'status' => $_POST['status'] ?? 'draft',
                    'notes' => $_POST['notes'] ?? null,
                    'created_by' => $currentUser['id'] ?? null
                ];

                if ($_POST['action'] === 'create') {
                    $id = $invoice->create($data);
                    // Save line items
                    if (!empty($_POST['item_description'])) {
                        foreach ($_POST['item_description'] as $i => $desc) {
                            if (!empty(trim($desc))) {
                                $invoice->addItem([
                                    'invoice_id' => $id,
                                    'description' => $desc,
                                    'quantity' => floatval($_POST['item_quantity'][$i] ?? 1),
                                    'unit_price' => floatval($_POST['item_unit_price'][$i] ?? 0)
                                ]);
                            }
                        }
                    }
                    $_SESSION['toast_success'] = 'Invoice created successfully.';
                } else {
                    $invoice->update($_POST['id'], $data);
                    // Remove existing items and re-add
                    $existingItems = $invoice->getItems($_POST['id']);
                    foreach ($existingItems as $item) {
                        $invoice->removeItem($item['id']);
                    }
                    if (!empty($_POST['item_description'])) {
                        foreach ($_POST['item_description'] as $i => $desc) {
                            if (!empty(trim($desc))) {
                                $invoice->addItem([
                                    'invoice_id' => $_POST['id'],
                                    'description' => $desc,
                                    'quantity' => floatval($_POST['item_quantity'][$i] ?? 1),
                                    'unit_price' => floatval($_POST['item_unit_price'][$i] ?? 0)
                                ]);
                            }
                        }
                    }
                    $_SESSION['toast_success'] = 'Invoice updated successfully.';
                }
            } elseif ($_POST['action'] === 'add_payment') {
                $invoice->addPayment([
                    'invoice_id' => $_POST['invoice_id'],
                    'payment_date' => $_POST['payment_date'],
                    'amount' => floatval($_POST['amount']),
                    'payment_method' => $_POST['payment_method'],
                    'reference_number' => $_POST['reference_number'] ?? null,
                    'notes' => $_POST['notes'] ?? null
                ]);
                $_SESSION['toast_success'] = 'Payment recorded successfully.';
            }
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: invoices.php');
    exit;
}

// Handle GET actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        try {
            $invoice->delete($_GET['id']);
            $_SESSION['toast_success'] = 'Invoice deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error deleting invoice: ' . $e->getMessage();
        }
        header('Location: invoices.php');
        exit;
    }
}

$invoices = $invoice->getAll();
$projects = $db->query("SELECT id, name FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$clients = $db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name")->fetchAll();
$editData = null;
$editItems = [];
$editPayments = [];
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editData = $invoice->getById($_GET['id']);
    $editItems = $invoice->getItems($_GET['id']);
    $editPayments = $invoice->getPayments($_GET['id']);
}
$viewData = null;
$viewItems = [];
$viewPayments = [];
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $viewData = $invoice->getById($_GET['id']);
    $viewItems = $invoice->getItems($_GET['id']);
    $viewPayments = $invoice->getPayments($_GET['id']);
}
$newInvoiceNumber = $invoice->generateInvoiceNumber();
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'invoices';
$pageTitle = 'TPV Construction and Services LTD · Invoices';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Financial</a></li>
                                <li class="breadcrumb-item active">Invoices</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">


                    <?php if (isset($_GET['action']) && $_GET['action'] === 'view' && $viewData): ?>
                    <!-- View Invoice -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">Invoice · <?php echo htmlspecialchars($viewData['invoice_number']); ?></span>
                            <div>
                                <a href="invoices.php?action=edit&id=<?php echo $viewData['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="invoices.php" class="btn btn-sm btn-secondary">Back to list</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Client:</strong> <?php echo htmlspecialchars($viewData['client_name'] ?? 'N/A'); ?></p>
                                    <p><strong>Project:</strong> <?php echo htmlspecialchars($viewData['project_name'] ?? 'N/A'); ?></p>
                                    <p><strong>Invoice Date:</strong> <?php echo $functions->formatDate($viewData['invoice_date']); ?></p>
                                    <p><strong>Due Date:</strong> <?php echo $functions->formatDate($viewData['due_date']); ?></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="status-badge status-<?php echo $viewData['status']; ?>"><?php echo ucfirst($viewData['status']); ?></span>
                                    <p class="mt-3"><strong>Total:</strong> <?php echo $functions->formatCurrency($viewData['total']); ?></p>
                                    <p><strong>Paid:</strong> <?php echo $functions->formatCurrency($viewData['amount_paid']); ?></p>
                                    <p><strong>Balance:</strong> <?php echo $functions->formatCurrency($viewData['total'] - $viewData['amount_paid']); ?></p>
                                </div>
                            </div>
                            <?php if ($viewItems): ?>
                            <h6 class="font-montserrat all-caps mb-3">Line Items</h6>
                            <table class="table table-sm">
                                <thead><tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                                <tbody>
                                    <?php foreach ($viewItems as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                                        <td><?php echo number_format($item['quantity'], 2); ?></td>
                                        <td><?php echo $functions->formatCurrency($item['unit_price']); ?></td>
                                        <td><?php echo $functions->formatCurrency($item['quantity'] * $item['unit_price']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                            <?php if ($viewPayments): ?>
                            <h6 class="font-montserrat all-caps mb-3 mt-4">Payments Received</h6>
                            <table class="table table-sm">
                                <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th>Amount</th></tr></thead>
                                <tbody>
                                    <?php foreach ($viewPayments as $p): ?>
                                    <tr>
                                        <td><?php echo $functions->formatDate($p['payment_date']); ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $p['payment_method'])); ?></td>
                                        <td><?php echo htmlspecialchars($p['reference_number'] ?? '-'); ?></td>
                                        <td><?php echo $functions->formatCurrency($p['amount']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                            <?php if ($viewData['notes']): ?>
                            <div class="mt-3"><strong>Notes:</strong><br><?php echo nl2br(htmlspecialchars($viewData['notes'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['action']) && (($_GET['action'] === 'new') || ($_GET['action'] === 'edit' && $editData))): ?>
                    <!-- Invoice Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <span class="card-title font-montserrat all-caps"><?php echo $editData ? 'Edit Invoice' : 'New Invoice'; ?></span>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
                                <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                                <?php if ($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Invoice Number</label>
                                        <input type="text" class="form-control" name="invoice_number" value="<?php echo htmlspecialchars($editData['invoice_number'] ?? $newInvoiceNumber); ?>" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Project</label>
                                        <select class="form-select" name="project_id">
                                            <option value="">-- Select Project --</option>
                                            <?php foreach ($projects as $p): ?>
                                            <option value="<?php echo $p['id']; ?>" <?php echo ($editData['project_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Client</label>
                                        <select class="form-select" name="client_id">
                                            <option value="">-- Select Client --</option>
                                            <?php foreach ($clients as $c): ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo ($editData['client_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['company_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Invoice Date</label>
                                        <input type="date" class="form-control" name="invoice_date" value="<?php echo htmlspecialchars($editData['invoice_date'] ?? date('Y-m-d')); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" class="form-control" name="due_date" value="<?php echo htmlspecialchars($editData['due_date'] ?? date('Y-m-d', strtotime('+30 days'))); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <?php foreach (['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'] as $s): ?>
                                            <option value="<?php echo $s; ?>" <?php echo ($editData['status'] ?? 'draft') === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Subtotal</label>
                                        <input type="number" step="0.01" class="form-control" name="subtotal" id="subtotal" value="<?php echo htmlspecialchars($editData['subtotal'] ?? '0'); ?>" onchange="calcTotal()">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Tax</label>
                                        <input type="number" step="0.01" class="form-control" name="tax" id="tax" value="<?php echo htmlspecialchars($editData['tax'] ?? '0'); ?>" onchange="calcTotal()">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Total</label>
                                        <input type="number" step="0.01" class="form-control" name="total" id="total" value="<?php echo htmlspecialchars($editData['total'] ?? '0'); ?>" readonly>
                                    </div>
                                </div>
                                <!-- Line Items -->
                                <h6 class="font-montserrat all-caps mt-3">Line Items</h6>
                                <div id="items-container">
                                    <?php if ($editItems): ?>
                                        <?php foreach ($editItems as $idx => $item): ?>
                                        <div class="item-row row g-2 align-items-center">
                                            <div class="col-md-6"><input type="text" class="form-control" name="item_description[]" placeholder="Description" value="<?php echo htmlspecialchars($item['description']); ?>"></div>
                                            <div class="col-md-2"><input type="number" step="0.01" class="form-control" name="item_quantity[]" placeholder="Qty" value="<?php echo $item['quantity']; ?>"></div>
                                            <div class="col-md-3"><input type="number" step="0.01" class="form-control" name="item_unit_price[]" placeholder="Unit Price" value="<?php echo $item['unit_price']; ?>"></div>
                                            <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-row').remove()">&times;</button></div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <div class="item-row row g-2 align-items-center">
                                        <div class="col-md-6"><input type="text" class="form-control" name="item_description[]" placeholder="Description"></div>
                                        <div class="col-md-2"><input type="number" step="0.01" class="form-control" name="item_quantity[]" placeholder="Qty" value="1"></div>
                                        <div class="col-md-3"><input type="number" step="0.01" class="form-control" name="item_unit_price[]" placeholder="Unit Price"></div>
                                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-row').remove()">&times;</button></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addItemRow()"><i class="fas fa-plus"></i> Add Item</button>
                                <div class="row mt-3">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($editData['notes'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="invoices.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Update Invoice' : 'Create Invoice'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Invoices List -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">All Invoices</span>
                            <a href="invoices.php?action=new" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Invoice</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" data-table id="invoices-table">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Project</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td><a href="invoices.php?action=view&id=<?php echo $inv['id']; ?>"><?php echo htmlspecialchars($inv['invoice_number']); ?></a></td>
                                        <td><?php echo htmlspecialchars($inv['project_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($inv['client_name'] ?? '-'); ?></td>
                                        <td><?php echo $functions->formatDate($inv['invoice_date']); ?></td>
                                        <td><?php echo $functions->formatDate($inv['due_date']); ?></td>
                                        <td><?php echo $functions->formatCurrency($inv['total']); ?></td>
                                        <td><?php echo $functions->formatCurrency($inv['amount_paid']); ?></td>
                                        <td><?php echo $functions->formatCurrency(($inv['total'] ?? 0) - ($inv['amount_paid'] ?? 0)); ?></td>
                                        <td><span class="status-badge status-<?php echo $inv['status']; ?>"><?php echo ucfirst($inv['status']); ?></span></td>
                                        <td>
                                            <a href="invoices.php?action=view&id=<?php echo $inv['id']; ?>" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye" style="width:14px;height:14px;"></i></a>
                                            <a href="invoices.php?action=edit&id=<?php echo $inv['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px;"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-success" title="Record Payment" data-bs-toggle="modal" data-bs-target="#paymentModal" data-invoice-id="<?php echo $inv['id']; ?>" data-invoice-number="<?php echo htmlspecialchars($inv['invoice_number']); ?>" data-balance="<?php echo ($inv['total'] ?? 0) - ($inv['amount_paid'] ?? 0); ?>"><i class="fas fa-dollar-sign" style="width:14px;height:14px;"></i></button>
                                            <a href="invoices.php?action=delete&id=<?php echo $inv['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirmAction(this, 'Delete this invoice?')"><i class="fas fa-trash" style="width:14px;height:14px;"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Record Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
          <input type="hidden" name="action" value="add_payment">
          <input type="hidden" name="invoice_id" id="paymentInvoiceId">
          <div class="mb-3">
            <label class="form-label">Invoice</label>
            <input type="text" id="paymentInvoiceNumber" class="form-control" disabled>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Payment Date</label>
              <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Amount</label>
              <input type="number" step="0.01" name="amount" id="paymentAmount" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select" required>
              <option value="cash">Cash</option>
              <option value="check">Check</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="credit_card">Credit Card</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Reference Number</label>
            <input type="text" name="reference_number" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Record Payment</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('paymentModal').addEventListener('show.bs.modal', function(e) {
  var btn = e.relatedTarget;
  document.getElementById('paymentInvoiceId').value = btn.getAttribute('data-invoice-id');
  document.getElementById('paymentInvoiceNumber').value = btn.getAttribute('data-invoice-number');
  document.getElementById('paymentAmount').value = btn.getAttribute('data-balance');
});
</script>
<?php require 'inc/admin_footer.php'; ?>
