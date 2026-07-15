<?php
/**
 * Stats Section
 *
 * @package lsc-group
 */

$stats      = get_sub_field( 'stats' );
$style      = get_sub_field( 'style' ) ?: 'band';
$columns    = get_sub_field( 'columns' ) ?: 'columns-3';
$background  = get_sub_field( 'background_color' ) ?: 'light';

if ( ! $stats || ! is_array( $stats ) ) {
	return;
}

if ( ! function_exists( 'lsc_parse_stat_counter_value' ) ) {
	function lsc_parse_stat_counter_value( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value || ! preg_match( '/-?\d+(?:,\d{3})*(?:\.\d+)?|-?\d+(?:\.\d+)?/', $value, $matches, PREG_OFFSET_CAPTURE ) ) {
			return false;
		}

		$number_text = $matches[0][0];
		$number_pos  = $matches[0][1];
		$prefix      = substr( $value, 0, $number_pos );
		$suffix      = substr( $value, $number_pos + strlen( $number_text ) );
		$target      = (float) str_replace( ',', '', $number_text );
		$decimals    = false === strpos( $number_text, '.' ) ? 0 : strlen( substr( strrchr( $number_text, '.' ), 1 ) );

		return [
			'prefix'   => $prefix,
			'target'   => $target,
			'suffix'   => $suffix,
			'decimals' => $decimals,
		];
	}
}

$is_cards = 'cards' === $style;

// Background: White (default) or Cream. Field values: light = White, subtle = Cream.
$bg_modifier = 'subtle' === $background ? 'cream' : 'white';

$section_classes = [
	'stats-section',
	'stats-section--' . ( $is_cards ? 'cards' : 'band' ),
	'stats-section--bg-' . $bg_modifier,
];

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

<div class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="lsc-container layout-padding <?php echo esc_attr( implode( ' ', $grid_classes ) ); ?>">
		<?php foreach ( $stats as $stat ) : ?>
			<?php
			$value = $stat['value'] ?? '';
			$label = $stat['label'] ?? '';
			$counter_data = lsc_parse_stat_counter_value( $value );

			if ( ! $value && ! $label ) {
				continue;
			}
			?>
			<div class="stats-section__item">
				<?php if ( $value ) : ?>
					<p
						class="stats-section__value<?php echo $counter_data ? ' js-stat-counter' : ''; ?>"
						<?php if ( $counter_data ) : ?>
							data-counter-prefix="<?php echo esc_attr( $counter_data['prefix'] ); ?>"
							data-counter-target="<?php echo esc_attr( $counter_data['target'] ); ?>"
							data-counter-suffix="<?php echo esc_attr( $counter_data['suffix'] ); ?>"
							data-counter-decimals="<?php echo esc_attr( $counter_data['decimals'] ); ?>"
						<?php endif; ?>
					><?php echo esc_html( $value ); ?></p>
				<?php endif; ?>

				<?php if ( $label ) : ?>
					<p class="stats-section__label"><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
