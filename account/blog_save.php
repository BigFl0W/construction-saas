<?php
// blog_save.php - Handles blog post submission
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';

// Check authentication
$auth = new Auth();
$auth->requireAuth();

// Initialize classes
$blog = new Blog();
$functions = Functions::getInstance();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blog_new.php');
    exit;
}

// Verify CSRF token
if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['blog_error'] = 'Invalid security token. Please try again.';
    header('Location: blog_new.php');
    exit;
}

// Prepare data array
$data = [
    'title' => trim($_POST['title'] ?? ''),
    'slug' => trim($_POST['slug'] ?? ''),
    'author_type' => $_POST['author_type'] ?? 'employee',
    'author_employee_id' => $_POST['author_employee_id'] ?? null,
    'author_client_id' => $_POST['author_client_id'] ?? null,
    'excerpt' => trim($_POST['excerpt'] ?? ''),
    'content' => $_POST['content'] ?? '',
    'categories' => $_POST['categories'] ?? [],
    'tags_string' => trim($_POST['tags_string'] ?? ''),
    'status' => 'pending_review', // Force pending review status
    'comment_status' => $_POST['comment_status'] ?? 'open',
    'published_at' => $_POST['published_at'] ?? null,
    'scheduled_for' => $_POST['scheduled_for'] ?? null,
    'featured_image_id_select' => $_POST['featured_image_id_select'] ?? null
];

// Validate required fields
$errors = [];

if (empty($data['title'])) {
    $errors[] = 'Title is required';
}

if (empty($data['content'])) {
    $errors[] = 'Content is required';
}

if (empty($data['categories'])) {
    $errors[] = 'At least one category is required';
}

// Validate author based on type
if ($data['author_type'] === 'employee' && empty($data['author_employee_id'])) {
    $errors[] = 'Please select an employee author';
}

if ($data['author_type'] === 'client' && empty($data['author_client_id'])) {
    $errors[] = 'Please select a client author';
}

// If there are errors, redirect back with error messages
if (!empty($errors)) {
    $_SESSION['blog_errors'] = $errors;
    $_SESSION['blog_form_data'] = $data;
    header('Location: blog_new.php');
    exit;
}

// Create the blog post
$result = $blog->createPost($data, $_FILES);

if ($result['success']) {
    // Log activity
    $functions->logActivity(
        $auth->getUserId(),
        'blog_create',
        "Created new blog post: {$data['title']} (Status: pending_review)"
    );
    
    $_SESSION['blog_success'] = 'Blog post created successfully and is pending review.';
    header('Location: blog_posts.php');
} else {
    $_SESSION['blog_error'] = $result['message'];
    $_SESSION['blog_form_data'] = $data;
    header('Location: blog_new.php');
}
exit;