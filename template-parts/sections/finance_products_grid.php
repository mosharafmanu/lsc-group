<?php
/**
 * Finance Products Grid Section
 *
 * @package lsc-group
 */

$eyebrow          = get_sub_field( 'eyebrow' );
$title            = get_sub_field( 'title' );
$description      = get_sub_field( 'description' );
$product_source   = get_sub_field( 'product_source' ) ?: 'all';
$selected_products = get_sub_field( 'selected_products' );
$posts_per_page   = get_sub_field( 'posts_per_page' );
$columns          = get_sub_field( 'columns' ) ?: 'columns-3';
$orderby          = get_sub_field( 'orderby' ) ?: 'menu_order';
$order            = get_sub_field( 'order' ) ?: 'ASC';

$posts_per_page = $posts_per_page ? (int) $posts_per_page : -1;

$section_classes = [
	'finance-products-section',
	'bg-lsc-subtle',
];

$products = [];

if ( 'selected' === $product_source && $selected_products && is_array( $selected_products ) ) {
	foreach ( $selected_products as $selected_product ) {
		$product_id = is_object( $selected_product ) ? $selected_product->ID : (int) $selected_product;

		if ( $product_id ) {
			$product = get_post( $product_id );

			if ( $product && 'finance_product' === $product->post_type && 'publish' === $product->post_status ) {
				$products[] = $product;
			}
		}
	}
} else {
	$product_query = new WP_Query(
		[
			'post_type'      => 'finance_product',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'orderby'        => $orderby,
			'order'          => $order,
		]
	);

	if ( $product_query->have_posts() ) {
		$products = $product_query->posts;
	}
}

if ( ! $eyebrow && ! $title && ! $description && ! $products ) {
	return;
}
?>

<?php $lsc_section_el = ( ! empty( $title ) ) ? 'section' : 'div'; ?>
<<?php echo $lsc_section_el; ?> class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="finance-products-section__inner lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
		<?php if ( $eyebrow || $title || $description ) : ?>
			<header class="section-header finance-products-section__header">
					<span class="section-header__divider" aria-hidden="true"></span>

				<?php if ( $eyebrow ) : ?>
					<p class="section-header__eyebrow finance-products-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="section-header__title finance-products-section__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="section-header__description finance-products-section__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $products ) : ?>
			<div class="finance-products-grid card-grid card-grid--center-last-row layout-inset <?php echo esc_attr( sanitize_html_class( $columns ) ); ?> mt-30 mt-lg-65">
					<?php foreach ( $products as $product ) : ?>
						<?php lsc_render_finance_product_card( $product->ID ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
	</div>
</<?php echo $lsc_section_el; ?>>

<?php
if ( isset( $product_query ) && $product_query instanceof WP_Query ) {
	wp_reset_postdata();
}
