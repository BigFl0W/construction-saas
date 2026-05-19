<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Equipment {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null, $categoryId = null, $limit = null, $offset = null) {
        $sql = "SELECT eq.*, ec.name as category_name FROM equipment eq LEFT JOIN equipment_categories ec ON eq.category_id = ec.id WHERE eq.deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND eq.status = :status"; $params['status'] = $status; }
        if ($categoryId) { $sql .= " AND eq.category_id = :cat_id"; $params['cat_id'] = $categoryId; }
        $sql .= " ORDER BY eq.name";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT eq.*, ec.name as category_name FROM equipment eq LEFT JOIN equipment_categories ec ON eq.category_id = ec.id WHERE eq.id = :id AND eq.deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $uuid = $this->functions->generateUUID();
        $sql = "INSERT INTO equipment (uuid, category_id, name, model, serial_number, purchase_date, purchase_price, current_value, status, location, notes, created_at, updated_at) VALUES (:uuid, :category_id, :name, :model, :serial_number, :purchase_date, :purchase_price, :current_value, :status, :location, :notes, NOW(), NOW())";
        $this->db->query($sql, [
            'uuid' => $uuid, 'category_id' => $data['category_id'] ?? null, 'name' => $data['name'],
            'model' => $data['model'] ?? null, 'serial_number' => $data['serial_number'] ?? null,
            'purchase_date' => $data['purchase_date'] ?? null, 'purchase_price' => $data['purchase_price'] ?? 0,
            'current_value' => $data['current_value'] ?? $data['purchase_price'] ?? 0,
            'status' => $data['status'] ?? 'available', 'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE equipment SET category_id = :category_id, name = :name, model = :model, serial_number = :serial_number, purchase_date = :purchase_date, purchase_price = :purchase_price, current_value = :current_value, status = :status, location = :location, notes = :notes, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, [
            'id' => $id, 'category_id' => $data['category_id'] ?? null, 'name' => $data['name'],
            'model' => $data['model'] ?? null, 'serial_number' => $data['serial_number'] ?? null,
            'purchase_date' => $data['purchase_date'] ?? null, 'purchase_price' => $data['purchase_price'] ?? 0,
            'current_value' => $data['current_value'] ?? 0, 'status' => $data['status'] ?? 'available',
            'location' => $data['location'] ?? null, 'notes' => $data['notes'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("UPDATE equipment SET deleted_at = NOW() WHERE id = :id", ['id' => $id]);
    }

    public function getCount($status = null) {
        $sql = "SELECT COUNT(*) as total FROM equipment WHERE deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND status = :status"; $params['status'] = $status; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }

    public function getCategories() {
        return $this->db->query("SELECT * FROM equipment_categories ORDER BY name")->fetchAll();
    }

    public function addCategory($name, $description = null) {
        return $this->db->query("INSERT INTO equipment_categories (name, description) VALUES (:name, :description)", ['name' => $name, 'description' => $description]);
    }

    public function getAssignments($equipmentId = null) {
        $sql = "SELECT ea.*, eq.name as equipment_name, p.name as project_name FROM equipment_assignments ea JOIN equipment eq ON ea.equipment_id = eq.id JOIN projects p ON ea.project_id = p.id";
        $params = [];
        if ($equipmentId) { $sql .= " WHERE ea.equipment_id = :eq_id"; $params['eq_id'] = $equipmentId; }
        $sql .= " ORDER BY ea.assigned_date DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getMaintenanceLogs($equipmentId = null) {
        $sql = "SELECT ml.*, eq.name as equipment_name FROM maintenance_logs ml JOIN equipment eq ON ml.equipment_id = eq.id";
        $params = [];
        if ($equipmentId) { $sql .= " WHERE ml.equipment_id = :eq_id"; $params['eq_id'] = $equipmentId; }
        $sql .= " ORDER BY ml.maintenance_date DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function addMaintenanceLog($data) {
        $sql = "INSERT INTO maintenance_logs (equipment_id, maintenance_date, description, cost, performed_by, next_maintenance_date, notes, created_at, updated_at) VALUES (:equipment_id, :maintenance_date, :description, :cost, :performed_by, :next_maintenance_date, :notes, NOW(), NOW())";
        return $this->db->query($sql, [
            'equipment_id' => $data['equipment_id'], 'maintenance_date' => $data['maintenance_date'],
            'description' => $data['description'], 'cost' => $data['cost'] ?? 0,
            'performed_by' => $data['performed_by'] ?? null, 'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
    }
}
