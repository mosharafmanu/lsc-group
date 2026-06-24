<?php
/**
 * One-off migration: old in-section testimonial repeaters -> `testimonial` CPT.
 *
 * Scans every flexible-content (`cms`) testimonials_section that uses the
 * Manual repeater and creates one Testimonial post per entry. Idempotent:
 * each migrated entry is stamped with a hash so re-running won't duplicate.
 *
 * Usage (from anywhere):
 *   php bin/migrate-testimonials.php --dry-run   # preview, writes nothing
 *   php bin/migrate-testimonials.php             # perform the migration
 *
 * CLI only.
 *
 * @package lsc-group
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( "This script can only be run from the command line.\n" );
}

// Bootstrap WordPress (script lives in themes/lsc-group/bin).
$wp_load = __DIR__ . '/../../../../wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	exit( "Could not find wp-load.php at {$wp_load}\n" );
}

require $wp_load;

if ( ! function_exists( 'have_rows' ) ) {
	exit( "ACF is not active — aborting.\n" );
}

$dry_run = in_array( '--dry-run', $argv, true ) || in_array( 'dry', $argv, true );

// Post types that can carry the `cms` flexible content (from the field group location).
$post_types = [ 'page', 'post', 'product', 'finance_product' ];

$scan = get_posts(
	[
		'post_type'      => $post_types,
		'post_status'    => [ 'publish', 'draft', 'pending', 'private', 'future' ],
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]
);

echo ( $dry_run ? "DRY RUN — no changes will be written.\n" : "MIGRATING…\n" );
echo 'Scanning ' . count( $scan ) . " posts for testimonials_section (Manual) entries.\n\n";

$created   = 0;
$skipped   = 0;
$found     = 0;

foreach ( $scan as $post_id ) {

	if ( ! have_rows( 'cms', $post_id ) ) {
		continue;
	}

	while ( have_rows( 'cms', $post_id ) ) {
		the_row();

		if ( 'testimonials_section' !== get_row_layout() ) {
			continue;
		}

		// Only the Manual repeater holds inline data; Library sections have none.
		if ( ! have_rows( 'testimonials' ) ) {
			continue;
		}

		while ( have_rows( 'testimonials' ) ) {
			the_row();

			$quote   = trim( (string) get_sub_field( 'quote' ) );
			$name    = trim( (string) get_sub_field( 'author_name' ) );
			$role    = trim( (string) get_sub_field( 'author_role' ) );
			$initial = trim( (string) get_sub_field( 'author_initial' ) );
			$rating  = get_sub_field( 'rating' );

			if ( '' === $quote ) {
				continue; // Nothing to migrate.
			}

			++$found;

			$title = '' !== $name ? $name : wp_trim_words( $quote, 6, '…' );
			$hash  = md5( $title . '|' . $quote );

			// Idempotency: skip if this exact entry was already migrated.
			$existing = get_posts(
				[
					'post_type'      => 'testimonial',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'meta_key'       => '_lsc_migrated_hash',
					'meta_value'     => $hash,
				]
			);

			if ( $existing ) {
				++$skipped;
				echo "  - SKIP (already migrated): {$title}\n";
				continue;
			}

			$source_label = get_the_title( $post_id ) . " (#{$post_id})";

			if ( $dry_run ) {
				++$created;
				echo "  + WOULD CREATE: {$title}  [from {$source_label}]\n";
				continue;
			}

			$new_id = wp_insert_post(
				[
					'post_type'   => 'testimonial',
					'post_status' => 'publish',
					'post_title'  => $title,
					'menu_order'  => $found,
				],
				true
			);

			if ( is_wp_error( $new_id ) || ! $new_id ) {
				echo "  ! ERROR creating: {$title}\n";
				continue;
			}

			update_field( 'field_testimonial_quote', $quote, $new_id );
			update_field( 'field_testimonial_author_role', $role, $new_id );
			update_field( 'field_testimonial_author_initial', $initial, $new_id );

			if ( $rating ) {
				update_field( 'field_testimonial_rating', $rating, $new_id );
			}

			update_post_meta( $new_id, '_lsc_migrated_hash', $hash );

			++$created;
			echo "  + CREATED (#{$new_id}): {$title}  [from {$source_label}]\n";
		}
	}
}

echo "\n";
echo "Done. Found {$found} testimonial entr" . ( 1 === $found ? 'y' : 'ies' ) . ".\n";
echo ( $dry_run ? "Would create: {$created}" : "Created: {$created}" ) . ", skipped (already migrated): {$skipped}.\n";

if ( $dry_run ) {
	echo "\nRe-run without --dry-run to perform the migration.\n";
}
