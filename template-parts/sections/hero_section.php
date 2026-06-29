<?php
/**
 * Hero Section
 *
 * @package lsc-group
 */

$eyebrow       = get_sub_field( 'eyebrow' );
$title_lines   = get_sub_field( 'title_lines' );
$legacy_title  = get_sub_field( 'title' );
$description   = get_sub_field( 'description' );
$buttons       = get_sub_field( 'buttons' );
$features      = get_sub_field( 'features' );
$media_type    = get_sub_field( 'media_type' ) ?: 'image';
$image         = get_sub_field( 'image' );
$mobile_image  = get_sub_field( 'mobile_image' );
$video         = get_sub_field( 'video' );
$section_index = isset( $GLOBALS['lsc_section_index'] ) ? (int) $GLOBALS['lsc_section_index'] : 0;

if ( ! $eyebrow && ! $title_lines && ! $legacy_title && ! $description && ! $image && ! $video ) {
	return;
}

$section_classes = [
	'hero-section',
	'hero-section--media-' . sanitize_html_class( $media_type ),
];

if ( 0 === $section_index ) {
	$section_classes[] = 'hero-section--first';
}

?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="hero-section__media" aria-hidden="true">
		<?php if ( 'video' === $media_type && $video && function_exists( 'lsc_render_video' ) ) : ?>
			<?php
			lsc_render_video(
				$video,
				[
					'behavior'        => 'autoplay',
					'autoplay'        => true,
					'class'           => 'hero-section__video',
					'container_class' => 'hero-section__video-wrap',
					'controls'        => false,
					'muted'           => true,
					'loop'            => true,
				]
			);
			?>
		<?php elseif ( $image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
			<?php
			lsc_render_responsive_picture(
				$image,
					[
						'class'         => 'hero-section__image',
						'sizes'         => '100vw',
						'lazy'          => 0 !== $section_index,
						'fetchpriority' => 0 === $section_index ? 'high' : 'auto',
					]
				);
			?>
		<?php endif; ?>

		<?php if ( 'image' === $media_type && $mobile_image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
			<div class="hero-section__mobile-image">
				<?php
				lsc_render_responsive_picture(
					$mobile_image,
					[
						'class'         => 'hero-section__image hero-section__image--mobile',
						'sizes'         => '100vw',
						'lazy'          => 0 !== $section_index,
						'fetchpriority' => 0 === $section_index ? 'high' : 'auto',
					]
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<div class="hero-section__overlay" aria-hidden="true"></div>

	<div class="hero-section__inner lsc-container layout-padding">
		<div class="hero-section__content">
			<?php if ( $eyebrow ) : ?>
				<p class="hero-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
				<h1 class="hero-section__title">
					<?php foreach ( $title_lines as $title_line ) : ?>
						<?php
						$line_parts       = $title_line['line_parts'] ?? [];
						$legacy_line_text = $title_line['text'] ?? '';
						$line_has_content = false;

						if ( $line_parts && is_array( $line_parts ) ) {
							foreach ( $line_parts as $line_part ) {
								if ( ! empty( $line_part['text'] ) ) {
									$line_has_content = true;
									break;
								}
							}
						} elseif ( $legacy_line_text ) {
							$line_has_content = true;
						}

						if ( ! $line_has_content ) {
							continue;
						}
						?>
						<span class="hero-section__title-line">
							<?php if ( $line_parts && is_array( $line_parts ) ) : ?>
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text    = $line_part['text'] ?? '';
									$is_highlight = ! empty( $line_part['highlight'] );

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'hero-section__title-part' ];

									if ( $is_highlight ) {
										$part_classes[] = 'color-lsc-accent';
									}
									?>
									<span class="<?php echo esc_attr( implode( ' ', $part_classes ) ); ?>"><?php echo esc_html( $part_text ); ?></span>
								<?php endforeach; ?>
							<?php else : ?>
								<?php
								$legacy_part_classes = [ 'hero-section__title-part' ];

								if ( ! empty( $title_line['highlight'] ) ) {
									$legacy_part_classes[] = 'color-lsc-accent';
								}
								?>
								<span class="<?php echo esc_attr( implode( ' ', $legacy_part_classes ) ); ?>"><?php echo esc_html( $legacy_line_text ); ?></span>
							<?php endif; ?>
						</span>
					<?php endforeach; ?>
				</h1>
			<?php elseif ( $legacy_title ) : ?>
				<h1 class="hero-section__title"><?php echo esc_html( $legacy_title ); ?></h1>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<div class="hero-section__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $buttons && is_array( $buttons ) ) : ?>
				<div class="hero-section__buttons btns">
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
								'class'     => 'site-btn--lg',
								'show_icon' => false,
							]
						);
						?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $features && is_array( $features ) ) : ?>
				<ul class="hero-section__features">
					<?php foreach ( $features as $feature ) : ?>
						<?php
						$feature_label = $feature['label'] ?? '';
						$feature_icon  = $feature['icon'] ?? null;

						if ( ! $feature_label ) {
							continue;
						}
						?>
						<li class="hero-section__feature">
							<?php if ( $feature_icon && function_exists( 'lsc_render_icon' ) ) : ?>
								<span class="hero-section__feature-icon" aria-hidden="true">
									<?php
									lsc_render_icon(
										$feature_icon,
										[
											'class' => 'hero-section__feature-icon-svg',
											'alt'   => '',
										]
									);
									?>
								</span>
							<?php endif; ?>
							<span class="hero-section__feature-label"><?php echo esc_html( $feature_label ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
