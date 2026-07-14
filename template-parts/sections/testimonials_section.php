<?php
/**
 * Testimonials Section
 *
 * @package lsc-group
 */

$eyebrow     = get_sub_field( 'eyebrow' );
$title_lines = get_sub_field( 'title_lines' );
$description = get_sub_field( 'description' );
$layout      = get_sub_field( 'layout' ) ?: 'carousel';
$source      = get_sub_field( 'source' ) ?: 'manual';

// Normalise both sources into one shape:
// [ rating, quote, author_name, author_role, author_initial, theme ].
$items = [];

if ( 'library' === $source ) {
	$selection = get_sub_field( 'library_selection' ) ?: 'all';
	$posts     = [];

	if ( 'selected' === $selection ) {
		$selected = get_sub_field( 'selected_testimonials' );

		if ( $selected && is_array( $selected ) ) {
			foreach ( $selected as $selected_post ) {
				$post_id = is_object( $selected_post ) ? $selected_post->ID : (int) $selected_post;
				$post    = $post_id ? get_post( $post_id ) : null;

				if ( $post && 'testimonial' === $post->post_type && 'publish' === $post->post_status ) {
					$posts[] = $post;
				}
			}
		}
	} else {
		$per_page = get_sub_field( 'posts_per_page' );

		$query = new WP_Query(
			[
				'post_type'      => 'testimonial',
				'post_status'    => 'publish',
				'posts_per_page' => $per_page ? (int) $per_page : -1,
				'orderby'        => get_sub_field( 'orderby' ) ?: 'menu_order',
				'order'          => get_sub_field( 'order' ) ?: 'ASC',
				'no_found_rows'  => true,
			]
		);

		if ( $query->have_posts() ) {
			$posts = $query->posts;
		}
	}

	foreach ( $posts as $post ) {
		$items[] = [
			'rating'         => get_field( 'rating', $post->ID ),
			'quote'          => get_field( 'quote', $post->ID ),
			'author_name'    => get_the_title( $post->ID ),
			'author_role'    => get_field( 'author_role', $post->ID ),
			'author_initial' => get_field( 'author_initial', $post->ID ),
			'theme'          => 'auto',
		];
	}
} else {
	$manual = get_sub_field( 'testimonials' );
	$items  = is_array( $manual ) ? $manual : [];
}

if ( ! $title_lines && ! $description && ! $items ) {
	return;
}

$section_classes = [
	'testimonials-section',
	'testimonials-section--' . sanitize_html_class( $layout ),
	'layout-padding pt-50 pb-50 pt-lg-100 pb-lg-60 layout-padding0',
];

// Background palette cycled by position when a testimonial's theme is "auto".
$stacked_palette = [ 'dark', 'orange', 'light' ];
?>

<?php
// Always a <section>: it groups heading-level testimonials (the h3 author names),
// so it needs to be a named region that contains them. Fall back to an sr-only
// heading when the editor sets no visible title.
$lsc_has_title = ! empty( $title_lines ) && is_array( $title_lines );
?>
<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<?php if ( ! $lsc_has_title ) : ?>
		<h2 class="sr-only"><?php esc_html_e( 'Testimonials', 'lsc-group' ); ?></h2>
	<?php endif; ?>
	<div class="section-header testimonials-section__header lsc-container">
		<?php if ( $eyebrow ) : ?>
			<div class="testimonials-section__eyebrow-wrap layout-padding-mobile">
				<span class="section-header__eyebrow testimonials-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			</div>
		<?php endif; ?>

		<div class="testimonials-section__intro layout-padding-mobile">
			<?php if ( $title_lines || $description ) : ?>
				<span class="section-header__divider" aria-hidden="true"></span>
			<?php endif; ?>

			<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
				<h2 class="section-header__title testimonials-section__title">
					<?php foreach ( $title_lines as $title_line ) : ?>
						<?php
						$line_parts       = $title_line['line_parts'] ?? [];
						$line_has_content = false;

						if ( $line_parts && is_array( $line_parts ) ) {
							foreach ( $line_parts as $line_part ) {
								if ( ! empty( $line_part['text'] ) ) {
									$line_has_content = true;
									break;
								}
							}
						}

						if ( ! $line_has_content ) {
							continue;
						}
						?>
						<span class="testimonials-section__title-line">
							<?php foreach ( $line_parts as $line_part ) : ?>
								<?php
								$part_text    = $line_part['text'] ?? '';
								$is_highlight = ! empty( $line_part['highlight'] );

								if ( ! $part_text ) {
									continue;
								}

								$part_classes = [ 'testimonials-section__title-part' ];

								if ( $is_highlight ) {
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
				<div class="section-header__description testimonials-section__description">
					<?php echo wp_trim_words( $description, 27, '...'); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $items ) : ?>
		<?php if ( 'stacked' === $layout ) : ?>
			<div class="testimonials-section__stack lsc-container layout-padding mt-40 mt-lg-60">
				<?php foreach ( $items as $index => $testimonial ) : ?>
					<?php
					$quote          = $testimonial['quote'] ?? '';
					$author_name    = $testimonial['author_name'] ?? '';
					$author_role    = $testimonial['author_role'] ?? '';
					$author_initial = $testimonial['author_initial'] ?? ( $author_name ? substr( $author_name, 0, 1 ) : '' );
					$theme          = $testimonial['theme'] ?? 'auto';

					if ( ! $quote ) {
						continue;
					}

					// Resolve "auto" to the position-cycled palette colour.
					if ( ! $theme || 'auto' === $theme ) {
						$theme = $stacked_palette[ $index % count( $stacked_palette ) ];
					}
					?>
					<div class="testimonial-card testimonial-card--stacked testimonial-card--theme-<?php echo esc_attr( sanitize_html_class( $theme ) ); ?>">
						<div class="testimonial-card__quote-icon" aria-hidden="true">
							<?php get_template_part( 'assets/svgs/quote' ); ?>
						</div>

						<div class="testimonial-card__body">
							<blockquote class="testimonial-card__quote">
								<p><?php echo esc_html( $quote ); ?></p>
							</blockquote>
						</div>

						<div class="testimonial-card__author">
							<?php if ( $author_initial ) : ?>
								<div class="testimonial-card__author-initial" aria-hidden="true">
									<?php echo esc_html( strtoupper( $author_initial ) ); ?>
								</div>
							<?php endif; ?>
							<div class="testimonial-card__author-info">
								<?php if ( $author_name ) : ?>
									<h3 class="testimonial-card__author-name"><?php echo esc_html( $author_name ); ?></h3>
								<?php endif; ?>
								<?php if ( $author_role ) : ?>
									<p class="testimonial-card__author-role"><?php echo esc_html( $author_role ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
		<div class="testimonials-section__slider-wrap lsc-container layout-inset mt-60">
			<div class="testimonials-section__carousel js-testimonials-carousel js-stage-padding">
				<?php foreach ( $items as $index => $testimonial ) : ?>
					<?php
					$rating         = intval( $testimonial['rating'] ?? 5 );
					$quote          = $testimonial['quote'] ?? '';
					$author_name    = $testimonial['author_name'] ?? '';
					$author_role    = $testimonial['author_role'] ?? '';
					$author_initial = $testimonial['author_initial'] ?? ( $author_name ? substr( $author_name, 0, 1 ) : '' );

					if ( ! $quote ) {
						continue;
					}

					$card_classes = [ 'testimonial-card' ];

					if ( 1 === $index ) {
						$card_classes[] = 'is-featured';
					}
					?>
					<div class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
						<div class="testimonial-card__quote-watermark" aria-hidden="true">
							<?php get_template_part( 'assets/svgs/quote-watermark' ); ?>
						</div>

						<div class="testimonial-card__header">
							<?php if ( $rating > 0 ) : ?>
								<div class="testimonial-card__rating">
									<?php for ( $i = 0; $i < $rating; $i++ ) : ?>
										<span class="testimonial-card__star" aria-hidden="true">
											<?php get_template_part( 'assets/svgs/star' ); ?>
										</span>
									<?php endfor; ?>
								</div>
							<?php endif; ?>

							<div class="testimonial-card__quote-icon" aria-hidden="true">
								<?php get_template_part( 'assets/svgs/quote' ); ?>
							</div>
						</div>

						<div class="testimonial-card__body">
							<blockquote class="testimonial-card__quote">
								<p><?php echo esc_html( $quote ); ?></p>
							</blockquote>
						</div>

						<div class="testimonial-card__author">
							<?php if ( $author_initial ) : ?>
								<div class="testimonial-card__author-initial" aria-hidden="true">
									<?php echo esc_html( strtoupper( $author_initial ) ); ?>
								</div>
							<?php endif; ?>
							<div class="testimonial-card__author-info">
								<?php if ( $author_name ) : ?>
									<h3 class="testimonial-card__author-name"><?php echo esc_html( $author_name ); ?></h3>
								<?php endif; ?>
								<?php if ( $author_role ) : ?>
									<p class="testimonial-card__author-role"><?php echo esc_html( $author_role ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="testimonials-section__arrows lsc-group-slick-arrow-container" role="group" aria-label="<?php esc_attr_e( 'Testimonial carousel controls', 'lsc-group' ); ?>">
				<button class="lsc-group-slick-arrow testimonials-section__arrow testimonials-section__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'lsc-group' ); ?>">
					<?php get_template_part( 'assets/svgs/angle-left-pagination' ); ?>
				</button>
				<button class="lsc-group-slick-arrow testimonials-section__arrow testimonials-section__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'lsc-group' ); ?>">
					<?php get_template_part( 'assets/svgs/angle-right-pagination' ); ?>
				</button>
			</div>
		</div>
		<?php endif; ?>
	<?php endif; ?>
</section>
