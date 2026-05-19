<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();

$functions = Functions::getInstance();
$db = Database::getInstance();

$response = ['success' => false];

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    
    // Validate
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        $response['error'] = 'Invalid image type. Allowed: JPG, PNG, GIF, WEBP.';
    } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $response['error'] = 'Image too large (max 5MB).';
    } else {
        // Create content subdirectory if needed
        $uploadDir = dirname(__DIR__) . '/uploads/blog/content/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Optional: insert into media table for tracking
            $uuid = $functions->generateUUID();
            $sql = "INSERT INTO media (uuid, user_id, filename, original_filename, file_path, file_size, mime_type, media_type, created_at)
                    VALUES (:uuid, :user_id, :filename, :original, :path, :size, :type, 'image', NOW())";
            $db->query($sql, [
                'uuid' => $uuid,
                'user_id' => $_SESSION['construction_auth']['user_id'],
                'filename' => $filename,
                'original' => $file['name'],
                'path' => 'uploads/blog/content/' . $filename,
                'size' => $file['size'],
                'type' => $file['type']
            ]);
            
            $response['success'] = true;
            // Return the public URL (adjust base path to match your setup)
            $response['url'] = '/uploads/blog/content/' . $filename;
        } else {
            $response['error'] = 'Failed to save image.';
        }
    }
} else {
    $response['error'] = 'No image uploaded.';
}

header('Content-Type: application/json');
echo json_encode($response);