<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$functions = Functions::getInstance();

// Get form data from session if there was an error
$formData = $_SESSION['blog_form_data'] ?? [];
$errors = $_SESSION['blog_errors'] ?? [];
unset($_SESSION['blog_form_data'], $_SESSION['blog_errors']);

// Fetch categories from database
$categories = $blog->getCategories();

// Fetch employees for author dropdown
$db = Database::getInstance();
$employees = $db->query("SELECT id, first_name, last_name, email FROM employees WHERE status = 'active' AND deleted_at IS NULL ORDER BY first_name")->fetchAll();

// Fetch clients for author dropdown
$clients = $db->query("SELECT id, company_name, contact_person, email FROM clients WHERE status IN ('active', 'company', 'individual') AND deleted_at IS NULL ORDER BY company_name")->fetchAll();

// Get current user for default author
$currentUser = $auth->getUserData();
$defaultEmployeeId = $currentUser['employee_id'] ?? 1;

$pageActive = 'blog_new';
$pageTitle = 'TPV Construction and Services LTD · New Post';
require 'inc/admin_header.php';
?>
            <!-- Breadcrumb & page header -->
            <div data-pages="parallax">
                <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <ol class="breadcrumb sm-p-b-5">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="blog_posts.php">Blog</a></li>
                            <li class="breadcrumb-item active">Create new post</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
            <div class="container-fluid p-l-25 p-r-25">
                <div class="alert alert-danger alert-custom">
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

            <!-- MAIN FORM CARD -->
            <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
                <div class="card card-default">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="h3"><i class="fas fa-pen-fancy me-2"></i>Write new blog article</span>
                        </div>
                        <div class="card-controls">
                            <ul>
                                <li><a href="#" class="card-refresh text-black" data-toggle="refresh"><i class="fas fa-sync-alt"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <form role="form" id="blogPostForm" action="blog_save.php" method="post" enctype="multipart/form-data">
                            <?php echo $auth->csrfField(); ?>
                            
                            <!-- SECTION 1: TITLE & AUTHOR -->
                            <div class="blog-form-section">
                                <h5 class="text-uppercase text-primary"><i class="fas fa-info-circle me-2"></i>Basic information</h5>
                                <div class="row m-t-20">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-default required">
                                            <label>Post title</label>
                                            <input type="text" name="title" class="form-control" 
                                                   placeholder="e.g. Topping out ceremony" 
                                                   value="<?php echo htmlspecialchars($formData['title'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group form-group-default">
                                            <label>Slug (URL-friendly)</label>
                                            <input type="text" name="slug" class="form-control" 
                                                   placeholder="auto-generate or custom"
                                                   value="<?php echo htmlspecialchars($formData['slug'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default">
                                            <label>Author type</label>
                                            <select name="author_type" class="full-width" id="authorTypeSelect" data-init-plugin="select2">
                                                <option value="employee" <?php echo ($formData['author_type'] ?? 'employee') === 'employee' ? 'selected' : ''; ?>>Employee</option>
                                                <option value="client" <?php echo ($formData['author_type'] ?? '') === 'client' ? 'selected' : ''; ?>>Client</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group form-group-default" id="authorEmployeeGroup">
                                            <label>Author (employee)</label>
                                            <select name="author_employee_id" class="full-width" data-init-plugin="select2">
                                                <option value="">Select employee</option>
                                                <?php foreach ($employees as $emp): ?>
                                                <option value="<?php echo $emp['id']; ?>" 
                                                    <?php echo (isset($formData['author_employee_id']) && $formData['author_employee_id'] == $emp['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group form-group-default d-none" id="authorClientGroup">
                                            <label>Author (client)</label>
                                            <select name="author_client_id" class="full-width" data-init-plugin="select2">
                                                <option value="">Select client</option>
                                                <?php foreach ($clients as $client): ?>
                                                <option value="<?php echo $client['id']; ?>"
                                                    <?php echo (isset($formData['author_client_id']) && $formData['author_client_id'] == $client['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($client['company_name'] ?: $client['contact_person']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: CONTENT & EXCERPT (with new editor) -->
                            <div class="blog-form-section">
                                <h5 class="text-uppercase text-primary"><i class="fas fa-file-alt me-2"></i>Content</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>Excerpt (short summary)</label>
                                            <textarea name="excerpt" class="form-control" rows="2" 
                                                      placeholder="Brief preview…"><?php echo htmlspecialchars($formData['excerpt'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label>Full content</label>
                                        <!-- Editor toolbar -->
                                        <div class="editor-toolbar">
                                            <button type="button" onclick="formatText('bold')" title="Bold"><i class="fas fa-bold"></i></button>
                                            <button type="button" onclick="formatText('italic')" title="Italic"><i class="fas fa-italic"></i></button>
                                            <button type="button" onclick="formatText('underline')" title="Underline"><i class="fas fa-underline"></i></button>
                                            <span class="divider"></span>
                                            <button type="button" onclick="insertList('ul')" title="Bullet list"><i class="fas fa-list-ul"></i></button>
                                            <button type="button" onclick="insertList('ol')" title="Numbered list"><i class="fas fa-list-ol"></i></button>
                                            <span class="divider"></span>
                                            <button type="button" onclick="insertLink()" title="Insert link"><i class="fas fa-link"></i></button>
                                        </div>
                                        <!-- Contenteditable editor -->
                                        <div class="text-editor" 
                                             id="contentEditor" 
                                             contenteditable="true"
                                             placeholder="Write your blog post here... You can use basic HTML formatting."><?php echo $formData['content'] ?? ''; ?></div>
                                        <!-- Hidden textarea to submit content -->
                                        <textarea name="content" id="contentHidden" class="d-none"><?php echo htmlspecialchars($formData['content'] ?? ''); ?></textarea>
                                        <span class="help-text text-muted small"><i class="fas fa-info-circle me-1"></i>Use the toolbar to format text.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 3: FEATURED IMAGE (updated with school logo pattern) -->
                            <div class="blog-form-section">
                                <h5 class="text-uppercase text-primary"><i class="fas fa-image me-2"></i>Featured image</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload new image</label>
                                            <div class="file-upload-area" onclick="document.getElementById('featuredImageUpload').click()" id="featuredImageUploadArea">
                                                <i class="fas fa-cloud-upload-alt text-4xl text-slate-400 mb-4"></i>
                                                <p class="text-sm font-medium text-slate-700 mb-1">Click to upload or drag & drop</p>
                                                <p class="text-xs text-slate-500">PNG, JPG, WebP up to 5MB (Recommended: 1200×630px)</p>
                                                <input type="file"
                                                    id="featuredImageUpload"
                                                    name="featured_image_upload"
                                                    class="hidden"
                                                    accept=".png,.jpg,.jpeg,.webp"
                                                    onchange="previewFeaturedImage(this)">
                                            </div>
                                            <div id="featuredImagePreview" class="mt-4 hidden">
                                                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
                                                    <img id="featuredPreviewImg" class="w-16 h-16 rounded-lg object-cover border-2 border-slate-200">
                                                    <div class="flex-1">
                                                        <p id="featuredFileName" class="text-sm font-medium text-slate-700"></p>
                                                        <p id="featuredFileSize" class="text-xs text-slate-500"></p>
                                                    </div>
                                                    <button type="button"
                                                        onclick="removeFeaturedImage()"
                                                        class="text-red-500 hover:text-red-700 p-2">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Or select existing image</label>
                                            <select name="featured_image_id_select" class="full-width" data-init-plugin="select2">
                                                <option value="">-- none --</option>
                                                <?php
                                                // Fetch recent media items
                                                $mediaItems = $db->query("SELECT id, filename, original_filename FROM media WHERE media_type = 'image' ORDER BY created_at DESC LIMIT 10")->fetchAll();
                                                foreach ($mediaItems as $media):
                                                ?>
                                                <option value="<?php echo $media['id']; ?>"
                                                    <?php echo (isset($formData['featured_image_id_select']) && $formData['featured_image_id_select'] == $media['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($media['original_filename'] ?: $media['filename']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 4: CATEGORIES & TAGS -->
                            <div class="blog-form-section">
                                <h5 class="text-uppercase text-primary"><i class="fas fa-tags me-2"></i>Categories & tags</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default form-group-default-select2 required">
                                            <label>Categories (at least one)</label>
                                            <select name="categories[]" class="full-width" multiple data-init-plugin="select2" required>
                                                <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>"
                                                    <?php echo (isset($formData['categories']) && in_array($category['id'], (array)$formData['categories'])) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Tags</label>
                                            <input type="text" name="tags_string" class="tagsinput custom-tag-input" 
                                                   data-role="tagsinput" value="<?php echo htmlspecialchars($formData['tags_string'] ?? 'construction, safety, lagos'); ?>">
                                            <span class="help-text text-muted small"><i class="fas fa-info-circle me-1"></i>Comma-separated; new tags will be created automatically</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 5: STATUS & PUBLISHING -->
                            <div class="blog-form-section">
                                <h5 class="text-uppercase text-primary"><i class="fas fa-clock me-2"></i>Publishing options</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Status</label>
                                            <select name="status" class="full-width" data-init-plugin="select2" id="statusSelect">
                                                <option value="draft" <?php echo ($formData['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                <option value="published" <?php echo ($formData['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                                <option value="scheduled" <?php echo ($formData['status'] ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                                <option value="pending_review" <?php echo ($formData['status'] ?? 'pending_review') === 'pending_review' ? 'selected' : ''; ?>>Pending review</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Comment status</label>
                                            <select name="comment_status" class="full-width" data-init-plugin="select2">
                                                <option value="open" <?php echo ($formData['comment_status'] ?? 'open') === 'open' ? 'selected' : ''; ?>>Open</option>
                                                <option value="closed" <?php echo ($formData['comment_status'] ?? '') === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                                <option value="disabled" <?php echo ($formData['comment_status'] ?? '') === 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default input-group">
                                            <div class="form-input-group">
                                                <label>Published at</label>
                                                <input type="text" name="published_at" class="form-control datepicker" 
                                                       placeholder="now (if published)"
                                                       value="<?php echo htmlspecialchars($formData['published_at'] ?? ''); ?>">
                                            </div>
                                            <div class="input-group-append"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default input-group">
                                            <div class="form-input-group">
                                                <label>Scheduled for</label>
                                                <input type="text" name="scheduled_for" class="form-control datepicker" 
                                                       placeholder="if scheduled"
                                                       value="<?php echo htmlspecialchars($formData['scheduled_for'] ?? ''); ?>">
                                            </div>
                                            <div class="input-group-append"><span class="input-group-text"><i class="far fa-clock"></i></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <a href="blog_posts.php" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="reset" class="btn btn-outline-warning me-2">
                                        <i class="fas fa-undo me-2"></i>Clear
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save blog post
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
<?php require 'inc/admin_footer.php'; ?>
