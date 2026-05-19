<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Project {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null, $limit = null, $offset = null) {
        $sql = "SELECT p.*, 
                c.company_name as client_name, 
                e.first_name as manager_first, e.last_name as manager_last,
                COALESCE((SELECT SUM(spent_amount) FROM project_budgets WHERE project_id = p.id), 0) as budget_used,
                COALESCE((SELECT SUM(allocated_amount) FROM project_budgets WHERE project_id = p.id), p.budget_total) as budget_total
                FROM projects p 
                LEFT JOIN clients c ON p.client_id = c.id 
                LEFT JOIN employees e ON p.project_manager_id = e.id 
                WHERE p.deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND p.status = :status"; $params['status'] = $status; }
        $sql .= " ORDER BY p.created_at DESC";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT p.*, 
                c.company_name as client_name, c.email as client_email, c.phone as client_phone, 
                e.first_name as manager_first, e.last_name as manager_last,
                COALESCE((SELECT SUM(spent_amount) FROM project_budgets WHERE project_id = p.id), 0) as budget_used,
                COALESCE((SELECT SUM(allocated_amount) FROM project_budgets WHERE project_id = p.id), p.budget_total) as budget_total
                FROM projects p 
                LEFT JOIN clients c ON p.client_id = c.id 
                LEFT JOIN employees e ON p.project_manager_id = e.id 
                WHERE p.id = :id AND p.deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $uuid = $this->functions->generateUUID();
        $sql = "INSERT INTO projects (uuid, project_number, name, description, client_id, project_manager_id, location_address, city, state, start_date, estimated_end_date, budget_total, budget_used, status, priority, progress_percent, notes, created_at, updated_at) VALUES (:uuid, :project_number, :name, :description, :client_id, :project_manager_id, :location_address, :city, :state, :start_date, :estimated_end_date, :budget_total, 0, :status, :priority, :progress_percent, :notes, NOW(), NOW())";
        $this->db->query($sql, [
            'uuid' => $uuid, 'project_number' => $data['project_number'], 'name' => $data['name'],
            'description' => $this->nullIfEmpty($data['description'] ?? null), 'client_id' => $data['client_id'], 'project_manager_id' => $this->nullIfEmpty($data['project_manager_id'] ?? null),
            'location_address' => $this->nullIfEmpty($data['location_address'] ?? null), 'city' => $this->nullIfEmpty($data['city'] ?? null), 'state' => $this->nullIfEmpty($data['state'] ?? null),
            'start_date' => $this->nullIfEmpty($data['start_date'] ?? null), 'estimated_end_date' => $this->nullIfEmpty($data['estimated_end_date'] ?? null),
            'budget_total' => $data['budget_total'] ?? 0, 'status' => $data['status'] ?? 'planning',
            'priority' => $data['priority'] ?? 'medium', 'progress_percent' => $data['progress_percent'] ?? 0,
            'notes' => $this->nullIfEmpty($data['notes'] ?? null)
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE projects SET project_number = :project_number, name = :name, description = :description, client_id = :client_id, project_manager_id = :project_manager_id, location_address = :location_address, city = :city, state = :state, start_date = :start_date, estimated_end_date = :estimated_end_date, actual_end_date = :actual_end_date, budget_total = :budget_total, status = :status, priority = :priority, progress_percent = :progress_percent, notes = :notes, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, [
            'id' => $id, 'project_number' => $data['project_number'], 'name' => $data['name'],
            'description' => $this->nullIfEmpty($data['description'] ?? null), 'client_id' => $data['client_id'],
            'project_manager_id' => $this->nullIfEmpty($data['project_manager_id'] ?? null), 'location_address' => $this->nullIfEmpty($data['location_address'] ?? null),
            'city' => $this->nullIfEmpty($data['city'] ?? null), 'state' => $this->nullIfEmpty($data['state'] ?? null), 'start_date' => $this->nullIfEmpty($data['start_date'] ?? null),
            'estimated_end_date' => $this->nullIfEmpty($data['estimated_end_date'] ?? null), 'actual_end_date' => $this->nullIfEmpty($data['actual_end_date'] ?? null),
            'budget_total' => $data['budget_total'] ?? 0, 'status' => $data['status'] ?? 'planning',
            'priority' => $data['priority'] ?? 'medium', 'progress_percent' => $data['progress_percent'] ?? 0,
            'notes' => $this->nullIfEmpty($data['notes'] ?? null)
        ]);
    }

    public function delete($id) {
        return $this->db->query("UPDATE projects SET deleted_at = NOW() WHERE id = :id", ['id' => $id]);
    }

    public function getCount($status = null) {
        $sql = "SELECT COUNT(*) as total FROM projects WHERE deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND status = :status"; $params['status'] = $status; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }

    public function getStages($projectId) {
        $sql = "SELECT * FROM project_stages WHERE project_id = :project_id ORDER BY sort_order, planned_start";
        return $this->db->query($sql, ['project_id' => $projectId])->fetchAll();
    }

    private function nullIfEmpty($val) {
        return ($val === '' || $val === null) ? null : $val;
    }

    public function addStage($data) {
        $sql = "INSERT INTO project_stages (project_id, name, description, planned_start, planned_end, actual_start, actual_end, status, progress_percent, notes, sort_order, created_at, updated_at) VALUES (:project_id, :name, :description, :planned_start, :planned_end, :actual_start, :actual_end, :status, :progress_percent, :notes, :sort_order, NOW(), NOW())";
        return $this->db->query($sql, [
            'project_id' => $data['project_id'], 'name' => $data['name'], 'description' => $this->nullIfEmpty($data['description'] ?? null),
            'planned_start' => $this->nullIfEmpty($data['planned_start'] ?? null), 'planned_end' => $this->nullIfEmpty($data['planned_end'] ?? null),
            'actual_start' => $this->nullIfEmpty($data['actual_start'] ?? null), 'actual_end' => $this->nullIfEmpty($data['actual_end'] ?? null),
            'status' => $data['status'] ?? 'pending', 'progress_percent' => $data['progress_percent'] ?? 0,
            'notes' => $this->nullIfEmpty($data['notes'] ?? null), 'sort_order' => $data['sort_order'] ?? 0
        ]);
    }

    public function updateStage($id, $data) {
        $sql = "UPDATE project_stages SET name = :name, description = :description, planned_start = :planned_start, planned_end = :planned_end, actual_start = :actual_start, actual_end = :actual_end, status = :status, progress_percent = :progress_percent, notes = :notes, updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, [
            'id' => $id, 'name' => $data['name'], 'description' => $this->nullIfEmpty($data['description'] ?? null),
            'planned_start' => $this->nullIfEmpty($data['planned_start'] ?? null), 'planned_end' => $this->nullIfEmpty($data['planned_end'] ?? null),
            'actual_start' => $this->nullIfEmpty($data['actual_start'] ?? null), 'actual_end' => $this->nullIfEmpty($data['actual_end'] ?? null),
            'status' => $data['status'] ?? 'pending', 'progress_percent' => $data['progress_percent'] ?? 0,
            'notes' => $this->nullIfEmpty($data['notes'] ?? null)
        ]);
    }

    public function deleteStage($id) {
        return $this->db->query("DELETE FROM project_stages WHERE id = :id", ['id' => $id]);
    }

    public function getAssignments($projectId) {
        $sql = "SELECT pa.*, e.first_name, e.last_name, e.email FROM project_assignments pa JOIN employees e ON pa.employee_id = e.id WHERE pa.project_id = :project_id ORDER BY pa.assigned_date";
        return $this->db->query($sql, ['project_id' => $projectId])->fetchAll();
    }

    public function generateProjectNumber() {
        $prefix = 'PROJ-' . date('Y') . '-';
        $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(project_number, LENGTH(:prefix) + 1) AS UNSIGNED)) as max_num FROM projects WHERE project_number LIKE :prefix2", ['prefix' => $prefix, 'prefix2' => $prefix . '%']);
        $result = $stmt->fetch();
        $nextNum = ($result['max_num'] ?? 0) + 1;
        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
}
