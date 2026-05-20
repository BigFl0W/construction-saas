<?php
// Start session and include required files
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';

// Initialize authentication
$auth = new Auth();
$auth->requireAuth(); // Redirect to login if not authenticated

// Get current user
$currentUser = $auth->getUserData();

// Initialize functions class
$functions = Functions::getInstance();

// Get database connection
$db = Database::getInstance();

// Fetch dashboard statistics
try {
    // Total active projects
    $stmt = $db->query("SELECT COUNT(*) as total FROM projects WHERE status = 'active' AND deleted_at IS NULL");
    $activeProjects = $stmt->fetch()['total'];
    
    // Total employees (active)
    $stmt = $db->query("SELECT COUNT(*) as total FROM employees WHERE status = 'active' AND deleted_at IS NULL");
    $totalEmployees = $stmt->fetch()['total'];
    
    // Total clients (active)
    $stmt = $db->query("SELECT COUNT(*) as total FROM clients WHERE status IN ('active', 'company', 'individual') AND deleted_at IS NULL");
    $totalClients = $stmt->fetch()['total'];
    
    // Total blog posts (published)
    $stmt = $db->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' AND deleted_at IS NULL");
    $totalBlogPosts = $stmt->fetch()['total'];
    
    // Budget summary
    $stmt = $db->query("SELECT 
                        SUM(budget_total) as total_budget,
                        SUM(budget_used) as used_budget
                        FROM projects 
                        WHERE deleted_at IS NULL");
    $budgetData = $stmt->fetch();
    $totalBudget = $budgetData['total_budget'] ?? 0;
    $usedBudget = $budgetData['used_budget'] ?? 0;
    $budgetPercentage = $totalBudget > 0 ? round(($usedBudget / $totalBudget) * 100, 1) : 0;
    
    // Invoices summary
    $stmt = $db->query("SELECT 
                        COUNT(*) as total_invoices,
                        SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_count,
                        SUM(CASE WHEN status IN ('sent', 'partial') THEN total - amount_paid ELSE 0 END) as outstanding_amount
                        FROM invoices 
                        WHERE status NOT IN ('paid', 'cancelled', 'draft')");
    $invoiceData = $stmt->fetch();
    $outstandingInvoices = $invoiceData['outstanding_amount'] ?? 0;
    $overdueInvoices = $invoiceData['overdue_count'] ?? 0;
    
    // Equipment stats
    $stmt = $db->query("SELECT 
                        COUNT(*) as total_equipment,
                        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available
                        FROM equipment 
                        WHERE deleted_at IS NULL");
    $equipmentData = $stmt->fetch();
    $totalEquipment = $equipmentData['total_equipment'] ?? 0;
    $availableEquipment = $equipmentData['available'] ?? 0;
    
    // Low stock materials
    $stmt = $db->query("SELECT name, current_stock, unit, reorder_level 
                        FROM materials 
                        WHERE current_stock <= reorder_level 
                        AND status = 'active' 
                        AND deleted_at IS NULL 
                        ORDER BY current_stock ASC 
                        LIMIT 5");
    $lowStockMaterials = $stmt->fetchAll();
    
    // Recent purchase orders
    $stmt = $db->query("SELECT 
                        po.po_number, 
                        s.name as supplier_name,
                        po.status,
                        po.total,
                        po.expected_delivery
                        FROM purchase_orders po
                        JOIN suppliers s ON po.supplier_id = s.id
                        ORDER BY po.created_at DESC
                        LIMIT 5");
    $recentPOs = $stmt->fetchAll();
    
    // Active project stages
    $stmt = $db->query("SELECT 
                        p.name as project_name,
                        ps.name as stage_name,
                        ps.status,
                        ps.progress_percent,
                        ps.actual_start,
                        ps.planned_end
                        FROM project_stages ps
                        JOIN projects p ON ps.project_id = p.id
                        WHERE ps.status IN ('in_progress', 'delayed')
                        AND p.deleted_at IS NULL
                        ORDER BY ps.updated_at DESC
                        LIMIT 5");
    $activeStages = $stmt->fetchAll();
    
    // Today's timesheets
    $stmt = $db->query("SELECT 
                        e.first_name,
                        e.last_name,
                        p.name as project_name,
                        t.hours_worked,
                        t.status
                        FROM timesheets t
                        JOIN employees e ON t.employee_id = e.id
                        JOIN projects p ON t.project_id = p.id
                        WHERE DATE(t.work_date) = CURDATE()
                        ORDER BY t.created_at DESC
                        LIMIT 5");
    $todayTimesheets = $stmt->fetchAll();
    
    // Recent blog comments
    $stmt = $db->query("SELECT 
                        bc.author_name,
                        bc.content,
                        bp.title as post_title,
                        bc.created_at
                        FROM blog_comments bc
                        JOIN blog_posts bp ON bc.post_id = bp.id
                        WHERE bc.status = 'approved'
                        AND bc.deleted_at IS NULL
                        ORDER BY bc.created_at DESC
                        LIMIT 5");
    $recentComments = $stmt->fetchAll();
    
    // Contact messages stats
    $unreadContactCount = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'unread'")->fetchColumn();
    $recentContactMessages = $db->query(
        "SELECT id, name, email, subject, message, created_at, status
         FROM contact_submissions
         ORDER BY created_at DESC
         LIMIT 5"
    )->fetchAll();
    
} catch (Exception $e) {
    error_log("Dashboard data fetch error: " . $e->getMessage());
    // Set default values on error
    $activeProjects = 0;
    $totalEmployees = 0;
    $totalClients = 0;
    $totalBlogPosts = 0;
    $totalBudget = 0;
    $usedBudget = 0;
    $budgetPercentage = 0;
    $outstandingInvoices = 0;
    $overdueInvoices = 0;
    $totalEquipment = 0;
    $availableEquipment = 0;
    $lowStockMaterials = [];
    $recentPOs = [];
    $activeStages = [];
    $todayTimesheets = [];
    $recentComments = [];
    $unreadContactCount = 0;
    $recentContactMessages = [];
}

// Get user's first name for greeting
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'dashboard';
$pageTitle = 'TPV Construction and Services LTD · Operator Dashboard';
require 'inc/admin_header.php';
?>

<!-- GOOGLE FONTS & PREMIUM DASHBOARD STYLES -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

body {
    font-family: 'Outfit', sans-serif !important;
    background-color: #f4f7fb !important;
}

/* Hero Section */
.dash-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 18px;
    padding: 24px 26px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.dash-hero::before {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: url('https://www.transparenttextures.com/patterns/cubes.png');
    opacity: 0.1;
    z-index: 1;
}

.dash-hero > * {
    position: relative;
    z-index: 2;
}

.dash-hero h2 {
    font-weight: 700;
    margin-bottom: 6px;
    font-size: 1.55rem;
    letter-spacing: -0.5px;
}

.dash-hero p {
    color: #cbd5e1;
    font-weight: 400;
    font-size: 0.96rem;
}

/* Modern Metric Tiles */
.modern-metric {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03), 0 2px 4px -2px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(226, 232, 240, 0.6);
    position: relative;
    overflow: hidden;
    margin-bottom: 16px;
}

.modern-metric:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.04);
    border-color: rgba(59, 130, 246, 0.3);
}

.modern-metric::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: #d4a13e; /* Theme Accent */
    transition: width 0.3s ease;
}

.modern-metric:hover::after {
    width: 100%;
}

.mm-icon {
    width: 48px;
    height: 48px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.mm-icon.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.mm-icon.gold { background: rgba(212, 161, 62, 0.1); color: #d4a13e; }
.mm-icon.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.mm-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.mm-content {
    text-align: right;
}

.mm-value {
    font-size: 1.65rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 2px;
}

.mm-label {
    font-size: 0.77rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Modern Cards */
.modern-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    margin-bottom: 20px;
    transition: box-shadow 0.2s ease;
    height: calc(100% - 20px);
}

.modern-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04);
}

.modern-card-header {
    padding: 16px 18px;
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modern-card-title {
    font-weight: 600;
    font-size: 0.98rem;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modern-card-body {
    padding: 18px;
}

/* Beautiful Lists & Tables */
.modern-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.modern-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #e2e8f0;
}

.modern-list-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.modern-table {
    border-collapse: separate;
    border-spacing: 0 8px;
    margin-top: -8px;
}

.modern-table th {
    background: transparent;
    border: none;
    color: #64748b;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 8px 12px;
}

.modern-table td {
    background: #f8fafc;
    border: none;
    padding: 12px 12px;
    font-size: 0.84rem;
    font-weight: 500;
    color: #334155;
    transition: background 0.2s;
}

.modern-table tr:hover td {
    background: #f1f5f9;
}

.modern-table td:first-child { border-radius: 8px 0 0 8px; }
.modern-table td:last-child { border-radius: 0 8px 8px 0; }

/* Visual Components */
.chart-container {
    background: #f8fafc;
    border-radius: 12px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.progress-modern {
    height: 8px;
    border-radius: 4px;
    background: #e2e8f0;
    overflow: hidden;
    margin-top: 8px;
}

.progress-modern-bar {
    height: 100%;
    border-radius: 4px;
    position: relative;
    overflow: hidden;
}

.progress-modern-bar::after {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Badges */
.badge-modern {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-modern.success { background: #dcfce7; color: #166534; }
.badge-modern.warning { background: #fef9c3; color: #854d0e; }
.badge-modern.danger { background: #fee2e2; color: #991b1b; }
.badge-modern.info { background: #e0e7ff; color: #3730a3; }

/* Layout refinements */
.dashboard-container {
    padding: 20px;
    max-width: 1320px;
    margin: 0 auto;
}

@media (max-width: 1199px) {
    .dash-hero h2 {
        font-size: 1.42rem;
    }
    .mm-value {
        font-size: 1.45rem;
    }
}

@media (max-width: 991px) {
    .dashboard-container {
        padding: 16px;
    }
    .dash-hero {
        padding: 20px;
        border-radius: 16px;
    }
    .dash-hero h2 {
        font-size: 1.28rem;
    }
    .dash-hero p {
        font-size: 0.88rem;
    }
    .modern-metric {
        padding: 16px;
    }
    .mm-icon {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }
    .mm-value {
        font-size: 1.3rem;
    }
    .mm-label {
        font-size: 0.71rem;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 12px;
    }
    .dash-hero {
        padding: 16px 16px 18px;
        margin-bottom: 16px;
    }
    .dash-hero h2 {
        font-size: 1.12rem;
        line-height: 1.3;
    }
    .dash-hero p {
        font-size: 0.82rem;
        margin-bottom: 0;
    }
    .modern-card-header {
        padding: 14px 14px;
    }
    .modern-card-title {
        font-size: 0.9rem;
    }
    .modern-card-body {
        padding: 14px;
    }
    .modern-table td,
    .modern-table th {
        font-size: 0.78rem;
    }
    .badge-modern {
        font-size: 0.66rem;
    }
}

@media (max-width: 575px) {
    .dashboard-container {
        padding: 10px;
    }
    .dash-hero {
        border-radius: 14px;
        padding: 14px;
    }
    .dash-hero h2 {
        font-size: 1rem;
    }
    .dash-hero p {
        font-size: 0.78rem;
    }
    .modern-metric {
        padding: 14px;
        border-radius: 12px;
    }
    .mm-content {
        text-align: left;
    }
    .mm-value {
        font-size: 1.18rem;
    }
    .mm-label {
        font-size: 0.67rem;
    }
    .modern-card {
        border-radius: 12px;
        margin-bottom: 16px;
        height: auto;
    }
    .modern-card-header,
    .modern-card-body {
        padding-left: 12px;
        padding-right: 12px;
    }
    .modern-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}
</style>

<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="dash-hero">
        <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h2>
        <p>Here’s what is happening with your projects today.</p>
    </div>

    <?php if ($unreadContactCount > 0): ?>
    <div class="modern-card" style="border:1px solid rgba(220,38,38,0.12); box-shadow: 0 16px 30px -24px rgba(220,38,38,0.45);">
        <div class="modern-card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <div style="width:54px;height:54px;border-radius:16px;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-bell" style="font-size:1.2rem;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <h3 class="modern-card-title mb-0">New contact enquiries waiting</h3>
                        <span class="badge-modern danger"><?php echo number_format($unreadContactCount); ?> unread</span>
                    </div>
                    <p class="mb-0 text-muted">
                        <?php if (!empty($recentContactMessages)): ?>
                            Latest from <strong><?php echo htmlspecialchars($recentContactMessages[0]['name']); ?></strong>
                            about <strong><?php echo htmlspecialchars($functions->truncateText($recentContactMessages[0]['subject'] ?: 'Website enquiry', 42)); ?></strong>.
                        <?php else: ?>
                            New messages have arrived from the website contact form.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="contact_messages.php?status=unread" class="btn btn-danger">
                    <i class="fas fa-inbox me-1"></i> Review Messages
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Metrics Row -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="modern-metric">
                <div class="mm-icon blue"><i class="fas fa-users"></i></div>
                <div class="mm-content">
                    <div class="mm-value"><?php echo number_format($totalEmployees); ?></div>
                    <div class="mm-label">Employees</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="modern-metric">
                <div class="mm-icon gold"><i class="fas fa-handshake"></i></div>
                <div class="mm-content">
                    <div class="mm-value"><?php echo number_format($totalClients); ?></div>
                    <div class="mm-label">Active Clients</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="modern-metric">
                <div class="mm-icon purple"><i class="fas fa-edit"></i></div>
                <div class="mm-content">
                    <div class="mm-value"><?php echo number_format($totalBlogPosts); ?></div>
                    <div class="mm-label">Blog Posts</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="modern-metric">
                <div class="mm-icon green"><i class="fas fa-helmet-safety"></i></div>
                <div class="mm-content">
                    <div class="mm-value"><?php echo number_format($activeProjects); ?></div>
                    <div class="mm-label">Active Projects</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial & Equipment Row -->
    <div class="row">
        <!-- Budget Utilization -->
        <div class="col-xl-4 col-lg-6 align-self-stretch d-flex">
            <div class="modern-card w-100">
                <div class="modern-card-header">
                    <h3 class="modern-card-title"><i class="fas fa-chart-pie text-muted"></i> Budget Utilization</h3>
                    <div class="dropdown">
                        <button class="btn btn-icon-only text-muted border-0" type="button"><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                </div>
                <div class="modern-card-body d-flex flex-column justify-content-center">
                    <div class="chart-container">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div>
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Used Budget</div>
                                <h2 class="mb-0 fw-bold text-dark"><?php echo $functions->formatCurrency($usedBudget); ?></h2>
                            </div>
                            <h3 class="mb-0 text-primary fw-bold"><?php echo $budgetPercentage; ?>%</h3>
                        </div>
                        <div class="text-muted mb-4 small">Total Budget: <?php echo $functions->formatCurrency($totalBudget); ?></div>
                        
                        <div class="progress-modern">
                            <div class="progress-modern-bar bg-primary" style="width: <?php echo $budgetPercentage; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices -->
        <div class="col-xl-4 col-lg-6 align-self-stretch d-flex">
            <div class="modern-card w-100">
                <div class="modern-card-header">
                    <h3 class="modern-card-title"><i class="fas fa-file-invoice-dollar text-muted"></i> outstanding invoices</h3>
                </div>
                <div class="modern-card-body d-flex flex-column justify-content-center">
                    <div class="chart-container" style="background: #fff8f8;">
                        <h2 class="fw-bold mb-1" style="color: #0f172a;"><?php echo $functions->formatCurrency($outstandingInvoices); ?></h2>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge-modern danger shadow-sm mt-2">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $overdueInvoices; ?> Overdue
                            </span>
                        </div>
                        
                        <?php if ($outstandingInvoices > 0): ?>
                            <?php $invoicePercent = min(100, round(($overdueInvoices * 100) / max(1, $invoiceData['total_invoices'] ?? 1))); ?>
                            <div class="progress-modern mt-auto">
                                <div class="progress-modern-bar bg-danger" style="width: <?php echo $invoicePercent; ?>%"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment -->
        <div class="col-xl-4 col-lg-12 align-self-stretch d-flex">
            <div class="modern-card w-100">
                <div class="modern-card-header">
                    <h3 class="modern-card-title"><i class="fas fa-truck-moving text-muted"></i> Fleet Status</h3>
                    <a href="equipment.php" class="text-primary small fw-semibold text-decoration-none">View Fleet <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="modern-card-body">
                    <div class="d-flex align-items-center mb-4 p-3 rounded" style="background: #f8fafc; border-left: 4px solid #10b981;">
                        <div class="me-3">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?php echo $availableEquipment; ?> <span class="text-muted fw-normal fs-6">ready</span></h3>
                            <div class="text-muted small">Out of <?php echo $totalEquipment; ?> total assets</div>
                        </div>
                    </div>

                    <?php if (!empty($lowStockMaterials)): ?>
                    <div>
                        <h4 class="fw-semibold fs-6 mb-3 text-danger"><i class="fas fa-box-open me-2"></i> Low Materials Alert</h4>
                        <ul class="modern-list">
                            <?php foreach ($lowStockMaterials as $material): ?>
                            <li class="modern-list-item py-1">
                                <span class="fw-medium text-dark"><?php echo htmlspecialchars($material['name']); ?></span>
                                <span class="badge-modern danger text-danger" style="background: none; padding: 0;">
                                    <?php echo number_format($material['current_stock']); ?> <?php echo htmlspecialchars($material['unit']); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> <!-- /row -->

    <!-- Complex Tables Row -->
    <div class="row mt-2">
        <!-- Purchase Orders -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="modern-card-title">Recent Purchase Orders</h3>
                    <a href="purchase_orders.php" class="text-primary small fw-semibold text-decoration-none">View All</a>
                </div>
                <div class="modern-card-body p-0" style="overflow-x: auto;">
                    <table class="table modern-table mb-0 w-100 px-3 pb-3" style="margin: 0; padding: 0 16px 16px 16px; width: calc(100% - 32px); margin-left: 16px; display:inline-table;">
                        <thead>
                            <tr>
                                <th>Order / Supplier</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Delivery</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPOs as $po): ?>
                            <?php 
                                $status = $po['status'];
                                $badgeClass = $status == 'received' ? 'success' : ($status == 'pending' ? 'warning' : 'info');
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($po['po_number']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($po['supplier_name']); ?></div>
                                </td>
                                <td><span class="badge-modern <?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span></td>
                                <td class="fw-semibold"><?php echo $functions->formatCurrency($po['total']); ?></td>
                                <td>
                                    <?php if ($po['expected_delivery']): ?>
                                        <div class="text-muted text-nowrap"><i class="far fa-calendar-alt me-1"></i> <?php echo $functions->formatDate($po['expected_delivery'], 'M d'); ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($recentPOs)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No recent purchase orders.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Today's Timesheets -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="modern-card-title">Today's Timesheets</h3>
                </div>
                <div class="modern-card-body p-0" style="overflow-x: auto;">
                    <table class="table modern-table mb-0 w-100 px-3 pb-3" style="margin: 0; padding: 0 16px 16px 16px; width: calc(100% - 32px); margin-left: 16px; display:inline-table;">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Project</th>
                                <th>Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($todayTimesheets)): ?>
                                <?php foreach ($todayTimesheets as $ts): ?>
                                <?php 
                                    $tStatus = $ts['status'];
                                    $tClass = $tStatus == 'approved' ? 'success' : ($tStatus == 'pending' ? 'warning' : 'info');
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background:#e2e8f0; color:#475569; display:flex; align-items:center; justify-content:center; font-weight:600; margin-right: 12px; font-size:12px;">
                                                <?php echo substr($ts['first_name'], 0, 1) . substr($ts['last_name'], 0, 1); ?>
                                            </div>
                                            <span class="fw-semibold text-dark"><?php echo htmlspecialchars($ts['first_name'] . ' ' . $ts['last_name']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($ts['project_name']); ?></div>
                                    </td>
                                    <td class="fw-bold"><?php echo number_format($ts['hours_worked'], 1); ?> h</td>
                                    <td><span class="badge-modern <?php echo $tClass; ?>"><?php echo ucfirst($tStatus); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <div style="background: #f8fafc; border-radius: 50%; width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 24px; color: #cbd5e1;">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>No timesheets logged for today yet.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- /row -->

    <!-- Map & Activities -->
    <div class="row mt-2">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="modern-card-title"><i class="fas fa-map-marked-alt text-muted"></i> Head Office Location</h3>
                </div>
                <div class="modern-card-body p-2">
                    <iframe style="width: 100%; height: 350px; border: 0; border-radius: 12px;"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127223.16264394466!2d7.00479655!3d4.81741045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1069cea39f2c48e3%3A0x53562bdd7d8832db!2sPort%20Harcourt%2C%20Rivers!5e0!3m2!1sen!2sng!4v1726019643240!5m2!1sen!2sng"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <div class="text-center text-muted small mt-2 fw-medium">
                        <i class="fas fa-location-dot text-danger me-1"></i> 23 Trans-Amadi, Port Harcourt · Operations & Admin
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="modern-card-title"><i class="fas fa-comments text-muted"></i> Recent Blog Comments</h3>
                </div>
                <div class="modern-card-body">
                    <?php if (!empty($recentComments)): ?>
                        <div class="d-flex flex-column gap-3">
                        <?php foreach ($recentComments as $comment): ?>
                            <div class="d-flex p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9; transition: all 0.2s; cursor:pointer;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='#f8fafc';">
                                <div class="me-3">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 14px; font-weight: bold;">
                                        <?php echo strtoupper(substr($comment['author_name'] ?: 'A', 0, 1)); ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($comment['author_name'] ?: 'Anonymous'); ?></div>
                                        <div class="text-muted small" style="font-size: 0.75rem;"><?php echo $functions->timeAgo($comment['created_at']); ?></div>
                                    </div>
                                    <div class="text-muted small mb-1">On: <span class="fw-medium">"<?php echo htmlspecialchars($functions->truncateText($comment['post_title'], 25)); ?>"</span></div>
                                    <div class="text-dark fst-italic" style="font-size: 0.85rem; line-height: 1.4;">
                                        “<?php echo htmlspecialchars($functions->truncateText($comment['content'], 60)); ?>”
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5 text-muted">
                            <i class="far fa-comments fs-1 mb-3 text-light"></i>
                            <p>No recent comments.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> <!-- /row -->

    <!-- Contact Messages -->
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="modern-card-title"><i class="fas fa-inbox text-muted"></i> Contact Messages
                        <?php if ($unreadContactCount > 0): ?>
                            <span class="badge-modern danger ms-2"><?php echo $unreadContactCount; ?> unread</span>
                        <?php endif; ?>
                    </h3>
                    <a href="contact_messages.php" class="text-primary small fw-semibold text-decoration-none">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="modern-card-body">
                    <?php if (!empty($recentContactMessages)): ?>
                        <div class="d-flex flex-column gap-3">
                        <?php foreach ($recentContactMessages as $cm): ?>
                            <a href="contact_messages.php?status=<?php echo $cm['status']; ?>" style="text-decoration:none;color:inherit;">
                                <div class="d-flex p-3 rounded align-items-start" style="background: <?php echo $cm['status'] === 'unread' ? '#fff8f8' : '#f8fafc'; ?>; border: 1px solid <?php echo $cm['status'] === 'unread' ? '#fecaca' : '#f1f5f9'; ?>; border-left: 3px solid <?php echo $cm['status'] === 'unread' ? '#dc2626' : '#e2e8f0'; ?>; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='<?php echo $cm['status'] === 'unread' ? '#fff8f8' : '#f8fafc'; ?>';">
                                    <div class="me-3">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $cm['status'] === 'unread' ? '#dc2626' : '#e2e8f0'; ?>; display: flex; align-items: center; justify-content: center; color: <?php echo $cm['status'] === 'unread' ? '#fff' : '#64748b'; ?>; font-size: 14px; font-weight: bold;">
                                            <?php echo strtoupper(substr($cm['name'] ?: '?', 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                <?php echo htmlspecialchars($cm['name']); ?>
                                                <?php if ($cm['status'] === 'unread'): ?>
                                                    <span style="display:inline-block;width:7px;height:7px;background:#dc2626;border-radius:50%;margin-left:6px;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.75rem;white-space:nowrap;margin-left:8px;">
                                                <?php echo $functions->timeAgo($cm['created_at']); ?>
                                            </div>
                                        </div>
                                        <div class="text-muted small mb-1"><?php echo htmlspecialchars($cm['email']); ?> &middot; <span class="fw-medium"><?php echo htmlspecialchars($functions->truncateText($cm['subject'], 40)); ?></span></div>
                                        <div class="text-dark" style="font-size: 0.85rem; line-height: 1.4; display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">
                                            <?php echo htmlspecialchars($functions->truncateText($cm['message'], 80)); ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5 text-muted">
                            <i class="far fa-inbox fs-1 mb-3 text-light"></i>
                            <p>No contact messages yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> <!-- /row -->

</div> <!-- /dashboard-container -->

<?php require 'inc/admin_footer.php'; ?>
