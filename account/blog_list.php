<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$functions = Functions::getInstance();
$db = Database::getInstance();

$currentUser = $auth->getUserData();

// Handle approve action
if (isset($_GET['approve'])) {
    $postId = (int)$_GET['approve'];
    try {
        $blog->updatePost($postId, ['status' => 'published']);
        $_SESSION['toast_success'] = 'Post approved and published.';
    } catch (Exception $e) {
        $_SESSION['toast_error'] = 'Failed to approve post.';
    }
    header('Location: blog_list.php');
    exit;
}

// Handle delete action
if (isset($_GET['delete'])) {
    $postId = (int)$_GET['delete'];
    try {
        $blog->deletePost($postId);
        $_SESSION['toast_success'] = 'Post deleted successfully.';
    } catch (Exception $e) {
        $_SESSION['toast_error'] = 'Failed to delete post.';
    }
    header('Location: blog_list.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$filters = [];
if ($statusFilter) $filters['status'] = $statusFilter;
if ($search) $filters['search'] = $search;

$posts = $blog->getPosts($filters, 200, 0);

$pageActive = 'blog_list';
$pageTitle = 'TPV Construction and Services LTD · Blog Posts';
require 'inc/admin_header.php';
?>
            <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home me-1"></i>Blog</a></li>
                    <li class="breadcrumb-item active">Manage posts</li>
                </ol>
            </div>

            <div class="container-fluid p-l-15 p-r-15 p-t-0 p-b-25">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="card-title mb-2 mb-sm-0">
                            <i class="fas fa-newspaper me-2"></i> All articles
                        </div>
                        <div class="d-flex gap-2">
                            <a href="blog_new.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> New post</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-6 mb-2 mb-sm-0">
                                <input type="text" class="form-control" id="searchBox" placeholder="Search title, author..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-sm-4">
                                <select class="form-select" id="statusFilter">
                                    <option value="">All statuses</option>
                                    <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="pending_review" <?php echo $statusFilter === 'pending_review' ? 'selected' : ''; ?>>Pending review</option>
                                    <option value="archived" <?php echo $statusFilter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <a href="blog_list.php" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover data-table" id="blogTable">
                                <thead>
                                    <tr>
                                        <th data-priority="1">ID</th>
                                        <th data-priority="2">Title</th>
                                        <th data-priority="5">Author</th>
                                        <th data-priority="6">Categories</th>
                                        <th data-priority="3">Status</th>
                                        <th data-priority="7">Created</th>
                                        <th data-priority="8">Views</th>
                                        <th data-priority="1" data-orderable="false">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($posts as $post): 
                                        $authorName = $post['author_type'] === 'employee' 
                                            ? ($post['employee_author'] ?? 'Employee') 
                                            : ($post['client_author'] ?? 'Client');
                                        $cats = [];
                                        $catStmt = $db->query(
                                            "SELECT c.name FROM blog_post_categories pc 
                                             JOIN blog_categories c ON pc.category_id = c.id 
                                             WHERE pc.post_id = :post_id",
                                            ['post_id' => $post['id']]
                                        );
                                        foreach ($catStmt->fetchAll() as $cat) {
                                            $cats[] = htmlspecialchars($cat['name']);
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo $post['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($post['slug']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($authorName); ?></td>
                                        <td><?php echo $cats ? implode(', ', $cats) : '-'; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo str_replace('_', '-', $post['status']); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $post['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $functions->formatDate($post['created_at'], 'M d, Y'); ?></td>
                                        <td><?php echo number_format($post['view_count'] ?? 0); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="blog_view.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-light border" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="blog_edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-light border" title="Edit"><i class="fas fa-edit"></i></a>
                                                <?php if ($post['status'] === 'pending_review'): ?>
                                                <a href="?approve=<?php echo $post['id']; ?>" class="btn btn-sm btn-light border text-success" title="Approve" onclick="return confirmAction(this, 'Approve this post?')"><i class="fas fa-check"></i></a>
                                                <?php endif; ?>
                                                <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirmAction(this, 'Delete this post?')"><i class="fas fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($posts)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No posts found. <a href="blog_new.php">Create one</a>.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
