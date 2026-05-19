<?php
require_once '../config/config.php';
$pageSettings = new Settings();
$supportPhone = trim((string) $pageSettings->get('company_phone', '+234 701 234 5678'));
$supportPhoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $supportPhone);
$aboutIntroEyebrow = trim((string) $pageSettings->get('about_intro_eyebrow', 'Welcome to TPV'));
$aboutIntroTitle = (string) $pageSettings->get('about_intro_title', 'TPV Construction and Services LTD<br>Company');
$aboutIntroBody = trim((string) $pageSettings->get('about_intro_body', "Serving Nigeria's construction needs since 2008 with excellence and innovation. TPV Construction and Services LTD has established itself as a trusted name in Nigeria's construction industry, delivering exceptional projects across residential, commercial, and industrial sectors. Our commitment to quality, safety, and sustainable building practices has made us the preferred choice for discerning clients throughout Nigeria."));
$aboutFeatureOne = trim((string) $pageSettings->get('about_feature_1', 'Comprehensive Services'));
$aboutFeatureTwo = trim((string) $pageSettings->get('about_feature_2', 'Advanced Technology'));
$aboutFeatureThree = trim((string) $pageSettings->get('about_feature_3', 'Transparent Communication'));
$aboutQuoteButton = trim((string) $pageSettings->get('about_quote_button_text', 'Get Free Quote'));
$aboutSupportLabel = trim((string) $pageSettings->get('about_support_label', 'call support center 24X7'));
$aboutStoryTitle = trim((string) $pageSettings->get('about_story_title', 'TPV Construction and Services LTD'));
$aboutStoryBody = trim((string) $pageSettings->get('about_story_body', 'With over 15 years of experience and hundreds of successful projects across Nigeria, TPV Construction and Services LTD has established itself as a trusted name in the construction industry.'));
$aboutBottomCtaBody = trim((string) $pageSettings->get('about_bottom_cta_body', "From residential homes in Lagos to commercial complexes in Abuja, TPV Construction and Services LTD delivers excellence across Nigeria. Let's bring your vision to life with quality craftsmanship and professional service."));
$aboutIntroImage = trim((string) $pageSettings->get('about_intro_image', 'wp-content/uploads/2024/06/about-us-img.png'));
$aboutHistoryImage = trim((string) $pageSettings->get('about_history_image', 'wp-content/uploads/2024/06/company-history-img.jpg'));
$aboutVideoBackgroundImage = trim((string) $pageSettings->get('about_video_bg_image', 'wp-content/uploads/2024/06/video-bg.jpg'));
$aboutCtaImage = trim((string) $pageSettings->get('about_cta_image', 'wp-content/uploads/2024/06/cta-box-img.png'));
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>About Us &#8211; TPV Construction and Services LTD</title>
	<meta name='robots' content='max-image-preview:large'>
	<link rel='dns-prefetch' href='//fonts.googleapis.com'>
	<link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Feed" href="../feed/">
	<link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Comments Feed" href="../comments/feed/">
	<link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed"
		href="../wp-json/oembed/1.0/embed-6?url=https%3A%2F%2Fdemo.awaikenthemes.com%2FTPV Construction Services%2Fabout-us%2F">
	<link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed"
		href="../wp-json/oembed/1.0/embed-7?url=https%3A%2F%2Fdemo.awaikenthemes.com%2FTPV Construction Services%2Fabout-us%2F&#038;format=xml">
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
	<link rel='stylesheet' id='elementor-frontend-css'
		href='../wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='elementor-post-7-css'
		href='../wp-content/uploads/elementor/css/post-7.css?ver=1770715450' media='all'>
	<link rel='stylesheet' id='widget-heading-css'
		href='../wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='e-animation-fadeInUp-css'
		href='../wp-content/plugins/elementor/assets/lib/animations/styles/fadeInUp.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-image-css'
		href='../wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-icon-list-css'
		href='../wp-content/plugins/elementor/assets/css/widget-icon-list.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-icon-box-css'
		href='../wp-content/plugins/elementor/assets/css/widget-icon-box.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='widget-counter-css'
		href='../wp-content/plugins/elementor/assets/css/widget-counter.min.css?ver=3.35.3' media='all'>
	<link rel='stylesheet' id='mediaelement-css'
		href='../wp-includes/js/mediaelement/mediaelementplayer-legacy.min.css?ver=4.2.17' media='all'>
	<link rel='stylesheet' id='wp-mediaelement-css'
		href='../wp-includes/js/mediaelement/wp-mediaelement.min.css?ver=6.9.1' media='all'>
	<link rel='stylesheet' id='elementor-post-723-css'
		href='../wp-content/uploads/elementor/css/post-723.css?ver=1770719669' media='all'>
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
	<link rel='stylesheet' id='fontawesome-6.4.0-css'
		href='../wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='bootstrap-5.3.2-css'
		href='../wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='TPV Construction Services-style-css' href='../wp-content/themes/tpv/style.css?ver=1.0.8' media='all'>
	<link rel='stylesheet' id='elementor-gf-local-manrope-css'
		href='../wp-content/uploads/elementor/google-fonts/css/manrope.css?ver=1744107304' media='all'>
	<link rel='stylesheet' id='elementor-gf-local-dmsans-css'
		href='../wp-content/uploads/elementor/google-fonts/css/dmsans.css?ver=1744107305' media='all'>
	<link rel='stylesheet' id='elementor-icons-ekiticons-css'
		href='../wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/css/ekiticons.css?ver=3.7.9'
		media='all'>
	<script src="../wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
	<script src="../wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
	<link rel="https://api.w.org/" href="../wp-json/">
	<link rel="alternate" title="JSON" type="application/json" href="../wp-json/wp/v2/pages/723">
	<link rel="EditURI" type="application/rsd+xml" title="RSD"
		href="https://tpvconstruction.com.ng/xmlrpc.php?rsd">

	<link rel="canonical" href="index.htm">
	<link rel='shortlink' href='index.htm?p=723'>
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
	<link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="32x32">
	<link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="192x192">
	<link rel="apple-touch-icon" href="../wp-content/uploads/2024/06/favicon.png">
	<meta name="msapplication-TileImage"
		content="https://tpvconstrcution.com.ng/wp-content/uploads/2024/06/favicon.png">
</head>

<body
	class="wp-singular page-template page-template-elementor_header_footer page page-id-723 wp-custom-logo wp-theme-TPV Construction Services tt-magic-cursor elementor-default elementor-template-full-width elementor-kit-7 elementor-page elementor-page-723">

	
	</div>

	<a class="skip-link screen-reader-text" href="#content">
		Skip to content </a>


	<?php
	include '../includes/header.php';
	?>

	<div data-elementor-type="wp-page" data-elementor-id="723" class="elementor elementor-723">
		<div class="elementor-element elementor-element-16d5d11 e-con-full e-flex e-con e-parent" data-id="16d5d11"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="elementor-element elementor-element-06ab317 e-con-full e-flex e-con e-child" data-id="06ab317"
				data-element_type="container" data-e-type="container"
				data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
				<div class="elementor-element elementor-element-b67bf2b e-flex e-con-boxed e-con e-child"
					data-id="b67bf2b" data-element_type="container" data-e-type="container">
					<div class="e-con-inner">
						<div class="elementor-element elementor-element-61a1670 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="61a1670" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h1 class="elementor-heading-title elementor-size-default">About Us</h1>
							</div>
						</div>
						<div class="elementor-element elementor-element-dfc4cb1 elementor-invisible elementor-widget elementor-widget-elementskit-breadcrumb"
							data-id="dfc4cb1" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation_mobile&quot;:&quot;fadeInUp&quot;,&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="elementskit-breadcrumb.default">
							<div class="elementor-widget-container">
								<div class="ekit-wid-con">
									<ol class="ekit-breadcrumb">
										<li class="ekit_breadcrumbs_start"><a
												href="https://tpvconstruction.com.ng">Home</a></li>
										<li class="brd_sep"><span class="separate_icon"><svg
													xmlns="http://www.w3.org/2000/svg" width="7" height="12"
													viewbox="0 0 7 12">
													<path
														d="M0.283883 11.68L4.95988 0.129999H6.70988L2.03388 11.68H0.283883Z"
														fill="white"></path>
												</svg></span></li>
										<li>About Us</li>
									</ol>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="elementor-element elementor-element-d43ff9f e-flex e-con-boxed e-con e-parent" data-id="d43ff9f"
			data-element_type="container" data-e-type="container">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-f7ca4ed e-con-full e-flex e-con e-child"
					data-id="f7ca4ed" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-b6f24f2 at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
						data-id="b6f24f2" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="image.default">
						<div class="elementor-widget-container">
							<img fetchpriority="high" decoding="async" width="574" height="659"
								src="<?php echo htmlspecialchars(tpv_asset_url($aboutIntroImage)); ?>"
								class="attachment-full size-full wp-image-872" alt="">
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-978811a e-con-full e-flex e-con e-child"
					data-id="978811a" data-element_type="container" data-e-type="container">

					<!-- Welcome to TPV -->
					<div class="elementor-element elementor-element-55c152e at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
						data-id="55c152e" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h3 class="elementor-heading-title elementor-size-default"><?php echo htmlspecialchars($aboutIntroEyebrow); ?></h3>
						</div>
					</div>

					<!-- TPV Construction and Services LTD -->
					<div class="elementor-element elementor-element-da702a5 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="da702a5" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default"><?php echo $aboutIntroTitle; ?></h2>
						</div>
					</div>

					<!-- Company Description -->
					<div class="elementor-element elementor-element-c068983 elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="c068983" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p><?php echo nl2br(htmlspecialchars($aboutIntroBody)); ?></p>
						</div>
					</div>

					<!-- Features List -->
					<div class="elementor-element elementor-element-c56365b e-con-full e-flex elementor-invisible e-con e-child"
						data-id="c56365b" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-element elementor-element-0352f40 elementor-align-start elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
							data-id="0352f40" data-element_type="widget" data-e-type="widget"
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
											</svg>
										</span>
										<span class="elementor-icon-list-text"><?php echo htmlspecialchars($aboutFeatureOne); ?></span>
									</li>
									<li class="elementor-icon-list-item">
										<span class="elementor-icon-list-icon">
											<svg aria-hidden="true" class="e-font-icon-svg e-fas-check-circle"
												viewbox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
												<path
													d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
												</path>
											</svg>
										</span>
										<span class="elementor-icon-list-text"><?php echo htmlspecialchars($aboutFeatureTwo); ?></span>
									</li>
									<li class="elementor-icon-list-item">
										<span class="elementor-icon-list-icon">
											<svg aria-hidden="true" class="e-font-icon-svg e-fas-check-circle"
												viewbox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
												<path
													d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
												</path>
											</svg>
										</span>
										<span class="elementor-icon-list-text"><?php echo htmlspecialchars($aboutFeatureThree); ?></span>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<!-- CTA Section -->
					<div class="elementor-element elementor-element-7624576 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="7624576" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:300}">

						<!-- Get Free Quote Button -->
						<div class="elementor-element elementor-element-d2c70f4 elementor-widget elementor-widget-button"
							data-id="d2c70f4" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="button.default">
							<div class="elementor-widget-container">
								<div class="elementor-button-wrapper">
									<a class="elementor-button elementor-button-link elementor-size-sm"
										href="../contact-us/">
										<span class="elementor-button-content-wrapper">
											<span class="elementor-button-icon">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
													viewbox="0 0 24 24" fill="none">
													<path
														d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
														stroke="white" stroke-width="1.5" stroke-linecap="round"
														stroke-linejoin="round"></path>
												</svg>
											</span>
											<span class="elementor-button-text"><?php echo htmlspecialchars($aboutQuoteButton); ?></span>
										</span>
									</a>
								</div>
							</div>
						</div>

						<!-- Call Support Center -->
						<div class="elementor-element elementor-element-8d07025 elementor-position-inline-start elementor-mobile-position-inline-start about-us-contact-support elementor-view-default elementor-widget elementor-widget-icon-box"
							data-id="8d07025" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="icon-box.default">
							<div class="elementor-widget-container">
								<div class="elementor-icon-box-wrapper">
									<div class="elementor-icon-box-icon">
										<a href="<?php echo htmlspecialchars($supportPhoneHref); ?>" class="elementor-icon" tabindex="-1"
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
											<a href="<?php echo htmlspecialchars($supportPhoneHref); ?>">
												<?php echo htmlspecialchars($aboutSupportLabel); ?>
											</a>
										</h3>
										<p class="elementor-icon-box-description">
											<?php echo htmlspecialchars($supportPhone); ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


			</div>
		</div>


		<div class="elementor-element elementor-element-2038bd0 e-flex e-con-boxed e-con e-parent" data-id="2038bd0"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-90392bd e-con-full e-flex e-con e-child"
					data-id="90392bd" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-b915904 image-anime at-image-animation at-animation-image-style-1 elementor-widget elementor-widget-image"
						data-id="b915904" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="image.default">
						<div class="elementor-widget-container">
							<img decoding="async" width="555" height="468"
								src="<?php echo htmlspecialchars(tpv_asset_url($aboutHistoryImage)); ?>"
								class="attachment-full size-full wp-image-1116" alt="">
						</div>
					</div>
					<div class="elementor-element elementor-element-5ea81ce elementor-widget__width-initial elementor-widget-mobile__width-initial elementor-absolute about-counter elementor-widget elementor-widget-counter"
						data-id="5ea81ce" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_position&quot;:&quot;absolute&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="counter.default">
						<div class="elementor-widget-container">
							<div class="elementor-counter">
								<div class="elementor-counter-title">Years Of Experience</div>
								<div class="elementor-counter-number-wrapper">
									<span class="elementor-counter-number-prefix"></span>
									<span class="elementor-counter-number" data-duration="2000" data-to-value="15"
										data-from-value="0" data-delimiter=",">0</span>
									<span class="elementor-counter-number-suffix">+</span>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="elementor-element elementor-element-636aaaa e-con-full e-flex e-con e-child"
					data-id="636aaaa" data-element_type="container" data-e-type="container">

					<!-- Our History Heading -->
					<div class="elementor-element elementor-element-ea3bbf6 at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
						data-id="ea3bbf6" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h3 class="elementor-heading-title elementor-size-default">our history</h3>
						</div>
					</div>

					<!-- TPV Construction and Services LTD Title -->
					<div class="elementor-element elementor-element-cf30dc8 elementor-widget-mobile__width-inherit at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="cf30dc8" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default"><?php echo htmlspecialchars($aboutStoryTitle); ?></h2>
						</div>
					</div>

					<!-- Nigeria's Premier Construction Partner -->
					<div class="elementor-element elementor-element-29e95c1 elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="29e95c1" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p class="wow fadeInUp" data-wow-delay="0.25s"><strong>Nigeria's Premier Construction Partner</strong></p>
							<p class="wow fadeInUp" data-wow-delay="0.5s"><?php echo nl2br(htmlspecialchars($aboutStoryBody)); ?></p>
							<p class="wow fadeInUp" data-wow-delay="0.75s">Our commitment to excellence, safety, and innovation has made us the preferred choice for residential, commercial, and industrial construction projects. From Lagos to Abuja, Port Harcourt to Kano, we deliver quality that stands the test of time.</p>
						</div>
					</div>
				</div>


			</div>
		</div>


		<div class="elementor-element elementor-element-acbcbb2 e-flex e-con-boxed e-con e-parent" data-id="acbcbb2"
			data-element_type="container" data-e-type="container">
			<div class="e-con-inner">
				<!-- Our Mission -->
				<div class="elementor-element elementor-element-005cbe3 e-con-full e-flex elementor-invisible e-con e-child"
					data-id="005cbe3" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;}">
					<div class="elementor-element elementor-element-4d27b32 elementor-view-default elementor-position-block-start elementor-mobile-position-block-start elementor-widget elementor-widget-icon-box"
						data-id="4d27b32" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="icon-box.default">
						<div class="elementor-widget-container">
							<div class="elementor-icon-box-wrapper">
								<div class="elementor-icon-box-icon">
									<span class="elementor-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
											viewbox="0 0 70 70" fill="none">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M33.0175 4.4549C32.7919 4.48634 31.869 4.61049 30.9667 4.7308C28.9454 5.00013 25.681 5.80924 23.8573 6.49297C15.4618 9.64037 9.28979 15.9352 6.29524 24.4042C5.7377 25.9809 5.14257 28.4146 4.83413 30.3792C4.49329 32.5495 4.49507 37.4594 4.83755 39.6484C6.08497 47.624 9.94058 54.4331 15.8244 59.0518C17.9625 60.7301 21.7014 62.7628 24.3557 63.6902C25.948 64.2467 29.1403 65.0231 30.8983 65.2818C39.1315 66.4931 47.5347 64.1929 54.1405 58.9197C55.4754 57.854 58.1376 55.1713 59.1543 53.8671C61.8259 50.4401 63.9166 46.0111 64.8758 41.7467C66.3997 34.9718 65.5449 27.5846 62.5441 21.5949C61.9733 20.4559 61.6106 20.0976 61.0279 20.0976C60.7346 20.0976 60.0194 20.8447 60.0194 21.1511C60.0194 21.2911 60.3519 22.1418 60.7583 23.0415C62.781 27.5209 63.6721 32.2197 63.3041 36.4657C62.9213 40.882 62.0046 44.369 60.293 47.9198C56.2822 56.2404 48.4274 61.7401 38.6913 63.0446C37.0643 63.2627 32.8486 63.2623 31.3085 63.0439C23.6952 61.9648 17.2333 58.4529 12.9243 53.0524C7.47841 46.2271 5.29938 36.154 7.46378 27.8106C9.97175 18.1425 17.8257 10.3301 27.7798 7.60203C30.6444 6.81699 32.0935 6.63228 35.3417 6.63816C38.3889 6.64363 39.5936 6.7891 42.246 7.47228C43.6568 7.83568 46.1471 8.78615 47.6088 9.51924C48.6703 10.0516 49.0502 10.0894 49.4646 9.70312C49.8738 9.32195 49.9635 8.85478 49.7183 8.38064C49.5607 8.0759 49.1633 7.81709 48.0323 7.28279C45.5328 6.10181 42.6267 5.19646 39.7831 4.71275C38.5509 4.50316 33.9323 4.32707 33.0175 4.4549ZM52.0065 10.2689C49.6406 12.6293 47.6722 14.689 47.6322 14.8461C47.5921 15.0032 47.6557 16.3252 47.7734 17.7842L47.9874 20.4366L40.9467 27.482C34.5196 33.9137 33.9061 34.5665 33.9061 34.975C33.9061 35.5754 34.4244 36.0937 35.0248 36.0937C35.4333 36.0937 36.0861 35.4802 42.5178 29.0531L49.5632 22.0125L52.2157 22.2264C53.6746 22.3441 54.9967 22.4077 55.1537 22.3677C55.3108 22.3276 57.3705 20.3593 59.7309 17.9933C63.9549 13.7597 64.021 13.6846 63.9316 13.238C63.7512 12.3354 63.5823 12.2732 60.7071 12.0507L58.1054 11.8495L58.0987 11.4277C58.0948 11.1957 58.0124 10.033 57.9155 8.84398C57.7205 6.45496 57.632 6.24222 56.7618 6.06818C56.3152 5.97877 56.2401 6.04494 52.0065 10.2689ZM52.7847 12.6345L49.8567 15.5655L50.0178 17.6507C50.1064 18.7977 50.1978 19.7551 50.2211 19.7783C50.2442 19.8016 51.2035 19.8914 52.3528 19.9779L54.4423 20.1353L57.3693 17.2112L60.2961 14.287L58.5513 14.1432C56.7635 13.9957 56.3019 13.8616 56.1188 13.4364C56.0614 13.3027 55.9464 12.4081 55.8636 11.4485L55.7128 9.70367L52.7847 12.6345ZM33.3593 14.2969C33.1713 14.3295 32.556 14.4225 31.9921 14.5035C24.1107 15.6352 17.8874 20.7276 15.433 28.0536C14.5803 30.5987 14.4326 31.6269 14.4356 34.9999C14.4381 37.6592 14.479 38.2585 14.7382 39.4214C16.5053 47.3533 21.9286 53.0383 29.5995 55.0001C31.9274 55.5954 32.8916 55.7123 35.41 55.7043C37.3445 55.6983 38.0602 55.6399 39.1477 55.3993C45.3707 54.0221 50.5616 50.1244 53.3694 44.7203C54.3221 42.8867 54.8364 41.4911 55.3177 39.4328C55.6659 37.944 55.6995 37.5654 55.7059 35.0683C55.7118 32.695 55.6712 32.1481 55.3976 30.9269C54.9526 28.9399 54.5045 27.6229 53.7316 26.0302C53.188 24.9102 52.982 24.6156 52.6437 24.4756C52.3081 24.3365 52.1451 24.3364 51.8111 24.4748C50.9491 24.8317 50.9349 25.3529 51.7422 26.9937C53.0494 29.6504 53.5183 31.7092 53.519 34.7948C53.5195 37.2261 53.2585 38.7757 52.4739 40.9993C50.5385 46.4844 46.1645 50.8834 40.9076 52.6313C38.7918 53.3347 37.7306 53.4993 35.2733 53.5051C32.726 53.511 31.3044 53.3055 29.2502 52.6342C26.4461 51.7179 24.1477 50.2856 21.9259 48.0698C19.1508 45.3024 17.491 42.1898 16.7259 38.3181C16.3942 36.6399 16.3952 33.0332 16.7277 31.4452C18.1299 24.7483 22.8662 19.5369 29.5273 17.3618C31.7211 16.6456 32.8166 16.4775 35.2733 16.4808C38.2926 16.4848 40.3759 16.9636 43.0061 18.2576C44.6469 19.0649 45.1681 19.0507 45.525 18.1887C45.6634 17.8547 45.6633 17.6917 45.5242 17.3561C45.3841 17.0176 45.0906 16.8125 43.9696 16.2699C41.2034 14.9308 38.8573 14.3602 35.8202 14.288C34.6547 14.2602 33.5473 14.2642 33.3593 14.2969ZM32.9491 24.2678C30.5837 24.746 28.9129 25.6289 27.2575 27.2753C26.1569 28.37 25.9279 28.6857 25.3207 29.9468C24.3783 31.9039 24.1481 32.8961 24.1481 34.9999C24.1481 37.1037 24.3783 38.0959 25.3207 40.053C25.9266 41.3113 26.1601 41.6339 27.263 42.7368C28.3659 43.8397 28.6885 44.0733 29.9468 44.6791C31.9237 45.6309 32.8932 45.8546 35.0683 45.8606C37.1812 45.8665 38.0601 45.6685 39.8534 44.7821C42.0957 43.6739 43.6752 42.0943 44.7814 39.8534C45.5341 38.3289 45.7951 37.3518 45.8953 35.6835C46.0328 33.3961 45.5729 31.2887 44.8521 30.9029C44.4403 30.6825 43.966 30.7357 43.6407 31.0389C43.2051 31.4445 43.1541 31.8533 43.426 32.7579C43.7443 33.8168 43.7407 35.971 43.4185 37.1874C42.6607 40.0484 40.711 42.1947 37.9815 43.1727C37.0895 43.4922 36.7768 43.5314 35.0683 43.5377C32.8135 43.5461 31.88 43.3057 30.2551 42.2981C27.7445 40.7416 26.3336 38.0624 26.3393 34.8632C26.3415 33.6603 26.4026 33.2505 26.7161 32.3353C27.6601 29.5793 29.5868 27.6536 32.3352 26.7187C33.7459 26.239 35.6244 26.1866 37.2398 26.5823C38.201 26.8177 38.3559 26.8245 38.6735 26.6464C39.4921 26.1869 39.3882 25.1523 38.4727 24.6473C37.4581 24.0876 34.7579 23.902 32.9491 24.2678Z"
												fill="#E5363D"></path>
										</svg>
									</span>
								</div>
								<div class="elementor-icon-box-content">
									<h3 class="elementor-icon-box-title">
										<span>Our Mission</span>
									</h3>
									<p class="elementor-icon-box-description">
										To provide excellent services to our clients by exceeding their expectations through safe, cost effective, efficient, timely and worldclass solutions in construction, engineering and real estate services.
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Our Vision -->
				<div class="elementor-element elementor-element-8bf8ae6 e-con-full e-flex elementor-invisible e-con e-child"
					data-id="8bf8ae6" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
					<div class="elementor-element elementor-element-0954640 elementor-view-default elementor-position-block-start elementor-mobile-position-block-start elementor-widget elementor-widget-icon-box"
						data-id="0954640" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="icon-box.default">
						<div class="elementor-widget-container">
							<div class="elementor-icon-box-wrapper">
								<div class="elementor-icon-box-icon">
									<span class="elementor-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
											viewbox="0 0 70 70" fill="none">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M34.2419 12.0935L33.9062 12.429V15.6242C33.9062 18.7875 33.9094 18.8225 34.2226 19.1168C34.6563 19.5241 35.4043 19.5151 35.7965 19.0977C36.084 18.7914 36.0938 18.678 36.0938 15.6051V12.429L35.7581 12.0935C35.5466 11.8818 35.2663 11.7578 35 11.7578C34.7337 11.7578 34.4534 11.8818 34.2419 12.0935ZM12.6465 16.298C12.0478 16.6413 11.8735 17.2222 12.1907 17.8166C12.4013 18.2113 15.1175 21.8385 15.5626 22.3193C16.2564 23.069 17.5 22.6394 17.5 21.6501C17.5 21.3341 17.0829 20.6816 15.715 18.8569C13.6682 16.1269 13.3734 15.8811 12.6465 16.298ZM56.37 16.3092C56.2051 16.4009 55.2668 17.5474 54.285 18.8569C52.3778 21.4009 52.2612 21.6712 52.7834 22.3351C53.185 22.8457 53.9571 22.8383 54.4374 22.3193C54.8825 21.8385 57.5987 18.2113 57.8093 17.8166C58.1327 17.2107 57.9467 16.6212 57.3284 16.2918C56.9559 16.0936 56.7513 16.0973 56.37 16.3092ZM31.4453 22.3703C29.9682 22.5455 27.9271 22.8931 26.7285 23.174C19.9321 24.7661 13.5972 28.4981 8.90777 33.6726C6.74734 36.0564 4.375 39.5023 4.375 40.2564C4.375 41.0673 7.17623 44.982 9.49238 47.4079C15.0469 53.2256 22.2004 56.8218 30.5566 57.9969C32.1669 58.2235 37.8331 58.2235 39.4434 57.9969C47.7656 56.8266 54.8532 53.2885 60.3631 47.5541C62.7856 45.0327 65.625 41.0948 65.625 40.2564C65.625 39.8033 64.595 38.0819 63.252 36.2903C58.4519 29.8877 51.7362 25.3723 44.031 23.367C40.9557 22.5667 39.5076 22.3933 35.5469 22.3517C33.5542 22.3307 31.7085 22.339 31.4453 22.3703ZM31.7188 24.6114C23.4686 25.4696 15.9768 29.1945 10.608 35.1077C9.38697 36.4525 7.53703 38.8829 7.0823 39.7398L6.78002 40.3096L7.53895 41.4487C12.1222 48.3282 19.4629 53.3608 27.5442 55.164C30.1217 55.7391 31.937 55.9259 34.9567 55.927C39.2191 55.9285 42.0202 55.495 45.7043 54.2638C52.4136 52.0215 58.3473 47.5187 62.3161 41.6575L63.2239 40.317L62.9196 39.7435C62.4803 38.9156 60.639 36.4852 59.4424 35.1537C54.5349 29.6927 47.4034 25.9153 39.9131 24.8093C38.2144 24.5584 33.3557 24.4411 31.7188 24.6114ZM32.8125 27.2694C29.8371 27.8198 27.3792 29.1665 25.3318 31.3678C23.6354 33.1918 22.3866 35.7833 22.0004 38.2812C21.7863 39.6659 21.9149 42.1783 22.2685 43.5154C23.2343 47.1685 25.7677 50.2981 29.0883 51.9401C31.34 53.0537 32.9127 53.3827 35.5116 53.2841C38.2473 53.1803 39.9905 52.6385 42.1786 51.2124C44.876 49.454 46.9139 46.6372 47.7258 43.5449C48.0851 42.1764 48.2152 39.6754 47.9996 38.2812C47.2034 33.132 43.3096 28.8185 38.29 27.525C36.6558 27.1038 34.301 26.994 32.8125 27.2694ZM32.5888 29.5904C29.4772 30.2978 26.6864 32.4927 25.2427 35.3682C25.0466 35.7585 24.7238 36.6198 24.5253 37.2822C24.2245 38.2854 24.1634 38.7602 24.1585 40.127C24.1546 41.2087 24.2297 42.0816 24.3787 42.6892C25.2874 46.3932 27.885 49.2892 31.3891 50.5049C37.536 52.6375 44.0597 49.055 45.6213 42.6892C45.7703 42.0816 45.8454 41.2087 45.8415 40.127C45.8329 37.7089 45.2223 35.8169 43.8056 33.8185C42.5734 32.0802 40.6135 30.6487 38.445 29.903C37.3904 29.5403 37.0393 29.4887 35.3418 29.4469C34.0387 29.4148 33.1599 29.4607 32.5888 29.5904ZM33.6476 33.7807C32.6285 33.9809 31.3432 34.6742 30.4994 35.4791C28.1788 37.6925 27.7689 41.1435 29.5072 43.8329C30.1906 44.8904 30.9962 45.5797 32.2656 46.1936C33.2772 46.6828 33.3141 46.6895 35.0071 46.6895H36.7232L37.8098 46.1539C40.1651 44.993 41.5655 42.7705 41.5609 40.2008C41.5535 36.0938 37.7173 32.9815 33.6476 33.7807ZM33.1543 36.346C31.5742 37.0947 30.7102 38.4569 30.702 40.2127C30.6975 41.1389 30.7501 41.3596 31.1737 42.1899C32.4696 44.7304 35.6424 45.3343 37.9034 43.4707C38.1899 43.2344 38.5983 42.6653 38.8544 42.1452C39.9476 39.9246 39.0737 37.4017 36.8457 36.346C36.1711 36.0263 35.8433 35.9573 35 35.9573C34.1567 35.9573 33.8289 36.0263 33.1543 36.346Z"
												fill="#E5363D"></path>
										</svg>
									</span>
								</div>
								<div class="elementor-icon-box-content">
									<h3 class="elementor-icon-box-title">
										<span>Our Vision</span>
									</h3>
									<p class="elementor-icon-box-description">
										To become a household name in construction, engineering and real estate services among private individuals, corporate and government organizations, locally and internationally in the next decade.
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Our Values (New Content) -->
				<div class="elementor-element elementor-element-eca1d9b e-con-full e-flex elementor-invisible e-con e-child"
					data-id="eca1d9b" data-element_type="container" data-e-type="container"
					data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:300}">
					<div class="elementor-element elementor-element-3f738f8 elementor-view-default elementor-position-block-start elementor-mobile-position-block-start elementor-widget elementor-widget-icon-box"
						data-id="3f738f8" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="icon-box.default">
						<div class="elementor-widget-container">
							<div class="elementor-icon-box-wrapper">
								<div class="elementor-icon-box-icon">
									<span class="elementor-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
											viewbox="0 0 70 70" fill="none">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M44.324 3.44532C44.208 3.56126 44.1599 4.35819 44.1599 6.1585C44.1599 8.45442 44.1848 8.73005 44.41 8.9339C44.7422 9.23454 45.0578 9.21936 45.3904 8.88673C45.6428 8.63434 45.6638 8.43104 45.6638 6.2512C45.6638 4.3344 45.6236 3.83169 45.451 3.58518C45.2165 3.25036 44.5959 3.17339 44.324 3.44532ZM31.5346 6.94929C31.4102 7.08682 31.3084 7.30011 31.3084 7.42329C31.3084 7.81458 33.6693 11.7802 33.9686 11.8917C34.365 12.0393 34.7746 11.8526 34.9149 11.4603C35.016 11.1772 34.8573 10.8138 33.9063 9.151C33.2856 8.06573 32.6836 7.07001 32.5688 6.93849C32.2976 6.62813 31.8208 6.63306 31.5346 6.94929ZM57.2584 6.87013C56.8964 7.15724 54.824 10.867 54.824 11.2279C54.824 11.4141 54.8978 11.6402 54.988 11.7305C55.2028 11.9453 55.8457 11.941 56.026 11.7236C56.4407 11.2241 58.3787 7.68702 58.3787 7.42999C58.3787 6.83157 57.7212 6.50304 57.2584 6.87013ZM37.0505 14.4557C36.5422 14.6508 28.9791 20.1716 28.9063 20.4008C28.8615 20.5423 28.9151 20.7933 29.0257 20.9589C29.3575 21.4558 44.4195 37.2896 44.6414 37.3748C44.7544 37.4182 44.9845 37.386 45.1528 37.3034C45.3211 37.2207 48.9041 33.5146 53.115 29.0674C59.7132 22.099 60.7712 20.9251 60.7712 20.5737C60.7712 20.198 60.5135 19.9809 57.49 17.8094C55.6853 16.5132 53.8453 15.2059 53.4013 14.9041L52.5937 14.3555L44.9247 14.3663C40.7067 14.3722 37.1633 14.4125 37.0505 14.4557ZM34.902 17.7499C33.457 18.7853 32.2318 19.6756 32.179 19.7284C32.1262 19.781 33.5016 19.8242 35.2353 19.8242H38.3875L39.633 17.9039C40.3181 16.8477 40.8787 15.9556 40.8787 15.9215C40.8787 15.8873 40.1251 15.8612 39.2039 15.8635L37.5291 15.8676L34.902 17.7499ZM42.6858 16.0303C42.6086 16.1243 42.0185 17.001 41.3744 17.9785L40.2034 19.7559L44.8003 19.7918C47.3285 19.8116 49.4209 19.8041 49.45 19.775C49.4791 19.746 48.9341 18.853 48.2392 17.7908L46.9754 15.8594H44.9008C43.4071 15.8594 42.7868 15.9072 42.6858 16.0303ZM49.0342 16.2499C49.1731 16.4646 49.7482 17.3567 50.312 18.2323L51.3371 19.8242H54.5384H57.7395L54.9607 17.8418L52.1819 15.8594H50.4817H48.7816L49.0342 16.2499ZM22.0673 16.4746C21.8656 16.7824 21.864 16.848 22.0513 17.1337C22.3142 17.535 26.0279 19.6875 26.457 19.6875C27.084 19.6875 27.4325 18.9758 27.0117 18.5549C26.6025 18.1457 22.9671 16.1328 22.6374 16.1328C22.419 16.1328 22.2085 16.259 22.0673 16.4746ZM65.2902 16.9637C62.6492 18.522 62.6169 18.5462 62.6169 18.971C62.6169 19.4431 62.838 19.6875 63.265 19.6875C63.6161 19.6875 67.3666 17.602 67.6414 17.2541C68.0296 16.7623 67.6679 16.1217 67.0164 16.147C66.8149 16.1548 66.0382 16.5223 65.2902 16.9637ZM32.1499 22.1423C33.2064 23.3194 42.7189 33.3316 42.7534 33.3028C42.7694 33.2895 41.7602 30.5897 40.5109 27.3034L38.2393 21.3281H34.8293H31.4191L32.1499 22.1423ZM39.9525 21.5674C40.0646 21.994 44.7378 34.245 44.8263 34.3438C44.8733 34.3964 45.9548 31.6894 47.2294 28.3281C48.5042 24.9669 49.6277 22.0168 49.7264 21.7725L49.9058 21.3281H44.8978C40.1955 21.3281 39.8936 21.3428 39.9525 21.5674ZM51.3318 21.7995C50.7767 23.128 47.0225 33.123 47.0633 33.1637C47.115 33.2154 58.1446 21.6252 58.2191 21.4409C58.2443 21.3789 56.7491 21.3281 54.8967 21.3281H51.5288L51.3318 21.7995ZM25.3901 38.8977C23.8713 39.2814 21.643 40.2489 17.9382 42.1339C15.8859 43.178 14.1486 43.9964 14.0777 43.9525C14.0067 43.9087 13.766 43.6074 13.5428 43.283C13.3197 42.9587 12.9142 42.5875 12.6419 42.4583C12.0633 42.1838 11.0983 42.1515 10.6419 42.3916C9.78226 42.8437 3.15646 47.5218 2.77077 47.9489C2.28296 48.4892 2.11029 49.1075 2.24577 49.8293C2.30443 50.142 4.1299 52.8991 7.60912 57.9299C10.5101 62.1246 13.0456 65.7334 13.2434 65.9494C13.9293 66.6982 15.0511 66.9315 15.8872 66.4992C16.2823 66.2948 22.6223 61.9404 23.3757 61.3561C23.8774 60.9669 24.1809 60.4379 24.2794 59.7815C24.3498 59.3122 24.2969 59.0543 24.0095 58.467C23.8129 58.0651 23.6521 57.6797 23.6521 57.6103C23.6521 57.3675 24.4944 56.4649 24.7211 56.4649C24.8469 56.4649 28.1493 57.4608 32.0598 58.6782C38.7818 60.7708 39.2293 60.8914 40.2634 60.888C43.4952 60.877 48.3033 58.3643 56.3279 52.4925C57.7942 51.4195 59.8552 49.9266 60.908 49.1747C63.8281 47.0891 64.4376 46.4256 64.817 44.9188C65.2562 43.1747 63.9908 41.2332 62.0017 40.5992C61.197 40.3427 59.6823 40.4014 58.6521 40.7289C58.0758 40.9121 55.7206 42.2078 51.9529 44.4142L46.1423 47.817L45.9955 47.1849C45.7796 46.2554 45.3542 45.5181 44.6697 44.8871C43.8526 44.1341 43.9006 44.1552 40.4365 43.0076C38.7623 42.4528 36.0696 41.4635 34.4529 40.8091C29.7461 38.9037 29.4219 38.805 27.6853 38.7476C26.6665 38.714 25.9261 38.7624 25.3901 38.8977ZM25.7502 40.4074C24.3609 40.7121 22.8124 41.3745 19.0037 43.293C17.011 44.2967 15.2945 45.1733 15.1892 45.2408C15.0401 45.3366 15.8362 46.5757 18.7997 50.861L22.6014 56.3582L23.0243 55.8663C23.4675 55.3506 24.2312 54.9609 24.7988 54.9609C24.9831 54.9609 28.415 55.9802 32.4252 57.226C39.3966 59.3917 39.7555 59.4881 40.6052 59.4233C41.8599 59.3275 43.4609 58.784 45.4157 57.7901C48.4491 56.2477 51.2106 54.4082 58.3992 49.1411C59.7263 48.1686 61.2146 47.0849 61.7065 46.7327C62.8892 45.8861 63.3528 45.1839 63.3619 44.2252C63.367 43.693 63.2921 43.3934 63.0829 43.111C62.1383 41.835 60.2048 41.5442 58.447 42.4136C58.1462 42.5623 55.2103 44.2534 51.9229 46.1714C46.3462 49.4252 45.9221 49.6992 45.5922 50.2621C44.7603 51.6818 43.4678 52.5029 41.9041 52.6054C41.001 52.6646 40.5968 52.5691 34.7425 50.9112C28.1588 49.0468 28.0032 48.9835 28.2327 48.2601C28.4463 47.5873 28.595 47.613 34.9615 49.4184C41.1243 51.166 41.8383 51.3073 42.6946 50.9485C44.3304 50.263 45.0668 48.287 44.2571 46.7559C43.7327 45.7641 43.2514 45.5026 39.9128 44.3948C38.2159 43.8318 35.3481 42.7792 33.5399 42.0558C28.8641 40.1853 27.7943 39.9589 25.7502 40.4074ZM11.3501 43.7143C11.0148 43.846 4.04636 48.6916 3.86206 48.9213C3.768 49.0384 3.69486 49.261 3.69923 49.4158C3.70374 49.5707 6.11943 53.1734 9.06763 57.4219C13.9929 64.5196 14.461 65.1492 14.8359 65.1796C15.1688 65.2066 15.9222 64.7414 18.932 62.6501C21.6047 60.7932 22.6553 59.988 22.7476 59.7255C22.8645 59.3931 22.4446 58.7407 17.6439 51.7959C14.7668 47.6339 12.2619 44.0902 12.0775 43.9209C11.7265 43.5988 11.6788 43.5853 11.3501 43.7143ZM15.2589 57.4718C13.8554 58.3214 13.657 60.3864 14.8801 61.4156C15.7801 62.1729 16.9419 62.2486 17.9031 61.6126C18.4914 61.2232 19.0037 60.3005 19.0037 59.6302C19.0037 59.1221 18.688 58.2999 18.3491 57.925C17.6266 57.1262 16.1803 56.914 15.2589 57.4718ZM15.812 58.9024C15.2686 59.5027 15.7295 60.5664 16.5327 60.5664C16.9899 60.5664 17.4998 60.0297 17.4998 59.5484C17.4998 58.7929 16.3197 58.3413 15.812 58.9024Z"
												fill="#E5363D"></path>
										</svg>
									</span>
								</div>
								<div class="elementor-icon-box-content">
									<h3 class="elementor-icon-box-title">
										<span>Our Values</span>
									</h3>
									<p class="elementor-icon-box-description">
										OUR CORPORATE VALUES: (SERVE)
									</p>
									<div class="values-points">
										<span class="value-point">• Stakeholders’ satisfaction</span>
										<span class="value-point">• Excellent service delivery</span>
										<span class="value-point">• Respect and integrity</span>
										<span class="value-point">• Value creation</span>
										<span class="value-point">• Exceeding expectations</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="elementor-element elementor-element-5222a0f e-con-full intro-video-box e-flex e-con e-parent"
			data-id="5222a0f" data-element_type="container" data-e-type="container">
			<div class="elementor-element elementor-element-4c21876 video-bg-img image-anime at-image-animation at-animation-image-none elementor-widget elementor-widget-image"
				data-id="4c21876" data-element_type="widget" data-e-type="widget"
				data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}" data-widget_type="image.default">
				<div class="elementor-widget-container">
					<img decoding="async" width="1920" height="768" src="<?php echo htmlspecialchars(tpv_asset_url($aboutVideoBackgroundImage)); ?>"
						class="attachment-full size-full wp-image-1650" alt="">
				</div>
			</div>
			<div class="elementor-element elementor-element-2a9e333 elementor-absolute video-content elementor-widget elementor-widget-elementskit-video"
				data-id="2a9e333" data-element_type="widget" data-e-type="widget"
				data-settings="{&quot;_position&quot;:&quot;absolute&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
				data-widget_type="elementskit-video.default">
				<div class="elementor-widget-container">
					<div class="ekit-wid-con">
						<div class="video-content" data-video-player="[]"
							data-video-setting="{&quot;videoVolume&quot;:&quot;horizontal&quot;,&quot;startVolume&quot;:0.8000000000000000444089209850062616169452667236328125,&quot;videoType&quot;:&quot;iframe&quot;,&quot;videoClass&quot;:&quot;mfp-fade&quot;,&quot;popupIcon&quot;:{&quot;value&quot;:&quot;icon icon-cancel&quot;,&quot;library&quot;:&quot;ekiticons&quot;},&quot;videoStyle&quot;:&quot;popup&quot;,&quot;videoTypeName&quot;:&quot;youtube&quot;,&quot;autoplay&quot;:false,&quot;muted&quot;:false,&quot;loop&quot;:true}">

							<a class="ekit_icon_button glow-ripple ekit-video-popup ekit-video-popup-btn"
								href="https://www.youtube.com/embed/Y-x0efG1seA?playlist=Y-x0efG1seA&mute=0&autoplay=0&loop=1&controls=0&start=0&end="
								aria-label="video-popup">
								<svg aria-hidden="true" class="e-font-icon-svg e-fas-play" viewbox="0 0 448 512"
									xmlns="http://www.w3.org/2000/svg">
									<path
										d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z">
									</path>
								</svg>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-6d424a6 e-flex e-con-boxed e-con e-parent" data-id="6d424a6"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-b15dc1b e-con-full e-flex e-con e-child"
					data-id="b15dc1b" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-b3cda68 e-con-full e-flex e-con e-child"
						data-id="b3cda68" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-096767f at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
							data-id="096767f" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h3 class="elementor-heading-title elementor-size-default">what we do</h3>
							</div>
						</div>
						<div class="elementor-element elementor-element-a15bde3 elementor-widget-tablet__width-inherit elementor-widget-mobile__width-inherit at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="a15bde3" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h2 class="elementor-heading-title elementor-size-default">Building Nigeria's future on a foundation of excellence</h2>
							</div>
						</div>
						<div class="elementor-element elementor-element-8235bc5 elementor-invisible elementor-widget elementor-widget-text-editor"
							data-id="8235bc5" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
								<p>We deliver comprehensive construction solutions across Nigeria — from residential homes in Lagos to commercial complexes in Abuja and industrial facilities in Port Harcourt. Every project reflects our commitment to quality, safety, and client satisfaction.</p>
							</div>
						</div>
					</div>
				</div>

				<div class="elementor-element elementor-element-a98a29f e-con-full e-flex e-con e-child"
					data-id="a98a29f" data-element_type="container" data-e-type="container">

					<!-- Building Control Approval Rate -->
					<div class="elementor-element elementor-element-4b27b70 e-con-full e-flex e-con e-child"
						data-id="4b27b70" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
						<div class="elementor-element elementor-element-52aa336 company-counter-item elementor-widget elementor-widget-counter"
							data-id="52aa336" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="counter.default">
							<div class="elementor-widget-container">
								<div class="elementor-counter">
									<div class="elementor-counter-title">Regulatory Approval Rate</div>
									<div class="elementor-counter-number-wrapper">
										<span class="elementor-counter-number-prefix"></span>
										<span class="elementor-counter-number" data-duration="2000" data-to-value="100"
											data-from-value="0" data-delimiter=",">0</span>
										<span class="elementor-counter-number-suffix">%</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Active Projects -->
					<div class="elementor-element elementor-element-f73233d e-con-full e-flex e-con e-child"
						data-id="f73233d" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
						<div class="elementor-element elementor-element-f57960a company-counter-item elementor-widget elementor-widget-counter"
							data-id="f57960a" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="counter.default">
							<div class="elementor-widget-container">
								<div class="elementor-counter">
									<div class="elementor-counter-title">Active Projects Nationwide</div>
									<div class="elementor-counter-number-wrapper">
										<span class="elementor-counter-number-prefix"></span>
										<span class="elementor-counter-number" data-duration="2000" data-to-value="45"
											data-from-value="0" data-delimiter=",">0</span>
										<span class="elementor-counter-number-suffix">+</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Completed Projects -->
					<div class="elementor-element elementor-element-76052ee e-con-full e-flex e-con e-child"
						data-id="76052ee" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
						<div class="elementor-element elementor-element-f35d5ee company-counter-item elementor-widget elementor-widget-counter"
							data-id="f35d5ee" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="counter.default">
							<div class="elementor-widget-container">
								<div class="elementor-counter">
									<div class="elementor-counter-title">Projects Completed Since 2008</div>
									<div class="elementor-counter-number-wrapper">
										<span class="elementor-counter-number-prefix"></span>
										<span class="elementor-counter-number" data-duration="2000" data-to-value="350"
											data-from-value="0" data-delimiter=",">0</span>
										<span class="elementor-counter-number-suffix">+</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Skilled Professionals -->
					<div class="elementor-element elementor-element-e67a12c e-con-full e-flex e-con e-child"
						data-id="e67a12c" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
						<div class="elementor-element elementor-element-525dd68 company-counter-item elementor-widget elementor-widget-counter"
							data-id="525dd68" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="counter.default">
							<div class="elementor-widget-container">
								<div class="elementor-counter">
									<div class="elementor-counter-title">Skilled Professionals & Workers</div>
									<div class="elementor-counter-number-wrapper">
										<span class="elementor-counter-number-prefix"></span>
										<span class="elementor-counter-number" data-duration="2000" data-to-value="250"
											data-from-value="0" data-delimiter=",">0</span>
										<span class="elementor-counter-number-suffix">+</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-de3c070 e-flex e-con-boxed e-con e-parent" data-id="de3c070"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-1e2644d e-con-full e-flex e-con e-child"
					data-id="1e2644d" data-element_type="container" data-e-type="container"
					data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">

					<!-- Main Heading -->
					<div class="elementor-element elementor-element-4f4ad91 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
						data-id="4f4ad91" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="heading.default">
						<div class="elementor-widget-container">
							<h2 class="elementor-heading-title elementor-size-default">Ready to build your dream project in Nigeria?</h2>
						</div>
					</div>

					<!-- Description Text -->
					<div class="elementor-element elementor-element-1fd35e9 elementor-invisible elementor-widget elementor-widget-text-editor"
						data-id="1fd35e9" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="text-editor.default">
						<div class="elementor-widget-container">
							<p><?php echo nl2br(htmlspecialchars($aboutBottomCtaBody)); ?></p>
						</div>
					</div>

					<!-- CTA Button -->
					<div class="elementor-element elementor-element-db61a71 btn-transparent elementor-invisible elementor-widget elementor-widget-button"
						data-id="db61a71" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="button.default">
						<div class="elementor-widget-container">
							<div class="elementor-button-wrapper">
								<a class="elementor-button elementor-button-link elementor-size-sm"
									href="../contact-us/">
									<span class="elementor-button-content-wrapper">
										<span class="elementor-button-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
												viewbox="0 0 24 24" fill="none">
												<path
													d="M12.75 15L15.75 12M15.75 12L12.75 9M15.75 12H8.25M21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3C14.3869 3 16.6761 3.94821 18.364 5.63604C20.0518 7.32387 21 9.61305 21 12Z"
													stroke="white" stroke-width="1.5" stroke-linecap="round"
													stroke-linejoin="round"></path>
											</svg>
										</span>
										<span class="elementor-button-text">Get Your Free Quote</span>
									</span>
								</a>
							</div>
						</div>
					</div>
				</div>

				<!-- Image Section -->
				<div class="elementor-element elementor-element-70f6b56 e-con-full e-flex e-con e-child"
					data-id="70f6b56" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-1ce5dd6 at-image-animation at-animation-image-none elementor-widget elementor-widget-image"
						data-id="1ce5dd6" data-element_type="widget" data-e-type="widget"
						data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
						data-widget_type="image.default">
						<div class="elementor-widget-container">
							<img loading="lazy" decoding="async" width="483" height="455"
								src="<?php echo htmlspecialchars(tpv_asset_url($aboutCtaImage)); ?>"
								class="attachment-large size-large wp-image-1251"
								alt="TPV Construction and Services LTD - Building Dreams Across Nigeria">
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="elementor-element elementor-element-b9d42ed e-flex e-con-boxed e-con e-parent" data-id="b9d42ed"
			data-element_type="container" data-e-type="container">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-9b8225d e-con-full e-flex e-con e-child"
					data-id="9b8225d" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-bdb0993 e-con-full e-flex e-con e-child"
						data-id="bdb0993" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-39662c3 at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
							data-id="39662c3" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h3 class="elementor-heading-title elementor-size-default">Team</h3>
							</div>
						</div>
						<div class="elementor-element elementor-element-11776b2 elementor-widget-tablet__width-inherit elementor-widget-mobile__width-inherit at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="11776b2" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h2 class="elementor-heading-title elementor-size-default">Our team</h2>
							</div>
						</div>
						<div class="elementor-element elementor-element-e3a4679 elementor-invisible elementor-widget elementor-widget-text-editor"
							data-id="e3a4679" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
								<p>We specialize in a wide range of construction services, including residential,
									commercial, and industrial projects.</p>
							</div>
						</div>
					</div>
				</div>
				<div class="elementor-element elementor-element-26e16fe e-con-full e-flex e-con e-child"
					data-id="26e16fe" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-e778810 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="e778810" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;}">
						<div class="elementor-element elementor-element-c4c50ca team-member-item elementor-widget elementor-widget-elementskit-team"
							data-id="c4c50ca" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="elementskit-team.default">
							<div class="elementor-widget-container">
								<div class="ekit-wid-con">
									<div class="profile-card elementor-animation- text-left ekit-team-style-default">
										<a aria-label="profile" href="javascript:void(0)"
											data-mfp-src="#ekit_team_modal_c4c50ca_723" class="ekit-team-popup">

											<div class="profile-header ekit-team-img ekit-img-overlay ekit-team-img-block"
												data-toggle="modal" data-target="ekit_team_modal_#c4c50ca_723">
												<img loading="lazy" decoding="async" width="412" height="379"
													src="../wp-content/uploads/2024/06/team-1.jpg"
													class="attachment-full size-full wp-image-413" alt="">
											</div><!-- .profile-header END -->
										</a>


										<div class="profile-body">

											<h2 class="profile-title">
												<a aria-label="profile" href="javascript:void(0)"
													data-mfp-src="#ekit_team_modal_c4c50ca_723" class="ekit-team-popup">
													Benjamin Miller </a>
											</h2>
											<p class="profile-designation">Project Manager</p>
										</div><!-- .profile-body END -->

										<div class="profile-footer">
											<ul class="ekit-team-social-list">
												<li class="elementor-repeater-item-e8d6be4">
													<a href="#" aria-label="Facebook">
														<i aria-hidden="true" class="icon icon-facebook"></i> </a>
												</li>
												<li class="elementor-repeater-item-45b3d13">
													<a href="#" aria-label="twitter">
														<i aria-hidden="true" class="icon icon-x-twitter"></i> </a>
												</li>
												<li class="elementor-repeater-item-8edd9a4">
													<a href="https://facebook.com" aria-label="instagram">
														<i aria-hidden="true" class="icon icon-instagram-1"></i> </a>
												</li>
											</ul>
										</div>
									</div>


									<div class="zoom-anim-dialog mfp-hide elementskit-team-popup team-popup-id-c4c50ca"
										id="ekit_team_modal_c4c50ca_723" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<button type="button" class="ekit-team-modal-close">
													<svg aria-hidden="true" class="e-font-icon-svg e-fas-times"
														viewbox="0 0 352 512" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z">
														</path>
													</svg> </button>

												<div class="modal-body">
													<div class="ekit-team-modal-img">
														<img loading="lazy" decoding="async" width="412" height="379"
															src="../wp-content/uploads/2024/06/team-1.jpg"
															class="attachment-full size-full wp-image-413" alt="">
													</div>

													<div class="ekit-team-modal-info has-img">
														<h2 class="ekit-team-modal-title">Benjamin Miller</h2>
														<p class="ekit-team-modal-position">Project Manager</p>

														<div class="ekit-team-modal-content">
															A small river named Duden flows by their place and supplies
															it with the necessary </div>

														<ul class="ekit-team-modal-list">
															<li><strong>Phone:</strong><a
																	href="tel:+1 (859) 254-6589">+1 (859) 254-6589</a>
															</li>

															<li><strong>Email:</strong><a
																	href="mailto:info@example.com">info@example.com</a>
															</li>
														</ul>

														<ul class="ekit-team-social-list">
															<li class="elementor-repeater-item-e8d6be4">
																<a href="#" aria-label="Facebook">
																	<i aria-hidden="true"
																		class="icon icon-facebook"></i> </a>
															</li>
															<li class="elementor-repeater-item-45b3d13">
																<a href="#" aria-label="twitter">
																	<i aria-hidden="true"
																		class="icon icon-x-twitter"></i> </a>
															</li>
															<li class="elementor-repeater-item-8edd9a4">
																<a href="https://facebook.com" aria-label="instagram">
																	<i aria-hidden="true"
																		class="icon icon-instagram-1"></i> </a>
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
					<div class="elementor-element elementor-element-181bc9d e-con-full e-flex elementor-invisible e-con e-child"
						data-id="181bc9d" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
						<div class="elementor-element elementor-element-e516d59 team-member-item elementor-widget elementor-widget-elementskit-team"
							data-id="e516d59" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="elementskit-team.default">
							<div class="elementor-widget-container">
								<div class="ekit-wid-con">
									<div class="profile-card elementor-animation- text-left ekit-team-style-default">
										<a aria-label="profile" href="javascript:void(0)"
											data-mfp-src="#ekit_team_modal_e516d59_723" class="ekit-team-popup">

											<div class="profile-header ekit-team-img ekit-img-overlay ekit-team-img-block"
												data-toggle="modal" data-target="ekit_team_modal_#e516d59_723">
												<img loading="lazy" decoding="async" width="412" height="379"
													src="../wp-content/uploads/2024/06/team-2.jpg"
													class="attachment-full size-full wp-image-414" alt="">
											</div><!-- .profile-header END -->
										</a>


										<div class="profile-body">

											<h2 class="profile-title">
												<a aria-label="profile" href="javascript:void(0)"
													data-mfp-src="#ekit_team_modal_e516d59_723" class="ekit-team-popup">
													Jane Smith </a>
											</h2>
											<p class="profile-designation">Lead Architect</p>
										</div><!-- .profile-body END -->

										<div class="profile-footer">
											<ul class="ekit-team-social-list">
												<li class="elementor-repeater-item-e8d6be4">
													<a href="#" aria-label="Facebook">
														<i aria-hidden="true" class="icon icon-facebook"></i> </a>
												</li>
												<li class="elementor-repeater-item-45b3d13">
													<a href="#" aria-label="twitter">
														<i aria-hidden="true" class="icon icon-x-twitter"></i> </a>
												</li>
												<li class="elementor-repeater-item-8edd9a4">
													<a href="https://facebook.com" aria-label="instagram">
														<i aria-hidden="true" class="icon icon-instagram-1"></i> </a>
												</li>
											</ul>
										</div>
									</div>


									<div class="zoom-anim-dialog mfp-hide elementskit-team-popup team-popup-id-e516d59"
										id="ekit_team_modal_e516d59_723" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<button type="button" class="ekit-team-modal-close">
													<svg aria-hidden="true" class="e-font-icon-svg e-fas-times"
														viewbox="0 0 352 512" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z">
														</path>
													</svg> </button>

												<div class="modal-body">
													<div class="ekit-team-modal-img">
														<img loading="lazy" decoding="async" width="412" height="379"
															src="../wp-content/uploads/2024/06/team-2.jpg"
															class="attachment-full size-full wp-image-414" alt="">
													</div>

													<div class="ekit-team-modal-info has-img">
														<h2 class="ekit-team-modal-title">Jane Smith</h2>
														<p class="ekit-team-modal-position">Lead Architect</p>

														<div class="ekit-team-modal-content">
															A small river named Duden flows by their place and supplies
															it with the necessary </div>

														<ul class="ekit-team-modal-list">
															<li><strong>Phone:</strong><a
																	href="tel:+1 (859) 254-6589">+1 (859) 254-6589</a>
															</li>

															<li><strong>Email:</strong><a
																	href="mailto:info@example.com">info@example.com</a>
															</li>
														</ul>

														<ul class="ekit-team-social-list">
															<li class="elementor-repeater-item-e8d6be4">
																<a href="#" aria-label="Facebook">
																	<i aria-hidden="true"
																		class="icon icon-facebook"></i> </a>
															</li>
															<li class="elementor-repeater-item-45b3d13">
																<a href="#" aria-label="twitter">
																	<i aria-hidden="true"
																		class="icon icon-x-twitter"></i> </a>
															</li>
															<li class="elementor-repeater-item-8edd9a4">
																<a href="https://facebook.com" aria-label="instagram">
																	<i aria-hidden="true"
																		class="icon icon-instagram-1"></i> </a>
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
					<div class="elementor-element elementor-element-976af91 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="976af91" data-element_type="container" data-e-type="container"
						data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-element elementor-element-f905512 team-member-item elementor-widget elementor-widget-elementskit-team"
							data-id="f905512" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="elementskit-team.default">
							<div class="elementor-widget-container">
								<div class="ekit-wid-con">
									<div class="profile-card elementor-animation- text-left ekit-team-style-default">
										<a aria-label="profile" href="javascript:void(0)"
											data-mfp-src="#ekit_team_modal_f905512_723" class="ekit-team-popup">

											<div class="profile-header ekit-team-img ekit-img-overlay ekit-team-img-block"
												data-toggle="modal" data-target="ekit_team_modal_#f905512_723">
												<img loading="lazy" decoding="async" width="412" height="379"
													src="../wp-content/uploads/2024/06/team-3.jpg"
													class="attachment-full size-full wp-image-415" alt="">
											</div><!-- .profile-header END -->
										</a>


										<div class="profile-body">

											<h2 class="profile-title">
												<a aria-label="profile" href="javascript:void(0)"
													data-mfp-src="#ekit_team_modal_f905512_723" class="ekit-team-popup">
													Mike Johnson </a>
											</h2>
											<p class="profile-designation">Chief Engineer</p>
										</div><!-- .profile-body END -->

										<div class="profile-footer">
											<ul class="ekit-team-social-list">
												<li class="elementor-repeater-item-e8d6be4">
													<a href="#" aria-label="Facebook">
														<i aria-hidden="true" class="icon icon-facebook"></i> </a>
												</li>
												<li class="elementor-repeater-item-45b3d13">
													<a href="#" aria-label="twitter">
														<i aria-hidden="true" class="icon icon-x-twitter"></i> </a>
												</li>
												<li class="elementor-repeater-item-8edd9a4">
													<a href="https://facebook.com" aria-label="instagram">
														<i aria-hidden="true" class="icon icon-instagram-1"></i> </a>
												</li>
											</ul>
										</div>
									</div>


									<div class="zoom-anim-dialog mfp-hide elementskit-team-popup team-popup-id-f905512"
										id="ekit_team_modal_f905512_723" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<button type="button" class="ekit-team-modal-close">
													<svg aria-hidden="true" class="e-font-icon-svg e-fas-times"
														viewbox="0 0 352 512" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z">
														</path>
													</svg> </button>

												<div class="modal-body">
													<div class="ekit-team-modal-img">
														<img loading="lazy" decoding="async" width="412" height="379"
															src="../wp-content/uploads/2024/06/team-3.jpg"
															class="attachment-full size-full wp-image-415" alt="">
													</div>

													<div class="ekit-team-modal-info has-img">
														<h2 class="ekit-team-modal-title">Mike Johnson</h2>
														<p class="ekit-team-modal-position">Chief Engineer</p>

														<div class="ekit-team-modal-content">
															A small river named Duden flows by their place and supplies
															it with the necessary </div>

														<ul class="ekit-team-modal-list">
															<li><strong>Phone:</strong><a
																	href="tel:+1 (859) 254-6589">+1 (859) 254-6589</a>
															</li>

															<li><strong>Email:</strong><a
																	href="mailto:info@example.com">info@example.com</a>
															</li>
														</ul>

														<ul class="ekit-team-social-list">
															<li class="elementor-repeater-item-e8d6be4">
																<a href="#" aria-label="Facebook">
																	<i aria-hidden="true"
																		class="icon icon-facebook"></i> </a>
															</li>
															<li class="elementor-repeater-item-45b3d13">
																<a href="#" aria-label="twitter">
																	<i aria-hidden="true"
																		class="icon icon-x-twitter"></i> </a>
															</li>
															<li class="elementor-repeater-item-8edd9a4">
																<a href="https://facebook.com" aria-label="instagram">
																	<i aria-hidden="true"
																		class="icon icon-instagram-1"></i> </a>
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
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-89ffd26 e-flex e-con-boxed e-con e-parent" data-id="89ffd26"
			data-element_type="container" data-e-type="container"
			data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
			<div class="e-con-inner">
				<div class="elementor-element elementor-element-02273ee e-con-full e-flex e-con e-child"
					data-id="02273ee" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-947a743 e-con-full e-flex e-con e-child"
						data-id="947a743" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-9f030a1 e-con-full contact-sidebar e-flex elementor-invisible e-con e-child"
							data-id="9f030a1" data-element_type="container" data-e-type="container"
							data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;}">
							<div class="elementor-element elementor-element-edd061c contact-info ekit-equal-height-disable elementor-widget elementor-widget-elementskit-icon-box"
								data-id="edd061c" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="elementskit-icon-box.default">
								<div class="elementor-widget-container">
									<div class="ekit-wid-con"> <!-- link opening -->
										<!-- end link opening -->

										<div
											class="elementskit-infobox text-center text-center icon-top-align elementor-animation-   ">
											<div class="elementskit-box-header elementor-animation-">
												<div class="elementskit-info-box-icon  ">
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
													Call Support Center 24/7 </h3>
												<p><a href="tel:+1 809 120 6705">+1 809 120 6705 </a></p>
											</div>


										</div>
									</div>
								</div>
							</div>
							<div class="elementor-element elementor-element-535eec4 contact-info ekit-equal-height-disable elementor-widget elementor-widget-elementskit-icon-box"
								data-id="535eec4" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="elementskit-icon-box.default">
								<div class="elementor-widget-container">
									<div class="ekit-wid-con"> <!-- link opening -->
										<!-- end link opening -->

										<div
											class="elementskit-infobox text-center text-center icon-top-align elementor-animation-   ">
											<div class="elementskit-box-header elementor-animation-">
												<div class="elementskit-info-box-icon  ">
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
											</div>


										</div>
									</div>
								</div>
							</div>
							<div class="elementor-element elementor-element-f0485c9 at-image-animation at-animation-image-none elementor-widget elementor-widget-image"
								data-id="f0485c9" data-element_type="widget" data-e-type="widget"
								data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
								data-widget_type="image.default">
								<div class="elementor-widget-container">
									<img loading="lazy" decoding="async" width="301" height="420"
										src="../wp-content/uploads/2024/06/contact-info-img.png"
										class="attachment-full size-full wp-image-1857" alt="">
								</div>
							</div>
						</div>
					</div>
					<div class="elementor-element elementor-element-a785400 e-con-full e-flex elementor-invisible e-con e-child"
						data-id="a785400" data-element_type="container" data-e-type="container"
						data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
						<div class="elementor-element elementor-element-10f7e68 at-heading-animation at-animation-heading-none elementor-invisible elementor-widget elementor-widget-heading"
							data-id="10f7e68" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h3 class="elementor-heading-title elementor-size-default">Contact us</h3>
							</div>
						</div>
						<div class="elementor-element elementor-element-5a16bfd at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading"
							data-id="5a16bfd" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="heading.default">
							<div class="elementor-widget-container">
								<h2 class="elementor-heading-title elementor-size-default">Get in touch with us</h2>
							</div>
						</div>
						<div class="elementor-element elementor-element-6462b45 contact-form elementor-widget elementor-widget-elementskit-contact-form7"
							data-id="6462b45" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="elementskit-contact-form7.default">
							<div class="elementor-widget-container">
								<div class="ekit-wid-con">
									<div class="ekit-form">
										<div class="wpcf7 no-js" id="wpcf7-f6700-p723-o1" lang="en-US" dir="ltr"
											data-wpcf7-id="6700">
											<div class="screen-reader-response">
												<p role="status" aria-live="polite" aria-atomic="true"></p>
												<ul></ul>
											</div>
											<form action="/TPV Construction Services/about-us/#wpcf7-f6700-p723-o1" method="post"
												class="wpcf7-form init" aria-label="Contact form"
												novalidate="novalidate" data-status="init">
												<fieldset class="hidden-fields-container"><input type="hidden"
														name="_wpcf7" value="6700"><input type="hidden"
														name="_wpcf7_version" value="6.1.5"><input type="hidden"
														name="_wpcf7_locale" value="en_US"><input type="hidden"
														name="_wpcf7_unit_tag" value="wpcf7-f6700-p723-o1"><input
														type="hidden" name="_wpcf7_container_post" value="723"><input
														type="hidden" name="_wpcf7_posted_data_hash" value="">
												</fieldset>
												<div class="row">
													<div class="form-group col-md-6 mb-4">
														<p><span class="wpcf7-form-control-wrap"
																data-name="your-name"><input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control"
																	id="your-name" aria-required="true"
																	aria-invalid="false" placeholder="Enter Your name"
																	value="" type="text" name="your-name"></span>
														</p>
													</div>
													<div class="form-group col-md-6 mb-4">
														<p><span class="wpcf7-form-control-wrap"
																data-name="email"><input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-email wpcf7-validates-as-required wpcf7-text wpcf7-validates-as-email form-control"
																	id="email" aria-required="true" aria-invalid="false"
																	placeholder="Enter Your email" value="" type="email"
																	name="email"></span>
														</p>
													</div>
													<div class="form-group col-md-6 mb-4">
														<p><span class="wpcf7-form-control-wrap"
																data-name="phone"><input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control"
																	id="phone" aria-required="true" aria-invalid="false"
																	placeholder="Phone number" value="" type="text"
																	name="phone"></span>
														</p>
													</div>
													<div class="form-group col-md-6 mb-4">
														<p><span class="wpcf7-form-control-wrap"
																data-name="Subject"><input size="40" maxlength="400"
																	class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control"
																	id="subject" aria-required="true"
																	aria-invalid="false" placeholder="Subject" value=""
																	type="text" name="Subject"></span>
														</p>
													</div>
													<div class="form-group col-md-12 mb-4">
														<p><span class="wpcf7-form-control-wrap"
																data-name="message"><textarea cols="40" rows="10"
																	maxlength="2000"
																	class="wpcf7-form-control wpcf7-textarea form-control"
																	id="msg" aria-invalid="false" placeholder="Message"
																	name="message"></textarea></span>
														</p>
													</div>
													<div class="col-md-12 form-btn">
														<p><input
																class="wpcf7-form-control wpcf7-submit has-spinner btn-default"
																id="msgSubmit" type="submit" value="Submit">
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
	</div>
	<?php
	include_once('../includes/footer.php');
	?>

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
	<link rel='stylesheet' id='elementor-post-225-css'
		href='../wp-content/uploads/elementor/css/post-225.css?ver=1770715449' media='all'>
	<link rel='stylesheet' id='elementor-post-1688-css'
		href='../wp-content/uploads/elementor/css/post-1688.css?ver=1770715449' media='all'>
	<script src="../wp-includes/js/dist/hooks.min.js?ver=dd5603f07f9220ed27f1" id="wp-hooks-js"></script>
	<script src="../wp-includes/js/dist/i18n.min.js?ver=c26c3dc7bed366793375" id="wp-i18n-js"></script>
	<script id="wp-i18n-js-after">
		wp.i18n.setLocaleData({
			'text direction\u0004ltr': ['ltr']
		});
		//# sourceURL=wp-i18n-js-after
	</script>
	<script src="../wp-content/plugins/contact-form-7/includes/swv/js/index.js?ver=6.1.5" id="swv-js"></script>
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
	<script src="../wp-content/plugins/contact-form-7/includes/js/index.js?ver=6.1.5" id="contact-form-7-js"></script>
	<script src="../wp-content/themes/tpv/assets/js/SmoothScroll.js?ver=1.0.8" id="SmoothScroll-js"></script>
	<script src="../wp-content/themes/tpv/assets/js/gsap.min.js?ver=1.0.8" id="gsap-js"></script>
	<script src="../wp-content/themes/tpv/assets/js/magiccursor.js?ver=1.0.8" id="magiccursor-js"></script>
	<script src="../wp-content/themes/tpv/assets/js/SplitText.js?ver=1.0.8" id="SplitText-js"></script>
	<script src="../wp-content/themes/tpv/assets/js/ScrollTrigger.min.js?ver=1.0.8" id="ScrollTrigger-js"></script>
	<script src="../wp-content/themes/tpv/assets/js/function.js?ver=1.0.8" id="theme-js-js"></script>
	<script src="../wp-content/plugins/elementskit-lite/libs/framework/assets/js/frontend-script.js?ver=3.7.9"
		id="elementskit-framework-js-frontend-js"></script>

	<script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/widget-scripts.js?ver=3.7.9"
		id="ekit-widget-scripts-js"></script>
	<script src="../wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.35.3"
		id="elementor-webpack-runtime-js"></script>
	<script src="../wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.35.3"
		id="elementor-frontend-modules-js"></script>
	<script src="../wp-includes/js/jquery/ui/core.min.js?ver=1.13.3" id="jquery-ui-core-js"></script>
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
				"id": 723,
				"title": "About%20Us%20%E2%80%93%20TPV Construction and Services LTD",
				"excerpt": "",
				"featuredImage": false
			}
		};
		//# sourceURL=elementor-frontend-js-before
	</script>
	<script src="../wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.35.3"
		id="elementor-frontend-js"></script>
	<script src="../wp-content/plugins/elementor/assets/lib/jquery-numerator/jquery-numerator.min.js?ver=0.2.1"
		id="jquery-numerator-js"></script>
	<script id="mediaelement-core-js-before">
		var mejsL10n = {
			"language": "en",
			"strings": {
				"mejs.download-file": "Download File",
				"mejs.install-flash": "You are using a browser that does not have Flash player enabled or installed. Please turn on your Flash player plugin or download the latest version from https://get.adobe.com/flashplayer/",
				"mejs.fullscreen": "Fullscreen",
				"mejs.play": "Play",
				"mejs.pause": "Pause",
				"mejs.time-slider": "Time Slider",
				"mejs.time-help-text": "Use Left/Right Arrow keys to advance one second, Up/Down arrows to advance ten seconds.",
				"mejs.live-broadcast": "Live Broadcast",
				"mejs.volume-help-text": "Use Up/Down Arrow keys to increase or decrease volume.",
				"mejs.unmute": "Unmute",
				"mejs.mute": "Mute",
				"mejs.volume-slider": "Volume Slider",
				"mejs.video-player": "Video Player",
				"mejs.audio-player": "Audio Player",
				"mejs.captions-subtitles": "Captions/Subtitles",
				"mejs.captions-chapters": "Chapters",
				"mejs.none": "None",
				"mejs.afrikaans": "Afrikaans",
				"mejs.albanian": "Albanian",
				"mejs.arabic": "Arabic",
				"mejs.belarusian": "Belarusian",
				"mejs.bulgarian": "Bulgarian",
				"mejs.catalan": "Catalan",
				"mejs.chinese": "Chinese",
				"mejs.chinese-simplified": "Chinese (Simplified)",
				"mejs.chinese-traditional": "Chinese (Traditional)",
				"mejs.croatian": "Croatian",
				"mejs.czech": "Czech",
				"mejs.danish": "Danish",
				"mejs.dutch": "Dutch",
				"mejs.english": "English",
				"mejs.estonian": "Estonian",
				"mejs.filipino": "Filipino",
				"mejs.finnish": "Finnish",
				"mejs.french": "French",
				"mejs.galician": "Galician",
				"mejs.german": "German",
				"mejs.greek": "Greek",
				"mejs.haitian-creole": "Haitian Creole",
				"mejs.hebrew": "Hebrew",
				"mejs.hindi": "Hindi",
				"mejs.hungarian": "Hungarian",
				"mejs.icelandic": "Icelandic",
				"mejs.indonesian": "Indonesian",
				"mejs.irish": "Irish",
				"mejs.italian": "Italian",
				"mejs.japanese": "Japanese",
				"mejs.korean": "Korean",
				"mejs.latvian": "Latvian",
				"mejs.lithuanian": "Lithuanian",
				"mejs.macedonian": "Macedonian",
				"mejs.malay": "Malay",
				"mejs.maltese": "Maltese",
				"mejs.norwegian": "Norwegian",
				"mejs.persian": "Persian",
				"mejs.polish": "Polish",
				"mejs.portuguese": "Portuguese",
				"mejs.romanian": "Romanian",
				"mejs.russian": "Russian",
				"mejs.serbian": "Serbian",
				"mejs.slovak": "Slovak",
				"mejs.slovenian": "Slovenian",
				"mejs.spanish": "Spanish",
				"mejs.swahili": "Swahili",
				"mejs.swedish": "Swedish",
				"mejs.tagalog": "Tagalog",
				"mejs.thai": "Thai",
				"mejs.turkish": "Turkish",
				"mejs.ukrainian": "Ukrainian",
				"mejs.vietnamese": "Vietnamese",
				"mejs.welsh": "Welsh",
				"mejs.yiddish": "Yiddish"
			}
		};
		//# sourceURL=mediaelement-core-js-before
	</script>
	<script src="../wp-includes/js/mediaelement/mediaelement-and-player.min.js?ver=4.2.17"
		id="mediaelement-core-js"></script>
	<script src="../wp-includes/js/mediaelement/mediaelement-migrate.min.js?ver=6.9.1"
		id="mediaelement-migrate-js"></script>
	<script id="mediaelement-js-extra">
		var _wpmejsSettings = {
			"pluginPath": "/TPV Construction Services/wp-includes/js/mediaelement/",
			"classPrefix": "mejs-",
			"stretching": "responsive",
			"audioShortcodeLibrary": "mediaelement",
			"videoShortcodeLibrary": "mediaelement"
		};
		//# sourceURL=mediaelement-js-extra
	</script>
	<script src="../wp-includes/js/mediaelement/wp-mediaelement.min.js?ver=6.9.1" id="wp-mediaelement-js"></script>
	<script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/animate-circle.min.js?ver=3.7.9"
		id="animate-circle-js"></script>

	<script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/elementor.js?ver=3.7.9"
		id="elementskit-elementor-js"></script>
	<script src="../wp-content/plugins/elementskit/widgets/init/assets/js/elementor.js?ver=4.2.1"
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
	<script src="../../assets/js/theme-panel.js"></script>

</body>

</html>
