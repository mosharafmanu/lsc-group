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

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :
					lsc_flexible_content( 'cms' );
				else :
					get_template_part( 'template-parts/content', get_post_type() );
				endif;

				if ( function_exists( 'lsc_render_back_to_blogs_button' ) ) {
					lsc_render_back_to_blogs_button();
				}

				// Related Articles (blog posts only): same-category, most recent, excluding the current post.
				if ( is_singular( 'post' ) && function_exists( 'lsc_render_post_card' ) ) :

					$lsc_related_args = [
						'post_type'           => 'post',
						'post_status'         => 'publish',
						'posts_per_page'      => 3,
						'post__not_in'        => [ get_the_ID() ],
						'orderby'             => 'date',
						'order'               => 'DESC',
						'no_found_rows'       => true,
						'ignore_sticky_posts' => true,
					];

					$lsc_post_cats = wp_get_post_categories( get_the_ID() );
					if ( $lsc_post_cats ) {
						$lsc_related_args['category__in'] = $lsc_post_cats;
					}

					$lsc_related = new WP_Query( $lsc_related_args );

					if ( $lsc_related->have_posts() ) :
						?>
						<section class="related-posts lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
							<header class="related-posts__header">
								<h2 class="related-posts__title"><?php esc_html_e( 'Related Articles', 'lsc-group' ); ?></h2>
							</header>

							<div class="blog-grid card-grid columns-3 mt-30 mt-lg-50">
								<?php foreach ( $lsc_related->posts as $lsc_related_post ) : ?>
									<?php lsc_render_post_card( $lsc_related_post->ID, [ 'variant' => 'default' ] ); ?>
								<?php endforeach; ?>
							</div>
						</section>
						<?php
					endif;

					wp_reset_postdata();

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
