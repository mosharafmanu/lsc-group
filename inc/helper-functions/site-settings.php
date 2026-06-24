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

if ( ! function_exists( 'lsc_get_social_medias' ) ) {
	function lsc_get_social_medias() {
		$contact = lsc_get_footer_contact_details();
		$items   = [];

		if ( ! empty( $contact['email'] ) ) {
			$items[] = [
				'type'  => 'mail',
				'label' => __( 'Email', 'lsc-group' ),
				'url'   => 'mailto:' . sanitize_email( $contact['email'] ),
			];
		}

		if ( ! empty( $contact['phone'] ) ) {
			$phone_href = lsc_get_phone_href( $contact['phone'] );

			if ( $phone_href ) {
				$items[] = [
					'type'  => 'phone',
					'label' => __( 'Phone', 'lsc-group' ),
					'url'   => $phone_href,
				];
			}
		}

		if ( ! empty( $contact['linkedin'] ) ) {
			$items[] = [
				'type'  => 'linkedin',
				'label' => __( 'LinkedIn', 'lsc-group' ),
				'url'   => esc_url_raw( $contact['linkedin'] ),
			];
		}

		return $items;
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

if ( ! function_exists( 'lsc_get_icon_svg' ) ) {
	function lsc_get_icon_svg( $icon, $class = 'footer-icon' ) {
		$icons = [
			'mail' => '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 6h16v12H4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m4 7 8 6 8-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'phone' => '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8.5 5.5 6.8 3.8A2 2 0 0 0 3.5 5c0 8.6 6.9 15.5 15.5 15.5a2 2 0 0 0 1.2-3.3l-1.7-1.7a2 2 0 0 0-2.1-.45l-2.1.84a11.2 11.2 0 0 1-6.18-6.18l.84-2.1a2 2 0 0 0-.46-2.1Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'linkedin' => '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.5 9.5V18M6.5 6.5v.01M11 18v-8.5M11 13.25c0-2.5 1.4-3.9 3.35-3.9 2.05 0 3.15 1.35 3.15 3.85V18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><rect x="3" y="3" width="18" height="18" rx="4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>',
			'map-pin' => '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21s6-5.3 6-11a6 6 0 0 0-12 0c0 5.7 6 11 6 11Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="10" r="2" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>',
			'arrow' => '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 17 10-10M9 7h8v8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'check-circle' => '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m8.5 12 2.4 2.4 4.6-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		];

		return $icons[ $icon ] ?? '';
	}
}

if ( ! function_exists( 'lsc_render_social_medias' ) ) {
	function lsc_render_social_medias( $args = [] ) {
		$defaults = [
			'list_class' => 'social-media-list',
			'item_class' => 'social-media-item',
			'link_class' => 'social-media-link',
			'icon_class' => 'social-media-icon',
			'echo'       => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$social_medias = lsc_get_social_medias();

		if ( ! $social_medias || ! is_array( $social_medias ) ) {
			return;
		}

		ob_start();
		?>
		<ul class="<?php echo esc_attr( $args['list_class'] ); ?>">
			<?php foreach ( $social_medias as $social ) : ?>
				<?php
				$type  = $social['type'] ?? '';
				$link  = $social['url'] ?? '';
				$label = $social['label'] ?? __( 'Contact link', 'lsc-group' );

				if ( ! $type || ! $link ) {
					continue;
				}
				?>

				<li class="<?php echo esc_attr( $args['item_class'] ); ?>">
					<a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>"<?php echo 'linkedin' === $type ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> aria-label="<?php echo esc_attr( $label ); ?>">
						<?php
						if ( 'linkedin' === $type ) {
							get_template_part( 'assets/svgs/linkedin' );
						} else {
							echo lsc_get_icon_svg( $type, $args['icon_class'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
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

		$contact = lsc_get_footer_contact_details();

		if ( empty( $contact['address'] ) && empty( $contact['phone'] ) && empty( $contact['email'] ) ) {
			return;
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<p class="<?php echo esc_attr( $args['title_class'] ); ?>"><?php esc_html_e( 'Contact', 'lsc-group' ); ?></p>

			<ul class="footer-contact-list">
				<?php if ( ! empty( $contact['address'] ) ) : ?>
					<li class="footer-contact-item">
						<?php echo lsc_get_icon_svg( 'map-pin', 'footer-contact-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo wp_kses_post( nl2br( $contact['address'] ) ); ?></span>
					</li>
				<?php endif; ?>

				<?php if ( ! empty( $contact['phone'] ) ) : ?>
					<li class="footer-contact-item">
						<?php echo lsc_get_icon_svg( 'phone', 'footer-contact-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_url( lsc_get_phone_href( $contact['phone'] ) ); ?>"><?php echo esc_html( $contact['phone'] ); ?></a>
					</li>
				<?php endif; ?>

				<?php if ( ! empty( $contact['email'] ) ) : ?>
					<li class="footer-contact-item">
						<?php echo lsc_get_icon_svg( 'mail', 'footer-contact-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $contact['email'] ) ); ?>"><?php echo esc_html( $contact['email'] ); ?></a>
					</li>
				<?php endif; ?>
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
