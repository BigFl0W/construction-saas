<?php
if (!function_exists('tpv_setting_asset_url')) {
    require_once dirname(__DIR__) . '/config/config.php';
}

$siteLogoUrl = tpv_setting_asset_url('site_logo', 'wp-content/uploads/2024/06/logo.png');
$siteHomeUrl = rtrim((string) SITE_URL, '/') . '/';
$siteAboutUrl = $siteHomeUrl . 'about-us/';
$siteContactUrl = $siteHomeUrl . 'contact-us/';
$siteBlogUrl = $siteHomeUrl . 'blog/';
$siteServicesUrl = $siteHomeUrl . 'services/';
$siteQuoteUrl = $siteHomeUrl . 'quote/';
$serviceMenuLinks = [
    ['label' => 'Building Construction', 'href' => $siteHomeUrl . 'services/building-construction/'],
    ['label' => 'Architecture Design', 'href' => $siteHomeUrl . 'services/architecture-design/'],
    ['label' => 'Building Renovation', 'href' => $siteHomeUrl . 'services/building-renovation/'],
    ['label' => 'Interior / Exterior', 'href' => $siteHomeUrl . 'services/interior-exterior/'],
    ['label' => 'Project Management', 'href' => $siteHomeUrl . 'services/project-management/'],
    ['label' => 'Steel & Fabrication', 'href' => $siteHomeUrl . 'services/steel-and-fabrication/'],
];

$uri = $_SERVER['REQUEST_URI'] ?? '';
$isHome = preg_match('#/Archive/?$#', $uri) || preg_match('#/Archive/index\.php$#', $uri) ? 'is-active' : '';
$isAbout = strpos($uri, '/about-us') !== false ? 'is-active' : '';
$isContact = strpos($uri, '/contact-us') !== false ? 'is-active' : '';
$isBlog = strpos($uri, '/blog') !== false || strpos($uri, '/post.php') !== false ? 'is-active' : '';
$isServices = strpos($uri, '/services') !== false ? 'is-active' : '';
$isServicesLanding = preg_match('#/services/?$#', $uri) ? 'is-active' : '';
$isQuote = strpos($uri, '/quote') !== false ? 'is-active' : '';

$serviceMenuLinks = array_map(static function (array $serviceMenuLink) use ($uri) {
    $currentPath = rtrim((string) (parse_url($uri, PHP_URL_PATH) ?: $uri), '/');
    $linkPath = rtrim((string) (parse_url($serviceMenuLink['href'] ?? '', PHP_URL_PATH) ?: ''), '/');
    $serviceMenuLink['is_active'] = $currentPath !== '' && $currentPath === $linkPath;
    return $serviceMenuLink;
}, $serviceMenuLinks);
?>
<style>
    .tpv-site-header {
        position: sticky;
        top: 0;
        z-index: 1100;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 10px 34px -28px rgba(15, 23, 42, 0.45);
    }

    .tpv-site-header__inner {
        max-width: 1220px;
        margin: 0 auto;
        padding: 12px 24px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 18px;
    }

    .tpv-site-header__brand {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        min-width: 0;
    }

    .tpv-site-header__brand img {
        width: auto;
        max-width: 104px;
        max-height: 72px;
        height: auto;
        display: block;
    }

    .tpv-site-header__nav {
        display: flex;
        justify-content: center;
    }

    .tpv-site-header__nav-list,
    .tpv-site-header__drawer-list,
    .tpv-site-header__submenu {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .tpv-site-header__nav-list {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .tpv-site-header__nav-item {
        position: relative;
        display: flex;
        align-items: center;
    }

    .tpv-site-header__nav-link,
    .tpv-site-header__drawer-link,
    .tpv-site-header__submenu-link,
    .tpv-site-header__drawer-summary {
        text-decoration: none;
        color: #1e293b;
        font-size: 14px;
        font-weight: 600;
    }

    .tpv-site-header__nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 14px;
        border-radius: 999px;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .tpv-site-header__nav-dropdown {
        position: relative;
    }

    .tpv-site-header__nav-summary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 14px;
        border-radius: 999px;
        cursor: pointer;
        user-select: none;
        list-style: none;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .tpv-site-header__nav-summary::-webkit-details-marker {
        display: none;
    }

    .tpv-site-header__nav-summary-icon {
        margin-left: 6px;
        font-size: 11px;
        line-height: 1;
        transition: transform 0.2s ease;
    }

    .tpv-site-header__nav-dropdown[open] > .tpv-site-header__nav-summary .tpv-site-header__nav-summary-icon {
        transform: rotate(180deg);
    }

    .tpv-site-header__nav-dropdown-menu {
        position: absolute;
        top: calc(100% + 14px);
        left: 50%;
        transform: translate(-50%, 10px);
        min-width: 290px;
        padding: 10px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 22px 48px -24px rgba(15, 23, 42, 0.35);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
        z-index: 30;
    }

    .tpv-site-header__nav-dropdown[open] > .tpv-site-header__nav-dropdown-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translate(-50%, 0);
    }

    .tpv-site-header__nav-dropdown-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #edf2f7;
        color: #334155;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: background-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
    }

    .tpv-site-header__nav-dropdown-link:hover,
    .tpv-site-header__nav-dropdown-link:focus-visible,
    .tpv-site-header__nav-dropdown-link.is-active {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.16);
        color: #dc2626;
        outline: none;
        transform: translateY(-1px);
    }

    .tpv-site-header__nav-item--dropdown:hover > .tpv-site-header__nav-dropdown > .tpv-site-header__nav-summary,
    .tpv-site-header__nav-item--dropdown:focus-within > .tpv-site-header__nav-dropdown > .tpv-site-header__nav-summary,
    .tpv-site-header__nav-summary.is-active {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .tpv-site-header__nav-link:hover,
    .tpv-site-header__nav-link:focus-visible {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
        outline: none;
        transform: translateY(-1px);
    }

    .tpv-site-header__nav-link.is-active {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .tpv-site-header__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 156px;
        padding: 11px 18px;
        border-radius: 999px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.01em;
        box-shadow: 0 18px 30px -20px rgba(220, 38, 38, 0.75);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        white-space: nowrap;
    }

    .tpv-site-header__cta:hover,
    .tpv-site-header__cta:focus-visible {
        transform: translateY(-1px);
        box-shadow: 0 22px 34px -20px rgba(220, 38, 38, 0.9);
        outline: none;
    }

    .tpv-site-header__menu-button {
        display: none;
        width: 48px;
        height: 48px;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        appearance: none;
        -webkit-appearance: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .tpv-site-header__menu-button:hover,
    .tpv-site-header__menu-button:focus-visible {
        transform: translateY(-1px);
        opacity: 0.82;
        outline: none;
        background: transparent !important;
        box-shadow: none !important;
    }

    .tpv-site-header__menu-icon {
        position: relative;
        width: 20px;
        height: 14px;
    }

    .tpv-site-header__menu-icon::before,
    .tpv-site-header__menu-icon::after,
    .tpv-site-header__menu-icon span {
        position: absolute;
        left: 0;
        width: 20px;
        height: 2.5px;
        border-radius: 999px;
        background: #16233f !important;
        transition: transform 0.22s ease, opacity 0.22s ease, top 0.22s ease;
        content: "";
    }

    .tpv-site-header__menu-icon::before { top: 0; }
    .tpv-site-header__menu-icon span { top: 5.5px; }
    .tpv-site-header__menu-icon::after { top: 11px; }

    .tpv-site-header__menu-button[aria-expanded="true"] .tpv-site-header__menu-icon::before {
        top: 5.5px;
        transform: rotate(45deg);
    }

    .tpv-site-header__menu-button[aria-expanded="true"] .tpv-site-header__menu-icon span {
        opacity: 0;
    }

    .tpv-site-header__menu-button[aria-expanded="true"] .tpv-site-header__menu-icon::after {
        top: 5.5px;
        transform: rotate(-45deg);
    }

    .tpv-site-header__mobile-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.22s ease, visibility 0.22s ease;
        z-index: 1098;
    }

    .tpv-site-header__mobile-panel {
        position: fixed;
        top: 0;
        right: 0;
        width: min(88vw, 360px);
        height: 100vh;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: -14px 0 38px rgba(15, 23, 42, 0.18);
        transform: translateX(100%);
        transition: transform 0.24s ease;
        z-index: 1099;
        display: flex;
        flex-direction: column;
        padding: 18px 18px 24px;
        overflow-y: auto;
    }

    body.tpv-mobile-menu-open .tpv-site-header__mobile-overlay {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    body.tpv-mobile-menu-open .tpv-site-header__mobile-panel {
        transform: translateX(0);
    }

    body.tpv-mobile-menu-open {
        overflow: hidden;
    }

    .tpv-site-header__drawer-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .tpv-site-header__drawer-brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .tpv-site-header__drawer-brand img {
        max-width: 74px;
        max-height: 48px;
        display: block;
    }

    .tpv-site-header__drawer-close {
        border: 0;
        background: #f3f6fb;
        color: #0f172a;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 22px;
        line-height: 1;
    }

    .tpv-site-header__drawer-list {
        display: grid;
        gap: 8px;
    }

    .tpv-site-header__drawer-link,
    .tpv-site-header__drawer-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 13px 15px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #ebf0f6;
    }

    .tpv-site-header__drawer-link.is-active,
    .tpv-site-header__drawer-summary.is-active {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.16);
        color: #dc2626;
    }

    .tpv-site-header__drawer-item details {
        border-radius: 18px;
    }

    .tpv-site-header__drawer-summary {
        cursor: pointer;
        list-style: none;
    }

    .tpv-site-header__drawer-summary::-webkit-details-marker {
        display: none;
    }

    .tpv-site-header__drawer-summary-icon {
        font-size: 12px;
        transition: transform 0.2s ease;
    }

    .tpv-site-header__drawer-item details[open] .tpv-site-header__drawer-summary-icon {
        transform: rotate(180deg);
    }

    .tpv-site-header__submenu {
        display: grid;
        gap: 8px;
        padding: 10px 8px 2px;
    }

    .tpv-site-header__submenu-link {
        display: flex;
        padding: 11px 14px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #edf2f7;
        color: #475569;
    }

    .tpv-site-header__drawer-footer {
        margin-top: 22px;
    }

    .tpv-site-header__drawer-footer .tpv-site-header__cta {
        width: 100%;
        min-width: 0;
    }

    @media (max-width: 1080px) {
        .tpv-site-header__inner {
            grid-template-columns: auto auto;
            justify-content: space-between;
            padding: 12px 18px;
        }

        .tpv-site-header__nav,
        .tpv-site-header__actions {
            display: none;
        }

        .tpv-site-header__menu-button {
            display: inline-flex;
        }
    }

    @media (min-width: 1081px) {
        .tpv-site-header__mobile-overlay,
        .tpv-site-header__mobile-panel {
            display: none;
        }
    }

    @media (max-width: 680px) {
        .tpv-site-header__inner {
            padding: 10px 14px;
            gap: 12px;
        }

        .tpv-site-header__brand img {
            max-width: 82px;
            max-height: 56px;
        }

        .tpv-site-header__menu-button {
            width: 46px;
            height: 46px;
        }

        .tpv-site-header__mobile-panel {
            width: min(92vw, 340px);
            padding: 16px 14px 20px;
        }
    }
</style>

<header class="tpv-site-header">
    <div class="tpv-site-header__inner">
        <a class="tpv-site-header__brand" href="<?php echo htmlspecialchars($siteHomeUrl); ?>" aria-label="TPV Construction and Services LTD home">
            <img src="<?php echo htmlspecialchars($siteLogoUrl); ?>" alt="TPV Construction and Services LTD">
        </a>

    <nav class="tpv-site-header__nav" aria-label="Primary navigation">
        <ul class="tpv-site-header__nav-list">
            <li class="tpv-site-header__nav-item"><a class="tpv-site-header__nav-link <?php echo $isHome; ?>" href="<?php echo htmlspecialchars($siteHomeUrl); ?>">Home</a></li>
            <li class="tpv-site-header__nav-item"><a class="tpv-site-header__nav-link <?php echo $isAbout; ?>" href="<?php echo htmlspecialchars($siteAboutUrl); ?>">About Us</a></li>
            <li class="tpv-site-header__nav-item"><a class="tpv-site-header__nav-link <?php echo $isContact; ?>" href="<?php echo htmlspecialchars($siteContactUrl); ?>">Contact Us</a></li>
            <li class="tpv-site-header__nav-item"><a class="tpv-site-header__nav-link <?php echo $isBlog; ?>" href="<?php echo htmlspecialchars($siteBlogUrl); ?>">Blog</a></li>
            <li class="tpv-site-header__nav-item tpv-site-header__nav-item--dropdown">
                <details class="tpv-site-header__nav-dropdown" <?php echo $isServicesLanding ? 'open' : ''; ?>>
                    <summary class="tpv-site-header__nav-summary <?php echo $isServices; ?>">
                        <span>Services</span>
                        <span class="tpv-site-header__nav-summary-icon">&#9662;</span>
                    </summary>
                    <div class="tpv-site-header__nav-dropdown-menu" role="menu" aria-label="Services submenu">
                        <a class="tpv-site-header__nav-dropdown-link <?php echo $isServicesLanding; ?>" href="<?php echo htmlspecialchars($siteServicesUrl); ?>">All Services</a>
                        <?php foreach ($serviceMenuLinks as $serviceMenuLink): ?>
                            <a class="tpv-site-header__nav-dropdown-link <?php echo !empty($serviceMenuLink['is_active']) ? 'is-active' : ''; ?>" href="<?php echo htmlspecialchars($serviceMenuLink['href']); ?>"><?php echo htmlspecialchars($serviceMenuLink['label']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </details>
            </li>
        </ul>
    </nav>

        <div class="tpv-site-header__actions">
            <a class="tpv-site-header__cta <?php echo $isQuote; ?>" href="<?php echo htmlspecialchars($siteQuoteUrl); ?>" aria-current="<?php echo $isQuote ? 'page' : 'false'; ?>">
                Get Free Quote
            </a>
        </div>

        <button class="tpv-site-header__menu-button" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="tpv-mobile-panel">
            <span class="tpv-site-header__menu-icon"><span></span></span>
        </button>
    </div>
</header>

<div class="tpv-site-header__mobile-overlay" data-mobile-overlay></div>

<aside class="tpv-site-header__mobile-panel" id="tpv-mobile-panel" aria-hidden="true">
    <div class="tpv-site-header__drawer-head">
        <a class="tpv-site-header__drawer-brand" href="<?php echo htmlspecialchars($siteHomeUrl); ?>">
            <img src="<?php echo htmlspecialchars($siteLogoUrl); ?>" alt="TPV Construction and Services LTD">
        </a>
        <button class="tpv-site-header__drawer-close" type="button" aria-label="Close menu" data-mobile-close>&times;</button>
    </div>

    <ul class="tpv-site-header__drawer-list">
        <li class="tpv-site-header__drawer-item"><a class="tpv-site-header__drawer-link <?php echo $isHome; ?>" href="<?php echo htmlspecialchars($siteHomeUrl); ?>">Home</a></li>
        <li class="tpv-site-header__drawer-item"><a class="tpv-site-header__drawer-link <?php echo $isAbout; ?>" href="<?php echo htmlspecialchars($siteAboutUrl); ?>">About Us</a></li>
        <li class="tpv-site-header__drawer-item"><a class="tpv-site-header__drawer-link <?php echo $isContact; ?>" href="<?php echo htmlspecialchars($siteContactUrl); ?>">Contact Us</a></li>
        <li class="tpv-site-header__drawer-item"><a class="tpv-site-header__drawer-link <?php echo $isBlog; ?>" href="<?php echo htmlspecialchars($siteBlogUrl); ?>">Blog</a></li>
        <li class="tpv-site-header__drawer-item">
            <details <?php echo $isServices ? 'open' : ''; ?>>
                <summary class="tpv-site-header__drawer-summary <?php echo $isServices; ?>">
                    <span>Services</span>
                    <span class="tpv-site-header__drawer-summary-icon">&#9662;</span>
                </summary>
                <ul class="tpv-site-header__submenu">
                    <li><a class="tpv-site-header__submenu-link <?php echo $isServicesLanding; ?>" href="<?php echo htmlspecialchars($siteServicesUrl); ?>">All Services</a></li>
                    <?php foreach ($serviceMenuLinks as $serviceMenuLink): ?>
                        <li><a class="tpv-site-header__submenu-link <?php echo !empty($serviceMenuLink['is_active']) ? 'is-active' : ''; ?>" href="<?php echo htmlspecialchars($serviceMenuLink['href']); ?>"><?php echo htmlspecialchars($serviceMenuLink['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </details>
        </li>
    </ul>

    <div class="tpv-site-header__drawer-footer">
        <a class="tpv-site-header__cta <?php echo $isQuote; ?>" href="<?php echo htmlspecialchars($siteQuoteUrl); ?>">
            Get Free Quote
        </a>
    </div>
</aside>

<script>
(() => {
    const menuButton = document.querySelector('.tpv-site-header__menu-button');
    const mobilePanel = document.getElementById('tpv-mobile-panel');
    const overlay = document.querySelector('[data-mobile-overlay]');
    const closeButton = document.querySelector('[data-mobile-close]');

    if (!menuButton || !mobilePanel || !overlay) {
        return;
    }

    const setMenuState = (isOpen) => {
        document.body.classList.toggle('tpv-mobile-menu-open', isOpen);
        menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        mobilePanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    };

    menuButton.addEventListener('click', () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
        setMenuState(!isOpen);
    });

    overlay.addEventListener('click', () => setMenuState(false));
    if (closeButton) {
        closeButton.addEventListener('click', () => setMenuState(false));
    }

    const desktopDropdowns = Array.from(document.querySelectorAll('.tpv-site-header__nav-dropdown'));
    if (desktopDropdowns.length) {
        document.addEventListener('click', (event) => {
            desktopDropdowns.forEach((dropdown) => {
                if (!dropdown.contains(event.target)) {
                    dropdown.removeAttribute('open');
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                desktopDropdowns.forEach((dropdown) => dropdown.removeAttribute('open'));
            }
        });
    }

    mobilePanel.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setMenuState(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMenuState(false);
        }
    });
})();
</script>
