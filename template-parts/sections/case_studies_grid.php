<?php
/**
 * Case Studies Grid Section
 *
 * Header (accent divider, heading, intro) above a grid of case-study cards
 * (featured image, title, excerpt, "Read Case Study" link). Pulls from the
 * Case Study CPT — all published or a hand-picked selection.
 *
 * @package lsc-group
 */

$eyebrow             = get_sub_field( 'eyebrow' );
$title               = get_sub_field( 'title' );
$description         = get_sub_field( 'description' );
$case_study_source   = get_sub_field( 'case_study_source' ) ?: 'all';
$selected_studies    = get_sub_field( 'selected_case_studies' );
$posts_per_page      = get_sub_field( 'posts_per_page' );
$columns             = get_sub_field( 'columns' ) ?: 'columns-3';
$orderby             = get_sub_field( 'orderby' ) ?: 'menu_order';
$order               = get_sub_field( 'order' ) ?: 'ASC';

$posts_per_page = $posts_per_page ? (int) $posts_per_page : -1;

$case_studies = [];

if ( 'selected' === $case_study_source && $selected_studies && is_array( $selected_studies ) ) {
	foreach ( $selected_studies as $selected_study ) {
		$study_id = is_object( $selected_study ) ? $selected_study->ID : (int) $selected_study;

		if ( $study_id ) {
			$study = get_post( $study_id );

			if ( $study && 'case_study' === $study->post_type && 'publish' === $study->post_status ) {
				$case_studies[] = $study;
			}
		}
	}
} else {
	$study_query = new WP_Query(
		[
			'post_type'      => 'case_study',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'orderby'        => $orderby,
			'order'          => $order,
		]
	);

	if ( $study_query->have_posts() ) {
		$case_studies = $study_query->posts;
	}
}

if ( ! $eyebrow && ! $title && ! $description && ! $case_studies ) {
	return;
}

$grid_classes = [
	'case-studies-grid',
	'card-grid',
	'card-grid--center-last-row',
	sanitize_html_class( $columns ),
];
?>

<section class="case-studies-section mt-50 mt-lg-70 mt-lg-150">
	<div class="case-studies-section__inner lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
		<?php if ( $eyebrow || $title || $description ) : ?>
			<header class="section-header case-studies-section__header">
				<span class="section-header__divider" aria-hidden="true"></span>

				<?php if ( $eyebrow ) : ?>
					<p class="section-header__eyebrow case-studies-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="section-header__title case-studies-section__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="section-header__description case-studies-section__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $case_studies ) : ?>
			<div class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?> mt-30 mt-lg-50">
				<?php foreach ( $case_studies as $case_study ) : ?>
						<?php lsc_render_case_study_card( $case_study->ID ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
if ( isset( $study_query ) && $study_query instanceof WP_Query ) {
	wp_reset_postdata();
}
