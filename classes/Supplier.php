<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Supplier {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null) {
        $sql = "SELECT s.*, (SELECT COUNT(*) FROM materials WHERE supplier_id = s.id AND deleted_at IS NULL) as material_count FROM suppliers s WHERE s.deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND s.status = :status"; $params['status'] = $status; }
        $sql .= " ORDER BY s.name";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM suppliers WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $uuid = $this->functions->generateUUID();
        $sql = "INSERT INTO suppliers (uuid, name, contact_person, email, phone, address, tax_id, payment_terms, status, notes, created_at, updated_at) VALUES (:uuid, :name, :contact_person, :email, :phone, :address, :tax_id, :payment_terms, :status, :notes, NOW(), NOW())";
        $this->db->query($sql, [
            'uuid' => $uuid, 'name' => $data['name'], 'contact_person' => $data['contact_person'] ?? null,
            'email' => $data['email'] ?? null, 'phone' => $data['phone'] ?? null, 'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null, 'payment_terms' => $data['payment_terms'] ?? null,
            'status' => $data['status'] ?? 'active', 'notes' => $data['notes'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE suppliers SET name = :name, contact_person = :contact_person, email = :email, phone = :phone, address = :address, tax_id = :tax_id, payment_terms = :payment_terms, status = :status, notes = :notes, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, [
            'id' => $id, 'name' => $data['name'], 'contact_person' => $data['contact_person'] ?? null,
            'email' => $data['email'] ?? null, 'phone' => $data['phone'] ?? null, 'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null, 'payment_terms' => $data['payment_terms'] ?? null,
            'status' => $data['status'] ?? 'active', 'notes' => $data['notes'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("UPDATE suppliers SET deleted_at = NOW() WHERE id = :id", ['id' => $id]);
    }
}
