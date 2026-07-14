<?php
/**
 * ACF field visibility tweaks.
 *
 * Per-field rules that ACF's built-in conditional logic can't express
 * (e.g. "only show on a specific post type").
 *
 * @package lsc-group
 */

if ( ! function_exists( 'lsc_get_admin_post_type' ) ) {
	/**
	 * Best-effort detection of the post type being edited in wp-admin.
	 *
	 * Works on the post edit screen and during ACF's field-rendering
	 * requests, where get_current_screen() / globals may be unavailable.
	 *
	 * @return string Post type slug, or '' if it can't be determined.
	 */
	function lsc_get_admin_post_type() {
		if ( ! is_admin() ) {
			return (string) get_post_type();
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( $screen && ! empty( $screen->post_type ) ) {
				return $screen->post_type;
			}
		}

		global $post, $typenow;

		if ( ! empty( $typenow ) ) {
			return $typenow;
		}

		if ( $post instanceof WP_Post ) {
			return $post->post_type;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['post'] ) ) {
			return (string) get_post_type( absint( $_GET['post'] ) );
		}

		if ( isset( $_GET['post_type'] ) ) {
			return sanitize_key( wp_unslash( $_GET['post_type'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return '';
	}
}

/**
 * Hide the Page Hero's "Show Product Key Facts Bar" toggle unless the
 * post being edited is a Finance Product. The facts bar pulls from
 * finance_product meta, so the toggle is meaningless elsewhere.
 */
add_filter(
	'acf/prepare_field/key=field_inner_hero_show_facts_bar',
	function ( $field ) {
		if ( 'finance_product' !== lsc_get_admin_post_type() ) {
			return false;
		}

		return $field;
	}
);

/**
 * Surface the video upload spec on every self-hosted video field.
 *
 * WordPress does not compress video on upload — whatever file is chosen is
 * served full-size. Targeting by field name covers all self_host video
 * fields across every layout (and any added later) in one place.
 */
add_filter(
	'acf/load_field/name=video_self_host_file',
	function ( $field ) {
		$spec = __( 'Upload a web-optimised MP4: 1080p max, under ~3&nbsp;MB, H.264, no audio track. Export a compressed / "for web" version — not the original 4K or camera file. Large videos noticeably slow the page (the file is served at full size; WordPress does not compress it).', 'lsc-group' );

		$field['instructions'] = $field['instructions']
			? $field['instructions'] . '<br>' . $spec
			: $spec;

		return $field;
	}
);
