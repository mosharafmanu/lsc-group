<?php
/**
 * Contact Section
 *
 * @package lsc-group
 */

$title_lines  = get_sub_field( 'title_lines' );
$description  = get_sub_field( 'description' );
$contact_info = get_sub_field( 'contact_info' );
$form_card    = get_sub_field( 'form_card' );

if ( ! $title_lines && ! $description && ! $contact_info && ! $form_card ) {
	return;
}

$section_classes = [
	'contact-section',
	'layout-padding',
	'bg-lsc-light',
];

// Get contact info from site settings
$site_settings = function_exists( 'lsc_get_footer_contact_details' ) ? lsc_get_footer_contact_details() : [];
$linkedin_url  = function_exists( 'lsc_get_social_medias' ) ? lsc_get_social_medias() : []; // This returns the LinkedIn URL from site settings

?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="container">
		<?php if ( $title_lines || $description ) : ?>
			<div class="contact-section__header text-center mb-50 mb-md-80">
				<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
					<h2 class="contact-section__title">
						<?php foreach ( $title_lines as $title_line ) : ?>
							<?php
							$line_parts       = $title_line['line_parts'] ?? [];
							$line_has_content = false;

							if ( $line_parts && is_array( $line_parts ) ) {
								foreach ( $line_parts as $line_part ) {
									if ( ! empty( $line_part['text'] ) ) {
										$line_has_content = true;
										break;
									}
								}
							}

							if ( ! $line_has_content ) {
								continue;
							}
							?>
							<span class="contact-section__title-line">
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text    = $line_part['text'] ?? '';
									$is_highlight = ! empty( $line_part['highlight'] );

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'contact-section__title-part' ];

									if ( $is_highlight ) {
										$part_classes[] = 'color-lsc-accent';
									}
									?>
									<span class="<?php echo esc_attr( implode( ' ', $part_classes ) ); ?>"><?php echo esc_html( $part_text ); ?></span>
								<?php endforeach; ?>
							</span>
						<?php endforeach; ?>
					</h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="contact-section__description mt-20">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="contact-section__grid">
			<div class="contact-section__col contact-section__col--info">
				<?php if ( $contact_info ) : ?>
					<div class="contact-info-card">
						<?php if ( ! empty( $contact_info['title'] ) ) : ?>
							<h3 class="contact-info-card__title"><?php echo esc_html( $contact_info['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $contact_info['text'] ) ) : ?>
							<p class="contact-info-card__text"><?php echo esc_html( $contact_info['text'] ); ?></p>
						<?php endif; ?>

						<ul class="contact-info-card__list">
							<?php
							$addr  = $site_settings['address'] ?? '';
							$phone = $site_settings['phone'] ?? '';
							$mail  = $site_settings['email'] ?? '';
							$link  = $site_settings['linkedin_url'] ?? '';
							?>

							<?php if ( $addr ) : ?>
								<li class="contact-info-card__item">
									<span class="contact-info-card__icon" aria-hidden="true">
										<?php if ( function_exists( 'lsc_get_icon_svg' ) ) echo lsc_get_icon_svg( 'map-pin', 'contact-icon' ); ?>
									</span>
									<div class="contact-info-card__details">
										<span class="contact-info-card__label"><?php esc_html_e( 'ADDRESS', 'lsc-group' ); ?></span>
										<address class="contact-info-card__value"><?php echo nl2br( esc_html( $addr ) ); ?></address>
									</div>
								</li>
							<?php endif; ?>

							<?php if ( $phone ) : ?>
								<li class="contact-info-card__item">
									<span class="contact-info-card__icon" aria-hidden="true">
										<?php if ( function_exists( 'lsc_get_icon_svg' ) ) echo lsc_get_icon_svg( 'phone', 'contact-icon' ); ?>
									</span>
									<div class="contact-info-card__details">
										<span class="contact-info-card__label"><?php esc_html_e( 'TELEPHONE', 'lsc-group' ); ?></span>
										<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>" class="contact-info-card__value"><?php echo esc_html( $phone ); ?></a>
									</div>
								</li>
							<?php endif; ?>

							<?php if ( $mail ) : ?>
								<li class="contact-info-card__item">
									<span class="contact-info-card__icon" aria-hidden="true">
										<?php if ( function_exists( 'lsc_get_icon_svg' ) ) echo lsc_get_icon_svg( 'mail', 'contact-icon' ); ?>
									</span>
									<div class="contact-info-card__details">
										<span class="contact-info-card__label"><?php esc_html_e( 'EMAIL', 'lsc-group' ); ?></span>
										<a href="mailto:<?php echo esc_attr( $mail ); ?>" class="contact-info-card__value"><?php echo esc_html( $mail ); ?></a>
									</div>
								</li>
							<?php endif; ?>
						</ul>

						<?php if ( $link ) : ?>
							<div class="contact-info-card__social">
								<span class="contact-info-card__label"><?php esc_html_e( 'CONNECT', 'lsc-group' ); ?></span>
								<div class="contact-info-card__social-link-wrap">
									<span class="contact-info-card__social-icon" aria-hidden="true">
										<?php if ( function_exists( 'lsc_get_icon_svg' ) ) echo lsc_get_icon_svg( 'linkedin', 'contact-icon' ); ?>
									</span>
									<a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener" class="contact-info-card__social-link">
										<?php esc_html_e( 'Follow us on LinkedIn', 'lsc-group' ); ?>
									</a>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="contact-section__col contact-section__col--form">
				<?php if ( $form_card ) : ?>
					<div class="contact-form-card">
						<?php if ( ! empty( $form_card['title'] ) ) : ?>
							<h3 class="contact-form-card__title"><?php echo esc_html( $form_card['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $form_card['form_code'] ) ) : ?>
							<div class="contact-form-card__form">
								<?php echo do_shortcode( $form_card['form_code'] ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
