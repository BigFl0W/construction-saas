<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Invoice {
    private $db;
    private $functions;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
        $this->ensureSchema();
    }

    public function getAll($status = null, $limit = null, $offset = null) {
        $sql = "SELECT
                    i.*,
                    p.name AS project_name,
                    c.company_name AS client_company_name,
                    c.contact_person AS client_contact_person,
                    c.email AS client_email,
                    COALESCE(NULLIF(c.company_name, ''), NULLIF(c.contact_person, ''), c.email) AS client_name
                FROM invoices i
                LEFT JOIN projects p ON i.project_id = p.id
                LEFT JOIN clients c ON i.client_id = c.id";
        $params = [];

        if ($status) {
            $sql .= " WHERE i.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY i.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $params['limit'] = (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
                $params['offset'] = (int) $offset;
            }
        }

        $rows = $this->db->query($sql, $params)->fetchAll();
        foreach ($rows as &$row) {
            $row['public_token'] = $this->ensurePublicTokenForRow($row);
        }

        return $rows;
    }

    public function getById($id) {
        $sql = "SELECT
                    i.*,
                    p.name AS project_name,
                    c.company_name AS client_company_name,
                    c.contact_person AS client_contact_person,
                    c.email AS client_email,
                    c.phone AS client_phone,
                    c.mobile AS client_mobile,
                    c.address_line1,
                    c.address_line2,
                    c.city,
                    c.state,
                    c.postal_code,
                    c.country,
                    COALESCE(NULLIF(c.company_name, ''), NULLIF(c.contact_person, ''), c.email) AS client_name
                FROM invoices i
                LEFT JOIN projects p ON i.project_id = p.id
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE i.id = :id";
        $row = $this->db->query($sql, ['id' => $id])->fetch();

        if ($row) {
            $row['public_token'] = $this->ensurePublicTokenForRow($row);
        }

        return $row;
    }

    public function getByToken($token) {
        $sql = "SELECT
                    i.*,
                    p.name AS project_name,
                    c.company_name AS client_company_name,
                    c.contact_person AS client_contact_person,
                    c.email AS client_email,
                    c.phone AS client_phone,
                    c.mobile AS client_mobile,
                    c.address_line1,
                    c.address_line2,
                    c.city,
                    c.state,
                    c.postal_code,
                    c.country,
                    COALESCE(NULLIF(c.company_name, ''), NULLIF(c.contact_person, ''), c.email) AS client_name
                FROM invoices i
                LEFT JOIN projects p ON i.project_id = p.id
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE i.public_token = :token
                LIMIT 1";

        return $this->db->query($sql, ['token' => $token])->fetch();
    }

    public function create($data) {
        $token = $this->functions->generateUUID();
        $sql = "INSERT INTO invoices (
                    invoice_number,
                    public_token,
                    project_id,
                    client_id,
                    recipient_name,
                    recipient_email,
                    invoice_date,
                    due_date,
                    subtotal,
                    tax,
                    total,
                    amount_paid,
                    status,
                    notes,
                    email_subject,
                    email_message,
                    created_by,
                    created_at,
                    updated_at
                ) VALUES (
                    :invoice_number,
                    :public_token,
                    :project_id,
                    :client_id,
                    :recipient_name,
                    :recipient_email,
                    :invoice_date,
                    :due_date,
                    :subtotal,
                    :tax,
                    :total,
                    0,
                    :status,
                    :notes,
                    :email_subject,
                    :email_message,
                    :created_by,
                    NOW(),
                    NOW()
                )";

        $this->db->query($sql, [
            'invoice_number' => $data['invoice_number'],
            'public_token' => $token,
            'project_id' => $data['project_id'] ?: null,
            'client_id' => $data['client_id'],
            'recipient_name' => $data['recipient_name'] ?: null,
            'recipient_email' => $data['recipient_email'] ?: null,
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $data['subtotal'] ?? 0,
            'tax' => $data['tax'] ?? 0,
            'total' => $data['total'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
            'email_subject' => $data['email_subject'] ?? null,
            'email_message' => $data['email_message'] ?? null,
            'created_by' => $data['created_by'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE invoices SET
                    project_id = :project_id,
                    client_id = :client_id,
                    recipient_name = :recipient_name,
                    recipient_email = :recipient_email,
                    invoice_date = :invoice_date,
                    due_date = :due_date,
                    subtotal = :subtotal,
                    tax = :tax,
                    total = :total,
                    amount_paid = :amount_paid,
                    status = :status,
                    notes = :notes,
                    email_subject = :email_subject,
                    email_message = :email_message,
                    updated_at = NOW()
                WHERE id = :id";

        return $this->db->query($sql, [
            'id' => $id,
            'project_id' => $data['project_id'] ?: null,
            'client_id' => $data['client_id'],
            'recipient_name' => $data['recipient_name'] ?: null,
            'recipient_email' => $data['recipient_email'] ?: null,
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $data['subtotal'] ?? 0,
            'tax' => $data['tax'] ?? 0,
            'total' => $data['total'] ?? 0,
            'amount_paid' => $data['amount_paid'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
            'email_subject' => $data['email_subject'] ?? null,
            'email_message' => $data['email_message'] ?? null,
        ]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM invoice_items WHERE invoice_id = :id", ['id' => $id]);
        $this->db->query("DELETE FROM payments_received WHERE invoice_id = :id", ['id' => $id]);
        return $this->db->query("DELETE FROM invoices WHERE id = :id", ['id' => $id]);
    }

    public function getCount($status = null) {
        $sql = "SELECT COUNT(*) AS total FROM invoices";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }

        return (int) ($this->db->query($sql, $params)->fetch()['total'] ?? 0);
    }

    public function getStats() {
        $sql = "SELECT
                    COUNT(*) AS total_invoices,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS draft_invoices,
                    SUM(CASE WHEN status IN ('sent', 'partial', 'overdue') THEN 1 ELSE 0 END) AS outstanding_invoices,
                    SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) AS overdue_invoices,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS paid_invoices,
                    COALESCE(SUM(total), 0) AS gross_total,
                    COALESCE(SUM(amount_paid), 0) AS amount_paid_total,
                    COALESCE(SUM(total - amount_paid), 0) AS balance_total
                FROM invoices";
        return $this->db->query($sql)->fetch();
    }

    public function generateInvoiceNumber() {
        $prefix = 'INV-' . date('Y') . '-';
        $stmt = $this->db->query(
            "SELECT MAX(CAST(SUBSTRING(invoice_number, LENGTH(:prefix) + 1) AS UNSIGNED)) AS max_num
             FROM invoices
             WHERE invoice_number LIKE :prefix2",
            ['prefix' => $prefix, 'prefix2' => $prefix . '%']
        );
        $result = $stmt->fetch();

        return $prefix . str_pad(((int) ($result['max_num'] ?? 0)) + 1, 4, '0', STR_PAD_LEFT);
    }

    public function addPayment($data) {
        $sql = "INSERT INTO payments_received (
                    invoice_id,
                    payment_date,
                    amount,
                    payment_method,
                    reference_number,
                    notes,
                    created_at,
                    updated_at
                ) VALUES (
                    :invoice_id,
                    :payment_date,
                    :amount,
                    :payment_method,
                    :reference_number,
                    :notes,
                    NOW(),
                    NOW()
                )";

        $result = $this->db->query($sql, [
            'invoice_id' => $data['invoice_id'],
            'payment_date' => $data['payment_date'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->updateInvoicePaidAmount($data['invoice_id']);
        return $result;
    }

    public function updateInvoicePaidAmount($invoiceId) {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) AS total_paid FROM payments_received WHERE invoice_id = :id",
            ['id' => $invoiceId]
        );
        $totalPaid = (float) ($stmt->fetch()['total_paid'] ?? 0);

        $invoice = $this->db->query(
            "SELECT total, sent_at, due_date FROM invoices WHERE id = :id",
            ['id' => $invoiceId]
        )->fetch();

        $status = empty($invoice['sent_at']) ? 'draft' : 'sent';
        if ($totalPaid >= (float) ($invoice['total'] ?? 0) && (float) ($invoice['total'] ?? 0) > 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        } elseif (!empty($invoice['due_date']) && strtotime($invoice['due_date']) < strtotime(date('Y-m-d')) && !empty($invoice['sent_at'])) {
            $status = 'overdue';
        }

        $this->db->query(
            "UPDATE invoices SET amount_paid = :paid, status = :status, updated_at = NOW() WHERE id = :id",
            ['paid' => $totalPaid, 'status' => $status, 'id' => $invoiceId]
        );
    }

    public function getPayments($invoiceId) {
        return $this->db->query(
            "SELECT * FROM payments_received WHERE invoice_id = :id ORDER BY payment_date DESC, id DESC",
            ['id' => $invoiceId]
        )->fetchAll();
    }

    public function getItems($invoiceId) {
        return $this->db->query(
            "SELECT * FROM invoice_items WHERE invoice_id = :id ORDER BY id ASC",
            ['id' => $invoiceId]
        )->fetchAll();
    }

    public function syncItems($invoiceId, array $items) {
        $this->db->query("DELETE FROM invoice_items WHERE invoice_id = :id", ['id' => $invoiceId]);
        foreach ($items as $item) {
            if (trim((string) ($item['description'] ?? '')) === '') {
                continue;
            }
            $this->addItem([
                'invoice_id' => $invoiceId,
                'description' => $item['description'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
            ]);
        }
    }

    public function addItem($data) {
        $quantity = (float) ($data['quantity'] ?? 1);
        $unitPrice = (float) ($data['unit_price'] ?? 0);
        $lineTotal = $quantity * $unitPrice;

        $sql = "INSERT INTO invoice_items (
                    invoice_id,
                    description,
                    quantity,
                    unit_price,
                    line_total
                ) VALUES (
                    :invoice_id,
                    :description,
                    :quantity,
                    :unit_price,
                    :line_total
                )";

        return $this->db->query($sql, [
            'invoice_id' => $data['invoice_id'],
            'description' => $data['description'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ]);
    }

    public function removeItem($id) {
        return $this->db->query("DELETE FROM invoice_items WHERE id = :id", ['id' => $id]);
    }

    public function markAsSent($invoiceId, $recipientEmail, $subject, $message) {
        $this->db->query(
            "UPDATE invoices
             SET recipient_email = :recipient_email,
                 email_subject = :email_subject,
                 email_message = :email_message,
                 sent_at = COALESCE(sent_at, NOW()),
                 last_emailed_at = NOW(),
                 status = CASE
                     WHEN amount_paid >= total AND total > 0 THEN 'paid'
                     WHEN amount_paid > 0 THEN 'partial'
                     WHEN due_date < CURDATE() THEN 'overdue'
                     ELSE 'sent'
                 END,
                 updated_at = NOW()
             WHERE id = :id",
            [
                'id' => $invoiceId,
                'recipient_email' => $recipientEmail,
                'email_subject' => $subject,
                'email_message' => $message,
            ]
        );
    }

    public function markViewedByToken($token) {
        $this->db->query(
            "UPDATE invoices SET viewed_at = COALESCE(viewed_at, NOW()), updated_at = NOW() WHERE public_token = :token",
            ['token' => $token]
        );
    }

    private function ensureSchema() {
        $this->ensureColumn('invoices', 'public_token', "ALTER TABLE invoices ADD COLUMN public_token CHAR(36) NULL AFTER invoice_number");
        $this->ensureColumn('invoices', 'recipient_name', "ALTER TABLE invoices ADD COLUMN recipient_name VARCHAR(255) NULL AFTER client_id");
        $this->ensureColumn('invoices', 'recipient_email', "ALTER TABLE invoices ADD COLUMN recipient_email VARCHAR(255) NULL AFTER recipient_name");
        $this->ensureColumn('invoices', 'email_subject', "ALTER TABLE invoices ADD COLUMN email_subject VARCHAR(255) NULL AFTER notes");
        $this->ensureColumn('invoices', 'email_message', "ALTER TABLE invoices ADD COLUMN email_message TEXT NULL AFTER email_subject");
        $this->ensureColumn('invoices', 'sent_at', "ALTER TABLE invoices ADD COLUMN sent_at DATETIME NULL AFTER email_message");
        $this->ensureColumn('invoices', 'last_emailed_at', "ALTER TABLE invoices ADD COLUMN last_emailed_at DATETIME NULL AFTER sent_at");
        $this->ensureColumn('invoices', 'viewed_at', "ALTER TABLE invoices ADD COLUMN viewed_at DATETIME NULL AFTER last_emailed_at");

        if (!$this->indexExists('invoices', 'idx_invoices_public_token')) {
            $this->db->query("ALTER TABLE invoices ADD INDEX idx_invoices_public_token (public_token)");
        }

        $this->db->query("ALTER TABLE invoices MODIFY project_id INT(11) NULL");
        $this->db->query("ALTER TABLE invoice_items MODIFY line_total DECIMAL(15,2) NULL");

        $this->backfillPublicTokens();
    }

    private function ensureColumn($table, $column, $sql) {
        if ($this->columnExists($table, $column)) {
            return;
        }
        $this->db->query($sql);
    }

    private function columnExists($table, $column) {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total
             FROM information_schema.columns
             WHERE table_schema = DATABASE()
             AND table_name = :table_name
             AND column_name = :column_name",
            ['table_name' => $table, 'column_name' => $column]
        );
        return ((int) ($stmt->fetch()['total'] ?? 0)) > 0;
    }

    private function indexExists($table, $index) {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
             AND table_name = :table_name
             AND index_name = :index_name",
            ['table_name' => $table, 'index_name' => $index]
        );
        return ((int) ($stmt->fetch()['total'] ?? 0)) > 0;
    }

    private function backfillPublicTokens() {
        $rows = $this->db->query("SELECT id FROM invoices WHERE public_token IS NULL OR public_token = ''")->fetchAll();
        foreach ($rows as $row) {
            $this->db->query(
                "UPDATE invoices SET public_token = :token, updated_at = NOW() WHERE id = :id",
                ['token' => $this->functions->generateUUID(), 'id' => $row['id']]
            );
        }
    }

    private function ensurePublicTokenForRow(array $row) {
        if (!empty($row['public_token'])) {
            return $row['public_token'];
        }

        $token = $this->functions->generateUUID();
        $this->db->query(
            "UPDATE invoices SET public_token = :token, updated_at = NOW() WHERE id = :id",
            ['token' => $token, 'id' => $row['id']]
        );
        return $token;
    }
}
