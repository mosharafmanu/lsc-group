<?php
/**
 * Case Study Card
 *
 * Renders one case study card — featured image, title, excerpt and a
 * "Read Case Study" link. Shared by the `case_studies_grid` section and the
 * single case study template's Related Case Studies block.
 *
 * @param int $args['post_id'] Case study post ID.
 * @package lsc-group
 */

$study_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;

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
