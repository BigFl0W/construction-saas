<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Material {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null, $supplierId = null, $lowStock = false, $limit = null, $offset = null) {
        $sql = "SELECT m.*, s.name as supplier_name, s.email as supplier_email FROM materials m LEFT JOIN suppliers s ON m.supplier_id = s.id WHERE m.deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND m.status = :status"; $params['status'] = $status; }
        if ($supplierId) { $sql .= " AND m.supplier_id = :supplier_id"; $params['supplier_id'] = $supplierId; }
        if ($lowStock) { $sql .= " AND m.current_stock <= m.reorder_level"; }
        $sql .= " ORDER BY m.name";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT m.*, s.name as supplier_name, s.email as supplier_email, s.phone as supplier_phone FROM materials m LEFT JOIN suppliers s ON m.supplier_id = s.id WHERE m.id = :id AND m.deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $uuid = $this->functions->generateUUID();
        $sql = "INSERT INTO materials (uuid, name, description, category, unit, current_stock, reorder_level, unit_cost, supplier_id, location_stored, status, notes, created_at, updated_at) VALUES (:uuid, :name, :description, :category, :unit, :current_stock, :reorder_level, :unit_cost, :supplier_id, :location_stored, :status, :notes, NOW(), NOW())";
        $this->db->query($sql, [
            'uuid' => $uuid, 'name' => $data['name'], 'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null, 'unit' => $data['unit'],
            'current_stock' => $data['current_stock'] ?? 0, 'reorder_level' => $data['reorder_level'] ?? 0,
            'unit_cost' => $data['unit_cost'] ?? 0, 'supplier_id' => $data['supplier_id'] ?? null,
            'location_stored' => $data['location_stored'] ?? null, 'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE materials SET name = :name, description = :description, category = :category, unit = :unit, current_stock = :current_stock, reorder_level = :reorder_level, unit_cost = :unit_cost, supplier_id = :supplier_id, location_stored = :location_stored, status = :status, notes = :notes, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, [
            'id' => $id, 'name' => $data['name'], 'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null, 'unit' => $data['unit'],
            'current_stock' => $data['current_stock'] ?? 0, 'reorder_level' => $data['reorder_level'] ?? 0,
            'unit_cost' => $data['unit_cost'] ?? 0, 'supplier_id' => $data['supplier_id'] ?? null,
            'location_stored' => $data['location_stored'] ?? null, 'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("UPDATE materials SET deleted_at = NOW() WHERE id = :id", ['id' => $id]);
    }

    public function getCount($status = null, $lowStock = false) {
        $sql = "SELECT COUNT(*) as total FROM materials WHERE deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND status = :status"; $params['status'] = $status; }
        if ($lowStock) { $sql .= " AND current_stock <= reorder_level"; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }

    public function adjustStock($id, $quantity, $reason = null) {
        $sql = "UPDATE materials SET current_stock = current_stock + :quantity, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id, 'quantity' => $quantity]);
    }

    public function logUsage($data) {
        $sql = "INSERT INTO material_usage (project_id, material_id, quantity_used, usage_date, notes, created_by, created_at) VALUES (:project_id, :material_id, :quantity_used, :usage_date, :notes, :created_by, NOW())";
        return $this->db->query($sql, [
            'project_id' => $data['project_id'], 'material_id' => $data['material_id'],
            'quantity_used' => $data['quantity_used'], 'usage_date' => $data['usage_date'],
            'notes' => $data['notes'] ?? null, 'created_by' => $data['created_by'] ?? null
        ]);
    }

    public function getUsage($materialId = null, $projectId = null) {
        $sql = "SELECT mu.*, m.name as material_name, p.name as project_name FROM material_usage mu JOIN materials m ON mu.material_id = m.id JOIN projects p ON mu.project_id = p.id";
        $params = [];
        $where = [];
        if ($materialId) { $where[] = "mu.material_id = :mat_id"; $params['mat_id'] = $materialId; }
        if ($projectId) { $where[] = "mu.project_id = :proj_id"; $params['proj_id'] = $projectId; }
        if ($where) { $sql .= " WHERE " . implode(' AND ', $where); }
        $sql .= " ORDER BY mu.usage_date DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }
}
