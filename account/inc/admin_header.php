<?php
if (!isset($pageActive)) $pageActive = '';
if (!isset($pageTitle)) $pageTitle = 'TPV Construction and Services LTD';
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
if (!function_exists('adminAvatarInitials')) {
    function adminAvatarInitials(array $user): string {
        $first = trim((string) ($user['first_name'] ?? ''));
        $last = trim((string) ($user['last_name'] ?? ''));
        $username = trim((string) ($user['username'] ?? 'U'));
        $initials = '';
        if ($first !== '') {
            $initials .= mb_substr($first, 0, 1);
        }
        if ($last !== '') {
            $initials .= mb_substr($last, 0, 1);
        }
        if ($initials === '') {
            $initials = mb_substr($username, 0, 2);
        }
        return strtoupper($initials);
    }
}
function navActive($key) { global $pageActive; return $pageActive === $key ? 'active' : ''; }
function navOpen($key) { global $pageActive; return strpos($pageActive, $key) === 0 ? 'open' : ''; }
// Collect toast + legacy flash messages
$toastSuccess = $_SESSION['toast_success'] ?? $_SESSION['blog_success'] ?? '';
$toastError = $_SESSION['toast_error'] ?? $_SESSION['blog_error'] ?? '';
$toastWarning = $_SESSION['toast_warning'] ?? '';
$toastInfo = $_SESSION['toast_info'] ?? '';
unset($_SESSION['toast_success'], $_SESSION['toast_error'], $_SESSION['toast_warning'], $_SESSION['toast_info']);
unset($_SESSION['blog_success'], $_SESSION['blog_error']);
$headerCsrfToken = $_SESSION['csrf_token'] ?? '';
if ($headerCsrfToken === '' && isset($auth) && method_exists($auth, 'generateCSRF')) {
    $headerCsrfToken = $auth->generateCSRF();
}

require_once dirname(__DIR__, 2) . '/classes/QuoteRequest.php';

$adminBrandLogo = 'assets/img/logo.png';
try {
    if (class_exists('Settings')) {
        $headerSettings = new Settings();
        $adminBrandLogo = tpv_setting_asset_url('site_logo', 'wp-content/uploads/2024/06/logo.png');
    }
} catch (Throwable $e) {
    $adminBrandLogo = 'assets/img/logo.png';
}

$unreadContactNotifications = 0;
$recentContactNotifications = [];
$unreadQuoteNotifications = 0;
$recentQuoteNotifications = [];
$recentEnquiryNotifications = [];
$totalEnquiryNotifications = 0;
try {
    $headerDb = Database::getInstance();
    $quoteRequestManager = new QuoteRequest();
    $contactTableExists = $headerDb->query("SHOW TABLES LIKE 'contact_submissions'")->fetchColumn();
    if ($contactTableExists) {
        $unreadContactNotifications = (int) $headerDb->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'unread'")->fetchColumn();
        $recentContactNotifications = $headerDb->query(
            "SELECT id, name, email, subject, created_at, status
             FROM contact_submissions
             WHERE status = 'unread'
             ORDER BY created_at DESC
             LIMIT 5"
        )->fetchAll();
    }
    $unreadQuoteNotifications = $quoteRequestManager->getUnreadCount();
    $recentQuoteNotifications = $quoteRequestManager->getRecentUnread(5);

    foreach ($recentContactNotifications as $notification) {
        $recentEnquiryNotifications[] = [
            'kind' => 'contact',
            'id' => (int) $notification['id'],
            'name' => $notification['name'] ?: 'New contact',
            'email' => $notification['email'],
            'subject' => $notification['subject'] ?: 'Website enquiry',
            'meta' => 'Contact form enquiry',
            'created_at' => $notification['created_at'],
            'href' => 'contact_messages.php?status=unread&focus=' . (int) $notification['id'] . '#message-' . (int) $notification['id']
        ];
    }

    foreach ($recentQuoteNotifications as $notification) {
        $recentEnquiryNotifications[] = [
            'kind' => 'quote',
            'id' => (int) $notification['id'],
            'name' => trim(($notification['first_name'] ?? '') . ' ' . ($notification['last_name'] ?? '')) ?: 'New quote request',
            'email' => $notification['email'] ?? '',
            'subject' => $notification['project_type'] ?: 'Quote request',
            'meta' => 'Quote request · ' . ($notification['project_location'] ?: 'Location not specified'),
            'created_at' => $notification['created_at'],
            'href' => 'quote_requests.php?status=unread&focus=' . (int) $notification['id'] . '#quote-request-' . (int) $notification['id']
        ];
    }

    usort($recentEnquiryNotifications, function ($a, $b) {
        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });
    $recentEnquiryNotifications = array_slice($recentEnquiryNotifications, 0, 8);
    $totalEnquiryNotifications = $unreadContactNotifications + $unreadQuoteNotifications;
} catch (Exception $e) {
    $unreadContactNotifications = 0;
    $recentContactNotifications = [];
    $unreadQuoteNotifications = 0;
    $recentQuoteNotifications = [];
    $recentEnquiryNotifications = [];
    $totalEnquiryNotifications = 0;
}
$headerAvatarUrl = !empty($currentUser['profile_image']) ? tpv_asset_url($currentUser['profile_image']) : '';
$headerAvatarInitials = adminAvatarInitials($currentUser);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    <style>
        :root {
            --sidebar-width: 238px;
            --sidebar-bg: #1a2332;
            --sidebar-hover: #232f41;
            --sidebar-active: #2a3a52;
            --accent: #d4a13e;
            --accent-rgb: 212, 161, 62;
            --topbar-height: 56px;
            --font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --text-primary: #1a2332;
            --text-muted: #6b7a8f;
            --border-color: #e4e9f0;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: var(--font);
            background: #f0f2f5;
            overflow-x: hidden;
            font-size: 0.84rem;
            line-height: 1.6;
            color: var(--text-primary);
        }
        h1, h2, h3, h4, h5, h6 { font-weight: 600; letter-spacing: -0.01em; }
        h1 { font-size: 1.5rem; }
        h2 { font-size: 1.25rem; }
        h3 { font-size: 1.125rem; }
        h4 { font-size: 1rem; }
        h5 { font-size: 0.938rem; }
        h6 { font-size: 0.875rem; }
        p { margin-bottom: 0.5rem; }
        a { color: var(--accent); text-decoration: none; }
        a:hover { color: #b8902e; }

        /* ===== SIDEBAR ===== */
        .page-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar-header {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-header .brand {
            width: auto;
            max-width: 156px;
            max-height: 48px;
            object-fit: contain;
            object-position: left center;
        }
        .sidebar-menu { flex: 1; overflow-y: auto; padding: 8px 0; }
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        .menu-items { list-style: none; padding: 0; margin: 0; }
        .menu-items > li > a {
            display: flex;
            align-items: center;
            padding: 9px 16px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.79rem;
            transition: all 0.15s;
            gap: 10px;
        }
        .menu-items > li > a:hover { background: var(--sidebar-hover); color: #fff; }
        .menu-items > li.active > a,
        .menu-items > li.open > a { background: var(--sidebar-active); color: #fff; }
        .menu-items > li.active > a { border-left: 3px solid var(--accent); }
        .menu-items > li > a .arrow { margin-left: auto; font-size: 0.65rem; transition: transform 0.2s; }
        .menu-items > li.open > a .arrow { transform: rotate(180deg); }
        .icon-thumbnail {
            width: 28px; height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--accent);
            flex-shrink: 0;
        }
        .icon-thumbnail i { font-size: 0.9rem; }
        .menu-items > li > a .title { flex: 1; }
        .menu-items > li > a .details { font-size: 0.65rem; color: rgba(255,255,255,0.3); }
        .sub-menu { list-style: none; padding: 0; margin: 0; display: none; background: rgba(0,0,0,0.15); }
        .menu-items > li.open > .sub-menu { display: block; }
        .sub-menu li a {
            display: flex;
            align-items: center;
            padding: 7px 16px 7px 46px;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            font-size: 0.77rem;
            transition: all 0.15s;
            gap: 8px;
        }
        .sub-menu li a:hover { color: #fff; background: rgba(255,255,255,0.03); }
        .sub-menu li.active a { color: var(--accent); background: rgba(212,161,62,0.08); }
        .sub-menu .icon-thumbnail { width: 22px; height: 22px; font-size: 0.55rem; }

        /* ===== SIDEBAR TOGGLE (mobile) ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1035;
        }

        /* ===== PAGE CONTAINER ===== */
        .page-container {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* ===== TOPBAR ===== */
        .header {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            padding: 0 16px;
            position: sticky;
            top: 0;
            z-index: 1025;
            gap: 10px;
        }
        .header .toggle-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #4a5568;
            padding: 4px 8px;
            cursor: pointer;
        }
        .header .brand {
            display: none;
            width: auto;
            max-width: 120px;
            max-height: 40px;
            object-fit: contain;
        }
        .header .profile-dropdown-toggle {
            background: none; border: none; padding: 0; cursor: pointer;
        }
        .header .thumbnail-wrapper { display: inline-block; overflow: hidden; border-radius: 50%; }
        .header .thumbnail-wrapper img { display: block; }
        .admin-avatar-fallback {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #d4a13e 0%, #ef3d43 100%);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .notification-bell {
            position: relative;
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: #fff;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }
        .notification-bell:hover {
            color: #0f172a;
            border-color: rgba(var(--accent-rgb), 0.4);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }
        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #dc2626;
            color: #fff;
            font-size: 0.68rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.25);
        }
        .nav-pill-badge {
            margin-left: auto;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            background: rgba(220, 38, 38, 0.16);
            color: #fca5a5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .notification-dropdown {
            width: 360px;
            padding: 0;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 40px rgba(15, 23, 42, 0.14);
        }
        .notification-dropdown-header {
            padding: 16px 18px 12px;
            border-bottom: 1px solid #eef2f7;
            background: #fff;
        }
        .notification-dropdown-header h6 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
        }
        .notification-dropdown-header p {
            margin: 4px 0 0;
            font-size: 0.78rem;
            color: #64748b;
        }
        .notification-dropdown-body {
            max-height: 360px;
            overflow-y: auto;
            background: #fff;
        }
        .notification-item {
            display: block;
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: inherit;
            text-decoration: none;
            transition: background 0.2s ease;
        }
        .notification-item:hover {
            background: #f8fafc;
            color: inherit;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 4px;
        }
        .notification-item-title strong {
            color: #0f172a;
            font-size: 0.85rem;
        }
        .notification-item-time {
            color: #94a3b8;
            font-size: 0.72rem;
            white-space: nowrap;
        }
        .notification-item-meta,
        .notification-item-subject {
            margin: 0;
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.45;
        }
        .notification-dropdown-footer {
            padding: 12px 18px;
            border-top: 1px solid #eef2f7;
            background: #fbfdff;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .notification-dropdown-footer a {
            font-size: 0.82rem;
            font-weight: 700;
        }
        .notification-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .notification-actions form {
            flex: 1 1 0;
            min-width: 0;
        }
        .notification-actions .btn {
            width: 100%;
            border-radius: 10px;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 8px 10px;
        }
        .notification-open-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 40px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
        }
        .notification-empty {
            padding: 28px 18px;
            text-align: center;
            color: #94a3b8;
        }
        .notification-empty i {
            display: block;
            font-size: 1.25rem;
            margin-bottom: 8px;
            color: #cbd5e1;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content-wrapper { flex: 1; padding: 16px; }
        .content { max-width: 1480px; }
        .breadcrumb {
            background: none; padding: 0; margin-bottom: 16px; font-size: 0.8rem;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: '\f105';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.7rem;
        }

        /* ===== FOOTER ===== */
        .footer {
            border-top: 1px solid var(--border-color);
            padding: 12px 20px;
            font-size: 0.75rem;
            color: #6c757d;
            background: #fff;
        }
        .footer .copyright {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .footer .copyright p {
            margin: 0;
        }
        .footer .footer-meta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-weight: 500;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 14px;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #edf2f7;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 10px 10px 0 0 !important;
        }
        .card-body { padding: 16px; }
        .card-title { font-size: 0.9rem; font-weight: 600; margin: 0; }

        /* ===== TABLES ===== */
        .table { margin-bottom: 0; }
        .table thead th {
            background: #f8fafc;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #4a5568;
            border-bottom-width: 1px;
            padding: 10px 12px;
        }
        .table td { padding: 10px 12px; vertical-align: middle; font-size: 0.825rem; }
        .table-hover tbody tr:hover { background: #f8fafc; }

        /* ===== STATUS BADGES ===== */
        .badge { font-weight: 500; padding: 0.3em 0.65em; font-size: 0.7rem; }
        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.65rem;
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 600;
        }
        .status-planning, .badge-bg-planning { background: #e9ecf3; color: #2d3e5f; }
        .status-active, .badge-bg-active { background: #d4edda; color: #155724; }
        .status-on_hold, .badge-bg-on_hold { background: #fff3cd; color: #856404; }
        .status-completed, .badge-bg-completed { background: #cce5ff; color: #004085; }
        .status-cancelled, .badge-bg-cancelled { background: #f8d7da; color: #721c24; }
        .status-draft, .badge-bg-draft { background: #fff6df; color: #b26000; }
        .status-sent, .badge-bg-sent { background: #e0f0ff; color: #004e9e; }
        .status-paid, .badge-bg-paid { background: #d4edda; color: #1e7e34; }
        .status-partial, .badge-bg-partial { background: #fff3cd; color: #856404; }
        .status-overdue, .badge-bg-overdue { background: #f8d7da; color: #721c24; }
        .status-published, .badge-bg-published { background: #d4edda; color: #1e7e34; }
        .status-pending_review, .badge-bg-pending_review { background: #e0f0ff; color: #004e9e; }
        .status-archived, .badge-bg-archived { background: #ebedf0; color: #4a4f55; }
        .status-pending, .badge-bg-pending { background: #fff3cd; color: #856404; }
        .status-in_progress, .badge-bg-in_progress { background: #cce5ff; color: #004085; }
        .status-delayed, .badge-bg-delayed { background: #f8d7da; color: #721c24; }
        .status-available, .badge-bg-available { background: #d4edda; color: #155724; }
        .status-in_use, .badge-bg-in_use { background: #cce5ff; color: #004085; }
        .status-maintenance, .badge-bg-maintenance { background: #fff3cd; color: #856404; }
        .status-out_of_service, .badge-bg-out_of_service { background: #f8d7da; color: #721c24; }
        .status-retired, .badge-bg-retired { background: #ebedf0; color: #4a4f55; }

        /* ===== METRIC TILES ===== */
        .metric-tile {
            background: #fff;
            border-radius: 10px;
            padding: 1.25rem 1rem;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }
        .metric-tile:hover { border-color: var(--accent); box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.1); }
        .metric-tile .value { font-size: 1.75rem; font-weight: 700; color: #1a2332; line-height: 1.2; }
        .metric-tile .label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; }

        /* ===== BUTTONS ===== */
        .btn { border-radius: 8px; font-weight: 500; font-size: 0.825rem; padding: 0.45rem 0.9rem; }
        .btn-sm { border-radius: 6px; font-size: 0.75rem; padding: 0.3rem 0.6rem; }
        .btn-lg { font-size: 0.938rem; padding: 0.6rem 1.2rem; border-radius: 10px; }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #1a2332; }
        .btn-primary:hover { background: #c08e2e; border-color: #c08e2e; color: #1a2332; }
        .btn-outline-primary { border-color: var(--accent); color: var(--accent); }
        .btn-outline-primary:hover { background: var(--accent); color: #1a2332; }
        .btn-icon-only {
            padding: 0.35rem 0.55rem;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            transition: all 0.15s;
        }
        .btn-icon-only:hover { background: #f1f5f9; border-color: #cbd5e0; }

        /* ===== MODALS ===== */
        .modal-content { border: none; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .modal-header { border-bottom: 1px solid #edf2f7; padding: 16px 20px; }
        .modal-body { padding: 20px; }
        .modal-footer { border-top: 1px solid #edf2f7; padding: 12px 20px; }
        .modal .form-label { font-weight: 500; font-size: 0.8rem; margin-bottom: 0.2rem; color: #374151; }
        .modal-title { font-size: 1rem; font-weight: 600; }
        .modal-backdrop.show { opacity: 0.5; }

        /* ===== CONFIRMATION MODAL ===== */
        #confirmModal .modal-header { border-bottom: none; padding-bottom: 0; }
        #confirmModal .modal-body { text-align: center; padding: 24px 20px 16px; }
        #confirmModal .modal-body i { font-size: 2.5rem; margin-bottom: 12px; }
        #confirmModal .modal-footer { border-top: none; justify-content: center; gap: 8px; padding-top: 0; }

        /* ===== FORMS ===== */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #d1d9e6;
            padding: 0.45rem 0.7rem;
            font-size: 0.825rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(var(--accent-rgb), 0.15);
        }
        .form-group { margin-bottom: 0.875rem; }
        .form-label { font-weight: 500; font-size: 0.8rem; margin-bottom: 0.2rem; color: #374151; }

        /* ===== ALERTS ===== */
        .alert { border-radius: 8px; border: none; font-size: 0.825rem; padding: 0.6rem 1rem; }

        /* ===== PROGRESS ===== */
        .progress { border-radius: 20px; background: #e9ecef; height: 6px; }
        .progress-bar { border-radius: 20px; }

        /* ===== TOAST ===== */
        .toast-container-custom {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 380px;
            width: 100%;
        }
        .toast-custom {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            font-size: 0.825rem;
            line-height: 1.4;
            animation: toastSlideIn 0.3s ease-out;
            color: #fff;
        }
        .toast-custom.success { background: #059669; }
        .toast-custom.error { background: #dc2626; }
        .toast-custom.warning { background: #d97706; }
        .toast-custom.info { background: #2563eb; }
        .toast-custom .toast-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
        .toast-custom .toast-body { flex: 1; padding: 0; }
        .toast-custom .toast-close {
            background: none; border: none; color: rgba(255,255,255,0.7);
            font-size: 1rem; cursor: pointer; padding: 0; line-height: 1; flex-shrink: 0;
        }
        .toast-custom .toast-close:hover { color: #fff; }

        @keyframes toastSlideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes toastSlideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        /* ===== DATATABLES OVERRIDES ===== */
        .dataTables_wrapper .dataTables_length select { border-radius: 6px; border-color: #d1d9e6; font-size: 0.8rem; }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px; border: 1px solid #d1d9e6;
            padding: 0.25rem 0.5rem; font-size: 0.8rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 6px !important; font-size: 0.8rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #1a2332 !important;
        }
        .dataTables_wrapper .dataTables_info { font-size: 0.8rem; }

        /* ===== COMPATIBILITY (Pages UI → Bootstrap 5) ===== */
        .pull-left { float: left; }
        .pull-right { float: right; }
        .no-margin { margin: 0 !important; }
        .no-padding { padding: 0 !important; }
        .semi-bold { font-weight: 600; }
        .hint-text { color: #6c757d; font-size: 0.8rem; }
        .all-caps { text-transform: uppercase; letter-spacing: 0.4px; font-size: 0.75rem; }
        .m-t-5 { margin-top: 5px; } .m-t-10 { margin-top: 10px; } .m-t-15 { margin-top: 15px; }
        .m-t-20 { margin-top: 20px; } .m-t-30 { margin-top: 30px; }
        .m-b-5 { margin-bottom: 5px; } .m-b-10 { margin-bottom: 10px; }
        .m-b-15 { margin-bottom: 15px; } .m-b-20 { margin-bottom: 20px; }
        .m-l-5 { margin-left: 5px; } .m-l-10 { margin-left: 10px; } .m-l-20 { margin-left: 20px; }
        .m-r-5 { margin-right: 5px; } .m-r-10 { margin-right: 10px; } .m-r-20 { margin-right: 20px; }
        .p-t-5 { padding-top: 5px; } .p-t-10 { padding-top: 10px; } .p-t-15 { padding-top: 15px; }
        .p-t-20 { padding-top: 20px; } .p-t-50 { padding-top: 50px; }
        .p-b-5 { padding-bottom: 5px; } .p-b-10 { padding-bottom: 10px; }
        .p-b-15 { padding-bottom: 15px; } .p-b-20 { padding-bottom: 20px; }
        .p-l-5 { padding-left: 5px; } .p-l-10 { padding-left: 10px; } .p-l-15 { padding-left: 15px; }
        .p-l-20 { padding-left: 20px; } .p-l-25 { padding-left: 25px; } .p-l-50 { padding-left: 50px; }
        .p-r-5 { padding-right: 5px; } .p-r-10 { padding-right: 10px; } .p-r-15 { padding-right: 15px; }
        .p-r-20 { padding-right: 20px; } .p-r-25 { padding-right: 25px; } .p-r-50 { padding-right: 50px; }
        .fs-14 { font-size: 14px; }
        .bg-complete-light { background: #e8f4fd; }
        .bg-primary-light { background: #e8f0fe; }
        .bg-warning-light { background: #fff8e1; }
        .text-complete { color: #2196f3; }
        .text-primary { color: var(--accent) !important; }
        .b-r { border-right: 1px solid #dee2e6; }
        .b-dashed { border-style: dashed; }
        .no-border { border: none !important; }
        .widget-loader-bar, .widget-loader-circle, .social-card { margin-bottom: 0; }
        .container-xs-height, .row-xs-height, .col-xs-height { display: block; }
        .col-top { vertical-align: top; }
        .col-bottom { vertical-align: bottom; }
        .sm-p-l-0, .sm-p-r-0, .sm-p-b-5, .sm-p-t-0, .sm-padding-10 { }
        .d32 { width: 32px; height: 32px; }
        .circular { border-radius: 50%; overflow: hidden; }
        .thumbnail-wrapper { display: inline-block; }
        .font-montserrat { font-weight: 600; }
        .scroll-table, .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive .table { min-width: 650px; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1199.98px) {
            .metric-tile .value { font-size: 1.5rem; }
            .metric-tile { padding: 1rem 0.875rem; }
        }
        @media (max-width: 991.98px) {
            .page-sidebar { transform: translateX(-100%); box-shadow: none; }
            .page-sidebar.show { transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,0.2); }
            .sidebar-overlay.show { display: block; }
            .page-container { margin-left: 0; }
            .header .toggle-sidebar { display: inline-flex; }
            .header .brand { display: inline-flex; }
            .page-content-wrapper { padding: 16px; }
            .card-body { padding: 16px; }
            .row { margin-left: -8px; margin-right: -8px; }
            .row > [class*="col-"] { padding-left: 8px; padding-right: 8px; }
        }
        @media (max-width: 767.98px) {
            .page-content-wrapper { padding: 12px; }
            .header { padding: 0 10px; gap: 6px; }
            .header .toggle-sidebar { font-size: 1.1rem; }
            .metric-tile .value { font-size: 1.25rem; }
            .metric-tile { padding: 0.75rem 0.625rem; }
            .metric-tile .label { font-size: 0.6rem; }
            .card-header { padding: 12px 14px; font-size: 0.8rem; }
            .card-body { padding: 12px; }
            .table td, .table th { font-size: 0.75rem; padding: 6px 8px; }
            .table thead th { font-size: 0.6rem; padding: 8px; }
            .btn { font-size: 0.75rem; padding: 0.35rem 0.7rem; }
            .btn-sm { font-size: 0.65rem; padding: 0.2rem 0.45rem; }
            .breadcrumb { font-size: 0.7rem; margin-bottom: 10px; }
            .footer { padding: 8px 12px; font-size: 0.65rem; }
            .footer .copyright {
                flex-direction: column;
                align-items: flex-start;
            }
            h1 { font-size: 1.25rem; }
            h2 { font-size: 1.1rem; }
            .toast-container-custom { max-width: calc(100% - 16px); right: 8px; top: 8px; }
            .toast-custom { font-size: 0.75rem; padding: 10px 12px; }
            .modal-dialog { margin: 0.5rem; }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter { float: none !important; text-align: left !important; margin-bottom: 6px; }
            .dataTables_wrapper .dataTables_filter input { max-width: 100%; }
        }
        @media (max-width: 575.98px) {
            .page-content-wrapper { padding: 8px; }
            .card-body { padding: 10px; }
            .table td, .table th { padding: 4px 6px; }
            .table-responsive .table { min-width: 500px; }
        }
        @media (min-width: 992px) {
            .d-lg-none { display: none !important; }
            .d-lg-inline-flex { display: inline-flex !important; }
        }
        <?php if (isset($extraStyles)) echo $extraStyles; ?>
    </style>
    
    <!-- GLOBALLY INJECTED PREMIUM PROFESSIONAL RESPONSIVE OVERRIDES -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: #f4f7fb !important;
            font-size: 0.84rem;
        }

        /* Sidebar Enchancements */
        .page-sidebar {
            background: #0f172a !important; /* Rich deep slate */
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        .menu-items > li > a {
            font-size: 0.79rem !important;
            font-weight: 500;
        }
        .menu-items > li > a:hover, .menu-items > li.active > a, .menu-items > li.open > a {
            background: #1e293b !important;
            border-radius: 0 8px 8px 0;
            margin-right: 10px;
        }

        /* Modern Cards */
        .card {
            background: #ffffff !important;
            border-radius: 14px !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03), 0 2px 4px -2px rgba(0,0,0,0.02) !important;
            margin-bottom: 18px !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
            overflow: hidden; 
        }
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04) !important;
        }
        .card-header {
            background: rgba(248, 250, 252, 0.5) !important;
            border-bottom: 1px dashed #e2e8f0 !important;
            border-radius: 14px 14px 0 0 !important;
            padding: 14px 18px !important;
        }
        .card-title {
            font-weight: 700 !important;
            font-size: 0.93rem !important;
            color: #0f172a !important;
        }
        .card-body {
            padding: 16px !important;
        }

        /* Modern Tables */
        .scroll-table, .table-responsive-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }
        .table {
            border-collapse: separate !important;
            border-spacing: 0 8px !important;
            border: none !important;
            margin-top: -8px !important;
        }
        .table thead th {
            background: transparent !important;
            border: none !important;
            color: #64748b !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 10px 14px !important;
        }
        .table tbody td {
            background: #f8fafc !important;
            border: none !important;
            padding: 12px !important;
            font-size: 0.82rem !important;
            font-weight: 500 !important;
            color: #334155 !important;
            vertical-align: middle !important;
            transition: background 0.2s !important;
        }
        .table tbody tr:hover td {
            background: #f1f5f9 !important;
        }
        .table tbody td:first-child { border-radius: 12px 0 0 12px !important; }
        .table tbody td:last-child { border-radius: 0 12px 12px 0 !important; }

        /* Buttons Overrides */
        .btn {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 0.42rem 0.88rem !important;
            font-size: 0.76rem !important;
            transition: all 0.2s ease !important;
        }
        .btn-sm { padding: 0.3rem 0.62rem !important; font-size: 0.72rem !important; }
        .btn-primary {
            background: var(--accent) !important;
            border: none !important;
            color: #1a2332 !important;
            box-shadow: 0 4px 6px -1px rgba(var(--accent-rgb), 0.2) !important;
        }
        .btn-primary:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 8px -2px rgba(var(--accent-rgb), 0.3) !important;
            background: #c08e2e !important;
        }

        /* Modals */
        .modal-content {
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25) !important;
            overflow: hidden !important;
        }
        .modal-header {
            background: #fff !important;
            border-bottom: 1px dashed #e2e8f0 !important;
            padding: 18px !important;
        }
        .modal-title { font-weight: 700 !important; font-size: 1.08rem !important; }
        .modal-body {
            background: #f8fafc !important;
            padding: 18px !important;
        }
        .form-label { font-weight: 600 !important; color: #334155 !important; }
        .form-control, .form-select, .select2-selection {
            border-radius: 10px !important;
            border: 1px solid #cbd5e1 !important;
            padding: 0.54rem 0.82rem !important;
            font-size: 0.82rem !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(var(--accent-rgb), 0.15) !important;
        }
        .select2-container--bootstrap-5 .select2-selection { height: calc(3rem + 2px) !important; padding-top: 0.4rem !important; }

        /* Page Headers */
        .breadcrumb {
            font-size: 0.74rem !important;
            font-weight: 500 !important;
            background: #e2e8f0;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-flex;
        }
        .header { border-bottom: none !important; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05) !important; }

        /* Input / Badges Refinement */
        .badge {
            padding: 0.4em 0.8em !important;
            border-radius: 20px !important;
            font-weight: 600 !important;
            letter-spacing: 0.3px;
        }

        /* Responsive Flow Enforcements */
        @media (max-width: 991px) {
            body { font-size: 0.84rem; }
            .card-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px;
            }
            .card-header > div { width: 100%; display: flex; justify-content: flex-end; }
            .page-sidebar { width: min(82vw, 300px); }
        }

        @media (max-width: 768px) {
            .page-content-wrapper { padding: 12px 8px !important; }
            .card { margin-bottom: 16px !important; border-radius: 12px !important; }
            .card-header { padding: 14px 16px !important; }
            .card-title { font-size: 0.94rem !important; }
            .card-body { padding: 14px !important; }
            .table tbody td { padding: 10px !important; font-size: 0.8rem !important; }
            .btn { padding: 0.44rem 0.88rem !important; font-size: 0.78rem !important; }
            .btn-sm { padding: 0.32rem 0.66rem !important; font-size: 0.72rem !important; }
            .form-control, .form-select, .select2-selection {
                padding: 0.56rem 0.8rem !important;
                font-size: 0.82rem !important;
            }
            
            /* Responsive table scaling ensures no hidden cells */
            .table-responsive-wrapper {
                border-radius: 12px;
                box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
            }
            
            /* Specific fix for inputs overlapping */
            .row > [class^="col-"] { margin-bottom: 12px; }
            
            /* Top Navigation Tweak for mobile */
            .breadcrumb { width: 100%; white-space: nowrap; overflow-x: auto; font-size: 0.75rem !important; }
        }

        @media (max-width: 575.98px) {
            .header {
                height: 56px;
                padding: 0 8px;
            }
            .header .brand img,
            .header .brand {
                max-height: 32px !important;
                max-width: 92px !important;
            }
            .notification-bell {
                width: 36px;
                height: 36px;
                border-radius: 10px;
            }
            .notification-dropdown {
                width: min(92vw, 360px);
            }
            .breadcrumb {
                padding: 6px 12px;
                font-size: 0.72rem !important;
            }
        }

        /* Specific Blog Professional View Styles */
        .featured-image {
            width: 100%;
            height: auto;
            border-radius: 16px;
            object-fit: cover;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .author-card {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            border: 1px dashed #cbd5e1;
            margin-top: 40px;
        }
        .author-avatar, .comment-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(var(--accent-rgb), 0.1);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .comment-avatar { width: 40px; height: 40px; font-size: 1.2rem; }
        .comment-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .tag-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e2e8f0;
            color: #475569;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        .tag-badge:hover { background: #cbd5e1; color: #1e293b; }
        .blog-content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #334155;
            padding: 10px 0;
        }
        .blog-content h2, .blog-content h3 { color: #0f172a; margin-top: 24px; margin-bottom: 16px; font-weight: 700; }
        .blog-content p { margin-bottom: 1.5rem; }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== TOAST CONTAINER ===== -->
<div class="toast-container-custom" id="toastContainer"></div>

<nav class="page-sidebar" id="mainSidebar">
    <div class="sidebar-header">
        <img src="<?php echo htmlspecialchars($adminBrandLogo); ?>" alt="TPV Construction and Services LTD" class="brand" />
    </div>
    <div class="sidebar-menu">
        <ul class="menu-items">
            <li class="m-t-5 <?php echo navActive('dashboard'); ?>">
                <a href="index.php">
                    <span class="icon-thumbnail"><i class="fas fa-chart-bar"></i></span>
                    <span class="title">Command Center</span>
                    <span class="details"><?php echo isset($activeProjects) ? $activeProjects : '0'; ?> active</span>
                </a>
            </li>
            <li class="<?php echo navOpen('project'); echo navActive('projects') ? ' active' : ''; echo navActive('project_stages') ? ' active' : ''; echo navActive('daily_reports') ? ' active' : ''; echo navActive('project_budget') ? ' active' : ''; echo navActive('project_media') ? ' active' : ''; ?>" data-menu-key="project">
                <a href="javascript:;" onclick="toggleSubmenu(this)">
                    <span class="icon-thumbnail"><i class="fas fa-helmet-safety"></i></span>
                    <span class="title">Projects</span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo navActive('projects'); ?>"><a href="projects.php"><span class="icon-thumbnail">A</span>Active projects</a></li>
                    <li class="<?php echo navActive('project_stages'); ?>"><a href="project_stages.php"><span class="icon-thumbnail">S</span>Project stages</a></li>
                    <li class="<?php echo navActive('daily_reports'); ?>"><a href="daily_reports.php"><span class="icon-thumbnail">DR</span>Daily reports</a></li>
                    <li class="<?php echo navActive('project_budget'); ?>"><a href="project_budget.php"><span class="icon-thumbnail"><i class="fas fa-dollar-sign"></i></span>Project budget</a></li>
                    <li class="<?php echo navActive('project_media'); ?>"><a href="project_media.php"><span class="icon-thumbnail"><i class="fas fa-photo-video"></i></span>Project media</a></li>
                </ul>
            </li>
            <li class="<?php echo navOpen('financial'); echo navActive('invoices') ? ' active' : ''; echo navActive('payments') ? ' active' : ''; echo navActive('expenses') ? ' active' : ''; echo navActive('purchase_orders') ? ' active' : ''; ?>" data-menu-key="financial">
                <a href="javascript:;" onclick="toggleSubmenu(this)">
                    <span class="icon-thumbnail"><i class="fas fa-dollar-sign"></i></span>
                    <span class="title">Financial</span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo navActive('invoices'); ?>"><a href="invoices.php"><span class="icon-thumbnail">IN</span>Invoices</a></li>
                    <li class="<?php echo navActive('payments'); ?>"><a href="payments.php"><span class="icon-thumbnail">PR</span>Payments received</a></li>
                    <li class="<?php echo navActive('expenses'); ?>"><a href="expenses.php"><span class="icon-thumbnail">EX</span>Expenses</a></li>
                    <li class="<?php echo navActive('purchase_orders'); ?>"><a href="purchase_orders.php"><span class="icon-thumbnail">PO</span>Purchase orders</a></li>
                </ul>
            </li>
            <li class="<?php echo navOpen('resource'); echo navActive('equipment') ? ' active' : ''; echo navActive('materials') ? ' active' : ''; echo navActive('suppliers') ? ' active' : ''; echo navActive('maintenance') ? ' active' : ''; ?>" data-menu-key="resource">
                <a href="javascript:;" onclick="toggleSubmenu(this)">
                    <span class="icon-thumbnail"><i class="fas fa-box"></i></span>
                    <span class="title">Resources</span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo navActive('equipment'); ?>"><a href="equipment.php"><span class="icon-thumbnail">EQ</span>Equipment fleet</a></li>
                    <li class="<?php echo navActive('materials'); ?>"><a href="materials.php"><span class="icon-thumbnail">M</span>Materials stock</a></li>
                    <li class="<?php echo navActive('suppliers'); ?>"><a href="suppliers.php"><span class="icon-thumbnail">S</span>Suppliers</a></li>
                    <li class="<?php echo navActive('maintenance'); ?>"><a href="maintenance.php"><span class="icon-thumbnail">MT</span>Maintenance</a></li>
                </ul>
            </li>
            <li class="<?php echo navOpen('workforce'); echo navActive('employees') ? ' active' : ''; echo navActive('timesheets') ? ' active' : ''; ?>" data-menu-key="workforce">
                <a href="javascript:;" onclick="toggleSubmenu(this)">
                    <span class="icon-thumbnail"><i class="fas fa-users"></i></span>
                    <span class="title">Workforce</span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo navActive('employees'); ?>"><a href="employees.php"><span class="icon-thumbnail">E</span>Employees</a></li>
                    <li class="<?php echo navActive('timesheets'); ?>"><a href="timesheets.php"><span class="icon-thumbnail">TS</span>Timesheets</a></li>
                </ul>
            </li>
            <li class="<?php echo navOpen('client'); echo navActive('clients') ? ' active' : ''; echo navActive('communications') ? ' active' : ''; echo navActive('documents') ? ' active' : ''; ?>" data-menu-key="client">
                <a href="javascript:;" onclick="toggleSubmenu(this)">
                    <span class="icon-thumbnail"><i class="fas fa-handshake"></i></span>
                    <span class="title">Clients</span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo navActive('clients'); ?>"><a href="clients.php"><span class="icon-thumbnail">C</span>Client list</a></li>
                    <li class="<?php echo navActive('communications'); ?>"><a href="communications.php"><span class="icon-thumbnail">CM</span>Communications</a></li>
                    <li class="<?php echo navActive('documents'); ?>"><a href="documents.php"><span class="icon-thumbnail">DOC</span>Documents</a></li>
                </ul>
            </li>
            <li class="<?php echo navOpen('blog'); echo navActive('blog_list') ? ' active' : ''; echo navActive('blog_categories') ? ' active' : ''; echo navActive('blog_comments') ? ' active' : ''; ?>" data-menu-key="blog">
                <a href="javascript:;" onclick="toggleSubmenu(this)">
                    <span class="icon-thumbnail"><i class="fas fa-edit"></i></span>
                    <span class="title">Company blog</span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo navActive('blog_list'); ?>"><a href="blog_list.php"><span class="icon-thumbnail">BP</span>All posts</a></li>
                    <li class="<?php echo navActive('blog_categories'); ?>"><a href="blog_categories.php"><span class="icon-thumbnail">CAT</span>Categories</a></li>
                    <li class="<?php echo navActive('blog_comments'); ?>"><a href="blog_comments.php"><span class="icon-thumbnail">COM</span>Comments</a></li>
                </ul>
            </li>
            <li class="<?php echo navActive('documents'); ?>">
                <a href="documents.php">
                    <span class="icon-thumbnail"><i class="fas fa-file-alt"></i></span>
                    <span class="title">Document center</span>
                </a>
            </li>
            <li class="<?php echo navActive('contact_messages'); ?>">
                <a href="contact_messages.php">
                    <span class="icon-thumbnail"><i class="fas fa-inbox"></i></span>
                    <span class="title">Contact Messages</span>
                    <?php if ($unreadContactNotifications > 0): ?>
                        <span class="nav-pill-badge"><?php echo $unreadContactNotifications > 99 ? '99+' : $unreadContactNotifications; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?php echo navActive('quote_requests'); ?>">
                <a href="quote_requests.php">
                    <span class="icon-thumbnail"><i class="fas fa-calculator"></i></span>
                    <span class="title">Quote Requests</span>
                    <?php if ($unreadQuoteNotifications > 0): ?>
                        <span class="nav-pill-badge"><?php echo $unreadQuoteNotifications > 99 ? '99+' : $unreadQuoteNotifications; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?php echo navActive('admin_users'); ?>">
                <a href="admin_users.php">
                    <span class="icon-thumbnail"><i class="fas fa-user-shield"></i></span>
                    <span class="title">Admin Users</span>
                </a>
            </li>
            <li class="<?php echo navActive('homepage_settings'); ?>">
                <a href="homepage_settings.php">
                    <span class="icon-thumbnail"><i class="fas fa-house"></i></span>
                    <span class="title">Homepage</span>
                </a>
            </li>
            <li class="<?php echo (strpos($pageActive, 'service_') === 0 || $pageActive === 'service_manager') ? 'open' : ''; ?>" data-menu-key="services">
                <a href="javascript:;" onclick="return toggleSubmenu(this)">
                    <span class="icon-thumbnail"><i class="fas fa-briefcase"></i></span>
                    <span class="title">Services</span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo navActive('service_manager'); ?>">
                        <a href="service_manager.php">
                            <span class="icon-thumbnail"><i class="fas fa-layer-group"></i></span>
                            <span class="title">Service Manager</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php echo navActive('about_page_settings'); ?>">
                <a href="about_page_settings.php">
                    <span class="icon-thumbnail"><i class="fas fa-address-card"></i></span>
                    <span class="title">About Page</span>
                </a>
            </li>
            <li class="<?php echo navActive('settings'); ?>">
                <a href="settings.php">
                    <span class="icon-thumbnail"><i class="fas fa-cog"></i></span>
                    <span class="title">System settings</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="page-container">
    <div class="header">
        <button class="toggle-sidebar" onclick="toggleSidebar()" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></button>
        <div class="brand"><img src="<?php echo htmlspecialchars($adminBrandLogo); ?>" alt="TPV Construction and Services LTD" /></div>
        <a href="projects.php?action=new" class="btn btn-primary btn-sm d-none d-lg-inline-flex"><i class="fas fa-plus me-1"></i> New project</a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="notification-bell" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="View enquiry notifications">
                    <i class="fas fa-bell"></i>
                    <?php if ($totalEnquiryNotifications > 0): ?>
                        <span class="notification-badge"><?php echo $totalEnquiryNotifications > 99 ? '99+' : $totalEnquiryNotifications; ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="notification-dropdown-header">
                        <h6>New Enquiries</h6>
                        <p>
                            <?php if ($totalEnquiryNotifications > 0): ?>
                                You have <?php echo $totalEnquiryNotifications; ?> unread enquiries.
                            <?php else: ?>
                                No new enquiries right now.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="notification-dropdown-body">
                        <?php if (!empty($recentEnquiryNotifications)): ?>
                            <?php foreach ($recentEnquiryNotifications as $notification): ?>
                                <a class="notification-item" href="<?php echo htmlspecialchars($notification['href']); ?>">
                                    <div class="notification-item-title">
                                        <strong><?php echo htmlspecialchars($notification['name']); ?></strong>
                                        <span class="notification-item-time"><?php echo htmlspecialchars(date('d M, H:i', strtotime($notification['created_at']))); ?></span>
                                    </div>
                                    <p class="notification-item-meta"><?php echo htmlspecialchars($notification['email']); ?></p>
                                    <p class="notification-item-subject"><?php echo htmlspecialchars(mb_strimwidth((string) $notification['meta'], 0, 70, '...')); ?></p>
                                    <p class="notification-item-subject"><strong><?php echo htmlspecialchars(mb_strimwidth((string) $notification['subject'], 0, 70, '...')); ?></strong></p>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="notification-empty">
                                <i class="fas fa-inbox"></i>
                                Your inbox is clear.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="notification-dropdown-footer">
                        <?php if ($totalEnquiryNotifications > 0): ?>
                        <div class="notification-actions">
                            <form method="post" action="notifications_actions.php">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($headerCsrfToken); ?>">
                                <input type="hidden" name="action" value="mark_all_read">
                                <button type="submit" class="btn btn-light">Mark all read</button>
                            </form>
                            <form method="post" action="notifications_actions.php" onsubmit="return confirm('Clear all unread notifications from the bell?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($headerCsrfToken); ?>">
                                <input type="hidden" name="action" value="clear_notifications">
                                <button type="submit" class="btn btn-outline-danger">Clear all</button>
                            </form>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex flex-column gap-2">
                            <a href="contact_messages.php" class="notification-open-link">Open Contact Messages</a>
                            <a href="quote_requests.php" class="notification-open-link">Open Quote Requests</a>
                        </div>
                    </div>
                </div>
            </div>
            <span class="d-none d-lg-inline text-muted small">
                <span class="fw-semibold"><?php echo htmlspecialchars($userName); ?></span>
                <span class="ms-1"><?php echo htmlspecialchars($currentUser['user_type'] ?? ''); ?></span>
            </span>
            <div class="dropdown">
                <button class="profile-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="thumbnail-wrapper d32 circular">
                        <?php if ($headerAvatarUrl !== ''): ?>
                            <img src="<?php echo htmlspecialchars($headerAvatarUrl); ?>" width="32" height="32" style="width:32px;height:32px;object-fit:cover;" alt="Avatar" />
                        <?php else: ?>
                            <span class="admin-avatar-fallback"><?php echo htmlspecialchars($headerAvatarInitials); ?></span>
                        <?php endif; ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="#"><small class="text-muted">Signed in as</small><br><b><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></b></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                    <li><a class="dropdown-item" href="activity.php"><i class="fas fa-clock me-2"></i>My Activity</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-content-wrapper">
        <div class="content">
