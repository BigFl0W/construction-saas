<?php
session_start();
$contactSent = isset($_GET['sent']);
$contactErrors = $_SESSION['contact_errors'] ?? [];
$contactOld = $_SESSION['contact_old'] ?? [];
unset($_SESSION['contact_errors'], $_SESSION['contact_old']);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us – TPV Construction and Services LTD</title>
    <!-- dns-prefetch & alternate links (kept) -->
    <link rel='dns-prefetch' href='//fonts.googleapis.com'>
    <link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Feed" href="../feed/">
    <link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Comments Feed" href="../comments/feed/">
    <link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed" href="../wp-json/oembed/1.0/embed-34">
    <link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed" href="../wp-json/oembed/1.0/embed-35">

    <!-- Original WordPress inline styles (kept) -->
    <style id='wp-img-auto-sizes-contain-inline-css'>img:is([sizes=auto i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px; }</style>
    <style id='wp-emoji-styles-inline-css'>img.wp-smiley, img.emoji { display: inline !important; border: none !important; }</style>
    <style id='classic-theme-styles-inline-css'>/*!*/ .wp-block-button__link { color:#fff; background-color:#32373c; border-radius:9999px; }</style>
    <style id='global-styles-inline-css'>:root{--wp--preset--aspect-ratio--square:1; }</style>

    <!-- Location Slider Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

        body { font-family: 'Outfit', sans-serif !important; }
        
        /* Premium Location Card Styles */
        .location-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border-radius: 24px;
            padding: 3rem 2rem;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05), 0 0 0 1px rgba(0,0,0,0.03);
            border: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .location-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(229,54,61,0.05), rgba(0,0,0,0));
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .location-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px -15px rgba(229,54,61,0.2), 0 0 0 1px rgba(229,54,61,0.1);
        }
        .location-card:hover::before {
            opacity: 1;
        }
        .location-icon {
            margin-bottom: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: rgba(229,54,61,0.07);
            border-radius: 50%;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: auto;
            margin-right: auto;
        }
        .location-card:hover .location-icon {
            background: #E5363D;
            transform: scale(1.1) rotate(10deg);
        }
        .location-icon svg {
            width: 35px;
            height: 35px;
            color: #E5363D;
            transition: all 0.5s ease;
        }
        .location-card:hover .location-icon svg {
            color: #ffffff;
            transform: scale(0.9);
        }
        .location-name {
            font-size: 26px;
            font-weight: 800;
            color: #1a1e29;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
            transition: color 0.3s;
            font-family: 'Outfit', sans-serif;
        }
        .location-city {
            font-size: 15px;
            font-weight: 700;
            color: #E5363D;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Outfit', sans-serif;
        }
        .location-address {
            font-size: 16px;
            line-height: 1.7;
            color: #64748b;
            margin-bottom: 25px;
            padding: 0 15px;
            font-family: 'Outfit', sans-serif;
        }
        .location-contact {
            margin-top: auto;
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 20px;
        }
        .location-phone {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 10px 0 15px;
        }
        .location-phone a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s;
        }
        .location-phone a:hover {
            color: #E5363D;
        }
        .location-email {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            color: #E5363D;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            border: 2px solid #E5363D;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(229,54,61,0.1);
        }
        .location-email:hover {
            background: #E5363D;
            color: #ffffff;
            box-shadow: 0 8px 15px rgba(229,54,61,0.25);
            transform: translateY(-2px);
        }

        /* Slider Navigation */
        .elementskit-testimonial-slider .swiper-button-prev,
        .elementskit-testimonial-slider .swiper-button-next {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: white;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 50%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            color: #1f1f2b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }
        .elementskit-testimonial-slider .swiper-button-prev {
            left: -20px;
        }
        .elementskit-testimonial-slider .swiper-button-next {
            right: -20px;
        }
        .elementskit-testimonial-slider .swiper-button-prev:hover,
        .elementskit-testimonial-slider .swiper-button-next:hover {
            background: #E5363D;
            color: white;
            border-color: #E5363D;
            box-shadow: 0 15px 30px rgba(229,54,61,0.3);
        }
        .elementskit-testimonial-slider .swiper-button-prev::after,
        .elementskit-testimonial-slider .swiper-button-next::after {
            font-size: 20px;
            font-weight: 900;
        }

        /* Pagination Dots */
        .elementskit-testimonial-slider .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: #cbd5e1;
            opacity: 1;
            transition: all 0.3s ease;
            border-radius: 20px;
        }
        .ekit-testimonial-slider .swiper-pagination-bullet-active {
            width: 30px;
            background: #E5363D;
            border-radius: 20px;
        }

        /* Premium Form Styles */
        .contact-form .form-control {
            background: #f8fafc;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            font-size: 16px;
            transition: all 0.3s ease;
            color: #1e293b;
            font-family: 'Outfit', sans-serif;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .contact-form .form-control:focus {
            background: #ffffff;
            border-color: #E5363D;
            box-shadow: 0 0 0 4px rgba(229,54,61,0.1);
            outline: none;
        }
        .contact-form textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        .contact-form .btn-default,
        .wpcf7-submit {
            background: linear-gradient(135deg, #E5363D 0%, #bd1218 100%);
            color: #ffffff !important;
            padding: 1.2rem 3rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 17px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(229,54,61,0.3);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            width: 100%;
            font-family: 'Outfit', sans-serif;
        }
        .contact-form .btn-default:hover,
        .wpcf7-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(229,54,61,0.5);
            background: linear-gradient(135deg, #bd1218 0%, #E5363D 100%);
        }

        /* Map Styling */
        .location-map iframe {
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.2);
            filter: contrast(1.1) saturate(1.2);
            transition: all 0.4s ease;
        }
        .location-map iframe:hover {
            transform: scale(1.01);
            box-shadow: 0 30px 50px -15px rgba(0,0,0,0.3);
        }
        
        /* Heading aesthetics */
        .elementor-heading-title {
            font-family: 'Outfit', sans-serif !important;
            letter-spacing: -0.5px;
        }
        .elementor-heading-title h1 {
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Social sidebar */
        .ekit_social_media li a {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
            color: #1e293b !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 45px !important;
            height: 45px !important;
        }
        .ekit_social_media li a:hover {
            transform: translateY(-5px) scale(1.1);
            background: #E5363D !important;
            color: white !important;
            box-shadow: 0 10px 20px rgba(229,54,61,0.3) !important;
        }
        
        /* Sidebar container Polish */
        .contact-sidebar {
            background: linear-gradient(145deg, #f8fafc, #ffffff);
            border-radius: 24px;
            padding: 3rem 2rem;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.02);
            height: 100%;
        }

        @media (max-width: 768px) {
            .elementskit-testimonial-slider .swiper-button-prev,
            .elementskit-testimonial-slider .swiper-button-next {
                display: none !important;
            }
            .location-card {
                padding: 2.5rem 1.5rem;
            }
        }

		/* Force slides to be visible */
        .elementskit-testimonial-slider .swiper-slide {
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
        }

        /* Ensure slider container has height */
        .elementskit-testimonial-slider .swiper-container,
        .elementskit-testimonial-slider .ekit-main-swiper {
            overflow: hidden !important;
            min-height: 400px !important;
        }
    </style>

    <!-- Original external stylesheets -->
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

    <?php include('../includes/header.php'); ?>

    <div data-elementor-type="wp-page" data-elementor-id="3084" class="elementor elementor-3084">
        <!-- Hero section -->
        <div class="elementor-element elementor-element-f781c3e e-con-full e-flex e-con e-parent" data-id="f781c3e" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div class="elementor-element elementor-element-cfeafcd e-con-full e-flex e-con e-child" data-id="cfeafcd" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                <div class="elementor-element elementor-element-160f4a5 e-flex e-con-boxed e-con e-child" data-id="160f4a5" data-element_type="container">
                    <div class="e-con-inner">
                        <div class="elementor-element elementor-element-077029b at-heading-animation at-animation-heading-style-3 elementor-widget elementor-widget-heading" data-id="077029b" data-element_type="widget" data-widget_type="heading.default">
                            <div class="elementor-widget-container">
                                <h1 class="elementor-heading-title elementor-size-default">Contact Us</h1>
                            </div>
                        </div>
                        <div class="elementor-element elementor-element-37c956b elementor-invisible elementor-widget elementor-widget-elementskit-breadcrumb" data-id="37c956b" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;}" data-widget_type="elementskit-breadcrumb.default">
                            <div class="elementor-widget-container">
                                <div class="ekit-wid-con">
                                    <ol class="ekit-breadcrumb">
                                        <li class="ekit_breadcrumbs_start"><a href="https://tpvconstruction.com.ng">Home</a></li>
                                        <li class="brd_sep"><span class="separate_icon"><svg width="7" height="12" viewBox="0 0 7 12"><path d="M0.283883 11.68L4.95988 0.129999H6.70988L2.03388 11.68H0.283883Z" fill="white"></path></svg></span></li>
                                        <li>Contact Us</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Slider Section - Using Original Testimonial Pattern -->
        <div class="elementor-element elementor-element-7bb9273 e-flex e-con-boxed e-con e-parent" data-id="7bb9273" data-element_type="container">
            <div class="e-con-inner">
                <div class="elementor-element elementor-element-f869698 elementor-widget elementor-widget-elementskit-testimonial" data-id="f869698" data-element_type="widget" data-widget_type="elementskit-testimonial.default">
                    <div class="elementor-widget-container">
                        <div class="ekit-wid-con">
                            <div class="elementskit-testimonial-slider ekit_testimonial_style_5 arrow_inside slider-dotted" data-config='{"rtl":false,"arrows":true,"dots":true,"pauseOnHover":true,"autoplay":{"delay":10000,"disableOnInteraction":false,"pauseOnMouseEnter":true},"speed":1500,"slidesPerGroup":1,"slidesPerView":3,"loop":true,"spaceBetween":30,"breakpoints":{"320":{"slidesPerView":1,"slidesPerGroup":1,"spaceBetween":10},"768":{"slidesPerView":2,"slidesPerGroup":1,"spaceBetween":20},"1024":{"slidesPerView":3,"slidesPerGroup":1,"spaceBetween":30}}}'>
                                <div class="ekit-main-swiper swiper">
                                    <div class="swiper-wrapper">
                                        <!-- Abuja Office -->
                                        <div class="swiper-slide">
                                            <div class="swiper-slide-inner">
                                                <div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style location-card">
                                                    <div class="elementskit-commentor-header">
                                                        <div class="location-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                                <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="currentColor" stroke-linejoin="round"/>
                                                                <circle cx="12" cy="10" r="3" stroke="currentColor"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="elementskit-commentor-content">
                                                        <h3 class="location-name">Abuja Office</h3>
                                                        <h4 class="location-city">Area 11, Abuja</h4>
                                                        <p class="location-address">2nd Floor, Right Wing, APDC Building, Area 11, Abuja</p>
                                                        <div class="location-contact">
                                                            <p class="location-phone"><a href="tel:09097128241">09097128241</a></p>
                                                            <a href="mailto:abuja@tpvconstruction.com.ng" class="location-email">abuja@tpvconstruction.com.ng</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ogun Office -->
                                        <div class="swiper-slide">
                                            <div class="swiper-slide-inner">
                                                <div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style location-card">
                                                    <div class="elementskit-commentor-header">
                                                        <div class="location-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                                <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="currentColor" stroke-linejoin="round"/>
                                                                <circle cx="12" cy="10" r="3" stroke="currentColor"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="elementskit-commentor-content">
                                                        <h3 class="location-name">Ogun Office</h3>
                                                        <h4 class="location-city">Ilaro, Ogun State</h4>
                                                        <p class="location-address">Beside Aladey Hotel, Along Federal Poly Express Road, Ilaro, Ogun State</p>
                                                        <div class="location-contact">
                                                            <p class="location-phone"><a href="tel:09097128241">09097128241</a></p>
                                                            <a href="mailto:ogun@tpvconstruction.com.ng" class="location-email">ogun@tpvconstruction.com.ng</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Nasarawa Office -->
                                        <div class="swiper-slide">
                                            <div class="swiper-slide-inner">
                                                <div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style location-card">
                                                    <div class="elementskit-commentor-header">
                                                        <div class="location-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                                <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="currentColor" stroke-linejoin="round"/>
                                                                <circle cx="12" cy="10" r="3" stroke="currentColor"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="elementskit-commentor-content">
                                                        <h3 class="location-name">Nasarawa Office</h3>
                                                        <h4 class="location-city">Keffi, Nasarawa</h4>
                                                        <p class="location-address">By New York Park and Gardens, Keffi, Nasarawa</p>
                                                        <div class="location-contact">
                                                            <p class="location-phone"><a href="tel:08069418816">08069418816</a></p>
                                                            <a href="mailto:nasarawa@tpvconstruction.com.ng" class="location-email">nasarawa@tpvconstruction.com.ng</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lagos Office -->
                                        <div class="swiper-slide">
                                            <div class="swiper-slide-inner">
                                                <div class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style location-card">
                                                    <div class="elementskit-commentor-header">
                                                        <div class="location-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                                <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="currentColor" stroke-linejoin="round"/>
                                                                <circle cx="12" cy="10" r="3" stroke="currentColor"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="elementskit-commentor-content">
                                                        <h3 class="location-name">Lagos Office</h3>
                                                        <h4 class="location-city">Ikeja, Lagos</h4>
                                                        <p class="location-address">10A, Onipinla Lane, Harmony Enclave, Off Adeniyi Jones Avenue, Ikeja, Lagos</p>
                                                        <div class="location-contact">
                                                            <p class="location-phone"><a href="tel:08104830712">08104830712</a></p>
                                                            <a href="mailto:lagos@tpvconstruction.com.ng" class="location-email">lagos@tpvconstruction.com.ng</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-pagination"></div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-button-next"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Map Section -->
        <div class="elementor-element elementor-element-fe6f3bc e-con-full e-flex e-con e-parent" data-id="fe6f3bc" data-element_type="container">
            <div class="elementor-element elementor-element-1dae5f8 location-map elementor-widget elementor-widget-google_maps" data-id="1dae5f8" data-element_type="widget" data-widget_type="google_maps.default">
                <div class="elementor-widget-container">
                    <div class="elementor-custom-embed">
                        <iframe id="locationMap"
                            class="w-100 mb-n2"
                            style="height: 450px; width: 100%; border: 0;"
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=Port+Harcourt+Rivers+Nigeria"
                            frameborder="0"
                            allowfullscreen=""
                            aria-hidden="false"
                            tabindex="0"
                            title="Google Map showing TPV Construction and Services LTD offices"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form & Sidebar -->
        <div class="elementor-element elementor-element-617a3cf e-flex e-con-boxed e-con e-parent" data-id="617a3cf" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div class="e-con-inner">
                <div class="elementor-element elementor-element-b5c14c8 e-con-full e-flex e-con e-child" data-id="b5c14c8" data-element_type="container">
                    <div class="elementor-element elementor-element-f130c6c e-con-full e-flex e-con e-child" data-id="f130c6c" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                        <div class="elementor-element elementor-element-1c38e3f at-heading-animation elementor-widget elementor-widget-heading" data-id="1c38e3f" data-element_type="widget" data-widget_type="heading.default">
                            <div class="elementor-widget-container">
                                <h2 class="elementor-heading-title elementor-size-default">Ready to get started? let's chat.</h2>
                            </div>
                        </div>
                        <div class="elementor-element elementor-element-e67b911 elementor-widget elementor-widget-text-editor" data-id="e67b911" data-element_type="widget" data-widget_type="text-editor.default">
                            <div class="elementor-widget-container">
                                <p>Please fill out the form below, and a member of our team will get back to you as soon as possible.</p>
                            </div>
                        </div>
                        <div class="contact-form elementor-widget">
                            <div class="elementor-widget-container">
                                <?php if ($contactSent): ?>
                                <div class="alert alert-success text-center py-4" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:10px;color:#fff;">
                                    <i class="fas fa-check-circle fa-2x mb-2" style="color:#10b981;"></i>
                                    <h4 style="color:#fff;">Thank You!</h4>
                                    <p class="mb-0">Your message has been sent successfully. We will get back to you within 24 hours.</p>
                                </div>
                                <?php else: ?>
                                <?php if (!empty($contactErrors)): ?>
                                <div class="alert alert-danger py-3" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:10px;color:#fff;">
                                    <ul class="mb-0" style="list-style:none;padding:0;">
                                        <?php foreach ($contactErrors as $err): ?>
                                        <li><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($err); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                <form action="../contact_process.php" method="post" novalidate>
                                    <div class="row">
                                        <div class="form-group col-md-6 mb-4">
                                            <input size="40" class="form-control" placeholder="Your Full Name *" type="text" name="name" value="<?php echo htmlspecialchars($contactOld['name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-4">
                                            <input size="40" class="form-control" placeholder="Email Address *" type="email" name="email" value="<?php echo htmlspecialchars($contactOld['email'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-4">
                                            <input size="40" class="form-control" placeholder="Phone Number *" type="text" name="phone" value="<?php echo htmlspecialchars($contactOld['phone'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-4">
                                            <input size="40" class="form-control" placeholder="Subject *" type="text" name="subject" value="<?php echo htmlspecialchars($contactOld['subject'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group col-md-12 mb-4">
                                            <textarea cols="40" rows="10" maxlength="2000" class="form-control" placeholder="Message" name="message" required><?php echo htmlspecialchars($contactOld['message'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-12 form-btn">
                                            <button type="submit" name="contact_submit" class="btn-default" value="1">Send Message</button>
                                        </div>
                                    </div>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-d9d3181 e-con-full e-flex e-con e-child" data-id="d9d3181" data-element_type="container">
                        <div class="elementor-element elementor-element-04a534d e-con-full contact-sidebar e-flex e-con e-child" data-id="04a534d" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                            <div class="elementor-element elementor-element-7a22d79 elementor-widget elementor-widget-heading" data-id="7a22d79" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h3 class="elementor-heading-title elementor-size-default">follow us</h3>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-6af3709 elementor-widget elementor-widget-elementskit-social-media" data-id="6af3709" data-element_type="widget" data-widget_type="elementskit-social-media.default">
                                <div class="elementor-widget-container">
                                    <div class="ekit-wid-con">
                                        <ul class="ekit_social_media">
                                            <li class="elementor-repeater-item-d13ec6c"><a href="https://facebook.com" class="instagram"><svg class="e-font-icon-svg e-fab-instagram" viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141z"></path></svg></a></li>
                                            <li class="elementor-repeater-item-9d2a925"><a href="https://facebook.com" class="facebook"><i class="icon icon-facebook"></i></a></li>
                                            <li class="elementor-repeater-item-c185241"><a href="https://facebook.com" class="twitter"><svg class="e-font-icon-svg e-fab-x-twitter" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48z"></path></svg></a></li>
                                            <li class="elementor-repeater-item-18dfedb"><a href="https://facebook.com" class="github"><i class="icon icon-github"></i></a></li>
                                            <li class="elementor-repeater-item-d0581ab"><a href="https://facebook.com" class="in"><svg class="e-font-icon-svg e-fab-linkedin-in" viewBox="0 0 448 512"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path></svg></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-9a0d627 elementor-widget elementor-widget-image" data-id="9a0d627" data-element_type="widget" data-widget_type="image.default">
                                <div class="elementor-widget-container">
                                    <img fetchpriority="high" decoding="async" width="301" height="420" src="../wp-content/uploads/2024/06/contact-info-img.png" class="attachment-full" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Map Update and Slider Initialization Script -->
<script>
jQuery(document).ready(function($) {
    'use strict';

    // Location to map query mapping
    const locationMapQueries = {
        0: 'APDC+Building+Area+11+Abuja+Nigeria',
        1: 'Federal+Poly+Express+Road+Ilaro+Ogun+State+Nigeria',
        2: 'Keffi+Nasarawa+Nigeria',
        3: 'Harmony+Enclave+Ikeja+Lagos+Nigeria'
    };

    // Function to initialize the slider manually if needed
    function initLocationSlider() {
        const $sliderContainer = $('.elementskit-testimonial-slider .ekit-main-swiper');
        
        if ($sliderContainer.length) {
            // Check if Swiper is available
            if (typeof Swiper === 'undefined') {
                // Load Swiper if not available
                loadSwiperLibrary();
                return;
            }

            // Get config from data attribute
            const configStr = $sliderContainer.closest('.elementskit-testimonial-slider').attr('data-config');
            let swiperConfig = {
                slidesPerView: 3,
                spaceBetween: 30,
                speed: 1500,
                loop: true,
                grabCursor: true,
                simulateTouch: true,
                autoplay: {
                    delay: 10000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 10
                    },
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30
                    }
                },
                on: {
                    init: function() {
                        console.log('Swiper initialized');
                        updateMapForSlide(this.realIndex);
                    },
                    slideChange: function() {
                        updateMapForSlide(this.realIndex);
                    }
                }
            };

            // Parse config if it exists
            if (configStr) {
                try {
                    const parsedConfig = JSON.parse(configStr);
                    swiperConfig = {...swiperConfig, ...parsedConfig};
                } catch (e) {
                    console.log('Error parsing slider config:', e);
                }
            }

            // Initialize Swiper
            try {
                const swiper = new Swiper($sliderContainer[0], swiperConfig);
                $sliderContainer.data('swiper', swiper);
                window.locationSwiper = swiper;
                console.log('Location slider initialized successfully');
            } catch (error) {
                console.error('Error initializing slider:', error);
            }
        }
    }

    // Update map based on slide index
    function updateMapForSlide(index) {
        const mapQuery = locationMapQueries[index] || 'Port+Harcourt+Rivers+Nigeria';
        const mapIframe = document.getElementById('locationMap');
        
        if (mapIframe) {
            const baseUrl = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=';
            mapIframe.src = baseUrl + mapQuery;
            console.log('Map updated to index:', index);
        }
    }

    // Load Swiper library if not present
    function loadSwiperLibrary() {
        if (typeof Swiper !== 'undefined') {
            initLocationSlider();
            return;
        }

        console.log('Loading Swiper library...');

        // Load Swiper CSS
        if (!$('link[href*="swiper-bundle.min.css"]').length) {
            $('<link>').appendTo('head').attr({
                rel: 'stylesheet',
                href: 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css'
            });
        }

        // Load Swiper JS
        $.getScript('https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js')
            .done(function() {
                console.log('Swiper loaded successfully');
                setTimeout(initLocationSlider, 5000);
            })
            .fail(function() {
                console.error('Failed to load Swiper');
            });
    }

    // Also try to use ElementsKit's existing slider
    function tryElementsKitSlider() {
        if (typeof elementskit !== 'undefined' && elementskit.testimonial) {
            console.log('Using ElementsKit slider');
            // ElementsKit should handle it, but we need to hook into their instance
            setTimeout(function() {
                const $sliderContainer = $('.elementskit-testimonial-slider .ekit-main-swiper');
                if ($sliderContainer.length && $sliderContainer[0].swiper) {
                    const swiper = $sliderContainer[0].swiper;
                    swiper.on('slideChange', function() {
                        updateMapForSlide(this.realIndex);
                    });
                    updateMapForSlide(swiper.realIndex);
                }
            }, 1000);
        } else {
            // Fallback to manual initialization
            initLocationSlider();
        }
    }

    // Start initialization
    setTimeout(tryElementsKitSlider, 500);

    // Handle window resize
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.locationSwiper) {
                window.locationSwiper.update();
            }
        }, 250);
    });

    // Manual trigger for debugging
    $('.elementskit-testimonial-slider').on('click', '.swiper-button-next', function() {
        console.log('Next button clicked');
    }).on('click', '.swiper-button-prev', function() {
        console.log('Prev button clicked');
    });
});
</script>	

    <!-- Original footer scripts -->
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
    <link rel='stylesheet' id='elementor-post-225-css' href='../wp-content/uploads/elementor/css/post-225.css?ver=1770715449' media='all'>
    <link rel='stylesheet' id='elementor-post-1688-css' href='../wp-content/uploads/elementor/css/post-1688.css?ver=1770715449' media='all'>
    <script src="../wp-includes/js/dist/hooks.min.js?ver=dd5603f07f9220ed27f1" id="wp-hooks-js"></script>
    <script src="../wp-includes/js/dist/i18n.min.js?ver=c26c3dc7bed366793375" id="wp-i18n-js"></script>
    <script id="wp-i18n-js-after">wp.i18n.setLocaleData({ 'text direction\u0004ltr': ['ltr'] });</script>
    <script src="../wp-content/plugins/contact-form-7/includes/swv/js/index.js?ver=6.1.5" id="swv-js"></script>
    <script id="contact-form-7-js-before">var wpcf7 = { "api": { "root": "https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-json\/", "namespace": "contact-form-7\/v1" }, "cached": 1 };</script>
    <script src="../wp-content/plugins/contact-form-7/includes/js/index.js?ver=6.1.5" id="contact-form-7-js"></script>
    <script src="../wp-content/themes/tpv/assets/js/SmoothScroll.js?ver=1.0.8" id="SmoothScroll-js"></script>
    <script src="../wp-content/themes/tpv/assets/js/gsap.min.js?ver=1.0.8" id="gsap-js"></script>
    <script src="../wp-content/themes/tpv/assets/js/magiccursor.js?ver=1.0.8" id="magiccursor-js"></script>
    <script src="../wp-content/themes/tpv/assets/js/SplitText.js?ver=1.0.8" id="SplitText-js"></script>
    <script src="../wp-content/themes/tpv/assets/js/ScrollTrigger.min.js?ver=1.0.8" id="ScrollTrigger-js"></script>
    <script src="../wp-content/themes/tpv/assets/js/function.js?ver=1.0.8" id="theme-js-js"></script>
    <script src="../wp-content/plugins/elementskit-lite/libs/framework/assets/js/frontend-script.js?ver=3.7.9" id="elementskit-framework-js-frontend-js"></script>
    <script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/widget-scripts.js?ver=3.7.9" id="ekit-widget-scripts-js"></script>
    <script src="../wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.35.3" id="elementor-webpack-runtime-js"></script>
    <script src="../wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.35.3" id="elementor-frontend-modules-js"></script>
    <script src="../wp-includes/js/jquery/ui/core.min.js?ver=1.13.3" id="jquery-ui-core-js"></script>
    <script id="elementor-frontend-js-before">var elementorFrontendConfig = {"environmentMode":{"edit":false,"wpPreview":false,"isScriptDebug":false},"i18n":{"shareOnFacebook":"Share on Facebook","shareOnTwitter":"Share on Twitter","pinIt":"Pin it","download":"Download","downloadImage":"Download image","fullscreen":"Fullscreen","zoom":"Zoom","share":"Share","playVideo":"Play Video","previous":"Previous","next":"Next","close":"Close","a11yCarouselPrevSlideMessage":"Previous slide","a11yCarouselNextSlideMessage":"Next slide","a11yCarouselFirstSlideMessage":"This is the first slide","a11yCarouselLastSlideMessage":"This is the last slide","a11yCarouselPaginationBulletMessage":"Go to slide"},"is_rtl":false,"breakpoints":{"xs":0,"sm":480,"md":768,"lg":1025,"xl":1440,"xxl":1600},"responsive":{"breakpoints":{"mobile":{"label":"Mobile Portrait","value":767,"default_value":767,"direction":"max","is_enabled":true},"mobile_extra":{"label":"Mobile Landscape","value":880,"default_value":880,"direction":"max","is_enabled":false},"tablet":{"label":"Tablet Portrait","value":1024,"default_value":1024,"direction":"max","is_enabled":true},"tablet_extra":{"label":"Tablet Landscape","value":1200,"default_value":1200,"direction":"max","is_enabled":false},"laptop":{"label":"Laptop","value":1366,"default_value":1366,"direction":"max","is_enabled":false},"widescreen":{"label":"Widescreen","value":2400,"default_value":2400,"direction":"min","is_enabled":false}},"hasCustomBreakpoints":false},"version":"3.35.3","is_static":false,"experimentalFeatures":{"e_font_icon_svg":true,"additional_custom_breakpoints":true,"container":true,"nested-elements":true,"home_screen":true,"global_classes_should_enforce_capabilities":true,"e_variables":true,"cloud-library":true,"e_opt_in_v4_page":true,"e_components":true,"e_interactions":true,"e_editor_one":true,"import-export-customization":true},"urls":{"assets":"https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-content\/plugins\/elementor\/assets\/","ajaxurl":"https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-admin\/admin-ajax.php","uploadUrl":"https:\/\/demo.awaikenthemes.com\/TPV Construction Services\/wp-content\/uploads"},"nonces":{"floatingButtonsClickTracking":"f2b6ebecb2"},"swiperClass":"swiper","settings":{"page":[],"editorPreferences":[]},"kit":{"body_background_background":"classic","active_breakpoints":["viewport_mobile","viewport_tablet"],"global_image_lightbox":"yes","lightbox_enable_counter":"yes","lightbox_enable_fullscreen":"yes","lightbox_enable_zoom":"yes","lightbox_enable_share":"yes","lightbox_title_src":"title","lightbox_description_src":"description"},"post":{"id":3084,"title":"Contact%20Us%20%E2%80%93%20TPV Construction Services","excerpt":"","featuredImage":false}};</script>
    <script src="../wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.35.3" id="elementor-frontend-js"></script>
    <script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/animate-circle.min.js?ver=3.7.9" id="animate-circle-js"></script>
    <script id="elementskit-elementor-js-extra">var ekit_config = { "ajaxurl": "https://tpvconstruction.com.ng/wp-admin/admin-ajax.php", "nonce": "a394ffb170" };</script>
    <script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/elementor.js?ver=3.7.9" id="elementskit-elementor-js"></script>
    <script src="../wp-content/plugins/elementskit/widgets/init/assets/js/elementor.js?ver=4.2.1" id="elementskit-elementor-pro-js"></script>
    <script id="wp-emoji-settings" type="application/json">{"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/17.0.2\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/17.0.2\/svg\/","svgExt":".svg","source":{"concatemoji":"https:\/\/tpvconstruction.com.ng\/wp-includes\/js\/wp-emoji-release.min.js?ver=6.9.1"}}</script>
    <script type="module">/* auto-generated wp-emoji-loader, kept as is */</script>
    <script src="../../assets/js/theme-panel.js"></script>

    <?php include('../includes/footer.php'); ?>
</body>
</html>