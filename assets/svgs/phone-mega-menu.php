<?php
/**
 * Phone icon for the mega menu CTA. Pass an optional class via get_template_part args:
 * get_template_part( 'assets/svgs/phone-mega-menu', null, [ 'class' => 'mega-menu__cta-icon' ] );
 *
 * @package lsc-group
 */

$lsc_svg_class  = isset( $args['class'] ) ? $args['class'] : '';
$lsc_class_attr = $lsc_svg_class ? ' class="' . esc_attr( $lsc_svg_class ) . '"' : '';
?>
<svg<?php echo $lsc_class_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M6.40625 1.09375L7.96875 4.84375C8.24219 5.46875 8.08594 6.21094 7.53906 6.64062L5.625 8.24219C6.91406 10.9766 9.14062 13.2031 11.875 14.4922L13.4766 12.5781C13.9062 12.0312 14.6484 11.875 15.2734 12.1484L19.0234 13.7109C19.7656 13.9844 20.1172 14.8047 19.9219 15.5469L18.9844 18.9844C18.7891 19.6484 18.2031 20.1172 17.5 20.1172C7.8125 20.1172 0 12.3047 0 2.61719C0 1.91406 0.46875 1.32812 1.13281 1.13281L4.57031 0.195312C5.3125 0 6.13281 0.351562 6.40625 1.09375Z" fill="#FF8A3B"/>
</svg>
