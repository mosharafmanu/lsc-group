<?php
/**
 * Specialist Cards Section
 *
 * Centered header (accent divider, heading, optional intro) above a grid of
 * cards — each an initial circle, a title, short copy and a call-to-action
 * link (e.g. "Contact Team"). CSS is Faisal's; this template emits BEM hooks
 * only.
 *
 * @package lsc-group
 */

$title_lines = get_sub_field( 'title_lines' );
$description = get_sub_field( 'description' );
$cards       = get_sub_field( 'cards' );
$columns     = get_sub_field( 'columns' ) ?: 'columns-3';

if ( ! $title_lines && ! $description && ! $cards ) {
	return;
}

$grid_classes = [
	'specialist-cards__grid',
	'card-grid',
	'card-grid--center-last-row',
	sanitize_html_class( $columns ),
];

$section_classes = [ 'specialist-cards', 'pb-50', 'pb-lg-90' ];

if ( isset( $GLOBALS['lsc_previous_layout'] ) && 'contact_panel' === $GLOBALS['lsc_previous_layout'] ) {
	$section_classes[] = 'specialist-cards--overlap-contact-panel';
	$section_classes[] = 'pt-100';
	$section_classes[] = 'pt-lg-180';
} else {
	$section_classes[] = 'pt-50';
	$section_classes[] = 'pt-lg-90';
}
?>

<?php $lsc_section_el = ( ! empty( $title_lines ) && is_array( $title_lines ) ) ? 'section' : 'div'; ?>
<<?php echo $lsc_section_el; ?> class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="lsc-container layout-padding">
		<?php if ( $title_lines || $description ) : ?>
			<div class="section-header specialist-cards__header text-center">
				<span class="section-header__divider" aria-hidden="true"></span>

				<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
					<h2 class="section-header__title specialist-cards__title">
						<?php foreach ( $title_lines as $title_line ) : ?>
							<?php
							$line_parts = $title_line['line_parts'] ?? [];

							if ( ! $line_parts || ! is_array( $line_parts ) ) {
								continue;
							}
							?>
							<span class="specialist-cards__title-line">
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text = $line_part['text'] ?? '';

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'specialist-cards__title-part' ];

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
					<div class="section-header__description specialist-cards__description mt-15">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $cards && is_array( $cards ) ) : ?>
			<div class="<?php echo esc_attr( implode( ' ', $grid_classes ) ); ?> mt-40 mt-lg-55">
				<?php foreach ( $cards as $card ) : ?>
					<?php
					$card_title       = $card['title'] ?? '';
					$card_description = $card['description'] ?? '';
					$card_link        = $card['link'] ?? '';
					$card_initial     = $card['initial'] ?? '';

					if ( ! $card_title && ! $card_description ) {
						continue;
					}

					// Initial defaults to the first letter of the title.
					if ( ! $card_initial && $card_title ) {
						$card_initial = mb_substr( $card_title, 0, 1 );
					}
					?>
					<div class="specialist-cards__card">
						<?php if ( $card_initial ) : ?>
							<span class="specialist-cards__avatar" aria-hidden="true">
								<span class="specialist-cards__initial color-lsc-accent"><?php echo esc_html( $card_initial ); ?></span>
							</span>
						<?php endif; ?>

						<?php if ( $card_title ) : ?>
							<h3 class="specialist-cards__card-title h5-style"><?php echo esc_html( $card_title ); ?></h3>
						<?php endif; ?>

						<?php if ( $card_description ) : ?>
							<div class="specialist-cards__card-description"><?php echo wp_kses_post( nl2br( $card_description ) ); ?></div>
						<?php endif; ?>

						<?php if ( $card_link && is_array( $card_link ) && ! empty( $card_link['url'] ) ) : ?>
							<a class="specialist-cards__link" href="<?php echo esc_url( $card_link['url'] ); ?>"<?php echo ! empty( $card_link['target'] ) ? ' target="' . esc_attr( $card_link['target'] ) . '" rel="noopener"' : ''; ?>>
								<?php echo esc_html( $card_link['title'] ? $card_link['title'] : __( 'Contact Team', 'lsc-group' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</<?php echo $lsc_section_el; ?>>
