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

		// Pre-compute the last layout name so templates can detect if they are last
		$last_layout_name = '';
		while ( have_rows( $field_name, $post_id ) ) {
			the_row();
			$last_layout_name = get_row_layout();
		}
		reset_rows();
		$GLOBALS['lsc_last_layout'] = $last_layout_name;

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

		unset( $GLOBALS['lsc_previous_layout'] );
		unset( $GLOBALS['lsc_section_index'] );
		unset( $GLOBALS['lsc_last_layout'] );
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

if ( ! function_exists( 'lsc_queried_cms_has_layout' ) ) {
	/**
	 * Whether the queried page's `cms` flexible content contains any of the
	 * given layouts. A real scan of the page's rows — not a guess — so it stays
	 * correct no matter which section lands on which page. Used to scope
	 * per-feature assets (Slick, Contact Form 7) to the pages that need them.
	 *
	 * @param string[] $layouts Layout names to look for.
	 * @param int|null $post_id Defaults to the queried object.
	 * @return bool
	 */
	function lsc_queried_cms_has_layout( $layouts, $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_queried_object_id();
		}

		if ( ! $post_id || ! have_rows( 'cms', $post_id ) ) {
			return false;
		}

		$layouts = (array) $layouts;
		$found   = false;

		while ( have_rows( 'cms', $post_id ) ) {
			the_row();
			if ( in_array( get_row_layout(), $layouts, true ) ) {
				$found = true;
				break;
			}
		}
		reset_rows();

		return $found;
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
	 * @return bool
	 */
	function lsc_page_needs_slick() {
		// Single case study always renders the related-case-studies carousel.
		if ( is_singular( 'case_study' ) ) {
			return true;
		}

		return lsc_queried_cms_has_layout( [ 'case_studies_grid', 'testimonials_section' ] );
	}
}

if ( ! function_exists( 'lsc_page_needs_contact_form' ) ) {
	/**
	 * Whether the page being rendered actually outputs a Contact Form 7 form.
	 *
	 * The form is rendered via do_shortcode() inside the contact_section /
	 * contact_panel layouts. CF7 otherwise enqueues its CSS + JS on every page;
	 * gating on this lets us skip those assets everywhere a form is absent.
	 * Also honours a raw [contact-form-7] shortcode dropped into post content.
	 *
	 * @return bool
	 */
	function lsc_page_needs_contact_form() {
		if ( lsc_queried_cms_has_layout( [ 'contact_section', 'contact_panel' ] ) ) {
			return true;
		}

		$queried = get_queried_object();
		if ( $queried instanceof WP_Post && has_shortcode( (string) $queried->post_content, 'contact-form-7' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'lsc_get_hero_lcp_poster' ) ) {
	/**
	 * URL of the image the leading hero paints as its LCP, so it can be
	 * <link rel="preload">ed (fetchpriority=high) from the document <head>.
	 *
	 * The hero's background <video> used to be the LCP: its eager autoplay
	 * download saturated the connection and starved the poster image, pushing
	 * mobile LCP past 14s. The video is now deferred and the poster image is the
	 * LCP — preloading it lets it paint immediately. Returns the SAME lsc-1600
	 * URL the <video poster> / hero <img> resolves to (so the preload is reused,
	 * not double-fetched).
	 *
	 * @param int|null $post_id Defaults to the queried object.
	 * @return array{desktop:string,mobile:string} Both empty when no leading hero.
	 */
	function lsc_get_hero_lcp_poster( $post_id = null ) {
		$empty = [ 'desktop' => '', 'mobile' => '' ];

		if ( ! function_exists( 'have_rows' ) ) {
			return $empty;
		}

		if ( null === $post_id ) {
			$post_id = get_queried_object_id();
		}

		if ( ! $post_id || ! have_rows( 'cms', $post_id ) ) {
			return $empty;
		}

		$attachment_id = 0;
		$mobile_id     = 0;

		if ( have_rows( 'cms', $post_id ) ) {
			the_row();

			if ( 'hero_section' === get_row_layout() ) {
				$media_type = get_sub_field( 'media_type' ) ?: 'image';
				$base_image = get_sub_field( 'image' );
				$base_id    = ( is_array( $base_image ) && ! empty( $base_image['ID'] ) ) ? (int) $base_image['ID'] : 0;
				$base_mob   = get_sub_field( 'mobile_image' );
				$base_mob_id = ( is_array( $base_mob ) && ! empty( $base_mob['ID'] ) ) ? (int) $base_mob['ID'] : 0;

				$rotation_on = get_sub_field( 'enable_word_rotation' );
				$slides      = $rotation_on ? get_sub_field( 'rotating_slides' ) : [];

				if ( $rotation_on && is_array( $slides ) && ! empty( $slides ) ) {
					// Rotating hero: the LCP is the first slide's poster/image.
					$slide = $slides[0];

					if ( 'video' === ( $slide['media_type'] ?? 'image' ) ) {
						$poster        = $slide['video']['video_self_host_poster'] ?? null;
						$attachment_id = ( is_array( $poster ) && ! empty( $poster['ID'] ) ) ? (int) $poster['ID'] : 0;
						// Mobile poster lives inside the video group.
						if ( ! empty( $slide['video']['mobile_poster']['ID'] ) ) {
							$mobile_id = (int) $slide['video']['mobile_poster']['ID'];
						}
					} elseif ( ! empty( $slide['image']['ID'] ) ) {
						$attachment_id = (int) $slide['image']['ID'];
						if ( ! empty( $slide['mobile_image']['ID'] ) ) {
							$mobile_id = (int) $slide['mobile_image']['ID'];
						}
					}
				} elseif ( 'video' === $media_type ) {
					$video         = get_sub_field( 'video' );
					$poster        = $video['video_self_host_poster'] ?? null;
					$attachment_id = ( is_array( $poster ) && ! empty( $poster['ID'] ) ) ? (int) $poster['ID'] : 0;
					if ( ! empty( $video['mobile_poster']['ID'] ) ) {
						$mobile_id = (int) $video['mobile_poster']['ID'];
					}
				} else {
					$attachment_id = $base_id;
					$mobile_id     = $base_mob_id;
				}

				if ( ! $attachment_id ) {
					$attachment_id = $base_id;
				}
			}
		}

		reset_rows();

		if ( ! $attachment_id ) {
			return $empty;
		}

		$desktop = wp_get_attachment_image_src( $attachment_id, 'lsc-1600' );
		$mobile  = $mobile_id ? wp_get_attachment_image_src( $mobile_id, 'lsc-1200' ) : false;

		return [
			'desktop' => $desktop ? $desktop[0] : '',
			'mobile'  => $mobile ? $mobile[0] : '',
		];
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
