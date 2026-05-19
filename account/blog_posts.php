<?php
// blog_posts.php - List all blog posts with pending review filter
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$functions = Functions::getInstance();

// Get database instance for additional queries
$db = Database::getInstance();

// Get filter from URL
$status = $_GET['status'] ?? 'all';
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build filters
$filters = [];
if ($status !== 'all') {
    $filters['status'] = $status;
}

// Get posts
$posts = $blog->getPosts($filters, $limit, $offset);

// Get counts for tabs
$allCount = count($blog->getPosts([], 1000));
$pendingCount = count($blog->getPosts(['status' => 'pending_review'], 1000));
$publishedCount = count($blog->getPosts(['status' => 'published'], 1000));

// Get current user for display
$currentUser = $auth->getUserData();

$pageActive = 'blog_posts';
$pageTitle = 'TPV Construction and Services LTD · Blog Posts';
require 'inc/admin_header.php';
?>
                <!-- Breadcrumb -->
                <div class="container-fluid p-l-25 p-r-25">
                    <div class="inner">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Blog</a></li>
                            <li class="breadcrumb-item active">All Posts</li>
                        </ol>
                    </div>
                </div>

                <?php
                // Forward old session messages to toast system
                if (isset($_SESSION['blog_success'])) {
                    $_SESSION['toast_success'] = $_SESSION['blog_success'];
                    unset($_SESSION['blog_success']);
                }
                if (isset($_SESSION['blog_error'])) {
                    $_SESSION['toast_error'] = $_SESSION['blog_error'];
                    unset($_SESSION['blog_error']);
                }
                ?>

                <!-- Main Content -->
                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                    <div class="card card-default">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                <i class="fas fa-blog me-2"></i>Blog Posts
                            </div>
                            <div>
                                <a href="blog_new.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>New Post
                                </a>
                            </div>
                        </div>
                        
                        <!-- Status Tabs -->
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-simple" role="tablist">
                                <li class="nav-item">
                                    <a href="blog_posts.php?status=all" class="<?php echo $status === 'all' ? 'active' : ''; ?>">
                                        All <span class="badge bg-secondary ms-2"><?php echo $allCount; ?></span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="blog_posts.php?status=pending_review" class="<?php echo $status === 'pending_review' ? 'active' : ''; ?>">
                                        Pending Review <span class="badge bg-warning ms-2"><?php echo $pendingCount; ?></span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="blog_posts.php?status=published" class="<?php echo $status === 'published' ? 'active' : ''; ?>">
                                        Published <span class="badge bg-success ms-2"><?php echo $publishedCount; ?></span>
                                    </a>
                                </li>
                            </ul>
                            
                            <div class="tab-content mt-4">
                                <div class="table-responsive">
                                    <table class="table table-hover data-table" id="blogPostsTable">
                                        <thead>
                                            <tr>
                                                <th data-priority="1">ID</th>
                                                <th data-priority="2">Title</th>
                                                <th data-priority="5">Author</th>
                                                <th data-priority="6">Categories</th>
                                                <th data-priority="3">Status</th>
                                                <th data-priority="7">Created</th>
                                                <th data-priority="8">Comments</th>
                                                <th data-priority="1" data-orderable="false">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($posts as $post): ?>
                                            <tr>
                                                <td><?php echo $post['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($post['title']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($post['slug']); ?></small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($post['author_type'] === 'employee') {
                                                        echo htmlspecialchars($post['employee_author'] ?? 'Employee');
                                                    } else {
                                                        echo htmlspecialchars($post['client_author'] ?? 'Client');
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Get categories for this post using $db instance
                                                    $catSql = "SELECT c.name FROM blog_post_categories pc 
                                                               JOIN blog_categories c ON pc.category_id = c.id 
                                                               WHERE pc.post_id = :post_id";
                                                    $catStmt = $db->query($catSql, ['post_id' => $post['id']]);
                                                    $cats = $catStmt->fetchAll();
                                                    foreach ($cats as $cat) {
                                                        echo '<span class="badge bg-light text-dark me-1">' . htmlspecialchars($cat['name']) . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-<?php echo str_replace('_', '-', $post['status']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $post['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $functions->formatDate($post['created_at'], 'M d, Y'); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $post['comment_count'] ?? 0; ?></span>
                                                </td>
                                                <td>
                                                    <a href="blog_view.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-info action-btn" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($post['status'] === 'pending_review'): ?>
                                                    <a href="blog_approve.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-success action-btn" title="Approve" onclick="return confirmAction(this, 'Approve this post?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <a href="blog_edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning action-btn" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="blog_delete.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger action-btn" title="Delete" onclick="return confirmAction(this, 'Delete this post?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            
                                            <?php if (empty($posts)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No blog posts found</p>
                                                    <a href="blog_new.php" class="btn btn-primary btn-sm">Create your first post</a>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require 'inc/admin_footer.php'; ?>
