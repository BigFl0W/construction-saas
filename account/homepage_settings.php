<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Settings.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$settings = new Settings();

function homepageHandleAssetUpload($fileKey, $targetName, $subDirectory = 'homepage') {
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

    $relativePath = 'uploads/' . trim($subDirectory, '/\\') . '/' . $targetName . '.' . $extension;
    $absolutePath = dirname(__DIR__) . '/' . $relativePath;

    foreach (glob($assetDir . $targetName . '.*') ?: [] as $existingFile) {
        if (is_file($existingFile)) {
            @unlink($existingFile);
        }
    }

    if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
        throw new Exception('Unable to save uploaded homepage asset.');
    }

    return $relativePath;
}

$homeHeroEyebrow = $settings->get('home_hero_eyebrow', 'Welcome to TPV Construction and Services LTD');
$homeHeroTitle = $settings->get('home_hero_title', 'Building dreams with precision and excellence');
$homeHeroBody = $settings->get('home_hero_body', 'we specialize in turning visions into reality with exceptional craftsmanship and meticulous attention to detail. With years of experience and a commitment to quality.');
$homeHeroPrimaryText = $settings->get('home_hero_primary_text', 'get started');
$homeHeroPrimaryLink = $settings->get('home_hero_primary_link', 'contact-us/');
$homeHeroSecondaryText = $settings->get('home_hero_secondary_text', 'view Projects');
$homeHeroSecondaryLink = $settings->get('home_hero_secondary_link', 'projects/');
$homeHeroBgImage = $settings->get('home_hero_bg_image', 'wp-content/uploads/2024/06/hero-bg.jpg');

$homeIntroImage = $settings->get('home_intro_image', 'wp-content/uploads/2024/06/about-us-img.png');
$homeIntroEyebrow = $settings->get('home_intro_eyebrow', 'Welcome to TPV');
$homeIntroTitle = $settings->get('home_intro_title', 'TPV Construction and Services LTD');
$homeIntroBody = $settings->get('home_intro_body', "Serving Nigeria's construction needs since 2008 with excellence and innovation. TPV Construction and Services LTD has established itself as a trusted name in Nigeria's construction industry, delivering exceptional projects across residential, commercial, and industrial sectors. Our commitment to quality, safety, and sustainable building practices has made us the preferred choice for discerning clients throughout Nigeria.");
$homeIntroFeature1 = $settings->get('home_intro_feature_1', 'Comprehensive Services');
$homeIntroFeature2 = $settings->get('home_intro_feature_2', 'Advanced Technology');
$homeIntroFeature3 = $settings->get('home_intro_feature_3', 'Transparent Communication');
$homeIntroButtonText = $settings->get('home_intro_button_text', 'Get Free Quote');
$homeIntroButtonLink = $settings->get('home_intro_button_link', 'quote/');

$homeWhy1Title = $settings->get('home_why_1_title', 'Innovative Solutions');
$homeWhy1Body = $settings->get('home_why_1_body', 'We combine modern construction technology with innovative approaches to deliver projects that exceed expectations while optimizing costs and timelines.');
$homeWhy1Image = $settings->get('home_why_1_image', 'wp-content/uploads/2024/06/why-choose-img-1.jpg');
$homeWhy1CounterTitle = $settings->get('home_why_1_counter_title', 'Projects Completed');
$homeWhy1CounterValue = $settings->get('home_why_1_counter_value', '450');
$homeWhy1CounterSuffix = $settings->get('home_why_1_counter_suffix', '+');

$homeWhy2Title = $settings->get('home_why_2_title', 'Quality Craftsmanship');
$homeWhy2Body = $settings->get('home_why_2_body', 'Our skilled craftsmen take pride in their work, ensuring every detail meets the highest standards of Nigerian and international construction quality.');
$homeWhy2Image = $settings->get('home_why_2_image', 'wp-content/uploads/2024/06/why-choose-img-2.jpg');
$homeWhy2CounterTitle = $settings->get('home_why_2_counter_title', 'Projects Completed');
$homeWhy2CounterValue = $settings->get('home_why_2_counter_value', '450');
$homeWhy2CounterSuffix = $settings->get('home_why_2_counter_suffix', '+');

$homeWhy3Title = $settings->get('home_why_3_title', 'Expertise And Experience');
$homeWhy3Body = $settings->get('home_why_3_body', 'With over a decade of experience in the Nigerian construction industry, our team brings deep local knowledge and proven expertise to every project.');
$homeWhy3Image = $settings->get('home_why_3_image', 'wp-content/uploads/2024/06/why-choose-img-3.jpg');
$homeWhy3CounterTitle = $settings->get('home_why_3_counter_title', 'Projects Completed');
$homeWhy3CounterValue = $settings->get('home_why_3_counter_value', '450');
$homeWhy3CounterSuffix = $settings->get('home_why_3_counter_suffix', '+');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            if (isset($_POST['settings']) && is_array($_POST['settings'])) {
                foreach ($_POST['settings'] as $key => $value) {
                    $group = $_POST['groups'][$key] ?? 'homepage';
                    $settings->set($key, $value, $group);
                }
            }

            $assetMap = [
                'home_hero_bg_image_file' => ['target' => 'home-hero-bg', 'setting' => 'home_hero_bg_image'],
                'home_intro_image_file' => ['target' => 'home-intro-image', 'setting' => 'home_intro_image'],
                'home_why_1_image_file' => ['target' => 'home-why-1', 'setting' => 'home_why_1_image'],
                'home_why_2_image_file' => ['target' => 'home-why-2', 'setting' => 'home_why_2_image'],
                'home_why_3_image_file' => ['target' => 'home-why-3', 'setting' => 'home_why_3_image'],
            ];

            foreach ($assetMap as $fileKey => $assetConfig) {
                $uploaded = homepageHandleAssetUpload($fileKey, $assetConfig['target'], 'homepage');
                if ($uploaded) {
                    $settings->set($assetConfig['setting'], $uploaded, 'homepage');
                }
            }

            $_SESSION['toast_success'] = 'Homepage settings updated successfully.';
        } catch (Throwable $e) {
            $_SESSION['toast_error'] = $e->getMessage();
        }
    }

    header('Location: homepage_settings.php');
    exit;
}

$pageActive = 'homepage_settings';
$pageTitle = 'TPV Construction and Services LTD · Homepage Settings';
require 'inc/admin_header.php';
?>

<div data-pages="parallax">
    <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Homepage Settings</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
    <form method="POST" enctype="multipart/form-data">
        <?php echo $auth->csrfField(); ?>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-house me-2"></i>
                    Homepage Hero
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Eyebrow Text</label>
                        <input type="hidden" name="groups[home_hero_eyebrow]" value="homepage">
                        <input type="text" name="settings[home_hero_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeHeroEyebrow); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Background Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($homeHeroBgImage)); ?>" alt="Homepage hero preview" style="max-width: 240px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="home_hero_bg_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Hero Title</label>
                        <input type="hidden" name="groups[home_hero_title]" value="homepage">
                        <textarea name="settings[home_hero_title]" class="form-control" rows="2"><?php echo htmlspecialchars($homeHeroTitle); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Hero Body</label>
                        <input type="hidden" name="groups[home_hero_body]" value="homepage">
                        <textarea name="settings[home_hero_body]" class="form-control" rows="4"><?php echo htmlspecialchars($homeHeroBody); ?></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Primary Button Text</label>
                        <input type="hidden" name="groups[home_hero_primary_text]" value="homepage">
                        <input type="text" name="settings[home_hero_primary_text]" class="form-control" value="<?php echo htmlspecialchars($homeHeroPrimaryText); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Primary Button Link</label>
                        <input type="hidden" name="groups[home_hero_primary_link]" value="homepage">
                        <input type="text" name="settings[home_hero_primary_link]" class="form-control" value="<?php echo htmlspecialchars($homeHeroPrimaryLink); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Secondary Button Text</label>
                        <input type="hidden" name="groups[home_hero_secondary_text]" value="homepage">
                        <input type="text" name="settings[home_hero_secondary_text]" class="form-control" value="<?php echo htmlspecialchars($homeHeroSecondaryText); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Secondary Button Link</label>
                        <input type="hidden" name="groups[home_hero_secondary_link]" value="homepage">
                        <input type="text" name="settings[home_hero_secondary_link]" class="form-control" value="<?php echo htmlspecialchars($homeHeroSecondaryLink); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-circle-info me-2"></i>
                    Homepage Intro
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Intro Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($homeIntroImage)); ?>" alt="Homepage intro preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="home_intro_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Intro Eyebrow</label>
                        <input type="hidden" name="groups[home_intro_eyebrow]" value="homepage">
                        <input type="text" name="settings[home_intro_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeIntroEyebrow); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Intro Title</label>
                        <input type="hidden" name="groups[home_intro_title]" value="homepage">
                        <input type="text" name="settings[home_intro_title]" class="form-control" value="<?php echo htmlspecialchars($homeIntroTitle); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Intro Body</label>
                        <input type="hidden" name="groups[home_intro_body]" value="homepage">
                        <textarea name="settings[home_intro_body]" class="form-control" rows="5"><?php echo htmlspecialchars($homeIntroBody); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Feature 1</label>
                        <input type="hidden" name="groups[home_intro_feature_1]" value="homepage">
                        <input type="text" name="settings[home_intro_feature_1]" class="form-control" value="<?php echo htmlspecialchars($homeIntroFeature1); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Feature 2</label>
                        <input type="hidden" name="groups[home_intro_feature_2]" value="homepage">
                        <input type="text" name="settings[home_intro_feature_2]" class="form-control" value="<?php echo htmlspecialchars($homeIntroFeature2); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Feature 3</label>
                        <input type="hidden" name="groups[home_intro_feature_3]" value="homepage">
                        <input type="text" name="settings[home_intro_feature_3]" class="form-control" value="<?php echo htmlspecialchars($homeIntroFeature3); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Intro Button Text</label>
                        <input type="hidden" name="groups[home_intro_button_text]" value="homepage">
                        <input type="text" name="settings[home_intro_button_text]" class="form-control" value="<?php echo htmlspecialchars($homeIntroButtonText); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Intro Button Link</label>
                        <input type="hidden" name="groups[home_intro_button_link]" value="homepage">
                        <input type="text" name="settings[home_intro_button_link]" class="form-control" value="<?php echo htmlspecialchars($homeIntroButtonLink); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-star me-2"></i>
                    Why Choose Us Cards
                </div>
            </div>
            <div class="card-body">
                <?php
                $whyCards = [
                    1 => [$homeWhy1Title, $homeWhy1Body, $homeWhy1Image, $homeWhy1CounterTitle, $homeWhy1CounterValue, $homeWhy1CounterSuffix],
                    2 => [$homeWhy2Title, $homeWhy2Body, $homeWhy2Image, $homeWhy2CounterTitle, $homeWhy2CounterValue, $homeWhy2CounterSuffix],
                    3 => [$homeWhy3Title, $homeWhy3Body, $homeWhy3Image, $homeWhy3CounterTitle, $homeWhy3CounterValue, $homeWhy3CounterSuffix],
                ];
                foreach ($whyCards as $index => [$title, $body, $image, $counterTitle, $counterValue, $counterSuffix]):
                ?>
                    <div class="border rounded-4 p-3 p-md-4 mb-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Card <?php echo $index; ?> Image</label>
                                <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                                    <img src="<?php echo htmlspecialchars(tpv_asset_url($image)); ?>" alt="Why choose image <?php echo $index; ?>" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                                </div>
                                <input type="file" name="home_why_<?php echo $index; ?>_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Card <?php echo $index; ?> Title</label>
                                        <input type="hidden" name="groups[home_why_<?php echo $index; ?>_title]" value="homepage">
                                        <input type="text" name="settings[home_why_<?php echo $index; ?>_title]" class="form-control" value="<?php echo htmlspecialchars($title); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Card <?php echo $index; ?> Body</label>
                                        <textarea name="settings[home_why_<?php echo $index; ?>_body]" class="form-control" rows="4"><?php echo htmlspecialchars($body); ?></textarea>
                                        <input type="hidden" name="groups[home_why_<?php echo $index; ?>_body]" value="homepage">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">Counter Title</label>
                                        <input type="hidden" name="groups[home_why_<?php echo $index; ?>_counter_title]" value="homepage">
                                        <input type="text" name="settings[home_why_<?php echo $index; ?>_counter_title]" class="form-control" value="<?php echo htmlspecialchars($counterTitle); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Counter Value</label>
                                        <input type="hidden" name="groups[home_why_<?php echo $index; ?>_counter_value]" value="homepage">
                                        <input type="text" name="settings[home_why_<?php echo $index; ?>_counter_value]" class="form-control" value="<?php echo htmlspecialchars($counterValue); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Suffix</label>
                                        <input type="hidden" name="groups[home_why_<?php echo $index; ?>_counter_suffix]" value="homepage">
                                        <input type="text" name="settings[home_why_<?php echo $index; ?>_counter_suffix]" class="form-control" value="<?php echo htmlspecialchars($counterSuffix); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Homepage Settings
            </button>
        </div>
    </form>
</div>
