<?php
if (!isset($pageActive)) $pageActive = '';
if (!isset($pageTitle)) $pageTitle = 'TPV Construction and Services LTD';
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
function navActive($key) { global $pageActive; return $pageActive === $key ? 'active' : ''; }
function navOpen($key) { global $pageActive; return strpos($pageActive, $key) === 0 ? 'open' : ''; }
// Collect toast + legacy flash messages
$toastSuccess = $_SESSION['toast_success'] ?? $_SESSION['blog_success'] ?? '';
$toastError = $_SESSION['toast_error'] ?? $_SESSION['blog_error'] ?? '';
$toastWarning = $_SESSION['toast_warning'] ?? '';
$toastInfo = $_SESSION['toast_info'] ?? '';
unset($_SESSION['toast_success'], $_SESSION['toast_error'], $_SESSION['toast_warning'], $_SESSION['toast_info']);
unset($_SESSION['blog_success'], $_SESSION['blog_error']);
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
            --sidebar-width: 260px;
            --sidebar-bg: #1a2332;
            --sidebar-hover: #232f41;
            --sidebar-active: #2a3a52;
            --accent: #d4a13e;
            --accent-rgb: 212, 161, 62;
            --topbar-height: 60px;
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
            font-size: 0.875rem;
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
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-header .brand { max-height: 36px; }
        .sidebar-menu { flex: 1; overflow-y: auto; padding: 8px 0; }
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        .menu-items { list-style: none; padding: 0; margin: 0; }
        .menu-items > li > a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.813rem;
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
            width: 30px; height: 30px;
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
            padding: 8px 20px 8px 54px;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            font-size: 0.8rem;
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
            padding: 0 20px;
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
        .header .brand { display: none; }
        .header .profile-dropdown-toggle {
            background: none; border: none; padding: 0; cursor: pointer;
        }
        .header .thumbnail-wrapper { display: inline-block; overflow: hidden; border-radius: 50%; }
        .header .thumbnail-wrapper img { display: block; }

        /* ===== PAGE CONTENT ===== */
        .page-content-wrapper { flex: 1; padding: 20px; }
        .content { max-width: 1600px; }
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

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 16px;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #edf2f7;
            padding: 14px 18px;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 10px 10px 0 0 !important;
        }
        .card-body { padding: 18px; }
        .card-title { font-size: 0.938rem; font-weight: 600; margin: 0; }

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
            font-size: 0.95rem;
        }

        /* Sidebar Enchancements */
        .page-sidebar {
            background: #0f172a !important; /* Rich deep slate */
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        .menu-items > li > a {
            font-size: 0.9rem !important;
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
            border-radius: 16px !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03), 0 2px 4px -2px rgba(0,0,0,0.02) !important;
            margin-bottom: 24px !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
            overflow: hidden; 
        }
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04) !important;
        }
        .card-header {
            background: rgba(248, 250, 252, 0.5) !important;
            border-bottom: 1px dashed #e2e8f0 !important;
            border-radius: 16px 16px 0 0 !important;
            padding: 20px 24px !important;
        }
        .card-title {
            font-weight: 700 !important;
            font-size: 1.15rem !important;
            color: #0f172a !important;
        }
        .card-body {
            padding: 24px !important;
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
            padding: 12px 16px !important;
        }
        .table tbody td {
            background: #f8fafc !important;
            border: none !important;
            padding: 16px !important;
            font-size: 0.95rem !important;
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
            padding: 0.55rem 1.2rem !important;
            transition: all 0.2s ease !important;
        }
        .btn-sm { padding: 0.4rem 0.8rem !important; font-size: 0.85rem !important; }
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
            padding: 24px !important;
        }
        .modal-title { font-weight: 700 !important; font-size: 1.25rem !important; }
        .modal-body {
            background: #f8fafc !important;
            padding: 24px !important;
        }
        .form-label { font-weight: 600 !important; color: #334155 !important; }
        .form-control, .form-select, .select2-selection {
            border-radius: 10px !important;
            border: 1px solid #cbd5e1 !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(var(--accent-rgb), 0.15) !important;
        }
        .select2-container--bootstrap-5 .select2-selection { height: calc(3rem + 2px) !important; padding-top: 0.4rem !important; }

        /* Page Headers */
        .breadcrumb {
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            background: #e2e8f0;
            padding: 8px 16px;
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
            .card-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px;
            }
            .card-header > div { width: 100%; display: flex; justify-content: flex-end; }
        }

        @media (max-width: 768px) {
            .page-content-wrapper { padding: 12px 8px !important; }
            .card { margin-bottom: 16px !important; border-radius: 12px !important; }
            .card-body { padding: 16px !important; }
            .table tbody td { padding: 12px !important; font-size: 0.85rem !important; }
            .btn { padding: 0.5rem 1rem !important; font-size: 0.85rem !important; }
            
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
        <img src="assets/img/logo.png" alt="TPV Construction and Services LTD" class="brand" style="max-height:36px;" />
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
            <li class="<?php echo navOpen('project'); echo navActive('projects') ? ' active' : ''; echo navActive('project_stages') ? ' active' : ''; echo navActive('daily_reports') ? ' active' : ''; echo navActive('project_budget') ? ' active' : ''; echo navActive('project_media') ? ' active' : ''; ?>">
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
            <li class="<?php echo navOpen('financial'); echo navActive('invoices') ? ' active' : ''; echo navActive('payments') ? ' active' : ''; echo navActive('expenses') ? ' active' : ''; echo navActive('purchase_orders') ? ' active' : ''; ?>">
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
            <li class="<?php echo navOpen('resource'); echo navActive('equipment') ? ' active' : ''; echo navActive('materials') ? ' active' : ''; echo navActive('suppliers') ? ' active' : ''; echo navActive('maintenance') ? ' active' : ''; ?>">
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
            <li class="<?php echo navOpen('workforce'); echo navActive('employees') ? ' active' : ''; echo navActive('timesheets') ? ' active' : ''; ?>">
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
            <li class="<?php echo navOpen('client'); echo navActive('clients') ? ' active' : ''; echo navActive('communications') ? ' active' : ''; echo navActive('documents') ? ' active' : ''; ?>">
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
            <li class="<?php echo navOpen('blog'); echo navActive('blog_list') ? ' active' : ''; echo navActive('blog_categories') ? ' active' : ''; echo navActive('blog_comments') ? ' active' : ''; ?>">
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
        <div class="brand"><img src="assets/img/logo.png" width="78" /></div>
        <a href="projects.php?action=new" class="btn btn-primary btn-sm d-none d-lg-inline-flex"><i class="fas fa-plus me-1"></i> New project</a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="d-none d-lg-inline text-muted small">
                <span class="fw-semibold"><?php echo htmlspecialchars($userName); ?></span>
                <span class="ms-1"><?php echo htmlspecialchars($currentUser['user_type'] ?? ''); ?></span>
            </span>
            <div class="dropdown">
                <button class="profile-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="thumbnail-wrapper d32 circular"><img src="assets/img/profiles/avatar.jpg" width="32" /></span>
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
