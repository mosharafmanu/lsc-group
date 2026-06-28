<?php
/**
 * @package lsc-group
 */

if ( ! defined( 'LSC_GROUP_VERSION' ) ) {
	define( 'LSC_GROUP_VERSION', '1.0.81' );
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
	// ── Core CSS ─────────────────────────────────────────────────
	wp_enqueue_style( 'lsc-group-spacer',         get_template_directory_uri() . '/assets/css/spacer.css',                        array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-utilities',      get_template_directory_uri() . '/assets/css/utilities.css',                     array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-video',          get_template_directory_uri() . '/assets/css/video-behaviors.css',               array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-video-popup',    get_template_directory_uri() . '/assets/css/video-popup.css',                   array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'slick-carousel',               get_template_directory_uri() . '/assets/css/slick.css',                         array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-slick-custom',   get_template_directory_uri() . '/assets/css/lsc-group-slick-custom.css',    array( 'slick-carousel' ), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-design-style',   get_template_directory_uri() . '/assets/css/lsc-group-design-style.css',    array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-form-style',    get_template_directory_uri() . '/assets/css/lsc-group-form.css',             array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-group-style',          get_stylesheet_uri(),                                                           array(), LSC_GROUP_VERSION );

	// ── Developer CSS (Temporary) ────────────────────────────────
	wp_enqueue_style( 'lsc-imran-style',          get_template_directory_uri() . '/imran.css',                                    array(), LSC_GROUP_VERSION );
	wp_enqueue_style( 'lsc-faisal-style',         get_template_directory_uri() . '/faisal.css',                                   array(), LSC_GROUP_VERSION );

	// ── Core JS ──────────────────────────────────────────────────
	wp_enqueue_script( 'jquery-vimeo-player',         get_template_directory_uri() . '/assets/js/jquery.mb.vimeo_player.min.js', array( 'jquery' ), LSC_GROUP_VERSION, true );
	wp_enqueue_script( 'slick-carousel',              get_template_directory_uri() . '/assets/js/slick.js',                       array( 'jquery' ), LSC_GROUP_VERSION, true );
	wp_enqueue_script( 'lsc-group-video-behaviors', get_template_directory_uri() . '/assets/js/video-behaviors.js',           array( 'jquery' ), LSC_GROUP_VERSION, true );
	wp_enqueue_script( 'lsc-group-video-popup',     get_template_directory_uri() . '/assets/js/video-popup.js',               array( 'jquery' ), LSC_GROUP_VERSION, true );
	wp_enqueue_script( 'lsc-group-scripts',         get_template_directory_uri() . '/assets/js/scripts.js',                   array( 'jquery', 'slick-carousel' ), LSC_GROUP_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'lsc_scripts' );


// ─────────────────────────────────────────────────────────────────
// EDITOR — Gutenberg disabled; theme uses ACF Flexible Content
// ─────────────────────────────────────────────────────────────────

add_filter( 'use_block_editor_for_post_type', '__return_false' );
add_filter( 'use_block_editor_for_post',      '__return_false' );

add_action( 'after_setup_theme', function() {
	remove_theme_support( 'widgets-block-editor' );
} );

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