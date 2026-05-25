<?php
// blog_comments.php - Manage blog comments
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

// Handle actions (approve, spam, delete, reply)
$toastSuccess = $_SESSION['toast_success'] ?? '';
$toastError = $_SESSION['toast_error'] ?? '';
unset($_SESSION['toast_success'], $_SESSION['toast_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !$auth->verifyCSRF($_POST['csrf_token'])) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $commentId = $_POST['comment_id'] ?? 0;
        
        if ($action === 'update_status' && $commentId && isset($_POST['status'])) {
            $result = $blog->updateCommentStatus($commentId, $_POST['status']);
            $_SESSION[($result['success'] ?? false) ? 'toast_success' : 'toast_error'] = $result['message'] ?? 'Action completed';
        } elseif ($action === 'delete' && $commentId) {
            $result = $blog->deleteComment($commentId);
            $_SESSION[($result['success'] ?? false) ? 'toast_success' : 'toast_error'] = $result['message'] ?? 'Action completed';
        } elseif ($action === 'reply' && $commentId && !empty($_POST['reply_content'])) {
            $result = $blog->addReply($commentId, $_POST['reply_content'], $currentUser['employee_id'] ?? null);
            $_SESSION[($result['success'] ?? false) ? 'toast_success' : 'toast_error'] = $result['message'] ?? 'Action completed';
        }
    }
    header('Location: blog_comments.php' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Fetch comments with optional filters
$filters = [];
if (isset($_GET['status']) && in_array($_GET['status'], ['pending', 'approved', 'spam', 'trash'])) {
    $filters['status'] = $_GET['status'];
}
if (isset($_GET['post_id']) && is_numeric($_GET['post_id'])) {
    $filters['post_id'] = $_GET['post_id'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

$comments = $blog->getComments($filters);

// Fetch all blog posts for filter dropdown
$posts = $db->query("SELECT id, title FROM blog_posts WHERE deleted_at IS NULL ORDER BY created_at DESC")->fetchAll();

// Count comments by status for statistics
$totalComments = $db->query("SELECT COUNT(*) FROM blog_comments WHERE deleted_at IS NULL")->fetchColumn();
$pendingCount = $db->query("SELECT COUNT(*) FROM blog_comments WHERE status = 'pending' AND deleted_at IS NULL")->fetchColumn();
$approvedCount = $db->query("SELECT COUNT(*) FROM blog_comments WHERE status = 'approved' AND deleted_at IS NULL")->fetchColumn();
$spamCount = $db->query("SELECT COUNT(*) FROM blog_comments WHERE status = 'spam' AND deleted_at IS NULL")->fetchColumn();
$trashCount = $db->query("SELECT COUNT(*) FROM blog_comments WHERE status = 'trash' AND deleted_at IS NULL")->fetchColumn();

// Generate CSRF token for AJAX (if needed, but we'll use the same token from auth)
$csrfToken = $auth->csrfFieldValue();

$pageActive = 'blog_comments';
$pageTitle = 'TPV Construction and Services LTD · Blog Comments';
require 'inc/admin_header.php';
?>
            <!-- breadcrumb -->
            <div data-pages="parallax">
                <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <ol class="breadcrumb sm-p-b-5">
                            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="blog_posts.php">Blog</a></li>
                            <li class="breadcrumb-item active">Comments</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- MAIN CARD -->
            <div class="container-fluid p-l-15 p-r-15 p-t-0 p-b-25">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card d-flex align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total</h6>
                                <h4 class="mb-0 fw-bold"><?php echo number_format($totalComments); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card d-flex align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Pending</h6>
                                <h4 class="mb-0 fw-bold"><?php echo number_format($pendingCount); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Approved</h6>
                                <h4 class="mb-0 fw-bold"><?php echo number_format($approvedCount); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card d-flex align-items-center">
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Spam</h6>
                                <h4 class="mb-0 fw-bold"><?php echo number_format($spamCount); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stat-card d-flex align-items-center">
                            <div class="stat-icon bg-secondary bg-opacity-10 text-secondary me-3">
                                <i class="fas fa-trash"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Trash</h6>
                                <h4 class="mb-0 fw-bold"><?php echo number_format($trashCount); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white py-3">
                        <div class="card-title mb-2 mb-sm-0">
                            <i class="fas fa-comment me-2"></i> Blog Comments
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- filter row -->
                        <div class="row filter-row m-b-20">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control rounded-pill" id="searchBox" placeholder="Search comments...">
                                    <span class="input-group-append">
                                        <button class="btn btn-outline-secondary rounded-pill" type="button"><i class="fas fa-search"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <form method="get" class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                    <select name="status" class="form-select form-select-sm" style="width:130px;">
                                        <option value="">All status</option>
                                        <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo ($filters['status'] ?? '') === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="spam" <?php echo ($filters['status'] ?? '') === 'spam' ? 'selected' : ''; ?>>Spam</option>
                                        <option value="trash" <?php echo ($filters['status'] ?? '') === 'trash' ? 'selected' : ''; ?>>Trash</option>
                                    </select>
                                    <select name="post_id" class="form-select form-select-sm" style="width:150px;">
                                        <option value="">All posts</option>
                                        <?php foreach ($posts as $post): ?>
                                        <option value="<?php echo $post['id']; ?>" <?php echo ($filters['post_id'] ?? '') == $post['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars(substr($post['title'], 0, 30) . (strlen($post['title']) > 30 ? '…' : '')); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><i class="fas fa-filter me-1"></i>Filter</button>
                                    <a href="blog_comments.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="fas fa-undo me-1"></i>Reset</a>
                                </form>
                            </div>
                        </div>

                        <!-- COMMENTS TABLE -->
                        <div class="table-responsive-wrapper">
                            <table class="table table-hover" data-table id="commentsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Author</th>
                                        <th>Comment</th>
                                        <th>Post</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th data-orderable="false">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($comments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No comments found.</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($comments as $comment): ?>
                                    <tr data-comment-id="<?php echo $comment['id']; ?>">
                                        <td><?php echo $comment['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="comment-avatar me-2">
                                                    <?php
                                                    if ($comment['author_employee_id']) {
                                                        echo substr($comment['employee_name'] ?? 'E', 0, 1);
                                                    } elseif ($comment['author_client_id']) {
                                                        echo substr($comment['client_name'] ?? 'C', 0, 1);
                                                    } else {
                                                        echo substr($comment['author_name'] ?? 'G', 0, 1);
                                                    }
                                                    ?>
                                                </div>
                                                <div>
                                                    <strong>
                                                        <?php
                                                        if ($comment['author_employee_id']) {
                                                            echo htmlspecialchars($comment['employee_name'] ?? 'Employee');
                                                        } elseif ($comment['author_client_id']) {
                                                            echo htmlspecialchars($comment['client_name'] ?? $comment['client_contact'] ?? 'Client');
                                                        } else {
                                                            echo htmlspecialchars($comment['author_name'] ?? 'Guest');
                                                        }
                                                        ?>
                                                    </strong>
                                                    <?php if (!empty($comment['author_email'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($comment['author_email']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <textarea class="comment-store-<?php echo $comment['id']; ?>" style="display:none;"><?php echo htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                        </td>
                                        <td>
                                            <div title="<?php echo htmlspecialchars($comment['content']); ?>">
                                                <?php echo nl2br(htmlspecialchars(substr($comment['content'], 0, 100) . (strlen($comment['content']) > 100 ? '…' : ''))); ?>
                                            </div>
                                            <?php if ($comment['parent_id']): ?>
                                            <span class="reply-indicator mt-1"><i class="fas fa-reply fa-xs"></i> Reply</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="../post.php?slug=<?php echo urlencode($comment['post_slug']); ?>" target="_blank" class="text-primary">
                                                <?php echo htmlspecialchars(substr($comment['post_title'], 0, 30) . (strlen($comment['post_title']) > 30 ? '…' : '')); ?>
                                                <i class="fas fa-external-link-alt fa-xs ms-1"></i>
                                            </a>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $comment['status']; ?>">
                                                <?php
                                                $icons = [
                                                    'pending' => 'fas fa-clock',
                                                    'approved' => 'fas fa-check-circle',
                                                    'spam' => 'fas fa-ban',
                                                    'trash' => 'fas fa-trash'
                                                ];
                                                $icon = $icons[$comment['status']] ?? 'fas fa-circle';
                                                ?>
                                                <i class="<?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst($comment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if ($comment['status'] !== 'approved'): ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-light border" title="Approve"><i class="fas fa-check text-success"></i></button>
                                                </form>
                                                <?php endif; ?>
                                                <?php if ($comment['status'] !== 'spam'): ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                    <input type="hidden" name="status" value="spam">
                                                    <button type="submit" class="btn btn-sm btn-light border" title="Mark as spam"><i class="fas fa-ban text-warning"></i></button>
                                                </form>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-light border reply-btn"
                                                        data-id="<?php echo $comment['id']; ?>"
                                                        data-author="<?php echo htmlspecialchars($comment['author_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-email="<?php echo htmlspecialchars($comment['author_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-post="<?php echo htmlspecialchars($comment['post_title'] ?? 'Blog Post', ENT_QUOTES, 'UTF-8'); ?>"
                                                        title="Reply"><i class="fas fa-reply text-info"></i></button>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                    <button type="button" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirmAction(this, 'Delete this comment?')"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="small hint-text m-t-15">
                            <i class="fas fa-info-circle me-1"></i> 
                            Use the filter bar to narrow down comments. Click column headers to sort.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="action" value="reply">
                        <input type="hidden" name="comment_id" id="replyCommentId" value="">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title"><i class="fas fa-reply text-info me-2"></i>Reply to <span id="replyAuthorName"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="original-message-card">
                                        <h6 class="fw-semibold mb-2"><i class="fas fa-comment-dots me-1 text-muted"></i>Original Comment</h6>
                                        <div class="original-sender mb-2">
                                            <strong id="replyFromName"></strong>
                                            <br><small class="text-muted" id="replyFromEmail"></small>
                                        </div>
                                        <div class="original-subject mb-2">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" id="replyPostTitle"></span>
                                        </div>
                                        <div class="original-body">
                                            <p id="replyOriginalComment" style="white-space:pre-line;font-size:13px;color:#475569;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Your Reply <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="reply_content" rows="10" placeholder="Type your reply here..." required></textarea>
                                    </div>
                                    <p class="small text-muted mt-2 mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        An email notification will be sent to <strong id="replyEmailInfo">this commenter</strong> when available.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-paper-plane me-1"></i>Send Reply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
        .original-message-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #f8fafc;
            padding: 16px;
            height: 100%;
        }
        .original-body {
            max-height: 220px;
            overflow-y: auto;
            padding: 12px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        </style>

<?php
$extraScripts = <<<'HEREDOC'
<script>
$(function() {
    $(document).on('click', '.reply-btn', function() {
        var id = $(this).data('id');
        var author = $(this).data('author') || 'Commenter';
        var email = $(this).data('email') || '';
        var postTitle = $(this).data('post') || 'Blog post';
        var originalComment = $('.comment-store-' + id).text();

        $('#replyCommentId').val(id);
        $('#replyAuthorName').text(author);
        $('#replyFromName').text(author);
        $('#replyFromEmail').text(email ? '<' + email + '>' : 'No email captured');
        $('#replyPostTitle').text(postTitle);
        $('#replyOriginalComment').text(originalComment);
        $('#replyEmailInfo').text(email || 'this commenter');
        $('#replyModal textarea[name="reply_content"]').val('');
        $('#replyModal').modal('show');
    });
});
</script>
HEREDOC;
?>

        <!-- FOOTER -->
<?php require 'inc/admin_footer.php'; ?>
