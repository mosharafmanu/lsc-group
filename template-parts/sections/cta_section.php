<?php
/**
 * CTA Section
 *
 * Centered call-to-action band: heading, optional copy and buttons.
 *
 * @package lsc-group
 */

// Default to the Global CTA (Site Settings); switch to "custom" to override per page.
$content_source = get_sub_field( 'content_source' ) ?: 'global';

if ( 'custom' === $content_source ) {
	$eyebrow     = get_sub_field( 'eyebrow' );
	$title_lines = get_sub_field( 'title_lines' );
	$description = get_sub_field( 'description' );
	$buttons     = get_sub_field( 'buttons' );
	$background   = get_sub_field( 'background' ) ?: 'dark';
} else {
	$cta         = function_exists( 'lsc_get_global_cta' ) ? lsc_get_global_cta() : [];
	$eyebrow     = $cta['eyebrow'] ?? '';
	$title_lines = $cta['title_lines'] ?? '';
	$description = $cta['description'] ?? '';
	$buttons     = $cta['buttons'] ?? '';
	$background   = ( $cta['background'] ?? '' ) ?: 'dark';
}

if ( ! $eyebrow && ! $title_lines && ! $description && ! $buttons ) {
	return;
}

$section_classes = [
	'cta-section',
	'bg-lsc-' . sanitize_html_class( $background ),
];
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?> pt-50 pb-50 pt-lg-90 pb-lg-90">
	<div class="cta-section__inner lsc-container layout-padding">
		<?php if ( $eyebrow ) : ?>
			<p class="cta-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
			<h2 class="cta-section__title">
				<?php foreach ( $title_lines as $title_line ) : ?>
					<?php
					$line_parts = $title_line['line_parts'] ?? [];

					if ( ! $line_parts || ! is_array( $line_parts ) ) {
						continue;
					}
					?>
					<span class="cta-section__title-line">
						<?php foreach ( $line_parts as $line_part ) : ?>
							<?php
							$part_text = $line_part['text'] ?? '';

							if ( ! $part_text ) {
								continue;
							}

							$part_classes = [ 'cta-section__title-part' ];

							if ( ! empty( $line_part['highlight'] ) ) {
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
			<div class="cta-section__description">
				<?php echo wp_kses_post( $description ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $buttons && is_array( $buttons ) ) : ?>
			<div class="cta-section__buttons btns">
				<?php foreach ( $buttons as $button ) : ?>
					<?php
					$button_link  = $button['button_link'] ?? [];
					$button_style = $button['button_style'] ?? 'btn-primary';

					if ( ! $button_link || ! function_exists( 'lsc_render_button' ) ) {
						continue;
					}

					lsc_render_button(
						$button_link,
						[
							'style'     => $button_style,
							'class'     => 'cta-section__button',
							'show_icon' => false,
						]
					);
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
