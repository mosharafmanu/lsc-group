<?php
/**
 * Stats Section
 *
 * @package lsc-group
 */

$stats   = get_sub_field( 'stats' );
$style   = get_sub_field( 'style' ) ?: 'band';
$columns = get_sub_field( 'columns' ) ?: 'columns-3';

if ( ! $stats || ! is_array( $stats ) ) {
	return;
}

$is_cards = 'cards' === $style;

$section_classes = [
	'stats-section',
	'stats-section--' . ( $is_cards ? 'cards' : 'band' ),
];

// Band keeps its subtle background; cards sit on the page background.
if ( ! $is_cards ) {
	$section_classes[] = 'bg-lsc-subtle';
}

$grid_classes = [
	'stats-section__grid',
	'card-grid',
	sanitize_html_class( $columns ),
];

// Cards reuse the global last-row centering so an orphan card stays centered.
if ( $is_cards ) {
	$grid_classes[] = 'card-grid--center-last-row';
}
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="lsc-container layout-padding <?php echo esc_attr( implode( ' ', $grid_classes ) ); ?>">
		<?php foreach ( $stats as $stat ) : ?>
			<?php
			$value = $stat['value'] ?? '';
			$label = $stat['label'] ?? '';

			if ( ! $value && ! $label ) {
				continue;
			}
			?>
			<div class="stats-section__item">
				<?php if ( $value ) : ?>
					<p class="stats-section__value"><?php echo esc_html( $value ); ?></p>
				<?php endif; ?>

				<?php if ( $label ) : ?>
					<p class="stats-section__label"><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
