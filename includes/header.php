<?php
if (!function_exists('tpv_setting_asset_url')) {
    require_once dirname(__DIR__) . '/config/config.php';
}

$siteLogoUrl = tpv_setting_asset_url('site_logo', 'wp-content/uploads/2024/06/logo.png');
$siteHomeUrl = SITE_URL;
$siteAboutUrl = SITE_URL . 'about-us/';
$siteContactUrl = SITE_URL . 'contact-us/';
$siteBlogUrl = SITE_URL . 'blog/';
$siteServicesUrl = SITE_URL . 'services/';
$siteQuoteUrl = SITE_URL . 'quote/';
$serviceMenuLinks = [
    ['label' => 'Building Construction', 'href' => SITE_URL . 'services/building-construction/'],
    ['label' => 'Architecture Design', 'href' => SITE_URL . 'services/architecture-design/'],
    ['label' => 'Building Renovation', 'href' => SITE_URL . 'services/building-renovation/'],
    ['label' => 'Interior / Exterior', 'href' => SITE_URL . 'services/interior-exterior/'],
    ['label' => 'Project Management', 'href' => SITE_URL . 'services/project-management/'],
    ['label' => 'Steel & Fabrication', 'href' => SITE_URL . 'services/steel-and-fabrication/'],
];
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isHome     = (preg_match('/^\/(tpv-new-website\/)?(index\.php)?$/', $uri)) ? 'current-menu-ancestor current-menu-parent' : '';
$isAbout    = (strpos($uri, '/about-us')   !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isContact  = (strpos($uri, '/contact-us') !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isBlog     = (strpos($uri, '/blog')       !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isServices = (strpos($uri, '/services')   !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isQuote    = (strpos($uri, '/quote')      !== false) ? 'current-menu-ancestor current-menu-parent' : '';
?>
    <style>
        .tpv-header-logo img.ata-site-logo-img {
            width: 112px !important;
            height: auto !important;
            max-width: 112px !important;
        }

        .tpv-header-cta.elementor-button {
            min-height: 52px;
            padding: 0 26px !important;
            border-radius: 18px !important;
            font-size: 16px !important;
            line-height: 1.2 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .tpv-header-cta .elementor-button-text {
            white-space: nowrap;
        }
    </style>
	<div class="ekit-template-content-markup ekit-template-content-header ekit-template-content-theme-support">
		<div data-elementor-type="wp-post" data-elementor-id="225" class="elementor elementor-225">
			<div class="elementor-element elementor-element-3c0e001 e-con-full e-flex e-con e-parent" data-id="3c0e001"
				data-element_type="container" data-e-type="container">
				<div class="elementor-element elementor-element-159e7cf e-con-full e-flex e-con e-child"
					data-id="159e7cf" data-element_type="container" data-e-type="container">
					<div class="elementor-element elementor-element-08aa86c e-con-full e-flex e-con e-child"
						data-id="08aa86c" data-element_type="container" data-e-type="container">
						<div class="elementor-element elementor-element-d7183c8 tpv-header-logo elementor-widget elementor-widget-TPV Construction Services-site-logo"
							data-id="d7183c8" data-element_type="widget" data-e-type="widget"
							data-settings="{&quot;align&quot;:&quot;left&quot;,&quot;width&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;width_tablet&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;width_mobile&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;space&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;space_tablet&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;space_mobile&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;image_border_radius&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;image_border_radius_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;image_border_radius_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_padding&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_padding_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_padding_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;caption_space&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:0,&quot;sizes&quot;:[]},&quot;caption_space_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;caption_space_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="TPV Construction Services-site-logo.default">
							<div class="elementor-widget-container">
								<div class="ata-site-logo">
									<a data-elementor-open-lightbox="" class='elementor-clickable'
										href="<?php echo htmlspecialchars($siteHomeUrl); ?>">
										<div class="ata-site-logo-set">
											<div class="ata-site-logo-container">
												<img class="ata-site-logo-img elementor-animation-"
													src="<?php echo htmlspecialchars($siteLogoUrl); ?>" alt="default-logo" width="112" height="38" />
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
												class="menu-item menu-item-type-custom menu-item-object-custom <?php echo $isHome; ?> menu-item-has-children menu-item-5912 nav-item elementskit-dropdown-has relative_position elementskit-dropdown-menu-default_width elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="<?php echo htmlspecialchars($siteHomeUrl); ?>"
													class="ekit-menu-nav-link">Home</a>

											</li>
											<li id="menu-item-3045"
												class="menu-item menu-item-type-post_type menu-item-object-page <?php echo $isAbout; ?> menu-item-3045 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="<?php echo htmlspecialchars($siteAboutUrl); ?>"
													class="ekit-menu-nav-link">About Us</a></li>

											<li id="menu-item-16"
												class="menu-item menu-item-type-post_type menu-item-object-page <?php echo $isContact; ?> menu-item-16 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="<?php echo htmlspecialchars($siteContactUrl); ?>"
													class="ekit-menu-nav-link">Contact Us</a></li>

											<li id="menu-item-16_blog"
												class="menu-item menu-item-type-post_type menu-item-object-page <?php echo $isBlog; ?> menu-item-16 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="<?php echo htmlspecialchars($siteBlogUrl); ?>"
													class="ekit-menu-nav-link">Blog</a></li>
											<li id="menu-item-3343"
												class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children <?php echo $isServices; ?> menu-item-3343 nav-item elementskit-dropdown-has relative_position elementskit-dropdown-menu-default_width elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="<?php echo htmlspecialchars($siteServicesUrl); ?>"
													class="ekit-menu-nav-link ekit-menu-dropdown-toggle">Services<i
														class="icon icon-down-arrow1 elementskit-submenu-indicator"></i></a>
												<ul class="elementskit-dropdown elementskit-submenu-panel">
													<?php foreach ($serviceMenuLinks as $serviceMenuIndex => $serviceMenuLink): ?>
													<li id="menu-item-service-<?php echo $serviceMenuIndex + 1; ?>"
														class="menu-item menu-item-type-post_type menu-item-object-page nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="<?php echo htmlspecialchars($serviceMenuLink['href']); ?>"
															class="dropdown-item"><?php echo htmlspecialchars($serviceMenuLink['label']); ?></a></li>
													<?php endforeach; ?>
												</ul>
											</li>


											<li id="menu-item-4598"
												class="mobile-menu menu-item menu-item-type-post_type menu-item-object-page <?php echo $isQuote; ?> menu-item-4598 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="<?php echo htmlspecialchars($siteQuoteUrl); ?>"
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
									<a class="elementor-button elementor-button-link elementor-size-sm tpv-header-cta<?php echo $isQuote ? ' tpv-quote-btn-active' : ''; ?>"
										href="<?php echo htmlspecialchars($siteQuoteUrl); ?>">
										<span class="elementor-button-content-wrapper">
											<span class="elementor-button-text">Get Free Quote</span>
										</span>
									</a>
									<?php if ($isQuote): ?>
									<style>
										.tpv-quote-btn-active.elementor-button {
											background: #E5363D !important;
											color: #ffffff !important;
											border-color: #E5363D !important;
											box-shadow: 0 6px 20px rgba(229,54,61,0.35) !important;
										}
									</style>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
