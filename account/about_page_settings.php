<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Settings.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$settings = new Settings();

function aboutHandleAssetUpload($fileKey, $targetName, $subDirectory = 'about') {
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
        throw new Exception('Unable to save uploaded About page asset.');
    }

    return $relativePath;
}

$aboutIntroEyebrow = $settings->get('about_intro_eyebrow', 'Welcome to TPV');
$aboutIntroTitle = $settings->get('about_intro_title', 'TPV Construction and Services LTD<br>Company');
$aboutIntroBody = $settings->get('about_intro_body', "Serving Nigeria's construction needs since 2008 with excellence and innovation. TPV Construction and Services LTD has established itself as a trusted name in Nigeria's construction industry, delivering exceptional projects across residential, commercial, and industrial sectors. Our commitment to quality, safety, and sustainable building practices has made us the preferred choice for discerning clients throughout Nigeria.");
$aboutFeatureOne = $settings->get('about_feature_1', 'Comprehensive Services');
$aboutFeatureTwo = $settings->get('about_feature_2', 'Advanced Technology');
$aboutFeatureThree = $settings->get('about_feature_3', 'Transparent Communication');
$aboutQuoteButton = $settings->get('about_quote_button_text', 'Get Free Quote');
$aboutSupportLabel = $settings->get('about_support_label', 'call support center 24X7');

$aboutHistoryEyebrow = $settings->get('about_history_eyebrow', 'our history');
$aboutStoryTitle = $settings->get('about_story_title', 'TPV Construction and Services LTD');
$aboutHistoryHighlight = $settings->get('about_history_highlight', "Nigeria's Premier Construction Partner");
$aboutStoryBody = $settings->get('about_story_body', 'With over 15 years of experience and hundreds of successful projects across Nigeria, TPV Construction and Services LTD has established itself as a trusted name in the construction industry.');
$aboutHistoryExtraBody = $settings->get('about_history_extra_body', 'Our commitment to excellence, safety, and innovation has made us the preferred choice for residential, commercial, and industrial construction projects. From Lagos to Abuja, Port Harcourt to Kano, we deliver quality that stands the test of time.');
$aboutExperienceLabel = $settings->get('about_experience_label', 'Years Of Experience');
$aboutExperienceValue = $settings->get('about_experience_value', '15');
$aboutExperienceSuffix = $settings->get('about_experience_suffix', '+');

$aboutMissionTitle = $settings->get('about_mission_title', 'Our Mission');
$aboutMissionBody = $settings->get('about_mission_body', 'To provide excellent services to our clients by exceeding their expectations through safe, cost effective, efficient, timely and worldclass solutions in construction, engineering and real estate services.');
$aboutVisionTitle = $settings->get('about_vision_title', 'Our Vision');
$aboutVisionBody = $settings->get('about_vision_body', 'To become a household name in construction, engineering and real estate services among private individuals, corporate and government organizations, locally and internationally in the next decade.');
$aboutValuesTitle = $settings->get('about_values_title', 'Our Values');
$aboutValuesIntro = $settings->get('about_values_intro', 'OUR CORPORATE VALUES: (SERVE)');
$aboutValuesPoints = $settings->get('about_values_points', "Stakeholders' satisfaction\nExcellent service delivery\nRespect and integrity\nValue creation\nExceeding expectations");
$aboutBottomCtaBody = $settings->get('about_bottom_cta_body', "From residential homes in Lagos to commercial complexes in Abuja, TPV Construction and Services LTD delivers excellence across Nigeria. Let's bring your vision to life with quality craftsmanship and professional service.");
$aboutWhatWeDoEyebrow = $settings->get('about_what_we_do_eyebrow', 'what we do');
$aboutWhatWeDoTitle = $settings->get('about_what_we_do_title', "Building Nigeria's future on a foundation of excellence");
$aboutWhatWeDoBody = $settings->get('about_what_we_do_body', 'We deliver comprehensive construction solutions across Nigeria — from residential homes in Lagos to commercial complexes in Abuja and industrial facilities in Port Harcourt. Every project reflects our commitment to quality, safety, and client satisfaction.');
$aboutStat1Title = $settings->get('about_stat_1_title', 'Regulatory Approval Rate');
$aboutStat1Value = $settings->get('about_stat_1_value', '100');
$aboutStat1Suffix = $settings->get('about_stat_1_suffix', '%');
$aboutStat2Title = $settings->get('about_stat_2_title', 'Active Projects Nationwide');
$aboutStat2Value = $settings->get('about_stat_2_value', '45');
$aboutStat2Suffix = $settings->get('about_stat_2_suffix', '+');
$aboutStat3Title = $settings->get('about_stat_3_title', 'Projects Completed Since 2008');
$aboutStat3Value = $settings->get('about_stat_3_value', '350');
$aboutStat3Suffix = $settings->get('about_stat_3_suffix', '+');
$aboutStat4Title = $settings->get('about_stat_4_title', 'Skilled Professionals & Workers');
$aboutStat4Value = $settings->get('about_stat_4_value', '250');
$aboutStat4Suffix = $settings->get('about_stat_4_suffix', '+');
$aboutCtaTitle = $settings->get('about_cta_title', 'Ready to build your dream project in Nigeria?');
$aboutCtaButtonText = $settings->get('about_cta_button_text', 'Get Your Free Quote');
$aboutTeamEyebrow = $settings->get('about_team_eyebrow', 'Team');
$aboutTeamTitle = $settings->get('about_team_title', 'Our team');
$aboutTeamBody = $settings->get('about_team_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects.');
$aboutTeamMember1Name = $settings->get('about_team_member_1_name', 'Benjamin Miller');
$aboutTeamMember1Role = $settings->get('about_team_member_1_role', 'Project Manager');
$aboutTeamMember1Bio = $settings->get('about_team_member_1_bio', 'A small river named Duden flows by their place and supplies it with the necessary');
$aboutTeamMember1Phone = $settings->get('about_team_member_1_phone', '+1 (859) 254-6589');
$aboutTeamMember1Email = $settings->get('about_team_member_1_email', 'info@example.com');
$aboutTeamMember1Facebook = $settings->get('about_team_member_1_facebook', 'https://facebook.com');
$aboutTeamMember1X = $settings->get('about_team_member_1_x', 'https://x.com');
$aboutTeamMember1Instagram = $settings->get('about_team_member_1_instagram', 'https://instagram.com');
$aboutTeamMember2Name = $settings->get('about_team_member_2_name', 'Jane Smith');
$aboutTeamMember2Role = $settings->get('about_team_member_2_role', 'Lead Architect');
$aboutTeamMember2Bio = $settings->get('about_team_member_2_bio', 'A small river named Duden flows by their place and supplies it with the necessary');
$aboutTeamMember2Phone = $settings->get('about_team_member_2_phone', '+1 (859) 254-6589');
$aboutTeamMember2Email = $settings->get('about_team_member_2_email', 'info@example.com');
$aboutTeamMember2Facebook = $settings->get('about_team_member_2_facebook', 'https://facebook.com');
$aboutTeamMember2X = $settings->get('about_team_member_2_x', 'https://x.com');
$aboutTeamMember2Instagram = $settings->get('about_team_member_2_instagram', 'https://instagram.com');
$aboutTeamMember3Name = $settings->get('about_team_member_3_name', 'Mike Johnson');
$aboutTeamMember3Role = $settings->get('about_team_member_3_role', 'Chief Engineer');
$aboutTeamMember3Bio = $settings->get('about_team_member_3_bio', 'A small river named Duden flows by their place and supplies it with the necessary');
$aboutTeamMember3Phone = $settings->get('about_team_member_3_phone', '+1 (859) 254-6589');
$aboutTeamMember3Email = $settings->get('about_team_member_3_email', 'info@example.com');
$aboutTeamMember3Facebook = $settings->get('about_team_member_3_facebook', 'https://facebook.com');
$aboutTeamMember3X = $settings->get('about_team_member_3_x', 'https://x.com');
$aboutTeamMember3Instagram = $settings->get('about_team_member_3_instagram', 'https://instagram.com');

$aboutIntroImage = $settings->get('about_intro_image', 'wp-content/uploads/2024/06/about-us-img.png');
$aboutHistoryImage = $settings->get('about_history_image', 'wp-content/uploads/2024/06/company-history-img.jpg');
$aboutVideoBackgroundImage = $settings->get('about_video_bg_image', 'wp-content/uploads/2024/06/video-bg.jpg');
$aboutCtaImage = $settings->get('about_cta_image', 'wp-content/uploads/2024/06/cta-box-img.png');
$aboutTeamMember1Image = $settings->get('about_team_member_1_image', 'wp-content/uploads/2024/06/team-1.jpg');
$aboutTeamMember2Image = $settings->get('about_team_member_2_image', 'wp-content/uploads/2024/06/team-2.jpg');
$aboutTeamMember3Image = $settings->get('about_team_member_3_image', 'wp-content/uploads/2024/06/team-3.jpg');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        try {
            if (isset($_POST['settings']) && is_array($_POST['settings'])) {
                foreach ($_POST['settings'] as $key => $value) {
                    $group = $_POST['groups'][$key] ?? 'content';
                    $settings->set($key, $value, $group);
                }
            }

            $assetMap = [
                'about_intro_image_file' => ['target' => 'about-intro-image', 'setting' => 'about_intro_image'],
                'about_history_image_file' => ['target' => 'about-history-image', 'setting' => 'about_history_image'],
                'about_video_bg_image_file' => ['target' => 'about-video-bg-image', 'setting' => 'about_video_bg_image'],
                'about_cta_image_file' => ['target' => 'about-cta-image', 'setting' => 'about_cta_image'],
                'about_team_member_1_image_file' => ['target' => 'about-team-member-1', 'setting' => 'about_team_member_1_image'],
                'about_team_member_2_image_file' => ['target' => 'about-team-member-2', 'setting' => 'about_team_member_2_image'],
                'about_team_member_3_image_file' => ['target' => 'about-team-member-3', 'setting' => 'about_team_member_3_image'],
            ];

            foreach ($assetMap as $fileKey => $assetConfig) {
                $uploaded = aboutHandleAssetUpload($fileKey, $assetConfig['target'], 'about');
                if ($uploaded) {
                    $settings->set($assetConfig['setting'], $uploaded, 'content');
                }
            }

            $_SESSION['toast_success'] = 'About page settings updated successfully.';
        } catch (Throwable $e) {
            $_SESSION['toast_error'] = $e->getMessage();
        }
    }

    header('Location: about_page_settings.php');
    exit;
}

$pageActive = 'about_page_settings';
$pageTitle = 'TPV Construction and Services LTD · About Page Settings';
require 'inc/admin_header.php';
?>

<div data-pages="parallax">
    <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">About Page Settings</li>
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
                    <i class="fas fa-circle-info me-2"></i>
                    Intro Section
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Intro Eyebrow</label>
                        <input type="hidden" name="groups[about_intro_eyebrow]" value="content">
                        <input type="text" name="settings[about_intro_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($aboutIntroEyebrow); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Quote Button Text</label>
                        <input type="hidden" name="groups[about_quote_button_text]" value="content">
                        <input type="text" name="settings[about_quote_button_text]" class="form-control" value="<?php echo htmlspecialchars($aboutQuoteButton); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Intro Title</label>
                        <input type="hidden" name="groups[about_intro_title]" value="content">
                        <textarea name="settings[about_intro_title]" class="form-control" rows="2"><?php echo htmlspecialchars($aboutIntroTitle); ?></textarea>
                        <div class="form-text">You can use simple HTML like &lt;br&gt; for line breaks.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Intro Body</label>
                        <input type="hidden" name="groups[about_intro_body]" value="content">
                        <textarea name="settings[about_intro_body]" class="form-control" rows="5"><?php echo htmlspecialchars($aboutIntroBody); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Feature 1</label>
                        <input type="hidden" name="groups[about_feature_1]" value="content">
                        <input type="text" name="settings[about_feature_1]" class="form-control" value="<?php echo htmlspecialchars($aboutFeatureOne); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Feature 2</label>
                        <input type="hidden" name="groups[about_feature_2]" value="content">
                        <input type="text" name="settings[about_feature_2]" class="form-control" value="<?php echo htmlspecialchars($aboutFeatureTwo); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Feature 3</label>
                        <input type="hidden" name="groups[about_feature_3]" value="content">
                        <input type="text" name="settings[about_feature_3]" class="form-control" value="<?php echo htmlspecialchars($aboutFeatureThree); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Support Label</label>
                        <input type="hidden" name="groups[about_support_label]" value="content">
                        <input type="text" name="settings[about_support_label]" class="form-control" value="<?php echo htmlspecialchars($aboutSupportLabel); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-landmark me-2"></i>
                    History Section
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">History Eyebrow</label>
                        <input type="hidden" name="groups[about_history_eyebrow]" value="content">
                        <input type="text" name="settings[about_history_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($aboutHistoryEyebrow); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Experience Label</label>
                        <input type="hidden" name="groups[about_experience_label]" value="content">
                        <input type="text" name="settings[about_experience_label]" class="form-control" value="<?php echo htmlspecialchars($aboutExperienceLabel); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Experience Value</label>
                        <input type="hidden" name="groups[about_experience_value]" value="content">
                        <input type="text" name="settings[about_experience_value]" class="form-control" value="<?php echo htmlspecialchars($aboutExperienceValue); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Suffix</label>
                        <input type="hidden" name="groups[about_experience_suffix]" value="content">
                        <input type="text" name="settings[about_experience_suffix]" class="form-control" value="<?php echo htmlspecialchars($aboutExperienceSuffix); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">History Title</label>
                        <input type="hidden" name="groups[about_story_title]" value="content">
                        <input type="text" name="settings[about_story_title]" class="form-control" value="<?php echo htmlspecialchars($aboutStoryTitle); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">History Highlight</label>
                        <input type="hidden" name="groups[about_history_highlight]" value="content">
                        <input type="text" name="settings[about_history_highlight]" class="form-control" value="<?php echo htmlspecialchars($aboutHistoryHighlight); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">History Paragraph 1</label>
                        <input type="hidden" name="groups[about_story_body]" value="content">
                        <textarea name="settings[about_story_body]" class="form-control" rows="4"><?php echo htmlspecialchars($aboutStoryBody); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">History Paragraph 2</label>
                        <input type="hidden" name="groups[about_history_extra_body]" value="content">
                        <textarea name="settings[about_history_extra_body]" class="form-control" rows="4"><?php echo htmlspecialchars($aboutHistoryExtraBody); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-gem me-2"></i>
                    Mission, Vision & Values
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mission Title</label>
                        <input type="hidden" name="groups[about_mission_title]" value="content">
                        <input type="text" name="settings[about_mission_title]" class="form-control" value="<?php echo htmlspecialchars($aboutMissionTitle); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vision Title</label>
                        <input type="hidden" name="groups[about_vision_title]" value="content">
                        <input type="text" name="settings[about_vision_title]" class="form-control" value="<?php echo htmlspecialchars($aboutVisionTitle); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mission Body</label>
                        <input type="hidden" name="groups[about_mission_body]" value="content">
                        <textarea name="settings[about_mission_body]" class="form-control" rows="5"><?php echo htmlspecialchars($aboutMissionBody); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vision Body</label>
                        <input type="hidden" name="groups[about_vision_body]" value="content">
                        <textarea name="settings[about_vision_body]" class="form-control" rows="5"><?php echo htmlspecialchars($aboutVisionBody); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Values Title</label>
                        <input type="hidden" name="groups[about_values_title]" value="content">
                        <input type="text" name="settings[about_values_title]" class="form-control" value="<?php echo htmlspecialchars($aboutValuesTitle); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Values Intro</label>
                        <input type="hidden" name="groups[about_values_intro]" value="content">
                        <input type="text" name="settings[about_values_intro]" class="form-control" value="<?php echo htmlspecialchars($aboutValuesIntro); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Values Points</label>
                        <input type="hidden" name="groups[about_values_points]" value="content">
                        <textarea name="settings[about_values_points]" class="form-control" rows="6"><?php echo htmlspecialchars($aboutValuesPoints); ?></textarea>
                        <div class="form-text">Enter one value point per line.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Bottom CTA Body</label>
                        <input type="hidden" name="groups[about_bottom_cta_body]" value="content">
                        <textarea name="settings[about_bottom_cta_body]" class="form-control" rows="4"><?php echo htmlspecialchars($aboutBottomCtaBody); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-building me-2"></i>
                    What We Do & Stats
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Section Eyebrow</label>
                        <input type="hidden" name="groups[about_what_we_do_eyebrow]" value="content">
                        <input type="text" name="settings[about_what_we_do_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($aboutWhatWeDoEyebrow); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Section Title</label>
                        <input type="hidden" name="groups[about_what_we_do_title]" value="content">
                        <textarea name="settings[about_what_we_do_title]" class="form-control" rows="3"><?php echo htmlspecialchars($aboutWhatWeDoTitle); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Section Body</label>
                        <input type="hidden" name="groups[about_what_we_do_body]" value="content">
                        <textarea name="settings[about_what_we_do_body]" class="form-control" rows="4"><?php echo htmlspecialchars($aboutWhatWeDoBody); ?></textarea>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold">Stat 1 Title</label>
                        <input type="hidden" name="groups[about_stat_1_title]" value="content">
                        <input type="text" name="settings[about_stat_1_title]" class="form-control" value="<?php echo htmlspecialchars($aboutStat1Title); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Stat 1 Value</label>
                        <input type="hidden" name="groups[about_stat_1_value]" value="content">
                        <input type="text" name="settings[about_stat_1_value]" class="form-control" value="<?php echo htmlspecialchars($aboutStat1Value); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Suffix</label>
                        <input type="hidden" name="groups[about_stat_1_suffix]" value="content">
                        <input type="text" name="settings[about_stat_1_suffix]" class="form-control" value="<?php echo htmlspecialchars($aboutStat1Suffix); ?>">
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold">Stat 2 Title</label>
                        <input type="hidden" name="groups[about_stat_2_title]" value="content">
                        <input type="text" name="settings[about_stat_2_title]" class="form-control" value="<?php echo htmlspecialchars($aboutStat2Title); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Stat 2 Value</label>
                        <input type="hidden" name="groups[about_stat_2_value]" value="content">
                        <input type="text" name="settings[about_stat_2_value]" class="form-control" value="<?php echo htmlspecialchars($aboutStat2Value); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Suffix</label>
                        <input type="hidden" name="groups[about_stat_2_suffix]" value="content">
                        <input type="text" name="settings[about_stat_2_suffix]" class="form-control" value="<?php echo htmlspecialchars($aboutStat2Suffix); ?>">
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold">Stat 3 Title</label>
                        <input type="hidden" name="groups[about_stat_3_title]" value="content">
                        <input type="text" name="settings[about_stat_3_title]" class="form-control" value="<?php echo htmlspecialchars($aboutStat3Title); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Stat 3 Value</label>
                        <input type="hidden" name="groups[about_stat_3_value]" value="content">
                        <input type="text" name="settings[about_stat_3_value]" class="form-control" value="<?php echo htmlspecialchars($aboutStat3Value); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Suffix</label>
                        <input type="hidden" name="groups[about_stat_3_suffix]" value="content">
                        <input type="text" name="settings[about_stat_3_suffix]" class="form-control" value="<?php echo htmlspecialchars($aboutStat3Suffix); ?>">
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold">Stat 4 Title</label>
                        <input type="hidden" name="groups[about_stat_4_title]" value="content">
                        <input type="text" name="settings[about_stat_4_title]" class="form-control" value="<?php echo htmlspecialchars($aboutStat4Title); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Stat 4 Value</label>
                        <input type="hidden" name="groups[about_stat_4_value]" value="content">
                        <input type="text" name="settings[about_stat_4_value]" class="form-control" value="<?php echo htmlspecialchars($aboutStat4Value); ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-semibold">Suffix</label>
                        <input type="hidden" name="groups[about_stat_4_suffix]" value="content">
                        <input type="text" name="settings[about_stat_4_suffix]" class="form-control" value="<?php echo htmlspecialchars($aboutStat4Suffix); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-bullhorn me-2"></i>
                    CTA & Team Intro
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold">CTA Title</label>
                        <input type="hidden" name="groups[about_cta_title]" value="content">
                        <textarea name="settings[about_cta_title]" class="form-control" rows="3"><?php echo htmlspecialchars($aboutCtaTitle); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Button Text</label>
                        <input type="hidden" name="groups[about_cta_button_text]" value="content">
                        <input type="text" name="settings[about_cta_button_text]" class="form-control" value="<?php echo htmlspecialchars($aboutCtaButtonText); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Team Eyebrow</label>
                        <input type="hidden" name="groups[about_team_eyebrow]" value="content">
                        <input type="text" name="settings[about_team_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($aboutTeamEyebrow); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Team Title</label>
                        <input type="hidden" name="groups[about_team_title]" value="content">
                        <input type="text" name="settings[about_team_title]" class="form-control" value="<?php echo htmlspecialchars($aboutTeamTitle); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Team Intro Body</label>
                        <input type="hidden" name="groups[about_team_body]" value="content">
                        <textarea name="settings[about_team_body]" class="form-control" rows="4"><?php echo htmlspecialchars($aboutTeamBody); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-images me-2"></i>
                    About Page Images
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Intro Section Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($aboutIntroImage)); ?>" alt="About intro preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="about_intro_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">History Section Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($aboutHistoryImage)); ?>" alt="About history preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="about_history_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Video Background Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($aboutVideoBackgroundImage)); ?>" alt="About video background preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="about_video_bg_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bottom CTA Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($aboutCtaImage)); ?>" alt="About CTA preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="about_cta_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-users me-2"></i>
                    Team Members
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Team Member 1 Name</label>
                        <input type="hidden" name="groups[about_team_member_1_name]" value="content">
                        <input type="text" name="settings[about_team_member_1_name]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember1Name); ?>">
                        <label class="form-label fw-semibold">Team Member 1 Role</label>
                        <input type="hidden" name="groups[about_team_member_1_role]" value="content">
                        <input type="text" name="settings[about_team_member_1_role]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember1Role); ?>">
                        <label class="form-label fw-semibold">Team Member 1 Bio</label>
                        <input type="hidden" name="groups[about_team_member_1_bio]" value="content">
                        <textarea name="settings[about_team_member_1_bio]" class="form-control mb-3" rows="4"><?php echo htmlspecialchars($aboutTeamMember1Bio); ?></textarea>
                        <label class="form-label fw-semibold">Team Member 1 Phone</label>
                        <input type="hidden" name="groups[about_team_member_1_phone]" value="content">
                        <input type="text" name="settings[about_team_member_1_phone]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember1Phone); ?>">
                        <label class="form-label fw-semibold">Team Member 1 Email</label>
                        <input type="hidden" name="groups[about_team_member_1_email]" value="content">
                        <input type="email" name="settings[about_team_member_1_email]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember1Email); ?>">
                        <label class="form-label fw-semibold">Facebook URL</label>
                        <input type="hidden" name="groups[about_team_member_1_facebook]" value="content">
                        <input type="url" name="settings[about_team_member_1_facebook]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember1Facebook); ?>">
                        <label class="form-label fw-semibold">X URL</label>
                        <input type="hidden" name="groups[about_team_member_1_x]" value="content">
                        <input type="url" name="settings[about_team_member_1_x]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember1X); ?>">
                        <label class="form-label fw-semibold">Instagram URL</label>
                        <input type="hidden" name="groups[about_team_member_1_instagram]" value="content">
                        <input type="url" name="settings[about_team_member_1_instagram]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember1Instagram); ?>">
                        <label class="form-label fw-semibold">Team Member 1 Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($aboutTeamMember1Image)); ?>" alt="Team member 1 preview" style="max-width: 180px; max-height: 180px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="about_team_member_1_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Team Member 2 Name</label>
                        <input type="hidden" name="groups[about_team_member_2_name]" value="content">
                        <input type="text" name="settings[about_team_member_2_name]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember2Name); ?>">
                        <label class="form-label fw-semibold">Team Member 2 Role</label>
                        <input type="hidden" name="groups[about_team_member_2_role]" value="content">
                        <input type="text" name="settings[about_team_member_2_role]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember2Role); ?>">
                        <label class="form-label fw-semibold">Team Member 2 Bio</label>
                        <input type="hidden" name="groups[about_team_member_2_bio]" value="content">
                        <textarea name="settings[about_team_member_2_bio]" class="form-control mb-3" rows="4"><?php echo htmlspecialchars($aboutTeamMember2Bio); ?></textarea>
                        <label class="form-label fw-semibold">Team Member 2 Phone</label>
                        <input type="hidden" name="groups[about_team_member_2_phone]" value="content">
                        <input type="text" name="settings[about_team_member_2_phone]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember2Phone); ?>">
                        <label class="form-label fw-semibold">Team Member 2 Email</label>
                        <input type="hidden" name="groups[about_team_member_2_email]" value="content">
                        <input type="email" name="settings[about_team_member_2_email]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember2Email); ?>">
                        <label class="form-label fw-semibold">Facebook URL</label>
                        <input type="hidden" name="groups[about_team_member_2_facebook]" value="content">
                        <input type="url" name="settings[about_team_member_2_facebook]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember2Facebook); ?>">
                        <label class="form-label fw-semibold">X URL</label>
                        <input type="hidden" name="groups[about_team_member_2_x]" value="content">
                        <input type="url" name="settings[about_team_member_2_x]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember2X); ?>">
                        <label class="form-label fw-semibold">Instagram URL</label>
                        <input type="hidden" name="groups[about_team_member_2_instagram]" value="content">
                        <input type="url" name="settings[about_team_member_2_instagram]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember2Instagram); ?>">
                        <label class="form-label fw-semibold">Team Member 2 Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($aboutTeamMember2Image)); ?>" alt="Team member 2 preview" style="max-width: 180px; max-height: 180px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="about_team_member_2_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Team Member 3 Name</label>
                        <input type="hidden" name="groups[about_team_member_3_name]" value="content">
                        <input type="text" name="settings[about_team_member_3_name]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember3Name); ?>">
                        <label class="form-label fw-semibold">Team Member 3 Role</label>
                        <input type="hidden" name="groups[about_team_member_3_role]" value="content">
                        <input type="text" name="settings[about_team_member_3_role]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember3Role); ?>">
                        <label class="form-label fw-semibold">Team Member 3 Bio</label>
                        <input type="hidden" name="groups[about_team_member_3_bio]" value="content">
                        <textarea name="settings[about_team_member_3_bio]" class="form-control mb-3" rows="4"><?php echo htmlspecialchars($aboutTeamMember3Bio); ?></textarea>
                        <label class="form-label fw-semibold">Team Member 3 Phone</label>
                        <input type="hidden" name="groups[about_team_member_3_phone]" value="content">
                        <input type="text" name="settings[about_team_member_3_phone]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember3Phone); ?>">
                        <label class="form-label fw-semibold">Team Member 3 Email</label>
                        <input type="hidden" name="groups[about_team_member_3_email]" value="content">
                        <input type="email" name="settings[about_team_member_3_email]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember3Email); ?>">
                        <label class="form-label fw-semibold">Facebook URL</label>
                        <input type="hidden" name="groups[about_team_member_3_facebook]" value="content">
                        <input type="url" name="settings[about_team_member_3_facebook]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember3Facebook); ?>">
                        <label class="form-label fw-semibold">X URL</label>
                        <input type="hidden" name="groups[about_team_member_3_x]" value="content">
                        <input type="url" name="settings[about_team_member_3_x]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember3X); ?>">
                        <label class="form-label fw-semibold">Instagram URL</label>
                        <input type="hidden" name="groups[about_team_member_3_instagram]" value="content">
                        <input type="url" name="settings[about_team_member_3_instagram]" class="form-control mb-3" value="<?php echo htmlspecialchars($aboutTeamMember3Instagram); ?>">
                        <label class="form-label fw-semibold">Team Member 3 Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($aboutTeamMember3Image)); ?>" alt="Team member 3 preview" style="max-width: 180px; max-height: 180px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="about_team_member_3_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save About Page Settings
            </button>
        </div>
    </form>
</div>
