<?php
/**
 * Full-Width Media Section
 *
 * A single full-width media block — image or video — selected via the Media
 * Type toggle. Used for the lead media on a single case study (and reusable
 * anywhere a standalone media band is needed). Rounded corners and sizing are
 * Faisal's CSS; this template emits BEM hooks only.
 *
 * @package lsc-group
 */

$media_type = get_sub_field( 'media_type' ) ?: 'image';
$image      = get_sub_field( 'image' );
$video      = get_sub_field( 'video' );

$is_video   = 'video' === $media_type && $video;
$show_image = $image && ! $is_video;

if ( ! $is_video && ! $show_image ) {
	return;
}

$section_classes = [
	'media-full',
	'media-full--' . ( $is_video ? 'video' : 'image' ),
];
?>

<div class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="lsc-container layout-padding">
		<div class="media-full__media">
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
						'class'              => 'media-full__video',
						'container_class'    => 'media-full__video-wrap',
					]
				);
				?>
			<?php elseif ( $show_image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
				<figure class="media-full__figure">
					<?php
					lsc_render_responsive_picture(
						$image,
						[
							'class' => 'media-full__image',
							'sizes' => '100vw',
						]
					);
					?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</div>
