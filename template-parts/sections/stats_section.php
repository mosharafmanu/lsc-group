<?php
/**
 * Stats Section
 *
 * @package lsc-group
 */

$stats   = get_sub_field( 'stats' );

if ( ! $stats || ! is_array( $stats ) ) {
	return;
}

$section_classes = [
	'stats-section',
	'bg-lsc-light',
];

$grid_classes = [
	'stats-section__grid',
	'card-grid',
	'columns-3',
];
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
