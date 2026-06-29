<?php
/**
 * Broker Callout Section
 *
 * Orange callout card: image on one side, heading + body copy + buttons on the other.
 *
 * @package lsc-group
 */

$title_lines    = get_sub_field( 'title_lines' );
$description     = get_sub_field( 'description' );
$chips          = get_sub_field( 'chips' );
$media_position = get_sub_field( 'media_position' ) ?: 'left';
$media_type     = get_sub_field( 'media_type' ) ?: 'image';
$image          = get_sub_field( 'image' );
$video          = get_sub_field( 'video' );

if ( ! $title_lines && ! $description && ! $chips && ! $image && ! $video ) {
	return;
}

$section_classes = [
	'broker-callout',
	'broker-callout--media-' . sanitize_html_class( $media_position ),
	'broker-callout--' . sanitize_html_class( $media_type ),
];
?>

<?php $lsc_section_el = ( ! empty( $title_lines ) && is_array( $title_lines ) ) ? 'section' : 'div'; ?>
<<?php echo $lsc_section_el; ?> class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?> pt-50 pb-50 pt-lg-100 pb-lg-110">
	<div class="broker-callout__inner lsc-container layout-padding">
		<div class="broker-callout__card media-<?php echo esc_attr( sanitize_html_class( $media_position ) ); ?>">
			<div class="broker-callout__media">
				<?php if ( 'video' === $media_type && $video && function_exists( 'lsc_render_video' ) ) : ?>
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
							'class'              => 'broker-callout__video',
							'container_class'    => 'broker-callout__video-wrap media',
						]
					);
					?>
				<?php elseif ( $image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
					<figure class="broker-callout__figure media">
						<?php
						lsc_render_responsive_picture(
							$image,
							[
								'class' => 'broker-callout__image',
								'sizes' => '(max-width: 991px) 100vw, 50vw',
							]
						);
						?>
					</figure>
				<?php endif; ?>
			</div>

			<div class="broker-callout__content">
				<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
					<h2 class="broker-callout__title">
						<?php foreach ( $title_lines as $title_line ) : ?>
							<?php
							$line_parts = $title_line['line_parts'] ?? [];

							if ( ! $line_parts || ! is_array( $line_parts ) ) {
								continue;
							}
							?>
							<span class="broker-callout__title-line">
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text = $line_part['text'] ?? '';

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'broker-callout__title-part' ];

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
					<div class="broker-callout__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $chips && is_array( $chips ) ) : ?>
					<div class="broker-callout__chips">
						<?php foreach ( $chips as $chip ) : ?>
							<?php
							$chip_label = $chip['label'] ?? '';
							$chip_link  = $chip['link'] ?? [];

							if ( ! $chip_label ) {
								continue;
							}

							$chip_url    = is_array( $chip_link ) ? ( $chip_link['url'] ?? '' ) : '';
							$chip_target = is_array( $chip_link ) ? ( $chip_link['target'] ?? '' ) : '';
							?>
							<?php if ( $chip_url ) : ?>
								<a class="broker-callout__chip" href="<?php echo esc_url( $chip_url ); ?>"<?php echo $chip_target ? ' target="' . esc_attr( $chip_target ) . '"' : ''; ?>>
									<?php echo esc_html( $chip_label ); ?>
								</a>
							<?php else : ?>
								<span class="broker-callout__chip"><?php echo esc_html( $chip_label ); ?></span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</<?php echo $lsc_section_el; ?>>
