<?php
/**
 * Blog Class
 * Central blog data access for admin and public pages.
 */

require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';
require_once dirname(__DIR__) . '/classes/Mailer.php';

class Blog
{
    private $db;
    private $functions;
    private $uploadDir;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
        $this->uploadDir = dirname(__DIR__) . '/uploads/blog/';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function createPost($data, $files = null)
    {
        try {
            $this->db->beginTransaction();

            $postData = $this->normalizePostData($data);
            $postData['uuid'] = $this->functions->generateUUID();
            $postData['slug'] = $this->generateSlug($postData['title'], $postData['slug']);
            $postData['featured_image_id'] = $this->resolveFeaturedImageId($postData, $files);

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

            $this->db->query($sql, [
                'uuid' => $postData['uuid'],
                'author_type' => $postData['author_type'],
                'author_employee_id' => $postData['author_employee_id'],
                'author_client_id' => $postData['author_client_id'],
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'featured_image_id' => $postData['featured_image_id'],
                'status' => $postData['status'],
                'comment_status' => $postData['comment_status'],
                'view_count' => 0,
                'published_at' => $postData['published_at'],
                'scheduled_for' => $postData['scheduled_for'],
            ]);

            $postId = (int) $this->db->lastInsertId();
            $this->syncPostCategories($postId, $postData['categories']);
            $this->syncPostTags($postId, $postData['tags_string']);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Blog post created successfully.',
                'post_id' => $postId,
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Blog post creation error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create blog post: ' . $e->getMessage(),
            ];
        }
    }

    public function updatePost($postId, $data, $files = null)
    {
        try {
            $existingPost = $this->getPostById((int) $postId);
            if (!$existingPost) {
                throw new Exception('Post not found.');
            }

            $this->db->beginTransaction();

            $postData = $this->normalizePostData($data);
            $postData['slug'] = $this->generateSlug($postData['title'], $postData['slug'], (int) $postId);
            $postData['featured_image_id'] = $this->resolveFeaturedImageId(
                $postData,
                $files,
                isset($existingPost['featured_image_id']) ? (int) $existingPost['featured_image_id'] : null
            );

            $sql = "UPDATE blog_posts SET
                        author_type = :author_type,
                        author_employee_id = :author_employee_id,
                        author_client_id = :author_client_id,
                        title = :title,
                        slug = :slug,
                        excerpt = :excerpt,
                        content = :content,
                        featured_image_id = :featured_image_id,
                        status = :status,
                        comment_status = :comment_status,
                        published_at = :published_at,
                        scheduled_for = :scheduled_for,
                        updated_at = NOW()
                    WHERE id = :id AND deleted_at IS NULL";

            $this->db->query($sql, [
                'id' => (int) $postId,
                'author_type' => $postData['author_type'],
                'author_employee_id' => $postData['author_employee_id'],
                'author_client_id' => $postData['author_client_id'],
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'featured_image_id' => $postData['featured_image_id'],
                'status' => $postData['status'],
                'comment_status' => $postData['comment_status'],
                'published_at' => $postData['published_at'],
                'scheduled_for' => $postData['scheduled_for'],
            ]);

            $this->syncPostCategories((int) $postId, $postData['categories']);
            $this->syncPostTags((int) $postId, $postData['tags_string']);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Blog post updated successfully.',
                'post_id' => (int) $postId,
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Blog post update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update blog post: ' . $e->getMessage(),
            ];
        }
    }

    public function uploadFeaturedImage($file)
    {
        try {
            $uuid = $this->functions->generateUUID();
            $originalFilename = $file['name'];
            $fileInfo = pathinfo($originalFilename);
            $extension = strtolower($fileInfo['extension'] ?? '');
            $baseName = $fileInfo['filename'] ?? 'blog-image';
            $filename = uniqid('blog_', true) . '_' . $this->functions->createSlug($baseName) . '.' . $extension;
            $filepath = 'uploads/blog/' . $filename;
            $fullPath = $this->uploadDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                throw new Exception('Failed to move uploaded file.');
            }

            $imageInfo = @getimagesize($fullPath);
            $width = $imageInfo[0] ?? null;
            $height = $imageInfo[1] ?? null;

            $sql = "INSERT INTO media (
                        uuid, user_id, filename, original_filename, file_path,
                        file_size, mime_type, media_type, width, height,
                        created_at, updated_at
                    ) VALUES (
                        :uuid, :user_id, :filename, :original_filename, :file_path,
                        :file_size, :mime_type, :media_type, :width, :height,
                        NOW(), NOW()
                    )";

            $this->db->query($sql, [
                'uuid' => $uuid,
                'user_id' => $_SESSION['construction_auth']['user_id'] ?? 1,
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'file_path' => $filepath,
                'file_size' => $file['size'],
                'mime_type' => $file['type'],
                'media_type' => 'image',
                'width' => $width,
                'height' => $height,
            ]);

            return [
                'success' => true,
                'media_id' => (int) $this->db->lastInsertId(),
                'filepath' => $filepath,
                'filename' => $filename,
            ];
        } catch (Exception $e) {
            error_log('Featured image upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getPosts($filters = [], $limit = 20, $offset = 0)
    {
        $sql = "SELECT p.*,
                       CONCAT(e.first_name, ' ', e.last_name) AS employee_author,
                       c.company_name AS client_author,
                       m.file_path AS featured_image_path,
                       (SELECT COUNT(*) FROM blog_comments bc WHERE bc.post_id = p.id AND bc.status = 'approved' AND bc.deleted_at IS NULL) AS comment_count
                FROM blog_posts p
                LEFT JOIN employees e ON p.author_employee_id = e.id
                LEFT JOIN clients c ON p.author_client_id = c.id
                LEFT JOIN media m ON p.featured_image_id = m.id
                WHERE p.deleted_at IS NULL";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND EXISTS (
                        SELECT 1 FROM blog_post_categories pc
                        WHERE pc.post_id = p.id AND pc.category_id = :category_id
                    )";
            $params['category_id'] = (int) $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                        p.title LIKE :search
                        OR p.excerpt LIKE :search
                        OR p.content LIKE :search
                    )";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY COALESCE(p.published_at, p.created_at) DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = (int) $limit;
            $params['offset'] = (int) $offset;
        }

        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Get posts error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPublishedPosts($filters = [], $limit = 12, $offset = 0)
    {
        $filters['status'] = 'published';
        return $this->getPosts($filters, $limit, $offset);
    }

    public function getPostById($postId)
    {
        $sql = "SELECT p.*,
                       CONCAT(e.first_name, ' ', e.last_name) AS employee_author,
                       c.company_name AS client_author,
                       m.file_path AS featured_image_path,
                       m.original_filename AS featured_image_name
                FROM blog_posts p
                LEFT JOIN employees e ON p.author_employee_id = e.id
                LEFT JOIN clients c ON p.author_client_id = c.id
                LEFT JOIN media m ON p.featured_image_id = m.id
                WHERE p.id = :id AND p.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->query($sql, ['id' => (int) $postId]);
        $post = $stmt->fetch();
        if (!$post) {
            return null;
        }

        $post['categories'] = $this->getPostCategories((int) $postId);
        $post['tags'] = $this->getPostTags((int) $postId);
        return $post;
    }

    public function getPostBySlug($slug)
    {
        $sql = "SELECT p.*,
                       CONCAT(e.first_name, ' ', e.last_name) AS employee_author,
                       c.company_name AS client_author,
                       m.file_path AS featured_image_path,
                       m.original_filename AS featured_image_name
                FROM blog_posts p
                LEFT JOIN employees e ON p.author_employee_id = e.id
                LEFT JOIN clients c ON p.author_client_id = c.id
                LEFT JOIN media m ON p.featured_image_id = m.id
                WHERE p.slug = :slug AND p.deleted_at IS NULL AND p.status = 'published'
                LIMIT 1";

        $stmt = $this->db->query($sql, ['slug' => $slug]);
        $post = $stmt->fetch();
        if (!$post) {
            return null;
        }

        $post['categories'] = $this->getPostCategories((int) $post['id']);
        $post['tags'] = $this->getPostTags((int) $post['id']);
        return $post;
    }

    public function getPostCategories($postId)
    {
        $sql = "SELECT c.*
                FROM blog_categories c
                INNER JOIN blog_post_categories pc ON pc.category_id = c.id
                WHERE pc.post_id = :post_id
                ORDER BY c.name";
        return $this->db->query($sql, ['post_id' => (int) $postId])->fetchAll();
    }

    public function getPostTags($postId)
    {
        $sql = "SELECT t.*
                FROM blog_tags t
                INNER JOIN blog_post_tags pt ON pt.tag_id = t.id
                WHERE pt.post_id = :post_id
                ORDER BY t.name";
        return $this->db->query($sql, ['post_id' => (int) $postId])->fetchAll();
    }

    public function getCategorySummaries()
    {
        try {
            $sql = "SELECT c.*,
                           COUNT(p.id) AS post_count
                    FROM blog_categories c
                    LEFT JOIN blog_post_categories pc ON pc.category_id = c.id
                    LEFT JOIN blog_posts p ON p.id = pc.post_id AND p.status = 'published' AND p.deleted_at IS NULL
                    GROUP BY c.id
                    ORDER BY post_count DESC, c.name ASC";
            return $this->db->query($sql)->fetchAll();
        } catch (Exception $e) {
            error_log('Get category summaries error: ' . $e->getMessage());
            return [];
        }
    }

    public function getDashboardStats()
    {
        $stats = [
            'total_posts' => 0,
            'published_posts' => 0,
            'draft_posts' => 0,
            'pending_comments' => 0,
        ];

        try {
            $stats['total_posts'] = (int) $this->db->query(
                "SELECT COUNT(*) FROM blog_posts WHERE deleted_at IS NULL"
            )->fetchColumn();
            $stats['published_posts'] = (int) $this->db->query(
                "SELECT COUNT(*) FROM blog_posts WHERE deleted_at IS NULL AND status = 'published'"
            )->fetchColumn();
            $stats['draft_posts'] = (int) $this->db->query(
                "SELECT COUNT(*) FROM blog_posts WHERE deleted_at IS NULL AND status IN ('draft', 'pending_review', 'scheduled', 'archived')"
            )->fetchColumn();
            $stats['pending_comments'] = (int) $this->db->query(
                "SELECT COUNT(*) FROM blog_comments WHERE deleted_at IS NULL AND status = 'pending'"
            )->fetchColumn();
        } catch (Exception $e) {
            error_log('Blog dashboard stats error: ' . $e->getMessage());
        }

        return $stats;
    }

    public function incrementViewCount($postId)
    {
        try {
            $this->db->query(
                "UPDATE blog_posts SET view_count = COALESCE(view_count, 0) + 1 WHERE id = :id AND deleted_at IS NULL",
                ['id' => (int) $postId]
            );
        } catch (Exception $e) {
            error_log('Increment blog view error: ' . $e->getMessage());
        }
    }

    public function getRelatedPosts($postId, $categoryId = null, $limit = 3)
    {
        $params = [
            'post_id' => (int) $postId,
            'limit' => (int) $limit,
        ];

        $sql = "SELECT DISTINCT p.id, p.title, p.slug, p.excerpt, p.created_at, p.published_at,
                       m.file_path AS featured_image_path
                FROM blog_posts p
                LEFT JOIN media m ON p.featured_image_id = m.id";

        if ($categoryId !== null) {
            $sql .= " INNER JOIN blog_post_categories pc ON pc.post_id = p.id";
        }

        $sql .= " WHERE p.id != :post_id
                  AND p.status = 'published'
                  AND p.deleted_at IS NULL";

        if ($categoryId !== null) {
            $sql .= " AND pc.category_id = :category_id";
            $params['category_id'] = (int) $categoryId;
        }

        $sql .= " ORDER BY COALESCE(p.published_at, p.created_at) DESC LIMIT :limit";

        try {
            return $this->db->query($sql, $params)->fetchAll();
        } catch (Exception $e) {
            error_log('Get related posts error: ' . $e->getMessage());
            return [];
        }
    }

    public function approvePost($postId)
    {
        try {
            $this->db->query(
                "UPDATE blog_posts
                 SET status = 'published',
                     published_at = COALESCE(published_at, NOW()),
                     updated_at = NOW()
                 WHERE id = :id AND status = 'pending_review' AND deleted_at IS NULL",
                ['id' => (int) $postId]
            );

            return ['success' => true, 'message' => 'Post approved successfully.'];
        } catch (Exception $e) {
            error_log('Approve post error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve post.'];
        }
    }

    public function deletePost($postId)
    {
        try {
            $this->db->query(
                "UPDATE blog_posts SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id",
                ['id' => (int) $postId]
            );
            return ['success' => true, 'message' => 'Post deleted successfully.'];
        } catch (Exception $e) {
            error_log('Delete post error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete post.'];
        }
    }

    public function getCategories()
    {
        try {
            return $this->db->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
        } catch (Exception $e) {
            error_log('Get categories error: ' . $e->getMessage());
            return [];
        }
    }

    public function getComments($filters = [], $limit = 50, $offset = 0)
    {
        $sql = "SELECT c.*, p.title AS post_title, p.slug AS post_slug,
                       CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                       cl.company_name AS client_name,
                       cl.contact_person AS client_contact
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
            $params['post_id'] = (int) $filters['post_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (c.content LIKE :search OR c.author_name LIKE :search OR c.author_email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = (int) $limit;
        $params['offset'] = (int) $offset;

        try {
            return $this->db->query($sql, $params)->fetchAll();
        } catch (Exception $e) {
            error_log('Get comments error: ' . $e->getMessage());
            return [];
        }
    }

    public function updateCommentStatus($commentId, $status)
    {
        $allowed = ['pending', 'approved', 'spam', 'trash'];
        if (!in_array($status, $allowed, true)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        try {
            $this->db->query(
                "UPDATE blog_comments SET status = :status, updated_at = NOW() WHERE id = :id",
                ['id' => (int) $commentId, 'status' => $status]
            );
            return ['success' => true, 'message' => 'Comment status updated'];
        } catch (Exception $e) {
            error_log('Update comment status error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function addReply($parentId, $content, $authorEmployeeId = null, $authorClientId = null)
    {
        try {
            $stmt = $this->db->query(
                "SELECT c.id, c.post_id, c.author_name, c.author_email, c.content AS parent_content,
                        p.title AS post_title, p.slug AS post_slug
                 FROM blog_comments c
                 LEFT JOIN blog_posts p ON c.post_id = p.id
                 WHERE c.id = :id AND c.deleted_at IS NULL
                 LIMIT 1",
                ['id' => (int) $parentId]
            );
            $parent = $stmt->fetch();
            if (!$parent) {
                return ['success' => false, 'message' => 'Parent comment not found'];
            }

            $this->db->query(
                "INSERT INTO blog_comments (
                    uuid, post_id, parent_id, author_employee_id, author_client_id, content, status, created_at, updated_at
                ) VALUES (
                    :uuid, :post_id, :parent_id, :author_employee_id, :author_client_id, :content, 'approved', NOW(), NOW()
                )",
                [
                    'uuid' => $this->functions->generateUUID(),
                    'post_id' => (int) $parent['post_id'],
                    'parent_id' => (int) $parentId,
                    'author_employee_id' => $authorEmployeeId,
                    'author_client_id' => $authorClientId,
                    'content' => $content,
                ]
            );

            $replyId = (int) $this->db->lastInsertId();
            $notificationStatus = $this->notifyCommentAuthorAboutReply($parent, (string) $content, $authorEmployeeId, $authorClientId);

            return [
                'success' => true,
                'message' => $notificationStatus['message'],
                'comment_id' => $replyId,
                'email_notified' => $notificationStatus['sent'] ?? false,
            ];
        } catch (Exception $e) {
            error_log('Add reply error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add reply'];
        }
    }

    public function deleteComment($commentId)
    {
        try {
            $this->db->query(
                "UPDATE blog_comments SET deleted_at = NOW() WHERE id = :id",
                ['id' => (int) $commentId]
            );
            return ['success' => true, 'message' => 'Comment deleted'];
        } catch (Exception $e) {
            error_log('Delete comment error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete comment'];
        }
    }

    private function normalizePostData(array $data)
    {
        $title = trim((string) ($data['title'] ?? ''));
        $status = (string) ($data['status'] ?? 'draft');
        $publishedAt = !empty($data['published_at']) ? str_replace('T', ' ', (string) $data['published_at']) : null;
        $scheduledFor = !empty($data['scheduled_for']) ? str_replace('T', ' ', (string) $data['scheduled_for']) : null;

        if ($status === 'published' && $publishedAt === null) {
            $publishedAt = date('Y-m-d H:i:s');
        }
        if ($status !== 'scheduled') {
            $scheduledFor = null;
        }
        if ($status !== 'published') {
            $publishedAt = $status === 'archived' ? null : $publishedAt;
        }

        return [
            'author_type' => (string) ($data['author_type'] ?? 'employee'),
            'author_employee_id' => $this->nullIfEmpty($data['author_employee_id'] ?? null),
            'author_client_id' => $this->nullIfEmpty($data['author_client_id'] ?? null),
            'title' => $title,
            'slug' => trim((string) ($data['slug'] ?? '')),
            'excerpt' => trim((string) ($data['excerpt'] ?? '')),
            'content' => (string) ($data['content'] ?? ''),
            'featured_image_id_select' => $this->nullIfEmpty($data['featured_image_id_select'] ?? null),
            'remove_featured_image' => !empty($data['remove_featured_image']),
            'status' => $status,
            'comment_status' => (string) ($data['comment_status'] ?? 'open'),
            'published_at' => $publishedAt,
            'scheduled_for' => $scheduledFor,
            'categories' => array_values(array_unique(array_map('intval', (array) ($data['categories'] ?? [])))),
            'tags_string' => trim((string) ($data['tags_string'] ?? '')),
        ];
    }

    private function resolveFeaturedImageId(array $data, $files = null, $existingId = null)
    {
        if (!empty($data['remove_featured_image'])) {
            return null;
        }

        if ($files && isset($files['featured_image_upload']) && ($files['featured_image_upload']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFeaturedImage($files['featured_image_upload']);
            if ($uploadResult['success']) {
                return (int) $uploadResult['media_id'];
            }
        }

        if (!empty($data['featured_image_id_select'])) {
            return (int) $data['featured_image_id_select'];
        }

        return $existingId;
    }

    private function syncPostCategories($postId, array $categories)
    {
        $this->db->query("DELETE FROM blog_post_categories WHERE post_id = :post_id", ['post_id' => (int) $postId]);

        foreach ($categories as $categoryId) {
            $this->db->query(
                "INSERT INTO blog_post_categories (post_id, category_id) VALUES (:post_id, :category_id)",
                ['post_id' => (int) $postId, 'category_id' => (int) $categoryId]
            );
        }
    }

    private function syncPostTags($postId, $tagsString)
    {
        $this->db->query("DELETE FROM blog_post_tags WHERE post_id = :post_id", ['post_id' => (int) $postId]);

        $tagNames = array_unique(array_filter(array_map('trim', explode(',', (string) $tagsString))));
        foreach ($tagNames as $tagName) {
            $stmt = $this->db->query("SELECT id FROM blog_tags WHERE name = :name LIMIT 1", ['name' => $tagName]);
            $tag = $stmt->fetch();

            if ($tag) {
                $tagId = (int) $tag['id'];
            } else {
                $this->db->query(
                    "INSERT INTO blog_tags (name, slug, usage_count, created_at, updated_at)
                     VALUES (:name, :slug, 1, NOW(), NOW())",
                    [
                        'name' => $tagName,
                        'slug' => $this->functions->createSlug($tagName),
                    ]
                );
                $tagId = (int) $this->db->lastInsertId();
            }

            $this->db->query(
                "INSERT INTO blog_post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)",
                ['post_id' => (int) $postId, 'tag_id' => $tagId]
            );
        }
    }

    private function generateSlug($title, $customSlug = '', $excludeId = null)
    {
        $baseSlug = !empty($customSlug)
            ? $this->functions->createSlug($customSlug)
            : $this->functions->createSlug($title);

        $slug = $baseSlug !== '' ? $baseSlug : 'post';
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT id FROM blog_posts WHERE slug = :slug";
        $params = ['slug' => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = (int) $excludeId;
        }

        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    private function nullIfEmpty($value)
    {
        return ($value === '' || $value === null) ? null : $value;
    }

    private function notifyCommentAuthorAboutReply(array $parentComment, string $replyContent, $authorEmployeeId = null, $authorClientId = null)
    {
        $recipientEmail = trim((string) ($parentComment['author_email'] ?? ''));
        if ($recipientEmail === '' || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return [
                'sent' => false,
                'message' => 'Reply added',
            ];
        }

        $recipientName = trim((string) ($parentComment['author_name'] ?? 'there'));
        if ($recipientName === '') {
            $recipientName = 'there';
        }

        $replyAuthorName = $this->resolveReplyAuthorName($authorEmployeeId, $authorClientId);
        $companyName = trim((string) $this->functions->getSetting('company_name', 'TPV Construction and Services LTD'));
        $companyEmail = trim((string) $this->functions->getSetting('company_email', 'info@tpvconstruction.com.ng'));
        $postTitle = trim((string) ($parentComment['post_title'] ?? 'our blog post'));
        $postUrl = rtrim((string) SITE_URL, '/') . '/post.php?slug=' . urlencode((string) ($parentComment['post_slug'] ?? ''));
        $subject = 'Reply to your comment on "' . $postTitle . '"';

        $body = '<h2 style="margin:0 0 18px;color:#0f172a;">We replied to your blog comment</h2>'
            . '<p style="margin:0 0 16px;color:#334155;line-height:1.7;">Dear <strong>' . htmlspecialchars($recipientName, ENT_QUOTES, 'UTF-8') . '</strong>,</p>'
            . '<p style="margin:0 0 16px;color:#334155;line-height:1.7;"><strong>' . htmlspecialchars($replyAuthorName, ENT_QUOTES, 'UTF-8') . '</strong> from <strong>' . htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') . '</strong> has replied to your comment on <strong>' . htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') . '</strong>.</p>'
            . '<div style="margin:0 0 18px;padding:16px 18px;border:1px solid #e2e8f0;border-radius:14px;background:#f8fafc;">'
            . '<div style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;">Your comment</div>'
            . '<p style="margin:0;color:#475569;line-height:1.7;">' . nl2br(htmlspecialchars((string) ($parentComment['parent_content'] ?? ''), ENT_QUOTES, 'UTF-8')) . '</p>'
            . '</div>'
            . '<div style="margin:0 0 18px;padding:16px 18px;border:1px solid #f3d6d6;border-radius:14px;background:#fff7f7;">'
            . '<div style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#ef4444;">Our reply</div>'
            . '<p style="margin:0;color:#334155;line-height:1.7;">' . nl2br(htmlspecialchars($replyContent, ENT_QUOTES, 'UTF-8')) . '</p>'
            . '</div>'
            . '<p style="margin:0 0 18px;color:#334155;line-height:1.7;">You can continue the conversation by replying directly to this email or by revisiting the article below.</p>'
            . '<p style="margin:0 0 24px;"><a href="' . htmlspecialchars($postUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:12px 22px;border-radius:999px;background:#ef4444;color:#ffffff;font-weight:700;text-decoration:none;">View article</a></p>'
            . '<p style="margin:0;color:#64748b;font-size:13px;line-height:1.7;">Best regards,<br><strong>' . htmlspecialchars($replyAuthorName, ENT_QUOTES, 'UTF-8') . '</strong><br>' . htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') . '</p>';

        $mailer = new Mailer();
        $sent = $mailer->send($recipientEmail, $subject, $body, $companyEmail !== '' ? $companyEmail : null);

        if ($sent) {
            try {
                $this->db->query(
                    "INSERT INTO communications (uuid, direction, type, subject, content, communication_date, attachments, created_at, updated_at)
                     VALUES (:uuid, 'outbound', 'email', :subject, :content, NOW(), :attachments, NOW(), NOW())",
                    [
                        'uuid' => $this->functions->generateUUID(),
                        'subject' => $subject,
                        'content' => "Reply to blog comment\n\nTo: {$recipientName} <{$recipientEmail}>\nPost: {$postTitle}\n\n{$replyContent}",
                        'attachments' => json_encode([
                            'blog_post_slug' => $parentComment['post_slug'] ?? null,
                            'blog_comment_id' => (int) ($parentComment['id'] ?? 0),
                        ]),
                    ]
                );
            } catch (Exception $e) {
                error_log('Blog comment communication log failed: ' . $e->getMessage());
            }

            return [
                'sent' => true,
                'message' => 'Reply added and email notification sent.',
            ];
        }

        return [
            'sent' => false,
            'message' => 'Reply added, but email notification could not be delivered.',
        ];
    }

    private function resolveReplyAuthorName($authorEmployeeId = null, $authorClientId = null)
    {
        if (!empty($authorEmployeeId)) {
            $employee = $this->db->query(
                "SELECT CONCAT(first_name, ' ', last_name) AS full_name
                 FROM employees
                 WHERE id = :id
                 LIMIT 1",
                ['id' => (int) $authorEmployeeId]
            )->fetch();

            $employeeName = trim((string) ($employee['full_name'] ?? ''));
            if ($employeeName !== '') {
                return $employeeName;
            }
        }

        if (!empty($authorClientId)) {
            $client = $this->db->query(
                "SELECT COALESCE(NULLIF(company_name, ''), NULLIF(contact_person, '')) AS display_name
                 FROM clients
                 WHERE id = :id
                 LIMIT 1",
                ['id' => (int) $authorClientId]
            )->fetch();

            $clientName = trim((string) ($client['display_name'] ?? ''));
            if ($clientName !== '') {
                return $clientName;
            }
        }

        return 'TPV Construction Team';
    }
}
