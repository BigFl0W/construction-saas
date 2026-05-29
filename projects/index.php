<?php
require_once dirname(__DIR__) . '/config/config.php';
$db = Database::getInstance();
if (!function_exists('galMediaUrl')) {
    function galMediaUrl($path) {
        if (!$path) return '';
        if (strpos($path, '/') === 0) $path = preg_replace('#^.*?uploads/#', '', $path);
        return UPLOAD_URL . $path;
    }
}
if (!function_exists('galInitials')) {
    function galInitials($label) {
        $label = trim((string) $label);
        if ($label === '') {
            return 'PM';
        }
        $parts = preg_split('/\s+/', $label);
        $initials = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }
        return $initials ?: 'PM';
    }
}
if (!function_exists('galSortMediaItems')) {
    function galSortMediaItems(array $items): array {
        usort($items, function ($a, $b) {
            $scoreA = (!empty($a['featured']) ? 100 : 0) + (($a['file_type'] ?? '') === 'image' ? 10 : 0) + (int) strtotime((string) ($a['created_at'] ?? ''));
            $scoreB = (!empty($b['featured']) ? 100 : 0) + (($b['file_type'] ?? '') === 'image' ? 10 : 0) + (int) strtotime((string) ($b['created_at'] ?? ''));
            return $scoreB <=> $scoreA;
        });

        return $items;
    }
}
$allMedia = $db->query(
    "SELECT * FROM project_media ORDER BY service, file_type, featured DESC, created_at DESC"
)->fetchAll();
$grouped = [];
foreach ($allMedia as $item) {
    $grouped[$item['service']][] = $item;
}
$requestedGalleryService = trim($_GET['service'] ?? '');
$selectedCollection = trim($_GET['collection'] ?? '');
$serviceCollections = [];
$galleryPayload = [];
foreach ($grouped as $serviceName => $items) {
    $orderedItems = galSortMediaItems($items);
    $previewItems = array_values(array_filter($orderedItems, function ($mediaItem) {
        return ($mediaItem['file_type'] ?? 'image') === 'image';
    }));
    $previewItems = array_slice($previewItems, 0, 3);
    if (count($previewItems) < 3) {
        $previewItems = array_slice($orderedItems, 0, 3);
    }
    $cover = $previewItems[0] ?? ($orderedItems[0] ?? null);
    $cardMeta = null;
    foreach ($orderedItems as $_mediaItem) {
        if (trim((string) ($_mediaItem['title'] ?? '')) !== '' || trim((string) ($_mediaItem['description'] ?? '')) !== '') {
            $cardMeta = $_mediaItem;
            break;
        }
    }
    if (!$cardMeta) {
        $cardMeta = $cover ?: ($orderedItems[0] ?? null);
    }
    $serviceCollections[$serviceName] = [
        'items' => $orderedItems,
        'preview' => $previewItems,
        'cover' => $cover,
        'meta' => $cardMeta,
        'count' => count($orderedItems),
        'images' => count(array_filter($orderedItems, function ($mediaItem) {
            return ($mediaItem['file_type'] ?? 'image') === 'image';
        })),
        'videos' => count(array_filter($orderedItems, function ($mediaItem) {
            return ($mediaItem['file_type'] ?? 'image') === 'video';
        })),
    ];
    $galleryPayload[$serviceName] = array_map(function ($mediaItem) {
        return [
            'type' => $mediaItem['file_type'] === 'video' ? 'video' : 'image',
            'url' => galMediaUrl($mediaItem['file_path'] ?? ''),
            'title' => $mediaItem['title'] ?? '',
            'description' => $mediaItem['description'] ?? '',
            'service' => $mediaItem['service'] ?? '',
        ];
    }, $orderedItems);
}
$selectedCollection = ($selectedCollection !== '' && isset($serviceCollections[$selectedCollection])) ? $selectedCollection : '';
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

	<?php include '../includes/quote_header.php'; ?>

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

				<div class="gallery-toolbar">
					<div class="gallery-filter-chips" role="tablist" aria-label="Filter project collections">
						<a class="gallery-filter-chip <?php echo $selectedCollection === '' ? 'is-active' : ''; ?>" href="index.php">All Services</a>
						<?php foreach ($serviceCollections as $serviceName => $collection): ?>
						<a class="gallery-filter-chip <?php echo $selectedCollection === $serviceName ? 'is-active' : ''; ?>" href="?collection=<?php echo urlencode($serviceName); ?>">
							<?php echo htmlspecialchars($serviceName); ?>
							<span><?php echo (int) $collection['count']; ?></span>
						</a>
						<?php endforeach; ?>
					</div>
					<p class="gallery-toolbar-note">Each service is presented as a complete collection. Open any card to browse every image and video related to that service.</p>
				</div>

				<div class="gallery-grid">
					<?php foreach ($serviceCollections as $service => $collection): ?>
					<?php if ($selectedCollection !== '' && $selectedCollection !== $service) continue; ?>
					<?php
					$total    = (int) $collection['count'];
					$imgCount = (int) $collection['images'];
					$vidCount = (int) $collection['videos'];
					$cover    = $collection['cover'] ?? null;
					$meta     = $collection['meta'] ?? null;
					$cardMediaTitle = trim((string) ($meta['title'] ?? ''));
					$cardMediaDescription = trim((string) ($meta['description'] ?? ''));
					$preview = $collection['preview'] ?? [];
					?>
					<button type="button"
					        class="gallery-card-link js-gallery-open"
					        data-gallery-service="<?php echo htmlspecialchars($service); ?>"
					        aria-label="Open <?php echo htmlspecialchars($service); ?> media collection">
						<div class="gallery-card">
							<div class="gallery-card-head">
								<div class="gallery-card-head__copy">
									<span class="gallery-card-eyebrow">Service collection</span>
									<h3 class="gallery-card-title"><?php echo htmlspecialchars($service); ?></h3>
									<p class="gallery-card-summary">
										<?php echo htmlspecialchars($cardMediaDescription !== '' ? $cardMediaDescription : 'Browse curated images and videos from this service portfolio.'); ?>
									</p>
								</div>
								<span class="gallery-card-pill"><?php echo $total; ?> media</span>
							</div>
							<div class="gallery-card-visual gallery-card-visual--<?php echo max(1, min(3, count($preview))); ?>">
								<?php if (!empty($preview)): ?>
									<?php foreach (array_slice($preview, 0, 3) as $index => $_p): ?>
									<div class="gallery-card-visual__tile gallery-card-visual__tile--<?php echo (int) $index; ?>">
										<?php if (($_p['file_type'] ?? 'image') === 'image'): ?>
										<img src="<?php echo htmlspecialchars(galMediaUrl($_p['file_path'])); ?>" alt="<?php echo htmlspecialchars($_p['title'] ?: $service); ?>" loading="lazy">
										<?php else: ?>
										<video src="<?php echo htmlspecialchars(galMediaUrl($_p['file_path'])); ?>" muted preload="metadata"></video>
										<span class="gallery-card-visual__play"><i class="fas fa-play"></i></span>
										<?php endif; ?>
										<?php if ($index === 0 && $cardMediaTitle !== ''): ?>
										<div class="gallery-card-visual__caption">
											<strong><?php echo htmlspecialchars($cardMediaTitle); ?></strong>
											<span><?php echo $imgCount; ?> photo<?php echo $imgCount !== 1 ? 's' : ''; ?><?php echo $vidCount ? ' • ' . $vidCount . ' video' . ($vidCount !== 1 ? 's' : '') : ''; ?></span>
										</div>
										<?php endif; ?>
										<?php if ($index === 2 && $total > 3): ?>
										<div class="gallery-card-visual__more">+<?php echo $total - 3; ?> more</div>
										<?php endif; ?>
									</div>
									<?php endforeach; ?>
								<?php else: ?>
									<div class="gallery-card-visual__empty">
										<i class="fas fa-image"></i>
										<span>No preview available</span>
									</div>
								<?php endif; ?>
							</div>
							<div class="gallery-card-body">
								<div class="gallery-card-body__copy">
									<span class="gallery-card-body__label">Featured project</span>
									<?php if ($cardMediaTitle !== ''): ?>
									<p class="gallery-card-media-title"><?php echo htmlspecialchars($cardMediaTitle); ?></p>
									<?php endif; ?>
									<?php if ($cardMediaDescription !== ''): ?>
									<p class="gallery-card-media-desc"><?php echo htmlspecialchars($cardMediaDescription); ?></p>
									<?php endif; ?>
									<span class="gallery-card-count">
										<i class="fas fa-image me-1"></i><?php echo $imgCount; ?> photo<?php echo $imgCount !== 1 ? 's' : ''; ?>
										<?php if ($vidCount): ?>&nbsp;&bull;&nbsp;<i class="fas fa-video me-1"></i><?php echo $vidCount; ?> video<?php echo $vidCount !== 1 ? 's' : ''; ?><?php endif; ?>
									</span>
								</div>
								<span class="gallery-card-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
							</div>
						</div>
					</button>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<div class="gallery-lightbox" id="projectGalleryLightbox" hidden>
			<div class="gallery-lightbox__backdrop" data-gallery-close></div>
			<div class="gallery-lightbox__dialog" role="dialog" aria-modal="true" aria-labelledby="projectGalleryTitle">
				<button type="button" class="gallery-lightbox__close" data-gallery-close aria-label="Close project gallery">
					<i class="fas fa-times"></i>
				</button>
				<div class="gallery-lightbox__header">
					<div>
						<span class="gallery-lightbox__eyebrow">Project Gallery</span>
						<h3 class="gallery-lightbox__title" id="projectGalleryTitle">Project Media</h3>
					</div>
					<div class="gallery-lightbox__counter" id="projectGalleryCounter">1 / 1</div>
				</div>
				<div class="gallery-lightbox__stage">
					<button type="button" class="gallery-lightbox__nav gallery-lightbox__nav--prev" id="galleryPrevBtn" aria-label="Previous media">
						<i class="fas fa-chevron-left"></i>
					</button>
					<div class="gallery-lightbox__media" id="projectGalleryMedia"></div>
					<button type="button" class="gallery-lightbox__nav gallery-lightbox__nav--next" id="galleryNextBtn" aria-label="Next media">
						<i class="fas fa-chevron-right"></i>
					</button>
				</div>
				<div class="gallery-lightbox__meta">
					<h4 class="gallery-lightbox__media-title" id="projectGalleryMediaTitle"></h4>
					<p class="gallery-lightbox__media-desc" id="projectGalleryMediaDesc" hidden></p>
				</div>
				<div class="gallery-lightbox__thumbs" id="projectGalleryThumbs"></div>
			</div>
		</div>

		<style>
		.gallery-section { padding:100px 0; }
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

		.gallery-toolbar {
			display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;margin:0 0 28px;
		}
		.gallery-filter-chips {
			display:flex;flex-wrap:wrap;gap:10px;
		}
		.gallery-filter-chip {
			display:inline-flex;align-items:center;gap:10px;
			padding:10px 16px;border-radius:999px;border:1px solid #dbe4f0;
			background:#fff;color:#0f172a;text-decoration:none;font-weight:700;
			font-size:13px;box-shadow:0 8px 18px rgba(15,23,42,0.04);
			transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease,background .2s ease,color .2s ease;
		}
		.gallery-filter-chip span {
			display:inline-flex;align-items:center;justify-content:center;
			min-width:24px;height:24px;padding:0 7px;border-radius:999px;
			background:#f8fafc;color:#64748b;font-size:11px;font-weight:800;
		}
		.gallery-filter-chip:hover,
		.gallery-filter-chip.is-active {
			transform:translateY(-1px);border-color:#E5363D;background:#fff5f5;color:#E5363D;
			box-shadow:0 16px 28px rgba(229,54,61,0.10);
		}
		.gallery-filter-chip.is-active span { background:#fff; color:#E5363D; }
		.gallery-toolbar-note {
			margin:0;max-width:430px;font-size:14px;line-height:1.7;color:#64748b;
		}
		.gallery-grid {
			display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:28px;
		}
		.gallery-card-link {
			text-decoration:none;display:block;width:100%;
			padding:0 !important;border:0 !important;background:none !important;text-align:left;cursor:pointer;
			box-shadow:none !important;appearance:none;-webkit-appearance:none;border-radius:0;
		}
		.gallery-card {
			background:#fff;border:1px solid #e2e8f0;border-radius:28px;overflow:hidden;
			box-shadow:0 20px 44px rgba(15,23,42,0.07);
			transition:transform 0.32s,box-shadow 0.32s,border-color 0.32s;
		}
		.gallery-card-link:hover .gallery-card {
			transform:translateY(-6px);box-shadow:0 26px 64px rgba(15,23,42,0.12);border-color:#d8e0ea;
		}
		.gallery-card-head {
			padding:22px 22px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;
		}
		.gallery-card-head__copy { min-width:0; }
		.gallery-card-eyebrow {
			display:inline-flex;align-items:center;gap:8px;
			padding:6px 12px;border-radius:999px;background:#f8fafc;
			color:#E5363D;font-size:11px;letter-spacing:0.14em;font-weight:800;text-transform:uppercase;
		}
		.gallery-card-head .gallery-card-title {
			margin:12px 0 8px;font-size:22px;font-weight:800;line-height:1.15;color:#0f172a;
		}
		.gallery-card-summary {
			margin:0;font-size:14px;line-height:1.7;color:#64748b;
			display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden;
		}
		.gallery-card-pill {
			flex-shrink:0;padding:10px 14px;border-radius:999px;background:#fef2f2;color:#E5363D;
			font-size:12px;font-weight:800;letter-spacing:0.03em;
		}
		.gallery-card-visual {
			margin:20px 22px 0;padding:8px;background:#f8fafc;border-radius:24px;
			display:grid;gap:8px;min-height:280px;overflow:hidden;
		}
		.gallery-card-visual--1 { grid-template-columns:1fr; }
		.gallery-card-visual--2 { grid-template-columns:1fr 1fr; }
		.gallery-card-visual--3 {
			grid-template-columns:1.2fr 1fr;grid-template-rows:repeat(2,1fr);
		}
		.gallery-card-visual__tile {
			position:relative;overflow:hidden;border-radius:18px;background:#e2e8f0;min-height:120px;
		}
		.gallery-card-visual__tile img,
		.gallery-card-visual__tile video {
			width:100%;height:100%;object-fit:cover;display:block;
		}
		.gallery-card-visual--1 .gallery-card-visual__tile { min-height:280px; }
		.gallery-card-visual--2 .gallery-card-visual__tile { min-height:260px; }
		.gallery-card-visual--3 .gallery-card-visual__tile--0 {
			grid-column:1;grid-row:1 / span 2;min-height:280px;
		}
		.gallery-card-visual--3 .gallery-card-visual__tile--1 {
			grid-column:2;grid-row:1;
		}
		.gallery-card-visual--3 .gallery-card-visual__tile--2 {
			grid-column:2;grid-row:2;
		}
		.gallery-card-visual__play {
			position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
			color:#fff;font-size:14px;background:linear-gradient(180deg, rgba(15,23,42,0.08), rgba(15,23,42,0.22));
		}
		.gallery-card-visual__play i {
			display:inline-flex;align-items:center;justify-content:center;
			width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.92);color:#E5363D;
			box-shadow:0 18px 36px rgba(15,23,42,0.16);
		}
		.gallery-card-visual__caption {
			position:absolute;left:12px;right:12px;bottom:12px;
			padding:12px 14px;border-radius:18px;background:rgba(15,23,42,0.42);backdrop-filter:blur(8px);
			color:#fff;
		}
		.gallery-card-visual__caption strong {
			display:block;font-size:13px;line-height:1.35;margin-bottom:4px;font-weight:800;
		}
		.gallery-card-visual__caption span {
			display:block;font-size:11px;opacity:0.88;font-weight:600;
		}
		.gallery-card-visual__more {
			position:absolute;right:12px;top:12px;padding:8px 12px;border-radius:999px;
			background:rgba(15,23,42,0.72);color:#fff;font-size:12px;font-weight:800;
		}
		.gallery-card-visual__empty {
			min-height:280px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;
			color:#94a3b8;font-weight:700;
		}
		.gallery-card-visual__empty i { font-size:42px;color:#cbd5e1; }
		.gallery-card-body {
			padding:18px 22px 22px;display:flex;align-items:flex-end;justify-content:space-between;gap:16px;
		}
		.gallery-card-body__copy { min-width:0; }
		.gallery-card-body__label {
			display:inline-block;margin-bottom:8px;font-size:12px;font-weight:800;letter-spacing:0.12em;
			text-transform:uppercase;color:#E5363D;
		}
		.gallery-card-media-title {
			font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px;
		}
		.gallery-card-media-desc {
			font-size:14px;line-height:1.7;color:#64748b;margin:0 0 10px;
			display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden;
		}
		.gallery-card-count { font-size:12px;color:#94a3b8;font-weight:600;margin:0; }
		.gallery-card-arrow {
			width:44px;height:44px;border-radius:50%;
			background:#fef2f2;color:#E5363D;
			display:flex;align-items:center;justify-content:center;
			flex-shrink:0;font-size:15px;
			transition:background 0.2s,color 0.2s;
		}
		.gallery-card-link:hover .gallery-card-arrow { background:#E5363D;color:#fff; }
		.gallery-lightbox {
			position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;
			padding:28px;
		}
		.gallery-lightbox[hidden] { display:none !important; }
		.gallery-lightbox__backdrop {
			position:absolute;inset:0;background:rgba(6,14,28,0.78);backdrop-filter:blur(6px);
		}
		.gallery-lightbox__dialog {
			position:relative;z-index:2;width:min(1120px,100%);max-height:min(90vh,920px);
			background:#fff;border-radius:28px;overflow:hidden;
			box-shadow:0 34px 90px rgba(15,23,42,0.35);
			display:flex;flex-direction:column;
		}
		.gallery-lightbox__close {
			position:absolute;top:18px;right:18px;z-index:3;width:46px;height:46px;border-radius:50%;
			border:0;background:#0f172a;color:#fff;font-size:16px;
			display:flex;align-items:center;justify-content:center;cursor:pointer;
			box-shadow:0 12px 24px rgba(15,23,42,0.28);
		}
		.gallery-lightbox__header {
			padding:28px 34px 16px;display:flex;align-items:flex-start;justify-content:space-between;gap:18px;
			border-bottom:1px solid #e2e8f0;
		}
		.gallery-lightbox__eyebrow {
			display:inline-block;font-size:12px;font-weight:800;letter-spacing:0.18em;text-transform:uppercase;color:#E5363D;
			margin-bottom:8px;
		}
		.gallery-lightbox__title { margin:0;font-size:30px;line-height:1.1;font-weight:800;color:#0f172a; }
		.gallery-lightbox__counter {
			flex-shrink:0;padding:10px 16px;border-radius:999px;background:#f8fafc;color:#475569;
			font-size:13px;font-weight:700;
		}
		.gallery-lightbox__stage {
			padding:24px 28px 18px;display:grid;grid-template-columns:60px minmax(0,1fr) 60px;gap:16px;align-items:center;
		}
		.gallery-lightbox__meta {
			padding:0 28px 18px;
		}
		.gallery-lightbox__media-title {
			margin:0;
			font-size:20px;
			font-weight:800;
			color:#0f172a;
		}
		.gallery-lightbox__media-desc {
			margin:8px 0 0;
			font-size:14px;
			line-height:1.7;
			color:#64748b;
		}
		.gallery-lightbox__nav {
			width:60px;height:60px;border-radius:50%;border:1px solid #e2e8f0;background:#fff;color:#0f172a;
			display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:18px;
			box-shadow:0 12px 26px rgba(15,23,42,0.08);
		}
		.gallery-lightbox__nav[disabled] { opacity:0.35;cursor:not-allowed; }
		.gallery-lightbox__media {
			background:#0f172a;border-radius:24px;min-height:460px;overflow:hidden;
			display:flex;align-items:center;justify-content:center;position:relative;
		}
		.gallery-lightbox__media img,
		.gallery-lightbox__media video {
			width:100%;height:100%;max-height:62vh;object-fit:contain;background:#0f172a;display:block;
		}
		.gallery-lightbox__video-badge {
			position:absolute;top:18px;left:18px;padding:8px 14px;border-radius:999px;
			background:rgba(255,255,255,0.12);backdrop-filter:blur(6px);color:#fff;
			font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;
		}
		.gallery-lightbox__thumbs {
			padding:0 28px 28px;display:flex;gap:12px;overflow:auto;
		}
		.gallery-lightbox__thumb {
			flex:0 0 96px;height:76px;border-radius:16px;border:2px solid transparent;overflow:hidden;
			background:#e2e8f0;cursor:pointer;position:relative;
		}
		.gallery-lightbox__thumb.is-active { border-color:#E5363D; box-shadow:0 10px 24px rgba(229,54,61,0.18); }
		.gallery-lightbox__thumb img,
		.gallery-lightbox__thumb video {
			width:100%;height:100%;object-fit:cover;display:block;
		}
		.gallery-lightbox__thumb-icon {
			position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
			background:rgba(15,23,42,0.3);color:#fff;font-size:16px;
		}

		@media (max-width:768px) {
			.gallery-grid { grid-template-columns:1fr;gap:18px; }
			.section-title { font-size:28px; }
			.gallery-section { padding:60px 0; }
			.gallery-card-head { padding:18px 18px 0; }
			.gallery-card-head .gallery-card-title { font-size:20px; }
			.gallery-card-visual { margin:16px 18px 0; min-height:240px; }
			.gallery-card-visual--1 .gallery-card-visual__tile,
			.gallery-card-visual--2 .gallery-card-visual__tile,
			.gallery-card-visual--3 .gallery-card-visual__tile { min-height:110px; }
			.gallery-card-visual--3 { grid-template-columns:1fr 1fr; }
			.gallery-card-visual--3 .gallery-card-visual__tile--0 { grid-column:1 / -1; grid-row:auto; min-height:190px; }
			.gallery-card-visual--3 .gallery-card-visual__tile--1,
			.gallery-card-visual--3 .gallery-card-visual__tile--2 { grid-column:auto; grid-row:auto; }
			.gallery-card-body { padding:16px 18px 18px; }
			.gallery-card-arrow { width:40px;height:40px; }
			.gallery-toolbar { margin-bottom:22px; }
			.gallery-toolbar-note { max-width:none; }
			.gallery-filter-chip { padding:9px 14px; font-size:12px; }
			.gallery-lightbox { padding:12px; }
			.gallery-lightbox__dialog { border-radius:22px;max-height:92vh; }
			.gallery-lightbox__header { padding:22px 18px 10px; }
			.gallery-lightbox__title { font-size:24px; padding-right:54px; }
			.gallery-lightbox__stage {
				padding:18px 14px 14px;grid-template-columns:1fr;gap:12px;
			}
			.gallery-lightbox__media { min-height:280px; }
			.gallery-lightbox__nav {
				position:absolute;top:50%;transform:translateY(-50%);z-index:2;width:44px;height:44px;font-size:14px;
				background:rgba(255,255,255,0.92);
			}
			.gallery-lightbox__nav--prev { left:20px; }
			.gallery-lightbox__nav--next { right:20px; }
			.gallery-lightbox__thumbs { padding:0 14px 18px; }
		}
		</style>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			const galleryData = <?php echo json_encode($galleryPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
			const initialService = <?php echo json_encode($requestedGalleryService, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
			const modal = document.getElementById('projectGalleryLightbox');
			if (!modal) return;

			const mediaHost = document.getElementById('projectGalleryMedia');
			const thumbsHost = document.getElementById('projectGalleryThumbs');
			const titleHost = document.getElementById('projectGalleryTitle');
			const counterHost = document.getElementById('projectGalleryCounter');
			const mediaTitleHost = document.getElementById('projectGalleryMediaTitle');
			const mediaDescHost = document.getElementById('projectGalleryMediaDesc');
			const prevBtn = document.getElementById('galleryPrevBtn');
			const nextBtn = document.getElementById('galleryNextBtn');
			const triggers = document.querySelectorAll('.js-gallery-open');
			let currentItems = [];
			let currentService = '';
			let currentIndex = 0;

			function renderMedia() {
				const active = currentItems[currentIndex];
				if (!active) return;
				titleHost.textContent = currentService + ' Gallery';
				counterHost.textContent = (currentIndex + 1) + ' / ' + currentItems.length;
				mediaTitleHost.textContent = active.title || currentService;
				if ((active.description || '').trim()) {
					mediaDescHost.textContent = active.description;
					mediaDescHost.hidden = false;
				} else {
					mediaDescHost.textContent = '';
					mediaDescHost.hidden = true;
				}

				if (active.type === 'video') {
					mediaHost.innerHTML =
						'<span class=\"gallery-lightbox__video-badge\">Video</span>' +
						'<video controls autoplay playsinline src=\"' + active.url.replace(/\"/g, '&quot;') + '\"></video>';
				} else {
					mediaHost.innerHTML =
						'<img src=\"' + active.url.replace(/\"/g, '&quot;') + '\" alt=\"' + (active.title || currentService).replace(/\"/g, '&quot;') + '\">';
				}

				thumbsHost.innerHTML = '';
				currentItems.forEach(function (item, index) {
					const btn = document.createElement('button');
					btn.type = 'button';
					btn.className = 'gallery-lightbox__thumb' + (index === currentIndex ? ' is-active' : '');
					btn.setAttribute('aria-label', 'View media ' + (index + 1));
					btn.addEventListener('click', function () {
						currentIndex = index;
						renderMedia();
					});

					if (item.type === 'video') {
						btn.innerHTML = '<video muted preload=\"metadata\" src=\"' + item.url.replace(/\"/g, '&quot;') + '\"></video><span class=\"gallery-lightbox__thumb-icon\"><i class=\"fas fa-play\"></i></span>';
					} else {
						btn.innerHTML = '<img src=\"' + item.url.replace(/\"/g, '&quot;') + '\" alt=\"\">';
					}
					thumbsHost.appendChild(btn);
				});

				prevBtn.disabled = currentItems.length <= 1;
				nextBtn.disabled = currentItems.length <= 1;
			}

			function openGallery(serviceName) {
				const items = galleryData[serviceName] || [];
				if (!items.length) return;
				currentItems = items;
				currentService = serviceName;
				currentIndex = 0;
				renderMedia();
				modal.hidden = false;
				document.body.style.overflow = 'hidden';
			}

			function closeGallery() {
				modal.hidden = true;
				document.body.style.overflow = '';
				mediaHost.innerHTML = '';
				mediaTitleHost.textContent = '';
				mediaDescHost.textContent = '';
				mediaDescHost.hidden = true;
			}

			triggers.forEach(function (trigger) {
				trigger.addEventListener('click', function () {
					openGallery(trigger.getAttribute('data-gallery-service') || '');
				});
			});

			if (initialService && galleryData[initialService] && galleryData[initialService].length) {
				openGallery(initialService);
				if (window.history && window.history.replaceState) {
					window.history.replaceState({}, document.title, window.location.pathname);
				}
			}

			prevBtn.addEventListener('click', function () {
				if (currentItems.length <= 1) return;
				currentIndex = (currentIndex - 1 + currentItems.length) % currentItems.length;
				renderMedia();
			});
			nextBtn.addEventListener('click', function () {
				if (currentItems.length <= 1) return;
				currentIndex = (currentIndex + 1) % currentItems.length;
				renderMedia();
			});
			modal.querySelectorAll('[data-gallery-close]').forEach(function (node) {
				node.addEventListener('click', closeGallery);
			});
			document.addEventListener('keydown', function (event) {
				if (modal.hidden) return;
				if (event.key === 'Escape') closeGallery();
				if (event.key === 'ArrowLeft') prevBtn.click();
				if (event.key === 'ArrowRight') nextBtn.click();
			});
		});
		</script>
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
