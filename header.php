<?php
/**
 * @package lsc-group
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

<header class="site-header layout-padding">
	<div class="header-main-inner">
		<div class="site-branding">
			<?php lsc_render_site_logo(); ?>
		</div>

		<nav id="primary-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'lsc-group' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'mainMenu',
				'container'      => false,
				'menu_class'     => 'main-menu',
				'fallback_cb'    => false,
				'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			] );
			?>
		</nav>

		<div class="header-actions">
			<?php
			if ( function_exists( 'lsc_render_header_phone' ) ) {
				lsc_render_header_phone();
			}

			if ( function_exists( 'lsc_render_header_button' ) ) {
				lsc_render_header_button( [ 'class' => 'site-btn btn-primary header-cta-btn' ] );
			}
			?>
		</div>

		<button class="mobile-menu-toggle" aria-controls="mobile-navigation" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle menu', 'lsc-group' ); ?>">
			<span class="hamburger-line"></span>
			<span class="hamburger-line"></span>
			<span class="hamburger-line"></span>
		</button>

	</div>
</header>
