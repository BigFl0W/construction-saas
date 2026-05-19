<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$functions = Functions::getInstance();

try {
    $db = Database::getInstance();
} catch (Exception $e) {
    error_log("Database connection error in blog_edit: " . $e->getMessage());
    $_SESSION['blog_error'] = 'Database connection failed';
    header('Location: blog_posts.php');
    exit;
}

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    $_SESSION['blog_error'] = 'Invalid post ID';
    header('Location: blog_posts.php');
    exit;
}

$formData = $_SESSION['blog_form_data'] ?? [];
$errors = $_SESSION['blog_errors'] ?? [];
unset($_SESSION['blog_form_data'], $_SESSION['blog_errors']);

try {
    $sql = "SELECT p.*, 
                   CONCAT(e.first_name, ' ', e.last_name) as employee_author,
                   c.company_name as client_author
            FROM blog_posts p
            LEFT JOIN employees e ON p.author_employee_id = e.id
            LEFT JOIN clients c ON p.author_client_id = c.id
            WHERE p.id = :id AND p.deleted_at IS NULL";
    
    $stmt = $db->query($sql, ['id' => $postId]);
    $post = $stmt->fetch();
    
    if (!$post) {
        $_SESSION['blog_error'] = 'Blog post not found';
        header('Location: blog_posts.php');
        exit;
    }
    
    $catStmt = $db->query("SELECT category_id FROM blog_post_categories WHERE post_id = :post_id", ['post_id' => $postId]);
    $postCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $tagStmt = $db->query("SELECT t.name FROM blog_tags t
               JOIN blog_post_tags pt ON t.id = pt.tag_id
               WHERE pt.post_id = :post_id", ['post_id' => $postId]);
    $postTags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
    $tagsString = implode(', ', $postTags);
    
} catch (Exception $e) {
    error_log("Error fetching post for edit: " . $e->getMessage());
    $_SESSION['blog_error'] = 'Error loading post for editing';
    header('Location: blog_posts.php');
    exit;
}

try {
    $categories = $db->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching categories: " . $e->getMessage());
    $categories = [];
}

try {
    $employees = $db->query("SELECT id, first_name, last_name, email FROM employees WHERE status = 'active' AND deleted_at IS NULL ORDER BY first_name")->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching employees: " . $e->getMessage());
    $employees = [];
}

try {
    $clients = $db->query("SELECT id, company_name, contact_person, email FROM clients WHERE status IN ('active', 'company', 'individual') AND deleted_at IS NULL ORDER BY company_name")->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching clients: " . $e->getMessage());
    $clients = [];
}

try {
    $mediaItems = $db->query("SELECT id, filename, original_filename, file_path FROM media WHERE media_type = 'image' ORDER BY created_at DESC LIMIT 20")->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching media: " . $e->getMessage());
    $mediaItems = [];
}

$featuredImage = null;
if ($post['featured_image_id']) {
    try {
        $imgStmt = $db->query("SELECT file_path, original_filename FROM media WHERE id = :id", ['id' => $post['featured_image_id']]);
        $featuredImage = $imgStmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching featured image: " . $e->getMessage());
    }
}

$currentUser = $auth->getUserData();

$pageActive = 'blog_edit';
$pageTitle = 'TPV Construction and Services LTD · Edit Post';
require 'inc/admin_header.php';
?>

<div data-pages="parallax">
    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="blog_posts.php">Blog</a></li>
                <li class="breadcrumb-item active">Edit Post</li>
            </ol>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="container-fluid p-l-25 p-r-25">
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
    <form id="blogEditForm" action="blog_update.php" method="post" enctype="multipart/form-data">
        <?php echo $auth->csrfField(); ?>
        <input type="hidden" name="post_id" value="<?php echo $postId; ?>">

        <div class="row">
            <!-- Main content column -->
            <div class="col-lg-8">

                <!-- Title -->
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Post title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg"
                                   placeholder="e.g. Topping out ceremony"
                                   value="<?php echo htmlspecialchars($formData['title'] ?? $post['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug (URL)</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted small">/</span>
                                <input type="text" name="slug" class="form-control"
                                       placeholder="auto-generate-from-title"
                                       value="<?php echo htmlspecialchars($formData['slug'] ?? $post['slug']); ?>">
                            </div>
                            <small class="text-muted">Leave empty to auto-generate from title</small>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i>Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Excerpt (short summary)</label>
                            <textarea name="excerpt" class="form-control" rows="2"
                                      placeholder="Brief preview of the post…"><?php echo htmlspecialchars($formData['excerpt'] ?? $post['excerpt']); ?></textarea>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold">Full content</label>
                        </div>

                        <!-- Toolbar -->
                        <div class="editor-toolbar border rounded-top p-2 bg-light d-flex flex-wrap gap-1">
                            <button type="button" onclick="execCmd('bold')" title="Bold"><i class="fas fa-bold"></i></button>
                            <button type="button" onclick="execCmd('italic')" title="Italic"><i class="fas fa-italic"></i></button>
                            <button type="button" onclick="execCmd('underline')" title="Underline"><i class="fas fa-underline"></i></button>
                            <span class="divider mx-1 text-muted">|</span>
                            <button type="button" onclick="execCmd('insertUnorderedList')" title="Bullet list"><i class="fas fa-list-ul"></i></button>
                            <button type="button" onclick="execCmd('insertOrderedList')" title="Numbered list"><i class="fas fa-list-ol"></i></button>
                            <span class="divider mx-1 text-muted">|</span>
                            <button type="button" onclick="execCmd('formatBlock', 'h2')" title="Heading"><i class="fas fa-heading"></i></button>
                            <button type="button" onclick="execCmd('formatBlock', 'p')" title="Paragraph"><i class="fas fa-paragraph"></i></button>
                            <span class="divider mx-1 text-muted">|</span>
                            <button type="button" onclick="insertLink()" title="Insert link"><i class="fas fa-link"></i></button>
                        </div>

                        <!-- Editor -->
                        <div class="editor-content border border-top-0 rounded-bottom p-3"
                             id="contentEditor"
                             contenteditable="true"
                             style="min-height:320px; background:#fff;"
                             placeholder="Write your blog post here..."><?php echo $formData['content'] ?? $post['content']; ?></div>

                        <textarea name="content" id="contentHidden" class="d-none"><?php echo htmlspecialchars($formData['content'] ?? $post['content']); ?></textarea>
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i>Use the toolbar to format text.</small>
                    </div>
                </div>

                <!-- Excerpt & Categories on mobile (visible below lg) -->
                <div class="d-lg-none">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i>Categories & Tags</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Categories <span class="text-danger">*</span></label>
                                <select name="categories[]" class="form-select" multiple required>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                        <?php echo (in_array($category['id'], (array)($formData['categories'] ?? $postCategories))) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Tags</label>
                                <input type="text" name="tags_string" class="form-control"
                                       value="<?php echo htmlspecialchars($formData['tags_string'] ?? $tagsString); ?>"
                                       placeholder="Comma-separated tags">
                                <small class="text-muted">Comma-separated; new tags are created automatically</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit (mobile) -->
                <div class="d-lg-none mb-4">
                    <div class="d-flex gap-2">
                        <a href="blog_posts.php" class="btn btn-outline-secondary flex-fill">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <a href="blog_view.php?id=<?php echo $postId; ?>" class="btn btn-info flex-fill">
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-save me-1"></i> Update
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar column -->
            <div class="col-lg-4">

                <!-- Status -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Publishing</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" id="statusSelect">
                                <option value="draft" <?php echo (($formData['status'] ?? $post['status']) === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo (($formData['status'] ?? $post['status']) === 'published') ? 'selected' : ''; ?>>Published</option>
                                <option value="scheduled" <?php echo (($formData['status'] ?? $post['status']) === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="pending_review" <?php echo (($formData['status'] ?? $post['status']) === 'pending_review') ? 'selected' : ''; ?>>Pending review</option>
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Published at</label>
                                <input type="date" name="published_at" class="form-control form-control-sm"
                                       value="<?php echo htmlspecialchars($formData['published_at'] ?? ($post['published_at'] ? date('Y-m-d', strtotime($post['published_at'])) : '')); ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Scheduled for</label>
                                <input type="date" name="scheduled_for" class="form-control form-control-sm"
                                       value="<?php echo htmlspecialchars($formData['scheduled_for'] ?? ($post['scheduled_for'] ? date('Y-m-d', strtotime($post['scheduled_for'])) : '')); ?>">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-semibold">Comments</label>
                            <select name="comment_status" class="form-select">
                                <option value="open" <?php echo (($formData['comment_status'] ?? $post['comment_status']) === 'open') ? 'selected' : ''; ?>>Open</option>
                                <option value="closed" <?php echo (($formData['comment_status'] ?? $post['comment_status']) === 'closed') ? 'selected' : ''; ?>>Closed</option>
                                <option value="disabled" <?php echo (($formData['comment_status'] ?? $post['comment_status']) === 'disabled') ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Author -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>Author</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="author_type" class="form-select" id="authorTypeSelect">
                                <option value="employee" <?php echo (($formData['author_type'] ?? $post['author_type']) === 'employee') ? 'selected' : ''; ?>>Employee</option>
                                <option value="client" <?php echo (($formData['author_type'] ?? $post['author_type']) === 'client') ? 'selected' : ''; ?>>Client</option>
                            </select>
                        </div>
                        <div id="authorEmployeeGroup" class="mb-3">
                            <label class="form-label fw-semibold">Employee</label>
                            <select name="author_employee_id" class="form-select">
                                <option value="">Select employee</option>
                                <?php foreach ($employees as $emp): ?>
                                <option value="<?php echo $emp['id']; ?>"
                                    <?php echo (($formData['author_employee_id'] ?? $post['author_employee_id']) == $emp['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="authorClientGroup" class="<?php echo (($formData['author_type'] ?? $post['author_type']) === 'client') ? '' : 'd-none'; ?>">
                            <label class="form-label fw-semibold">Client</label>
                            <select name="author_client_id" class="form-select">
                                <option value="">Select client</option>
                                <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>"
                                    <?php echo (($formData['author_client_id'] ?? $post['author_client_id']) == $client['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['company_name'] ?: $client['contact_person']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-image me-2"></i>Featured Image</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($featuredImage): ?>
                        <div class="current-image mb-3">
                            <label class="form-label small text-muted">Current:</label>
                            <div class="position-relative">
                                <img src="../<?php echo htmlspecialchars($featuredImage['file_path']); ?>"
                                     alt="Featured" class="img-fluid rounded border" style="max-height:140px;width:100%;object-fit:cover;">
                                <small class="text-muted d-block mt-1"><?php echo htmlspecialchars($featuredImage['original_filename']); ?></small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload new image</label>
                            <div class="file-upload-area" id="featuredImageUploadArea"
                                 onclick="document.getElementById('featuredImageUpload').click()">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-1 small fw-medium">Click to upload or drag & drop</p>
                                <p class="small text-muted mb-0">PNG, JPG, WebP up to 5MB</p>
                                <input type="file" id="featuredImageUpload" name="featured_image_upload"
                                       class="d-none" accept=".png,.jpg,.jpeg,.webp"
                                       onchange="previewFeaturedImage(this)">
                            </div>
                            <div id="featuredImagePreview" class="mt-3" style="display:none;">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                                    <img id="featuredPreviewImg" class="rounded border"
                                         style="width:64px;height:64px;object-fit:cover;">
                                    <div class="flex-fill">
                                        <p id="featuredFileName" class="mb-0 small fw-medium"></p>
                                        <p id="featuredFileSize" class="mb-0 small text-muted"></p>
                                    </div>
                                    <button type="button" onclick="removeFeaturedImage()"
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="featured_image_id" id="featuredImageId"
                                   value="<?php echo $post['featured_image_id']; ?>">
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Or pick from library</label>
                            <select name="featured_image_id_select" class="form-select">
                                <option value="">-- Keep current or select --</option>
                                <?php foreach ($mediaItems as $media): ?>
                                <option value="<?php echo $media['id']; ?>"
                                    <?php echo ($post['featured_image_id'] == $media['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($media['original_filename'] ?: $media['filename']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Uploaded new image takes priority over selected.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Categories & Tags (desktop) -->
                <div class="card d-none d-lg-block">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i>Categories & Tags</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Categories <span class="text-danger">*</span></label>
                            <select name="categories[]" class="form-select" multiple required>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo (in_array($category['id'], (array)($formData['categories'] ?? $postCategories))) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Tags</label>
                            <input type="text" name="tags_string" class="form-control"
                                   value="<?php echo htmlspecialchars($formData['tags_string'] ?? $tagsString); ?>"
                                   placeholder="Comma-separated tags">
                            <small class="text-muted">Comma-separated; new tags created automatically</small>
                        </div>
                    </div>
                </div>

                <!-- Submit (desktop) -->
                <div class="d-none d-lg-block">
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-save me-2"></i> Update Post
                        </button>
                        <div class="d-flex gap-2">
                            <a href="blog_view.php?id=<?php echo $postId; ?>" class="btn btn-info flex-fill">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            <a href="blog_posts.php" class="btn btn-outline-secondary flex-fill">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<style>
.editor-toolbar button {
    width: 34px; height: 34px;
    border: none; border-radius: 6px;
    background: transparent;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}
.editor-toolbar button:hover { background: #e2e8f0; color: #0f172a; }
.editor-toolbar button:active { background: #cbd5e1; }
.editor-content:empty::before {
    content: attr(placeholder);
    color: #94a3b8;
    pointer-events: none;
}
.editor-content:focus { outline: none; }
.editor-content img { max-width: 100%; height: auto; border-radius: 8px; }
.file-upload-area {
    border: 2px dashed #d1d9e6;
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #f8fafc;
}
.file-upload-area:hover {
    border-color: #d4a13e;
    background: #fffef5;
}
</style>

<?php
$extraScripts = <<<'SCRIPT'
<script>
function execCmd(cmd, val) {
    document.getElementById('contentEditor').focus();
    document.execCommand(cmd, false, val || null);
    updateContent();
}

function insertLink() {
    var url = prompt('Enter URL:', 'https://');
    if (url) {
        document.getElementById('contentEditor').focus();
        document.execCommand('createLink', false, url);
        updateContent();
    }
}

function updateContent() {
    document.getElementById('contentHidden').value = document.getElementById('contentEditor').innerHTML;
}

document.getElementById('contentEditor').addEventListener('input', updateContent);
document.getElementById('contentEditor').addEventListener('paste', function(e) {
    e.preventDefault();
    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
    document.execCommand('insertText', false, text);
    updateContent();
});

document.getElementById('blogEditForm').addEventListener('submit', function() {
    updateContent();
});

document.getElementById('authorTypeSelect').addEventListener('change', function() {
    var emp = document.getElementById('authorEmployeeGroup');
    var cli = document.getElementById('authorClientGroup');
    if (this.value === 'employee') {
        emp.classList.remove('d-none');
        cli.classList.add('d-none');
    } else {
        emp.classList.add('d-none');
        cli.classList.remove('d-none');
    }
});

function previewFeaturedImage(input) {
    var preview = document.getElementById('featuredImagePreview');
    var img = document.getElementById('featuredPreviewImg');
    var name = document.getElementById('featuredFileName');
    var size = document.getElementById('featuredFileSize');
    var hidden = document.getElementById('featuredImageId');

    if (input.files && input.files[0]) {
        var file = input.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
        name.textContent = file.name;
        var sz = file.size;
        size.textContent = sz >= 1048576 ? (sz / 1048576).toFixed(1) + ' MB' : (sz / 1024).toFixed(0) + ' KB';
        preview.style.display = 'block';
        hidden.value = '';
    }
}

function removeFeaturedImage() {
    document.getElementById('featuredImagePreview').style.display = 'none';
    document.getElementById('featuredImageUpload').value = '';
    document.getElementById('featuredImageId').value = '';
}

document.getElementById('featuredImageUploadArea').addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = '#d4a13e';
    this.style.background = '#fffef5';
});
document.getElementById('featuredImageUploadArea').addEventListener('dragleave', function() {
    this.style.borderColor = '#d1d9e6';
    this.style.background = '#f8fafc';
});
document.getElementById('featuredImageUploadArea').addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = '#d1d9e6';
    this.style.background = '#f8fafc';
    var files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('featuredImageUpload').files = files;
        previewFeaturedImage(document.getElementById('featuredImageUpload'));
    }
});
</script>
SCRIPT;
require 'inc/admin_footer.php';
?>
