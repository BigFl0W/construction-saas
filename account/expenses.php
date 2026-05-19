<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();
require_once '../classes/Expense.php';

$expense = new Expense();

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Security validation failed. Please try again.';
        header('Location: expenses.php');
        exit;
    } else {
        try {
            $receiptPath = null;
            if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $functions->uploadFile($_FILES['receipt'], UPLOAD_PATH . 'receipts', ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
                if ($uploadResult['success']) {
                    $receiptPath = $uploadResult['filepath'];
                }
            }

            $data = [
                'project_id' => $_POST['project_id'] ?: null,
                'expense_date' => $_POST['expense_date'],
                'category' => $_POST['category'] ?? null,
                'description' => $_POST['description'],
                'amount' => floatval($_POST['amount']),
                'payment_method' => $_POST['payment_method'] ?? null,
                'vendor' => $_POST['vendor'] ?? null,
                'receipt_path' => $receiptPath,
                'approved_by' => $_POST['approved_by'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'created_by' => $currentUser['id'] ?? null
            ];

            if ($_POST['action'] === 'create') {
                $expense->create($data);
                $_SESSION['toast_success'] = 'Expense recorded successfully.';
                header('Location: expenses.php');
                exit;
            } elseif ($_POST['action'] === 'update') {
                $existing = $expense->getById($_POST['id']);
                if (!$receiptPath && $existing) {
                    $data['receipt_path'] = $existing['receipt_path'];
                }
                $expense->update($_POST['id'], $data);
                $_SESSION['toast_success'] = 'Expense updated successfully.';
                header('Location: expenses.php');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
            header('Location: expenses.php');
            exit;
        }
    }
}

// Handle GET actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        try {
            $exp = $expense->getById($_GET['id']);
            if ($exp && $exp['receipt_path']) {
                $functions->deleteFile($exp['receipt_path']);
            }
            $expense->delete($_GET['id']);
            $_SESSION['toast_success'] = 'Expense deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error deleting expense: ' . $e->getMessage();
        }
        header('Location: expenses.php');
        exit;
    }
}

$expenses = $expense->getAll();
$projects = $db->query("SELECT id, name FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$categories = $expense->getCategories();
$editData = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editData = $expense->getById($_GET['id']);
}
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'expenses';
$pageTitle = 'TPV Construction and Services LTD · Expenses';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Financial</a></li>
                                <li class="breadcrumb-item active">Expenses</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <?php if (isset($_GET['action']) && ($_GET['action'] === 'new' || ($_GET['action'] === 'edit' && $editData))): ?>
                    <!-- Expense Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <span class="card-title font-montserrat all-caps"><?php echo $editData ? 'Edit Expense' : 'Record New Expense'; ?></span>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
                                <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                                <?php if ($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Project</label>
                                        <select class="form-select" name="project_id">
                                            <option value="">-- No Project (General) --</option>
                                            <?php foreach ($projects as $p): ?>
                                            <option value="<?php echo $p['id']; ?>" <?php echo ($editData['project_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Expense Date</label>
                                        <input type="date" class="form-control" name="expense_date" value="<?php echo htmlspecialchars($editData['expense_date'] ?? date('Y-m-d')); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Category</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="category" id="category" value="<?php echo htmlspecialchars($editData['category'] ?? ''); ?>" list="category-list" placeholder="Type or select">
                                            <datalist id="category-list">
                                                <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo htmlspecialchars($cat); ?>">
                                                <?php endforeach; ?>
                                                <option value="Materials">
                                                <option value="Labor">
                                                <option value="Equipment">
                                                <option value="Transportation">
                                                <option value="Office Supplies">
                                                <option value="Utilities">
                                                <option value="Insurance">
                                                <option value="Permits">
                                                <option value="Subcontractors">
                                                <option value="Maintenance">
                                                <option value="Travel">
                                                <option value="Other">
                                            </datalist>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Description</label>
                                        <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($editData['description'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Amount</label>
                                        <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo htmlspecialchars($editData['amount'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <select class="form-select" name="payment_method">
                                            <option value="">-- Select --</option>
                                            <option value="cash" <?php echo ($editData['payment_method'] ?? '') === 'cash' ? 'selected' : ''; ?>>Cash</option>
                                            <option value="check" <?php echo ($editData['payment_method'] ?? '') === 'check' ? 'selected' : ''; ?>>Check</option>
                                            <option value="bank_transfer" <?php echo ($editData['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                            <option value="credit_card" <?php echo ($editData['payment_method'] ?? '') === 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Vendor / Payee</label>
                                        <input type="text" class="form-control" name="vendor" value="<?php echo htmlspecialchars($editData['vendor'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Approved By</label>
                                        <input type="text" class="form-control" name="approved_by" value="<?php echo htmlspecialchars($editData['approved_by'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Receipt (PDF, JPG, PNG)</label>
                                        <input type="file" class="form-control" name="receipt" accept=".pdf,.jpg,.jpeg,.png,.gif">
                                        <?php if ($editData && $editData['receipt_path']): ?>
                                        <small class="text-muted">Current receipt: <a href="<?php echo htmlspecialchars(str_replace($_SERVER['DOCUMENT_ROOT'], '', $editData['receipt_path'])); ?>" target="_blank">View</a></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"><?php echo htmlspecialchars($editData['notes'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="expenses.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Update Expense' : 'Record Expense'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Expenses List -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">All Expenses</span>
                            <a href="expenses.php?action=new" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Record Expense</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" data-table id="expenses-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Project</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Vendor</th>
                                        <th>Payment Method</th>
                                        <th>Receipt</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expenses as $e): ?>
                                    <tr>
                                        <td><?php echo $functions->formatDate($e['expense_date']); ?></td>
                                        <td><?php echo htmlspecialchars($e['project_name'] ?? '-'); ?></td>
                                        <td><span class="category-badge"><?php echo htmlspecialchars($e['category'] ?? 'Uncategorized'); ?></span></td>
                                        <td><?php echo htmlspecialchars($functions->truncateText($e['description'], 40)); ?></td>
                                        <td><?php echo $functions->formatCurrency($e['amount']); ?></td>
                                        <td><?php echo htmlspecialchars($e['vendor'] ?? '-'); ?></td>
                                        <td><span class="method-badge"><?php echo $e['payment_method'] ? ucfirst(str_replace('_', ' ', $e['payment_method'])) : '-'; ?></span></td>
                                        <td>
                                            <?php if ($e['receipt_path']): ?>
                                            <a href="<?php echo htmlspecialchars(str_replace($_SERVER['DOCUMENT_ROOT'], '', $e['receipt_path'])); ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-paperclip" style="width:14px;height:14px;"></i></a>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="expenses.php?action=edit&id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px;"></i></a>
                                            <a href="expenses.php?action=delete&id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirmAction(this, 'Delete this expense record?')"><i class="fas fa-trash" style="width:14px;height:14px;"></i></a>
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
