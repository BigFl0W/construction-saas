<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Timesheet {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($employeeId = null, $projectId = null, $dateFrom = null, $dateTo = null, $status = null, $limit = null, $offset = null) {
        $sql = "SELECT t.*, e.first_name, e.last_name, p.name as project_name FROM timesheets t JOIN employees e ON t.employee_id = e.id JOIN projects p ON t.project_id = p.id";
        $params = [];
        $where = [];
        if ($employeeId) { $where[] = "t.employee_id = :employee_id"; $params['employee_id'] = $employeeId; }
        if ($projectId) { $where[] = "t.project_id = :project_id"; $params['project_id'] = $projectId; }
        if ($dateFrom) { $where[] = "t.work_date >= :date_from"; $params['date_from'] = $dateFrom; }
        if ($dateTo) { $where[] = "t.work_date <= :date_to"; $params['date_to'] = $dateTo; }
        if ($status) { $where[] = "t.status = :status"; $params['status'] = $status; }
        if ($where) { $sql .= " WHERE " . implode(' AND ', $where); }
        $sql .= " ORDER BY t.work_date DESC, t.created_at DESC";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT t.*, e.first_name, e.last_name, p.name as project_name FROM timesheets t JOIN employees e ON t.employee_id = e.id JOIN projects p ON t.project_id = p.id WHERE t.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO timesheets (employee_id, project_id, work_date, hours_worked, overtime_hours, description, status, created_at, updated_at) VALUES (:employee_id, :project_id, :work_date, :hours_worked, :overtime_hours, :description, :status, NOW(), NOW())";
        return $this->db->query($sql, [
            'employee_id' => $data['employee_id'], 'project_id' => $data['project_id'],
            'work_date' => $data['work_date'], 'hours_worked' => $data['hours_worked'],
            'overtime_hours' => $data['overtime_hours'] ?? 0, 'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'draft'
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE timesheets SET employee_id = :employee_id, project_id = :project_id, work_date = :work_date, hours_worked = :hours_worked, overtime_hours = :overtime_hours, description = :description, status = :status, updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, [
            'id' => $id, 'employee_id' => $data['employee_id'], 'project_id' => $data['project_id'],
            'work_date' => $data['work_date'], 'hours_worked' => $data['hours_worked'],
            'overtime_hours' => $data['overtime_hours'] ?? 0, 'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'draft'
        ]);
    }

    public function approve($id, $approvedBy) {
        $sql = "UPDATE timesheets SET status = 'approved', approved_by = :approved_by, approved_at = NOW(), updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, ['id' => $id, 'approved_by' => $approvedBy]);
    }

    public function reject($id, $approvedBy) {
        $sql = "UPDATE timesheets SET status = 'rejected', approved_by = :approved_by, approved_at = NOW(), updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, ['id' => $id, 'approved_by' => $approvedBy]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM timesheets WHERE id = :id", ['id' => $id]);
    }

    public function getCount($employeeId = null) {
        $sql = "SELECT COUNT(*) as total FROM timesheets";
        $params = [];
        if ($employeeId) { $sql .= " WHERE employee_id = :employee_id"; $params['employee_id'] = $employeeId; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }

    public function getHoursByEmployee($employeeId, $dateFrom, $dateTo) {
        $sql = "SELECT SUM(hours_worked) as total_hours, SUM(overtime_hours) as total_overtime FROM timesheets WHERE employee_id = :employee_id AND work_date BETWEEN :date_from AND :date_to AND status = 'approved'";
        return $this->db->query($sql, ['employee_id' => $employeeId, 'date_from' => $dateFrom, 'date_to' => $dateTo])->fetch();
    }
}
