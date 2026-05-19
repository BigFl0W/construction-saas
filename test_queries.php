<?php
require_once 'config/config.php';
require_once 'config/Database.php';

$db = Database::getInstance();

try {
    $db->query("SELECT COUNT(*) as total FROM projects WHERE status = 'active' AND deleted_at IS NULL");
    echo "Query 1 OK\n";
    $db->query("SELECT COUNT(*) as total FROM employees WHERE status = 'active' AND deleted_at IS NULL");
    echo "Query 2 OK\n";
    $db->query("SELECT COUNT(*) as total FROM clients WHERE status IN ('active', 'company', 'individual') AND deleted_at IS NULL");
    echo "Query 3 OK\n";
    $db->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' AND deleted_at IS NULL");
    echo "Query 4 OK\n";
    $db->query("SELECT SUM(budget_total) as total_budget, SUM(budget_used) as used_budget FROM projects WHERE deleted_at IS NULL");
    echo "Query 5 OK\n";
    $db->query("SELECT COUNT(*) as total_invoices, SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_count, SUM(CASE WHEN status IN ('sent', 'partial') THEN total - amount_paid ELSE 0 END) as outstanding_amount FROM invoices WHERE status NOT IN ('paid', 'cancelled', 'draft')");
    echo "Query 6 OK\n";
    $db->query("SELECT COUNT(*) as total_equipment, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available FROM equipment WHERE deleted_at IS NULL");
    echo "Query 7 OK\n";
    $db->query("SELECT name, current_stock, unit, reorder_level FROM materials WHERE current_stock <= reorder_level AND status = 'active' AND deleted_at IS NULL ORDER BY current_stock ASC LIMIT 5");
    echo "Query 8 OK\n";
    $db->query("SELECT po.po_number, s.name as supplier_name, po.status, po.total, po.expected_delivery FROM purchase_orders po JOIN suppliers s ON po.supplier_id = s.id WHERE po.deleted_at IS NULL ORDER BY po.created_at DESC LIMIT 5");
    echo "Query 9 OK\n";
    $db->query("SELECT p.name as project_name, ps.name as stage_name, ps.status, ps.progress_percent, ps.actual_start, ps.planned_end FROM project_stages ps JOIN projects p ON ps.project_id = p.id WHERE ps.status IN ('in_progress', 'delayed') AND p.deleted_at IS NULL ORDER BY ps.updated_at DESC LIMIT 5");
    echo "Query 10 OK\n";
    $db->query("SELECT e.first_name, e.last_name, p.name as project_name, t.hours_worked, t.status FROM timesheets t JOIN employees e ON t.employee_id = e.id JOIN projects p ON t.project_id = p.id WHERE DATE(t.work_date) = CURDATE() AND t.deleted_at IS NULL ORDER BY t.created_at DESC LIMIT 5");
    echo "Query 11 OK\n";
    $db->query("SELECT bc.author_name, bc.content, bp.title as post_title, bc.created_at FROM blog_comments bc JOIN blog_posts bp ON bc.post_id = bp.id WHERE bc.status = 'approved' AND bc.deleted_at IS NULL ORDER BY bc.created_at DESC LIMIT 5");
    echo "Query 12 OK\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
