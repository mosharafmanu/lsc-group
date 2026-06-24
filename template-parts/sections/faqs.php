<?php
/**
 * FAQs Section
 *
 * Centered header above a jQuery slide-toggle accordion (plus/minus icons).
 *
 * @package lsc-group
 */

$title_lines = get_sub_field( 'title_lines' );
$description = get_sub_field( 'description' );
$faqs        = get_sub_field( 'faqs' );

if ( ! $title_lines && ! $description && ! $faqs ) {
	return;
}

// Unique base so multiple FAQ sections on one page keep distinct aria ids.
static $faq_section_index = 0;
++$faq_section_index;
$base_id = 'faq-' . $faq_section_index;
?>

<section class="faqs pt-50 pb-50 pt-lg-90 pb-lg-90">
	<div class="lsc-container layout-padding">
		<?php if ( $title_lines || $description ) : ?>
			<div class="faqs__header text-center">
				<?php if ( $title_lines && is_array( $title_lines ) ) : ?>
					<h2 class="faqs__title">
						<?php foreach ( $title_lines as $title_line ) : ?>
							<?php
							$line_parts = $title_line['line_parts'] ?? [];

							if ( ! $line_parts || ! is_array( $line_parts ) ) {
								continue;
							}
							?>
							<span class="faqs__title-line">
								<?php foreach ( $line_parts as $line_part ) : ?>
									<?php
									$part_text = $line_part['text'] ?? '';

									if ( ! $part_text ) {
										continue;
									}

									$part_classes = [ 'faqs__title-part' ];

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
					<div class="faqs__description mt-15">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $faqs && is_array( $faqs ) ) : ?>
			<div class="faqs__list mt-40 mt-lg-55" data-faq-accordion>
				<?php foreach ( $faqs as $index => $faq ) : ?>
					<?php
					$question = $faq['question'] ?? '';
					$answer   = $faq['answer'] ?? '';

					if ( ! $question || ! $answer ) {
						continue;
					}

					$item_id     = $base_id . '-' . $index;
					$button_id   = $item_id . '-button';
					$panel_id    = $item_id . '-panel';
					?>
					<div class="faqs__item">
						<h3 class="faqs__question-heading">
							<button type="button" class="faqs__question" id="<?php echo esc_attr( $button_id ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
								<span class="faqs__question-text"><?php echo esc_html( $question ); ?></span>
								<span class="faqs__icon" aria-hidden="true">
									<span class="faqs__icon-plus"><?php get_template_part( 'assets/svgs/plus' ); ?></span>
									<span class="faqs__icon-minus"><?php get_template_part( 'assets/svgs/minus' ); ?></span>
								</span>
							</button>
						</h3>
						<div class="faqs__answer" id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $button_id ); ?>" hidden>
							<div class="faqs__answer-inner">
								<?php echo wp_kses_post( wpautop( $answer ) ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
