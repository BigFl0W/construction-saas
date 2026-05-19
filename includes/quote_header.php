<?php
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isHome     = (preg_match('/^\/(tpv-new-website\/)?(index\.php)?$/', $uri)) ? 'current-menu-ancestor current-menu-parent' : '';
$isAbout    = (strpos($uri, '/about-us')   !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isContact  = (strpos($uri, '/contact-us') !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isBlog     = (strpos($uri, '/blog')       !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isServices = (strpos($uri, '/services')   !== false) ? 'current-menu-ancestor current-menu-parent' : '';
$isQuote    = (strpos($uri, '/quote')      !== false) ? 'current-menu-ancestor current-menu-parent' : '';
?>
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
							data-settings="{&quot;align&quot;:&quot;left&quot;,&quot;ekit_we_effect_on&quot;:&quot;none&quot;}"
							data-widget_type="TPV Construction Services-site-logo.default">
							<div class="elementor-widget-container">
								<div class="ata-site-logo">
									<a data-elementor-open-lightbox="" class='elementor-clickable'
										href="../">
										<div class="ata-site-logo-set">
											<div class="ata-site-logo-container">
												<img class="ata-site-logo-img elementor-animation-"
													src="../wp-content/uploads/2024/06/logo.png" alt="TPV Construction and Services LTD" width="150" height="50" />
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
												data-vertical-menu="750px"><a href="../"
													class="ekit-menu-nav-link">Home</a>
											</li>
											<li id="menu-item-3045"
												class="menu-item menu-item-type-post_type menu-item-object-page <?php echo $isAbout; ?> menu-item-3045 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="../about-us/"
													class="ekit-menu-nav-link">About Us</a></li>

											<li id="menu-item-16"
												class="menu-item menu-item-type-post_type menu-item-object-page <?php echo $isContact; ?> menu-item-16 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="../contact-us/"
													class="ekit-menu-nav-link">Contact Us</a></li>

											<li id="menu-item-16_blog"
												class="menu-item menu-item-type-post_type menu-item-object-page <?php echo $isBlog; ?> menu-item-16 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="../blog/"
													class="ekit-menu-nav-link">Blog</a></li>
											<li id="menu-item-3343"
												class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children <?php echo $isServices; ?> menu-item-3343 nav-item elementskit-dropdown-has relative_position elementskit-dropdown-menu-default_width elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="../services/"
													class="ekit-menu-nav-link ekit-menu-dropdown-toggle">Services<i
														class="icon icon-down-arrow1 elementskit-submenu-indicator"></i></a>
												<ul class="elementskit-dropdown elementskit-submenu-panel">
													<li id="menu-item-5670"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5670 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="../services/building-construction/"
															class=" dropdown-item">Building Construction</a>
													<li id="menu-item-5671"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5671 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="../services/architecture-design/"
															class=" dropdown-item">Architecture Design</a>
													<li id="menu-item-5672"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5672 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="../services/building-renovation/"
															class=" dropdown-item">Building Renovation</a>
													<li id="menu-item-5673"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5673 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="../services/flooring-roofing/"
															class=" dropdown-item">Flooring &#038; Roofing</a>
													<li id="menu-item-5674"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5674 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="../services/building-maintenance/"
															class=" dropdown-item">Building Maintenance</a>
													<li id="menu-item-5675"
														class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5675 nav-item elementskit-mobile-builder-content"
														data-vertical-menu="750px"><a
															href="../services/project-management/"
															class=" dropdown-item">Project Management</a></li>
												</ul>
											</li>

											<!-- Get Free Quote — shown as active in mobile menu when on the quote page -->
											<li id="menu-item-4598"
												class="mobile-menu menu-item menu-item-type-post_type menu-item-object-page <?php echo $isQuote; ?> menu-item-4598 nav-item elementskit-mobile-builder-content"
												data-vertical-menu="750px"><a href="../quote/"
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
									<!-- On the quote page itself, show the button as active/filled -->
									<a class="elementor-button elementor-button-link elementor-size-sm<?php echo $isQuote ? ' tpv-quote-btn-active' : ''; ?>"
										href="../quote/" aria-current="<?php echo $isQuote ? 'page' : 'false'; ?>">
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

	<!-- Quote-page CTA button active style -->
	<?php if ($isQuote): ?>
	<style>
		.tpv-quote-btn-active {
			background: #E5363D !important;
			color: #ffffff !important;
			border-color: #E5363D !important;
			box-shadow: 0 6px 20px rgba(229,54,61,0.35) !important;
		}
		.tpv-quote-btn-active:hover {
			background: #c0121a !important;
			box-shadow: 0 10px 28px rgba(229,54,61,0.45) !important;
		}
	</style>
	<?php endif; ?>
