<?php
// blog_update.php - Update an existing blog post
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$db = Database::getInstance();

// Verify CSRF token
if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['blog_error'] = 'Invalid security token. Please try again.';
    header('Location: blog_edit.php?id=' . $_POST['post_id']);
    exit;
}

$postId = (int)($_POST['post_id'] ?? 0);

if (!$postId) {
    $_SESSION['blog_error'] = 'Invalid post ID';
    header('Location: blog_posts.php');
    exit;
}

function nullIfEmpty($val) {
    return ($val === '' || $val === null) ? null : $val;
}

// Prepare data array
$data = [
    'title' => trim($_POST['title'] ?? ''),
    'slug' => trim($_POST['slug'] ?? ''),
    'author_type' => $_POST['author_type'] ?? 'employee',
    'author_employee_id' => nullIfEmpty($_POST['author_employee_id'] ?? null),
    'author_client_id' => nullIfEmpty($_POST['author_client_id'] ?? null),
    'excerpt' => trim($_POST['excerpt'] ?? ''),
    'content' => $_POST['content'] ?? '',
    'categories' => $_POST['categories'] ?? [],
    'tags_string' => trim($_POST['tags_string'] ?? ''),
    'status' => $_POST['status'] ?? 'draft',
    'comment_status' => $_POST['comment_status'] ?? 'open',
    'published_at' => nullIfEmpty($_POST['published_at'] ?? null),
    'scheduled_for' => nullIfEmpty($_POST['scheduled_for'] ?? null),
    'featured_image_id_select' => nullIfEmpty($_POST['featured_image_id_select'] ?? null)
];

// Handle featured image upload if provided
$featuredImageId = null;
if (!empty($_FILES['featured_image_upload']) && $_FILES['featured_image_upload']['error'] === UPLOAD_ERR_OK) {
    // Upload new image
    $uploadResult = $blog->uploadFeaturedImage($_FILES['featured_image_upload']);
    if ($uploadResult['success']) {
        $featuredImageId = $uploadResult['media_id'];
    }
} elseif (!empty($_POST['featured_image_id_select'])) {
    $featuredImageId = $_POST['featured_image_id_select'];
}

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
    header('Location: blog_edit.php?id=' . $postId);
    exit;
}

try {
    // Begin transaction
    $db->beginTransaction();
    
    // Generate slug if empty
    if (empty($data['slug'])) {
        $functions = Functions::getInstance();
        $baseSlug = $functions->createSlug($data['title']);
        
        // Check if slug exists and make it unique
        $slug = $baseSlug;
        $counter = 1;
        while (slugExistsForEdit($db, $slug, $postId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $slug;
    }
    
    // Handle published_at and scheduled_for based on status
    $publishedAt = null;
    $scheduledFor = null;
    
    if ($data['status'] === 'published') {
        $publishedAt = !empty($data['published_at']) ? $data['published_at'] : date('Y-m-d H:i:s');
    } elseif ($data['status'] === 'scheduled' && !empty($data['scheduled_for'])) {
        $scheduledFor = $data['scheduled_for'];
    }
    
    // Update blog post
    $sql = "UPDATE blog_posts SET
                title = :title,
                slug = :slug,
                author_type = :author_type,
                author_employee_id = :author_employee_id,
                author_client_id = :author_client_id,
                excerpt = :excerpt,
                content = :content,
                featured_image_id = :featured_image_id,
                status = :status,
                comment_status = :comment_status,
                published_at = :published_at,
                scheduled_for = :scheduled_for,
                updated_at = NOW()
            WHERE id = :id";
    
    $params = [
        'id' => $postId,
        'title' => $data['title'],
        'slug' => $data['slug'],
        'author_type' => $data['author_type'],
        'author_employee_id' => $data['author_employee_id'],
        'author_client_id' => $data['author_client_id'],
        'excerpt' => $data['excerpt'],
        'content' => $data['content'],
        'featured_image_id' => $featuredImageId,
        'status' => $data['status'],
        'comment_status' => $data['comment_status'],
        'published_at' => $publishedAt,
        'scheduled_for' => $scheduledFor
    ];
    
    $db->query($sql, $params);
    
    // Update categories
    $db->query("DELETE FROM blog_post_categories WHERE post_id = :post_id", ['post_id' => $postId]);
    if (!empty($data['categories'])) {
        $catIds = array_unique(array_map('intval', (array)$data['categories']));
        $catSql = "INSERT INTO blog_post_categories (post_id, category_id) VALUES (:post_id, :category_id)";
        foreach ($catIds as $categoryId) {
            $db->query($catSql, [
                'post_id' => $postId,
                'category_id' => $categoryId
            ]);
        }
    }
    
    // Update tags
    $db->query("DELETE FROM blog_post_tags WHERE post_id = :post_id", ['post_id' => $postId]);
    if (!empty($data['tags_string'])) {
        $tagNames = array_map('trim', explode(',', $data['tags_string']));
        
        foreach ($tagNames as $tagName) {
            if (empty($tagName)) continue;
            
            // Check if tag exists
            $tagSql = "SELECT id FROM blog_tags WHERE name = :name";
            $tagStmt = $db->query($tagSql, ['name' => $tagName]);
            $tag = $tagStmt->fetch();
            
            if ($tag) {
                $tagId = $tag['id'];
            } else {
                // Create new tag
                $functions = Functions::getInstance();
                $slug = $functions->createSlug($tagName);
                $insertSql = "INSERT INTO blog_tags (name, slug, usage_count, created_at, updated_at) 
                              VALUES (:name, :slug, 1, NOW(), NOW())";
                $db->query($insertSql, [
                    'name' => $tagName,
                    'slug' => $slug
                ]);
                $tagId = $db->lastInsertId();
            }
            
            // Link tag to post
            $linkSql = "INSERT INTO blog_post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)";
            $db->query($linkSql, [
                'post_id' => $postId,
                'tag_id' => $tagId
            ]);
        }
    }
    
    // Commit transaction
    $db->commit();
    
    // Log activity
    $functions = Functions::getInstance();
    $functions->logActivity(
        $auth->getUserId(),
        'blog_update',
        "Updated blog post: {$data['title']} (ID: $postId)"
    );
    
    $_SESSION['blog_success'] = 'Blog post updated successfully.';
    header('Location: blog_view.php?id=' . $postId);
    exit;
    
} catch (Exception $e) {
    $db->rollback();
    error_log("Blog update error: " . $e->getMessage());
    $_SESSION['blog_error'] = 'Failed to update blog post: ' . $e->getMessage();
    $_SESSION['blog_form_data'] = $data;
    header('Location: blog_edit.php?id=' . $postId);
    exit;
}

// Helper function to check if slug exists for edit
function slugExistsForEdit($db, $slug, $excludeId) {
    $sql = "SELECT id FROM blog_posts WHERE slug = :slug AND id != :id";
    $stmt = $db->query($sql, ['slug' => $slug, 'id' => $excludeId]);
    return $stmt->rowCount() > 0;
}