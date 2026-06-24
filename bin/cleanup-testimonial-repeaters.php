<?php
/**
 * One-off cleanup: point migrated testimonials_section blocks at the Library
 * and clear their old Manual repeater data (removes the duplicate copy).
 *
 * For every flexible-content `testimonials_section` still set to Source = Manual,
 * it maps each entry back to its `testimonial` CPT post (by the same name|quote
 * hash the migration stamped), and ONLY if every entry maps cleanly it:
 *   - sets Source = Library, Which = Selected
 *   - stores those testimonials (in order) in Selected Testimonials
 *   - empties the Manual repeater
 *
 * A section is left untouched if any entry has no matching CPT post — so run
 * bin/migrate-testimonials.php FIRST. Idempotent (already-Library rows skip).
 *
 * Usage:
 *   php bin/cleanup-testimonial-repeaters.php --dry-run   # preview, writes nothing
 *   php bin/cleanup-testimonial-repeaters.php             # perform the cleanup
 *
 * CLI only.
 *
 * @package lsc-group
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( "This script can only be run from the command line.\n" );
}

$wp_load = __DIR__ . '/../../../../wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	exit( "Could not find wp-load.php at {$wp_load}\n" );
}

require $wp_load;

if ( ! function_exists( 'have_rows' ) ) {
	exit( "ACF is not active — aborting.\n" );
}

$dry_run = in_array( '--dry-run', $argv, true ) || in_array( 'dry', $argv, true );

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

echo ( $dry_run ? "DRY RUN — no changes will be written.\n" : "CLEANING UP…\n" );
echo 'Scanning ' . count( $scan ) . " posts.\n\n";

$cleared = 0;
$skipped = 0;

/**
 * Find the migrated CPT post id for a manual entry, by the migration hash.
 */
function lsc_find_testimonial_by_entry( $title, $quote ) {
	$hash  = md5( $title . '|' . $quote );
	$match = get_posts(
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

	return $match ? (int) $match[0] : 0;
}

foreach ( $scan as $post_id ) {

	if ( ! have_rows( 'cms', $post_id ) ) {
		continue;
	}

	$updates = []; // row_index => [ ids ]

	while ( have_rows( 'cms', $post_id ) ) {
		the_row();

		if ( 'testimonials_section' !== get_row_layout() ) {
			continue;
		}

		$row_index = get_row_index();
		$source    = get_sub_field( 'source' ) ?: 'manual';

		if ( 'library' === $source ) {
			continue; // Already on the library — nothing to clean.
		}

		if ( ! have_rows( 'testimonials' ) ) {
			continue; // Manual but empty — leave as-is.
		}

		$total   = 0;
		$matched = 0;
		$ids     = [];

		while ( have_rows( 'testimonials' ) ) {
			the_row();

			$quote = trim( (string) get_sub_field( 'quote' ) );

			if ( '' === $quote ) {
				continue;
			}

			++$total;

			$name  = trim( (string) get_sub_field( 'author_name' ) );
			$title = '' !== $name ? $name : wp_trim_words( $quote, 6, '…' );
			$found = lsc_find_testimonial_by_entry( $title, $quote );

			if ( $found ) {
				++$matched;
				$ids[] = $found;
			}
		}

		$label = get_the_title( $post_id ) . " (#{$post_id}) row {$row_index}";

		if ( 0 === $total ) {
			continue;
		}

		if ( $matched < $total ) {
			++$skipped;
			echo "  - SKIP {$label}: {$matched}/{$total} entries found in library — run the migration first.\n";
			continue;
		}

		$updates[ $row_index ] = array_values( array_unique( $ids ) );
	}

	foreach ( $updates as $row_index => $ids ) {
		$label = get_the_title( $post_id ) . " (#{$post_id}) row {$row_index}";

		if ( $dry_run ) {
			++$cleared;
			echo '  + WOULD CLEAR ' . $label . ' → Library/Selected (' . count( $ids ) . " testimonials)\n";
			continue;
		}

		update_sub_field( [ 'cms', $row_index, 'source' ], 'library', $post_id );
		update_sub_field( [ 'cms', $row_index, 'library_selection' ], 'selected', $post_id );
		update_sub_field( [ 'cms', $row_index, 'selected_testimonials' ], $ids, $post_id );
		update_sub_field( [ 'cms', $row_index, 'testimonials' ], [], $post_id );

		++$cleared;
		echo '  + CLEARED ' . $label . ' → Library/Selected (' . count( $ids ) . " testimonials)\n";
	}
}

echo "\n";
echo ( $dry_run ? "Would clear: {$cleared}" : "Cleared: {$cleared}" ) . " section(s), skipped: {$skipped}.\n";

if ( $dry_run ) {
	echo "\nRe-run without --dry-run to perform the cleanup.\n";
}
