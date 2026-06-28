<?php
/**
 * Finance Product card component.
 *
 * Renders one finance product card — featured image, title, excerpt and a
 * "Learn More" link. Shared by the finance products grid and list sections.
 *
 * @package lsc-group
 */

if ( ! function_exists( 'lsc_render_finance_product_card' ) ) {
	/**
	 * @param int|null $post_id Finance product post ID (defaults to the current post).
	 * @param array    $args    Reserved for future options.
	 */
	function lsc_render_finance_product_card( $post_id = null, $args = [] ) {
		$product_id = $post_id ? absint( $post_id ) : get_the_ID();

		if ( ! $product_id ) {
			return;
		}

		$product_title = get_the_title( $product_id );
		$url           = get_permalink( $product_id );
		$excerpt       = has_excerpt( $product_id ) ? get_the_excerpt( $product_id ) : '';
		?>
		<article class="finance-product-card">
			<?php if ( has_post_thumbnail( $product_id ) ) : ?>
				<a class="finance-product-card__media" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'lsc-group' ), $product_title ) ); ?>">
					<?php echo get_the_post_thumbnail( $product_id, 'large', [ 'class' => 'finance-product-card__image' ] ); ?>
				</a>
			<?php endif; ?>

			<div class="finance-product-card__content">
				<?php if ( $product_title ) : ?>
					<h6 class="finance-product-card__title">
						<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $product_title ); ?></a>
					</h6>
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
		<?php
	}
}
