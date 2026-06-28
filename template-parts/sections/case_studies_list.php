<?php
/**
 * Case Studies List Section (machine name: case_studies_list)
 *
 * A paginated listing of all published Case Studies as cards — built for the
 * Case Studies page. Unlike `case_studies_grid` (which shows a fixed, optionally
 * hand-picked set), this section always queries all Case Studies and paginates
 * them `posts_per_page` at a time.
 *
 * Paging uses the `cs_page` query var (`?cs_page=N`) so it works reliably when
 * embedded in a static Page, avoiding the `/page/N/` canonical-redirect quirk.
 * Reuses the shared `.case-studies-section` / card-grid styling.
 *
 * @package lsc-group
 */

$eyebrow        = get_sub_field( 'eyebrow' );
$title          = get_sub_field( 'title' );
$description    = get_sub_field( 'description' );
$columns        = get_sub_field( 'columns' ) ?: 'columns-3';
$posts_per_page = (int) get_sub_field( 'posts_per_page' );
$orderby        = get_sub_field( 'orderby' ) ?: 'menu_order';
$order          = get_sub_field( 'order' ) ?: 'ASC';

if ( $posts_per_page < 1 ) {
	$posts_per_page = 6;
}

$paged = max( 1, (int) get_query_var( 'cs_page' ) );

$study_query = new WP_Query(
	[
		'post_type'      => 'case_study',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'paged'          => $paged,
		'orderby'        => $orderby,
		'order'          => $order,
	]
);

if ( ! $eyebrow && ! $title && ! $description && ! $study_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

$grid_classes = [
	'case-studies-grid',
	'card-grid',
	'card-grid--center-last-row',
	sanitize_html_class( $columns ),
];
?>

<section class="case-studies-section case-studies-section--list mt-50 mt-lg-150">
	<div class="case-studies-section__inner lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
		<?php if ( $eyebrow || $title || $description ) : ?>
			<header class="case-studies-section__header">
				<span class="case-studies-section__divider" aria-hidden="true"></span>

				<?php if ( $eyebrow ) : ?>
					<p class="case-studies-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="case-studies-section__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="case-studies-section__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $study_query->have_posts() ) : ?>
			<div class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?> mt-30 mt-lg-50">
				<?php foreach ( $study_query->posts as $case_study ) : ?>
					<?php lsc_render_case_study_card( $case_study->ID ); ?>
				<?php endforeach; ?>
			</div>

			<?php
			if ( $study_query->max_num_pages > 1 && function_exists( 'lsc_render_pagination' ) ) {
				lsc_render_pagination( $study_query );
			}
			?>
		<?php endif; ?>
	</div>
</section>

<?php
wp_reset_postdata();
