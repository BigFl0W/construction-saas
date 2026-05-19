<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found – TPV Construction and Services LTD</title>
    <meta name="description" content="The page you're looking for doesn't exist. Return to TPV Construction and Services LTD homepage.">
    <link rel='dns-prefetch' href='//fonts.googleapis.com'>
    <style id='wp-img-auto-sizes-contain-inline-css'>img:is([sizes=auto i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px; }</style>
    <link rel='stylesheet' id='elementor-frontend-css' href='wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='ekit-widget-styles-css' href='wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' id='ekit-responsive-css' href='wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' id='TPV-css-variable-css' href='wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='fontawesome-css' href='wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='bootstrap-css' href='wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='TPV-style-css' href='wp-content/themes/tpv/style.css?ver=1.0.8' media='all'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Outfit', sans-serif !important; background: #0f172a; color: #fff; margin: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .error-wrap { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; text-align: center; position: relative; overflow: hidden; }
        .error-wrap::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 50% 50%, rgba(229,54,61,0.12) 0%, transparent 60%); pointer-events: none; }
        .error-inner { position: relative; z-index: 1; max-width: 600px; }
        .error-code { font-size: clamp(6rem, 15vw, 10rem); font-weight: 900; line-height: 1; color: #E5363D; letter-spacing: -5px; margin-bottom: 10px; text-shadow: 0 0 60px rgba(229,54,61,0.3); }
        .error-line { width: 60px; height: 3px; background: #E5363D; margin: 0 auto 24px; border-radius: 2px; }
        .error-inner h1 { font-size: clamp(1.5rem, 4vw, 2.2rem); font-weight: 800; margin-bottom: 14px; color: #fff; letter-spacing: -0.5px; }
        .error-inner p { font-size: 1.05rem; color: #94a3b8; line-height: 1.7; margin-bottom: 32px; }
        .error-btn { display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #E5363D 0%, #c0121a 100%); color: #fff; font-weight: 700; font-size: 15px; padding: 14px 36px; border-radius: 50px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 10px 30px -5px rgba(229,54,61,0.4); text-transform: uppercase; letter-spacing: 1px; }
        .error-btn:hover { transform: translateY(-3px); box-shadow: 0 20px 40px -5px rgba(229,54,61,0.5); color: #fff; }
        .error-links { margin-top: 30px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .error-links a { color: #94a3b8; font-size: 13px; text-decoration: none; transition: color 0.2s; }
        .error-links a:hover { color: #E5363D; }
        .error-footer { text-align: center; padding: 20px; font-size: 12px; color: #475569; border-top: 1px solid rgba(255,255,255,0.06); }
    </style>
</head>
<body>
    <div class="error-wrap">
        <div class="error-inner">
            <div class="error-code">404</div>
            <div class="error-line"></div>
            <h1>Page Not Found</h1>
            <p>The page you're looking for doesn't exist, was removed, or the link you followed may be incorrect.</p>
            <a href="<?php echo defined('SITE_URL') ? SITE_URL : './'; ?>" class="error-btn">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <div class="error-links">
                <a href="./about-us/">About Us</a>
                <a href="./services/">Services</a>
                <a href="./projects/">Projects</a>
                <a href="./contact-us/">Contact</a>
                <a href="./blog/">Blog</a>
            </div>
        </div>
    </div>
    <div class="error-footer">&copy; <?php echo date('Y'); ?> TPV Construction and Services LTD. All Rights Reserved.</div>
</body>
</html>
