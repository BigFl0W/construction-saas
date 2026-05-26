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

$homeIntroSupportTitle = $settings->get('home_intro_support_title', 'call support center 24X7');

$homeServicesEyebrow = $settings->get('home_services_eyebrow', 'Our services');
$homeServicesTitle = $settings->get('home_services_title', 'Our construction services');
$homeServicesBody = $settings->get('home_services_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects.');
$homeServicesButtonText = $settings->get('home_services_button_text', 'view all services');
$homeServicesButtonLink = $settings->get('home_services_button_link', 'services/');

$homeServicesCards = [];
$homeServicesCardDefaults = [
    1 => ['title' => 'Architecture & Design', 'body' => 'Our architectural team creates innovative designs that blend functionality with aesthetic appeal. We specialize in residential, commercial, and industrial architecture, ensuring every project meets modern standards while respecting local building codes.', 'image' => 'wp-content/uploads/2024/06/service-img-2.jpg', 'link' => 'services/architecture-design/', 'button_text' => 'View More'],
    2 => ['title' => 'Renovation & Remodeling', 'body' => 'Transform your existing space with our expert renovation services. From interior remodeling to structural upgrades, we breathe new life into buildings while maintaining their integrity and improving functionality for modern needs.', 'image' => 'wp-content/uploads/2024/06/service-img-3.png', 'link' => 'services/building-renovation/', 'button_text' => 'View More'],
    3 => ['title' => 'Construction & Building', 'body' => 'From foundation to finishing, TPV Construction and Services LTD delivers complete building solutions. Our expertise covers residential homes, commercial complexes, and industrial facilities, all built to the highest standards of quality and safety.', 'image' => 'wp-content/uploads/2024/06/service-img-1.jpg', 'link' => 'services/building-construction/', 'button_text' => 'View More'],
    4 => ['title' => 'Interior & Exterior', 'body' => 'Enhance your property with our comprehensive interior and exterior finishing services. From stunning interior designs, flooring, and painting to exterior cladding, landscaping, and facade improvements, we transform spaces inside and out with attention to detail and quality craftsmanship.', 'image' => 'wp-content/uploads/2024/06/service-img-4.png', 'link' => 'services/interior-exterior/', 'button_text' => 'View More'],
];
foreach ($homeServicesCardDefaults as $index => $defaults) {
    $homeServicesCards[$index] = [
        'title' => $settings->get("home_services_card_{$index}_title", $defaults['title']),
        'body' => $settings->get("home_services_card_{$index}_body", $defaults['body']),
        'image' => $settings->get("home_services_card_{$index}_image", $defaults['image']),
        'link' => $settings->get("home_services_card_{$index}_link", $defaults['link']),
        'button_text' => $settings->get("home_services_card_{$index}_button_text", $defaults['button_text']),
    ];
}

$homeWhyEyebrow = $settings->get('home_why_eyebrow', 'Why choose TPV?');
$homeWhyTitle = $settings->get('home_why_title', "Building Nigeria's future with excellence since 2008");
$homeWhyBody = $settings->get('home_why_body', 'With over 400 successful projects completed across Nigeria, TPV Construction and Services LTD has earned the trust of clients through our commitment to quality, safety, and innovation. Our experienced team combines local expertise with modern construction techniques to deliver exceptional results every time.');

$homeProjectsEyebrow = $settings->get('home_projects_eyebrow', 'Our projects');
$homeProjectsTitle = $settings->get('home_projects_title', 'Explore our diverse range of projects');
$homeProjectsBody = $settings->get('home_projects_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects.');
$homeProjectsButtonText = $settings->get('home_projects_button_text', 'view all projects');
$homeProjectsButtonLink = $settings->get('home_projects_button_link', 'projects/');

$homeCtaTitle = $settings->get('home_cta_title', 'From Concept to Completion - We Build It All');
$homeCtaBody = $settings->get('home_cta_body', "Whether you're planning a residential dream home, a commercial development, or an industrial facility, TPV Construction and Services LTD provides end-to-end solutions tailored to the Nigerian market. Our integrated approach ensures seamless execution from design to handover.");
$homeCtaButtonText = $settings->get('home_cta_button_text', 'Start Your Project');
$homeCtaButtonLink = $settings->get('home_cta_button_link', 'quote/');
$homeCtaImage = $settings->get('home_cta_image', 'wp-content/uploads/2024/06/cta-box-img.png');

$homeTestimonialsEyebrow = $settings->get('home_testimonials_eyebrow', 'Testimonials');
$homeTestimonialsTitle = $settings->get('home_testimonials_title', 'What our clients say about us');
$homeTestimonialsBody = $settings->get('home_testimonials_body', "Don't just take our word for it - hear from some of our satisfied clients across Nigeria who have experienced our commitment to excellence firsthand.");
$homeTestimonials = [];
$homeTestimonialDefaults = [
    1 => ['body' => "\"TPV Construction and Services LTD built our family home in Ikoyi, and I couldn't be more impressed with their professionalism. From the foundation to the finishing touches, their attention to detail was remarkable. The project was completed on time and within budget - a rare find in Nigeria's construction industry. I highly recommend them.\"", 'name' => 'Chief Adebayo Ogunlesi', 'role' => 'Lagos, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-1.jpg'],
    2 => ['body' => "\"As a healthcare professional, I needed a reliable contractor for my medical clinic in Abuja. TPV Construction and Services LTD exceeded my expectations. Their team understood the unique requirements of a healthcare facility and delivered a space that's both functional and welcoming. The quality of workmanship is exceptional.\"", 'name' => 'Dr. Ngozi Okonkwo', 'role' => 'Abuja, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-2.jpg'],
    3 => ['body' => "\"I contracted TPV Construction and Services LTD for the renovation of my commercial plaza in Kano. Their project management was impeccable - they coordinated everything seamlessly and minimized disruption to my tenants. The renovation has significantly increased the value of my property. These guys are true professionals.\"", 'name' => 'Alhaji Suleiman Bello', 'role' => 'Kano, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-3.jpg'],
    4 => ['body' => "\"The team at TPV Construction and Services LTD renovated our office headquarters in Port Harcourt, and the transformation is incredible. They understood our brand identity and created a workspace that reflects our corporate values. The project was delivered ahead of schedule, and the communication throughout was excellent.\"", 'name' => 'Mrs. Funmilayo Adekunle', 'role' => 'Port Harcourt, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-4.jpg'],
];
foreach ($homeTestimonialDefaults as $index => $defaults) {
    $homeTestimonials[$index] = [
        'body' => $settings->get("home_testimonial_{$index}_body", $defaults['body']),
        'name' => $settings->get("home_testimonial_{$index}_name", $defaults['name']),
        'role' => $settings->get("home_testimonial_{$index}_role", $defaults['role']),
        'image' => $settings->get("home_testimonial_{$index}_image", $defaults['image']),
    ];
}

$homeFaqEyebrow = $settings->get('home_faq_eyebrow', 'Frequently Asked Questions');
$homeFaqTitle = $settings->get('home_faq_title', 'Common Questions About Our Construction Services');
$homeFaqBody = $settings->get('home_faq_body', 'Find answers to the most common questions about our construction process, pricing, timelines, and how we deliver quality projects across Nigeria.');
$homeFaqImage = $settings->get('home_faq_image', 'wp-content/uploads/2024/06/faq-image.jpg');
$homeFaqs = [];
$homeFaqDefaults = [
    1 => ['question' => 'Do you offer free quotations and project consultations?', 'answer' => 'Yes, we provide free, no-obligation quotations for all potential projects. Our team will visit your site, discuss your requirements, and provide a detailed estimate. We believe in transparency and ensuring you have all the information needed to make an informed decision before any work begins.'],
    2 => ['question' => 'What construction services do you offer across Nigeria?', 'answer' => 'We offer comprehensive construction services including: residential building, commercial development, industrial construction, architectural design, renovation and remodeling, project management, and construction consultancy. Our team handles projects of all sizes across Lagos, Abuja, Port Harcourt, Kano, and other major Nigerian cities.'],
    3 => ['question' => 'How long does a typical construction project take?', 'answer' => "Project timelines vary based on scope, size, and complexity. A standard residential home typically takes 6-12 months, while commercial projects range from 8-18 months. During your free consultation, we'll provide a detailed timeline based on your specific requirements, site conditions, and project specifications."],
    4 => ['question' => 'How do I start a construction project with TPV?', 'answer' => "Starting your project is simple: contact us via phone, email, or our website to schedule a free consultation. We'll visit your site, discuss your vision and requirements, and provide a detailed proposal. Once approved, our team handles everything from permits and approvals to construction and final handover."],
    5 => ['question' => 'Are you licensed and insured to operate in Nigeria?', 'answer' => 'Yes, TPV Construction and Services LTD is fully licensed by the relevant Nigerian authorities and holds comprehensive insurance coverage. We comply with all local building regulations and industry standards. Our team includes certified professionals who ensure every project meets legal requirements and safety standards.'],
];
foreach ($homeFaqDefaults as $index => $defaults) {
    $homeFaqs[$index] = [
        'question' => $settings->get("home_faq_{$index}_question", $defaults['question']),
        'answer' => $settings->get("home_faq_{$index}_answer", $defaults['answer']),
    ];
}

$homeBlogEyebrow = $settings->get('home_blog_eyebrow', 'News & blog');
$homeBlogTitle = $settings->get('home_blog_title', 'Articles & blog posts');
$homeBlogBody = $settings->get('home_blog_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects.');
$homeBlogEmptyText = $settings->get('home_blog_empty_text', 'No blog posts yet.');

$homeContactPhoneTitle = $settings->get('home_contact_phone_title', 'Call Our Head Office');
$homeContactPhoneValue = $settings->get('home_contact_phone_value', '+234 701 234 5678');
$homeContactPhoneNote = $settings->get('home_contact_phone_note', 'Available Mon-Fri, 8am-6pm');
$homeContactEmailTitle = $settings->get('home_contact_email_title', 'Write To Us');
$homeContactEmailValue = $settings->get('home_contact_email_value', 'info@tpvconstruction.com.ng');
$homeContactEmailNote = $settings->get('home_contact_email_note', 'We reply within 24 hours');
$homeContactImage = $settings->get('home_contact_image', 'wp-content/uploads/2024/06/contact-info-img.png');
$homeContactEyebrow = $settings->get('home_contact_eyebrow', 'Contact us');
$homeContactTitle = $settings->get('home_contact_title', 'Get in touch with us');
$homeContactPlaceholderName = $settings->get('home_contact_placeholder_name', 'Your Full Name *');
$homeContactPlaceholderEmail = $settings->get('home_contact_placeholder_email', 'Email Address *');
$homeContactPlaceholderPhone = $settings->get('home_contact_placeholder_phone', 'Phone Number *');
$homeContactPlaceholderSubject = $settings->get('home_contact_placeholder_subject', 'Subject *');
$homeContactPlaceholderMessage = $settings->get('home_contact_placeholder_message', 'Tell us about your project...');
$homeContactSubmitText = $settings->get('home_contact_submit_text', 'Send Message');

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
                'home_services_card_1_image_file' => ['target' => 'home-services-card-1', 'setting' => 'home_services_card_1_image'],
                'home_services_card_2_image_file' => ['target' => 'home-services-card-2', 'setting' => 'home_services_card_2_image'],
                'home_services_card_3_image_file' => ['target' => 'home-services-card-3', 'setting' => 'home_services_card_3_image'],
                'home_services_card_4_image_file' => ['target' => 'home-services-card-4', 'setting' => 'home_services_card_4_image'],
                'home_cta_image_file' => ['target' => 'home-cta-image', 'setting' => 'home_cta_image'],
                'home_testimonial_1_image_file' => ['target' => 'home-testimonial-1', 'setting' => 'home_testimonial_1_image'],
                'home_testimonial_2_image_file' => ['target' => 'home-testimonial-2', 'setting' => 'home_testimonial_2_image'],
                'home_testimonial_3_image_file' => ['target' => 'home-testimonial-3', 'setting' => 'home_testimonial_3_image'],
                'home_testimonial_4_image_file' => ['target' => 'home-testimonial-4', 'setting' => 'home_testimonial_4_image'],
                'home_faq_image_file' => ['target' => 'home-faq-image', 'setting' => 'home_faq_image'],
                'home_contact_image_file' => ['target' => 'home-contact-image', 'setting' => 'home_contact_image'],
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
                    Homepage Feature Strip
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-light border mb-4">
                    This controls the homepage section with the left feature card, the large construction image in the middle, the right feature card, and the lower continuation card.
                </div>
                <?php
                $whyCards = [
                    1 => [$homeWhy1Title, $homeWhy1Body, $homeWhy1Image, $homeWhy1CounterTitle, $homeWhy1CounterValue, $homeWhy1CounterSuffix],
                    2 => [$homeWhy2Title, $homeWhy2Body, $homeWhy2Image, $homeWhy2CounterTitle, $homeWhy2CounterValue, $homeWhy2CounterSuffix],
                    3 => [$homeWhy3Title, $homeWhy3Body, $homeWhy3Image, $homeWhy3CounterTitle, $homeWhy3CounterValue, $homeWhy3CounterSuffix],
                ];
                $whyCardLabels = [
                    1 => ['title' => 'Left Feature Card', 'image' => 'Center Image 1'],
                    2 => ['title' => 'Right Feature Card', 'image' => 'Center Image 2'],
                    3 => ['title' => 'Lower Feature Card', 'image' => 'Supporting Image 3'],
                ];
                foreach ($whyCards as $index => [$title, $body, $image, $counterTitle, $counterValue, $counterSuffix]):
                ?>
                    <div class="border rounded-4 p-3 p-md-4 mb-4">
                        <h6 class="fw-bold mb-3"><?php echo htmlspecialchars($whyCardLabels[$index]['title']); ?></h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><?php echo htmlspecialchars($whyCardLabels[$index]['image']); ?></label>
                                <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                                    <img src="<?php echo htmlspecialchars(tpv_asset_url($image)); ?>" alt="Why choose image <?php echo $index; ?>" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                                </div>
                                <input type="file" name="home_why_<?php echo $index; ?>_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold"><?php echo htmlspecialchars($whyCardLabels[$index]['title']); ?> Title</label>
                                        <input type="hidden" name="groups[home_why_<?php echo $index; ?>_title]" value="homepage">
                                        <input type="text" name="settings[home_why_<?php echo $index; ?>_title]" class="form-control" value="<?php echo htmlspecialchars($title); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold"><?php echo htmlspecialchars($whyCardLabels[$index]['title']); ?> Body</label>
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

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-headset me-2"></i>
                    Intro Support
                </div>
            </div>
            <div class="card-body">
                <label class="form-label fw-semibold">Support Box Title</label>
                <input type="hidden" name="groups[home_intro_support_title]" value="homepage">
                <input type="text" name="settings[home_intro_support_title]" class="form-control" value="<?php echo htmlspecialchars($homeIntroSupportTitle); ?>">
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header">
                <div class="card-title d-flex align-items-center">
                    <i class="fas fa-helmet-safety me-2"></i>
                    Services Section
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Eyebrow</label>
                        <input type="hidden" name="groups[home_services_eyebrow]" value="homepage">
                        <input type="text" name="settings[home_services_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeServicesEyebrow); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Section Title</label>
                        <input type="hidden" name="groups[home_services_title]" value="homepage">
                        <input type="text" name="settings[home_services_title]" class="form-control" value="<?php echo htmlspecialchars($homeServicesTitle); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Section Description</label>
                        <input type="hidden" name="groups[home_services_body]" value="homepage">
                        <textarea name="settings[home_services_body]" class="form-control" rows="3"><?php echo htmlspecialchars($homeServicesBody); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bottom Button Text</label>
                        <input type="hidden" name="groups[home_services_button_text]" value="homepage">
                        <input type="text" name="settings[home_services_button_text]" class="form-control" value="<?php echo htmlspecialchars($homeServicesButtonText); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bottom Button Link</label>
                        <input type="hidden" name="groups[home_services_button_link]" value="homepage">
                        <input type="text" name="settings[home_services_button_link]" class="form-control" value="<?php echo htmlspecialchars($homeServicesButtonLink); ?>">
                    </div>
                </div>

                <?php foreach ($homeServicesCards as $index => $card): ?>
                    <div class="border rounded-4 p-3 p-md-4 mb-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Card <?php echo $index; ?> Image</label>
                                <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                                    <img src="<?php echo htmlspecialchars(tpv_asset_url($card['image'])); ?>" alt="Service card <?php echo $index; ?>" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                                </div>
                                <input type="file" name="home_services_card_<?php echo $index; ?>_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Card <?php echo $index; ?> Title</label>
                                        <input type="hidden" name="groups[home_services_card_<?php echo $index; ?>_title]" value="homepage">
                                        <input type="text" name="settings[home_services_card_<?php echo $index; ?>_title]" class="form-control" value="<?php echo htmlspecialchars($card['title']); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Card <?php echo $index; ?> Body</label>
                                        <input type="hidden" name="groups[home_services_card_<?php echo $index; ?>_body]" value="homepage">
                                        <textarea name="settings[home_services_card_<?php echo $index; ?>_body]" class="form-control" rows="4"><?php echo htmlspecialchars($card['body']); ?></textarea>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label fw-semibold">Card Link</label>
                                        <input type="hidden" name="groups[home_services_card_<?php echo $index; ?>_link]" value="homepage">
                                        <input type="text" name="settings[home_services_card_<?php echo $index; ?>_link]" class="form-control" value="<?php echo htmlspecialchars($card['link']); ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">Button Text</label>
                                        <input type="hidden" name="groups[home_services_card_<?php echo $index; ?>_button_text]" value="homepage">
                                        <input type="text" name="settings[home_services_card_<?php echo $index; ?>_button_text]" class="form-control" value="<?php echo htmlspecialchars($card['button_text']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header"><div class="card-title d-flex align-items-center"><i class="fas fa-award me-2"></i>Feature Strip Intro</div></div>
            <div class="card-body">
                <div class="alert alert-light border mb-4">
                    This controls the heading and intro text shown directly above the homepage feature strip.
                </div>
                <div class="row g-4">
                    <div class="col-md-4"><label class="form-label fw-semibold">Eyebrow</label><input type="hidden" name="groups[home_why_eyebrow]" value="homepage"><input type="text" name="settings[home_why_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeWhyEyebrow); ?>"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Title</label><input type="hidden" name="groups[home_why_title]" value="homepage"><input type="text" name="settings[home_why_title]" class="form-control" value="<?php echo htmlspecialchars($homeWhyTitle); ?>"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Description</label><input type="hidden" name="groups[home_why_body]" value="homepage"><textarea name="settings[home_why_body]" class="form-control" rows="4"><?php echo htmlspecialchars($homeWhyBody); ?></textarea></div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header"><div class="card-title d-flex align-items-center"><i class="fas fa-diagram-project me-2"></i>Projects Section</div></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4"><label class="form-label fw-semibold">Eyebrow</label><input type="hidden" name="groups[home_projects_eyebrow]" value="homepage"><input type="text" name="settings[home_projects_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeProjectsEyebrow); ?>"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Title</label><input type="hidden" name="groups[home_projects_title]" value="homepage"><input type="text" name="settings[home_projects_title]" class="form-control" value="<?php echo htmlspecialchars($homeProjectsTitle); ?>"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Description</label><input type="hidden" name="groups[home_projects_body]" value="homepage"><textarea name="settings[home_projects_body]" class="form-control" rows="3"><?php echo htmlspecialchars($homeProjectsBody); ?></textarea></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Button Text</label><input type="hidden" name="groups[home_projects_button_text]" value="homepage"><input type="text" name="settings[home_projects_button_text]" class="form-control" value="<?php echo htmlspecialchars($homeProjectsButtonText); ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Button Link</label><input type="hidden" name="groups[home_projects_button_link]" value="homepage"><input type="text" name="settings[home_projects_button_link]" class="form-control" value="<?php echo htmlspecialchars($homeProjectsButtonLink); ?>"></div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header"><div class="card-title d-flex align-items-center"><i class="fas fa-bullhorn me-2"></i>CTA Section</div></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">CTA Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($homeCtaImage)); ?>" alt="CTA preview" style="max-width: 220px; max-height: 150px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="home_cta_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">CTA Title</label>
                        <input type="hidden" name="groups[home_cta_title]" value="homepage">
                        <input type="text" name="settings[home_cta_title]" class="form-control mb-3" value="<?php echo htmlspecialchars($homeCtaTitle); ?>">
                        <label class="form-label fw-semibold">CTA Description</label>
                        <input type="hidden" name="groups[home_cta_body]" value="homepage">
                        <textarea name="settings[home_cta_body]" class="form-control" rows="4"><?php echo htmlspecialchars($homeCtaBody); ?></textarea>
                    </div>
                    <div class="col-md-6"><label class="form-label fw-semibold">CTA Button Text</label><input type="hidden" name="groups[home_cta_button_text]" value="homepage"><input type="text" name="settings[home_cta_button_text]" class="form-control" value="<?php echo htmlspecialchars($homeCtaButtonText); ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">CTA Button Link</label><input type="hidden" name="groups[home_cta_button_link]" value="homepage"><input type="text" name="settings[home_cta_button_link]" class="form-control" value="<?php echo htmlspecialchars($homeCtaButtonLink); ?>"></div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header"><div class="card-title d-flex align-items-center"><i class="fas fa-comments me-2"></i>Testimonials Section</div></div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-4"><label class="form-label fw-semibold">Eyebrow</label><input type="hidden" name="groups[home_testimonials_eyebrow]" value="homepage"><input type="text" name="settings[home_testimonials_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeTestimonialsEyebrow); ?>"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Title</label><input type="hidden" name="groups[home_testimonials_title]" value="homepage"><input type="text" name="settings[home_testimonials_title]" class="form-control" value="<?php echo htmlspecialchars($homeTestimonialsTitle); ?>"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Description</label><input type="hidden" name="groups[home_testimonials_body]" value="homepage"><textarea name="settings[home_testimonials_body]" class="form-control" rows="3"><?php echo htmlspecialchars($homeTestimonialsBody); ?></textarea></div>
                </div>
                <?php foreach ($homeTestimonials as $index => $item): ?>
                    <div class="border rounded-4 p-3 p-md-4 mb-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Testimonial <?php echo $index; ?> Image</label>
                                <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                                    <img src="<?php echo htmlspecialchars(tpv_asset_url($item['image'])); ?>" alt="Testimonial <?php echo $index; ?>" style="max-width: 120px; max-height: 120px; width: auto; height: auto;">
                                </div>
                                <input type="file" name="home_testimonial_<?php echo $index; ?>_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-7"><label class="form-label fw-semibold">Name</label><input type="hidden" name="groups[home_testimonial_<?php echo $index; ?>_name]" value="homepage"><input type="text" name="settings[home_testimonial_<?php echo $index; ?>_name]" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>"></div>
                                    <div class="col-md-5"><label class="form-label fw-semibold">Role / Location</label><input type="hidden" name="groups[home_testimonial_<?php echo $index; ?>_role]" value="homepage"><input type="text" name="settings[home_testimonial_<?php echo $index; ?>_role]" class="form-control" value="<?php echo htmlspecialchars($item['role']); ?>"></div>
                                    <div class="col-12"><label class="form-label fw-semibold">Quote</label><input type="hidden" name="groups[home_testimonial_<?php echo $index; ?>_body]" value="homepage"><textarea name="settings[home_testimonial_<?php echo $index; ?>_body]" class="form-control" rows="4"><?php echo htmlspecialchars($item['body']); ?></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header"><div class="card-title d-flex align-items-center"><i class="fas fa-circle-question me-2"></i>FAQ Section</div></div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-4"><label class="form-label fw-semibold">Eyebrow</label><input type="hidden" name="groups[home_faq_eyebrow]" value="homepage"><input type="text" name="settings[home_faq_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeFaqEyebrow); ?>"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Title</label><input type="hidden" name="groups[home_faq_title]" value="homepage"><input type="text" name="settings[home_faq_title]" class="form-control" value="<?php echo htmlspecialchars($homeFaqTitle); ?>"></div>
                    <div class="col-md-7"><label class="form-label fw-semibold">Description</label><input type="hidden" name="groups[home_faq_body]" value="homepage"><textarea name="settings[home_faq_body]" class="form-control" rows="3"><?php echo htmlspecialchars($homeFaqBody); ?></textarea></div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">FAQ Side Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($homeFaqImage)); ?>" alt="FAQ preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="home_faq_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                </div>
                <?php foreach ($homeFaqs as $index => $item): ?>
                    <div class="border rounded-4 p-3 p-md-4 mb-3">
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label fw-semibold">FAQ <?php echo $index; ?> Question</label><input type="hidden" name="groups[home_faq_<?php echo $index; ?>_question]" value="homepage"><input type="text" name="settings[home_faq_<?php echo $index; ?>_question]" class="form-control" value="<?php echo htmlspecialchars($item['question']); ?>"></div>
                            <div class="col-12"><label class="form-label fw-semibold">FAQ <?php echo $index; ?> Answer</label><input type="hidden" name="groups[home_faq_<?php echo $index; ?>_answer]" value="homepage"><textarea name="settings[home_faq_<?php echo $index; ?>_answer]" class="form-control" rows="4"><?php echo htmlspecialchars($item['answer']); ?></textarea></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header"><div class="card-title d-flex align-items-center"><i class="fas fa-newspaper me-2"></i>Blog Preview Section</div></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4"><label class="form-label fw-semibold">Eyebrow</label><input type="hidden" name="groups[home_blog_eyebrow]" value="homepage"><input type="text" name="settings[home_blog_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeBlogEyebrow); ?>"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Title</label><input type="hidden" name="groups[home_blog_title]" value="homepage"><input type="text" name="settings[home_blog_title]" class="form-control" value="<?php echo htmlspecialchars($homeBlogTitle); ?>"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Description</label><input type="hidden" name="groups[home_blog_body]" value="homepage"><textarea name="settings[home_blog_body]" class="form-control" rows="3"><?php echo htmlspecialchars($homeBlogBody); ?></textarea></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Empty State Text</label><input type="hidden" name="groups[home_blog_empty_text]" value="homepage"><input type="text" name="settings[home_blog_empty_text]" class="form-control" value="<?php echo htmlspecialchars($homeBlogEmptyText); ?>"></div>
                </div>
            </div>
        </div>

        <div class="card card-default mb-3">
            <div class="card-header"><div class="card-title d-flex align-items-center"><i class="fas fa-envelope-open-text me-2"></i>Contact Section</div></div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Sidebar Image</label>
                        <div class="border rounded-3 p-3 bg-light mb-2 text-center">
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($homeContactImage)); ?>" alt="Contact preview" style="max-width: 180px; max-height: 140px; width: auto; height: auto;">
                        </div>
                        <input type="file" name="home_contact_image_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                    </div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Section Eyebrow</label><input type="hidden" name="groups[home_contact_eyebrow]" value="homepage"><input type="text" name="settings[home_contact_eyebrow]" class="form-control" value="<?php echo htmlspecialchars($homeContactEyebrow); ?>"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Section Title</label><input type="hidden" name="groups[home_contact_title]" value="homepage"><input type="text" name="settings[home_contact_title]" class="form-control" value="<?php echo htmlspecialchars($homeContactTitle); ?>"></div>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Phone Card</h6>
                        <label class="form-label fw-semibold">Title</label><input type="hidden" name="groups[home_contact_phone_title]" value="homepage"><input type="text" name="settings[home_contact_phone_title]" class="form-control mb-2" value="<?php echo htmlspecialchars($homeContactPhoneTitle); ?>">
                        <label class="form-label fw-semibold">Phone Value</label><input type="hidden" name="groups[home_contact_phone_value]" value="homepage"><input type="text" name="settings[home_contact_phone_value]" class="form-control mb-2" value="<?php echo htmlspecialchars($homeContactPhoneValue); ?>">
                        <label class="form-label fw-semibold">Note</label><input type="hidden" name="groups[home_contact_phone_note]" value="homepage"><input type="text" name="settings[home_contact_phone_note]" class="form-control" value="<?php echo htmlspecialchars($homeContactPhoneNote); ?>">
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Email Card</h6>
                        <label class="form-label fw-semibold">Title</label><input type="hidden" name="groups[home_contact_email_title]" value="homepage"><input type="text" name="settings[home_contact_email_title]" class="form-control mb-2" value="<?php echo htmlspecialchars($homeContactEmailTitle); ?>">
                        <label class="form-label fw-semibold">Email Value</label><input type="hidden" name="groups[home_contact_email_value]" value="homepage"><input type="text" name="settings[home_contact_email_value]" class="form-control mb-2" value="<?php echo htmlspecialchars($homeContactEmailValue); ?>">
                        <label class="form-label fw-semibold">Note</label><input type="hidden" name="groups[home_contact_email_note]" value="homepage"><input type="text" name="settings[home_contact_email_note]" class="form-control" value="<?php echo htmlspecialchars($homeContactEmailNote); ?>">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-semibold">Name Placeholder</label><input type="hidden" name="groups[home_contact_placeholder_name]" value="homepage"><input type="text" name="settings[home_contact_placeholder_name]" class="form-control" value="<?php echo htmlspecialchars($homeContactPlaceholderName); ?>"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Email Placeholder</label><input type="hidden" name="groups[home_contact_placeholder_email]" value="homepage"><input type="text" name="settings[home_contact_placeholder_email]" class="form-control" value="<?php echo htmlspecialchars($homeContactPlaceholderEmail); ?>"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Phone Placeholder</label><input type="hidden" name="groups[home_contact_placeholder_phone]" value="homepage"><input type="text" name="settings[home_contact_placeholder_phone]" class="form-control" value="<?php echo htmlspecialchars($homeContactPlaceholderPhone); ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Subject Placeholder</label><input type="hidden" name="groups[home_contact_placeholder_subject]" value="homepage"><input type="text" name="settings[home_contact_placeholder_subject]" class="form-control" value="<?php echo htmlspecialchars($homeContactPlaceholderSubject); ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Submit Button Text</label><input type="hidden" name="groups[home_contact_submit_text]" value="homepage"><input type="text" name="settings[home_contact_submit_text]" class="form-control" value="<?php echo htmlspecialchars($homeContactSubmitText); ?>"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Message Placeholder</label><input type="hidden" name="groups[home_contact_placeholder_message]" value="homepage"><input type="text" name="settings[home_contact_placeholder_message]" class="form-control" value="<?php echo htmlspecialchars($homeContactPlaceholderMessage); ?>"></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Homepage Settings
            </button>
        </div>
    </form>
</div>
