<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

function refreshInvoicePaymentStatus($db, $invoiceId) {
    if (!$invoiceId) return;
    $stmt = $db->query("SELECT COALESCE(SUM(amount), 0) as total_paid FROM payments_received WHERE invoice_id = :id", ['id' => $invoiceId]);
    $totalPaid = $stmt->fetch()['total_paid'];
    $stmt2 = $db->query("SELECT total FROM invoices WHERE id = :id", ['id' => $invoiceId]);
    $inv = $stmt2->fetch();
    if (!$inv) return;
    $status = 'sent';
    if ($totalPaid >= $inv['total']) { $status = 'paid'; }
    elseif ($totalPaid > 0) { $status = 'partial'; }
    $db->query("UPDATE invoices SET amount_paid = :paid, status = :status, updated_at = NOW() WHERE id = :id", ['paid' => $totalPaid, 'status' => $status, 'id' => $invoiceId]);
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Security validation failed. Please try again.';
    } else {
        try {
            if ($_POST['action'] === 'add') {
                $sql = "INSERT INTO payments_received (invoice_id, payment_date, amount, payment_method, reference_number, notes, created_at, updated_at) VALUES (:invoice_id, :payment_date, :amount, :payment_method, :reference_number, :notes, NOW(), NOW())";
                $db->query($sql, [
                    'invoice_id' => $_POST['invoice_id'],
                    'payment_date' => $_POST['payment_date'],
                    'amount' => floatval($_POST['amount']),
                    'payment_method' => $_POST['payment_method'],
                    'reference_number' => $_POST['reference_number'] ?? null,
                    'notes' => $_POST['notes'] ?? null
                ]);
                refreshInvoicePaymentStatus($db, $_POST['invoice_id']);
                $_SESSION['toast_success'] = 'Payment recorded successfully.';
            } elseif ($_POST['action'] === 'update') {
                $oldPayment = $db->query("SELECT invoice_id FROM payments_received WHERE id = :id", ['id' => $_POST['id']])->fetch();
                $sql = "UPDATE payments_received SET invoice_id = :invoice_id, payment_date = :payment_date, amount = :amount, payment_method = :payment_method, reference_number = :reference_number, notes = :notes, updated_at = NOW() WHERE id = :id";
                $db->query($sql, [
                    'id' => $_POST['id'],
                    'invoice_id' => $_POST['invoice_id'],
                    'payment_date' => $_POST['payment_date'],
                    'amount' => floatval($_POST['amount']),
                    'payment_method' => $_POST['payment_method'],
                    'reference_number' => $_POST['reference_number'] ?? null,
                    'notes' => $_POST['notes'] ?? null
                ]);
                refreshInvoicePaymentStatus($db, $oldPayment['invoice_id'] ?? null);
                refreshInvoicePaymentStatus($db, $_POST['invoice_id']);
                $_SESSION['toast_success'] = 'Payment updated successfully.';
            }
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: payments.php');
    exit;
}

// Handle GET actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        try {
            $p = $db->query("SELECT invoice_id FROM payments_received WHERE id = :id", ['id' => $_GET['id']])->fetch();
            $db->query("DELETE FROM payments_received WHERE id = :id", ['id' => $_GET['id']]);
            if ($p) {
                refreshInvoicePaymentStatus($db, $p['invoice_id']);
            }
            $_SESSION['toast_success'] = 'Payment deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error deleting payment: ' . $e->getMessage();
        }
        header('Location: payments.php');
        exit;
    }
}

$editPayment = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editPayment = $db->query("SELECT * FROM payments_received WHERE id = :id", ['id' => $_GET['id']])->fetch();
}
$payments = $db->query("SELECT p.*, i.invoice_number, i.total as invoice_total FROM payments_received p JOIN invoices i ON p.invoice_id = i.id ORDER BY p.payment_date DESC")->fetchAll();
$invoiceParams = [];
$invoiceSql = "SELECT id, invoice_number, total, amount_paid, (total - amount_paid) as balance FROM invoices WHERE status NOT IN ('paid', 'cancelled')";
if ($editPayment) {
    $invoiceSql .= " OR id = :edit_invoice_id";
    $invoiceParams['edit_invoice_id'] = $editPayment['invoice_id'];
}
$invoiceSql .= " ORDER BY invoice_number";
$invoices = $db->query($invoiceSql, $invoiceParams)->fetchAll();
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'payments';
$pageTitle = 'TPV Construction and Services LTD · Payments';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Financial</a></li>
                                <li class="breadcrumb-item active">Payments Received</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <?php if (isset($_GET['action']) && ($_GET['action'] === 'new' || ($_GET['action'] === 'edit' && $editPayment))): ?>
                    <!-- Payment Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <span class="card-title font-montserrat all-caps"><?php echo $editPayment ? 'Edit Payment' : 'Record Payment'; ?></span>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
                                <input type="hidden" name="action" value="<?php echo $editPayment ? 'update' : 'add'; ?>">
                                <?php if ($editPayment): ?><input type="hidden" name="id" value="<?php echo $editPayment['id']; ?>"><?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Invoice</label>
                                        <select class="form-select" name="invoice_id" required>
                                            <option value="">-- Select Invoice --</option>
                                            <?php foreach ($invoices as $inv): ?>
                                            <option value="<?php echo $inv['id']; ?>" <?php echo ($editPayment['invoice_id'] ?? '') == $inv['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($inv['invoice_number']); ?> (Balance: <?php echo $functions->formatCurrency($inv['balance']); ?>)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" name="payment_date" value="<?php echo htmlspecialchars($editPayment['payment_date'] ?? date('Y-m-d')); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Amount</label>
                                        <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo htmlspecialchars($editPayment['amount'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <select class="form-select" name="payment_method">
                                            <option value="cash" <?php echo ($editPayment['payment_method'] ?? '') === 'cash' ? 'selected' : ''; ?>>Cash</option>
                                            <option value="check" <?php echo ($editPayment['payment_method'] ?? '') === 'check' ? 'selected' : ''; ?>>Check</option>
                                            <option value="bank_transfer" <?php echo ($editPayment['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                            <option value="credit_card" <?php echo ($editPayment['payment_method'] ?? '') === 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" class="form-control" name="reference_number" value="<?php echo htmlspecialchars($editPayment['reference_number'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Notes</label>
                                        <input type="text" class="form-control" name="notes" value="<?php echo htmlspecialchars($editPayment['notes'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="payments.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary"><?php echo $editPayment ? 'Update Payment' : 'Record Payment'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Payments List -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">All Payments Received</span>
                            <a href="payments.php?action=new" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Record Payment</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" data-table id="payments-table">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Reference</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['invoice_number']); ?></td>
                                        <td><?php echo $functions->formatDate($p['payment_date']); ?></td>
                                        <td><?php echo $functions->formatCurrency($p['amount']); ?></td>
                                        <td><span class="method-badge"><?php echo ucfirst(str_replace('_', ' ', $p['payment_method'])); ?></span></td>
                                        <td><?php echo htmlspecialchars($p['reference_number'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($functions->truncateText($p['notes'] ?? '', 30)); ?></td>
                                        <td>
                                            <a href="payments.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px;"></i></a>
                                            <a href="payments.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirmAction(this, 'Delete this payment record?')"><i class="fas fa-trash" style="width:14px;height:14px;"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
