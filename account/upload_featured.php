<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';

$auth = new Auth();
$auth->requireAuth(); // ensure logged in

$blog = new Blog();
$response = ['success' => false];

if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
    // Reuse the Blog class upload method
    $result = $blog->uploadFeaturedImage($_FILES['featured_image']);
    if ($result['success']) {
        $response['success'] = true;
        $response['media_id'] = $result['media_id'];
        // Optionally return a URL for preview
        $response['file_url'] = '/uploads/blog/' . $result['filename']; // adjust path as needed
    } else {
        $response['message'] = $result['message'];
    }
} else {
    $response['message'] = 'No file uploaded or upload error.';
}

header('Content-Type: application/json');
echo json_encode($response);