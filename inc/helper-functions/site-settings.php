<?php
/**
 * @package lsc-group
 */

// Header Options

if ( ! function_exists( 'lsc_get_header_button' ) ) {
	function lsc_get_header_button( $field = 'header_button' ) {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( $field, 'options' );
	}
}

if ( ! function_exists( 'lsc_render_header_button' ) ) {
	function lsc_render_header_button( $args = [] ) {
		$defaults = [
			'field' => 'header_button',
			'class' => 'site-btn btn-primary',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$button = lsc_get_header_button( $args['field'] );

		if ( ! $button || ! is_array( $button ) ) {
			return;
		}

		$url    = $button['url'] ?? '#';
		$title  = $button['title'] ?? 'Get Started';
		$target = $button['target'] ?? '';

		ob_start();
		?>
		<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>"<?php echo $target ? ' target="' . esc_attr( $target ) . '" rel="noopener noreferrer"' : ''; ?>>
			<?php echo esc_html( $title ); ?>
		</a>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

// Global CTA (Site Settings) — used as the default content for the CTA flexible layout

if ( ! function_exists( 'lsc_get_global_cta' ) ) {
	function lsc_get_global_cta() {
		if ( ! function_exists( 'get_field' ) ) {
			return [];
		}

		return [
			'eyebrow'     => get_field( 'global_cta_eyebrow', 'options' ),
			'title_lines' => get_field( 'global_cta_title_lines', 'options' ),
			'description' => get_field( 'global_cta_description', 'options' ),
			'buttons'     => get_field( 'global_cta_buttons', 'options' ),
			'background'  => get_field( 'global_cta_background', 'options' ),
		];
	}
}

// Global "Apply Now" link (Site Settings) — used by the Case Study sidebar

if ( ! function_exists( 'lsc_get_apply_now_link' ) ) {
	function lsc_get_apply_now_link() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'apply_now_link', 'options' );
	}
}

if ( ! function_exists( 'lsc_get_header_phone' ) ) {
	function lsc_get_header_phone() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'header_phone', 'options' );
	}
}

if ( ! function_exists( 'lsc_get_phone_href' ) ) {
	function lsc_get_phone_href( $phone ) {
		$phone = preg_replace( '/[^0-9+]/', '', (string) $phone );

		return $phone ? 'tel:' . $phone : '';
	}
}

if ( ! function_exists( 'lsc_render_header_phone' ) ) {
	function lsc_render_header_phone( $args = [] ) {
		$phone = lsc_get_header_phone();

		if ( ! $phone ) {
			return;
		}

		$defaults = [
			'class' => 'header-phone-link',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$href = lsc_get_phone_href( $phone );

		if ( ! $href ) {
			return;
		}

		$output = '<a href="' . esc_url( $href ) . '" class="' . esc_attr( $args['class'] ) . '">' . esc_html( $phone ) . '</a>';

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}


if ( ! function_exists( 'lsc_get_site_logo' ) ) {
	function lsc_get_site_logo() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'site_logo', 'options' );
	}
}

if ( ! function_exists( 'lsc_render_site_logo' ) ) {
	function lsc_render_site_logo( $args = [] ) {
		$logo = lsc_get_site_logo();

		if ( ! $logo ) {
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo-link site-name" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
				<?php
			}
			return;
		}

		if ( ! function_exists( 'lsc_render_responsive_picture' ) ) {
			return;
		}

		$defaults = [
			'class'      => 'site-logo',
			'alt'        => get_bloginfo( 'name' ),
			'link_class' => 'site-logo-link',
		];
		$args = wp_parse_args( $args, $defaults );

		$home_url = home_url( '/' );
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			lsc_render_responsive_picture(
				$logo,
				[
					'class' => $args['class'],
					'alt'   => $args['alt'],
					'sizes' => '(max-width: 768px) 100px, 160px',
				]
			);
			?>
		</a>
		<?php
	}
}

// ─────────────────────────────────────────────────────────────────
// Footer Options
// ─────────────────────────────────────────────────────────────────

if ( ! function_exists( 'lsc_get_footer_tagline' ) ) {
	function lsc_get_footer_tagline() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}
		return get_field( 'footer_tagline', 'options' );
	}
}

if ( ! function_exists( 'lsc_render_footer_tagline' ) ) {
	function lsc_render_footer_tagline( $args = [] ) {
		$tagline = lsc_get_footer_tagline();
		if ( ! $tagline ) {
			return;
		}

		$defaults = [
			'class' => 'footer-tagline',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$output = '<p class="' . esc_attr( $args['class'] ) . '">' . wp_kses_post( nl2br( $tagline ) ) . '</p>';

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'lsc_get_footer_logo' ) ) {
	function lsc_get_footer_logo() {
		return lsc_get_site_logo();
	}
}

if ( ! function_exists( 'lsc_render_footer_logo' ) ) {
	function lsc_render_footer_logo( $args = [] ) {
		if ( ! function_exists( 'lsc_render_responsive_picture' ) ) {
			return;
		}

		$logo = lsc_get_footer_logo();

		if ( ! $logo ) {
			return;
		}

		$defaults = [
			'class'      => 'footer-logo',
			'alt'        => get_bloginfo( 'name' ),
			'link_class' => 'footer-logo-link',
		];
		$args = wp_parse_args( $args, $defaults );

		$home_url = home_url( '/' );
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			lsc_render_responsive_picture(
				$logo,
				[
					'class' => $args['class'],
					'alt'   => $args['alt'],
					'sizes' => '(max-width: 768px) 100px, 160px',
				]
			);
			?>
		</a>
		<?php
	}
}

if ( ! function_exists( 'lsc_get_footer_contact_details' ) ) {
	function lsc_get_footer_contact_details() {
		if ( ! function_exists( 'get_field' ) ) {
			return [];
		}

		return [
			'address'  => get_field( 'footer_contact_address', 'options' ),
			'phone'    => get_field( 'footer_contact_phone', 'options' ),
			'email'    => get_field( 'footer_contact_email', 'options' ),
			'linkedin' => get_field( 'footer_linkedin_url', 'options' ),
			'hours'    => get_field( 'footer_opening_hours', 'options' ),
		];
	}
}

if ( ! function_exists( 'lsc_get_contact_items' ) ) {
	/**
	 * All available contact items, sourced once from Site Settings.
	 *
	 * Each item: value, link URL, link target, and an uploaded icon (Site
	 * Settings → "{Type} Icon"). Only items that have a value are returned.
	 *
	 * Keys: address, phone, email, linkedin, hours.
	 *
	 * @return array<string,array> Keyed by item type.
	 */
	function lsc_get_contact_items() {
		$contact  = function_exists( 'lsc_get_footer_contact_details' ) ? lsc_get_footer_contact_details() : [];
		$address  = $contact['address'] ?? '';
		$phone    = $contact['phone'] ?? '';
		$email    = $contact['email'] ?? '';
		$linkedin = $contact['linkedin'] ?? '';
		$hours    = $contact['hours'] ?? '';

		$icon_override = static function ( $field ) {
			return function_exists( 'get_field' ) ? get_field( $field, 'options' ) : null;
		};

		$items = [];

		if ( $address ) {
			$items['address'] = [
				'key'         => 'address',
				'aria'        => __( 'Address', 'lsc-group' ),
				'value'       => $address,
				'url'         => '',
				'target'      => '',
				'icon_custom' => $icon_override( 'contact_icon_address' ),
			];
		}

		if ( $phone ) {
			$items['phone'] = [
				'key'         => 'phone',
				'aria'        => __( 'Phone', 'lsc-group' ),
				'value'       => $phone,
				'url'         => lsc_get_phone_href( $phone ),
				'target'      => '',
				'icon_custom' => $icon_override( 'contact_icon_phone' ),
			];
		}

		if ( $email ) {
			$items['email'] = [
				'key'         => 'email',
				'aria'        => __( 'Email', 'lsc-group' ),
				'value'       => $email,
				'url'         => 'mailto:' . sanitize_email( $email ),
				'target'      => '',
				'icon_custom' => $icon_override( 'contact_icon_email' ),
			];
		}

		if ( $linkedin ) {
			$items['linkedin'] = [
				'key'         => 'linkedin',
				'aria'        => __( 'LinkedIn', 'lsc-group' ),
				'value'       => $linkedin,
				'url'         => esc_url_raw( $linkedin ),
				'target'      => '_blank',
				'icon_custom' => $icon_override( 'contact_icon_linkedin' ),
			];
		}

		if ( $hours ) {
			$items['hours'] = [
				'key'         => 'hours',
				'aria'        => __( 'Opening Hours', 'lsc-group' ),
				'value'       => $hours,
				'url'         => '',
				'target'      => '',
				'icon_custom' => null,
			];
		}

		return $items;
	}
}

if ( ! function_exists( 'lsc_get_contact_items_for' ) ) {
	/**
	 * Contact items selected for a given location, in that location's order.
	 *
	 * Locations: footer_icons, footer_contact, contact_section, contact_panel.
	 * The per-location "Items" checkbox in Site Settings decides which appear;
	 * an empty/unset selection shows every available item (the defaults).
	 *
	 * @param string $location Location key.
	 * @return array<string,array> Ordered subset of lsc_get_contact_items().
	 */
	function lsc_get_contact_items_for( $location ) {
		$order = [
			'footer_icons'    => [ 'email', 'phone', 'linkedin' ],
			'footer_contact'  => [ 'address', 'phone', 'email' ],
			'contact_section' => [ 'address', 'phone', 'email', 'linkedin' ],
			'contact_panel'   => [ 'email', 'phone', 'address', 'hours' ],
		];

		$fields = [
			'footer_icons'    => 'footer_icons_items',
			'footer_contact'  => 'footer_contact_items',
			'contact_section' => 'contact_section_items',
			'contact_panel'   => 'contact_panel_items',
		];

		if ( ! isset( $order[ $location ] ) ) {
			return [];
		}

		$all      = lsc_get_contact_items();
		$selected = ( function_exists( 'get_field' ) && isset( $fields[ $location ] ) ) ? get_field( $fields[ $location ], 'options' ) : null;

		// Empty/unset selection → show all available items for this location.
		if ( empty( $selected ) || ! is_array( $selected ) ) {
			$selected = $order[ $location ];
		}

		$result = [];

		foreach ( $order[ $location ] as $key ) {
			if ( isset( $all[ $key ] ) && in_array( $key, $selected, true ) ) {
				$result[ $key ] = $all[ $key ];
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'lsc_render_contact_icon' ) ) {
	/**
	 * Render a contact item's uploaded icon (Site Settings → "{Type} Icon").
	 * The class is applied to the rendered <svg>/<img>. Nothing is output when
	 * no icon has been uploaded for the item.
	 *
	 * @param array  $item  A single item from lsc_get_contact_items().
	 * @param string $class CSS class for the icon element.
	 */
	function lsc_render_contact_icon( $item, $class = '' ) {
		if ( ! empty( $item['icon_custom'] ) && function_exists( 'lsc_render_icon' ) ) {
			lsc_render_icon( $item['icon_custom'], [ 'class' => $class, 'alt' => $item['aria'] ?? '' ] );
		}
	}
}

if ( ! function_exists( 'lsc_get_footer_copyright' ) ) {
	function lsc_get_footer_copyright() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'footer_copyright', 'options' );
	}
}

if ( ! function_exists( 'lsc_render_footer_menu' ) ) {
	function lsc_render_footer_menu( $args = [] ) {
		$defaults = [
			'location'        => '',
			'container_class' => 'footer-menu-column',
			'title_class'     => 'footer-menu-title',
			'menu_class'      => 'footer-menu-list',
			'show_title'      => true,
			'title'           => '',
			'echo'            => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['location'] ) ) {
			return;
		}

		if ( ! has_nav_menu( $args['location'] ) ) {
			return;
		}

		$locations = get_nav_menu_locations();
		$menu_id   = $locations[ $args['location'] ] ?? 0;
		$menu_obj  = wp_get_nav_menu_object( $menu_id );
		$menu_name = $args['title'] ? $args['title'] : ( $menu_obj->name ?? '' );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['container_class'] ); ?>">
			<?php if ( $args['show_title'] && ! empty( $menu_name ) ) : ?>
				<p class="<?php echo esc_attr( $args['title_class'] ); ?>"><?php echo esc_html( $menu_name ); ?></p>
			<?php endif; ?>
			<?php
			wp_nav_menu(
				[
					'theme_location' => $args['location'],
					'container'      => false,
					'menu_class'     => $args['menu_class'],
					'depth'          => 1,
					'fallback_cb'    => false,
				]
			);
			?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'lsc_render_footer_contact_icons' ) ) {
	/**
	 * The compact contact-icon row under the footer logo (icon-only links).
	 * Items chosen via Site Settings → "Footer Icon Row — Items".
	 */
	function lsc_render_footer_contact_icons( $args = [] ) {
		$defaults = [
			'list_class' => 'social-media-list',
			'item_class' => 'social-media-item',
			'link_class' => 'social-media-link',
			'icon_class' => 'social-media-icon',
			'echo'       => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$items = lsc_get_contact_items_for( 'footer_icons' );

		if ( ! $items ) {
			return;
		}

		ob_start();
		?>
		<ul class="<?php echo esc_attr( $args['list_class'] ); ?>">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$link   = $item['url'] ?? '';
				$label  = $item['aria'] ?? __( 'Contact link', 'lsc-group' );
				$target = $item['target'] ?? '';

				if ( ! $link ) {
					continue;
				}

				$target_attr = $target ? ' target="' . esc_attr( $target ) . '"' : '';
				$rel_attr    = '_blank' === $target ? ' rel="noopener noreferrer"' : '';
				?>

				<li class="<?php echo esc_attr( $args['item_class'] ); ?>">
					<a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>"<?php echo $target_attr . $rel_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php echo esc_attr( $label ); ?>">
						<?php lsc_render_contact_icon( $item, $args['icon_class'] ); ?>
					</a>
				</li>

			<?php endforeach; ?>
		</ul>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'lsc_render_footer_contact' ) ) {
	function lsc_render_footer_contact( $args = [] ) {
		$defaults = [
			'class'       => 'footer-contact-column',
			'title_class' => 'footer-menu-title',
			'echo'        => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$items = lsc_get_contact_items_for( 'footer_contact' );

		if ( ! $items ) {
			return;
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<p class="<?php echo esc_attr( $args['title_class'] ); ?>"><?php esc_html_e( 'Contact', 'lsc-group' ); ?></p>

			<ul class="footer-contact-list">
				<?php foreach ( $items as $item ) : ?>
					<li class="footer-contact-item">
						<?php lsc_render_contact_icon( $item, 'footer-contact-icon' ); ?>
						<?php if ( ! empty( $item['url'] ) ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo '_blank' === ( $item['target'] ?? '' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $item['value'] ); ?></a>
						<?php elseif ( 'address' === $item['key'] ) : ?>
							<span><?php echo wp_kses_post( nl2br( $item['value'] ) ); ?></span>
						<?php else : ?>
							<span><?php echo esc_html( $item['value'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'lsc_get_footer_company_registrations' ) ) {
	function lsc_get_footer_company_registrations() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'footer_company_registrations', 'options' );
	}
}

if ( ! function_exists( 'lsc_render_footer_company_registrations' ) ) {
	function lsc_render_footer_company_registrations( $args = [] ) {
		$registrations = lsc_get_footer_company_registrations();

		if ( ! $registrations || ! is_array( $registrations ) ) {
			return;
		}

		$defaults = [
			'class' => 'footer-company-registrations lsc-container layout-padding',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<?php foreach ( $registrations as $registration ) : ?>
				<?php
				$title = $registration['company_name'] ?? '';
				$text  = $registration['registration_text'] ?? '';

				if ( ! $title && ! $text ) {
					continue;
				}
				?>
				<div class="footer-company-registration">
					<?php if ( $title ) : ?>
						<p class="footer-company-title"><?php echo esc_html( $title ); ?></p>
					<?php endif; ?>

					<?php if ( $text ) : ?>
						<p class="footer-company-text"><?php echo wp_kses_post( nl2br( $text ) ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'lsc_render_footer_copyright' ) ) {
	function lsc_render_footer_copyright( $args = [] ) {
		$defaults = [
			'class' => 'footer-copyright-text',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$copyright = lsc_get_footer_copyright();

		if ( ! $copyright ) {
			return;
		}

		$copyright = str_replace( '{year}', gmdate( 'Y' ), $copyright );

		ob_start();
		?>

		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<p><?php echo esc_html( $copyright ); ?></p>
		</div>

		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'lsc_get_website_credit' ) ) {
	function lsc_get_website_credit() {
		if ( ! function_exists( 'get_field' ) ) {
			return [];
		}

		return [
			'text' => get_field( 'website_credit_text', 'options' ),
			'link' => get_field( 'website_credit_link', 'options' ),
		];
	}
}

if ( ! function_exists( 'lsc_render_website_credit' ) ) {
	function lsc_render_website_credit( $args = [] ) {
		$credit = lsc_get_website_credit();

		if ( empty( $credit['text'] ) && empty( $credit['link'] ) ) {
			return;
		}

		$defaults = [
			'class' => 'website-credit',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$link   = is_array( $credit['link'] ) ? $credit['link'] : [];
		$url    = $link['url'] ?? '';
		$title  = $link['title'] ?? '';
		$target = $link['target'] ?? '';
		$text   = $credit['text'] ?: __( 'Website by', 'lsc-group' );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<span><?php echo esc_html( $text ); ?></span>

			<?php if ( $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>"<?php echo $target ? ' target="' . esc_attr( $target ) . '" rel="noopener noreferrer"' : ''; ?> aria-label="<?php echo esc_attr( $title ?: $text ); ?>">
					<?php get_template_part( 'assets/svgs/so-marketing' ); ?>
				</a>
			<?php else : ?>
				<?php get_template_part( 'assets/svgs/so-marketing' ); ?>
			<?php endif; ?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}
