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

$background_style = get_sub_field( 'background_style' ) ?: 'light';

$section_classes = [
	'contact-section',
	'contact-section--bg-' . $background_style,
	'pt-50 pb-50 pt-lg-90 pb-lg-90',
];

?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="lsc-container layout-padding">
		<?php if ( $title_lines || $description ) : ?>
			<div class="contact-section__header text-center">
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

		<div class="contact-section__grid mt-40 mt-lg-65">
			<div class="contact-section__col contact-section__col--info">
				<?php if ( $contact_info ) : ?>
					<div class="contact-info-card">
						<?php if ( ! empty( $contact_info['title'] ) ) : ?>
							<h5 class="contact-info-card__title"><?php echo esc_html( $contact_info['title'] ); ?></h5>
						<?php endif; ?>

						<?php if ( ! empty( $contact_info['text'] ) ) : ?>
							<p class="contact-info-card__text"><?php echo esc_html( $contact_info['text'] ); ?></p>
						<?php endif; ?>

						<?php
						$info_items    = function_exists( 'lsc_get_contact_items_for' ) ? lsc_get_contact_items_for( 'contact_section' ) : [];
						$info_labels   = [
							'address' => __( 'ADDRESS', 'lsc-group' ),
							'phone'   => __( 'TELEPHONE', 'lsc-group' ),
							'email'   => __( 'EMAIL', 'lsc-group' ),
						];
						$linkedin_item = $info_items['linkedin'] ?? null;
						?>

						<?php if ( $info_items ) : ?>
							<ul class="contact-info-card__list">
								<?php foreach ( $info_items as $item ) : ?>
									<?php if ( ! isset( $info_labels[ $item['key'] ] ) ) { continue; } ?>
									<li class="contact-info-card__item">
										<span class="contact-info-card__icon" aria-hidden="true">
											<?php lsc_render_contact_icon( $item, 'contact-icon' ); ?>
										</span>
										<div class="contact-info-card__details">
											<span class="contact-info-card__label"><?php echo esc_html( $info_labels[ $item['key'] ] ); ?></span>
											<?php if ( 'address' === $item['key'] ) : ?>
												<address class="contact-info-card__value"><?php echo nl2br( esc_html( $item['value'] ) ); ?></address>
											<?php else : ?>
												<a href="<?php echo esc_url( $item['url'] ); ?>" class="contact-info-card__value"><?php echo esc_html( $item['value'] ); ?></a>
											<?php endif; ?>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>

							<?php if ( $linkedin_item ) : ?>
								<div class="contact-info-card__social">
									<span class="contact-info-card__label"><?php esc_html_e( 'CONNECT', 'lsc-group' ); ?></span>
									<div class="contact-info-card__social-link-wrap">
										<span class="contact-info-card__social-icon" aria-hidden="true">
											<?php lsc_render_contact_icon( $linkedin_item, '' ); ?>
										</span>
										<a href="<?php echo esc_url( $linkedin_item['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="contact-info-card__social-link">
											<?php esc_html_e( 'Follow us on LinkedIn', 'lsc-group' ); ?>
										</a>
									</div>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="contact-section__col contact-section__col--form">
				<?php if ( $form_card ) : ?>
					<div class="contact-form-card">
						<?php if ( ! empty( $form_card['title'] ) ) : ?>
							<h5 class="contact-form-card__title"><?php echo esc_html( $form_card['title'] ); ?></h5>
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
