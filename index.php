<?php
require_once 'config/config.php';
require_once 'classes/Blog.php';
require_once 'classes/Project.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$db = Database::getInstance();
$pageSettings = new Settings();
$blog = new Blog();
$projectModel = new Project();

function homepage_url(string $path = ''): string
{
    $base = rtrim((string) SITE_URL, '/') . '/';
    $path = ltrim($path, '/');
    return $path === '' ? $base : $base . $path;
}

function homepage_link(string $value, string $fallback = ''): string
{
    $value = trim($value);
    if ($value === '') {
        $value = $fallback;
    }

    if ($value === '') {
        return homepage_url();
    }

    if (
        str_starts_with($value, 'http://') ||
        str_starts_with($value, 'https://') ||
        str_starts_with($value, 'mailto:') ||
        str_starts_with($value, 'tel:') ||
        str_starts_with($value, '#')
    ) {
        return $value;
    }

    return homepage_url($value);
}

function homepage_excerpt(string $text, int $limit = 140): string
{
    $plain = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
    if ($plain === '') {
        return '';
    }
    return mb_strimwidth($plain, 0, $limit, '...');
}

function homepage_project_image(Database $db, int $projectId): string
{
    static $cache = [];

    if (array_key_exists($projectId, $cache)) {
        return $cache[$projectId];
    }

    try {
        $image = $db->query(
            "SELECT file_path
             FROM project_media
             WHERE project_id = :project_id AND media_type = 'image'
             ORDER BY is_featured DESC, sort_order ASC, id ASC
             LIMIT 1",
            ['project_id' => $projectId]
        )->fetchColumn();
        $cache[$projectId] = $image ? tpv_asset_url((string) $image) : '';
    } catch (Throwable $e) {
        $cache[$projectId] = '';
    }

    return $cache[$projectId];
}

function homepage_post_author(array $post): string
{
    if (($post['author_type'] ?? '') === 'client' && !empty($post['client_author'])) {
        return (string) $post['client_author'];
    }

    if (!empty($post['employee_author'])) {
        return (string) $post['employee_author'];
    }

    return 'TPV Editorial Team';
}

function homepage_post_image(array $post): string
{
    $path = trim((string) ($post['featured_image_path'] ?? ''));
    return $path !== '' ? tpv_asset_url($path) : '';
}

function homepage_read_time(array $post): string
{
    $words = str_word_count(strip_tags((string) ($post['content'] ?? '')));
    $minutes = max(1, (int) ceil($words / 220));
    return $minutes . ' min read';
}

$supportPhone = trim((string) $pageSettings->get('company_phone', '+234 701 234 5678'));
$supportPhoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $supportPhone);
$supportEmail = trim((string) $pageSettings->get('company_email', 'info@tpvconstruction.com.ng'));

$homeHeroEyebrow = $pageSettings->get('home_hero_eyebrow', 'Welcome to TPV Construction and Services LTD');
$homeHeroTitle = $pageSettings->get('home_hero_title', 'Building dreams with precision and excellence');
$homeHeroBody = $pageSettings->get('home_hero_body', 'we specialize in turning visions into reality with exceptional craftsmanship and meticulous attention to detail. With years of experience and a commitment to quality.');
$homeHeroPrimaryText = $pageSettings->get('home_hero_primary_text', 'get started');
$homeHeroPrimaryLink = $pageSettings->get('home_hero_primary_link', 'contact-us/');
$homeHeroSecondaryText = $pageSettings->get('home_hero_secondary_text', 'view Projects');
$homeHeroSecondaryLink = $pageSettings->get('home_hero_secondary_link', 'projects/');
$homeHeroBgImage = tpv_asset_url($pageSettings->get('home_hero_bg_image', 'wp-content/uploads/2024/06/hero-bg.jpg'));

$homeIntroImage = tpv_asset_url($pageSettings->get('home_intro_image', 'wp-content/uploads/2024/06/about-us-img.png'));
$homeIntroEyebrow = $pageSettings->get('home_intro_eyebrow', 'Welcome to TPV');
$homeIntroTitle = $pageSettings->get('home_intro_title', 'TPV Construction and Services LTD');
$homeIntroBody = $pageSettings->get('home_intro_body', "Serving Nigeria's construction needs since 2008 with excellence and innovation. TPV Construction and Services LTD has established itself as a trusted name in Nigeria's construction industry, delivering exceptional projects across residential, commercial, and industrial sectors. Our commitment to quality, safety, and sustainable building practices has made us the preferred choice for discerning clients throughout Nigeria.");
$homeIntroFeature1 = $pageSettings->get('home_intro_feature_1', 'Comprehensive Services');
$homeIntroFeature2 = $pageSettings->get('home_intro_feature_2', 'Advanced Technology');
$homeIntroFeature3 = $pageSettings->get('home_intro_feature_3', 'Transparent Communication');
$homeIntroButtonText = $pageSettings->get('home_intro_button_text', 'Get Free Quote');
$homeIntroButtonLink = $pageSettings->get('home_intro_button_link', 'quote/');
$homeIntroSupportTitle = $pageSettings->get('home_intro_support_title', 'call support center 24X7');

$homeWhyEyebrow = $pageSettings->get('home_why_eyebrow', 'Why choose TPV?');
$homeWhyTitle = $pageSettings->get('home_why_title', "Building Nigeria's future with excellence since 2008");
$homeWhyBody = $pageSettings->get('home_why_body', 'With over 400 successful projects completed across Nigeria, TPV Construction and Services LTD has earned the trust of clients through our commitment to quality, safety, and innovation. Our experienced team combines local expertise with modern construction techniques to deliver exceptional results every time.');

$homeWhy1Title = $pageSettings->get('home_why_1_title', 'Innovative Solutions');
$homeWhy1Body = $pageSettings->get('home_why_1_body', 'We combine modern construction technology with innovative approaches to deliver projects that exceed expectations while optimizing costs and timelines.');
$homeWhy1Image = tpv_asset_url($pageSettings->get('home_why_1_image', 'wp-content/uploads/2024/06/why-choose-img-1.jpg'));
$homeWhy1CounterTitle = $pageSettings->get('home_why_1_counter_title', 'Projects Completed');
$homeWhy1CounterValue = $pageSettings->get('home_why_1_counter_value', '450');
$homeWhy1CounterSuffix = $pageSettings->get('home_why_1_counter_suffix', '+');

$homeWhy2Title = $pageSettings->get('home_why_2_title', 'Quality Craftsmanship');
$homeWhy2Body = $pageSettings->get('home_why_2_body', 'Our skilled craftsmen take pride in their work, ensuring every detail meets the highest standards of Nigerian and international construction quality.');
$homeWhy2Image = tpv_asset_url($pageSettings->get('home_why_2_image', 'wp-content/uploads/2024/06/why-choose-img-2.jpg'));
$homeWhy2CounterTitle = $pageSettings->get('home_why_2_counter_title', 'Projects Completed');
$homeWhy2CounterValue = $pageSettings->get('home_why_2_counter_value', '450');
$homeWhy2CounterSuffix = $pageSettings->get('home_why_2_counter_suffix', '+');

$homeWhy3Title = $pageSettings->get('home_why_3_title', 'Expertise And Experience');
$homeWhy3Body = $pageSettings->get('home_why_3_body', 'With over a decade of experience in the Nigerian construction industry, our team brings deep local knowledge and proven expertise to every project.');
$homeWhy3Image = tpv_asset_url($pageSettings->get('home_why_3_image', 'wp-content/uploads/2024/06/why-choose-img-3.jpg'));
$homeWhy3CounterTitle = $pageSettings->get('home_why_3_counter_title', 'Projects Completed');
$homeWhy3CounterValue = $pageSettings->get('home_why_3_counter_value', '450');
$homeWhy3CounterSuffix = $pageSettings->get('home_why_3_counter_suffix', '+');

$homeServicesEyebrow = $pageSettings->get('home_services_eyebrow', 'Our services');
$homeServicesTitle = $pageSettings->get('home_services_title', 'Our construction services');
$homeServicesBody = $pageSettings->get('home_services_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects.');
$homeServicesButtonText = $pageSettings->get('home_services_button_text', 'view all services');
$homeServicesButtonLink = $pageSettings->get('home_services_button_link', 'services/');
$homeServicesCardDefaults = [
    1 => ['title' => 'Architecture & Design', 'body' => 'Our architectural team creates innovative designs that blend functionality with aesthetic appeal. We specialize in residential, commercial, and industrial architecture, ensuring every project meets modern standards while respecting local building codes.', 'image' => 'wp-content/uploads/2024/06/service-img-2.jpg', 'link' => 'services/architecture-design/', 'button_text' => 'View More'],
    2 => ['title' => 'Renovation & Remodeling', 'body' => 'Transform your existing space with our expert renovation services. From interior remodeling to structural upgrades, we breathe new life into buildings while maintaining their integrity and improving functionality for modern needs.', 'image' => 'wp-content/uploads/2024/06/service-img-3.png', 'link' => 'services/building-renovation/', 'button_text' => 'View More'],
    3 => ['title' => 'Construction & Building', 'body' => 'From foundation to finishing, TPV Construction and Services LTD delivers complete building solutions. Our expertise covers residential homes, commercial complexes, and industrial facilities, all built to the highest standards of quality and safety.', 'image' => 'wp-content/uploads/2024/06/service-img-1.jpg', 'link' => 'services/building-construction/', 'button_text' => 'View More'],
    4 => ['title' => 'Interior & Exterior', 'body' => 'Enhance your property with our comprehensive interior and exterior finishing services. From stunning interior designs, flooring, and painting to exterior cladding, landscaping, and facade improvements, we transform spaces inside and out with attention to detail and quality craftsmanship.', 'image' => 'wp-content/uploads/2024/06/service-img-4.png', 'link' => 'services/interior-exterior/', 'button_text' => 'View More'],
];
$homeServicesCards = [];
foreach ($homeServicesCardDefaults as $index => $defaults) {
    $homeServicesCards[$index] = [
        'title' => $pageSettings->get("home_services_card_{$index}_title", $defaults['title']),
        'body' => $pageSettings->get("home_services_card_{$index}_body", $defaults['body']),
        'image' => tpv_asset_url($pageSettings->get("home_services_card_{$index}_image", $defaults['image'])),
        'link' => $pageSettings->get("home_services_card_{$index}_link", $defaults['link']),
        'button_text' => $pageSettings->get("home_services_card_{$index}_button_text", $defaults['button_text']),
    ];
}

$homeProjectsEyebrow = $pageSettings->get('home_projects_eyebrow', 'Our projects');
$homeProjectsTitle = $pageSettings->get('home_projects_title', 'Explore our diverse range of projects');
$homeProjectsBody = $pageSettings->get('home_projects_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects.');
$homeProjectsButtonText = $pageSettings->get('home_projects_button_text', 'view all projects');
$homeProjectsButtonLink = $pageSettings->get('home_projects_button_link', 'projects/');

$homeCtaTitle = $pageSettings->get('home_cta_title', 'From Concept to Completion - We Build It All');
$homeCtaBody = $pageSettings->get('home_cta_body', "Whether you're planning a residential dream home, a commercial development, or an industrial facility, TPV Construction and Services LTD provides end-to-end solutions tailored to the Nigerian market. Our integrated approach ensures seamless execution from design to handover.");
$homeCtaButtonText = $pageSettings->get('home_cta_button_text', 'Start Your Project');
$homeCtaButtonLink = $pageSettings->get('home_cta_button_link', 'quote/');
$homeCtaImage = tpv_asset_url($pageSettings->get('home_cta_image', 'wp-content/uploads/2024/06/cta-box-img.png'));

$homeTestimonialsEyebrow = $pageSettings->get('home_testimonials_eyebrow', 'Testimonials');
$homeTestimonialsTitle = $pageSettings->get('home_testimonials_title', 'What our clients say about us');
$homeTestimonialsBody = $pageSettings->get('home_testimonials_body', "Don't just take our word for it - hear from some of our satisfied clients across Nigeria who have experienced our commitment to excellence firsthand.");
$homeTestimonialDefaults = [
    1 => ['body' => "TPV Construction and Services LTD built our family home in Ikoyi, and I couldn't be more impressed with their professionalism. From the foundation to the finishing touches, their attention to detail was remarkable. The project was completed on time and within budget.", 'name' => 'Chief Adebayo Ogunlesi', 'role' => 'Lagos, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-1.jpg'],
    2 => ['body' => 'As a healthcare professional, I needed a reliable contractor for my medical clinic in Abuja. TPV Construction and Services LTD exceeded my expectations and delivered a space that is both functional and welcoming.', 'name' => 'Dr. Ngozi Okonkwo', 'role' => 'Abuja, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-2.jpg'],
    3 => ['body' => 'Their project management was impeccable. The renovation of our commercial plaza was coordinated seamlessly and completed with minimal disruption to tenants.', 'name' => 'Alhaji Suleiman Bello', 'role' => 'Kano, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-3.jpg'],
    4 => ['body' => 'The team transformed our office headquarters into a workspace that reflects our brand and was delivered ahead of schedule.', 'name' => 'Mrs. Funmilayo Adekunle', 'role' => 'Port Harcourt, Nigeria', 'image' => 'wp-content/uploads/2024/06/author-4.jpg'],
];
$homeTestimonials = [];
foreach ($homeTestimonialDefaults as $index => $defaults) {
    $homeTestimonials[$index] = [
        'body' => $pageSettings->get("home_testimonial_{$index}_body", $defaults['body']),
        'name' => $pageSettings->get("home_testimonial_{$index}_name", $defaults['name']),
        'role' => $pageSettings->get("home_testimonial_{$index}_role", $defaults['role']),
        'image' => tpv_asset_url($pageSettings->get("home_testimonial_{$index}_image", $defaults['image'])),
    ];
}

$homeFaqEyebrow = $pageSettings->get('home_faq_eyebrow', 'Frequently Asked Questions');
$homeFaqTitle = $pageSettings->get('home_faq_title', 'Common Questions About Our Construction Services');
$homeFaqBody = $pageSettings->get('home_faq_body', 'Find answers to the most common questions about our construction process, pricing, timelines, and how we deliver quality projects across Nigeria.');
$homeFaqImageSetting = (string) $pageSettings->get('home_faq_image', 'wp-content/uploads/2024/06/faq-image.jpg');
$homeFaqImage = tpv_asset_url($homeFaqImageSetting);
$homeFaqImageExists = $homeFaqImageSetting !== '' && file_exists(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($homeFaqImageSetting, '/')));
$homeFaqDefaults = [
    1 => ['question' => 'Do you offer free quotations and project consultations?', 'answer' => 'Yes, we provide free, no-obligation quotations for all potential projects. Our team will visit your site, discuss your requirements, and provide a detailed estimate before work begins.'],
    2 => ['question' => 'What construction services do you offer across Nigeria?', 'answer' => 'We offer comprehensive services including residential building, commercial development, industrial construction, architectural design, renovation, and project management.'],
    3 => ['question' => 'How long does a typical construction project take?', 'answer' => 'Project timelines vary based on scope and complexity. During your consultation, we provide a realistic timeline based on your site conditions and deliverables.'],
    4 => ['question' => 'How do I start a construction project with TPV?', 'answer' => 'Contact us to schedule a free consultation. We will review your site, understand your vision, and submit a detailed proposal for approval.'],
    5 => ['question' => 'Are you licensed and insured to operate in Nigeria?', 'answer' => 'Yes, TPV Construction and Services LTD is fully licensed and insured, and our team works in compliance with relevant building regulations and safety standards.'],
];
$homeFaqs = [];
foreach ($homeFaqDefaults as $index => $defaults) {
    $homeFaqs[$index] = [
        'question' => $pageSettings->get("home_faq_{$index}_question", $defaults['question']),
        'answer' => $pageSettings->get("home_faq_{$index}_answer", $defaults['answer']),
    ];
}

$homeBlogEyebrow = $pageSettings->get('home_blog_eyebrow', 'News & blog');
$homeBlogTitle = $pageSettings->get('home_blog_title', 'Articles & blog posts');
$homeBlogBody = $pageSettings->get('home_blog_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects.');
$homeBlogEmptyText = $pageSettings->get('home_blog_empty_text', 'No blog posts yet.');

$homeContactPhoneTitle = $pageSettings->get('home_contact_phone_title', 'Call Our Head Office');
$homeContactPhoneValue = trim((string) $pageSettings->get('home_contact_phone_value', '+234 701 234 5678'));
$homeContactPhoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $homeContactPhoneValue);
$homeContactPhoneNote = $pageSettings->get('home_contact_phone_note', 'Available Mon-Fri, 8am-6pm');
$homeContactEmailTitle = $pageSettings->get('home_contact_email_title', 'Write To Us');
$homeContactEmailValue = trim((string) $pageSettings->get('home_contact_email_value', 'info@tpvconstruction.com.ng'));
$homeContactEmailNote = $pageSettings->get('home_contact_email_note', 'We reply within 24 hours');
$homeContactImage = tpv_asset_url($pageSettings->get('home_contact_image', 'wp-content/uploads/2024/06/contact-info-img.png'));
$homeContactEyebrow = $pageSettings->get('home_contact_eyebrow', 'Contact us');
$homeContactTitle = $pageSettings->get('home_contact_title', 'Get in touch with us');
$homeContactPlaceholderName = $pageSettings->get('home_contact_placeholder_name', 'Your Full Name *');
$homeContactPlaceholderEmail = $pageSettings->get('home_contact_placeholder_email', 'Email Address *');
$homeContactPlaceholderPhone = $pageSettings->get('home_contact_placeholder_phone', 'Phone Number *');
$homeContactPlaceholderSubject = $pageSettings->get('home_contact_placeholder_subject', 'Subject *');
$homeContactPlaceholderMessage = $pageSettings->get('home_contact_placeholder_message', 'Tell us about your project...');
$homeContactSubmitText = $pageSettings->get('home_contact_submit_text', 'Send Message');

$latestPosts = $blog->getPublishedPosts([], 3, 0);
$homeProjects = array_slice($projectModel->getAll(null, 4, 0), 0, 4);

$contactSent = isset($_GET['contact_sent']);
$contactErrors = $_SESSION['contact_errors'] ?? [];
$contactOld = $_SESSION['contact_old'] ?? [];
unset($_SESSION['contact_errors'], $_SESSION['contact_old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPV Construction and Services LTD</title>
    <meta name="description" content="<?php echo htmlspecialchars(homepage_excerpt((string) $homeHeroBody, 160)); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --home-navy: #14233f;
            --home-navy-soft: #264166;
            --home-copy: #607089;
            --home-line: #dfe7f1;
            --home-surface: #f5f8fc;
            --home-card: rgba(255, 255, 255, 0.94);
            --home-accent: #d4a13e;
            --home-danger: #ef4444;
            --home-shadow: 0 30px 64px rgba(17, 34, 60, 0.09);
            --home-radius: 30px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: var(--home-navy);
            background:
                radial-gradient(circle at top right, rgba(212, 161, 62, 0.12), transparent 20%),
                linear-gradient(180deg, #f8fbff 0%, #ffffff 38%, #f7f9fc 100%);
        }

        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }

        .home-shell {
            width: min(1220px, calc(100% - 32px));
            margin: 0 auto;
        }

        .home-page {
            padding: 24px 0 72px;
        }

        .home-section {
            margin-top: 28px;
        }

        .home-card {
            border: 1px solid var(--home-line);
            background: var(--home-card);
            box-shadow: var(--home-shadow);
            border-radius: var(--home-radius);
        }

        .home-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            width: fit-content;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            background: rgba(20, 35, 63, 0.06);
            color: var(--home-navy);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .home-pill::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--home-accent);
        }

        .home-section-head {
            display: grid;
            gap: 0.8rem;
            max-width: 760px;
            margin-bottom: 20px;
        }

        .home-section-head h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 3.45rem);
            line-height: 1.02;
            letter-spacing: -0.05em;
        }

        .home-section-head p {
            margin: 0;
            color: var(--home-copy);
            font-size: 1rem;
            line-height: 1.85;
        }

        .home-button,
        .home-button--ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            min-height: 52px;
            padding: 0.95rem 1.3rem;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .home-button {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            box-shadow: 0 18px 34px -20px rgba(220, 38, 38, 0.9);
        }

        .home-button:hover,
        .home-button--ghost:hover {
            transform: translateY(-1px);
        }

        .home-button--ghost {
            background: #fff;
            color: var(--home-navy);
            border: 1px solid rgba(20, 35, 63, 0.14);
        }

        .home-button--accent {
            background: linear-gradient(135deg, #e7b548 0%, #d4a13e 100%);
            color: #16233f;
            box-shadow: 0 16px 30px -20px rgba(212, 161, 62, 0.85);
        }

        .home-hero {
            position: relative;
            overflow: hidden;
            min-height: 620px;
            display: grid;
            align-items: end;
            background:
                linear-gradient(115deg, rgba(10, 22, 43, 0.82) 0%, rgba(10, 22, 43, 0.58) 36%, rgba(10, 22, 43, 0.16) 100%),
                url('<?php echo htmlspecialchars($homeHeroBgImage, ENT_QUOTES, 'UTF-8'); ?>') center/cover no-repeat;
        }

        .home-hero__content {
            padding: 58px;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.55fr);
            gap: 28px;
            align-items: end;
        }

        .home-hero__copy {
            color: #fff;
            display: grid;
            gap: 1rem;
            max-width: 740px;
        }

        .home-hero__eyebrow {
            display: inline-flex;
            width: fit-content;
            padding: 0.58rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #f8fafc;
            font-size: 0.76rem;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            font-weight: 800;
        }

        .home-hero__copy h1 {
            margin: 0;
            font-size: clamp(2.85rem, 6vw, 6rem);
            line-height: 0.94;
            letter-spacing: -0.07em;
            max-width: 10.6ch;
        }

        .home-hero__copy p {
            margin: 0;
            font-size: 1.03rem;
            line-height: 1.95;
            color: rgba(255, 255, 255, 0.84);
            max-width: 58ch;
        }

        .home-hero__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            margin-top: 0.3rem;
        }

        .home-hero__support {
            padding: 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 28px;
            backdrop-filter: blur(16px);
            color: #fff;
            display: grid;
            gap: 0.85rem;
        }

        .home-hero__support-label {
            font-size: 0.76rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.68);
            font-weight: 800;
        }

        .home-hero__support-number {
            font-size: clamp(1.4rem, 2.3vw, 2rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
            font-weight: 800;
        }

        .home-intro {
            display: grid;
            grid-template-columns: minmax(280px, 0.95fr) minmax(0, 1.05fr);
            gap: 1.2rem;
        }

        .home-intro__media {
            overflow: hidden;
            min-height: 520px;
        }

        .home-intro__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-intro__content {
            padding: 30px;
            display: grid;
            gap: 1rem;
        }

        .home-intro__content h2 {
            margin: 0;
            font-size: clamp(2rem, 3.2vw, 3.5rem);
            line-height: 1;
            letter-spacing: -0.06em;
        }

        .home-intro__content p {
            margin: 0;
            color: var(--home-copy);
            line-height: 1.9;
        }

        .home-intro__features {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .home-mini-card {
            padding: 16px 18px;
            border-radius: 22px;
            background: #f6f9fd;
            border: 1px solid rgba(20, 35, 63, 0.08);
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .home-intro__footer {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: center;
            margin-top: 0.25rem;
        }

        .home-support-badge {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.95rem 1.1rem;
            border-radius: 22px;
            background: #fff6e4;
            border: 1px solid rgba(212, 161, 62, 0.22);
            color: #8b5f07;
            font-size: 0.88rem;
            font-weight: 700;
            min-width: 260px;
        }

        .home-support-badge strong {
            display: block;
            max-width: 12ch;
            line-height: 1.35;
            text-transform: lowercase;
        }

        .home-support-badge a {
            font-size: 1.05rem;
            line-height: 1.25;
            letter-spacing: -0.03em;
            color: #7c5804;
            text-align: right;
            word-break: break-word;
        }

        .home-feature-layout {
            display: grid;
            gap: 1.15rem;
        }

        .home-feature-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 1fr) minmax(0, 1fr);
            gap: 1.15rem;
            align-items: stretch;
        }

        .home-feature-stack {
            display: grid;
            gap: 1.15rem;
        }

        .home-feature-card {
            padding: 32px;
            display: grid;
            gap: 1rem;
            min-height: 100%;
        }

        .home-feature-card__icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(239, 68, 68, 0.12);
            background: rgba(239, 68, 68, 0.05);
        }

        .home-feature-card__icon svg {
            width: 32px;
            height: 32px;
            stroke: var(--home-danger);
        }

        .home-feature-card h3 {
            margin: 0;
            font-size: clamp(1.7rem, 2vw, 2.45rem);
            line-height: 1.02;
            letter-spacing: -0.05em;
        }

        .home-feature-card p {
            margin: 0;
            color: var(--home-copy);
            line-height: 1.85;
        }

        .home-feature-counter {
            padding-top: 18px;
            border-top: 1px solid var(--home-line);
            display: grid;
            gap: 0.15rem;
            margin-top: auto;
        }

        .home-feature-counter strong {
            font-size: clamp(3rem, 4vw, 4.4rem);
            line-height: 0.95;
            letter-spacing: -0.08em;
        }

        .home-feature-counter span {
            color: var(--home-navy-soft);
            font-size: 1rem;
        }

        .home-feature-image {
            overflow: hidden;
            min-height: 390px;
        }

        .home-feature-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-services__grid,
        .home-projects__grid,
        .home-testimonials__grid,
        .home-blog__grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
        }

        .home-service-card,
        .home-project-card,
        .home-testimonial-card,
        .home-blog-card {
            overflow: hidden;
        }

        .home-service-card {
            grid-column: span 6;
            display: grid;
            grid-template-columns: minmax(150px, 180px) minmax(0, 1fr);
            gap: 0;
        }

        .home-service-card__media {
            min-height: 240px;
            background: linear-gradient(135deg, #d8e5f4, #c1cfde);
        }

        .home-service-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-service-card__content {
            padding: 24px;
            display: grid;
            gap: 0.9rem;
        }

        .home-service-card__content h3,
        .home-project-card h3,
        .home-blog-card h3,
        .home-testimonial-card h3 {
            margin: 0;
            font-size: 1.5rem;
            line-height: 1.08;
            letter-spacing: -0.05em;
        }

        .home-service-card__content p,
        .home-project-card p,
        .home-blog-card p,
        .home-testimonial-card p {
            margin: 0;
            color: var(--home-copy);
            line-height: 1.8;
        }

        .home-project-card,
        .home-blog-card {
            grid-column: span 4;
            display: grid;
            gap: 0;
        }

        .home-project-card__media,
        .home-blog-card__media {
            height: 260px;
            background: linear-gradient(135deg, #d9e4f3, #bccadc);
        }

        .home-project-card__media img,
        .home-blog-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-project-card__content,
        .home-blog-card__content {
            padding: 24px;
            display: grid;
            gap: 0.85rem;
        }

        .home-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            color: #72829e;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .home-inline-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--home-danger);
            font-weight: 800;
        }

        .home-inline-link::after {
            content: "›";
            font-size: 1.05rem;
        }

        .home-cta {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(260px, 420px);
            gap: 0;
            overflow: hidden;
        }

        .home-cta__content {
            padding: 34px;
            display: grid;
            gap: 1rem;
            background:
                radial-gradient(circle at top right, rgba(212, 161, 62, 0.14), transparent 30%),
                linear-gradient(135deg, #ffffff 0%, #f7fbff 100%);
        }

        .home-cta__content h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 3.3rem);
            line-height: 1;
            letter-spacing: -0.05em;
        }

        .home-cta__content p {
            margin: 0;
            color: var(--home-copy);
            line-height: 1.9;
        }

        .home-cta__media {
            min-height: 100%;
            background: linear-gradient(135deg, #dce8f6 0%, #c9d6e7 100%);
        }

        .home-cta__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-testimonial-card {
            grid-column: span 3;
            padding: 24px;
            display: grid;
            gap: 1rem;
        }

        .home-testimonial-card__person {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .home-testimonial-card__person img {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            object-fit: cover;
        }

        .home-testimonial-card__person strong,
        .home-testimonial-card__person span {
            display: block;
        }

        .home-testimonial-card__person span {
            margin-top: 0.2rem;
            color: #7f8ca6;
            font-size: 0.88rem;
        }

        .home-faq {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(280px, 0.95fr);
            gap: 1rem;
        }

        .home-faq__list {
            padding: 28px;
            display: grid;
            gap: 0.85rem;
        }

        .home-faq__item {
            border: 1px solid var(--home-line);
            border-radius: 22px;
            background: #fff;
            overflow: hidden;
        }

        .home-faq__item summary {
            list-style: none;
            cursor: pointer;
            padding: 18px 22px;
            font-weight: 700;
            color: var(--home-navy);
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .home-faq__item summary::-webkit-details-marker { display: none; }

        .home-faq__item summary::after {
            content: "+";
            color: var(--home-danger);
            font-size: 1.15rem;
            line-height: 1;
        }

        .home-faq__item[open] summary::after { content: "–"; }

        .home-faq__answer {
            padding: 0 22px 20px;
            color: var(--home-copy);
            line-height: 1.8;
        }

        .home-faq__media {
            overflow: hidden;
            min-height: 100%;
        }

        .home-faq__placeholder {
            width: 100%;
            height: 100%;
            min-height: 420px;
            display: grid;
            align-content: end;
            gap: 0.9rem;
            padding: 30px;
            background:
                linear-gradient(180deg, rgba(20, 35, 63, 0.05), rgba(20, 35, 63, 0.12)),
                radial-gradient(circle at top right, rgba(212, 161, 62, 0.18), transparent 26%),
                linear-gradient(135deg, #edf3fb 0%, #dbe5f2 100%);
        }

        .home-faq__placeholder strong {
            font-size: clamp(1.5rem, 2vw, 2.1rem);
            line-height: 1.05;
            letter-spacing: -0.05em;
        }

        .home-faq__placeholder p {
            margin: 0;
            color: var(--home-copy);
            line-height: 1.8;
            max-width: 32ch;
        }

        .home-faq__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-blog__empty {
            padding: 28px;
            color: var(--home-copy);
        }

        .home-contact {
            display: grid;
            grid-template-columns: minmax(280px, 0.92fr) minmax(0, 1.08fr);
            gap: 1rem;
            align-items: start;
        }

        .home-contact__info,
        .home-contact__form {
            padding: 28px;
        }

        .home-contact__info {
            display: grid;
            gap: 1rem;
        }

        .home-contact__visual {
            overflow: hidden;
            border-radius: 24px;
            min-height: 240px;
            background: linear-gradient(135deg, #d7e2f3 0%, #bccadc 100%);
        }

        .home-contact__visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-contact__cards {
            display: grid;
            gap: 0.85rem;
        }

        .home-contact__info-card {
            padding: 18px 20px;
            border-radius: 22px;
            background: #f7fbff;
            border: 1px solid var(--home-line);
            display: grid;
            gap: 0.25rem;
        }

        .home-contact__info-card strong {
            font-size: 1rem;
        }

        .home-contact__info-card a {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--home-navy);
        }

        .home-contact__info-card span {
            color: var(--home-copy);
            font-size: 0.9rem;
        }

        .home-contact__form-head {
            display: grid;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .home-contact__form-head h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 3.25rem);
            line-height: 1;
            letter-spacing: -0.05em;
        }

        .home-alert {
            margin-bottom: 1rem;
            padding: 16px 18px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .home-alert--success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.18);
            color: #047857;
        }

        .home-alert--error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.14);
            color: #b91c1c;
        }

        .home-contact__fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .home-field,
        .home-contact__fields textarea {
            width: 100%;
            border: 1px solid var(--home-line);
            border-radius: 18px;
            background: #fff;
            color: var(--home-navy);
            font: inherit;
            padding: 0.98rem 1rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .home-contact__fields textarea {
            min-height: 180px;
            resize: vertical;
            grid-column: 1 / -1;
        }

        .home-field:focus,
        .home-contact__fields textarea:focus {
            border-color: rgba(212, 161, 62, 0.7);
            box-shadow: 0 0 0 4px rgba(212, 161, 62, 0.12);
        }

        .home-contact__fields .home-field--full {
            grid-column: 1 / -1;
        }

        @media (max-width: 1120px) {
            .home-hero__content,
            .home-intro,
            .home-feature-grid,
            .home-cta,
            .home-faq,
            .home-contact {
                grid-template-columns: 1fr;
            }

            .home-services__grid .home-service-card,
            .home-projects__grid .home-project-card,
            .home-testimonials__grid .home-testimonial-card,
            .home-blog__grid .home-blog-card {
                grid-column: span 6;
            }
        }

        @media (max-width: 820px) {
            .home-page {
                padding: 16px 0 60px;
            }

            .home-hero {
                min-height: 0;
            }

            .home-hero__content,
            .home-intro__content,
            .home-feature-card,
            .home-cta__content,
            .home-faq__list,
            .home-contact__info,
            .home-contact__form {
                padding: 22px;
            }

            .home-intro__features,
            .home-contact__fields {
                grid-template-columns: 1fr;
            }

            .home-intro__footer {
                flex-direction: column;
                align-items: stretch;
            }

            .home-intro__footer .home-button--accent,
            .home-intro__footer .home-support-badge {
                width: 100%;
            }

            .home-service-card,
            .home-project-card,
            .home-testimonial-card,
            .home-blog-card {
                grid-column: 1 / -1 !important;
            }

            .home-service-card {
                grid-template-columns: 1fr;
            }

            .home-service-card__media {
                min-height: 220px;
            }
        }

        @media (max-width: 640px) {
            .home-shell {
                width: min(100%, calc(100% - 20px));
            }

            .home-section {
                margin-top: 22px;
            }

            .home-card {
                border-radius: 24px;
            }

            .home-hero__content,
            .home-intro__content,
            .home-feature-card,
            .home-cta__content,
            .home-faq__list,
            .home-contact__info,
            .home-contact__form {
                padding: 18px;
            }

            .home-hero__copy h1,
            .home-intro__content h2,
            .home-section-head h2,
            .home-cta__content h2,
            .home-contact__form-head h2 {
                letter-spacing: -0.06em;
            }

            .home-support-badge {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.35rem;
                border-radius: 18px;
                padding: 0.95rem 1rem;
                min-width: 0;
                justify-items: center;
                text-align: center;
            }

            .home-support-badge strong,
            .home-support-badge a {
                max-width: none;
                text-align: center;
            }

            .home-support-badge a {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/quote_header.php'; ?>

<main class="home-page">
    <div class="home-shell">
        <section class="home-section">
            <div class="home-hero home-card">
                <div class="home-hero__content">
                    <div class="home-hero__copy">
                        <span class="home-hero__eyebrow"><?php echo htmlspecialchars($homeHeroEyebrow); ?></span>
                        <h1><?php echo nl2br(htmlspecialchars($homeHeroTitle)); ?></h1>
                        <p><?php echo nl2br(htmlspecialchars($homeHeroBody)); ?></p>
                        <div class="home-hero__actions">
                            <a class="home-button" href="<?php echo htmlspecialchars(homepage_link((string) $homeHeroPrimaryLink, 'contact-us/')); ?>">
                                <?php echo htmlspecialchars($homeHeroPrimaryText); ?>
                            </a>
                            <a class="home-button home-button--ghost" href="<?php echo htmlspecialchars(homepage_link((string) $homeHeroSecondaryLink, 'projects/')); ?>">
                                <?php echo htmlspecialchars($homeHeroSecondaryText); ?>
                            </a>
                        </div>
                    </div>
                    <div class="home-hero__support">
                        <span class="home-hero__support-label"><?php echo htmlspecialchars($homeIntroSupportTitle); ?></span>
                        <a class="home-hero__support-number" href="<?php echo htmlspecialchars($supportPhoneHref); ?>"><?php echo htmlspecialchars($supportPhone); ?></a>
                        <span style="color:rgba(255,255,255,0.74);font-size:0.96rem;line-height:1.7;">Speak with our team about project planning, timelines, and your next construction brief.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-section" id="home-intro">
            <div class="home-intro">
                <div class="home-intro__media home-card">
                    <img src="<?php echo htmlspecialchars($homeIntroImage); ?>" alt="<?php echo htmlspecialchars($homeIntroTitle); ?>">
                </div>
                <div class="home-intro__content home-card">
                    <span class="home-pill"><?php echo htmlspecialchars($homeIntroEyebrow); ?></span>
                    <h2><?php echo htmlspecialchars($homeIntroTitle); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($homeIntroBody)); ?></p>
                    <div class="home-intro__features">
                        <div class="home-mini-card"><?php echo htmlspecialchars($homeIntroFeature1); ?></div>
                        <div class="home-mini-card"><?php echo htmlspecialchars($homeIntroFeature2); ?></div>
                        <div class="home-mini-card"><?php echo htmlspecialchars($homeIntroFeature3); ?></div>
                    </div>
                    <div class="home-intro__footer">
                        <a class="home-button home-button--accent" href="<?php echo htmlspecialchars(homepage_link((string) $homeIntroButtonLink, 'quote/')); ?>">
                            <?php echo htmlspecialchars($homeIntroButtonText); ?>
                        </a>
                        <div class="home-support-badge">
                            <strong><?php echo htmlspecialchars($homeIntroSupportTitle); ?></strong>
                            <a href="<?php echo htmlspecialchars($supportPhoneHref); ?>"><?php echo htmlspecialchars($supportPhone); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-section" id="home-feature-strip">
            <div class="home-feature-layout">
                <div class="home-section-head">
                    <span class="home-pill"><?php echo htmlspecialchars($homeWhyEyebrow); ?></span>
                    <h2><?php echo htmlspecialchars($homeWhyTitle); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($homeWhyBody)); ?></p>
                </div>
                <div class="home-feature-grid">
                    <article class="home-feature-card home-card">
                        <div class="home-feature-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.8c.5.4 1 1 1.1 1.7h5.8c.1-.7.6-1.3 1.1-1.7A7 7 0 0 0 12 2Z"/><path d="M8.5 8.5h7"/></svg>
                        </div>
                        <h3><?php echo htmlspecialchars($homeWhy1Title); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($homeWhy1Body)); ?></p>
                        <div class="home-feature-counter">
                            <strong><?php echo htmlspecialchars($homeWhy1CounterValue . $homeWhy1CounterSuffix); ?></strong>
                            <span><?php echo htmlspecialchars($homeWhy1CounterTitle); ?></span>
                        </div>
                    </article>

                    <div class="home-feature-stack">
                        <div class="home-feature-image home-card">
                            <img src="<?php echo htmlspecialchars($homeWhy1Image); ?>" alt="<?php echo htmlspecialchars($homeWhy1Title); ?>">
                        </div>
                        <article class="home-feature-card home-card">
                            <div class="home-feature-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="m4 13 4 4L20 5"/><path d="M12 21h9"/><path d="M3 21h3"/></svg>
                            </div>
                            <h3><?php echo htmlspecialchars($homeWhy3Title); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($homeWhy3Body)); ?></p>
                            <div class="home-feature-counter">
                                <strong><?php echo htmlspecialchars($homeWhy3CounterValue . $homeWhy3CounterSuffix); ?></strong>
                                <span><?php echo htmlspecialchars($homeWhy3CounterTitle); ?></span>
                            </div>
                        </article>
                    </div>

                    <article class="home-feature-card home-card">
                        <div class="home-feature-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M12 3 4 7v6c0 5 3.4 7.7 8 8 4.6-.3 8-3 8-8V7l-8-4Z"/><path d="m9.5 12 1.8 1.8 3.4-3.6"/></svg>
                        </div>
                        <h3><?php echo htmlspecialchars($homeWhy2Title); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($homeWhy2Body)); ?></p>
                        <div class="home-feature-counter">
                            <strong><?php echo htmlspecialchars($homeWhy2CounterValue . $homeWhy2CounterSuffix); ?></strong>
                            <span><?php echo htmlspecialchars($homeWhy2CounterTitle); ?></span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-section" id="home-services">
            <div class="home-section-head">
                <span class="home-pill"><?php echo htmlspecialchars($homeServicesEyebrow); ?></span>
                <h2><?php echo htmlspecialchars($homeServicesTitle); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($homeServicesBody)); ?></p>
            </div>
            <div class="home-services__grid">
                <?php foreach ($homeServicesCards as $card): ?>
                    <article class="home-service-card home-card">
                        <div class="home-service-card__media">
                            <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>">
                        </div>
                        <div class="home-service-card__content">
                            <h3><?php echo htmlspecialchars($card['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($card['body'])); ?></p>
                            <div>
                                <a class="home-inline-link" href="<?php echo htmlspecialchars(homepage_link((string) $card['link'], 'services/')); ?>">
                                    <?php echo htmlspecialchars($card['button_text']); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div style="margin-top:18px;">
                <a class="home-button home-button--accent" href="<?php echo htmlspecialchars(homepage_link((string) $homeServicesButtonLink, 'services/')); ?>">
                    <?php echo htmlspecialchars($homeServicesButtonText); ?>
                </a>
            </div>
        </section>

        <section class="home-section" id="home-projects">
            <div class="home-section-head">
                <span class="home-pill"><?php echo htmlspecialchars($homeProjectsEyebrow); ?></span>
                <h2><?php echo htmlspecialchars($homeProjectsTitle); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($homeProjectsBody)); ?></p>
            </div>
            <div class="home-projects__grid">
                <?php if (empty($homeProjects)): ?>
                    <div class="home-blog__empty home-card" style="grid-column:1 / -1;">
                        No projects available yet.
                    </div>
                <?php else: ?>
                    <?php foreach ($homeProjects as $project): ?>
                        <?php
                        $projectSlug = trim((string) ($project['slug'] ?? ''));
                        $projectUrl = $projectSlug !== '' ? homepage_url('projects/' . rawurlencode($projectSlug) . '/') : homepage_link((string) $homeProjectsButtonLink, 'projects/');
                        $projectImage = homepage_project_image($db, (int) ($project['id'] ?? 0));
                        $projectExcerpt = (string) ($project['short_description'] ?? $project['description'] ?? '');
                        $projectLocation = trim(implode(', ', array_filter([(string) ($project['city'] ?? ''), (string) ($project['state'] ?? '')])));
                        ?>
                        <article class="home-project-card home-card">
                            <div class="home-project-card__media">
                                <?php if ($projectImage !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($projectImage); ?>" alt="<?php echo htmlspecialchars((string) ($project['name'] ?? 'Project')); ?>">
                                <?php else: ?>
                                    <div style="width:100%;height:100%;display:grid;place-items:center;font-weight:800;color:#17315a;background:linear-gradient(135deg,#dbe7f4 0%,#c5d1df 100%);">Project Spotlight</div>
                                <?php endif; ?>
                            </div>
                            <div class="home-project-card__content">
                                <div class="home-meta">
                                    <?php if ($projectLocation !== ''): ?><span><?php echo htmlspecialchars($projectLocation); ?></span><?php endif; ?>
                                    <span><?php echo htmlspecialchars(ucfirst((string) ($project['status'] ?? 'planning'))); ?></span>
                                </div>
                                <h3><?php echo htmlspecialchars((string) ($project['name'] ?? 'Project')); ?></h3>
                                <p><?php echo htmlspecialchars(homepage_excerpt($projectExcerpt, 135)); ?></p>
                                <div>
                                    <a class="home-inline-link" href="<?php echo htmlspecialchars($projectUrl); ?>">View project</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div style="margin-top:18px;">
                <a class="home-button" href="<?php echo htmlspecialchars(homepage_link((string) $homeProjectsButtonLink, 'projects/')); ?>">
                    <?php echo htmlspecialchars($homeProjectsButtonText); ?>
                </a>
            </div>
        </section>

        <section class="home-section" id="home-cta">
            <div class="home-cta home-card">
                <div class="home-cta__content">
                    <span class="home-pill">Project-ready delivery</span>
                    <h2><?php echo htmlspecialchars($homeCtaTitle); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($homeCtaBody)); ?></p>
                    <div>
                        <a class="home-button" href="<?php echo htmlspecialchars(homepage_link((string) $homeCtaButtonLink, 'quote/')); ?>">
                            <?php echo htmlspecialchars($homeCtaButtonText); ?>
                        </a>
                    </div>
                </div>
                <div class="home-cta__media">
                    <img src="<?php echo htmlspecialchars($homeCtaImage); ?>" alt="<?php echo htmlspecialchars($homeCtaTitle); ?>">
                </div>
            </div>
        </section>

        <section class="home-section" id="home-testimonials">
            <div class="home-section-head">
                <span class="home-pill"><?php echo htmlspecialchars($homeTestimonialsEyebrow); ?></span>
                <h2><?php echo htmlspecialchars($homeTestimonialsTitle); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($homeTestimonialsBody)); ?></p>
            </div>
            <div class="home-testimonials__grid">
                <?php foreach ($homeTestimonials as $item): ?>
                    <article class="home-testimonial-card home-card">
                        <div class="home-testimonial-card__person">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <span><?php echo htmlspecialchars($item['role']); ?></span>
                            </div>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($item['body'])); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="home-section" id="home-faq">
            <div class="home-section-head">
                <span class="home-pill"><?php echo htmlspecialchars($homeFaqEyebrow); ?></span>
                <h2><?php echo htmlspecialchars($homeFaqTitle); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($homeFaqBody)); ?></p>
            </div>
            <div class="home-faq">
                <div class="home-faq__list home-card">
                    <?php foreach ($homeFaqs as $index => $item): ?>
                        <details class="home-faq__item" <?php echo $index === 1 ? 'open' : ''; ?>>
                            <summary><?php echo htmlspecialchars($item['question']); ?></summary>
                            <div class="home-faq__answer"><?php echo nl2br(htmlspecialchars($item['answer'])); ?></div>
                        </details>
                    <?php endforeach; ?>
                </div>
                <div class="home-faq__media home-card">
                    <?php if ($homeFaqImageExists): ?>
                        <img src="<?php echo htmlspecialchars($homeFaqImage); ?>" alt="<?php echo htmlspecialchars($homeFaqTitle); ?>">
                    <?php else: ?>
                        <div class="home-faq__placeholder">
                            <strong><?php echo htmlspecialchars($homeFaqTitle); ?></strong>
                            <p>Upload the FAQ side image from Admin Dashboard > Homepage Settings > FAQ Section to replace this placeholder.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="home-section" id="home-blog-preview">
            <div class="home-section-head">
                <span class="home-pill"><?php echo htmlspecialchars($homeBlogEyebrow); ?></span>
                <h2><?php echo htmlspecialchars($homeBlogTitle); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($homeBlogBody)); ?></p>
            </div>
            <?php if (empty($latestPosts)): ?>
                <div class="home-blog__empty home-card"><?php echo htmlspecialchars($homeBlogEmptyText); ?></div>
            <?php else: ?>
                <div class="home-blog__grid">
                    <?php foreach ($latestPosts as $post): ?>
                        <?php
                        $postTitle = (string) ($post['title'] ?? 'Untitled article');
                        $postSlug = trim((string) ($post['slug'] ?? ''));
                        $postImage = homepage_post_image($post);
                        $postUrl = $postSlug !== '' ? homepage_url('post.php?slug=' . rawurlencode($postSlug)) : homepage_url('blog/');
                        $postExcerpt = trim((string) ($post['excerpt'] ?? ''));
                        if ($postExcerpt === '') {
                            $postExcerpt = homepage_excerpt((string) ($post['content'] ?? ''), 145);
                        }
                        ?>
                        <article class="home-blog-card home-card">
                            <div class="home-blog-card__media">
                                <?php if ($postImage !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($postImage); ?>" alt="<?php echo htmlspecialchars($postTitle); ?>">
                                <?php else: ?>
                                    <div style="width:100%;height:100%;display:grid;place-items:center;font-weight:800;color:#17315a;background:linear-gradient(135deg,#dbe7f4 0%,#c5d1df 100%);">TPV Journal</div>
                                <?php endif; ?>
                            </div>
                            <div class="home-blog-card__content">
                                <div class="home-meta">
                                    <span><?php echo htmlspecialchars(date('M j, Y', strtotime((string) ($post['published_at'] ?? 'now')))); ?></span>
                                    <span><?php echo htmlspecialchars(homepage_post_author($post)); ?></span>
                                    <span><?php echo htmlspecialchars(homepage_read_time($post)); ?></span>
                                </div>
                                <h3><?php echo htmlspecialchars($postTitle); ?></h3>
                                <p><?php echo htmlspecialchars($postExcerpt); ?></p>
                                <div>
                                    <a class="home-inline-link" href="<?php echo htmlspecialchars($postUrl); ?>">Read article</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="home-section" id="home-contact">
            <div class="home-contact">
                <div class="home-contact__info home-card">
                    <div class="home-contact__visual">
                        <img src="<?php echo htmlspecialchars($homeContactImage); ?>" alt="<?php echo htmlspecialchars($homeContactTitle); ?>">
                    </div>
                    <div class="home-contact__cards">
                        <div class="home-contact__info-card">
                            <strong><?php echo htmlspecialchars($homeContactPhoneTitle); ?></strong>
                            <a href="<?php echo htmlspecialchars($homeContactPhoneHref); ?>"><?php echo htmlspecialchars($homeContactPhoneValue); ?></a>
                            <span><?php echo htmlspecialchars($homeContactPhoneNote); ?></span>
                        </div>
                        <div class="home-contact__info-card">
                            <strong><?php echo htmlspecialchars($homeContactEmailTitle); ?></strong>
                            <a href="mailto:<?php echo htmlspecialchars($homeContactEmailValue); ?>"><?php echo htmlspecialchars($homeContactEmailValue); ?></a>
                            <span><?php echo htmlspecialchars($homeContactEmailNote); ?></span>
                        </div>
                    </div>
                </div>

                <div class="home-contact__form home-card">
                    <div class="home-contact__form-head">
                        <span class="home-pill"><?php echo htmlspecialchars($homeContactEyebrow); ?></span>
                        <h2><?php echo htmlspecialchars($homeContactTitle); ?></h2>
                    </div>

                    <?php if ($contactSent): ?>
                        <div class="home-alert home-alert--success">
                            Your message has been sent successfully. We will get back to you within 24 hours.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($contactErrors)): ?>
                        <div class="home-alert home-alert--error">
                            <?php echo htmlspecialchars(implode(' ', $contactErrors)); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars(homepage_url('contact_process.php')); ?>" method="post">
                        <input type="hidden" name="redirect_success" value="index.php?contact_sent=1#home-contact">
                        <input type="hidden" name="redirect_error" value="index.php#home-contact">
                        <div class="home-contact__fields">
                            <input class="home-field" type="text" name="name" placeholder="<?php echo htmlspecialchars($homeContactPlaceholderName); ?>" value="<?php echo htmlspecialchars((string) ($contactOld['name'] ?? '')); ?>" required>
                            <input class="home-field" type="email" name="email" placeholder="<?php echo htmlspecialchars($homeContactPlaceholderEmail); ?>" value="<?php echo htmlspecialchars((string) ($contactOld['email'] ?? '')); ?>" required>
                            <input class="home-field" type="text" name="phone" placeholder="<?php echo htmlspecialchars($homeContactPlaceholderPhone); ?>" value="<?php echo htmlspecialchars((string) ($contactOld['phone'] ?? '')); ?>" required>
                            <input class="home-field" type="text" name="subject" placeholder="<?php echo htmlspecialchars($homeContactPlaceholderSubject); ?>" value="<?php echo htmlspecialchars((string) ($contactOld['subject'] ?? '')); ?>" required>
                            <textarea name="message" placeholder="<?php echo htmlspecialchars($homeContactPlaceholderMessage); ?>" required><?php echo htmlspecialchars((string) ($contactOld['message'] ?? '')); ?></textarea>
                        </div>
                        <div style="margin-top:16px;">
                            <button class="home-button" type="submit" name="contact_submit" value="1">
                                <?php echo htmlspecialchars($homeContactSubmitText); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
