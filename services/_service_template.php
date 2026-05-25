<?php
if (!isset($serviceConfig) || !is_array($serviceConfig)) {
    throw new RuntimeException('Service page configuration is required.');
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/classes/Settings.php';
require_once dirname(__DIR__) . '/classes/ServiceContent.php';

$settings = new Settings();
$serviceSlug = (string) ($serviceConfig['slug'] ?? '');
$serviceDefaultTitle = (string) ($serviceConfig['default_title'] ?? 'Service');

if ($serviceSlug === '') {
    throw new RuntimeException('Service slug is required.');
}

$serviceContentManager = new ServiceContent();
$serviceRecord = $serviceContentManager->getResolvedBySlug($serviceSlug);
if (!$serviceRecord) {
    throw new RuntimeException('Service content could not be loaded.');
}

$serviceTitle = trim((string) ($serviceRecord['page_title'] ?? $serviceDefaultTitle)) ?: $serviceDefaultTitle;
$serviceName = trim((string) ($serviceRecord['name'] ?? $serviceDefaultTitle)) ?: $serviceDefaultTitle;
$serviceMetaDescription = trim((string) ($serviceRecord['seo_description'] ?? ''));
$serviceHeroEyebrow = trim((string) ($serviceRecord['hero_eyebrow'] ?? 'Service Expertise')) ?: 'Service Expertise';
$serviceHeroSecondaryButtonText = trim((string) ($serviceRecord['hero_secondary_button_text'] ?? 'View Projects')) ?: 'View Projects';
$serviceHeroSecondaryButtonLink = trim((string) ($serviceRecord['hero_secondary_button_link'] ?? 'projects/'));
$serviceHeroEmptyNote = trim((string) ($serviceRecord['hero_empty_note'] ?? 'Upload a lead service image from the Service Manager to feature it here.')) ?: 'Upload a lead service image from the Service Manager to feature it here.';
$serviceOverviewEyebrow = trim((string) ($serviceRecord['overview_eyebrow'] ?? 'Overview')) ?: 'Overview';
$serviceOverviewTitle = trim((string) ($serviceRecord['overview_title'] ?? $serviceName)) ?: $serviceName;
$serviceOverviewBody = trim((string) ($serviceRecord['overview_body'] ?? ''));
$serviceHighlight2Title = trim((string) ($serviceRecord['highlight_2_title'] ?? ''));
$serviceHighlight2Body = trim((string) ($serviceRecord['highlight_2_body'] ?? ''));
$serviceHighlight3Title = trim((string) ($serviceRecord['highlight_3_title'] ?? ''));
$serviceHighlight3Body = trim((string) ($serviceRecord['highlight_3_body'] ?? ''));
$serviceContentBody = trim((string) ($serviceRecord['content_body'] ?? ''));
$serviceBenefitsEyebrow = trim((string) ($serviceRecord['benefits_eyebrow'] ?? 'Key Benefits')) ?: 'Key Benefits';
$serviceBenefitsTitle = trim((string) ($serviceRecord['benefits_title'] ?? ($serviceName . ' at a glance'))) ?: ($serviceName . ' at a glance');
$serviceSustainableTitle = trim((string) ($serviceRecord['sustainable_title'] ?? ''));
$serviceSustainableBody1 = trim((string) ($serviceRecord['sustainable_body_1'] ?? ''));
$serviceSustainableBody2 = trim((string) ($serviceRecord['sustainable_body_2'] ?? ''));
$serviceProcessEyebrow = trim((string) ($serviceRecord['process_eyebrow'] ?? ''));
$serviceProcessTitle = trim((string) ($serviceRecord['process_title'] ?? ''));
$serviceProcessBody = trim((string) ($serviceRecord['process_body'] ?? ''));
$serviceGalleryEyebrow = trim((string) ($serviceRecord['gallery_eyebrow'] ?? 'Project Gallery')) ?: 'Project Gallery';
$serviceGalleryTitle = trim((string) ($serviceRecord['gallery_title'] ?? ('Selected visuals from ' . $serviceName))) ?: ('Selected visuals from ' . $serviceName);
$serviceGalleryEmptyNote = trim((string) ($serviceRecord['gallery_empty_note'] ?? 'Upload service gallery images from the Service Manager to populate this section.')) ?: 'Upload service gallery images from the Service Manager to populate this section.';
$serviceCtaEyebrow = trim((string) ($serviceRecord['cta_eyebrow'] ?? 'Work With Us')) ?: 'Work With Us';
$serviceCtaTitle = trim((string) ($serviceRecord['cta_title'] ?? ''));
$serviceCtaBody = trim((string) ($serviceRecord['cta_body'] ?? ''));
$serviceCtaButtonText = trim((string) ($serviceRecord['cta_button_text'] ?? 'Get Free Quote')) ?: 'Get Free Quote';
$serviceCtaButtonLink = trim((string) ($serviceRecord['cta_button_link'] ?? 'contact-us/'));
$serviceSupportTitle = trim((string) ($serviceRecord['support_title'] ?? ''));
$serviceSupportBody = trim((string) ($serviceRecord['support_body'] ?? ''));
$servicePhoneLabel = trim((string) ($serviceRecord['phone_label'] ?? 'Call Support Center 24/7')) ?: 'Call Support Center 24/7';
$serviceEmailLabel = trim((string) ($serviceRecord['email_label'] ?? 'Write To Us')) ?: 'Write To Us';
$serviceContactEyebrow = trim((string) ($serviceRecord['contact_eyebrow'] ?? 'Contact us')) ?: 'Contact us';
$serviceContactTitle = trim((string) ($serviceRecord['contact_title'] ?? 'Get in touch with us')) ?: 'Get in touch with us';
$serviceOfficeEyebrow = trim((string) ($serviceRecord['office_eyebrow'] ?? 'Office Details')) ?: 'Office Details';
$serviceOfficeLink1Title = trim((string) ($serviceRecord['office_link_1_title'] ?? 'Visit the contact page')) ?: 'Visit the contact page';
$serviceOfficeLink1Body = trim((string) ($serviceRecord['office_link_1_body'] ?? 'See office locations, send a message, or request a callback from our team.')) ?: 'See office locations, send a message, or request a callback from our team.';
$serviceOfficeLink2Title = trim((string) ($serviceRecord['office_link_2_title'] ?? 'Start a project brief')) ?: 'Start a project brief';
$serviceOfficeLink2Body = trim((string) ($serviceRecord['office_link_2_body'] ?? 'Share your scope and we will respond with the next practical steps.')) ?: 'Share your scope and we will respond with the next practical steps.';
$serviceRelatedEyebrow = trim((string) ($serviceRecord['related_eyebrow'] ?? 'More Services')) ?: 'More Services';
$serviceRelatedTitle = trim((string) ($serviceRecord['related_title'] ?? 'Explore related capabilities')) ?: 'Explore related capabilities';

$companyPhone = trim((string) $settings->get('company_phone', '+234 701 234 5678'));
$companyEmail = trim((string) $settings->get('company_email', 'info@tpvconstruction.com.ng'));
$companyAddress = trim((string) $settings->get('company_address', '2nd Floor, Right Wing, APDC Building, Area 11, Abuja, Nigeria'));
$supportPhone = $companyPhone !== '' ? $companyPhone : '+234 701 234 5678';
$supportEmail = $companyEmail !== '' ? $companyEmail : 'info@tpvconstruction.com.ng';
$supportPhoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $supportPhone);
$supportEmailHref = 'mailto:' . $supportEmail;

$serviceCtaHref = SITE_URL . 'contact-us/';
if ($serviceCtaButtonLink !== '') {
    if (preg_match('#^(https?:)?//#i', $serviceCtaButtonLink)) {
        $serviceCtaHref = $serviceCtaButtonLink;
    } elseif (str_starts_with($serviceCtaButtonLink, '/')) {
        $serviceCtaHref = $serviceCtaButtonLink;
    } else {
        $serviceCtaHref = SITE_URL . ltrim($serviceCtaButtonLink, '/');
    }
}
$serviceHeroSecondaryHref = SITE_URL . 'projects/';
if ($serviceHeroSecondaryButtonLink !== '') {
    if (preg_match('#^(https?:)?//#i', $serviceHeroSecondaryButtonLink)) {
        $serviceHeroSecondaryHref = $serviceHeroSecondaryButtonLink;
    } elseif (str_starts_with($serviceHeroSecondaryButtonLink, '/')) {
        $serviceHeroSecondaryHref = $serviceHeroSecondaryButtonLink;
    } else {
        $serviceHeroSecondaryHref = SITE_URL . ltrim($serviceHeroSecondaryButtonLink, '/');
    }
}

$heroMedia = '';
foreach (['gallery_1', 'gallery_2', 'gallery_3', 'gallery_4', 'gallery_5', 'gallery_6', 'sustainable_image_1', 'sustainable_image_2', 'cta_image', 'contact_image'] as $mediaField) {
    $value = trim((string) ($serviceRecord[$mediaField] ?? ''));
    if ($value !== '') {
        $heroMedia = $value;
        break;
    }
}
$heroMediaUrl = $heroMedia !== '' ? tpv_asset_url($heroMedia) : '';

$features = [];
for ($featureIndex = 1; $featureIndex <= 5; $featureIndex++) {
    $featureValue = trim((string) ($serviceRecord['feature_' . $featureIndex] ?? ''));
    if ($featureValue !== '') {
        $features[] = $featureValue;
    }
}

$highlights = [];
if ($serviceHighlight2Title !== '' || $serviceHighlight2Body !== '') {
    $highlights[] = ['title' => $serviceHighlight2Title, 'body' => $serviceHighlight2Body];
}
if ($serviceHighlight3Title !== '' || $serviceHighlight3Body !== '') {
    $highlights[] = ['title' => $serviceHighlight3Title, 'body' => $serviceHighlight3Body];
}

if (!empty($highlights)) {
    $seenHighlights = [];
    $uniqueHighlights = [];
    foreach ($highlights as $highlight) {
        $key = mb_strtolower(trim((string) ($highlight['title'] ?? '')));
        if (isset($seenHighlights[$key])) {
            continue;
        }
        $seenHighlights[$key] = true;
        $uniqueHighlights[] = $highlight;
    }
    $highlights = $uniqueHighlights;
}

$processSteps = [];
for ($stepIndex = 1; $stepIndex <= 3; $stepIndex++) {
    $stepTitle = trim((string) ($serviceRecord['step_' . $stepIndex . '_title'] ?? ''));
    $stepBody = trim((string) ($serviceRecord['step_' . $stepIndex . '_body'] ?? ''));
    if ($stepTitle !== '' || $stepBody !== '') {
        $processSteps[] = [
            'number' => str_pad((string) $stepIndex, 2, '0', STR_PAD_LEFT),
            'title' => $stepTitle,
            'body' => $stepBody,
        ];
    }
}

$galleryItems = [];
foreach (['gallery_1', 'gallery_2', 'gallery_3', 'gallery_4', 'gallery_5', 'gallery_6'] as $galleryField) {
    $value = trim((string) ($serviceRecord[$galleryField] ?? ''));
    if ($value !== '') {
        $galleryItems[] = tpv_asset_url($value);
    }
}

$storyImages = [];
foreach (['sustainable_image_1', 'sustainable_image_2'] as $storyImageField) {
    $value = trim((string) ($serviceRecord[$storyImageField] ?? ''));
    if ($value !== '') {
        $storyImages[] = tpv_asset_url($value);
    }
}

$relatedServices = [];
foreach (ServiceContent::getRegistry() as $relatedSlug => $relatedConfig) {
    if ($relatedSlug === $serviceSlug) {
        continue;
    }
    $relatedRecord = $serviceContentManager->getResolvedBySlug($relatedSlug);
    if (!$relatedRecord) {
        continue;
    }
    $relatedImage = '';
    foreach (['gallery_1', 'gallery_2', 'sustainable_image_1', 'cta_image'] as $relatedField) {
        $value = trim((string) ($relatedRecord[$relatedField] ?? ''));
        if ($value !== '') {
            $relatedImage = tpv_asset_url($value);
            break;
        }
    }
    $relatedServices[] = [
        'name' => trim((string) ($relatedRecord['name'] ?? $relatedConfig['name'])) ?: $relatedConfig['name'],
        'title' => trim((string) ($relatedRecord['overview_title'] ?? $relatedConfig['name'])) ?: $relatedConfig['name'],
        'href' => SITE_URL . 'services/' . $relatedSlug . '/',
        'image' => $relatedImage,
        'icon' => $relatedConfig['icon'],
    ];
}
$relatedServices = array_slice($relatedServices, 0, 3);

$metaDescription = $serviceMetaDescription !== ''
    ? $serviceMetaDescription
    : trim(strip_tags($serviceOverviewBody !== '' ? $serviceOverviewBody : $serviceContentBody));
if ($metaDescription === '') {
    $metaDescription = 'Explore ' . $serviceName . ' by TPV Construction and Services LTD.';
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($serviceTitle); ?> - TPV Construction and Services LTD</title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="robots" content="max-image-preview:large">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/uploads/elementor/css/post-7.css?ver=1770715450'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/uploads/elementor/css/post-225.css?ver=1770715449'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementskit/widgets/init/assets/css/widget-styles-pro.css?ver=4.2.1'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/style.css?ver=1.0.8'); ?>">
    <link rel="icon" href="<?php echo htmlspecialchars(SITE_URL . 'wp-content/uploads/2024/06/favicon.png'); ?>" sizes="32x32">
    <style>
        :root {
            --service-bg: #f5f7fb;
            --service-surface: #ffffff;
            --service-ink: #0f172a;
            --service-muted: #5f6f86;
            --service-line: #dbe3ef;
            --service-accent: #d4a13e;
            --service-accent-strong: #b88422;
            --service-panel: #eef3f9;
            --service-navy: #16233f;
        }

        * { box-sizing: border-box; }

        body.service-page-rebuilt {
            margin: 0;
            background: var(--service-bg);
            color: var(--service-ink);
            font-family: 'DM Sans', sans-serif;
        }

        body.service-page-rebuilt h1,
        body.service-page-rebuilt h2,
        body.service-page-rebuilt h3,
        body.service-page-rebuilt h4 {
            font-family: 'Manrope', sans-serif;
            color: var(--service-ink);
            letter-spacing: -0.03em;
        }

        .service-main {
            display: block;
        }

        .service-page-rebuilt .header-menu .ekit-menu-nav-link,
        .service-page-rebuilt .header-menu .ekit-menu-nav-link:visited,
        .service-page-rebuilt .header-menu .dropdown-item,
        .service-page-rebuilt .header-menu .dropdown-item:visited {
            color: #17233d !important;
        }

        .service-page-rebuilt .header-menu .ekit-menu-nav-link:hover,
        .service-page-rebuilt .header-menu .dropdown-item:hover,
        .service-page-rebuilt .header-menu .current-menu-ancestor > .ekit-menu-nav-link,
        .service-page-rebuilt .header-menu .current-menu-parent > .ekit-menu-nav-link,
        .service-page-rebuilt .header-menu .current-menu-item > .ekit-menu-nav-link {
            color: #e5363d !important;
        }

        .service-page-rebuilt .header-menu .elementskit-submenu-indicator,
        .service-page-rebuilt .header-menu .icon-down-arrow1 {
            color: #17233d !important;
        }

        .service-shell {
            width: min(1220px, calc(100% - 40px));
            margin: 0 auto;
        }

        .service-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--service-accent-strong);
        }

        .service-eyebrow::before {
            content: "";
            width: 42px;
            height: 1px;
            background: rgba(212, 161, 62, 0.6);
        }

        .service-hero {
            padding: 42px 0 52px;
        }

        .service-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
            gap: 32px;
            align-items: stretch;
        }

        .service-hero-copy {
            background: var(--service-surface);
            border: 1px solid rgba(219, 227, 239, 0.8);
            border-radius: 34px;
            padding: 42px;
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.07);
        }

        .service-hero-copy h1 {
            margin: 18px 0 18px;
            font-size: clamp(2.5rem, 4.4vw, 4.7rem);
            line-height: 0.96;
        }

        .service-hero-copy p {
            margin: 0;
            max-width: 640px;
            font-size: 17px;
            line-height: 1.9;
            color: var(--service-muted);
        }

        .service-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .service-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 54px;
            padding: 0 24px;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background-color .2s ease;
        }

        .service-btn:hover {
            transform: translateY(-2px);
        }

        .service-btn-primary {
            background: var(--service-accent);
            color: #182033;
            box-shadow: 0 18px 30px rgba(212, 161, 62, 0.22);
        }

        .service-btn-secondary {
            background: transparent;
            border: 1px solid var(--service-line);
            color: var(--service-ink);
        }

        .service-hero-aside {
            display: grid;
            gap: 18px;
        }

        .service-hero-media {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            min-height: 430px;
            background: linear-gradient(145deg, #dfe7f1 0%, #eef3f9 100%);
            border: 1px solid rgba(219, 227, 239, 0.85);
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.08);
        }

        .service-hero-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .service-hero-media-empty {
            width: 100%;
            height: 100%;
            min-height: 430px;
            display: grid;
            place-items: center;
            padding: 32px;
            text-align: center;
            color: var(--service-navy);
        }

        .service-hero-media-empty span {
            display: inline-flex;
            width: 92px;
            height: 92px;
            border-radius: 28px;
            background: rgba(22, 35, 63, 0.08);
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 18px;
        }

        .service-facts {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .service-facts.service-facts--triple {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .service-fact-card {
            background: var(--service-surface);
            border: 1px solid var(--service-line);
            border-radius: 24px;
            padding: 22px;
            min-height: 142px;
        }

        .service-fact-card h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .service-fact-card p {
            margin: 0;
            font-size: 14px;
            line-height: 1.8;
            color: var(--service-muted);
        }

        .service-section {
            padding: 26px 0 58px;
        }

        .service-section-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
            gap: 28px;
            align-items: start;
        }

        .service-panel {
            background: var(--service-surface);
            border: 1px solid rgba(219, 227, 239, 0.85);
            border-radius: 30px;
            padding: 34px;
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.06);
        }

        .service-panel h2 {
            margin: 14px 0 14px;
            font-size: clamp(1.9rem, 2.3vw, 2.9rem);
            line-height: 1.05;
        }

        .service-panel p {
            margin: 0 0 14px;
            font-size: 16px;
            line-height: 1.9;
            color: var(--service-muted);
        }

        .service-feature-list {
            list-style: none;
            margin: 26px 0 0;
            padding: 0;
            display: grid;
            gap: 14px;
        }

        .service-feature-list li {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border-radius: 18px;
            background: var(--service-panel);
            color: var(--service-ink);
            font-size: 15px;
            font-weight: 600;
        }

        .service-feature-list li i {
            color: var(--service-accent-strong);
            margin-top: 3px;
        }

        .service-story-stack {
            display: grid;
            gap: 16px;
        }

        .service-story-image {
            overflow: hidden;
            border-radius: 24px;
            min-height: 220px;
            background: linear-gradient(145deg, #dde6f1 0%, #edf3f8 100%);
            border: 1px solid var(--service-line);
        }

        .service-story-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .service-process {
            padding-top: 8px;
        }

        .service-process-head {
            max-width: 760px;
            margin-bottom: 28px;
        }

        .service-process-head h2 {
            margin: 14px 0 12px;
            font-size: clamp(2rem, 2.5vw, 3rem);
        }

        .service-process-head p {
            margin: 0;
            font-size: 16px;
            line-height: 1.9;
            color: var(--service-muted);
        }

        .service-process-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .service-step-card {
            background: var(--service-surface);
            border: 1px solid var(--service-line);
            border-radius: 26px;
            padding: 28px 24px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.05);
        }

        .service-step-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(212, 161, 62, 0.15);
            color: var(--service-accent-strong);
            font-family: 'Manrope', sans-serif;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .service-step-card h3 {
            margin: 0 0 10px;
            font-size: 20px;
        }

        .service-step-card p {
            margin: 0;
            font-size: 15px;
            line-height: 1.85;
            color: var(--service-muted);
        }

        .service-gallery-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 16px;
        }

        .service-gallery-card {
            overflow: hidden;
            border-radius: 24px;
            background: linear-gradient(145deg, #dde6f1 0%, #edf3f8 100%);
            border: 1px solid var(--service-line);
            min-height: 200px;
        }

        .service-gallery-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .service-gallery-card.is-large {
            grid-column: span 6;
            min-height: 320px;
        }

        .service-gallery-card.is-small {
            grid-column: span 3;
            min-height: 220px;
        }

        .service-cta-wrap {
            background: linear-gradient(140deg, #16233f 0%, #213458 100%);
            color: #f4f7fb;
            border-radius: 34px;
            padding: 38px;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(260px, 0.9fr);
            gap: 28px;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        .service-cta-wrap::after {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            right: -80px;
            top: -120px;
            background: rgba(255, 255, 255, 0.06);
        }

        .service-cta-copy,
        .service-cta-media {
            position: relative;
            z-index: 1;
        }

        .service-cta-copy h2 {
            margin: 14px 0 14px;
            font-size: clamp(2rem, 2.8vw, 3.1rem);
            color: #ffffff;
        }

        .service-cta-copy p {
            margin: 0;
            font-size: 16px;
            line-height: 1.9;
            color: rgba(244, 247, 251, 0.74);
        }

        .service-cta-media {
            min-height: 260px;
            border-radius: 28px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.09);
        }

        .service-cta-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .service-contact-grid {
            display: grid;
            grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.15fr);
            gap: 24px;
        }

        .service-contact-card {
            background: var(--service-surface);
            border: 1px solid var(--service-line);
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.06);
            height: 100%;
        }

        .service-contact-card h3 {
            margin: 14px 0 10px;
            font-size: 24px;
        }

        .service-contact-card p {
            margin: 0 0 18px;
            font-size: 15px;
            line-height: 1.85;
            color: var(--service-muted);
        }

        .service-contact-methods {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .service-contact-link {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 16px 18px;
            border-radius: 18px;
            background: var(--service-panel);
            text-decoration: none;
            color: inherit;
            transition: transform .2s ease;
        }

        .service-contact-link:hover {
            transform: translateY(-2px);
        }

        .service-contact-link i {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(212, 161, 62, 0.18);
            color: var(--service-accent-strong);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .service-contact-link strong {
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
            color: var(--service-ink);
        }

        .service-contact-link span {
            display: block;
            color: var(--service-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .service-related-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .service-related-card {
            text-decoration: none;
            color: inherit;
            background: var(--service-surface);
            border: 1px solid var(--service-line);
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.05);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .service-related-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 42px rgba(15, 23, 42, 0.08);
        }

        .service-related-media {
            min-height: 220px;
            background: linear-gradient(145deg, #dde6f1 0%, #edf3f8 100%);
            overflow: hidden;
        }

        .service-related-media img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .service-related-fallback {
            min-height: 220px;
            display: grid;
            place-items: center;
            color: var(--service-navy);
            font-size: 28px;
        }

        .service-related-body {
            padding: 22px;
        }

        .service-related-body h3 {
            margin: 0 0 8px;
            font-size: 20px;
        }

        .service-related-body p {
            margin: 0;
            color: var(--service-muted);
            font-size: 15px;
            line-height: 1.8;
        }

        .service-empty-note {
            font-size: 15px;
            color: var(--service-muted);
            padding: 20px 0 0;
        }

        @media (max-width: 1199px) {
            .service-hero-grid,
            .service-section-grid,
            .service-cta-wrap,
            .service-contact-grid {
                grid-template-columns: 1fr;
            }

            .service-process-grid,
            .service-related-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .service-shell {
                width: min(100% - 24px, 100%);
            }

            .service-hero {
                padding-top: 26px;
            }

            .service-hero-copy,
            .service-panel,
            .service-contact-card,
            .service-step-card,
            .service-cta-wrap {
                padding: 24px;
                border-radius: 24px;
            }

            .service-facts {
                grid-template-columns: 1fr;
            }

            .service-facts.service-facts--triple {
                grid-template-columns: 1fr;
            }

            .service-gallery-card.is-large,
            .service-gallery-card.is-small {
                grid-column: span 12;
                min-height: 220px;
            }

            .service-hero-media,
            .service-hero-media-empty {
                min-height: 300px;
            }
        }
    </style>
</head>
<body class="service-page-rebuilt wp-custom-logo elementor-default elementor-kit-7">
    <?php include dirname(__DIR__) . '/includes/header.php'; ?>

    <main class="service-main">
        <section class="service-hero">
            <div class="service-shell">
                <div class="service-hero-grid">
                    <div class="service-hero-copy">
                        <span class="service-eyebrow"><?php echo htmlspecialchars($serviceHeroEyebrow); ?></span>
                        <h1><?php echo htmlspecialchars($serviceTitle); ?></h1>
                        <p><?php echo htmlspecialchars($serviceOverviewBody !== '' ? $serviceOverviewBody : $serviceContentBody); ?></p>
                        <div class="service-hero-actions">
                            <a href="<?php echo htmlspecialchars($serviceCtaHref); ?>" class="service-btn service-btn-primary">
                                <i class="fas fa-arrow-right"></i>
                                <?php echo htmlspecialchars($serviceCtaButtonText); ?>
                            </a>
                            <a href="<?php echo htmlspecialchars($serviceHeroSecondaryHref); ?>" class="service-btn service-btn-secondary">
                                <i class="fas fa-images"></i>
                                <?php echo htmlspecialchars($serviceHeroSecondaryButtonText); ?>
                            </a>
                        </div>
                    </div>
                    <div class="service-hero-aside">
                        <div class="service-hero-media">
                            <?php if ($heroMediaUrl !== ''): ?>
                                <img src="<?php echo htmlspecialchars($heroMediaUrl); ?>" alt="<?php echo htmlspecialchars($serviceName); ?>">
                            <?php else: ?>
                                <div class="service-hero-media-empty">
                                    <div>
                                        <span><i class="fas fa-briefcase"></i></span>
                                        <h3><?php echo htmlspecialchars($serviceName); ?></h3>
                                        <p class="service-empty-note"><?php echo htmlspecialchars($serviceHeroEmptyNote); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($highlights)): ?>
                            <div class="service-facts">
                                <?php foreach ($highlights as $highlight): ?>
                                    <div class="service-fact-card">
                                        <h3><?php echo htmlspecialchars($highlight['title']); ?></h3>
                                        <p><?php echo htmlspecialchars($highlight['body']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="service-section">
            <div class="service-shell">
                <div class="service-section-grid">
                    <article class="service-panel">
                        <span class="service-eyebrow"><?php echo htmlspecialchars($serviceOverviewEyebrow); ?></span>
                        <h2><?php echo htmlspecialchars($serviceOverviewTitle); ?></h2>
                        <?php if ($serviceContentBody !== ''): ?>
                            <p><?php echo nl2br(htmlspecialchars($serviceContentBody)); ?></p>
                        <?php endif; ?>
                        <?php if ($serviceSustainableTitle !== '' || $serviceSustainableBody1 !== '' || $serviceSustainableBody2 !== ''): ?>
                            <h3 style="margin:22px 0 12px;font-size:1.5rem;"><?php echo htmlspecialchars($serviceSustainableTitle !== '' ? $serviceSustainableTitle : 'How we deliver value'); ?></h3>
                            <?php if ($serviceSustainableBody1 !== ''): ?><p><?php echo nl2br(htmlspecialchars($serviceSustainableBody1)); ?></p><?php endif; ?>
                            <?php if ($serviceSustainableBody2 !== ''): ?><p><?php echo nl2br(htmlspecialchars($serviceSustainableBody2)); ?></p><?php endif; ?>
                        <?php endif; ?>
                    </article>

                    <aside class="service-panel">
                        <span class="service-eyebrow"><?php echo htmlspecialchars($serviceBenefitsEyebrow); ?></span>
                        <h2 style="font-size:2rem;"><?php echo htmlspecialchars($serviceBenefitsTitle); ?></h2>
                        <?php if (!empty($features)): ?>
                            <ul class="service-feature-list">
                                <?php foreach ($features as $feature): ?>
                                    <li><i class="fas fa-check-circle"></i><span><?php echo htmlspecialchars($feature); ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="service-empty-note">Add benefits in the Service Manager to show them here.</p>
                        <?php endif; ?>
                    </aside>
                </div>
            </div>
        </section>

        <?php if (!empty($storyImages)): ?>
            <section class="service-section" style="padding-top:0;">
                <div class="service-shell">
                    <div class="service-story-stack">
                        <?php foreach ($storyImages as $storyImage): ?>
                            <div class="service-story-image">
                                <img src="<?php echo htmlspecialchars($storyImage); ?>" alt="<?php echo htmlspecialchars($serviceName); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="service-section">
            <div class="service-shell">
                <div class="service-process">
                    <div class="service-process-head">
                        <span class="service-eyebrow"><?php echo htmlspecialchars($serviceProcessEyebrow !== '' ? $serviceProcessEyebrow : 'Process'); ?></span>
                        <h2><?php echo htmlspecialchars($serviceProcessTitle !== '' ? $serviceProcessTitle : 'Our delivery process'); ?></h2>
                        <p><?php echo htmlspecialchars($serviceProcessBody !== '' ? $serviceProcessBody : 'We move from planning to execution with clear communication, practical coordination, and disciplined delivery.'); ?></p>
                    </div>
                    <?php if (!empty($processSteps)): ?>
                        <div class="service-process-grid">
                            <?php foreach ($processSteps as $step): ?>
                                <div class="service-step-card">
                                    <span class="service-step-index"><?php echo htmlspecialchars($step['number']); ?></span>
                                    <h3><?php echo htmlspecialchars($step['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($step['body']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="service-section" style="padding-top:0;">
            <div class="service-shell">
                <div class="service-panel">
                    <span class="service-eyebrow"><?php echo htmlspecialchars($serviceGalleryEyebrow); ?></span>
                    <h2><?php echo htmlspecialchars($serviceGalleryTitle); ?></h2>
                    <?php if (!empty($galleryItems)): ?>
                        <div class="service-gallery-grid" style="margin-top:24px;">
                            <?php foreach ($galleryItems as $galleryIndex => $galleryItem): ?>
                                <div class="service-gallery-card <?php echo $galleryIndex < 2 ? 'is-large' : 'is-small'; ?>">
                                    <img src="<?php echo htmlspecialchars($galleryItem); ?>" alt="<?php echo htmlspecialchars($serviceName . ' image ' . ($galleryIndex + 1)); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="service-empty-note"><?php echo htmlspecialchars($serviceGalleryEmptyNote); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="service-section" style="padding-top:0;">
            <div class="service-shell">
                <div class="service-cta-wrap">
                    <div class="service-cta-copy">
                        <span class="service-eyebrow" style="color:#e5c279;"><?php echo htmlspecialchars($serviceCtaEyebrow); ?></span>
                        <h2><?php echo htmlspecialchars($serviceCtaTitle !== '' ? $serviceCtaTitle : 'Ready to discuss your project?'); ?></h2>
                        <p><?php echo htmlspecialchars($serviceCtaBody !== '' ? $serviceCtaBody : 'Tell us what you are planning and our team will help shape the right execution path.'); ?></p>
                        <div class="service-hero-actions" style="margin-top:22px;">
                            <a href="<?php echo htmlspecialchars($serviceCtaHref); ?>" class="service-btn service-btn-primary"><?php echo htmlspecialchars($serviceCtaButtonText); ?></a>
                            <a href="<?php echo htmlspecialchars(SITE_URL . 'quote/'); ?>" class="service-btn service-btn-secondary" style="border-color:rgba(255,255,255,0.22);color:#fff;">Request a Quote</a>
                        </div>
                    </div>
                    <div class="service-cta-media">
                        <?php if (trim((string) ($serviceRecord['cta_image'] ?? '')) !== ''): ?>
                            <img src="<?php echo htmlspecialchars(tpv_asset_url($serviceRecord['cta_image'])); ?>" alt="<?php echo htmlspecialchars($serviceName); ?>">
                        <?php elseif ($heroMediaUrl !== ''): ?>
                            <img src="<?php echo htmlspecialchars($heroMediaUrl); ?>" alt="<?php echo htmlspecialchars($serviceName); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="service-section">
            <div class="service-shell">
                <div class="service-contact-grid">
                    <div class="service-contact-card">
                        <span class="service-eyebrow"><?php echo htmlspecialchars($serviceContactEyebrow); ?></span>
                        <h3><?php echo htmlspecialchars($serviceSupportTitle !== '' ? $serviceSupportTitle : $serviceContactTitle); ?></h3>
                        <p><?php echo htmlspecialchars($serviceSupportBody !== '' ? $serviceSupportBody : 'Reach out to our team for consultations, pricing guidance, or execution planning.'); ?></p>
                        <div class="service-contact-methods">
                            <a href="<?php echo htmlspecialchars($supportPhoneHref); ?>" class="service-contact-link">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <strong><?php echo htmlspecialchars($servicePhoneLabel); ?></strong>
                                    <span><?php echo htmlspecialchars($supportPhone); ?></span>
                                </div>
                            </a>
                            <a href="<?php echo htmlspecialchars($supportEmailHref); ?>" class="service-contact-link">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong><?php echo htmlspecialchars($serviceEmailLabel); ?></strong>
                                    <span><?php echo htmlspecialchars($supportEmail); ?></span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="service-contact-card">
                        <span class="service-eyebrow"><?php echo htmlspecialchars($serviceOfficeEyebrow); ?></span>
                        <h3><?php echo htmlspecialchars($serviceContactTitle); ?></h3>
                        <p><?php echo htmlspecialchars($companyAddress); ?></p>
                        <div class="service-contact-methods">
                            <a href="<?php echo htmlspecialchars(SITE_URL . 'contact-us/'); ?>" class="service-contact-link">
                                <i class="fas fa-location-arrow"></i>
                                <div>
                                    <strong><?php echo htmlspecialchars($serviceOfficeLink1Title); ?></strong>
                                    <span><?php echo htmlspecialchars($serviceOfficeLink1Body); ?></span>
                                </div>
                            </a>
                            <a href="<?php echo htmlspecialchars(SITE_URL . 'quote/'); ?>" class="service-contact-link">
                                <i class="fas fa-file-signature"></i>
                                <div>
                                    <strong><?php echo htmlspecialchars($serviceOfficeLink2Title); ?></strong>
                                    <span><?php echo htmlspecialchars($serviceOfficeLink2Body); ?></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!empty($relatedServices)): ?>
            <section class="service-section" style="padding-top:0;">
                <div class="service-shell">
                    <div class="service-panel">
                        <span class="service-eyebrow"><?php echo htmlspecialchars($serviceRelatedEyebrow); ?></span>
                        <h2><?php echo htmlspecialchars($serviceRelatedTitle); ?></h2>
                        <div class="service-related-grid" style="margin-top:24px;">
                            <?php foreach ($relatedServices as $relatedService): ?>
                                <a href="<?php echo htmlspecialchars($relatedService['href']); ?>" class="service-related-card">
                                    <div class="service-related-media">
                                        <?php if ($relatedService['image'] !== ''): ?>
                                            <img src="<?php echo htmlspecialchars($relatedService['image']); ?>" alt="<?php echo htmlspecialchars($relatedService['name']); ?>">
                                        <?php else: ?>
                                            <div class="service-related-fallback"><i class="<?php echo htmlspecialchars($relatedService['icon']); ?>"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="service-related-body">
                                        <h3><?php echo htmlspecialchars($relatedService['name']); ?></h3>
                                        <p><?php echo htmlspecialchars($relatedService['title']); ?></p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include dirname(__DIR__) . '/includes/footer.php'; ?>

    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-includes/js/jquery/jquery.min.js?ver=3.7.1'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/js/SmoothScroll.js?ver=1.0.8'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/js/gsap.min.js?ver=1.0.8'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/js/magiccursor.js?ver=1.0.8'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/js/SplitText.js?ver=1.0.8'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/js/ScrollTrigger.min.js?ver=1.0.8'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/themes/tpv/assets/js/function.js?ver=1.0.8'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementskit-lite/libs/framework/assets/js/frontend-script.js?ver=3.7.9'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementskit-lite/widgets/init/assets/js/widget-scripts.js?ver=3.7.9'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.35.3'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.35.3'); ?>"></script>
    <script src="<?php echo htmlspecialchars(SITE_URL . 'wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.35.3'); ?>"></script>
</body>
</html>
