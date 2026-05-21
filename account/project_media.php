<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

function ensureProjectMediaTable($db) {
    $db->query(
        "CREATE TABLE IF NOT EXISTS project_media (
            id INT AUTO_INCREMENT PRIMARY KEY,
            service VARCHAR(150) NOT NULL,
            title VARCHAR(255) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            file_path VARCHAR(255) NOT NULL,
            file_type ENUM('image', 'video') NOT NULL DEFAULT 'image',
            mime_type VARCHAR(150) DEFAULT NULL,
            file_size BIGINT DEFAULT NULL,
            featured TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_project_media_service (service),
            INDEX idx_project_media_type (file_type),
            INDEX idx_project_media_featured (featured),
            INDEX idx_project_media_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

ensureProjectMediaTable($db);

function mediaUrl($path) {
    if (!$path) return '';
    if (strpos($path, '/') === 0) {
        $path = preg_replace('#^.*?uploads/#', '', $path);
    }
    return UPLOAD_URL . $path;
}

function mediaPath($path) {
    if (!$path) return '';
    if (strpos($path, '/') !== 0) {
        return UPLOAD_PATH . $path;
    }
    return preg_replace('#^.*?uploads/#', UPLOAD_PATH, $path);
}

$services = [
    'Building Construction',
    'Architecture & Design',
    'Building Renovation',
    'Flooring & Roofing',
    'Building Maintenance',
    'Project Management',
    'Real Estate',
    'Interior & Exterior',
    'Steel & Fabrication',
];

$errors = [];
$success = false;

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $service = trim($_POST['service'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $featured = isset($_POST['featured']) ? 1 : 0;

        if (!in_array($service, $services)) {
            $errors[] = 'Invalid service selected.';
        }

        if (empty($_FILES['media_files']) || empty($_FILES['media_files']['name'][0])) {
            $errors[] = 'Please select at least one file to upload.';
        }

        if (empty($errors)) {
            $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $video_exts = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
            $allowed = array_merge($image_exts, $video_exts);
            $targetDir = UPLOAD_PATH . 'project_media';
            $maxSize = 50 * 1024 * 1024;
            $uploaded = 0;
            $fileErrors = [];

            foreach ($_FILES['media_files']['name'] as $i => $name) {
                if ($_FILES['media_files']['error'][$i] !== UPLOAD_ERR_OK) {
                    $fileErrors[] = "'$name' failed to upload (error code " . $_FILES['media_files']['error'][$i] . ')';
                    continue;
                }

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $file_type = in_array($ext, $image_exts) ? 'image' : (in_array($ext, $video_exts) ? 'video' : null);

                if (!$file_type) {
                    $fileErrors[] = "'$name' type not allowed (" . $ext . ')';
                    continue;
                }

                $file = [
                    'name' => $_FILES['media_files']['name'][$i],
                    'tmp_name' => $_FILES['media_files']['tmp_name'][$i],
                    'size' => $_FILES['media_files']['size'][$i],
                    'error' => $_FILES['media_files']['error'][$i],
                    'type' => $_FILES['media_files']['type'][$i],
                ];

                $uploadResult = $functions->uploadFile($file, $targetDir, $allowed, $maxSize);

                if (!$uploadResult['success']) {
                    $fileErrors[] = "'$name': " . implode(' ', $uploadResult['errors']);
                    continue;
                }

                $relativePath = 'project_media/' . $uploadResult['filename'];
                $db->query(
                    "INSERT INTO project_media (service, title, description, file_path, file_type, mime_type, file_size, featured)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [$service, $title, $description, $relativePath, $file_type, $uploadResult['mime_type'], $uploadResult['size'], $featured]
                );
                $uploaded++;
            }

            if ($uploaded > 0) {
                $_SESSION['toast_success'] = "$uploaded file(s) uploaded successfully.";
                if (!empty($fileErrors)) {
                    $_SESSION['toast_warning'] = 'Some files failed: ' . implode(' | ', $fileErrors);
                }
            } else {
                $_SESSION['toast_error'] = 'No files were uploaded. ' . (!empty($fileErrors) ? implode(' ', $fileErrors) : '');
            }
            header('Location: project_media.php');
            exit;
        }
    }

    if (!empty($errors)) {
        $_SESSION['toast_error'] = implode(' ', $errors);
        header('Location: project_media.php');
        exit;
    }
}

// Handle metadata update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $service = trim($_POST['service'] ?? '');
        if (!in_array($service, $services)) {
            $_SESSION['toast_error'] = 'Invalid service selected.';
        } else {
            $db->query(
                "UPDATE project_media SET service = ?, title = ?, description = ?, featured = ?, updated_at = NOW() WHERE id = ?",
                [$service, trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''), isset($_POST['featured']) ? 1 : 0, (int)$_POST['id']]
            );
            $_SESSION['toast_success'] = 'Media updated successfully.';
        }
    }
    header('Location: project_media.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $db->query("SELECT file_path FROM project_media WHERE id = ?", [$id]);
    $item = $stmt->fetch();
    if ($item) {
        $functions->deleteFile(mediaPath($item['file_path']));
        $db->query("DELETE FROM project_media WHERE id = ?", [$id]);
        $_SESSION['toast_success'] = 'Media deleted successfully.';
    }
    header('Location: project_media.php');
    exit;
}

// Handle featured toggle
if (isset($_GET['featured'])) {
    $id = (int) $_GET['featured'];
    $stmt = $db->query("SELECT featured FROM project_media WHERE id = ?", [$id]);
    $item = $stmt->fetch();
    if ($item) {
        $new = $item['featured'] ? 0 : 1;
        $db->query("UPDATE project_media SET featured = ? WHERE id = ?", [$new, $id]);
        $_SESSION['toast_success'] = $new ? 'Marked as featured.' : 'Removed featured status.';
    }
    header('Location: project_media.php');
    exit;
}

// Fetch all media
$filterService = $_GET['service'] ?? '';
if ($filterService && in_array($filterService, $services)) {
    $stmt = $db->query("SELECT * FROM project_media WHERE service = ? ORDER BY created_at DESC", [$filterService]);
} else {
    $stmt = $db->query("SELECT * FROM project_media ORDER BY created_at DESC");
}
$mediaItems = $stmt->fetchAll();
$editMedia = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editMedia = $db->query("SELECT * FROM project_media WHERE id = ?", [(int)$_GET['edit']])->fetch();
}

// Stats
$totalCount = count($mediaItems);
$imageCount = count(array_filter($mediaItems, fn($m) => $m['file_type'] === 'image'));
$videoCount = count(array_filter($mediaItems, fn($m) => $m['file_type'] === 'video'));
$featuredCount = count(array_filter($mediaItems, fn($m) => $m['featured']));

$pageActive = 'project_media';
$pageTitle = 'TPV Construction and Services LTD · Project Media';
require 'inc/admin_header.php';
?>

<style>
.project-media-page .metric-tile {
    background: #fff;
    border: 1px solid #e4e9f0;
    border-radius: 22px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
}
.project-media-page .metric-tile .value {
    font-size: 1.65rem;
    font-weight: 700;
    line-height: 1;
    color: #0f172a;
}
.project-media-page .metric-tile .label {
    margin-top: 0.25rem;
    font-size: 0.82rem;
    color: #6b7a8f;
}
.project-media-page .media-card {
    border: 1px solid #e4e9f0;
    border-radius: 24px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    overflow: hidden;
}
.project-media-page .media-card .card-header {
    background: #fff;
    border-bottom: 1px solid #edf2f7;
}
.project-media-page .media-toolbar {
    display: grid;
    grid-template-columns: minmax(0, 320px) auto;
    gap: 1rem;
    align-items: end;
    margin-bottom: 1.5rem;
}
.project-media-page .media-filter-form {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.project-media-page .media-filter-form .form-select { min-width: 240px; }
.project-media-page .media-empty {
    border: 1px dashed #d7dee8;
    border-radius: 22px;
    background: linear-gradient(180deg, #fbfcfe 0%, #f5f7fb 100%);
    padding: 3rem 1.5rem;
}
.project-media-page .pm-service-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.38rem 0.7rem;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 0.76rem;
    font-weight: 600;
}
.project-media-page .pm-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.36rem 0.65rem;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: capitalize;
}
.project-media-page .pm-type-badge.image { background: rgba(22, 163, 74, 0.12); color: #15803d; }
.project-media-page .pm-type-badge.video { background: rgba(2, 132, 199, 0.12); color: #0369a1; }
.project-media-page .pm-icon-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.project-media-page .pm-action-group {
    display: flex;
    gap: 0.4rem;
}
.project-media-page .pm-drop-area {
    cursor: pointer;
    border: 2px dashed #d1d9e6 !important;
    transition: all 0.2s ease;
    border-radius: 18px !important;
    background: linear-gradient(180deg, #fbfcfe 0%, #f5f7fb 100%) !important;
}
.project-media-page .pm-drop-area:hover {
    border-color: rgba(212, 161, 62, 0.6) !important;
    background: #fffdf7 !important;
}
.project-media-page .pm-modal .modal-content {
    border: 1px solid #e4e9f0;
    border-radius: 22px;
    box-shadow: 0 22px 50px rgba(15, 23, 42, 0.12);
}
.project-media-page .pm-modal .modal-body,
.project-media-page .pm-modal .modal-header,
.project-media-page .pm-modal .modal-footer {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}
@media (max-width: 991.98px) {
    .project-media-page .media-toolbar { grid-template-columns: 1fr; }
}
@media (max-width: 767.98px) {
    .project-media-page .metric-tile {
        padding: 1rem;
        border-radius: 18px;
    }
    .project-media-page .metric-tile .value { font-size: 1.35rem; }
    .project-media-page .media-card { border-radius: 20px; }
    .project-media-page .media-filter-form {
        flex-direction: column;
        align-items: stretch;
    }
    .project-media-page .media-filter-form .form-select,
    .project-media-page .media-filter-form .btn {
        width: 100%;
        min-width: 0;
    }
    .project-media-page .pm-action-group { flex-wrap: wrap; }
}
</style>

<div class="project-media-page">
<div data-pages="parallax">
    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                <li class="breadcrumb-item active">Project Media</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-20">
    <div class="row m-b-20">
        <div class="col-md-3 col-sm-6">
            <div class="metric-tile d-flex align-items-center justify-content-between">
                <div><i class="fas fa-photo-video" style="color: #d4a13e; font-size: 1.5rem;"></i></div>
                <div class="text-end">
                    <div class="value"><?php echo $totalCount; ?></div>
                    <div class="label">Total media</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-tile d-flex align-items-center justify-content-between">
                <div><i class="fas fa-image" style="color: #d4a13e; font-size: 1.5rem;"></i></div>
                <div class="text-end">
                    <div class="value"><?php echo $imageCount; ?></div>
                    <div class="label">Images</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-tile d-flex align-items-center justify-content-between">
                <div><i class="fas fa-video" style="color: #d4a13e; font-size: 1.5rem;"></i></div>
                <div class="text-end">
                    <div class="value"><?php echo $videoCount; ?></div>
                    <div class="label">Videos</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-tile d-flex align-items-center justify-content-between">
                <div><i class="fas fa-star" style="color: #d4a13e; font-size: 1.5rem;"></i></div>
                <div class="text-end">
                    <div class="value"><?php echo $featuredCount; ?></div>
                    <div class="label">Featured</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card media-card">
        <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
            <div class="card-title fw-bold fs-5 mb-0">
                <i class="fas fa-photo-video me-2"></i> Project Media Gallery
            </div>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-1"></i> Upload Media
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Filter by service -->
            <div class="media-toolbar">
                <div>
                    <div class="text-muted small mb-1">Library filter</div>
                    <h6 class="fw-semibold mb-0">Browse media by service</h6>
                </div>
                <form method="GET" class="media-filter-form">
                        <select name="service" class="form-select" onchange="this.form.submit()">
                            <option value="">All Services</option>
                            <?php foreach ($services as $s): ?>
                                <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $filterService === $s ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <?php if ($filterService): ?>
                            <a href="project_media.php" class="btn btn-outline-secondary">Clear</a>
                        <?php endif; ?>
                </form>
            </div>

            <?php if (empty($mediaItems)): ?>
                <div class="media-empty text-center">
                    <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                    <h5 class="fw-semibold mb-2">No media uploaded yet</h5>
                    <p class="text-muted mb-0">Upload your first project image or video to start building the service gallery.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table data-table" data-page-length="25">
                        <thead>
                            <tr>
                                <th style="width:60px">Preview</th>
                                <th>Title</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th style="width:80px">Featured</th>
                                <th>Uploaded</th>
                                <th style="width:100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mediaItems as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['file_type'] === 'image'): ?>
                                            <img src="<?php echo htmlspecialchars(mediaUrl($item['file_path'])); ?>" alt=""
                                                 style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                        <?php else: ?>
                                            <div style="width:50px;height:50px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-video" style="color:#d4a13e;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['title'] ?: '(untitled)'); ?></strong>
                                        <?php if ($item['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($item['description'], 0, 80)) . (strlen($item['description']) > 80 ? '...' : ''); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="pm-service-badge"><?php echo htmlspecialchars($item['service']); ?></span></td>
                                    <td>
                                        <span class="pm-type-badge <?php echo $item['file_type'] === 'image' ? 'image' : 'video'; ?>">
                                            <?php echo $item['file_type']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $size = $item['file_size'];
                                        if ($size >= 1048576) {
                                            echo round($size / 1048576, 1) . ' MB';
                                        } elseif ($size >= 1024) {
                                            echo round($size / 1024, 0) . ' KB';
                                        } else {
                                            echo $size . ' B';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?featured=<?php echo $item['id']; ?><?php echo $filterService ? '&service=' . urlencode($filterService) : ''; ?>"
                                           class="btn btn-sm pm-icon-btn <?php echo $item['featured'] ? 'btn-warning' : 'btn-outline-secondary'; ?>"
                                           title="<?php echo $item['featured'] ? 'Remove featured' : 'Mark as featured'; ?>">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($item['created_at'])); ?></td>
                                    <td>
                                        <div class="pm-action-group">
                                            <a href="<?php echo htmlspecialchars(mediaUrl($item['file_path'])); ?>"
                                               class="btn btn-sm btn-outline-secondary pm-icon-btn" target="_blank" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?edit=<?php echo $item['id']; ?>"
                                               class="btn btn-sm btn-outline-secondary pm-icon-btn" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="?delete=<?php echo $item['id']; ?>"
                                               class="btn btn-sm btn-outline-danger pm-icon-btn"
                                               onclick="return confirmAction(this, 'Delete this media item?')"
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade pm-modal" id="editMediaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-pen me-2"></i>Edit Project Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken() ?? ''; ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($editMedia['id'] ?? ''); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service <span class="text-danger">*</span></label>
                            <select name="service" class="form-select" required>
                                <option value="">Select service…</option>
                                <?php foreach ($services as $s): ?>
                                    <option value="<?php echo htmlspecialchars($s); ?>" <?php echo ($editMedia['service'] ?? '') === $s ? 'selected' : ''; ?>><?php echo htmlspecialchars($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($editMedia['title'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editMedia['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="featured" id="editFeaturedCheck" class="form-check-input" value="1" <?php echo !empty($editMedia['featured']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="editFeaturedCheck">
                            <i class="fas fa-star text-warning me-1"></i> Mark as featured
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="project_media.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade pm-modal" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Project Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken() ?? ''; ?>">
                    <input type="hidden" name="action" value="upload">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service <span class="text-danger">*</span></label>
                            <select name="service" class="form-select" required>
                                <option value="">Select service…</option>
                                <?php foreach ($services as $s): ?>
                                    <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. 3-Bedroom Bungalow">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief description of the project/media…"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Files <span class="text-danger">*</span></label>
                        <div class="border rounded-3 p-4 text-center bg-light pm-drop-area" id="dropArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="mb-1"><strong>Click to upload</strong> or drag & drop</p>
                            <p class="text-muted small mb-2">
                                Images: JPG, PNG, GIF, WebP, SVG &bull; Videos: MP4, WebM, OGG, MOV, AVI &bull; Max 50MB each
                            </p>
                            <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>You can select multiple files at once</p>
                            <input type="file" name="media_files[]" id="mediaFileInput" class="d-none" multiple
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.mp4,.webm,.ogg,.mov,.avi">
                        </div>
                        <div id="fileInfo" class="mt-2 d-none"></div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="featured" id="featuredCheck" class="form-check-input" value="1">
                        <label class="form-check-label" for="featuredCheck">
                            <i class="fas fa-star text-warning me-1"></i> Mark as featured
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="fas fa-upload me-1"></i> Upload
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($editMedia): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('editMediaModal')).show();
});
</script>
<?php endif; ?>

<?php
$extraScripts = <<<'SCRIPT'
<script>
document.querySelectorAll('.data-table').forEach(function(table) {
    table.classList.add('align-middle');
});

document.getElementById('dropArea').addEventListener('click', function() {
    document.getElementById('mediaFileInput').click();
});

document.getElementById('mediaFileInput').addEventListener('change', function() {
    var fi = document.getElementById('fileInfo');
    if (this.files.length > 0) {
        var html = '<div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-check-circle text-success"></i> <strong>' + this.files.length + ' file(s) selected</strong></div><ul class="list-group list-group-flush small" style="max-height:200px;overflow-y:auto;">';
        for (var j = 0; j < this.files.length; j++) {
            var f = this.files[j];
            var size = f.size;
            var sizeStr = '';
            if (size >= 1048576) sizeStr = (size / 1048576).toFixed(1) + ' MB';
            else if (size >= 1024) sizeStr = (size / 1024).toFixed(0) + ' KB';
            else sizeStr = size + ' B';
            var icon = f.type.indexOf('video') > -1 ? 'fa-video' : 'fa-image';
            html += '<li class="list-group-item px-2 py-1 d-flex align-items-center gap-2"><i class="fas ' + icon + ' text-muted"></i> ' + f.name + ' <span class="text-muted">(' + sizeStr + ')</span></li>';
        }
        html += '</ul>';
        fi.innerHTML = html;
        fi.classList.remove('d-none');
        document.getElementById('dropArea').style.borderColor = '#059669';
    } else {
        fi.classList.add('d-none');
        document.getElementById('dropArea').style.borderColor = '#d1d9e6';
    }
});

document.addEventListener('dragover', function(e) { e.preventDefault(); });
document.addEventListener('drop', function(e) { e.preventDefault(); });

var dropArea = document.getElementById('dropArea');
dropArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = '#d4a13e';
    this.style.background = '#fff8e1';
});
dropArea.addEventListener('dragleave', function() {
    this.style.borderColor = '#d1d9e6';
    this.style.background = '#f8fafc';
});
dropArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = '#d1d9e6';
    this.style.background = '#f8fafc';
    var files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('mediaFileInput').files = files;
        document.getElementById('mediaFileInput').dispatchEvent(new Event('change'));
    }
});
</script>
SCRIPT;
require 'inc/admin_footer.php';
?>
</div>
