<?php
// blog_comment_add.php - Add a comment to a blog post
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blog_posts.php');
    exit;
}

// Verify CSRF token
if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['blog_error'] = 'Invalid security token';
    header('Location: blog_view.php?id=' . $_POST['post_id']);
    exit;
}

$postId = (int)($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if (!$postId || empty($content)) {
    $_SESSION['blog_error'] = 'Invalid comment data';
    header('Location: blog_view.php?id=' . $postId);
    exit;
}

// Get current user
$currentUser = $auth->getUserData();

// Determine author type and ID
$authorEmployeeId = null;
$authorClientId = null;
$authorName = null;
$authorEmail = null;

if ($currentUser['user_type'] === 'employee' && isset($currentUser['employee_id'])) {
    $authorEmployeeId = $currentUser['employee_id'];
} elseif ($currentUser['user_type'] === 'client' && isset($currentUser['client_id'])) {
    $authorClientId = $currentUser['client_id'];
} else {
    // For users without employee/client link, use name from user account
    $authorName = $currentUser['first_name'] . ' ' . $currentUser['last_name'];
    $authorEmail = $currentUser['email'];
}

// Insert comment
$sql = "INSERT INTO blog_comments (
            uuid, post_id, author_name, author_email, 
            author_employee_id, author_client_id, content, status,
            ip_address, user_agent, created_at, updated_at
        ) VALUES (
            UUID(), :post_id, :author_name, :author_email,
            :author_employee_id, :author_client_id, :content, 'pending',
            :ip, :user_agent, NOW(), NOW()
        )";

try {
    $db->query($sql, [
        'post_id' => $postId,
        'author_name' => $authorName,
        'author_email' => $authorEmail,
        'author_employee_id' => $authorEmployeeId,
        'author_client_id' => $authorClientId,
        'content' => $content,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    
    $_SESSION['blog_success'] = 'Your comment has been submitted and is pending approval.';
} catch (Exception $e) {
    error_log("Comment add error: " . $e->getMessage());
    $_SESSION['blog_error'] = 'Failed to add comment. Please try again.';
}

header('Location: blog_view.php?id=' . $postId);
exit;