<?php
/**
 * Single Case Study template.
 *
 * Owns the page skeleton for a case study and runs the `cms` flexible content
 * itself (instead of the plain dispatcher) so it can split the sections:
 *
 *  - The page hero (`inner_hero`) breaks OUT of the columns and renders
 *    full-width at the top, exactly like a regular page hero.
 *  - Every other cms section flows into the LEFT column of a two-column band,
 *    beside a sticky "Case Summary" sidebar (right column) that reads the
 *    Case Study Details post meta + the global Apply Now link.
 *
 * Related Case Studies and the CTA are handled separately — not through this
 * loop. The two-column grid, sticky sidebar and all visual styling (including
 * resetting the nested `.lsc-container` inside the left column) are Faisal's
 * CSS; this template emits BEM hooks only.
 *
 * @package lsc-group
 */

get_header();

// Layouts that break out of the two-column band and render full-width.
$lsc_fullwidth_layouts = [ 'inner_hero' ];

// --- Case Summary sidebar (built once, dropped into the right column) -------
$lsc_summary_rows = array_filter(
	[
		'Client Type'         => get_field( 'client_type' ),
		'Sector'              => get_field( 'sector' ),
		'Funding Requirement' => get_field( 'funding_requirement' ),
		'Outcome'             => get_field( 'outcome' ),
	]
);

$lsc_apply_link  = function_exists( 'lsc_get_apply_now_link' ) ? lsc_get_apply_now_link() : false;
$lsc_has_apply   = $lsc_apply_link && is_array( $lsc_apply_link ) && function_exists( 'lsc_render_button' );
$lsc_has_sidebar = ! empty( $lsc_summary_rows ) || $lsc_has_apply;

ob_start();
if ( $lsc_has_sidebar ) :
	?>
	<aside class="case-study-layout__sidebar">
		<div class="case-summary">
			<h2 class="case-summary__title">Case Summary</h2>

			<?php if ( $lsc_summary_rows ) : ?>
				<dl class="case-summary__list">
					<?php foreach ( $lsc_summary_rows as $lsc_label => $lsc_value ) : ?>
						<div class="case-summary__row">
							<dt class="case-summary__label"><?php echo esc_html( $lsc_label ); ?></dt>
							<dd class="case-summary__value"><?php echo esc_html( $lsc_value ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php if ( $lsc_has_apply ) : ?>
				<div class="case-summary__apply">
					<?php
					lsc_render_button(
						$lsc_apply_link,
						[
							'style'     => 'btn-primary',
							'class'     => 'case-summary__apply-btn',
							'show_icon' => false,
						]
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</aside>
	<?php
endif;
$lsc_sidebar_html = ob_get_clean();
?>

<main id="primary" class="site-main single-case-study">

	<?php
	while ( have_posts() ) :
		the_post();

		if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :

			$lsc_previous_layout = '';
			$lsc_section_index   = 0;
			$lsc_column_open     = false;

			while ( have_rows( 'cms' ) ) :
				the_row();
				$lsc_layout = get_row_layout();

				if ( empty( $lsc_layout ) ) {
					continue;
				}

				$lsc_is_fullwidth = in_array( $lsc_layout, $lsc_fullwidth_layouts, true );

				// A full-width section closes the two-column band if it is open.
				if ( $lsc_is_fullwidth && $lsc_column_open ) {
					echo '</div>'; // .case-study-layout__main
					echo $lsc_sidebar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '</div></div></section>';
					$lsc_column_open = false;
				}

				// Open the two-column band before the first left-column section.
				if ( ! $lsc_is_fullwidth && ! $lsc_column_open ) {
					echo '<section class="case-study-layout"><div class="lsc-container layout-padding"><div class="case-study-layout__grid"><div class="case-study-layout__main">';
					$lsc_column_open = true;
				}

				// Render the section using the dispatcher's conventions.
				$GLOBALS['lsc_previous_layout'] = $lsc_previous_layout;
				$GLOBALS['lsc_section_index']   = $lsc_section_index;

				$lsc_template_path = 'template-parts/sections/' . $lsc_layout;

				if ( locate_template( $lsc_template_path . '.php' ) ) {
					get_template_part( $lsc_template_path );
				} elseif ( current_user_can( 'manage_options' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					echo '<!-- Missing template: ' . esc_html( $lsc_template_path ) . '.php -->';
				}

				$lsc_previous_layout = $lsc_layout;
				$lsc_section_index++;
			endwhile;

			// Close the band if it is still open, dropping in the sidebar.
			if ( $lsc_column_open ) {
				echo '</div>'; // .case-study-layout__main
				echo $lsc_sidebar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</div></div></section>';
			}

			$GLOBALS['lsc_last_layout'] = $lsc_previous_layout;
			unset( $GLOBALS['lsc_previous_layout'], $GLOBALS['lsc_section_index'] );

		else :
			get_template_part( 'template-parts/content', 'page' );
		endif;

		// --- Related Case Studies (full-width, auto-populated, excludes this post) ---
		$lsc_related = new WP_Query(
			[
				'post_type'           => 'case_study',
				'post_status'         => 'publish',
				'posts_per_page'      => 3,
				'post__not_in'        => [ get_the_ID() ],
				'orderby'             => 'menu_order date',
				'order'               => 'ASC',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			]
		);

		if ( $lsc_related->have_posts() ) :
			?>
			<section class="case-studies-section case-studies-section--related">
				<div class="case-studies-section__inner lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
					<header class="section-header case-studies-section__header">
						<span class="section-header__divider" aria-hidden="true"></span>
						<h2 class="section-header__title case-studies-section__title"><?php esc_html_e( 'Related Case Studies', 'lsc-group' ); ?></h2>
					</header>

					<div class="case-studies-grid card-grid card-grid--center-last-row columns-3 mt-30 mt-lg-50">
						<?php foreach ( $lsc_related->posts as $lsc_related_study ) : ?>
							<?php lsc_render_case_study_card( $lsc_related_study->ID ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
		endif;

		wp_reset_postdata();

		// --- CTA (full-width, from the Global CTA in Site Settings) ---
		if ( function_exists( 'lsc_get_global_cta' ) ) {
			get_template_part( 'template-parts/cta-band', null, lsc_get_global_cta() );
		}

	endwhile;
	?>

</main>

<?php
get_footer();
