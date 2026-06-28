<?php
/**
 * Feature Cards (Icon Grid) Section
 *
 * Centered header (accent divider, heading, intro copy) above a grid of
 * icon + title + copy cards.
 *
 * @package lsc-group
 */

$title_lines = get_sub_field( 'title_lines' );
$description = get_sub_field( 'description' );
$cards       = get_sub_field( 'cards' );
$columns     = get_sub_field( 'columns' ) ?: 'columns-4';

if ( ! $title_lines && ! $description && ! $cards ) {
	return;
}

$grid_classes = [
	'feature-cards__grid',
	'card-grid',
	'card-grid--center-last-row',
	sanitize_html_class( $columns ),
];
?>

<section class="feature-cards pt-50 pb-50 pt-lg-90 pb-lg-100">
	<div class="lsc-container layout-padding">
		<?php if ( $title_lines || $description ) : ?>
			<div class="feature-cards__header text-center">
				<span class="feature-cards__divider" aria-hidden="true"></span>

				<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
					<h2 class="feature-cards__title">
						<?php foreach ( $title_lines as $title_line ) : ?>
							<?php
							$line_parts = $title_line['line_parts'] ?? [];

							if ( ! $line_parts || ! is_array( $line_parts ) ) {
								continue;
							}
							?>
							<span class="feature-cards__title-line">
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text = $line_part['text'] ?? '';

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'feature-cards__title-part' ];

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
					<div class="feature-cards__description mt-15">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $cards && is_array( $cards ) ) : ?>
			<div class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?> mt-40 mt-lg-55">
				<?php foreach ( $cards as $card ) : ?>
					<?php
					$card_icon        = $card['icon'] ?? '';
					$card_title       = $card['title'] ?? '';
					$card_description = $card['description'] ?? '';

					if ( ! $card_title && ! $card_description ) {
						continue;
					}
					?>
					<div class="feature-cards__card">
						<span class="feature-cards__icon color-lsc-accent" aria-hidden="true">
							<?php
							if ( $card_icon && function_exists( 'lsc_render_icon' ) ) {
								lsc_render_icon( $card_icon, [ 'class' => 'feature-cards__icon-svg' ] );
							}
							?>
						</span>

						<?php if ( $card_title ) : ?>
							<h5 class="feature-cards__card-title"><?php echo esc_html( $card_title ); ?></h5>
						<?php endif; ?>

						<?php if ( $card_description ) : ?>
							<div class="feature-cards__card-description"><?php echo wp_kses_post( nl2br( $card_description ) ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
