<?php
/**
 * Rich Text Section
 *
 * A single free-form WYSIWYG block. Reuses the global `.entry-content`
 * typography so headings, lists, links and quotes render consistently with
 * post content. Section spacing/visuals are Faisal's CSS; this template emits
 * BEM hooks only.
 *
 * @package lsc-group
 */

$content = get_sub_field( 'content' );

if ( ! $content ) {
	return;
}
?>

<div class="rich-text">
	<div class="lsc-container layout-padding">
		<div class="rich-text__content entry-content">
			<?php echo wp_kses_post( $content ); ?>
		</div>
	</div>
</div>
