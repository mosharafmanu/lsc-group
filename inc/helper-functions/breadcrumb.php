<?php
/**
 * @package lsc-group
 */

if ( ! function_exists( 'lsc_breadcrumb' ) ) {
	function lsc_breadcrumb( $layout_padding = false, $margin_top = 'mt-30', $margin_bottom = '' ) {
		if ( is_front_page() ) {
			return;
		}

		$classes = [ 'lsc-group-breadcrumb' ];

		if ( $layout_padding ) {
			$classes[] = 'layout-padding';
		}
		if ( ! empty( $margin_top ) ) {
			$classes[] = $margin_top;
		}
		if ( ! empty( $margin_bottom ) ) {
			$classes[] = $margin_bottom;
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" role="navigation" aria-label="Breadcrumb navigation">';
		echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'lsc-group' ) . '</a>';
		echo ' <span class="breadcrumb-separator">/</span> ';

		if ( is_home() ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page ) {
				echo '<span class="current">' . esc_html( get_the_title( $posts_page ) ) . '</span>';
			} else {
				echo '<span class="current">' . esc_html__( 'Blog', 'lsc-group' ) . '</span>';
			}

		} elseif ( is_singular( 'post' ) ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page ) {
				echo '<a href="' . esc_url( get_permalink( $posts_page ) ) . '">' . esc_html( get_the_title( $posts_page ) ) . '</a>';
			} else {
				echo '<a href="' . esc_url( home_url( '/blog/' ) ) . '">' . esc_html__( 'Blog', 'lsc-group' ) . '</a>';
			}
			echo ' <span class="breadcrumb-separator">/</span> ';
			echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';

		} elseif ( is_singular( 'product' ) ) {
			if ( function_exists( 'wc_get_page_id' ) ) {
				$shop_page_id = wc_get_page_id( 'shop' );
				if ( $shop_page_id && $shop_page_id > 0 ) {
					echo '<a href="' . esc_url( get_permalink( $shop_page_id ) ) . '">' . esc_html( get_the_title( $shop_page_id ) ) . '</a>';
					echo ' <span class="breadcrumb-separator">/</span> ';
				}
			}
			echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';

		} elseif ( is_page() ) {
			$parents   = [];
			$parent_id = wp_get_post_parent_id( get_the_ID() );

			while ( $parent_id ) {
				$parent = get_post( $parent_id );
				if ( ! $parent ) {
					break;
				}
				$parents[]  = '<a href="' . esc_url( get_permalink( $parent->ID ) ) . '">' . esc_html( get_the_title( $parent->ID ) ) . '</a>';
				$parent_id = $parent->post_parent;
			}

			if ( $parents ) {
				$parents = array_reverse( $parents );
				foreach ( $parents as $parent_link ) {
					echo $parent_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo ' <span class="breadcrumb-separator">/</span> ';
				}
			}

			echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';

		} elseif ( is_category() ) {
			echo '<span class="current">' . single_cat_title( '', false ) . '</span>';

		} elseif ( is_tag() ) {
			echo '<span class="current">' . single_tag_title( '', false ) . '</span>';

		} elseif ( is_tax() ) {
			echo '<span class="current">' . single_term_title( '', false ) . '</span>';

		} elseif ( is_post_type_archive() ) {
			echo '<span class="current">' . post_type_archive_title( '', false ) . '</span>';

		} elseif ( is_search() ) {
			echo '<span class="current">' . sprintf( esc_html__( 'Search results for: %s', 'lsc-group' ), get_search_query() ) . '</span>';

		} elseif ( is_404() ) {
			echo '<span class="current">' . esc_html__( '404 Not Found', 'lsc-group' ) . '</span>';

		} else {
			$title = get_the_title();
			if ( $title ) {
				echo '<span class="current">' . esc_html( $title ) . '</span>';
			}
		}

		echo '</div>';
	}
}
