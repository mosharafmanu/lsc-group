<?php
/**
 * Content + Card 50/50 Section
 *
 * Content column (eyebrow, heading, copy, checklist) beside an aside column
 * holding a highlighted card (title, copy, buttons) stacked over an image.
 *
 * @package lsc-group
 */

$eyebrow          = get_sub_field( 'eyebrow' );
$title_lines      = get_sub_field( 'title_lines' );
$description      = get_sub_field( 'description' );
$features         = get_sub_field( 'features' );
$card_position    = get_sub_field( 'card_position' ) ?: 'right';
$card_title       = get_sub_field( 'card_title' );
$card_description = get_sub_field( 'card_description' );
$card_buttons     = get_sub_field( 'card_buttons' );
$image            = get_sub_field( 'image' );

if ( ! $eyebrow && ! $title_lines && ! $description && ! $features && ! $card_title && ! $card_buttons && ! $image ) {
	return;
}

$section_classes = [
	'content-card-5050',
	'content-card-5050--card-' . sanitize_html_class( $card_position ),
];
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?> pt-50 pb-50 pt-lg-100">
	<div class="content-card-5050__inner card-<?php echo esc_attr( sanitize_html_class( $card_position ) ); ?> lsc-container layout-padding">
		<div class="content-card-5050__content">
			<?php if ( $eyebrow ) : ?>
				<p class="content-card-5050__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
				<h2 class="content-card-5050__title">
					<?php foreach ( $title_lines as $title_line ) : ?>
						<?php
						$line_parts = $title_line['line_parts'] ?? [];

						if ( ! $line_parts || ! is_array( $line_parts ) ) {
							continue;
						}
						?>
						<span class="content-card-5050__title-line">
							<?php foreach ( $line_parts as $line_part ) : ?>
								<?php
								$part_text = $line_part['text'] ?? '';

								if ( ! $part_text ) {
									continue;
								}

								$part_classes = [ 'content-card-5050__title-part' ];

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
				<div class="content-card-5050__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $features && is_array( $features ) ) : ?>
				<ul class="lsc-list content-card-5050__features">
					<?php foreach ( $features as $feature ) : ?>
						<?php
						$feature_label = $feature['label'] ?? '';

						if ( ! $feature_label ) {
							continue;
						}
						?>
						<li class="content-card-5050__feature"><?php echo esc_html( $feature_label ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="content-card-5050__aside">
			<?php if ( $card_title || $card_description || $card_buttons ) : ?>
				<div class="content-card-5050__card">
					<?php if ( $card_title ) : ?>
						<p class="content-card-5050__card-title"><?php echo esc_html( $card_title ); ?></p>
					<?php endif; ?>

					<?php if ( $card_description ) : ?>
						<div class="content-card-5050__card-description"><?php echo wp_kses_post( nl2br( $card_description ) ); ?></div>
					<?php endif; ?>

					<?php if ( $card_buttons && is_array( $card_buttons ) ) : ?>
						<div class="content-card-5050__card-buttons btns">
							<?php foreach ( $card_buttons as $button ) : ?>
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
										'class'     => 'content-card-5050__card-button',
										'show_icon' => false,
									]
								);
								?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
				<figure class="content-card-5050__figure media">
					<?php
					lsc_render_responsive_picture(
						$image,
						[
							'class' => 'content-card-5050__image',
							'sizes' => '(max-width: 991px) 100vw, 40vw',
						]
					);
					?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
