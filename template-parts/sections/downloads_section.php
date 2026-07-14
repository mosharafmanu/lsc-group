<?php
/**
 * Downloads Section
 *
 * Optional header above a stacked list of download rows (icon, title, subtitle,
 * Download button and optional Web Form button). Pulls from the Download CPT —
 * all published or a hand-picked selection — mirroring the case_studies_grid / testimonials Source pattern.
 * CSS is Faisal's; this template emits BEM hooks only.
 *
 * @package lsc-group
 */

$eyebrow         = get_sub_field( 'eyebrow' );
$title           = get_sub_field( 'title' );
$description     = get_sub_field( 'description' );
$download_source = get_sub_field( 'download_source' ) ?: 'all';
$selected        = get_sub_field( 'selected_downloads' );
$posts_per_page  = get_sub_field( 'posts_per_page' );
$orderby         = get_sub_field( 'orderby' ) ?: 'menu_order';
$order           = get_sub_field( 'order' ) ?: 'ASC';

$posts_per_page = $posts_per_page ? (int) $posts_per_page : -1;

$downloads = [];

if ( 'selected' === $download_source && $selected && is_array( $selected ) ) {
	foreach ( $selected as $selected_download ) {
		$download_id = is_object( $selected_download ) ? $selected_download->ID : (int) $selected_download;

		if ( $download_id ) {
			$download = get_post( $download_id );

			if ( $download && 'download' === $download->post_type && 'publish' === $download->post_status ) {
				$downloads[] = $download;
			}
		}
	}
} else {
	$download_query = new WP_Query(
		[
			'post_type'      => 'download',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'orderby'        => $orderby,
			'order'          => $order,
			'no_found_rows'  => true,
		]
	);

	if ( $download_query->have_posts() ) {
		$downloads = $download_query->posts;
	}
}

if ( ! $eyebrow && ! $title && ! $description && ! $downloads ) {
	return;
}
?>

<?php $lsc_section_el = ( ! empty( $title ) ) ? 'section' : 'div'; ?>
<<?php echo $lsc_section_el; ?> class="downloads-section">
	<div class="downloads-section__inner lsc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
		<?php if ( $eyebrow || $title || $description ) : ?>
			<header class="section-header downloads-section__header">
					<span class="section-header__divider" aria-hidden="true"></span>

				<?php if ( $eyebrow ) : ?>
					<p class="section-header__eyebrow downloads-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="section-header__title downloads-section__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="section-header__description downloads-section__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $downloads ) : ?>
			<ul class="downloads-section__list">
				<?php foreach ( $downloads as $download ) : ?>
					<?php
					$download_id    = $download->ID;
					$download_title = get_the_title( $download_id );
					$subtitle       = (string) get_field( 'subtitle', $download_id );
					$file           = get_field( 'file', $download_id );
					$file_url       = is_array( $file ) ? ( $file['url'] ?? '' ) : '';
					$web_form_url   = (string) get_field( 'web_form_url', $download_id );
					$web_form_label = (string) get_field( 'web_form_button_label', $download_id );
					$web_form_label = $web_form_label ?: __( 'Web Form', 'lsc-group' );

					if ( ! $file_url ) {
						continue;
					}
					?>
					<li class="download-item">
					   <div class="download-item-wrapper">
						 <div class="download-item-left">
							 <span class="download-item__icon" aria-hidden="true">
							    <?php get_template_part( 'assets/svgs/download' ); ?>
						     </span>
							 <div class="download-item__content">
								<?php if ( $download_title ) : ?>
									<h3 class="download-item__title h5-style"><?php echo esc_html( $download_title ); ?></h3>
								<?php endif; ?>

								<?php if ( $subtitle ) : ?>
									<p class="download-item__subtitle"><?php echo esc_html( $subtitle ); ?></p>
								<?php endif; ?>
							 </div>
						</div>
						<div class="download-item-right">
							<a class="download-item__button site-btn btn-primary" href="<?php echo esc_url( $file_url ); ?>" download target="_blank" rel="noopener">
							   <span class="download-item__button-text"><?php esc_html_e( 'Download', 'lsc-group' ); ?></span>
							   <span class="download-item__button-icon" aria-hidden="true"><?php get_template_part( 'assets/svgs/arrow-down' ); ?></span>
						    </a>
							<?php if ( $web_form_url ) : ?>
								<a class="download-item__button download-item__button--web-form site-btn btn-secondary" href="<?php echo esc_url( $web_form_url ); ?>" target="_blank" rel="noopener">
								   <span class="download-item__button-text"><?php echo esc_html( $web_form_label ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					   </div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</<?php echo $lsc_section_el; ?>>

<?php
if ( isset( $download_query ) && $download_query instanceof WP_Query ) {
	wp_reset_postdata();
}
