<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Employee {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function getAll($status = null, $limit = null, $offset = null) {
        $sql = "SELECT e.*, r.name as role_name FROM employees e LEFT JOIN roles r ON e.role_id = r.id WHERE e.deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND e.status = :status"; $params['status'] = $status; }
        $sql .= " ORDER BY e.last_name, e.first_name";
        if ($limit) { $sql .= " LIMIT :limit OFFSET :offset"; $params['limit'] = (int)$limit; $params['offset'] = (int)$offset; }
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT e.*, r.name as role_name, s.first_name as supervisor_first, s.last_name as supervisor_last FROM employees e LEFT JOIN roles r ON e.role_id = r.id LEFT JOIN employees s ON e.supervisor_id = s.id WHERE e.id = :id AND e.deleted_at IS NULL";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function create($data) {
        $uuid = $this->functions->generateUUID();
        $sql = "INSERT INTO employees (uuid, first_name, last_name, email, phone, mobile, address_line1, address_line2, city, state, postal_code, country, emergency_contact_name, emergency_contact_phone, hire_date, employee_type, role_id, supervisor_id, hourly_rate, salary, payment_frequency, tax_id, bank_account_info, status, notes, created_at, updated_at) VALUES (:uuid, :first_name, :last_name, :email, :phone, :mobile, :address_line1, :address_line2, :city, :state, :postal_code, :country, :emergency_contact_name, :emergency_contact_phone, :hire_date, :employee_type, :role_id, :supervisor_id, :hourly_rate, :salary, :payment_frequency, :tax_id, :bank_account_info, :status, :notes, NOW(), NOW())";
        $this->db->query($sql, [
            'uuid' => $uuid, 'first_name' => $data['first_name'], 'last_name' => $data['last_name'],
            'email' => $data['email'], 'phone' => $data['phone'] ?? null, 'mobile' => $data['mobile'] ?? null,
            'address_line1' => $data['address_line1'] ?? null, 'address_line2' => $data['address_line2'] ?? null,
            'city' => $data['city'] ?? null, 'state' => $data['state'] ?? null, 'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? 'USA', 'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null, 'hire_date' => $data['hire_date'],
            'employee_type' => $data['employee_type'] ?? 'employee', 'role_id' => $data['role_id'] ?? null,
            'supervisor_id' => $data['supervisor_id'] ?? null, 'hourly_rate' => $data['hourly_rate'] ?? 0,
            'salary' => $data['salary'] ?? 0, 'payment_frequency' => $data['payment_frequency'] ?? 'hourly',
            'tax_id' => $data['tax_id'] ?? null, 'bank_account_info' => $data['bank_account_info'] ?? null,
            'status' => $data['status'] ?? 'active', 'notes' => $data['notes'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE employees SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, mobile = :mobile, address_line1 = :address_line1, address_line2 = :address_line2, city = :city, state = :state, postal_code = :postal_code, country = :country, emergency_contact_name = :emergency_contact_name, emergency_contact_phone = :emergency_contact_phone, hire_date = :hire_date, employee_type = :employee_type, role_id = :role_id, supervisor_id = :supervisor_id, hourly_rate = :hourly_rate, salary = :salary, payment_frequency = :payment_frequency, tax_id = :tax_id, bank_account_info = :bank_account_info, status = :status, notes = :notes, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->db->query($sql, [
            'id' => $id, 'first_name' => $data['first_name'], 'last_name' => $data['last_name'],
            'email' => $data['email'], 'phone' => $data['phone'] ?? null, 'mobile' => $data['mobile'] ?? null,
            'address_line1' => $data['address_line1'] ?? null, 'address_line2' => $data['address_line2'] ?? null,
            'city' => $data['city'] ?? null, 'state' => $data['state'] ?? null, 'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? 'USA', 'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null, 'hire_date' => $data['hire_date'],
            'employee_type' => $data['employee_type'] ?? 'employee', 'role_id' => $data['role_id'] ?? null,
            'supervisor_id' => $data['supervisor_id'] ?? null, 'hourly_rate' => $data['hourly_rate'] ?? 0,
            'salary' => $data['salary'] ?? 0, 'payment_frequency' => $data['payment_frequency'] ?? 'hourly',
            'tax_id' => $data['tax_id'] ?? null, 'bank_account_info' => $data['bank_account_info'] ?? null,
            'status' => $data['status'] ?? 'active', 'notes' => $data['notes'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->db->query("UPDATE employees SET deleted_at = NOW() WHERE id = :id", ['id' => $id]);
    }

    public function getCount($status = null) {
        $sql = "SELECT COUNT(*) as total FROM employees WHERE deleted_at IS NULL";
        $params = [];
        if ($status) { $sql .= " AND status = :status"; $params['status'] = $status; }
        return $this->db->query($sql, $params)->fetch()['total'];
    }

    public function getAllRoles() {
        return $this->db->query("SELECT * FROM roles ORDER BY name")->fetchAll();
    }
}
