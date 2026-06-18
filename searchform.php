<?php
/**
 * Custom Search Form
 *
 * @package lsc-group
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-field" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'lsc-group' ); ?></label>
	<input 
		type="search" 
		id="search-field" 
		class="search-field" 
		placeholder="<?php esc_attr_e( 'Search...', 'lsc-group' ); ?>" 
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s" 
		required
	/>
	<button type="submit" class="search-submit">
		<?php esc_html_e( 'Search', 'lsc-group' ); ?>
	</button>
</form>

