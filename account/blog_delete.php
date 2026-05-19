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

$result = $blog->deletePost($postId);

if ($result['success']) {
    $functions->logActivity($auth->getUserId(), 'blog_delete', "Deleted blog post ID: $postId");
    $_SESSION['toast_success'] = 'Blog post deleted successfully.';
} else {
    $_SESSION['toast_error'] = $result['message'] ?? 'Failed to delete post.';
}

header('Location: blog_list.php');
exit;
