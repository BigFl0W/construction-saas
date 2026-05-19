<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();
require_once '../classes/PurchaseOrder.php';

$po = new PurchaseOrder();

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Security validation failed. Please try again.';
    } else {
        try {
            if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
                $data = [
                    'po_number' => $_POST['po_number'],
                    'supplier_id' => $_POST['supplier_id'],
                    'project_id' => $_POST['project_id'] ?: null,
                    'order_date' => $_POST['order_date'],
                    'expected_delivery' => $_POST['expected_delivery'] ?: null,
                    'delivery_date' => $_POST['delivery_date'] ?: null,
                    'status' => $_POST['status'] ?? 'draft',
                    'subtotal' => floatval($_POST['subtotal'] ?? 0),
                    'tax' => floatval($_POST['tax'] ?? 0),
                    'total' => floatval($_POST['total'] ?? 0),
                    'payment_terms' => $_POST['payment_terms'] ?? null,
                    'notes' => $_POST['notes'] ?? null,
                    'created_by' => $currentUser['id'] ?? null
                ];

                if ($_POST['action'] === 'create') {
                    $id = $po->create($data);
                    // Save line items
                    if (!empty($_POST['item_material_id'])) {
                        foreach ($_POST['item_material_id'] as $i => $matId) {
                            if (!empty($matId)) {
                                $po->addItem([
                                    'purchase_order_id' => $id,
                                    'material_id' => $matId,
                                    'quantity' => floatval($_POST['item_quantity'][$i] ?? 1),
                                    'unit_price' => floatval($_POST['item_unit_price'][$i] ?? 0),
                                    'notes' => $_POST['item_notes'][$i] ?? null
                                ]);
                            }
                        }
                    }
                    $_SESSION['toast_success'] = 'Purchase order created successfully.';
                } else {
                    $po->update($_POST['id'], $data);
                    // Rebuild items
                    $db->query("DELETE FROM purchase_order_items WHERE purchase_order_id = :id", ['id' => $_POST['id']]);
                    if (!empty($_POST['item_material_id'])) {
                        foreach ($_POST['item_material_id'] as $i => $matId) {
                            if (!empty($matId)) {
                                $po->addItem([
                                    'purchase_order_id' => $_POST['id'],
                                    'material_id' => $matId,
                                    'quantity' => floatval($_POST['item_quantity'][$i] ?? 1),
                                    'unit_price' => floatval($_POST['item_unit_price'][$i] ?? 0),
                                    'notes' => $_POST['item_notes'][$i] ?? null
                                ]);
                            }
                        }
                    }
                    $_SESSION['toast_success'] = 'Purchase order updated successfully.';
                }
            }
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        }
    }
    header('Location: purchase_orders.php');
    exit;
}

// Handle GET actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        try {
            $db->query("DELETE FROM purchase_order_items WHERE purchase_order_id = :id", ['id' => $_GET['id']]);
            $po->delete($_GET['id']);
            $_SESSION['toast_success'] = 'Purchase order deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['toast_error'] = 'Error deleting purchase order: ' . $e->getMessage();
        }
        header('Location: purchase_orders.php');
        exit;
    }
}

$orders = $po->getAll();
$suppliers = $db->query("SELECT id, name FROM suppliers WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$projects = $db->query("SELECT id, name FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$materials = $db->query("SELECT id, name, unit, unit_cost FROM materials WHERE status = 'active' AND deleted_at IS NULL ORDER BY name")->fetchAll();
$editData = null;
$editItems = [];
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editData = $po->getById($_GET['id']);
    $editItems = $po->getItems($_GET['id']);
}
$viewData = null;
$viewItems = [];
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $viewData = $po->getById($_GET['id']);
    $viewItems = $po->getItems($_GET['id']);
}
$newPONumber = $po->generatePONumber();
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'purchase_orders';
$pageTitle = 'TPV Construction and Services LTD · Purchase Orders';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="#">Financial</a></li>
                                <li class="breadcrumb-item active">Purchase Orders</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <?php if (isset($_GET['action']) && $_GET['action'] === 'view' && $viewData): ?>
                    <!-- View Purchase Order -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">Purchase Order · <?php echo htmlspecialchars($viewData['po_number']); ?></span>
                            <div>
                                <a href="purchase_orders.php?action=edit&id=<?php echo $viewData['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="purchase_orders.php" class="btn btn-sm btn-secondary">Back to list</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Supplier:</strong> <?php echo htmlspecialchars($viewData['supplier_name'] ?? 'N/A'); ?></p>
                                    <p><strong>Supplier Email:</strong> <?php echo htmlspecialchars($viewData['supplier_email'] ?? 'N/A'); ?></p>
                                    <p><strong>Supplier Phone:</strong> <?php echo htmlspecialchars($viewData['supplier_phone'] ?? 'N/A'); ?></p>
                                    <p><strong>Project:</strong> <?php echo htmlspecialchars($viewData['project_name'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="status-badge status-<?php echo $viewData['status']; ?>"><?php echo ucfirst($viewData['status']); ?></span>
                                    <p class="mt-3"><strong>Order Date:</strong> <?php echo $functions->formatDate($viewData['order_date']); ?></p>
                                    <p><strong>Expected Delivery:</strong> <?php echo $viewData['expected_delivery'] ? $functions->formatDate($viewData['expected_delivery']) : 'N/A'; ?></p>
                                    <p><strong>Delivery Date:</strong> <?php echo $viewData['delivery_date'] ? $functions->formatDate($viewData['delivery_date']) : 'N/A'; ?></p>
                                    <h4><?php echo $functions->formatCurrency($viewData['total']); ?></h4>
                                </div>
                            </div>
                            <?php if ($viewItems): ?>
                            <h6 class="font-montserrat all-caps mb-3">Line Items</h6>
                            <table class="table table-sm">
                                <thead><tr><th>Material</th><th>Qty</th><th>Unit</th><th>Unit Price</th><th>Total</th></tr></thead>
                                <tbody>
                                    <?php foreach ($viewItems as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['material_name']); ?></td>
                                        <td><?php echo number_format($item['quantity'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                        <td><?php echo $functions->formatCurrency($item['unit_price']); ?></td>
                                        <td><?php echo $functions->formatCurrency($item['quantity'] * $item['unit_price']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                            <?php if ($viewData['notes']): ?>
                            <div class="mt-3"><strong>Notes:</strong><br><?php echo nl2br(htmlspecialchars($viewData['notes'])); ?></div>
                            <?php endif; ?>
                            <?php if ($viewData['payment_terms']): ?>
                            <div class="mt-2"><strong>Payment Terms:</strong> <?php echo htmlspecialchars($viewData['payment_terms']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ((isset($_GET['action']) && ($_GET['action'] === 'new' || $_GET['action'] === 'edit')) && ($_GET['action'] !== 'edit' || $editData)): ?>
                    <!-- PO Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <span class="card-title font-montserrat all-caps"><?php echo $editData ? 'Edit Purchase Order' : 'New Purchase Order'; ?></span>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRF(); ?>">
                                <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                                <?php if ($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">PO Number</label>
                                        <input type="text" class="form-control" name="po_number" value="<?php echo htmlspecialchars($editData['po_number'] ?? $newPONumber); ?>" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Supplier</label>
                                        <select class="form-select" name="supplier_id" required>
                                            <option value="">-- Select Supplier --</option>
                                            <?php foreach ($suppliers as $s): ?>
                                            <option value="<?php echo $s['id']; ?>" <?php echo ($editData['supplier_id'] ?? '') == $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
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
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Order Date</label>
                                        <input type="date" class="form-control" name="order_date" value="<?php echo htmlspecialchars($editData['order_date'] ?? date('Y-m-d')); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Expected Delivery</label>
                                        <input type="date" class="form-control" name="expected_delivery" value="<?php echo htmlspecialchars($editData['expected_delivery'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Delivery Date</label>
                                        <input type="date" class="form-control" name="delivery_date" value="<?php echo htmlspecialchars($editData['delivery_date'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <?php foreach (['draft', 'sent', 'confirmed', 'partial', 'received', 'cancelled'] as $s): ?>
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
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Payment Terms</label>
                                        <input type="text" class="form-control" name="payment_terms" value="<?php echo htmlspecialchars($editData['payment_terms'] ?? ''); ?>" placeholder="e.g. Net 30">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Notes</label>
                                        <input type="text" class="form-control" name="notes" value="<?php echo htmlspecialchars($editData['notes'] ?? ''); ?>">
                                    </div>
                                </div>

                                <!-- Line Items -->
                                <h6 class="font-montserrat all-caps mt-3">Order Items</h6>
                                <div id="items-container">
                                    <?php if ($editItems): ?>
                                        <?php foreach ($editItems as $idx => $item): ?>
                                        <div class="item-row row g-2 align-items-center">
                                            <div class="col-md-4">
                                                <select class="form-select item-material" name="item_material_id[]" onchange="updateUnitPrice(this)">
                                                    <option value="">-- Select Material --</option>
                                                    <?php foreach ($materials as $m): ?>
                                                    <option value="<?php echo $m['id']; ?>" data-cost="<?php echo $m['unit_cost']; ?>" <?php echo $item['material_id'] == $m['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['name']); ?> (<?php echo htmlspecialchars($m['unit']); ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2"><input type="number" step="0.01" class="form-control" name="item_quantity[]" placeholder="Qty" value="<?php echo $item['quantity']; ?>"></div>
                                            <div class="col-md-3"><input type="number" step="0.01" class="form-control item-unit-price" name="item_unit_price[]" placeholder="Unit Price" value="<?php echo $item['unit_price']; ?>"></div>
                                            <div class="col-md-2"><input type="text" class="form-control" name="item_notes[]" placeholder="Notes" value="<?php echo htmlspecialchars($item['notes'] ?? ''); ?>"></div>
                                            <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-row').remove()">&times;</button></div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <div class="item-row row g-2 align-items-center">
                                        <div class="col-md-4">
                                            <select class="form-select item-material" name="item_material_id[]" onchange="updateUnitPrice(this)">
                                                <option value="">-- Select Material --</option>
                                                <?php foreach ($materials as $m): ?>
                                                <option value="<?php echo $m['id']; ?>" data-cost="<?php echo $m['unit_cost']; ?>"><?php echo htmlspecialchars($m['name']); ?> (<?php echo htmlspecialchars($m['unit']); ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2"><input type="number" step="0.01" class="form-control" name="item_quantity[]" placeholder="Qty" value="1"></div>
                                        <div class="col-md-3"><input type="number" step="0.01" class="form-control item-unit-price" name="item_unit_price[]" placeholder="Unit Price"></div>
                                        <div class="col-md-2"><input type="text" class="form-control" name="item_notes[]" placeholder="Notes"></div>
                                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-row').remove()">&times;</button></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addItemRow()"><i class="fas fa-plus"></i> Add Item</button>

                                <div class="text-end mt-3">
                                    <a href="purchase_orders.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Update Purchase Order' : 'Create Purchase Order'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Purchase Orders List -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="card-title font-montserrat all-caps">All Purchase Orders</span>
                            <a href="purchase_orders.php?action=new" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Purchase Order</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" data-table id="po-table">
                                <thead>
                                    <tr>
                                        <th>PO #</th>
                                        <th>Supplier</th>
                                        <th>Project</th>
                                        <th>Order Date</th>
                                        <th>Expected Delivery</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td><a href="purchase_orders.php?action=view&id=<?php echo $o['id']; ?>"><?php echo htmlspecialchars($o['po_number']); ?></a></td>
                                        <td><?php echo htmlspecialchars($o['supplier_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($o['project_name'] ?? '-'); ?></td>
                                        <td><?php echo $functions->formatDate($o['order_date']); ?></td>
                                        <td><?php echo $o['expected_delivery'] ? $functions->formatDate($o['expected_delivery']) : '-'; ?></td>
                                        <td><?php echo $functions->formatCurrency($o['total']); ?></td>
                                        <td><span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                                        <td>
                                            <a href="purchase_orders.php?action=view&id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye" style="width:14px;height:14px;"></i></a>
                                            <a href="purchase_orders.php?action=edit&id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-pen" style="width:14px;height:14px;"></i></a>
                                            <a href="purchase_orders.php?action=delete&id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirmAction(this, 'Delete this purchase order?')"><i class="fas fa-trash" style="width:14px;height:14px;"></i></a>
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
