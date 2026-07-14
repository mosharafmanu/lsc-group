<?php
/**
 * @package lsc-group
 */

add_filter( 'walker_nav_menu_start_el', function( $item_output, $item, $depth, $args ) {
	if ( ! in_array( 'menu-item-has-children', $item->classes ) ) {
		return $item_output;
	}
	if ( ! isset( $args->theme_location ) || 'mainMenu' !== $args->theme_location ) {
		return $item_output;
	}

	ob_start();
	get_template_part( 'assets/svgs/submenu-indicator' );
	$indicator = ob_get_clean();

	if ( ! $indicator ) {
		return $item_output;
	}

	$chevron = '<span class="submenu-indicator" aria-hidden="true">' . $indicator . '</span>';

	return str_replace( '</a>', $chevron . '</a>', $item_output );
}, 10, 4 );
