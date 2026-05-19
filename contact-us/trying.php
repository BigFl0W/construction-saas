<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us – TPV Construction and Services LTD (Swiper Slider)</title>
    <!-- dns-prefetch & alternate links (kept) -->
    <link rel='dns-prefetch' href='//fonts.googleapis.com'>
    <link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Feed" href="../feed/">
    <link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Comments Feed" href="../comments/feed/">
    <!-- oEmbed etc (kept) -->
    <link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed" href="../wp-json/oembed/1.0/embed-34">
    <link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed" href="../wp-json/oembed/1.0/embed-35">

    <!-- Original WordPress inline styles (kept) -->
    <style id='wp-img-auto-sizes-contain-inline-css'>img:is([sizes=auto i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px; }</style>
    <style id='wp-emoji-styles-inline-css'>img.wp-smiley, img.emoji { display: inline !important; border: none !important; }</style>
    <style id='classic-theme-styles-inline-css'>/*!*/ .wp-block-button__link { color:#fff; background-color:#32373c; border-radius:9999px; }</style>
    <style id='global-styles-inline-css'>:root{--wp--preset--aspect-ratio--square:1; }</style>

    <!-- Swiper CSS (required for slider) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- ===== SLIDER STYLES (blended with TPV design) ===== -->
    <style>
        /* Existing TPV custom styles (summary) */
        .office-slider-container {
            position: relative;
            width: 100%;
            margin: 0 auto;
            min-height: 320px;
        }
        .office-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 2rem 1.5rem;
            box-shadow: 0 20px 35px -8px rgba(0,0,0,0.1);
            border: 1px solid rgba(229,54,61,0.1);
            transition: all 0.3s ease;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .office-card .office-name {
            font-size: 26px;
            font-weight: 700;
            color: #0A0C1E;
            margin-bottom: 16px;
            font-family: "Manrope", sans-serif;
        }
        .office-card .office-address {
            font-size: 16px;
            line-height: 1.6;
            color: #5A5B70;
            margin-bottom: 20px;
            font-family: "DM Sans", sans-serif;
            white-space: pre-line;
        }
        .office-card .office-phone {
            font-size: 18px;
            font-weight: 600;
            color: #E5363D;
            margin: 10px 0 8px;
        }
        .office-card .office-email {
            display: inline-block;
            background: #f0f5fe;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            color: #004e9e;
            font-weight: 500;
            font-size: 0.95rem;
            margin-top: 12px;
            text-decoration: none;
            border: 1px solid #dbe4f0;
            transition: 0.2s;
        }
        .office-card .office-email:hover {
            background: #e1eaf3;
        }

        /* Swiper slider specific styles (inspired by testimonial pattern) */
        .testimonial-slider-custom {
            position: relative;
            overflow: hidden;
            padding: 20px 0 60px;
            width: 100%;
        }
        .testimonial-slider-custom .swiper {
            overflow: visible !important;
            padding: 10px 5px;
        }
        .testimonial-slider-custom .swiper-slide {
            opacity: 0.5;
            transform: scale(0.9);
            transition: all 0.5s ease;
            height: auto;
        }
        .testimonial-slider-custom .swiper-slide-active {
            opacity: 1;
            transform: scale(1);
        }
        /* navigation arrows */
        .testimonial-slider-custom .swiper-button-prev,
        .testimonial-slider-custom .swiper-button-next {
            width: 44px;
            height: 44px;
            background: white;
            border: 1px solid #eaeef5;
            border-radius: 50%;
            box-shadow: 0 8px 18px rgba(0,0,0,0.05);
            color: #1f1f2b;
            transition: 0.2s;
        }
        .testimonial-slider-custom .swiper-button-prev:hover,
        .testimonial-slider-custom .swiper-button-next:hover {
            background: #E5363D;
            color: white;
            border-color: #E5363D;
        }
        .testimonial-slider-custom .swiper-button-prev:after,
        .testimonial-slider-custom .swiper-button-next:after {
            font-size: 20px;
            font-weight: 600;
        }
        /* pagination dots */
        .testimonial-slider-custom .swiper-pagination {
            bottom: 0 !important;
        }
        .testimonial-slider-custom .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: #d0dae8;
            opacity: 1;
            transition: 0.2s;
            border-radius: 20px;
        }
        .testimonial-slider-custom .swiper-pagination-bullet-active {
            width: 32px;
            background: #E5363D;
            border-radius: 20px;
        }
        /* map styling */
        iframe {
            border-radius: 24px;
            box-shadow: 0 22px 40px -18px #10182f;
        }
        .elementor-icon-box-wrapper {
            text-align: center;
        }
        /* remove duplicate arrows */
        .slider-arrrow, .slider-arrr0ow { display: none; }
        /* keep responsiveness */
        @media (max-width: 768px) {
            .testimonial-slider-custom .swiper-button-prev,
            .testimonial-slider-custom .swiper-button-next {
                display: none;
            }
        }
    </style>

    <!-- Original external stylesheets (kept) -->
    <link rel='stylesheet' id='contact-form-7-css' href='../wp-content/plugins/contact-form-7/includes/css/styles.css?ver=6.1.5' media='all'>
    <link rel='stylesheet' id='elementor-frontend-css' href='../wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='elementor-post-7-css' href='../wp-content/uploads/elementor/css/post-7.css?ver=1770715450' media='all'>
    <link rel='stylesheet' id='widget-heading-css' href='../wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='e-animation-fadeInUp-css' href='../wp-content/plugins/elementor/assets/lib/animations/styles/fadeInUp.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='widget-icon-box-css' href='../wp-content/plugins/elementor/assets/css/widget-icon-box.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='widget-image-css' href='../wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='widget-google_maps-css' href='../wp-content/plugins/elementor/assets/css/widget-google_maps.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='elementor-post-3084-css' href='../wp-content/uploads/elementor/css/post-3084.css?ver=1770724992' media='all'>
    <link rel='stylesheet' id='ekit-widget-styles-css' href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' id='ekit-widget-styles-pro-css' href='../wp-content/plugins/elementskit/widgets/init/assets/css/widget-styles-pro.css?ver=4.2.1' media='all'>
    <link rel='stylesheet' id='ekit-responsive-css' href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' id='TPV Construction Services-font-css' href='../../css2?family=Manrope:wght@200..800&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap' media='all'>
    <link rel='stylesheet' id='TPV Construction Services-css-variable-css' href='../wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='fontawesome-6.4.0-css' href='../wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='bootstrap-5.3.2-css' href='../wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='TPV Construction Services-style-css' href='../wp-content/themes/tpv/style.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='elementor-gf-local-manrope-css' href='../wp-content/uploads/elementor/google-fonts/css/manrope.css?ver=1744107304' media='all'>
    <link rel='stylesheet' id='elementor-gf-local-dmsans-css' href='../wp-content/uploads/elementor/google-fonts/css/dmsans.css?ver=1744107305' media='all'>
    <link rel='stylesheet' id='elementor-icons-ekiticons-css' href='../wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/css/ekiticons.css?ver=3.7.9' media='all'>

    <script src="../wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
    <script src="../wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
    <link rel="https://api.w.org/" href="../wp-json/">
    <link rel="alternate" title="JSON" type="application/json" href="../wp-json/wp/v2/pages/3084">
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://tpvconstruction.com.ng/xmlrpc.php?rsd">
    <link rel="canonical" href="index.htm">
    <link rel='shortlink' href='index.htm?p=3084'>
    <meta name="generator" content="Elementor 3.35.3; features: e_font_icon_svg, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap">
    <script>var elementskit_module_parallax_url = "https://tpvconstruction.com.ng/wp-content/plugins/elementskit/modules/parallax/";</script>
    <style>.e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload) * { background-image: none !important; }</style>
    <link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="32x32">
    <link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="192x192">
    <link rel="apple-touch-icon" href="../wp-content/uploads/2024/06/favicon.png">
    <meta name="msapplication-TileImage" content="https://tpvconstrcution.com.ng/wp-content/uploads/2024/06/favicon.png">
</head>
<body class="wp-singular page-template page-template-elementor_header_footer page page-id-3084 wp-custom-logo wp-theme-TPV Construction Services tt-magic-cursor elementor-default elementor-template-full-width elementor-kit-7 elementor-page elementor-page-3084">
    </div>
    <a class="skip-link screen-reader-text" href="#content">Skip to content</a>

    <!-- header would be injected via PHP -->
	 <?php
		// include header (kept as is)
		include('../includes/header.php');
	?>

    <div data-elementor-type="wp-page" data-elementor-id="3084" class="elementor elementor-3084">
        <!-- Hero section (unchanged) -->
        <div class="elementor-element elementor-element-f781c3e e-con-full e-flex e-con e-parent" data-id="f781c3e" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div class="elementor-element elementor-element-cfeafcd e-con-full e-flex e-con e-child" data-id="cfeafcd" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                <div class="elementor-element elementor-element-160f4a5 e-flex e-con-boxed e-con e-child" data-id="160f4a5" data-element_type="container">
                    <div class="e-con-inner">
                        <div class="elementor-element elementor-element-077029b at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading" data-id="077029b" data-element_type="widget" data-widget_type="heading.default"><div class="elementor-widget-container"><h1 class="elementor-heading-title elementor-size-default">Contact Us</h1></div></div>
                        <div class="elementor-element elementor-element-37c956b elementor-invisible elementor-widget elementor-widget-elementskit-breadcrumb" data-id="37c956b" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;}" data-widget_type="elementskit-breadcrumb.default"><div class="elementor-widget-container"><div class="ekit-wid-con"><ol class="ekit-breadcrumb"><li class="ekit_breadcrumbs_start"><a href="https://tpvconstruction.com.ng">Home</a></li><li class="brd_sep"><span class="separate_icon"><svg width="7" height="12" viewBox="0 0 7 12"><path d="M0.283883 11.68L4.95988 0.129999H6.70988L2.03388 11.68H0.283883Z" fill="white"></path></svg></span></li><li>Contact Us</li></ol></div></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== OFFICE LOCATIONS SLIDER (Swiper, based on testimonial pattern) ========== -->
        <div class="elementor-element elementor-element-949fc23 e-flex e-con-boxed e-con e-parent" data-id="949fc23" data-element_type="container">
            <div class="e-con-inner">
                <!-- Left column: heading & intro (like testimonial section) -->
                <div class="elementor-element elementor-element-256f3db e-con-full e-flex e-con e-child" data-id="256f3db" data-element_type="container">
                    <div class="elementor-element elementor-element-08bb083 at-heading-animation at-animation-heading-none elementor-widget elementor-widget-heading" data-id="08bb083" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h3 class="elementor-heading-title elementor-size-default">Our Offices</h3>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-92123f7 at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading" data-id="92123f7" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default">Visit us at any of our locations</h2>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-6d86cde elementor-widget__width-initial elementor-invisible elementor-widget elementor-widget-text-editor" data-id="6d86cde" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:100}" data-widget_type="text-editor.default">
                        <div class="elementor-widget-container">
                            <p>We are strategically located across Nigeria to serve you better. Browse through our offices – the map updates automatically.</p>
                        </div>
                    </div>
                </div>

                <!-- Right column: Swiper slider with office cards -->
                <div class="elementor-element elementor-element-f869698 e-con-full e-flex e-con e-child" data-id="f869698" data-element_type="container">
                    <div class="elementor-element elementor-element-65aa66b testimonial-item elementor-widget elementor-widget-elementskit-testimonial" data-id="65aa66b" data-element_type="widget" data-widget_type="elementskit-testimonial.default">
                        <div class="elementor-widget-container">
                            <div class="ekit-wid-con">
                                <!-- custom swiper slider (based on testimonial structure) -->
                                <div class="testimonial-slider-custom">
                                    <div class="swiper" id="officeSwiper">
                                        <div class="swiper-wrapper">
                                            <!-- Slides will be injected via JS -->
                                        </div>
                                        <!-- navigation arrows -->
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-button-next"></div>
                                        <!-- pagination dots -->
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GOOGLE MAP iframe (updated via JS) -->
        <div class="elementor-element elementor-element-fe6f3bc e-con-full e-flex e-con e-parent" data-id="fe6f3bc" data-element_type="container">
            <div class="elementor-element elementor-element-1dae5f8 elementor-widget elementor-widget-google_maps" data-id="1dae5f8" data-element_type="widget" data-widget_type="google_maps.default">
                <div class="elementor-widget-container">
                    <div class="elementor-custom-embed">
                        <iframe id="mainMapIframe" class="w-100 mb-n2" style="height:450px" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127223.16264394466!2d7.00479655!3d4.81741045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1069cea39f2c48e3%3A0x53562bdd7d8832db!2sPort%20Harcourt%2C%20Rivers!5e0!3m2!1sen!2sng!4v1726019643240!5m2!1sen!2sng" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact form & sidebar (unchanged) -->
        <div class="elementor-element elementor-element-617a3cf e-flex e-con-boxed e-con e-parent" data-id="617a3cf" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div class="e-con-inner">
                <div class="elementor-element elementor-element-b5c14c8 e-con-full e-flex e-con e-child" data-id="b5c14c8" data-element_type="container">
                    <div class="elementor-element elementor-element-f130c6c e-con-full e-flex e-con e-child" data-id="f130c6c" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                        <div class="elementor-element elementor-element-1c38e3f at-heading-animation elementor-widget elementor-widget-heading" data-id="1c38e3f" data-element_type="widget" data-widget_type="heading.default"><div class="elementor-widget-container"><h2 class="elementor-heading-title elementor-size-default">Ready to get started? let's chat.</h2></div></div>
                        <div class="elementor-element elementor-element-e67b911 elementor-widget elementor-widget-text-editor" data-id="e67b911" data-element_type="widget" data-widget_type="text-editor.default"><div class="elementor-widget-container"><p>Please fill out the form below, and a member of our team will get back to you as soon as possible.</p></div></div>
                        <div class="elementor-element elementor-element-1bc699e contact-form elementor-widget elementor-widget-elementskit-contact-form7" data-id="1bc699e" data-element_type="widget" data-widget_type="elementskit-contact-form7.default"><div class="elementor-widget-container"><div class="ekit-wid-con"><div class="ekit-form"><div class="wpcf7 no-js" id="wpcf7-f6700-p3084-o1"><div class="screen-reader-response"><p role="status"></p><ul></ul></div><form action="/contact-us/#wpcf7-f6700-p3084-o1" method="post" class="wpcf7-form init"><fieldset class="hidden-fields-container"><input type="hidden" name="_wpcf7" value="6700"/><input type="hidden" name="_wpcf7_version" value="6.1.5"/><input type="hidden" name="_wpcf7_locale" value="en_US"/><input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f6700-p3084-o1"/><input type="hidden" name="_wpcf7_container_post" value="3084"/><input type="hidden" name="_wpcf7_posted_data_hash" value=""/></fieldset><div class="row"><div class="form-group col-md-6 mb-4"><p><span class="wpcf7-form-control-wrap" data-name="your-name"><input size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" placeholder="Enter Your name" type="text" name="your-name"/></span></p></div><div class="form-group col-md-6 mb-4"><p><span class="wpcf7-form-control-wrap" data-name="email"><input size="40" class="wpcf7-form-control wpcf7-email wpcf7-validates-as-required wpcf7-text wpcf7-validates-as-email form-control" placeholder="Enter Your email" type="email" name="email"/></span></p></div><div class="form-group col-md-6 mb-4"><p><span class="wpcf7-form-control-wrap" data-name="phone"><input size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" placeholder="Phone number" type="text" name="phone"/></span></p></div><div class="form-group col-md-6 mb-4"><p><span class="wpcf7-form-control-wrap" data-name="Subject"><input size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" placeholder="Subject" type="text" name="Subject"/></span></p></div><div class="form-group col-md-12 mb-4"><p><span class="wpcf7-form-control-wrap" data-name="message"><textarea cols="40" rows="10" maxlength="2000" class="wpcf7-form-control wpcf7-textarea form-control" placeholder="Message" name="message"></textarea></span></p></div><div class="col-md-12 form-btn"><p><input class="wpcf7-form-control wpcf7-submit has-spinner btn-default" type="submit" value="Submit"/></p></div></div><div class="wpcf7-response-output"></div></form></div></div></div></div></div>
                    </div>
                    <div class="elementor-element elementor-element-d9d3181 e-con-full e-flex e-con e-child" data-id="d9d3181" data-element_type="container">
                        <div class="elementor-element elementor-element-04a534d e-con-full contact-sidebar e-flex e-con e-child" data-id="04a534d" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                            <div class="elementor-element elementor-element-7a22d79 elementor-widget elementor-widget-heading" data-id="7a22d79" data-element_type="widget" data-widget_type="heading.default"><div class="elementor-widget-container"><h3 class="elementor-heading-title elementor-size-default">follow us</h3></div></div>
                            <div class="elementor-element elementor-element-6af3709 elementor-widget elementor-widget-elementskit-social-media" data-id="6af3709" data-element_type="widget" data-widget_type="elementskit-social-media.default"><div class="elementor-widget-container"><div class="ekit-wid-con"><ul class="ekit_social_media"><li class="elementor-repeater-item-d13ec6c"><a href="https://facebook.com" class="instagram"><svg class="e-font-icon-svg e-fab-instagram" viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141z"></path></svg></a></li><li class="elementor-repeater-item-9d2a925"><a href="https://facebook.com" class="facebook"><i class="icon icon-facebook"></i></a></li><li class="elementor-repeater-item-c185241"><a href="https://facebook.com" class="twitter"><svg class="e-font-icon-svg e-fab-x-twitter" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48z"></path></svg></a></li><li class="elementor-repeater-item-18dfedb"><a href="https://facebook.com" class="github"><i class="icon icon-github"></i></a></li><li class="elementor-repeater-item-d0581ab"><a href="https://facebook.com" class="in"><svg class="e-font-icon-svg e-fab-linkedin-in" viewBox="0 0 448 512"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path></svg></a></li></ul></div></div></div>
                            <div class="elementor-element elementor-element-9a0d627 elementor-widget elementor-widget-image" data-id="9a0d627" data-element_type="widget" data-widget_type="image.default"><div class="elementor-widget-container"><img fetchpriority="high" decoding="async" width="301" height="420" src="../wp-content/uploads/2024/06/contact-info-img.png" class="attachment-full" alt=""></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Office data & slider initialization -->
	 <?php
		// include header (kept as is)
		include('../includes/header.php');
    ?>
    <script>
        (function() {
            const offices = [
                {
                    name: 'Head Office Port Harcourt',
                    address: '123 ph Street, Port Harcourt, Rivers State,\nNigeria',
                    phone: '+234 701 234 5678',
                    email: 'headoffice@tpvconstruction.com.ng',
                    mapSrc: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127223.16264394466!2d7.00479655!3d4.81741045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1069cea39f2c48e3%3A0x53562bdd7d8832db!2sPort%20Harcourt%2C%20Rivers!5e0!3m2!1sen!2sng!4v1726019643240!5m2!1sen!2sng'
                },
                {
                    name: 'Lagos Office',
                    address: '123 Lagos Street, Victoria Island,\nLagos, Nigeria',
                    phone: '+234 809 876 5432',
                    email: 'lagos@tpvconstruction.com.ng',
                    mapSrc: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126916.65798753738!2d3.3212715!3d6.4283593!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b9228c2e5c7e9%3A0x8e46f3b41ef5c8a!2sVictoria%20Island%2C%20Lagos!5e0!3m2!1sen!2sng!4v1710000000000'
                },
                {
                    name: 'Abuja Office',
                    address: '123 Abuja Street, Central Business District,\nAbuja, Nigeria',
                    phone: '+234 803 333 4444',
                    email: 'abuja@tpvconstruction.com.ng',
                    mapSrc: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126916.65798753738!2d7.47648015!3d9.072264!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e0b1b5b2b9a97%3A0x9a9b9e9c9c9b9a!2sCentral%20Business%20District%2C%20Abuja!5e0!3m2!1sen!2sng!4v1711111111111'
                },
                {
                    name: 'Enugu Branch',
                    address: '101 Zik Avenue,\nEnugu, Nigeria',
                    phone: '+234 812 345 6789',
                    email: 'enugu@tpvconstruction.com.ng',
                    mapSrc: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126916.65798753738!2d7.47648015!3d6.451639!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e0b1b5b2b9a97%3A0x9a9b9e9c9c9b9a!2sEnugu!5e0!3m2!1sen!2sng!4v1712222222222'
                },
                {
                    name: 'Kaduna Branch',
                    address: '202 Ahmadu Bello Way,\nKaduna, Nigeria',
                    phone: '+234 814 567 8901',
                    email: 'kaduna@tpvconstruction.com.ng',
                    mapSrc: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126916.65798753738!2d7.421394!3d10.526412!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e0b1b5b2b9a97%3A0x9a9b9e9c9c9b9a!2sKaduna!5e0!3m2!1sen!2sng!4v1713333333333'
                }
            ];

            const mapIframe = document.getElementById('mainMapIframe');
            const swiperWrapper = document.querySelector('#officeSwiper .swiper-wrapper');

            // Build slides
            if (swiperWrapper) {
                offices.forEach((office, index) => {
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    slide.innerHTML = `
                        <div class="office-card">
                            <div class="office-name">${office.name}</div>
                            <div class="office-address">${office.address}</div>
                            <div class="office-phone">${office.phone}</div>
                            <div class="office-email">${office.email}</div>
                        </div>
                    `;
                    swiperWrapper.appendChild(slide);
                });
            }

            // Initialize Swiper with testimonial-like settings
            const swiper = new Swiper('#officeSwiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                speed: 800,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: false,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    }
                },
                on: {
                    slideChange: function () {
                        // update map based on real index (considering loop)
                        let realIndex = this.realIndex;
                        // ensure within bounds
                        if (realIndex >= offices.length) realIndex = 0;
                        mapIframe.src = offices[realIndex].mapSrc;
                    },
                    init: function () {
                        mapIframe.src = offices[0].mapSrc;
                    }
                }
            });

            // additional: if user clicks on pagination dots, map already updates via slideChange
        })();
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
				"floatingButtonsClickTracking": "f2b6ebecb2"
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
				"id": 3084,
				"title": "Contact%20Us%20%E2%80%93%20TPV Construction and Services LTD",
				"excerpt": "",
				"featuredImage": false
			}
		};
		//# sourceURL=elementor-frontend-js-before
	</script>
	<script src="../wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.35.3"
		id="elementor-frontend-js"></script>
	<script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/animate-circle.min.js?ver=3.7.9"
		id="animate-circle-js"></script>
	<script id="elementskit-elementor-js-extra">
		var ekit_config = {
			"ajaxurl": "https://tpvconstruction.com.ng/wp-admin/admin-ajax.php",
			"nonce": "a394ffb170"
		};
		//# sourceURL=elementskit-elementor-js-extra
	</script>
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