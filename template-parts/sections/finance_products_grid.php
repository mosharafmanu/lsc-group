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

$grid_classes = [
	'finance-products-grid',
	'card-grid',
	sanitize_html_class( $columns ),
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

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="finance-products-section__inner lsc-container layout-padding">
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

		<?php if ( $products ) : ?>
			<div class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?>">
				<?php foreach ( $products as $product ) : ?>
					<?php
					$product_id = $product->ID;
					$title      = get_the_title( $product_id );
					$url        = get_permalink( $product_id );
					$excerpt    = has_excerpt( $product_id ) ? get_the_excerpt( $product_id ) : '';
					?>
					<article class="finance-product-card">
						<?php if ( has_post_thumbnail( $product_id ) ) : ?>
							<a class="finance-product-card__media" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'lsc-group' ), $title ) ); ?>">
								<?php echo get_the_post_thumbnail( $product_id, 'large', [ 'class' => 'finance-product-card__image' ] ); ?>
							</a>
						<?php endif; ?>

						<div class="finance-product-card__content">
							<?php if ( $title ) : ?>
								<h3 class="finance-product-card__title">
									<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
								</h3>
							<?php endif; ?>

							<?php if ( $excerpt ) : ?>
								<p class="finance-product-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
							<?php endif; ?>

							<a class="finance-product-card__link" href="<?php echo esc_url( $url ); ?>">
								<span><?php esc_html_e( 'Learn More', 'lsc-group' ); ?></span>
								<span aria-hidden="true">-&gt;</span>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
if ( isset( $product_query ) && $product_query instanceof WP_Query ) {
	wp_reset_postdata();
}
