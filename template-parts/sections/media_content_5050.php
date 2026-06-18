<?php
/**
 * Media Content 50/50 Section
 *
 * @package lsc-group
 */

$eyebrow        = get_sub_field( 'eyebrow' );
$title_lines    = get_sub_field( 'title_lines' );
$description    = get_sub_field( 'description' );
$buttons        = get_sub_field( 'buttons' );
$media_position = get_sub_field( 'media_position' ) ?: 'right';
$media_type     = get_sub_field( 'media_type' ) ?: 'image';
$image          = get_sub_field( 'image' );
$video          = get_sub_field( 'video' );
$media_label    = get_sub_field( 'media_label' );
$media_caption  = get_sub_field( 'media_caption' );

if ( ! $eyebrow && ! $title_lines && ! $description && ! $buttons && ! $image && ! $video ) {
	return;
}

$section_classes = [
	'media-content-5050',
	'media-content-5050--media-' . sanitize_html_class( $media_position ),
	'media-content-5050--' . sanitize_html_class( $media_type ),
];
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="media-content-5050__inner lsc-container layout-padding">
		<div class="media-content-5050__content">
			<?php if ( $eyebrow ) : ?>
				<p class="media-content-5050__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
				<h2 class="media-content-5050__title">
					<?php foreach ( $title_lines as $title_line ) : ?>
						<?php
						$line_parts = $title_line['line_parts'] ?? [];

						if ( ! $line_parts || ! is_array( $line_parts ) ) {
							continue;
						}
						?>
						<span class="media-content-5050__title-line">
							<?php foreach ( $line_parts as $line_part ) : ?>
								<?php
								$part_text = $line_part['text'] ?? '';

								if ( ! $part_text ) {
									continue;
								}

								$part_classes = [ 'media-content-5050__title-part' ];

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
				<div class="media-content-5050__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $buttons && is_array( $buttons ) ) : ?>
				<div class="media-content-5050__buttons btns">
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
								'class'     => 'media-content-5050__button',
								'show_icon' => false,
							]
						);
						?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="media-content-5050__media">
			<?php if ( 'video' === $media_type && $video && function_exists( 'lsc_render_video' ) ) : ?>
				<?php
				lsc_render_video(
					$video,
					[
						'behavior'        => 'autoplay',
						'autoplay'        => true,
						'class'           => 'media-content-5050__video',
						'container_class' => 'media-content-5050__video-wrap',
						'controls'        => false,
						'muted'           => true,
						'loop'            => true,
					]
				);
				?>
			<?php elseif ( $image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
				<figure class="media-content-5050__figure">
					<?php
					lsc_render_responsive_picture(
						$image,
						[
							'class' => 'media-content-5050__image',
							'sizes' => '(max-width: 991px) 100vw, 50vw',
						]
					);
					?>

					<?php if ( $media_label || $media_caption ) : ?>
						<figcaption class="media-content-5050__caption">
							<?php if ( $media_label ) : ?>
								<span class="media-content-5050__caption-label"><?php echo esc_html( $media_label ); ?></span>
							<?php endif; ?>

							<?php if ( $media_caption ) : ?>
								<span class="media-content-5050__caption-text"><?php echo esc_html( $media_caption ); ?></span>
							<?php endif; ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
