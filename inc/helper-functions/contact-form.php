<?php
/**
 * Contact Form 7 tweaks
 *
 * Converts the CF7 submit control from an <input type="submit"> into a
 * <button type="submit"> so it can carry an icon beside the label (an input is
 * a replaced element and cannot contain child SVG / pseudo-content). The base
 * `button[type="submit"]` styling already matches the input, so the look is
 * preserved; faisal.css adds the gap + icon sizing.
 *
 * @package lsc-group
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the butterfly/send icon SVG markup (theme asset).
 *
 * @return string
 */
if ( ! function_exists( 'lsc_get_submit_icon_svg' ) ) {
	function lsc_get_submit_icon_svg() {
		$path = get_theme_file_path( '/assets/svgs/butter-fly.php' );

		if ( ! is_readable( $path ) ) {
			return '';
		}

		ob_start();
		include $path;
		return trim( ob_get_clean() );
	}
}

/**
 * Rewrite CF7 submit inputs as buttons with the icon appended after the label.
 *
 * @param string $html The rendered CF7 form HTML.
 * @return string
 */
if ( ! function_exists( 'lsc_cf7_submit_button_icon' ) ) {
	function lsc_cf7_submit_button_icon( $html ) {
		if ( false === strpos( $html, 'type="submit"' ) ) {
			return $html;
		}

		return preg_replace_callback(
			'/<input\b([^>]*?)\/?>/i',
			function ( $matches ) {
				$attrs = $matches[1];

				// Only transform the submit control; leave every other input as-is.
				if ( ! preg_match( '/type\s*=\s*["\']submit["\']/i', $attrs ) ) {
					return $matches[0];
				}

				$label = '';
				if ( preg_match( '/value\s*=\s*["\'](.*?)["\']/i', $attrs, $value_match ) ) {
					$label = $value_match[1];
				}

				// Drop type + value; keep the remaining attributes (class, id, …).
				$attrs = preg_replace( '/\s*type\s*=\s*["\'][^"\']*["\']/i', '', $attrs );
				$attrs = preg_replace( '/\s*value\s*=\s*["\'][^"\']*["\']/i', '', $attrs );

				$icon = lsc_get_submit_icon_svg();

				return '<button type="submit"' . $attrs . '>'
					. ( $icon ? '<span class="wpcf7-submit__icon" aria-hidden="true">' . $icon . '</span>' : '' )
					. '<span class="wpcf7-submit__text">' . esc_html( $label ) . '</span>'
					. '</button>';
			},
			$html
		);
	}
}
add_filter( 'wpcf7_form_elements', 'lsc_cf7_submit_button_icon' );
