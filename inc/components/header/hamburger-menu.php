<?php
/**
 * Mobile Navigation Component
 * @package lsc-group
 */

if ( ! function_exists( 'lsc_render_mobile_navigation' ) ) {
	function lsc_render_mobile_navigation() {
		// The bar is just logo + hamburger; both CTAs and the phone live in the drawer.
		$primary_cta   = function_exists( 'lsc_get_header_button' ) ? lsc_get_header_button() : false;
		$secondary_cta = function_exists( 'lsc_get_header_button' ) ? lsc_get_header_button( 'header_button_secondary' ) : false;
		$header_phone  = function_exists( 'lsc_get_header_phone' ) ? lsc_get_header_phone() : false;
		?>

		<div class="mobile-menu-overlay" aria-hidden="true"></div>

		<nav id="mobile-navigation" class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile navigation', 'lsc-group' ); ?>" aria-hidden="true">
			<h2 class="sr-only"><?php esc_html_e( 'Mobile navigation', 'lsc-group' ); ?></h2>
			<div class="mobile-nav-inner">

				<div class="mobile-nav-header">
					<div class="site-branding mobile-nav-logo">
						<?php lsc_render_site_logo(); ?>
					</div>
					<button class="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'lsc-group' ); ?>">
						<span></span>
						<span></span>
					</button>
				</div>

				<div class="mobile-nav-menu">
					<?php
					wp_nav_menu( [
						'theme_location' => 'mainMenu',
						'container'      => false,
						'menu_class'     => 'mobile-menu',
						'fallback_cb'    => false,
						'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'walker'         => class_exists( 'LSC_Mobile_Mega_Walker' ) ? new LSC_Mobile_Mega_Walker() : '',
					] );
					?>
				</div>

				<?php if ( $header_phone || $primary_cta || $secondary_cta ) : ?>
				<div class="mobile-nav-cta">
					<?php
					if ( $primary_cta && function_exists( 'lsc_render_header_button' ) ) {
						lsc_render_header_button( [
							'field' => 'header_button',
							'class' => 'site-btn btn-primary mobile-cta-btn',
						] );
					}

					if ( $secondary_cta && function_exists( 'lsc_render_header_button' ) ) {
						lsc_render_header_button( [
							'field' => 'header_button_secondary',
							'class' => 'site-btn btn-outline mobile-cta-btn',
						] );
					}

					if ( $header_phone && function_exists( 'lsc_render_header_phone' ) ) {
						lsc_render_header_phone( [ 'class' => 'mobile-nav-phone' ] );
					}
					?>
				</div>
				<?php endif; ?>

			</div>
		</nav>

		<?php
	}
}
