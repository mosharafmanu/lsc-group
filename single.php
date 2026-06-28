<?php
/**
 * @package lsc-group
 */

get_header();

$post_slug = '';
if ( is_singular() ) {
	global $post;
	if ( $post ) {
		$post_type = get_post_type();
		$post_slug = $post_type . '-' . $post->post_name;
	}
}
?>

	<main id="primary" class="site-main <?php echo esc_attr( $post_slug ); ?>">

		<?php if ( is_singular( 'post' ) ) : ?>
			<div class="reading-progress" aria-hidden="true"><span class="reading-progress__bar"></span></div>
		<?php endif; ?>

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :
					lsc_flexible_content( 'cms' );
				else :
					get_template_part( 'template-parts/content', get_post_type() );
				endif;

				// Previous / next article navigation (blog posts only).
				if ( is_singular( 'post' ) ) :
					$lsc_prev = get_previous_post();
					$lsc_next = get_next_post();

					if ( $lsc_prev || $lsc_next ) :
						?>
						<nav class="post-nav post-inner layout-padding mt-40 mt-lg-60" aria-label="<?php esc_attr_e( 'Article navigation', 'lsc-group' ); ?>">
							<?php if ( $lsc_prev ) : ?>
								<a class="post-nav__link post-nav__link--prev" href="<?php echo esc_url( get_permalink( $lsc_prev ) ); ?>" rel="prev">
									<span class="post-nav__label"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Previous', 'lsc-group' ); ?></span>
									<span class="post-nav__title"><?php echo esc_html( get_the_title( $lsc_prev ) ); ?></span>
								</a>
							<?php else : ?>
								<span class="post-nav__spacer"></span>
							<?php endif; ?>

							<?php if ( $lsc_next ) : ?>
								<a class="post-nav__link post-nav__link--next" href="<?php echo esc_url( get_permalink( $lsc_next ) ); ?>" rel="next">
									<span class="post-nav__label"><?php esc_html_e( 'Next', 'lsc-group' ); ?> <span aria-hidden="true">&rarr;</span></span>
									<span class="post-nav__title"><?php echo esc_html( get_the_title( $lsc_next ) ); ?></span>
								</a>
							<?php endif; ?>
						</nav>
						<?php
					endif;
				endif;

				if ( function_exists( 'lsc_render_back_to_blogs_button' ) ) {
					lsc_render_back_to_blogs_button();
				}

				// Related Articles (blog posts only): same-category first, then most-recent
				// to backfill up to 3 so the row always reads as a deliberate set.
				if ( is_singular( 'post' ) && function_exists( 'lsc_render_post_card' ) ) :

					$lsc_related_posts = [];
					$lsc_exclude       = [ get_the_ID() ];
					$lsc_post_cats     = wp_get_post_categories( get_the_ID() );

					if ( $lsc_post_cats ) {
						$lsc_cat_query = new WP_Query( [
							'post_type'           => 'post',
							'post_status'         => 'publish',
							'posts_per_page'      => 3,
							'post__not_in'        => $lsc_exclude,
							'category__in'        => $lsc_post_cats,
							'orderby'             => 'date',
							'order'               => 'DESC',
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
						] );
						$lsc_related_posts = $lsc_cat_query->posts;
					}

					if ( count( $lsc_related_posts ) < 3 ) {
						$lsc_fill_query = new WP_Query( [
							'post_type'           => 'post',
							'post_status'         => 'publish',
							'posts_per_page'      => 3 - count( $lsc_related_posts ),
							'post__not_in'        => array_merge( $lsc_exclude, wp_list_pluck( $lsc_related_posts, 'ID' ) ),
							'orderby'             => 'date',
							'order'               => 'DESC',
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
						] );
						$lsc_related_posts = array_merge( $lsc_related_posts, $lsc_fill_query->posts );
					}

					if ( $lsc_related_posts ) :
						$lsc_related_count = count( $lsc_related_posts );
						// 1 item → full-width featured card; 2 → halves; 3 → thirds.
						$lsc_related_cols    = 2 === $lsc_related_count ? 'columns-2' : 'columns-3';
						$lsc_related_variant = 1 === $lsc_related_count ? 'featured' : 'default';
						?>
						<section class="related-posts lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
							<header class="related-posts__header">
								<h2 class="related-posts__title"><?php esc_html_e( 'Related Articles', 'lsc-group' ); ?></h2>
							</header>

							<div class="blog-grid card-grid <?php echo esc_attr( $lsc_related_cols ); ?> mt-30 mt-lg-50">
								<?php foreach ( $lsc_related_posts as $lsc_related_post ) : ?>
									<?php lsc_render_post_card( $lsc_related_post->ID, [ 'variant' => $lsc_related_variant ] ); ?>
								<?php endforeach; ?>
							</div>
						</section>
						<?php
					endif;

				endif;

				if ( comments_open() || get_comments_number() ) {
					echo '<div class="comments-wrap post-inner layout-padding mt-30 mt-md-40 mt-lg-50 pb-50 pb-md-70 pb-lg-100">';
					comments_template();
					echo '</div>';
				}

				// Close the post with the global CTA band, like the rest of the theme.
				if ( is_singular( 'post' ) && function_exists( 'lsc_get_global_cta' ) ) {
					get_template_part( 'template-parts/cta-band', null, lsc_get_global_cta() );
				}

			endwhile;
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>

	</main>

<?php
get_footer();
