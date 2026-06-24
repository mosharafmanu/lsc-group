<?php
/**
 * Media + Content 50/50 Section
 *
 * Alternating 50/50: heading + copy + checklist on one side, image or
 * video on the other. Optional background (toggle) which Faisal styles with
 * its colour + padding; off = plain, no padding. The gap above the section is
 * a baked-in default margin-top (no editor control needed).
 *
 * @package lsc-group
 */

$enable_background = get_sub_field( 'enable_background' );
$eyebrow        = get_sub_field( 'eyebrow' );
$title_lines    = get_sub_field( 'title_lines' );
$description    = get_sub_field( 'description' );
$features       = get_sub_field( 'features' );
$buttons        = get_sub_field( 'buttons' );
$media_position = get_sub_field( 'media_position' ) ?: 'right';
$media_type     = get_sub_field( 'media_type' ) ?: 'image';
$image          = get_sub_field( 'image' );
$video          = get_sub_field( 'video' );

if ( ! $eyebrow && ! $title_lines && ! $description && ! $features && ! $buttons && ! $image && ! $video ) {
	return;
}

$is_video   = 'video' === $media_type && $video;
$show_image = $image && ! $is_video;

$section_classes = [
	'media-content-5050',
	'media-content-5050--media-' . sanitize_html_class( $media_position ),
	// Default top gap between sections (managed here, not by the editor).
	'mt-50',
	'mt-lg-90',
];

// Background is a toggle; Faisal's CSS gives this modifier its colour + padding.
if ( $enable_background ) {
	$section_classes[] = 'media-content-5050--has-bg';
}
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="lsc-container layout-padding">
		<div class="media-content-5050__grid">
			<div class="media-content-5050__media">
				<?php if ( $is_video && function_exists( 'lsc_render_video' ) ) : ?>
					<?php
					$video_behavior = $video['video_behavior'] ?? 'autoplay';

					lsc_render_video(
						$video,
						[
							'behavior'           => $video_behavior,
							'autoplay'           => ! empty( $video['video_autoplay'] ),
							'autoplay_on_scroll' => ! empty( $video['video_autoplay_on_scroll'] ),
							'controls'           => 'autoplay' === $video_behavior && ! empty( $video['video_controls'] ),
							'muted'              => ! empty( $video['video_muted'] ),
							'loop'               => ! empty( $video['video_loop'] ),
							'popup_autoplay'     => ! empty( $video['video_popup_autoplay'] ),
							'popup_controls'     => ! empty( $video['video_popup_controls'] ),
							'class'              => 'media-content-5050__video',
							'container_class'    => 'media-content-5050__video-wrap',
						]
					);
					?>
				<?php elseif ( $show_image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
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
					</figure>
				<?php endif; ?>
			</div>

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

				<?php if ( $features && is_array( $features ) ) : ?>
					<ul class="media-content-5050__features">
						<?php foreach ( $features as $feature ) : ?>
							<?php
							$feature_label = $feature['label'] ?? '';

							if ( ! $feature_label ) {
								continue;
							}
							?>
							<li class="media-content-5050__feature"><?php echo esc_html( $feature_label ); ?></li>
						<?php endforeach; ?>
					</ul>
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
		</div>
	</div>
</section>
