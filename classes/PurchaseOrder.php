<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class PurchaseOrder {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null, $supplierId = null, $projectId = null) {
        $sql = "SELECT po.*, s.name as supplier_name, p.name as project_name FROM purchase_orders po LEFT JOIN suppliers s ON po.supplier_id = s.id LEFT JOIN projects p ON po.project_id = p.id";
        $params = [];
        $where = [];
        if ($status) { $where[] = "po.status = :status"; $params['status'] = $status; }
        if ($supplierId) { $where[] = "po.supplier_id = :supplier_id"; $params['supplier_id'] = $supplierId; }
        if ($projectId) { $where[] = "po.project_id = :project_id"; $params['project_id'] = $projectId; }
        if ($where) { $sql .= " WHERE " . implode(' AND ', $where); }
        $sql .= " ORDER BY po.created_at DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT po.*, s.name as supplier_name, s.email as supplier_email, s.phone as supplier_phone, p.name as project_name FROM purchase_orders po LEFT JOIN suppliers s ON po.supplier_id = s.id LEFT JOIN projects p ON po.project_id = p.id WHERE po.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO purchase_orders (po_number, supplier_id, project_id, order_date, expected_delivery, delivery_date, status, subtotal, tax, total, payment_terms, notes, created_by, created_at, updated_at) VALUES (:po_number, :supplier_id, :project_id, :order_date, :expected_delivery, :delivery_date, :status, :subtotal, :tax, :total, :payment_terms, :notes, :created_by, NOW(), NOW())";
        return $this->db->query($sql, [
            'po_number' => $data['po_number'], 'supplier_id' => $data['supplier_id'],
            'project_id' => $data['project_id'] ?? null, 'order_date' => $data['order_date'],
            'expected_delivery' => $data['expected_delivery'] ?? null, 'delivery_date' => $data['delivery_date'] ?? null,
            'status' => $data['status'] ?? 'draft', 'subtotal' => $data['subtotal'] ?? 0, 'tax' => $data['tax'] ?? 0,
            'total' => $data['total'] ?? 0, 'payment_terms' => $data['payment_terms'] ?? null,
            'notes' => $data['notes'] ?? null, 'created_by' => $data['created_by'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE purchase_orders SET supplier_id = :supplier_id, project_id = :project_id, order_date = :order_date, expected_delivery = :expected_delivery, delivery_date = :delivery_date, status = :status, subtotal = :subtotal, tax = :tax, total = :total, payment_terms = :payment_terms, notes = :notes, updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, [
            'id' => $id, 'supplier_id' => $data['supplier_id'], 'project_id' => $data['project_id'] ?? null,
            'order_date' => $data['order_date'], 'expected_delivery' => $data['expected_delivery'] ?? null,
            'delivery_date' => $data['delivery_date'] ?? null, 'status' => $data['status'] ?? 'draft',
            'subtotal' => $data['subtotal'] ?? 0, 'tax' => $data['tax'] ?? 0, 'total' => $data['total'] ?? 0,
            'payment_terms' => $data['payment_terms'] ?? null, 'notes' => $data['notes'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM purchase_orders WHERE id = :id", ['id' => $id]);
    }

    public function generatePONumber() {
        $prefix = 'PO-' . date('Y') . '-';
        $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(po_number, LENGTH(:prefix) + 1) AS UNSIGNED)) as max_num FROM purchase_orders WHERE po_number LIKE :prefix2", ['prefix' => $prefix, 'prefix2' => $prefix . '%']);
        $result = $stmt->fetch();
        return $prefix . str_pad(($result['max_num'] ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getItems($poId) {
        return $this->db->query("SELECT poi.*, m.name as material_name, m.unit FROM purchase_order_items poi JOIN materials m ON poi.material_id = m.id WHERE poi.purchase_order_id = :id", ['id' => $poId])->fetchAll();
    }

    public function addItem($data) {
        $sql = "INSERT INTO purchase_order_items (purchase_order_id, material_id, quantity, unit_price, received_quantity, notes) VALUES (:purchase_order_id, :material_id, :quantity, :unit_price, 0, :notes)";
        return $this->db->query($sql, [
            'purchase_order_id' => $data['purchase_order_id'], 'material_id' => $data['material_id'],
            'quantity' => $data['quantity'], 'unit_price' => $data['unit_price'], 'notes' => $data['notes'] ?? null
        ]);
    }
}
