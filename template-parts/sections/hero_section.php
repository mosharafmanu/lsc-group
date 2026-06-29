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

// Fallback poster for video slides that don't carry their own ACF poster — keeps
// the first slide's LCP a real image instead of a black frame, and gives deferred
// slides a still to show before their video loads.
$hero_fallback_poster = is_array( $image ) ? ( $image['sizes']['lsc-1600'] ?? ( $image['url'] ?? '' ) ) : '';

// Rotating final word + synced background media.
$enable_rotation   = get_sub_field( 'enable_word_rotation' );
$rotation_interval = (int) get_sub_field( 'rotation_interval' );
$rotation_interval = $rotation_interval >= 1000 ? $rotation_interval : 3000;
$rotating_slides   = $enable_rotation ? get_sub_field( 'rotating_slides' ) : [];

// Keep only slides that actually carry a rotating word.
$rotating_slides = is_array( $rotating_slides )
	? array_values(
		array_filter(
			$rotating_slides,
			static function ( $slide ) {
				return ! empty( $slide['word'] );
			}
		)
	)
	: [];

// Rotation needs at least two slides to cycle.
$rotation_active = $enable_rotation && count( $rotating_slides ) >= 2;
$rotation_id     = $rotation_active ? wp_unique_id( 'hero-rotation-' ) : '';

// Pre-compute the title lines that have content so we know which is last.
$valid_title_lines = [];

if ( $title_lines && is_array( $title_lines ) ) {
	foreach ( $title_lines as $title_line ) {
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

		if ( $line_has_content ) {
			$valid_title_lines[] = $title_line;
		}
	}
}

$last_line_index = count( $valid_title_lines ) - 1;

if ( ! $eyebrow && ! $valid_title_lines && ! $legacy_title && ! $description && ! $image && ! $video && ! $rotation_active ) {
	return;
}

$section_classes = [
	'hero-section',
	'hero-section--media-' . sanitize_html_class( $media_type ),
];

if ( $rotation_active ) {
	$section_classes[] = 'hero-section--rotating';
}

if ( 0 === $section_index ) {
	$section_classes[] = 'hero-section--first';
}

?>

<?php // The hero carries the page's main <h1>, so it is a <div>: its heading titles <main>, not a peer sub-section. ?>
<div class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>"<?php echo $rotation_id ? ' id="' . esc_attr( $rotation_id ) . '"' : ''; ?>>
	<div class="hero-section__media" aria-hidden="true">
		<?php if ( $rotation_active ) : ?>
			<?php foreach ( $rotating_slides as $slide_index => $slide ) : ?>
				<?php
				$slide_type    = $slide['media_type'] ?? 'image';
				$slide_image   = $slide['image'] ?? null;
				$slide_mobile  = $slide['mobile_image'] ?? null;
				$slide_video   = $slide['video'] ?? null;
				$slide_is_first = 0 === $slide_index && 0 === $section_index;

				// Fall back to the base hero media if a slide has none of its own.
				if ( 'video' !== $slide_type && ! $slide_image && ! $slide_video ) {
					$slide_image  = $image;
					$slide_mobile = $slide_mobile ?: $mobile_image;
				}

				$slide_classes = [ 'hero-section__media-slide' ];

				if ( 0 === $slide_index ) {
					$slide_classes[] = 'is-active';
				}
				?>
				<div class="<?php echo esc_attr( implode( ' ', $slide_classes ) ); ?>" data-slide-index="<?php echo (int) $slide_index; ?>">
					<?php if ( 'video' === $slide_type && $slide_video && function_exists( 'lsc_render_video' ) ) : ?>
						<?php
						lsc_render_video(
							$slide_video,
							[
								'behavior'        => 'autoplay',
								'autoplay'        => true,
								'class'           => 'hero-section__video',
								'container_class' => 'hero-section__video-wrap',
								'controls'        => false,
								'muted'           => true,
								'loop'            => true,
								// Only the first slide loads eagerly; the rest wait for
								// the rotator to play them (preload="none", no autoplay).
								'defer'           => ! $slide_is_first,
								'poster'          => $hero_fallback_poster,
							]
						);
						?>
					<?php elseif ( $slide_image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
						<?php
						lsc_render_responsive_picture(
							$slide_image,
							[
								'class'         => 'hero-section__image',
								'sizes'         => '100vw',
								'lazy'          => $slide_is_first ? 0 : 1,
								'fetchpriority' => $slide_is_first ? 'high' : 'auto',
							]
						);
						?>
					<?php endif; ?>

					<?php if ( 'video' !== $slide_type && $slide_mobile && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
						<div class="hero-section__mobile-image">
							<?php
							lsc_render_responsive_picture(
								$slide_mobile,
								[
									'class'         => 'hero-section__image hero-section__image--mobile',
									'sizes'         => '100vw',
									'lazy'          => $slide_is_first ? 0 : 1,
									'fetchpriority' => $slide_is_first ? 'high' : 'auto',
								]
							);
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php elseif ( 'video' === $media_type && $video && function_exists( 'lsc_render_video' ) ) : ?>
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

		<?php if ( ! $rotation_active && 'image' === $media_type && $mobile_image && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
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

				<?php
				// Inline stack of rotating words, appended after the final title line.
				$render_title_rotator = static function () use ( $rotation_active, $rotating_slides ) {
					if ( ! $rotation_active ) {
						return;
					}
					?>
					<span class="hero-section__title-rotator" data-hero-rotator>
						<?php foreach ( $rotating_slides as $word_index => $slide ) : ?>
							<?php
							$word_classes = [ 'hero-section__title-word' ];

							if ( 0 === $word_index ) {
								$word_classes[] = 'is-active';
							}

							if ( ! empty( $slide['highlight'] ) ) {
								$word_classes[] = 'color-lsc-accent';
							}
							?>
							<span class="<?php echo esc_attr( implode( ' ', $word_classes ) ); ?>" data-slide-index="<?php echo (int) $word_index; ?>"><?php echo esc_html( $slide['word'] ); ?></span>
						<?php endforeach; ?>
					</span>
					<?php
				};
				?>

					<?php if ( $valid_title_lines ) : ?>
						<h1 class="hero-section__title">
							<?php foreach ( $valid_title_lines as $line_index => $title_line ) : ?>
								<?php
								// Normalize this line to a flat list of { text, highlight } parts.
								$line_parts = $title_line['line_parts'] ?? [];
								$parts      = [];

								if ( $line_parts && is_array( $line_parts ) ) {
									foreach ( $line_parts as $line_part ) {
										if ( ! empty( $line_part['text'] ) ) {
											$parts[] = [
												'text'      => $line_part['text'],
												'highlight' => ! empty( $line_part['highlight'] ),
											];
										}
									}
								} elseif ( ! empty( $title_line['text'] ) ) {
									$parts[] = [
										'text'      => $title_line['text'],
										'highlight' => ! empty( $title_line['highlight'] ),
									];
								}

								$is_rotating_line = $rotation_active && $line_index === $last_line_index;
								$group_part       = null;

								if ( $is_rotating_line ) {
									// The rotating word REPLACES the last word of the title, so the
									// static copy never duplicates it.
									if ( $parts ) {
										$last_part_index = count( $parts ) - 1;
										$words           = preg_split( '/\s+/', trim( $parts[ $last_part_index ]['text'] ) );
										array_pop( $words );
										$remaining = implode( ' ', $words );

										if ( '' === $remaining ) {
											array_pop( $parts ); // The whole part was the rotating word.
										} else {
											$parts[ $last_part_index ]['text'] = $remaining;
										}
									}

									// Keep the final remaining word inline with the rotator (its own
									// block line) without flattening the line breaks between parts.
									if ( $parts ) {
										$group_part = array_pop( $parts );
									}
								}
								?>
								<span class="hero-section__title-line">
									<?php foreach ( $parts as $part ) : ?>
										<?php
										$part_classes = [ 'hero-section__title-part' ];

										if ( $part['highlight'] ) {
											$part_classes[] = 'color-lsc-accent';
										}
										?>
										<span class="<?php echo esc_attr( implode( ' ', $part_classes ) ); ?>"><?php echo esc_html( $part['text'] ); ?></span>
									<?php endforeach; ?>
									<?php if ( $is_rotating_line ) : ?>
										<span class="hero-section__title-rotating-group">
											<?php if ( $group_part ) : ?>
												<?php
												$group_part_classes = [ 'hero-section__title-part' ];

												if ( $group_part['highlight'] ) {
													$group_part_classes[] = 'color-lsc-accent';
												}
												?>
												<span class="<?php echo esc_attr( implode( ' ', $group_part_classes ) ); ?>"><?php echo esc_html( $group_part['text'] ); ?></span>
											<?php endif; ?>
											<?php $render_title_rotator(); ?>
										</span>
									<?php endif; ?>
								</span>
							<?php endforeach; ?>
						</h1>
					<?php elseif ( $legacy_title || $rotation_active ) : ?>
						<h1 class="hero-section__title">
							<span class="hero-section__title-line">
								<?php if ( $rotation_active ) : ?>
									<span class="hero-section__title-rotating-group">
										<?php if ( $legacy_title ) : ?>
											<span class="hero-section__title-part"><?php echo esc_html( $legacy_title ); ?></span>
										<?php endif; ?>
										<?php $render_title_rotator(); ?>
									</span>
								<?php else : ?>
									<span class="hero-section__title-part"><?php echo esc_html( $legacy_title ); ?></span>
								<?php endif; ?>
							</span>
						</h1>
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

	<?php
		if ( $rotation_active ) :
			// Cross-fade styles: scoped to rotating heroes, printed once per page.
			// Kept inline so they reach the browser independent of the cached
			// scripts.js / faisal.css build pipeline.
			if ( empty( $GLOBALS['lsc_hero_rotation_style_printed'] ) ) :
				$GLOBALS['lsc_hero_rotation_style_printed'] = true;
				?>
					<style>
						/* Background media slides stack in one grid cell and cross-fade. */
						.hero-section--rotating .hero-section__media { display: grid; }
						.hero-section--rotating .hero-section__media-slide {
							grid-area: 1 / 1;
							opacity: 0;
							transition: opacity 150ms ease-in-out;
						}
						.hero-section--rotating .hero-section__media-slide.is-active { opacity: 1; }
						.hero-section--rotating .hero-section__media-slide video {
							width: 100%;
							height: 100%;
							object-fit: cover;
						}
						/* Per-slide desktop/mobile image swap (slides only carry both when a mobile image is set). */
						.hero-section--rotating .hero-section__mobile-image { display: none; }
						@media ( max-width: 767px ) {
							.hero-section--rotating .hero-section__media-slide:has( .hero-section__mobile-image ) > picture { display: none; }
							.hero-section--rotating .hero-section__mobile-image { display: block; }
						}
						/* Only the final word + rotator go inline (their own block line);
						   every other title part keeps faisal.css's per-part line break.
						   Specificity beats faisal.css ".hero-section__title span { display: block }". */
						.hero-section__title .hero-section__title-rotating-group {
							display: flex;
							flex-wrap: wrap;
							align-items: baseline;
							column-gap: 0.25em;
						}
						.hero-section__title .hero-section__title-rotator { display: grid; }
						.hero-section__title .hero-section__title-word {
							grid-area: 1 / 1;
							opacity: 0;
							transition: opacity 150ms ease-in-out;
						}
						.hero-section__title .hero-section__title-word.is-active { opacity: 1; }
						@media ( prefers-reduced-motion: reduce ) {
							.hero-section--rotating .hero-section__media-slide,
							.hero-section__title .hero-section__title-word { transition: none; }
						}
					</style>
				<?php
			endif;
			?>
			<script>
				( function () {
					var root = document.getElementById( '<?php echo esc_js( $rotation_id ); ?>' );
					if ( ! root ) { return; }
					if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) { return; }

					var media = root.querySelectorAll( '.hero-section__media-slide' );
					var words = root.querySelectorAll( '.hero-section__title-word' );
					var count = Math.max( media.length, words.length );
					if ( count < 2 ) { return; }

					// Play the incoming slide's video (deferred slides have preload="none"
					// + no autoplay, so .play() is what actually fetches them) and pause the
					// outgoing one so only the visible slide ever decodes.
					function videoIn( slide ) { return slide ? slide.querySelector( 'video' ) : null; }
					function playSlide( i ) {
						var v = videoIn( media[ i ] );
						if ( v ) { var p = v.play(); if ( p && p.catch ) { p.catch( function () {} ); } }
					}
					function pauseSlide( i ) {
						var v = videoIn( media[ i ] );
						if ( v && ! v.paused ) { v.pause(); }
					}

					var index = 0;
					window.setInterval( function () {
						var prev = index;
						index = ( index + 1 ) % count;
						if ( media[ prev ] ) { media[ prev ].classList.remove( 'is-active' ); pauseSlide( prev ); }
						if ( media[ index ] ) { media[ index ].classList.add( 'is-active' ); playSlide( index ); }
						if ( words[ prev ] ) { words[ prev ].classList.remove( 'is-active' ); }
						if ( words[ index ] ) { words[ index ].classList.add( 'is-active' ); }
					}, <?php echo (int) $rotation_interval; ?> );
				} )();
			</script>
			<?php
		endif;
		?>
</div>
