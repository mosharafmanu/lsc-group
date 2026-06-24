<?php
/**
 * Project custom post types.
 *
 * @package lsc-group
 */

if ( ! function_exists( 'lsc_register_finance_product_post_type' ) ) {
	function lsc_register_finance_product_post_type() {
		$labels = [
			'name'                  => _x( 'Finance Products', 'Post type general name', 'lsc-group' ),
			'singular_name'         => _x( 'Finance Product', 'Post type singular name', 'lsc-group' ),
			'menu_name'             => _x( 'Finance Products', 'Admin menu text', 'lsc-group' ),
			'name_admin_bar'        => _x( 'Finance Product', 'Add new on toolbar', 'lsc-group' ),
			'add_new'               => __( 'Add New', 'lsc-group' ),
			'add_new_item'          => __( 'Add New Finance Product', 'lsc-group' ),
			'new_item'              => __( 'New Finance Product', 'lsc-group' ),
			'edit_item'             => __( 'Edit Finance Product', 'lsc-group' ),
			'view_item'             => __( 'View Finance Product', 'lsc-group' ),
			'all_items'             => __( 'All Finance Products', 'lsc-group' ),
			'search_items'          => __( 'Search Finance Products', 'lsc-group' ),
			'not_found'             => __( 'No finance products found.', 'lsc-group' ),
			'not_found_in_trash'    => __( 'No finance products found in Trash.', 'lsc-group' ),
			'featured_image'        => __( 'Product Card Image', 'lsc-group' ),
			'set_featured_image'    => __( 'Set product card image', 'lsc-group' ),
			'remove_featured_image' => __( 'Remove product card image', 'lsc-group' ),
			'use_featured_image'    => __( 'Use as product card image', 'lsc-group' ),
		];

		register_post_type(
			'finance_product',
			[
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable'  => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => true,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => 'finance-product' ],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-money-alt',
				'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
				'exclude_from_search' => false,
			]
		);
	}
}
add_action( 'init', 'lsc_register_finance_product_post_type' );

if ( ! function_exists( 'lsc_register_testimonial_post_type' ) ) {
	function lsc_register_testimonial_post_type() {
		$labels = [
			'name'               => _x( 'Testimonials', 'Post type general name', 'lsc-group' ),
			'singular_name'      => _x( 'Testimonial', 'Post type singular name', 'lsc-group' ),
			'menu_name'          => _x( 'Testimonials', 'Admin menu text', 'lsc-group' ),
			'name_admin_bar'     => _x( 'Testimonial', 'Add new on toolbar', 'lsc-group' ),
			'add_new'            => __( 'Add New', 'lsc-group' ),
			'add_new_item'       => __( 'Add New Testimonial', 'lsc-group' ),
			'new_item'           => __( 'New Testimonial', 'lsc-group' ),
			'edit_item'          => __( 'Edit Testimonial', 'lsc-group' ),
			'view_item'          => __( 'View Testimonial', 'lsc-group' ),
			'all_items'          => __( 'All Testimonials', 'lsc-group' ),
			'search_items'       => __( 'Search Testimonials', 'lsc-group' ),
			'not_found'          => __( 'No testimonials found.', 'lsc-group' ),
			'not_found_in_trash' => __( 'No testimonials found in Trash.', 'lsc-group' ),
		];

		register_post_type(
			'testimonial',
			[
				'labels'              => $labels,
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'query_var'           => false,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_position'       => 21,
				'menu_icon'           => 'dashicons-format-quote',
				'supports'            => [ 'title', 'page-attributes' ],
				'exclude_from_search' => true,
			]
		);
	}
}
add_action( 'init', 'lsc_register_testimonial_post_type' );

if ( ! function_exists( 'lsc_register_case_study_post_type' ) ) {
	function lsc_register_case_study_post_type() {
		$labels = [
			'name'                  => _x( 'Case Studies', 'Post type general name', 'lsc-group' ),
			'singular_name'         => _x( 'Case Study', 'Post type singular name', 'lsc-group' ),
			'menu_name'             => _x( 'Case Studies', 'Admin menu text', 'lsc-group' ),
			'name_admin_bar'        => _x( 'Case Study', 'Add new on toolbar', 'lsc-group' ),
			'add_new'               => __( 'Add New', 'lsc-group' ),
			'add_new_item'          => __( 'Add New Case Study', 'lsc-group' ),
			'new_item'              => __( 'New Case Study', 'lsc-group' ),
			'edit_item'             => __( 'Edit Case Study', 'lsc-group' ),
			'view_item'             => __( 'View Case Study', 'lsc-group' ),
			'all_items'             => __( 'All Case Studies', 'lsc-group' ),
			'search_items'          => __( 'Search Case Studies', 'lsc-group' ),
			'not_found'             => __( 'No case studies found.', 'lsc-group' ),
			'not_found_in_trash'    => __( 'No case studies found in Trash.', 'lsc-group' ),
			'featured_image'        => __( 'Case Study Card Image', 'lsc-group' ),
			'set_featured_image'    => __( 'Set card image', 'lsc-group' ),
			'remove_featured_image' => __( 'Remove card image', 'lsc-group' ),
			'use_featured_image'    => __( 'Use as card image', 'lsc-group' ),
		];

		register_post_type(
			'case_study',
			[
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'query_var'           => true,
				'rewrite'             => [ 'slug' => 'case-study' ],
				'capability_type'     => 'post',
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_position'       => 22,
				'menu_icon'           => 'dashicons-portfolio',
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
				'exclude_from_search' => false,
			]
		);
	}
}
add_action( 'init', 'lsc_register_case_study_post_type' );
