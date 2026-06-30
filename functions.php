<?php
/**
 * @package lsc-group
 */

if ( ! defined( 'LSC_GROUP_VERSION' ) ) {
	define( 'LSC_GROUP_VERSION', '1.0.129' );
}


// ─────────────────────────────────────────────────────────────────
// ACF DEPENDENCY CHECK
// This theme requires Advanced Custom Fields (free or pro).
// Without it the dispatcher, section builder, and all settings
// helpers are non-functional. Fail loudly rather than silently.
// ─────────────────────────────────────────────────────────────────

add_action( 'admin_notices', function () {
	if ( class_exists( 'ACF' ) ) {
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$install_url = admin_url( 'plugin-install.php?s=advanced+custom+fields&tab=search&type=term' );
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: %s: URL to plugin installer */
					__( '<strong>LSC Group requires Advanced Custom Fields.</strong> The page builder, section templates, and all site settings depend on it. <a href="%s">Install ACF Free &rarr;</a>', 'lsc-group' ),
					[
						'strong' => [],
						'a'      => [ 'href' => [] ],
					]
				),
				esc_url( $install_url )
			);
			?>
		</p>
	</div>
	<?php
} );


// ─────────────────────────────────────────────────────────────────
// THEME SETUP
// ─────────────────────────────────────────────────────────────────

function lsc_setup() {
	load_theme_textdomain( 'lsc-group', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'mainMenu'   => esc_html__( 'Main Menu',   'lsc-group' ),
		'footerMenu' => esc_html__( 'Footer Menu', 'lsc-group' ),
	) );

	// Controls max width for oEmbed — should match --lsc-container-max.
	$GLOBALS['content_width'] = 1440;
}
add_action( 'after_setup_theme', 'lsc_setup' );


// ─────────────────────────────────────────────────────────────────
// WIDGET AREAS
// ─────────────────────────────────────────────────────────────────

function lsc_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'lsc-group' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'lsc_widgets_init' );


// ─────────────────────────────────────────────────────────────────
// SCRIPTS & STYLES
// ─────────────────────────────────────────────────────────────────

function lsc_scripts() {
	// Slick (carousel) ships CSS + JS only on pages that actually render a
	// carousel — see lsc_page_needs_slick(). The enqueues stay inline below
	// (rather than register-then-enqueue) so the cascade/load order is
	// identical to before on the pages that do need them.
	$lsc_needs_slick = function_exists( 'lsc_page_needs_slick' ) && lsc_page_needs_slick();

	// ── Core CSS ─────────────────────────────────────────────────
	wp_enqueue_style( 'lsc-group-spacer',         get_template_directory_uri() . '/assets/css/spacer.css',                        array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-utilities',      get_template_directory_uri() . '/assets/css/utilities.css',                     array(), LSC_GROUP_VERSION );
	// Video CSS is registered, not enqueued — lsc_render_video() pulls it in at
	// render time (same as the video JS), so pages without a video ship neither.
	// video-behaviors loads for any video; video-popup only for onclick-popup.
	wp_register_style( 'lsc-group-video',         get_template_directory_uri() . '/assets/css/video-behaviors.css',               array(), LSC_GROUP_VERSION );
	wp_register_style( 'lsc-group-video-popup',   get_template_directory_uri() . '/assets/css/video-popup.css',                   array(), LSC_GROUP_VERSION );
	if ( $lsc_needs_slick ) {
		wp_enqueue_style( 'slick-carousel',           get_template_directory_uri() . '/assets/css/slick.css',                         array(), LSC_GROUP_VERSION );
		wp_enqueue_style( 'lsc-group-slick-custom',   get_template_directory_uri() . '/assets/css/lsc-group-slick-custom.css',    array( 'slick-carousel' ), LSC_GROUP_VERSION );
	}
	wp_enqueue_style( 'lsc-group-design-style',   get_template_directory_uri() . '/assets/css/lsc-group-design-style.css',    array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-form-style',    get_template_directory_uri() . '/assets/css/lsc-group-form.css',             array(), LSC_GROUP_VERSION );
	// style.css now carries all design CSS — the former imran.css + faisal.css
	// were consolidated into it (header → sections → blog → footer order).
	wp_enqueue_style( 'lsc-group-style',          get_stylesheet_uri(),                                                           array(), LSC_GROUP_VERSION );

	// ── Core JS ──────────────────────────────────────────────────
	// scripts.js no longer hard-depends on Slick — its carousel inits self-guard
	// when $.fn.slick is absent — so it loads everywhere while Slick stays conditional.
	if ( $lsc_needs_slick ) {
		wp_enqueue_script( 'slick-carousel',          get_template_directory_uri() . '/assets/js/slick.js',                       array( 'jquery' ), LSC_GROUP_VERSION, true );
	}
	wp_enqueue_script( 'lsc-group-scripts',         get_template_directory_uri() . '/assets/js/scripts.js',                   array( 'jquery' ), LSC_GROUP_VERSION, true );

	// Video scripts are registered, not enqueued — lsc_render_video() pulls in
	// only what a rendered video actually needs, so pages without video ship none.
	wp_register_script( 'jquery-vimeo-player',      get_template_directory_uri() . '/assets/js/jquery.mb.vimeo_player.min.js', array( 'jquery' ), LSC_GROUP_VERSION, true );
	wp_register_script( 'lsc-group-video-behaviors', get_template_directory_uri() . '/assets/js/video-behaviors.js',           array( 'jquery' ), LSC_GROUP_VERSION, true );
	wp_register_script( 'lsc-group-video-popup',    get_template_directory_uri() . '/assets/js/video-popup.js',               array( 'jquery' ), LSC_GROUP_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'lsc_scripts' );


// ─────────────────────────────────────────────────────────────────
// Move jQuery to the footer so it stops blocking the first paint.
// WordPress loads jQuery render-blocking in <head> by default; nothing
// above the footer needs it (the hero rotator is vanilla JS, and every
// jQuery-dependent script — scripts.js, slick, CF7 — already loads in the
// footer, so dependency order is preserved). Skips admin + the login page.
// ─────────────────────────────────────────────────────────────────

function lsc_jquery_to_footer() {
	if ( is_admin() ) {
		return;
	}

	$scripts = wp_scripts();
	foreach ( [ 'jquery', 'jquery-core', 'jquery-migrate' ] as $handle ) {
		if ( isset( $scripts->registered[ $handle ] ) ) {
			$scripts->add_data( $handle, 'group', 1 );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'lsc_jquery_to_footer', 100 );


// ─────────────────────────────────────────────────────────────────
// CONTACT FORM 7 — load its CSS/JS only where a form renders
// CF7 enqueues site-wide by default; the form only appears in the
// contact_section / contact_panel layouts (a few pages). Gate its
// own load toggles so every other page ships none of it.
// ─────────────────────────────────────────────────────────────────

function lsc_cf7_conditional_assets( $load ) {
	if ( ! function_exists( 'lsc_page_needs_contact_form' ) ) {
		return $load;
	}
	return lsc_page_needs_contact_form() ? $load : false;
}
add_filter( 'wpcf7_load_js',  'lsc_cf7_conditional_assets' );
add_filter( 'wpcf7_load_css', 'lsc_cf7_conditional_assets' );


// ─────────────────────────────────────────────────────────────────
// EDITOR — Gutenberg disabled; theme uses ACF Flexible Content
// ─────────────────────────────────────────────────────────────────

add_filter( 'use_block_editor_for_post_type', '__return_false' );
add_filter( 'use_block_editor_for_post',      '__return_false' );

add_action( 'after_setup_theme', function() {
	remove_theme_support( 'widgets-block-editor' );
} );

// Route any core-block CSS through the single 'wp-block-library' handle (which
// we dequeue below) instead of per-block stylesheets — so stray block content
// ships zero block CSS. The theme uses ACF, not blocks, so this is belt-and-braces.
add_filter( 'should_load_separate_core_block_assets', '__return_false' );

add_action( 'wp_enqueue_scripts', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 100 );

add_action( 'admin_enqueue_scripts', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
}, 100 );


// ─────────────────────────────────────────────────────────────────
// DISABLE EMOJI — the wp-head emoji detection script + its inline
// <style> block load on every page; modern browsers render emoji
// natively, so this is dead weight.
// ─────────────────────────────────────────────────────────────────

add_action( 'init', function() {
	remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles',     'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles',  'print_emoji_styles' );
	remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
	remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );

	add_filter( 'tiny_mce_plugins', function( $plugins ) {
		return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
	} );

	// Drop the emoji DNS-prefetch hint too.
	add_filter( 'wp_resource_hints', function( $urls, $relation ) {
		if ( 'dns-prefetch' === $relation ) {
			$urls = array_filter( $urls, function( $url ) {
				return false === strpos( $url, 's.w.org/images/core/emoji/' );
			} );
		}
		return $urls;
	}, 10, 2 );
} );


// ─────────────────────────────────────────────────────────────────
// ACF JSON SYNC
// ─────────────────────────────────────────────────────────────────

add_filter( 'acf/settings/save_json', function( $path ) {
	return get_stylesheet_directory() . '/acf-json';
} );

add_filter( 'acf/settings/load_json', function( $paths ) {
	unset( $paths[0] );
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
} );


// ─────────────────────────────────────────────────────────────────
// CORE INCLUDES
// ─────────────────────────────────────────────────────────────────

require get_template_directory() . '/inc/image-sizes.php';
require get_template_directory() . '/inc/post-types.php';

foreach ( glob( get_template_directory() . '/inc/components/*/*.php' ) as $file ) {
	require $file;
}

foreach ( glob( get_template_directory() . '/inc/helper-functions/*.php' ) as $file ) {
	require $file;
}


// ─────────────────────────────────────────────────────────────────
// WOOCOMMERCE
// Self-contained, optional module — see inc/woocommerce/woocommerce-setup.php.
// Guarded with file_exists() so projects that don't need WooCommerce can
// delete the whole module (that file, woocommerce/, assets/{css,js}/woocommerce/,
// .ai/WOOCOMMERCE.md) without touching this require.
// ─────────────────────────────────────────────────────────────────

$lsc_woocommerce_setup = get_template_directory() . '/inc/woocommerce/woocommerce-setup.php';
if ( file_exists( $lsc_woocommerce_setup ) ) {
	require $lsc_woocommerce_setup;
}


// ─────────────────────────────────────────────────────────────────
// POST CONTENT CLEANUP
// ─────────────────────────────────────────────────────────────────

add_filter( 'the_content', function( $content ) {
	if ( is_admin() || 'post' !== get_post_type() ) {
		return $content;
	}
	return preg_replace( '/(<[^>]+) style=".*?"/i', '$1', $content );
}, 20 );

function menu_width_admin_styles() {
  echo '<style>
	.menu-item-bar .menu-item-handle {
	  max-width: 70% !important;
	}
	.menu-item-settings {
	  max-width: 70% !important;
	}
  </style>';
}
add_action('admin_head', 'menu_width_admin_styles');