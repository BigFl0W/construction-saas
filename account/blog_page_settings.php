<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Settings.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$settings = new Settings();

$fields = [
    'blog_page_meta_description' => 'Read practical construction insights, project stories, design ideas, and company updates from TPV Construction and Services LTD.',
    'blog_page_badge' => 'Insights & Project Stories',
    'blog_page_title' => 'The TPV Journal',
    'blog_page_description' => 'Explore practical construction guidance, project milestones, engineering perspectives, and company updates from our team.',
    'blog_page_featured_label' => 'Featured Story',
    'blog_page_latest_heading' => 'Latest Articles',
    'blog_page_latest_description' => 'Fresh updates, practical advice, and behind-the-scenes highlights from our construction work.',
    'blog_page_empty_title' => 'Fresh stories are on the way.',
    'blog_page_empty_body' => 'We are preparing new project updates and practical construction articles. Please check back soon.',
    'blog_page_cta_title' => 'Need a partner for your next build?',
    'blog_page_cta_body' => 'From design to execution, our team can help you plan, deliver, and maintain exceptional projects.',
    'blog_page_cta_button_text' => 'Request a Quote',
    'blog_page_cta_button_link' => '../quote/',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        foreach ($fields as $key => $default) {
            $value = isset($_POST['settings'][$key]) ? trim((string) $_POST['settings'][$key]) : $default;
            $settings->set($key, $value, 'blog_page');
        }
        $_SESSION['toast_success'] = 'Blog page settings updated successfully.';
    }

    header('Location: blog_page_settings.php');
    exit;
}

$values = [];
foreach ($fields as $key => $default) {
    $values[$key] = (string) $settings->get($key, $default);
}

$pageActive = 'blog_page_settings';
$pageTitle = 'TPV Construction and Services LTD · Blog Page Settings';
require 'inc/admin_header.php';
?>

<div class="container-fluid p-l-25 p-r-25 p-t-15 p-b-25">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 m-b-20">
        <div>
            <ol class="breadcrumb m-b-10">
                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                <li class="breadcrumb-item active">Blog Page Settings</li>
            </ol>
            <h1 class="m-b-5" style="font-size:1.65rem;">Blog Page Settings</h1>
            <p class="text-muted m-0">Manage the public-facing blog page separately from the post editor.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="blog_manager.php" class="btn btn-outline-secondary">
                <i class="fas fa-pen-nib me-2"></i>Open Blog Studio
            </a>
            <a href="../blog/" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                <i class="fas fa-arrow-up-right-from-square me-2"></i>Preview Website Blog
            </a>
        </div>
    </div>

    <form method="post" class="card border-0 shadow-sm" style="border-radius:24px; overflow:hidden;">
        <div class="card-body p-4 p-lg-5">
            <?php echo $auth->csrfField(); ?>
            <div class="row g-4">
                <div class="col-12">
                    <h5 class="mb-0">Hero Section</h5>
                    <hr class="mt-2">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Meta Description</label>
                    <textarea name="settings[blog_page_meta_description]" class="form-control" rows="2"><?php echo htmlspecialchars($values['blog_page_meta_description']); ?></textarea>
                    <div class="form-text">SEO only. This updates the browser/page metadata, not the visible hero text on the blog page.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hero Badge</label>
                    <input type="text" name="settings[blog_page_badge]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_badge']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hero Title</label>
                    <input type="text" name="settings[blog_page_title]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_title']); ?>">
                    <div class="form-text">Visible on the frontend.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Featured Label</label>
                    <input type="text" name="settings[blog_page_featured_label]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_featured_label']); ?>">
                    <div class="form-text">Visible on the featured story card.</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Hero Description</label>
                    <textarea name="settings[blog_page_description]" class="form-control" rows="3"><?php echo htmlspecialchars($values['blog_page_description']); ?></textarea>
                    <div class="form-text">Visible on the frontend.</div>
                </div>

                <div class="col-12">
                    <h5 class="mb-0 mt-2">Article Grid</h5>
                    <hr class="mt-2">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Latest Heading</label>
                    <input type="text" name="settings[blog_page_latest_heading]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_latest_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Latest Description</label>
                    <input type="text" name="settings[blog_page_latest_description]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_latest_description']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Empty State Title</label>
                    <input type="text" name="settings[blog_page_empty_title]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_empty_title']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Empty State Body</label>
                    <input type="text" name="settings[blog_page_empty_body]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_empty_body']); ?>">
                </div>

                <div class="col-12">
                    <h5 class="mb-0 mt-2">Call To Action</h5>
                    <hr class="mt-2">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CTA Title</label>
                    <input type="text" name="settings[blog_page_cta_title]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_cta_title']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CTA Button Text</label>
                    <input type="text" name="settings[blog_page_cta_button_text]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_cta_button_text']); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">CTA Body</label>
                    <textarea name="settings[blog_page_cta_body]" class="form-control" rows="2"><?php echo htmlspecialchars($values['blog_page_cta_body']); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">CTA Button Link</label>
                    <input type="text" name="settings[blog_page_cta_button_link]" class="form-control" value="<?php echo htmlspecialchars($values['blog_page_cta_button_link']); ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Blog Page Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require 'inc/admin_footer.php'; ?>
