<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Settings.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$settings = new Settings();

function serviceHandleAssetUpload($fileKey, $targetName, $subDirectory = 'services/steel-and-fabrication') {
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
        throw new Exception('Unable to save uploaded service asset.');
    }

    return $relativePath;
}

$serviceTitle = $settings->get('service_sf_page_title', 'Steel & Fabrication');
$serviceOverviewTitle = $settings->get('service_sf_overview_title', 'Steel & Fabrication');
$serviceOverviewBody = $settings->get('service_sf_overview_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.');
$serviceHighlight2Title = $settings->get('service_sf_highlight_2_title', 'Contractor Service');
$serviceHighlight2Body = $settings->get('service_sf_highlight_2_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.');
$serviceHighlight3Title = $settings->get('service_sf_highlight_3_title', 'Onsite Supervision');
$serviceHighlight3Body = $settings->get('service_sf_highlight_3_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.');
$serviceContentBody = $settings->get('service_sf_content_body', "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need t.variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going");
$serviceFeature1 = $settings->get('service_sf_feature_1', '100 Satisfaction Guarantee');
$serviceFeature2 = $settings->get('service_sf_feature_2', 'Export And Profession Enginers');
$serviceFeature3 = $settings->get('service_sf_feature_3', 'We Are Award Winning Company');
$serviceFeature4 = $settings->get('service_sf_feature_4', 'Full Satisfaction Guarantee');
$serviceFeature5 = $settings->get('service_sf_feature_5', 'Professional Qualified');
$serviceSustainableTitle = $settings->get('service_sf_sustainable_title', 'The future of sustainable building practices');
$serviceSustainableBody1 = $settings->get('service_sf_sustainable_body_1', "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage.");
$serviceSustainableBody2 = $settings->get('service_sf_sustainable_body_2', "of Lorem Ipsum, you need t.variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going");
$serviceProcessEyebrow = $settings->get('service_sf_process_eyebrow', 'Better process');
$serviceProcessTitle = $settings->get('service_sf_process_title', 'The process of working with us');
$serviceProcessBody = $settings->get('service_sf_process_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects. From initial design to final inspection, we work closely with our clients to understand their unique needs and vision.');
$serviceStep1Title = $settings->get('service_sf_step_1_title', 'Leave A Request');
$serviceStep1Body = $settings->get('service_sf_step_1_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.');
$serviceStep2Title = $settings->get('service_sf_step_2_title', 'Cost Calculation');
$serviceStep2Body = $settings->get('service_sf_step_2_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.');
$serviceStep3Title = $settings->get('service_sf_step_3_title', 'Signing Of A Contract');
$serviceStep3Body = $settings->get('service_sf_step_3_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.');
$serviceCtaTitle = $settings->get('service_sf_cta_title', "Let's bulid something great together!");
$serviceCtaBody = $settings->get('service_sf_cta_body', "Don't wait any longer to bring your construction dreams to life. Partner with TPV Construction and Services LTD and experience unparalleled service and quality.");
$serviceCtaButtonText = $settings->get('service_sf_cta_button_text', 'Get Free Quote');
$serviceCtaButtonLink = $settings->get('service_sf_cta_button_link', 'contact-us/');
$serviceSupportTitle = $settings->get('service_sf_support_title', 'You Still Have A Question');
$serviceSupportBody = $settings->get('service_sf_support_body', 'if you cannot find answer to your question our FAQ, you can alwas contact us. web will answer you shortly!');
$servicePhoneLabel = $settings->get('service_sf_phone_label', 'Call Support Center 24/7');
$serviceEmailLabel = $settings->get('service_sf_email_label', 'Write To Us');
$serviceContactEyebrow = $settings->get('service_sf_contact_eyebrow', 'Contact us');
$serviceContactTitle = $settings->get('service_sf_contact_title', 'Get in touch with us');

$serviceGallery1 = $settings->get('service_sf_gallery_1', 'wp-content/uploads/2024/06/service-img-1.jpg');
$serviceGallery2 = $settings->get('service_sf_gallery_2', 'wp-content/uploads/2024/06/service-img-2.jpg');
$serviceGallery3 = $settings->get('service_sf_gallery_3', 'wp-content/uploads/2024/06/service-img-3.png');
$serviceGallery4 = $settings->get('service_sf_gallery_4', 'wp-content/uploads/2024/06/service-img-4.png');
$serviceGallery5 = $settings->get('service_sf_gallery_5', 'wp-content/uploads/2024/06/service-img-5.jpg');
$serviceGallery6 = $settings->get('service_sf_gallery_6', 'wp-content/uploads/2024/06/service-img-6.jpg');
$serviceSustainableImage1 = $settings->get('service_sf_sustainable_image_1', 'wp-content/uploads/2024/06/company-history-img.jpg');
$serviceSustainableImage2 = $settings->get('service_sf_sustainable_image_2', 'wp-content/uploads/2024/06/service-suitabilities-img-2.jpg');
$serviceCtaImage = $settings->get('service_sf_cta_image', 'wp-content/uploads/2024/06/cta-box-img.png');
$serviceContactImage = $settings->get('service_sf_contact_image', 'wp-content/uploads/2024/06/contact-info-img.png');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            if (isset($_POST['settings']) && is_array($_POST['settings'])) {
                foreach ($_POST['settings'] as $key => $value) {
                    $group = $_POST['groups'][$key] ?? 'services';
                    $settings->set($key, $value, $group);
                }
            }

            $assetMap = [
                'service_sf_gallery_1_file' => ['target' => 'gallery-1', 'setting' => 'service_sf_gallery_1'],
                'service_sf_gallery_2_file' => ['target' => 'gallery-2', 'setting' => 'service_sf_gallery_2'],
                'service_sf_gallery_3_file' => ['target' => 'gallery-3', 'setting' => 'service_sf_gallery_3'],
                'service_sf_gallery_4_file' => ['target' => 'gallery-4', 'setting' => 'service_sf_gallery_4'],
                'service_sf_gallery_5_file' => ['target' => 'gallery-5', 'setting' => 'service_sf_gallery_5'],
                'service_sf_gallery_6_file' => ['target' => 'gallery-6', 'setting' => 'service_sf_gallery_6'],
                'service_sf_sustainable_image_1_file' => ['target' => 'sustainable-1', 'setting' => 'service_sf_sustainable_image_1'],
                'service_sf_sustainable_image_2_file' => ['target' => 'sustainable-2', 'setting' => 'service_sf_sustainable_image_2'],
                'service_sf_cta_image_file' => ['target' => 'cta-image', 'setting' => 'service_sf_cta_image'],
                'service_sf_contact_image_file' => ['target' => 'contact-image', 'setting' => 'service_sf_contact_image'],
            ];

            foreach ($assetMap as $fileKey => $assetConfig) {
                $uploaded = serviceHandleAssetUpload($fileKey, $assetConfig['target']);
                if ($uploaded) {
                    $settings->set($assetConfig['setting'], $uploaded, 'services');
                }
            }

            $_SESSION['toast_success'] = 'Steel & Fabrication page updated successfully.';
        } catch (Throwable $e) {
            $_SESSION['toast_error'] = $e->getMessage();
        }
    }

    header('Location: service_steel_and_fabrication_settings.php');
    exit;
}

$pageActive = 'service_steel_and_fabrication_settings';
$pageTitle = 'TPV Construction and Services LTD Â· Steel & Fabrication Settings';
require 'inc/admin_header.php';
?>

<div data-pages="parallax">
    <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Steel & Fabrication Settings</li>
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
                    <i class="fas fa-hard-hat me-2"></i>
                    Hero & Overview
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Service Page Title</label>
                        <input type="hidden" name="groups[service_sf_page_title]" value="services">
                        <input type="text" name="settings[service_sf_page_title]" class="form-control" value="<?php echo htmlspecialchars($serviceTitle); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Overview Card Title</label>
                        <input type="hidden" name="groups[service_sf_overview_title]" value="services">
                        <input type="text" name="settings[service_sf_overview_title]" class="form-control" value="<?php echo htmlspecialchars($serviceOverviewTitle); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Overview Card Body</label>
                        <input type="hidden" name="groups[service_sf_overview_body]" value="services">
                        <textarea name="settings[service_sf_overview_body]" class="form-control" rows="3"><?php echo htmlspecialchars($serviceOverviewBody); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Highlight Card 2 Title</label>
                        <input type="hidden" name="groups[service_sf_highlight_2_title]" value="services">
                        <input type="text" name="settings[service_sf_highlight_2_title]" class="form-control" value="<?php echo htmlspecialchars($serviceHighlight2Title); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Highlight Card 3 Title</label>
                        <input type="hidden" name="groups[service_sf_highlight_3_title]" value="services">
                        <input type="text" name="settings[service_sf_highlight_3_title]" class="form-control" value="<?php echo htmlspecialchars($serviceHighlight3Title); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Highlight Card 2 Body</label>
                        <input type="hidden" name="groups[service_sf_highlight_2_body]" value="services">
                        <textarea name="settings[service_sf_highlight_2_body]" class="form-control" rows="3"><?php echo htmlspecialchars($serviceHighlight2Body); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Highlight Card 3 Body</label>
                        <input type="hidden" name="groups[service_sf_highlight_3_body]" value="services">
                        <textarea name="settings[service_sf_highlight_3_body]" class="form-control" rows="3"><?php echo htmlspecialchars($serviceHighlight3Body); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Main Content Paragraph</label>
                        <input type="hidden" name="groups[service_sf_content_body]" value="services">
                        <textarea name="settings[service_sf_content_body]" class="form-control" rows="4"><?php echo htmlspecialchars($serviceContentBody); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-images me-2"></i>
                    Service Images
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <?php
                    $galleryImages = [
                        1 => $serviceGallery1,
                        2 => $serviceGallery2,
                        3 => $serviceGallery3,
                        4 => $serviceGallery4,
                        5 => $serviceGallery5,
                        6 => $serviceGallery6,
                    ];
                    foreach ($galleryImages as $index => $imagePath):
                    ?>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gallery Image <?php echo $index; ?></label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($imagePath)); ?>" alt="Gallery <?php echo $index; ?>" style="max-width: 180px; max-height: 120px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="service_sf_gallery_<?php echo $index; ?>_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <?php endforeach; ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sustainable Image 1</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($serviceSustainableImage1)); ?>" alt="Sustainable image 1" style="max-width: 180px; max-height: 120px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="service_sf_sustainable_image_1_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sustainable Image 2</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($serviceSustainableImage2)); ?>" alt="Sustainable image 2" style="max-width: 180px; max-height: 120px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="service_sf_sustainable_image_2_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-list-check me-2"></i>
                    Features & Sustainable Section
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Feature 1</label>
                        <input type="hidden" name="groups[service_sf_feature_1]" value="services">
                        <input type="text" name="settings[service_sf_feature_1]" class="form-control" value="<?php echo htmlspecialchars($serviceFeature1); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Feature 2</label>
                        <input type="hidden" name="groups[service_sf_feature_2]" value="services">
                        <input type="text" name="settings[service_sf_feature_2]" class="form-control" value="<?php echo htmlspecialchars($serviceFeature2); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Feature 3</label>
                        <input type="hidden" name="groups[service_sf_feature_3]" value="services">
                        <input type="text" name="settings[service_sf_feature_3]" class="form-control" value="<?php echo htmlspecialchars($serviceFeature3); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Feature 4</label>
                        <input type="hidden" name="groups[service_sf_feature_4]" value="services">
                        <input type="text" name="settings[service_sf_feature_4]" class="form-control" value="<?php echo htmlspecialchars($serviceFeature4); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Feature 5</label>
                        <input type="hidden" name="groups[service_sf_feature_5]" value="services">
                        <input type="text" name="settings[service_sf_feature_5]" class="form-control" value="<?php echo htmlspecialchars($serviceFeature5); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Sustainable Section Title</label>
                        <input type="hidden" name="groups[service_sf_sustainable_title]" value="services">
                        <input type="text" name="settings[service_sf_sustainable_title]" class="form-control" value="<?php echo htmlspecialchars($serviceSustainableTitle); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sustainable Paragraph 1</label>
                        <input type="hidden" name="groups[service_sf_sustainable_body_1]" value="services">
                        <textarea name="settings[service_sf_sustainable_body_1]" class="form-control" rows="4"><?php echo htmlspecialchars($serviceSustainableBody1); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sustainable Paragraph 2</label>
                        <input type="hidden" name="groups[service_sf_sustainable_body_2]" value="services">
                        <textarea name="settings[service_sf_sustainable_body_2]" class="form-control" rows="4"><?php echo htmlspecialchars($serviceSustainableBody2); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-diagram-project me-2"></i>
                    Working Process
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Eyebrow</label>
                        <input type="hidden" name="groups[service_sf_process_eyebrow]" value="services">
                        <input type="text" name="settings[service_sf_process_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($serviceProcessEyebrow); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Section Title</label>
                        <input type="hidden" name="groups[service_sf_process_title]" value="services">
                        <input type="text" name="settings[service_sf_process_title]" class="form-control" value="<?php echo htmlspecialchars($serviceProcessTitle); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Section Body</label>
                        <input type="hidden" name="groups[service_sf_process_body]" value="services">
                        <textarea name="settings[service_sf_process_body]" class="form-control" rows="4"><?php echo htmlspecialchars($serviceProcessBody); ?></textarea>
                    </div>
                    <?php for ($step = 1; $step <= 3; $step++): ?>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Step <?php echo $step; ?> Title</label>
                        <input type="hidden" name="groups[service_sf_step_<?php echo $step; ?>_title]" value="services">
                        <input type="text" name="settings[service_sf_step_<?php echo $step; ?>_title]" class="form-control" value="<?php echo htmlspecialchars(${'serviceStep' . $step . 'Title'}); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Step <?php echo $step; ?> Body</label>
                        <input type="hidden" name="groups[service_sf_step_<?php echo $step; ?>_body]" value="services">
                        <textarea name="settings[service_sf_step_<?php echo $step; ?>_body]" class="form-control" rows="3"><?php echo htmlspecialchars(${'serviceStep' . $step . 'Body'}); ?></textarea>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-bullhorn me-2"></i>
                    CTA & Contact Sidebar
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Title</label>
                        <input type="hidden" name="groups[service_sf_cta_title]" value="services">
                        <textarea name="settings[service_sf_cta_title]" class="form-control" rows="2"><?php echo htmlspecialchars($serviceCtaTitle); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($serviceCtaImage)); ?>" alt="CTA image" style="max-width: 180px; max-height: 120px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="service_sf_cta_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">CTA Body</label>
                        <input type="hidden" name="groups[service_sf_cta_body]" value="services">
                        <textarea name="settings[service_sf_cta_body]" class="form-control" rows="3"><?php echo htmlspecialchars($serviceCtaBody); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Button Text</label>
                        <input type="hidden" name="groups[service_sf_cta_button_text]" value="services">
                        <input type="text" name="settings[service_sf_cta_button_text]" class="form-control" value="<?php echo htmlspecialchars($serviceCtaButtonText); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Button Link</label>
                        <input type="hidden" name="groups[service_sf_cta_button_link]" value="services">
                        <input type="text" name="settings[service_sf_cta_button_link]" class="form-control" value="<?php echo htmlspecialchars($serviceCtaButtonLink); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Support Title</label>
                        <input type="hidden" name="groups[service_sf_support_title]" value="services">
                        <input type="text" name="settings[service_sf_support_title]" class="form-control" value="<?php echo htmlspecialchars($serviceSupportTitle); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Support Body</label>
                        <input type="hidden" name="groups[service_sf_support_body]" value="services">
                        <input type="text" name="settings[service_sf_support_body]" class="form-control" value="<?php echo htmlspecialchars($serviceSupportBody); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Phone Label</label>
                        <input type="hidden" name="groups[service_sf_phone_label]" value="services">
                        <input type="text" name="settings[service_sf_phone_label]" class="form-control" value="<?php echo htmlspecialchars($servicePhoneLabel); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Email Label</label>
                        <input type="hidden" name="groups[service_sf_email_label]" value="services">
                        <input type="text" name="settings[service_sf_email_label]" class="form-control" value="<?php echo htmlspecialchars($serviceEmailLabel); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Contact Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($serviceContactImage)); ?>" alt="Contact image" style="max-width: 120px; max-height: 120px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="service_sf_contact_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Form Eyebrow</label>
                        <input type="hidden" name="groups[service_sf_contact_eyebrow]" value="services">
                        <input type="text" name="settings[service_sf_contact_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($serviceContactEyebrow); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Form Title</label>
                        <input type="hidden" name="groups[service_sf_contact_title]" value="services">
                        <input type="text" name="settings[service_sf_contact_title]" class="form-control" value="<?php echo htmlspecialchars($serviceContactTitle); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4 py-2">
                <i class="fas fa-save me-2"></i>
                Save Steel & Fabrication Page
            </button>
        </div>
    </form>
</div>

<?php require 'inc/admin_footer.php'; ?>

