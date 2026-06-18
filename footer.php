<?php
/**
 * @package lsc-group
 */

?>

<footer id="colophon" class="site-footer">

	<div class="footer-top layout-padding">

		<div class="footer-brand-column">
			<?php if ( function_exists( 'lsc_render_footer_logo' ) ) : ?>
				<?php lsc_render_footer_logo(); ?>
			<?php endif; ?>

			<?php if ( function_exists( 'lsc_render_footer_tagline' ) ) : ?>
				<?php lsc_render_footer_tagline(); ?>
			<?php endif; ?>

			<?php if ( function_exists( 'lsc_render_social_medias' ) ) : ?>
				<?php lsc_render_social_medias(); ?>
			<?php endif; ?>
		</div>

		<?php
		if ( function_exists( 'lsc_render_footer_menu' ) ) {
			lsc_render_footer_menu( [ 'location' => 'footerMenu', 'title' => __( 'Home', 'lsc-group' ) ] );
		}
		?>

		<?php if ( function_exists( 'lsc_render_footer_contact' ) ) : ?>
			<?php lsc_render_footer_contact(); ?>
		<?php endif; ?>

	</div>

	<?php if ( function_exists( 'lsc_render_footer_company_registrations' ) ) : ?>
		<?php lsc_render_footer_company_registrations(); ?>
	<?php endif; ?>

	<div class="footer-bottom layout-padding">

		<?php if ( function_exists( 'lsc_render_footer_copyright' ) ) : ?>
			<?php lsc_render_footer_copyright(); ?>
		<?php endif; ?>

		<?php if ( function_exists( 'lsc_render_website_credit' ) ) : ?>
			<?php lsc_render_website_credit(); ?>
		<?php endif; ?>

	</div>

</footer>

</div><!-- #page -->

<?php //  lsc_render_mobile_navigation(); ?>

<?php wp_footer(); ?>

</body>
</html>
