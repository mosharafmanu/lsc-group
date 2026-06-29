<?php
/**
 * Quote Block Section
 *
 * A single dark pull-quote block (quote mark + quote + author name/role +
 * avatar). Built for the case study left column and reusable anywhere.
 *
 * Source toggle (mirrors testimonials_section): "Manual" = a one-off quote
 * typed into this section, or "Testimonial Library" = one testimonial pulled
 * from the Testimonial CPT. Both sources normalise to one shape before output.
 *
 * CSS is Faisal's; this template emits BEM hooks only.
 *
 * @package lsc-group
 */

$source = get_sub_field( 'source' ) ?: 'manual';

$quote       = '';
$author_name = '';
$author_role = '';
$avatar      = false;
$initial     = '';

if ( 'library' === $source ) {
	$selected      = get_sub_field( 'testimonial' );
	$testimonial   = is_array( $selected ) ? ( $selected[0] ?? null ) : $selected;

	if ( $testimonial instanceof WP_Post ) {
		$quote       = (string) get_field( 'quote', $testimonial->ID );
		$author_name = get_the_title( $testimonial->ID );
		$author_role = (string) get_field( 'author_role', $testimonial->ID );
		$initial     = (string) get_field( 'author_initial', $testimonial->ID );
	}
} else {
	$quote       = (string) get_sub_field( 'quote' );
	$author_name = (string) get_sub_field( 'author_name' );
	$author_role = (string) get_sub_field( 'author_role' );
	$avatar      = get_sub_field( 'avatar' );
}

if ( ! $quote ) {
	return;
}

// Initial fallback from the author name when there is no avatar image.
if ( ! $initial && $author_name ) {
	$initial = mb_substr( $author_name, 0, 1 );
}

$has_author = $author_name || $author_role || $avatar || $initial;
?>

<div class="quote-block">
	<div class="lsc-container layout-padding">
		<figure class="quote-block__card">
			<span class="quote-block__mark" aria-hidden="true">
				<?php get_template_part( 'assets/svgs/quote' ); ?>
			</span>

			<blockquote class="quote-block__quote"><?php echo esc_html( $quote ); ?></blockquote>

			<?php if ( $has_author ) : ?>
				<figcaption class="quote-block__author">
					<span class="quote-block__avatar">
						<?php if ( $avatar && function_exists( 'lsc_render_responsive_picture' ) ) : ?>
							<?php
							lsc_render_responsive_picture(
								$avatar,
								[
									'class' => 'quote-block__avatar-img',
									'sizes' => '48px',
								]
							);
							?>
						<?php elseif ( $initial ) : ?>
							<span class="quote-block__avatar-initial"><?php echo esc_html( $initial ); ?></span>
						<?php endif; ?>
					</span>

					<?php if ( $author_name || $author_role ) : ?>
						<span class="quote-block__meta">
							<?php if ( $author_name ) : ?>
								<span class="quote-block__name"><?php echo esc_html( $author_name ); ?></span>
							<?php endif; ?>
							<?php if ( $author_role ) : ?>
								<span class="quote-block__role"><?php echo esc_html( $author_role ); ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</figcaption>
			<?php endif; ?>
		</figure>
	</div>
</div>
