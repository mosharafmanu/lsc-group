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
		<?php // No aria-label: the link's accessible name comes from its content (title + excerpt), so it matches the visible text (WCAG 2.5.3). ?>
		<a class="finance-product-card" href="<?php echo esc_url( $url ); ?>">
			<?php if ( has_post_thumbnail( $product_id ) ) : ?>
				<div class="finance-product-card__media">
					<?php echo get_the_post_thumbnail( $product_id, 'lsc-900', [ 'class' => 'finance-product-card__image', 'loading' => 'lazy', 'sizes' => '(max-width: 767px) 100vw, (max-width: 1199px) 50vw, 33vw' ] ); ?>
				</div>
			<?php endif; ?>

			<div class="finance-product-card__content">
				<?php if ( $product_title ) : ?>
					<h3 class="finance-product-card__title h6-style"><?php echo esc_html( $product_title ); ?></h3>
				<?php endif; ?>

				<?php if ( $excerpt ) : ?>
					<p class="finance-product-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<span class="finance-product-card__link" aria-hidden="true">
					<span><?php esc_html_e( 'Learn More', 'lsc-group' ); ?></span>
					<span class="finance-product-card__link-icon">-&gt;</span>
				</span>
			</div>
		</a>
		<?php
	}
}
