<?php
// blog_view.php - View a single blog post
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';
require_once '../classes/Functions.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$functions = Functions::getInstance();

try {
    $db = Database::getInstance();
} catch (Exception $e) {
    error_log("Database connection error in blog_view: " . $e->getMessage());
    $_SESSION['blog_error'] = 'Database connection failed';
    header('Location: blog_posts.php');
    exit;
}

// Get post ID from URL
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    $_SESSION['blog_error'] = 'Invalid post ID';
    header('Location: blog_posts.php');
    exit;
}

// Debug: Check if post exists with a simple query
try {
    $checkSql = "SELECT id FROM blog_posts WHERE id = :id";
    $checkStmt = $db->query($checkSql, ['id' => $postId]);
    if ($checkStmt->rowCount() === 0) {
        error_log("Post ID $postId not found in database");
        $_SESSION['blog_error'] = 'Blog post not found';
        header('Location: blog_posts.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Error checking post existence: " . $e->getMessage());
    $_SESSION['blog_error'] = 'Error verifying post';
    header('Location: blog_posts.php');
    exit;
}

// Fetch post details with corrected column names
try {
    $sql = "SELECT p.*, 
                   CONCAT(e.first_name, ' ', e.last_name) as employee_author,
                   e.email as employee_email,
                   e.mobile as employee_phone,
                   c.company_name as client_author,
                   c.contact_person as client_contact,
                   c.email as client_email,
                   c.phone as client_phone,
                   u.username as created_by_username,
                   u.user_type as author_user_type
            FROM blog_posts p
            LEFT JOIN employees e ON p.author_employee_id = e.id
            LEFT JOIN clients c ON p.author_client_id = c.id
            LEFT JOIN users u ON (u.employee_id = e.id OR u.client_id = c.id)
            WHERE p.id = :id AND p.deleted_at IS NULL";
    
    $stmt = $db->query($sql, ['id' => $postId]);
    $post = $stmt->fetch();
    
    if (!$post) {
        error_log("Post ID $postId not found after join query");
        $_SESSION['blog_error'] = 'Blog post not found';
        header('Location: blog_posts.php');
        exit;
    }
    
    // Update view count
    $db->query("UPDATE blog_posts SET view_count = view_count + 1 WHERE id = :id", ['id' => $postId]);
    
    // Fetch categories for this post
    $catSql = "SELECT c.* FROM blog_categories c
               JOIN blog_post_categories pc ON c.id = pc.category_id
               WHERE pc.post_id = :post_id
               ORDER BY c.name";
    $catStmt = $db->query($catSql, ['post_id' => $postId]);
    $categories = $catStmt->fetchAll();
    
    // Fetch tags for this post
    $tagSql = "SELECT t.* FROM blog_tags t
               JOIN blog_post_tags pt ON t.id = pt.tag_id
               WHERE pt.post_id = :post_id
               ORDER BY t.name";
    $tagStmt = $db->query($tagSql, ['post_id' => $postId]);
    $tags = $tagStmt->fetchAll();
    
    // Fetch approved comments for this post
    $commentSql = "SELECT c.*, 
                          CONCAT(e.first_name, ' ', e.last_name) as employee_commenter,
                          cl.company_name as client_commenter,
                          cl.contact_person as client_contact_person
                   FROM blog_comments c
                   LEFT JOIN employees e ON c.author_employee_id = e.id
                   LEFT JOIN clients cl ON c.author_client_id = cl.id
                   WHERE c.post_id = :post_id 
                   AND c.status = 'approved'
                   AND c.deleted_at IS NULL
                   ORDER BY c.created_at DESC";
    $commentStmt = $db->query($commentSql, ['post_id' => $postId]);
    $comments = $commentStmt->fetchAll();
    
    // Fetch related posts (same categories)
    if (!empty($categories)) {
        $catIds = array_column($categories, 'id');
        $catPlaceholders = implode(',', array_fill(0, count($catIds), '?'));
        
        $relatedSql = "SELECT DISTINCT p.id, p.title, p.slug, p.excerpt, p.created_at,
                              CONCAT(e.first_name, ' ', e.last_name) as author_name
                       FROM blog_posts p
                       LEFT JOIN blog_post_categories pc ON p.id = pc.post_id
                       LEFT JOIN employees e ON p.author_employee_id = e.id
                       WHERE pc.category_id IN ($catPlaceholders)
                       AND p.id != ?
                       AND p.status = 'published'
                       AND p.deleted_at IS NULL
                       ORDER BY p.created_at DESC
                       LIMIT 5";
        
        $params = array_merge($catIds, [$postId]);
        $relatedStmt = $db->query($relatedSql, $params);
        $relatedPosts = $relatedStmt->fetchAll();
    } else {
        $relatedPosts = [];
    }
    
    // Get previous and next post
    $prevSql = "SELECT id, title FROM blog_posts 
                WHERE id < ? AND status = 'published' AND deleted_at IS NULL 
                ORDER BY id DESC LIMIT 1";
    $prevStmt = $db->query($prevSql, [$postId]);
    $prevPost = $prevStmt->fetch();
    
    $nextSql = "SELECT id, title FROM blog_posts 
                WHERE id > ? AND status = 'published' AND deleted_at IS NULL 
                ORDER BY id ASC LIMIT 1";
    $nextStmt = $db->query($nextSql, [$postId]);
    $nextPost = $nextStmt->fetch();
    
} catch (Exception $e) {
    error_log("Blog view error: " . $e->getMessage());
    error_log("SQL State: " . (isset($stmt) ? 'Query executed' : 'Query failed'));
    $_SESSION['blog_error'] = 'Error loading blog post: ' . $e->getMessage();
    header('Location: blog_posts.php');
    exit;
}

// Get current user
$currentUser = $auth->getUserData();

// Get featured image if exists
$featuredImage = null;
if ($post['featured_image_id']) {
    try {
        $imgSql = "SELECT file_path, original_filename, filename FROM media WHERE id = :id";
        $imgStmt = $db->query($imgSql, ['id' => $post['featured_image_id']]);
        $featuredImage = $imgStmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching featured image: " . $e->getMessage());
    }
}

$pageActive = 'blog_view';
$pageTitle = 'TPV Construction and Services LTD · View Post';
require 'inc/admin_header.php';
?>
            <!-- Breadcrumb -->
            <div class="container-fluid p-l-25 p-r-25">
                <div class="inner">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="blog_posts.php">Blog</a></li>
                        <li class="breadcrumb-item active">View Post</li>
                    </ol>
                </div>
            </div>

            <?php
            // Messages forwarded to toast system via admin_header.php
            ?>

            <!-- Main Content -->
            <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                <div class="row">
                    <!-- Main Post Column -->
                    <div class="col-lg-8">
                        <!-- Post Header -->
                        <div class="card card-default mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h1 class="h2 mb-0"><?php echo htmlspecialchars($post['title']); ?></h1>
                                    <span class="status-badge status-<?php echo str_replace('_', '-', $post['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $post['status'])); ?>
                                    </span>
                                </div>
                                
                                <!-- Post Meta -->
                                <div class="d-flex flex-wrap align-items-center text-muted mb-4">
                                    <div class="me-3">
                                        <i class="fas fa-user me-1"></i>
                                        <?php 
                                        if ($post['author_type'] === 'employee') {
                                            echo htmlspecialchars($post['employee_author'] ?? 'Employee');
                                        } else {
                                            echo htmlspecialchars($post['client_author'] ?? 'Client');
                                        }
                                        ?>
                                    </div>
                                    <div class="me-3">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo $functions->formatDate($post['created_at'], 'F j, Y'); ?>
                                    </div>
                                    <div class="me-3">
                                        <i class="fas fa-eye me-1"></i>
                                        <?php echo number_format($post['view_count']); ?> views
                                    </div>
                                    <div>
                                        <i class="fas fa-comment me-1"></i>
                                        <?php echo count($comments); ?> comments
                                    </div>
                                </div>
                                
                                <!-- Categories and Tags -->
                                <div class="mb-4">
                                    <?php foreach ($categories as $cat): ?>
                                        <a href="blog_posts.php?category=<?php echo $cat['id']; ?>" class="badge bg-primary text-decoration-none me-1">
                                            <i class="fas fa-folder me-1"></i><?php echo htmlspecialchars($cat['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                    
                                    <?php foreach ($tags as $tag): ?>
                                        <a href="blog_posts.php?tag=<?php echo $tag['id']; ?>" class="tag-badge">
                                            <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($tag['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Featured Image -->
                                <?php if ($featuredImage): ?>
                                    <img src="../<?php echo htmlspecialchars($featuredImage['file_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                         class="featured-image">
                                <?php endif; ?>
                                
                                <!-- Excerpt -->
                                <?php if ($post['excerpt']): ?>
                                    <div class="lead text-muted mb-4 p-3 bg-light rounded">
                                        <i class="fas fa-quote-left me-2 text-primary"></i>
                                        <?php echo nl2br(htmlspecialchars($post['excerpt'])); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Full Content -->
                                <div class="blog-content">
                                    <?php echo $post['content']; ?>
                                </div>
                                
                                <!-- Author Card -->
                                <div class="author-card">
                                    <div class="d-flex align-items-center">
                                        <div class="author-avatar me-3">
                                            <?php 
                                            if ($post['author_type'] === 'employee') {
                                                echo strtoupper(substr($post['employee_author'] ?? 'E', 0, 1));
                                            } else {
                                                echo strtoupper(substr($post['client_author'] ?? 'C', 0, 1));
                                            }
                                            ?>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">About the Author</h5>
                                            <h4 class="mb-2">
                                                <?php 
                                                if ($post['author_type'] === 'employee') {
                                                    echo htmlspecialchars($post['employee_author'] ?? 'Employee');
                                                } else {
                                                    echo htmlspecialchars($post['client_author'] ?? 'Client');
                                                }
                                                ?>
                                            </h4>
                                            <?php if ($post['author_type'] === 'employee'): ?>
                                                <?php if (!empty($post['employee_email'])): ?>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <?php echo htmlspecialchars($post['employee_email']); ?>
                                                </p>
                                                <?php endif; ?>
                                                <?php if (!empty($post['employee_phone'])): ?>
                                                <p class="mb-0 text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    <?php echo htmlspecialchars($post['employee_phone']); ?>
                                                </p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-building me-1"></i>
                                                    <?php echo htmlspecialchars($post['client_author'] ?? ''); ?>
                                                </p>
                                                <?php if (!empty($post['client_contact'])): ?>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    <?php echo htmlspecialchars($post['client_contact']); ?>
                                                </p>
                                                <?php endif; ?>
                                                <?php if (!empty($post['client_email'])): ?>
                                                <p class="mb-0 text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <?php echo htmlspecialchars($post['client_email']); ?>
                                                </p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Comments Section -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-comments me-2"></i>
                                            Comments (<?php echo count($comments); ?>)
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($comments)): ?>
                                            <p class="text-muted text-center py-3">
                                                <i class="fas fa-comment-slash fa-2x mb-2"></i><br>
                                                No comments yet. Be the first to comment!
                                            </p>
                                        <?php else: ?>
                                            <?php foreach ($comments as $comment): ?>
                                                <div class="comment-item">
                                                    <div class="d-flex">
                                                        <div class="comment-avatar me-3">
                                                            <?php 
                                                            if ($comment['author_employee_id']) {
                                                                echo strtoupper(substr($comment['employee_commenter'] ?? 'E', 0, 1));
                                                            } elseif ($comment['author_client_id']) {
                                                                echo strtoupper(substr($comment['client_commenter'] ?? 'C', 0, 1));
                                                            } else {
                                                                echo strtoupper(substr($comment['author_name'] ?? 'A', 0, 1));
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="mb-0">
                                                                    <?php 
                                                                    if ($comment['author_employee_id']) {
                                                                        echo htmlspecialchars($comment['employee_commenter'] ?? 'Employee');
                                                                        echo ' <span class="badge bg-info">Staff</span>';
                                                                    } elseif ($comment['author_client_id']) {
                                                                        echo htmlspecialchars($comment['client_commenter'] ?? 'Client');
                                                                        echo ' <span class="badge bg-success">Client</span>';
                                                                    } else {
                                                                        echo htmlspecialchars($comment['author_name'] ?? 'Anonymous');
                                                                    }
                                                                    ?>
                                                                </h6>
                                                                <span class="comment-meta">
                                                                    <i class="far fa-clock me-1"></i>
                                                                    <?php echo $functions->timeAgo($comment['created_at']); ?>
                                                                </span>
                                                            </div>
                                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        
                                        <!-- Add Comment Form -->
                                        <?php if ($post['comment_status'] === 'open'): ?>
                                        <hr>
                                        <h6 class="mb-3"><i class="fas fa-reply me-2"></i>Leave a Comment</h6>
                                        <form action="blog_comment_add.php" method="POST">
                                            <?php echo $auth->csrfField(); ?>
                                            <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                            <div class="mb-3">
                                                <textarea name="content" class="form-control" rows="3" 
                                                          placeholder="Write your comment..." required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane me-2"></i>Post Comment
                                            </button>
                                        </form>
                                        <?php elseif ($post['comment_status'] === 'closed'): ?>
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle me-2"></i>Comments are closed for this post.
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar Column -->
                    <div class="col-lg-4">
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <div class="card card-default mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                                </div>
                                <div class="card-body">
                                    <a href="blog_posts.php" class="btn btn-outline-secondary w-100 mb-2">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Posts
                                    </a>
                                    <a href="blog_edit.php?id=<?php echo $postId; ?>" class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-edit me-2"></i>Edit Post
                                    </a>
                                    <?php if ($post['status'] === 'pending_review'): ?>
                                        <a href="blog_approve.php?id=<?php echo $postId; ?>" 
                                           class="btn btn-success w-100 mb-2"
                                           onclick="return confirmAction(this, 'Approve this post?')">
                                            <i class="fas fa-check me-2"></i>Approve Post
                                        </a>
                                    <?php endif; ?>
                                    <a href="blog_delete.php?id=<?php echo $postId; ?>" 
                                       class="btn btn-danger w-100"
                                       onclick="return confirmAction(this, 'Delete this post?')">
                                        <i class="fas fa-trash me-2"></i>Delete Post
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Post Info Card -->
                            <div class="card card-default mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Post Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th>ID:</th>
                                            <td>#<?php echo $post['id']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>UUID:</th>
                                            <td><small class="text-muted"><?php echo $post['uuid']; ?></small></td>
                                        </tr>
                                        <tr>
                                            <th>Created:</th>
                                            <td><?php echo $functions->formatDate($post['created_at'], 'M j, Y g:i A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated:</th>
                                            <td><?php echo $functions->timeAgo($post['updated_at']); ?></td>
                                        </tr>
                                        <?php if ($post['published_at']): ?>
                                        <tr>
                                            <th>Published:</th>
                                            <td><?php echo $functions->formatDate($post['published_at'], 'M j, Y'); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($post['scheduled_for']): ?>
                                        <tr>
                                            <th>Scheduled:</th>
                                            <td><?php echo $functions->formatDate($post['scheduled_for'], 'M j, Y'); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>Views:</th>
                                            <td><?php echo number_format($post['view_count']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Comments:</th>
                                            <td><?php echo count($comments); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Related Posts -->
                            <?php if (!empty($relatedPosts)): ?>
                            <div class="card card-default">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-link me-2"></i>Related Posts</h6>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($relatedPosts as $related): ?>
                                        <div class="related-post-card">
                                            <a href="blog_view.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($related['title']); ?></h6>
                                                <small class="text-muted">
                                                    <i class="far fa-calendar me-1"></i>
                                                    <?php echo $functions->formatDate($related['created_at'], 'M d, Y'); ?>
                                                </small>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Navigation -->
                            <?php if ($prevPost || $nextPost): ?>
                            <div class="nav-links">
                                <?php if ($prevPost): ?>
                                    <a href="blog_view.php?id=<?php echo $prevPost['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-chevron-left me-2"></i>Previous
                                    </a>
                                <?php else: ?>
                                    <span></span>
                                <?php endif; ?>
                                
                                <?php if ($nextPost): ?>
                                    <a href="blog_view.php?id=<?php echo $nextPost['id']; ?>" class="btn btn-outline-primary">
                                        Next<i class="fas fa-chevron-right ms-2"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'inc/admin_footer.php'; ?>
