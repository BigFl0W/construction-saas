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

$employees = $db->query(
    "SELECT id, first_name, last_name FROM employees
     WHERE status = 'active' AND deleted_at IS NULL
     ORDER BY first_name, last_name"
)->fetchAll();
$clients = $db->query(
    "SELECT id, company_name, contact_person FROM clients
     WHERE status IN ('active', 'company', 'individual') AND deleted_at IS NULL
     ORDER BY company_name, contact_person"
)->fetchAll();
$categories = $blog->getCategories();
$mediaItems = $db->query(
    "SELECT id, original_filename, filename, file_path
     FROM media
     WHERE media_type = 'image'
     ORDER BY created_at DESC
     LIMIT 20"
)->fetchAll();

$filterStatus = trim((string) ($_GET['status'] ?? ''));
$filterSearch = trim((string) ($_GET['search'] ?? ''));
$filterCategory = (int) ($_GET['category_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
        header('Location: blog_manager.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete_post') {
        $result = $blog->deletePost((int) ($_POST['post_id'] ?? 0));
        $_SESSION[$result['success'] ? 'toast_success' : 'toast_error'] = $result['message'];
        header('Location: blog_manager.php');
        exit;
    }

    if ($action === 'save_post') {
        $postId = (int) ($_POST['post_id'] ?? 0);
        $authorType = $_POST['author_type'] ?? 'employee';
        $defaultEmployeeId = $currentUser['employee_id'] ?? ($employees[0]['id'] ?? null);

        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
            'content' => (string) ($_POST['content'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'comment_status' => $_POST['comment_status'] ?? 'open',
            'published_at' => trim((string) ($_POST['published_at'] ?? '')),
            'scheduled_for' => trim((string) ($_POST['scheduled_for'] ?? '')),
            'author_type' => $authorType,
            'author_employee_id' => $authorType === 'employee'
                ? ($_POST['author_employee_id'] ?: $defaultEmployeeId)
                : null,
            'author_client_id' => $authorType === 'client'
                ? ($_POST['author_client_id'] ?: null)
                : null,
            'categories' => $_POST['categories'] ?? [],
            'tags_string' => trim((string) ($_POST['tags_string'] ?? '')),
            'featured_image_id_select' => $_POST['featured_image_id_select'] ?? null,
            'remove_featured_image' => !empty($_POST['remove_featured_image']),
        ];

        $errors = [];
        if ($data['title'] === '') {
            $errors[] = 'Post title is required.';
        }
        if (trim(strip_tags($data['content'])) === '') {
            $errors[] = 'Post content is required.';
        }
        if (empty($data['categories'])) {
            $errors[] = 'Select at least one category.';
        }
        if ($data['author_type'] === 'employee' && empty($data['author_employee_id'])) {
            $errors[] = 'Select an employee author.';
        }
        if ($data['author_type'] === 'client' && empty($data['author_client_id'])) {
            $errors[] = 'Select a client author.';
        }

        if (!empty($errors)) {
            $_SESSION['toast_error'] = implode(' ', $errors);
            $_SESSION['blog_manager_form'] = $data;
            header('Location: blog_manager.php' . ($postId > 0 ? '?edit=' . $postId : '?compose=1'));
            exit;
        }

        $result = $postId > 0
            ? $blog->updatePost($postId, $data, $_FILES)
            : $blog->createPost($data, $_FILES);

        $_SESSION[$result['success'] ? 'toast_success' : 'toast_error'] = $result['message'];
        $_SESSION['blog_manager_form'] = [];

        if ($result['success']) {
            $targetId = $result['post_id'] ?? $postId;
            header('Location: blog_manager.php?edit=' . (int) $targetId);
            exit;
        }

        header('Location: blog_manager.php' . ($postId > 0 ? '?edit=' . $postId : '?compose=1'));
        exit;
    }
}

$stats = $blog->getDashboardStats();
$filters = [];
if ($filterStatus !== '') {
    $filters['status'] = $filterStatus;
}
if ($filterSearch !== '') {
    $filters['search'] = $filterSearch;
}
if ($filterCategory > 0) {
    $filters['category_id'] = $filterCategory;
}
$posts = $blog->getPosts($filters, 100, 0);

$formState = $_SESSION['blog_manager_form'] ?? [];
unset($_SESSION['blog_manager_form']);

$editingId = (int) ($_GET['edit'] ?? 0);
$editingPost = $editingId > 0 ? $blog->getPostById($editingId) : null;
$isComposeMode = isset($_GET['compose']) || $editingPost !== null;

$selectedPost = [
    'id' => $editingPost['id'] ?? 0,
    'title' => $formState['title'] ?? ($editingPost['title'] ?? ''),
    'slug' => $formState['slug'] ?? ($editingPost['slug'] ?? ''),
    'excerpt' => $formState['excerpt'] ?? ($editingPost['excerpt'] ?? ''),
    'content' => $formState['content'] ?? ($editingPost['content'] ?? ''),
    'status' => $formState['status'] ?? ($editingPost['status'] ?? 'draft'),
    'comment_status' => $formState['comment_status'] ?? ($editingPost['comment_status'] ?? 'open'),
    'published_at' => $formState['published_at'] ?? ($editingPost['published_at'] ?? ''),
    'scheduled_for' => $formState['scheduled_for'] ?? ($editingPost['scheduled_for'] ?? ''),
    'author_type' => $formState['author_type'] ?? ($editingPost['author_type'] ?? 'employee'),
    'author_employee_id' => $formState['author_employee_id'] ?? ($editingPost['author_employee_id'] ?? ($currentUser['employee_id'] ?? '')),
    'author_client_id' => $formState['author_client_id'] ?? ($editingPost['author_client_id'] ?? ''),
    'categories' => $formState['categories'] ?? array_column($editingPost['categories'] ?? [], 'id'),
    'tags_string' => $formState['tags_string'] ?? implode(', ', array_column($editingPost['tags'] ?? [], 'name')),
    'featured_image_id_select' => $formState['featured_image_id_select'] ?? ($editingPost['featured_image_id'] ?? ''),
    'featured_image_path' => $editingPost['featured_image_path'] ?? '',
];

$pageActive = 'blog_manager';
$pageTitle = 'TPV Construction and Services LTD · Blog Studio';
require 'inc/admin_header.php';
?>

<style>
    .blog-studio-grid {
        display: grid;
        gap: 1.25rem;
    }
    .blog-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }
    .blog-stat-card {
        background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        border: 1px solid #e6ebf2;
        border-radius: 20px;
        padding: 1.1rem 1.2rem;
        box-shadow: 0 18px 40px rgba(22, 33, 58, 0.05);
    }
    .blog-stat-card small {
        display: block;
        color: #6f7b91;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-bottom: 0.3rem;
    }
    .blog-stat-card strong {
        display: block;
        font-size: 1.75rem;
        color: #17233a;
        line-height: 1.1;
    }
    .blog-panel {
        background: #fff;
        border: 1px solid #e5eaf2;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .blog-panel__header {
        padding: 1.2rem 1.35rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .blog-panel__body {
        padding: 1.35rem;
    }
    .blog-fieldset {
        border: 1px solid #edf2f7;
        border-radius: 20px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #fcfdff;
    }
    .blog-fieldset h6 {
        margin-bottom: 0.85rem;
        font-size: 0.92rem;
        color: #17233a;
    }
    .blog-content-textarea {
        min-height: 360px;
        font-family: Georgia, "Times New Roman", serif;
        line-height: 1.75;
    }
    .blog-thumb {
        width: 88px;
        height: 88px;
        border-radius: 18px;
        object-fit: cover;
        border: 1px solid #e7ecf3;
        background: #f5f7fb;
    }
    .blog-table td,
    .blog-table th {
        vertical-align: middle;
    }
    .blog-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.38rem 0.72rem;
        border-radius: 999px;
        background: #f5f7fb;
        color: #40506b;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .blog-pill--published {
        background: #e8f8ef;
        color: #127446;
    }
    .blog-pill--draft {
        background: #fff4dd;
        color: #9f6700;
    }
    .blog-pill--pending_review,
    .blog-pill--scheduled,
    .blog-pill--archived {
        background: #eef2ff;
        color: #3949ab;
    }
    .blog-manager-topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .blog-post-row-title {
        color: #17233a;
        font-weight: 700;
    }
    .blog-post-row-meta {
        color: #6d7890;
        font-size: 0.78rem;
        margin-top: 0.15rem;
    }
    @media (max-width: 991px) {
        .blog-content-textarea {
            min-height: 260px;
        }
    }
</style>

<div class="container-fluid p-l-25 p-r-25 p-t-15 p-b-25">
    <div class="blog-manager-topbar">
        <div>
            <ol class="breadcrumb m-b-10">
                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                <li class="breadcrumb-item active">Blog Studio</li>
            </ol>
            <h1 class="m-b-5" style="font-size:1.7rem;">Blog Studio</h1>
            <p class="text-muted m-0">Create, edit, and publish professional blog posts from one clean dashboard.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="blog_page_settings.php" class="btn btn-outline-secondary">
                <i class="fas fa-sliders me-2"></i>Blog Page Settings
            </a>
            <a href="../blog/" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                <i class="fas fa-arrow-up-right-from-square me-2"></i>Preview Website Blog
            </a>
            <a href="blog_manager.php?compose=1" class="btn btn-primary">
                <i class="fas fa-pen-nib me-2"></i>New Post
            </a>
        </div>
    </div>

    <div class="blog-studio-grid">
        <div class="blog-stats-grid">
            <div class="blog-stat-card">
                <small>Total Posts</small>
                <strong><?php echo number_format($stats['total_posts']); ?></strong>
            </div>
            <div class="blog-stat-card">
                <small>Published</small>
                <strong><?php echo number_format($stats['published_posts']); ?></strong>
            </div>
            <div class="blog-stat-card">
                <small>In Draft / Review</small>
                <strong><?php echo number_format($stats['draft_posts']); ?></strong>
            </div>
            <div class="blog-stat-card">
                <small>Pending Comments</small>
                <strong><?php echo number_format($stats['pending_comments']); ?></strong>
            </div>
        </div>

        <?php if ($isComposeMode): ?>
            <div class="blog-panel">
                <div class="blog-panel__header">
                    <div>
                        <h4 class="m-0"><?php echo $selectedPost['id'] ? 'Edit Post' : 'Compose New Post'; ?></h4>
                        <p class="text-muted m-0">
                            <?php echo $selectedPost['id'] ? 'Refine your article and publish updates safely.' : 'Draft a polished article that syncs straight to the website.'; ?>
                        </p>
                    </div>
                    <a href="blog_manager.php" class="btn btn-light border">Close Editor</a>
                </div>
                <div class="blog-panel__body">
                    <form method="post" enctype="multipart/form-data">
                        <?php echo $auth->csrfField(); ?>
                        <input type="hidden" name="action" value="save_post">
                        <input type="hidden" name="post_id" value="<?php echo (int) $selectedPost['id']; ?>">

                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="blog-fieldset">
                                    <h6>Story</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Post Title</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($selectedPost['title']); ?>" placeholder="e.g. How We Deliver Safer Commercial Buildouts">
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Slug</label>
                                            <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($selectedPost['slug']); ?>" placeholder="auto-generated-if-left-empty">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Tags</label>
                                            <input type="text" name="tags_string" class="form-control" value="<?php echo htmlspecialchars($selectedPost['tags_string']); ?>" placeholder="construction, design, project delivery">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label fw-semibold">Excerpt</label>
                                        <textarea name="excerpt" class="form-control" rows="3" placeholder="A short summary used on the listing page and previews."><?php echo htmlspecialchars($selectedPost['excerpt']); ?></textarea>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label fw-semibold">Content</label>
                                        <textarea name="content" class="form-control blog-content-textarea" placeholder="Write the article body here. Basic HTML is supported for formatting."><?php echo htmlspecialchars($selectedPost['content']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="blog-fieldset">
                                    <h6>Publishing</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="status" class="form-select">
                                            <?php foreach (['draft' => 'Draft', 'published' => 'Published', 'pending_review' => 'Pending Review', 'scheduled' => 'Scheduled', 'archived' => 'Archived'] as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $selectedPost['status'] === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Comment Status</label>
                                        <select name="comment_status" class="form-select">
                                            <option value="open" <?php echo $selectedPost['comment_status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                                            <option value="closed" <?php echo $selectedPost['comment_status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                        </select>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Author Type</label>
                                            <select name="author_type" class="form-select" id="blogAuthorType">
                                                <option value="employee" <?php echo $selectedPost['author_type'] === 'employee' ? 'selected' : ''; ?>>Employee</option>
                                                <option value="client" <?php echo $selectedPost['author_type'] === 'client' ? 'selected' : ''; ?>>Client</option>
                                            </select>
                                        </div>
                                        <div class="col-12 blog-author-group" data-author-group="employee">
                                            <label class="form-label fw-semibold">Employee Author</label>
                                            <select name="author_employee_id" class="form-select">
                                                <option value="">Select employee</option>
                                                <?php foreach ($employees as $employee): ?>
                                                    <?php $employeeName = trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')); ?>
                                                    <option value="<?php echo (int) $employee['id']; ?>" <?php echo (string) $selectedPost['author_employee_id'] === (string) $employee['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($employeeName); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12 blog-author-group" data-author-group="client">
                                            <label class="form-label fw-semibold">Client Author</label>
                                            <select name="author_client_id" class="form-select">
                                                <option value="">Select client</option>
                                                <?php foreach ($clients as $client): ?>
                                                    <?php $clientName = trim((string) ($client['company_name'] ?: $client['contact_person'])); ?>
                                                    <option value="<?php echo (int) $client['id']; ?>" <?php echo (string) $selectedPost['author_client_id'] === (string) $client['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($clientName); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Publish Date</label>
                                            <input type="datetime-local" name="published_at" class="form-control" value="<?php echo htmlspecialchars($selectedPost['published_at'] ? date('Y-m-d\TH:i', strtotime((string) $selectedPost['published_at'])) : ''); ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Schedule For</label>
                                            <input type="datetime-local" name="scheduled_for" class="form-control" value="<?php echo htmlspecialchars($selectedPost['scheduled_for'] ? date('Y-m-d\TH:i', strtotime((string) $selectedPost['scheduled_for'])) : ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="blog-fieldset">
                                    <h6>Taxonomy</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Categories</label>
                                        <select name="categories[]" class="form-select" multiple size="6">
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo (int) $category['id']; ?>" <?php echo in_array((int) $category['id'], array_map('intval', (array) $selectedPost['categories']), true) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Use Ctrl or Cmd to select multiple categories.</div>
                                    </div>
                                </div>

                                <div class="blog-fieldset">
                                    <h6>Featured Image</h6>
                                    <?php if (!empty($selectedPost['featured_image_path'])): ?>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <img class="blog-thumb" src="<?php echo htmlspecialchars(tpv_asset_url($selectedPost['featured_image_path'])); ?>" alt="Featured image">
                                            <div class="small text-muted">Current image shown on the website.</div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Upload New Image</label>
                                        <input type="file" name="featured_image_upload" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Or Select Existing</label>
                                        <select name="featured_image_id_select" class="form-select">
                                            <option value="">Keep current / none</option>
                                            <?php foreach ($mediaItems as $media): ?>
                                                <?php $mediaLabel = $media['original_filename'] ?: $media['filename']; ?>
                                                <option value="<?php echo (int) $media['id']; ?>" <?php echo (string) $selectedPost['featured_image_id_select'] === (string) $media['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($mediaLabel); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_featured_image" value="1" id="removeFeaturedImage">
                                        <label class="form-check-label" for="removeFeaturedImage">Remove current featured image</label>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i><?php echo $selectedPost['id'] ? 'Update Post' : 'Save Post'; ?>
                                    </button>
                                    <?php if ($selectedPost['id'] && $selectedPost['status'] === 'published' && $selectedPost['slug'] !== ''): ?>
                                        <a href="../post.php?slug=<?php echo urlencode($selectedPost['slug']); ?>" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                                            <i class="fas fa-arrow-up-right-from-square me-2"></i>View Live
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="blog-panel">
            <div class="blog-panel__header">
                <div>
                    <h4 class="m-0">Published & Draft Posts</h4>
                    <p class="text-muted m-0">Everything here feeds the public website directly.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="blog_categories.php" class="btn btn-light border">Manage Categories</a>
                    <a href="blog_comments.php" class="btn btn-light border">Manage Comments</a>
                </div>
            </div>
            <div class="blog-panel__body">
                <form method="get" class="row g-3 align-items-end m-b-20">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Search</label>
                        <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($filterSearch); ?>" placeholder="Search titles, excerpt, content">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All statuses</option>
                            <?php foreach (['published' => 'Published', 'draft' => 'Draft', 'pending_review' => 'Pending Review', 'scheduled' => 'Scheduled', 'archived' => 'Archived'] as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php echo $filterStatus === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="0">All categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo (int) $category['id']; ?>" <?php echo $filterCategory === (int) $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="blog_manager.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table blog-table align-middle">
                        <thead>
                            <tr>
                                <th>Post</th>
                                <th>Status</th>
                                <th>Author</th>
                                <th>Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No blog posts found for the selected filters.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($posts as $post): ?>
                                <?php
                                    $authorName = $post['author_type'] === 'client'
                                        ? ($post['client_author'] ?: 'Client')
                                        : ($post['employee_author'] ?: 'Employee');
                                    $statusClass = 'blog-pill--' . str_replace('_', '_', (string) $post['status']);
                                    $liveUrl = $post['slug'] ? '../post.php?slug=' . urlencode($post['slug']) : '';
                                ?>
                                <tr>
                                    <td>
                                        <div class="blog-post-row-title"><?php echo htmlspecialchars($post['title']); ?></div>
                                        <div class="blog-post-row-meta">
                                            <?php echo htmlspecialchars($post['slug']); ?>
                                            <?php if (!empty($post['comment_count'])): ?>
                                                · <?php echo (int) $post['comment_count']; ?> comments
                                            <?php endif; ?>
                                            · <?php echo number_format((int) ($post['view_count'] ?? 0)); ?> views
                                        </div>
                                    </td>
                                    <td>
                                        <span class="blog-pill <?php echo htmlspecialchars($statusClass); ?>">
                                            <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', (string) $post['status']))); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($authorName); ?></td>
                                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime((string) ($post['updated_at'] ?? $post['created_at'])))); ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <?php if ($liveUrl !== '' && $post['status'] === 'published'): ?>
                                                <a href="<?php echo htmlspecialchars($liveUrl); ?>" class="btn btn-sm btn-light border" target="_blank" rel="noopener">View</a>
                                            <?php endif; ?>
                                            <a href="blog_manager.php?edit=<?php echo (int) $post['id']; ?>" class="btn btn-sm btn-light border">Edit</a>
                                            <form method="post" onsubmit="return confirm('Delete this post? This can be restored only from the database.');">
                                                <?php echo $auth->csrfField(); ?>
                                                <input type="hidden" name="action" value="delete_post">
                                                <input type="hidden" name="post_id" value="<?php echo (int) $post['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const authorType = document.getElementById('blogAuthorType');
        if (!authorType) {
            return;
        }

        const groups = document.querySelectorAll('.blog-author-group');
        const syncGroups = () => {
            const value = authorType.value;
            groups.forEach((group) => {
                group.style.display = group.dataset.authorGroup === value ? '' : 'none';
            });
        };

        authorType.addEventListener('change', syncGroups);
        syncGroups();
    })();
</script>

<?php require 'inc/admin_footer.php'; ?>
