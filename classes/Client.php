<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Client {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null, $search = null, $limit = null, $offset = null) {
        $sql = "SELECT c.*, (SELECT COUNT(*) FROM projects WHERE client_id = c.id AND deleted_at IS NULL) as project_count FROM clients c WHERE c.deleted_at IS NULL";
        $params = [];
        if ($status) {
            $sql .= " AND c.status = :status";
            $params['status'] = $status;
        }
        if ($search) {
            $sql .= " AND (c.company_name LIKE :search OR c.contact_person LIKE :search2 OR c.email LIKE :search3)";
            $params['search'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        }
        $sql .= " ORDER BY c.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = (int)$limit;
            $params['offset'] = (int)$offset;
        }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM clients WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $uuid = $this->functions->generateUUID();
        $sql = "INSERT INTO clients (uuid, client_type, company_name, contact_person, email, phone, mobile, tax_id, address_line1, address_line2, city, state, postal_code, country, website, notes, status, created_at, updated_at) VALUES (:uuid, :client_type, :company_name, :contact_person, :email, :phone, :mobile, :tax_id, :address_line1, :address_line2, :city, :state, :postal_code, :country, :website, :notes, :status, NOW(), NOW())";
        $this->db->query($sql, [
            'uuid' => $uuid, 'client_type' => $data['client_type'], 'company_name' => $data['company_name'] ?? null,
            'contact_person' => $data['contact_person'] ?? null, 'email' => $data['email'], 'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null, 'tax_id' => $data['tax_id'] ?? null, 'address_line1' => $data['address_line1'] ?? null,
            'address_line2' => $data['address_line2'] ?? null, 'city' => $data['city'] ?? null, 'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? null, 'country' => $data['country'] ?? 'USA', 'website' => $data['website'] ?? null,
            'notes' => $data['notes'] ?? null, 'status' => $data['status'] ?? 'lead'
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE clients SET client_type = :client_type, company_name = :company_name, contact_person = :contact_person, email = :email, phone = :phone, mobile = :mobile, tax_id = :tax_id, address_line1 = :address_line1, address_line2 = :address_line2, city = :city, state = :state, postal_code = :postal_code, country = :country, website = :website, notes = :notes, status = :status, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, [
            'id' => $id, 'client_type' => $data['client_type'], 'company_name' => $data['company_name'] ?? null,
            'contact_person' => $data['contact_person'] ?? null, 'email' => $data['email'], 'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null, 'tax_id' => $data['tax_id'] ?? null, 'address_line1' => $data['address_line1'] ?? null,
            'address_line2' => $data['address_line2'] ?? null, 'city' => $data['city'] ?? null, 'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? null, 'country' => $data['country'] ?? 'USA', 'website' => $data['website'] ?? null,
            'notes' => $data['notes'] ?? null, 'status' => $data['status'] ?? 'lead'
        ]);
    }

    public function delete($id) {
        $sql = "UPDATE clients SET deleted_at = NOW() WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }

    public function getCount($status = null, $search = null) {
        $sql = "SELECT COUNT(*) as total FROM clients WHERE deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND status = :status"; $params['status'] = $status; }
        if ($search) { $sql .= " AND (company_name LIKE :search OR contact_person LIKE :search2 OR email LIKE :search3)"; $params['search'] = "%$search%"; $params['search2'] = "%$search%"; $params['search3'] = "%$search%"; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }
}
