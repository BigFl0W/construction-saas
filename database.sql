-- ======================================================================
-- COMPLETE DATABASE FOR A CONSTRUCTION COMPANY WITH INTEGRATED BLOG
-- ======================================================================
-- Run this script to create a single, unified database.
-- It includes:
--   - Core: clients, employees, projects, equipment, materials
--   - Financials: invoices, expenses, purchase orders
--   - Documents and daily reports
--   - Blog: posts, categories, tags, comments, analytics
-- All relationships are enforced with foreign keys.
-- ======================================================================

-- Drop database if exists (comment out for production)
DROP DATABASE IF EXISTS construction_db;
CREATE DATABASE construction_db;
USE construction_db;

-- ======================================================================
-- 1. CORE TABLES: Clients, Employees, Roles
-- ======================================================================

CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    client_type ENUM('company', 'individual') NOT NULL,
    company_name VARCHAR(255) NULL,
    contact_person VARCHAR(255) NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    mobile VARCHAR(50),
    tax_id VARCHAR(50),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'USA',
    website VARCHAR(255),
    notes TEXT,
    status ENUM('active', 'inactive', 'lead') DEFAULT 'lead',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_clients_email (email),
    INDEX idx_clients_status (status),
    INDEX idx_clients_company (company_name),
    FULLTEXT INDEX idx_clients_search (company_name, contact_person, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    mobile VARCHAR(50),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'USA',
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(50),
    hire_date DATE,
    termination_date DATE NULL,
    employee_type ENUM('employee', 'subcontractor', 'temporary') DEFAULT 'employee',
    role_id INT,
    supervisor_id INT NULL,
    hourly_rate DECIMAL(10,2),
    salary DECIMAL(10,2),
    payment_frequency ENUM('hourly', 'weekly', 'biweekly', 'monthly') DEFAULT 'hourly',
    tax_id VARCHAR(50),
    bank_account_info TEXT,
    status ENUM('active', 'inactive', 'on_leave', 'terminated') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL,
    FOREIGN KEY (supervisor_id) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_employees_email (email),
    INDEX idx_employees_status (status),
    INDEX idx_employees_supervisor (supervisor_id),
    FULLTEXT INDEX idx_employees_name (first_name, last_name, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE employee_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    document_type VARCHAR(100),
    file_path VARCHAR(500) NOT NULL,
    issue_date DATE,
    expiry_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_employee_docs_employee (employee_id),
    INDEX idx_employee_docs_expiry (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 2. PROJECT MANAGEMENT
-- ======================================================================

CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    project_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    client_id INT NOT NULL,
    project_manager_id INT NULL,
    location_address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    start_date DATE,
    estimated_end_date DATE,
    actual_end_date DATE NULL,
    budget_total DECIMAL(15,2),
    budget_used DECIMAL(15,2) DEFAULT 0,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    progress_percent INT DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (project_manager_id) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_projects_client (client_id),
    INDEX idx_projects_manager (project_manager_id),
    INDEX idx_projects_status (status),
    INDEX idx_projects_dates (start_date, estimated_end_date),
    FULLTEXT INDEX idx_projects_search (name, description, project_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_stages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    planned_start DATE,
    planned_end DATE,
    actual_start DATE NULL,
    actual_end DATE NULL,
    status ENUM('pending', 'in_progress', 'completed', 'delayed') DEFAULT 'pending',
    progress_percent INT DEFAULT 0,
    notes TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_stages_project (project_id),
    INDEX idx_stages_dates (planned_start, planned_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    employee_id INT NOT NULL,
    role_on_project VARCHAR(100),
    assigned_date DATE NOT NULL,
    end_date DATE NULL,
    hours_per_week DECIMAL(5,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_employee (project_id, employee_id, assigned_date),
    INDEX idx_assignments_employee (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 3. TIMESHEETS AND LABOR TRACKING
-- ======================================================================

CREATE TABLE timesheets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    project_id INT NOT NULL,
    work_date DATE NOT NULL,
    hours_worked DECIMAL(5,2) NOT NULL,
    overtime_hours DECIMAL(5,2) DEFAULT 0,
    description TEXT,
    status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_timesheets_employee (employee_id),
    INDEX idx_timesheets_project (project_id),
    INDEX idx_timesheets_date (work_date),
    UNIQUE KEY unique_timesheet (employee_id, project_id, work_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 4. EQUIPMENT MANAGEMENT
-- ======================================================================

CREATE TABLE equipment_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE equipment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    category_id INT NULL,
    name VARCHAR(255) NOT NULL,
    model VARCHAR(100),
    serial_number VARCHAR(100) UNIQUE,
    purchase_date DATE,
    purchase_price DECIMAL(10,2),
    current_value DECIMAL(10,2),
    status ENUM('available', 'in_use', 'maintenance', 'out_of_service', 'retired') DEFAULT 'available',
    location VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES equipment_categories(id) ON DELETE SET NULL,
    INDEX idx_equipment_status (status),
    INDEX idx_equipment_serial (serial_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE equipment_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipment_id INT NOT NULL,
    project_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    returned_date DATE NULL,
    condition_on_return VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_assignments_equipment (equipment_id),
    INDEX idx_assignments_project (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE maintenance_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipment_id INT NOT NULL,
    maintenance_date DATE NOT NULL,
    description TEXT NOT NULL,
    cost DECIMAL(10,2),
    performed_by VARCHAR(255),
    next_maintenance_date DATE NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    INDEX idx_maintenance_equipment (equipment_id),
    INDEX idx_maintenance_next (next_maintenance_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 5. MATERIALS AND SUPPLIERS
-- ======================================================================

CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    tax_id VARCHAR(50),
    payment_terms VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_suppliers_name (name),
    INDEX idx_suppliers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    unit VARCHAR(50) NOT NULL,
    current_stock DECIMAL(15,2) DEFAULT 0,
    reorder_level DECIMAL(15,2) DEFAULT 0,
    unit_cost DECIMAL(10,2),
    supplier_id INT NULL,
    location_stored VARCHAR(255),
    status ENUM('active', 'discontinued') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    INDEX idx_materials_name (name),
    INDEX idx_materials_supplier (supplier_id),
    INDEX idx_materials_stock (current_stock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_number VARCHAR(50) UNIQUE NOT NULL,
    supplier_id INT NOT NULL,
    project_id INT NULL,
    order_date DATE NOT NULL,
    expected_delivery DATE NULL,
    delivery_date DATE NULL,
    status ENUM('draft', 'sent', 'confirmed', 'partial', 'received', 'cancelled') DEFAULT 'draft',
    subtotal DECIMAL(15,2),
    tax DECIMAL(15,2),
    total DECIMAL(15,2),
    payment_terms VARCHAR(100),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_po_supplier (supplier_id),
    INDEX idx_po_project (project_id),
    INDEX idx_po_status (status),
    INDEX idx_po_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_id INT NOT NULL,
    material_id INT NOT NULL,
    quantity DECIMAL(15,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    received_quantity DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE RESTRICT,
    INDEX idx_poi_order (purchase_order_id),
    INDEX idx_poi_material (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE material_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    material_id INT NOT NULL,
    quantity_used DECIMAL(15,2) NOT NULL,
    usage_date DATE NOT NULL,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_usage_project (project_id),
    INDEX idx_usage_material (material_id),
    INDEX idx_usage_date (usage_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 6. FINANCIALS: Invoices, Expenses, Payments
-- ======================================================================

CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    project_id INT NOT NULL,
    client_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(15,2),
    tax DECIMAL(15,2),
    total DECIMAL(15,2),
    amount_paid DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_invoices_project (project_id),
    INDEX idx_invoices_client (client_id),
    INDEX idx_invoices_status (status),
    INDEX idx_invoices_due (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    description TEXT NOT NULL,
    quantity DECIMAL(10,2),
    unit_price DECIMAL(10,2),
    line_total DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice_items_invoice (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments_received (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'check', 'bank_transfer', 'credit_card') NOT NULL,
    reference_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_payments_invoice (invoice_id),
    INDEX idx_payments_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NULL,
    expense_date DATE NOT NULL,
    category VARCHAR(100),
    description TEXT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'check', 'bank_transfer', 'credit_card'),
    vendor VARCHAR(255),
    receipt_path VARCHAR(500),
    approved_by INT NULL,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_expenses_project (project_id),
    INDEX idx_expenses_date (expense_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_budgets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    allocated_amount DECIMAL(15,2) NOT NULL,
    spent_amount DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_category (project_id, category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 7. DOCUMENTS AND COMMUNICATIONS
-- ======================================================================

CREATE TABLE document_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    user_id INT NOT NULL,               -- references employees(id)
    post_id INT NULL,                    -- can be used for project docs or blog later
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100),
    media_type ENUM('image', 'video', 'audio', 'document', 'other') DEFAULT 'document',
    width INT NULL,
    height INT NULL,
    duration INT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES employees(id) ON DELETE RESTRICT,
    -- post_id will reference blog_posts later; we'll add that constraint after blog_posts is created
    INDEX idx_media_user (user_id),
    INDEX idx_media_post (post_id),
    INDEX idx_media_type (media_type),
    INDEX idx_media_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: The foreign key for media.post_id will be added after blog_posts table is created.

CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    category_id INT NULL,
    project_id INT NULL,
    client_id INT NULL,
    employee_id INT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100),
    description TEXT,
    version VARCHAR(20),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES document_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE RESTRICT,
    INDEX idx_documents_project (project_id),
    INDEX idx_documents_client (client_id),
    INDEX idx_documents_employee (employee_id),
    INDEX idx_documents_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE daily_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    report_date DATE NOT NULL,
    weather VARCHAR(255),
    temperature VARCHAR(50),
    work_description TEXT,
    delays_issues TEXT,
    safety_notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_daily_report (project_id, report_date),
    INDEX idx_reports_date (report_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE communications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    project_id INT NULL,
    client_id INT NULL,
    employee_id INT NULL,
    direction ENUM('inbound', 'outbound') NOT NULL,
    type ENUM('email', 'phone', 'meeting', 'note') NOT NULL,
    subject VARCHAR(255),
    content TEXT,
    communication_date DATETIME NOT NULL,
    attachments TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_comm_project (project_id),
    INDEX idx_comm_client (client_id),
    INDEX idx_comm_date (communication_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(80),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    ip_address VARCHAR(100),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contact_status (status),
    INDEX idx_contact_email (email),
    INDEX idx_contact_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_replies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    submission_id INT NOT NULL,
    admin_user_id INT NULL,
    reply_message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES contact_submissions(id) ON DELETE CASCADE,
    INDEX idx_contact_replies_submission (submission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 8. BLOG MODULE (Fully Integrated)
-- ======================================================================

CREATE TABLE blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) UNIQUE NOT NULL,
    description TEXT,
    parent_id INT NULL,
    icon VARCHAR(50),
    color VARCHAR(20),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    INDEX idx_blog_categories_parent (parent_id),
    INDEX idx_blog_categories_slug (slug),
    INDEX idx_blog_categories_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(60) UNIQUE NOT NULL,
    description TEXT,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_blog_tags_slug (slug),
    INDEX idx_blog_tags_usage (usage_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    author_type ENUM('employee', 'client') DEFAULT 'employee',
    author_employee_id INT NULL,
    author_client_id INT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image_id INT NULL,
    status ENUM('draft', 'published', 'scheduled', 'archived', 'pending_review') DEFAULT 'draft',
    comment_status ENUM('open', 'closed', 'disabled') DEFAULT 'open',
    view_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    scheduled_for TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT chk_blog_author CHECK (
        (author_type = 'employee' AND author_employee_id IS NOT NULL AND author_client_id IS NULL) OR
        (author_type = 'client' AND author_client_id IS NOT NULL AND author_employee_id IS NULL)
    ),
    FOREIGN KEY (author_employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (author_client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (featured_image_id) REFERENCES media(id) ON DELETE SET NULL,
    INDEX idx_blog_posts_author_employee (author_employee_id),
    INDEX idx_blog_posts_author_client (author_client_id),
    INDEX idx_blog_posts_status (status),
    INDEX idx_blog_posts_published (published_at),
    INDEX idx_blog_posts_slug (slug),
    INDEX idx_blog_posts_created (created_at),
    FULLTEXT INDEX idx_blog_posts_search (title, excerpt, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Now add the missing foreign key in media table
ALTER TABLE media ADD CONSTRAINT fk_media_blog_post FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE SET NULL;

CREATE TABLE blog_post_categories (
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE CASCADE,
    INDEX idx_blog_post_categories_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE,
    INDEX idx_blog_post_tags_tag (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    post_id INT NOT NULL,
    parent_id INT NULL,
    author_name VARCHAR(100) NULL,
    author_email VARCHAR(255) NULL,
    author_employee_id INT NULL,
    author_client_id INT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'pending',
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES blog_comments(id) ON DELETE CASCADE,
    FOREIGN KEY (author_employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (author_client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_blog_comments_post (post_id),
    INDEX idx_blog_comments_status (status),
    INDEX idx_blog_comments_created (created_at),
    INDEX idx_blog_comments_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_post_reactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    employee_id INT NULL,
    client_id INT NULL,
    reaction_type ENUM('like', 'love', 'laugh', 'wow', 'sad', 'angry') DEFAULT 'like',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    UNIQUE KEY unique_blog_reaction (post_id, employee_id, client_id, reaction_type),
    INDEX idx_blog_reactions_post (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_post_views (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    employee_id INT NULL,
    client_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer TEXT,
    session_id VARCHAR(100),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_blog_views_post (post_id),
    INDEX idx_blog_views_date (viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_post_daily_stats (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    date DATE NOT NULL,
    views INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    comments INT DEFAULT 0,
    reactions INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_blog_daily (post_id, date),
    INDEX idx_blog_stats_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 8.5. USERS TABLE (for authentication)
-- ======================================================================

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    user_type ENUM('admin', 'manager', 'staff', 'client') DEFAULT 'staff',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    profile_image VARCHAR(500),
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_users_username (username),
    INDEX idx_users_email (email),
    INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_tokens_token (token),
    INDEX idx_user_tokens_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_action (action),
    INDEX idx_activity_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- 9. SYSTEM SETTINGS (unified)
-- ======================================================================

CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    is_autoload BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_settings_group (setting_group),
    INDEX idx_settings_autoload (is_autoload)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- SAMPLE INITIAL DATA
-- ======================================================================

-- Insert sample settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('company_name', 'IronBridge Construction', 'general'),
('company_email', 'info@ironbridge.com', 'general'),
('company_phone', '+1 (555) 123-4567', 'general'),
('company_address', '123 Builder St, Construction City, ST 12345', 'general'),
('tax_rate', '8.5', 'financial'),
('default_payment_terms', 'Net 30', 'financial'),
('timezone', 'America/New_York', 'general'),
('date_format', 'Y-m-d', 'formatting'),
('blog_comments_auto_approve', '0', 'blog');

-- Insert sample roles
INSERT INTO roles (name, description) VALUES
('Project Manager', 'Oversees project execution, budget, and team'),
('Site Supervisor', 'Manages daily on-site operations'),
('Architect', 'Designs and plans structures'),
('Engineer', 'Civil/structural engineering'),
('Foreman', 'Leads crew on site'),
('Laborer', 'General construction work'),
('Safety Officer', 'Ensures compliance with safety regulations'),
('Accountant', 'Handles financials'),
('Administrator', 'Office administration');

-- Insert sample employees (UUIDs will be generated)
INSERT INTO employees (uuid, first_name, last_name, email, phone, role_id, hire_date, hourly_rate, status) VALUES
(UUID(), 'Marcus', 'Thorne', 'marcus.thorne@ironbridge.com', '555-0101', 1, '2022-01-15', 65.00, 'active'),
(UUID(), 'Sarah', 'Jenkins', 'sarah.jenkins@ironbridge.com', '555-0102', 3, '2021-03-10', 55.00, 'active'),
(UUID(), 'David', 'Okafor', 'david.okafor@ironbridge.com', '555-0103', 2, '2022-06-20', 45.00, 'active'),
(UUID(), 'Linda', 'Park', 'linda.park@ironbridge.com', '555-0104', 7, '2023-02-01', 40.00, 'active');

-- Insert sample client
INSERT INTO clients (uuid, client_type, company_name, contact_person, email, phone, status) VALUES
(UUID(), 'company', 'Harbor Developers LLC', 'Michael Reed', 'm.reed@harbordevelopers.com', '555-0201', 'active');

-- Insert sample project
INSERT INTO projects (uuid, project_number, name, description, client_id, project_manager_id, start_date, estimated_end_date, budget_total, status) VALUES
(UUID(), 'PROJ-2025-001', 'Lagos Island High-Rise', '25-story mixed-use building with retail and apartments', 1, 1, '2025-02-01', '2027-01-31', 15000000.00, 'active');

-- Insert sample project stages
INSERT INTO project_stages (project_id, name, planned_start, planned_end, status) VALUES
(1, 'Site Preparation', '2025-02-01', '2025-03-15', 'completed'),
(1, 'Foundation', '2025-03-16', '2025-06-30', 'in_progress'),
(1, 'Structural Framing', '2025-07-01', '2026-03-31', 'pending');

-- Insert sample equipment categories and equipment
INSERT INTO equipment_categories (name) VALUES
('Excavators'), ('Cranes'), ('Concrete Mixers'), ('Generators'), ('Power Tools');

INSERT INTO equipment (uuid, category_id, name, serial_number, purchase_date, purchase_price, status) VALUES
(UUID(), 1, 'Caterpillar 320 Excavator', 'CAT320-2023-001', '2023-05-10', 250000.00, 'available'),
(UUID(), 2, 'Liebherr 100 Ton Crane', 'LIE-100-002', '2022-11-20', 800000.00, 'available');

-- Insert sample suppliers and materials
INSERT INTO suppliers (uuid, name, email, phone, status) VALUES
(UUID(), 'Builders Depot', 'sales@buildersdepot.com', '555-0301', 'active'),
(UUID(), 'Concrete Supply Co.', 'orders@concretesupply.com', '555-0302', 'active');

INSERT INTO materials (uuid, name, unit, current_stock, reorder_level, supplier_id) VALUES
(UUID(), 'Portland Cement', 'bag', 500, 100, 2),
(UUID(), 'Rebar #4', 'ton', 20, 5, 1),
(UUID(), 'Plywood 4x8', 'sheet', 150, 50, 1);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, first_name, last_name, user_type, status) VALUES
('admin', 'admin@tpvconstruction.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'admin', 'active'),
('manager', 'manager@tpvconstruction.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Project', 'Manager', 'manager', 'active');

-- Insert sample blog categories
INSERT INTO blog_categories (name, slug, description) VALUES
('Company News', 'company-news', 'Updates and announcements from IronBridge'),
('Project Updates', 'project-updates', 'Progress reports on active projects'),
('Safety Tips', 'safety-tips', 'Best practices and safety guidelines on site'),
('Industry Insights', 'industry-insights', 'Trends and news in construction'),
('Employee Spotlight', 'employee-spotlight', 'Meet our talented team members');

-- Insert sample blog tags
INSERT INTO blog_tags (name, slug) VALUES
('construction', 'construction'),
('safety', 'safety'),
('sustainability', 'sustainability'),
('technology', 'technology'),
('award', 'award');

-- Insert a sample blog post (using employee ID 1 as author)
INSERT INTO blog_posts (uuid, author_type, author_employee_id, title, slug, excerpt, content, status, published_at) VALUES
(UUID(), 'employee', 1,
 'Topping out ceremony at Lagos Island High-Rise',
 'topping-out-lagos-island',
 'We celebrated a major milestone with our client and team.',
 '<p>Last Friday, we held a topping out ceremony for the Lagos Island High-Rise project. The structure has reached its full height of 25 stories, and we were joined by the client, architects, and the entire construction crew. It was a great moment to recognize everyone\'s hard work.</p><p>The next phase will focus on interior fit-out and facade installation. Stay tuned for more updates!</p>',
 'published',
 NOW());

-- Link post to categories and tags
INSERT INTO blog_post_categories (post_id, category_id) VALUES
(1, 2), (1, 1);
INSERT INTO blog_post_tags (post_id, tag_id) VALUES
(1, 1), (1, 4);

-- Insert a sample comment
INSERT INTO blog_comments (uuid, post_id, author_name, author_email, content, status) VALUES
(UUID(), 1, 'John Doe', 'john.doe@example.com', 'Congratulations to the whole team! Can’t wait to see the finished building.', 'approved');

-- ======================================================================
-- ADDITIONAL INDEXES FOR PERFORMANCE
-- ======================================================================

CREATE INDEX idx_timesheets_work_date ON timesheets(work_date);
CREATE INDEX idx_projects_manager_status ON projects(project_manager_id, status);
CREATE INDEX idx_equipment_assignments_dates ON equipment_assignments(assigned_date, returned_date);
CREATE INDEX idx_purchase_orders_supplier_status ON purchase_orders(supplier_id, status);
CREATE INDEX idx_invoices_client_status ON invoices(client_id, status);
CREATE INDEX idx_daily_reports_project_date ON daily_reports(project_id, report_date);
CREATE INDEX idx_blog_comments_approved ON blog_comments(post_id, status, created_at);
CREATE INDEX idx_blog_posts_published_status ON blog_posts(status, published_at);
CREATE INDEX idx_blog_views_daily ON blog_post_views(post_id, viewed_at);

-- ======================================================================
-- END OF SCHEMA
-- ======================================================================
