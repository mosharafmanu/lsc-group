<?php
/**
 * Process Steps Section
 *
 * Centered header (accent divider, heading, optional intro) above a row of
 * auto-numbered steps (number badge from the loop index, title, copy).
 *
 * @package lsc-group
 */

$title_lines = get_sub_field( 'title_lines' );
$description = get_sub_field( 'description' );
$steps       = get_sub_field( 'steps' );
$columns     = get_sub_field( 'columns' ) ?: 'columns-4';

if ( ! $title_lines && ! $description && ! $steps ) {
	return;
}

$grid_classes = [
	'process-steps__grid',
	'card-grid',
	'card-grid--center-last-row',
	sanitize_html_class( $columns ),
];
?>

<?php $lsc_section_el = ( ! empty( $title_lines ) && is_array( $title_lines ) ) ? 'section' : 'div'; ?>
<<?php echo $lsc_section_el; ?> class="process-steps pt-50 pb-50 pt-lg-90 pb-lg-90">
	<div class="lsc-container layout-padding">
		<?php if ( $title_lines || $description ) : ?>
			<div class="section-header process-steps__header text-center">
				<span class="section-header__divider" aria-hidden="true"></span>

				<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
					<h2 class="section-header__title process-steps__title">
						<?php foreach ( $title_lines as $title_line ) : ?>
							<?php
							$line_parts = $title_line['line_parts'] ?? [];

							if ( ! $line_parts || ! is_array( $line_parts ) ) {
								continue;
							}
							?>
							<span class="process-steps__title-line">
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text = $line_part['text'] ?? '';

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'process-steps__title-part' ];

									if ( ! empty( $line_part['highlight'] ) ) {
										$part_classes[] = 'color-lsc-accent';
									}
									?>
									<span class="<?php echo esc_attr( implode( ' ', $part_classes ) ); ?>"><?php echo esc_html( $part_text ); ?></span>
								<?php endforeach; ?>
							</span>
						<?php endforeach; ?>
					</h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="section-header__description process-steps__description mt-15">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $steps && is_array( $steps ) ) : ?>
			<ol class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?> mt-40 mt-lg-55">
				<?php foreach ( $steps as $index => $step ) : ?>
					<?php
					$step_title       = $step['title'] ?? '';
					$step_description = $step['description'] ?? '';

					if ( ! $step_title && ! $step_description ) {
						continue;
					}
					?>
					<li class="process-steps__step">
						<span class="process-steps__number" aria-hidden="true"><?php echo esc_html( $index + 1 ); ?></span>

						<?php if ( $step_title ) : ?>
							<h3 class="process-steps__step-title h5-style"><?php echo esc_html( $step_title ); ?></h3>
						<?php endif; ?>

						<?php if ( $step_description ) : ?>
							<div class="process-steps__step-description"><?php echo wp_kses_post( nl2br( $step_description ) ); ?></div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</<?php echo $lsc_section_el; ?>>
