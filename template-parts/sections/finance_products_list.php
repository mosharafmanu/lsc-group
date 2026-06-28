<?php
/**
 * Finance Products List Section (machine name: finance_products_list)
 *
 * A paginated listing of all published Finance Products as cards — built for the
 * Products page. Unlike `finance_products_grid` (which shows a fixed, optionally
 * hand-picked set), this section always queries all products and paginates them
 * `posts_per_page` at a time.
 *
 * Paging uses the `fp_page` query var (`?fp_page=N`) so it works reliably when
 * embedded in a static Page, avoiding the `/page/N/` canonical-redirect quirk.
 * Reuses the shared `.finance-products-section` / card-grid styling.
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

$paged = max( 1, (int) get_query_var( 'fp_page' ) );

$product_query = new WP_Query(
	[
		'post_type'      => 'finance_product',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'paged'          => $paged,
		'orderby'        => $orderby,
		'order'          => $order,
	]
);

if ( ! $eyebrow && ! $title && ! $description && ! $product_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

$section_classes = [
	'finance-products-section',
	'finance-products-section--list',
	'bg-lsc-subtle',
];

$grid_classes = [
	'finance-products-grid',
	'card-grid',
	'card-grid--center-last-row',
	'card-grid--inset',
	sanitize_html_class( $columns ),
];
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="finance-products-section__inner lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
		<?php if ( $eyebrow || $title || $description ) : ?>
			<header class="finance-products-section__header">
				<?php if ( $eyebrow ) : ?>
					<p class="finance-products-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="finance-products-section__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="finance-products-section__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $product_query->have_posts() ) : ?>
			<div class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?> mt-30 mt-lg-65">
				<?php foreach ( $product_query->posts as $product ) : ?>
					<?php lsc_render_finance_product_card( $product->ID ); ?>
				<?php endforeach; ?>
			</div>

			<?php
			if ( $product_query->max_num_pages > 1 && function_exists( 'lsc_render_pagination' ) ) {
				lsc_render_pagination( $product_query, 'fp_page' );
			}
			?>
		<?php endif; ?>
	</div>
</section>

<?php
wp_reset_postdata();
