<?php
if (!function_exists('tpv_setting_asset_url')) {
    require_once dirname(__DIR__) . '/config/config.php';
}

require_once dirname(__DIR__) . '/classes/Settings.php';

$footerSettings = new Settings();
$companyName = trim((string) $footerSettings->get('company_name', 'TPV Construction and Services LTD'));
$companyEmailDefault = trim((string) $footerSettings->get('company_email', 'info@tpvconstruction.com.ng'));
$companyPhoneDefault = trim((string) $footerSettings->get('company_phone', '+234 701 234 5678'));
$companyAddressDefault = trim((string) $footerSettings->get('company_address', '2nd Floor, Right Wing, APDC Building, Area 11, Abuja, Nigeria'));
$footerLogoUrl = tpv_setting_asset_url('footer_logo', 'wp-content/uploads/2024/06/footer-logo.png');
$footerDescription = trim((string) $footerSettings->get('footer_description', "Building Nigeria's future with excellence, integrity, and innovation. Your trusted partner for quality construction across the nation."));
$footerServicesHeading = trim((string) $footerSettings->get('footer_services_heading', 'Our Services'));
$footerCompanyHeading = trim((string) $footerSettings->get('footer_company_heading', 'Company'));
$footerContactHeading = trim((string) $footerSettings->get('footer_contact_heading', 'Contact Us'));
$footerPhone = trim((string) $footerSettings->get('footer_phone', $companyPhoneDefault ?: '+234 701 234 5678'));
$footerEmail = trim((string) $footerSettings->get('footer_email', $companyEmailDefault ?: 'info@tpvconstruction.com.ng'));
$footerLocationsRaw = (string) $footerSettings->get('footer_locations', $companyAddressDefault ?: '2nd Floor, Right Wing, APDC Building, Area 11, Abuja, Nigeria');
$footerCopyright = trim((string) $footerSettings->get('footer_copyright', 'Copyright © 2026 ' . ($companyName ?: 'TPV Construction and Services LTD') . '. All Rights Reserved.'));
$footerInstagramUrl = trim((string) $footerSettings->get('footer_instagram_url', '#'));
$footerFacebookUrl = trim((string) $footerSettings->get('footer_facebook_url', '#'));
$footerTwitterUrl = trim((string) $footerSettings->get('footer_twitter_url', '#'));
$footerLinkedinUrl = trim((string) $footerSettings->get('footer_linkedin_url', '#'));
$footerLocations = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $footerLocationsRaw))));

if (!$footerLocations && $companyAddressDefault !== '') {
    $footerLocations[] = $companyAddressDefault;
}

$footerPhoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $footerPhone);

$serviceLinks = [
    ['label' => 'Building Construction', 'href' => SITE_URL . 'services/building-construction/'],
    ['label' => 'Architecture Design', 'href' => SITE_URL . 'services/architecture-design/'],
    ['label' => 'Building Renovation', 'href' => SITE_URL . 'services/building-renovation/'],
    ['label' => 'Interior / Exterior', 'href' => SITE_URL . 'services/interior-exterior/'],
    ['label' => 'Project Management', 'href' => SITE_URL . 'services/project-management/'],
    ['label' => 'Steel & Fabrication', 'href' => SITE_URL . 'services/steel-and-fabrication/'],
];

$companyLinks = [
    ['label' => 'About Us', 'href' => SITE_URL . 'about-us/'],
    ['label' => 'Services', 'href' => SITE_URL . 'services/'],
    ['label' => 'Blog', 'href' => SITE_URL . 'blog/'],
    ['label' => 'FAQs', 'href' => SITE_URL . 'faqs/'],
];

$socialLinks = [
    ['label' => 'Instagram', 'href' => $footerInstagramUrl ?: '#', 'icon' => 'ig'],
    ['label' => 'Facebook', 'href' => $footerFacebookUrl ?: '#', 'icon' => 'f'],
    ['label' => 'X', 'href' => $footerTwitterUrl ?: '#', 'icon' => 'x'],
    ['label' => 'LinkedIn', 'href' => $footerLinkedinUrl ?: '#', 'icon' => 'in'],
];

$footerLocationEntries = [];
foreach ($footerLocations as $location) {
    $parts = explode(':', $location, 2);
    if (count($parts) === 2) {
        $footerLocationEntries[] = [
            'title' => trim($parts[0]),
            'detail' => trim($parts[1]),
        ];
    } else {
        $footerLocationEntries[] = [
            'title' => 'Office',
            'detail' => trim($location),
        ];
    }
}
?>
<footer class="tpv-site-footer">
    <style>
        .tpv-site-footer {
            --footer-bg: #121d3a;
            --footer-border: rgba(255, 255, 255, 0.1);
            --footer-text: #edf2ff;
            --footer-muted: #9aa7c3;
            --footer-soft: #7f8aa8;
            margin-top: 88px;
            background: linear-gradient(180deg, #162442 0%, #111b35 100%);
            color: var(--footer-text);
        }

        .tpv-site-footer * {
            box-sizing: border-box;
        }

        .tpv-footer-shell {
            width: min(1220px, calc(100% - 48px));
            margin: 0 auto;
            padding: 54px 0 22px;
        }

        .tpv-footer-top {
            display: grid;
            grid-template-columns: minmax(240px, 1.25fr) repeat(2, minmax(150px, 0.9fr)) minmax(240px, 1fr);
            gap: 44px;
            align-items: start;
        }

        .tpv-footer-brand {
            max-width: 320px;
        }

        .tpv-footer-brand-lockup {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
        }

        .tpv-footer-logo-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.96);
            display: grid;
            place-items: center;
            overflow: hidden;
            flex: 0 0 auto;
        }

        .tpv-footer-logo-wrap img {
            width: 70%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .tpv-footer-brand-name {
            color: #ffffff;
            font-size: 22px !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
            letter-spacing: -0.02em;
        }

        .tpv-footer-brand-copy {
            margin: 0;
            color: var(--footer-muted);
            font-size: 15px;
            line-height: 1.9;
        }

        .tpv-footer-group {
            min-width: 0;
        }

        .tpv-footer-heading {
            margin: 0 0 18px;
            color: #cfd7eb;
            font-size: 12px !important;
            line-height: 1.2 !important;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .tpv-footer-links,
        .tpv-footer-meta-links,
        .tpv-footer-location-list,
        .tpv-footer-socials {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .tpv-footer-links {
            display: grid;
            gap: 12px;
        }

        .tpv-footer-links a,
        .tpv-footer-contact-link {
            color: var(--footer-muted);
            text-decoration: none;
            font-size: 15px !important;
            line-height: 1.65 !important;
            transition: color 0.2s ease;
        }

        .tpv-footer-links a:hover,
        .tpv-footer-contact-link:hover,
        .tpv-footer-meta-links a:hover,
        .tpv-footer-socials a:hover {
            color: #ffffff;
        }

        .tpv-footer-contact-copy {
            margin: 0 0 16px;
            color: var(--footer-muted);
            font-size: 15px;
            line-height: 1.8;
        }

        .tpv-footer-contact-stack {
            display: grid;
            gap: 10px;
        }

        .tpv-footer-locations-row {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .tpv-footer-location-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 0;
            margin-top: 12px;
        }

        .tpv-footer-location-list li {
            display: inline-flex;
            align-items: baseline;
            flex-wrap: wrap;
            max-width: 100%;
        }

        .tpv-footer-location-list li:not(:last-child)::after {
            content: "";
            display: inline-block;
            width: 1px;
            height: 14px;
            margin: 0 18px;
            background: rgba(255, 255, 255, 0.16);
            vertical-align: middle;
        }

        .tpv-footer-location-title {
            color: #d9e2f3;
            font-size: 13px !important;
            line-height: 1.5 !important;
            font-weight: 600;
            margin-right: 6px;
        }

        .tpv-footer-location-detail {
            color: var(--footer-soft);
            font-size: 13px !important;
            line-height: 1.65 !important;
        }

        .tpv-footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-top: 42px;
            padding-top: 22px;
            border-top: 1px solid var(--footer-border);
        }

        .tpv-footer-copy {
            margin: 0;
            color: var(--footer-soft);
            font-size: 13px !important;
            line-height: 1.7 !important;
        }

        .tpv-footer-bottom-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 24px;
            flex-wrap: wrap;
        }

        .tpv-footer-meta-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 28px;
        }

        .tpv-footer-meta-links a,
        .tpv-footer-socials a {
            color: var(--footer-soft);
            text-decoration: none;
            font-size: 13px !important;
            line-height: 1.6 !important;
        }

        .tpv-footer-socials {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .tpv-footer-socials a {
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #dfe8fb;
            font-weight: 700;
            letter-spacing: 0.01em;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .tpv-footer-socials a:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            transform: translateY(-1px);
        }

        @media (max-width: 1024px) {
            .tpv-footer-top {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 34px;
            }

            .tpv-footer-brand {
                max-width: none;
            }
        }

        @media (max-width: 767px) {
            .tpv-site-footer {
                margin-top: 72px;
            }

            .tpv-footer-shell {
                width: min(100% - 24px, 1220px);
                padding: 44px 0 20px;
            }

            .tpv-footer-top {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .tpv-footer-locations-row {
                margin-top: 18px;
                padding-top: 16px;
            }

            .tpv-footer-bottom {
                flex-direction: column;
                align-items: flex-start;
            }

            .tpv-footer-bottom-right {
                justify-content: flex-start;
                gap: 16px;
            }

            .tpv-footer-meta-links {
                justify-content: flex-start;
                gap: 18px;
            }

            .tpv-footer-location-list {
                display: grid;
                gap: 10px;
            }

            .tpv-footer-location-list li {
                display: grid;
                gap: 2px;
            }

            .tpv-footer-location-list li:not(:last-child)::after {
                display: none;
            }
        }
    </style>

    <div class="tpv-footer-shell">
        <div class="tpv-footer-top">
            <div class="tpv-footer-brand">
                <div class="tpv-footer-brand-lockup">
                    <div class="tpv-footer-logo-wrap">
                        <img src="<?php echo htmlspecialchars($footerLogoUrl); ?>" alt="<?php echo htmlspecialchars($companyName); ?> Logo">
                    </div>
                    <div class="tpv-footer-brand-name"><?php echo htmlspecialchars($companyName); ?></div>
                </div>
                <p class="tpv-footer-brand-copy"><?php echo htmlspecialchars($footerDescription); ?></p>
            </div>

            <div class="tpv-footer-group">
                <div class="tpv-footer-heading"><?php echo htmlspecialchars($footerServicesHeading); ?></div>
                <ul class="tpv-footer-links">
                    <?php foreach ($serviceLinks as $link): ?>
                        <li><a href="<?php echo htmlspecialchars($link['href']); ?>"><?php echo htmlspecialchars($link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tpv-footer-group">
                <div class="tpv-footer-heading"><?php echo htmlspecialchars($footerCompanyHeading); ?></div>
                <ul class="tpv-footer-links">
                    <?php foreach ($companyLinks as $link): ?>
                        <li><a href="<?php echo htmlspecialchars($link['href']); ?>"><?php echo htmlspecialchars($link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tpv-footer-group">
                <div class="tpv-footer-heading"><?php echo htmlspecialchars($footerContactHeading); ?></div>
                <p class="tpv-footer-contact-copy">Connect with our team for project enquiries, site visits, and updates from TPV Construction.</p>
                <div class="tpv-footer-contact-stack">
                    <a class="tpv-footer-contact-link" href="<?php echo htmlspecialchars($footerPhoneHref); ?>"><?php echo htmlspecialchars($footerPhone); ?></a>
                    <a class="tpv-footer-contact-link" href="mailto:<?php echo htmlspecialchars($footerEmail); ?>"><?php echo htmlspecialchars($footerEmail); ?></a>
                </div>
            </div>
        </div>

        <?php if ($footerLocations): ?>
            <div class="tpv-footer-locations-row">
                <div class="tpv-footer-heading">Office Locations</div>
                <ul class="tpv-footer-location-list">
                    <?php foreach ($footerLocationEntries as $location): ?>
                        <li>
                            <span class="tpv-footer-location-title"><?php echo htmlspecialchars($location['title']); ?></span>
                            <span class="tpv-footer-location-detail"><?php echo htmlspecialchars($location['detail']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="tpv-footer-bottom">
            <p class="tpv-footer-copy"><?php echo htmlspecialchars($footerCopyright); ?></p>
            <div class="tpv-footer-bottom-right">
                <ul class="tpv-footer-socials">
                    <?php foreach ($socialLinks as $social): ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($social['href']); ?>" aria-label="<?php echo htmlspecialchars($social['label']); ?>">
                                <?php echo htmlspecialchars($social['icon']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <ul class="tpv-footer-meta-links">
                    <li><a href="<?php echo htmlspecialchars(SITE_URL . 'about-us/'); ?>">About</a></li>
                    <li><a href="<?php echo htmlspecialchars(SITE_URL . 'services/'); ?>">Services</a></li>
                    <li><a href="<?php echo htmlspecialchars(SITE_URL . 'contact-us/'); ?>">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script>
    jQuery(document).ready(function($) {
        'use strict';

        function animateCounter($counter) {
            const $numberElement = $counter.find('.elementor-counter-number');
            const fromValue = parseFloat($numberElement.data('from-value')) || 0;
            const toValue = parseFloat($numberElement.data('to-value')) || 0;
            const duration = parseFloat($numberElement.data('duration')) || 3000;
            const delimiter = $numberElement.data('delimiter') || ',';

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
            }

            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const currentValue = Math.floor(progress * (toValue - fromValue) + fromValue);

                $numberElement.text(formatNumber(currentValue));

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    $numberElement.text(formatNumber(toValue));
                }
            };

            window.requestAnimationFrame(step);
        }

        function isElementPartiallyInViewport($element) {
            const rect = $element[0].getBoundingClientRect();
            const windowHeight = window.innerHeight || document.documentElement.clientHeight;
            const windowWidth = window.innerWidth || document.documentElement.clientWidth;

            return (
                rect.top <= windowHeight &&
                rect.bottom >= 0 &&
                rect.left <= windowWidth &&
                rect.right >= 0
            );
        }

        function initCounters() {
            $('.elementor-counter').each(function() {
                const $counter = $(this);
                const $numberElement = $counter.find('.elementor-counter-number');

                if ($counter.data('counter-animated')) {
                    return;
                }

                if (isElementPartiallyInViewport($counter)) {
                    $counter.data('counter-animated', true);
                    $numberElement.text('0');
                    animateCounter($counter);
                }
            });
        }

        function jQueryAnimateCounter($counter) {
            const $numberElement = $counter.find('.elementor-counter-number');
            const fromValue = parseFloat($numberElement.data('from-value')) || 0;
            const toValue = parseFloat($numberElement.data('to-value')) || 0;
            const duration = parseFloat($numberElement.data('duration')) || 3000;
            const delimiter = $numberElement.data('delimiter') || ',';

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
            }

            $({ counter: fromValue }).animate(
                { counter: toValue },
                {
                    duration: duration,
                    easing: 'linear',
                    step: function() {
                        $numberElement.text(formatNumber(Math.floor(this.counter)));
                    },
                    complete: function() {
                        $numberElement.text(formatNumber(toValue));
                    }
                }
            );
        }

        if (!window.requestAnimationFrame) {
            $('.elementor-counter').each(function() {
                const $counter = $(this);
                if (isElementPartiallyInViewport($counter)) {
                    jQueryAnimateCounter($counter);
                }
            });
        } else {
            initCounters();
            $(window).on('scroll resize', function() {
                initCounters();
            });
        }
    });
</script>
