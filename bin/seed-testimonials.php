<?php
/**
 * One-off reseed: delete all current `testimonial` posts and create the real
 * set transcribed from the live Testimonials page.
 *
 * Any flexible-content testimonials_section using Source = Library / Selected
 * is re-pointed by matching author name, so existing selections keep working
 * after the old posts are deleted.
 *
 * Usage:
 *   php bin/seed-testimonials.php --dry-run   # preview, writes nothing
 *   php bin/seed-testimonials.php             # delete + reseed
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

if ( ! function_exists( 'update_field' ) ) {
	exit( "ACF is not active — aborting.\n" );
}

$dry_run = in_array( '--dry-run', $argv, true ) || in_array( 'dry', $argv, true );

/**
 * The testimonials, in page order. role left "" where the subtitle line was
 * not legible in the source — fill those in the admin.
 */
$data = [
	[ 'name' => 'Sebastiano Carrelli', 'role' => '', 'quote' => "Having found LSC this year, we love working with them. Dynamic, commercial and with an excellent in house legal team that focus on delivery, we are sure to be using them long into the future!" ],
	[ 'name' => 'William H', 'role' => '', 'quote' => "We have a great experience with LSC when purchasing two properties. Customer service was great throughout and the team responsive and helpful. We really appreciated their flexibility. We would highly recommend LSC to anyone in need of financial assistance and will be using them again in the future." ],
	[ 'name' => 'Rossendale Construction', 'role' => 'Borrower', 'quote' => "We were very pleased with the process which was carried out in a speedy, efficient and professional manner. We will use your services again should we need them!" ],
	[ 'name' => 'Keith M', 'role' => 'Business owner', 'quote' => "I have used LSC for 10 years and without their considerable assistance, I would not have been able to continue my successful business. Shaun and the team have always assisted with the minimum of fuss and without asking tedious questions. Thank you all." ],
	[ 'name' => 'Michael D', 'role' => '', 'quote' => "It has truly been an experience that takes me back to when lending was a more pleasant enterprise. Your company ethos is both refreshing and should be applauded. Such a refreshing firm to work with, you clearly are what you say on the tin." ],
	[ 'name' => 'Mr & Mrs S', 'role' => 'Developer', 'quote' => "Many thanks for all your help! Your patience and attention to detail has been a real help to us whilst we have carried out the whole development..." ],
	[ 'name' => 'Dave Cookson', 'role' => '', 'quote' => "I have used LSC on many occasions and have always been impressed by their speed and their positive attitude to lending. I would readily recommend them to any of my clients..." ],
	[ 'name' => 'Property Saints', 'role' => '', 'quote' => "I started using LSC in 2013 and continue to do so. The whole process from start to finish was as easy as can be. Not only was it easy to do it was a lower cost too compared to the rest of the lenders." ],
	[ 'name' => 'Ken Howieson', 'role' => 'Edge Business Solutions', 'quote' => "Thank you to LSC for your support on recent commercial bridging loan for my client. He was delighted with the speed and ease of the lending you supplied. I would like to thank you personally for taking into consideration his circumstances, the property itself and the decisions you made in the terms of the lending." ],
	[ 'name' => 'Gonzalo Villanueva', 'role' => 'JGV Investments', 'quote' => "I am very happy with LSC, they are second to none. While the majority of the lenders look for excuses not to lend you, LSC is the opposite, they want to make sure that you have the funds on time to make the deal happen. So they are very positive, fast and efficient. I need to say thanks to them because they very good for my business." ],
	[ 'name' => 'Marc Green', 'role' => '', 'quote' => "LSC were initially selected as our preferred lending partner based on their ability to take a commercial view and perform within a tight time frame and they didn't disappoint. The speed at which decisions were made and issues overcome at every stage of the transaction was refreshing and we were thoroughly delighted with the outcome. We would not hesitate considering LSC for future property related transactions." ],
	[ 'name' => 'Pete S', 'role' => 'Broker', 'quote' => "We have been dealing with LSC since they entered the bridging market. We've always found the LSC team and their advisor's to be very knowledgeable and professional. They pride themselves on their speed and efficiency and we are grateful for these attributes. Furthermore, as a broker, it is excellent to be able to deal directly with the decision makers in LSC and also to be able to discuss matters through with their advisor's. It is always a bonus to deal with a well funded lender because when LSC say they'll do the deal then they do it. We highly recommend LSC to other brokers and also to our clients." ],
	[ 'name' => 'Mark F', 'role' => 'Broker', 'quote' => "I have been brokering for nearly 20 years. You are only as good as your last deal, and your reputation is everything. That's why working with the right lenders is so important, because you have to deliver. LSC does that. I have introduced some testing deals and situations, and they have come through for me and my client. The biggest compliment I can pay them is that they know what they are doing, and they do what they say they will do. I have no reservation recommending LSC and hope to build on the deals we have done so far this year." ],
	[ 'name' => 'Richard N', 'role' => 'Broker', 'quote' => "A client approached me having bought a piece of land for £500,000 on a 28 day contract. The client had no money to input to the transaction although he did have a house mortgage free. This office approached LSC who completed the transaction in 17 days and also provided the development finance. I was very impressed as was the client." ],
	[ 'name' => 'John W', 'role' => 'Broker', 'quote' => "We have been dealing with LSC for some time now, and can rely on their common sense approach to decision making. They are always fast to respond, very proactive and ultimately willing & able to lend" ],
	[ 'name' => 'Samantha Williamson', 'role' => 'SJW Property Finance', 'quote' => "There are many short term lenders in the marketplace claiming to offer similar things, and you hear many stories from other brokers about lenders who have changed terms at the last minute. What I like about LSC is their knowledge and experience of the local property market, and their ability to thoroughly assess a deal before terms are issued. This ensures that there are no last minute changes, and they stand by their terms. This gives me confidence in recommending them to a client, and gives the client certainty that the deal will complete. They are flexible in structuring deals, and will competitively price them reflecting the client's situation and needs. I have been referring business to them since setting up SJW Property Finance in 2014 and have found their service to be excellent, and would certainly recommend them to other brokers and clients." ],
	[ 'name' => 'Brian R', 'role' => 'Broker', 'quote' => "Having introduced a number of loans to LSC over the past 3 years I have been very impressed by the service levels offered. Decisions are made quickly and they always deliver on their commitments. Direct access to the decision makers streamlines the process and empowers the broker to make commitments to the borrower which they know will be upheld. Their Surveyors and Solicitors are also approachable and share the same commercial values as the lender. Our clients' relationship with LSC, and therefore our own, has been very profitable and we are very happy to introduce new clients." ],
];

// 1. Capture existing Library/Selected references (by author name) so we can re-point.
$post_types = [ 'page', 'post', 'product', 'finance_product' ];
$scan       = get_posts(
	[
		'post_type'      => $post_types,
		'post_status'    => [ 'publish', 'draft', 'pending', 'private', 'future' ],
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]
);

$selections = []; // [ [ post_id, row_index, [names...] ] ]

foreach ( $scan as $post_id ) {
	if ( ! have_rows( 'cms', $post_id ) ) {
		continue;
	}

	while ( have_rows( 'cms', $post_id ) ) {
		the_row();

		if ( 'testimonials_section' !== get_row_layout() ) {
			continue;
		}

		if ( 'library' !== ( get_sub_field( 'source' ) ?: 'manual' ) ) {
			continue;
		}

		if ( 'selected' !== ( get_sub_field( 'library_selection' ) ?: 'all' ) ) {
			continue;
		}

		$picked = get_sub_field( 'selected_testimonials' );
		$names  = [];

		if ( $picked && is_array( $picked ) ) {
			foreach ( $picked as $p ) {
				$pid     = is_object( $p ) ? $p->ID : (int) $p;
				$names[] = get_the_title( $pid );
			}
		}

		$selections[] = [ $post_id, get_row_index(), $names ];
	}
}

// 2. Existing testimonials to delete.
$existing = get_posts(
	[
		'post_type'      => 'testimonial',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]
);

echo ( $dry_run ? "DRY RUN — no changes will be written.\n\n" : "RESEEDING…\n\n" );
echo 'Existing testimonials to delete: ' . count( $existing ) . "\n";
echo 'New testimonials to create: ' . count( $data ) . "\n";
echo 'Library/Selected sections to re-point: ' . count( $selections ) . "\n\n";

if ( $dry_run ) {
	foreach ( $data as $i => $t ) {
		$role = '' !== $t['role'] ? $t['role'] : '(no role — fill in admin)';
		echo '  + ' . ( $i + 1 ) . '. ' . $t['name'] . ' — ' . $role . "\n";
	}
	echo "\nRe-run without --dry-run to apply.\n";
	exit;
}

// 3. Delete existing.
foreach ( $existing as $id ) {
	wp_delete_post( $id, true );
}

// 4. Insert new.
$name_to_id = [];

foreach ( $data as $i => $t ) {
	$new_id = wp_insert_post(
		[
			'post_type'   => 'testimonial',
			'post_status' => 'publish',
			'post_title'  => $t['name'],
			'menu_order'  => $i + 1,
		],
		true
	);

	if ( is_wp_error( $new_id ) || ! $new_id ) {
		echo '  ! ERROR creating: ' . $t['name'] . "\n";
		continue;
	}

	update_field( 'field_testimonial_quote', $t['quote'], $new_id );
	update_field( 'field_testimonial_author_role', $t['role'], $new_id );
	update_field( 'field_testimonial_rating', '5', $new_id );

	$name_to_id[ $t['name'] ] = $new_id;
	echo '  + CREATED (#' . $new_id . '): ' . $t['name'] . "\n";
}

// 5. Re-point captured selections by name.
foreach ( $selections as $sel ) {
	list( $post_id, $row_index, $names ) = $sel;
	$ids = [];

	foreach ( $names as $name ) {
		if ( isset( $name_to_id[ $name ] ) ) {
			$ids[] = $name_to_id[ $name ];
		}
	}

	if ( $ids ) {
		update_sub_field( [ 'cms', $row_index, 'selected_testimonials' ], $ids, $post_id );
		echo '  ~ RE-POINTED ' . get_the_title( $post_id ) . " (#{$post_id}) row {$row_index} → " . count( $ids ) . " testimonials\n";
	} else {
		echo '  ! Could not re-point ' . get_the_title( $post_id ) . " (#{$post_id}) row {$row_index} — selected names not in new set: " . implode( ', ', $names ) . "\n";
	}
}

echo "\nDone. Created " . count( $name_to_id ) . " testimonials.\n";
