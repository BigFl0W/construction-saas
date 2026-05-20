<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/QuoteRequest.php';

$auth = new Auth();
$auth->requireAuth();
$quoteRequests = new QuoteRequest();
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !$auth->verifyCSRF($_POST['csrf_token'])) {
    $_SESSION['toast_error'] = 'Invalid security token.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'mark_all_read') {
    $contactAffected = 0;
    $quoteAffected = 0;

    if ($db->query("SHOW TABLES LIKE 'contact_submissions'")->fetchColumn()) {
        $contactAffected = $db->query("UPDATE contact_submissions SET status = 'read' WHERE status = 'unread'")->rowCount();
    }
    $quoteAffected = $db->query("UPDATE quote_requests SET status = 'read' WHERE status = 'unread'")->rowCount();
    $affected = $contactAffected + $quoteAffected;
    $_SESSION['toast_success'] = $affected > 0 ? $affected . ' enquiry notification' . ($affected === 1 ? ' was' : 's were') . ' marked as read.' : 'There were no unread enquiry notifications to mark as read.';
} elseif ($action === 'clear_notifications') {
    $contactAffected = 0;
    $quoteAffected = 0;

    if ($db->query("SHOW TABLES LIKE 'contact_submissions'")->fetchColumn()) {
        $contactAffected = $db->query("UPDATE contact_submissions SET status = 'archived' WHERE status = 'unread'")->rowCount();
    }
    $quoteAffected = $db->query("UPDATE quote_requests SET status = 'archived' WHERE status = 'unread'")->rowCount();
    $affected = $contactAffected + $quoteAffected;
    $_SESSION['toast_success'] = $affected > 0 ? $affected . ' enquiry notification' . ($affected === 1 ? ' was' : 's were') . ' cleared from the notification center.' : 'There were no unread enquiry notifications to clear.';
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
