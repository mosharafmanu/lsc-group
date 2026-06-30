<?php
/**
 * Feature Columns (3-Column) Section
 *
 * Left: intro content (eyebrow, heading, copy, checklist).
 * Middle: a stack of small info cards (title + copy).
 * Right: a highlighted action card (title, copy, buttons) stacked over an image.
 *
 * @package lsc-group
 */

$eyebrow          = get_sub_field( 'eyebrow' );
$title_lines      = get_sub_field( 'title_lines' );
$description      = get_sub_field( 'description' );
$features         = get_sub_field( 'features' );
$info_cards       = get_sub_field( 'info_cards' );
$card_title       = get_sub_field( 'card_title' );
$card_description = get_sub_field( 'card_description' );
$card_buttons     = get_sub_field( 'card_buttons' );
$media_type       = get_sub_field( 'media_type' ) ?: 'image';
$image            = get_sub_field( 'image' );
$video            = get_sub_field( 'video' );

if ( ! $eyebrow && ! $title_lines && ! $description && ! $features && ! $info_cards && ! $card_title && ! $card_buttons && ! $image && ! $video ) {
	return;
}
?>

<?php $lsc_section_el = ( ! empty( $title_lines ) && is_array( $title_lines ) ) ? 'section' : 'div'; ?>
<<?php echo $lsc_section_el; ?> class="feature-columns pt-50 pb-50 pt-lg-100">
	<div class="feature-columns__inner lsc-container layout-padding">
		<div class="feature-columns__content">
			<?php if ( $eyebrow ) : ?>
				<p class="feature-columns__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
				<h2 class="feature-columns__title">
					<?php foreach ( $title_lines as $title_line ) : ?>
						<?php
						$line_parts = $title_line['line_parts'] ?? [];

						if ( ! $line_parts || ! is_array( $line_parts ) ) {
							continue;
						}
						?>
						<span class="feature-columns__title-line">
							<?php foreach ( $line_parts as $line_part ) : ?>
								<?php
								$part_text = $line_part['text'] ?? '';

								if ( ! $part_text ) {
									continue;
								}

								$part_classes = [ 'feature-columns__title-part' ];

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
				<div class="feature-columns__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $features && is_array( $features ) ) : ?>
				<ul class="lsc-list feature-columns__features">
					<?php foreach ( $features as $feature ) : ?>
						<?php
						$feature_label = $feature['label'] ?? '';

						if ( ! $feature_label ) {
							continue;
						}
						?>
						<li class="feature-columns__feature"><?php echo esc_html( $feature_label ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="feature-columns-cards-right">
			<?php if ( $info_cards && is_array( $info_cards ) ) : ?>
			<div class="feature-columns__cards">
				<?php foreach ( $info_cards as $info_card ) : ?>
					<?php
					$info_title       = $info_card['title'] ?? '';
					$info_description = $info_card['description'] ?? '';

					if ( ! $info_title && ! $info_description ) {
						continue;
					}
					?>
					<div class="feature-columns__info-card">
						<?php if ( $info_title ) : ?>
							<p class="feature-columns__info-card-title"><?php echo esc_html( $info_title ); ?></p>
						<?php endif; ?>

						<?php if ( $info_description ) : ?>
							<div class="feature-columns__info-card-description"><?php echo wp_kses_post( nl2br( $info_description ) ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="feature-columns__aside">
			<?php if ( $card_title || $card_description || $card_buttons ) : ?>
				<div class="feature-columns__card">
					<?php if ( $card_title ) : ?>
						<p class="feature-columns__card-title"><?php echo esc_html( $card_title ); ?></p>
					<?php endif; ?>

					<?php if ( $card_description ) : ?>
						<div class="feature-columns__card-description"><?php echo wp_kses_post( nl2br( $card_description ) ); ?></div>
					<?php endif; ?>

					<?php if ( $card_buttons && is_array( $card_buttons ) ) : ?>
						<div class="feature-columns__card-buttons btns">
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
										'class'     => 'site-btn--block',
										'show_icon' => false,
									]
								);
								?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( 'video' === $media_type && $video && function_exists( 'lsc_render_video' ) ) : ?>
				<figure class="feature-columns__figure media">
					<?php
					$fc_behavior = $video['video_behavior'] ?? 'autoplay';

					lsc_render_video(
						$video,
						[
							'behavior'           => $fc_behavior,
							'autoplay'           => ! empty( $video['video_autoplay'] ),
							'autoplay_on_scroll' => ! empty( $video['video_autoplay_on_scroll'] ),
							'controls'           => 'autoplay' === $fc_behavior && ! empty( $video['video_controls'] ),
							'muted'              => ! empty( $video['video_muted'] ),
							'loop'               => ! empty( $video['video_loop'] ),
							'popup_autoplay'     => ! empty( $video['video_popup_autoplay'] ),
							'popup_controls'     => ! empty( $video['video_popup_controls'] ),
							'class'              => 'feature-columns__video',
							'container_class'    => 'feature-columns__video-wrap',
						]
					);
					?>
				</figure>
			<?php elseif ( $image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
				<figure class="feature-columns__figure media">
					<?php
					lsc_render_responsive_picture(
						$image,
						[
							'class' => 'feature-columns__image',
							'sizes' => '(max-width: 991px) 100vw, 25vw',
						]
					);
					?>
				</figure>
			<?php endif; ?>
		</div>
		</div>
	</div>
</<?php echo $lsc_section_el; ?>>
