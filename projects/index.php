<?php
require_once dirname(__DIR__) . '/config/config.php';
$db = Database::getInstance();
$allMedia = $db->query(
    "SELECT * FROM project_media ORDER BY service, file_type, featured DESC, created_at DESC"
)->fetchAll();
$grouped = [];
foreach ($allMedia as $item) {
    $grouped[$item['service']][] = $item;
}
if (!function_exists('galMediaUrl')) {
    function galMediaUrl($path) {
        if (!$path) return '';
        if (strpos($path, '/') === 0) $path = preg_replace('#^.*?uploads/#', '', $path);
        return UPLOAD_URL . $path;
    }
}
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Projects &ndash; TPV Construction and Services LTD</title>
	<meta name="description" content="Explore our portfolio of completed construction projects across Nigeria.">
	<meta name="robots" content="max-image-preview:large">
	<link rel="dns-prefetch" href="//fonts.googleapis.com">
	<link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="32x32">
	<link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="192x192">
	<link rel="apple-touch-icon" href="../wp-content/uploads/2024/06/favicon.png">

	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

	<!-- Theme & plugin CSS -->
	<link rel="stylesheet" href="../wp-content/plugins/contact-form-7/includes/css/styles.css?ver=6.1.5">
	<link rel="stylesheet" href="../wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9">
	<link rel="stylesheet" href="../wp-content/plugins/elementskit/widgets/init/assets/css/widget-styles-pro.css?ver=4.2.1">
	<link rel="stylesheet" href="../wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9">
	<link rel="stylesheet" href="../wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/css/ekiticons.css?ver=3.7.9">
	<link rel="stylesheet" href="../wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8">
	<link rel="stylesheet" href="../wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8">
	<link rel="stylesheet" href="../wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8">
	<link rel="stylesheet" href="../wp-content/themes/tpv/style.css?ver=1.0.8">

	<!-- Elementor core + per-post CSS -->
	<link rel="stylesheet" href="../wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3">
	<link rel="stylesheet" href="../wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.35.3">
	<link rel="stylesheet" href="../wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.35.3">
	<link rel="stylesheet" href="../wp-content/uploads/elementor/css/post-7.css?ver=1770715450">
	<link rel="stylesheet" href="../wp-content/uploads/elementor/css/post-225.css?ver=1770715449">
	<link rel="stylesheet" href="../wp-content/uploads/elementor/css/post-1688.css?ver=1770715449">
	<link rel="stylesheet" href="../wp-content/uploads/elementor/google-fonts/css/manrope.css?ver=1744107304">
	<link rel="stylesheet" href="../wp-content/uploads/elementor/google-fonts/css/dmsans.css?ver=1744107305">

	<!-- jQuery -->
	<script src="../wp-includes/js/jquery/jquery.min.js?ver=3.7.1"></script>
	<script src="../wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1"></script>

	<script type="text/javascript">var elementskit_module_parallax_url = "https://tpvconstruction.com.ng/wp-content/plugins/elementskit/modules/parallax/";</script>
</head>

	<link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Projects Feed"
		href="feed/">
	<style id='wp-img-auto-sizes-contain-inline-css'>
		img:is([sizes=auto i], [sizes^="auto," i]) {
			contain-intrinsic-size: 3000px 1500px
		}

		/*# sourceURL=wp-img-auto-sizes-contain-inline-css */
	</style>
	<style id='wp-emoji-styles-inline-css'>
		img.wp-smiley,
		img.emoji {
			display: inline !important;
			border: none !important;
			box-shadow: none !important;
			height: 1em !important;
			width: 1em !important;
			margin: 0 0.07em !important;
			vertical-align: -0.1em !important;
			background: none !important;
			padding: 0 !important;
		}

		/*# sourceURL=wp-emoji-styles-inline-css */
	</style>
	<style id='classic-theme-styles-inline-css'>
		/*! This file is auto-generated */
		.wp-block-button__link {
			color: #fff;
			background-color: #32373c;
			border-radius: 9999px;
			box-shadow: none;
			text-decoration: none;
			padding: calc(.667em + 2px) calc(1.333em + 2px);
			font-size: 1.125em
		}

		.wp-block-file__button {
			background: #32373c;
			color: #fff;
			text-decoration: none
		}

		/*# sourceURL=/wp-includes/css/classic-themes.min.css */
	</style>
	<style id='global-styles-inline-css'>
		:root {
			--wp--preset--aspect-ratio--square: 1;
			--wp--preset--aspect-ratio--4-3: 4/3;
			--wp--preset--aspect-ratio--3-4: 3/4;
			--wp--preset--aspect-ratio--3-2: 3/2;
			--wp--preset--aspect-ratio--2-3: 2/3;
			--wp--preset--aspect-ratio--16-9: 16/9;
			--wp--preset--aspect-ratio--9-16: 9/16;
			--wp--preset--color--black: #000000;
			--wp--preset--color--cyan-bluish-gray: #abb8c3;
			--wp--preset--color--white: #ffffff;
			--wp--preset--color--pale-pink: #f78da7;
			--wp--preset--color--vivid-red: #cf2e2e;
			--wp--preset--color--luminous-vivid-orange: #ff6900;
			--wp--preset--color--luminous-vivid-amber: #fcb900;
			--wp--preset--color--light-green-cyan: #7bdcb5;
			--wp--preset--color--vivid-green-cyan: #00d084;
			--wp--preset--color--pale-cyan-blue: #8ed1fc;
			--wp--preset--color--vivid-cyan-blue: #0693e3;
			--wp--preset--color--vivid-purple: #9b51e0;
			--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg, rgb(6, 147, 227) 0%, rgb(155, 81, 224) 100%);
			--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg, rgb(122, 220, 180) 0%, rgb(0, 208, 130) 100%);
			--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg, rgb(252, 185, 0) 0%, rgb(255, 105, 0) 100%);
			--wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg, rgb(255, 105, 0) 0%, rgb(207, 46, 46) 100%);
			--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg, rgb(238, 238, 238) 0%, rgb(169, 184, 195) 100%);
			--wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg, rgb(74, 234, 220) 0%, rgb(151, 120, 209) 20%, rgb(207, 42, 186) 40%, rgb(238, 44, 130) 60%, rgb(251, 105, 98) 80%, rgb(254, 248, 76) 100%);
			--wp--preset--gradient--blush-light-purple: linear-gradient(135deg, rgb(255, 206, 236) 0%, rgb(152, 150, 240) 100%);
			--wp--preset--gradient--blush-bordeaux: linear-gradient(135deg, rgb(254, 205, 165) 0%, rgb(254, 45, 45) 50%, rgb(107, 0, 62) 100%);
			--wp--preset--gradient--luminous-dusk: linear-gradient(135deg, rgb(255, 203, 112) 0%, rgb(199, 81, 192) 50%, rgb(65, 88, 208) 100%);
			--wp--preset--gradient--pale-ocean: linear-gradient(135deg, rgb(255, 245, 203) 0%, rgb(182, 227, 212) 50%, rgb(51, 167, 181) 100%);
			--wp--preset--gradient--electric-grass: linear-gradient(135deg, rgb(202, 248, 128) 0%, rgb(113, 206, 126) 100%);
			--wp--preset--gradient--midnight: linear-gradient(135deg, rgb(2, 3, 129) 0%, rgb(40, 116, 252) 100%);
			--wp--preset--font-size--small: 13px;
			--wp--preset--font-size--medium: 20px;
			--wp--preset--font-size--large: 36px;
			--wp--preset--font-size--x-large: 42px;
			--wp--preset--spacing--20: 0.44rem;
			--wp--preset--spacing--30: 0.67rem;
			--wp--preset--spacing--40: 1rem;
			--wp--preset--spacing--50: 1.5rem;
			--wp--preset--spacing--60: 2.25rem;
			--wp--preset--spacing--70: 3.38rem;
			--wp--preset--spacing--80: 5.06rem;
			--wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);
			--wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);
			--wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);
			--wp--preset--shadow--outlined: 6px 6px 0px -3px rgb(255, 255, 255), 6px 6px rgb(0, 0, 0);
			--wp--preset--shadow--crisp: 6px 6px 0px rgb(0, 0, 0);
		}

		:where(.is-layout-flex) {
			gap: 0.5em;
		}

		:where(.is-layout-grid) {
			gap: 0.5em;
		}

		body .is-layout-flex {
			display: flex;
		}

		.is-layout-flex {
			flex-wrap: wrap;
			align-items: center;
		}

		.is-layout-flex> :is(*, div) {
			margin: 0;
		}

		body .is-layout-grid {
			display: grid;
		}

		.is-layout-grid> :is(*, div) {
			margin: 0;
		}

		:where(.wp-block-columns.is-layout-flex) {
			gap: 2em;
		}

		:where(.wp-block-columns.is-layout-grid) {
			gap: 2em;
		}

		:where(.wp-block-post-template.is-layout-flex) {
			gap: 1.25em;
		}

		:where(.wp-block-post-template.is-layout-grid) {
			gap: 1.25em;
		}

		.has-black-color {
			color: var(--wp--preset--color--black) !important;
		}

		.has-cyan-bluish-gray-color {
			color: var(--wp--preset--color--cyan-bluish-gray) !important;
		}

		.has-white-color {
			color: var(--wp--preset--color--white) !important;
		}

		.has-pale-pink-color {
			color: var(--wp--preset--color--pale-pink) !important;
		}

		.has-vivid-red-color {
			color: var(--wp--preset--color--vivid-red) !important;
		}

		.has-luminous-vivid-orange-color {
			color: var(--wp--preset--color--luminous-vivid-orange) !important;
		}

		.has-luminous-vivid-amber-color {
			color: var(--wp--preset--color--luminous-vivid-amber) !important;
		}

		.has-light-green-cyan-color {
			color: var(--wp--preset--color--light-green-cyan) !important;
		}

		.has-vivid-green-cyan-color {
			color: var(--wp--preset--color--vivid-green-cyan) !important;
		}

		.has-pale-cyan-blue-color {
			color: var(--wp--preset--color--pale-cyan-blue) !important;
		}

		.has-vivid-cyan-blue-color {
			color: var(--wp--preset--color--vivid-cyan-blue) !important;
		}

		.has-vivid-purple-color {
			color: var(--wp--preset--color--vivid-purple) !important;
		}

		.has-black-background-color {
			background-color: var(--wp--preset--color--black) !important;
		}

		.has-cyan-bluish-gray-background-color {
			background-color: var(--wp--preset--color--cyan-bluish-gray) !important;
		}

		.has-white-background-color {
			background-color: var(--wp--preset--color--white) !important;
		}

		.has-pale-pink-background-color {
			background-color: var(--wp--preset--color--pale-pink) !important;
		}

		.has-vivid-red-background-color {
			background-color: var(--wp--preset--color--vivid-red) !important;
		}

		.has-luminous-vivid-orange-background-color {
			background-color: var(--wp--preset--color--luminous-vivid-orange) !important;
		}

		.has-luminous-vivid-amber-background-color {
			background-color: var(--wp--preset--color--luminous-vivid-amber) !important;
		}

		.has-light-green-cyan-background-color {
			background-color: var(--wp--preset--color--light-green-cyan) !important;
		}

		.has-vivid-green-cyan-background-color {
			background-color: var(--wp--preset--color--vivid-green-cyan) !important;
		}

		.has-pale-cyan-blue-background-color {
			background-color: var(--wp--preset--color--pale-cyan-blue) !important;
		}

		.has-vivid-cyan-blue-background-color {
			background-color: var(--wp--preset--color--vivid-cyan-blue) !important;
		}

		.has-vivid-purple-background-color {
			background-color: var(--wp--preset--color--vivid-purple) !important;
		}

		.has-black-border-color {
			border-color: var(--wp--preset--color--black) !important;
		}

		.has-cyan-bluish-gray-border-color {
			border-color: var(--wp--preset--color--cyan-bluish-gray) !important;
		}

		.has-white-border-color {
			border-color: var(--wp--preset--color--white) !important;
		}

		.has-pale-pink-border-color {
			border-color: var(--wp--preset--color--pale-pink) !important;
		}

		.has-vivid-red-border-color {
			border-color: var(--wp--preset--color--vivid-red) !important;
		}

		.has-luminous-vivid-orange-border-color {
			border-color: var(--wp--preset--color--luminous-vivid-orange) !important;
		}

		.has-luminous-vivid-amber-border-color {
			border-color: var(--wp--preset--color--luminous-vivid-amber) !important;
		}

		.has-light-green-cyan-border-color {
			border-color: var(--wp--preset--color--light-green-cyan) !important;
		}

		.has-vivid-green-cyan-border-color {
			border-color: var(--wp--preset--color--vivid-green-cyan) !important;
		}

		.has-pale-cyan-blue-border-color {
			border-color: var(--wp--preset--color--pale-cyan-blue) !important;
		}

		.has-vivid-cyan-blue-border-color {
			border-color: var(--wp--preset--color--vivid-cyan-blue) !important;
		}

		.has-vivid-purple-border-color {
			border-color: var(--wp--preset--color--vivid-purple) !important;
		}

		.has-vivid-cyan-blue-to-vivid-purple-gradient-background {
			background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;
		}

		.has-light-green-cyan-to-vivid-green-cyan-gradient-background {
			background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;
		}

		.has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
			background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;
		}

		.has-luminous-vivid-orange-to-vivid-red-gradient-background {
			background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;
		}

		.has-very-light-gray-to-cyan-bluish-gray-gradient-background {
			background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;
		}

		.has-cool-to-warm-spectrum-gradient-background {
			background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;
		}

		.has-blush-light-purple-gradient-background {
			background: var(--wp--preset--gradient--blush-light-purple) !important;
		}

		.has-blush-bordeaux-gradient-background {
			background: var(--wp--preset--gradient--blush-bordeaux) !important;
		}

		.has-luminous-dusk-gradient-background {
			background: var(--wp--preset--gradient--luminous-dusk) !important;
		}

		.has-pale-ocean-gradient-background {
			background: var(--wp--preset--gradient--pale-ocean) !important;
		}

		.has-electric-grass-gradient-background {
			background: var(--wp--preset--gradient--electric-grass) !important;
		}

		.has-midnight-gradient-background {
			background: var(--wp--preset--gradient--midnight) !important;
		}

		.has-small-font-size {
			font-size: var(--wp--preset--font-size--small) !important;
		}

		.has-medium-font-size {
			font-size: var(--wp--preset--font-size--medium) !important;
		}

		.has-large-font-size {
			font-size: var(--wp--preset--font-size--large) !important;
		}

		.has-x-large-font-size {
			font-size: var(--wp--preset--font-size--x-large) !important;
		}

		:where(.wp-block-post-template.is-layout-flex) {
			gap: 1.25em;
		}

		:where(.wp-block-post-template.is-layout-grid) {
			gap: 1.25em;
		}

		:where(.wp-block-term-template.is-layout-flex) {
			gap: 1.25em;
		}

		:where(.wp-block-term-template.is-layout-grid) {
			gap: 1.25em;
		}

		:where(.wp-block-columns.is-layout-flex) {
			gap: 2em;
		}

		:where(.wp-block-columns.is-layout-grid) {
			gap: 2em;
		}

		:root :where(.wp-block-pullquote) {
			font-size: 1.5em;
			line-height: 1.6;
		}

		/*# sourceURL=global-styles-inline-css */
	</style>
	<link rel='stylesheet' id='contact-form-7-css'
		href='../wp-content/plugins/contact-form-7/includes/css/styles.css?ver=6.1.5' media='all'>
	<link rel='stylesheet' id='ekit-widget-styles-css'
		href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9' media='all'>
	<link rel='stylesheet' id='ekit-widget-styles-pro-css'
		href='../wp-content/plugins/elementskit/widgets/init/assets/css/widget-styles-pro.css?ver=4.2.1' media='all'>
	<link rel='stylesheet' id='ekit-responsive-css'
		href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9' media='all'>
	<link rel='stylesheet' id='TPV Construction Services-font-css'
		href='../../css2?family=Manrope%3Awght%40200..800&#038;family=DM+Sans%3Aital%2Copsz%2Cwght%400%2C9..40%2C100..1000%3B1%2C9..40%2C100..1000&#038;display=swap'
		media='all'>
	<link rel='stylesheet' id='TPV Construction Services-css-variable-css'
		href='../wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='fontawesome-6.4.0-css' href='../wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8'
		media='all'>
	<link rel='stylesheet' id='bootstrap-5.3.2-css'
		href='../wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='TPV Construction Services-style-css' href='../wp-content/themes/tpv/style.css?ver=1.0.8'
		media='all'>
	<script src="../wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
	<script src="../wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
	<link rel="https://api.w.org/" href="../wp-json/">
	<link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://tpvconstruction.com.ng/xmlrpc.php?rsd">

	<meta name="generator"
		content="Elementor 3.35.3; features: e_font_icon_svg, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap">
	<script
		type="text/javascript">var elementskit_module_parallax_url = "https://tpvconstruction.com.ng/wp-content/plugins/elementskit/modules/parallax/";</script>
	<style>
		.e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload),
		.e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload) * {
			background-image: none !important;
		}

		@media screen and (max-height: 1024px) {

			.e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload),
			.e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload) * {
				background-image: none !important;
			}
		}

		@media screen and (max-height: 640px) {

			.e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload),
			.e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload) * {
				background-image: none !important;
			}
		}
	</style>
	<link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="32x32">
	<link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="192x192">
	<link rel="apple-touch-icon" href="../wp-content/uploads/2024/06/favicon.png">
	<meta name="msapplication-TileImage"
		content="https://tpvconstrcution.com.ng/wp-content/uploads/2024/06/favicon.png">
</head>

<body
	class="archive post-type-archive post-type-archive-awaiken-portfolio wp-custom-logo wp-theme-TPV Construction Services tt-magic-cursor elementor-default elementor-kit-7">

	
	<div id="magic-cursor">
		<div id="ball"></div>
	</div>

	<a class="skip-link screen-reader-text" href="#content">Skip to content</a>

	<?php include '../includes/header.php'; ?>

	<main id="content" class="site-main">
		<div class="page-header" style="background-image: url('../wp-content/uploads/2024/06/page-header-bg.jpg')">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-12">
						<div class="page-header-box">
							<h1 class="entry-title">Our Portfolio</h1>
							<div role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs">
								<ol class="trail-items">
									<li class="trail-item trail-begin"><a href="../" rel="home"><span>Home</span></a></li>
									<li class="trail-item trail-end"><span>Projects</span></li>
								</ol>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if (!empty($grouped)): ?>
		<section class="gallery-section">
			<div class="container">
				<div class="section-heading text-center">
					<span class="section-subtitle">our gallery</span>
					<h2 class="section-title">Project Photo Gallery</h2>
					<p class="section-desc">Browse our completed projects and see the quality of our work firsthand. Hover over any project to explore all associated images and videos.</p>
				</div>

				<div class="gallery-grid">
					<?php foreach ($grouped as $service => $items): ?>
					<?php
					$cover = null;
					foreach ($items as $_m) { if ($_m['file_type'] === 'image') { $cover = $_m; break; } }
					if (!$cover) $cover = $items[0];
					$total    = count($items);
					$imgCount = count(array_filter($items, fn($m) => $m['file_type'] === 'image'));
					$vidCount = $total - $imgCount;
					$galleryUrl = 'gallery.php?service=' . urlencode($service);
					$preview = array_filter($items, fn($m) => $m['file_type'] === 'image');
					$preview = array_slice($preview, 0, 3);
					if (count($preview) < 3) $preview = array_slice($items, 0, 3);
					?>
					<a href="<?php echo htmlspecialchars($galleryUrl); ?>" class="gallery-card-link">
						<div class="gallery-card">
							<div class="gallery-card-image">
								<img src="<?php echo htmlspecialchars(galMediaUrl($cover['file_path'])); ?>"
								     alt="<?php echo htmlspecialchars($service); ?>" loading="lazy">
								<div class="gallery-card-view-overlay">
									<span class="gallery-view-btn"><i class="fas fa-images me-2"></i>View Gallery</span>
								</div>
								<div class="gallery-card-stats">
									<span><i class="fas fa-image"></i> <?php echo $imgCount; ?></span>
									<?php if ($vidCount > 0): ?>
									<span><i class="fas fa-video"></i> <?php echo $vidCount; ?></span>
									<?php endif; ?>
								</div>
							</div>
							<?php if (count($preview) > 1): ?>
							<div class="gallery-card-strip">
								<?php foreach (array_slice($preview, 1, 2) as $_p): ?>
								<div class="gallery-strip-thumb">
									<?php if ($_p['file_type'] === 'image'): ?>
									<img src="<?php echo htmlspecialchars(galMediaUrl($_p['file_path'])); ?>" alt="" loading="lazy">
									<?php else: ?>
									<video src="<?php echo htmlspecialchars(galMediaUrl($_p['file_path'])); ?>" muted preload="metadata"></video>
									<span class="gallery-strip-play"><i class="fas fa-play"></i></span>
									<?php endif; ?>
								</div>
								<?php endforeach; ?>
								<?php if ($total > 3): ?>
								<div class="gallery-strip-more">+<?php echo $total - 3; ?></div>
								<?php endif; ?>
							</div>
							<?php endif; ?>
							<div class="gallery-card-body">
								<div>
									<h3 class="gallery-card-title"><?php echo htmlspecialchars($service); ?></h3>
									<span class="gallery-card-count">
										<i class="fas fa-image me-1"></i><?php echo $imgCount; ?> photo<?php echo $imgCount != 1 ? 's' : ''; ?>
										<?php if ($vidCount): ?>&nbsp;&bull;&nbsp;<i class="fas fa-video me-1"></i><?php echo $vidCount; ?> video<?php echo $vidCount != 1 ? 's' : ''; ?><?php endif; ?>
									</span>
								</div>
								<span class="gallery-card-arrow"><i class="fas fa-arrow-right"></i></span>
							</div>
						</div>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<style>
		.gallery-section { padding:100px 0;background:#f8fafc; }
		.section-heading { margin-bottom:56px; }
		.section-subtitle {
			display:inline-block;font-size:13px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;
			color:#E5363D;margin-bottom:14px;position:relative;
		}
		.section-subtitle::after {
			content:'';display:block;width:40px;height:3px;background:#E5363D;
			margin:10px auto 0;border-radius:2px;
		}
		.section-title { font-size:38px;font-weight:800;color:#0f172a;margin:0 0 14px; }
		.section-desc { max-width:580px;margin:0 auto;color:#64748b;font-size:16px;line-height:1.7; }

		.gallery-grid {
			display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:28px;
		}
		.gallery-card-link { text-decoration:none;display:block; }
		.gallery-card {
			background:#fff;border-radius:18px;overflow:hidden;
			box-shadow:0 4px 20px rgba(0,0,0,0.06);
			transition:transform 0.32s,box-shadow 0.32s;
		}
		.gallery-card-link:hover .gallery-card {
			transform:translateY(-6px);box-shadow:0 16px 48px rgba(0,0,0,0.13);
		}
		/* Cover image */
		.gallery-card-image {
			position:relative;aspect-ratio:16/11;overflow:hidden;background:#e2e8f0;
		}
		.gallery-card-image img {
			width:100%;height:100%;object-fit:cover;display:block;
			transition:transform 0.55s cubic-bezier(0.25,0.46,0.45,0.94);
		}
		.gallery-card-link:hover .gallery-card-image img { transform:scale(1.07); }
		.gallery-card-view-overlay {
			position:absolute;inset:0;
			background:rgba(10,16,30,0.42);
			display:flex;align-items:center;justify-content:center;
			opacity:0;transition:opacity 0.3s;
		}
		.gallery-card-link:hover .gallery-card-view-overlay { opacity:1; }
		.gallery-view-btn {
			background:#E5363D;color:#fff;font-size:14px;font-weight:700;
			padding:11px 24px;border-radius:50px;letter-spacing:0.3px;
			box-shadow:0 4px 16px rgba(229,54,61,0.45);
		}
		.gallery-card-stats {
			position:absolute;top:13px;right:13px;display:flex;gap:6px;z-index:3;
		}
		.gallery-card-stats span {
			background:rgba(0,0,0,0.52);backdrop-filter:blur(4px);
			color:#fff;font-size:12px;font-weight:600;padding:4px 10px;
			border-radius:20px;display:flex;align-items:center;gap:5px;
		}
		/* Thumbnail strip */
		.gallery-card-strip {
			display:grid;grid-template-columns:1fr 1fr 1fr;gap:3px;height:78px;
		}
		.gallery-strip-thumb {
			position:relative;overflow:hidden;background:#cbd5e1;
		}
		.gallery-strip-thumb img,.gallery-strip-thumb video {
			width:100%;height:100%;object-fit:cover;display:block;
		}
		.gallery-strip-play {
			position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
			background:rgba(0,0,0,0.35);color:#fff;font-size:11px;
		}
		.gallery-strip-more {
			display:flex;align-items:center;justify-content:center;
			background:#1e293b;color:#fff;font-size:18px;font-weight:800;
		}
		/* Info row */
		.gallery-card-body {
			padding:16px 20px;display:flex;align-items:center;justify-content:space-between;
			border-top:1px solid #f1f5f9;
		}
		.gallery-card-title { font-size:16px;font-weight:700;color:#0f172a;margin:0 0 2px; }
		.gallery-card-count { font-size:12px;color:#94a3b8;font-weight:500;margin:0; }
		.gallery-card-arrow {
			width:36px;height:36px;border-radius:50%;
			background:#fef2f2;color:#E5363D;
			display:flex;align-items:center;justify-content:center;
			flex-shrink:0;font-size:14px;
			transition:background 0.2s,color 0.2s;
		}
		.gallery-card-link:hover .gallery-card-arrow { background:#E5363D;color:#fff; }

		@media (max-width:768px) {
			.gallery-grid { grid-template-columns:1fr;gap:18px; }
			.section-title { font-size:28px; }
			.gallery-section { padding:60px 0; }
		}
		</style>
		<?php else: ?>
		<section class="gallery-section">
			<div class="container">
				<div class="text-center py-5">
					<i class="fas fa-images fa-3x text-muted mb-3"></i>
					<p class="text-muted">No gallery media available yet.</p>
				</div>
			</div>
		</section>
		<?php endif; ?>
	</main>

	<?php include '../includes/footer.php'; ?>
</body>
</html>