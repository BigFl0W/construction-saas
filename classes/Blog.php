<?php
/**
 * Blog Class
 * Handles all blog-related database operations
 */

require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Blog {
    private $db;
    private $functions;
    private $uploadDir;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
        $this->uploadDir = dirname(__DIR__) . '/uploads/blog/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Create a new blog post
     */
    public function createPost($data, $files = null) {
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Generate UUID
            $uuid = $this->functions->generateUUID();
            
            // Handle featured image upload if provided
            $featuredImageId = null;
            if ($files && isset($files['featured_image_upload']) && $files['featured_image_upload']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadFeaturedImage($files['featured_image_upload']);
                if ($uploadResult['success']) {
                    $featuredImageId = $uploadResult['media_id'];
                }
            } elseif (!empty($data['featured_image_id_select'])) {
                $featuredImageId = $data['featured_image_id_select'];
            }
            
            // Prepare blog post data
            $slug = $this->generateSlug($data['title'], $data['slug'] ?? '');
            
            // Set default published_at if status is published
            $publishedAt = null;
            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $publishedAt = date('Y-m-d H:i:s');
            } elseif (!empty($data['published_at'])) {
                $publishedAt = $data['published_at'];
            }
            
            // Set scheduled_for if status is scheduled
            $scheduledFor = null;
            if ($data['status'] === 'scheduled' && !empty($data['scheduled_for'])) {
                $scheduledFor = $data['scheduled_for'];
            }
            
            // Insert blog post
            $sql = "INSERT INTO blog_posts (
                        uuid, author_type, author_employee_id, author_client_id,
                        title, slug, excerpt, content, featured_image_id,
                        status, comment_status, view_count, published_at, scheduled_for,
                        created_at, updated_at
                    ) VALUES (
                        :uuid, :author_type, :author_employee_id, :author_client_id,
                        :title, :slug, :excerpt, :content, :featured_image_id,
                        :status, :comment_status, :view_count, :published_at, :scheduled_for,
                        NOW(), NOW()
                    )";
            
            $params = [
                'uuid' => $uuid,
                'author_type' => $data['author_type'],
                'author_employee_id' => !empty($data['author_employee_id']) ? $data['author_employee_id'] : null,
                'author_client_id' => !empty($data['author_client_id']) ? $data['author_client_id'] : null,
                'title' => $data['title'],
                'slug' => $slug,
                'excerpt' => $data['excerpt'] ?? null,
                'content' => $data['content'] ?? '',
                'featured_image_id' => $featuredImageId,
                'status' => $data['status'] ?? 'pending_review',
                'comment_status' => $data['comment_status'] ?? 'open',
                'view_count' => 0,
                'published_at' => $publishedAt,
                'scheduled_for' => $scheduledFor
            ];
            
            $this->db->query($sql, $params);
            $postId = $this->db->lastInsertId();
            
            // Handle categories
            if (!empty($data['categories'])) {
                $this->addPostCategories($postId, $data['categories']);
            }
            
            // Handle tags
            if (!empty($data['tags_string'])) {
                $this->addPostTags($postId, $data['tags_string']);
            }
            
            // Commit transaction
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Blog post created successfully',
                'post_id' => $postId,
                'status' => $data['status']
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            error_log("Blog post creation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create blog post: ' . $e->getMessage()
            ];
        }
    }
    
  /**
 * Upload featured image and create media record
 */
public function uploadFeaturedImage($file) {   // <-- changed to public
    try {
        // Generate UUID for media
        $uuid = $this->functions->generateUUID();
        
        // Get file info
        $originalFilename = $file['name'];
        $fileInfo = pathinfo($originalFilename);
        $extension = strtolower($fileInfo['extension']);
        
        // Generate unique filename
        $filename = uniqid() . '_' . $this->functions->createSlug($fileInfo['filename']) . '.' . $extension;
        $filepath = 'uploads/blog/' . $filename;
        $fullPath = $this->uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception("Failed to move uploaded file");
        }
        
        // Get image dimensions if it's an image
        $imageInfo = getimagesize($fullPath);
        $width = $imageInfo[0] ?? null;
        $height = $imageInfo[1] ?? null;
        
        // Insert into media table
        $sql = "INSERT INTO media (
                    uuid, user_id, filename, original_filename, file_path,
                    file_size, mime_type, media_type, width, height,
                    created_at, updated_at
                ) VALUES (
                    :uuid, :user_id, :filename, :original_filename, :file_path,
                    :file_size, :mime_type, :media_type, :width, :height,
                    NOW(), NOW()
                )";
        
        $params = [
            'uuid' => $uuid,
            'user_id' => $_SESSION['construction_auth']['user_id'] ?? 1,
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'file_path' => $filepath,
            'file_size' => $file['size'],
            'mime_type' => $file['type'],
            'media_type' => 'image',
            'width' => $width,
            'height' => $height
        ];
        
        $this->db->query($sql, $params);
        $mediaId = $this->db->lastInsertId();
        
        return [
            'success' => true,
            'media_id' => $mediaId,
            'filepath' => $filepath,
            'filename' => $filename   // optional, for preview URL
        ];
        
    } catch (Exception $e) {
        error_log("Featured image upload error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
    
    /**
     * Add categories to a post
     */
    private function addPostCategories($postId, $categories) {
        $sql = "INSERT INTO blog_post_categories (post_id, category_id) VALUES (:post_id, :category_id)";
        
        foreach ($categories as $categoryId) {
            $this->db->query($sql, [
                'post_id' => $postId,
                'category_id' => $categoryId
            ]);
        }
    }
    
    /**
     * Add tags to a post (create new tags if they don't exist)
     */
    private function addPostTags($postId, $tagsString) {
        // Split tags by comma
        $tagNames = array_map('trim', explode(',', $tagsString));
        
        foreach ($tagNames as $tagName) {
            if (empty($tagName)) continue;
            
            // Check if tag exists
            $sql = "SELECT id FROM blog_tags WHERE name = :name";
            $stmt = $this->db->query($sql, ['name' => $tagName]);
            $tag = $stmt->fetch();
            
            if ($tag) {
                $tagId = $tag['id'];
                // Update usage count
                $this->db->query("UPDATE blog_tags SET usage_count = usage_count + 1 WHERE id = :id", ['id' => $tagId]);
            } else {
                // Create new tag
                $slug = $this->functions->createSlug($tagName);
                $sql = "INSERT INTO blog_tags (name, slug, usage_count, created_at, updated_at) 
                        VALUES (:name, :slug, 1, NOW(), NOW())";
                $this->db->query($sql, [
                    'name' => $tagName,
                    'slug' => $slug
                ]);
                $tagId = $this->db->lastInsertId();
            }
            
            // Link tag to post
            $sql = "INSERT INTO blog_post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)";
            $this->db->query($sql, [
                'post_id' => $postId,
                'tag_id' => $tagId
            ]);
        }
    }
    
    /**
     * Generate slug from title
     */
    private function generateSlug($title, $customSlug = '') {
        if (!empty($customSlug)) {
            $slug = $this->functions->createSlug($customSlug);
        } else {
            $slug = $this->functions->createSlug($title);
        }
        
        // Check if slug exists and make it unique
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Check if slug exists
     */
    private function slugExists($slug) {
        $sql = "SELECT id FROM blog_posts WHERE slug = :slug";
        $stmt = $this->db->query($sql, ['slug' => $slug]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Get all blog posts with filters
     */
    public function getPosts($filters = [], $limit = 20, $offset = 0) {
        $sql = "SELECT p.*, 
                       CONCAT(e.first_name, ' ', e.last_name) as employee_author,
                       c.company_name as client_author,
                       (SELECT COUNT(*) FROM blog_comments WHERE post_id = p.id AND status = 'approved') as comment_count
                FROM blog_posts p
                LEFT JOIN employees e ON p.author_employee_id = e.id
                LEFT JOIN clients c ON p.author_client_id = c.id
                WHERE p.deleted_at IS NULL";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.id IN (SELECT post_id FROM blog_post_categories WHERE category_id = :category_id)";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE :search OR p.content LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get posts error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get pending review posts
     */
    public function getPendingPosts() {
        return $this->getPosts(['status' => 'pending_review']);
    }
    
    /**
     * Approve a blog post
     */
    public function approvePost($postId) {
        try {
            $sql = "UPDATE blog_posts SET 
                    status = 'published',
                    published_at = COALESCE(published_at, NOW()),
                    updated_at = NOW()
                    WHERE id = :id AND status = 'pending_review'";
            
            $this->db->query($sql, ['id' => $postId]);
            
            return ['success' => true, 'message' => 'Post approved successfully'];
        } catch (Exception $e) {
            error_log("Approve post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve post'];
        }
    }
    
    /**
     * Delete a blog post (soft delete)
     */
    public function deletePost($postId) {
        try {
            $sql = "UPDATE blog_posts SET deleted_at = NOW() WHERE id = :id";
            $this->db->query($sql, ['id' => $postId]);
            
            return ['success' => true, 'message' => 'Post deleted successfully'];
        } catch (Exception $e) {
            error_log("Delete post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete post'];
        }
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        try {
            $sql = "SELECT * FROM blog_categories ORDER BY name";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get categories error: " . $e->getMessage());
            return [];
        }
    }


     /**
     * Get comments with optional filters
     */
    public function getComments($filters = [], $limit = 50, $offset = 0) {
        $sql = "SELECT c.*, p.title as post_title, p.slug as post_slug,
                       CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       cl.company_name as client_name,
                       cl.contact_person as client_contact
                FROM blog_comments c
                LEFT JOIN blog_posts p ON c.post_id = p.id
                LEFT JOIN employees e ON c.author_employee_id = e.id
                LEFT JOIN clients cl ON c.author_client_id = cl.id
                WHERE c.deleted_at IS NULL";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['post_id'])) {
            $sql .= " AND c.post_id = :post_id";
            $params['post_id'] = $filters['post_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.content LIKE :search OR c.author_name LIKE :search OR c.author_email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get comments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update comment status
     */
    public function updateCommentStatus($commentId, $status) {
        $allowed = ['pending', 'approved', 'spam', 'trash'];
        if (!in_array($status, $allowed)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        try {
            $sql = "UPDATE blog_comments SET status = :status, updated_at = NOW() WHERE id = :id";
            $this->db->query($sql, ['id' => $commentId, 'status' => $status]);
            return ['success' => true, 'message' => 'Comment status updated'];
        } catch (Exception $e) {
            error_log("Update comment status error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    /**
     * Add a reply to a comment
     */
    public function addReply($parentId, $content, $authorEmployeeId = null, $authorClientId = null) {
        try {
            // Get the post_id from parent comment
            $stmt = $this->db->query("SELECT post_id FROM blog_comments WHERE id = :id", ['id' => $parentId]);
            $parent = $stmt->fetch();
            if (!$parent) {
                return ['success' => false, 'message' => 'Parent comment not found'];
            }
            
            $uuid = $this->functions->generateUUID();
            
            $sql = "INSERT INTO blog_comments (
                        uuid, post_id, parent_id, author_employee_id, author_client_id,
                        content, status, created_at, updated_at
                    ) VALUES (
                        :uuid, :post_id, :parent_id, :author_employee_id, :author_client_id,
                        :content, 'approved', NOW(), NOW()
                    )";
            
            $params = [
                'uuid' => $uuid,
                'post_id' => $parent['post_id'],
                'parent_id' => $parentId,
                'author_employee_id' => $authorEmployeeId,
                'author_client_id' => $authorClientId,
                'content' => $content
            ];
            
            $this->db->query($sql, $params);
            return ['success' => true, 'message' => 'Reply added', 'comment_id' => $this->db->lastInsertId()];
        } catch (Exception $e) {
            error_log("Add reply error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add reply'];
        }
    }

    /**
     * Soft delete a comment
     */
    public function deleteComment($commentId) {
        try {
            $sql = "UPDATE blog_comments SET deleted_at = NOW() WHERE id = :id";
            $this->db->query($sql, ['id' => $commentId]);
            return ['success' => true, 'message' => 'Comment deleted'];
        } catch (Exception $e) {
            error_log("Delete comment error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete comment'];
        }
    }
}