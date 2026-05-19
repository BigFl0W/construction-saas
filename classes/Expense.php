<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Expense {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($projectId = null, $category = null, $limit = null, $offset = null) {
        $sql = "SELECT e.*, p.name as project_name FROM expenses e LEFT JOIN projects p ON e.project_id = p.id";
        $params = [];
        $where = [];
        if ($projectId) { $where[] = "e.project_id = :project_id"; $params['project_id'] = $projectId; }
        if ($category) { $where[] = "e.category = :category"; $params['category'] = $category; }
        if ($where) { $sql .= " WHERE " . implode(' AND ', $where); }
        $sql .= " ORDER BY e.expense_date DESC";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT e.*, p.name as project_name FROM expenses e LEFT JOIN projects p ON e.project_id = p.id WHERE e.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO expenses (project_id, expense_date, category, description, amount, payment_method, vendor, receipt_path, approved_by, notes, created_by, created_at, updated_at) VALUES (:project_id, :expense_date, :category, :description, :amount, :payment_method, :vendor, :receipt_path, :approved_by, :notes, :created_by, NOW(), NOW())";
        return $this->db->query($sql, [
            'project_id' => $data['project_id'] ?? null, 'expense_date' => $data['expense_date'],
            'category' => $data['category'] ?? null, 'description' => $data['description'],
            'amount' => $data['amount'], 'payment_method' => $data['payment_method'] ?? null,
            'vendor' => $data['vendor'] ?? null, 'receipt_path' => $data['receipt_path'] ?? null,
            'approved_by' => $data['approved_by'] ?? null, 'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE expenses SET project_id = :project_id, expense_date = :expense_date, category = :category, description = :description, amount = :amount, payment_method = :payment_method, vendor = :vendor, receipt_path = :receipt_path, approved_by = :approved_by, notes = :notes, updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, [
            'id' => $id, 'project_id' => $data['project_id'] ?? null, 'expense_date' => $data['expense_date'],
            'category' => $data['category'] ?? null, 'description' => $data['description'], 'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? null, 'vendor' => $data['vendor'] ?? null,
            'receipt_path' => $data['receipt_path'] ?? null, 'approved_by' => $data['approved_by'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM expenses WHERE id = :id", ['id' => $id]);
    }

    public function getCount($projectId = null) {
        $sql = "SELECT COUNT(*) as total FROM expenses";
        $params = [];
        if ($projectId) { $sql .= " WHERE project_id = :project_id"; $params['project_id'] = $projectId; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }

    public function getTotalByProject($projectId) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE project_id = :project_id";
        return $this->db->query($sql, ['project_id' => $projectId])->fetch()['total'];
    }

    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM expenses WHERE category IS NOT NULL ORDER BY category";
        return array_column($this->db->query($sql)->fetchAll(), 'category');
    }
}
