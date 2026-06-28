<?php
/**
 * Single post template — used when no flexible content layouts are set.
 *
 * @package lsc-group
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>

	<?php if ( has_post_thumbnail() ) :
		$thumbnail_id = get_post_thumbnail_id();
	?>
		<div class="post-thumbnail">
			<?php if ( function_exists( 'lsc_render_responsive_picture' ) ) :
				lsc_render_responsive_picture(
					[
						'ID'  => $thumbnail_id,
						'url' => wp_get_attachment_url( $thumbnail_id ),
						'alt' => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ?: get_the_title(),
					],
					[
						'sizes'         => '100vw',
						'fetchpriority' => 'high',
						'lazy'          => false,
						'class'         => 'post-thumbnail-image',
					]
				);
			else :
				the_post_thumbnail( 'lsc-1200', [ 'class' => 'post-thumbnail-image' ] );
			endif; ?>
		</div>
	<?php endif; ?>

	<div class="post-inner layout-padding">

		<header class="entry-header pt-50 pt-md-70 pt-lg-100">

			<?php $categories = get_the_category();
			if ( $categories ) : ?>
				<div class="entry-categories mb-20">
					<?php foreach ( $categories as $category ) : ?>
						<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="entry-category">
							<?php echo esc_html( $category->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<?php
			$lsc_word_count = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ) );
			$lsc_read_time  = max( 1, (int) ceil( $lsc_word_count / 220 ) );
			?>
			<div class="entry-meta mt-20">
				<span class="entry-author">
					<?php
					printf(
						/* translators: %s: Author name */
						esc_html__( 'By %s', 'lsc-group' ),
						esc_html( get_the_author() )
					);
					?>
				</span>
				<time class="entry-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
				<span class="entry-read-time">
					<?php
					printf(
						/* translators: %s: estimated reading time in minutes */
						esc_html( _n( '%s min read', '%s min read', $lsc_read_time, 'lsc-group' ) ),
						number_format_i18n( $lsc_read_time )
					);
					?>
				</span>
			</div>

		</header>

		<div class="entry-content mt-50 mt-md-60">
			<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Post title */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'lsc-group' ),
					[ 'span' => [ 'class' => [] ] ]
				),
				wp_kses_post( get_the_title() )
			) );

			wp_link_pages( [
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'lsc-group' ),
				'after'  => '</div>',
			] );
			?>
		</div>

		<?php $tags = get_the_tags(); if ( $tags ) : ?>
			<footer class="entry-footer pb-30">
				<div class="post-tags">
					<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="post-tag">
							<?php echo esc_html( $tag->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</footer>
		<?php endif; ?>

	</div>

</article>
