<?php
/**
 * Mega Menu
 *
 * Adds a per-menu-item "Menu Type" toggle (Regular / Mega) via ACF on nav menu
 * items. Regular items keep WordPress's default dropdown; mega items render a
 * full-width panel of cards (from a post type or a manual repeater) plus a CTA
 * bar. Markup only — BEM hooks are styled in style.css.
 *
 * @package lsc-group
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read the mega menu type for a nav menu item.
 *
 * @param int $item_id Nav menu item (post) ID.
 * @return string 'mega' or 'regular'.
 */
if ( ! function_exists( 'lsc_get_menu_item_type' ) ) {
	function lsc_get_menu_item_type( $item_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return 'regular';
		}

		return 'mega' === get_field( 'menu_type', $item_id ) ? 'mega' : 'regular';
	}
}

/**
 * Populate the Post Type select with public post types (excluding Media), so
 * the list works on any ACF version and picks up future post types
 * automatically.
 */
add_filter(
	'acf/load_field/key=field_mega_post_type',
	function ( $field ) {
		$field['choices'] = [];

		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		unset( $post_types['attachment'] );

		foreach ( $post_types as $slug => $object ) {
			$label = ! empty( $object->labels->singular_name ) ? $object->labels->singular_name : $object->label;

			$field['choices'][ $slug ] = $label;
		}

		return $field;
	}
);

/**
 * Scope the "Selected Posts" relationship to the post type chosen on the same
 * menu item, so editors only pick from the relevant content.
 */
add_filter(
	'acf/fields/relationship/query/key=field_mega_selected_posts',
	function ( $args, $field, $post_id ) {
		$post_type = get_field( 'mega_post_type', $post_id );

		if ( $post_type ) {
			$args['post_type'] = (array) $post_type;
		}

		return $args;
	},
	10,
	3
);

/**
 * Flag mega items on the <li> so style.css can hook them.
 */
add_filter(
	'nav_menu_css_class',
	function ( $classes, $item ) {
		if ( 'mega' === lsc_get_menu_item_type( $item->ID ) ) {
			$classes[] = 'menu-item--has-mega';
		}

		return $classes;
	},
	10,
	2
);

/**
 * Normalise a mega item's cards (post-type or manual source) into one shape.
 *
 * @param int $item_id Nav menu item ID.
 * @return array<int,array{title:string,description:string,url:string,target:string,link_text:string,image_html:string}>
 */
if ( ! function_exists( 'lsc_get_mega_menu_cards' ) ) {
	function lsc_get_mega_menu_cards( $item_id ) {
		$cards  = [];
		$source = get_field( 'mega_source', $item_id ) ?: 'post_type';

		if ( 'manual' === $source ) {
			$items = get_field( 'mega_items', $item_id );

			if ( $items && is_array( $items ) ) {
				foreach ( $items as $row ) {
					$link  = $row['link'] ?? [];
					$image = $row['image'] ?? null;

					$image_html = '';
					if ( $image ) {
						$image_html = lsc_render_responsive_picture(
							$image,
							[
								'class' => 'mega-menu__card-image',
								'echo'  => false,
							]
						);
					}

					$cards[] = [
						'title'       => $row['title'] ?? '',
						'description' => $row['description'] ?? '',
						'url'         => $link['url'] ?? '',
						'target'      => $link['target'] ?? '',
						'link_text'   => ! empty( $link['title'] ) ? $link['title'] : __( 'Learn More', 'lsc-group' ),
						'image_html'  => $image_html ?: '',
					];
				}
			}

			return $cards;
		}

		// Post-type source.
		$post_type = get_field( 'mega_post_type', $item_id );

		if ( ! $post_type ) {
			return $cards;
		}

		$selection = get_field( 'mega_selection', $item_id ) ?: 'all';
		$posts     = [];

		if ( 'selected' === $selection ) {
			$selected = get_field( 'mega_selected_posts', $item_id );

			if ( $selected && is_array( $selected ) ) {
				foreach ( $selected as $selected_post ) {
					$post_id = is_object( $selected_post ) ? $selected_post->ID : (int) $selected_post;
					$post    = $post_id ? get_post( $post_id ) : null;

					if ( $post && 'publish' === $post->post_status ) {
						$posts[] = $post;
					}
				}
			}
		} else {
			$per_page = get_field( 'mega_posts_per_page', $item_id );
			$per_page = $per_page ? (int) $per_page : -1;

			$query = new WP_Query(
				[
					'post_type'      => (array) $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => $per_page,
					'orderby'        => get_field( 'mega_orderby', $item_id ) ?: 'menu_order',
					'order'          => get_field( 'mega_order', $item_id ) ?: 'ASC',
					'no_found_rows'  => true,
				]
			);

			$posts = $query->have_posts() ? $query->posts : [];
			wp_reset_postdata();
		}

		foreach ( $posts as $post ) {
			$post_id    = $post->ID;
			// Render a SINGLE lsc-600 image (no srcset). The mega menu is a hidden
			// dropdown, so WP's responsive `sizes="auto"` (added to lazy images)
			// resolves a near-zero width and downgrades to the tiny 300px thumbnail,
			// which then looks blurry once the card is shown. A single right-sized
			// source avoids that downgrade while staying lazy.
			$image_html = '';
			if ( has_post_thumbnail( $post_id ) ) {
				$thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'lsc-600' );
				if ( $thumb_src ) {
					$image_html = sprintf(
						'<img class="mega-menu__card-image" src="%s" width="%d" height="%d" alt="" loading="lazy" decoding="async">',
						esc_url( $thumb_src[0] ),
						(int) $thumb_src[1],
						(int) $thumb_src[2]
					);
				}
			}

			$cards[] = [
				'title'       => get_the_title( $post_id ),
				'description' => has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '',
				'url'         => get_permalink( $post_id ),
				'target'      => '',
				'link_text'   => __( 'Learn More', 'lsc-group' ),
				'image_html'  => $image_html,
			];
		}

		return $cards;
	}
}

/**
 * Build the mega panel markup for a nav menu item.
 *
 * @param WP_Post $item Nav menu item.
 * @return string Panel HTML (empty string when there is nothing to show).
 */
if ( ! function_exists( 'lsc_render_mega_menu_panel' ) ) {
	function lsc_render_mega_menu_panel( $item ) {
		$item_id = $item->ID;
		$cards   = lsc_get_mega_menu_cards( $item_id );

		$heading = get_field( 'mega_heading', $item_id );
		$heading = $heading ? $heading : wp_strip_all_tags( $item->title );
		$columns = get_field( 'mega_columns', $item_id ) ?: 'columns-4';

		// CTA bar is global — same on every mega menu (Site Settings).
		$cta_text  = get_field( 'mega_cta_text', 'options' );
		$cta_phone = get_field( 'mega_cta_phone', 'options' );

		if ( ! $cta_phone && function_exists( 'lsc_get_header_phone' ) ) {
			$cta_phone = lsc_get_header_phone();
		}

		// Nothing meaningful to render.
		if ( empty( $cards ) && ! $cta_text && ! $cta_phone ) {
			return '';
		}

		// Plain card-grid (no centre-last-row) so an incomplete final row stays
		// left-aligned to the container instead of centring.
		$grid_classes = [
			'mega-menu__grid',
			'card-grid',
			sanitize_html_class( $columns ),
		];

		ob_start();
		?>
		<div class="mega-menu" role="region" aria-label="<?php echo esc_attr( $heading ); ?>">
			<div class="mega-menu__panel">
			<div class="mega-menu__container lsc-container layout-padding">
				<?php if ( $heading ) : ?>
					<div class="mega-menu__heading h2-style"><?php echo esc_html( $heading ); ?></div>
				<?php endif; ?>

				<?php if ( $cards ) : ?>
					<ul class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?>">
						<?php
						foreach ( $cards as $card ) :
							$has_link = ! empty( $card['url'] );
							$target   = ! empty( $card['target'] ) ? ' target="' . esc_attr( $card['target'] ) . '" rel="noopener"' : '';
							?>
							<li class="mega-menu__card">
								<?php
								// The whole card is one link when a URL exists; falls back to a
								// plain wrapper for manual cards with no link set.
								$card_tag   = $has_link ? 'a' : 'div';
								$card_attrs = $has_link ? ' href="' . esc_url( $card['url'] ) . '"' . $target : '';
								?>
								<<?php echo $card_tag; ?> class="mega-menu__card-link-wrap"<?php echo $card_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php if ( $card['image_html'] ) : ?>
										<span class="mega-menu__card-media">
											<?php echo $card['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</span>
									<?php endif; ?>

									<span class="mega-menu__card-body">
										<?php if ( $card['title'] ) : ?>
											<span class="mega-menu__card-title"><?php echo esc_html( $card['title'] ); ?></span>
										<?php endif; ?>

										<?php if ( $card['description'] ) : ?>
											<span class="mega-menu__card-description"><?php echo esc_html( $card['description'] ); ?></span>
										<?php endif; ?>

										<?php if ( $has_link ) : ?>
											<span class="mega-menu__card-link">
												<span class="mega-menu__card-link-text"><?php echo esc_html( $card['link_text'] ); ?></span>
												<span class="mega-menu__card-link-icon" aria-hidden="true">&rarr;</span>
											</span>
										<?php endif; ?>
									</span>
								</<?php echo $card_tag; ?>>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $cta_text || $cta_phone ) : ?>
					<div class="mega-menu__cta">
						<?php if ( $cta_text ) : ?>
							<span class="mega-menu__cta-text"><?php echo esc_html( $cta_text ); ?></span>
						<?php endif; ?>

						<?php if ( $cta_phone ) : ?>
							<a class="mega-menu__cta-phone" href="<?php echo esc_url( lsc_get_phone_href( $cta_phone ) ); ?>">
								<?php get_template_part( 'assets/svgs/phone-mega-menu', null, [ 'class' => 'mega-menu__cta-icon' ] ); ?>
								<span class="mega-menu__cta-phone-number"><?php echo esc_html( $cta_phone ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

/**
 * Nav walker: mega items output an ACF-built panel instead of a child dropdown.
 */
if ( ! class_exists( 'LSC_Mega_Menu_Walker' ) ) {
	class LSC_Mega_Menu_Walker extends Walker_Nav_Menu {

		/**
		 * Drop a mega item's children so the default sub-menu is not rendered
		 * (the panel is built from ACF instead).
		 */
		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( $element && isset( $element->ID ) && 'mega' === lsc_get_menu_item_type( $element->ID ) ) {
				unset( $children_elements[ $element->ID ] );
			}

			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		/**
		 * Append the mega panel after a mega item's link, inside its <li>.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			parent::start_el( $output, $item, $depth, $args, $id );

			if ( 'mega' === lsc_get_menu_item_type( $item->ID ) ) {
				$output .= lsc_render_mega_menu_panel( $item );
			}
		}
	}
}

/**
 * Mobile nav walker: there is no hover panel on touch, so a mega item's ACF cards
 * are rendered as a collapsible sub-menu (links only) and the <li> is flagged
 * `menu-item-has-children` so the existing accordion JS adds a chevron + slide
 * toggle. WP children of mega items are dropped (the cards are the source), matching
 * the desktop walker.
 */
if ( ! class_exists( 'LSC_Mobile_Mega_Walker' ) ) {
	class LSC_Mobile_Mega_Walker extends Walker_Nav_Menu {

		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( $element && isset( $element->ID ) && 0 === $depth && 'mega' === lsc_get_menu_item_type( $element->ID ) ) {
				unset( $children_elements[ $element->ID ] );
			}

			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$is_mega = ( 0 === $depth && 'mega' === lsc_get_menu_item_type( $item->ID ) );

			// Flag the item so the accordion JS treats it like a dropdown.
			if ( $is_mega && ! in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
				$item->classes[] = 'menu-item-has-children';
			}

			parent::start_el( $output, $item, $depth, $args, $id );

			if ( $is_mega ) {
				$cards = lsc_get_mega_menu_cards( $item->ID );

				if ( $cards ) {
					// Image cards (image with title overlaid), mirroring the desktop
					// mega panel. `--i` drives the staggered reveal in CSS.
					$output .= '<ul class="sub-menu mega-mobile-grid">';

					$i = 0;
					foreach ( $cards as $card ) {
						$url   = ! empty( $card['url'] ) ? $card['url'] : '#';
						$title = isset( $card['title'] ) ? $card['title'] : '';
						$img   = isset( $card['image_html'] ) ? $card['image_html'] : '';

						$output .= '<li class="menu-item mega-mobile-card-item" style="--i:' . (int) $i . '">';
						$output .= '<a class="mega-mobile-card" href="' . esc_url( $url ) . '">';
						if ( $img ) {
							$output .= '<span class="mega-mobile-card__media">' . $img . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						$output .= '<span class="mega-mobile-card__title"><span class="mega-mobile-card__title-text">' . esc_html( $title ) . '</span><span class="mega-mobile-card__arrow" aria-hidden="true">&rarr;</span></span>';
						$output .= '</a></li>';
						$i++;
					}

					$output .= '</ul>';
				}
			}
		}
	}
}
