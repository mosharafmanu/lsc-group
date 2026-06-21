<?php
/**
 * Testimonials Section
 *
 * @package lsc-group
 */

$eyebrow          = get_sub_field( 'eyebrow' );
$title_lines      = get_sub_field( 'title_lines' );
$description      = get_sub_field( 'description' );
$testimonials     = get_sub_field( 'testimonials' );

if ( ! $title_lines && ! $description && ! $testimonials ) {
	return;
}

$section_classes = [
	'testimonials-section',
];
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="testimonials-section__header lsc-container layout-padding pt-50 pb-50 pt-lg-100 pb-lg-100">
		<?php if ( $eyebrow ) : ?>
			<div class="testimonials-section__eyebrow-wrap">
				<span class="testimonials-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			</div>
		<?php endif; ?>

		<div class="testimonials-section__intro">
			<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
				<h2 class="testimonials-section__title">
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
				<div class="testimonials-section__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $testimonials && is_array( $testimonials ) ) : ?>
		<div class="testimonials-section__grid card-grid columns-3 lsc-container mt-50 mt-md-60">
			<?php foreach ( $testimonials as $index => $testimonial ) : ?>
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
								<p class="testimonial-card__author-name"><?php echo esc_html( $author_name ); ?></p>
							<?php endif; ?>
							<?php if ( $author_role ) : ?>
								<p class="testimonial-card__author-role"><?php echo esc_html( $author_role ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
