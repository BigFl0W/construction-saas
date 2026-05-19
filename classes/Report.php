<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Report {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getDailyReports($projectId = null, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT dr.*, p.name as project_name, e.first_name, e.last_name FROM daily_reports dr JOIN projects p ON dr.project_id = p.id JOIN employees e ON dr.created_by = e.id";
        $params = [];
        $where = [];
        if ($projectId) { $where[] = "dr.project_id = :project_id"; $params['project_id'] = $projectId; }
        if ($dateFrom) { $where[] = "dr.report_date >= :date_from"; $params['date_from'] = $dateFrom; }
        if ($dateTo) { $where[] = "dr.report_date <= :date_to"; $params['date_to'] = $dateTo; }
        if ($where) { $sql .= " WHERE " . implode(' AND ', $where); }
        $sql .= " ORDER BY dr.report_date DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getDailyReportById($id) {
        $sql = "SELECT dr.*, p.name as project_name, e.first_name, e.last_name FROM daily_reports dr JOIN projects p ON dr.project_id = p.id JOIN employees e ON dr.created_by = e.id WHERE dr.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function createDailyReport($data) {
        $sql = "INSERT INTO daily_reports (project_id, report_date, weather, temperature, work_description, delays_issues, safety_notes, created_by, created_at, updated_at) VALUES (:project_id, :report_date, :weather, :temperature, :work_description, :delays_issues, :safety_notes, :created_by, NOW(), NOW())";
        return $this->db->query($sql, [
            'project_id' => $data['project_id'], 'report_date' => $data['report_date'],
            'weather' => $data['weather'] ?? null, 'temperature' => $data['temperature'] ?? null,
            'work_description' => $data['work_description'] ?? null, 'delays_issues' => $data['delays_issues'] ?? null,
            'safety_notes' => $data['safety_notes'] ?? null, 'created_by' => $data['created_by']
        ]);
    }

    public function updateDailyReport($id, $data) {
        $sql = "UPDATE daily_reports SET project_id = :project_id, report_date = :report_date, weather = :weather, temperature = :temperature, work_description = :work_description, delays_issues = :delays_issues, safety_notes = :safety_notes, updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, [
            'id' => $id, 'project_id' => $data['project_id'], 'report_date' => $data['report_date'],
            'weather' => $data['weather'] ?? null, 'temperature' => $data['temperature'] ?? null,
            'work_description' => $data['work_description'] ?? null, 'delays_issues' => $data['delays_issues'] ?? null,
            'safety_notes' => $data['safety_notes'] ?? null
        ]);
    }

    public function deleteDailyReport($id) {
        return $this->db->query("DELETE FROM daily_reports WHERE id = :id", ['id' => $id]);
    }
}
