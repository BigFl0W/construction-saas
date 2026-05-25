<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/classes/Auth.php';
require_once dirname(__DIR__) . '/classes/ServiceContent.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$serviceManager = new ServiceContent();
$registry = ServiceContent::getRegistry();

function serviceManagerUploadAsset(string $fileKey, string $targetName, string $subDirectory): ?string {
    if (!isset($_FILES[$fileKey]) || !is_array($_FILES[$fileKey]) || ($_FILES[$fileKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fileKey];
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new Exception('Failed to upload ' . str_replace('_', ' ', $fileKey) . '.');
    }

    if (($file['size'] ?? 0) > MAX_FILE_SIZE) {
        throw new Exception('Uploaded file exceeds the 5MB size limit.');
    }

    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
    if (!in_array($extension, $allowed, true)) {
        throw new Exception('Only PNG, JPG, JPEG, SVG, and WEBP files are allowed.');
    }

    $assetDir = rtrim(UPLOAD_PATH, '/\\') . '/' . trim($subDirectory, '/\\') . '/';
    if (!is_dir($assetDir)) {
        mkdir($assetDir, 0755, true);
    }

    foreach (glob($assetDir . $targetName . '.*') ?: [] as $existingFile) {
        if (is_file($existingFile)) {
            @unlink($existingFile);
        }
    }

    $relativePath = 'uploads/' . trim($subDirectory, '/\\') . '/' . $targetName . '.' . $extension;
    $absolutePath = dirname(__DIR__) . '/' . $relativePath;

    if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
        throw new Exception('Unable to save uploaded service asset.');
    }

    return $relativePath;
}

$selectedSlug = $_GET['service'] ?? array_key_first($registry);
if (!isset($registry[$selectedSlug])) {
    $selectedSlug = array_key_first($registry);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedSlug = $_POST['service_slug'] ?? '';
    if (!isset($registry[$postedSlug])) {
        $_SESSION['toast_error'] = 'Invalid service selected.';
        header('Location: service_manager.php');
        exit;
    }

    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
        header('Location: service_manager.php?service=' . urlencode($postedSlug));
        exit;
    }

    try {
        $current = $serviceManager->getResolvedBySlug($postedSlug);
        $payload = [];
        foreach (ServiceContent::getEditableColumns() as $column) {
            $payload[$column] = isset($_POST['service'][$column])
                ? trim((string) $_POST['service'][$column])
                : ($current[$column] ?? null);
        }

        $assetDir = $registry[$postedSlug]['asset_dir'];
        $assetMap = [
            'gallery_1_file' => ['column' => 'gallery_1', 'target' => 'gallery-1'],
            'gallery_2_file' => ['column' => 'gallery_2', 'target' => 'gallery-2'],
            'gallery_3_file' => ['column' => 'gallery_3', 'target' => 'gallery-3'],
            'gallery_4_file' => ['column' => 'gallery_4', 'target' => 'gallery-4'],
            'gallery_5_file' => ['column' => 'gallery_5', 'target' => 'gallery-5'],
            'gallery_6_file' => ['column' => 'gallery_6', 'target' => 'gallery-6'],
            'sustainable_image_1_file' => ['column' => 'sustainable_image_1', 'target' => 'sustainable-1'],
            'sustainable_image_2_file' => ['column' => 'sustainable_image_2', 'target' => 'sustainable-2'],
            'cta_image_file' => ['column' => 'cta_image', 'target' => 'cta-image'],
            'contact_image_file' => ['column' => 'contact_image', 'target' => 'contact-image'],
        ];

        foreach ($assetMap as $fileKey => $config) {
            $uploadedPath = serviceManagerUploadAsset($fileKey, $config['target'], $assetDir);
            if ($uploadedPath !== null) {
                $payload[$config['column']] = $uploadedPath;
            }
        }

        $serviceManager->save($postedSlug, $payload);
        $_SESSION['toast_success'] = $registry[$postedSlug]['name'] . ' content updated successfully.';
        header('Location: service_manager.php?service=' . urlencode($postedSlug));
        exit;
    } catch (Throwable $e) {
        $_SESSION['toast_error'] = $e->getMessage();
        header('Location: service_manager.php?service=' . urlencode($postedSlug));
        exit;
    }
}

$services = $serviceManager->getAllResolved();
$currentService = $services[$selectedSlug] ?? $serviceManager->getResolvedBySlug($selectedSlug);

$pageActive = 'service_manager';
$pageTitle = 'TPV Construction and Services LTD · Service Manager';
require __DIR__ . '/inc/admin_header.php';
?>

<style>
.service-manager .service-switcher {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1rem;
}
.service-manager .service-switcher-card {
    background: #fff;
    border: 1px solid #e4e9f0;
    border-radius: 20px;
    padding: 1rem 1.1rem;
    display: flex;
    gap: 0.9rem;
    align-items: flex-start;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    color: inherit;
}
.service-manager .service-switcher-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
}
.service-manager .service-switcher-card.active {
    border-color: rgba(212, 161, 62, 0.55);
    box-shadow: 0 18px 34px rgba(212, 161, 62, 0.12);
    background: linear-gradient(180deg, #fffdf7 0%, #ffffff 100%);
}
.service-manager .service-switcher-card .icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: rgba(212, 161, 62, 0.14);
    color: #d4a13e;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.service-manager .service-switcher-card h6 {
    margin: 0 0 0.25rem;
    font-size: 0.95rem;
}
.service-manager .service-switcher-card .active-note {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    margin-top: 0.45rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #b88422;
}
.service-manager .service-switcher-card p {
    margin: 0;
    color: #6b7a8f;
    font-size: 0.78rem;
}
.service-manager .editor-card {
    border: 1px solid #e4e9f0;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
}
.service-manager .editor-card .card-header {
    background: #fff;
    border-bottom: 1px solid #edf2f7;
}
.service-manager .preview-box {
    border: 1px solid #e4e9f0;
    border-radius: 14px;
    background: #f8fafc;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.service-manager .preview-box img {
    max-width: 100%;
    max-height: 138px;
    object-fit: contain;
}
</style>

<div class="service-manager">
    <div data-pages="parallax">
        <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
            <div class="inner">
                <ol class="breadcrumb sm-p-b-5">
                    <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                    <li class="breadcrumb-item active">Service Manager</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 m-b-20">
            <div>
                <h1 class="m-b-5" style="font-size:1.55rem;">Service Content Manager</h1>
                <p class="text-muted m-0">Manage all service pages from one place with structured content and media.</p>
                <p class="m-0" style="color:#b88422;font-weight:700;">Currently editing: <?php echo htmlspecialchars($registry[$selectedSlug]['name']); ?></p>
            </div>
            <a href="<?php echo htmlspecialchars('../services/' . $selectedSlug . '/'); ?>" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                <i class="fas fa-arrow-up-right-from-square me-2"></i>Preview <?php echo htmlspecialchars($registry[$selectedSlug]['name']); ?>
            </a>
        </div>

        <div class="service-switcher m-b-25">
            <?php foreach ($registry as $slug => $config): $item = $services[$slug] ?? null; ?>
                <a href="service_manager.php?service=<?php echo urlencode($slug); ?>" class="service-switcher-card <?php echo $slug === $selectedSlug ? 'active' : ''; ?>">
                    <span class="icon"><i class="<?php echo htmlspecialchars($config['icon']); ?>"></i></span>
                    <div>
                        <h6><?php echo htmlspecialchars($config['name']); ?></h6>
                        <p><?php echo !empty($item['is_custom']) ? 'Custom content active' : 'Using legacy settings fallback'; ?></p>
                        <?php if ($slug === $selectedSlug): ?>
                            <span class="active-note"><i class="fas fa-pen"></i>Editing this service</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <form method="POST" enctype="multipart/form-data" class="editor-card card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3 p-4">
                <div>
                    <h5 class="m-0"><?php echo htmlspecialchars($registry[$selectedSlug]['name']); ?></h5>
                    <small class="text-muted">Edit the content that appears on the <?php echo htmlspecialchars($registry[$selectedSlug]['name']); ?> service page.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge <?php echo ($currentService['status'] ?? 'published') === 'draft' ? 'bg-secondary' : 'bg-success'; ?>">
                        <?php echo htmlspecialchars(ucfirst($currentService['status'] ?? 'published')); ?>
                    </span>
                    <?php if (!empty($currentService['is_custom'])): ?>
                        <span class="badge bg-warning text-dark">Custom</span>
                    <?php else: ?>
                        <span class="badge bg-light text-dark border">Fallback</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-4">
                <?php echo $auth->csrfField(); ?>
                <input type="hidden" name="service_slug" value="<?php echo htmlspecialchars($selectedSlug); ?>">

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Service Name</label>
                        <input type="text" name="service[name]" class="form-control" value="<?php echo htmlspecialchars($currentService['name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Hero Eyebrow</label>
                        <input type="text" name="service[hero_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['hero_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="service[status]" class="form-select">
                            <option value="published" <?php echo ($currentService['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo ($currentService['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Page Title</label>
                        <input type="text" name="service[page_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['page_title'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">SEO Description</label>
                        <textarea name="service[seo_description]" class="form-control" rows="2"><?php echo htmlspecialchars($currentService['seo_description'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-12"><hr class="my-1"><h6 class="mb-0">Hero & Overview</h6></div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Overview Title</label>
                        <input type="text" name="service[overview_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['overview_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Section Eyebrow</label>
                        <input type="text" name="service[overview_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['overview_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Main Content Intro</label>
                        <textarea name="service[content_body]" class="form-control" rows="4"><?php echo htmlspecialchars($currentService['content_body'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hero Secondary Button Text</label>
                        <input type="text" name="service[hero_secondary_button_text]" class="form-control" value="<?php echo htmlspecialchars($currentService['hero_secondary_button_text'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hero Secondary Button Link</label>
                        <input type="text" name="service[hero_secondary_button_link]" class="form-control" value="<?php echo htmlspecialchars($currentService['hero_secondary_button_link'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Overview Body</label>
                        <textarea name="service[overview_body]" class="form-control" rows="4"><?php echo htmlspecialchars($currentService['overview_body'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Hero Empty State Note</label>
                        <textarea name="service[hero_empty_note]" class="form-control" rows="2"><?php echo htmlspecialchars($currentService['hero_empty_note'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-12"><hr class="my-1"><h6 class="mb-0">Hero Side Cards</h6></div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Side Card 1 Title</label>
                        <input type="text" name="service[highlight_2_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['highlight_2_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Side Card 2 Title</label>
                        <input type="text" name="service[highlight_3_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['highlight_3_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Side Card 1 Body</label>
                        <textarea name="service[highlight_2_body]" class="form-control" rows="3"><?php echo htmlspecialchars($currentService['highlight_2_body'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Side Card 2 Body</label>
                        <textarea name="service[highlight_3_body]" class="form-control" rows="3"><?php echo htmlspecialchars($currentService['highlight_3_body'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-12"><hr class="my-1"><h6 class="mb-0">Feature Points</h6></div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Benefits Eyebrow</label>
                        <input type="text" name="service[benefits_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['benefits_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Benefits Title</label>
                        <input type="text" name="service[benefits_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['benefits_title'] ?? ''); ?>">
                    </div>
                    <?php for ($featureIndex = 1; $featureIndex <= 5; $featureIndex++): ?>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Feature <?php echo $featureIndex; ?></label>
                            <input type="text" name="service[feature_<?php echo $featureIndex; ?>]" class="form-control" value="<?php echo htmlspecialchars($currentService['feature_' . $featureIndex] ?? ''); ?>">
                        </div>
                    <?php endfor; ?>

                    <div class="col-12"><hr class="my-1"><h6 class="mb-0">Sustainable / Story Section</h6></div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Section Title</label>
                        <input type="text" name="service[sustainable_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['sustainable_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Body Paragraph 1</label>
                        <textarea name="service[sustainable_body_1]" class="form-control" rows="3"><?php echo htmlspecialchars($currentService['sustainable_body_1'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Body Paragraph 2</label>
                        <textarea name="service[sustainable_body_2]" class="form-control" rows="3"><?php echo htmlspecialchars($currentService['sustainable_body_2'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-12"><hr class="my-1"><h6 class="mb-0">Process</h6></div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Eyebrow</label>
                        <input type="text" name="service[process_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['process_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Process Title</label>
                        <input type="text" name="service[process_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['process_title'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Process Body</label>
                        <textarea name="service[process_body]" class="form-control" rows="3"><?php echo htmlspecialchars($currentService['process_body'] ?? ''); ?></textarea>
                    </div>
                    <?php for ($step = 1; $step <= 3; $step++): ?>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Step <?php echo $step; ?> Title</label>
                            <input type="text" name="service[step_<?php echo $step; ?>_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['step_' . $step . '_title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Step <?php echo $step; ?> Body</label>
                            <textarea name="service[step_<?php echo $step; ?>_body]" class="form-control" rows="2"><?php echo htmlspecialchars($currentService['step_' . $step . '_body'] ?? ''); ?></textarea>
                        </div>
                    <?php endfor; ?>

                    <div class="col-12"><hr class="my-1"><h6 class="mb-0">Call To Action & Contact</h6></div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gallery Eyebrow</label>
                        <input type="text" name="service[gallery_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['gallery_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Gallery Title</label>
                        <input type="text" name="service[gallery_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['gallery_title'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Gallery Empty State Note</label>
                        <textarea name="service[gallery_empty_note]" class="form-control" rows="2"><?php echo htmlspecialchars($currentService['gallery_empty_note'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">CTA Eyebrow</label>
                        <input type="text" name="service[cta_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['cta_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Title</label>
                        <textarea name="service[cta_title]" class="form-control" rows="2"><?php echo htmlspecialchars($currentService['cta_title'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Body</label>
                        <textarea name="service[cta_body]" class="form-control" rows="3"><?php echo htmlspecialchars($currentService['cta_body'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">CTA Button Text</label>
                        <input type="text" name="service[cta_button_text]" class="form-control" value="<?php echo htmlspecialchars($currentService['cta_button_text'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">CTA Button Link</label>
                        <input type="text" name="service[cta_button_link]" class="form-control" value="<?php echo htmlspecialchars($currentService['cta_button_link'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Contact Eyebrow</label>
                        <input type="text" name="service[contact_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['contact_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Support Title</label>
                        <input type="text" name="service[support_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['support_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Title</label>
                        <input type="text" name="service[contact_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['contact_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office Eyebrow</label>
                        <input type="text" name="service[office_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['office_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Support Body</label>
                        <textarea name="service[support_body]" class="form-control" rows="3"><?php echo htmlspecialchars($currentService['support_body'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Phone Label</label>
                        <input type="text" name="service[phone_label]" class="form-control" value="<?php echo htmlspecialchars($currentService['phone_label'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Email Label</label>
                        <input type="text" name="service[email_label]" class="form-control" value="<?php echo htmlspecialchars($currentService['email_label'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office Link 1 Title</label>
                        <input type="text" name="service[office_link_1_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['office_link_1_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office Link 1 Body</label>
                        <textarea name="service[office_link_1_body]" class="form-control" rows="2"><?php echo htmlspecialchars($currentService['office_link_1_body'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office Link 2 Title</label>
                        <input type="text" name="service[office_link_2_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['office_link_2_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office Link 2 Body</label>
                        <textarea name="service[office_link_2_body]" class="form-control" rows="2"><?php echo htmlspecialchars($currentService['office_link_2_body'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Related Services Eyebrow</label>
                        <input type="text" name="service[related_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($currentService['related_eyebrow'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Related Services Title</label>
                        <input type="text" name="service[related_title]" class="form-control" value="<?php echo htmlspecialchars($currentService['related_title'] ?? ''); ?>">
                    </div>

                    <div class="col-12"><hr class="my-1"><h6 class="mb-0">Images</h6></div>
                    <?php
                    $imageFields = [
                        'gallery_1' => 'Gallery Image 1',
                        'gallery_2' => 'Gallery Image 2',
                        'gallery_3' => 'Gallery Image 3',
                        'gallery_4' => 'Gallery Image 4',
                        'gallery_5' => 'Gallery Image 5',
                        'gallery_6' => 'Gallery Image 6',
                        'sustainable_image_1' => 'Sustainable Image 1',
                        'sustainable_image_2' => 'Sustainable Image 2',
                        'cta_image' => 'CTA Image',
                        'contact_image' => 'Contact Image',
                    ];
                    foreach ($imageFields as $field => $label):
                    ?>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?php echo htmlspecialchars($label); ?></label>
                            <div class="preview-box mb-2">
                                <?php if (!empty($currentService[$field])): ?>
                                    <img src="<?php echo htmlspecialchars(tpv_asset_url($currentService[$field])); ?>" alt="<?php echo htmlspecialchars($label); ?>">
                                <?php else: ?>
                                    <span class="text-muted small">No image selected</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="<?php echo htmlspecialchars($field); ?>_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-end gap-2">
                <a href="<?php echo htmlspecialchars('../services/' . $selectedSlug . '/'); ?>" class="btn btn-light border" target="_blank" rel="noopener">Preview</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Service Content
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/inc/admin_footer.php'; ?>
