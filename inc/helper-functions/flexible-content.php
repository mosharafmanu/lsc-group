<?php
/**
 * @package lsc-group
 */

if ( ! function_exists( 'lsc_flexible_content' ) ) {
	function lsc_flexible_content( $field_name = 'cms', $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! have_rows( $field_name, $post_id ) ) {
			return;
		}

		// Track previous layout and section index for conditional styling
		$previous_layout = '';
		$section_index   = 0;

		while ( have_rows( $field_name, $post_id ) ) {
			the_row();

			$layout = get_row_layout();

			if ( empty( $layout ) ) {
				continue;
			}

			$GLOBALS['lsc_previous_layout'] = $previous_layout;
			$GLOBALS['lsc_section_index']   = $section_index;

			$template_path = 'template-parts/sections/' . $layout;
			$template_file = locate_template( $template_path . '.php' );

			if ( $template_file ) {
				get_template_part( $template_path );
			} elseif ( current_user_can( 'manage_options' ) && WP_DEBUG ) {
				echo '<!-- Missing template: ' . esc_html( $template_path ) . '.php -->';
			}

			$previous_layout = $layout;
			$section_index++;
		}

		$GLOBALS['lsc_last_layout'] = $previous_layout;

		unset( $GLOBALS['lsc_previous_layout'] );
		unset( $GLOBALS['lsc_section_index'] );
	}
}

if ( ! function_exists( 'lsc_get_last_flexible_layout' ) ) {
	function lsc_get_last_flexible_layout( $field_name = 'cms', $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! have_rows( $field_name, $post_id ) ) {
			return false;
		}

		$last_layout = '';

		while ( have_rows( $field_name, $post_id ) ) {
			the_row();
			$last_layout = get_row_layout();
		}

		reset_rows();

		return $last_layout;
	}
}

if ( ! function_exists( 'lsc_get_first_flexible_layout' ) ) {
	function lsc_get_first_flexible_layout( $field_name = 'cms', $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! have_rows( $field_name, $post_id ) ) {
			return false;
		}

		$first_layout = '';

		if ( have_rows( $field_name, $post_id ) ) {
			the_row();
			$first_layout = get_row_layout();
		}

		reset_rows();

		return $first_layout;
	}
}

if ( ! function_exists( 'lsc_page_needs_slick' ) ) {
	/**
	 * Whether the page being rendered actually contains a Slick carousel.
	 *
	 * Only three flexible-content layouts render a carousel, plus the single
	 * case-study template (its "Related Case Studies" block). Everything else
	 * ships no carousel, so Slick's CSS + JS can be skipped entirely there.
	 *
	 * This is a scan of the queried page's real `cms` layouts — not a guess —
	 * so it stays correct no matter which section lands on which page. The
	 * carousels are mobile/tablet-only, but the device is unknown server-side,
	 * so any page that *has* a carousel layout loads Slick regardless of width.
	 *
	 * @return bool
	 */
	function lsc_page_needs_slick() {
		// Single case study always renders the related-case-studies carousel.
		if ( is_singular( 'case_study' ) ) {
			return true;
		}

		if ( ! function_exists( 'have_rows' ) ) {
			return false;
		}

		$post_id = get_queried_object_id();

		if ( ! $post_id || ! have_rows( 'cms', $post_id ) ) {
			return false;
		}

		$carousel_layouts = [ 'case_studies_grid', 'finance_products_grid', 'testimonials_section' ];

		$needs = false;
		while ( have_rows( 'cms', $post_id ) ) {
			the_row();
			if ( in_array( get_row_layout(), $carousel_layouts, true ) ) {
				$needs = true;
				break;
			}
		}
		reset_rows();

		return $needs;
	}
}

if ( ! function_exists( 'lsc_has_hero_first_section' ) ) {
	function lsc_has_hero_first_section( $field_name = 'cms', $post_id = null ) {
		// Blog page uses inner_hero from options, not flexible content
		if ( is_home() ) {
			return true;
		}

		$first_layout = lsc_get_first_flexible_layout( $field_name, $post_id );

		return in_array( $first_layout, [ 'hero_section', 'inner_hero' ], true );
	}
}
