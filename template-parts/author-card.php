<?php
/**
 * Author card — avatar, name and bio, shown at the end of a single post.
 * Renders nothing when the author has no bio set.
 *
 * @package lsc-group
 */

$lsc_author_id  = (int) get_post_field( 'post_author', get_the_ID() );
$lsc_author_bio = $lsc_author_id ? get_the_author_meta( 'description', $lsc_author_id ) : '';

if ( ! $lsc_author_id || ! $lsc_author_bio ) {
	return;
}

$lsc_author_name = get_the_author_meta( 'display_name', $lsc_author_id );
$lsc_author_url  = get_author_posts_url( $lsc_author_id );
?>
<aside class="author-card">
	<div class="author-card__avatar">
		<?php echo get_avatar( $lsc_author_id, 96, '', $lsc_author_name, [ 'class' => 'author-card__avatar-img' ] ); ?>
	</div>

	<div class="author-card__body">
		<span class="author-card__eyebrow"><?php esc_html_e( 'Written by', 'lsc-group' ); ?></span>
		<h3 class="author-card__name"><?php echo esc_html( $lsc_author_name ); ?></h3>
		<p class="author-card__bio"><?php echo esc_html( $lsc_author_bio ); ?></p>
		<a class="author-card__link" href="<?php echo esc_url( $lsc_author_url ); ?>">
			<span><?php printf( esc_html__( 'More from %s', 'lsc-group' ), esc_html( $lsc_author_name ) ); ?></span>
			<span aria-hidden="true">-&gt;</span>
		</a>
	</div>
</aside>
