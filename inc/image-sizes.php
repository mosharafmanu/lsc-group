<?php
/**
 * @package lsc-group
 */

add_action( 'after_setup_theme', 'lsc_register_image_sizes' );
function lsc_register_image_sizes() {
	add_image_size( 'lsc-300',  300,  9999, false );
	add_image_size( 'lsc-600',  600,  9999, false );
	add_image_size( 'lsc-900',  900,  9999, false );
	add_image_size( 'lsc-1200', 1200, 9999, false );
	add_image_size( 'lsc-1600', 1600, 9999, false );
}

// ─────────────────────────────────────────────────────────────────
// DISABLE WORDPRESS DEFAULT SIZES
// ─────────────────────────────────────────────────────────────────

add_filter( 'intermediate_image_sizes_advanced', function( $sizes ) {
	unset( $sizes['medium'] );
	unset( $sizes['medium_large'] );
	unset( $sizes['large'] );
	unset( $sizes['1536x1536'] );
	unset( $sizes['2048x2048'] );
	return $sizes;
} );

// ─────────────────────────────────────────────────────────────────
// SRCSET + WEBP
// ─────────────────────────────────────────────────────────────────

add_filter( 'max_srcset_image_width', function() {
	return 3840;
} );

// Scale freshly-uploaded originals down to 2048px max (WP default is 2560).
// Affects new uploads only; keeps the largest served file leaner.
add_filter( 'big_image_size_threshold', function() {
	return 2048;
} );

add_filter( 'mime_types', function( $mimes ) {
	$mimes['webp'] = 'image/webp';
	return $mimes;
} );
