<?php
/**
 * Inner Hero Section
 *
 * Compact page hero for inner pages. Background image (no video),
 * eyebrow badge, heading, description and buttons.
 *
 * @package lsc-group
 */

$eyebrow     = get_sub_field( 'eyebrow' );
$title_lines = get_sub_field( 'title_lines' );
$description = get_sub_field( 'description' );
$buttons     = get_sub_field( 'buttons' );
$image       = get_sub_field( 'image' );

if ( ! $eyebrow && ! $title_lines && ! $description && ! $buttons && ! $image ) {
	return;
}
?>

<section class="inner-hero">
	<?php if ( $image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
		<div class="inner-hero__media" aria-hidden="true">
			<?php
			lsc_render_responsive_picture(
				$image,
				[
					'class'         => 'inner-hero__image',
					'sizes'         => '100vw',
					'lazy'          => false,
					'fetchpriority' => 'high',
				]
			);
			?>
		</div>
	<?php endif; ?>

	<div class="inner-hero__inner lsc-container layout-padding">
		<div class="inner-hero__content">
			<?php if ( $eyebrow ) : ?>
				<p class="inner-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
				<h1 class="inner-hero__title">
					<?php foreach ( $title_lines as $title_line ) : ?>
						<?php
						$line_parts = $title_line['line_parts'] ?? [];

						if ( ! $line_parts || ! is_array( $line_parts ) ) {
							continue;
						}
						?>
						<span class="inner-hero__title-line">
							<?php foreach ( $line_parts as $line_part ) : ?>
								<?php
								$part_text = $line_part['text'] ?? '';

								if ( ! $part_text ) {
									continue;
								}

								$part_classes = [ 'inner-hero__title-part' ];

								if ( ! empty( $line_part['highlight'] ) ) {
									$part_classes[] = 'color-lsc-accent';
								}
								?>
								<span class="<?php echo esc_attr( implode( ' ', $part_classes ) ); ?>"><?php echo esc_html( $part_text ); ?></span>
							<?php endforeach; ?>
						</span>
					<?php endforeach; ?>
				</h1>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<div class="inner-hero__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $buttons && is_array( $buttons ) ) : ?>
				<div class="inner-hero__buttons btns">
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
								'class'     => 'inner-hero__button',
								'show_icon' => false,
							]
						);
						?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
