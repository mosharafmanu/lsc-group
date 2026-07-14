<?php
/**
 * @package lsc-group
 */

/**
 * Register the public query vars used by paginated custom listings embedded in a
 * static Page (Case Studies, Finance Products, …). Tracking the page via a
 * dedicated `?{var}=N` sidesteps the canonical-redirect issues that come with
 * `/page/N/` on singular pages, and a per-listing var lets two paginated
 * sections coexist on one page without fighting over the same page number.
 */
add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'cs_page';
		$vars[] = 'fp_page';
		return $vars;
	}
);

/**
 * Render pagination markup.
 *
 * @param WP_Query|null $query    Optional custom query to paginate. When supplied,
 *                                paging is driven by the `$page_var` query var and
 *                                links are emitted as `?{$page_var}=N`. When omitted,
 *                                the main query and standard `/page/N/` links are used.
 * @param string        $page_var Query var that holds the current page number.
 */
function lsc_render_pagination( $query = null, $page_var = 'cs_page' ) {
	ob_start();
	get_template_part( 'assets/svgs/angle-left-pagination' );
	$prev_arrow = ob_get_clean();

	ob_start();
	get_template_part( 'assets/svgs/angle-right-pagination' );
	$next_arrow = ob_get_clean();

	$args = [
		'mid_size'  => 1, // Reduced from 2 to 1 for better mobile display
		'end_size'  => 1,
		'prev_text' => '<span class="pagination-arrow">' . $prev_arrow . '</span>',
		'next_text' => '<span class="pagination-arrow">' . $next_arrow . '</span>',
		'type'      => 'list',
	];

	if ( $query instanceof WP_Query ) {
		$args['current'] = max( 1, (int) get_query_var( $page_var ) );
		$args['total']   = (int) $query->max_num_pages;
		$args['base']    = add_query_arg( $page_var, '%#%' );
		$args['format']  = '';
	}

	$pagination = paginate_links( $args );

	if ( ! $pagination ) {
		return;
	}

	$allowed_tags = [
		'nav'  => [
			'class'      => [],
			'aria-label' => [],
		],
		'ul'   => [ 'class' => [] ],
		'li'   => [ 'class' => [] ],
		'a'    => [
			'class' => [],
			'href'  => [],
		],
		'span' => [
			'class'        => [],
			'aria-current' => [],
		],
		'svg'  => [
			'width'   => [],
			'height'  => [],
			'viewbox' => [],
			'fill'    => [],
			'xmlns'   => [],
		],
		'path' => [
			'd'            => [],
			'stroke'       => [],
			'stroke-width' => [],
			'fill'         => [],
		],
	];
	?>
	<nav class="blog-pagination pagination" aria-label="<?php esc_attr_e( 'Blog pagination', 'lsc-group' ); ?>">
		<?php echo wp_kses( $pagination, $allowed_tags ); ?>
	</nav>
	<?php
}
