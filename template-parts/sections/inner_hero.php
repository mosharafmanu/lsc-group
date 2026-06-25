<?php
/**
 * Page Hero Section (machine name: inner_hero)
 *
 * One hero for inner pages with three selectable styles:
 *   - image : dark hero, heading over a full-width background photo
 *   - text  : light hero, heading + text on a plain background
 *   - split : light hero, heading + text beside a side photo
 *
 * Default style is "image" so existing placements render unchanged.
 *
 * @package lsc-group
 */

$hero_style     = get_sub_field( 'hero_style' ) ?: 'image';
$eyebrow        = get_sub_field( 'eyebrow' );
$title_lines    = get_sub_field( 'title_lines' );
$description    = get_sub_field( 'description' );
$buttons        = get_sub_field( 'buttons' );
$media_type     = get_sub_field( 'media_type' ) ?: 'image';
$image          = get_sub_field( 'image' );
$video          = get_sub_field( 'video' );
$image_position = get_sub_field( 'image_position' ) ?: 'right';
$show_facts_bar = get_sub_field( 'show_facts_bar' );

if ( ! $eyebrow && ! $title_lines && ! $description && ! $buttons && ! $image && ! $video ) {
	return;
}

// The "split" (Text + Media) style can show a video; image and background styles use the image.
$split_is_video = 'split' === $hero_style && 'video' === $media_type && $video;
// "text" style never shows media, even if one was uploaded before switching.
$show_image     = $image && 'text' !== $hero_style && ! $split_is_video;

// Product Key Facts bar: only on the image style, only when enabled, and only
// when the current Finance Product actually has facts. Pulls from product meta.
$product_facts = ( 'image' === $hero_style && $show_facts_bar ) ? get_field( 'product_facts' ) : null;
$has_facts_bar = $product_facts && is_array( $product_facts );

$section_classes = [ 'inner-hero ', 'inner-hero--' . $hero_style ];

if ( 'split' === $hero_style ) {
	$section_classes[] = 'inner-hero--image-' . $image_position;
}

if ( $has_facts_bar ) {
	$section_classes[] = 'inner-hero--has-facts';
}
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<?php if ( 'image' === $hero_style && $show_image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
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

		<?php if ( 'split' === $hero_style && $split_is_video && function_exists( 'lsc_render_video' ) ) : ?>
			<div class="inner-hero__media">
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
						'class'              => 'inner-hero__video',
						'container_class'    => 'inner-hero__video-wrap',
					]
				);
				?>
			</div>
		<?php elseif ( 'split' === $hero_style && $show_image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
			<div class="inner-hero__media">
				<?php
				lsc_render_responsive_picture(
					$image,
					[
						'class'         => 'inner-hero__image',
						'sizes'         => '(max-width: 991px) 100vw, 50vw',
						'lazy'          => false,
						'fetchpriority' => 'high',
					]
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $has_facts_bar ) : ?>
		<?php
		$facts_columns = min( max( count( $product_facts ), 2 ), 5 );
		?>
		<div class="inner-hero__facts-wrap lsc-container layout-padding">
			<div class="inner-hero__facts card-grid columns-<?php echo esc_attr( $facts_columns ); ?>">
				<?php foreach ( $product_facts as $fact ) : ?>
					<?php
					$fact_label     = $fact['label'] ?? '';
					$fact_value     = $fact['value'] ?? '';
					$fact_highlight = ! empty( $fact['highlight'] );

					if ( ! $fact_label && ! $fact_value ) {
						continue;
					}

					$fact_value_classes = [ 'inner-hero__fact-value' ];

					if ( $fact_highlight ) {
						$fact_value_classes[] = 'color-lsc-accent';
					}
					?>
					<div class="inner-hero__fact">
						<?php if ( $fact_label ) : ?>
							<span class="inner-hero__fact-label"><?php echo esc_html( $fact_label ); ?></span>
						<?php endif; ?>

						<?php if ( $fact_value ) : ?>
							<span class="<?php echo esc_attr( implode( ' ', $fact_value_classes ) ); ?>"><?php echo esc_html( $fact_value ); ?></span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
