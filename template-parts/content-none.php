<?php
/**
 * No results template
 *
 * @package lsc-group
 */

?>

<div class="no-results not-found">

	<h2 class="page-title"><?php esc_html_e( 'Nothing Found', 'lsc-group' ); ?></h2>

	<div class="page-content">
		<?php if ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'lsc-group' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we cannot find what you are looking for. Perhaps searching can help.', 'lsc-group' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div>

</div>
