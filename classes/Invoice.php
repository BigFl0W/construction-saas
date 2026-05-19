<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Invoice {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null, $limit = null, $offset = null) {
        $sql = "SELECT i.*, p.name as project_name, c.company_name as client_name FROM invoices i LEFT JOIN projects p ON i.project_id = p.id LEFT JOIN clients c ON i.client_id = c.id";
        $params = [];
        if ($status) { $sql .= " WHERE i.status = :status"; $params['status'] = $status; }
        $sql .= " ORDER BY i.created_at DESC";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT i.*, p.name as project_name, c.company_name as client_name, c.email as client_email FROM invoices i LEFT JOIN projects p ON i.project_id = p.id LEFT JOIN clients c ON i.client_id = c.id WHERE i.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO invoices (invoice_number, project_id, client_id, invoice_date, due_date, subtotal, tax, total, amount_paid, status, notes, created_by, created_at, updated_at) VALUES (:invoice_number, :project_id, :client_id, :invoice_date, :due_date, :subtotal, :tax, :total, 0, :status, :notes, :created_by, NOW(), NOW())";
        $this->db->query($sql, [
            'invoice_number' => $data['invoice_number'], 'project_id' => $data['project_id'],
            'client_id' => $data['client_id'], 'invoice_date' => $data['invoice_date'], 'due_date' => $data['due_date'],
            'subtotal' => $data['subtotal'] ?? 0, 'tax' => $data['tax'] ?? 0, 'total' => $data['total'] ?? 0,
            'status' => $data['status'] ?? 'draft', 'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE invoices SET project_id = :project_id, client_id = :client_id, invoice_date = :invoice_date, due_date = :due_date, subtotal = :subtotal, tax = :tax, total = :total, amount_paid = :amount_paid, status = :status, notes = :notes, updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, [
            'id' => $id, 'project_id' => $data['project_id'], 'client_id' => $data['client_id'],
            'invoice_date' => $data['invoice_date'], 'due_date' => $data['due_date'],
            'subtotal' => $data['subtotal'] ?? 0, 'tax' => $data['tax'] ?? 0, 'total' => $data['total'] ?? 0,
            'amount_paid' => $data['amount_paid'] ?? 0, 'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM invoices WHERE id = :id", ['id' => $id]);
    }

    public function getCount($status = null) {
        $sql = "SELECT COUNT(*) as total FROM invoices";
        $params = [];
        if ($status) { $sql .= " WHERE status = :status"; $params['status'] = $status; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }

    public function generateInvoiceNumber() {
        $prefix = 'INV-' . date('Y') . '-';
        $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(invoice_number, LENGTH(:prefix) + 1) AS UNSIGNED)) as max_num FROM invoices WHERE invoice_number LIKE :prefix2", ['prefix' => $prefix, 'prefix2' => $prefix . '%']);
        $result = $stmt->fetch();
        return $prefix . str_pad(($result['max_num'] ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    public function addPayment($data) {
        $sql = "INSERT INTO payments_received (invoice_id, payment_date, amount, payment_method, reference_number, notes, created_at, updated_at) VALUES (:invoice_id, :payment_date, :amount, :payment_method, :reference_number, :notes, NOW(), NOW())";
        $result = $this->db->query($sql, [
            'invoice_id' => $data['invoice_id'], 'payment_date' => $data['payment_date'],
            'amount' => $data['amount'], 'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'] ?? null, 'notes' => $data['notes'] ?? null
        ]);
        $this->updateInvoicePaidAmount($data['invoice_id']);
        return $result;
    }

    public function updateInvoicePaidAmount($invoiceId) {
        $stmt = $this->db->query("SELECT COALESCE(SUM(amount), 0) as total_paid FROM payments_received WHERE invoice_id = :id", ['id' => $invoiceId]);
        $totalPaid = $stmt->fetch()['total_paid'];
        $stmt2 = $this->db->query("SELECT total FROM invoices WHERE id = :id", ['id' => $invoiceId]);
        $invoice = $stmt2->fetch();
        $status = 'sent';
        if ($totalPaid >= $invoice['total']) { $status = 'paid'; }
        elseif ($totalPaid > 0) { $status = 'partial'; }
        $this->db->query("UPDATE invoices SET amount_paid = :paid, status = :status, updated_at = NOW() WHERE id = :id", ['paid' => $totalPaid, 'status' => $status, 'id' => $invoiceId]);
    }

    public function getPayments($invoiceId) {
        return $this->db->query("SELECT * FROM payments_received WHERE invoice_id = :id ORDER BY payment_date DESC", ['id' => $invoiceId])->fetchAll();
    }

    public function getItems($invoiceId) {
        return $this->db->query("SELECT * FROM invoice_items WHERE invoice_id = :id", ['id' => $invoiceId])->fetchAll();
    }

    public function addItem($data) {
        $sql = "INSERT INTO invoice_items (invoice_id, description, quantity, unit_price) VALUES (:invoice_id, :description, :quantity, :unit_price)";
        return $this->db->query($sql, [
            'invoice_id' => $data['invoice_id'], 'description' => $data['description'],
            'quantity' => $data['quantity'] ?? 1, 'unit_price' => $data['unit_price'] ?? 0
        ]);
    }

    public function removeItem($id) {
        return $this->db->query("DELETE FROM invoice_items WHERE id = :id", ['id' => $id]);
    }
}
