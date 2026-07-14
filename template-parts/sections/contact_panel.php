<?php
/**
 * Contact Panel Section
 *
 * Two cards designed to overlap the hero above: an enquiry form (left) and a
 * contact information card with a map below it (right). A self-contained
 * "Overlap The Hero Above" toggle adds the `--overlap` modifier; the
 * negative-margin overlap is Faisal's CSS.
 *
 * Contact details (email / phone / office address / opening hours) come from
 * Site Settings via lsc_get_footer_contact_details(). This is a separate
 * section from the general `contact_section` — its info/form markup is copied
 * here on purpose. CSS is Faisal's; this template emits BEM hooks only.
 *
 * @package lsc-group
 */

$form_title  = get_sub_field( 'form_title' );
$form_code   = get_sub_field( 'form_code' );
$info_title  = get_sub_field( 'info_title' );
$map_embed   = get_sub_field( 'map_embed' );
$overlap     = get_sub_field( 'overlap_hero' );

$info_items = function_exists( 'lsc_get_contact_items_for' ) ? lsc_get_contact_items_for( 'contact_panel' ) : [];
$hours_item = $info_items['hours'] ?? null;

$has_form = $form_title || $form_code;
$has_info = $info_title || (bool) $info_items;

if ( ! $has_form && ! $has_info && ! $map_embed ) {
	return;
}

$section_classes = [ 'contact-panel pb-50 pb-lg-100' ];

if ( $overlap ) {
	$section_classes[] = 'contact-panel--overlap';
}
?>

<?php $lsc_section_el = ( ! empty( $form_title ) || ! empty( $info_title ) ) ? 'section' : 'div'; ?>
<<?php echo $lsc_section_el; ?> class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="lsc-container layout-padding">
		<div class="contact-panel__grid">

			<?php if ( $has_form ) : ?>
				<div class="contact-panel__form">
					<div class="contact-form-card">
						<?php if ( $form_title ) : ?>
							<h2 class="contact-form-card__title"><?php echo esc_html( $form_title ); ?></h2>
						<?php endif; ?>

						<?php if ( $form_code ) : ?>
							<div class="contact-form-card__form">
								<?php echo do_shortcode( $form_code ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $has_info || $map_embed ) : ?>
				<div class="contact-panel__aside">

					<?php if ( $has_info ) : ?>
						<div class="contact-panel__info">
							<div class="contact-info-card">
								<?php if ( $info_title ) : ?>
									<h2 class="contact-info-card__title"><?php echo esc_html( $info_title ); ?></h2>
								<?php endif; ?>

								<?php
								$panel_labels = [
									'email'   => __( 'EMAIL', 'lsc-group' ),
									'phone'   => __( 'TELEPHONE', 'lsc-group' ),
									'address' => __( 'OFFICE ADDRESS', 'lsc-group' ),
								];
								?>
								<ul class="contact-info-card__list">
									<?php foreach ( $info_items as $item ) : ?>
										<?php if ( ! isset( $panel_labels[ $item['key'] ] ) ) { continue; } ?>
										<li class="contact-info-card__item">
											<span class="contact-info-card__icon" aria-hidden="true">
												<?php lsc_render_contact_icon( $item, 'contact-icon' ); ?>
											</span>
											<div class="contact-info-card__details">
												<span class="contact-info-card__label"><?php echo esc_html( $panel_labels[ $item['key'] ] ); ?></span>
												<?php if ( 'address' === $item['key'] ) : ?>
													<address class="contact-info-card__value"><?php echo nl2br( esc_html( $item['value'] ) ); ?></address>
												<?php else : ?>
													<a href="<?php echo esc_url( $item['url'] ); ?>" class="contact-info-card__value"><?php echo esc_html( $item['value'] ); ?></a>
												<?php endif; ?>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>

								<?php if ( $hours_item ) : ?>
									<div class="contact-info-card__hours">
										<span class="contact-info-card__label"><?php esc_html_e( 'OPENING HOURS', 'lsc-group' ); ?></span>
										<p class="contact-info-card__value"><?php echo nl2br( esc_html( $hours_item['value'] ) ); ?></p>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $map_embed ) : ?>
						<div class="contact-panel__map">
							<?php echo do_shortcode( $map_embed ); ?>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>
	</div>
</<?php echo $lsc_section_el; ?>>
