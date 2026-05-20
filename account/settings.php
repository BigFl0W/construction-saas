<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Settings.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$settings = new Settings();
$siteLogo = $settings->get('site_logo', 'wp-content/uploads/2024/06/logo.png');
$footerLogo = $settings->get('footer_logo', 'wp-content/uploads/2024/06/footer-logo.png');
$footerDescription = $settings->get('footer_description', "Building Nigeria's future with excellence, integrity, and innovation. Your trusted partner for quality construction across the nation.");
$footerServicesHeading = $settings->get('footer_services_heading', 'Our Services');
$footerCompanyHeading = $settings->get('footer_company_heading', 'Company');
$footerContactHeading = $settings->get('footer_contact_heading', 'Contact Us');
$footerPhone = $settings->get('footer_phone', '+234 701 234 5678');
$footerEmail = $settings->get('footer_email', 'info@tpvconstruction.com.ng');
$footerLocations = $settings->get('footer_locations', "Abuja Office: 2nd Floor, Right Wing, APDC Building, Area 11, Abuja\nOgun Office: Beside Aladey Hotel, Along Federal Poly Express Road, Ilaro\nNasarawa Office: By New York Park and Gardens, Keffi, Nasarawa\nLagos Office: 10A, Onipinla Lane, Harmony Enclave, Ikeja");
$footerCopyright = $settings->get('footer_copyright', 'Copyright © 2026 TPV Construction and Services LTD. All Rights Reserved.');
$footerInstagram = $settings->get('footer_instagram_url', '#');
$footerFacebook = $settings->get('footer_facebook_url', '#');
$footerTwitter = $settings->get('footer_twitter_url', '#');
$footerLinkedin = $settings->get('footer_linkedin_url', '#');
$companyName = $settings->get('company_name', 'TPV Construction and Services LTD');
$companyEmail = $settings->get('company_email', 'info@tpvconstruction.com.ng');
$companyPhone = $settings->get('company_phone', '+234 701 234 5678');
$companyAddress = $settings->get('company_address', '2nd Floor, Right Wing, APDC Building, Area 11, Abuja, Nigeria');
$timezoneSetting = $settings->get('timezone', 'Africa/Lagos');
$contactHeroTitle = $settings->get('contact_hero_title', 'Contact Us');
$contactFormEyebrow = $settings->get('contact_form_eyebrow', 'Contact us');
$contactFormTitle = $settings->get('contact_form_title', "Get in touch with us");
$contactFormBody = $settings->get('contact_form_body', 'Please fill out the form below, and a member of our team will get back to you as soon as possible.');
$contactPhoneTitle = $settings->get('contact_phone_title', 'Call Our Head Office');
$contactPhoneNote = $settings->get('contact_phone_note', 'Available Mon-Fri, 8am-6pm');
$contactEmailTitle = $settings->get('contact_email_title', 'Write To Us');
$contactEmailNote = $settings->get('contact_email_note', 'We reply within 24 hours');
$contactSidebarHeading = $settings->get('contact_sidebar_heading', 'follow us');
$contactSidebarImage = $settings->get('contact_sidebar_image', 'wp-content/uploads/2024/06/contact-info-img.png');
$contactMapApiKey = $settings->get('contact_map_api_key', 'AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8');
$contactMapDefaultQuery = $settings->get('contact_map_default_query', 'Port+Harcourt+Rivers+Nigeria');
$contactSuccessMessage = $settings->get('contact_success_message', 'Your message has been sent successfully. We will get back to you within 24 hours.');
$contactLocationsData = $settings->get('contact_locations_data', "Abuja Office|Area 11, Abuja|2nd Floor, Right Wing, APDC Building, Area 11, Abuja|09097128241|abuja@tpvconstruction.com.ng|APDC+Building+Area+11+Abuja+Nigeria\nOgun Office|Ilaro, Ogun State|Beside Aladey Hotel, Along Federal Poly Express Road, Ilaro, Ogun State|09097128241|ogun@tpvconstruction.com.ng|Federal+Poly+Express+Road+Ilaro+Ogun+State+Nigeria\nNasarawa Office|Keffi, Nasarawa|By New York Park and Gardens, Keffi, Nasarawa|08069418816|nasarawa@tpvconstruction.com.ng|Keffi+Nasarawa+Nigeria\nLagos Office|Ikeja, Lagos|10A, Onipinla Lane, Harmony Enclave, Off Adeniyi Jones Avenue, Ikeja, Lagos|08104830712|lagos@tpvconstruction.com.ng|Harmony+Enclave+Ikeja+Lagos+Nigeria");
$mailDriver = $settings->get('mail_driver', 'phpmailer');
$smtpHost = $settings->get('smtp_host', '');
$smtpPort = $settings->get('smtp_port', '587');
$smtpUsername = $settings->get('smtp_username', '');
$smtpPassword = $settings->get('smtp_password', '');
$smtpEncryption = $settings->get('smtp_encryption', 'tls');
$smtpFromEmail = $settings->get('smtp_from_email', '');
$smtpFromName = $settings->get('smtp_from_name', $companyName);

$normalizationMap = [
    'company_name' => ['current' => $companyName, 'replacement' => 'TPV Construction and Services LTD'],
    'company_email' => ['current' => $companyEmail, 'replacement' => 'info@tpvconstruction.com.ng'],
    'company_phone' => ['current' => $companyPhone, 'replacement' => '+234 701 234 5678'],
    'company_address' => ['current' => $companyAddress, 'replacement' => '2nd Floor, Right Wing, APDC Building, Area 11, Abuja, Nigeria'],
    'timezone' => ['current' => $timezoneSetting, 'replacement' => 'Africa/Lagos'],
    'contact_email' => ['current' => $settings->get('contact_email', 'info@tpvconstruction.com.ng'), 'replacement' => 'info@tpvconstruction.com.ng'],
    'site_name' => ['current' => $settings->get('site_name', 'TPV Construction and Services LTD'), 'replacement' => 'TPV Construction and Services LTD'],
    'site_tagline' => ['current' => $settings->get('site_tagline', 'Building Excellence'), 'replacement' => 'Building Excellence']
];

foreach ($normalizationMap as $key => $item) {
    $currentValue = trim((string) $item['current']);
    $replacement = $item['replacement'];
    $shouldNormalize = $currentValue === '';

    if ($key === 'company_name' && stripos($currentValue, 'ironbridge') !== false) {
        $shouldNormalize = true;
    }
    if ($key === 'company_email' && (stripos($currentValue, 'ironbridge') !== false || !filter_var($currentValue, FILTER_VALIDATE_EMAIL))) {
        $shouldNormalize = true;
    }
    if ($key === 'company_phone' && stripos($currentValue, '555') !== false) {
        $shouldNormalize = true;
    }
    if ($key === 'company_address' && stripos($currentValue, 'builder st') !== false) {
        $shouldNormalize = true;
    }
    if ($key === 'timezone' && $currentValue === 'America/New_York') {
        $shouldNormalize = true;
    }
    if ($key === 'contact_email' && stripos($currentValue, '@tpvconstruction.com.ng') === false) {
        $shouldNormalize = true;
    }

    if ($shouldNormalize) {
        $settings->set($key, $replacement, 'general');
    }
}

$companyName = $settings->get('company_name', 'TPV Construction and Services LTD');
$companyEmail = $settings->get('company_email', 'info@tpvconstruction.com.ng');
$companyPhone = $settings->get('company_phone', '+234 701 234 5678');
$companyAddress = $settings->get('company_address', '2nd Floor, Right Wing, APDC Building, Area 11, Abuja, Nigeria');
$timezoneSetting = $settings->get('timezone', 'Africa/Lagos');

function handleAssetUpload($fileKey, $targetName, $subDirectory = 'branding') {
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
        throw new Exception('Unable to save uploaded logo file.');
    }

    return $relativePath;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        if ($_POST['action'] === 'update' && isset($_POST['settings']) && is_array($_POST['settings'])) {
            foreach ($_POST['settings'] as $key => $value) {
                $group = $_POST['groups'][$key] ?? 'general';
                $settings->set($key, $value, $group);
            }
            $uploadedSiteLogo = handleAssetUpload('site_logo_file', 'site-logo');
            if ($uploadedSiteLogo) {
                $settings->set('site_logo', $uploadedSiteLogo, 'branding');
            }

            $uploadedFooterLogo = handleAssetUpload('footer_logo_file', 'footer-logo');
            if ($uploadedFooterLogo) {
                $settings->set('footer_logo', $uploadedFooterLogo, 'branding');
            }

            $uploadedContactSidebarImage = handleAssetUpload('contact_sidebar_image_file', 'contact-sidebar-image', 'contact');
            if ($uploadedContactSidebarImage) {
                $settings->set('contact_sidebar_image', $uploadedContactSidebarImage, 'content');
            }
            $_SESSION['toast_success'] = 'Settings updated successfully.';
        } elseif ($_POST['action'] === 'add') {
            $key = trim($_POST['setting_key']);
            $val = trim($_POST['setting_value']);
            $group = trim($_POST['setting_group']);
            if ($key && $val && $group) {
                $settings->set($key, $val, $group);
                $_SESSION['toast_success'] = 'Setting "' . htmlspecialchars($key) . '" added.';
            } else {
                $_SESSION['toast_error'] = 'All fields required.';
            }
        } elseif ($_POST['action'] === 'delete' && !empty($_POST['setting_key'])) {
            $settings->delete($_POST['setting_key']);
            $_SESSION['toast_success'] = 'Setting deleted.';
        }
    }
    header('Location: settings.php');
    exit;
}

$groups = $settings->getGroups();
if (empty($groups)) {
    $settings->set('site_name', 'TPV Construction and Services LTD', 'general');
    $settings->set('site_tagline', 'Building Excellence', 'general');
    $settings->set('contact_email', 'info@tpvconstruction.com.ng', 'general');
    $settings->set('company_name', 'TPV Construction and Services LTD', 'general');
    $settings->set('company_email', 'info@tpvconstruction.com.ng', 'general');
    $settings->set('company_phone', '+234 701 234 5678', 'general');
    $settings->set('company_address', '2nd Floor, Right Wing, APDC Building, Area 11, Abuja, Nigeria', 'general');
    $settings->set('timezone', 'Africa/Lagos', 'general');
    $settings->set('currency', 'USD', 'financial');
    $settings->set('tax_rate', '7.5', 'financial');
    $settings->set('date_format', 'Y-m-d', 'formatting');
    $settings->set('time_format', 'H:i', 'formatting');
    $settings->set('blog_comments_enabled', '1', 'blog');
    $settings->set('blog_auto_approve', '0', 'blog');
    $groups = ['general', 'financial', 'formatting', 'blog'];
}

$allSettings = $settings->getAll();
$grouped = [];
$managedSettingKeys = [
    'company_name',
    'company_email',
    'company_phone',
    'company_address',
    'timezone',
    'site_logo',
    'footer_logo',
    'footer_description',
    'footer_services_heading',
    'footer_company_heading',
    'footer_contact_heading',
    'footer_phone',
    'footer_email',
    'footer_locations',
    'footer_copyright',
    'footer_instagram_url',
    'footer_facebook_url',
    'footer_twitter_url',
    'footer_linkedin_url',
    'about_intro_eyebrow',
    'about_intro_title',
    'about_intro_body',
    'about_feature_1',
    'about_feature_2',
    'about_feature_3',
    'about_quote_button_text',
    'about_support_label',
    'about_story_title',
    'about_story_body',
    'about_bottom_cta_body',
    'about_intro_image',
    'about_history_image',
    'about_video_bg_image',
    'about_cta_image',
    'contact_hero_title',
    'contact_form_eyebrow',
    'contact_form_title',
    'contact_form_body',
    'contact_phone_title',
    'contact_phone_note',
    'contact_email_title',
    'contact_email_note',
    'contact_sidebar_heading',
    'contact_sidebar_image',
    'contact_map_api_key',
    'contact_map_default_query',
    'contact_success_message',
    'contact_locations_data',
    'mail_driver',
    'smtp_host',
    'smtp_port',
    'smtp_username',
    'smtp_password',
    'smtp_encryption',
    'smtp_from_email',
    'smtp_from_name'
];
foreach ($allSettings as $s) {
    if (in_array($s['setting_key'], $managedSettingKeys, true)) {
        continue;
    }
    $g = $s['setting_group'];
    if (!isset($grouped[$g])) $grouped[$g] = [];
    $grouped[$g][] = $s;
}

$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';
$pageActive = 'settings';
$pageTitle = 'TPV Construction and Services LTD · Settings';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item active">System Settings</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <?php echo $auth->csrfField(); ?>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-building me-2"></i>
                                    Company Profile
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Company Name</label>
                                        <input type="hidden" name="groups[company_name]" value="general">
                                        <input type="text" name="settings[company_name]" class="form-control" value="<?php echo htmlspecialchars($companyName); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Company Email</label>
                                        <input type="hidden" name="groups[company_email]" value="general">
                                        <input type="email" name="settings[company_email]" class="form-control" value="<?php echo htmlspecialchars($companyEmail); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Company Phone</label>
                                        <input type="hidden" name="groups[company_phone]" value="general">
                                        <input type="text" name="settings[company_phone]" class="form-control" value="<?php echo htmlspecialchars($companyPhone); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Timezone</label>
                                        <input type="hidden" name="groups[timezone]" value="general">
                                        <input type="text" name="settings[timezone]" class="form-control" value="<?php echo htmlspecialchars($timezoneSetting); ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Company Address</label>
                                        <input type="hidden" name="groups[company_address]" value="general">
                                        <textarea name="settings[company_address]" class="form-control" rows="3"><?php echo htmlspecialchars($companyAddress); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-image me-2"></i>
                                    Branding Assets
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Site Logo</label>
                                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                                            <img src="<?php echo htmlspecialchars(tpv_asset_url($siteLogo)); ?>" alt="Site logo preview" style="max-width: 180px; max-height: 72px; width: auto; height: auto;">
                                        </div>
                                        <input type="file" name="site_logo_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                                        <div class="form-text">Used in the main website header.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Footer Logo</label>
                                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                                            <img src="<?php echo htmlspecialchars(tpv_asset_url($footerLogo)); ?>" alt="Footer logo preview" style="max-width: 180px; max-height: 86px; width: auto; height: auto;">
                                        </div>
                                        <input type="file" name="footer_logo_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                                        <div class="form-text">Used in the main website footer.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-phone-volume me-2"></i>
                                    Contact Page Content
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Hero Title</label>
                                        <input type="hidden" name="groups[contact_hero_title]" value="content">
                                        <input type="text" name="settings[contact_hero_title]" class="form-control" value="<?php echo htmlspecialchars($contactHeroTitle); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Sidebar Heading</label>
                                        <input type="hidden" name="groups[contact_sidebar_heading]" value="content">
                                        <input type="text" name="settings[contact_sidebar_heading]" class="form-control" value="<?php echo htmlspecialchars($contactSidebarHeading); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Form Eyebrow</label>
                                        <input type="hidden" name="groups[contact_form_eyebrow]" value="content">
                                        <input type="text" name="settings[contact_form_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($contactFormEyebrow); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Form Title</label>
                                        <input type="hidden" name="groups[contact_form_title]" value="content">
                                        <input type="text" name="settings[contact_form_title]" class="form-control" value="<?php echo htmlspecialchars($contactFormTitle); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Form Intro Text</label>
                                        <input type="hidden" name="groups[contact_form_body]" value="content">
                                        <textarea name="settings[contact_form_body]" class="form-control" rows="3"><?php echo htmlspecialchars($contactFormBody); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone Card Title</label>
                                        <input type="hidden" name="groups[contact_phone_title]" value="content">
                                        <input type="text" name="settings[contact_phone_title]" class="form-control" value="<?php echo htmlspecialchars($contactPhoneTitle); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone Note</label>
                                        <input type="hidden" name="groups[contact_phone_note]" value="content">
                                        <input type="text" name="settings[contact_phone_note]" class="form-control" value="<?php echo htmlspecialchars($contactPhoneNote); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Card Title</label>
                                        <input type="hidden" name="groups[contact_email_title]" value="content">
                                        <input type="text" name="settings[contact_email_title]" class="form-control" value="<?php echo htmlspecialchars($contactEmailTitle); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Note</label>
                                        <input type="hidden" name="groups[contact_email_note]" value="content">
                                        <input type="text" name="settings[contact_email_note]" class="form-control" value="<?php echo htmlspecialchars($contactEmailNote); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Map API Key</label>
                                        <input type="hidden" name="groups[contact_map_api_key]" value="content">
                                        <input type="text" name="settings[contact_map_api_key]" class="form-control" value="<?php echo htmlspecialchars($contactMapApiKey); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Default Map Query</label>
                                        <input type="hidden" name="groups[contact_map_default_query]" value="content">
                                        <input type="text" name="settings[contact_map_default_query]" class="form-control" value="<?php echo htmlspecialchars($contactMapDefaultQuery); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Success Message</label>
                                        <input type="hidden" name="groups[contact_success_message]" value="content">
                                        <input type="text" name="settings[contact_success_message]" class="form-control" value="<?php echo htmlspecialchars($contactSuccessMessage); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Office Cards</label>
                                        <input type="hidden" name="groups[contact_locations_data]" value="content">
                                        <textarea name="settings[contact_locations_data]" class="form-control" rows="6"><?php echo htmlspecialchars($contactLocationsData); ?></textarea>
                                        <div class="form-text">Use one office per line in this format: <code>Name|City|Address|Phone|Email|MapQuery</code></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-photo-video me-2"></i>
                                    Contact Page Images
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Sidebar Image</label>
                                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                                            <img src="<?php echo htmlspecialchars(tpv_asset_url($contactSidebarImage)); ?>" alt="Contact sidebar preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                                        </div>
                                        <input type="file" name="contact_sidebar_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-envelope-open-text me-2"></i>
                                    Mail Delivery
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Mail Driver</label>
                                        <input type="hidden" name="groups[mail_driver]" value="mail">
                                        <select name="settings[mail_driver]" class="form-select">
                                            <option value="phpmailer" <?php echo $mailDriver === 'phpmailer' ? 'selected' : ''; ?>>PHPMailer (Recommended)</option>
                                            <option value="resend" <?php echo $mailDriver === 'resend' ? 'selected' : ''; ?>>Resend API</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">SMTP Host</label>
                                        <input type="hidden" name="groups[smtp_host]" value="mail">
                                        <input type="text" name="settings[smtp_host]" class="form-control" value="<?php echo htmlspecialchars($smtpHost); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">SMTP Port</label>
                                        <input type="hidden" name="groups[smtp_port]" value="mail">
                                        <input type="text" name="settings[smtp_port]" class="form-control" value="<?php echo htmlspecialchars($smtpPort); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">SMTP Username</label>
                                        <input type="hidden" name="groups[smtp_username]" value="mail">
                                        <input type="text" name="settings[smtp_username]" class="form-control" value="<?php echo htmlspecialchars($smtpUsername); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">SMTP Password</label>
                                        <input type="hidden" name="groups[smtp_password]" value="mail">
                                        <input type="text" name="settings[smtp_password]" class="form-control" value="<?php echo htmlspecialchars($smtpPassword); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Encryption</label>
                                        <input type="hidden" name="groups[smtp_encryption]" value="mail">
                                        <select name="settings[smtp_encryption]" class="form-select">
                                            <option value="tls" <?php echo $smtpEncryption === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo $smtpEncryption === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                            <option value="none" <?php echo $smtpEncryption === 'none' ? 'selected' : ''; ?>>None</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">From Email</label>
                                        <input type="hidden" name="groups[smtp_from_email]" value="mail">
                                        <input type="email" name="settings[smtp_from_email]" class="form-control" value="<?php echo htmlspecialchars($smtpFromEmail); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">From Name</label>
                                        <input type="hidden" name="groups[smtp_from_name]" value="mail">
                                        <input type="text" name="settings[smtp_from_name]" class="form-control" value="<?php echo htmlspecialchars($smtpFromName); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-shoe-prints me-2"></i>
                                    Footer Content
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Footer Description</label>
                                        <input type="hidden" name="groups[footer_description]" value="footer">
                                        <textarea name="settings[footer_description]" class="form-control" rows="4"><?php echo htmlspecialchars($footerDescription); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Office Locations</label>
                                        <input type="hidden" name="groups[footer_locations]" value="footer">
                                        <textarea name="settings[footer_locations]" class="form-control" rows="4"><?php echo htmlspecialchars($footerLocations); ?></textarea>
                                        <div class="form-text">Use one office address per line.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Services Heading</label>
                                        <input type="hidden" name="groups[footer_services_heading]" value="footer">
                                        <input type="text" name="settings[footer_services_heading]" class="form-control" value="<?php echo htmlspecialchars($footerServicesHeading); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Company Heading</label>
                                        <input type="hidden" name="groups[footer_company_heading]" value="footer">
                                        <input type="text" name="settings[footer_company_heading]" class="form-control" value="<?php echo htmlspecialchars($footerCompanyHeading); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Contact Heading</label>
                                        <input type="hidden" name="groups[footer_contact_heading]" value="footer">
                                        <input type="text" name="settings[footer_contact_heading]" class="form-control" value="<?php echo htmlspecialchars($footerContactHeading); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone</label>
                                        <input type="hidden" name="groups[footer_phone]" value="footer">
                                        <input type="text" name="settings[footer_phone]" class="form-control" value="<?php echo htmlspecialchars($footerPhone); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="hidden" name="groups[footer_email]" value="footer">
                                        <input type="email" name="settings[footer_email]" class="form-control" value="<?php echo htmlspecialchars($footerEmail); ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Copyright Text</label>
                                        <input type="hidden" name="groups[footer_copyright]" value="footer">
                                        <input type="text" name="settings[footer_copyright]" class="form-control" value="<?php echo htmlspecialchars($footerCopyright); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-share-alt me-2"></i>
                                    Footer Social Links
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Instagram URL</label>
                                        <input type="hidden" name="groups[footer_instagram_url]" value="footer">
                                        <input type="url" name="settings[footer_instagram_url]" class="form-control" value="<?php echo htmlspecialchars($footerInstagram); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Facebook URL</label>
                                        <input type="hidden" name="groups[footer_facebook_url]" value="footer">
                                        <input type="url" name="settings[footer_facebook_url]" class="form-control" value="<?php echo htmlspecialchars($footerFacebook); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">X / Twitter URL</label>
                                        <input type="hidden" name="groups[footer_twitter_url]" value="footer">
                                        <input type="url" name="settings[footer_twitter_url]" class="form-control" value="<?php echo htmlspecialchars($footerTwitter); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">LinkedIn URL</label>
                                        <input type="hidden" name="groups[footer_linkedin_url]" value="footer">
                                        <input type="url" name="settings[footer_linkedin_url]" class="form-control" value="<?php echo htmlspecialchars($footerLinkedin); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php foreach ($grouped as $groupName => $groupSettings): ?>
                        <div class="card card-default mb-3">
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <?php
                                    $icon = 'settings';
                                    if ($groupName === 'general') $icon = 'globe';
                                    elseif ($groupName === 'financial') $icon = 'dollar-sign';
                                    elseif ($groupName === 'formatting') $icon = 'type';
                                    elseif ($groupName === 'blog') $icon = 'edit';
                                    ?>
                                    <i class="fas fa-cog me-2"></i>
                                    <?php echo htmlspecialchars(ucfirst($groupName)); ?> Settings
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover m-b-0">
                                    <thead>
                                        <tr>
                                            <th style="width:250px;">Key</th>
                                            <th>Value</th>
                                            <th style="width:100px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupSettings as $s): ?>
                                        <tr class="setting-row">
                                            <td><code><?php echo htmlspecialchars($s['setting_key']); ?></code></td>
                                            <td class="setting-value">
                                                <input type="hidden" name="groups[<?php echo htmlspecialchars($s['setting_key']); ?>]" value="<?php echo htmlspecialchars($groupName); ?>">
                                                <?php if (in_array($s['setting_key'], ['blog_comments_enabled', 'blog_auto_approve', 'maintenance_mode'])): ?>
                                                <select name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-select form-select-sm">
                                                    <option value="1" <?php echo $s['setting_value'] === '1' ? 'selected' : ''; ?>>Enabled</option>
                                                    <option value="0" <?php echo $s['setting_value'] === '0' ? 'selected' : ''; ?>>Disabled</option>
                                                </select>
                                                <?php elseif (in_array($s['setting_key'], ['site_description', 'address', 'email_signature', 'invoice_terms', 'invoice_footer'])): ?>
                                                <textarea name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($s['setting_value']); ?></textarea>
                                                <?php elseif (in_array($s['setting_key'], ['tax_rate', 'currency'])): ?>
                                                <input type="text" name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['setting_value']); ?>" style="max-width:150px;">
                                                <?php else: ?>
                                                <input type="text" name="settings[<?php echo htmlspecialchars($s['setting_key']); ?>]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['setting_value']); ?>">
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-client-icon" onclick="return confirmAction(this, 'Delete this setting?', function(){ document.getElementById('deleteKey').value='<?php echo htmlspecialchars($s['setting_key']); ?>'; document.getElementById('deleteForm').submit(); })" title="Delete">
                                                    <i class="fas fa-trash" style="width:14px;height:14px;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Save All Settings</button>
                        </div>
                    </form>

                    <!-- Separate delete form -->
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="setting_key" id="deleteKey">
                        <?php echo $auth->csrfField(); ?>
                    </form>
                </div>
            </div>
<?php require 'inc/admin_footer.php'; ?>
