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

<?php
// Whether this hero paints a background <video>. When it does we render a real
// <img> poster behind it: a deferred <video> never produces a paint (its bytes
// are skipped on mobile), so Chrome would otherwise pick the <video> as the LCP
// and resolve it absurdly late (~16s). The <img> gives Chrome a fast, real LCP;
// the video fades in over it once it plays on desktop.
$hero_has_video = false;

if ( $rotation_active ) {
	foreach ( $rotating_slides as $rotation_slide ) {
		if ( 'video' === ( $rotation_slide['media_type'] ?? 'image' ) && ! empty( $rotation_slide['video'] ) ) {
			$hero_has_video = true;
			break;
		}
	}
} elseif ( 'video' === $media_type && $video ) {
	$hero_has_video = true;
}

// Render the poster <img> for a hero video. Uses the SAME lsc-1600 URL the
// <video poster> resolves to (its own poster, else the base-image fallback), so
// the head preload is reused, not double-fetched. The first slide is the LCP:
// eager + fetchpriority=high; the rest are lazy.
$render_hero_poster = static function ( $video_data, $fallback_url, $is_first ) {
	$poster = ( is_array( $video_data ) && ! empty( $video_data['video_self_host_poster'] ) )
		? $video_data['video_self_host_poster']
		: null;

	$src    = '';
	$width  = 0;
	$height = 0;

	if ( is_array( $poster ) && ! empty( $poster['url'] ) ) {
		$src    = $poster['sizes']['lsc-1600'] ?? $poster['url'];
		$width  = (int) ( $poster['sizes']['lsc-1600-width'] ?? $poster['width'] ?? 0 );
		$height = (int) ( $poster['sizes']['lsc-1600-height'] ?? $poster['height'] ?? 0 );
	} elseif ( $fallback_url ) {
		$src = $fallback_url;
	}

	if ( ! $src ) {
		return;
	}

	printf(
		'<img class="hero-section__poster" src="%s" alt=""%s decoding="async" loading="%s"%s>',
		esc_url( $src ),
		( $width && $height ) ? ' width="' . $width . '" height="' . $height . '"' : '',
		$is_first ? 'eager' : 'lazy',
		$is_first ? ' fetchpriority="high"' : ''
	);
};
?>

<?php // The hero carries the page's main <h1>, so it is a <div>: its heading titles <main>, not a peer sub-section. ?>
<div class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>"<?php echo $rotation_id ? ' id="' . esc_attr( $rotation_id ) . '"' : ''; ?>>
	<div class="hero-section__media" aria-hidden="true">
		<?php
		if ( $hero_has_video && empty( $GLOBALS['lsc_hero_poster_style_printed'] ) ) :
			$GLOBALS['lsc_hero_poster_style_printed'] = true;
			?>
			<style>
				/* Stack the poster <img> and the deferred video in one grid cell. */
				.hero-section--media-video .hero-section__media { display: grid; }
				.hero-section--media-video .hero-section__media > * { grid-column: 1; grid-row: 1; }
				.hero-section__media-slide { display: grid; }
				.hero-section__media-slide > * { grid-column: 1; grid-row: 1; }
				/* The poster is the painted LCP; the video fades in over it once it
				   actually plays (desktop). Until then — and always on mobile — the
				   poster shows and no video bytes are fetched. */
				.hero-section__video { opacity: 0; transition: opacity 0.4s ease; }
				.hero-section__video.is-playing { opacity: 1; }
				@media ( prefers-reduced-motion: reduce ) {
					.hero-section__video { transition: none; }
				}
			</style>
			<?php
		endif;
		?>
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
						// Poster <img> = the painted LCP, behind the deferred video.
						$render_hero_poster( $slide_video, $hero_fallback_poster, $slide_is_first );

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
								// Every slide is deferred (preload="none", no autoplay) so
								// no video competes with the LCP poster on load. The rotator
								// JS plays them — but only on desktop, so mobile downloads
								// zero video and the poster image alone is the LCP.
								'defer'           => true,
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
			// Poster <img> = the painted LCP, behind the deferred video.
			$render_hero_poster( $video, $hero_fallback_poster, 0 === $section_index );

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
					// Deferred (preload="none", no autoplay) so the poster — not the
					// video — is the LCP. Started by the inline script below on desktop
					// only; mobile keeps the poster and downloads no video.
					'defer'           => true,
					'poster'          => $hero_fallback_poster,
				]
			);
			?>
			<?php
			// Start non-rotating hero videos. Desktop: as soon as the DOM is ready.
			// Mobile: AFTER the page loads so the deferred video never competes with the
			// LCP poster on cellular. Reduced-motion users keep the static poster. Once.
			if ( empty( $GLOBALS['lsc_hero_video_script_printed'] ) ) :
				$GLOBALS['lsc_hero_video_script_printed'] = true;
				?>
				<script>
					( function () {
						if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) { return; }
						var isDesktop = ! window.matchMedia || window.matchMedia( '(min-width: 768px)' ).matches;
						function startHeroVideos() {
							var vids = document.querySelectorAll( '.hero-section:not(.hero-section--rotating) .hero-section__video' );
							for ( var i = 0; i < vids.length; i++ ) {
								( function ( v ) {
									// Reveal (fade in over the poster) only once a frame is ready.
									v.addEventListener( 'playing', function () { v.classList.add( 'is-playing' ); } );
									var p = v.play();
									if ( p && p.catch ) { p.catch( function () {} ); }
								} )( vids[ i ] );
							}
						}
						if ( isDesktop ) {
							if ( 'loading' === document.readyState ) {
								document.addEventListener( 'DOMContentLoaded', startHeroVideos );
							} else {
								startHeroVideos();
							}
						} else if ( 'complete' === document.readyState ) {
							window.setTimeout( startHeroVideos, 200 );
						} else {
							window.addEventListener( 'load', function () { window.setTimeout( startHeroVideos, 200 ); } );
						}
					} )();
				</script>
				<?php
			endif;
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
			// scripts.js / style.css build pipeline.
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
						   every other title part keeps style.css's per-part line break.
						   Specificity beats style.css ".hero-section__title span { display: block }". */
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

					// Media SWAP (rotation) is desktop-only; slide 0's video also plays on
					// mobile but only after load (see below) so it never competes with the
					// LCP poster. The other slides' videos never load on mobile.
					var isDesktop = ! window.matchMedia || window.matchMedia( '(min-width: 768px)' ).matches;

					// Play the incoming slide's video (deferred slides have preload="none"
					// + no autoplay, so .play() is what actually fetches them) and pause the
					// outgoing one so only the visible slide ever decodes.
					function videoIn( slide ) { return slide ? slide.querySelector( 'video' ) : null; }
					function playSlide( i ) {
						var v = videoIn( media[ i ] );
						if ( ! v ) { return; }
						if ( ! v.getAttribute( 'data-reveal-bound' ) ) {
							v.setAttribute( 'data-reveal-bound', '1' );
							// Fade the video in over the poster only once a frame is ready.
							v.addEventListener( 'playing', function () { v.classList.add( 'is-playing' ); } );
						}
						var p = v.play();
						if ( p && p.catch ) { p.catch( function () {} ); }
					}
					function pauseSlide( i ) {
						var v = videoIn( media[ i ] );
						if ( v && ! v.paused ) { v.pause(); }
					}

										// Slide 0 is already active; play its video, then rotate words + media.
					var index = 0;
					function tick() {
						var prev = index;
						index = ( index + 1 ) % count;
						if ( media[ prev ] ) { media[ prev ].classList.remove( 'is-active' ); pauseSlide( prev ); }
						if ( media[ index ] ) { media[ index ].classList.add( 'is-active' ); playSlide( index ); }
						if ( words[ prev ] ) { words[ prev ].classList.remove( 'is-active' ); }
						if ( words[ index ] ) { words[ index ].classList.add( 'is-active' ); }
					}
					function startRotation() {
						playSlide( 0 );
						window.setInterval( tick, <?php echo (int) $rotation_interval; ?> );
					}
					// Desktop starts immediately. Mobile waits for the load event so the deferred
					// videos never compete with the LCP poster on cellular — the poster wins the
					// LCP, then words + background media rotate on mobile too (client requirement).
					if ( isDesktop ) {
						startRotation();
					} else if ( 'complete' === document.readyState ) {
						window.setTimeout( startRotation, 200 );
					} else {
						window.addEventListener( 'load', function () { window.setTimeout( startRotation, 200 ); } );
					}
				} )();
			</script>
			<?php
		endif;
		?>
</div>
