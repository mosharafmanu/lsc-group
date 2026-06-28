<?php
/**
 * Timeline Section
 *
 * Centered header above a row of numbered timeline steps (year, title, copy).
 * Step numbers auto-increment from the loop.
 *
 * @package lsc-group
 */

$eyebrow     = get_sub_field( 'eyebrow' );
$title_lines = get_sub_field( 'title_lines' );
$description = get_sub_field( 'description' );
$items       = get_sub_field( 'items' );

if ( ! $eyebrow && ! $title_lines && ! $description && ! $items ) {
	return;
}
?>

<section class="timeline-section pt-60 pb-50 pt-lg-100 pb-lg-110">
	<div class="timeline-section__inner lsc-container layout-padding">
		<?php if ( $eyebrow || $title_lines || $description ) : ?>
			<header class="section-header timeline-section__header text-center">
					<span class="section-header__divider" aria-hidden="true"></span>

				<?php if ( $eyebrow ) : ?>
					<p class="section-header__eyebrow timeline-section__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
					<h2 class="section-header__title timeline-section__title">
						<?php foreach ( $title_lines as $title_line ) : ?>
							<?php
							$line_parts = $title_line['line_parts'] ?? [];

							if ( ! $line_parts || ! is_array( $line_parts ) ) {
								continue;
							}
							?>
							<span class="timeline-section__title-line">
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text = $line_part['text'] ?? '';

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'timeline-section__title-part' ];

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
					<div class="section-header__description timeline-section__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $items && is_array( $items ) ) : ?>
			<ol class="timeline-section__list">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$year       = $item['year'] ?? '';
					$item_title = $item['title'] ?? '';
					$item_text  = $item['description'] ?? '';

					if ( ! $year && ! $item_title && ! $item_text ) {
						continue;
					}
					?>
					<li class="timeline-section__item">
						<div class="timeline-section__marker">
							<span class="timeline-section__number"><?php echo esc_html( $index + 1 ); ?></span>
							<span class="timeline-border"></span>
						</div>

						<div class="timeline-section__content">
							<?php if ( $year ) : ?>
								<span class="timeline-section__year"><?php echo esc_html( $year ); ?></span>
							<?php endif; ?>

							<?php if ( $item_title ) : ?>
								<h5 class="timeline-section__item-title"><?php echo esc_html( $item_title ); ?></h5>
							<?php endif; ?>

							<?php if ( $item_text ) : ?>
								<p class="timeline-section__item-text"><?php echo esc_html( $item_text ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
