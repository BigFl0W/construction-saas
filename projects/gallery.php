<?php
require_once dirname(__DIR__) . '/config/config.php';
$db = Database::getInstance();

$service = trim($_GET['service'] ?? '');

if (!$service) {
    header('Location: index.php');
    exit;
}

// Load all media for this service
$items = $db->query(
    "SELECT * FROM project_media WHERE service = :service ORDER BY featured DESC, file_type ASC, created_at ASC",
    ['service' => $service]
)->fetchAll();

if (empty($items)) {
    header('Location: index.php');
    exit;
}

// Load all other services for the sidebar nav
$allServices = $db->query(
    "SELECT service, COUNT(*) as total,
     SUM(file_type = 'image') as img_count,
     SUM(file_type = 'video') as vid_count
     FROM project_media
     GROUP BY service ORDER BY service"
)->fetchAll();

function galMediaUrl($path) {
    if (!$path) return '';
    if (strpos($path, '/') === 0) $path = preg_replace('#^.*?uploads/#', '', $path);
    return UPLOAD_URL . $path;
}

$imgItems = array_filter($items, fn($m) => $m['file_type'] === 'image');
$vidItems = array_filter($items, fn($m) => $m['file_type'] === 'video');
$total = count($items);
$imgCount = count($imgItems);
$vidCount = count($vidItems);
$cover = null;
foreach ($items as $m) { if ($m['file_type'] === 'image') { $cover = $m; break; } }
if (!$cover) $cover = $items[0];
?><!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service); ?> Gallery – TPV Construction and Services LTD</title>
    <meta name="description" content="Browse all photos and videos from the <?php echo htmlspecialchars($service); ?> project by TPV Construction and Services LTD.">
    <meta name='robots' content='max-image-preview:large'>
    <link rel='dns-prefetch' href='//fonts.googleapis.com'>
    <link rel='stylesheet' href='../wp-content/plugins/contact-form-7/includes/css/styles.css?ver=6.1.5' media='all'>
    <link rel='stylesheet' href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' href='../wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' href='../wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' href='../wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' href='../wp-content/themes/tpv/style.css?ver=1.0.8' media='all'>
    <link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="32x32">
    <script src="../wp-includes/js/jquery/jquery.min.js?ver=3.7.1"></script>
    <style>
    /* ── Page chrome ─────────────────────────────────────────── */
    body { background:#f8fafc; }
    .gal-hero {
        position:relative;min-height:380px;display:flex;align-items:flex-end;
        background:#1e293b;overflow:hidden;
    }
    .gal-hero-bg {
        position:absolute;inset:0;object-fit:cover;width:100%;height:100%;
        opacity:0.38;transition:opacity 0.4s;
    }
    .gal-hero-overlay {
        position:absolute;inset:0;
        background:linear-gradient(to top,rgba(15,23,42,0.85) 0%,rgba(15,23,42,0.2) 60%,transparent 100%);
    }
    .gal-hero-content {
        position:relative;z-index:2;padding:40px 32px 36px;width:100%;
    }
    .gal-breadcrumb {
        display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,0.7);
        margin-bottom:14px;flex-wrap:wrap;
    }
    .gal-breadcrumb a { color:rgba(255,255,255,0.7);text-decoration:none; }
    .gal-breadcrumb a:hover { color:#E5363D; }
    .gal-breadcrumb .sep { color:rgba(255,255,255,0.35); }
    .gal-hero-title {
        font-size:clamp(28px,5vw,48px);font-weight:800;color:#fff;line-height:1.15;
        margin:0 0 12px;text-shadow:0 2px 8px rgba(0,0,0,0.4);
    }
    .gal-hero-meta {
        display:flex;flex-wrap:wrap;gap:12px;
    }
    .gal-hero-chip {
        display:inline-flex;align-items:center;gap:6px;
        background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);
        color:#fff;font-size:13px;font-weight:600;
        padding:5px 14px;border-radius:50px;
    }
    /* ── Layout ──────────────────────────────────────────────── */
    .gal-wrap { max-width:1400px;margin:0 auto;padding:40px 20px 80px; }
    .gal-layout { display:grid;grid-template-columns:220px 1fr;gap:30px; }
    @media(max-width:900px) { .gal-layout { grid-template-columns:1fr; } }
    /* ── Sidebar ─────────────────────────────────────────────── */
    .gal-sidebar {
        position:sticky;top:80px;align-self:start;
        background:#fff;border-radius:16px;padding:20px;
        box-shadow:0 4px 16px rgba(0,0,0,0.06);
    }
    .gal-sidebar h6 {
        font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
        color:#94a3b8;margin:0 0 14px;
    }
    .gal-nav-item {
        display:flex;align-items:center;gap:10px;
        padding:10px 12px;border-radius:10px;margin-bottom:4px;
        text-decoration:none;color:#334155;font-size:13px;font-weight:600;
        transition:background 0.2s,color 0.2s;
    }
    .gal-nav-item:hover { background:#f1f5f9;color:#E5363D; }
    .gal-nav-item.active { background:#fef2f2;color:#E5363D; }
    .gal-nav-count {
        margin-left:auto;background:#f1f5f9;color:#64748b;
        font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px;min-width:28px;text-align:center;
    }
    .gal-nav-item.active .gal-nav-count { background:#E5363D;color:#fff; }
    /* ── Filter bar ──────────────────────────────────────────── */
    .gal-filter-bar {
        display:flex;align-items:center;justify-content:space-between;
        margin-bottom:24px;flex-wrap:wrap;gap:12px;
    }
    .gal-filter-btns { display:flex;gap:8px;flex-wrap:wrap; }
    .gal-filter-btn {
        background:#fff;border:1.5px solid #e2e8f0;color:#64748b;
        font-size:13px;font-weight:600;padding:7px 18px;border-radius:50px;
        cursor:pointer;transition:all 0.2s;
    }
    .gal-filter-btn.active { background:#E5363D;border-color:#E5363D;color:#fff; }
    .gal-filter-btn:hover:not(.active) { border-color:#E5363D;color:#E5363D; }
    .gal-count-label { font-size:13px;color:#94a3b8;font-weight:500; }
    /* ── Grid ────────────────────────────────────────────────── */
    .gal-grid {
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
        gap:14px;
    }
    .gal-item {
        position:relative;border-radius:14px;overflow:hidden;
        background:#e2e8f0;cursor:pointer;
        aspect-ratio:4/3;
        box-shadow:0 2px 10px rgba(0,0,0,0.06);
        transition:transform 0.25s,box-shadow 0.25s;
    }
    .gal-item:hover { transform:scale(1.02);box-shadow:0 8px 28px rgba(0,0,0,0.12); }
    .gal-item img,.gal-item video {
        width:100%;height:100%;object-fit:cover;display:block;
    }
    .gal-item-overlay {
        position:absolute;inset:0;
        background:linear-gradient(to top,rgba(0,0,0,0.65) 0%,transparent 55%);
        opacity:0;transition:opacity 0.25s;display:flex;flex-direction:column;justify-content:flex-end;
        padding:16px;
    }
    .gal-item:hover .gal-item-overlay { opacity:1; }
    .gal-item-label { color:#fff;font-size:12px;font-weight:600;line-height:1.3; }
    .gal-item-type {
        position:absolute;top:10px;right:10px;
        background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);
        color:#fff;font-size:11px;border-radius:6px;padding:3px 8px;
        font-weight:600;letter-spacing:0.3px;
    }
    .gal-play-icon {
        position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
        width:52px;height:52px;border-radius:50%;
        background:rgba(229,54,61,0.9);color:#fff;
        display:flex;align-items:center;justify-content:center;
        font-size:18px;border:3px solid rgba(255,255,255,0.5);
        transition:transform 0.2s;
    }
    .gal-item:hover .gal-play-icon { transform:translate(-50%,-50%) scale(1.1); }
    .gal-item[data-type="video"][data-hidden="1"],
    .gal-item[data-type="image"][data-hidden="1"] { display:none; }
    /* No results state */
    .gal-empty {
        text-align:center;padding:60px 20px;color:#94a3b8;
        grid-column:1/-1;
    }
    .gal-empty i { font-size:48px;margin-bottom:16px;display:block; }
    /* ── Lightbox ─────────────────────────────────────────────── */
    #galLightbox {
        display:none;position:fixed;inset:0;z-index:99999;
        background:rgba(5,10,20,0.96);
        flex-direction:column;align-items:center;justify-content:center;
    }
    #galLightbox.open { display:flex; }
    .lb-close {
        position:absolute;top:18px;right:18px;
        background:rgba(255,255,255,0.1);border:none;color:#fff;
        font-size:26px;width:46px;height:46px;border-radius:50%;cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        transition:background 0.2s;z-index:2;
    }
    .lb-close:hover { background:rgba(229,54,61,0.8); }
    .lb-nav {
        position:absolute;top:50%;transform:translateY(-50%);
        background:rgba(255,255,255,0.1);border:none;color:#fff;
        font-size:22px;width:52px;height:52px;border-radius:50%;cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        transition:background 0.2s;z-index:2;
    }
    .lb-nav:hover { background:rgba(229,54,61,0.8); }
    #lbPrev { left:16px; }
    #lbNext { right:16px; }
    .lb-media-wrap {
        max-width:90vw;max-height:80vh;display:flex;align-items:center;justify-content:center;
    }
    .lb-media-wrap img {
        max-width:100%;max-height:80vh;object-fit:contain;border-radius:10px;
        box-shadow:0 20px 60px rgba(0,0,0,0.6);
    }
    .lb-media-wrap video {
        max-width:100%;max-height:80vh;border-radius:10px;
        box-shadow:0 20px 60px rgba(0,0,0,0.6);outline:none;
    }
    .lb-caption {
        position:absolute;bottom:18px;left:50%;transform:translateX(-50%);
        background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);
        color:#fff;font-size:13px;font-weight:600;
        padding:8px 20px;border-radius:50px;
        white-space:nowrap;max-width:80vw;overflow:hidden;text-overflow:ellipsis;
    }
    .lb-counter {
        position:absolute;top:18px;left:50%;transform:translateX(-50%);
        background:rgba(0,0,0,0.5);color:rgba(255,255,255,0.7);
        font-size:12px;font-weight:600;padding:5px 14px;border-radius:30px;
    }
    .lb-thumb-strip {
        position:absolute;bottom:60px;left:50%;transform:translateX(-50%);
        display:flex;gap:6px;overflow-x:auto;max-width:80vw;
        padding:6px;
    }
    .lb-thumb-strip::-webkit-scrollbar { height:3px; }
    .lb-thumb-strip::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.3);border-radius:4px; }
    .lb-thumb {
        width:52px;height:38px;border-radius:6px;overflow:hidden;
        flex-shrink:0;cursor:pointer;border:2px solid transparent;
        transition:border-color 0.2s;
    }
    .lb-thumb.active { border-color:#E5363D; }
    .lb-thumb img,.lb-thumb video {
        width:100%;height:100%;object-fit:cover;display:block;
    }
    @media(max-width:600px) {
        .lb-nav { display:none; }
        .lb-thumb-strip { display:none; }
        .lb-caption { font-size:11px; }
    }
    </style>
</head>
<body class="wp-theme-TPV Construction Services elementor-default elementor-kit-7">

<!-- Preloader -->
<div class="preloader">
    <div class="loading-container">
        <div class="loading"></div>
        <div id="loading-icon"><img src="../wp-content/themes/tpv/assets/images/loader.png" alt=""></div>
    </div>
</div>
<div id="magic-cursor"><div id="ball"></div></div>

<!-- Site Header (same as projects/index.php) -->
<div class="ekit-template-content-markup ekit-template-content-header ekit-template-content-theme-support">
    <div data-elementor-type="wp-post" data-elementor-id="225" class="elementor elementor-225">
        <div class="elementor-element elementor-element-3c0e001 e-con-full e-flex e-con e-parent" data-id="3c0e001" data-element_type="container">
            <div class="elementor-element elementor-element-159e7cf e-con-full e-flex e-con e-child" data-id="159e7cf" data-element_type="container">
                <div class="elementor-element elementor-element-08aa86c e-con-full e-flex e-con e-child" data-id="08aa86c" data-element_type="container">
                    <div class="elementor-element elementor-element-d7183c8 elementor-widget elementor-widget-TPV Construction Services-site-logo">
                        <div class="elementor-widget-container">
                            <div class="ata-site-logo">
                                <a href="https://tpvconstruction.com.ng">
                                    <div class="ata-site-logo-set"><div class="ata-site-logo-container">
                                        <img src="<?php echo htmlspecialchars(tpv_setting_asset_url('site_logo', 'wp-content/uploads/2024/06/logo.png')); ?>" alt="TPV Construction" width="150" height="50">
                                    </div></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="elementor-element elementor-element-d936ea6 e-con-full e-flex e-con e-child" data-id="d936ea6" data-element_type="container">
                    <div class="elementor-element elementor-element-d67d018 header-menu elementor-widget elementor-widget-ekit-nav-menu" data-widget_type="ekit-nav-menu.default">
                        <div class="elementor-widget-container">
                            <nav class="ekit-wid-con ekit_menu_responsive_tablet" data-hamburger-icon="icon icon-menu-11" data-hamburger-icon-type="icon" data-responsive-breakpoint="1024">
                                <button class="elementskit-menu-hamburger elementskit-menu-toggler" type="button" aria-label="hamburger-icon">
                                    <i aria-hidden="true" class="ekit-menu-icon icon icon-menu-11"></i>
                                </button>
                                <div id="ekit-megamenu-header-menu" class="elementskit-menu-container elementskit-menu-offcanvas-elements elementskit-navbar-nav-default ekit-nav-menu-one-page- ekit-nav-dropdown-hover">
                                    <ul id="menu-header-menu" class="elementskit-navbar-nav elementskit-menu-po-center submenu-click-on-icon">
                                        <li class="nav-item elementskit-mobile-builder-content"><a href="../" class="ekit-menu-nav-link">Home</a></li>
                                        <li class="nav-item elementskit-mobile-builder-content"><a href="../about-us/" class="ekit-menu-nav-link">About Us</a></li>
                                        <li class="nav-item elementskit-mobile-builder-content"><a href="../services/" class="ekit-menu-nav-link">Services</a></li>
                                        <li class="nav-item active elementskit-mobile-builder-content"><a href="index.php" class="ekit-menu-nav-link active">Projects</a></li>
                                        <li class="nav-item elementskit-mobile-builder-content"><a href="../blog/" class="ekit-menu-nav-link">Blog</a></li>
                                        <li class="nav-item elementskit-mobile-builder-content"><a href="../contact-us/" class="ekit-menu-nav-link">Contact Us</a></li>
                                    </ul>
                                    <div class="elementskit-nav-identity-panel"><button class="elementskit-menu-close elementskit-menu-toggler" type="button">X</button></div>
                                </div>
                                <div class="elementskit-menu-overlay elementskit-menu-offcanvas-elements elementskit-menu-toggler ekit-nav-menu--overlay"></div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="elementor-element elementor-element-1380be6 e-con-full elementor-hidden-tablet elementor-hidden-mobile e-flex e-con e-child">
                    <div class="elementor-widget elementor-widget-button">
                        <div class="elementor-widget-container">
                            <div class="elementor-button-wrapper">
                                <a class="elementor-button elementor-button-link elementor-size-sm" href="../contact-us/">
                                    <span class="elementor-button-content-wrapper"><span class="elementor-button-text">Contact Us</span></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hero -->
<div class="gal-hero">
    <img class="gal-hero-bg" src="<?php echo htmlspecialchars(galMediaUrl($cover['file_path'])); ?>"
         alt="<?php echo htmlspecialchars($service); ?>">
    <div class="gal-hero-overlay"></div>
    <div class="gal-hero-content">
        <div class="gal-breadcrumb">
            <a href="../"><i class="fas fa-home me-1"></i>Home</a>
            <span class="sep">/</span>
            <a href="index.php">Projects</a>
            <span class="sep">/</span>
            <span><?php echo htmlspecialchars($service); ?></span>
        </div>
        <h1 class="gal-hero-title"><?php echo htmlspecialchars($service); ?></h1>
        <div class="gal-hero-meta">
            <?php if ($imgCount): ?>
            <span class="gal-hero-chip"><i class="fas fa-image"></i><?php echo $imgCount; ?> Photo<?php echo $imgCount != 1 ? 's' : ''; ?></span>
            <?php endif; ?>
            <?php if ($vidCount): ?>
            <span class="gal-hero-chip"><i class="fas fa-video"></i><?php echo $vidCount; ?> Video<?php echo $vidCount != 1 ? 's' : ''; ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="gal-wrap">
    <div class="gal-layout">

        <!-- Sidebar navigation -->
        <aside class="gal-sidebar d-none d-lg-block">
            <h6>All Projects</h6>
            <?php foreach ($allServices as $svc): ?>
            <?php $isActive = $svc['service'] === $service; ?>
            <a href="gallery.php?service=<?php echo urlencode($svc['service']); ?>"
               class="gal-nav-item <?php echo $isActive ? 'active' : ''; ?>">
                <i class="fas fa-images" style="width:16px;"></i>
                <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <?php echo htmlspecialchars($svc['service']); ?>
                </span>
                <span class="gal-nav-count"><?php echo $svc['total']; ?></span>
            </a>
            <?php endforeach; ?>
        </aside>

        <!-- Gallery main -->
        <div>
            <!-- Filter bar -->
            <div class="gal-filter-bar">
                <div class="gal-filter-btns">
                    <button class="gal-filter-btn active" data-filter="all">
                        <i class="fas fa-th me-1"></i> All <span style="opacity:0.6;">(<?php echo $total; ?>)</span>
                    </button>
                    <?php if ($imgCount): ?>
                    <button class="gal-filter-btn" data-filter="image">
                        <i class="fas fa-image me-1"></i> Photos <span style="opacity:0.6;">(<?php echo $imgCount; ?>)</span>
                    </button>
                    <?php endif; ?>
                    <?php if ($vidCount): ?>
                    <button class="gal-filter-btn" data-filter="video">
                        <i class="fas fa-video me-1"></i> Videos <span style="opacity:0.6;">(<?php echo $vidCount; ?>)</span>
                    </button>
                    <?php endif; ?>
                </div>
                <a href="index.php" class="gal-count-label" style="text-decoration:none;color:#94a3b8;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Projects
                </a>
            </div>

            <!-- Grid -->
            <div class="gal-grid" id="galGrid">
                <?php foreach ($items as $idx => $media): ?>
                <?php
                $isImg = $media['file_type'] === 'image';
                $url = galMediaUrl($media['file_path']);
                $label = $media['title'] ?: $service;
                ?>
                <div class="gal-item"
                     data-type="<?php echo $isImg ? 'image' : 'video'; ?>"
                     data-index="<?php echo $idx; ?>"
                     data-hidden="0">
                    <?php if ($isImg): ?>
                    <img src="<?php echo htmlspecialchars($url); ?>"
                         alt="<?php echo htmlspecialchars($label); ?>"
                         loading="lazy">
                    <?php else: ?>
                    <video src="<?php echo htmlspecialchars($url); ?>" preload="metadata" muted></video>
                    <div class="gal-play-icon"><i class="fas fa-play"></i></div>
                    <?php endif; ?>
                    <div class="gal-item-overlay">
                        <span class="gal-item-label"><?php echo htmlspecialchars($label); ?></span>
                    </div>
                    <span class="gal-item-type"><?php echo $isImg ? 'Photo' : 'Video'; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="galLightbox">
    <button class="lb-close" id="lbClose"><i class="fas fa-times"></i></button>
    <button class="lb-nav" id="lbPrev"><i class="fas fa-chevron-left"></i></button>
    <button class="lb-nav" id="lbNext"><i class="fas fa-chevron-right"></i></button>
    <div class="lb-counter" id="lbCounter">1 / <?php echo $total; ?></div>
    <div class="lb-media-wrap" id="lbMedia"></div>
    <div id="lbThumbStrip" class="lb-thumb-strip"></div>
    <div class="lb-caption" id="lbCaption"></div>
</div>

<!-- Footer (same structure as projects page) -->
<div class="ekit-template-content-markup ekit-template-content-footer ekit-template-content-theme-support">
    <div data-elementor-type="wp-post" data-elementor-id="1688" class="elementor elementor-1688">
        <div class="elementor-element elementor-element-aac5742 e-flex e-con-boxed e-con e-parent" data-settings='{"background_background":"classic"}'>
            <div class="e-con-inner">
                <div class="elementor-element elementor-element-518b814 e-con-full e-flex e-con e-child">
                    <div class="elementor-element elementor-element-2b12312 e-con-full e-flex e-con e-child">
                        <div class="elementor-widget elementor-widget-image">
                            <div class="elementor-widget-container">
                                <img loading="lazy" width="234" height="78"
                                     src="<?php echo htmlspecialchars(tpv_setting_asset_url('footer_logo', 'wp-content/uploads/2024/06/footer-logo.png')); ?>"
                                     class="attachment-full size-full" alt="">
                            </div>
                        </div>
                        <div class="elementor-widget elementor-widget-text-editor">
                            <div class="elementor-widget-container">
                                <p>Our post-construction services gives you peace of mind knowing that we are still here for you even after.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer bottom -->
<div class="site-info" style="background:#0f172a;padding:18px;text-align:center;font-size:13px;color:#64748b;">
    &copy; <?php echo date('Y'); ?> TPV Construction and Services LTD. All Rights Reserved.
</div>

<script>
// ── Media data for lightbox ──────────────────────────────────────
var galleryData = <?php
$lbData = [];
foreach ($items as $m) {
    $lbData[] = [
        'url' => galMediaUrl($m['file_path']),
        'type' => $m['file_type'],
        'title' => $m['title'] ?: $service
    ];
}
echo json_encode($lbData);
?>;

var visibleData = galleryData.slice(); // filtered subset
var currentIndex = 0;

// ── Filter ───────────────────────────────────────────────────────
document.querySelectorAll('.gal-filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.gal-filter-btn').forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        var filter = this.getAttribute('data-filter');
        var items = document.querySelectorAll('.gal-item');
        visibleData = [];
        items.forEach(function(item) {
            var type = item.getAttribute('data-type');
            var show = (filter === 'all' || filter === type);
            item.setAttribute('data-hidden', show ? '0' : '1');
            item.style.display = show ? '' : 'none';
            if (show) {
                var i = parseInt(item.getAttribute('data-index'));
                visibleData.push(galleryData[i]);
            }
        });
    });
});

// ── Open lightbox ────────────────────────────────────────────────
document.querySelectorAll('.gal-item').forEach(function(item) {
    item.addEventListener('click', function() {
        var rawIndex = parseInt(this.getAttribute('data-index'));
        // Find position in visibleData
        var pos = visibleData.findIndex(function(d) { return d.url === galleryData[rawIndex].url; });
        if (pos < 0) pos = 0;
        openLightbox(pos);
    });
});

function openLightbox(pos) {
    if (!visibleData.length) return;
    currentIndex = Math.max(0, Math.min(pos, visibleData.length - 1));
    renderLightbox();
    document.getElementById('galLightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    var lb = document.getElementById('galLightbox');
    lb.classList.remove('open');
    document.body.style.overflow = '';
    var v = lb.querySelector('video');
    if (v) v.pause();
}

function renderLightbox() {
    var d = visibleData[currentIndex];
    var mediaWrap = document.getElementById('lbMedia');
    var caption = document.getElementById('lbCaption');
    var counter = document.getElementById('lbCounter');

    // Stop any playing video
    var oldVid = mediaWrap.querySelector('video');
    if (oldVid) oldVid.pause();

    mediaWrap.innerHTML = '';
    if (d.type === 'image') {
        var img = document.createElement('img');
        img.src = d.url;
        img.alt = d.title;
        mediaWrap.appendChild(img);
    } else {
        var vid = document.createElement('video');
        vid.src = d.url;
        vid.controls = true;
        vid.autoplay = true;
        vid.style.maxWidth = '90vw';
        vid.style.maxHeight = '80vh';
        mediaWrap.appendChild(vid);
    }
    caption.textContent = d.title;
    counter.textContent = (currentIndex + 1) + ' / ' + visibleData.length;
    renderThumbs();
}

function renderThumbs() {
    var strip = document.getElementById('lbThumbStrip');
    strip.innerHTML = '';
    visibleData.forEach(function(d, i) {
        var thumb = document.createElement('div');
        thumb.className = 'lb-thumb' + (i === currentIndex ? ' active' : '');
        if (d.type === 'image') {
            var img = document.createElement('img');
            img.src = d.url;
            thumb.appendChild(img);
        } else {
            var vid = document.createElement('video');
            vid.src = d.url;
            vid.preload = 'metadata';
            vid.muted = true;
            thumb.appendChild(vid);
        }
        thumb.addEventListener('click', function() { openLightbox(i); });
        strip.appendChild(thumb);
    });
    // Scroll active thumb into view
    var activeThumb = strip.children[currentIndex];
    if (activeThumb) activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
}

document.getElementById('lbClose').addEventListener('click', closeLightbox);
document.getElementById('lbPrev').addEventListener('click', function() {
    currentIndex = (currentIndex - 1 + visibleData.length) % visibleData.length;
    renderLightbox();
});
document.getElementById('lbNext').addEventListener('click', function() {
    currentIndex = (currentIndex + 1) % visibleData.length;
    renderLightbox();
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (!document.getElementById('galLightbox').classList.contains('open')) return;
    if (e.key === 'ArrowLeft') document.getElementById('lbPrev').click();
    if (e.key === 'ArrowRight') document.getElementById('lbNext').click();
    if (e.key === 'Escape') closeLightbox();
});

// Click outside media to close
document.getElementById('galLightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});

// Swipe support
var touchStartX = 0;
document.getElementById('galLightbox').addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
}, { passive: true });
document.getElementById('galLightbox').addEventListener('touchend', function(e) {
    var diff = touchStartX - e.changedTouches[0].screenX;
    if (Math.abs(diff) > 50) {
        if (diff > 0) document.getElementById('lbNext').click();
        else document.getElementById('lbPrev').click();
    }
}, { passive: true });
</script>

<!-- Theme scripts (preloader, cursor, ElementsKit) -->
<script src="../wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/js/ekits.icon-pack.min.js?ver=3.7.9"></script>
<script src="../wp-content/themes/tpv/assets/js/bootstrap.min.js?ver=1.0.8"></script>
<script src="../wp-content/themes/tpv/assets/js/magic-cursor.js?ver=1.0.8"></script>
<script src="../wp-content/themes/tpv/assets/js/custom.js?ver=1.0.9"></script>
<script src="../wp-content/plugins/elementskit-lite/modules/megamenu/assets/js/elementskit-megamenu-script.js?ver=3.7.9"></script>

</body>
</html>
