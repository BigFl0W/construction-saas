<?php
require_once 'config/config.php';
require_once 'classes/Blog.php';
require_once 'classes/Functions.php';
$db = Database::getInstance();

// Fetch latest 3 published blog posts
$sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.published_at,
               m.file_path AS featured_image_path
        FROM blog_posts p
        LEFT JOIN media m ON p.featured_image_id = m.id
        WHERE p.status = 'published' AND p.deleted_at IS NULL
        ORDER BY p.published_at DESC
        LIMIT 3";
$stmt = $db->query($sql);
$latestPosts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>TPV Construction and Services LTD &#8211; </title>
	<meta name='robots' content='max-image-preview:large'>
	<link rel='dns-prefetch' href='//fonts.googleapis.com'>
	<link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Feed" href="feed/">
	<link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Comments Feed"
		href="comments/feed/">
	<link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed"
		href="wp-json/oembed/1.0/embed?url=https%3A%2F%2Fdemo.awaikenthemes.com%2FTPV Construction Services%2F">
	<link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed"
		href="wp-json/oembed/1.0/embed-1?url=https%3A%2F%2Fdemo.awaikenthemes.com%2FTPV Construction Services%2F&#038;format=xml">
	<style id='wp-img-auto-sizes-contain-inline-css'>
		img:is([sizes=auto i], [sizes^="auto," i]) {
			contain-intrinsic-size: 3000px 1500px
		}

		/*# sourceURL=wp-img-auto-sizes-contain-inline-css */
	</style>
	<link rel='stylesheet' id='elementor-frontend-css'
		href='wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='elementor-post-225-css'
		href='wp-content/uploads/elementor/css/post-225.css?ver=1770715449' media='all'>
	<link rel='stylesheet' id='elementor-post-1688-css'
		href='wp-content/uploads/elementor/css/post-1688.css?ver=1770715449' media='all'>
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
		/* Enhanced Testimonials Slider Styles */
		.elementskit-testimonial-slider {
			position: relative;
			overflow: hidden;
			padding: 20px 0 60px;
		}

		/* Swiper Container */
		.elementskit-testimonial-slider .ekit-main-swiper {
			overflow: visible !important;
		}

		/* Slide Styles */
		.elementskit-testimonial-slider .swiper-slide {
			opacity: 0.5;
			transform: scale(0.9);
			transition: all 0.5s ease;
		}

		.elementskit-testimonial-slider .swiper-slide-active {
			opacity: 1;
			transform: scale(1);
		}

		/* Active Slide Custom Class */
		.elementskit-testimonial-slider .swiper-slide-active-custom {
			/* box-shadow: 0 10px 30px rgba(0,0,0,0.1); */
		}

		/* Pagination Styles */
		.elementskit-testimonial-slider .swiper-pagination {
			bottom: 0 !important;
		}

		.elementskit-testimonial-slider .swiper-pagination-bullet {
			width: 10px;
			height: 10px;
			background: #ddd;
			opacity: 1;
			transition: all 0.3s ease;
		}

		.elementskit-testimonial-slider .swiper-pagination-bullet-active {
			width: 30px;
			border-radius: 5px;
			background: #E5363D;
		}

		/* Navigation Arrows */
		.elementskit-testimonial-slider .swiper-button-prev,
		.elementskit-testimonial-slider .swiper-button-next {
			width: 40px;
			height: 40px;
			background: rgba(255, 255, 255, 0.9);
			border-radius: 50%;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			color: #333;
			transition: all 0.3s ease;
		}

		.elementskit-testimonial-slider .swiper-button-prev:hover,
		.elementskit-testimonial-slider .swiper-button-next:hover {
			background: #E5363D;
			color: #fff;
		}

		.elementskit-testimonial-slider .swiper-button-prev:after,
		.elementskit-testimonial-slider .swiper-button-next:after {
			font-size: 18px;
			font-weight: bold;
		}

		/* Progress Bar */
		.elementskit-testimonial-slider .swiper-progress-bar {
			position: absolute;
			bottom: 40px;
			left: 50%;
			transform: translateX(-50%);
			width: 200px;
			height: 3px;
			background: rgba(0, 0, 0, 0.1);
			border-radius: 3px;
			overflow: hidden;
			z-index: 10;
		}

		.elementskit-testimonial-slider .swiper-progress-bar .progress-fill {
			height: 100%;
			background: #E5363D;
			width: 0;
			border-radius: 3px;
		}

		/* Touch State */
		.elementskit-testimonial-slider .ekit-main-swiper.touching {
			cursor: grabbing;
		}

		/* Responsive Adjustments */
		@media (max-width: 768px) {

			.elementskit-testimonial-slider .swiper-button-prev,
			.elementskit-testimonial-slider .swiper-button-next {
				display: none;
			}

			.elementskit-testimonial-slider .swiper-pagination {
				bottom: 10px;
			}
		}

		/* Slide Content Animation */
		.elementskit-testimonial-slider .swiper-slide-active .elementskit-commentor-content p {
			animation: fadeInUp 0.8s ease forwards;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(20px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
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
		href='wp-content/plugins/contact-form-7/includes/css/styles.css?ver=6.1.5' media='all'>
	<link rel='stylesheet' id='elementor-post-7-css' href='wp-content/uploads/elementor/css/post-7.css?ver=1770715450'
		media='all'>
	<link rel='stylesheet' id='e-animation-fadeInUp-css'
		href='wp-content/plugins/elementor/assets/lib/animations/styles/fadeInUp.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-heading-css'
		href='wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-image-css'
		href='wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-icon-list-css'
		href='wp-content/plugins/elementor/assets/css/widget-icon-list.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-icon-box-css'
		href='wp-content/plugins/elementor/assets/css/widget-icon-box.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-counter-css'
		href='wp-content/plugins/elementor/assets/css/widget-counter.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='swiper-css'
		href='wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4.5' media='all'>
	<link rel='stylesheet' id='elementor-post-10-css' href='wp-content/uploads/elementor/css/post-10.css?ver=1770717671'
		media='all'>
	<link rel='stylesheet' id='ekit-widget-styles-css'
		href='wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9' media='all'>
	<link rel='stylesheet' id='ekit-widget-styles-pro-css'
		href='wp-content/plugins/elementskit/widgets/init/assets/css/widget-styles-pro.css?ver=4.2.1' media='all'>
	<link rel='stylesheet' id='ekit-responsive-css'
		href='wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9' media='all'>
	<link rel='stylesheet' id='TPV Construction Services-font-css'
		href='../css2?family=Manrope%3Awght%40200..800&#038;family=DM+Sans%3Aital%2Copsz%2Cwght%400%2C9..40%2C100..1000%3B1%2C9..40%2C100..1000&#038;display=swap'
		media='all'>
	<link rel='stylesheet' id='TPV Construction Services-css-variable-css'
		href='wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='fontawesome-6.4.0-css' href='wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8'
		media='all'>
	<link rel='stylesheet' id='bootstrap-5.3.2-css'
		href='wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='TPV Construction Services-style-css' href='wp-content/themes/tpv/style.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='elementor-gf-local-manrope-css'
		href='wp-content/uploads/elementor/google-fonts/css/manrope.css?ver=1744107304' media='all'>
	<link rel='stylesheet' id='elementor-gf-local-dmsans-css'
		href='wp-content/uploads/elementor/google-fonts/css/dmsans.css?ver=1744107305' media='all'>
	<link rel='stylesheet' id='elementor-icons-ekiticons-css'
		href='wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/css/ekiticons.css?ver=3.7.9'
		media='all'>
	<script src="wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
	<script src="wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
	<link rel="https://api.w.org/" href="wp-json/">
	<link rel="alternate" title="JSON" type="application/json" href="wp-json/wp/v2/pages/10">
	<link rel="EditURI" type="application/rsd+xml" title="RSD"
		href="https://tpvconstruction.com.ng/xmlrpc.php?rsd">

	<link rel="canonical" href="index.htm">
	<link rel='shortlink' href='index.htm'>
	<meta name="generator"
		content="Elementor 3.35.3; features: e_font_icon_svg, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap">
	<script
		type="text/javascript">
		var elementskit_module_parallax_url = "https://tpvconstruction.com.ng/wp-content/plugins/elementskit/modules/parallax/";
	</script>
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
	<link rel="icon" href="wp-content/uploads/2024/06/favicon.png" sizes="32x32">
	<link rel="icon" href="wp-content/uploads/2024/06/favicon.png" sizes="192x192">
	<link rel="apple-touch-icon" href="wp-content/uploads/2024/06/favicon.png">
	<meta name="msapplication-TileImage"
		content="https://tpvconstrcution.com.ng/wp-content/uploads/2024/06/favicon.png">
</head>

<body
	class="home wp-singular page-template page-template-elementor_header_footer page page-id-10 wp-custom-logo wp-theme-TPV Construction Services tt-magic-cursor elementor-default elementor-template-full-width elementor-kit-7 elementor-page elementor-page-10">

	<div class="preloader">
		<div class="loading-container">
			<div class="loading"></div>
			<div id="loading-icon"><img src="wp-content/themes/tpv/assets/images/loader.png" alt=""></div>
		</div>
	</div>
	<div id="magic-cursor">
		<div id="ball"></div>
	</div>

	<a class="skip-link screen-reader-text" href="#content">
		Skip to content </a>


	<div class="ekit-template-content-markup ekit-template-content-header ekit-template-content-theme-support">
		<div data-elementor-type="wp-post" data-elementor-id="225" class="elementor elementor-225">
			<div class="elementor-element elementor-element-3c0e001 e-con-full e-flex e-con e-parent" data-id="3c0e001"
				data-element_type="container" data-e-type="container">
				<div class="elementor-element elementor-element-159e7cf e-con-full e-flex e-con e-child"
					data-id="159e7cf" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-08aa86c e-con-full e-flex e-con e-child"
						data-id="08aa86c" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-d7183c8 elementor-widget elementor-widget-TPV Construction Services-site-logo"
							data-id="d7183c8" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;align&quot;:&quot;left&quot;,&quot;width&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;width_tablet&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;width_mobile&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;space&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;space_tablet&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;space_mobile&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;image_border_radius&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;image_border_radius_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;image_border_radius_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_padding&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_padding_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_padding_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_space&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:0,&quot;sizes&quot;:[]},&quot;caption_space_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;caption_space_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="TPV Construction Services-site-logo.default">
							<div class="elementor-widget-container">
								<div class="ata-site-logo">
									<a data-elementor-open-lightbox="" class='elementor-clickable'
										href="https://tpvconstruction.com.ng">
										<div class="ata-site-logo-set">
											<div class="ata-site-logo-container">
												<img class="ata-site-logo-img elementor-animation-"
													src="<?php echo htmlspecialchars(tpv_setting_asset_url('site_logo', 'wp-content/uploads/2024/06/logo.png')); ?>" alt="default-logo" width="150" height="50" />
											</div>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-d936ea6 e-con-full e-flex e-con e-child"
						data-id="d936ea6" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-d67d018 header-menu elementor-widget elementor-widget-ekit-nav-menu"
							data-id="d67d018" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="ekit-nav-menu.default">
							<div class="elementor-widget-container">
								<nav class="ekit-wid-con ekit_menu_responsive_tablet"
									data-hamburger-icon="icon icon-menu-11" data-hamburger-icon-type="icon"
									data-responsive-breakpoint="1024">
									<button class="elementskit-menu-hamburger elementskit-menu-toggler" type="button"
										aria-label="hamburger-icon">
										<i aria-hidden="true" class="ekit-menu-icon icon icon-menu-11"></i> </button>
									<div id="ekit-megamenu-header-menu"
										class="elementskit-menu-container elementskit-menu-offcanvas-elements elementskit-navbar-nav-default ekit-nav-menu-one-page- ekit-nav-dropdown-hover">
										<ul id="menu-header-menu"
											class="elementskit-navbar-nav elementskit-menu-po-center submenu-click-on-icon">
											<li id="menu-item-5912"
												class="menu-item menu-item-type-custom menu-item-object-custom current-menu-ancestor current-menu-parent menu-item-has-children menu-item-5912 nav-item elementskit-dropdown-has relative_position elementskit-dropdown-menu-default_width elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="./"
													class="ekit-menu-nav-link">Home</a>

											</li>
											<li id="menu-item-3045"
												class="menu-item menu-item-type-post_type menu-item-object-page menu-item-3045 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="about-us/"
													class="ekit-menu-nav-link">About Us</a></li>

											<li id="menu-item-16"
												class="menu-item menu-item-type-post_type menu-item-object-page menu-item-16 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="contact-us/"
													class="ekit-menu-nav-link">Contact Us</a></li>

											<li id="menu-item-16"
												class="menu-item menu-item-type-post_type menu-item-object-page menu-item-16 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="blog/"
													class="ekit-menu-nav-link">Blog</a></li>
											<li id="menu-item-3343"
												class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-3343 nav-item elementskit-dropdown-has relative_position elementskit-dropdown-menu-default_width elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="services/"
													class="ekit-menu-nav-link ekit-menu-dropdown-toggle">Services<i
														class="icon icon-down-arrow1 elementskit-submenu-indicator"></i></a>
												<ul class="elementskit-dropdown elementskit-submenu-panel">
													<li id="menu-item-5670"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5670 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/building-construction/"
															class=" dropdown-item">Building Construction</a>
													<li id="menu-item-5671"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5671 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/architecture-design/"
															class=" dropdown-item">Architecture Design</a>
													<li id="menu-item-5672"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5672 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/building-renovation/"
															class=" dropdown-item">Building Renovation</a>
													<li id="menu-item-5673"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5673 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/flooring-roofing/"
															class=" dropdown-item">Flooring &#038; Roofing</a>
													<li id="menu-item-5674"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5674 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/building-maintenance/"
															class=" dropdown-item">Building Maintenance</a>
													<li id="menu-item-5675"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5675 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/project-management/"
															class=" dropdown-item">Project Management</a></li>
												</ul>
											</li>


											<li id="menu-item-4598"
												class="mobile-menu menu-item menu-item-type-post_type menu-item-object-page menu-item-4598 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="quote/"
													class="ekit-menu-nav-link">Get Free Quote</a></li>
										</ul>
										<div class="elementskit-nav-identity-panel"><button
												class="elementskit-menu-close elementskit-menu-toggler"
												type="button">X</button></div>
									</div>
									<div
										class="elementskit-menu-overlay elementskit-menu-offcanvas-elements elementskit-menu-toggler ekit-nav-menu--overlay">
									</div>
								</nav>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-1380be6 e-con-full elementor-hidden-tablet elementor-hidden-mobile e-flex e-con e-child"
						data-id="1380be6" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-a57c1c9 btn-bg-whiter elementor-widget elementor-widget-button"
							data-id="a57c1c9" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="button.default">
							<div class="elementor-widget-container">
								<div class="elementor-button-wrapper">
									<a class="elementor-button elementor-button-link elementor-size-sm"
										href="quote/">
										<span class="elementor-button-content-wrapper">
											<span class="elementor-button-text">Get Free Quote</span>
										</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div data-elementor-type="wp-page" data-elementor-id="10" class="elementor elementor-10">
		<div class="elementor-element elementor-element-70b33d8 e-con-full hero-shape-1 e-flex e-con e-parent"
			data-id="70b33d8" data-element_type="container" data-e-type="container">
			<div data-ekitparallax="{&quot;ekit_section_parallax_bg&quot;:&quot;yes&quot;,&quot;ekit_section_parallax_bg_speed&quot;:0.5,&quot;elementor_lazy_load&quot;:&quot;1&quot;}"
				class="elementor-element elementor-element-fe91c30 e-flex e-con-boxed e-con e-child" data-id="fe91c30"
				data-element_type="container" data-e-type="container"
				data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
				<div class="e-con-inner">
					<div class="elementor-element elementor-element-5f24818 e-con-full e-flex e-con e-child"
						data-id="5f24818" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-17ea61f at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
							data-id="17ea61f" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h3 class="elementor-heading-title elementor-size-default">Welcome to TPV Construction and Services LTD</h3>
							</div>
						</div>
						<div class="elementor-element elementor-element-f3f7de9 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="f3f7de9" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h1 class="elementor-heading-title elementor-size-default">Building dreams with
									precision and excellence</h1>
							</div>
						</div>
						<div class="elementor-element elementor-element-09bd4ae elementor-invisible elementor-widget elementor-widget-text-editor"
							data-id="09bd4ae" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
								<p>we specialize in turning visions into reality with exceptional craftsmanship and
									meticulous attention to detail. With years of experience and a commitment to
									quality.</p>
							</div>
						</div>
						<div class="elementor-element elementor-element-f877922 e-con-full e-flex elementor-invisible e-con e-child"
							data-id="f877922" data-element_type="container" data-e-type="container"
							data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:200}">
							<div class="elementor-element elementor-element-bbaeda4 elementor-widget elementor-widget-button"
								data-id="bbaeda4" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="button.default">
								<div class="elementor-widget-container">
									<div class="elementor-button-wrapper">
										<a class="elementor-button elementor-button-link elementor-size-sm"
											href="contact-us/">
											<span class="elementor-button-content-wrapper">
												<span class="elementor-button-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
														viewbox="0 0 24 24" fill="none">
														<path
															d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
															stroke="white" stroke-width="1.5" stroke-linecap="round"
															stroke-linejoin="round"></path>
													</svg> </span>
												<span class="elementor-button-text">get started</span>
											</span>
										</a>
									</div>
								</div>
							</div>
							<div class="elementor-element elementor-element-1f6da57 btn-highlighted elementor-widget elementor-widget-button"
								data-id="1f6da57" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="button.default">
								<div class="elementor-widget-container">
									<div class="elementor-button-wrapper">
										<a class="elementor-button elementor-button-link elementor-size-sm"
											href="projects/">
											<span class="elementor-button-content-wrapper">
												<span class="elementor-button-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
														viewbox="0 0 24 24" fill="none">
														<path
															d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
															stroke="white" stroke-width="1.5" stroke-linecap="round"
															stroke-linejoin="round"></path>
													</svg> </span>
												<span class="elementor-button-text">view Projects</span>
											</span>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="elementor-element elementor-element-ea70e8a e-flex e-con-boxed e-con e-parent" data-id="ea70e8a"
			data-element_type="container" data-e-type="container">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-ec381d2 e-con-full e-flex e-con e-child"
					data-id="ec381d2" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-5791a6a at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
						data-id="5791a6a" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="image.default">
						<div class="elementor-widget-container">
							<img fetchpriority="high" decoding="async" width="574" height="659"
								src="wp-content/uploads/2024/06/about-us-img.png"
								class="attachment-full size-full wp-image-872" alt="">
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-a54d303 e-con-full e-flex e-con e-child"
					data-id="a54d303" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-11fc39c at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
						data-id="11fc39c" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h3 class="elementor-heading-title elementor-size-default">Welcome to TPV</h3>
						</div>
					</div>
					<div class="elementor-element elementor-element-2261fd2 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="2261fd2" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default">TPV Construction and Services LTD</h2>
						</div>
					</div>
					<div class="elementor-element elementor-element-8fbf117 elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="8fbf117" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p>Serving Nigeria's construction needs since 2008 with excellence and innovation. TPV Construction and Services LTD has established itself as a trusted name in Nigeria's construction industry, delivering exceptional projects across residential, commercial, and industrial sectors. Our commitment to quality, safety, and sustainable building practices has made us the preferred choice for discerning clients throughout Nigeria.</p>
						</div>
					</div>
					<div class="elementor-element elementor-element-b3ae5c3 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="b3ae5c3" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-element elementor-element-2b502a2 elementor-align-start elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
							data-id="2b502a2" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="icon-list.default">
							<div class="elementor-widget-container">
								<ul class="elementor-icon-list-items">
									<li class="elementor-icon-list-item">
										<span class="elementor-icon-list-icon">
											<svg aria-hidden="true" class="e-font-icon-svg e-fas-check-circle"
												viewbox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
												<path
													d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
												</path>
											</svg> </span>
										<span class="elementor-icon-list-text">Comprehensive Services</span>
									</li>
									<li class="elementor-icon-list-item">
										<span class="elementor-icon-list-icon">
											<svg aria-hidden="true" class="e-font-icon-svg e-fas-check-circle"
												viewbox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
												<path
													d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
												</path>
											</svg> </span>
										<span class="elementor-icon-list-text">Advanced Technology</span>
									</li>
									<li class="elementor-icon-list-item">
										<span class="elementor-icon-list-icon">
											<svg aria-hidden="true" class="e-font-icon-svg e-fas-check-circle"
												viewbox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
												<path
													d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
												</path>
											</svg> </span>
										<span class="elementor-icon-list-text">Transparent Communication</span>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-096a792 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="096a792" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:300}">
						<div class="elementor-element elementor-element-e62f738 elementor-widget elementor-widget-button"
							data-id="e62f738" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="button.default">
							<div class="elementor-widget-container">
								<div class="elementor-button-wrapper">
									<a class="elementor-button elementor-button-link elementor-size-sm"
										href="quote/">
										<span class="elementor-button-content-wrapper">
											<span class="elementor-button-icon">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
													viewbox="0 0 24 24" fill="none">
													<path
														d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
														stroke="white" stroke-width="1.5" stroke-linecap="round"
														stroke-linejoin="round"></path>
												</svg> </span>
											<span class="elementor-button-text">Get Free Quote</span>
										</span>
									</a>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-2614ad9 elementor-position-inline-start elementor-mobile-position-inline-start about-us-contact-support elementor-view-default elementor-widget elementor-widget-icon-box"
							data-id="2614ad9" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="icon-box.default">
							<div class="elementor-widget-container">
								<div class="elementor-icon-box-wrapper">
									<div class="elementor-icon-box-icon">
										<a href="tel:+2347012345678" class="elementor-icon" tabindex="-1"
											aria-label="call support center 24X7">
											<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
												viewbox="0 0 70 70" fill="none">
												<g clip-path="url(#clip0_389_16083)">
													<path
														d="M68.5417 32.0833C67.7367 32.0833 67.0833 31.43 67.0833 30.625C67.0833 15.3475 54.6525 2.91667 39.375 2.91667C38.57 2.91667 37.9167 2.26333 37.9167 1.45833C37.9167 0.653333 38.57 0 39.375 0C56.2625 0 70 13.7375 70 30.625C70 31.43 69.3467 32.0833 68.5417 32.0833ZM58.3333 30.625C58.3333 20.1717 49.8283 11.6667 39.375 11.6667C38.57 11.6667 37.9167 12.32 37.9167 13.125C37.9167 13.93 38.57 14.5833 39.375 14.5833C48.2213 14.5833 55.4167 21.7788 55.4167 30.625C55.4167 31.43 56.07 32.0833 56.875 32.0833C57.68 32.0833 58.3333 31.43 58.3333 30.625ZM64.8492 64.9571L67.7017 61.6758C69.1833 60.1942 70 58.2225 70 56.1283C70 54.0342 69.1833 52.0596 67.5588 50.4554L60.4246 44.9925C57.3679 41.9358 52.395 41.9329 49.3325 44.9838L44.8642 49.3413C34.1308 44.9692 25.3867 36.2163 20.6733 25.1242L25.0133 20.6763C28.0729 17.6167 28.0729 12.6379 25.1388 9.72125L19.425 2.30417C16.4587 -0.662083 11.2204 -0.592083 8.4 2.23708L4.97583 5.22375C1.76458 8.4175 0 12.7546 0 17.43C0 40.0312 29.9688 70 52.57 70C57.2454 70 61.5796 68.2354 64.8492 64.9571ZM17.2346 4.2175L22.9483 11.6346C24.8704 13.5567 24.8704 16.6863 22.9367 18.6229L17.9142 23.7738C17.5117 24.1879 17.3892 24.8004 17.605 25.3371C22.6479 37.8933 32.5179 47.7604 44.6892 52.4037C45.22 52.6079 45.8208 52.4825 46.2262 52.0887L51.3771 47.0662C51.3771 47.0662 51.3858 47.0575 51.3887 47.0517C53.2525 45.185 56.3588 45.0654 58.5054 47.1771L65.6396 52.64C67.5617 54.5621 67.5617 57.6888 65.5725 59.6867L62.7171 62.9679C60.0688 65.6221 56.4638 67.0833 52.57 67.0833C31.6867 67.0833 2.91667 38.3133 2.91667 17.43C2.91667 13.5363 4.37792 9.93125 6.96208 7.35L10.3892 4.36042C11.3692 3.38042 12.6613 2.90208 13.9329 2.90208C15.155 2.90208 16.3567 3.34542 17.2375 4.22042L17.2346 4.2175Z"
														fill="#E5363D"></path>
												</g>
												<defs>
													<clippath id="clip0_389_16083">
														<rect width="70" height="70" fill="white"></rect>
													</clippath>
												</defs>
											</svg>
										</a>
									</div>
									<div class="elementor-icon-box-content">
										<h3 class="elementor-icon-box-title">
											<a href="tel:+2347012345678">
												call support center 24X7
											</a>
										</h3>
										<p class="elementor-icon-box-description">
											+234 701 234 5678
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-22383b9 e-con-full e-flex e-con e-parent" data-id="22383b9"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="elementor-element elementor-element-4e96ca5 e-flex e-con-boxed e-con e-child" data-id="4e96ca5"
				data-element_type="container" data-e-type="container">
				<div class="e-con-inner">
					<div class="elementor-element elementor-element-1cba059 e-con-full e-flex e-con e-child"
						data-id="1cba059" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-8fe3355 at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
							data-id="8fe3355" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h3 class="elementor-heading-title elementor-size-default">Our services</h3>
							</div>
						</div>
						<div class="elementor-element elementor-element-8281ce8 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="8281ce8" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h2 class="elementor-heading-title elementor-size-default">Our construction services
								</h2>
							</div>
						</div>
						<div class="elementor-element elementor-element-3b44d86 elementor-widget__width-initial elementor-invisible elementor-widget elementor-widget-text-editor"
							data-id="3b44d86" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
								<p>We specialize in a wide range of construction services, including residential,
									commercial, and industrial projects.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="elementor-element elementor-element-4748a3f e-con-full e-flex e-con e-child" data-id="4748a3f"
				data-element_type="container" data-e-type="container">
				<!-- Architecture & Design Service -->
				<div class="elementor-element elementor-element-355dfec e-con-full e-flex elementor-invisible e-con e-child"
					data-id="355dfec" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;}">
					<div class="elementor-element elementor-element-1456859 service-item ekit-equal-height-disable elementor-widget elementor-widget-elementskit-image-box"
						data-id="1456859" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="elementskit-image-box.default">
						<div class="elementor-widget-container">
							<div class="ekit-wid-con">
								<div class="elementskit-info-image-box ekit-image-box text-left simple-card">
									<a href="services/architecture-design/">
										<div class="elementskit-box-header image-box-img-left">
											<img decoding="async" width="1200" height="800"
												src="wp-content/uploads/2024/06/service-img-2.jpg"
												class="attachment-full size-full wp-image-2599" alt="Architecture & Design">
										</div>
									</a>
									<div class="elementskit-box-body ekit-image-box-body">
										<div class="elementskit-box-content ekit-image-box-body-inner">
											<h3 class="elementskit-info-box-title">
												Architecture & Design
											</h3>
											<div class="elementskit-box-style-content">
												Our architectural team creates innovative designs that blend functionality with aesthetic appeal. We specialize in residential, commercial, and industrial architecture, ensuring every project meets modern standards while respecting local building codes.
											</div>
										</div>
										<div class="elementskit-box-footer">
											<div class="box-footer">
												<div class="btn-wraper">
													<a href="services/architecture-design/"
														class="elementskit-btn whitespace--normal">
														View More
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
															viewbox="0 0 24 24" fill="none">
															<path
																d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
																stroke="white" stroke-width="1.5" stroke-linecap="round"
																stroke-linejoin="round"></path>
														</svg>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Renovation & Remodeling Service -->
				<div class="elementor-element elementor-element-3d20c86 e-con-full e-flex elementor-invisible e-con e-child"
					data-id="3d20c86" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
					<div class="elementor-element elementor-element-9449951 service-item ekit-equal-height-disable elementor-widget elementor-widget-elementskit-image-box"
						data-id="9449951" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="elementskit-image-box.default">
						<div class="elementor-widget-container">
							<div class="ekit-wid-con">
								<div class="elementskit-info-image-box ekit-image-box text-left simple-card">
									<a href="services/building-renovation/">
										<div class="elementskit-box-header image-box-img-left">
											<img loading="lazy" decoding="async" width="1200" height="800"
												src="wp-content/uploads/2024/06/service-img-3.png"
												class="attachment-full size-full wp-image-2600" alt="Renovation & Remodeling">
										</div>
									</a>
									<div class="elementskit-box-body ekit-image-box-body">
										<div class="elementskit-box-content ekit-image-box-body-inner">
											<h3 class="elementskit-info-box-title">
												Renovation & Remodeling
											</h3>
											<div class="elementskit-box-style-content">
												Transform your existing space with our expert renovation services. From interior remodeling to structural upgrades, we breathe new life into buildings while maintaining their integrity and improving functionality for modern needs.
											</div>
										</div>
										<div class="elementskit-box-footer">
											<div class="box-footer">
												<div class="btn-wraper">
													<a href="services/building-renovation/"
														class="elementskit-btn whitespace--normal">
														View More
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
															viewbox="0 0 24 24" fill="none">
															<path
																d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
																stroke="white" stroke-width="1.5" stroke-linecap="round"
																stroke-linejoin="round"></path>
														</svg>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Construction & Building Service -->
				<div class="elementor-element elementor-element-323e15a e-con-full e-flex elementor-invisible e-con e-child"
					data-id="323e15a" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:200}">
					<div class="elementor-element elementor-element-28116e6 service-item ekit-equal-height-disable elementor-widget elementor-widget-elementskit-image-box"
						data-id="28116e6" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="elementskit-image-box.default">
						<div class="elementor-widget-container">
							<div class="ekit-wid-con">
								<div class="elementskit-info-image-box ekit-image-box text-left simple-card">
									<a href="services/building-construction/">
										<div class="elementskit-box-header image-box-img-left">
											<img decoding="async" width="1200" height="800"
												src="wp-content/uploads/2024/06/service-img-1.jpg"
												class="attachment-full size-full wp-image-2186" alt="Construction & Building">
										</div>
									</a>
									<div class="elementskit-box-body ekit-image-box-body">
										<div class="elementskit-box-content ekit-image-box-body-inner">
											<h3 class="elementskit-info-box-title">
												Construction & Building
											</h3>
											<div class="elementskit-box-style-content">
												From foundation to finishing, TPV Construction and Services LTD delivers complete building solutions. Our expertise covers residential homes, commercial complexes, and industrial facilities, all built to the highest standards of quality and safety.
											</div>
										</div>
										<div class="elementskit-box-footer">
											<div class="box-footer">
												<div class="btn-wraper">
													<a href="services/building-construction/"
														class="elementskit-btn whitespace--normal">
														View More
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
															viewbox="0 0 24 24" fill="none">
															<path
																d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
																stroke="white" stroke-width="1.5" stroke-linecap="round"
																stroke-linejoin="round"></path>
														</svg>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Interior & Exterior Service (New 4th Service) -->
				<div class="elementor-element elementor-element-c4ca1fa e-con-full e-flex elementor-invisible e-con e-child"
					data-id="c4ca1fa" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:300}">
					<div class="elementor-element elementor-element-d066bbb service-item ekit-equal-height-disable elementor-widget elementor-widget-elementskit-image-box"
						data-id="d066bbb" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="elementskit-image-box.default">
						<div class="elementor-widget-container">
							<div class="ekit-wid-con">
								<div class="elementskit-info-image-box ekit-image-box text-left simple-card">
									<a href="services/interior-exterior/">
										<div class="elementskit-box-header image-box-img-left">
											<img loading="lazy" decoding="async" width="1200" height="800"
												src="wp-content/uploads/2024/06/service-img-4.png"
												class="attachment-full size-full wp-image-2598" alt="Interior & Exterior">
										</div>
									</a>
									<div class="elementskit-box-body ekit-image-box-body">
										<div class="elementskit-box-content ekit-image-box-body-inner">
											<h3 class="elementskit-info-box-title">
												Interior & Exterior
											</h3>
											<div class="elementskit-box-style-content">
												Enhance your property with our comprehensive interior and exterior finishing services. From stunning interior designs, flooring, and painting to exterior cladding, landscaping, and facade improvements, we transform spaces inside and out with attention to detail and quality craftsmanship.
											</div>
										</div>
										<div class="elementskit-box-footer">
											<div class="box-footer">
												<div class="btn-wraper">
													<a href="services/interior-exterior/"
														class="elementskit-btn whitespace--normal">
														View More
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
															viewbox="0 0 24 24" fill="none">
															<path
																d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
																stroke="white" stroke-width="1.5" stroke-linecap="round"
																stroke-linejoin="round"></path>
														</svg>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="elementor-element elementor-element-97891ae e-flex e-con-boxed e-con e-child" data-id="97891ae"
				data-element_type="container" data-e-type="container">
				<div class="e-con-inner">
					<div class="elementor-element elementor-element-55792ef e-con-full e-flex elementor-invisible e-con e-child"
						data-id="55792ef" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
						<div class="elementor-element elementor-element-c5fa780 elementor-widget elementor-widget-button"
							data-id="c5fa780" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="button.default">
							<div class="elementor-widget-container">
								<div class="elementor-button-wrapper">
									<a class="elementor-button elementor-button-link elementor-size-sm"
										href="services/">
										<span class="elementor-button-content-wrapper">
											<span class="elementor-button-icon">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
													viewbox="0 0 24 24" fill="none">
													<path
														d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
														stroke="white" stroke-width="1.5" stroke-linecap="round"
														stroke-linejoin="round"></path>
												</svg> </span>
											<span class="elementor-button-text">view all services</span>
										</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-d78df6d e-flex e-con-boxed e-con e-parent" data-id="d78df6d"
			data-element_type="container" data-e-type="container">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-34b2dcf e-con-full e-flex e-con e-child"
					data-id="34b2dcf" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-33c961a at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading"
						data-id="33c961a" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h3 class="elementor-heading-title elementor-size-default">Why choose TPV?</h3>
						</div>
					</div>
					<div class="elementor-element elementor-element-c0e1873 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="c0e1873" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default">Building Nigeria's future with excellence since 2008</h2>
						</div>
					</div>
					<div class="elementor-element elementor-element-5ee97fd elementor-widget__width-initial elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="5ee97fd" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p>With over 400 successful projects completed across Nigeria, TPV Construction and Services LTD has earned the trust of clients through our commitment to quality, safety, and innovation. Our experienced team combines local expertise with modern construction techniques to deliver exceptional results every time.</p>
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-c1f1a59 e-con-full e-flex e-con e-child"
					data-id="c1f1a59" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-14476ba e-con-full e-flex elementor-invisible e-con e-child"
						data-id="14476ba" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;}">
						<div class="elementor-element elementor-element-b985f75 elementor-view-default elementor-position-block-start elementor-mobile-position-block-start elementor-widget elementor-widget-icon-box"
							data-id="b985f75" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="icon-box.default">
							<div class="elementor-widget-container">
								<div class="elementor-icon-box-wrapper">
									<div class="elementor-icon-box-icon">
										<span class="elementor-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
												viewbox="0 0 70 70" fill="none">
												<path fill-rule="evenodd" clip-rule="evenodd"
													d="M34.196 3.3508C33.8429 3.6986 33.84 3.72361 33.84 6.42943C33.84 9.13525 33.8429 9.16026 34.196 9.50806C34.4203 9.72925 34.7175 9.85886 35 9.85886C35.2825 9.85886 35.5797 9.72925 35.804 9.50806C36.1571 9.16026 36.16 9.13525 36.16 6.42943C36.16 3.72361 36.1571 3.6986 35.804 3.3508C35.5797 3.1296 35.2825 3 35 3C34.7175 3 34.4203 3.1296 34.196 3.3508ZM14.5438 11.4197C13.7997 12.153 13.9614 12.4817 16.1168 14.6149C17.1319 15.6196 18.1105 16.4788 18.2915 16.5244C19.078 16.7223 19.9246 15.8879 19.7238 15.1129C19.6776 14.9346 18.8057 13.9702 17.7862 12.9698C15.6215 10.8457 15.288 10.6864 14.5438 11.4197ZM54.2125 11.14C53.6879 11.3568 50.225 14.9336 50.225 15.2586C50.225 16.0604 50.9829 16.707 51.7085 16.5244C51.8895 16.4788 52.8681 15.6196 53.8832 14.6149C56.027 12.4931 56.1981 12.1509 55.4731 11.4364C55.0246 10.9944 54.7315 10.9256 54.2125 11.14ZM32.97 12.2343C28.2447 12.8272 24.2865 14.7001 21.0796 17.8604C18.2776 20.6217 16.4098 24.0787 15.6119 27.9802C15.254 29.7307 15.2641 33.4663 15.6315 35.2223C16.8021 40.8175 20.1672 45.5015 25.0478 48.3292L25.72 48.7187V54.6568C25.72 61.2617 25.7175 61.2424 26.6715 61.9602C27.1841 62.3459 27.3188 62.3751 28.7894 62.4203L30.36 62.4687V63.8182C30.36 65.417 30.5504 65.96 31.3116 66.5328L31.859 66.9446L34.804 66.9884C38.1239 67.0379 38.4629 66.9646 39.1769 66.0431C39.5353 65.5804 39.5716 65.4126 39.6162 64.0036L39.6649 62.468L41.2231 62.42C42.653 62.376 42.8232 62.3403 43.2927 61.987C44.2873 61.2389 44.28 61.2922 44.28 54.6632V48.7124L44.7317 48.4689C45.8026 47.8919 47.7146 46.4503 48.7827 45.4146C52.803 41.5161 54.9706 36.0856 54.6752 30.6523C54.4096 25.7696 52.3881 21.2749 48.9258 17.869C45.2361 14.2397 40.7914 12.3492 35.6525 12.2237C34.4562 12.1946 33.2491 12.1993 32.97 12.2343ZM32.2138 14.6531C29.6772 15.0423 26.7414 16.2702 24.629 17.8254C17.8382 22.8255 15.6753 31.8552 19.4859 39.2967C21.0638 42.378 23.961 45.2683 26.9835 46.7764C27.3994 46.9837 27.8071 47.2779 27.8898 47.4301C27.9724 47.5822 28.04 48.4504 28.04 49.3593V51.012H30.94H33.84V48.4019V45.7917L32.1821 44.1626C31.2703 43.2666 30.4835 42.3736 30.4338 42.1782C30.2294 41.3756 31.0505 40.5664 31.8355 40.7966C32.0208 40.8509 32.8086 41.5146 33.5862 42.2715L35 43.6476L36.4137 42.2715C37.1914 41.5146 37.9792 40.8509 38.1645 40.7966C38.9495 40.5664 39.7706 41.3756 39.5662 42.1782C39.5165 42.3736 38.7297 43.2666 37.8179 44.1626L36.16 45.7917V48.4019V51.012H39.06H41.96V49.3593C41.96 48.4504 42.0241 47.5887 42.1022 47.4447C42.1805 47.3005 42.7286 46.9208 43.3202 46.6009C47.1861 44.51 50.1798 40.9632 51.5194 36.8865C52.1577 34.9441 52.3275 33.8287 52.3275 31.5786C52.3275 30.3883 52.242 29.081 52.1264 28.5064C50.2647 19.2441 41.5962 13.2133 32.2138 14.6531ZM6.35598 30.7862C6.13152 31.0073 6 31.3002 6 31.5786C6 31.8569 6.13152 32.1499 6.35598 32.3709C6.70891 32.7189 6.73428 32.7217 9.48 32.7217C12.2257 32.7217 12.2511 32.7189 12.604 32.3709C12.8285 32.1499 12.96 31.8569 12.96 31.5786C12.96 31.3002 12.8285 31.0073 12.604 30.7862C12.2511 30.4383 12.2257 30.4354 9.48 30.4354C6.73428 30.4354 6.70891 30.4383 6.35598 30.7862ZM57.396 30.7862C57.1715 31.0073 57.04 31.3002 57.04 31.5786C57.04 31.8569 57.1715 32.1499 57.396 32.3709C57.7489 32.7189 57.7743 32.7217 60.52 32.7217C63.2657 32.7217 63.2911 32.7189 63.644 32.3709C63.8685 32.1499 64 31.8569 64 31.5786C64 31.3002 63.8685 31.0073 63.644 30.7862C63.2911 30.4383 63.2657 30.4354 60.52 30.4354C57.7743 30.4354 57.7489 30.4383 57.396 30.7862ZM17.9625 46.8535C17.7232 47.0022 16.7948 47.8539 15.8994 48.7463C13.9773 50.6619 13.8154 51.0196 14.5438 51.7375C15.2781 52.4611 15.6309 52.301 17.6464 50.3301C19.9339 48.0933 20.1105 47.7483 19.3512 47C18.857 46.513 18.5611 46.4817 17.9625 46.8535ZM50.6488 47C50.1737 47.4683 50.1323 47.7083 50.4303 48.2685C50.7144 48.8026 53.9938 51.9535 54.3914 52.0744C55.1121 52.2936 56.0205 51.3984 55.7981 50.6882C55.6754 50.2964 52.478 47.0646 51.936 46.7847C51.3676 46.491 51.124 46.5317 50.6488 47ZM28.04 54.4414V55.5846H35H41.96V54.4414V53.2983H35H28.04V54.4414ZM28.04 59.014V60.1572H35H41.96V59.014V57.8709H35H28.04V59.014ZM32.68 63.5866V64.7297H35H37.32V63.5866V62.4434H35H32.68V63.5866Z"
													fill="#E5363D"></path>
											</svg>
										</span>
									</div>
									<div class="elementor-icon-box-content">
										<h3 class="elementor-icon-box-title">
											<span>Innovative Solutions</span>
										</h3>
										<p class="elementor-icon-box-description">
											We combine modern construction technology with innovative approaches to deliver projects that exceed expectations while optimizing costs and timelines.
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-4369509 elementor-widget elementor-widget-counter"
							data-id="4369509" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="counter.default">
							<div class="elementor-widget-container">
								<div class="elementor-counter">
									<p class="elementor-counter-title">Projects Completed</p>
									<div class="elementor-counter-number-wrapper">
										<span class="elementor-counter-number-prefix"></span>
										<span class="elementor-counter-number" data-duration="3000" data-to-value="450"
											data-from-value="0" data-delimiter=",">0</span>
										<span class="elementor-counter-number-suffix">+</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-fd70748 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="fd70748" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
						<div class="elementor-element elementor-element-5c0a15b why-choose-us-img image-anime at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
							data-id="5c0a15b" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="image.default">
							<div class="elementor-widget-container">
								<img loading="lazy" decoding="async" width="413" height="461"
									src="wp-content/uploads/2024/06/why-choose-img-1.jpg"
									class="attachment-full size-full wp-image-1087" alt="">
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-a0233e8 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="a0233e8" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-element elementor-element-db1e688 elementor-view-default elementor-position-block-start elementor-mobile-position-block-start elementor-widget elementor-widget-icon-box"
							data-id="db1e688" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="icon-box.default">
							<div class="elementor-widget-container">
								<div class="elementor-icon-box-wrapper">
									<div class="elementor-icon-box-icon">
										<span class="elementor-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
												viewbox="0 0 70 70" fill="none">
												<path fill-rule="evenodd" clip-rule="evenodd"
													d="M13.1212 2.28588C12.7785 2.56241 8.06759 8.1842 7.65124 8.81336C7.19671 9.50028 7.50594 10.5392 8.26115 10.863C8.50004 10.9653 9.01075 11.0491 9.39608 11.0491H10.0965L10.1343 17.304L10.172 23.5589L10.4693 23.9866C10.9959 24.7441 11.2434 24.8033 13.8809 24.8033C16.5499 24.8033 16.928 24.7118 17.3837 23.9553C17.6323 23.5425 17.6411 23.3155 17.6411 17.3028V11.0778L18.5987 11.0282C19.4632 10.9834 19.5939 10.9399 19.9433 10.5816C20.4888 10.0218 20.5597 9.4137 20.1509 8.79777C19.619 7.99662 14.835 2.28339 14.5776 2.14205C14.1784 1.92277 13.4864 1.99115 13.1212 2.28588ZM34.9064 2.20296C34.6064 2.29911 33.7492 2.83291 33.0016 3.3891C32.2541 3.94529 31.4654 4.47476 31.2491 4.56554C31.0327 4.65632 30.074 4.78836 29.1186 4.85883C27.7153 4.96245 27.2478 5.04969 26.6868 5.31325C25.1894 6.01654 24.6271 6.86956 24.0311 9.34217C23.8155 10.2369 23.5458 11.1195 23.4318 11.3037C23.3179 11.4878 22.6359 12.2281 21.9164 12.9485C20.2963 14.5706 20.0076 15.195 20.0994 16.8783C20.1523 17.8476 20.2164 18.0491 20.9543 19.5636L21.7522 21.201L21.6456 22.642C21.4474 25.3184 21.4915 26.0872 21.8902 26.9203C22.3306 27.8402 23.0116 28.4832 24.1245 29.0297C24.5965 29.2614 25.0015 29.4611 25.0247 29.4736C25.0478 29.486 24.1428 31.5355 23.0138 34.0279C20.7981 38.9188 20.6999 39.245 21.2972 39.7269C21.7318 40.0777 21.8469 40.073 24.4291 39.5979C25.6265 39.3775 26.6209 39.2154 26.6389 39.2376C26.6569 39.2598 27.171 40.1622 27.7811 41.2429C28.9523 43.3171 29.3382 43.7972 29.8343 43.7972C30.4493 43.7972 30.7651 43.2587 32.8302 38.689L34.8721 34.1705L35.5143 34.0036C36.053 33.8635 36.2901 33.8684 36.9846 34.0337C38.6783 34.4366 38.468 34.165 40.0913 38.046C40.9912 40.1973 41.6574 41.6163 41.8538 41.7996C42.2314 42.1521 42.7976 42.1809 43.1456 41.8651C43.2845 41.7391 43.9304 40.7516 44.581 39.6709C45.2315 38.5903 45.7795 37.6871 45.7987 37.6639C45.818 37.6406 46.8172 37.8322 48.0193 38.0896C49.2214 38.3469 50.3659 38.5575 50.5626 38.5575C51.0182 38.5575 51.5187 38.0257 51.5169 37.5434C51.5161 37.3447 50.7821 35.4432 49.8856 33.3178C48.989 31.1925 48.2293 29.386 48.1972 29.3034C48.1651 29.2208 48.4052 29.0168 48.7306 28.8501C49.4595 28.4766 50.2601 27.6047 50.6213 26.7913C50.9273 26.1016 50.9725 24.9685 50.7893 22.5765L50.6839 21.201L51.4919 19.5636L52.2998 17.9262L52.3028 16.6163C52.3053 15.4038 52.2771 15.2537 51.9236 14.5988C51.6914 14.1686 51.1018 13.4752 50.42 12.8304C49.8031 12.2469 49.1894 11.5928 49.0561 11.3766C48.9229 11.1605 48.6327 10.2468 48.4114 9.34624C47.7998 6.85777 47.2446 6.01549 45.7547 5.3156C45.2075 5.0586 44.7324 4.96782 43.4506 4.87521C42.5626 4.81102 41.6349 4.70217 41.3891 4.63339C41.1434 4.56449 40.329 4.0417 39.5796 3.47162C38.8301 2.90155 37.9436 2.33959 37.6098 2.22288C36.8858 1.9698 35.6624 1.96089 34.9064 2.20296ZM57.5471 2.43639C56.7158 3.24737 52.3406 8.62774 52.1566 9.06513C51.9169 9.6352 52.0309 10.1325 52.5093 10.6027C52.8476 10.9352 53.0036 10.9849 53.8438 11.0284L54.7953 11.0778V17.3028C54.7953 23.3155 54.8041 23.5425 55.0527 23.9553C55.5094 24.7133 55.8841 24.8033 58.5811 24.8028C60.7412 24.8024 61.0294 24.7762 61.3957 24.5471C62.2949 23.9846 62.2603 24.2605 62.3011 17.3099L62.3377 11.0491H63.0393C63.4251 11.0491 63.9364 10.9653 64.1752 10.863C64.9255 10.5414 65.2428 9.49085 64.7926 8.81873C64.3734 8.19285 59.6338 2.54289 59.3059 2.27828C58.7823 1.8557 58.0774 1.9191 57.5471 2.43639ZM35.608 4.23282C35.4128 4.29609 34.7521 4.72981 34.1397 5.19653C32.0998 6.75115 31.9445 6.8118 29.5396 6.9927C27.713 7.13011 27.3186 7.26045 26.7941 7.89995C26.4229 8.3524 26.4307 8.33039 26.0606 10.0012C25.623 11.9764 25.2386 12.6708 23.8193 14.0489C22.5367 15.2942 22.1098 15.9374 22.1098 16.6246C22.1098 16.8577 22.4126 17.669 22.8086 18.4968C23.8852 20.7474 23.9666 21.1357 23.7735 23.0953C23.5587 25.2737 23.5752 25.644 23.9112 26.2018C24.1342 26.572 24.4354 26.7879 25.2917 27.1914C28.2063 28.565 28.2535 28.603 29.6169 30.6667C31.083 32.8859 31.3534 32.9812 33.9838 32.2044C34.891 31.9365 35.8198 31.7459 36.2182 31.7459C36.6166 31.7459 37.5454 31.9365 38.4526 32.2044C41.074 32.9786 41.3479 32.8798 42.8537 30.6185C44.07 28.7918 44.5526 28.375 46.3926 27.5618C48.0191 26.843 48.2147 26.7173 48.5354 26.1849C48.8648 25.6381 48.8833 25.2368 48.6749 23.164C48.4696 21.1218 48.5422 20.7661 49.6278 18.4968C50.0238 17.669 50.3266 16.8577 50.3266 16.6246C50.3266 15.938 49.9 15.2945 48.6222 14.0538C47.186 12.6593 46.9023 12.1429 46.385 9.98246C45.7652 7.39301 45.5299 7.19128 42.8954 6.99178C41.9616 6.92104 40.9835 6.80053 40.7218 6.7239C40.2783 6.59422 39.3833 6.01576 37.6865 4.7619C36.9389 4.20964 36.2331 4.02991 35.608 4.23282ZM12.099 6.72639L10.3565 8.82227H10.9443C11.4239 8.82227 11.6011 8.89287 11.9055 9.20516L12.2787 9.58805V16.1477V22.7074H13.9385H15.5983V16.1171C15.5983 9.67765 15.6041 9.52006 15.8572 9.24157C16.118 8.95418 16.3733 8.85855 17.0551 8.79266C17.4183 8.75756 17.3881 8.70987 15.7257 6.69364C14.7902 5.55886 13.9835 4.63051 13.9331 4.63051C13.8828 4.63051 13.0574 5.57366 12.099 6.72639ZM56.7227 6.69364C55.0583 8.70359 55.0238 8.75769 55.3836 8.79266C56.0636 8.85881 56.3186 8.95457 56.5792 9.24157C56.8323 9.52006 56.8381 9.67765 56.8381 16.1171V22.7074H58.4979H60.1577V16.1652V9.62289L60.4616 9.26044C60.6939 8.98339 60.9153 8.88134 61.4017 8.82737L62.0381 8.75677L60.3237 6.69364C59.381 5.55886 58.5694 4.63051 58.5203 4.63051C58.4712 4.63051 57.6624 5.55886 56.7227 6.69364ZM34.2392 8.36969C30.8366 9.11399 28.0212 11.6199 26.8322 14.9624C25.9004 17.5819 26.0551 20.3417 27.2772 22.9039C28.9881 26.4908 32.3191 28.6206 36.2182 28.6206C43.1501 28.6206 47.9287 21.6796 45.615 14.9717C45.144 13.606 44.5679 12.6535 43.4958 11.4678C41.1203 8.8406 37.5498 7.64556 34.2392 8.36969ZM35.3577 10.4154C32.0848 10.7497 29.3135 13.2739 28.5499 16.6163C28.252 17.9199 28.3529 19.8538 28.7803 21.0362C29.2239 22.2629 29.8371 23.2583 30.6798 24.1188C34.4893 28.0091 40.7984 26.9559 43.2509 22.0203C44.3947 19.7186 44.3327 16.7616 43.0929 14.4682C42.8279 13.978 42.2333 13.2063 41.7201 12.6865C39.9801 10.9246 37.8426 10.1615 35.3577 10.4154ZM37.2735 17.1253L34.541 19.6649L33.5852 18.7063C33.0595 18.179 32.4641 17.7051 32.2621 17.653C31.499 17.4565 30.7507 18.3051 31.0447 19.0334C31.2509 19.5441 33.8518 22.1444 34.2622 22.2502C34.4472 22.2978 34.7199 22.2701 34.8684 22.1886C35.2679 21.9692 41.1718 16.4337 41.3528 16.1088C41.5874 15.6874 41.5494 15.4317 41.1863 14.989C40.9391 14.6876 40.7486 14.5859 40.4308 14.5859C40.058 14.5859 39.6711 14.897 37.2735 17.1253ZM45.7113 30.3384C45.5253 30.5002 44.97 31.2567 44.4773 32.0196C43.3521 33.7619 42.7527 34.2984 41.5023 34.6825L41.041 34.8242L41.8215 36.6896C42.2508 37.7155 42.6496 38.5393 42.7075 38.5202C42.7656 38.5011 43.2014 37.8385 43.676 37.0478C44.1871 36.1964 44.6703 35.5442 44.8611 35.4483C45.1305 35.3127 45.4578 35.3427 46.8634 35.6315C47.7874 35.8213 48.5701 35.9496 48.6025 35.9163C48.6765 35.8405 46.2322 30.0428 46.1267 30.0437C46.0842 30.0441 45.8972 30.1766 45.7113 30.3384ZM25.3465 34.0071C24.4932 35.8975 23.8085 37.4588 23.8248 37.4768C23.8413 37.4948 24.482 37.3925 25.2485 37.2496C26.816 36.9572 27.3815 36.9265 27.7253 37.1153C27.8552 37.1867 28.3476 37.9272 28.8197 38.7611C29.2916 39.5948 29.7232 40.2291 29.7789 40.1704C29.8347 40.1119 30.4003 38.9145 31.036 37.5096L32.1918 34.9552L31.4321 34.7963C29.8669 34.4687 29.219 33.9471 27.9615 32.0013C27.4517 31.2125 27.0037 30.5678 26.9662 30.5686C26.9285 30.5695 26.1996 32.1169 25.3465 34.0071ZM21.7268 44.5112C20.603 44.8073 19.7069 45.2684 16.1 47.4072C14.2308 48.5155 12.6207 49.4511 12.522 49.4862C12.4075 49.5268 12.1761 49.2762 11.883 48.7938C11.3337 47.8899 10.7354 47.5313 9.78894 47.5386C9.24095 47.5428 8.93363 47.6691 7.61842 48.4308C5.90742 49.4219 5.32968 49.9186 5.1162 50.5821C4.79483 51.581 4.90042 51.8217 8.64802 58.6313C12.9907 66.5225 12.9044 66.3801 13.5455 66.7156C14.4811 67.2053 15.0754 67.0971 16.8112 66.1205C18.8031 65 19.2172 64.6043 19.4351 63.6132C19.5618 63.0371 19.3817 62.2883 18.9262 61.4957L18.5495 60.8402L19.1486 60.4948L19.7478 60.1492L23.8973 61.2917C32.3604 63.6218 31.7153 63.475 33.2178 63.4133C34.2621 63.3705 34.7292 63.2868 35.3312 63.0349C36.5668 62.5177 52.7346 52.2606 53.2874 51.6431C53.8722 50.9899 54.1869 50.0496 54.1808 48.9735C54.1679 46.6679 52.3817 45.0839 50.0138 45.2781C48.3043 45.4182 47.6324 45.743 41.6412 49.3246C37.1695 51.9977 36.5405 52.3585 36.4722 52.2884C36.4493 52.2649 36.4928 51.9672 36.5688 51.6265C36.8494 50.3679 36.2666 48.9981 35.0428 48.0404C34.6351 47.7215 34.1701 47.5442 33.0245 47.2709C31.4709 46.9002 29.4833 46.263 26.2794 45.1086C24.1328 44.3351 22.9729 44.183 21.7268 44.5112ZM21.7268 46.788C21.3406 46.9195 20.8235 47.1344 20.5777 47.2656C20.2142 47.4595 13.5861 51.3684 13.4527 51.4675C13.4313 51.4834 14.3403 53.1768 15.4726 55.2305L17.5314 58.9645L18.3823 58.4545C18.8502 58.1739 19.3843 57.9444 19.5692 57.9444C19.7539 57.9444 22.5766 58.6798 25.8416 59.5785C29.1067 60.4772 32.0959 61.251 32.4844 61.2978C33.2567 61.3909 34.2771 61.1981 35.0053 60.8215C35.251 60.6943 39.1005 58.2839 43.5597 55.4651C52.0893 50.073 52.1141 50.0547 52.1141 49.1329C52.1141 48.5858 51.7954 47.9072 51.4196 47.6547C50.9494 47.3386 49.4942 47.4525 48.4675 47.8859C48.0419 48.0655 45.495 49.5267 42.8076 51.1329C40.1203 52.7393 37.5671 54.2036 37.1338 54.3871C36.0289 54.855 34.1487 54.9257 32.4345 54.5635C29.9989 54.0491 26.2761 52.9278 25.9721 52.6172C25.3877 52.02 25.8343 50.8708 26.6508 50.8708C26.8373 50.8708 28.1362 51.1934 29.5371 51.5878C32.5136 52.4257 33.1874 52.5416 33.7327 52.3099C34.532 51.9702 34.7528 50.7737 34.1434 50.0854C33.8379 49.7402 33.5354 49.6224 31.8133 49.1788C30.7249 48.8984 28.5703 48.2113 27.0254 47.6518C23.8265 46.4934 22.9918 46.3573 21.7268 46.788ZM8.44833 50.4186C7.78121 50.8107 7.21701 51.2194 7.19441 51.3269C7.15623 51.5095 14.1639 64.3685 14.4934 64.7201C14.6112 64.846 14.9317 64.7168 15.9745 64.1234C16.7065 63.7069 17.325 63.3058 17.3489 63.2321C17.4183 63.0184 10.0503 49.761 9.84601 49.7321C9.74438 49.7177 9.11544 50.0267 8.44833 50.4186ZM8.80672 51.493C8.70113 51.619 8.57779 51.8291 8.53247 51.9597C8.42267 52.2767 8.76242 52.9266 9.0945 53.0347C10.035 53.3409 10.7307 52.1831 10.035 51.4694C9.76507 51.1925 9.04675 51.2063 8.80672 51.493Z"
													fill="#E5363D"></path>
											</svg>
										</span>
									</div>
									<div class="elementor-icon-box-content">
										<h3 class="elementor-icon-box-title">
											<span>Quality Craftsmanship</span>
										</h3>
										<p class="elementor-icon-box-description">
											Our skilled craftsmen take pride in their work, ensuring every detail meets the highest standards of Nigerian and international construction quality.
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-75f5ac1 elementor-widget elementor-widget-counter"
							data-id="75f5ac1" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="counter.default">
							<div class="elementor-widget-container">
								<div class="elementor-counter">
									<p class="elementor-counter-title">Projects Completed</p>
									<div class="elementor-counter-number-wrapper">
										<span class="elementor-counter-number-prefix"></span>
										<span class="elementor-counter-number" data-duration="3000" data-to-value="450"
											data-from-value="0" data-delimiter=",">0</span>
										<span class="elementor-counter-number-suffix">+</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-4a50d54 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="4a50d54" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:300}">
						<div class="elementor-element elementor-element-c15b894 why-choose-us-img image-anime at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
							data-id="c15b894" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="image.default">
							<div class="elementor-widget-container">
								<img loading="lazy" decoding="async" width="413" height="461"
									src="wp-content/uploads/2024/06/why-choose-img-2.jpg"
									class="attachment-full size-full wp-image-1085" alt="">
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-f2fa51c e-con-full e-flex elementor-invisible e-con e-child"
						data-id="f2fa51c" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:400}">
						<div class="elementor-element elementor-element-5131db5 elementor-view-default elementor-position-block-start elementor-mobile-position-block-start elementor-widget elementor-widget-icon-box"
							data-id="5131db5" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="icon-box.default">
							<div class="elementor-widget-container">
								<div class="elementor-icon-box-wrapper">
									<div class="elementor-icon-box-icon">
										<span class="elementor-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
												viewbox="0 0 70 70" fill="none">
												<path fill-rule="evenodd" clip-rule="evenodd"
													d="M42.315 3.2268C41.0489 3.67151 40.4283 4.15038 39.0427 5.75156C38.5572 6.31246 38.0447 6.75743 37.6675 6.9455C37.1042 7.22627 36.9235 7.24557 34.8158 7.24887L32.5656 7.25243L31.753 7.64431C30.213 8.38706 28.9785 9.8577 28.564 11.443C28.4053 12.0501 28.3817 12.5441 28.4481 13.8743C28.576 16.4392 28.5287 16.5369 25.9917 18.9552C24.8767 20.0181 24.2521 21.4972 24.2521 23.0749V23.7962L23.2208 23.7345C21.8619 23.6534 20.6079 23.9274 19.2516 24.6019C17.2749 25.5851 15.6248 27.6521 15.057 29.8562C14.794 30.8778 14.7953 32.814 15.06 33.8461C15.6456 36.1302 17.2221 38.0907 19.3453 39.1753C20.5497 39.7904 21.5722 39.9857 23.252 39.9213C24.472 39.8745 24.8318 39.8092 25.6285 39.4898C26.8463 39.0018 27.9392 38.2401 28.7695 37.3009L29.4348 36.5482L30.2085 37.2833C31.595 38.6008 33.0925 39.0684 35.0696 38.801C37.0014 38.54 38.0028 38.9646 39.3467 40.6147C40.7446 42.3309 42.0781 43.0194 44.0043 43.0194C45.9305 43.0194 47.2641 42.3309 48.662 40.6147C50.0059 38.9646 51.0072 38.54 52.9391 38.801C54.9182 39.0686 56.4136 38.6009 57.8041 37.2795C59.2843 35.8731 59.6854 34.6787 59.5588 32.055L59.4748 30.3173L59.852 29.4985C60.1631 28.8233 60.4039 28.533 61.2257 27.8423C62.3285 26.9157 62.6937 26.4807 63.1607 25.5386C64.2566 23.3278 63.7906 20.6459 62.017 18.9552C59.4799 16.5369 59.4327 16.4392 59.5606 13.8743C59.6269 12.5441 59.6033 12.0501 59.4447 11.443C59.0301 9.8577 57.7956 8.38706 56.2557 7.64431L55.4431 7.25243L53.1928 7.25014C50.9919 7.24798 50.9285 7.24049 50.2975 6.90461C49.9021 6.69419 49.3698 6.23081 48.9224 5.7075C47.5518 4.10492 47.0055 3.6804 45.7683 3.25702C44.8029 2.92673 43.2091 2.91276 42.315 3.2268ZM43.096 4.90697C42.0532 5.17288 41.3474 5.73188 40.3237 7.10207C39.8075 7.79301 38.74 8.52751 37.7809 8.85171C37.0311 9.10517 36.801 9.12308 35.2542 9.04803C33.3041 8.95342 32.6584 9.08168 31.7803 9.73757C30.6803 10.5594 30.1899 11.6626 30.2623 13.1526C30.2879 13.679 30.3026 14.5669 30.2947 15.1256C30.2697 16.921 29.5977 18.1943 27.9134 19.6379C26.6086 20.7561 26.232 21.3927 26.1552 22.6091C26.0582 24.1437 26.1365 24.3505 27.0251 24.9076C28.6451 25.9233 29.9484 27.6479 30.5248 29.5387C30.7601 30.3103 30.8125 30.7563 30.8011 31.888C30.793 32.7013 30.709 33.5646 30.6 33.9547C30.4154 34.615 30.4176 34.6322 30.7494 35.1636C31.137 35.7846 31.6917 36.2889 32.426 36.6879C32.9017 36.9463 33.0871 36.9651 34.8777 36.9357C37.6675 36.8901 38.7376 37.2595 40.0861 38.7336C40.4753 39.1592 41.014 39.7471 41.2833 40.0402C42.0505 40.8756 42.7379 41.1581 44.0043 41.1581C45.2707 41.1581 45.9582 40.8756 46.7254 40.0402C46.9947 39.7471 47.5333 39.1592 47.9225 38.7336C49.2723 37.258 50.3545 36.8846 53.1385 36.9336C54.8093 36.9629 55.1306 36.936 55.5337 36.7323C56.7214 36.1325 57.6985 34.7951 57.7741 33.6658C57.7929 33.3864 57.7655 32.615 57.7135 31.9515C57.5228 29.5239 57.9989 28.3859 59.9922 26.5041C61.7083 24.8841 61.9346 24.4339 61.8574 22.794C61.7915 21.3939 61.468 20.8146 60.0952 19.6379C58.4074 18.1914 57.736 16.9169 57.7179 15.1256C57.7121 14.5669 57.7255 13.624 57.7475 13.0304C57.7816 12.1088 57.7444 11.8581 57.4933 11.316C57.1222 10.5146 56.2899 9.66239 55.5301 9.30581C54.9922 9.05336 54.7757 9.0333 52.9428 9.0672C51.1883 9.09971 50.8505 9.07203 50.1925 8.84231C49.1633 8.48306 48.2387 7.80774 47.5132 6.88569C46.6711 5.81518 46.2866 5.46711 45.6099 5.16323C44.9258 4.85592 43.7612 4.73719 43.096 4.90697ZM42.7416 9.99345C39.9529 10.2327 36.9735 11.6593 34.9323 13.7327C28.6291 20.1354 30.4066 30.8482 38.4302 34.813C39.0649 35.1267 40.1428 35.5334 40.8254 35.7169C41.9103 36.0087 42.3105 36.0506 44.0043 36.0506C45.6981 36.0506 46.0983 36.0087 47.1833 35.7169C52.221 34.3626 55.9322 30.1827 56.7606 24.9302C56.968 23.6159 56.8515 21.1422 56.5221 19.8654C55.0699 14.2358 50.3469 10.278 44.6919 9.9518C44.2106 9.92399 43.333 9.94278 42.7416 9.99345ZM42.2116 11.8849C37.7621 12.7064 34.2959 15.9795 33.2266 20.3691C32.8485 21.9216 32.855 24.2263 33.2416 25.7174C33.5517 26.9129 34.0716 28.1496 34.6472 29.0606L35.0317 29.6693L35.3984 28.8817C36.3515 26.8344 37.971 25.1917 39.9948 24.2195L40.7422 23.8603L40.3646 23.4931C40.1569 23.2911 39.7855 22.7117 39.5391 22.2057C37.6036 18.229 41.3892 13.8148 45.5465 15.2011C48.4346 16.1641 49.8081 19.4555 48.4696 22.2057C48.2232 22.7117 47.8518 23.2911 47.6441 23.4931L47.2664 23.8603L48.0139 24.2195C50.0376 25.1917 51.6572 26.8344 52.6103 28.8817L52.977 29.6693L53.3615 29.0606C55.2303 26.1023 55.5947 22.1811 54.3101 18.8515C53.3629 16.3962 51.3091 14.1372 48.917 12.9195C47.522 12.2094 46.1277 11.8713 44.3794 11.8195C43.5199 11.7941 42.5444 11.8235 42.2116 11.8849ZM43.3621 16.7967C43.1807 16.8283 42.7311 17.0369 42.3628 17.2606C41.6038 17.7213 40.9646 18.6582 40.8312 19.5053C40.4994 21.6132 42.4307 23.5749 44.5058 23.2379C46.2084 22.9613 47.5161 21.0638 47.1487 19.4027C46.76 17.6457 45.0895 16.496 43.3621 16.7967ZM43.2542 25.1686C41.1264 25.529 39.8142 26.1617 38.5769 27.4245C37.6552 28.365 36.9079 29.6096 36.6315 30.6641C36.4975 31.1753 36.5086 31.2424 36.7762 31.5336C37.6623 32.4976 39.9836 33.6685 41.7632 34.0487C42.8952 34.2906 45.1135 34.2906 46.2455 34.0487C48.025 33.6685 50.3464 32.4976 51.2325 31.5336C51.5 31.2424 51.5112 31.1753 51.3771 30.6641C50.8893 28.8028 49.2762 26.8706 47.4921 26.0104C46.9146 25.7319 46.1889 25.4443 45.8795 25.3711C45.2046 25.2117 43.6897 25.0948 43.2542 25.1686ZM22.0019 25.6062C19.9919 25.8713 18.1225 27.2462 17.2587 29.0943C16.0774 31.6218 16.5794 34.4507 18.5505 36.3733C20.5343 38.3083 23.2429 38.7056 25.7219 37.4248C27.1806 36.6712 28.2845 35.3237 28.7679 33.7068C28.9817 32.992 29.0217 32.5844 28.9828 31.5181C28.9377 30.2793 28.9063 30.1485 28.4082 29.1277C27.1892 26.6287 24.6852 25.2523 22.0019 25.6062ZM23.6073 30.642C22.5209 31.7378 21.6136 32.6091 21.5908 32.5785C21.568 32.5479 21.3036 32.0943 21.0032 31.5705C20.3977 30.5151 20.1997 30.3107 19.7773 30.3047C19.4153 30.2996 18.8765 30.8356 18.8765 31.2009C18.8765 31.4944 20.3703 34.2301 20.7946 34.7134C20.9521 34.893 21.1887 34.9992 21.4311 34.9992C21.7656 34.9992 22.1434 34.667 24.2845 32.49C26.4526 30.2857 26.7524 29.932 26.7524 29.5785C26.7524 29.1294 26.2802 28.6498 25.8381 28.6498C25.6754 28.6498 24.8643 29.3741 23.6073 30.642ZM23.3145 40.4067C22.0906 40.7056 21.4126 40.9482 20.4987 41.4139C18.5169 42.4243 16.1396 44.4294 13.8285 47.0402L12.4782 48.5655L11.5725 48.4959C10.0889 48.382 9.98518 48.446 7.34401 51.1063C5.32629 53.1384 5 53.5208 5 53.8529C5 54.1929 5.74596 54.9964 11.2824 60.6194C16.7197 66.1417 17.6142 67 17.9316 67C18.2395 67 18.6553 66.6394 20.5285 64.7471C23.1061 62.1435 23.3038 61.8353 23.2313 60.5323L23.1829 59.6632L23.8738 59.439C26.1295 58.7068 29.2802 58.5745 34.5937 58.9886C41.6488 59.5383 44.4157 59.2656 48.1298 57.6539C52.0118 55.9692 60.6235 50.7156 63.2773 48.4131C64.0263 47.7632 64.3233 47.4009 64.6361 46.7555C64.9908 46.0236 65.0304 45.8354 64.9845 45.102C64.9164 44.0159 64.4737 43.3364 63.5057 42.832C62.8968 42.5148 62.7103 42.4789 61.8609 42.515C60.9587 42.5534 60.8167 42.6024 59.4232 43.3557C56.1728 45.113 51.8466 47.1823 48.4736 48.5936L47.0047 49.208V48.6026C47.0047 47.7196 46.6811 46.8946 46.0592 46.1921C45.205 45.2269 44.6569 45.0474 42.1365 44.9083C38.2997 44.6962 36.0394 44.1426 31.5144 42.3067C27.5911 40.7148 26.6745 40.4477 24.9397 40.3907C24.1834 40.3659 23.452 40.373 23.3145 40.4067ZM23.7511 42.2355C22.4533 42.403 21.1213 43.0022 19.5016 44.1466C18.5286 44.8341 13.876 49.4768 13.876 49.7601C13.876 49.8474 15.7099 51.7822 17.9513 54.0599L22.0266 58.2009L22.9206 57.8493C25.528 56.8236 28.6277 56.6463 34.7009 57.1756C38.087 57.4706 42.1256 57.4733 43.551 57.1812C45.9827 56.683 47.8733 55.9049 51.3561 53.9686C56.5161 51.1 61.8654 47.544 62.7618 46.3865C63.3982 45.5645 63.3357 44.6926 62.6186 44.3909C62.0913 44.1691 61.4553 44.316 60.382 44.9076C55.844 47.4091 48.9381 50.5348 45.192 51.7827C43.0255 52.5044 42.1404 52.6508 39.9616 52.6479C36.9475 52.6439 33.7326 52.0855 31.9141 51.2499C31.3082 50.9714 31.119 50.8172 31.0524 50.5475C30.8454 49.7098 31.5013 49.1353 32.2888 49.4646C34.3199 50.3141 36.9364 50.7766 39.8164 50.7949C42.8966 50.8143 44.1687 50.4792 44.9191 49.4501C45.2277 49.0268 45.2418 48.0815 44.9463 47.6235C44.5321 46.9813 44.1331 46.8545 42.2538 46.7676C38.1514 46.578 35.5041 45.9205 30.3709 43.816C26.8637 42.3782 25.3835 42.0247 23.7511 42.2355ZM8.91381 52.0441L7.12011 53.8506L12.5296 59.3477L17.9389 64.845L19.7204 63.0249C21.3153 61.3953 21.5018 61.1593 21.5018 60.7708C21.5018 60.3686 21.1363 59.9654 16.5308 55.2872C11.9306 50.6144 11.528 50.2377 11.1337 50.2377C10.7529 50.2377 10.5167 50.4298 8.91381 52.0441Z"
													fill="#E5363D"></path>
											</svg>
										</span>
									</div>
									<div class="elementor-icon-box-content">
										<h3 class="elementor-icon-box-title">
											<span>Expertise And Experience</span>
										</h3>
										<p class="elementor-icon-box-description">
											With over a decade of experience in the Nigerian construction industry, our team brings deep local knowledge and proven expertise to every project.
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-da56358 elementor-widget elementor-widget-counter"
							data-id="da56358" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="counter.default">
							<div class="elementor-widget-container">
								<div class="elementor-counter">
									<p class="elementor-counter-title">Projects Completed</p>
									<div class="elementor-counter-number-wrapper">
										<span class="elementor-counter-number-prefix"></span>
										<span class="elementor-counter-number" data-duration="3000" data-to-value="450"
											data-from-value="0" data-delimiter=",">0</span>
										<span class="elementor-counter-number-suffix">+</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-a4e8952 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="a4e8952" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:500}">
						<div class="elementor-element elementor-element-59cc9d2 why-choose-us-img image-anime at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
							data-id="59cc9d2" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="image.default">
							<div class="elementor-widget-container">
								<img loading="lazy" decoding="async" width="413" height="461"
									src="wp-content/uploads/2024/06/why-choose-img-3.jpg"
									class="attachment-full size-full wp-image-1086" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="elementor-element elementor-element-a2545d6 e-con-full e-flex e-con e-parent" data-id="a2545d6"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="elementor-element elementor-element-128d785 e-flex e-con-boxed e-con e-child" data-id="128d785"
				data-element_type="container" data-e-type="container">
				<div class="e-con-inner">
					<div class="elementor-element elementor-element-ab5180f e-con-full e-flex e-con e-child"
						data-id="ab5180f" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-762e6d9 at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading"
							data-id="762e6d9" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h3 class="elementor-heading-title elementor-size-default">Our projects</h3>
							</div>
						</div>
						<div class="elementor-element elementor-element-8e04e0a at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="8e04e0a" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h2 class="elementor-heading-title elementor-size-default">Explore our diverse range of
									projects</h2>
							</div>
						</div>
						<div class="elementor-element elementor-element-c61dc47 elementor-widget__width-initial elementor-widget-tablet__width-inherit elementor-widget-mobile__width-inherit elementor-invisible elementor-widget elementor-widget-text-editor"
							data-id="c61dc47" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
								<p>We specialize in a wide range of construction services, including residential,
									commercial, and industrial projects.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="elementor-element elementor-element-cf3dc23 e-con-full e-flex e-con e-child" data-id="cf3dc23"
				data-element_type="container" data-e-type="container">
				<div class="elementor-element elementor-element-f5e48a0 elementor-grid-4 elementor-grid-tablet-2 elementor-grid-mobile-1 awaiken-portfolio-gutter-30 elementor-invisible elementor-widget elementor-widget-TPV Construction Services-portfolio-grid"
					data-id="f5e48a0" data-element_type="widget" data-e-type="widget"
					data-settings="{&quot;columns&quot;:&quot;4&quot;,&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;grid_layout&quot;:&quot;grid&quot;,&quot;aspect_ratio&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:1,&quot;sizes&quot;:[]},&quot;columns_tablet&quot;:&quot;2&quot;,&quot;columns_mobile&quot;:&quot;1&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
					data-widget_type="TPV Construction Services-portfolio-grid.default">
					<div class="elementor-widget-container">
						<div id="awaiken-portfolio-f5e48a0">
							<div
								class="awaiken-portfolio-grid elementor-grid awaiken-portfolio-layout-grid awaiken-portfolio-item-design-1">
								<article
									class="awaiken-portfolio-grid-item  post-4672 awaiken-portfolio type-awaiken-portfolio status-publish has-post-thumbnail hentry">
									<div class="awaiken-portfolio-grid-item__wrapper">
										<a class="awaiken-portfolio-grid-item__link"
											href="projects/aspen-heights/">
											<div class="awaiken-portfolio-grid-item__img">
												<img loading="lazy" decoding="async" width="1200" height="800"
													src="wp-content/uploads/2024/06/our-project-1-1.jpg"
													class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
													alt="">
											</div>
										</a>
										<div class="awaiken-portfolio-grid-item__content">
											<div class="awaiken-portfolio-grid-title__wrap">
												<a class="awaiken-portfolio-grid-item__link"
													href="projects/aspen-heights/">
													<h4 class="awaiken-portfolio-grid-item__title">Aspen Heights</h4>
												</a>
												<div class="awaiken-portfolio-grid-item__excerpt">
													<p>Our post-construction services gives you peace of mind knowing
														that we are still here for you even after.</p>
												</div>
											</div>

											<div class="awaiken-portfolio-grid-item__readmore">
												<a class="awaiken-portfolio-grid-item__link btn-default"
													href="projects/aspen-heights/">view more</a>
											</div>
										</div>
									</div>
								</article>
								<article
									class="awaiken-portfolio-grid-item  post-4670 awaiken-portfolio type-awaiken-portfolio status-publish has-post-thumbnail hentry">
									<div class="awaiken-portfolio-grid-item__wrapper">
										<a class="awaiken-portfolio-grid-item__link"
											href="projects/forest-hill-towers/">
											<div class="awaiken-portfolio-grid-item__img">
												<img loading="lazy" decoding="async" width="1200" height="800"
													src="wp-content/uploads/2024/06/our-project-2.jpg"
													class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
													alt="">
											</div>
										</a>
										<div class="awaiken-portfolio-grid-item__content">
											<div class="awaiken-portfolio-grid-title__wrap">
												<a class="awaiken-portfolio-grid-item__link"
													href="projects/forest-hill-towers/">
													<h4 class="awaiken-portfolio-grid-item__title">Forest Hill Towers
													</h4>
												</a>
												<div class="awaiken-portfolio-grid-item__excerpt">
													<p>Our post-construction services gives you peace of mind knowing
														that we are still here for you even after.</p>
												</div>
											</div>

											<div class="awaiken-portfolio-grid-item__readmore">
												<a class="awaiken-portfolio-grid-item__link btn-default"
													href="projects/forest-hill-towers/">view more</a>
											</div>
										</div>
									</div>
								</article>
								<article
									class="awaiken-portfolio-grid-item  post-4668 awaiken-portfolio type-awaiken-portfolio status-publish has-post-thumbnail hentry">
									<div class="awaiken-portfolio-grid-item__wrapper">
										<a class="awaiken-portfolio-grid-item__link"
											href="projects/bayside-residences/">
											<div class="awaiken-portfolio-grid-item__img">
												<img loading="lazy" decoding="async" width="1200" height="800"
													src="wp-content/uploads/2024/06/our-project-3.jpg"
													class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
													alt="">
											</div>
										</a>
										<div class="awaiken-portfolio-grid-item__content">
											<div class="awaiken-portfolio-grid-title__wrap">
												<a class="awaiken-portfolio-grid-item__link"
													href="projects/bayside-residences/">
													<h4 class="awaiken-portfolio-grid-item__title">Bayside Residences
													</h4>
												</a>
												<div class="awaiken-portfolio-grid-item__excerpt">
													<p>Our post-construction services gives you peace of mind knowing
														that we are still here for you even after.</p>
												</div>
											</div>

											<div class="awaiken-portfolio-grid-item__readmore">
												<a class="awaiken-portfolio-grid-item__link btn-default"
													href="projects/bayside-residences/">view more</a>
											</div>
										</div>
									</div>
								</article>
								<article
									class="awaiken-portfolio-grid-item  post-4666 awaiken-portfolio type-awaiken-portfolio status-publish has-post-thumbnail hentry">
									<div class="awaiken-portfolio-grid-item__wrapper">
										<a class="awaiken-portfolio-grid-item__link"
											href="projects/parkview-plaza/">
											<div class="awaiken-portfolio-grid-item__img">
												<img loading="lazy" decoding="async" width="1200" height="800"
													src="wp-content/uploads/2024/06/our-project-4.jpg"
													class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
													alt="">
											</div>
										</a>
										<div class="awaiken-portfolio-grid-item__content">
											<div class="awaiken-portfolio-grid-title__wrap">
												<a class="awaiken-portfolio-grid-item__link"
													href="projects/parkview-plaza/">
													<h4 class="awaiken-portfolio-grid-item__title">Parkview Plaza</h4>
												</a>
												<div class="awaiken-portfolio-grid-item__excerpt">
													<p>Our post-construction services gives you peace of mind knowing
														that we are still here for you even after.</p>
												</div>
											</div>

											<div class="awaiken-portfolio-grid-item__readmore">
												<a class="awaiken-portfolio-grid-item__link btn-default"
													href="projects/parkview-plaza/">view more</a>
											</div>
										</div>
									</div>
								</article>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="elementor-element elementor-element-4c529fc e-flex e-con-boxed e-con e-child" data-id="4c529fc"
				data-element_type="container" data-e-type="container">
				<div class="e-con-inner">
					<div class="elementor-element elementor-element-20ac1c8 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="20ac1c8" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-element elementor-element-e2a73b9 elementor-widget elementor-widget-button"
							data-id="e2a73b9" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="button.default">
							<div class="elementor-widget-container">
								<div class="elementor-button-wrapper">
									<a class="elementor-button elementor-button-link elementor-size-sm"
										href="projects/">
										<span class="elementor-button-content-wrapper">
											<span class="elementor-button-icon">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
													viewbox="0 0 24 24" fill="none">
													<path
														d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
														stroke="white" stroke-width="1.5" stroke-linecap="round"
														stroke-linejoin="round"></path>
												</svg> </span>
											<span class="elementor-button-text">view all projects</span>
										</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="elementor-element elementor-element-a52dd4a e-flex e-con-boxed e-con e-parent" data-id="a52dd4a"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-017540c e-con-full e-flex e-con e-child"
					data-id="017540c" data-element_type="container" data-e-type="container"
					data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
					<div class="elementor-element elementor-element-e5223c8 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="e5223c8" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default">From Concept to Completion — We Build It All</h2>
						</div>
					</div>
					<div class="elementor-element elementor-element-b27a18a elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="b27a18a" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p>Whether you're planning a residential dream home, a commercial development, or an industrial facility, TPV Construction and Services LTD provides end-to-end solutions tailored to the Nigerian market. Our integrated approach ensures seamless execution from design to handover.</p>
						</div>
					</div>
					<div class="elementor-element elementor-element-d69f8b0 btn-transparent elementor-invisible elementor-widget elementor-widget-button"
						data-id="d69f8b0" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:200,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="button.default">
						<div class="elementor-widget-container">
							<div class="elementor-button-wrapper">
								<a class="elementor-button elementor-button-link elementor-size-sm" href="quote/">
									<span class="elementor-button-content-wrapper">
										<span class="elementor-button-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
												<path d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
											</svg>
										</span>
										<span class="elementor-button-text">Start Your Project</span>
									</span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-1cbbf8f e-con-full e-flex e-con e-child"
					data-id="1cbbf8f" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-2e318dc at-image-animation at-animation-image-none elementor-widget elementor-widget-image"
						data-id="2e318dc" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="image.default">
						<div class="elementor-widget-container">
							<img loading="lazy" decoding="async" width="483" height="455"
								src="wp-content/uploads/2024/06/cta-box-img.png"
								class="attachment-large size-large wp-image-1251" alt="TPV Construction and Services LTD">
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="elementor-element elementor-element-7bb9273 e-flex e-con-boxed e-con e-parent" data-id="7bb9273"
			data-element_type="container" data-e-type="container">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-256f3db e-con-full e-flex e-con e-child"
					data-id="256f3db" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-08bb083 at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading"
						data-id="08bb083" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h3 class="elementor-heading-title elementor-size-default">Testimonials</h3>
						</div>
					</div>
					<div class="elementor-element elementor-element-92123f7 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="92123f7" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default">What our clients say about us</h2>
						</div>
					</div>
					<div class="elementor-element elementor-element-6d86cde elementor-widget__width-initial elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="6d86cde" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p>Don't just take our word for it — hear from some of our satisfied clients across Nigeria who have experienced our commitment to excellence firsthand.</p>
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-f869698 e-con-full e-flex e-con e-child"
					data-id="f869698" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-65aa66b testimonial-item elementor-widget elementor-widget-elementskit-testimonial"
						data-id="65aa66b" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="elementskit-testimonial.default">
						<div class="elementor-widget-container">
							<div class="ekit-wid-con">
								<div class="elementskit-testimonial-slider ekit_testimonial_style_5 arrow_inside slider-dotted"
									data-config="{&quot;rtl&quot;:false,&quot;arrows&quot;:false,&quot;dots&quot;:true,&quot;pauseOnHover&quot;:false,&quot;autoplay&quot;:true,&quot;speed&quot;:1000,&quot;slidesPerGroup&quot;:1,&quot;slidesPerView&quot;:3,&quot;loop&quot;:true,&quot;spaceBetween&quot;:30,&quot;breakpoints&quot;:{&quot;320&quot;:{&quot;slidesPerView&quot;:1,&quot;slidesPerGroup&quot;:1,&quot;spaceBetween&quot;:10},&quot;768&quot;:{&quot;slidesPerView&quot;:2,&quot;slidesPerGroup&quot;:1,&quot;spaceBetween&quot;:30},&quot;1024&quot;:{&quot;slidesPerView&quot;:3,&quot;slidesPerGroup&quot;:1,&quot;spaceBetween&quot;:30}}}">
									<div class="ekit-main-swiper swiper">
										<div class="swiper-wrapper">
											<!-- Testimonial 1 - Chief Adebayo Ogunlesi -->
											<div class="swiper-slide">
												<div class="swiper-slide-inner">
													<div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style elementskit-testimonial-slider-block-style-two elementor-repeater-item-bdee5de">
														<div class="elementskit-commentor-header">
															<ul class="elementskit-stars">
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
															</ul>
															<div class="elementskit-icon-content elementskit-watermark-icon"></div>
														</div>
														<div class="elementskit-commentor-content">
															<p>"TPV Construction and Services LTD built our family home in Ikoyi, and I couldn't be more impressed with their professionalism. From the foundation to the finishing touches, their attention to detail was remarkable. The project was completed on time and within budget — a rare find in Nigeria's construction industry. I highly recommend them."</p>
														</div>
														<div class="elementskit-commentor-bio">
															<div class="elementkit-commentor-details client_left">
																<div class="elementskit-commentor-image ekit-testimonial--avatar">
																	<img loading="lazy" decoding="async" width="65" height="65"
																		src="wp-content/uploads/2024/06/author-1.jpg"
																		class="attachment-full size-full" alt="Chief Adebayo Ogunlesi">
																</div>
																<div class="elementskit-profile-info">
																	<strong class="elementskit-author-name">Chief Adebayo Ogunlesi</strong>
																	<span class="elementskit-author-des">Lagos, Nigeria</span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- Testimonial 2 - Dr. Ngozi Okonkwo -->
											<div class="swiper-slide">
												<div class="swiper-slide-inner">
													<div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style elementskit-testimonial-slider-block-style-two elementor-repeater-item-bae1464">
														<div class="elementskit-commentor-header">
															<ul class="elementskit-stars">
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
															</ul>
															<div class="elementskit-icon-content elementskit-watermark-icon"></div>
														</div>
														<div class="elementskit-commentor-content">
															<p>"As a healthcare professional, I needed a reliable contractor for my medical clinic in Abuja. TPV Construction and Services LTD exceeded my expectations. Their team understood the unique requirements of a healthcare facility and delivered a space that's both functional and welcoming. The quality of workmanship is exceptional."</p>
														</div>
														<div class="elementskit-commentor-bio">
															<div class="elementkit-commentor-details client_left">
																<div class="elementskit-commentor-image ekit-testimonial--avatar">
																	<img loading="lazy" decoding="async" width="65" height="65"
																		src="wp-content/uploads/2024/06/author-2.jpg"
																		class="attachment-full size-full" alt="Dr. Ngozi Okonkwo">
																</div>
																<div class="elementskit-profile-info">
																	<strong class="elementskit-author-name">Dr. Ngozi Okonkwo</strong>
																	<span class="elementskit-author-des">Abuja, Nigeria</span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- Testimonial 3 - Alhaji Suleiman Bello -->
											<div class="swiper-slide">
												<div class="swiper-slide-inner">
													<div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style elementskit-testimonial-slider-block-style-two elementor-repeater-item-faf6e68">
														<div class="elementskit-commentor-header">
															<ul class="elementskit-stars">
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
															</ul>
															<div class="elementskit-icon-content elementskit-watermark-icon"></div>
														</div>
														<div class="elementskit-commentor-content">
															<p>"I contracted TPV Construction and Services LTD for the renovation of my commercial plaza in Kano. Their project management was impeccable — they coordinated everything seamlessly and minimized disruption to my tenants. The renovation has significantly increased the value of my property. These guys are true professionals."</p>
														</div>
														<div class="elementskit-commentor-bio">
															<div class="elementkit-commentor-details client_left">
																<div class="elementskit-commentor-image ekit-testimonial--avatar">
																	<img loading="lazy" decoding="async" width="65" height="65"
																		src="wp-content/uploads/2024/06/author-3.jpg"
																		class="attachment-full size-full" alt="Alhaji Suleiman Bello">
																</div>
																<div class="elementskit-profile-info">
																	<strong class="elementskit-author-name">Alhaji Suleiman Bello</strong>
																	<span class="elementskit-author-des">Kano, Nigeria</span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- Testimonial 4 - Mrs. Funmilayo Adekunle -->
											<div class="swiper-slide">
												<div class="swiper-slide-inner">
													<div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style elementskit-testimonial-slider-block-style-two elementor-repeater-item-ab8ae80">
														<div class="elementskit-commentor-header">
															<ul class="elementskit-stars">
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
																<li><a><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 576 512">
																			<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
																		</svg></a></li>
															</ul>
															<div class="elementskit-icon-content elementskit-watermark-icon"></div>
														</div>
														<div class="elementskit-commentor-content">
															<p>"The team at TPV Construction and Services LTD renovated our office headquarters in Port Harcourt, and the transformation is incredible. They understood our brand identity and created a workspace that reflects our corporate values. The project was delivered ahead of schedule, and the communication throughout was excellent."</p>
														</div>
														<div class="elementskit-commentor-bio">
															<div class="elementkit-commentor-details client_left">
																<div class="elementskit-commentor-image ekit-testimonial--avatar">
																	<img loading="lazy" decoding="async" width="65" height="65"
																		src="wp-content/uploads/2024/06/author-4.jpg"
																		class="attachment-full size-full" alt="Mrs. Funmilayo Adekunle">
																</div>
																<div class="elementskit-profile-info">
																	<strong class="elementskit-author-name">Mrs. Funmilayo Adekunle</strong>
																	<span class="elementskit-author-des">Port Harcourt, Nigeria</span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="swiper-pagination"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="elementor-element elementor-element-3207e83 e-flex e-con-boxed e-con e-parent" data-id="3207e83"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-83e4aef e-con-full e-flex e-con e-child"
					data-id="83e4aef" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-9d835f3 at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
						data-id="9d835f3" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h3 class="elementor-heading-title elementor-size-default">Frequently Asked Questions</h3>
						</div>
					</div>
					<div class="elementor-element elementor-element-96f7ef1 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="96f7ef1" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h1 class="elementor-heading-title elementor-size-default">Common Questions About Our Construction Services</h1>
						</div>
					</div>
					<div class="elementor-element elementor-element-1c43542 elementor-widget__width-initial elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="1c43542" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p>Find answers to the most common questions about our construction process, pricing, timelines, and how we deliver quality projects across Nigeria.</p>
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-ecb1ae8 e-con-full e-flex e-con e-child"
					data-id="ecb1ae8" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-c5fdc0a e-con-full e-flex e-con e-child"
						data-id="c5fdc0a" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
						<div class="elementor-element elementor-element-cf92a7a e-con-full e-flex e-con e-child"
							data-id="cf92a7a" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-300c568 image-anime at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
								data-id="300c568" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="image.default">
								<div class="elementor-widget-container">
									<img loading="lazy" decoding="async" width="268" height="268"
										src="wp-content/uploads/2024/06/our-faqs-img-1.png"
										class="attachment-large size-large wp-image-1835" alt="Construction consultation">
								</div>
							</div>
							<div class="elementor-element elementor-element-3f41a94 image-anime at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
								data-id="3f41a94" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="image.default">
								<div class="elementor-widget-container">
									<img loading="lazy" decoding="async" width="203" height="197"
										src="wp-content/uploads/2024/06/our-faqs-img-2.jpg"
										class="attachment-full size-full wp-image-1837" alt="Building plans">
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-d8cafeb e-con-full e-flex e-con e-child"
							data-id="d8cafeb" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-2474a8b image-anime at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
								data-id="2474a8b" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="image.default">
								<div class="elementor-widget-container">
									<img loading="lazy" decoding="async" width="268" height="268"
										src="wp-content/uploads/2024/06/our-faqs-img-3.jpg"
										class="attachment-full size-full wp-image-1832" alt="Construction site">
								</div>
							</div>
							<div class="elementor-element elementor-element-19fe1fc at-image-animation at-animation-image-style-1 image-anime elementor-widget elementor-widget-image"
								data-id="19fe1fc" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="image.default">
								<div class="elementor-widget-container">
									<img loading="lazy" decoding="async" width="203" height="197"
										src="wp-content/uploads/2024/06/our-faqs-img-4.jpg"
										class="attachment-full size-full wp-image-1833" alt="Completed project">
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-28b912b elementor-view-stacked elementor-absolute our-faqs-bulitup elementor-shape-circle elementor-widget elementor-widget-icon"
							data-id="28b912b" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_position&quot;:&quot;absolute&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="icon.default">
							<div class="elementor-widget-container">
								<div class="elementor-icon-wrapper">
									<div class="elementor-icon">
										<img loading="lazy" decoding="async" width="58" height="58"
											src="wp-content/uploads/2024/06/favicon.png" alt="svg">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-0416ec5 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="0416ec5" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
						<div class="elementor-element elementor-element-b8d919a faq-accordion elementor-widget elementor-widget-elementskit-accordion"
							data-id="b8d919a" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="elementskit-accordion.default">
							<div class="elementor-widget-container">
								<div class="ekit-wid-con">
									<div class="elementskit-accordion accoedion-primary" id="accordion-698b01e7f05d6">

										<!-- FAQ 1 -->
										<div class="elementskit-card active">
											<div class="elementskit-card-header" id="primaryHeading-0-b8d919a">
												<a href="#collapse-d4f2ee4698b01e7f05d6"
													class="ekit-accordion--toggler elementskit-btn-link collapsed"
													data-ekit-toggle="collapse"
													data-target="#Collapse-d4f2ee4698b01e7f05d6" aria-expanded="true"
													aria-controls="Collapse-d4f2ee4698b01e7f05d6">
													<span class="ekit-accordion-title">Do you offer free quotations and project consultations?</span>
													<div class="ekit_accordion_icon_group">
														<div class="ekit_accordion_normal_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
														<div class="ekit_accordion_active_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
													</div>
												</a>
											</div>
											<div id="Collapse-d4f2ee4698b01e7f05d6" class=" show collapse"
												aria-labelledby="primaryHeading-0-b8d919a"
												data-parent="#accordion-698b01e7f05d6">
												<div class="elementskit-card-body ekit-accordion--content">
													<p>Yes, we provide free, no-obligation quotations for all potential projects. Our team will visit your site, discuss your requirements, and provide a detailed estimate. We believe in transparency and ensuring you have all the information needed to make an informed decision before any work begins.</p>
												</div>
											</div>
										</div>

										<!-- FAQ 2 -->
										<div class="elementskit-card">
											<div class="elementskit-card-header" id="primaryHeading-1-b8d919a">
												<a href="#collapse-738b5c1698b01e7f05d6"
													class="ekit-accordion--toggler elementskit-btn-link collapsed"
													data-ekit-toggle="collapse"
													data-target="#Collapse-738b5c1698b01e7f05d6" aria-expanded="false"
													aria-controls="Collapse-738b5c1698b01e7f05d6">
													<span class="ekit-accordion-title">What construction services do you offer across Nigeria?</span>
													<div class="ekit_accordion_icon_group">
														<div class="ekit_accordion_normal_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
														<div class="ekit_accordion_active_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
													</div>
												</a>
											</div>
											<div id="Collapse-738b5c1698b01e7f05d6" class=" collapse"
												aria-labelledby="primaryHeading-1-b8d919a"
												data-parent="#accordion-698b01e7f05d6">
												<div class="elementskit-card-body ekit-accordion--content">
													<p>We offer comprehensive construction services including: residential building, commercial development, industrial construction, architectural design, renovation and remodeling, project management, and construction consultancy. Our team handles projects of all sizes across Lagos, Abuja, Port Harcourt, Kano, and other major Nigerian cities.</p>
												</div>
											</div>
										</div>

										<!-- FAQ 3 -->
										<div class="elementskit-card">
											<div class="elementskit-card-header" id="primaryHeading-2-b8d919a">
												<a href="#collapse-724e9da698b01e7f05d6"
													class="ekit-accordion--toggler elementskit-btn-link collapsed"
													data-ekit-toggle="collapse"
													data-target="#Collapse-724e9da698b01e7f05d6" aria-expanded="false"
													aria-controls="Collapse-724e9da698b01e7f05d6">
													<span class="ekit-accordion-title">How long does a typical construction project take?</span>
													<div class="ekit_accordion_icon_group">
														<div class="ekit_accordion_normal_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
														<div class="ekit_accordion_active_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
													</div>
												</a>
											</div>
											<div id="Collapse-724e9da698b01e7f05d6" class=" collapse"
												aria-labelledby="primaryHeading-2-b8d919a"
												data-parent="#accordion-698b01e7f05d6">
												<div class="elementskit-card-body ekit-accordion--content">
													<p>Project timelines vary based on scope, size, and complexity. A standard residential home typically takes 6-12 months, while commercial projects range from 8-18 months. During your free consultation, we'll provide a detailed timeline based on your specific requirements, site conditions, and project specifications.</p>
												</div>
											</div>
										</div>

										<!-- FAQ 4 -->
										<div class="elementskit-card">
											<div class="elementskit-card-header" id="primaryHeading-3-b8d919a">
												<a href="#collapse-e5b1f77698b01e7f05d6"
													class="ekit-accordion--toggler elementskit-btn-link collapsed"
													data-ekit-toggle="collapse"
													data-target="#Collapse-e5b1f77698b01e7f05d6" aria-expanded="false"
													aria-controls="Collapse-e5b1f77698b01e7f05d6">
													<span class="ekit-accordion-title">How do I start a construction project with TPV?</span>
													<div class="ekit_accordion_icon_group">
														<div class="ekit_accordion_normal_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
														<div class="ekit_accordion_active_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
													</div>
												</a>
											</div>
											<div id="Collapse-e5b1f77698b01e7f05d6" class=" collapse"
												aria-labelledby="primaryHeading-3-b8d919a"
												data-parent="#accordion-698b01e7f05d6">
												<div class="elementskit-card-body ekit-accordion--content">
													<p>Starting your project is simple: contact us via phone, email, or our website to schedule a free consultation. We'll visit your site, discuss your vision and requirements, and provide a detailed proposal. Once approved, our team handles everything from permits and approvals to construction and final handover.</p>
												</div>
											</div>
										</div>

										<!-- FAQ 5 -->
										<div class="elementskit-card">
											<div class="elementskit-card-header" id="primaryHeading-4-b8d919a">
												<a href="#collapse-5b8f77698b01e7f05d6"
													class="ekit-accordion--toggler elementskit-btn-link collapsed"
													data-ekit-toggle="collapse"
													data-target="#Collapse-5b8f77698b01e7f05d6" aria-expanded="false"
													aria-controls="Collapse-5b8f77698b01e7f05d6">
													<span class="ekit-accordion-title">Are you licensed and insured to operate in Nigeria?</span>
													<div class="ekit_accordion_icon_group">
														<div class="ekit_accordion_normal_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
														<div class="ekit_accordion_active_icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewbox="0 0 32 32" fill="none">
																<g clip-path="url(#clip0_263_1413)">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M7.93794 22.4683L8.62581 21.7826L11.7662 24.9225C13.4935 26.6495 14.9277 28.0625 14.9534 28.0625C14.979 28.0625 15 21.7484 15 14.0312V0L16 0L17 0V14.0312C17 21.7484 17.021 28.0625 17.0466 28.0625C17.0723 28.0625 18.5065 26.6495 20.2337 24.9225L23.3742 21.7826L24.0621 22.4683C24.4404 22.8455 24.75 23.1828 24.75 23.2179C24.75 23.2529 22.795 25.2362 20.4055 27.6252L16.0611 31.9688L16.5149 32.0044C16.8031 32.027 16.5924 32.0398 15.9375 32.0396C15.2101 32.0393 15.0584 32.0288 15.4225 32.004L15.9388 31.9688L11.5944 27.6252C9.205 25.2362 7.25 23.2529 7.25 23.2179C7.25 23.1828 7.55956 22.8455 7.93794 22.4683ZM16 0.0254375C16.5672 0.0254375 16.7992 0.0349375 16.5156 0.0465C16.232 0.0580625 15.7679 0.0580625 15.4844 0.0465C15.2008 0.0349375 15.4328 0.0254375 16 0.0254375Z" fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_263_1413">
																		<rect width="32" height="32" fill="white" transform="matrix(0 1 1 0 0 0)"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
													</div>
												</a>
											</div>
											<div id="Collapse-5b8f77698b01e7f05d6" class=" collapse"
												aria-labelledby="primaryHeading-4-b8d919a"
												data-parent="#accordion-698b01e7f05d6">
												<div class="elementskit-card-body ekit-accordion--content">
													<p>Yes, TPV Construction and Services LTD is fully licensed by the relevant Nigerian authorities and holds comprehensive insurance coverage. We comply with all local building regulations and industry standards. Our team includes certified professionals who ensure every project meets legal requirements and safety standards.</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-3cebe27 e-flex e-con-boxed e-con e-parent" data-id="3cebe27"
			data-element_type="container" data-e-type="container">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-09adf0f e-con-full e-flex e-con e-child"
					data-id="09adf0f" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-e816b55 at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading"
						data-id="e816b55" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h3 class="elementor-heading-title elementor-size-default">News &amp; blog</h3>
						</div>
					</div>
					<div class="elementor-element elementor-element-13c07de at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="13c07de" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default">Articles &amp; blog posts</h2>
						</div>
					</div>
					<div class="elementor-element elementor-element-a527f4b elementor-widget__width-initial elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="a527f4b" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p>We specialize in a wide range of construction services, including residential,
								commercial, and industrial projects.</p>
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-2488f8c e-con-full e-flex elementor-invisible e-con e-child"
					data-id="2488f8c" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
					<div class="elementor-element elementor-element-e3857f1 latest-blog-post elementor-widget elementor-widget-elementskit-blog-posts"
						data-id="e3857f1" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="elementskit-blog-posts.default">
						<div class="elementor-widget-container">
							<div class="ekit-wid-con">
								<div id="post-items--e3857f1"
									class="row post-items ekit-blog-posts-content wihout-masonry " data-enable="no">

									<?php if (empty($latestPosts)): ?>
										<p class="text-muted text-center py-4">No blog posts yet.</p>
									<?php else: ?>
										<?php foreach ($latestPosts as $post): ?>
											<?php
												$img = $post['featured_image_path']
													? (strpos($post['featured_image_path'], '/') === 0
														? preg_replace('#^.*?uploads/#', '', $post['featured_image_path'])
														: $post['featured_image_path'])
													: 'wp-content/uploads/2024/06/post-1.jpg';
											?>
											<div class="col-lg-4 col-md-6">
												<div class="elementskit-post-image-card">
													<div class="elementskit-entry-header">
														<a href="<?php echo htmlspecialchars($post['slug']); ?>/"
															class="elementskit-entry-thumb">
															<img decoding="async"
																src="<?php echo htmlspecialchars($img); ?>"
																alt="<?php echo htmlspecialchars($post['title']); ?>">
														</a>
													</div>
													<div class="elementskit-post-body">
														<h2 class="entry-title">
															<a href="<?php echo htmlspecialchars($post['slug']); ?>/">
																<?php echo htmlspecialchars($post['title']); ?>
															</a>
														</h2>
														<?php if ($post['excerpt']): ?>
															<p><?php echo htmlspecialchars(substr($post['excerpt'], 0, 120)); ?></p>
														<?php endif; ?>
														<div class="btn-wraper">
															<a href="<?php echo htmlspecialchars($post['slug']); ?>/"
																class="elementskit-btn whitespace--normal">
																Read more <svg xmlns="http://www.w3.org/2000/svg" width="24"
																	height="24" viewbox="0 0 24 24" fill="none">
																	<path d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
																		stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
																</svg>
															</a>
														</div>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>


							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-b486ce1 e-flex e-con-boxed e-con e-parent" data-id="b486ce1"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-f18f202 e-con-full e-flex e-con e-child"
					data-id="f18f202" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-1ce556b e-con-full e-flex e-con e-child"
						data-id="1ce556b" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-9d104ce e-con-full contact-sidebar e-flex elementor-invisible e-con e-child"
							data-id="9d104ce" data-element_type="container" data-e-type="container"
							data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;}">

							<!-- Phone Contact -->
							<div class="elementor-element elementor-element-aec5e65 contact-info ekit-equal-height-disable elementor-widget elementor-widget-elementskit-icon-box"
								data-id="aec5e65" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="elementskit-icon-box.default">
								<div class="elementor-widget-container">
									<div class="ekit-wid-con">
										<div class="elementskit-infobox text-center text-center icon-top-align elementor-animation-">
											<div class="elementskit-box-header elementor-animation-">
												<div class="elementskit-info-box-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
														viewbox="0 0 70 70" fill="none">
														<g clip-path="url(#clip0_389_16083)">
															<path
																d="M68.5417 32.0833C67.7367 32.0833 67.0833 31.43 67.0833 30.625C67.0833 15.3475 54.6525 2.91667 39.375 2.91667C38.57 2.91667 37.9167 2.26333 37.9167 1.45833C37.9167 0.653333 38.57 0 39.375 0C56.2625 0 70 13.7375 70 30.625C70 31.43 69.3467 32.0833 68.5417 32.0833ZM58.3333 30.625C58.3333 20.1717 49.8283 11.6667 39.375 11.6667C38.57 11.6667 37.9167 12.32 37.9167 13.125C37.9167 13.93 38.57 14.5833 39.375 14.5833C48.2213 14.5833 55.4167 21.7788 55.4167 30.625C55.4167 31.43 56.07 32.0833 56.875 32.0833C57.68 32.0833 58.3333 31.43 58.3333 30.625ZM64.8492 64.9571L67.7017 61.6758C69.1833 60.1942 70 58.2225 70 56.1283C70 54.0342 69.1833 52.0596 67.5588 50.4554L60.4246 44.9925C57.3679 41.9358 52.395 41.9329 49.3325 44.9838L44.8642 49.3413C34.1308 44.9692 25.3867 36.2163 20.6733 25.1242L25.0133 20.6763C28.0729 17.6167 28.0729 12.6379 25.1388 9.72125L19.425 2.30417C16.4587 -0.662083 11.2204 -0.592083 8.4 2.23708L4.97583 5.22375C1.76458 8.4175 0 12.7546 0 17.43C0 40.0312 29.9688 70 52.57 70C57.2454 70 61.5796 68.2354 64.8492 64.9571ZM17.2346 4.2175L22.9483 11.6346C24.8704 13.5567 24.8704 16.6863 22.9367 18.6229L17.9142 23.7738C17.5117 24.1879 17.3892 24.8004 17.605 25.3371C22.6479 37.8933 32.5179 47.7604 44.6892 52.4037C45.22 52.6079 45.8208 52.4825 46.2262 52.0887L51.3771 47.0662C51.3771 47.0662 51.3858 47.0575 51.3887 47.0517C53.2525 45.185 56.3588 45.0654 58.5054 47.1771L65.6396 52.64C67.5617 54.5621 67.5617 57.6888 65.5725 59.6867L62.7171 62.9679C60.0688 65.6221 56.4638 67.0833 52.57 67.0833C31.6867 67.0833 2.91667 38.3133 2.91667 17.43C2.91667 13.5363 4.37792 9.93125 6.96208 7.35L10.3892 4.36042C11.3692 3.38042 12.6613 2.90208 13.9329 2.90208C15.155 2.90208 16.3567 3.34542 17.2375 4.22042L17.2346 4.2175Z"
																fill="#E5363D"></path>
														</g>
														<defs>
															<clippath id="clip0_389_16083">
																<rect width="70" height="70" fill="white"></rect>
															</clippath>
														</defs>
													</svg>
												</div>
											</div>
											<div class="box-body">
												<h3 class="elementskit-info-box-title">
													Call Our Head Office </h3>
												<p><a href="tel:+2347012345678">+234 701 234 5678</a></p>
												<small class="contact-note">Available Mon-Fri, 8am-6pm</small>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Email Contact -->
							<div class="elementor-element elementor-element-1831205 contact-info ekit-equal-height-disable elementor-widget elementor-widget-elementskit-icon-box"
								data-id="1831205" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="elementskit-icon-box.default">
								<div class="elementor-widget-container">
									<div class="ekit-wid-con">
										<div class="elementskit-infobox text-center text-center icon-top-align elementor-animation-">
											<div class="elementskit-box-header elementor-animation-">
												<div class="elementskit-info-box-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
														viewbox="0 0 70 70" fill="none">
														<g clip-path="url(#clip0_392_16117)">
															<path fill-rule="evenodd" clip-rule="evenodd"
																d="M33.0304 0.224684C31.5221 0.548024 30.5569 1.07384 27.5532 3.20843C26.0006 4.31175 24.5101 5.36394 24.2409 5.5466L23.7516 5.87869L17.9942 5.88088C12.2749 5.8832 12.2334 5.88525 11.6928 6.19136C11.3878 6.36418 11.0135 6.73851 10.8407 7.04353C10.5413 7.57209 10.5324 7.69746 10.5302 11.4308L10.528 15.274L5.43522 18.8786C2.63413 20.861 0.267528 22.577 0.175926 22.6917C-0.0831556 23.016 -0.0614173 66.8527 0.198075 67.5801C0.505965 68.4431 1.06665 69.1205 1.81792 69.5369L2.52995 69.9314H35.0007H67.4714L68.1915 69.5325C68.9487 69.1129 69.6795 68.2029 69.8222 67.5018C69.8646 67.2934 69.9528 67.0899 70.0183 67.0494C70.0851 67.0081 70.1374 57.2555 70.1374 44.8274C70.1374 31.0635 70.0888 22.709 70.009 22.7583C69.9384 22.8019 69.8308 22.7561 69.7697 22.6564C69.7088 22.5569 67.3672 20.8531 64.5661 18.8704L59.4733 15.2654L59.4711 11.4266C59.4689 7.69773 59.46 7.57209 59.1606 7.04353C58.9878 6.73851 58.6135 6.36418 58.3085 6.19136C57.7679 5.88525 57.7266 5.8832 52.0029 5.88088L46.2414 5.87869L43.7314 4.10271C39.8144 1.33115 39.3074 1.0022 38.4602 0.683239C36.6343 -0.00390927 34.8139 -0.157718 33.0304 0.224684ZM33.9069 2.41834C33.266 2.52552 31.9256 3.01566 31.4424 3.31959C30.671 3.80466 28.1725 5.61892 28.0884 5.75468C28.043 5.82824 30.8892 5.87869 35.0838 5.87869C38.9733 5.87869 42.13 5.85695 42.0987 5.83056C41.3607 5.20658 38.3609 3.18 37.8235 2.94224C36.6547 2.42545 35.1048 2.21804 33.9069 2.41834ZM12.8522 19.8632V31.5237L21.0037 38.2861C25.4869 42.0055 29.532 45.3743 29.9929 45.7721C31.6871 47.2354 33.0389 47.7764 35.0007 47.7766C37.0862 47.7767 38.2306 47.2747 40.5011 45.364C40.9754 44.9649 44.9152 41.6909 49.2562 38.0885L57.1491 31.5386V19.8708V8.20291H35.0007H12.8522V19.8632ZM35.2741 12.7998C31.3113 13.2116 28.5228 14.4579 25.6522 17.1004C23.6329 18.9594 21.8714 21.7746 21.1679 24.2674C20.9151 25.163 20.8586 25.7078 20.8566 27.2752C20.8534 29.5764 21.0567 30.4915 22.0109 32.4705C23.7378 36.052 26.6585 38.1383 31.2682 39.0833C37.8571 40.4339 43.6384 39.2824 47.4434 35.8612C48.904 34.5482 49.299 33.5972 48.6383 32.9851C48.3445 32.7128 48.0537 32.6252 47.5573 32.6598C47.4328 32.6685 46.8442 33.1583 46.2493 33.7483C44.0126 35.9672 41.1751 37.0979 37.3932 37.2775C34.8054 37.4004 31.9809 37.0225 29.5319 36.2257C25.9157 35.0493 23.3488 31.6392 23.1411 27.7363C23.052 26.0591 23.334 24.7073 24.1217 23.0369C25.8595 19.3512 29.4243 16.3995 33.2917 15.4441C34.8065 15.0699 37.1563 15.0341 38.6792 15.3619C41.964 16.0692 44.6828 18.7386 45.4518 22.0115C45.721 23.1569 45.7243 25.4098 45.4584 26.4603C44.9725 28.3803 43.6202 30.327 42.3835 30.887C41.5323 31.2726 40.166 31.2784 39.7858 30.8982C39.4454 30.5578 39.4439 30.6039 39.8464 29.0089C40.2515 27.4037 41.6999 20.2461 41.6999 19.8493C41.6999 19.6934 41.554 19.3962 41.3759 19.1891C41.1289 18.9021 40.9292 18.8123 40.5378 18.8123C39.8792 18.8123 39.4292 19.3047 39.3 20.1669L39.2068 20.7884L38.5574 20.181C35.3147 17.1477 30.504 18.7747 28.0912 23.7205C27.3607 25.2177 27.0756 26.3716 27.073 27.8419C27.0671 31.0422 28.9787 33.2807 31.9736 33.5804C33.4299 33.7261 35.4777 33.0889 36.7899 32.0814L37.3779 31.6301L37.7967 32.1869C38.0407 32.5116 38.5287 32.9048 38.9665 33.1299C39.6695 33.4912 39.8048 33.5128 41.0846 33.4681C42.272 33.4266 42.5709 33.3644 43.3573 32.9949C44.4411 32.4857 45.2071 31.8205 46.017 30.6855C46.814 29.5688 47.3766 28.338 47.7311 26.9362C47.9824 25.9424 48.0122 25.5264 47.9511 23.8649C47.8765 21.8414 47.6593 20.827 46.9853 19.3545C45.8552 16.8861 43.4968 14.6388 40.9781 13.6305C39.5895 13.0746 36.6916 12.6526 35.2741 12.7998ZM6.81075 20.7471C4.80399 22.157 3.17362 23.3546 3.1877 23.4088C3.20192 23.4629 4.84391 24.8647 6.83659 26.524L10.4596 29.541L10.4966 26.6557C10.5169 25.0688 10.5169 22.5135 10.4966 20.9772L10.4596 18.1841L6.81075 20.7471ZM59.4733 23.8582C59.4733 29.3227 59.483 29.5395 59.7208 29.3679C60.3937 28.8822 66.8552 23.4671 66.8485 23.3943C66.8419 23.3206 60.1583 18.5618 59.6644 18.2791C59.502 18.1862 59.4733 19.0261 59.4733 23.8582ZM33.8 21.1112C32.0898 21.6677 30.2698 23.8582 29.5981 26.1686C29.2756 27.2783 29.3609 28.8339 29.793 29.7174C30.4173 30.9941 31.8593 31.5336 33.5138 31.1096C34.6597 30.8161 35.538 30.2302 36.4585 29.1451C37.6739 27.7124 37.8413 27.3871 38.1654 25.8264L38.4657 24.3812L38.0522 23.4975C37.309 21.9095 36.3379 21.1018 35.0255 20.98C34.6735 20.9474 34.122 21.0063 33.8 21.1112ZM0.0673717 44.9803C0.0673717 57.1995 0.0835045 62.1982 0.103192 56.0886C0.12288 49.979 0.12288 39.9814 0.103192 33.8719C0.0835045 27.7622 0.0673717 32.761 0.0673717 44.9803ZM2.32487 46.0695C2.32487 65.1036 2.33895 66.3304 2.55647 66.1542C5.76048 63.5583 26.5924 46.1659 26.5924 46.0869C26.5924 46.0253 21.2399 41.5288 14.6979 36.0946C8.15593 30.6605 2.69565 26.1204 2.56413 26.0057C2.33636 25.8074 2.32487 26.7742 2.32487 46.0695ZM55.4811 35.9386C48.8112 41.4755 43.3663 46.0475 43.3815 46.0987C43.3965 46.1499 48.8537 50.7151 55.5085 56.2434L67.6081 66.295L67.6431 56.1845C67.6624 50.6237 67.6624 41.5285 67.6431 35.9728L67.6081 25.8716L55.4811 35.9386ZM16.4904 57.6114L4.47436 67.6072L19.7375 67.6419C28.1322 67.6611 41.8719 67.6611 50.2701 67.6419L65.5395 67.6072L54.2691 58.2505C48.0704 53.1044 42.6591 48.604 42.2441 48.2496L41.4896 47.6055L40.6719 48.2273C38.6728 49.7475 36.0066 50.4345 33.6298 50.0417C31.915 49.7584 30.5997 49.1669 28.9243 47.9253L28.5065 47.6158L16.4904 57.6114Z"
																fill="#E5363D"></path>
														</g>
														<defs>
															<clippath id="clip0_392_16117">
																<rect width="70" height="70" fill="white"></rect>
															</clippath>
														</defs>
													</svg>
												</div>
											</div>
											<div class="box-body">
												<h3 class="elementskit-info-box-title">
													Write To Us </h3>
												<p><a href="mailto:info@tpvconstruction.com.ng">info@tpvconstruction.com.ng</a></p>
												<small class="contact-note">We reply within 24 hours</small>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Office Hours -->
							<div class="elementor-element elementor-element-48600bf at-image-animation at-animation-image-none elementor-widget elementor-widget-image"
								data-id="48600bf" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="image.default">
								<div class="elementor-widget-container">
									<img loading="lazy" decoding="async" width="301" height="420"
										src="wp-content/uploads/2024/06/contact-info-img.png"
										class="attachment-full size-full wp-image-1857" alt="TPV Construction and Services LTD Contact">
								</div>
							</div>
						</div>
					</div>

					<!-- Contact Form Section -->
					<div class="elementor-element elementor-element-f8fca35 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="f8fca35" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">

						<div class="elementor-element elementor-element-49c625f at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
							data-id="49c625f" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h3 class="elementor-heading-title elementor-size-default">Contact us</h3>
							</div>
						</div>

						<div class="elementor-element elementor-element-91b6244 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="91b6244" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h2 class="elementor-heading-title elementor-size-default">Get in touch with us</h2>
							</div>
						</div>

						<div class="elementor-element elementor-element-14ceed9 contact-form elementor-widget elementor-widget-elementskit-contact-form7"
							data-id="14ceed9" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="elementskit-contact-form7.default">
							<div class="elementor-widget-container">
								<div class="ekit-wid-con">
									<div class="ekit-form">
										<div class="wpcf7 no-js" id="wpcf7-f6700-p10-o1" lang="en-US" dir="ltr"
											data-wpcf7-id="6700">
											<div class="screen-reader-response">
												<p role="status" aria-live="polite" aria-atomic="true"></p>
												<ul></ul>
											</div>
											<form action="/TPV Construction Services/#wpcf7-f6700-p10-o1" method="post"
												class="wpcf7-form init" aria-label="Contact form"
												novalidate="novalidate" data-status="init">
												<fieldset class="hidden-fields-container">
													<input type="hidden" name="_wpcf7" value="6700">
													<input type="hidden" name="_wpcf7_version" value="6.1.5">
													<input type="hidden" name="_wpcf7_locale" value="en_US">
													<input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f6700-p10-o1">
													<input type="hidden" name="_wpcf7_container_post" value="10">
													<input type="hidden" name="_wpcf7_posted_data_hash" value="">
												</fieldset>

												<div class="row">
													<div class="form-group col-md-6 mb-4">
														<p>
															<span class="wpcf7-form-control-wrap" data-name="your-name">
																<input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control"
																	id="your-name" aria-required="true"
																	aria-invalid="false" placeholder="Your Full Name *"
																	value="" type="text" name="your-name">
															</span>
														</p>
													</div>

													<div class="form-group col-md-6 mb-4">
														<p>
															<span class="wpcf7-form-control-wrap" data-name="email">
																<input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-email wpcf7-validates-as-required wpcf7-text wpcf7-validates-as-email form-control"
																	id="email" aria-required="true" aria-invalid="false"
																	placeholder="Email Address *" value="" type="email"
																	name="email">
															</span>
														</p>
													</div>

													<div class="form-group col-md-6 mb-4">
														<p>
															<span class="wpcf7-form-control-wrap" data-name="phone">
																<input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control"
																	id="phone" aria-required="true" aria-invalid="false"
																	placeholder="Phone Number *" value="" type="text"
																	name="phone">
															</span>
														</p>
													</div>

													<div class="form-group col-md-6 mb-4">
														<p>
															<span class="wpcf7-form-control-wrap" data-name="Subject">
																<input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control"
																	id="subject" aria-required="true"
																	aria-invalid="false" placeholder="Subject *" value=""
																	type="text" name="Subject">
															</span>
														</p>
													</div>

													<div class="form-group col-md-12 mb-4">
														<p>
															<span class="wpcf7-form-control-wrap" data-name="message">
																<textarea cols="40" rows="5"
																	maxlength="2000"
																	class="wpcf7-form-control wpcf7-textarea form-control"
																	id="msg" aria-invalid="false" placeholder="Tell us about your project..."
																	name="message"></textarea>
															</span>
														</p>
													</div>

													<div class="col-md-12 form-btn">
														<p>
															<input class="wpcf7-form-control wpcf7-submit has-spinner btn-default"
																id="msgSubmit" type="submit" value="Send Message">
														</p>
													</div>
												</div>

												<div class="wpcf7-response-output" aria-hidden="true"></div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Additional CSS for contact section -->
		<style>
			/* Contact Info Box Styles */
			.contact-info .elementskit-infobox {
				padding: 30px 20px;
				background: rgba(255, 255, 255, 0.05);
				border-radius: 10px;
				margin-bottom: 15px;
				transition: all 0.3s ease;
			}

			.contact-info .elementskit-infobox:hover {
				background: rgba(229, 54, 61, 0.1);
				transform: translateY(-5px);
			}

			.contact-info .elementskit-info-box-icon {
				background: transparent !important;
				margin-bottom: 15px;
			}

			.contact-info .elementskit-info-box-icon svg {
				width: 50px;
				height: 50px;
			}

			.contact-info .box-body h3 {
				color: #ffffff;
				font-size: 18px;
				font-weight: 600;
				margin-bottom: 10px;
				font-family: 'Manrope', sans-serif;
			}

			.contact-info .box-body p {
				margin-bottom: 5px;
			}

			.contact-info .box-body p a {
				color: #E5363D;
				font-size: 16px;
				font-weight: 500;
				text-decoration: none;
				transition: color 0.3s ease;
				font-family: 'DM Sans', sans-serif;
			}

			.contact-info .box-body p a:hover {
				color: #ffffff;
			}

			.contact-info .contact-note {
				color: rgba(255, 255, 255, 0.6);
				font-size: 12px;
				display: block;
				margin-top: 10px;
			}

			/* Contact Form Styles */
			.contact-form .form-control {
				height: 50px;
				background: rgba(255, 255, 255, 0.1);
				border: 1px solid rgba(255, 255, 255, 0.2);
				border-radius: 5px;
				color: #ffffff;
				padding: 10px 15px;
				font-size: 14px;
				transition: all 0.3s ease;
			}

			.contact-form .form-control:focus {
				background: rgba(255, 255, 255, 0.15);
				border-color: #E5363D;
				outline: none;
				box-shadow: none;
			}

			.contact-form .form-control::placeholder {
				color: rgba(255, 255, 255, 0.5);
			}

			.contact-form textarea.form-control {
				height: 120px;
				resize: none;
			}

			.contact-form .btn-default {
				background: #E5363D;
				color: #ffffff;
				border: none;
				padding: 12px 35px;
				font-size: 16px;
				font-weight: 600;
				border-radius: 5px;
				cursor: pointer;
				transition: all 0.3s ease;
				width: 100%;
				text-transform: uppercase;
				letter-spacing: 1px;
			}

			.contact-form .btn-default:hover {
				background: #ffffff;
				color: #E5363D;
			}

			/* Contact Sidebar Image */
			.contact-sidebar .elementor-widget-image {
				margin-top: 20px;
			}

			.contact-sidebar .elementor-widget-image img {
				border-radius: 10px;
				max-width: 100%;
				height: auto;
			}

			/* Responsive Adjustments */
			@media (max-width: 768px) {
				.contact-info .elementskit-infobox {
					padding: 20px 15px;
				}

				.contact-info .box-body h3 {
					font-size: 16px;
				}

				.contact-info .box-body p a {
					font-size: 14px;
				}

				.contact-form .btn-default {
					padding: 10px 25px;
					font-size: 14px;
				}
			}
		</style>
	</div>


	<div class="ekit-template-content-markup ekit-template-content-footer ekit-template-content-theme-support">
		<div data-elementor-type="wp-post" data-elementor-id="1688" class="elementor elementor-1688">
			<div class="elementor-element elementor-element-aac5742 e-flex e-con-boxed e-con e-parent" data-id="aac5742"
				data-element_type="container" data-e-type="container"
				data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
				<div class="e-con-inner">
					<div class="elementor-element elementor-element-518b814 e-con-full e-flex e-con e-child"
						data-id="518b814" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-2b12312 e-con-full e-flex e-con e-child"
							data-id="2b12312" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-65cb43e at-image-animation at-animation-image-none elementor-widget elementor-widget-image"
								data-id="65cb43e" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="image.default">
								<div class="elementor-widget-container">
									<img width="150" height="108"  src="<?php echo htmlspecialchars(tpv_setting_asset_url('footer_logo', 'wp-content/uploads/2024/06/footer-logo.png')); ?>"
										class="attachment-full size-full wp-image-1698" alt="TPV Construction and Services LTD Logo">
								</div>
							</div>
							<div class="elementor-element elementor-element-7b129ce elementor-widget elementor-widget-text-editor"
								data-id="7b129ce" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="text-editor.default">
								<div class="elementor-widget-container">
									<p>Building Nigeria's future with excellence, integrity, and innovation. Your trusted partner for quality construction across the nation.</p>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-6ab77f9 e-con-full e-flex e-con e-child"
							data-id="6ab77f9" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-4450da0 at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading"
								data-id="4450da0" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="heading.default">
								<div class="elementor-widget-container">
									<h3 class="elementor-heading-title elementor-size-default">Our Services</h3>
								</div>
							</div>
							<div class="elementor-element elementor-element-80e8ad5 footer-menu-list elementor-widget elementor-widget-ekit-vertical-menu"
								data-id="80e8ad5" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="ekit-vertical-menu.default">
								<div class="elementor-widget-container">
									<div class="ekit-wid-con">
										<div class="ekit-vertical-main-menu-wraper badge-position-left">
											<div class="ekit-vertical-menu-container">
												<ul id="menu-our-services-menu"
													class="ekit-vertical-navbar-nav submenu-click-on-icon">
													<li id="menu-item-5700"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5700 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/building-construction/"
															class="ekit-menu-nav-link">Building Construction</a></li>
													<li id="menu-item-5701"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5701 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/architecture-design/"
															class="ekit-menu-nav-link">Architecture Design</a></li>
													<li id="menu-item-5702"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5702 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/building-renovation/"
															class="ekit-menu-nav-link">Building Renovation</a></li>
													<li id="menu-item-5703"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5703 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/flooring-roofing/"
															class="ekit-menu-nav-link">Flooring & Roofing</a></li>
													<li id="menu-item-5704"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5704 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="services/building-maintenance/"
															class="ekit-menu-nav-link">Building Maintenance</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-15c722f e-con-full e-flex e-con e-child"
							data-id="15c722f" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-f7018c4 at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading"
								data-id="f7018c4" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="heading.default">
								<div class="elementor-widget-container">
									<h3 class="elementor-heading-title elementor-size-default">Company</h3>
								</div>
							</div>
							<div class="elementor-element elementor-element-400af0a footer-menu-list elementor-widget elementor-widget-ekit-vertical-menu"
								data-id="400af0a" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="ekit-vertical-menu.default">
								<div class="elementor-widget-container">
									<div class="ekit-wid-con">
										<div class="ekit-vertical-main-menu-wraper badge-position-left">
											<div class="ekit-vertical-menu-container">
												<ul id="menu-footer-menu"
													class="ekit-vertical-navbar-nav submenu-click-on-icon">
													<li id="menu-item-3071"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-3071 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a href="about-us/"
															class="ekit-menu-nav-link">About Us</a></li>
													<li id="menu-item-3068"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-3068 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a href="services/"
															class="ekit-menu-nav-link">Services</a></li>
													<li id="menu-item-3075"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-3075 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a href="blog/"
															class="ekit-menu-nav-link">Blog</a></li>
													<li id="menu-item-3070"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-3070 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a href="faqs/"
															class="ekit-menu-nav-link">FAQs</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-d886e3e e-con-full e-flex e-con e-child"
							data-id="d886e3e" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-6083d14 at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading"
								data-id="6083d14" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="heading.default">
								<div class="elementor-widget-container">
									<h3 class="elementor-heading-title elementor-size-default">Contact Us</h3>
								</div>
							</div>
							<div class="elementor-element elementor-element-6ac0296 e-con-full e-flex e-con e-child"
								data-id="6ac0296" data-element_type="container" data-e-type="container">
								<div class="elementor-element elementor-element-331b94a footer-info-box ekit-equal-height-disable elementor-widget elementor-widget-elementskit-icon-box"
									data-id="331b94a" data-element_type="widget" data-e-type="widget"
									data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
									data-widget_type="elementskit-icon-box.default">
									<div class="elementor-widget-container">
										<div class="ekit-wid-con">
											<a href="tel:+2347012345678" class="ekit_global_links">
												<div class="elementskit-infobox text-left text-left icon-lef-right-aligin elementor-animation- media">
													<div class="elementskit-box-header elementor-animation-">
														<div class="elementskit-info-box-icon text-center">
															<svg xmlns="http://www.w3.org/2000/svg" width="70"
																height="70" viewbox="0 0 70 70" fill="none">
																<g clip-path="url(#clip0_389_16083)">
																	<path
																		d="M68.5417 32.0833C67.7367 32.0833 67.0833 31.43 67.0833 30.625C67.0833 15.3475 54.6525 2.91667 39.375 2.91667C38.57 2.91667 37.9167 2.26333 37.9167 1.45833C37.9167 0.653333 38.57 0 39.375 0C56.2625 0 70 13.7375 70 30.625C70 31.43 69.3467 32.0833 68.5417 32.0833ZM58.3333 30.625C58.3333 20.1717 49.8283 11.6667 39.375 11.6667C38.57 11.6667 37.9167 12.32 37.9167 13.125C37.9167 13.93 38.57 14.5833 39.375 14.5833C48.2213 14.5833 55.4167 21.7788 55.4167 30.625C55.4167 31.43 56.07 32.0833 56.875 32.0833C57.68 32.0833 58.3333 31.43 58.3333 30.625ZM64.8492 64.9571L67.7017 61.6758C69.1833 60.1942 70 58.2225 70 56.1283C70 54.0342 69.1833 52.0596 67.5588 50.4554L60.4246 44.9925C57.3679 41.9358 52.395 41.9329 49.3325 44.9838L44.8642 49.3413C34.1308 44.9692 25.3867 36.2163 20.6733 25.1242L25.0133 20.6763C28.0729 17.6167 28.0729 12.6379 25.1388 9.72125L19.425 2.30417C16.4587 -0.662083 11.2204 -0.592083 8.4 2.23708L4.97583 5.22375C1.76458 8.4175 0 12.7546 0 17.43C0 40.0312 29.9688 70 52.57 70C57.2454 70 61.5796 68.2354 64.8492 64.9571ZM17.2346 4.2175L22.9483 11.6346C24.8704 13.5567 24.8704 16.6863 22.9367 18.6229L17.9142 23.7738C17.5117 24.1879 17.3892 24.8004 17.605 25.3371C22.6479 37.8933 32.5179 47.7604 44.6892 52.4037C45.22 52.6079 45.8208 52.4825 46.2262 52.0887L51.3771 47.0662C51.3771 47.0662 51.3858 47.0575 51.3887 47.0517C53.2525 45.185 56.3588 45.0654 58.5054 47.1771L65.6396 52.64C67.5617 54.5621 67.5617 57.6888 65.5725 59.6867L62.7171 62.9679C60.0688 65.6221 56.4638 67.0833 52.57 67.0833C31.6867 67.0833 2.91667 38.3133 2.91667 17.43C2.91667 13.5363 4.37792 9.93125 6.96208 7.35L10.3892 4.36042C11.3692 3.38042 12.6613 2.90208 13.9329 2.90208C15.155 2.90208 16.3567 3.34542 17.2375 4.22042L17.2346 4.2175Z"
																		fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_389_16083">
																		<rect width="70" height="70" fill="white"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
													</div>
													<div class="box-body">
														<h3 class="elementskit-info-box-title">
															+234 701 234 5678 </h3>
													</div>
												</div>
											</a>
										</div>
									</div>
								</div>
								<div class="elementor-element elementor-element-dbea2a3 footer-info-box ekit-equal-height-disable elementor-widget elementor-widget-elementskit-icon-box"
									data-id="dbea2a3" data-element_type="widget" data-e-type="widget"
									data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
									data-widget_type="elementskit-icon-box.default">
									<div class="elementor-widget-container">
										<div class="ekit-wid-con">
											<a href="mailto:info@tpvconstruction.com.ng" class="ekit_global_links">
												<div class="elementskit-infobox text-left text-left icon-lef-right-aligin elementor-animation- media">
													<div class="elementskit-box-header elementor-animation-">
														<div class="elementskit-info-box-icon text-center">
															<svg xmlns="http://www.w3.org/2000/svg" width="70"
																height="70" viewbox="0 0 70 70" fill="none">
																<g clip-path="url(#clip0_392_16117)">
																	<path fill-rule="evenodd" clip-rule="evenodd"
																		d="M33.0304 0.224684C31.5221 0.548024 30.5569 1.07384 27.5532 3.20843C26.0006 4.31175 24.5101 5.36394 24.2409 5.5466L23.7516 5.87869L17.9942 5.88088C12.2749 5.8832 12.2334 5.88525 11.6928 6.19136C11.3878 6.36418 11.0135 6.73851 10.8407 7.04353C10.5413 7.57209 10.5324 7.69746 10.5302 11.4308L10.528 15.274L5.43522 18.8786C2.63413 20.861 0.267528 22.577 0.175926 22.6917C-0.0831556 23.016 -0.0614173 66.8527 0.198075 67.5801C0.505965 68.4431 1.06665 69.1205 1.81792 69.5369L2.52995 69.9314H35.0007H67.4714L68.1915 69.5325C68.9487 69.1129 69.6795 68.2029 69.8222 67.5018C69.8646 67.2934 69.9528 67.0899 70.0183 67.0494C70.0851 67.0081 70.1374 57.2555 70.1374 44.8274C70.1374 31.0635 70.0888 22.709 70.009 22.7583C69.9384 22.8019 69.8308 22.7561 69.7697 22.6564C69.7088 22.5569 67.3672 20.8531 64.5661 18.8704L59.4733 15.2654L59.4711 11.4266C59.4689 7.69773 59.46 7.57209 59.1606 7.04353C58.9878 6.73851 58.6135 6.36418 58.3085 6.19136C57.7679 5.88525 57.7266 5.8832 52.0029 5.88088L46.2414 5.87869L43.7314 4.10271C39.8144 1.33115 39.3074 1.0022 38.4602 0.683239C36.6343 -0.00390927 34.8139 -0.157718 33.0304 0.224684ZM33.9069 2.41834C33.266 2.52552 31.9256 3.01566 31.4424 3.31959C30.671 3.80466 28.1725 5.61892 28.0884 5.75468C28.043 5.82824 30.8892 5.87869 35.0838 5.87869C38.9733 5.87869 42.13 5.85695 42.0987 5.83056C41.3607 5.20658 38.3609 3.18 37.8235 2.94224C36.6547 2.42545 35.1048 2.21804 33.9069 2.41834ZM12.8522 19.8632V31.5237L21.0037 38.2861C25.4869 42.0055 29.532 45.3743 29.9929 45.7721C31.6871 47.2354 33.0389 47.7764 35.0007 47.7766C37.0862 47.7767 38.2306 47.2747 40.5011 45.364C40.9754 44.9649 44.9152 41.6909 49.2562 38.0885L57.1491 31.5386V19.8708V8.20291H35.0007H12.8522V19.8632ZM35.2741 12.7998C31.3113 13.2116 28.5228 14.4579 25.6522 17.1004C23.6329 18.9594 21.8714 21.7746 21.1679 24.2674C20.9151 25.163 20.8586 25.7078 20.8566 27.2752C20.8534 29.5764 21.0567 30.4915 22.0109 32.4705C23.7378 36.052 26.6585 38.1383 31.2682 39.0833C37.8571 40.4339 43.6384 39.2824 47.4434 35.8612C48.904 34.5482 49.299 33.5972 48.6383 32.9851C48.3445 32.7128 48.0537 32.6252 47.5573 32.6598C47.4328 32.6685 46.8442 33.1583 46.2493 33.7483C44.0126 35.9672 41.1751 37.0979 37.3932 37.2775C34.8054 37.4004 31.9809 37.0225 29.5319 36.2257C25.9157 35.0493 23.3488 31.6392 23.1411 27.7363C23.052 26.0591 23.334 24.7073 24.1217 23.0369C25.8595 19.3512 29.4243 16.3995 33.2917 15.4441C34.8065 15.0699 37.1563 15.0341 38.6792 15.3619C41.964 16.0692 44.6828 18.7386 45.4518 22.0115C45.721 23.1569 45.7243 25.4098 45.4584 26.4603C44.9725 28.3803 43.6202 30.327 42.3835 30.887C41.5323 31.2726 40.166 31.2784 39.7858 30.8982C39.4454 30.5578 39.4439 30.6039 39.8464 29.0089C40.2515 27.4037 41.6999 20.2461 41.6999 19.8493C41.6999 19.6934 41.554 19.3962 41.3759 19.1891C41.1289 18.9021 40.9292 18.8123 40.5378 18.8123C39.8792 18.8123 39.4292 19.3047 39.3 20.1669L39.2068 20.7884L38.5574 20.181C35.3147 17.1477 30.504 18.7747 28.0912 23.7205C27.3607 25.2177 27.0756 26.3716 27.073 27.8419C27.0671 31.0422 28.9787 33.2807 31.9736 33.5804C33.4299 33.7261 35.4777 33.0889 36.7899 32.0814L37.3779 31.6301L37.7967 32.1869C38.0407 32.5116 38.5287 32.9048 38.9665 33.1299C39.6695 33.4912 39.8048 33.5128 41.0846 33.4681C42.272 33.4266 42.5709 33.3644 43.3573 32.9949C44.4411 32.4857 45.2071 31.8205 46.017 30.6855C46.814 29.5688 47.3766 28.338 47.7311 26.9362C47.9824 25.9424 48.0122 25.5264 47.9511 23.8649C47.8765 21.8414 47.6593 20.827 46.9853 19.3545C45.8552 16.8861 43.4968 14.6388 40.9781 13.6305C39.5895 13.0746 36.6916 12.6526 35.2741 12.7998ZM6.81075 20.7471C4.80399 22.157 3.17362 23.3546 3.1877 23.4088C3.20192 23.4629 4.84391 24.8647 6.83659 26.524L10.4596 29.541L10.4966 26.6557C10.5169 25.0688 10.5169 22.5135 10.4966 20.9772L10.4596 18.1841L6.81075 20.7471ZM59.4733 23.8582C59.4733 29.3227 59.483 29.5395 59.7208 29.3679C60.3937 28.8822 66.8552 23.4671 66.8485 23.3943C66.8419 23.3206 60.1583 18.5618 59.6644 18.2791C59.502 18.1862 59.4733 19.0261 59.4733 23.8582ZM33.8 21.1112C32.0898 21.6677 30.2698 23.8582 29.5981 26.1686C29.2756 27.2783 29.3609 28.8339 29.793 29.7174C30.4173 30.9941 31.8593 31.5336 33.5138 31.1096C34.6597 30.8161 35.538 30.2302 36.4585 29.1451C37.6739 27.7124 37.8413 27.3871 38.1654 25.8264L38.4657 24.3812L38.0522 23.4975C37.309 21.9095 36.3379 21.1018 35.0255 20.98C34.6735 20.9474 34.122 21.0063 33.8 21.1112ZM0.0673717 44.9803C0.0673717 57.1995 0.0835045 62.1982 0.103192 56.0886C0.12288 49.979 0.12288 39.9814 0.103192 33.8719C0.0835045 27.7622 0.0673717 32.761 0.0673717 44.9803ZM2.32487 46.0695C2.32487 65.1036 2.33895 66.3304 2.55647 66.1542C5.76048 63.5583 26.5924 46.1659 26.5924 46.0869C26.5924 46.0253 21.2399 41.5288 14.6979 36.0946C8.15593 30.6605 2.69565 26.1204 2.56413 26.0057C2.33636 25.8074 2.32487 26.7742 2.32487 46.0695ZM55.4811 35.9386C48.8112 41.4755 43.3663 46.0475 43.3815 46.0987C43.3965 46.1499 48.8537 50.7151 55.5085 56.2434L67.6081 66.295L67.6431 56.1845C67.6624 50.6237 67.6624 41.5285 67.6431 35.9728L67.6081 25.8716L55.4811 35.9386ZM16.4904 57.6114L4.47436 67.6072L19.7375 67.6419C28.1322 67.6611 41.8719 67.6611 50.2701 67.6419L65.5395 67.6072L54.2691 58.2505C48.0704 53.1044 42.6591 48.604 42.2441 48.2496L41.4896 47.6055L40.6719 48.2273C38.6728 49.7475 36.0066 50.4345 33.6298 50.0417C31.915 49.7584 30.5997 49.1669 28.9243 47.9253L28.5065 47.6158L16.4904 57.6114Z"
																		fill="#E5363D"></path>
																</g>
																<defs>
																	<clippath id="clip0_392_16117">
																		<rect width="70" height="70" fill="white"></rect>
																	</clippath>
																</defs>
															</svg>
														</div>
													</div>
													<div class="box-body">
														<h3 class="elementskit-info-box-title">
															info@tpvconstruction.com.ng </h3>
													</div>
												</div>
											</a>
										</div>
									</div>
								</div>

								<!-- Location Marquee -->
								<div class="elementor-element elementor-element-2807571 footer-info-box location-marquee ekit-equal-height-disable elementor-widget elementor-widget-elementskit-icon-box"
									data-id="2807571" data-element_type="widget" data-e-type="widget"
									data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
									data-widget_type="elementskit-icon-box.default">
									<div class="elementor-widget-container">
										<div class="ekit-wid-con location-marquee-container">
											<div class="elementskit-infobox text-left text-left icon-lef-right-aligin elementor-animation- media">
												<div class="elementskit-box-header elementor-animation-">
													<div class="elementskit-info-box-icon text-center">
														<svg xmlns="http://www.w3.org/2000/svg" width="71"
															height="71" viewbox="0 0 71 71" fill="none">
															<g clip-path="url(#clip0_474_31)">
																<path
																	d="M68.901 32.5371C68.096 32.5371 67.4427 33.1997 67.4427 34.0161V47.3275H46.1715L29.526 30.4457V2.95619H36.8177C37.6227 2.95619 38.276 2.29358 38.276 1.47715C38.276 0.660715 37.6227 -0.00189633 36.8177 -0.00189633H13.4844C6.24813 -0.00189633 0.359375 5.97048 0.359375 13.3095V57.6808C0.359375 65.0198 6.24813 70.9922 13.4844 70.9922H57.2344C64.4706 70.9922 70.3594 65.0198 70.3594 57.6808V34.0161C70.3594 33.1997 69.706 32.5371 68.901 32.5371ZM13.4844 2.95619H26.6094V30.144L3.27604 41.9763V13.3095C3.27604 7.60039 7.85521 2.95619 13.4844 2.95619ZM3.27604 57.6808V45.2835L27.779 32.8565L43.6748 48.9781L30.6694 68.0311H13.4844C7.85521 68.0311 3.27604 63.3899 3.27604 57.6808ZM57.2344 68.0341H34.216L46.3319 50.2856H67.4427V57.6808C67.4427 63.3899 62.8635 68.0341 57.2344 68.0341ZM54.3177 22.1838C57.5348 22.1838 60.151 19.5304 60.151 16.2676C60.151 13.0048 57.5348 10.3514 54.3177 10.3514C51.1006 10.3514 48.4844 13.0048 48.4844 16.2676C48.4844 19.5304 51.1006 22.1838 54.3177 22.1838ZM54.3177 13.3095C55.9277 13.3095 57.2344 14.6347 57.2344 16.2676C57.2344 17.9004 55.9277 19.2257 54.3177 19.2257C52.7077 19.2257 51.401 17.9004 51.401 16.2676C51.401 14.6347 52.7077 13.3095 54.3177 13.3095ZM43.0185 27.7065L48.7702 33.0666C50.2606 34.5397 52.2323 35.3502 54.3177 35.3502C56.4031 35.3502 58.3777 34.5397 59.839 33.0932L65.6577 27.6621C68.691 24.6005 70.3594 20.5302 70.3594 16.1995C70.3594 11.8689 68.691 7.79858 65.6577 4.73696C62.6302 1.6783 58.6023 -0.0078125 54.3177 -0.0078125C50.0331 -0.0078125 46.0052 1.67534 42.9748 4.73696C39.9444 7.79858 38.276 11.8689 38.276 16.1995C38.276 20.5302 39.9444 24.6035 43.0185 27.7065ZM45.031 6.8372C47.5131 4.3317 50.809 2.95619 54.3177 2.95619C57.8265 2.95619 61.1223 4.33466 63.6015 6.8372C66.0806 9.33974 67.4427 12.6646 67.4427 16.2025C67.4427 19.7404 66.0777 23.0653 63.6394 25.5264L57.8323 30.9456C55.9481 32.8122 52.7194 32.8417 50.771 30.916L45.031 25.5648C42.5548 23.0623 41.1927 19.7374 41.1927 16.1995C41.1927 12.6617 42.5577 9.33974 45.031 6.8372Z"
																	fill="#E5363D"></path>
															</g>
															<defs>
																<clippath id="clip0_474_31">
																	<rect width="70" height="71" fill="white"
																		transform="translate(0.359375)"></rect>
																</clippath>
															</defs>
														</svg>
													</div>
												</div>
												<div class="box-body location-text">
													<div class="marquee-wrapper">
														<div class="marquee-content">
															<span class="marquee-item">🏢 Head Office: 123 Main Street, Port Harcourt, Rivers State, Nigeria</span>
															<span class="marquee-separator">✦</span>
															<span class="marquee-item">🏙️ Lagos Branch: 456 Island Avenue, Victoria Island, Lagos, Nigeria</span>
															<span class="marquee-separator">✦</span>
															<span class="marquee-item">🏛️ Abuja Office: 789 Central Business District, Abuja, Nigeria</span>
															<span class="marquee-separator">✦</span>
															<span class="marquee-item">🏘️ Enugu Branch: 101 Zik Avenue, Enugu, Nigeria</span>
															<span class="marquee-separator">✦</span>
															<span class="marquee-item">🏗️ Kaduna Branch: 202 Ahmadu Bello Way, Kaduna, Nigeria</span>
															<span class="marquee-separator">✦</span>

															<!-- Duplicate for seamless loop -->
															<span class="marquee-item">🏢 Head Office: 123 Main Street, Port Harcourt, Rivers State, Nigeria</span>
															<span class="marquee-separator">✦</span>
															<span class="marquee-item">🏙️ Lagos Branch: 456 Island Avenue, Victoria Island, Lagos, Nigeria</span>
															<span class="marquee-separator">✦</span>
															<span class="marquee-item">🏛️ Abuja Office: 789 Central Business District, Abuja, Nigeria</span>
															<span class="marquee-separator">✦</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="elementor-element elementor-element-4294ee3 e-con-full e-flex e-con e-child"
						data-id="4294ee3" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-5ec7427 e-con-full e-flex e-con e-child"
							data-id="5ec7427" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-7e521ed elementor-widget elementor-widget-text-editor"
								data-id="7e521ed" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="text-editor.default">
								<div class="elementor-widget-container">
									<p>Copyright © 2026 TPV Construction and Services LTD. All Rights Reserved.</p>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-dbb027d e-con-full e-flex e-con e-child"
							data-id="dbb027d" data-element_type="container" data-e-type="container">
							<div class="elementor-element elementor-element-fc9583a elementor-widget elementor-widget-elementskit-social-media"
								data-id="fc9583a" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="elementskit-social-media.default">
								<div class="elementor-widget-container">
									<div class="ekit-wid-con">
										<ul class="ekit_social_media">
											<li class="elementor-repeater-item-4b0caec">
												<a href="#" aria-label="Instagram" class="instagram">
													<i aria-hidden="true" class="icon icon-instagram-1"></i>
												</a>
											</li>
											<li class="elementor-repeater-item-2d9d002">
												<a href="#" aria-label="Facebook" class="facebook">
													<i aria-hidden="true" class="icon icon-facebook"></i>
												</a>
											</li>
											<li class="elementor-repeater-item-189442f">
												<a href="#" aria-label="Twitter" class="twitter">
													<svg aria-hidden="true" class="e-font-icon-svg e-fab-x-twitter"
														viewbox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z">
														</path>
													</svg>
												</a>
											</li>
											<li class="elementor-repeater-item-a68971f">
												<a href="#" aria-label="LinkedIn" class="linkedin">
													<i aria-hidden="true" class="icon icon-linkedin"></i>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- CSS for Location Marquee -->
	<style>
		.location-marquee .location-text {
			width: 100%;
			overflow: hidden;
			position: relative;
		}

		.marquee-wrapper {
			width: 100%;
			overflow: hidden;
			position: relative;
			padding: 5px 0;
		}

		.marquee-content {
			display: flex;
			align-items: center;
			white-space: nowrap;
			animation: marquee 45s linear infinite;
			width: fit-content;
		}

		.location-marquee-container:hover .marquee-content {
			animation-play-state: paused;
		}

		.marquee-item {
			display: inline-block;
			color: #ffffff;
			font-size: 14px;
			font-weight: 400;
			margin: 0 5px;
		}

		.marquee-separator {
			display: inline-block;
			color: #E5363D;
			font-size: 16px;
			margin: 0 15px;
			font-weight: bold;
		}

		@keyframes marquee {
			0% {
				transform: translateX(0);
			}

			100% {
				transform: translateX(-50%);
			}
		}

		/* Responsive adjustments */
		@media (max-width: 768px) {
			.marquee-item {
				font-size: 12px;
			}

			.marquee-separator {
				margin: 0 10px;
				font-size: 14px;
			}

			.marquee-content {
				animation: marquee 35s linear infinite;
			}
		}

		@media (max-width: 480px) {
			.marquee-item {
				font-size: 11px;
			}

			.marquee-separator {
				margin: 0 8px;
			}

			.marquee-content {
				animation: marquee 30s linear infinite;
			}
		}
	</style>

	<script>
		jQuery(document).ready(function($) {
			'use strict';

			// Counter Animation Function
			function animateCounter($counter) {
				const $numberElement = $counter.find('.elementor-counter-number');
				const fromValue = parseFloat($numberElement.data('from-value')) || 0;
				const toValue = parseFloat($numberElement.data('to-value')) || 0;
				const duration = parseFloat($numberElement.data('duration')) || 3000;
				const delimiter = $numberElement.data('delimiter') || ',';

				// Format number with commas
				function formatNumber(num) {
					return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
				}

				// Animate using requestAnimationFrame
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

			// Check if element is in viewport
			function isElementInViewport($element) {
				const rect = $element[0].getBoundingClientRect();
				return (
					rect.top >= 0 &&
					rect.left >= 0 &&
					rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
					rect.right <= (window.innerWidth || document.documentElement.clientWidth)
				);
			}

			// Check if element is partially visible
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

			// Initialize counters
			function initCounters() {
				$('.elementor-counter').each(function() {
					const $counter = $(this);
					const $numberElement = $counter.find('.elementor-counter-number');

					// Check if counter has already been animated
					if ($counter.data('counter-animated')) {
						return;
					}

					// Check if counter is visible
					if (isElementPartiallyInViewport($counter)) {
						// Mark as animated
						$counter.data('counter-animated', true);

						// Get the target value
						const toValue = parseFloat($numberElement.data('to-value')) || 0;

						// Start with 0
						$numberElement.text('0');

						// Animate
						animateCounter($counter);
					}
				});
			}

			// jQuery Counter Fallback (if requestAnimationFrame is not supported)
			function jQueryAnimateCounter($counter) {
				const $numberElement = $counter.find('.elementor-counter-number');
				const fromValue = parseFloat($numberElement.data('from-value')) || 0;
				const toValue = parseFloat($numberElement.data('to-value')) || 0;
				const duration = parseFloat($numberElement.data('duration')) || 3000;
				const delimiter = $numberElement.data('delimiter') || ',';

				function formatNumber(num) {
					return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
				}

				$({
					count: fromValue
				}).animate({
					count: toValue
				}, {
					duration: duration,
					easing: 'swing',
					step: function() {
						$numberElement.text(formatNumber(Math.floor(this.count)));
					},
					complete: function() {
						$numberElement.text(formatNumber(toValue));
					}
				});
			}

			// Initialize counters with jQuery fallback
			function initCountersWithFallback() {
				$('.elementor-counter').each(function() {
					const $counter = $(this);

					if ($counter.data('counter-animated')) {
						return;
					}

					if (isElementPartiallyInViewport($counter)) {
						$counter.data('counter-animated', true);

						// Use requestAnimationFrame if available, otherwise use jQuery animate
						if (window.requestAnimationFrame) {
							animateCounter($counter);
						} else {
							jQueryAnimateCounter($counter);
						}
					}
				});
			}

			// Throttle function to limit scroll event calls
			function throttle(func, limit) {
				let inThrottle;
				return function() {
					const args = arguments;
					const context = this;
					if (!inThrottle) {
						func.apply(context, args);
						inThrottle = true;
						setTimeout(() => inThrottle = false, limit);
					}
				};
			}

			// Initialize on page load
			setTimeout(function() {
				initCountersWithFallback();
			}, 500);

			// Initialize on window load (ensures all images are loaded)
			$(window).on('load', function() {
				initCountersWithFallback();
			});

			// Initialize on scroll (throttled)
			$(window).on('scroll', throttle(function() {
				initCountersWithFallback();
			}, 200));

			// Initialize on resize
			$(window).on('resize', throttle(function() {
				initCountersWithFallback();
			}, 200));

			// For Elementor frontend (if using Elementor page builder)
			if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
					if ($scope.find('.elementor-counter').length) {
						setTimeout(function() {
							initCountersWithFallback();
						}, 500);
					}
				});
			}

			// MutationObserver for dynamically added content
			if (window.MutationObserver) {
				const observer = new MutationObserver(function(mutations) {
					mutations.forEach(function(mutation) {
						if (mutation.addedNodes.length) {
							initCountersWithFallback();
						}
					});
				});

				observer.observe(document.body, {
					childList: true,
					subtree: true
				});
			}

			// Intersection Observer for better performance (modern browsers)
			if (window.IntersectionObserver) {
				const counterObserver = new IntersectionObserver((entries) => {
					entries.forEach(entry => {
						if (entry.isIntersecting) {
							const $counter = $(entry.target);

							if (!$counter.data('counter-animated')) {
								$counter.data('counter-animated', true);

								if (window.requestAnimationFrame) {
									animateCounter($counter);
								} else {
									jQueryAnimateCounter($counter);
								}

								// Unobserve after animation starts
								counterObserver.unobserve(entry.target);
							}
						}
					});
				}, {
					threshold: 0.3, // Trigger when 30% of the element is visible
					rootMargin: '50px' // Start loading 50px before the element enters viewport
				});

				// Observe all counters
				$('.elementor-counter').each(function() {
					counterObserver.observe(this);
				});
			}

			// Manual trigger for counters (can be called from anywhere)
			window.refreshCounters = function() {
				initCountersWithFallback();
			};

			// Handle counters in tabs, accordions, etc.
			$(document).on('ekit_accordion_change ekit_tab_change', function() {
				setTimeout(function() {
					initCountersWithFallback();
				}, 300);
			});
		});
	</script>

	<script>
		jQuery(document).ready(function($) {
			'use strict';

			// Initialize Testimonials Slider
			function initTestimonialsSlider() {
				// Check if Swiper is available
				if (typeof Swiper === 'undefined') {
					// If Swiper is not loaded, load it dynamically
					loadSwiperLibrary();
					return;
				}

				// Find all testimonial sliders
				$('.elementskit-testimonial-slider').each(function() {
					const $slider = $(this);
					const $container = $slider.find('.ekit-main-swiper');

					if ($container.length && !$container.hasClass('swiper-initialized')) {

						// Get slider configuration from data attribute
						let config = $slider.data('config');

						// Parse config if it's a string
						if (typeof config === 'string') {
							try {
								config = JSON.parse(config);
							} catch (e) {
								console.error('Error parsing slider config:', e);
								config = getDefaultConfig();
							}
						} else if (!config) {
							config = getDefaultConfig();
						}

						// Enhanced configuration with additional options
						const swiperConfig = {
							// Core options
							slidesPerView: config.slidesPerView || 3,
							spaceBetween: config.spaceBetween || 30,
							speed: config.speed || 1000,
							loop: config.loop || true,

							// Autoplay configuration
							autoplay: config.autoplay ? {
								delay: config.autoplayDelay || 3000,
								disableOnInteraction: false,
								pauseOnMouseEnter: config.pauseOnHover || true,
							} : false,

							// Pagination
							pagination: config.dots ? {
								el: $slider.find('.swiper-pagination')[0],
								clickable: true,
								dynamicBullets: true,
							} : false,

							// Navigation arrows
							navigation: config.arrows ? {
								nextEl: $slider.find('.swiper-button-next')[0],
								prevEl: $slider.find('.swiper-button-prev')[0],
							} : false,

							// Responsive breakpoints
							breakpoints: config.breakpoints || {
								320: {
									slidesPerView: 1,
									spaceBetween: 10,
								},
								768: {
									slidesPerView: 2,
									spaceBetween: 20,
								},
								1024: {
									slidesPerView: config.slidesPerView || 3,
									spaceBetween: config.spaceBetween || 30,
								},
							},

							// Additional features
							grabCursor: true,
							keyboard: {
								enabled: true,
							},
							mousewheel: false,
							watchSlidesProgress: true,
							watchSlidesVisibility: true,

							// Callbacks
							on: {
								init: function() {
									console.log('Testimonials slider initialized');
									$container.addClass('swiper-initialized');
								},
								slideChange: function() {
									// Optional: Add custom behavior on slide change
									updateActiveSlide(this);
								},
								autoplayStart: function() {
									// Optional: Handle autoplay start
								},
								autoplayStop: function() {
									// Optional: Handle autoplay stop
								},
							},
						};

						// Initialize Swiper
						try {
							const swiper = new Swiper($container[0], swiperConfig);

							// Store swiper instance for later use
							$container.data('swiper', swiper);

							// Add pause on hover functionality
							if (swiperConfig.autoplay) {
								$container.on('mouseenter', function() {
									swiper.autoplay.stop();
								}).on('mouseleave', function() {
									swiper.autoplay.start();
								});
							}

						} catch (error) {
							console.error('Error initializing Swiper:', error);
						}
					}
				});
			}

			// Update active slide (optional animation)
			function updateActiveSlide(swiper) {
				// Remove active class from all slides
				$(swiper.slides).removeClass('swiper-slide-active-custom');

				// Add active class to current slide
				$(swiper.slides[swiper.activeIndex]).addClass('swiper-slide-active-custom');
			}

			// Get default configuration
			function getDefaultConfig() {
				return {
					slidesPerView: 3,
					spaceBetween: 30,
					speed: 1000,
					loop: true,
					autoplay: true,
					autoplayDelay: 3000,
					dots: true,
					arrows: false,
					pauseOnHover: true,
					breakpoints: {
						320: {
							slidesPerView: 1,
							spaceBetween: 10,
						},
						768: {
							slidesPerView: 2,
							spaceBetween: 20,
						},
						1024: {
							slidesPerView: 3,
							spaceBetween: 30,
						},
					},
				};
			}

			// Load Swiper library dynamically if not present
			function loadSwiperLibrary() {
				if (typeof Swiper !== 'undefined') {
					initTestimonialsSlider();
					return;
				}

				// Load Swiper CSS
				const swiperCSS = document.createElement('link');
				swiperCSS.rel = 'stylesheet';
				swiperCSS.href = 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css';
				document.head.appendChild(swiperCSS);

				// Load Swiper JS
				const script = document.createElement('script');
				script.src = 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js';
				script.onload = function() {
					// Initialize after Swiper is loaded
					initTestimonialsSlider();
				};
				document.body.appendChild(script);
			}

			// Enhanced slider with custom animations
			function enhanceSliderWithAnimations() {
				$('.elementskit-testimonial-slider .swiper-slide').each(function() {
					$(this).on('mouseenter', function() {
						// Optional: Add hover effects
						$(this).css({
							'transform': 'scale(1.02)',
							'transition': 'transform 0.3s ease'
						});
					}).on('mouseleave', function() {
						$(this).css({
							'transform': 'scale(1)',
							'transition': 'transform 0.3s ease'
						});
					});
				});
			}

			// Add custom navigation if needed
			function addCustomNavigation() {
				$('.elementskit-testimonial-slider').each(function() {
					const $slider = $(this);
					const $container = $slider.find('.ekit-main-swiper');

					// Check if custom navigation buttons exist, if not add them
					if ($slider.find('.swiper-button-prev').length === 0) {
						const $prevBtn = $('<div class="swiper-button-prev"></div>');
						const $nextBtn = $('<div class="swiper-button-next"></div>');

						$slider.append($prevBtn);
						$slider.append($nextBtn);
					}
				});
			}

			// Handle window resize
			function handleResize() {
				let resizeTimer;
				$(window).on('resize', function() {
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(function() {
						$('.elementskit-testimonial-slider .ekit-main-swiper').each(function() {
							const swiper = $(this).data('swiper');
							if (swiper) {
								swiper.update();
							}
						});
					}, 250);
				});
			}

			// Initialize on document ready
			initTestimonialsSlider();

			// Re-initialize on Elementor frontend reload (for page builders)
			if (typeof elementorFrontend !== 'undefined') {
				$(window).on('elementor/frontend/init', function() {
					elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
						if ($scope.find('.elementskit-testimonial-slider').length) {
							setTimeout(initTestimonialsSlider, 500);
						}
					});
				});
			}

			// Initialize on AJAX content load
			$(document).on('ajaxComplete', function() {
				setTimeout(initTestimonialsSlider, 500);
			});

			// Optional: Add progress bar
			function addProgressBar($slider, swiper) {
				if ($slider.find('.swiper-progress-bar').length === 0) {
					const $progressBar = $('<div class="swiper-progress-bar"><div class="progress-fill"></div></div>');
					$slider.append($progressBar);

					const $progressFill = $progressBar.find('.progress-fill');

					if (swiper.params.autoplay && swiper.params.autoplay.enabled) {
						const duration = swiper.params.autoplay.delay;

						swiper.on('slideChange', function() {
							$progressFill.css({
								'transition': 'none',
								'width': '0%'
							});

							setTimeout(function() {
								$progressFill.css({
									'transition': 'width ' + duration + 'ms linear',
									'width': '100%'
								});
							}, 50);
						});

						// Trigger initial progress bar
						setTimeout(function() {
							$progressFill.css({
								'transition': 'width ' + duration + 'ms linear',
								'width': '100%'
							});
						}, 100);
					}
				}
			}

			// Add progress bar to sliders (optional - uncomment if needed)
			// $('.elementskit-testimonial-slider .ekit-main-swiper').each(function() {
			//     const swiper = $(this).data('swiper');
			//     if (swiper) {
			//         addProgressBar($(this).closest('.elementskit-testimonial-slider'), swiper);
			//     }
			// });

			// Add keyboard navigation
			$(document).on('keydown', function(e) {
				const $activeSlider = $('.elementskit-testimonial-slider .ekit-main-swiper.swiper-initialized');

				if ($activeSlider.length) {
					const swiper = $activeSlider.data('swiper');

					if (swiper) {
						switch (e.keyCode) {
							case 37: // Left arrow
								swiper.slidePrev();
								break;
							case 39: // Right arrow
								swiper.slideNext();
								break;
						}
					}
				}
			});

			// Add touch swipe enhancement
			function enhanceTouchSwipe() {
				$('.elementskit-testimonial-slider .ekit-main-swiper').on('touchstart', function(e) {
					$(this).addClass('touching');
				}).on('touchend', function(e) {
					$(this).removeClass('touching');
				});
			}

			enhanceTouchSwipe();
			handleResize();
		});
	</script>

	<script>
		const lazyloadRunObserver = () => {
			const lazyloadBackgrounds = document.querySelectorAll(`.e-con.e-parent:not(.e-lazyloaded)`);
			const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						let lazyloadBackground = entry.target;
						if (lazyloadBackground) {
							lazyloadBackground.classList.add('e-lazyloaded');
						}
						lazyloadBackgroundObserver.unobserve(entry.target);
					}
				});
			}, {
				rootMargin: '200px 0px 200px 0px'
			});
			lazyloadBackgrounds.forEach((lazyloadBackground) => {
				lazyloadBackgroundObserver.observe(lazyloadBackground);
			});
		};
		const events = [
			'DOMContentLoaded',
			'elementor/lazyload/observe',
		];
		events.forEach((event) => {
			document.addEventListener(event, lazyloadRunObserver);
		});
	</script>
	<script src="wp-includes/js/dist/hooks.min.js?ver=dd5603f07f9220ed27f1" id="wp-hooks-js"></script>
	<script src="wp-includes/js/dist/i18n.min.js?ver=c26c3dc7bed366793375" id="wp-i18n-js"></script>
	<script id="wp-i18n-js-after">
		wp.i18n.setLocaleData({
			'text direction\u0004ltr': ['ltr']
		});
		//# sourceURL=wp-i18n-js-after
	</script>
	<script src="wp-content/plugins/contact-form-7/includes/swv/js/index.js?ver=6.1.5" id="swv-js"></script>
	<script id="contact-form-7-js-before">
		var wpcf7 = {
			"api": {
				"root": "https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-json\/",
				"namespace": "contact-form-7\/v1"
			},
			"cached": 1
		};
		//# sourceURL=contact-form-7-js-before
	</script>
	<script src="wp-content/plugins/contact-form-7/includes/js/index.js?ver=6.1.5" id="contact-form-7-js"></script>
	<script src="wp-content/themes/tpv/assets/js/SmoothScroll.js?ver=1.0.8" id="SmoothScroll-js"></script>
	<script src="wp-content/themes/tpv/assets/js/gsap.min.js?ver=1.0.8" id="gsap-js"></script>
	<script src="wp-content/themes/tpv/assets/js/magiccursor.js?ver=1.0.8" id="magiccursor-js"></script>
	<script src="wp-content/themes/tpv/assets/js/SplitText.js?ver=1.0.8" id="SplitText-js"></script>
	<script src="wp-content/themes/tpv/assets/js/ScrollTrigger.min.js?ver=1.0.8" id="ScrollTrigger-js"></script>
	<script src="wp-content/themes/tpv/assets/js/function.js?ver=1.0.8" id="theme-js-js"></script>
	<script src="wp-content/plugins/elementskit-lite/libs/framework/assets/js/frontend-script.js?ver=3.7.9"
		id="elementskit-framework-js-frontend-js"></script>

	<script src="wp-content/plugins/elementskit-lite/widgets/init/assets/js/widget-scripts.js?ver=3.7.9"
		id="ekit-widget-scripts-js"></script>
	<script src="wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.35.3"
		id="elementor-webpack-runtime-js"></script>
	<script src="wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.35.3"
		id="elementor-frontend-modules-js"></script>
	<script src="wp-includes/js/jquery/ui/core.min.js?ver=1.13.3" id="jquery-ui-core-js"></script>
	<script id="elementor-frontend-js-before">
		var elementorFrontendConfig = {
			"environmentMode": {
				"edit": false,
				"wpPreview": false,
				"isScriptDebug": false
			},
			"i18n": {
				"shareOnFacebook": "Share on Facebook",
				"shareOnTwitter": "Share on Twitter",
				"pinIt": "Pin it",
				"download": "Download",
				"downloadImage": "Download image",
				"fullscreen": "Fullscreen",
				"zoom": "Zoom",
				"share": "Share",
				"playVideo": "Play Video",
				"previous": "Previous",
				"next": "Next",
				"close": "Close",
				"a11yCarouselPrevSlideMessage": "Previous slide",
				"a11yCarouselNextSlideMessage": "Next slide",
				"a11yCarouselFirstSlideMessage": "This is the first slide",
				"a11yCarouselLastSlideMessage": "This is the last slide",
				"a11yCarouselPaginationBulletMessage": "Go to slide"
			},
			"is_rtl": false,
			"breakpoints": {
				"xs": 0,
				"sm": 480,
				"md": 768,
				"lg": 1025,
				"xl": 1440,
				"xxl": 1600
			},
			"responsive": {
				"breakpoints": {
					"mobile": {
						"label": "Mobile Portrait",
						"value": 767,
						"default_value": 767,
						"direction": "max",
						"is_enabled": true
					},
					"mobile_extra": {
						"label": "Mobile Landscape",
						"value": 880,
						"default_value": 880,
						"direction": "max",
						"is_enabled": false
					},
					"tablet": {
						"label": "Tablet Portrait",
						"value": 1024,
						"default_value": 1024,
						"direction": "max",
						"is_enabled": true
					},
					"tablet_extra": {
						"label": "Tablet Landscape",
						"value": 1200,
						"default_value": 1200,
						"direction": "max",
						"is_enabled": false
					},
					"laptop": {
						"label": "Laptop",
						"value": 1366,
						"default_value": 1366,
						"direction": "max",
						"is_enabled": false
					},
					"widescreen": {
						"label": "Widescreen",
						"value": 2400,
						"default_value": 2400,
						"direction": "min",
						"is_enabled": false
					}
				},
				"hasCustomBreakpoints": false
			},
			"version": "3.35.3",
			"is_static": false,
			"experimentalFeatures": {
				"e_font_icon_svg": true,
				"additional_custom_breakpoints": true,
				"container": true,
				"nested-elements": true,
				"home_screen": true,
				"global_classes_should_enforce_capabilities": true,
				"e_variables": true,
				"cloud-library": true,
				"e_opt_in_v4_page": true,
				"e_components": true,
				"e_interactions": true,
				"e_editor_one": true,
				"import-export-customization": true
			},
			"urls": {
				"assets": "https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-content\/plugins\/elementor\/assets\/",
				"ajaxurl": "https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-admin\/admin-ajax.php",
				"uploadUrl": "https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-content\/uploads"
			},
			"nonces": {
				"floatingButtonsClickTracking": "cb0ff6a0d2"
			},
			"swiperClass": "swiper",
			"settings": {
				"page": [],
				"editorPreferences": []
			},
			"kit": {
				"body_background_background": "classic",
				"active_breakpoints": ["viewport_mobile", "viewport_tablet"],
				"global_image_lightbox": "yes",
				"lightbox_enable_counter": "yes",
				"lightbox_enable_fullscreen": "yes",
				"lightbox_enable_zoom": "yes",
				"lightbox_enable_share": "yes",
				"lightbox_title_src": "title",
				"lightbox_description_src": "description"
			},
			"post": {
				"id": 10,
				"title": "TPV Construction and Services LTD%20%E2%80%93%20Construction%20WordPress%20Theme",
				"excerpt": "",
				"featuredImage": false
			}
		};
		//# sourceURL=elementor-frontend-js-before
	</script>
	<script src="wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.35.3" id="elementor-frontend-js"></script>
	<script src="wp-content/plugins/elementskit/modules/parallax/assets/js/jarallax.js?ver=4.2.1"
		id="jarallax-js"></script>
	<script src="wp-content/plugins/elementskit/modules/parallax/assets/js/parallax-admin.js?ver=4.2.1"
		id="elementskit-parallax-admin-js"></script>
	<script src="wp-content/plugins/elementor/assets/lib/jquery-numerator/jquery-numerator.min.js?ver=0.2.1"
		id="jquery-numerator-js"></script>
	<script src="wp-includes/js/imagesloaded.min.js?ver=5.0.0" id="imagesloaded-js"></script>
	<script src="wp-content/plugins/elementskit-lite/widgets/init/assets/js/animate-circle.min.js?ver=3.7.9"
		id="animate-circle-js"></script>

	<script src="wp-content/plugins/elementskit-lite/widgets/init/assets/js/elementor.js?ver=3.7.9"
		id="elementskit-elementor-js"></script>
	<script src="wp-content/plugins/elementskit/widgets/init/assets/js/elementor.js?ver=4.2.1"
		id="elementskit-elementor-pro-js"></script>
	<script id="wp-emoji-settings" type="application/json">
		{
			"baseUrl": "https://s.w.org/images/core/emoji/17.0.2/72x72/",
			"ext": ".png",
			"svgUrl": "https://s.w.org/images/core/emoji/17.0.2/svg/",
			"svgExt": ".svg",
			"source": {
				"concatemoji": "https://tpvconstruction.com.ng/wp-includes/js/wp-emoji-release.min.js?ver=6.9.1"
			}
		}
	</script>
	<script type="module">
		/*! This file is auto-generated */
		const a = JSON.parse(document.getElementById("wp-emoji-settings").textContent),
			o = (window._wpemojiSettings = a, "wpEmojiSettingsSupports"),
			s = ["flag", "emoji"];

		function i(e) {
			try {
				var t = {
					supportTests: e,
					timestamp: (new Date).valueOf()
				};
				sessionStorage.setItem(o, JSON.stringify(t))
			} catch (e) {}
		}

		function c(e, t, n) {
			e.clearRect(0, 0, e.canvas.width, e.canvas.height), e.fillText(t, 0, 0);
			t = new Uint32Array(e.getImageData(0, 0, e.canvas.width, e.canvas.height).data);
			e.clearRect(0, 0, e.canvas.width, e.canvas.height), e.fillText(n, 0, 0);
			const a = new Uint32Array(e.getImageData(0, 0, e.canvas.width, e.canvas.height).data);
			return t.every((e, t) => e === a[t])
		}

		function p(e, t) {
			e.clearRect(0, 0, e.canvas.width, e.canvas.height), e.fillText(t, 0, 0);
			var n = e.getImageData(16, 16, 1, 1);
			for (let e = 0; e < n.data.length; e++)
				if (0 !== n.data[e]) return !1;
			return !0
		}

		function u(e, t, n, a) {
			switch (t) {
				case "flag":
					return n(e, "\ud83c\udff3\ufe0f\u200d\u26a7\ufe0f", "\ud83c\udff3\ufe0f\u200b\u26a7\ufe0f") ? !1 : !n(e, "\ud83c\udde8\ud83c\uddf6", "\ud83c\udde8\u200b\ud83c\uddf6") && !n(e, "\ud83c\udff4\udb40\udc67\udb40\udc62\udb40\udc65\udb40\udc6e\udb40\udc67\udb40\udc7f", "\ud83c\udff4\u200b\udb40\udc67\u200b\udb40\udc62\u200b\udb40\udc65\u200b\udb40\udc6e\u200b\udb40\udc67\u200b\udb40\udc7f");
				case "emoji":
					return !a(e, "\ud83e\u1fac8")
			}
			return !1
		}

		function f(e, t, n, a) {
			let r;
			const o = (r = "undefined" != typeof WorkerGlobalScope && self instanceof WorkerGlobalScope ? new OffscreenCanvas(300, 150) : document.createElement("canvas")).getContext("2d", {
					willReadFrequently: !0
				}),
				s = (o.textBaseline = "top", o.font = "600 32px Arial", {});
			return e.forEach(e => {
				s[e] = t(o, e, n, a)
			}), s
		}

		function r(e) {
			var t = document.createElement("script");
			t.src = e, t.defer = !0, document.head.appendChild(t)
		}
		a.supports = {
			everything: !0,
			everythingExceptFlag: !0
		}, new Promise(t => {
			let n = function() {
				try {
					var e = JSON.parse(sessionStorage.getItem(o));
					if ("object" == typeof e && "number" == typeof e.timestamp && (new Date).valueOf() < e.timestamp + 604800 && "object" == typeof e.supportTests) return e.supportTests
				} catch (e) {}
				return null
			}();
			if (!n) {
				if ("undefined" != typeof Worker && "undefined" != typeof OffscreenCanvas && "undefined" != typeof URL && URL.createObjectURL && "undefined" != typeof Blob) try {
					var e = "postMessage(" + f.toString() + "(" + [JSON.stringify(s), u.toString(), c.toString(), p.toString()].join(",") + "));",
						a = new Blob([e], {
							type: "text/javascript"
						});
					const r = new Worker(URL.createObjectURL(a), {
						name: "wpTestEmojiSupports"
					});
					return void(r.onmessage = e => {
						i(n = e.data), r.terminate(), t(n)
					})
				} catch (e) {}
				i(n = f(s, u, c, p))
			}
			t(n)
		}).then(e => {
			for (const n in e) a.supports[n] = e[n], a.supports.everything = a.supports.everything && a.supports[n], "flag" !== n && (a.supports.everythingExceptFlag = a.supports.everythingExceptFlag && a.supports[n]);
			var t;
			a.supports.everythingExceptFlag = a.supports.everythingExceptFlag && !a.supports.flag, a.supports.everything || ((t = a.source || {}).concatemoji ? r(t.concatemoji) : t.wpemoji && t.twemoji && (r(t.twemoji), r(t.wpemoji)))
		});
		//# sourceURL=https://tpvconstruction.com.ng/wp-includes/js/wp-emoji-loader.min.js
	</script>
	<script src="../assets/js/theme-panel.js"></script>

</body>

</html>
