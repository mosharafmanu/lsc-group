<?php
/**
 * CTA Section
 *
 * Centered call-to-action band: heading, optional copy and buttons. Pulls from
 * the Global CTA (Site Settings) by default; switch Content Source to "custom"
 * to override per page. Rendering is delegated to the shared `cta-band` partial
 * (also used directly by the single case study template).
 *
 * @package lsc-group
 */

// Default to the Global CTA (Site Settings); switch to "custom" to override per page.
$content_source = get_sub_field( 'content_source' ) ?: 'global';

if ( 'custom' === $content_source ) {
	$cta = [
		'eyebrow'     => get_sub_field( 'eyebrow' ),
		'title_lines' => get_sub_field( 'title_lines' ),
		'description' => get_sub_field( 'description' ),
		'buttons'     => get_sub_field( 'buttons' ),
		'background'  => get_sub_field( 'background' ) ?: 'dark',
	];
} else {
	$cta = function_exists( 'lsc_get_global_cta' ) ? lsc_get_global_cta() : [];
}

get_template_part( 'template-parts/cta-band', null, $cta );
