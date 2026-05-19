<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Communication {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($projectId = null, $clientId = null, $type = null, $limit = null, $offset = null) {
        $sql = "SELECT c.*, p.name as project_name, cl.company_name as client_name FROM communications c LEFT JOIN projects p ON c.project_id = p.id LEFT JOIN clients cl ON c.client_id = cl.id";
        $params = [];
        $where = [];
        if ($projectId) { $where[] = "c.project_id = :project_id"; $params['project_id'] = $projectId; }
        if ($clientId) { $where[] = "c.client_id = :client_id"; $params['client_id'] = $clientId; }
        if ($type) { $where[] = "c.type = :type"; $params['type'] = $type; }
        if ($where) { $sql .= " WHERE " . implode(' AND ', $where); }
        $sql .= " ORDER BY c.communication_date DESC";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT c.*, p.name as project_name, cl.company_name as client_name FROM communications c LEFT JOIN projects p ON c.project_id = p.id LEFT JOIN clients cl ON c.client_id = cl.id WHERE c.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $uuid = $this->functions->generateUUID();
        $sql = "INSERT INTO communications (uuid, project_id, client_id, employee_id, direction, type, subject, content, communication_date, attachments, created_by, created_at, updated_at) VALUES (:uuid, :project_id, :client_id, :employee_id, :direction, :type, :subject, :content, :communication_date, :attachments, :created_by, NOW(), NOW())";
        return $this->db->query($sql, [
            'uuid' => $uuid, 'project_id' => $data['project_id'] ?? null, 'client_id' => $data['client_id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null, 'direction' => $data['direction'] ?? 'outbound',
            'type' => $data['type'], 'subject' => $data['subject'] ?? null, 'content' => $data['content'] ?? null,
            'communication_date' => $data['communication_date'], 'attachments' => $data['attachments'] ?? null,
            'created_by' => $data['created_by'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE communications SET project_id = :project_id, client_id = :client_id, employee_id = :employee_id, direction = :direction, type = :type, subject = :subject, content = :content, communication_date = :communication_date, attachments = :attachments, updated_at = NOW() WHERE id = :id";
        return $this->db->query($sql, [
            'id' => $id,
            'project_id' => $data['project_id'] ?? null,
            'client_id' => $data['client_id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'direction' => $data['direction'] ?? 'outbound',
            'type' => $data['type'],
            'subject' => $data['subject'] ?? null,
            'content' => $data['content'] ?? null,
            'communication_date' => $data['communication_date'],
            'attachments' => $data['attachments'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM communications WHERE id = :id", ['id' => $id]);
    }

    public function getCount($projectId = null) {
        $sql = "SELECT COUNT(*) as total FROM communications";
        $params = [];
        if ($projectId) { $sql .= " WHERE project_id = :project_id"; $params['project_id'] = $projectId; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }
}
