<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$functions = Functions::getInstance();

$postId = $_GET['id'] ?? 0;

if (!$postId) {
    $_SESSION['toast_error'] = 'Invalid post ID';
    header('Location: blog_list.php');
    exit;
}

$result = $blog->approvePost($postId);

if ($result['success']) {
    $functions->logActivity($auth->getUserId(), 'blog_approve', "Approved blog post ID: $postId");
    $_SESSION['toast_success'] = 'Blog post approved and published.';
} else {
    $_SESSION['toast_error'] = $result['message'] ?? 'Failed to approve post.';
}

header('Location: blog_list.php');
exit;
