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

<a class="skip-link sr-only" href="#primary"><?php esc_html_e( 'Skip to content', 'lsc-group' ); ?></a>

<div id="page" class="site">

<header class="site-header">
    <div class="lsc-container layout-padding">
        <div class="header-main-inner">
		<div class="site-branding">
			<?php lsc_render_site_logo(); ?>
		</div>

		  <div class="header-right">
                    <nav id="primary-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'lsc-group' ); ?>">
                        <h2 class="sr-only"><?php esc_html_e( 'Main navigation', 'lsc-group' ); ?></h2>
                        <?php
                        wp_nav_menu( [
                            'theme_location' => 'mainMenu',
                            'container'      => false,
                            'menu_class'     => 'main-menu',
                            'fallback_cb'    => false,
                            'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                            'walker'         => class_exists( 'LSC_Mega_Menu_Walker' ) ? new LSC_Mega_Menu_Walker() : '',
                        ] );
                        ?>
                    </nav>

                    <div class="header-actions">
                        <?php
                        if ( function_exists( 'lsc_render_header_phone' ) ) {
                            lsc_render_header_phone();
                        }

                        if ( function_exists( 'lsc_render_header_button' ) ) {
                            ?>
                            <div class="header-cta-group">
                                <?php
                                // Secondary / outline CTA (e.g. "Become A Broker") — renders only if set.
                                lsc_render_header_button( [
                                    'field' => 'header_button_secondary',
                                    'class' => 'site-btn btn-outline header-cta-btn header-cta-btn--secondary',
                                ] );

                                // Primary / solid CTA (e.g. "Quick Quote").
                                lsc_render_header_button( [
                                    'field' => 'header_button',
                                    'class' => 'site-btn btn-primary header-cta-btn header-cta-btn--primary',
                                ] );
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
            </div>

		<button class="mobile-menu-toggle d-none" aria-controls="mobile-navigation" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle menu', 'lsc-group' ); ?>">
			<span class="hamburger-line"></span>
			<span class="hamburger-line"></span>
			<span class="hamburger-line"></span>
		</button>

	  </div>
    </div>
</header>
