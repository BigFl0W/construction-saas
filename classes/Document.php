<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Document {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($projectId = null, $clientId = null, $employeeId = null, $categoryId = null) {
        $sql = "SELECT d.*, dc.name as category_name, p.name as project_name, cl.company_name as client_name, CONCAT(e.first_name, ' ', e.last_name) as employee_name FROM documents d LEFT JOIN document_categories dc ON d.category_id = dc.id LEFT JOIN projects p ON d.project_id = p.id LEFT JOIN clients cl ON d.client_id = cl.id LEFT JOIN employees e ON d.employee_id = e.id WHERE d.deleted_at IS NULL";
        $params = [];
        $where = [];
        if ($projectId) { $where[] = "d.project_id = :project_id"; $params['project_id'] = $projectId; }
        if ($clientId) { $where[] = "d.client_id = :client_id"; $params['client_id'] = $clientId; }
        if ($employeeId) { $where[] = "d.employee_id = :employee_id"; $params['employee_id'] = $employeeId; }
        if ($categoryId) { $where[] = "d.category_id = :category_id"; $params['category_id'] = $categoryId; }
        if ($where) { $sql .= " AND " . implode(' AND ', $where); }
        $sql .= " ORDER BY d.created_at DESC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT d.*, dc.name as category_name FROM documents d LEFT JOIN document_categories dc ON d.category_id = dc.id WHERE d.id = :id AND d.deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data, $file) {
        $uuid = $this->functions->generateUUID();
        $uploadDir = dirname(__DIR__) . '/uploads/documents/';
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $file['name']);
        $filepath = 'uploads/documents/' . $filename;
        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
        $sql = "INSERT INTO documents (uuid, category_id, project_id, client_id, employee_id, filename, original_filename, file_path, file_size, mime_type, description, version, created_by, created_at, updated_at) VALUES (:uuid, :category_id, :project_id, :client_id, :employee_id, :filename, :original_filename, :file_path, :file_size, :mime_type, :description, :version, :created_by, NOW(), NOW())";
        return $this->db->query($sql, [
            'uuid' => $uuid, 'category_id' => $data['category_id'] ?? null, 'project_id' => $data['project_id'] ?? null,
            'client_id' => $data['client_id'] ?? null, 'employee_id' => $data['employee_id'] ?? null,
            'filename' => $filename, 'original_filename' => $file['name'], 'file_path' => $filepath,
            'file_size' => $file['size'], 'mime_type' => $file['type'] ?? null,
            'description' => $data['description'] ?? null, 'version' => $data['version'] ?? '1.0',
            'created_by' => $data['created_by']
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE documents SET category_id = :category_id, project_id = :project_id, client_id = :client_id, employee_id = :employee_id, description = :description, version = :version, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, [
            'id' => $id,
            'category_id' => $data['category_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'client_id' => $data['client_id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'description' => $data['description'] ?? null,
            'version' => $data['version'] ?? '1.0'
        ]);
    }

    public function delete($id) {
        $doc = $this->getById($id);
        if ($doc) {
            $filePath = dirname(__DIR__) . '/' . $doc['file_path'];
            if (file_exists($filePath)) { unlink($filePath); }
        }
        return $this->db->query("UPDATE documents SET deleted_at = NOW() WHERE id = :id", ['id' => $id]);
    }

    public function getCategories() {
        return $this->db->query("SELECT * FROM document_categories ORDER BY name")->fetchAll();
    }

    public function addCategory($name, $description = null) {
        return $this->db->query("INSERT INTO document_categories (name, description) VALUES (:name, :description)", ['name' => $name, 'description' => $description]);
    }
}
