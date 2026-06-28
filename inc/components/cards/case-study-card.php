<?php
/**
 * Case Study card component.
 *
 * Renders one case study card — featured image, title, excerpt and a
 * "Read Case Study" link. Shared by the case studies grid/list sections and
 * the single case study template's Related Case Studies block.
 *
 * @package lsc-group
 */

if ( ! function_exists( 'lsc_render_case_study_card' ) ) {
	/**
	 * @param int|null $post_id Case study post ID (defaults to the current post).
	 * @param array    $args    Reserved for future options.
	 */
	function lsc_render_case_study_card( $post_id = null, $args = [] ) {
		$study_id = $post_id ? absint( $post_id ) : get_the_ID();

		if ( ! $study_id ) {
			return;
		}

		$study_title = get_the_title( $study_id );
		$url         = get_permalink( $study_id );
		$excerpt     = has_excerpt( $study_id ) ? get_the_excerpt( $study_id ) : '';
		?>
		<article class="case-study-card">
			<?php if ( has_post_thumbnail( $study_id ) ) : ?>
				<a class="case-study-card__media" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read %s', 'lsc-group' ), $study_title ) ); ?>">
					<?php echo get_the_post_thumbnail( $study_id, 'large', [ 'class' => 'case-study-card__image' ] ); ?>
				</a>
			<?php endif; ?>

			<div class="case-study-card__content">
				<?php if ( $study_title ) : ?>
					<h6 class="case-study-card__title">
						<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $study_title ); ?></a>
					</h6>
				<?php endif; ?>

				<?php if ( $excerpt ) : ?>
					<p class="case-study-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<a class="case-study-card__link" href="<?php echo esc_url( $url ); ?>">
					<span><?php esc_html_e( 'Read Case Study', 'lsc-group' ); ?></span>
					<span aria-hidden="true">-&gt;</span>
				</a>
			</div>
		</article>
		<?php
	}
}
